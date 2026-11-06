<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\DataExportCustomColumn;
use App\Models\DataExportReport;
use App\Models\Madrasa;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentCustomFieldValue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DataExportController extends Controller
{
    /**
     * Available standard columns.
     */
    private function standardColumns(): array
    {
        return [
            'photo'              => 'ছবি',
            'student_id'         => 'আইডি',
            'name'               => 'নাম',
            'father_name'        => 'বাবার নাম',
            'mother_name'        => 'মাতার নাম',
            'phone'              => 'মোবাইল এক',
            'phone2'             => 'মোবাইল ২',
            'email'              => 'ই-মেইল',
            'academic_year'      => 'শিক্ষাবর্ষ',
            'class'              => 'ক্লাশ',
            'section'            => 'সাব ক্লাস',
            'admission_no'       => 'ভর্তি সিরিয়াল',
            'gender'             => 'লিঙ্গ',
            'residence'          => 'আবাসিক',
            'admission_type'     => 'নতুন/পুরাতন',
            'date_of_birth'      => 'জন্ম তারিখ',
            'national_id'        => 'জাতীয় পরিচয়পত্র/জন্ম নিবন্ধন',
            'blood_group'        => 'রক্তের গ্রুপ',
            'permanent_division' => 'স্থায়ী বিভাগ',
            'permanent_district' => 'স্থায়ী জেলা',
            'permanent_thana'    => 'স্থায়ী থানা',
            'permanent_post'     => 'স্থায়ী ডাক',
            'permanent_village'  => 'স্থায়ী গ্রাম',
            'present_division'   => 'বর্তমান বিভাগ',
            'present_district'   => 'বর্তমান জেলা',
            'present_thana'      => 'বর্তমান থানা',
            'present_post'       => 'বর্তমান ডাক',
            'present_village'    => 'বর্তমান গ্রাম',
            'financial_status'   => 'আর্থিক অবস্থা',
            'user_status'        => 'ইউজার সক্রিয়',
        ];
    }

    /**
     * Resolve the active institution.
     */
    private function institutionId(): int
    {
        $user = auth()->user();

        abort_unless($user, 401);

        $institutionId =
            session('active_institution_id')
            ?: session('institution_id')
            ?: $user->active_institution_id
            ?: $user->institution_id;

        abort_if(!$institutionId, 403, 'প্রতিষ্ঠান নির্বাচন করা হয়নি।');

        return (int) $institutionId;
    }

    /**
     * Normalise the columns of a saved report to a plain array.
     */
    private function reportColumnKeys($report): array
    {
        $columns = $report->columns;

        if (is_string($columns)) {
            $columns = json_decode($columns, true);
        }

        return is_array($columns) ? array_values($columns) : [];
    }

    /**
     * Main Data Export page.
     */
    public function index(Request $request)
    {
        $institutionId = $this->institutionId();

        $institution = Madrasa::find($institutionId);

        $institutionName = $institution?->display_name
            ?? $institution?->name
            ?? 'মাদরাসা';

        // Saved reports (current institution + current user)
        $reports = DataExportReport::query()
            ->where('institution_id', $institutionId)
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        $reportsData = $reports->map(function ($report) {
            return [
                'id'      => $report->id,
                'name'    => $report->report_name,
                'columns' => $this->reportColumnKeys($report),
            ];
        })->values();

        // Custom columns
        $customColumns = DataExportCustomColumn::query()
            ->where('institution_id', $institutionId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $academicYears = AcademicYear::query()
            ->where('institution_id', $institutionId)
            ->orderByDesc('id')
            ->get();

        $classes = Classes::query()
            ->where('institution_id', $institutionId)
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        $sections = Section::query()
            ->where('institution_id', $institutionId)
            ->orderBy('id')
            ->get();

        // Selected report
        $selectedReport = null;

        if ($request->filled('report_id')) {
            $selectedReport = DataExportReport::query()
                ->where('institution_id', $institutionId)
                ->where('user_id', auth()->id())
                ->findOrFail($request->integer('report_id'));
        }

        /*
        |--------------------------------------------------------------------------
        | Report data (only when a report is selected)
        |--------------------------------------------------------------------------
        */

        $reportMeta = null;
        $reportRows = [];

        if ($selectedReport) {

            $standard   = $this->standardColumns();
            $customById = $customColumns->keyBy('id');

            // Column meta
            $columnsMeta = [];

            foreach ($this->reportColumnKeys($selectedReport) as $key) {

                if (isset($standard[$key])) {
                    $columnsMeta[] = [
                        'key'      => $key,
                        'label'    => $standard[$key],
                        'kind'     => 'standard',
                        'input'    => null,
                        'customId' => null,
                    ];
                    continue;
                }

                if (preg_match('/^custom_(\d+)$/', $key, $m)) {
                    $custom = $customById->get((int) $m[1]);

                    if ($custom) {
                        $columnsMeta[] = [
                            'key'      => $key,
                            'label'    => $custom->name,
                            'kind'     => 'custom',
                            'input'    => $custom->type,
                            'customId' => $custom->id,
                        ];
                    }
                }
            }

            // Students
            $query = Student::query()
                ->with([
                    'user',
                    'class',
                    'section',
                    'academicYear',
                    'admissions' => function ($q) use ($institutionId) {
                        $q->where('institution_id', $institutionId)
                            ->orderByDesc('id');
                    },
                ])
                ->where('institution_id', $institutionId);

            if ($request->filled('academic_year_id')) {
                $query->where('academic_year_id', $request->integer('academic_year_id'));
            }

            if ($request->filled('section_id')) {
                $query->where('section_id', $request->integer('section_id'));
            }

            if ($request->filled('class_id')) {
                $query->where('class_id', $request->integer('class_id'));
            }

            if ($request->filled('status')) {
                $status = $request->input('status');

                // accepts active / inactive (and old 1 / 0 values)
                if ($status === '1') {
                    $status = 'active';
                } elseif ($status === '0') {
                    $status = 'inactive';
                }

                $query->where('status', $status);
            }

            if ($request->filled('admission_type')) {
                $admissionType = $request->input('admission_type');

                $query->whereHas('admissions', function ($q) use ($institutionId, $admissionType) {
                    $q->where('institution_id', $institutionId)
                        ->where('admission_type', $admissionType);
                });
            }

            if ($request->filled('residence_status')) {
                $residenceStatus = $request->input('residence_status');

                $query->whereHas('admissions', function ($q) use ($institutionId, $residenceStatus) {
                    $q->where('institution_id', $institutionId)
                        ->where('residence_status', $residenceStatus);
                });
            }

            $students = $query->orderBy('id')->get();

            // Custom values (loaded in one query)
            $customIds = collect($columnsMeta)
                ->where('kind', 'custom')
                ->pluck('customId')
                ->all();

            $valueMap = [];

            if (!empty($customIds) && $students->isNotEmpty()) {
                StudentCustomFieldValue::query()
                    ->where('institution_id', $institutionId)
                    ->whereIn('student_id', $students->pluck('id'))
                    ->whereIn('custom_column_id', $customIds)
                    ->get()
                    ->each(function ($v) use (&$valueMap) {
                        $yearKey = (string) ($v->academic_year_id ?? 'null');
                        $valueMap[$v->student_id][$v->custom_column_id][$yearKey] = $v->value;
                    });
            }

            foreach ($students as $student) {
                $reportRows[] = $this->buildRow($student, $columnsMeta, $valueMap);
            }

            $reportMeta = [
                'id'      => $selectedReport->id,
                'name'    => $selectedReport->report_name,
                'columns' => $columnsMeta,
            ];
        }

        return view('admin.students.data-export', [
            'reports'         => $reports,
            'reportsData'     => $reportsData,
            'customColumns'   => $customColumns,
            'standardColumns' => $this->standardColumns(),
            'academicYears'   => $academicYears,
            'classes'         => $classes,
            'sections'        => $sections,
            'selectedReport'  => $selectedReport,
            'reportMeta'      => $reportMeta,
            'reportRows'      => $reportRows,
            'institutionId'   => $institutionId,
            'institutionName' => $institutionName,
        ]);
    }

    /**
     * Build one table row for a student.
     */
    private function buildRow(Student $student, array $columnsMeta, array $valueMap): array
    {
        $user      = $student->user;
        $admission = $student->admissions->first();

        $genderLabels = [
            'male'   => 'পুরুষ',
            'female' => 'মহিলা',
            'other'  => 'অন্যান্য',
        ];

        $residenceLabels = [
            'resident'     => 'আবাসিক',
            'non-resident' => 'অনাবাসিক',
            'daycare'      => 'ডে-কেয়ার',
            'nightcare'    => 'নাইট-কেয়ার',
        ];

        $typeLabels = [
            'new' => 'নতুন',
            'old' => 'পুরাতন',
        ];

        $financialLabels = [
            'solvent'   => 'সচ্ছল',
            'insolvent' => 'অসচ্ছল',
            'orphan'    => 'এতিম',
            'helpless'  => 'অসহায়',
        ];

        $dob = '';

        if ($user?->date_of_birth) {
            try {
                $dob = Carbon::parse($user->date_of_birth)->format('d-m-Y');
            } catch (\Throwable $e) {
                $dob = (string) $user->date_of_birth;
            }
        }

        $standardValues = [
            'photo'              => $user?->photo ? asset('storage/' . $user->photo) : '',
            'student_id'         => $user?->institution_user_id ?: $student->student_id,
            'name'               => $user?->name_bn ?: $user?->name,
            'father_name'        => $user?->father_name,
            'mother_name'        => $user?->mother_name,
            'phone'              => $user?->phone,
            'phone2'             => $user?->phone2,
            'email'              => $user?->email,
            'academic_year'      => $student->academicYear?->name_bn ?: $student->academicYear?->name,
            'class'              => $student->class?->name_bn ?: $student->class?->name,
            'section'            => $student->section?->name_bn ?: $student->section?->name,
            'admission_no'       => $admission?->admission_no,
            'gender'             => $genderLabels[$user?->gender] ?? $user?->gender,
            'residence'          => $residenceLabels[$admission?->residence_status] ?? $admission?->residence_status,
            'admission_type'     => $typeLabels[$admission?->admission_type] ?? $admission?->admission_type,
            'date_of_birth'      => $dob,
            'national_id'        => $user?->national_id ?: $user?->birth_registration_no,
            'blood_group'        => $user?->blood_group,
            'permanent_division' => $user?->permanent_division,
            'permanent_district' => $user?->permanent_district,
            'permanent_thana'    => $user?->permanent_thana,
            'permanent_post'     => $user?->permanent_post,
            'permanent_village'  => $user?->permanent_village,
            'present_division'   => $user?->present_division,
            'present_district'   => $user?->present_district,
            'present_thana'      => $user?->present_thana,
            'present_post'       => $user?->present_post,
            'present_village'    => $user?->present_village,
            'financial_status'   => $financialLabels[$admission?->financial_status] ?? $admission?->financial_status,
            'user_status'        => $user?->status ? 'সক্রিয়' : 'নিষ্ক্রিয়',
        ];

        $cells = [];

        foreach ($columnsMeta as $column) {

            if ($column['kind'] === 'custom') {
                $byYear  = $valueMap[$student->id][$column['customId']] ?? [];
                $yearKey = (string) ($student->academic_year_id ?? 'null');

                $cells[$column['key']] = (string) ($byYear[$yearKey] ?? $byYear['null'] ?? '');
                continue;
            }

            $cells[$column['key']] = (string) ($standardValues[$column['key']] ?? '');
        }

        return [
            'id'               => $student->id,
            'academic_year_id' => $student->academic_year_id,
            'cells'            => $cells,
        ];
    }

    /**
     * Create or update a report configuration.
     */
    public function store(Request $request)
    {
        $institutionId = $this->institutionId();

        $validated = $request->validate([
            'report_name' => ['required', 'string', 'max:150'],
            'columns'     => ['required', 'array', 'min:1', 'max:10'],
            'columns.*'   => ['required', 'string', 'max:50'],
            'report_id'   => ['nullable', 'integer'],
        ], [
            'report_name.required' => 'রিপোর্টের নাম লিখুন।',
            'columns.required'     => 'অন্তত একটি কলাম নির্বাচন করুন।',
            'columns.max'          => 'সর্বোচ্চ ১০টি কলাম নির্বাচন করা যাবে।',
        ]);

        $columns = array_values(array_unique($validated['columns']));

        if (count($columns) > 10) {
            throw ValidationException::withMessages([
                'columns' => 'সর্বোচ্চ ১০টি আলাদা কলাম নির্বাচন করা যাবে।',
            ]);
        }

        $standardKeys = array_keys($this->standardColumns());

        foreach ($columns as $columnKey) {
            if (in_array($columnKey, $standardKeys, true)) {
                continue;
            }

            if (preg_match('/^custom_(\d+)$/', $columnKey, $matches)) {
                $exists = DataExportCustomColumn::query()
                    ->where('institution_id', $institutionId)
                    ->where('is_active', true)
                    ->whereKey((int) $matches[1])
                    ->exists();

                if ($exists) {
                    continue;
                }
            }

            throw ValidationException::withMessages([
                'columns' => 'নির্বাচিত কলামের মধ্যে অবৈধ কলাম রয়েছে।',
            ]);
        }

        if ($request->filled('report_id')) {
            $report = DataExportReport::query()
                ->where('institution_id', $institutionId)
                ->where('user_id', auth()->id())
                ->findOrFail($request->integer('report_id'));

            $report->update([
                'report_name' => $validated['report_name'],
                'columns'     => $columns,
            ]);
        } else {
            $report = DataExportReport::create([
                'institution_id' => $institutionId,
                'user_id'        => auth()->id(),
                'report_name'    => $validated['report_name'],
                'columns'        => $columns,
            ]);
        }

        return redirect()
            ->route('dashboard.students.data-export', [
                'report_id' => $report->id,
            ])
            ->with('success', 'রিপোর্ট কনফিগারেশন সংরক্ষণ করা হয়েছে।');
    }

    /**
     * Create an institution-specific custom column.
     */
    public function storeCustomColumn(Request $request)
    {
        $institutionId = $this->institutionId();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:text,number,grade,textarea'],
        ], [
            'name.required' => 'নতুন কলামের নাম লিখুন।',
        ]);

        $baseSlug = Str::slug($validated['name']);

        if ($baseSlug === '') {
            $baseSlug = 'custom-field';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (
            DataExportCustomColumn::query()
                ->where('institution_id', $institutionId)
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $column = DataExportCustomColumn::create([
            'institution_id' => $institutionId,
            'name'           => $validated['name'],
            'slug'           => $slug,
            'type'           => $validated['type'],
            'is_active'      => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'নতুন কলাম তৈরি হয়েছে।',
            'column'  => [
                'id'   => $column->id,
                'key'  => 'custom_' . $column->id,
                'name' => $column->name,
                'type' => $column->type,
            ],
        ]);
    }

    /**
     * Delete a custom column.
     */
    public function destroyCustomColumn(Request $request, DataExportCustomColumn $column)
    {
        $institutionId = $this->institutionId();

        abort_unless(
            (int) $column->institution_id === $institutionId,
            403,
            'এই কাস্টম কলামটি মুছে ফেলার অনুমতি নেই।'
        );

        $columnKey = 'custom_' . $column->id;

        $usedInReports = DataExportReport::query()
            ->where('institution_id', $institutionId)
            ->get()
            ->contains(function ($report) use ($columnKey) {
                return in_array($columnKey, $this->reportColumnKeys($report), true);
            });

        if ($usedInReports) {
            $message = 'এই কাস্টম কলামটি সংরক্ষিত রিপোর্টে ব্যবহার করা হচ্ছে। '
                . 'আগে সংশ্লিষ্ট রিপোর্ট থেকে কলামটি সরিয়ে নিন।';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return back()->with('error', $message);
        }

        // Remove saved student values of this column too
        StudentCustomFieldValue::query()
            ->where('institution_id', $institutionId)
            ->where('custom_column_id', $column->id)
            ->delete();

        $column->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'কাস্টম কলাম সফলভাবে মুছে ফেলা হয়েছে।',
                'id'      => $column->id,
            ]);
        }

        return back()->with('success', 'কাস্টম কলাম সফলভাবে মুছে ফেলা হয়েছে।');
    }

    /**
     * Save an individual student's custom value.
     */
    public function saveStudentValue(Request $request)
    {
        $institutionId = $this->institutionId();

        $validated = $request->validate([
            'student_id'       => ['required', 'integer'],
            'custom_column_id' => ['required', 'integer'],
            'academic_year_id' => ['nullable', 'integer'],
            'value'            => ['nullable', 'string', 'max:10000'],
        ]);

        $student = Student::query()
            ->where('institution_id', $institutionId)
            ->findOrFail($validated['student_id']);

        $customColumn = DataExportCustomColumn::query()
            ->where('institution_id', $institutionId)
            ->where('is_active', true)
            ->findOrFail($validated['custom_column_id']);

        $academicYearId = $validated['academic_year_id']
            ?? $student->academic_year_id;

        if ($academicYearId) {
            AcademicYear::query()
                ->where('institution_id', $institutionId)
                ->findOrFail($academicYearId);
        }

        StudentCustomFieldValue::updateOrCreate(
            [
                'institution_id'   => $institutionId,
                'student_id'       => $student->id,
                'academic_year_id' => $academicYearId,
                'custom_column_id' => $customColumn->id,
            ],
            [
                'value' => $validated['value'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'শিক্ষার্থীর তথ্য সংরক্ষণ হয়েছে।',
        ]);
    }

    /**
     * Delete a saved report.
     */
    public function destroy(Request $request, $id)
    {
        $report = DataExportReport::query()
            ->where('institution_id', $this->institutionId())
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $report->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'রিপোর্ট মুছে ফেলা হয়েছে।',
            ]);
        }

        return redirect()
            ->route('dashboard.students.data-export')
            ->with('success', 'রিপোর্ট মুছে ফেলা হয়েছে।');
    }

    /**
     * Clear all reports created by the current user in this institution.
     */
    public function clearAll(Request $request)
    {
        DataExportReport::query()
            ->where('institution_id', $this->institutionId())
            ->where('user_id', auth()->id())
            ->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'আপনার সব রিপোর্ট মুছে ফেলা হয়েছে।',
            ]);
        }

        return redirect()
            ->route('dashboard.students.data-export')
            ->with('success', 'আপনার সব রিপোর্ট মুছে ফেলা হয়েছে।');
    }
}