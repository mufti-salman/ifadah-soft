<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AcademicYearController extends Controller
{
    /**
     * Resolve the currently authorized institution.
     */
    private function institutionId(): int
    {
        $user = auth()->user();

        $institutionId = $user?->active_institution_id;

        if (!$institutionId && app()->bound('institution_id')) {
            $institutionId = app('institution_id');
        }

        abort_unless(
            $user && $institutionId,
            403,
            'প্রতিষ্ঠান নির্ধারণ করা যায়নি।'
        );

        return (int) $institutionId;
    }

    /**
     * Retrieve an academic year belonging to the current institution.
     */
    private function scopedYear(int $id): AcademicYear
    {
        return AcademicYear::query()
            ->where('institution_id', $this->institutionId())
            ->findOrFail($id);
    }

    /**
     * Resolve the database name.
     *
     * English name has priority.
     * If English is empty, use Bengali.
     * If Bengali is also empty, use Arabic.
     */
 /**
 * Resolve academic year names independently.
 *
 * English, Bengali and Arabic names must remain separate.
 * The database name column is NOT NULL, so use Bengali or Arabic
 * as a database fallback only when English is empty.
 */
private function resolveName(array $data): array
{
    $englishName = filled($data['name'] ?? null)
        ? trim($data['name'])
        : null;

    $bengaliName = filled($data['name_bn'] ?? null)
        ? trim($data['name_bn'])
        : null;

    $arabicName = filled($data['name_ar'] ?? null)
        ? trim($data['name_ar'])
        : null;

    $databaseName = $englishName
        ?? $bengaliName
        ?? $arabicName;

    if (blank($databaseName)) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'name_bn' => 'অন্তত বাংলা, English অথবা Arabic নাম লিখুন।',
        ]);
    }

    return [
        // Keep the database NOT NULL requirement satisfied.
        'name' => $databaseName,

        // Preserve each language independently.
        'name_bn' => $bengaliName,
        'name_ar' => $arabicName,
    ];
}

    /**
     * Check whether the resolved name already exists
     * within the current institution.
     */
    private function ensureUniqueName(
        string $name,
        int $institutionId,
        ?int $ignoreId = null
    ): void {
        $query = AcademicYear::query()
            ->where('institution_id', $institutionId)
            ->where('name', $name);

        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'name_bn' => 'এই নামে শিক্ষাবর্ষ আগে থেকেই রয়েছে।',
            ]);
        }
    }

    /**
     * Display academic years for the current institution.
     */
    public function index()
    {
        $institutionId = $this->institutionId();

        $academicYears = AcademicYear::query()
            ->where('institution_id', $institutionId)
            ->with([
                'createdBy',
                'updatedBy',
                'parentYear',
            ])
            ->orderByDesc('id')
            ->get();

        return view(
            'admin.academic-years.index',
            compact('academicYears')
        );
    }

    /**
     * Redirect creation to the listing page.
     */
    public function create()
    {
        return redirect()->route(
            'dashboard.academic-years.index'
        );
    }

    /**
     * Show one academic year.
     */
    public function show($id)
    {
        $year = $this->scopedYear((int) $id);

        return redirect()
            ->route('dashboard.academic-years.index')
            ->with('success', 'শিক্ষাবর্ষ: ' . $year->name);
    }

    /**
     * Validation rules.
     */
    private function rules(
        int $institutionId,
        ?int $ignoreId = null
    ): array {
        $nameRule = Rule::unique('academic_years', 'name')
            ->where('institution_id', $institutionId);

        $codeRule = Rule::unique('academic_years', 'code')
            ->where('institution_id', $institutionId);

        if ($ignoreId !== null) {
            $nameRule->ignore($ignoreId);
            $codeRule->ignore($ignoreId);
        }

        return [
            // English name is optional.
            'name' => [
                'nullable',
                'string',
                'max:100',
                $nameRule,
            ],

            'name_bn' => [
                'nullable',
                'string',
                'max:100',
            ],

            'name_ar' => [
                'nullable',
                'string',
                'max:100',
            ],

            // Legacy fields are retained.
            'code' => [
                'nullable',
                'string',
                'max:50',
                $codeRule,
            ],

            'start_date' => [
                'nullable',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'start_month_id' => [
                'nullable',
                'integer',
                Rule::exists('institution_months', 'id')
                    ->where('institution_id', $institutionId)
                    ->where('is_active', true),
            ],

            'semester_count' => [
                'nullable',
                'integer',
                'min:1',
                'max:12',
            ],

            'is_current' => [
                'sometimes',
                'boolean',
            ],

            'status' => [
                'sometimes',
                Rule::in(['active', 'inactive']),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'parent_year_id' => [
                'nullable',
                'integer',
                Rule::exists('academic_years', 'id')
                    ->where('institution_id', $institutionId),
            ],
        ];
    }

    /**
     * Create an academic year.
     */
    public function store(Request $request)
    {
        $institutionId = $this->institutionId();

        $data = $request->validate(
            $this->rules($institutionId)
        );

        // Resolve the required database name from available languages.
        $resolvedNames = $this->resolveName($data);

        $data = array_merge($data, $resolvedNames);

        $this->ensureUniqueName(
            $data['name'],
            $institutionId
        );

        // Protect ownership and audit fields.
        unset(
            $data['institution_id'],
            $data['created_by'],
            $data['updated_by']
        );

        $data['institution_id'] = $institutionId;
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        // Preserve compatibility with existing database columns.
        $data['code'] = $data['code'] ?? null;
        $data['start_month_id'] = $data['start_month_id'] ?? null;
        $data['start_date'] = $data['start_date'] ?? null;
        $data['end_date'] = $data['end_date'] ?? null;
        $data['parent_year_id'] = $data['parent_year_id'] ?? null;
        $data['description'] = $data['description'] ?? null;

        $data['semester_count'] = $data['semester_count'] ?? 2;
        $data['status'] = $data['status'] ?? 'active';
        $data['is_current'] = $request->boolean('is_current');

        if (
            $data['is_current']
            && $data['status'] !== 'active'
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'is_current' =>
                        'নিষ্ক্রিয় শিক্ষাবর্ষকে বর্তমান করা যাবে না।',
                ]);
        }

        DB::transaction(function () use (
            $data,
            $institutionId
        ) {
            if ($data['is_current']) {
                AcademicYear::query()
                    ->where('institution_id', $institutionId)
                    ->where('is_current', true)
                    ->update([
                        'is_current' => false,
                    ]);
            }

            AcademicYear::create($data);
        });

        return redirect()
            ->route('dashboard.academic-years.index')
            ->with('success', 'শিক্ষাবর্ষ তৈরি হয়েছে।');
    }

    /**
     * Create an academic year through AJAX.
     */
    public function storeAjax(Request $request)
    {
        $institutionId = $this->institutionId();

        $data = $request->validate([
            'name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'name_bn' => [
                'nullable',
                'string',
                'max:100',
            ],

            'name_ar' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        $resolvedNames = $this->resolveName($data);

        $this->ensureUniqueName(
            $resolvedNames['name'],
            $institutionId
        );

        $academicYear = AcademicYear::create([
            'institution_id' => $institutionId,

            'name' => $resolvedNames['name'],
            'name_bn' => $resolvedNames['name_bn'],
            'name_ar' => $resolvedNames['name_ar'],

            'code' => null,
            'start_month_id' => null,
            'start_date' => null,
            'end_date' => null,
            'semester_count' => 2,
            'description' => null,
            'parent_year_id' => null,

            'is_current' => false,
            'status' => 'active',

            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'শিক্ষাবর্ষ যোগ করা হয়েছে!',
            'data' => $academicYear,
        ]);
    }

    /**
     * Fetch an academic year for editing.
     */
    public function edit($id)
    {
        $year = $this->scopedYear((int) $id);

        return response()->json($year);
    }

    /**
     * Update an academic year.
     */
    public function update(Request $request, $id)
    {
        $institutionId = $this->institutionId();

        $year = $this->scopedYear((int) $id);

        $data = $request->validate(
            $this->rules($institutionId, $year->id)
        );

        /*
         * If the form submits the language fields, resolve the
         * database name again. Otherwise, preserve existing values.
         */
        $hasNameInput =
            $request->exists('name')
            || $request->exists('name_bn')
            || $request->exists('name_ar');

        if ($hasNameInput) {
            $nameInputs = [
                'name' => $data['name'] ?? null,
                'name_bn' => $data['name_bn'] ?? null,
                'name_ar' => $data['name_ar'] ?? null,
            ];

            $resolvedNames = $this->resolveName($nameInputs);

            $this->ensureUniqueName(
                $resolvedNames['name'],
                $institutionId,
                $year->id
            );

            $data = array_merge($data, $resolvedNames);
        }

        // Never accept ownership or creation audit fields from the form.
        unset(
            $data['institution_id'],
            $data['created_by'],
            $data['updated_by']
        );

        // Checkbox is false when unchecked in a full edit form.
        $data['is_current'] = $request->boolean('is_current');
        $data['updated_by'] = auth()->id();

        $newStatus = $data['status'] ?? $year->status;

        if (
            $data['is_current']
            && $newStatus !== 'active'
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'is_current' =>
                        'নিষ্ক্রিয় শিক্ষাবর্ষকে বর্তমান করা যাবে না।',
                ]);
        }

        DB::transaction(function () use (
            $data,
            $institutionId,
            $year
        ) {
            if ($data['is_current']) {
                AcademicYear::query()
                    ->where('institution_id', $institutionId)
                    ->where('id', '!=', $year->id)
                    ->where('is_current', true)
                    ->update([
                        'is_current' => false,
                    ]);
            }

            $year->update($data);
        });

        return redirect()
            ->route('dashboard.academic-years.index')
            ->with('success', 'শিক্ষাবর্ষ আপডেট হয়েছে।');
    }

    /**
     * Set an academic year as current.
     */
    public function setCurrent($id)
    {
        $institutionId = $this->institutionId();

        $year = $this->scopedYear((int) $id);

        abort_unless(
            $year->status === 'active',
            422,
            'নিষ্ক্রিয় শিক্ষাবর্ষকে বর্তমান করা যাবে না।'
        );

        DB::transaction(function () use (
            $institutionId,
            $year
        ) {
            AcademicYear::query()
                ->where('institution_id', $institutionId)
                ->where('id', '!=', $year->id)
                ->update([
                    'is_current' => false,
                ]);

            $year->update([
                'is_current' => true,
                'updated_by' => auth()->id(),
            ]);
        });

        return back()->with(
            'success',
            'বর্তমান শিক্ষাবর্ষ পরিবর্তন করা হয়েছে।'
        );
    }

    /**
     * Toggle the academic year status.
     */
    public function toggleStatus($id)
    {
        $year = $this->scopedYear((int) $id);

        $newStatus = $year->status === 'active'
            ? 'inactive'
            : 'active';

        if (
            $newStatus === 'inactive'
            && $year->is_current
        ) {
            return back()->with(
                'error',
                'বর্তমান শিক্ষাবর্ষ নিষ্ক্রিয় করার আগে অন্য শিক্ষাবর্ষকে বর্তমান করুন।'
            );
        }

        $year->update([
            'status' => $newStatus,
            'updated_by' => auth()->id(),
        ]);

        return back()->with(
            'success',
            'শিক্ষাবর্ষের স্ট্যাটাস পরিবর্তন করা হয়েছে।'
        );
    }

    /**
     * Delete an academic year belonging to the current institution.
     */
    public function destroy($id)
    {
        $year = $this->scopedYear((int) $id);

        if ($year->is_current) {
            return back()->with(
                'error',
                'বর্তমান শিক্ষাবর্ষ ডিলিট করার আগে অন্য শিক্ষাবর্ষকে বর্তমান করুন।'
            );
        }

        if ($year->childYears()->exists()) {
            return back()->with(
                'error',
                'এই শিক্ষাবর্ষের অধীনে অন্য শিক্ষাবর্ষ আছে। আগে সেগুলো সরান।'
            );
        }

        $year->delete();

        return back()->with(
            'success',
            'শিক্ষাবর্ষ ডিলিট হয়েছে।'
        );
    }
}
