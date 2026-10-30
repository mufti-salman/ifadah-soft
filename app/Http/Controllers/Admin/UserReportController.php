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
     * Display the user report form.
     */
    public function index()
    {
        $institutionId = $this->institutionId();

        $institution = Madrasa::query()
            ->findOrFail($institutionId);

        $roles = Role::query()
            ->whereIn('slug', [
                'student',
                'user',
                'user_admin',
                'support_admin',
            ])
            ->get();

        $students = Student::query()
            ->where('institution_id', $institutionId)
            ->whereHas('user', function (Builder $query) use ($institutionId) {
                $query->where('institution_id', $institutionId);
            })
            ->with('user')
            ->orderByDesc('id')
            ->get();

        // Teacher and guardian are profile/relationship types,
        // so they are included separately from role records.
        $userTypes = collect([
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
        ])->merge($roles)->unique('slug')->values();

        return view('admin.user-reports.index', [
            'institution' => $institution,
            'roles' => $userTypes,
            'students' => $students,
        ]);
    }

    /**
     * Generate report preview.
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

            'student_id' => [
                'required_if:report_type,3',
                'nullable',
                'integer',
            ],
        ], [
            'report_type.required' => 'রিপোর্টের ধরন নির্বাচন করুন।',
            'report_type.in' => 'সঠিক রিপোর্টের ধরন নির্বাচন করুন।',
            'student_id.required_if' => 'ভর্তির ফরমের জন্য একজন শিক্ষার্থী নির্বাচন করুন।',
            'user_id_to.gte' => 'শেষ আইডি শুরুর আইডির সমান বা বড় হতে হবে।',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Institution Details
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Default Data
        |--------------------------------------------------------------------------
        */

        $users = collect();
        $students = collect();
        $selectedStudent = null;
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

            /*
             * Find guardian IDs without requiring a guardianStudents()
             * relationship on the User model.
             */
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

                $user->setAttribute('report_type_label', $typeLabel);
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
                    ->map(fn (Collection $group) => $group->count()),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Report 3: Student Admission Form
        |--------------------------------------------------------------------------
        */

        if ($reportType === 3) {
            $selectedStudent = Student::query()
                ->where('institution_id', $institutionId)
                ->whereKey((int) $validated['student_id'])
                ->whereHas('user', function (Builder $query) use ($institutionId) {
                    $query->where('institution_id', $institutionId);
                })
                ->with([
                    'user',
                    'class',
                    'academicYear',
                    'guardian',
                    'admissions' => function ($query) {
                        $query->latest('id');
                    },
                ])
                ->firstOrFail();
        }

        /*
        |--------------------------------------------------------------------------
        | Report 4: Students Without Active Admission
        |--------------------------------------------------------------------------
        */

        if ($reportType === 4) {
            $query = Student::query()
                ->where('institution_id', $institutionId)
                ->whereHas('user', function (Builder $query) use ($institutionId) {
                    $query->where('institution_id', $institutionId);
                })
                ->whereDoesntHave('admissions', function (Builder $query) {
                    $query->where('status', 'active');
                })
                ->with('user')
                ->orderBy('id');

            if (
                !empty($validated['gender'])
                && $validated['gender'] !== 'all'
            ) {
                $gender = $validated['gender'];

                $query->whereHas('user', function (Builder $userQuery) use ($gender) {
                    $userQuery->where('gender', $gender);
                });
            }

            if (
                !empty($validated['status'])
                && $validated['status'] !== 'all'
            ) {
                $status = $validated['status'] === 'active' ? 1 : 0;

                $query->whereHas('user', function (Builder $userQuery) use ($status) {
                    $userQuery->where('status', $status);
                });
            }

            $students = $query->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Return Preview
        |--------------------------------------------------------------------------
        */

        return view('admin.user-reports.preview', [
            'institution' => $institution,
            'reportType' => $reportType,
            'reportTitle' => $reportTitle,
            'users' => $users,
            'students' => $students,
            'selectedStudent' => $selectedStudent,
            'statistics' => $statistics,
            'validated' => $validated,
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
        /*
         * User type.
         */
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
                $query->whereHas('role', function (Builder $roleQuery) use ($userType) {
                    $roleQuery->where('slug', $userType);
                });
            }
        }

        /*
         * Gender.
         */
        if (
            !empty($filters['gender'])
            && $filters['gender'] !== 'all'
        ) {
            $query->where('gender', $filters['gender']);
        }

        /*
         * User status.
         */
        if (
            !empty($filters['status'])
            && $filters['status'] !== 'all'
        ) {
            $status = $filters['status'] === 'active' ? 1 : 0;

            $query->where('status', $status);
        }

        /*
         * Institution user ID range.
         */
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