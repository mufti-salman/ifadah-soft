<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class UserController extends Controller
{
    private const CODE_STARTS = [
        'student'       => 101,
        'user'          => 201,
        'user_admin'    => 301,
        'support_admin' => 401,
    ];

    private const CODE_ROLES = [
        'student',
        'user',
        'user_admin',
        'support_admin',
    ];

    private const INSTITUTION_CODE_ROLES = [
        'student',
        'user',
        'user_admin',
    ];

    /**
     * Check whether Super Admin is currently inside a Madrasa panel.
     */
    private function isSwitchedInstitutionMode(): bool
    {
        return auth()->user()->is_super_admin
            && session()->has('active_institution_id');
    }

    /**
     * Get active institution ID according to current context.
     */
    private function getActiveInstitutionId()
    {
        $authUser = auth()->user();

        if ($authUser->is_super_admin) {
            return session('active_institution_id');
        }

        return $authUser->institution_id;
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();

        $query = User::with([
            'role',
            'institution',
            'student',
        ]);

        if ($authUser->is_super_admin) {

            /*
             * Super Admin system mode:
             * Show everything except students.
             *
             * Super Admin switched Madrasa mode:
             * Show only student + user of selected institution.
             */
            if (session()->has('active_institution_id')) {

                $query->where(
                    'institution_id',
                    session('active_institution_id')
                );

                $query->whereHas('role', function ($q) {
                    $q->whereIn('slug', [
                        'student',
                        'user',
                    ]);
                });

            } else {

                /*
                 * Super Admin system mode:
                 * Students must never be shown.
                 */
                $query->whereHas('role', function ($q) {
                    $q->whereNotIn('slug', [
                        'student',
                    ]);
                });
            }

        } elseif ($authUser->is_support_admin) {

            $query->whereHas('role', function ($q) {
                $q->where('slug', 'user_admin');
            });

        } else {

            /*
             * Madrasa/User Admin:
             * Only own institution.
             *
             * Only student + user.
             */
            $query->where(
                'institution_id',
                $authUser->institution_id
            );

            $query->whereHas('role', function ($q) {
                $q->whereIn('slug', [
                    'student',
                    'user',
                ]);
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

        if ($request->filled('user_type')) {
            $userType = $request->user_type;

            $query->whereHas('role', function ($q) use ($userType) {
                $q->where('slug', $userType);
            });
        }

        $users = $query->latest('id')->paginate(10);

        $divisions = Division::all();

        return view(
            'admin.users.index',
            compact('users', 'divisions')
        );
    }

    public function create()
    {
        $authUser = auth()->user();

        $userTypes = [];

        /*
         * Super Admin:
         *
         * System mode:
         * user_admin + support_admin
         *
         * Switched Madrasa mode:
         * student + user
         */
        if ($authUser->is_super_admin) {

            if (session()->has('active_institution_id')) {

                $userTypes = [
                    'student' => 'শিক্ষার্থী',
                    'user'    => 'ইউজার',
                ];

            } else {

                $userTypes = [
                    'user_admin'    => 'ইউজার অ্যাডমিন',
                    'support_admin' => 'সাপোর্ট অ্যাডমিন',
                ];
            }

        } elseif ($authUser->is_support_admin) {

            $userTypes = [
                'user_admin' => 'ইউজার অ্যাডমিন',
            ];

        } elseif ($authUser->is_user_admin) {

            $userTypes = [
                'student' => 'শিক্ষার্থী',
                'user'    => 'ইউজার',
            ];
        }

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
            )
                ->where('status', 1)
                ->get();

        } else {

            $institutions = Madrasa::where(
                'id',
                $authUser->institution_id
            )
                ->where('status', 1)
                ->get();
        }

        if ($authUser->is_super_admin) {

            $institutionId =
                session('active_institution_id');

        } elseif ($authUser->is_support_admin) {

            $institutionId =
                request()->input('institution_id');

        } else {

            $institutionId =
                $authUser->institution_id;
        }

        $roles = Role::orderBy(
            'level',
            'desc'
        )->get();

        $previewIds = [];

        foreach ($userTypes as $type => $label) {

            if (in_array(
                $type,
                self::CODE_ROLES,
                true
            )) {

                if (
                    in_array(
                        $type,
                        self::INSTITUTION_CODE_ROLES,
                        true
                    )
                    && !$institutionId
                ) {

                    $previewIds[$type] = null;

                } else {

                    $previewIds[$type] =
                        $this->previewRoleCode(
                            $institutionId,
                            $type
                        );
                }
            }
        }

        $divisions = Division::with([
            'districts.upazilas'
        ])->get();

        $academicYears = AcademicYear::where(
            'status',
            1
        )
            ->orderBy('name', 'desc')
            ->get();

        $classes = Classes::where(
            'status',
            1
        )->get();

        return view(
            'admin.users.create',
            compact(
                'userTypes',
                'roles',
                'institutions',
                'divisions',
                'previewIds',
                'academicYears',
                'classes'
            )
        );
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $photoPath = null;

        try {

            $authUser = auth()->user();

            $role = Role::where(
                'slug',
                $request->user_type
            )->firstOrFail();

            $roleSlug = strtolower(
                trim($role->slug)
            );

            $allowedRoles = [];

            /*
             * Super Admin:
             *
             * System mode:
             * user_admin + support_admin
             *
             * Switched Madrasa mode:
             * student + user
             */
            if ($authUser->is_super_admin) {

                if (session()->has('active_institution_id')) {

                    $allowedRoles = [
                        'student',
                        'user',
                    ];

                } else {

                    $allowedRoles = [
                        'user_admin',
                        'support_admin',
                    ];
                }

            } elseif ($authUser->is_support_admin) {

                $allowedRoles = [
                    'user_admin',
                ];

            } elseif ($authUser->is_user_admin) {

                $allowedRoles = [
                    'student',
                    'user',
                ];
            }

            if (!in_array(
                $roleSlug,
                $allowedRoles,
                true
            )) {
                abort(
                    403,
                    'এই Role তৈরি করার অনুমতি আপনার নেই।'
                );
            }

            if (!in_array(
                $roleSlug,
                self::CODE_ROLES,
                true
            )) {
                abort(
                    403,
                    'এই Role-এর জন্য User Code তৈরি করা যাবে না।'
                );
            }

            $isStudent =
                $roleSlug === 'student';

            $institutionRequired =
                in_array(
                    $roleSlug,
                    self::INSTITUTION_CODE_ROLES,
                    true
                );

            /*
             * Institution selection.
             *
             * Super Admin switched mode:
             * always use active institution from session.
             */
            if ($roleSlug === 'support_admin') {

                $institutionId = null;

            } elseif ($authUser->is_super_admin) {

                if (
                    session()->has(
                        'active_institution_id'
                    )
                ) {

                    $institutionId =
                        session(
                            'active_institution_id'
                        );

                } else {

                    $institutionId =
                        $request->institution_id;
                }

            } elseif ($authUser->is_support_admin) {

                $institutionId =
                    $request->institution_id;

            } else {

                $institutionId =
                    $authUser->institution_id;
            }

            if (
                $institutionRequired
                && !$institutionId
            ) {

                throw ValidationException::withMessages([
                    'institution_id' =>
                        'প্রতিষ্ঠান নির্বাচন করা আবশ্যক।',
                ]);
            }

            $rules = [

                'user_type' => [
                    'required',
                    'exists:roles,slug'
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

                'date_of_birth' => [
                    'nullable',
                    'date'
                ],

                'blood_group' => [
                    'nullable',
                    'string',
                    'max:10'
                ],

                'religion' => [
                    'nullable',
                    'string',
                    'max:50'
                ],

                'nid' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'birth_certificate' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'phone2' => [
                    'nullable',
                    'string',
                    'max:30'
                ],

                'phone_relation' => [
                    'nullable',
                    'string',
                    'max:50'
                ],

                'phone2_relation' => [
                    'nullable',
                    'string',
                    'max:50'
                ],

                'father_name' => [
                    'nullable',
                    'string',
                    'max:255'
                ],

                'father_phone' => [
                    'nullable',
                    'string',
                    'max:30'
                ],

                'mother_name' => [
                    'nullable',
                    'string',
                    'max:255'
                ],

                'mother_phone' => [
                    'nullable',
                    'string',
                    'max:30'
                ],

                'guardian_name' => [
                    'nullable',
                    'string',
                    'max:255'
                ],

                'guardian_phone' => [
                    'nullable',
                    'string',
                    'max:30'
                ],

                'guardian_relation' => [
                    'nullable',
                    'string',
                    'max:50'
                ],

                'code_generation_type' => [
                    'nullable',
                    'in:auto,manual'
                ],

                'custom_id' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'present_division_id' => [
                    'nullable',
                    'exists:divisions,id'
                ],

                'present_district_id' => [
                    'nullable',
                    'exists:districts,id'
                ],

                'present_upazila_id' => [
                    'nullable',
                    'exists:upazilas,id'
                ],

                'present_union' => [
                    'nullable',
                    'string',
                    'max:255'
                ],

                'present_post_office' => [
                    'nullable',
                    'string',
                    'max:255'
                ],

                'present_village_road' => [
                    'nullable',
                    'string',
                    'max:255'
                ],

                'present_postal_code' => [
                    'nullable',
                    'string',
                    'max:20'
                ],

                'permanent_division_id' => [
                    'nullable',
                    'exists:divisions,id'
                ],

                'permanent_district_id' => [
                    'nullable',
                    'exists:districts,id'
                ],

                'permanent_upazila_id' => [
                    'nullable',
                    'exists:upazilas,id'
                ],

                'permanent_union' => [
                    'nullable',
                    'string',
                    'max:255'
                ],

                'permanent_post_office' => [
                    'nullable',
                    'string',
                    'max:255'
                ],

                'permanent_village_road' => [
                    'nullable',
                    'string',
                    'max:255'
                ],

                'permanent_postal_code' => [
                    'nullable',
                    'string',
                    'max:20'
                ],
            ];

            /*
             * Institution is required from request only when:
             * - Super Admin is in system mode
             * - Support Admin
             *
             * In switched Super Admin mode,
             * institution comes from session.
             */
            if (
                (
                    (
                        $authUser->is_super_admin
                        && !session()->has(
                            'active_institution_id'
                        )
                    )
                    || $authUser->is_support_admin
                )
                && $institutionRequired
            ) {

                $rules['institution_id'] = [
                    'required',
                    'exists:madrasas,id',
                ];
            }

            if ($isStudent) {

                $rules['academic_year_id'] = [
                    'nullable',
                    'exists:academic_years,id'
                ];

                $rules['class_id'] = [
                    'nullable',
                    'exists:classes,id'
                ];

                $rules['section_id'] = [
                    'nullable',
                    'exists:sections,id'
                ];

                $rules['guardian_user_id'] = [
                    'nullable',
                    'exists:users,id'
                ];
            }

            $validated =
                $request->validate($rules);

            $generationType =
                $validated['code_generation_type']
                ?? 'auto';

            $gender =
                $validated['gender'];

            $customId = trim(
                (string)(
                    $validated['custom_id']
                    ?? ''
                )
            );

            /*
             * MANUAL CODE
             *
             * Manual sequence is independent
             * from Auto sequence.
             */
if ($generationType === 'manual') {

    /*
     * If user entered a Manual Code,
     * use exactly that code.
     */
    if ($customId !== '') {

        if (!preg_match(
            '/^[0-9]+$/',
            $customId
        )) {

            throw ValidationException::withMessages([
                'custom_id' =>
                    'User Code শুধুমাত্র সংখ্যা হতে হবে।',
            ]);
        }

        $minimumCode =
            self::CODE_STARTS[$roleSlug];

        if (
            (int) $customId < $minimumCode
        ) {

            throw ValidationException::withMessages([
                'custom_id' =>
                    'এই Role-এর User Code '
                    . $minimumCode
                    . ' থেকে শুরু হতে হবে।',
            ]);
        }

        /*
         * Check duplicate Manual Code
         */
        $codeQuery = User::query()
            ->where(
                'institution_user_id',
                $customId
            )
            ->where(
                'role_id',
                $role->id
            )
            ->where(
                'code_generation_type',
                'manual'
            );

        if (
            in_array(
                $roleSlug,
                self::INSTITUTION_CODE_ROLES,
                true
            )
        ) {

            $codeQuery->where(
                'institution_id',
                $institutionId
            );

        } elseif (
            $roleSlug === 'support_admin'
        ) {

            $codeQuery->whereNull(
                'institution_id'
            );
        }

        if ($codeQuery->exists()) {

            throw ValidationException::withMessages([
                'custom_id' =>
                    'এই Manual User Code ইতোমধ্যে ব্যবহার করা হয়েছে।',
            ]);
        }

    } else {

        /*
         * Manual selected but no code entered.
         *
         * Generate:
         * last saved Manual code + 1
         */
        $customId =
            $this->generateManualCode(
                $institutionId,
                $roleSlug
            );
    }
}
            /*
             * AUTO CODE
             *
             * Auto sequence is independent
             * from Manual sequence.
             */
            if ($generationType === 'auto') {
                $customId = '';
            }

            $username =
                $this->generateUsername(
                    $request->name,
                    $request->name_bn
                );

            if ($request->hasFile('photo')) {

                $photoPath =
                    $request->file('photo')
                        ->store(
                            'users/photos',
                            'public'
                        );
            }

            if ($generationType === 'manual') {

                $institutionUserId =
                    $customId;

            } else {

                $institutionUserId =
                    $this->generateRoleCode(
                        $institutionId,
                        $roleSlug
                    );
            }

            $user = User::create([

                'institution_user_id' =>
                    $institutionUserId,

                'code_generation_type' =>
                    $generationType,

                'institution_id' =>
                    $institutionId,

                'role_id' =>
                    $role->id,

                'username' =>
                    $username,

                'email' =>
                    $request->email,

                'password' =>
                    Hash::make(
                        $request->password
                        ?? 'password123'
                    ),

                'name' =>
                    $request->name,

                'name_bn' =>
                    $request->name_bn,

                'photo' =>
                    $photoPath,

                'phone' =>
                    $request->phone,

                'phone2' =>
                    $request->phone2,

                'phone_owner' =>
                    $request->phone_relation,

                'gender' =>
                    $gender,

                'date_of_birth' =>
                    $request->date_of_birth,

                'age' =>
                    $request->date_of_birth
                        ? Carbon::parse(
                            $request->date_of_birth
                        )->age
                        : null,

                'blood_group' =>
                    $request->blood_group,

                'religion' =>
                    $request->religion,

                'nid' =>
                    $request->nid,

                'birth_certificate' =>
                    $request->birth_certificate,

                'father_name' =>
                    $request->father_name,

                'father_phone' =>
                    $request->father_phone,

                'mother_name' =>
                    $request->mother_name,

                'mother_phone' =>
                    $request->mother_phone,

                'guardian_name' =>
                    $request->guardian_name,

                'guardian_phone' =>
                    $request->guardian_phone,

                'guardian_relation' =>
                    $request->guardian_relation,

                'present_division_id' =>
                    $request->present_division_id,

                'present_district_id' =>
                    $request->present_district_id,

                'present_upazila_id' =>
                    $request->present_upazila_id,

                'present_union' =>
                    $request->present_union,

                'present_post_office' =>
                    $request->present_post_office,

                'present_village_road' =>
                    $request->present_village_road,

                'present_postal_code' =>
                    $request->present_postal_code,

                'present_address_full' =>
                    $this->generateAddressString(
                        $request,
                        'present'
                    ),

                'permanent_division_id' =>
                    $request->permanent_division_id,

                'permanent_district_id' =>
                    $request->permanent_district_id,

                'permanent_upazila_id' =>
                    $request->permanent_upazila_id,

                'permanent_union' =>
                    $request->permanent_union,

                'permanent_post_office' =>
                    $request->permanent_post_office,

                'permanent_village_road' =>
                    $request->permanent_village_road,

                'permanent_postal_code' =>
                    $request->permanent_postal_code,

                'permanent_address_full' =>
                    $this->generateAddressString(
                        $request,
                        'permanent'
                    ),

                'status' => 1,

                'created_by' =>
                    auth()->id(),
            ]);

            if ($isStudent) {

                $academicYearId =
                    $request->academic_year_id;

                $studentId =
                    $this->generateStudentId(
                        $institutionId,
                        $academicYearId
                    );

                Student::create([

                    'user_id' =>
                        $user->id,

                    'institution_id' =>
                        $institutionId,

                    'academic_year_id' =>
                        $academicYearId,

                    'class_id' =>
                        $request->class_id,

                    'section_id' =>
                        $request->section_id,

                    'guardian_user_id' =>
                        $request->guardian_user_id,

                    'student_id' =>
                        $studentId,

                    'is_hostel' =>
                        $request->boolean(
                            'is_hostel'
                        ),

                    'is_transport' =>
                        $request->boolean(
                            'is_transport'
                        ),

                    'status' => 1,

                    'created_by' =>
                        auth()->id(),

                    'updated_by' =>
                        auth()->id(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('dashboard.user.index')
                ->with(
                    'success',
                    'ইউজার সফলভাবে তৈরি হয়েছে।'
                );

        } catch (ValidationException $e) {

            DB::rollBack();

            throw $e;

        } catch (\Exception $e) {

            DB::rollBack();

            if (
                $photoPath
                && Storage::disk('public')
                    ->exists($photoPath)
            ) {

                Storage::disk('public')
                    ->delete($photoPath);
            }

            return back()
                ->with(
                    'error',
                    'ইউজার তৈরি করতে সমস্যা হয়েছে: '
                    . $e->getMessage()
                )
                ->withInput();
        }
    }

private function generateRoleCode(
    $institutionId,
    string $roleSlug,
    ?int $excludeUserId = null
): string {

    if (!array_key_exists(
        $roleSlug,
        self::CODE_STARTS
    )) {

        throw ValidationException::withMessages([
            'user_type' =>
                'এই Role-এর জন্য User Code তৈরি করা যাবে না।',
        ]);
    }

    $role = Role::where(
        'slug',
        $roleSlug
    )->firstOrFail();

    /*
     * Role-specific AUTO starting number.
     *
     * student       => 101
     * user          => 201
     * user_admin    => 301
     * support_admin => 401
     */
    $nextCode =
        self::CODE_STARTS[$roleSlug];

    /*
     * Only AUTO generated codes are considered.
     *
     * MANUAL codes are completely ignored.
     */
    $query = User::query()
        ->where(
            'role_id',
            $role->id
        )
        ->whereNotNull(
            'institution_user_id'
        )
        ->where(
            'code_generation_type',
            'auto'
        );

    /*
     * Institution-based roles:
     *
     * student
     * user
     * user_admin
     */
    if (
        in_array(
            $roleSlug,
            self::INSTITUTION_CODE_ROLES,
            true
        )
    ) {

        if (!$institutionId) {

            return (string)
                self::CODE_STARTS[$roleSlug];
        }

        $query->where(
            'institution_id',
            $institutionId
        );

    } elseif (
        $roleSlug === 'support_admin'
    ) {

        /*
         * Support Admin has no institution.
         */
        $query->whereNull(
            'institution_id'
        );
    }

    if ($excludeUserId) {

        $query->where(
            'id',
            '!=',
            $excludeUserId
        );
    }

    /*
     * Get all existing AUTO codes.
     *
     * We do NOT use MAX() here.
     *
     * This is important because an old/manual code like:
     *
     * Student => 1001
     *
     * must NOT make Auto start from 1002.
     */
    $existingAutoCodes = $query
        ->whereRaw(
            'institution_user_id REGEXP "^[0-9]+$"'
        )
        ->pluck(
            'institution_user_id'
        )
        ->map(function ($code) {
            return (int)$code;
        })
        ->filter(function ($code) use ($nextCode) {
            return $code >= $nextCode;
        })
        ->unique()
        ->sort()
        ->values();


    foreach ($existingAutoCodes as $existingCode) {

        if ($existingCode === $nextCode) {

            $nextCode++;

            continue;
        }

        /*
         * First gap found.
         */
        if ($existingCode > $nextCode) {
            break;
        }
    }

    return (string)$nextCode;
}

    /**
     * Generate MANUAL sequence code.
     *
     * Manual sequence is independent from Auto.
     *
     * Example:
     *
     * Student manual starts:
     * 501
     *
     * Next manual:
     * 502
     *
     * Next manual:
     * 503
     *
     * Auto remains:
     * 101, 102, 103...
     */
private function generateManualCode(
    $institutionId,
    string $roleSlug,
    ?int $excludeUserId = null
): string {
    if (!array_key_exists($roleSlug, self::CODE_STARTS)) {
        throw ValidationException::withMessages([
            'custom_id' => 'এই User Type-এর জন্য Code Generation চালু নেই।',
        ]);
    }

    $role = Role::where('slug', $roleSlug)->firstOrFail();

    $query = User::query()
        ->where('role_id', $role->id)
        ->where('code_generation_type', 'manual')
        ->whereNotNull('institution_user_id');

    if (in_array($roleSlug, self::INSTITUTION_CODE_ROLES, true)) {
        if (!$institutionId) {
            return (string) self::CODE_STARTS[$roleSlug];
        }

        $query->where('institution_id', $institutionId);
    } elseif ($roleSlug === 'support_admin') {
        $query->whereNull('institution_id');
    }

    if ($excludeUserId) {
        $query->where('id', '!=', $excludeUserId);
    }

    $lastManualCode = $query
        ->whereRaw('institution_user_id REGEXP "^[0-9]+$"')
        ->orderByDesc('id')
        ->value('institution_user_id');

    if ($lastManualCode !== null && is_numeric($lastManualCode)) {
        return (string) ((int) $lastManualCode + 1);
    }

    return (string) self::CODE_STARTS[$roleSlug];
}

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

            $query->whereNull(
                'academic_year_id'
            );
        }

        $lastStudent =
            $query->latest('id')->first();

        $newNumber = 1;

        if (
            $lastStudent
            && $lastStudent->student_id
        ) {

            $studentIdString =
                $lastStudent->student_id;

            if ($academicYearId) {

                $pattern =
                    '/^STU-'
                    . preg_quote(
                        $institutionId,
                        '/'
                    )
                    . '-'
                    . preg_quote(
                        $academicYearId,
                        '/'
                    )
                    . '-(\d+)$/';

            } else {

                $pattern =
                    '/^STU-'
                    . preg_quote(
                        $institutionId,
                        '/'
                    )
                    . '-(\d+)$/';
            }

            if (
                preg_match(
                    $pattern,
                    $studentIdString,
                    $matches
                )
            ) {

                $newNumber =
                    ((int)$matches[1]) + 1;
            }
        }

        if ($academicYearId) {

            $studentId =
                'STU-'
                . $institutionId
                . '-'
                . $academicYearId
                . '-'
                . str_pad(
                    $newNumber,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

        } else {

            $studentId =
                'STU-'
                . $institutionId
                . '-'
                . str_pad(
                    $newNumber,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
        }

        while (
            Student::where(
                'student_id',
                $studentId
            )->exists()
        ) {

            $newNumber++;

            if ($academicYearId) {

                $studentId =
                    'STU-'
                    . $institutionId
                    . '-'
                    . $academicYearId
                    . '-'
                    . str_pad(
                        $newNumber,
                        4,
                        '0',
                        STR_PAD_LEFT
                    );

            } else {

                $studentId =
                    'STU-'
                    . $institutionId
                    . '-'
                    . str_pad(
                        $newNumber,
                        4,
                        '0',
                        STR_PAD_LEFT
                    );
            }
        }

        return $studentId;
    }

    public function show($id)
    {
        $authUser = auth()->user();

        $user = User::with([
            'role',
            'institution',
            'student',
            'creator',
            'updater',
            'presentDivision',
            'presentDistrict',
            'presentUpazila',
            'permanentDivision',
            'permanentDistrict',
            'permanentUpazila',
        ])->findOrFail($id);

        /*
         * Switched Super Admin can only access
         * users from active institution.
         */
        if (
            $authUser->is_super_admin
            && session()->has(
                'active_institution_id'
            )
        ) {

            if (
                (string)$user->institution_id
                !==
                (string)session(
                    'active_institution_id'
                )
            ) {

                abort(
                    403,
                    'এই User দেখার অনুমতি আপনার নেই।'
                );
            }

            if (
                !in_array(
                    optional($user->role)->slug,
                    [
                        'student',
                        'user',
                    ],
                    true
                )
            ) {

                abort(
                    403,
                    'এই User দেখার অনুমতি আপনার নেই।'
                );
            }

        } elseif (
            $authUser->is_super_admin
            && !session()->has(
                'active_institution_id'
            )
        ) {

            /*
             * System Super Admin:
             * Students cannot be accessed.
             */
            if (
                optional($user->role)->slug
                === 'student'
            ) {

                abort(
                    403,
                    'এই User দেখার অনুমতি আপনার নেই।'
                );
            }

        } elseif (
            !$authUser->is_super_admin
            && !$authUser->is_support_admin
            && $user->institution_id
                !== $authUser->institution_id
        ) {

            abort(
                403,
                'এই User দেখার অনুমতি আপনার নেই।'
            );
        }

        if (
            $authUser->is_support_admin
            && optional($user->role)->slug
                !== 'user_admin'
        ) {

            abort(
                403,
                'এই User দেখার অনুমতি আপনার নেই।'
            );
        }

        return view(
            'admin.users.show',
            compact('user')
        );
    }

    public function edit($id)
    {
        $authUser = auth()->user();

        $user = User::with([
            'role',
            'student',
            'institution',
        ])->findOrFail($id);

        /*
         * Switched Super Admin:
         * only selected institution student/user.
         */
        if (
            $authUser->is_super_admin
            && session()->has(
                'active_institution_id'
            )
        ) {

            if (
                (string)$user->institution_id
                !==
                (string)session(
                    'active_institution_id'
                )
            ) {

                abort(
                    403,
                    'এই User edit করার অনুমতি আপনার নেই।'
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
                    'এই User edit করার অনুমতি আপনার নেই।'
                );
            }

        } elseif (
            $authUser->is_super_admin
            && !session()->has(
                'active_institution_id'
            )
        ) {

            /*
             * System Super Admin:
             * Student cannot be edited.
             */
            if (
                optional($user->role)->slug
                === 'student'
            ) {

                abort(
                    403,
                    'এই User edit করার অনুমতি আপনার নেই।'
                );
            }

        } elseif (
            !$authUser->is_super_admin
            && !$authUser->is_support_admin
            && $user->institution_id
                !== $authUser->institution_id
        ) {

            abort(
                403,
                'এই User edit করার অনুমতি আপনার নেই।'
            );
        }

        if (
            $authUser->is_support_admin
            && optional($user->role)->slug
                !== 'user_admin'
        ) {

            abort(
                403,
                'এই User edit করার অনুমতি আপনার নেই।'
            );
        }

        if (
            $authUser->is_super_admin
            && session()->has(
                'active_institution_id'
            )
        ) {

            $institutions = Madrasa::where(
                'id',
                session('active_institution_id')
            )->get();

        } elseif (
            $authUser->is_super_admin
            || $authUser->is_support_admin
        ) {

            $institutions = Madrasa::where(
                'status',
                1
            )->get();

        } else {

            $institutions = Madrasa::where(
                'id',
                $authUser->institution_id
            )->get();
        }

        $roles = Role::orderBy(
            'level',
            'desc'
        )->get();

        $divisions = Division::with([
            'districts.upazilas'
        ])->get();

        $academicYears = AcademicYear::where(
            'status',
            1
        )
            ->orderBy('name', 'desc')
            ->get();

        $classes = Classes::where(
            'status',
            1
        )->get();

        $sections = collect();

        if (
            $user->student
            && $user->student->class_id
        ) {

            $sections = Section::where(
                'class_id',
                $user->student->class_id
            )
                ->where('status', 1)
                ->get();
        }

        $userTypes = [];

        if ($authUser->is_super_admin) {

            if (
                session()->has(
                    'active_institution_id'
                )
            ) {

                $userTypes = [
                    'student' => 'শিক্ষার্থী',
                    'user'    => 'ইউজার',
                ];

            } else {

                $userTypes = [
                    'user_admin' =>
                        'ইউজার অ্যাডমিন',

                    'support_admin' =>
                        'সাপোর্ট অ্যাডমিন',
                ];
            }

        } elseif ($authUser->is_support_admin) {

            $userTypes = [
                'user_admin' =>
                    'ইউজার অ্যাডমিন',
            ];

        } elseif ($authUser->is_user_admin) {

            $userTypes = [
                'student' =>
                    'শিক্ষার্থী',

                'user' =>
                    'ইউজার',
            ];
        }

        return view(
            'admin.users.edit',
            compact(
                'user',
                'roles',
                'institutions',
                'divisions',
                'academicYears',
                'classes',
                'sections',
                'userTypes'
            )
        );
    }

    public function update(
        Request $request,
        $id
    ) {
        $authUser = auth()->user();

        $user = User::with([
            'role',
            'student',
        ])->findOrFail($id);

        /*
         * Switched Super Admin:
         * only selected institution student/user.
         */
        if (
            $authUser->is_super_admin
            && session()->has(
                'active_institution_id'
            )
        ) {

            if (
                (string)$user->institution_id
                !==
                (string)session(
                    'active_institution_id'
                )
            ) {

                abort(
                    403,
                    'এই User update করার অনুমতি আপনার নেই।'
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
                    'এই User update করার অনুমতি আপনার নেই।'
                );
            }

        } elseif (
            $authUser->is_super_admin
            && !session()->has(
                'active_institution_id'
            )
        ) {

            /*
             * System Super Admin:
             * Student cannot be updated.
             */
            if (
                optional($user->role)->slug
                === 'student'
            ) {

                abort(
                    403,
                    'এই User update করার অনুমতি আপনার নেই।'
                );
            }

        } elseif (
            !$authUser->is_super_admin
            && !$authUser->is_support_admin
            && $user->institution_id
                !== $authUser->institution_id
        ) {

            abort(
                403,
                'এই User update করার অনুমতি আপনার নেই।'
            );
        }

        if (
            $authUser->is_support_admin
            && optional($user->role)->slug
                !== 'user_admin'
        ) {

            abort(
                403,
                'এই User update করার অনুমতি আপনার নেই।'
            );
        }

        $role = Role::where(
            'slug',
            $request->user_type
        )->firstOrFail();

        $roleSlug = strtolower(
            trim($role->slug)
        );

        $allowedRoles = [];

        if ($authUser->is_super_admin) {

            if (
                session()->has(
                    'active_institution_id'
                )
            ) {

                $allowedRoles = [
                    'student',
                    'user',
                ];

            } else {

                $allowedRoles = [
                    'user_admin',
                    'support_admin',
                ];
            }

        } elseif ($authUser->is_support_admin) {

            $allowedRoles = [
                'user_admin',
            ];

        } elseif ($authUser->is_user_admin) {

            $allowedRoles = [
                'student',
                'user',
            ];
        }

        if (!in_array(
            $roleSlug,
            $allowedRoles,
            true
        )) {

            return back()
                ->with(
                    'error',
                    'এই Role assign করার অনুমতি আপনার নেই।'
                )
                ->withInput();
        }

        if (!in_array(
            $roleSlug,
            self::CODE_ROLES,
            true
        )) {

            return back()
                ->with(
                    'error',
                    'এই Role User Code System-এর অংশ নয়।'
                )
                ->withInput();
        }

        $isStudent =
            $roleSlug === 'student';

        $institutionRequired =
            in_array(
                $roleSlug,
                self::INSTITUTION_CODE_ROLES,
                true
            );

        /*
         * Institution assignment.
         */
        if ($roleSlug === 'support_admin') {

            $institutionId = null;

        } elseif ($authUser->is_super_admin) {

            if (
                session()->has(
                    'active_institution_id'
                )
            ) {

                $institutionId =
                    session(
                        'active_institution_id'
                    );

            } else {

                $institutionId =
                    $request->institution_id;
            }

        } elseif ($authUser->is_support_admin) {

            $institutionId =
                $request->institution_id;

        } else {

            $institutionId =
                $authUser->institution_id;
        }

        $rules = [

            'user_type' => [
                'required',
                'exists:roles,slug'
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

            'gender' => [
                'required',
                'in:male,female'
            ],

            'password' => [
                'nullable',
                'string',
                'min:6'
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048'
            ],

            'date_of_birth' => [
                'nullable',
                'date'
            ],

            'blood_group' => [
                'nullable',
                'string',
                'max:10'
            ],

            'religion' => [
                'nullable',
                'string',
                'max:50'
            ],

            'nid' => [
                'nullable',
                'string',
                'max:100'
            ],

            'birth_certificate' => [
                'nullable',
                'string',
                'max:100'
            ],

            'phone2' => [
                'nullable',
                'string',
                'max:30'
            ],

            'code_generation_type' => [
                'nullable',
                'in:auto,manual'
            ],

            'custom_id' => [
                'nullable',
                'string',
                'max:100'
            ],
        ];

        /*
         * Institution is required from request only
         * in system mode.
         */
        if (
            (
                (
                    $authUser->is_super_admin
                    && !session()->has(
                        'active_institution_id'
                    )
                )
                || $authUser->is_support_admin
            )
            && $institutionRequired
        ) {

            $rules['institution_id'] = [
                'required',
                'exists:madrasas,id',
            ];
        }

        if ($isStudent) {

            $rules['academic_year_id'] = [
                'nullable',
                'exists:academic_years,id'
            ];

            $rules['class_id'] = [
                'nullable',
                'exists:classes,id'
            ];

            $rules['section_id'] = [
                'nullable',
                'exists:sections,id'
            ];

            $rules['guardian_user_id'] = [
                'nullable',
                'exists:users,id'
            ];
        }

        $validated =
            $request->validate($rules);

        if (
            $institutionRequired
            && !$institutionId
        ) {

            throw ValidationException::withMessages([
                'institution_id' =>
                    'প্রতিষ্ঠান নির্বাচন করা আবশ্যক।',
            ]);
        }

        DB::beginTransaction();

        $oldPhoto =
            $user->photo;

        $photoPath =
            $user->photo;

        try {

            $generationType =
                $validated['code_generation_type']
                ?? 'auto';

            $gender =
                $validated['gender'];

            $customId = trim(
                (string)(
                    $validated['custom_id']
                    ?? ''
                )
            );

            /*
             * MANUAL CODE
             */
            if ($generationType === 'manual') {

                if ($customId !== '') {

                    if (!preg_match(
                        '/^[0-9]+$/',
                        $customId
                    )) {

                        throw ValidationException::withMessages([
                            'custom_id' =>
                                'User Code শুধুমাত্র সংখ্যা হতে হবে।',
                        ]);
                    }

                    /*
                     * Role starting number.
                     */
                    $minimumCode =
                        self::CODE_STARTS[$roleSlug];

                    if (
                        (int)$customId
                        < $minimumCode
                    ) {

                        throw ValidationException::withMessages([
                            'custom_id' =>
                                'এই Role-এর User Code '
                                . $minimumCode
                                . ' থেকে শুরু হতে হবে।',
                        ]);
                    }

                    $codeQuery = User::query()
                        ->where(
                            'institution_user_id',
                            $customId
                        )
                        ->where(
                            'role_id',
                            $role->id
                        )
                        ->where(
                            'id',
                            '!=',
                            $user->id
                        );

                    if (
                        in_array(
                            $roleSlug,
                            self::INSTITUTION_CODE_ROLES,
                            true
                        )
                    ) {

                        $codeQuery->where(
                            'institution_id',
                            $institutionId
                        );

                    } elseif (
                        $roleSlug === 'support_admin'
                    ) {

                        $codeQuery->whereNull(
                            'institution_id'
                        );
                    }

                    if ($codeQuery->exists()) {

                        throw ValidationException::withMessages([
                            'custom_id' =>
                                'এই Role ও প্রতিষ্ঠানের জন্য User Code ইতোমধ্যে ব্যবহার করা হয়েছে।',
                        ]);
                    }

                    $institutionUserId =
                        $customId;

                } else {

                    /*
                     * Generate next MANUAL sequence.
                     */
                    $institutionUserId =
                        $this->generateManualCode(
                            $institutionId,
                            $roleSlug,
                            $user->id
                        );
                }

            } else {

                /*
                 * AUTO CODE
                 *
                 * Existing auto code remains unchanged
                 * when role + institution are unchanged.
                 */
                $sameRole =
                    $user->role_id === $role->id;

                $sameInstitution =
                    (string)$user->institution_id
                    ===
                    (string)$institutionId;

                if (
                    $user->institution_user_id
                    && $user->code_generation_type
                        === 'auto'
                    && $sameRole
                    && $sameInstitution
                ) {

                    $institutionUserId =
                        $user->institution_user_id;

                } else {

                    $institutionUserId =
                        $this->generateRoleCode(
                            $institutionId,
                            $roleSlug,
                            $user->id
                        );
                }
            }

            if ($request->hasFile('photo')) {

                if (
                    $oldPhoto
                    && Storage::disk('public')
                        ->exists($oldPhoto)
                ) {

                    Storage::disk('public')
                        ->delete($oldPhoto);
                }

                $photoPath =
                    $request->file('photo')
                        ->store(
                            'users/photos',
                            'public'
                        );
            }

            $data = [

                'institution_id' =>
                    $institutionId,

                'role_id' =>
                    $role->id,

                'institution_user_id' =>
                    $institutionUserId,

                'code_generation_type' =>
                    $generationType,

                'email' =>
                    $request->email,

                'name' =>
                    $request->name,

                'name_bn' =>
                    $request->name_bn,

                'photo' =>
                    $photoPath,

                'phone' =>
                    $request->phone,

                'phone2' =>
                    $request->phone2,

                'phone_owner' =>
                    $request->phone_relation,

                'gender' =>
                    $gender,

                'date_of_birth' =>
                    $request->date_of_birth,

                'age' =>
                    $request->date_of_birth
                        ? Carbon::parse(
                            $request->date_of_birth
                        )->age
                        : null,

                'blood_group' =>
                    $request->blood_group,

                'religion' =>
                    $request->religion,

                'nid' =>
                    $request->nid,

                'birth_certificate' =>
                    $request->birth_certificate,

                'father_name' =>
                    $request->father_name,

                'father_phone' =>
                    $request->father_phone,

                'mother_name' =>
                    $request->mother_name,

                'mother_phone' =>
                    $request->mother_phone,

                'guardian_name' =>
                    $request->guardian_name,

                'guardian_phone' =>
                    $request->guardian_phone,

                'guardian_relation' =>
                    $request->guardian_relation,

                'present_division_id' =>
                    $request->present_division_id,

                'present_district_id' =>
                    $request->present_district_id,

                'present_upazila_id' =>
                    $request->present_upazila_id,

                'present_union' =>
                    $request->present_union,

                'present_post_office' =>
                    $request->present_post_office,

                'present_village_road' =>
                    $request->present_village_road,

                'present_postal_code' =>
                    $request->present_postal_code,

                'present_address_full' =>
                    $this->generateAddressString(
                        $request,
                        'present'
                    ),

                'permanent_division_id' =>
                    $request->permanent_division_id,

                'permanent_district_id' =>
                    $request->permanent_district_id,

                'permanent_upazila_id' =>
                    $request->permanent_upazila_id,

                'permanent_union' =>
                    $request->permanent_union,

                'permanent_post_office' =>
                    $request->permanent_post_office,

                'permanent_village_road' =>
                    $request->permanent_village_road,

                'permanent_postal_code' =>
                    $request->permanent_postal_code,

                'updated_by' =>
                    auth()->id(),
            ];

            if ($request->filled('password')) {

                $data['password'] =
                    Hash::make(
                        $request->password
                    );
            }

            $user->update($data);

            if ($isStudent) {

                $studentData = [

                    'institution_id' =>
                        $institutionId,

                    'academic_year_id' =>
                        $request->academic_year_id,

                    'class_id' =>
                        $request->class_id,

                    'section_id' =>
                        $request->section_id,

                    'guardian_user_id' =>
                        $request->guardian_user_id,

                    'is_hostel' =>
                        $request->boolean(
                            'is_hostel'
                        ),

                    'is_transport' =>
                        $request->boolean(
                            'is_transport'
                        ),

                    'updated_by' =>
                        auth()->id(),
                ];

                if ($user->student) {

                    $user->student->update(
                        $studentData
                    );

                } else {

                    $studentData['user_id'] =
                        $user->id;

                    $studentData['student_id'] =
                        $this->generateStudentId(
                            $institutionId,
                            $request->academic_year_id
                        );

                    $studentData['status'] = 1;

                    $studentData['created_by'] =
                        auth()->id();

                    Student::create(
                        $studentData
                    );
                }

            } else {

                if ($user->student) {
                    $user->student->delete();
                }
            }

            DB::commit();

            return redirect()
                ->route('dashboard.user.index')
                ->with(
                    'success',
                    'ইউজার সফলভাবে আপডেট হয়েছে.'
                );

        } catch (ValidationException $e) {

            DB::rollBack();

            if (
                $photoPath
                && $photoPath !== $oldPhoto
                && Storage::disk('public')
                    ->exists($photoPath)
            ) {

                Storage::disk('public')
                    ->delete($photoPath);
            }

            throw $e;

        } catch (\Exception $e) {

            DB::rollBack();

            if (
                $photoPath
                && $photoPath !== $oldPhoto
                && Storage::disk('public')
                    ->exists($photoPath)
            ) {

                Storage::disk('public')
                    ->delete($photoPath);
            }

            return back()
                ->with(
                    'error',
                    'আপডেট করতে সমস্যা হয়েছে: '
                    . $e->getMessage()
                )
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {

            $user = User::with([
                'role',
                'student'
            ])->findOrFail($id);

            if (
                optional($user->role)->slug
                === 'super_admin'
            ) {

                return back()->with(
                    'error',
                    'Super Admin ডিলিট করা যাবে না।'
                );
            }

            $authUser = auth()->user();

            /*
             * Switched Super Admin:
             * only student/user of selected institution.
             */
            if (
                $authUser->is_super_admin
                && session()->has(
                    'active_institution_id'
                )
            ) {

                if (
                    (string)$user->institution_id
                    !==
                    (string)session(
                        'active_institution_id'
                    )
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
                && !session()->has(
                    'active_institution_id'
                )
            ) {

                /*
                 * System Super Admin:
                 * Student cannot be deleted.
                 */
                if (
                    optional($user->role)->slug
                    === 'student'
                ) {

                    abort(
                        403,
                        'এই User ডিলিট করার অনুমতি আপনার নেই।'
                    );
                }

            } elseif (
                $authUser->is_support_admin
            ) {

                if (
                    optional($user->role)->slug
                    !== 'user_admin'
                ) {

                    abort(
                        403,
                        'এই User ডিলিট করার অনুমতি আপনার নেই।'
                    );
                }

            } elseif (
                !$authUser->is_super_admin
            ) {

                if (
                    $user->institution_id
                    !==
                    $authUser->institution_id
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
                && Storage::disk('public')
                    ->exists($user->photo)
            ) {

                Storage::disk('public')
                    ->delete($user->photo);
            }

            $user->delete();

            return redirect()
                ->route('dashboard.user.index')
                ->with(
                    'success',
                    'ইউজার সফলভাবে ডিলিট হয়েছে.'
                );

        } catch (\Exception $e) {

            return back()
                ->with(
                    'error',
                    'ডিলিট করতে সমস্যা হয়েছে: '
                    . $e->getMessage()
                );
        }
    }

    public function toggleStatus($id)
    {
        $user = User::with(
            'role'
        )->findOrFail($id);

        if (
            optional($user->role)->slug
            === 'super_admin'
        ) {

            return back()->with(
                'error',
                'Super Admin-এর Status পরিবর্তন করা যাবে না।'
            );
        }

        $authUser = auth()->user();

        /*
         * Switched Super Admin:
         * only student/user of selected institution.
         */
        if (
            $authUser->is_super_admin
            && session()->has(
                'active_institution_id'
            )
        ) {

            if (
                (string)$user->institution_id
                !==
                (string)session(
                    'active_institution_id'
                )
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
            && !session()->has(
                'active_institution_id'
            )
        ) {

            /*
             * System Super Admin:
             * Student status cannot be changed.
             */
            if (
                optional($user->role)->slug
                === 'student'
            ) {

                abort(
                    403,
                    'এই User-এর Status পরিবর্তন করার অনুমতি আপনার নেই।'
                );
            }

        } elseif (
            $authUser->is_support_admin
        ) {

            if (
                optional($user->role)->slug
                !== 'user_admin'
            ) {

                abort(
                    403,
                    'এই User-এর Status পরিবর্তন করার অনুমতি আপনার নেই।'
                );
            }

        } elseif (
            !$authUser->is_super_admin
        ) {

            if (
                $user->institution_id
                !==
                $authUser->institution_id
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
            'status' =>
                $user->status == 1
                    ? 0
                    : 1,

            'updated_by' =>
                auth()->id(),
        ]);

        return back()->with(
            'success',
            'User Status সফলভাবে পরিবর্তন হয়েছে।'
        );
    }

    private function generateUsername(
        $nameEn = null,
        $nameBn = null
    ) {

        $base = preg_replace(
            '/[^a-zA-Z0-9]/',
            '',
            strtolower(
                $nameEn ?? ''
            )
        );

        if (empty($base)) {

            $base =
                'user'
                . substr(
                    md5(
                        ($nameBn ?? '')
                        . microtime(true)
                    ),
                    0,
                    6
                );
        }

        $username = $base;
        $counter = 1;

        while (
            User::where(
                'username',
                $username
            )->exists()
        ) {

            $username =
                $base . $counter;

            $counter++;
        }

        return $username;
    }

    public function getDistricts($divisionId)
    {
        return response()->json(
            District::where(
                'division_id',
                $divisionId
            )
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
            Upazila::where(
                'district_id',
                $districtId
            )
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
            Section::where(
                'class_id',
                $classId
            )
                ->where(
                    'status',
                    1
                )
                ->orderBy(
                    'section_name'
                )
                ->get([
                    'id',
                    'section_name'
                ])
        );
    }

    private function generateAddressString(
        Request $request,
        string $type
    ) {

        $parts = [];

        $villageRoad =
            $request->input(
                "{$type}_village_road"
            );

        if ($villageRoad) {
            $parts[] = $villageRoad;
        }

        $union =
            $request->input(
                "{$type}_union"
            );

        if ($union) {
            $parts[] = $union;
        }

        $upazilaId =
            $request->input(
                "{$type}_upazila_id"
            );

        if ($upazilaId) {

            $upazila =
                Upazila::find(
                    $upazilaId
                );

            if ($upazila) {
                $parts[] =
                    $upazila->name_bn;
            }
        }

        $districtId =
            $request->input(
                "{$type}_district_id"
            );

        if ($districtId) {

            $district =
                District::find(
                    $districtId
                );

            if ($district) {
                $parts[] =
                    $district->name_bn;
            }
        }

        $divisionId =
            $request->input(
                "{$type}_division_id"
            );

        if ($divisionId) {

            $division =
                Division::find(
                    $divisionId
                );

            if ($division) {
                $parts[] =
                    $division->name_bn;
            }
        }

        $postalCode =
            $request->input(
                "{$type}_postal_code"
            );

        if ($postalCode) {
            $parts[] = $postalCode;
        }

        return implode(
            ', ',
            $parts
        );
    }

public function previewId(Request $request)
{
    $userType = $request->input('user_type');
    $institutionId = $request->input('institution_id');
    $generationType = $request->input('generation_type', 'auto');

    if ($generationType === 'manual') {

        $previewId = $this->generateManualCode(
            $institutionId,
            $userType
        );

    } else {

        $previewId = $this->previewRoleCode(
            $institutionId,
            $userType
        );
    }

    return response()->json([
        'success' => true,
        'preview_id' => $previewId,
    ]);
}

    private function previewRoleCode(
        $institutionId,
        string $roleSlug
    ): ?string {

        if (!in_array(
            $roleSlug,
            self::CODE_ROLES,
            true
        )) {
            return null;
        }

        if (
            in_array(
                $roleSlug,
                self::INSTITUTION_CODE_ROLES,
                true
            )
            && !$institutionId
        ) {
            return null;
        }

        return $this->generateRoleCode(
            $institutionId,
            $roleSlug
        );
    }
}