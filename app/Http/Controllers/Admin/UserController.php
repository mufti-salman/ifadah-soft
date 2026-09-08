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
    /**
     * =========================================================================
     * USER LIST
     * =========================================================================
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */

        $query = User::with([
            'role',
            'institution',
            'student',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Institution Restriction
        |--------------------------------------------------------------------------
        |
        | Super Admin:
        |     সব institution-এর user দেখতে পারবে।
        |
        | Other users:
        |     নিজের institution-এর user দেখতে পারবে।
        |
        |--------------------------------------------------------------------------
        */

        if (!$authUser->is_super_admin) {

            $query->where(
                'institution_id',
                $authUser->institution_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | System Role Visibility
        |--------------------------------------------------------------------------
        |
        | Super Admin ছাড়া অন্য কেউ system-level admin দেখতে পারবে না।
        |
        | System roles:
        |
        | super_admin
        | support_admin
        | user_admin
        |
        |--------------------------------------------------------------------------
        */

        if (!$authUser->is_super_admin) {

            $query->whereHas('role', function ($q) {

                $q->whereNotIn('slug', [
                    'super_admin',
                    'support_admin',
                    'user_admin',
                ]);

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim(
                $request->search
            );

            $query->where(function ($q) use ($search) {

                $q->where(
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
                )

                ->orWhere(
                    'email',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'username',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'institution_user_id',
                    'like',
                    "%{$search}%"
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | User Type Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('user_type')) {

            $userType = $request->user_type;

            $query->whereHas(
                'role',
                function ($q) use ($userType) {

                    $q->where(
                        'slug',
                        $userType
                    );

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $users = $query
            ->latest('id')
            ->paginate(10);


        /*
        |--------------------------------------------------------------------------
        | Divisions
        |--------------------------------------------------------------------------
        */

        $divisions = Division::all();


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.users.index',
            compact(
                'users',
                'divisions'
            )
        );
    }


    /**
     * =========================================================================
     * CREATE USER FORM
     * =========================================================================
     */
    public function create()
    {
        $authUser = auth()->user();


        /*
        |--------------------------------------------------------------------------
        | User Types
        |--------------------------------------------------------------------------
        |
        | RoleSeeder অনুযায়ী role slug ব্যবহার করা হয়েছে।
        |
        |--------------------------------------------------------------------------
        */

        $userTypes = [

            'student' => 'শিক্ষার্থী',

            'user' => 'ইউজার',

        ];


        /*
        |--------------------------------------------------------------------------
        | User Admin
        |--------------------------------------------------------------------------
        |
        | Super Admin User Admin তৈরি করতে পারবে।
        |
        |--------------------------------------------------------------------------
        */

        if ($authUser->is_super_admin) {

            $userTypes['user_admin'] =
                'ইউজার অ্যাডমিন';
        }


        /*
        |--------------------------------------------------------------------------
        | Support Admin
        |--------------------------------------------------------------------------
        |
        | Super Admin Support Admin তৈরি করতে পারবে।
        |
        |--------------------------------------------------------------------------
        */

        if ($authUser->is_super_admin) {

            $userTypes['support_admin'] =
                'সাপোর্ট অ্যাডমিন';
        }


        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        |
        | Super Admin নিজে Super Admin তৈরি করবে না।
        |
        |--------------------------------------------------------------------------
        */


        /*
        |--------------------------------------------------------------------------
        | Institution List
        |--------------------------------------------------------------------------
        */

        $institutions = collect();


        if ($authUser->is_super_admin) {

            $institutions = Madrasa::where(
                'status',
                1
            )
                ->orderBy('id')
                ->get();

        } else {

            if ($authUser->institution_id) {

                $institutions = Madrasa::where(
                    'id',
                    $authUser->institution_id
                )
                    ->where(
                        'status',
                        1
                    )
                    ->get();
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $roles = Role::orderBy(
            'level',
            'desc'
        )->get();


        /*
        |--------------------------------------------------------------------------
        | Divisions
        |--------------------------------------------------------------------------
        */

        $divisions = Division::with([
            'districts.upazilas'
        ])->get();


        /*
        |--------------------------------------------------------------------------
        | Preview IDs
        |--------------------------------------------------------------------------
        */

        $institutionId =
            $authUser->institution_id;


        $previewIds = [];


        foreach (
            array_keys($userTypes)
            as $type
        ) {

            /*
            |--------------------------------------------------------------------------
            | System-level users
            |--------------------------------------------------------------------------
            |
            | System user-এর institution ID লাগে না।
            |
            |--------------------------------------------------------------------------
            */

            if (
                in_array(
                    $type,
                    [
                        'support_admin',
                        'user_admin'
                    ],
                    true
                )
            ) {

                $previewIds[$type] = null;

                continue;
            }


            $previewIds[$type] =
                User::previewInstitutionUserId(
                    $institutionId,
                    $type
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Academic Years
        |--------------------------------------------------------------------------
        */

        $academicYears = AcademicYear::where(
            'status',
            1
        )
            ->orderBy(
                'name',
                'desc'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Classes
        |--------------------------------------------------------------------------
        */

        $classes = Classes::where(
            'status',
            1
        )->get();


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

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


    /**
     * =========================================================================
     * STORE USER
     * =========================================================================
     */
    public function store(Request $request)
    {
        $authUser = auth()->user();


        DB::beginTransaction();


        try {

            /*
            |--------------------------------------------------------------------------
            | Find Role
            |--------------------------------------------------------------------------
            */

            $role = Role::where(
                'slug',
                $request->user_type
            )->first();


            if (!$role) {

                DB::rollBack();

                return back()
                    ->with(
                        'error',
                        'নির্বাচিত Role পাওয়া যায়নি।'
                    )
                    ->withInput();
            }


            /*
            |--------------------------------------------------------------------------
            | Role Slug
            |--------------------------------------------------------------------------
            */

            $roleSlug = $role->slug;


            /*
            |--------------------------------------------------------------------------
            | Protected System Roles
            |--------------------------------------------------------------------------
            |
            | শুধু Super Admin system-level admin তৈরি করতে পারবে।
            |
            |--------------------------------------------------------------------------
            */

            if (
                in_array(
                    $roleSlug,
                    [
                        'support_admin',
                        'user_admin'
                    ],
                    true
                )
                &&
                !$authUser->is_super_admin
            ) {

                DB::rollBack();

                return back()
                    ->with(
                        'error',
                        'এই ধরনের Admin তৈরি করার অনুমতি আপনার নেই।'
                    )
                    ->withInput();
            }


            /*
            |--------------------------------------------------------------------------
            | Super Admin Creation Protection
            |--------------------------------------------------------------------------
            */

            if (
                $roleSlug === 'super_admin'
                &&
                !$authUser->is_super_admin
            ) {

                DB::rollBack();

                return back()
                    ->with(
                        'error',
                        'Super Admin তৈরি করার অনুমতি আপনার নেই।'
                    )
                    ->withInput();
            }


            /*
            |--------------------------------------------------------------------------
            | Student Check
            |--------------------------------------------------------------------------
            */

            $isStudent =
                $roleSlug === 'student';


            /*
            |--------------------------------------------------------------------------
            | Institution User
            |--------------------------------------------------------------------------
            |
            | Super Admin:
            |     Form থেকে institution নেবে।
            |
            | Others:
            |     নিজের institution ব্যবহার করবে।
            |
            |--------------------------------------------------------------------------
            */

            if ($authUser->is_super_admin) {

                $institutionId =
                    $request->input(
                        'institution_id'
                    );

            } else {

                $institutionId =
                    $authUser->institution_id;
            }


            /*
            |--------------------------------------------------------------------------
            | Institution Requirement
            |--------------------------------------------------------------------------
            |
            | Student এবং normal User অবশ্যই institution-এর অধীনে থাকবে।
            |
            |--------------------------------------------------------------------------
            */

            $institutionRequired =
                in_array(
                    $roleSlug,
                    [
                        'student',
                        'user'
                    ],
                    true
                );


            if (
                $institutionRequired
                &&
                !$institutionId
            ) {

                DB::rollBack();

                return back()
                    ->with(
                        'error',
                        'এই User-এর জন্য Institution নির্বাচন করা আবশ্যক।'
                    )
                    ->withInput();
            }


            /*
            |--------------------------------------------------------------------------
            | Validation Rules
            |--------------------------------------------------------------------------
            */

            $rules = [

                'user_type' => [
                    'required',
                    'string',
                    'exists:roles,slug',
                ],

                'name_bn' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'name' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'phone' => [
                    'required',
                    'string',
                    'max:30',
                    'unique:users,phone',
                ],

                'gender' => [
                    'required',
                    'in:male,female',
                ],

                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                    'unique:users,email',
                ],

                'password' => [
                    'nullable',
                    'string',
                    'min:6',
                ],

                'photo' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png',
                    'max:2048',
                ],

                'date_of_birth' => [
                    'nullable',
                    'date',
                ],

                'blood_group' => [
                    'nullable',
                    'string',
                    'max:10',
                ],

                'religion' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'nid' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'birth_certificate' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'phone2' => [
                    'nullable',
                    'string',
                    'max:30',
                ],

                'phone_relation' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'phone2_relation' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'father_name' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'father_phone' => [
                    'nullable',
                    'string',
                    'max:30',
                ],

                'mother_name' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'mother_phone' => [
                    'nullable',
                    'string',
                    'max:30',
                ],

                'guardian_name' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'guardian_phone' => [
                    'nullable',
                    'string',
                    'max:30',
                ],

                'guardian_relation' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                /*
                |--------------------------------------------------------------------------
                | Present Address
                |--------------------------------------------------------------------------
                */

                'present_division_id' => [
                    'nullable',
                    'integer',
                ],

                'present_district_id' => [
                    'nullable',
                    'integer',
                ],

                'present_upazila_id' => [
                    'nullable',
                    'integer',
                ],

                'present_union' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'present_post_office' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'present_village_road' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'present_postal_code' => [
                    'nullable',
                    'string',
                    'max:20',
                ],

                /*
                |--------------------------------------------------------------------------
                | Permanent Address
                |--------------------------------------------------------------------------
                */

                'permanent_division_id' => [
                    'nullable',
                    'integer',
                ],

                'permanent_district_id' => [
                    'nullable',
                    'integer',
                ],

                'permanent_upazila_id' => [
                    'nullable',
                    'integer',
                ],

                'permanent_union' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'permanent_post_office' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'permanent_village_road' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'permanent_postal_code' => [
                    'nullable',
                    'string',
                    'max:20',
                ],
            ];


            /*
            |--------------------------------------------------------------------------
            | Super Admin Institution Validation
            |--------------------------------------------------------------------------
            */

            if (
                $authUser->is_super_admin
                &&
                $institutionRequired
            ) {

                $rules['institution_id'] = [
                    'required',
                    'integer',
                    'exists:madrasas,id',
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | Student Validation
            |--------------------------------------------------------------------------
            */

            if ($isStudent) {

                $rules['academic_year_id'] = [
                    'nullable',
                    'exists:academic_years,id',
                ];

                $rules['class_id'] = [
                    'nullable',
                    'exists:classes,id',
                ];

                $rules['section_id'] = [
                    'nullable',
                    'exists:sections,id',
                ];

                $rules['guardian_user_id'] = [
                    'nullable',
                    'exists:users,id',
                ];

                $rules['is_hostel'] = [
                    'nullable',
                    'boolean',
                ];

                $rules['is_transport'] = [
                    'nullable',
                    'boolean',
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | Validate
            |--------------------------------------------------------------------------
            */

            $validated =
                $request->validate(
                    $rules
                );


            /*
            |--------------------------------------------------------------------------
            | Username
            |--------------------------------------------------------------------------
            */

            $username =
                $this->generateUsername(
                    $request->name,
                    $request->name_bn
                );


            /*
            |--------------------------------------------------------------------------
            | Photo Upload
            |--------------------------------------------------------------------------
            */

            $photoPath = null;


            if ($request->hasFile('photo')) {

                $photoPath =
                    $request
                        ->file('photo')
                        ->store(
                            'users/photos',
                            'public'
                        );
            }


            /*
            |--------------------------------------------------------------------------
            | Institution User ID
            |--------------------------------------------------------------------------
            |
            | System-level users:
            |
            | support_admin
            | user_admin
            |
            | এদের institution ID থাকবে না।
            |
            |--------------------------------------------------------------------------
            */

            $institutionUserId = null;


            if ($institutionId) {

                $institutionUserId =
                    User::generateInstitutionUserId(
                        $institutionId,
                        $roleSlug
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | User Create
            |--------------------------------------------------------------------------
            */

            $user = User::create([

                'institution_user_id' =>
                    $institutionUserId,

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
                        ??
                        'password123'
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
                    $request->gender,

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

                /*
                |--------------------------------------------------------------------------
                | Present Address
                |--------------------------------------------------------------------------
                */

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

                /*
                |--------------------------------------------------------------------------
                | Permanent Address
                |--------------------------------------------------------------------------
                */

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

                'status' =>
                    1,

                'created_by' =>
                    auth()->id(),

            ]);


            /*
            |--------------------------------------------------------------------------
            | Student Create
            |--------------------------------------------------------------------------
            */

            if ($isStudent) {

                $academicYearId =
                    $request->academic_year_id;


                /*
                |--------------------------------------------------------------------------
                | Generate Student ID
                |--------------------------------------------------------------------------
                */

                $studentId =
                    $this->generateStudentId(
                        $institutionId,
                        $academicYearId
                    );


                /*
                |--------------------------------------------------------------------------
                | Create Student
                |--------------------------------------------------------------------------
                */

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

                    'status' =>
                        1,

                    'created_by' =>
                        auth()->id(),

                    'updated_by' =>
                        auth()->id(),

                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Commit
            |--------------------------------------------------------------------------
            */

            DB::commit();


            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route(
                    'dashboard.user.index'
                )
                ->with(
                    'success',
                    'ইউজার সফলভাবে তৈরি হয়েছে।'
                );

        } catch (
            ValidationException $e
        ) {

            DB::rollBack();

            throw $e;

        } catch (
            \Exception $e
        ) {

            DB::rollBack();


            /*
            |--------------------------------------------------------------------------
            | Delete Uploaded Photo If Failed
            |--------------------------------------------------------------------------
            */

            if (
                isset($photoPath)
                &&
                $photoPath
                &&
                Storage::disk('public')
                    ->exists($photoPath)
            ) {

                Storage::disk('public')
                    ->delete($photoPath);
            }


            return back()
                ->with(
                    'error',
                    'ইউজার তৈরি করতে সমস্যা হয়েছে: '
                    .
                    $e->getMessage()
                )
                ->withInput();
        }
    }


    /**
     * =========================================================================
     * GENERATE STUDENT ID
     * =========================================================================
     */
    private function generateStudentId(
        $institutionId,
        $academicYearId = null
    ) {
        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Student table-এ institution_id ব্যবহার করা হচ্ছে।
        | তাই এখানে madrasa_id ব্যবহার করা যাবে না।
        |
        |--------------------------------------------------------------------------
        */

        $query = Student::where(
            'institution_id',
            $institutionId
        );


        /*
        |--------------------------------------------------------------------------
        | Academic Year
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Last Student
        |--------------------------------------------------------------------------
        */

        $lastStudent = $query
            ->latest('id')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Starting Number
        |--------------------------------------------------------------------------
        */

        $newNumber = 1;


        /*
        |--------------------------------------------------------------------------
        | Extract Last Number
        |--------------------------------------------------------------------------
        */

        if (
            $lastStudent
            &&
            $lastStudent->student_id
        ) {

            $studentIdString =
                $lastStudent->student_id;


            if ($academicYearId) {

                $pattern =
                    '/^STU-' .
                    preg_quote(
                        $institutionId,
                        '/'
                    ) .
                    '-' .
                    preg_quote(
                        $academicYearId,
                        '/'
                    ) .
                    '-(\d+)$/';

            } else {

                $pattern =
                    '/^STU-' .
                    preg_quote(
                        $institutionId,
                        '/'
                    ) .
                    '-(\d+)$/';
            }


            if (
                preg_match(
                    $pattern,
                    $studentIdString,
                    $matches
                )
            ) {

                $newNumber =
                    ((int) $matches[1]) + 1;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Generate Student ID
        |--------------------------------------------------------------------------
        */

        if ($academicYearId) {

            $studentId =
                'STU-' .
                $institutionId .
                '-' .
                $academicYearId .
                '-' .
                str_pad(
                    $newNumber,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

        } else {

            $studentId =
                'STU-' .
                $institutionId .
                '-' .
                str_pad(
                    $newNumber,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate Protection
        |--------------------------------------------------------------------------
        */

        while (
            Student::where(
                'student_id',
                $studentId
            )->exists()
        ) {

            $newNumber++;


            if ($academicYearId) {

                $studentId =
                    'STU-' .
                    $institutionId .
                    '-' .
                    $academicYearId .
                    '-' .
                    str_pad(
                        $newNumber,
                        4,
                        '0',
                        STR_PAD_LEFT
                    );

            } else {

                $studentId =
                    'STU-' .
                    $institutionId .
                    '-' .
                    str_pad(
                        $newNumber,
                        4,
                        '0',
                        STR_PAD_LEFT
                    );
            }
        }


        return $studentId;
    }


    /**
     * =========================================================================
     * SHOW USER
     * =========================================================================
     */
    public function show($id)
    {
        $authUser = auth()->user();


        /*
        |--------------------------------------------------------------------------
        | Load User
        |--------------------------------------------------------------------------
        */

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
        |--------------------------------------------------------------------------
        | Institution Security
        |--------------------------------------------------------------------------
        */

        if (
            !$authUser->is_super_admin
            &&
            $user->institution_id
            !==
            $authUser->institution_id
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


    /**
     * =========================================================================
     * EDIT USER
     * =========================================================================
     */
    public function edit($id)
    {
        $authUser = auth()->user();


        $user = User::with([
            'role',
            'student',
            'institution',
        ])->findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Institution Security
        |--------------------------------------------------------------------------
        */

        if (
            !$authUser->is_super_admin
            &&
            $user->institution_id
            !==
            $authUser->institution_id
        ) {

            abort(
                403,
                'এই User edit করার অনুমতি আপনার নেই।'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Institutions
        |--------------------------------------------------------------------------
        */

        if ($authUser->is_super_admin) {

            $institutions =
                Madrasa::where(
                    'status',
                    1
                )->get();

        } else {

            $institutions =
                Madrasa::where(
                    'id',
                    $authUser->institution_id
                )->get();
        }


        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $roles = Role::orderBy(
            'level',
            'desc'
        )->get();


        /*
        |--------------------------------------------------------------------------
        | Divisions
        |--------------------------------------------------------------------------
        */

        $divisions =
            Division::with([
                'districts.upazilas'
            ])->get();


        /*
        |--------------------------------------------------------------------------
        | Academic Years
        |--------------------------------------------------------------------------
        */

        $academicYears =
            AcademicYear::where(
                'status',
                1
            )
                ->orderBy(
                    'name',
                    'desc'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | Classes
        |--------------------------------------------------------------------------
        */

        $classes =
            Classes::where(
                'status',
                1
            )->get();


        /*
        |--------------------------------------------------------------------------
        | Sections
        |--------------------------------------------------------------------------
        */

        $sections = collect();


        if (
            $user->student
            &&
            $user->student->class_id
        ) {

            $sections =
                Section::where(
                    'class_id',
                    $user->student->class_id
                )
                    ->where(
                        'status',
                        1
                    )
                    ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | User Types
        |--------------------------------------------------------------------------
        */

        $userTypes = [

            'student' =>
                'শিক্ষার্থী',

            'user' =>
                'ইউজার',

        ];


        if ($authUser->is_super_admin) {

            $userTypes['user_admin'] =
                'ইউজার অ্যাডমিন';

            $userTypes['support_admin'] =
                'সাপোর্ট অ্যাডমিন';
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


    /**
     * =========================================================================
     * UPDATE USER
     * =========================================================================
     */
    public function update(
        Request $request,
        $id
    ) {
        $authUser = auth()->user();


        $user = User::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Institution Security
        |--------------------------------------------------------------------------
        */

        if (
            !$authUser->is_super_admin
            &&
            $user->institution_id
            !==
            $authUser->institution_id
        ) {

            abort(
                403,
                'এই User update করার অনুমতি আপনার নেই।'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Role
        |--------------------------------------------------------------------------
        */

        $role =
            Role::where(
                'slug',
                $request->user_type
            )->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | System Role Protection
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $role->slug,
                [
                    'support_admin',
                    'user_admin'
                ],
                true
            )
            &&
            !$authUser->is_super_admin
        ) {

            return back()
                ->with(
                    'error',
                    'এই Role assign করার অনুমতি আপনার নেই।'
                )
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | Student
        |--------------------------------------------------------------------------
        */

        $isStudent =
            $role->slug === 'student';


        /*
        |--------------------------------------------------------------------------
        | Institution
        |--------------------------------------------------------------------------
        */

        if ($authUser->is_super_admin) {

            $institutionId =
                $request->institution_id;

        } else {

            $institutionId =
                $authUser->institution_id;
        }


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $rules = [

            'user_type' => [
                'required',
                'exists:roles,slug',
            ],

            'name_bn' => [
                'required',
                'string',
                'max:255',
            ],

            'name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
                'unique:users,phone,' .
                $user->id,
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:users,email,' .
                $user->id,
            ],

            'gender' => [
                'required',
                'in:male,female',
            ],

            'password' => [
                'nullable',
                'string',
                'min:6',
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],

            'date_of_birth' => [
                'nullable',
                'date',
            ],

            'blood_group' => [
                'nullable',
                'string',
                'max:10',
            ],

            'religion' => [
                'nullable',
                'string',
                'max:50',
            ],

            'nid' => [
                'nullable',
                'string',
                'max:100',
            ],

            'birth_certificate' => [
                'nullable',
                'string',
                'max:100',
            ],
        ];


        if (
            $authUser->is_super_admin
            &&
            in_array(
                $role->slug,
                [
                    'student',
                    'user'
                ],
                true
            )
        ) {

            $rules['institution_id'] = [
                'required',
                'exists:madrasas,id',
            ];
        }


        if ($isStudent) {

            $rules['academic_year_id'] = [
                'nullable',
                'exists:academic_years,id',
            ];

            $rules['class_id'] = [
                'nullable',
                'exists:classes,id',
            ];

            $rules['section_id'] = [
                'nullable',
                'exists:sections,id',
            ];

            $rules['guardian_user_id'] = [
                'nullable',
                'exists:users,id',
            ];
        }


        $request->validate($rules);


        DB::beginTransaction();


        try {

            /*
            |--------------------------------------------------------------------------
            | Photo
            |--------------------------------------------------------------------------
            */

            $photoPath =
                $user->photo;


            if ($request->hasFile('photo')) {

                if (
                    $user->photo
                    &&
                    Storage::disk('public')
                        ->exists($user->photo)
                ) {

                    Storage::disk('public')
                        ->delete(
                            $user->photo
                        );
                }


                $photoPath =
                    $request
                        ->file('photo')
                        ->store(
                            'users/photos',
                            'public'
                        );
            }


            /*
            |--------------------------------------------------------------------------
            | Update Data
            |--------------------------------------------------------------------------
            */

            $data = [

                'institution_id' =>
                    $institutionId,

                'role_id' =>
                    $role->id,

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
                    $request->gender,

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

                'updated_by' =>
                    auth()->id(),
            ];


            /*
            |--------------------------------------------------------------------------
            | Password
            |--------------------------------------------------------------------------
            */

            if (
                $request->filled(
                    'password'
                )
            ) {

                $data['password'] =
                    Hash::make(
                        $request->password
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Update User
            |--------------------------------------------------------------------------
            */

            $user->update($data);


            /*
            |--------------------------------------------------------------------------
            | Student
            |--------------------------------------------------------------------------
            */

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

                    $studentData['status'] =
                        1;

                    $studentData['created_by'] =
                        auth()->id();

                    Student::create(
                        $studentData
                    );
                }

            } else {

                /*
                |--------------------------------------------------------------------------
                | If Role Changed From Student
                |--------------------------------------------------------------------------
                */

                if ($user->student) {

                    $user->student->delete();
                }
            }


            DB::commit();


            return redirect()
                ->route(
                    'dashboard.user.index'
                )
                ->with(
                    'success',
                    'ইউজার সফলভাবে আপডেট হয়েছে।'
                );

        } catch (\Exception $e) {

            DB::rollBack();


            return back()
                ->with(
                    'error',
                    'আপডেট করতে সমস্যা হয়েছে: ' .
                    $e->getMessage()
                )
                ->withInput();
        }
    }


    /**
     * =========================================================================
     * DELETE USER
     * =========================================================================
     */
    public function destroy($id)
    {
        try {

            $user =
                User::with('role')
                    ->findOrFail($id);


            /*
            |--------------------------------------------------------------------------
            | Super Admin Protection
            |--------------------------------------------------------------------------
            */

            if (
                optional(
                    $user->role
                )->slug
                ===
                'super_admin'
            ) {

                return back()
                    ->with(
                        'error',
                        'Super Admin ডিলিট করা যাবে না।'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Institution Security
            |--------------------------------------------------------------------------
            */

            $authUser =
                auth()->user();


            if (
                !$authUser->is_super_admin
                &&
                $user->institution_id
                !==
                $authUser->institution_id
            ) {

                abort(
                    403,
                    'এই User ডিলিট করার অনুমতি আপনার নেই।'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Student Delete
            |--------------------------------------------------------------------------
            */

            if ($user->student) {

                $user->student->delete();
            }


            /*
            |--------------------------------------------------------------------------
            | Photo Delete
            |--------------------------------------------------------------------------
            */

            if (
                $user->photo
                &&
                Storage::disk('public')
                    ->exists($user->photo)
            ) {

                Storage::disk('public')
                    ->delete(
                        $user->photo
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | User Delete
            |--------------------------------------------------------------------------
            */

            $user->delete();


            return redirect()
                ->route(
                    'dashboard.user.index'
                )
                ->with(
                    'success',
                    'ইউজার সফলভাবে ডিলিট হয়েছে।'
                );

        } catch (\Exception $e) {

            return back()
                ->with(
                    'error',
                    'ডিলিট করতে সমস্যা হয়েছে: ' .
                    $e->getMessage()
                );
        }
    }


    /**
     * =========================================================================
     * TOGGLE USER STATUS
     * =========================================================================
     */
    public function toggleStatus($id)
    {
        $user =
            User::with('role')
                ->findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Super Admin Protection
        |--------------------------------------------------------------------------
        */

        if (
            optional(
                $user->role
            )->slug
            ===
            'super_admin'
        ) {

            return back()
                ->with(
                    'error',
                    'Super Admin-এর Status পরিবর্তন করা যাবে না।'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Institution Security
        |--------------------------------------------------------------------------
        */

        $authUser =
            auth()->user();


        if (
            !$authUser->is_super_admin
            &&
            $user->institution_id
            !==
            $authUser->institution_id
        ) {

            abort(
                403,
                'এই User-এর Status পরিবর্তন করার অনুমতি আপনার নেই।'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Toggle
        |--------------------------------------------------------------------------
        */

        $user->update([

            'status' =>
                $user->status == 1
                    ? 0
                    : 1,

            'updated_by' =>
                auth()->id(),

        ]);


        return back()
            ->with(
                'success',
                'User Status সফলভাবে পরিবর্তন হয়েছে।'
            );
    }


    /**
     * =========================================================================
     * GENERATE USERNAME
     * =========================================================================
     */
    private function generateUsername(
        $nameEn = null,
        $nameBn = null
    ) {

        $base =
            preg_replace(
                '/[^a-zA-Z0-9]/',
                '',
                strtolower(
                    $nameEn ?? ''
                )
            );


        /*
        |--------------------------------------------------------------------------
        | Bangla Name Fallback
        |--------------------------------------------------------------------------
        */

        if (empty($base)) {

            $base =
                'user' .
                substr(
                    md5(
                        ($nameBn ?? '')
                        .
                        microtime(true)
                    ),
                    0,
                    6
                );
        }


        $username =
            $base;


        $counter = 1;


        while (
            User::where(
                'username',
                $username
            )->exists()
        ) {

            $username =
                $base .
                $counter;

            $counter++;
        }


        return $username;
    }


    /**
     * =========================================================================
     * GET DISTRICTS
     * =========================================================================
     */
    public function getDistricts(
        $divisionId
    ) {

        return response()->json(

            District::where(
                'division_id',
                $divisionId
            )
                ->orderBy(
                    'name_bn'
                )
                ->get([
                    'id',
                    'name_bn'
                ])
        );
    }


    /**
     * =========================================================================
     * GET UPAZILAS
     * =========================================================================
     */
    public function getUpazilas(
        $districtId
    ) {

        return response()->json(

            Upazila::where(
                'district_id',
                $districtId
            )
                ->orderBy(
                    'name_bn'
                )
                ->get([
                    'id',
                    'name_bn'
                ])
        );
    }


    /**
     * =========================================================================
     * GET SECTIONS
     * =========================================================================
     */
    public function getSections(
        $classId
    ) {

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


    /**
     * =========================================================================
     * GENERATE ADDRESS STRING
     * =========================================================================
     */
    private function generateAddressString(
        Request $request,
        string $type
    ) {

        $parts = [];


        /*
        |--------------------------------------------------------------------------
        | Village / Road
        |--------------------------------------------------------------------------
        */

        $villageRoad =
            $request->input(
                "{$type}_village_road"
            );


        if ($villageRoad) {

            $parts[] =
                $villageRoad;
        }


        /*
        |--------------------------------------------------------------------------
        | Union
        |--------------------------------------------------------------------------
        */

        $union =
            $request->input(
                "{$type}_union"
            );


        if ($union) {

            $parts[] =
                $union;
        }


        /*
        |--------------------------------------------------------------------------
        | Upazila
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | District
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Division
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Postal Code
        |--------------------------------------------------------------------------
        */

        $postalCode =
            $request->input(
                "{$type}_postal_code"
            );


        if ($postalCode) {

            $parts[] =
                $postalCode;
        }


        return implode(
            ', ',
            $parts
        );
    }


    /**
     * =========================================================================
     * PREVIEW INSTITUTION USER ID
     * =========================================================================
     */
    public function previewId(
        Request $request
    ) {

        $userType =
            $request->user_type;


        /*
        |--------------------------------------------------------------------------
        | System-level Roles
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $userType,
                [
                    'support_admin',
                    'user_admin'
                ],
                true
            )
        ) {

            return response()->json([

                'success' =>
                    true,

                'preview_id' =>
                    null,

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Institution
        |--------------------------------------------------------------------------
        */

        $institutionId =
            $request->institution_id
            ??
            $request->madrasa_id
            ??
            auth()->user()->institution_id;


        if (!$institutionId) {

            return response()->json([

                'success' =>
                    false,

                'preview_id' =>
                    '100',

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Find Role
        |--------------------------------------------------------------------------
        */

        $role =
            Role::where(
                'slug',
                $userType
            )->first();


        if (!$role) {

            return response()->json([

                'success' =>
                    false,

                'preview_id' =>
                    '100',

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Last User
        |--------------------------------------------------------------------------
        */

        $lastUser =
            User::where(
                'institution_id',
                $institutionId
            )
                ->where(
                    'role_id',
                    $role->id
                )
                ->whereNotNull(
                    'institution_user_id'
                )
                ->orderByRaw(
                    'CAST(institution_user_id AS UNSIGNED) DESC'
                )
                ->first();


        /*
        |--------------------------------------------------------------------------
        | Generate
        |--------------------------------------------------------------------------
        */

        if ($lastUser) {

            $previewId =
                (int)
                $lastUser->institution_user_id
                + 1;

        } else {

            $previewId =
                match ($userType) {

                    'student' =>
                        101,

                    'user' =>
                        201,

                    default =>
                        100,
                };
        }


        return response()->json([

            'success' =>
                true,

            'preview_id' =>
                (string) $previewId,

        ]);
    }
}