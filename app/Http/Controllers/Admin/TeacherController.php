<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Role;
use App\Models\Madrasa;
use App\Models\Classes;
use App\Models\Division;
use App\Models\District;
use App\Models\Upazila;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TeacherController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Teacher Index
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $authUser = auth()->user();

        $query = Teacher::query()
            ->with([
                'user.role',
                'user',
                'classTeacherFor',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Institution Isolation
        |--------------------------------------------------------------------------
        */

        if ($authUser->is_super_admin) {

            $institutionId =
                $authUser->active_institution_id;

            if ($institutionId) {

                $query->where(
                    'teachers.institution_id',
                    $institutionId
                );
            }

        } else {

            $query->where(
                'teachers.institution_id',
                $authUser->institution_id
            );
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
                    'teacher_id',
                    'like',
                    "%{$search}%"
                );

                $q->orWhere(
                    'designation',
                    'like',
                    "%{$search}%"
                );

                $q->orWhereHas(
                    'user',
                    function ($userQuery) use ($search) {

                        $userQuery
                            ->where(
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
                                'institution_user_id',
                                'like',
                                "%{$search}%"
                            );
                    }
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('status')
            &&
            $request->status !== 'all'
        ) {

            $status = (int) $request->status;

            $query->whereHas(
                'user',
                function ($userQuery) use ($status) {

                    $userQuery->where(
                        'status',
                        $status
                    );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $perPage = (int) $request->get(
            'per_page',
            20
        );

        if (
            !in_array(
                $perPage,
                [10, 20, 50],
                true
            )
        ) {
            $perPage = 20;
        }

        $teachers = $query
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

/*
|--------------------------------------------------------------------------
| Teacher Code Preview
|--------------------------------------------------------------------------
*/

$teacherCode = null;

$authUser = auth()->user();

$institutionId = $authUser->is_super_admin
    ? $authUser->active_institution_id
    : $authUser->institution_id;

if ($institutionId) {
    $teacherCode = Teacher::generateTeacherCode(
        (int) $institutionId
    );
}

/*
|--------------------------------------------------------------------------
| Return View
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| User Code Preview
|--------------------------------------------------------------------------
*/

$userCode = null;

if ($institutionId) {
    $userCode = User::previewInstitutionUserId(
        (int) $institutionId,
        'user'
    );
}

/*
|--------------------------------------------------------------------------
| Return View
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Address Data
|--------------------------------------------------------------------------
*/

$divisions = Division::query()
    ->orderBy('name')
    ->get();

/*
|--------------------------------------------------------------------------
| Classes
|--------------------------------------------------------------------------
*/

$classes = Classes::query()
    ->when(
        $institutionId,
        function ($query) use ($institutionId) {
            $query->where(
                'institution_id',
                $institutionId
            );
        }
    )
    ->orderBy('name')
    ->get();

/*
|--------------------------------------------------------------------------
| Return Teacher List View
|--------------------------------------------------------------------------
*/

return view(
    'admin.teachers.index',
    compact(
        'teachers',
        'teacherCode',
        'userCode',
        'divisions',
        'classes'
    )
);
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $authUser = auth()->user();

        $institutionId =
            $authUser->is_super_admin
                ? $authUser->active_institution_id
                : $authUser->institution_id;

        /*
        |--------------------------------------------------------------------------
        | Teacher Code Preview
        |--------------------------------------------------------------------------
        */

        $teacherCode = null;

        if ($institutionId) {

            $teacherCode =
                Teacher::generateTeacherCode(
                    (int) $institutionId
                );
        }

        /*
        |--------------------------------------------------------------------------
        | User Code Preview
        |--------------------------------------------------------------------------
        */

        $userCode = null;

        $userRole = Role::query()
            ->where(
                'slug',
                'user'
            )
            ->first();

        if (
            $institutionId
            &&
            $userRole
        ) {

            $userCode =
                User::previewInstitutionUserId(
                    $institutionId,
                    'user'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Address Data
        |--------------------------------------------------------------------------
        */

        $divisions = Division::query()
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Classes
        |--------------------------------------------------------------------------
        */

        $classes = Classes::query()
            ->when(
                $institutionId,
                function ($query) use ($institutionId) {

                    $query->where(
                        'institution_id',
                        $institutionId
                    );
                }
            )
            ->orderBy('name')
            ->get();

        return view(
            'admin.teachers.create',
            compact(
                'teacherCode',
                'userCode',
                'divisions',
                'classes'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $institutionId =
            $authUser->is_super_admin
                ? $authUser->active_institution_id
                : $authUser->institution_id;

        if (!$institutionId) {

            throw ValidationException::withMessages([
                'institution_id' =>
                    'কোনো প্রতিষ্ঠান নির্বাচন করা হয়নি।'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            /*
            |------------------------------------------------------------------
            | Personal
            |------------------------------------------------------------------
            */

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

            'gender' => [
                'required',
                'in:male,female',
            ],

            'date_of_birth' => [
                'nullable',
                'date',
            ],

            'age' => [
                'nullable',
                'integer',
                'min:0',
                'max:150',
            ],

            'nid' => [
                'nullable',
                'string',
                'max:50',
            ],

            'birth_certificate' => [
                'nullable',
                'string',
                'max:100',
            ],

            'blood_group' => [
                'nullable',
                'string',
                'max:20',
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
            ],

            'phone2' => [
                'nullable',
                'string',
                'max:30',
            ],

            'phone_owner' => [
                'nullable',
                'string',
                'max:100',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            /*
            |------------------------------------------------------------------
            | Parents
            |------------------------------------------------------------------
            */

            'father_name' => [
                'required',
                'string',
                'max:255',
            ],

            'mother_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
            |------------------------------------------------------------------
            | Permanent Address
            |------------------------------------------------------------------
            */

            'permanent_division_id' => [
                'nullable',
                'exists:divisions,id',
            ],

            'permanent_district_id' => [
                'nullable',
                'exists:districts,id',
            ],

            'permanent_upazila_id' => [
                'nullable',
                'exists:upazilas,id',
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

            /*
            |------------------------------------------------------------------
            | Present Address
            |------------------------------------------------------------------
            */

            'present_division_id' => [
                'nullable',
                'exists:divisions,id',
            ],

            'present_district_id' => [
                'nullable',
                'exists:districts,id',
            ],

            'present_upazila_id' => [
                'nullable',
                'exists:upazilas,id',
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

            /*
            |------------------------------------------------------------------
            | Teacher Information
            |------------------------------------------------------------------
            */

            'designation' => [
                'required',
                'string',
                'max:100',
            ],

            'joining_date' => [
                'required',
                'date',
            ],

            'pass_department' => [
                'nullable',
                'string',
                'max:150',
            ],

            'pass_year' => [
                'nullable',
                'integer',
                'min:1900',
                'max:' . date('Y'),
            ],

            'pass_date' => [
                'nullable',
                'date',
            ],

            'qualification' => [
                'nullable',
                'string',
            ],

            'completion_institution' => [
                'nullable',
                'string',
                'max:255',
            ],

            'experience' => [
                'nullable',
                'string',
            ],

            'expertise_subjects' => [
                'nullable',
                'string',
            ],

            'salary_scale' => [
                'nullable',
                'string',
                'max:50',
            ],

            'basic_salary' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'is_class_teacher' => [
                'nullable',
                'boolean',
            ],

            'class_teacher_for' => [
                'nullable',
                'exists:classes,id',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | User Role
        |--------------------------------------------------------------------------
        */

        $role = Role::query()
            ->where(
                'slug',
                'user'
            )
            ->first();

        if (!$role) {

            throw ValidationException::withMessages([
                'role' =>
                    'User role পাওয়া যায়নি।'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        $teacher = DB::transaction(
            function () use (
                $request,
                $validated,
                $institutionId,
                $role,
                $authUser
            ) {

                /*
                |--------------------------------------------------------------------------
                | Generate User Code
                |--------------------------------------------------------------------------
                */

                $userCode =
                    User::generateInstitutionUserId(
                        $institutionId,
                        'user'
                    );

                /*
                |--------------------------------------------------------------------------
                | Generate Teacher Code
                |--------------------------------------------------------------------------
                */

                $teacherCode =
                    Teacher::generateTeacherCode(
                        (int) $institutionId
                    );

                /*
                |--------------------------------------------------------------------------
                | Username
                |--------------------------------------------------------------------------
                */

                $baseUsername =
                    strtolower(
                        preg_replace(
                            '/[^a-zA-Z0-9]/',
                            '',
                            (string) (
                                $validated['name']
                                ?? $validated['name_bn']
                            )
                        )
                    );

                if (!$baseUsername) {
                    $baseUsername = 'teacher';
                }

                $username = $baseUsername;

                $counter = 1;

                while (
                    User::where(
                        'username',
                        $username
                    )->exists()
                ) {

                    $username =
                        $baseUsername
                        . $counter;

                    $counter++;
                }

                /*
                |--------------------------------------------------------------------------
                | Photo
                |--------------------------------------------------------------------------
                */

                $photoPath = null;

                if ($request->hasFile('photo')) {

                    $photoPath =
                        $request
                            ->file('photo')
                            ->store(
                                'users',
                                'public'
                            );
                }

                /*
                |--------------------------------------------------------------------------
                | User
                |--------------------------------------------------------------------------
                */

                $user = User::create([

                    'institution_id' =>
                        $institutionId,

                    'role_id' =>
                        $role->id,

                    'institution_user_id' =>
                        $userCode,

                    'code_generation_type' =>
                        'auto',

                    'username' =>
                        $username,

                    'email' =>
                        $validated['email']
                        ?? null,

                    'password' =>
                        Hash::make(
                            $validated['phone']
                        ),

                    'name' =>
                        $validated['name']
                        ?? $validated['name_bn'],

                    'name_bn' =>
                        $validated['name_bn'],

                    'photo' =>
                        $photoPath,

                    'phone' =>
                        $validated['phone'],

                    'phone2' =>
                        $validated['phone2']
                        ?? null,

                    'phone_owner' =>
                        $validated['phone_owner']
                        ?? null,

                    'gender' =>
                        $validated['gender'],

                    'date_of_birth' =>
                        $validated['date_of_birth']
                        ?? null,

                    'age' =>
                        $validated['age']
                        ?? null,

                    'blood_group' =>
                        $validated['blood_group']
                        ?? null,

                    'nid' =>
                        $validated['nid']
                        ?? null,

                    'birth_certificate' =>
                        $validated['birth_certificate']
                        ?? null,

                    'father_name' =>
                        $validated['father_name'],

                    'mother_name' =>
                        $validated['mother_name']
                        ?? null,

                    /*
                    |--------------------------------------------------------------------------
                    | Permanent Address
                    |--------------------------------------------------------------------------
                    */

                    'permanent_division_id' =>
                        $validated[
                            'permanent_division_id'
                        ] ?? null,

                    'permanent_district_id' =>
                        $validated[
                            'permanent_district_id'
                        ] ?? null,

                    'permanent_upazila_id' =>
                        $validated[
                            'permanent_upazila_id'
                        ] ?? null,

                    'permanent_post_office' =>
                        $validated[
                            'permanent_post_office'
                        ] ?? null,

                    'permanent_village_road' =>
                        $validated[
                            'permanent_village_road'
                        ] ?? null,

                    /*
                    |--------------------------------------------------------------------------
                    | Present Address
                    |--------------------------------------------------------------------------
                    */

                    'present_division_id' =>
                        $validated[
                            'present_division_id'
                        ] ?? null,

                    'present_district_id' =>
                        $validated[
                            'present_district_id'
                        ] ?? null,

                    'present_upazila_id' =>
                        $validated[
                            'present_upazila_id'
                        ] ?? null,

                    'present_post_office' =>
                        $validated[
                            'present_post_office'
                        ] ?? null,

                    'present_village_road' =>
                        $validated[
                            'present_village_road'
                        ] ?? null,

                    'status' =>
                        1,

                    'created_by' =>
                        $authUser->id,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Teacher
                |--------------------------------------------------------------------------
                */

                $teacher = Teacher::create([

                    'user_id' =>
                        $user->id,

                    'institution_id' =>
                        $institutionId,

                    'teacher_id' =>
                        $teacherCode,

                    'designation' =>
                        $validated['designation'],

                    'joining_date' =>
                        $validated['joining_date'],

                    'pass_department' =>
                        $validated[
                            'pass_department'
                        ] ?? null,

                    'pass_year' =>
                        $validated[
                            'pass_year'
                        ] ?? null,

                    'pass_date' =>
                        $validated[
                            'pass_date'
                        ] ?? null,

                    'qualification' =>
                        $validated[
                            'qualification'
                        ] ?? null,

                    'completion_institution' =>
                        $validated[
                            'completion_institution'
                        ] ?? null,

                    'experience' =>
                        $validated[
                            'experience'
                        ] ?? null,

                    'expertise_subjects' =>
                        $validated[
                            'expertise_subjects'
                        ] ?? null,

                    'salary_scale' =>
                        $validated[
                            'salary_scale'
                        ] ?? null,

                    'basic_salary' =>
                        $validated[
                            'basic_salary'
                        ] ?? null,

                    'is_class_teacher' =>
                        $request->boolean(
                            'is_class_teacher'
                        ),

                    'class_teacher_for' =>
                        $validated[
                            'class_teacher_for'
                        ] ?? null,

                    'remarks' =>
                        $validated[
                            'remarks'
                        ] ?? null,
                ]);

                return $teacher;
            }
        );

        return redirect()
            ->route('dashboard.teacher.index')
            ->with(
                'success',
                'শিক্ষক সফলভাবে তৈরি হয়েছে।'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $teacher = $this->findTeacher(
            $id
        );

        $teacher->load([
            'user',
            'user.role',
            'classTeacherFor',
            'institution',
        ]);

        return view(
            'admin.teachers.show',
            compact('teacher')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $teacher = $this->findTeacher(
            $id
        );

        $teacher->load('user');

        $institutionId =
            $teacher->institution_id;

        $divisions = Division::query()
            ->orderBy('name')
            ->get();

        $classes = Classes::query()
            ->where(
                'institution_id',
                $institutionId
            )
            ->orderBy('name')
            ->get();

        return view(
            'admin.teachers.edit',
            compact(
                'teacher',
                'divisions',
                'classes'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    ) {

        $teacher = $this->findTeacher(
            $id
        );

        $user = $teacher->user;

        $validated = $request->validate([

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

            'gender' => [
                'required',
                'in:male,female',
            ],

            'date_of_birth' => [
                'nullable',
                'date',
            ],

            'age' => [
                'nullable',
                'integer',
                'min:0',
                'max:150',
            ],

            'nid' => [
                'nullable',
                'string',
                'max:50',
            ],

            'birth_certificate' => [
                'nullable',
                'string',
                'max:100',
            ],

            'blood_group' => [
                'nullable',
                'string',
                'max:20',
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
            ],

            'phone2' => [
                'nullable',
                'string',
                'max:30',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'father_name' => [
                'required',
                'string',
                'max:255',
            ],

            'mother_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'designation' => [
                'required',
                'string',
                'max:100',
            ],

            'joining_date' => [
                'required',
                'date',
            ],

            'pass_department' => [
                'nullable',
                'string',
                'max:150',
            ],

            'pass_year' => [
                'nullable',
                'integer',
                'min:1900',
                'max:' . date('Y'),
            ],

            'pass_date' => [
                'nullable',
                'date',
            ],

            'qualification' => [
                'nullable',
                'string',
            ],

            'completion_institution' => [
                'nullable',
                'string',
                'max:255',
            ],

            'experience' => [
                'nullable',
                'string',
            ],

            'expertise_subjects' => [
                'nullable',
                'string',
            ],

            'salary_scale' => [
                'nullable',
                'string',
                'max:50',
            ],

            'basic_salary' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'is_class_teacher' => [
                'nullable',
                'boolean',
            ],

            'class_teacher_for' => [
                'nullable',
                'exists:classes,id',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        DB::transaction(function () use (
            $request,
            $validated,
            $teacher,
            $user
        ) {

            $photoPath = $user->photo;

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
                            'users',
                            'public'
                        );
            }

            $user->update([

                'name' =>
                    $validated['name']
                    ?? $validated['name_bn'],

                'name_bn' =>
                    $validated['name_bn'],

                'email' =>
                    $validated['email']
                    ?? null,

                'phone' =>
                    $validated['phone'],

                'phone2' =>
                    $validated['phone2']
                    ?? null,

                'gender' =>
                    $validated['gender'],

                'date_of_birth' =>
                    $validated['date_of_birth']
                    ?? null,

                'age' =>
                    $validated['age']
                    ?? null,

                'blood_group' =>
                    $validated['blood_group']
                    ?? null,

                'nid' =>
                    $validated['nid']
                    ?? null,

                'birth_certificate' =>
                    $validated['birth_certificate']
                    ?? null,

                'father_name' =>
                    $validated['father_name'],

                'mother_name' =>
                    $validated['mother_name']
                    ?? null,

                'photo' =>
                    $photoPath,

                'updated_by' =>
                    auth()->id(),
            ]);

            $teacher->update([

                'designation' =>
                    $validated['designation'],

                'joining_date' =>
                    $validated['joining_date'],

                'pass_department' =>
                    $validated[
                        'pass_department'
                    ] ?? null,

                'pass_year' =>
                    $validated[
                        'pass_year'
                    ] ?? null,

                'pass_date' =>
                    $validated[
                        'pass_date'
                    ] ?? null,

                'qualification' =>
                    $validated[
                        'qualification'
                    ] ?? null,

                'completion_institution' =>
                    $validated[
                        'completion_institution'
                    ] ?? null,

                'experience' =>
                    $validated[
                        'experience'
                    ] ?? null,

                'expertise_subjects' =>
                    $validated[
                        'expertise_subjects'
                    ] ?? null,

                'salary_scale' =>
                    $validated[
                        'salary_scale'
                    ] ?? null,

                'basic_salary' =>
                    $validated[
                        'basic_salary'
                    ] ?? null,

                'is_class_teacher' =>
                    $request->boolean(
                        'is_class_teacher'
                    ),

                'class_teacher_for' =>
                    $validated[
                        'class_teacher_for'
                    ] ?? null,

                'remarks' =>
                    $validated[
                        'remarks'
                    ] ?? null,
            ]);
        });

        return redirect()
            ->route('dashboard.teacher.index')
            ->with(
                'success',
                'শিক্ষকের তথ্য সফলভাবে আপডেট হয়েছে।'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $teacher = $this->findTeacher(
            $id
        );

        DB::transaction(function () use (
            $teacher
        ) {

            $user = $teacher->user;

            if (
                $user
                &&
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

            $teacher->delete();

            if ($user) {
                $user->delete();
            }
        });

        return redirect()
            ->route('dashboard.teacher.index')
            ->with(
                'success',
                'শিক্ষক সফলভাবে মুছে ফেলা হয়েছে।'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Status
    |--------------------------------------------------------------------------
    */

    public function toggleStatus($id)
    {
        $teacher = $this->findTeacher(
            $id
        );

        $user = $teacher->user;

        if (!$user) {
            abort(404);
        }

        $user->update([
            'status' =>
                $user->status ? 0 : 1,
            'updated_by' =>
                auth()->id(),
        ]);

        return back()->with(
            'success',
            'শিক্ষকের স্ট্যাটাস পরিবর্তন হয়েছে।'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Get Districts
    |--------------------------------------------------------------------------
    */

    public function getDistricts(
        $divisionId
    ) {

        $districts = District::query()
            ->where(
                'division_id',
                $divisionId
            )
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return response()->json(
            $districts
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Get Upazilas
    |--------------------------------------------------------------------------
    */

    public function getUpazilas(
        $districtId
    ) {

        $upazilas = Upazila::query()
            ->where(
                'district_id',
                $districtId
            )
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return response()->json(
            $upazilas
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Find Teacher With Institution Isolation
    |--------------------------------------------------------------------------
    */

    protected function findTeacher($id): Teacher
    {
        $authUser = auth()->user();

        $query = Teacher::query()
            ->with('user');

        if ($authUser->is_super_admin) {

            $institutionId =
                $authUser->active_institution_id;

            if ($institutionId) {

                $query->where(
                    'institution_id',
                    $institutionId
                );
            }

        } else {

            $query->where(
                'institution_id',
                $authUser->institution_id
            );
        }

        return $query->findOrFail($id);
    }
}