<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassRoutine;
use App\Models\ClassTimeSlot;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ClassRoutineController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Current Institution
    |--------------------------------------------------------------------------
    */

    private function institutionId(): int
    {
        $user = auth()->user();

        $institutionId = $user?->active_institution_id;

        if (!$institutionId && app()->bound('institution_id')) {
            $institutionId = app('institution_id');
        }

        abort_unless($user && $institutionId, 403, 'প্রতিষ্ঠান নির্ধারণ করা যায়নি।');

        return (int) $institutionId;
    }

    private function scopedRoutine($id): ClassRoutine
    {
        return ClassRoutine::where('institution_id', $this->institutionId())
            ->findOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Page (Class Routine tab + Time Slot tab)
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $institutionId = $this->institutionId();

        $tab = $request->input('tab') === 'slots' ? 'slots' : 'routines';

        $academicYears = AcademicYear::where('institution_id', $institutionId)
            ->orderByDesc('id')
            ->get();

        // Default = current academic year
        $selectedYearId = $request->filled('academic_year_id')
            ? (int) $request->input('academic_year_id')
            : (int) ($academicYears->firstWhere('is_current', true)?->id ?? 0);

        $sections = Section::where('institution_id', $institutionId)
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $timeSlots = ClassTimeSlot::where('institution_id', $institutionId)
            ->orderBy('start_time')
            ->get();

        $teachers = Teacher::with('user')
            ->where('institution_id', $institutionId)
            ->get()
            ->sortBy(fn ($t) => $t->full_name)
            ->values();

        $query = ClassRoutine::with([
                'timeSlot',
                'subject',
                'teacher.user',
                'section',
                'academicYear',
            ])
            ->where('institution_id', $institutionId);

        if ($selectedYearId) {
            $query->where('academic_year_id', $selectedYearId);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', (int) $request->input('section_id'));
        }

        $routines = $query
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.class-routines.index', [
            'tab'            => $tab,
            'routines'       => $routines,
            'academicYears'  => $academicYears,
            'sections'       => $sections,
            'timeSlots'      => $timeSlots,
            'teachers'       => $teachers,
            'selectedYearId' => $selectedYearId,
            'days'           => ClassRoutine::DAYS,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Subjects of a section (AJAX)
    |--------------------------------------------------------------------------
    */

    public function subjects(Request $request)
    {
        $institutionId = $this->institutionId();

        $request->validate([
            'section_id' => ['required', 'integer'],
        ]);

        $section = Section::where('institution_id', $institutionId)
            ->findOrFail($request->integer('section_id'));

        $table = (new Subject)->getTable();

        $query = Subject::query()->where('institution_id', $institutionId);

        if (Schema::hasColumn($table, 'section_id')) {
            $query->where('section_id', $section->id);
        } elseif (Schema::hasColumn($table, 'class_id')) {
            $query->where('class_id', $section->class_id);
        }

        $subjects = $query->orderBy('id')->get()->map(fn ($s) => [
            'id'   => $s->id,
            'name' => $s->name_bn ?: $s->name,
        ]);

        return response()->json($subjects->values());
    }

    /*
    |--------------------------------------------------------------------------
    | Validation + conflict helpers
    |--------------------------------------------------------------------------
    */

    private function validateRoutine(Request $request, int $institutionId, bool $multipleDays): array
    {
        $subjectTable = (new Subject)->getTable();

        $rules = [
            'academic_year_id' => [
                'required',
                Rule::exists('academic_years', 'id')->where('institution_id', $institutionId),
            ],
            'time_slot_id' => [
                'required',
                Rule::exists('class_time_slots', 'id')->where('institution_id', $institutionId),
            ],
            'section_id' => [
                'required',
                Rule::exists('sections', 'id')->where('institution_id', $institutionId),
            ],
            'subject_id' => [
                'required',
                Rule::exists($subjectTable, 'id')->where('institution_id', $institutionId),
            ],
            'teacher_id' => [
                'required',
                Rule::exists('teachers', 'id')->where('institution_id', $institutionId),
            ],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ];

        if ($multipleDays) {
            $rules['days']   = ['required', 'array', 'min:1'];
            $rules['days.*'] = ['required', Rule::in(array_keys(ClassRoutine::DAYS))];
        } else {
            $rules['day'] = ['required', Rule::in(array_keys(ClassRoutine::DAYS))];
        }

        return $request->validate($rules, [
            'academic_year_id.required' => 'শিক্ষাবর্ষ নির্বাচন করুন।',
            'time_slot_id.required'     => 'সময় নির্বাচন করুন।',
            'section_id.required'       => 'সাব ক্লাস নির্বাচন করুন।',
            'subject_id.required'       => 'বিষয় নির্বাচন করুন।',
            'teacher_id.required'       => 'শিক্ষক নির্বাচন করুন।',
            'days.required'             => 'কমপক্ষে একটি দিন নির্বাচন করুন।',
            'day.required'              => 'দিন নির্বাচন করুন।',
            '*.exists'                  => 'নির্বাচিত তথ্য সঠিক নয়।',
        ]);
    }

    private function conflicts(int $institutionId, array $data, string $day, ?int $ignoreId = null): array
    {
        $messages = [];
        $dayLabel = ClassRoutine::DAYS[$day] ?? $day;

        $base = ClassRoutine::where('institution_id', $institutionId)
            ->where('academic_year_id', $data['academic_year_id'])
            ->where('time_slot_id', $data['time_slot_id'])
            ->where('day', $day);

        if ($ignoreId) {
            $base->where('id', '!=', $ignoreId);
        }

        if ((clone $base)->where('section_id', $data['section_id'])->exists()) {
            $messages[] = $dayLabel . ': এই সাব ক্লাসে এই সময়ে ইতোমধ্যে রুটিন আছে।';
        }

        if ((clone $base)->where('teacher_id', $data['teacher_id'])->exists()) {
            $messages[] = $dayLabel . ': এই শিক্ষকের এই সময়ে অন্য ক্লাসে রুটিন আছে।';
        }

        return $messages;
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $institutionId = $this->institutionId();

        $validated = $this->validateRoutine($request, $institutionId, true);

        $section = Section::where('institution_id', $institutionId)
            ->findOrFail($validated['section_id']);

        $days = array_values(array_unique($validated['days']));

        $problems = [];

        foreach ($days as $day) {
            $problems = array_merge($problems, $this->conflicts($institutionId, $validated, $day));
        }

        if ($problems) {
            return back()->withInput()->withErrors(['conflict' => $problems]);
        }

        try {
            DB::transaction(function () use ($days, $validated, $section, $institutionId) {
                foreach ($days as $day) {
                    ClassRoutine::create([
                        'institution_id'   => $institutionId,
                        'academic_year_id' => $validated['academic_year_id'],
                        'class_id'         => $section->class_id,
                        'section_id'       => $section->id,
                        'subject_id'       => $validated['subject_id'],
                        'teacher_id'       => $validated['teacher_id'],
                        'time_slot_id'     => $validated['time_slot_id'],
                        'day'              => $day,
                        'remarks'          => $validated['remarks'] ?? null,
                        'created_by'       => auth()->id(),
                    ]);
                }
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'রুটিন সংরক্ষণ করতে সমস্যা হয়েছে।');
        }

        return redirect()
            ->route('dashboard.class-routines.index', ['academic_year_id' => $validated['academic_year_id']])
            ->with('success', 'ক্লাস রুটিন সংরক্ষণ করা হয়েছে।');
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $institutionId = $this->institutionId();

        $routine = $this->scopedRoutine($id);

        $validated = $this->validateRoutine($request, $institutionId, false);

        $section = Section::where('institution_id', $institutionId)
            ->findOrFail($validated['section_id']);

        $problems = $this->conflicts($institutionId, $validated, $validated['day'], $routine->id);

        if ($problems) {
            return back()->withInput()->withErrors(['conflict' => $problems]);
        }

        try {
            $routine->update([
                'academic_year_id' => $validated['academic_year_id'],
                'class_id'         => $section->class_id,
                'section_id'       => $section->id,
                'subject_id'       => $validated['subject_id'],
                'teacher_id'       => $validated['teacher_id'],
                'time_slot_id'     => $validated['time_slot_id'],
                'day'              => $validated['day'],
                'remarks'          => $validated['remarks'] ?? null,
                'updated_by'       => auth()->id(),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'রুটিন আপডেট করতে সমস্যা হয়েছে।');
        }

        return redirect()
            ->route('dashboard.class-routines.index', ['academic_year_id' => $validated['academic_year_id']])
            ->with('success', 'ক্লাস রুটিন আপডেট করা হয়েছে।');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $this->scopedRoutine($id)->delete();

        return back()->with('success', 'ক্লাস রুটিন মুছে ফেলা হয়েছে।');
    }
}