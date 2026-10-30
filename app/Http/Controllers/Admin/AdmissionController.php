<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class AdmissionController extends Controller
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

        if (
            !$institutionId
            && app()->bound('institution_id')
        ) {
            $institutionId = app('institution_id');
        }

        abort_unless(
            $user && $institutionId,
            403,
            'প্রতিষ্ঠান নির্ধারণ করা যায়নি।'
        );

        return (int) $institutionId;
    }

    /*
    |--------------------------------------------------------------------------
    | Institution Scoped Admission
    |--------------------------------------------------------------------------
    */

    private function scopedAdmission($id): Admission
    {
        return Admission::where(
            'institution_id',
            $this->institutionId()
        )->findOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Admission List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $institutionId = $this->institutionId();

        $query = Admission::with([
            'student.user',
            'academicYear',
            'class',
        ])
        ->where('institution_id', $institutionId);

        // Academic Year Filter
        if ($request->filled('academic_year_id')) {
            $query->where(
                'academic_year_id',
                $request->academic_year_id
            );
        }

        // Class Filter
        if ($request->filled('class_id')) {
            $query->where(
                'class_id',
                $request->class_id
            );
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where(
                    'admission_no',
                    'like',
                    "%{$search}%"
                )
                ->orWhereHas(
                    'student',
                    function ($sq) use ($search) {
                        $sq->where(
                            'institution_id',
                            $this->institutionId()
                        )
                        ->where(function ($studentQuery) use ($search) {
                            $studentQuery->where(
                                'student_id',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhereHas(
                                'user',
                                function ($uq) use ($search) {
                                    $uq->where(
                                        'name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'name_bn',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'phone',
                                        'like',
                                        "%{$search}%"
                                    );
                                }
                            );
                        });
                    }
                );
            });
        }

        $admissions = $query
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        // Selected Student
        $selectedStudent = null;

        if ($request->filled('student_id')) {
            $selectedStudent = Student::with([
                'user',
                'class',
            ])
            ->where('institution_id', $institutionId)
            ->find($request->student_id);
        }

        // Active Academic Years
        $academicYears = AcademicYear::where(
            'institution_id',
            $institutionId
        )
        ->where('status', 'active')
        ->get();

        // Active Classes
        $classes = Classes::where(
            'institution_id',
            $institutionId
        )
        ->where('status', 'active')
        ->get();

        // Current Academic Year
        $currentYear = AcademicYear::where(
            'institution_id',
            $institutionId
        )
        ->where('is_current', true)
        ->first();

        // Current Year Active Admissions
        $currentYearStudents = Admission::where(
            'institution_id',
            $institutionId
        )
        ->where(
            'academic_year_id',
            $currentYear?->id
        )
        ->where('status', 'active')
        ->count();

        // Total Active Students
        $totalStudents = Student::where(
            'institution_id',
            $institutionId
        )
        ->where('status', 'active')
        ->count();

        return view(
            'admin.admissions.index',
            compact(
                'admissions',
                'academicYears',
                'classes',
                'currentYearStudents',
                'totalStudents',
                'selectedStudent'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create Admission Form
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $institutionId = $this->institutionId();

        $students = Student::with('user')
            ->where('institution_id', $institutionId)
            ->whereDoesntHave(
                'admissions',
                function ($q) use ($institutionId) {
                    $q->where(
                        'institution_id',
                        $institutionId
                    )
                    ->where('status', 'active');
                }
            )
            ->get();

        $academicYears = AcademicYear::where(
            'institution_id',
            $institutionId
        )
        ->where('status', 'active')
        ->get();

        $classes = Classes::where(
            'institution_id',
            $institutionId
        )
        ->where('status', 'active')
        ->get();

        return view(
            'admin.admissions.create',
            compact(
                'students',
                'academicYears',
                'classes'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store Admission
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $institutionId = $this->institutionId();

        $validated = $request->validate([
            'student_id' => [
                'required',
                Rule::exists('students', 'id')
                    ->where('institution_id', $institutionId),
            ],

            'academic_year_id' => [
                'required',
                Rule::exists('academic_years', 'id')
                    ->where('institution_id', $institutionId)
                    ->where('status', 'active'),
            ],

            'class_id' => [
                'required',
                Rule::exists('classes', 'id')
                    ->where('institution_id', $institutionId)
                    ->where('status', 'active'),
            ],

            'entry_date' => [
                'required',
                'date',
            ],

            'financial_status' => [
                'nullable',
                'in:solvent,insolvent,orphan,helpless',
            ],

            'residence_status' => [
                'nullable',
                'in:resident,non-resident,daycare,nightcare',
            ],

            'admission_type' => [
                'nullable',
                'in:new,old',
            ],
        ]);

        try {
            DB::beginTransaction();

            // Verify student belongs to current institution
            $student = Student::where(
                'institution_id',
                $institutionId
            )->findOrFail($validated['student_id']);

            // Generate admission number
            $lastAdmission = Admission::latest('id')->first();

            $nextId = $lastAdmission
                ? $lastAdmission->id + 1
                : 1;

            $admissionNo = 'ADM-'
                . date('Y')
                . '-'
                . str_pad(
                    $nextId,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

            // Create Admission
            $admission = Admission::create([
                'institution_id' => $institutionId,

                'admission_no' => $admissionNo,

                'student_id' => $student->id,

                'academic_year_id' =>
                    $validated['academic_year_id'],

                'class_id' => $validated['class_id'],

                'admission_date' => $validated['entry_date'],

                'financial_status' =>
                    $validated['financial_status'] ?? 'solvent',

                'residence_status' =>
                    $validated['residence_status'] ?? 'non-resident',

                'admission_type' =>
                    $validated['admission_type'] ?? 'new',

                'status' => 'active',

                'created_by' => auth()->id(),
            ]);

            // Update student academic information
            $student->update([
                'academic_year_id' =>
                    $validated['academic_year_id'],

                'class_id' => $validated['class_id'],

                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('dashboard.admissions.index')
                ->with(
                    'success',
                    'শিক্ষার্থী ভর্তি সম্পন্ন হয়েছে। ভর্তি নম্বর: '
                    . $admissionNo
                );

        } catch (\Throwable $e) {
            DB::rollBack();

            report($e);

            return back()
                ->with(
                    'error',
                    'ভর্তি করতে সমস্যা হয়েছে। তথ্য ও Laravel log পরীক্ষা করুন।'
                )
                ->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Show Admission
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $admission = $this->scopedAdmission($id);

        $admission->load([
            'student.user',
            'academicYear',
            'class',
        ]);

        return view(
            'admin.admissions.show',
            compact('admission')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Admission
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $institutionId = $this->institutionId();

        $admission = $this->scopedAdmission($id);

        $academicYears = AcademicYear::where(
            'institution_id',
            $institutionId
        )->get();

        $classes = Classes::where(
            'institution_id',
            $institutionId
        )->get();

        return view(
            'admin.admissions.edit',
            compact(
                'admission',
                'academicYears',
                'classes'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Admission
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $institutionId = $this->institutionId();

        $admission = $this->scopedAdmission($id);

        $validated = $request->validate([
            'class_id' => [
                'required',
                Rule::exists('classes', 'id')
                    ->where('institution_id', $institutionId),
            ],

            'financial_status' => [
                'nullable',
                'in:solvent,insolvent,orphan,helpless',
            ],

            'residence_status' => [
                'nullable',
                'in:resident,non-resident,daycare,nightcare',
            ],

            'status' => [
                'required',
                'in:active,inactive,transferred,graduated,dropped',
            ],

            'leaving_date' => [
                'nullable',
                'date',
            ],

            'leaving_reason' => [
                'nullable',
                'string',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        try {
            DB::beginTransaction();

            $admission->update([
                'class_id' => $validated['class_id'],

                'financial_status' =>
                    $validated['financial_status']
                    ?? $admission->financial_status,

                'residence_status' =>
                    $validated['residence_status']
                    ?? $admission->residence_status,

                'status' => $validated['status'],

                'leaving_date' =>
                    $validated['leaving_date'] ?? null,

                'leaving_reason' =>
                    $validated['leaving_reason'] ?? null,

                'remarks' =>
                    $validated['remarks'] ?? null,

                'updated_by' => auth()->id(),
            ]);

            // Keep student class in sync
            Student::where(
                'institution_id',
                $institutionId
            )
            ->where('id', $admission->student_id)
            ->update([
                'class_id' => $validated['class_id'],
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('dashboard.admissions.index')
                ->with(
                    'success',
                    'ভর্তি তথ্য আপডেট করা হয়েছে।'
                );

        } catch (\Throwable $e) {
            DB::rollBack();

            report($e);

            return back()
                ->with(
                    'error',
                    'আপডেট করতে সমস্যা হয়েছে। তথ্য ও Laravel log পরীক্ষা করুন।'
                )
                ->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Admission
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $admission = $this->scopedAdmission($id);

        $admission->delete();

        return redirect()
            ->route('dashboard.admissions.index')
            ->with(
                'success',
                'ভর্তি তথ্য ডিলিট করা হয়েছে।'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Admission Status
    |--------------------------------------------------------------------------
    */

    public function toggleStatus($id)
    {
        $admission = $this->scopedAdmission($id);

        $admission->status =
            $admission->status === 'active'
                ? 'inactive'
                : 'active';

        $admission->save();

        return back()->with(
            'success',
            'স্ট্যাটাস পরিবর্তন করা হয়েছে।'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Search Students
    |--------------------------------------------------------------------------
    */

    public function search(Request $request)
    {
        $institutionId = $this->institutionId();

        $q = trim((string) $request->input('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $students = Student::with([
            'user',
            'class',
        ])
        ->where('institution_id', $institutionId)
        ->where(function ($query) use ($q) {
            $query->where(
                'student_id',
                'like',
                "%{$q}%"
            )
            ->orWhereHas(
                'user',
                function ($userQuery) use ($q) {
                    $userQuery->where(
                        'name',
                        'like',
                        "%{$q}%"
                    )
                    ->orWhere(
                        'name_bn',
                        'like',
                        "%{$q}%"
                    )
                    ->orWhere(
                        'phone',
                        'like',
                        "%{$q}%"
                    )
                    ->orWhere(
                        'institution_user_id',
                        'like',
                        "%{$q}%"
                    );
                }
            );
        })
        ->limit(20)
        ->get();

        return response()->json(
            $students->map(function ($student) {
                $user = $student->user;

                return [
                    'id' => $student->id,

                    'institution_user_id' =>
                        $user?->institution_user_id ?? '',

                    'student_id' =>
                        $student->student_id ?? '',

                    'name' =>
                        $user?->name_bn
                        ?: ($user?->name ?? ''),

                    'father_name' =>
                        $user?->father_name ?? '',

                    'mobile' =>
                        $user?->phone ?? '',

                    'class_name' =>
                        $student->class?->name_bn
                        ?: ($student->class?->name ?? ''),

                    'photo' =>
                        $user?->photo
                            ? asset('storage/' . $user->photo)
                            : asset('images/default-user.png'),
                ];
            })
        );
    }
}