<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Madrasa;
use App\Models\User;
use App\Models\UserType;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class UserReportController extends Controller
{
    /**
     * Get active institution ID.
     */
    private function institutionId(): int
    {
        $institutionId = auth()->user()?->active_institution_id;

        abort_if(
            !$institutionId,
            403,
            'প্রতিষ্ঠান নির্বাচন করা হয়নি।'
        );

        return (int) $institutionId;
    }

    /**
     * Get allowed user types.
     *
     * Allowed:
     * student
     * teacher
     * donor_member
     */
    private function getUserTypes(): Collection
    {
        return UserType::query()
            ->whereIn('slug', [
                'student',
                'teacher',
                'donor_member',
            ])
            ->orderBy('id')
            ->get();
    }

    /**
     * Get allowed user type IDs.
     */
    private function getAllowedUserTypeIds(): array
    {
        return $this->getUserTypes()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    /**
     * Get Bengali user type label.
     */
    private function getUserTypeLabel($userType): string
    {
        if (!$userType) {
            return 'ইউজার';
        }

        return match ($userType->slug) {
            'student' => 'শিক্ষার্থী',
            'teacher' => 'শিক্ষক/স্টাফ',
            'donor_member' => 'দাতা সদস্য',

            default => (
                $userType->name_bn
                ?? $userType->display_name
                ?? $userType->name
                ?? 'ইউজার'
            ),
        };
    }

    /**
     * User reports index.
     */
    public function index()
    {
        $institutionId = $this->institutionId();

        $institution = Madrasa::findOrFail($institutionId);

        $userTypes = $this->getUserTypes();

        return view('admin.user-reports.index', [

            'institution' => $institution,

            // User type dropdown
            'userTypes' => $userTypes,

            'reportType' => 1,
            'reportTitle' => '',

            'userTypeId' => '',
            'gender' => '',
            'status' => '',

            'userIdFrom' => '',
            'userIdTo' => '',

            'users' => collect(),
            'students' => collect(),

            'studentsWithoutAdmission' => collect(),

            'selectedStudent' => null,

            'statistics' => [],

            'byRole' => collect(),

            'validated' => [],

            'previewGenerated' => false,
        ]);
    }

    /**
     * Generate report preview.
     */
    public function preview(Request $request)
    {
        $institutionId = $this->institutionId();

        $institution = Madrasa::findOrFail($institutionId);

        $userTypes = $this->getUserTypes();

        $allowedUserTypeIds = $userTypes
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'report_type' => [
                'required',
                'integer',
                'in:1,2,3,4',
            ],

            'user_type_id' => [
                'nullable',
                'integer',
                Rule::in($allowedUserTypeIds),
            ],

            'gender' => [
                'nullable',
                'in:male,female,other,all',
            ],

            'status' => [
                'nullable',
                'in:active,inactive,all',
            ],

            'user_id_from' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'user_id_to' => [
                'nullable',
                'integer',
                'min:1',
            ],

        ]);

        /*
        |--------------------------------------------------------------------------
        | Filter values
        |--------------------------------------------------------------------------
        */

        $reportType = (int) $validated['report_type'];

        $userTypeId = $validated['user_type_id'] ?? '';

        $gender = $validated['gender'] ?? '';

        $status = $validated['status'] ?? '';

        $userIdFrom = $validated['user_id_from'] ?? '';

        $userIdTo = $validated['user_id_to'] ?? '';

        /*
        |--------------------------------------------------------------------------
        | Report titles
        |--------------------------------------------------------------------------
        */

        $reportTitles = [

            1 => 'ইউজার তালিকা রিপোর্ট',

            2 => 'ইউজার পরিসংখ্যান রিপোর্ট',

            3 => 'ভর্তি ফরম',

            4 => 'ভর্তি হয়নি এমন শিক্ষার্থী',

        ];

        $reportTitle = $reportTitles[$reportType] ?? '';

        /*
        |--------------------------------------------------------------------------
        | Default data
        |--------------------------------------------------------------------------
        */

        $users = collect();

        $students = collect();

        $studentsWithoutAdmission = collect();

        $selectedStudent = null;

        $statistics = [];

        $byRole = collect();

        /*
        |--------------------------------------------------------------------------
        | Report 1: User List
        |--------------------------------------------------------------------------
        */

        if ($reportType === 1) {

            $query = User::query()

                ->where('institution_id', $institutionId)

                ->whereIn(
                    'user_type_id',
                    $allowedUserTypeIds
                )

                ->with([
                    'userType',
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
             * Attach Bengali user type label.
             */
            foreach ($users as $user) {

                $user->user_type_label = $this->getUserTypeLabel(
                    $user->userType
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

                ->whereIn(
                    'user_type_id',
                    $allowedUserTypeIds
                );

            $this->applyUserFilters(
                $query,
                $validated,
                $institutionId
            );

            $users = $query->get();

            /*
             * Basic statistics.
             */
            $totalUsers = $users->count();

            $activeUsers = $users
                ->where('status', 1)
                ->count();

            $inactiveUsers = $users
                ->where('status', 0)
                ->count();

            $maleUsers = $users
                ->where('gender', 'male')
                ->count();

            $femaleUsers = $users
                ->where('gender', 'female')
                ->count();

            $statistics = [

                'total' => $totalUsers,

                'active' => $activeUsers,

                'inactive' => $inactiveUsers,

                'male' => $maleUsers,

                'female' => $femaleUsers,

            ];

            /*
             * Group users by user type.
             */
            $typeCounts = $users->groupBy('user_type_id');

            $byRole = $userTypes->map(function ($type) use ($typeCounts) {

                $typeUsers = $typeCounts->get(
                    $type->id,
                    collect()
                );

                return (object) [

                    'id' => $type->id,

                    'name' => $type->name ?? '',

                    'name_bn' => $this->getUserTypeLabel($type),

                    'slug' => $type->slug ?? '',

                    'total' => $typeUsers->count(),

                ];

            });
        }

        /*
        |--------------------------------------------------------------------------
        | Report 3: Admission Form
        |--------------------------------------------------------------------------
        */

        if ($reportType === 3) {

            $students = Student::query()

                ->where('institution_id', $institutionId)

                ->with([
                    'user',
                ])

                ->orderBy('id')

                ->get();

            /*
             * Keep admission form blank by default.
             */
            $selectedStudent = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Report 4: Students Without Admission
        |--------------------------------------------------------------------------
        */

        if ($reportType === 4) {

            $query = Student::query()

                ->where('institution_id', $institutionId)

                ->whereHas('user', function ($q) use ($institutionId) {

                    $q->where('institution_id', $institutionId)

                        ->where('user_type_id', 1);

                })

                ->whereDoesntHave('admissions', function ($q) {

                    $q->where('status', 'active');

                })

                ->with([
                    'user',
                ]);

            /*
             * User type filter.
             *
             * This report only contains students.
             */
            if (
                !empty($userTypeId) &&
                (int) $userTypeId !== 1
            ) {

                $query->whereRaw('1 = 0');

            }

            /*
             * Gender filter.
             */
            if (!empty($gender) && $gender !== 'all') {

                $query->whereHas('user', function ($q) use (
                    $gender,
                    $institutionId
                ) {

                    $q->where('institution_id', $institutionId)

                        ->where('gender', $gender);

                });

            }

            /*
             * Status filter.
             */
            if (!empty($status) && $status !== 'all') {

                $statusValue = $status === 'active' ? 1 : 0;

                $query->whereHas('user', function ($q) use (
                    $statusValue,
                    $institutionId
                ) {

                    $q->where('institution_id', $institutionId)

                        ->where('status', $statusValue);

                });

            }

            /*
             * User ID range filter.
             */
            if (!empty($userIdFrom)) {

                $query->whereHas('user', function ($q) use (
                    $userIdFrom,
                    $institutionId
                ) {

                    $q->where('institution_id', $institutionId)

                        ->whereRaw(
                            'CAST(institution_user_id AS UNSIGNED) >= ?',
                            [(int) $userIdFrom]
                        );

                });

            }

            if (!empty($userIdTo)) {

                $query->whereHas('user', function ($q) use (
                    $userIdTo,
                    $institutionId
                ) {

                    $q->where('institution_id', $institutionId)

                        ->whereRaw(
                            'CAST(institution_user_id AS UNSIGNED) <= ?',
                            [(int) $userIdTo]
                        );

                });

            }

            $studentsWithoutAdmission = $query

                ->orderBy('id')

                ->get();

            $students = $studentsWithoutAdmission;
        }

        /*
        |--------------------------------------------------------------------------
        | Return report view
        |--------------------------------------------------------------------------
        */

        return view('admin.user-reports.index', [

            'institution' => $institution,

            // Important: Blade dropdown uses $userTypes
            'userTypes' => $userTypes,

            'reportType' => $reportType,

            'reportTitle' => $reportTitle,

            'userTypeId' => $userTypeId,

            'gender' => $gender,

            'status' => $status,

            'userIdFrom' => $userIdFrom,

            'userIdTo' => $userIdTo,

            'users' => $users,

            'students' => $students,

            'studentsWithoutAdmission' => $studentsWithoutAdmission,

            'selectedStudent' => $selectedStudent,

            'statistics' => $statistics,

            'byRole' => $byRole,

            'validated' => $validated,

            'previewGenerated' => true,

        ]);
    }

    /**
     * Apply common user filters.
     */
    private function applyUserFilters(
        $query,
        array $filters,
        int $institutionId
    ): void {

        /*
         * Institution restriction.
         */
        $query->where('institution_id', $institutionId);

        /*
         * Allowed user types.
         */
        $allowedUserTypeIds = $this->getAllowedUserTypeIds();

        $query->whereIn(
            'user_type_id',
            $allowedUserTypeIds
        );

        /*
         * User type filter.
         */
        if (!empty($filters['user_type_id'])) {

            $query->where(
                'user_type_id',
                (int) $filters['user_type_id']
            );

        }

        /*
         * Gender filter.
         */
        if (
            !empty($filters['gender']) &&
            $filters['gender'] !== 'all'
        ) {

            $query->where(
                'gender',
                $filters['gender']
            );

        }

        /*
         * Status filter.
         */
        if (
            !empty($filters['status']) &&
            $filters['status'] !== 'all'
        ) {

            $statusValue = $filters['status'] === 'active'
                ? 1
                : 0;

            $query->where(
                'status',
                $statusValue
            );

        }

        /*
         * Institution User ID - From.
         */
        if (!empty($filters['user_id_from'])) {

            $query->whereRaw(
                'CAST(institution_user_id AS UNSIGNED) >= ?',
                [(int) $filters['user_id_from']]
            );

        }

        /*
         * Institution User ID - To.
         */
        if (!empty($filters['user_id_to'])) {

            $query->whereRaw(
                'CAST(institution_user_id AS UNSIGNED) <= ?',
                [(int) $filters['user_id_to']]
            );

        }
    }
}