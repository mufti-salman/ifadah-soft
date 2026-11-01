<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Madrasa;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UserReportController extends Controller
{
    /**
     * Get the currently selected institution ID.
     */
    private function institutionId(): int
    {
        $institutionId = auth()->user()?->active_institution_id;

        abort_unless(
            $institutionId,
            403,
            'প্রথমে একটি মাদরাসা নির্বাচন করুন।'
        );

        return (int) $institutionId;
    }

    /**
     * Get available user types.
     */
    private function getUserTypes(): Collection
    {
        $roles = Role::query()
            ->whereIn('slug', [
                'student',
                'user',
                'user_admin',
                'support_admin',
            ])
            ->get();

        return collect([
            (object) [
                'slug' => 'teacher',
                'name_bn' => 'শিক্ষক',
                'name' => 'Teacher',
            ],
            (object) [
                'slug' => 'guardian',
                'name_bn' => 'অভিভাবক',
                'name' => 'Guardian',
            ],
        ])
            ->merge($roles)
            ->unique('slug')
            ->values();
    }

    /**
     * Display report form and optional inline preview.
     */
    public function index()
    {
        $institutionId = $this->institutionId();

        $institution = Madrasa::query()
            ->findOrFail($institutionId);

        return view('admin.user-reports.index', [
            'institution' => $institution,
            'roles' => $this->getUserTypes(),

            'reportType' => null,
            'reportTitle' => '',
            'users' => collect(),
            'students' => collect(),
            'selectedStudent' => null,
            'statistics' => [],
            'validated' => [],
            'previewGenerated' => false,
        ]);
    }

    /**
     * Generate report and render it on the same page.
     */
    public function preview(Request $request)
    {
        $institutionId = $this->institutionId();

        $validated = $request->validate([
            'report_type' => [
                'required',
                'in:1,2,3,4',
            ],

            'user_type' => [
                'nullable',
                'in:student,teacher,guardian,user,user_admin,support_admin',
            ],

            'gender' => [
                'nullable',
                'in:male,female,all',
            ],

            'status' => [
                'nullable',
                'in:active,inactive,all',
            ],

            'user_id_from' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'user_id_to' => [
                'nullable',
                'integer',
                'min:0',
                'gte:user_id_from',
            ],
        ], [
            'report_type.required' => 'রিপোর্টের ধরন নির্বাচন করুন।',
            'report_type.in' => 'সঠিক রিপোর্টের ধরন নির্বাচন করুন।',
            'user_id_to.gte' => 'শেষ আইডি শুরুর আইডির সমান বা বড় হতে হবে।',
        ]);

        $institution = Madrasa::query()
            ->findOrFail($institutionId);

        $reportType = (int) $validated['report_type'];

        $reportTitles = [
            1 => 'আইডি সিরিয়াল ইউজার ডাটা তালিকা',
            2 => 'ইউজার পরিসংখ্যান',
            3 => 'শিক্ষার্থীর ভর্তির ফরম',
            4 => 'ভর্তি বিহীন শিক্ষার্থীর তালিকা',
        ];

        $reportTitle = $reportTitles[$reportType];

        $users = collect();
        $students = collect();
        $statistics = [];

        /*
        |--------------------------------------------------------------------------
        | Report 1: User Data List
        |--------------------------------------------------------------------------
        */

        if ($reportType === 1) {
            $query = User::query()
                ->where('institution_id', $institutionId)
                ->with([
                    'role',
                    'student',
                    'teacher',
                ]);

            $this->applyUserFilters(
                $query,
                $validated,
                $institutionId
            );

            $users = $query
                ->orderByRaw(
                    'CAST(institution_user_id AS UNSIGNED) ASC'
                )
                ->get();

            $guardianIds = Student::query()
                ->where('institution_id', $institutionId)
                ->whereNotNull('guardian_user_id')
                ->distinct()
                ->pluck('guardian_user_id')
                ->flip();

            foreach ($users as $user) {
                if ($user->student) {
                    $typeLabel = 'শিক্ষার্থী';
                } elseif ($user->teacher) {
                    $typeLabel = 'শিক্ষক';
                } elseif ($guardianIds->has($user->id)) {
                    $typeLabel = 'অভিভাবক';
                } else {
                    $typeLabel = $user->role?->name_bn
                        ?? $user->role?->name
                        ?? 'ব্যবহারকারী';
                }

                $user->setAttribute(
                    'report_type_label',
                    $typeLabel
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Report 2: User Statistics
        |--------------------------------------------------------------------------
        */

        if ($reportType === 2) {
            $query = User::query()
                ->where('institution_id', $institutionId)
                ->with('role');

            $this->applyUserFilters(
                $query,
                $validated,
                $institutionId
            );

            $filteredUsers = $query->get();

            $statistics = [
                'total' => $filteredUsers->count(),

                'active' => $filteredUsers
                    ->where('status', 1)
                    ->count(),

                'inactive' => $filteredUsers
                    ->where('status', 0)
                    ->count(),

                'male' => $filteredUsers
                    ->filter(fn ($user) => $user->gender === 'male')
                    ->count(),

                'female' => $filteredUsers
                    ->filter(fn ($user) => $user->gender === 'female')
                    ->count(),

                'by_role' => $filteredUsers
                    ->groupBy(function ($user) {
                        return $user->role?->name_bn
                            ?? $user->role?->name
                            ?? 'অন্যান্য';
                    })
                    ->map(
                        fn (Collection $group) => $group->count()
                    ),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Report 3: Blank Student Admission Form
        |--------------------------------------------------------------------------
        |
        | No student selection is required.
        | The blank admission form is rendered on the same page.
        |
        */

        if ($reportType === 3) {
            $students = collect();
        }

        /*
        |--------------------------------------------------------------------------
        | Report 4: Students Without Active Admission
        |--------------------------------------------------------------------------
        */

        if ($reportType === 4) {
            $query = Student::query()
                ->where('institution_id', $institutionId)
                ->whereHas('user', function (Builder $userQuery) use (
                    $institutionId
                ) {
                    $userQuery->where(
                        'institution_id',
                        $institutionId
                    );
                })
                ->whereDoesntHave('admissions', function (
                    Builder $admissionQuery
                ) {
                    $admissionQuery->where(
                        'status',
                        'active'
                    );
                })
                ->with('user')
                ->orderBy('id');

            if (
                !empty($validated['gender'])
                && $validated['gender'] !== 'all'
            ) {
                $gender = $validated['gender'];

                $query->whereHas('user', function (
                    Builder $userQuery
                ) use ($gender) {
                    $userQuery->where('gender', $gender);
                });
            }

            if (
                !empty($validated['status'])
                && $validated['status'] !== 'all'
            ) {
                $status = $validated['status'] === 'active'
                    ? 1
                    : 0;

                $query->whereHas('user', function (
                    Builder $userQuery
                ) use ($status) {
                    $userQuery->where('status', $status);
                });
            }

            $students = $query->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Return the same Blade page with inline preview data.
        |--------------------------------------------------------------------------
        */

        return view('admin.user-reports.index', [
            'institution' => $institution,
            'roles' => $this->getUserTypes(),

            'reportType' => $reportType,
            'reportTitle' => $reportTitle,
            'users' => $users,
            'students' => $students,
            'selectedStudent' => null,
            'statistics' => $statistics,
            'validated' => $validated,
            'previewGenerated' => true,
        ]);
    }

    /**
     * Apply filters to user queries.
     */
    private function applyUserFilters(
        Builder $query,
        array $filters,
        int $institutionId
    ): void {
        if (!empty($filters['user_type'])) {
            $userType = $filters['user_type'];

            if ($userType === 'teacher') {
                $query->whereHas('teacher');
            } elseif ($userType === 'guardian') {
                $query->whereIn(
                    'id',
                    Student::query()
                        ->select('guardian_user_id')
                        ->where('institution_id', $institutionId)
                        ->whereNotNull('guardian_user_id')
                );
            } elseif ($userType === 'student') {
                $query->whereHas('student');
            } else {
                $query->whereHas('role', function (
                    Builder $roleQuery
                ) use ($userType) {
                    $roleQuery->where('slug', $userType);
                });
            }
        }

        if (
            !empty($filters['gender'])
            && $filters['gender'] !== 'all'
        ) {
            $query->where(
                'gender',
                $filters['gender']
            );
        }

        if (
            !empty($filters['status'])
            && $filters['status'] !== 'all'
        ) {
            $status = $filters['status'] === 'active'
                ? 1
                : 0;

            $query->where('status', $status);
        }

        if (
            isset($filters['user_id_from'])
            && $filters['user_id_from'] !== ''
        ) {
            $query->whereRaw(
                'CAST(institution_user_id AS UNSIGNED) >= ?',
                [(int) $filters['user_id_from']]
            );
        }

        if (
            isset($filters['user_id_to'])
            && $filters['user_id_to'] !== ''
        ) {
            $query->whereRaw(
                'CAST(institution_user_id AS UNSIGNED) <= ?',
                [(int) $filters['user_id_to']]
            );
        }
    }
}