<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserCodeSetting;
use App\Models\UserType;
use App\Models\Student;
use App\Models\Role;
use App\Models\Madrasa;
use App\Models\Classes;
use App\Models\AcademicYear;
use App\Models\Section;
use App\Models\Division;
use App\Models\District;
use App\Models\Upazila;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | USER TYPE CONFIGURATION
    |--------------------------------------------------------------------------
    */

private const DEFAULT_USER_TYPE_CODE_STARTS = [
    'student'      => 101,
    'teacher'      => 201,
    'donor_member' => 301,
];

private const ALLOWED_USER_TYPE_SLUGS = [
    'student',
    'teacher',
    'donor_member',
];


    /*
    |--------------------------------------------------------------------------
    | USER TYPE HELPERS
    |--------------------------------------------------------------------------
    */

private function getAllowedUserTypes()
{
    return UserType::where('is_active', 1)
        ->whereIn('slug', [
            'student',
            'teacher',
            'donor_member',
        ])
        ->orderByRaw("
            CASE slug
                WHEN 'student' THEN 1
                WHEN 'teacher' THEN 2
                WHEN 'donor_member' THEN 3
                ELSE 4
            END
        ")
        ->get();
}


    private function getDefaultUserTypeCodeStart(UserType $userType): int
    {
        return self::DEFAULT_USER_TYPE_CODE_STARTS[$userType->slug] ?? 101;
    }


    private function getUserTypeOrFail(int $userTypeId): UserType
    {
        return UserType::active()
            ->whereIn('slug', self::ALLOWED_USER_TYPE_SLUGS)
            ->findOrFail($userTypeId);
    }


    /*
    |--------------------------------------------------------------------------
    | INSTITUTION CONTEXT
    |--------------------------------------------------------------------------
    */

    private function isSwitchedInstitutionMode(): bool
    {
        return auth()->user()->is_super_admin
            && session()->has('active_institution_id');
    }


    private function getActiveInstitutionId()
    {
        $authUser = auth()->user();

        if ($authUser->is_super_admin) {
            return session('active_institution_id');
        }

        return $authUser->institution_id;
    }


    /*
    |--------------------------------------------------------------------------
    | ROLE HELPERS
    |--------------------------------------------------------------------------
    */

    private function allowedRoleSlugs(): array
    {
        $authUser = auth()->user();

        if ($authUser->is_super_admin) {
            return session()->has('active_institution_id')
                ? ['user']
                : ['user_admin', 'support_admin'];
        }

        if ($authUser->is_support_admin) {
            return ['user_admin'];
        }

        if ($authUser->is_user_admin) {
            return ['user'];
        }

        return [];
    }


    private function allowedRolesQuery()
    {
        return Role::whereIn('slug', $this->allowedRoleSlugs())
            ->orderBy('level', 'desc');
    }


    /**
     * New normal user gets Role "user".
     *
     * System Super Admin / Support Admin:
     * user_admin is created by this form.
     *
     * Existing Role architecture remains unchanged.
     */
    private function getDefaultRoleForNewUser(): Role
    {
        $authUser = auth()->user();

        if (
            ($authUser->is_super_admin && !session()->has('active_institution_id'))
            || $authUser->is_support_admin
        ) {
            return Role::where('slug', 'user_admin')->firstOrFail();
        }

        return Role::where('slug', 'user')->firstOrFail();
    }


    /*
    |--------------------------------------------------------------------------
    | INSTITUTION RESOLUTION
    |--------------------------------------------------------------------------
    */

    private function resolveInstitutionIdForRole(
        Request $request,
        string $roleSlug
    ) {
        $authUser = auth()->user();

        if ($roleSlug === 'support_admin') {
            return null;
        }

        if ($authUser->is_super_admin) {

            return session()->has('active_institution_id')
                ? (int) session('active_institution_id')
                : (
                    $request->filled('institution_id')
                        ? (int) $request->institution_id
                        : null
                );
        }

        if ($authUser->is_support_admin) {

            return $request->filled('institution_id')
                ? (int) $request->institution_id
                : null;
        }

        return $authUser->institution_id
            ? (int) $authUser->institution_id
            : null;
    }


    /*
    |--------------------------------------------------------------------------
    | USER CODE SETTINGS
    |--------------------------------------------------------------------------
    */

    private function codeSettingLegacyKey(int $userTypeId): string
    {
        return 'user_type_' . $userTypeId;
    }


    private function findUserTypeSetting(
        $institutionId,
        UserType $userType
    ): ?UserCodeSetting {

        $query = UserCodeSetting::query()
            ->where('user_type_id', $userType->id);

        if ($institutionId) {
            $query->where('institution_id', $institutionId);
        } else {
            $query->whereNull('institution_id');
        }

        $setting = $query->first();

        /*
         * Preserve existing Student setting.
         */
        if (!$setting && $userType->slug === 'student') {

            $legacyQuery = UserCodeSetting::query()
                ->where('role_slug', 'student');

            if ($institutionId) {
                $legacyQuery->where('institution_id', $institutionId);
            } else {
                $legacyQuery->whereNull('institution_id');
            }

            $setting = $legacyQuery->first();

            if ($setting) {
                $setting->user_type_id = $userType->id;
                $setting->save();
            }
        }

        return $setting;
    }


    private function saveOrFindUserTypeSetting(
        $institutionId,
        UserType $userType
    ): UserCodeSetting {

        $setting = $this->findUserTypeSetting(
            $institutionId,
            $userType
        );

        if ($setting) {
            return $setting;
        }

        $legacyKey = $this->codeSettingLegacyKey(
            (int) $userType->id
        );

        $query = UserCodeSetting::query()
            ->where('role_slug', $legacyKey);

        if ($institutionId) {
            $query->where('institution_id', $institutionId);
        } else {
            $query->whereNull('institution_id');
        }

        $setting = $query->first();

        if (!$setting) {

            $setting = new UserCodeSetting();

            $setting->institution_id = $institutionId;
            $setting->role_slug = $legacyKey;
            $setting->user_type_id = $userType->id;

            $setting->start_code = $this->getDefaultUserTypeCodeStart(
                $userType
            );

            $setting->gender_preference = 'male';

            $setting->save();

        } else {

            $setting->user_type_id = $userType->id;
            $setting->save();
        }

        return $setting;
    }


    private function getConfiguredCodeStart(
        $institutionId,
        int $userTypeId
    ): int {

        $userType = UserType::find($userTypeId);

        if (!$userType) {
            return 101;
        }

        $setting = $this->findUserTypeSetting(
            $institutionId,
            $userType
        );

        if ($setting && $setting->start_code !== null) {
            return max(1, (int) $setting->start_code);
        }

        return $this->getDefaultUserTypeCodeStart($userType);
    }


    /*
    |--------------------------------------------------------------------------
    | GLOBAL USER CODE SCOPE
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | User Type is NOT included in this query.
    |
    | All User Types share the same code namespace
    | inside one institution.
    |
    */

public function show($username)
{
    $user = User::with([
        'userType',
        'institution',
    ])
    ->where('username', $username)
    ->firstOrFail();

    return view('admin.users.show', compact('user'));
}

    private function userTypeCodeScopeQuery(
        $institutionId,
        ?int $userTypeId = null
    ) {

        $query = User::query()
            ->whereNotNull('institution_user_id');

        if ($institutionId) {
            $query->where('institution_id', $institutionId);
        } else {
            $query->whereNull('institution_id');
        }

        return $query;
    }


    /**
     * Check whether a numeric code already exists globally.
     *
     * Numeric comparison also treats "00500" and "500"
     * as the same numeric User Code.
     */
    private function userCodeExists(
        $institutionId,
        string $code,
        ?int $excludeUserId = null
    ): bool {

        $query = $this->userTypeCodeScopeQuery($institutionId)
            ->whereRaw(
                "institution_user_id REGEXP '^[0-9]+$'"
            )
            ->whereRaw(
                'CAST(institution_user_id AS UNSIGNED) = ?',
                [(int) $code]
            );

        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }

        return $query->exists();
    }


    /*
    |--------------------------------------------------------------------------
    | AUTO USER CODE GENERATOR
    |--------------------------------------------------------------------------
    */

    private function generateUserTypeCode(
        $institutionId,
        int $userTypeId,
        ?int $excludeUserId = null
    ): string {

        $nextCode = $this->getConfiguredCodeStart(
            $institutionId,
            $userTypeId
        );

        $query = $this->userTypeCodeScopeQuery(
            $institutionId
        );

        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }

        $existingCodes = $query
            ->whereRaw(
                "institution_user_id REGEXP '^[0-9]+$'"
            )
            ->pluck('institution_user_id')
            ->map(fn ($code) => (int) $code)
            ->unique()
            ->sort()
            ->values();

        foreach ($existingCodes as $existingCode) {

            if ($existingCode < $nextCode) {
                continue;
            }

            if ($existingCode === $nextCode) {
                $nextCode++;
                continue;
            }

            break;
        }

        return (string) $nextCode;
    }


    /*
    |--------------------------------------------------------------------------
    | MANUAL USER CODE GENERATOR
    |--------------------------------------------------------------------------
    */

    private function generateManualUserTypeCode(
        $institutionId,
        int $userTypeId,
        ?int $excludeUserId = null
    ): string {

        $userType = UserType::findOrFail($userTypeId);

        $setting = $this->findUserTypeSetting(
            $institutionId,
            $userType
        );

        $nextCode = (
            $setting
            && $setting->manual_start_code !== null
        )
            ? (int) $setting->manual_start_code
            : $this->getConfiguredCodeStart(
                $institutionId,
                $userTypeId
            );

        $query = $this->userTypeCodeScopeQuery(
            $institutionId
        );

        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }

        $usedCodes = $query
            ->whereRaw(
                "institution_user_id REGEXP '^[0-9]+$'"
            )
            ->pluck('institution_user_id')
            ->map(fn ($code) => (int) $code)
            ->flip();

        while ($usedCodes->has($nextCode)) {
            $nextCode++;
        }

        return (string) $nextCode;
    }


    /*
    |--------------------------------------------------------------------------
    | USER AUTHORIZATION
    |--------------------------------------------------------------------------
    */

    private function authorizeManagedUser(User $user): void
    {
        $authUser = auth()->user();

        $targetRole = optional($user->role)->slug;

        if ($targetRole === 'super_admin') {
            abort(403, 'Super Admin account পরিবর্তন করা যাবে না।');
        }

        if ($authUser->is_super_admin) {

            if (session()->has('active_institution_id')) {

                abort_unless(
                    (int) $user->institution_id ===
                    (int) session('active_institution_id')
                    &&
                    !in_array(
                        $targetRole,
                        ['support_admin', 'user_admin'],
                        true
                    ),
                    403,
                    'এই User পরিচালনা করার অনুমতি আপনার নেই।'
                );
            }

            return;
        }

        if ($authUser->is_support_admin) {

            abort_unless(
                $targetRole === 'user_admin',
                403,
                'এই User পরিচালনা করার অনুমতি আপনার নেই।'
            );

            return;
        }

        abort_unless(
            (int) $user->institution_id ===
            (int) $authUser->institution_id
            &&
            !in_array(
                $targetRole,
                ['super_admin', 'support_admin', 'user_admin'],
                true
            ),
            403,
            'এই User পরিচালনা করার অনুমতি আপনার নেই।'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | USER INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $authUser = auth()->user();

        $query = User::with([
            'role',
            'userType',
            'institution',
            'student'
        ]);

        if ($authUser->is_super_admin) {

            if (session()->has('active_institution_id')) {

                $query->where(
                    'institution_id',
                    session('active_institution_id')
                )->whereHas('role', function ($q) {

                    $q->whereNotIn(
                        'slug',
                        ['super_admin', 'support_admin']
                    );

                });

            } else {

                $query->whereHas('role', function ($q) {
                    $q->whereNotIn('slug', ['super_admin']);
                });
            }

        } elseif ($authUser->is_support_admin) {

            $query->whereHas('role', function ($q) {
                $q->where('slug', 'user_admin');
            });

        } else {

            $query->where(
                'institution_id',
                $authUser->institution_id
            )->whereHas('role', function ($q) {

                $q->whereNotIn(
                    'slug',
                    ['super_admin', 'support_admin', 'user_admin']
                );

            });
        }

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_bn', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere(
                        'institution_user_id',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        if ($request->filled('user_type_id')) {

            $query->where(
                'user_type_id',
                $request->integer('user_type_id')
            );

        } elseif ($request->filled('user_type')) {

            $query->whereHas('userType', function ($q) use ($request) {
                $q->where('slug', $request->user_type);
            });
        }

        if ($request->filled('role_id')) {
            $query->where(
                'role_id',
                $request->integer('role_id')
            );
        }

        $users = $query
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $divisions = Division::all();

        $userTypes = $this->getAllowedUserTypes();

        return view(
            'admin.users.index',
            compact(
                'users',
                'divisions',
                'userTypes'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE USER FORM
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $authUser = auth()->user();

        $userTypes = $this->getAllowedUserTypes();

        if (
            $authUser->is_super_admin
            && !session()->has('active_institution_id')
        ) {

            $institutions = Madrasa::where('status', 1)
                ->orderBy('name')
                ->get();

        } elseif ($authUser->is_support_admin) {

            $institutions = Madrasa::where('status', 1)
                ->orderBy('name')
                ->get();

        } elseif ($authUser->is_super_admin) {

            $institutions = Madrasa::where(
                'id',
                session('active_institution_id')
            )->where('status', 1)->get();

        } else {

            $institutions = Madrasa::where(
                'id',
                $authUser->institution_id
            )->where('status', 1)->get();
        }


        if ($authUser->is_super_admin) {

            $institutionId = session()->has('active_institution_id')
                ? (int) session('active_institution_id')
                : null;

        } elseif ($authUser->is_support_admin) {

            $institutionId = null;

        } else {

            $institutionId = $authUser->institution_id;
        }


        $previewIds = [];

        foreach ($userTypes as $type) {

            $previewIds[$type->id] = $institutionId
                ? $this->generateUserTypeCode(
                    $institutionId,
                    (int) $type->id
                )
                : null;
        }


        $divisions = Division::with([
            'districts.upazilas'
        ])->get();

        $academicYears = AcademicYear::where('status', 1)
            ->orderBy('name', 'desc')
            ->get();

        $classes = Classes::where('status', 1)->get();

        $selectedUserTypeId = old('user_type_id');

        $genderPreference = 'male';

        if ($institutionId && $selectedUserTypeId) {

            $selectedType = $userTypes->firstWhere(
                'id',
                (int) $selectedUserTypeId
            );

            if ($selectedType) {

                $setting = $this->findUserTypeSetting(
                    $institutionId,
                    $selectedType
                );

                $genderPreference = $setting->gender_preference ?? 'male';
            }
        }


        return view(
            'admin.users.create',
            compact(
                'userTypes',
                'institutions',
                'divisions',
                'previewIds',
                'academicYears',
                'classes',
                'genderPreference'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE USER
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([

            'user_type_id' => [
                'required',
                'integer',
                'exists:user_types,id'
            ],

            'name_bn' => [
                'required',
                'string',
                'max:255'
            ],

            'name' => [
                'nullable',
                'string',
                'max:255'
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
                'unique:users,phone'
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:users,email'
            ],

            'password' => [
                'nullable',
                'string',
                'min:6'
            ],

            'gender' => [
                'required',
                'in:male,female'
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048'
            ],

            'date_of_birth' => ['nullable', 'date'],

            'blood_group' => ['nullable', 'string', 'max:10'],

            'religion' => ['nullable', 'string', 'max:50'],

            'nid' => ['nullable', 'string', 'max:100'],

            'birth_certificate' => ['nullable', 'string', 'max:100'],

            'phone2' => ['nullable', 'string', 'max:30'],

            'phone_relation' => ['nullable', 'string', 'max:50'],

            'phone2_relation' => ['nullable', 'string', 'max:50'],

            'father_name' => ['nullable', 'string', 'max:255'],

            'father_phone' => ['nullable', 'string', 'max:30'],

            'mother_name' => ['nullable', 'string', 'max:255'],

            'mother_phone' => ['nullable', 'string', 'max:30'],

            'guardian_name' => ['nullable', 'string', 'max:255'],

            'guardian_phone' => ['nullable', 'string', 'max:30'],

            'guardian_relation' => ['nullable', 'string', 'max:50'],

            'code_generation_type' => [
                'nullable',
                'in:auto,manual'
            ],

            'custom_id' => [
                'nullable',
                'string',
                'max:100'
            ],

            'institution_id' => [
                'nullable',
                'exists:madrasas,id'
            ],

            'present_division_id' => ['nullable', 'exists:divisions,id'],

            'present_district_id' => ['nullable', 'exists:districts,id'],

            'present_upazila_id' => ['nullable', 'exists:upazilas,id'],

            'present_union' => ['nullable', 'string', 'max:255'],

            'present_post_office' => ['nullable', 'string', 'max:255'],

            'present_village_road' => ['nullable', 'string', 'max:255'],

            'present_postal_code' => ['nullable', 'string', 'max:20'],

            'permanent_division_id' => ['nullable', 'exists:divisions,id'],

            'permanent_district_id' => ['nullable', 'exists:districts,id'],

            'permanent_upazila_id' => ['nullable', 'exists:upazilas,id'],

            'permanent_union' => ['nullable', 'string', 'max:255'],

            'permanent_post_office' => ['nullable', 'string', 'max:255'],

            'permanent_village_road' => ['nullable', 'string', 'max:255'],

            'permanent_postal_code' => ['nullable', 'string', 'max:20'],

            'academic_year_id' => ['nullable', 'exists:academic_years,id'],

            'class_id' => ['nullable', 'exists:classes,id'],

            'section_id' => ['nullable', 'exists:sections,id'],

            'guardian_user_id' => ['nullable', 'exists:users,id'],

        ]);


        $userType = $this->getUserTypeOrFail(
            (int) $validated['user_type_id']
        );


        /*
         * Role is assigned automatically.
         * No role_id is required from the browser.
         */
        $role = $this->getDefaultRoleForNewUser();

        $roleSlug = $role->slug;


        $institutionId = $this->resolveInstitutionIdForRole(
            $request,
            $roleSlug
        );


        if ($roleSlug !== 'support_admin' && !$institutionId) {

            throw ValidationException::withMessages([
                'institution_id' => 'প্রতিষ্ঠান নির্বাচন করা আবশ্যক।',
            ]);
        }


        if (
            $institutionId
            && !Madrasa::where('id', $institutionId)->exists()
        ) {

            throw ValidationException::withMessages([
                'institution_id' => 'নির্বাচিত প্রতিষ্ঠান পাওয়া যায়নি।',
            ]);
        }


        /*
         * Prevent Support Admin from assigning users
         * outside selected institutions.
         */
        $authUser = auth()->user();

        if (
            $authUser->is_support_admin
            && !$request->filled('institution_id')
        ) {

            throw ValidationException::withMessages([
                'institution_id' => 'প্রতিষ্ঠান নির্বাচন করুন।',
            ]);
        }


        $isStudent = $userType->slug === 'student';

        $generationType = $validated['code_generation_type'] ?? 'auto';

        $customId = trim(
            (string) ($validated['custom_id'] ?? '')
        );


        /*
        |--------------------------------------------------------------------------
        | MANUAL CODE
        |--------------------------------------------------------------------------
        */

        if ($generationType === 'manual') {

            if ($customId === '') {

                $customId = $this->generateManualUserTypeCode(
                    $institutionId,
                    (int) $userType->id
                );
            }


            if (!preg_match('/^[0-9]+$/', $customId)) {

                throw ValidationException::withMessages([
                    'custom_id' => 'User Code শুধুমাত্র সংখ্যা হতে হবে।',
                ]);
            }


            $setting = $this->findUserTypeSetting(
                $institutionId,
                $userType
            );

            $minimumCode = (
                $setting
                && $setting->manual_start_code !== null
            )
                ? (int) $setting->manual_start_code
                : $this->getConfiguredCodeStart(
                    $institutionId,
                    (int) $userType->id
                );


            if ((int) $customId < $minimumCode) {

                throw ValidationException::withMessages([
                    'custom_id' => 'এই User Type-এর Manual Code '
                        . $minimumCode
                        . ' থেকে শুরু হতে হবে।',
                ]);
            }


            if ($this->userCodeExists($institutionId, $customId)) {

                throw ValidationException::withMessages([
                    'custom_id' => 'এই User Code ইতোমধ্যে অন্য একজন ব্যবহার করছেন। অন্য Code দিন।',
                ]);
            }

        } else {

            $customId = $this->generateUserTypeCode(
                $institutionId,
                (int) $userType->id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CREATE USER
        |--------------------------------------------------------------------------
        */

        $photoPath = null;

        DB::beginTransaction();

        try {

            /*
             * Lock same-institution user rows while generating/saving
             * the code to reduce concurrent duplicate generation.
             */
            $this->userTypeCodeScopeQuery($institutionId)
                ->lockForUpdate()
                ->get(['id']);


            /*
             * Recheck after acquiring the lock.
             */
            if ($this->userCodeExists($institutionId, $customId)) {

                if ($generationType === 'manual') {

                    throw ValidationException::withMessages([
                        'custom_id' => 'এই User Code ইতোমধ্যে ব্যবহার করা হয়েছে।',
                    ]);
                }

                $customId = $this->generateUserTypeCode(
                    $institutionId,
                    (int) $userType->id
                );
            }


            if ($request->hasFile('photo')) {

                $photoPath = $request->file('photo')
                    ->store('users/photos', 'public');
            }


            $user = User::create([

                'institution_user_id' => $customId,

                'code_generation_type' => $generationType,

                'institution_id' => $institutionId,

                'user_type_id' => $userType->id,

                'role_id' => $role->id,

                'username' => $this->generateUsername(
                    $request->name,
                    $request->name_bn
                ),

                'email' => $request->email,

                'password' => Hash::make(
                    $request->password ?: 'password123'
                ),

                'name' => $request->name,

                'name_bn' => $request->name_bn,

                'photo' => $photoPath,

                'phone' => $request->phone,

                'phone2' => $request->phone2,

                'phone_owner' => $request->phone_relation,

                'gender' => $request->gender,

                'date_of_birth' => $request->date_of_birth,

                'age' => $request->date_of_birth
                    ? Carbon::parse($request->date_of_birth)->age
                    : null,

                'blood_group' => $request->blood_group,

                'religion' => $request->religion,

                'nid' => $request->nid,

                'birth_certificate' => $request->birth_certificate,

                'father_name' => $request->father_name,

                'father_phone' => $request->father_phone,

                'mother_name' => $request->mother_name,

                'mother_phone' => $request->mother_phone,

                'guardian_name' => $request->guardian_name,

                'guardian_phone' => $request->guardian_phone,

                'guardian_relation' => $request->guardian_relation,

                'present_division_id' => $request->present_division_id,

                'present_district_id' => $request->present_district_id,

                'present_upazila_id' => $request->present_upazila_id,

                'present_union' => $request->present_union,

                'present_post_office' => $request->present_post_office,

                'present_village_road' => $request->present_village_road,

                'present_postal_code' => $request->present_postal_code,

                'present_address_full' => $this->generateAddressString(
                    $request,
                    'present'
                ),

                'permanent_division_id' => $request->permanent_division_id,

                'permanent_district_id' => $request->permanent_district_id,

                'permanent_upazila_id' => $request->permanent_upazila_id,

                'permanent_union' => $request->permanent_union,

                'permanent_post_office' => $request->permanent_post_office,

                'permanent_village_road' => $request->permanent_village_road,

                'permanent_postal_code' => $request->permanent_postal_code,

                'permanent_address_full' => $this->generateAddressString(
                    $request,
                    'permanent'
                ),

                'status' => 1,

                'created_by' => auth()->id(),

            ]);


            if ($isStudent) {

                $studentId = $this->generateStudentId(
                    $institutionId,
                    $request->academic_year_id
                );


                Student::create([

                    'user_id' => $user->id,

                    'institution_id' => $institutionId,

                    'academic_year_id' => $request->academic_year_id,

                    'class_id' => $request->class_id,

                    'section_id' => $request->section_id,

                    'guardian_user_id' => $request->guardian_user_id,

                    'student_id' => $studentId,

                    'is_hostel' => $request->boolean('is_hostel'),

                    'is_transport' => $request->boolean('is_transport'),

                    'status' => 1,

                    'created_by' => auth()->id(),

                    'updated_by' => auth()->id(),

                ]);
            }


            DB::commit();


            return redirect()
                ->route('dashboard.user.index')
                ->with(
                    'success',
                    'ইউজার সফলভাবে তৈরি হয়েছে।'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            if (
                $photoPath
                && Storage::disk('public')->exists($photoPath)
            ) {
                Storage::disk('public')->delete($photoPath);
            }


            if ($e instanceof ValidationException) {
                throw $e;
            }


            return back()
                ->with(
                    'error',
                    'ইউজার তৈরি করতে সমস্যা হয়েছে: ' . $e->getMessage()
                )
                ->withInput();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | STUDENT ID GENERATOR
    |--------------------------------------------------------------------------
    */

    private function generateStudentId(
        $institutionId,
        $academicYearId = null
    ) {

        $query = Student::where(
            'institution_id',
            $institutionId
        );

        if ($academicYearId) {

            $query->where(
                'academic_year_id',
                $academicYearId
            );

        } else {

            $query->whereNull('academic_year_id');
        }


        $lastStudent = $query->latest('id')->first();

        $newNumber = 1;


        if ($lastStudent && $lastStudent->student_id) {

            $studentIdString = $lastStudent->student_id;

            if ($academicYearId) {

                $pattern = '/^STU-'
                    . preg_quote($institutionId, '/')
                    . '-'
                    . preg_quote($academicYearId, '/')
                    . '-(\d+)$/';

            } else {

                $pattern = '/^STU-'
                    . preg_quote($institutionId, '/')
                    . '-(\d+)$/';
            }


            if (preg_match($pattern, $studentIdString, $matches)) {
                $newNumber = ((int) $matches[1]) + 1;
            }
        }


        if ($academicYearId) {

            $studentId = 'STU-'
                . $institutionId
                . '-'
                . $academicYearId
                . '-'
                . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        } else {

            $studentId = 'STU-'
                . $institutionId
                . '-'
                . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        }


        while (
            Student::where('student_id', $studentId)->exists()
        ) {

            $newNumber++;

            if ($academicYearId) {

                $studentId = 'STU-'
                    . $institutionId
                    . '-'
                    . $academicYearId
                    . '-'
                    . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            } else {

                $studentId = 'STU-'
                    . $institutionId
                    . '-'
                    . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            }
        }

        return $studentId;
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT USER
    |--------------------------------------------------------------------------
    */

    public function edit($username)
    {
        $user = User::with([
            'role',
            'userType',
            'student',
            'institution'
        ])
            ->where('username', $username)
            ->firstOrFail();


        $this->authorizeManagedUser($user);

        $authUser = auth()->user();


        if (
            $authUser->is_super_admin
            && session()->has('active_institution_id')
        ) {

            $institutions = Madrasa::where(
                'id',
                session('active_institution_id')
            )->get();

        } elseif (
            $authUser->is_super_admin
            || $authUser->is_support_admin
        ) {

            $institutions = Madrasa::where('status', 1)
                ->orderBy('name')
                ->get();

        } else {

            $institutions = Madrasa::where(
                'id',
                $authUser->institution_id
            )->get();
        }


        $userTypes = $this->getAllowedUserTypes();


        /*
         * Keep legacy User Type visible for an existing account.
         */
        if (
            $user->userType
            && !$userTypes->contains('id', $user->user_type_id)
        ) {

            $userTypes->push($user->userType);
        }


        $divisions = Division::with([
            'districts.upazilas'
        ])->get();


        $academicYears = AcademicYear::where('status', 1)
            ->orderBy('name', 'desc')
            ->get();


        $classes = Classes::where('status', 1)->get();


        $sections = collect();

        if ($user->student && $user->student->class_id) {

            $sections = Section::where(
                'class_id',
                $user->student->class_id
            )
                ->where('status', 1)
                ->get();
        }


        return view(
            'admin.users.edit',
            compact(
                'user',
                'institutions',
                'divisions',
                'academicYears',
                'classes',
                'sections',
                'userTypes'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE USER
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $username)
    {
        $user = User::with([
            'role',
            'userType',
            'student'
        ])
            ->where('username', $username)
            ->firstOrFail();


        $this->authorizeManagedUser($user);


        $validated = $request->validate([

            'user_type_id' => [
                'required',
                'integer',
                'exists:user_types,id'
            ],

            'name_bn' => [
                'required',
                'string',
                'max:255'
            ],

            'name' => [
                'nullable',
                'string',
                'max:255'
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
                'unique:users,phone,' . $user->id
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:users,email,' . $user->id
            ],

            'password' => [
                'nullable',
                'string',
                'min:6'
            ],

            'gender' => [
                'required',
                'in:male,female'
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048'
            ],

            'date_of_birth' => ['nullable', 'date'],

            'blood_group' => ['nullable', 'string', 'max:10'],

            'religion' => ['nullable', 'string', 'max:50'],

            'nid' => ['nullable', 'string', 'max:100'],

            'birth_certificate' => ['nullable', 'string', 'max:100'],

            'phone2' => ['nullable', 'string', 'max:30'],

            'phone_relation' => ['nullable', 'string', 'max:50'],

            'phone2_relation' => ['nullable', 'string', 'max:50'],

            'father_name' => ['nullable', 'string', 'max:255'],

            'father_phone' => ['nullable', 'string', 'max:30'],

            'mother_name' => ['nullable', 'string', 'max:255'],

            'mother_phone' => ['nullable', 'string', 'max:30'],

            'guardian_name' => ['nullable', 'string', 'max:255'],

            'guardian_phone' => ['nullable', 'string', 'max:30'],

            'guardian_relation' => ['nullable', 'string', 'max:50'],

            'code_generation_type' => [
                'nullable',
                'in:auto,manual'
            ],

            'custom_id' => [
                'nullable',
                'string',
                'max:100'
            ],

            'institution_id' => [
                'nullable',
                'exists:madrasas,id'
            ],

            'present_division_id' => ['nullable', 'exists:divisions,id'],

            'present_district_id' => ['nullable', 'exists:districts,id'],

            'present_upazila_id' => ['nullable', 'exists:upazilas,id'],

            'present_union' => ['nullable', 'string', 'max:255'],

            'present_post_office' => ['nullable', 'string', 'max:255'],

            'present_village_road' => ['nullable', 'string', 'max:255'],

            'present_postal_code' => ['nullable', 'string', 'max:20'],

            'permanent_division_id' => ['nullable', 'exists:divisions,id'],

            'permanent_district_id' => ['nullable', 'exists:districts,id'],

            'permanent_upazila_id' => ['nullable', 'exists:upazilas,id'],

            'permanent_union' => ['nullable', 'string', 'max:255'],

            'permanent_post_office' => ['nullable', 'string', 'max:255'],

            'permanent_village_road' => ['nullable', 'string', 'max:255'],

            'permanent_postal_code' => ['nullable', 'string', 'max:20'],

            'academic_year_id' => ['nullable', 'exists:academic_years,id'],

            'class_id' => ['nullable', 'exists:classes,id'],

            'section_id' => ['nullable', 'exists:sections,id'],

            'guardian_user_id' => ['nullable', 'exists:users,id'],

        ]);


        $userType = $this->getUserTypeOrFail(
            (int) $validated['user_type_id']
        );


        /*
         * Role is deliberately preserved.
         * Edit form cannot change Role.
         */
        $role = $user->role;

        if (!$role) {
            abort(403, 'User-এর Role পাওয়া যায়নি।');
        }

        $roleSlug = $role->slug;


        $authUser = auth()->user();


        if (
            !$request->filled('institution_id')
            && $user->institution_id
        ) {

            $request->merge([
                'institution_id' => $user->institution_id
            ]);
        }


        $institutionId = $this->resolveInstitutionIdForRole(
            $request,
            $roleSlug
        );


        if (
            $roleSlug !== 'support_admin'
            && !$institutionId
        ) {

            throw ValidationException::withMessages([
                'institution_id' => 'প্রতিষ্ঠান নির্বাচন করা আবশ্যক।',
            ]);
        }


        if (
            $authUser->is_user_admin
            && (int) $institutionId !==
            (int) $authUser->institution_id
        ) {

            abort(
                403,
                'অন্য প্রতিষ্ঠানে User assign করার অনুমতি নেই।'
            );
        }


        if (
            $authUser->is_support_admin
            && (int) $institutionId <= 0
        ) {

            throw ValidationException::withMessages([
                'institution_id' => 'প্রতিষ্ঠান নির্বাচন করুন।',
            ]);
        }


        $generationType = $validated['code_generation_type']
            ?? ($user->code_generation_type ?: 'auto');


        $customId = trim(
            (string) ($validated['custom_id'] ?? '')
        );


        $sameUserType = (int) $user->user_type_id
            === (int) $userType->id;

        $sameInstitution = (string) $user->institution_id
            === (string) $institutionId;


        /*
        |--------------------------------------------------------------------------
        | MANUAL CODE
        |--------------------------------------------------------------------------
        */

        if ($generationType === 'manual') {

            if ($customId === '') {

                if (
                    $sameUserType
                    && $sameInstitution
                    && $user->code_generation_type === 'manual'
                    && $user->institution_user_id
                ) {

                    $customId = (string) $user->institution_user_id;

                } else {

                    $customId = $this->generateManualUserTypeCode(
                        $institutionId,
                        (int) $userType->id,
                        $user->id
                    );
                }
            }


            if (!preg_match('/^[0-9]+$/', $customId)) {

                throw ValidationException::withMessages([
                    'custom_id' => 'User Code শুধুমাত্র সংখ্যা হতে হবে।',
                ]);
            }


            $setting = $this->findUserTypeSetting(
                $institutionId,
                $userType
            );


            $minimumCode = (
                $setting
                && $setting->manual_start_code !== null
            )
                ? (int) $setting->manual_start_code
                : $this->getConfiguredCodeStart(
                    $institutionId,
                    (int) $userType->id
                );


            if ((int) $customId < $minimumCode) {

                throw ValidationException::withMessages([
                    'custom_id' => 'এই User Type-এর Manual Code '
                        . $minimumCode
                        . ' থেকে শুরু হতে হবে।',
                ]);
            }


            if (
                $this->userCodeExists(
                    $institutionId,
                    $customId,
                    $user->id
                )
            ) {

                throw ValidationException::withMessages([
                    'custom_id' => 'এই User Code অন্য একজন ব্যবহার করছেন।',
                ]);
            }

        } else {

            /*
             * Preserve current Auto Code if:
             * User Type and Institution remain unchanged.
             */
            if (
                $sameUserType
                && $sameInstitution
                && $user->code_generation_type === 'auto'
                && $user->institution_user_id
            ) {

                $customId = (string) $user->institution_user_id;

            } else {

                $customId = $this->generateUserTypeCode(
                    $institutionId,
                    (int) $userType->id,
                    $user->id
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE USER
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();

        $oldPhoto = $user->photo;

        $newPhoto = $oldPhoto;

        try {

            $this->userTypeCodeScopeQuery($institutionId)
                ->lockForUpdate()
                ->get(['id']);


            /*
             * Recheck the selected code.
             */
            if (
                $this->userCodeExists(
                    $institutionId,
                    $customId,
                    $user->id
                )
            ) {

                if ($generationType === 'manual') {

                    throw ValidationException::withMessages([
                        'custom_id' => 'এই User Code অন্য একজন ব্যবহার করছেন।',
                    ]);

                } else {

                    $customId = $this->generateUserTypeCode(
                        $institutionId,
                        (int) $userType->id,
                        $user->id
                    );
                }
            }


            if ($request->hasFile('photo')) {

                $newPhoto = $request->file('photo')
                    ->store('users/photos', 'public');
            }


            $data = [

                'institution_id' => $institutionId,

                'user_type_id' => $userType->id,

                /*
                 * Do not update role_id here.
                 */

                'institution_user_id' => $customId,

                'code_generation_type' => $generationType,

                'email' => $request->email,

                'name' => $request->name,

                'name_bn' => $request->name_bn,

                'photo' => $newPhoto,

                'phone' => $request->phone,

                'phone2' => $request->phone2,

                'phone_owner' => $request->phone_relation,

                'gender' => $request->gender,

                'date_of_birth' => $request->date_of_birth,

                'age' => $request->date_of_birth
                    ? Carbon::parse($request->date_of_birth)->age
                    : null,

                'blood_group' => $request->blood_group,

                'religion' => $request->religion,

                'nid' => $request->nid,

                'birth_certificate' => $request->birth_certificate,

                'father_name' => $request->father_name,

                'father_phone' => $request->father_phone,

                'mother_name' => $request->mother_name,

                'mother_phone' => $request->mother_phone,

                'guardian_name' => $request->guardian_name,

                'guardian_phone' => $request->guardian_phone,

                'guardian_relation' => $request->guardian_relation,

                'present_division_id' => $request->present_division_id,

                'present_district_id' => $request->present_district_id,

                'present_upazila_id' => $request->present_upazila_id,

                'present_union' => $request->present_union,

                'present_post_office' => $request->present_post_office,

                'present_village_road' => $request->present_village_road,

                'present_postal_code' => $request->present_postal_code,

                'present_address_full' => $this->generateAddressString(
                    $request,
                    'present'
                ),

                'permanent_division_id' => $request->permanent_division_id,

                'permanent_district_id' => $request->permanent_district_id,

                'permanent_upazila_id' => $request->permanent_upazila_id,

                'permanent_union' => $request->permanent_union,

                'permanent_post_office' => $request->permanent_post_office,

                'permanent_village_road' => $request->permanent_village_road,

                'permanent_postal_code' => $request->permanent_postal_code,

                'permanent_address_full' => $this->generateAddressString(
                    $request,
                    'permanent'
                ),

                'updated_by' => auth()->id(),

            ];


            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }


            $user->update($data);


            /*
             * Student Profile follows User Type, not Role.
             *
             * Existing student profile is not deleted
             * when User Type changes.
             */
            if ($userType->slug === 'student') {

                $studentData = [

                    'institution_id' => $institutionId,

                    'academic_year_id' => $request->academic_year_id,

                    'class_id' => $request->class_id,

                    'section_id' => $request->section_id,

                    'guardian_user_id' => $request->guardian_user_id,

                    'is_hostel' => $request->boolean('is_hostel'),

                    'is_transport' => $request->boolean('is_transport'),

                    'updated_by' => auth()->id(),

                ];


                if ($user->student) {

                    $user->student->update($studentData);

                } else {

                    $studentData['user_id'] = $user->id;

                    $studentData['student_id'] = $this->generateStudentId(
                        $institutionId,
                        $request->academic_year_id
                    );

                    $studentData['status'] = 1;

                    $studentData['created_by'] = auth()->id();

                    Student::create($studentData);
                }
            }


            DB::commit();


            if (
                $newPhoto
                && $newPhoto !== $oldPhoto
                && $oldPhoto
                && Storage::disk('public')->exists($oldPhoto)
            ) {

                Storage::disk('public')->delete($oldPhoto);
            }


            return redirect()
                ->route('dashboard.user.index')
                ->with(
                    'success',
                    'ইউজার সফলভাবে আপডেট হয়েছে।'
                );

        } catch (\Throwable $e) {

            DB::rollBack();


            if (
                $newPhoto
                && $newPhoto !== $oldPhoto
                && Storage::disk('public')->exists($newPhoto)
            ) {

                Storage::disk('public')->delete($newPhoto);
            }


            if ($e instanceof ValidationException) {
                throw $e;
            }


            return back()
                ->with(
                    'error',
                    'আপডেট করতে সমস্যা হয়েছে: ' . $e->getMessage()
                )
                ->withInput();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE USER
    |--------------------------------------------------------------------------
    */

    public function destroy($username)
    {
        try {

            $user = User::with([
                'role',
                'student'
            ])
                ->where('username', $username)
                ->firstOrFail();


            if (optional($user->role)->slug === 'super_admin') {

                return back()->with(
                    'error',
                    'Super Admin ডিলিট করা যাবে না।'
                );
            }


            $authUser = auth()->user();


            if (
                $authUser->is_super_admin
                && session()->has('active_institution_id')
            ) {

                if (
                    (string) $user->institution_id
                    !==
                    (string) session('active_institution_id')
                ) {

                    abort(
                        403,
                        'এই User ডিলিট করার অনুমতি আপনার নেই।'
                    );
                }

                if (
                    !in_array(
                        optional($user->role)->slug,
                        ['student', 'user'],
                        true
                    )
                ) {

                    abort(
                        403,
                        'এই User ডিলিট করার অনুমতি আপনার নেই।'
                    );
                }

            } elseif (
                $authUser->is_super_admin
                && !session()->has('active_institution_id')
            ) {

                if (optional($user->role)->slug === 'student') {

                    abort(
                        403,
                        'এই User ডিলিট করার অনুমতি আপনার নেই।'
                    );
                }

            } elseif ($authUser->is_support_admin) {

                if (optional($user->role)->slug !== 'user_admin') {

                    abort(
                        403,
                        'এই User ডিলিট করার অনুমতি আপনার নেই।'
                    );
                }

            } elseif (!$authUser->is_super_admin) {

                if (
                    (int) $user->institution_id
                    !==
                    (int) $authUser->institution_id
                ) {

                    abort(
                        403,
                        'এই User ডিলিট করার অনুমতি আপনার নেই।'
                    );
                }

                if (
                    !in_array(
                        optional($user->role)->slug,
                        ['student', 'user'],
                        true
                    )
                ) {

                    abort(
                        403,
                        'এই User ডিলিট করার অনুমতি আপনার নেই।'
                    );
                }
            }


            if ($user->student) {
                $user->student->delete();
            }


            if (
                $user->photo
                && Storage::disk('public')->exists($user->photo)
            ) {

                Storage::disk('public')->delete($user->photo);
            }


            $user->delete();


            return redirect()
                ->route('dashboard.user.index')
                ->with(
                    'success',
                    'ইউজার সফলভাবে ডিলিট হয়েছে।'
                );

        } catch (\Exception $e) {

            return back()
                ->with(
                    'error',
                    'ডিলিট করতে সমস্যা হয়েছে: ' . $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | TOGGLE USER STATUS
    |--------------------------------------------------------------------------
    */

    public function toggleStatus($username)
    {
        $user = User::with('role')
            ->where('username', $username)
            ->firstOrFail();


        if (optional($user->role)->slug === 'super_admin') {

            return back()->with(
                'error',
                'Super Admin-এর Status পরিবর্তন করা যাবে না।'
            );
        }


        $authUser = auth()->user();


        if (
            $authUser->is_super_admin
            && session()->has('active_institution_id')
        ) {

            if (
                (string) $user->institution_id
                !==
                (string) session('active_institution_id')
            ) {

                abort(
                    403,
                    'এই User-এর Status পরিবর্তন করার অনুমতি আপনার নেই।'
                );
            }

            if (
                !in_array(
                    optional($user->role)->slug,
                    ['student', 'user'],
                    true
                )
            ) {

                abort(
                    403,
                    'এই User-এর Status পরিবর্তন করার অনুমতি আপনার নেই।'
                );
            }

        } elseif (
            $authUser->is_super_admin
            && !session()->has('active_institution_id')
        ) {

            if (optional($user->role)->slug === 'student') {

                abort(
                    403,
                    'এই User-এর Status পরিবর্তন করার অনুমতি আপনার নেই।'
                );
            }

        } elseif ($authUser->is_support_admin) {

            if (optional($user->role)->slug !== 'user_admin') {

                abort(
                    403,
                    'এই User-এর Status পরিবর্তন করার অনুমতি আপনার নেই।'
                );
            }

        } elseif (!$authUser->is_super_admin) {

            if (
                (int) $user->institution_id
                !==
                (int) $authUser->institution_id
            ) {

                abort(
                    403,
                    'এই User-এর Status পরিবর্তন করার অনুমতি আপনার নেই।'
                );
            }

            if (
                !in_array(
                    optional($user->role)->slug,
                    ['student', 'user'],
                    true
                )
            ) {

                abort(
                    403,
                    'এই User-এর Status পরিবর্তন করার অনুমতি আপনার নেই।'
                );
            }
        }


        $user->update([

            'status' => $user->status == 1 ? 0 : 1,

            'updated_by' => auth()->id(),

        ]);


        return back()->with(
            'success',
            'User Status সফলভাবে পরিবর্তন হয়েছে।'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | USERNAME GENERATOR
    |--------------------------------------------------------------------------
    */

    private function generateUsername(
        $nameEn = null,
        $nameBn = null
    ) {

        $name = trim((string) $nameEn);

        $baseName = Str::slug($name, '_');

        $baseName = preg_replace(
            '/[^a-z0-9_]/',
            '',
            $baseName
        );

        $baseName = trim($baseName, '_');


        if ($baseName === '') {

            $baseName = 'user';

        } else {

            $baseName = 'user_' . $baseName;
        }


        $username = $baseName;

        $counter = 2;


        while (User::where('username', $username)->exists()) {

            $username = $baseName . $counter;

            $counter++;
        }


        return $username;
    }


    /*
    |--------------------------------------------------------------------------
    | DISTRICT / UPAZILA / SECTION AJAX
    |--------------------------------------------------------------------------
    */

    public function getDistricts($divisionId)
    {
        return response()->json(
            District::where('division_id', $divisionId)
                ->orderBy('name_bn')
                ->get([
                    'id',
                    'name_bn'
                ])
        );
    }


    public function getUpazilas($districtId)
    {
        return response()->json(
            Upazila::where('district_id', $districtId)
                ->orderBy('name_bn')
                ->get([
                    'id',
                    'name_bn'
                ])
        );
    }


    public function getSections($classId)
    {
        return response()->json(
            Section::where('class_id', $classId)
                ->where('status', 1)
                ->orderBy('section_name')
                ->get([
                    'id',
                    'section_name'
                ])
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ADDRESS GENERATOR
    |--------------------------------------------------------------------------
    */

    private function generateAddressString(
        Request $request,
        string $type
    ) {

        $parts = [];


        $villageRoad = $request->input(
            "{$type}_village_road"
        );

        if ($villageRoad) {
            $parts[] = $villageRoad;
        }


        $union = $request->input(
            "{$type}_union"
        );

        if ($union) {
            $parts[] = $union;
        }


        $upazilaId = $request->input(
            "{$type}_upazila_id"
        );

        if ($upazilaId) {

            $upazila = Upazila::find($upazilaId);

            if ($upazila) {
                $parts[] = $upazila->name_bn;
            }
        }


        $districtId = $request->input(
            "{$type}_district_id"
        );

        if ($districtId) {

            $district = District::find($districtId);

            if ($district) {
                $parts[] = $district->name_bn;
            }
        }


        $divisionId = $request->input(
            "{$type}_division_id"
        );

        if ($divisionId) {

            $division = Division::find($divisionId);

            if ($division) {
                $parts[] = $division->name_bn;
            }
        }


        $postalCode = $request->input(
            "{$type}_postal_code"
        );

        if ($postalCode) {
            $parts[] = $postalCode;
        }


        return implode(', ', $parts);
    }


    /*
    |--------------------------------------------------------------------------
    | USER CODE PREVIEW
    |--------------------------------------------------------------------------
    */

    public function previewId(Request $request)
    {
        $generationType = $request->input(
            'generation_type',
            'auto'
        );


        if (!in_array($generationType, ['auto', 'manual'], true)) {

            return response()->json([
                'success' => false,
                'message' => 'Code Generation Type সঠিক নয়।',
            ], 422);
        }


        $userType = null;


        if ($request->filled('user_type_id')) {

            $userType = UserType::active()
                ->whereIn('slug', self::ALLOWED_USER_TYPE_SLUGS)
                ->find($request->integer('user_type_id'));

        } elseif ($request->filled('user_type')) {

            $userType = UserType::active()
                ->whereIn('slug', self::ALLOWED_USER_TYPE_SLUGS)
                ->where('slug', $request->input('user_type'))
                ->first();
        }


        if (!$userType) {

            return response()->json([
                'success' => false,
                'message' => 'User Type সঠিক নয়।',
            ], 422);
        }


        $authUser = auth()->user();


        /*
         * Role is no longer required from Preview AJAX.
         */
        if ($authUser->is_super_admin) {

            $institutionId = session()->has('active_institution_id')
                ? (int) session('active_institution_id')
                : (
                    $request->filled('institution_id')
                        ? (int) $request->institution_id
                        : null
                );

        } elseif ($authUser->is_support_admin) {

            $institutionId = $request->filled('institution_id')
                ? (int) $request->institution_id
                : null;

        } else {

            $institutionId = $authUser->institution_id;
        }


        if (!$institutionId) {

            return response()->json([
                'success' => false,
                'message' => 'প্রতিষ্ঠান নির্বাচন করুন।',
            ], 422);
        }


        if (!Madrasa::where('id', $institutionId)->exists()) {

            return response()->json([
                'success' => false,
                'message' => 'নির্বাচিত প্রতিষ্ঠান পাওয়া যায়নি।',
            ], 422);
        }


        $previewId = $generationType === 'manual'

            ? $this->generateManualUserTypeCode(
                $institutionId,
                (int) $userType->id
            )

            : $this->generateUserTypeCode(
                $institutionId,
                (int) $userType->id
            );


        return response()->json([

            'success' => true,

            'preview_id' => $previewId,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | USER CODE SETTING INSTITUTION
    |--------------------------------------------------------------------------
    */

    private function getCodeSettingInstitutionId(): ?int
    {
        $authUser = auth()->user();

        if (!$authUser) {
            return null;
        }


        if (
            $authUser->is_super_admin
            || $authUser->is_support_admin
        ) {

            $activeInstitutionId = session(
                'active_institution_id'
            );

            return $activeInstitutionId
                ? (int) $activeInstitutionId
                : null;
        }


        return $authUser->institution_id
            ? (int) $authUser->institution_id
            : null;
    }


    /*
    |--------------------------------------------------------------------------
    | GET USER CODE SETTINGS
    |--------------------------------------------------------------------------
    */

    public function getCodeSetting()
    {
        $institutionId = $this->getCodeSettingInstitutionId();


        if (!$institutionId) {

            return response()->json([
                'success' => false,
                'message' => 'প্রতিষ্ঠান নির্বাচন করা নেই।',
            ], 422);
        }


        $userTypes = $this->getAllowedUserTypes();

        $settings = [];


        foreach ($userTypes as $userType) {

            $setting = $this->findUserTypeSetting(
                $institutionId,
                $userType
            );


            $settings[$userType->id] = [

                'user_type_id' => (int) $userType->id,

                'user_type' => $userType->slug,

                'name' => $userType->display_name,

                'start_code' => $setting
                    ? (int) $setting->start_code
                    : $this->getDefaultUserTypeCodeStart($userType),

                'manual_start_code' => (
                    $setting
                    && $setting->manual_start_code !== null
                )
                    ? (int) $setting->manual_start_code
                    : null,

                'gender_preference' => (
                    $setting
                    && in_array(
                        $setting->gender_preference,
                        ['male', 'female', 'entry'],
                        true
                    )
                )
                    ? $setting->gender_preference
                    : 'male',

            ];
        }


        return response()->json([

            'success' => true,

            'user_types' => $userTypes,

            'settings' => $settings,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SAVE USER CODE SETTINGS
    |--------------------------------------------------------------------------
    */

    public function saveCodeSetting(Request $request)
    {
        $institutionId = $this->getCodeSettingInstitutionId();


        if (!$institutionId) {

            return response()->json([
                'success' => false,
                'message' => 'প্রতিষ্ঠান নির্বাচন করা নেই।',
            ], 422);
        }


        $validator = Validator::make(
            $request->all(),
            [

                'user_type_id' => [
                    'required',
                    'integer',
                    'exists:user_types,id'
                ],

                'generation_type' => [
                    'required',
                    'in:auto,manual'
                ],

                'start_code' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:999999999'
                ],

                'manual_start_code' => [
                    'nullable',
                    'integer',
                    'min:1',
                    'max:999999999'
                ],

                'gender_preference' => [
                    'required',
                    'in:male,female,entry'
                ],

            ],
            [

                'user_type_id.required' => 'User Type নির্বাচন করুন।',

                'user_type_id.exists' => 'নির্বাচিত User Type পাওয়া যায়নি।',

                'generation_type.required' => 'Code Generation Type নির্বাচন করুন।',

                'start_code.required' => 'Auto Starting Code দিতে হবে।',

                'start_code.integer' => 'Starting Code অবশ্যই পূর্ণসংখ্যা হতে হবে।',

                'manual_start_code.integer' => 'Manual Code অবশ্যই পূর্ণসংখ্যা হতে হবে।',

                'gender_preference.required' => 'Gender Preference নির্বাচন করুন।',

            ]
        );


        if ($validator->fails()) {

            return response()->json([

                'success' => false,

                'message' => $validator->errors()->first(),

                'errors' => $validator->errors(),

            ], 422);
        }


        $userType = UserType::active()
            ->whereIn('slug', self::ALLOWED_USER_TYPE_SLUGS)
            ->find($request->integer('user_type_id'));


        if (!$userType) {

            return response()->json([

                'success' => false,

                'message' => 'নিষ্ক্রিয় অথবা অনুপস্থিত User Type নির্বাচন করা যাবে না।',

            ], 422);
        }


        if (
            $request->input('generation_type') === 'manual'
            &&
            (
                !$request->filled('manual_start_code')
                ||
                (int) $request->manual_start_code < 1
            )
        ) {

            return response()->json([

                'success' => false,

                'message' => 'Manual Starting Code দিতে হবে।',

            ], 422);
        }


        $setting = $this->saveOrFindUserTypeSetting(
            $institutionId,
            $userType
        );


        if ($request->input('generation_type') === 'auto') {

            $setting->start_code = (int) $request->start_code;

        } else {

            $setting->manual_start_code = (int) $request->manual_start_code;
        }


        $setting->gender_preference = $request->gender_preference;

        $setting->save();


        return response()->json([

            'success' => true,

            'message' => 'User Code Setting সফলভাবে সংরক্ষণ করা হয়েছে।',

            'settings' => [

                $userType->id => [

                    'user_type_id' => (int) $userType->id,

                    'user_type' => $userType->slug,

                    'start_code' => (int) $setting->start_code,

                    'manual_start_code' => (
                        $setting->manual_start_code !== null
                    )
                        ? (int) $setting->manual_start_code
                        : null,

                    'gender_preference' => $setting->gender_preference,

                ],

            ],

        ]);
    }
}