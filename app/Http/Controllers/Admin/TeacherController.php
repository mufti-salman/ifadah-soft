<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\District;
use App\Models\Division;
use App\Models\Madrasa;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\Upazila;
use App\Models\User;
use App\Models\UserCodeSetting;
use App\Models\UserType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TeacherController extends Controller
{
    private const DEFAULT_USER_CODE_START = 201;

    private const ADDRESS_KEYS = [
        'permanent_division_id', 'permanent_district_id', 'permanent_upazila_id',
        'permanent_post_office', 'permanent_village_road',
        'present_division_id', 'present_district_id', 'present_upazila_id',
        'present_post_office', 'present_village_road',
    ];

    /*
    |--------------------------------------------------------------------------
    | Institution helpers
    |--------------------------------------------------------------------------
    */

    /**
     * List scope. null = সব প্রতিষ্ঠান (শুধু Super/Support Admin)।
     */
    private function listInstitutionId(): ?int
    {
        $u = auth()->user();

        if ($u->is_super_admin) {
            $id = $u->active_institution_id;
            return $id ? (int) $id : null;
        }

        if ($u->is_support_admin) {
            return null;
        }

        return (int) $u->institution_id; // 0 হলে কিছুই দেখাবে না
    }

    /**
     * Create form / store এর জন্য প্রতিষ্ঠান।
     */
    private function currentInstitutionId(?Request $request = null): ?int
    {
        $u = auth()->user();

        if ($u->is_super_admin) {
            $id = $u->active_institution_id;

            if (!$id && $request && $request->filled('institution_id')) {
                $id = $request->input('institution_id');
            }

            return $id ? (int) $id : null;
        }

        if ($u->is_support_admin) {
            return ($request && $request->filled('institution_id'))
                ? (int) $request->input('institution_id')
                : null;
        }

        return $u->institution_id ? (int) $u->institution_id : null;
    }

    private function availableInstitutions()
    {
        $u = auth()->user();

        $query = Madrasa::query()->where('status', 1);

        if ($u->is_super_admin) {
            if ($u->active_institution_id) {
                $query->where('id', $u->active_institution_id);
            }
        } elseif (!$u->is_support_admin) {
            $query->where('id', $u->institution_id);
        }

        return $query->orderBy('name')->get();
    }

    private function teacherUserType(): ?UserType
    {
        return UserType::active()->where('slug', 'teacher')->first();
    }

    /*
    |--------------------------------------------------------------------------
    | User Code helpers (UserController এর logic অনুযায়ী)
    |--------------------------------------------------------------------------
    */

    private function findCodeSetting(int $institutionId, UserType $type): ?UserCodeSetting
    {
        return UserCodeSetting::query()
            ->where('institution_id', $institutionId)
            ->where(function ($q) use ($type) {
                $q->where('user_type_id', $type->id)
                    ->orWhere('role_slug', 'user_type_' . $type->id);
            })
            ->first();
    }

    private function autoStart(int $institutionId, UserType $type): int
    {
        $s = $this->findCodeSetting($institutionId, $type);

        return ($s && $s->start_code !== null)
            ? max(1, (int) $s->start_code)
            : self::DEFAULT_USER_CODE_START;
    }

    private function manualStart(int $institutionId, UserType $type): int
    {
        $s = $this->findCodeSetting($institutionId, $type);

        return ($s && $s->manual_start_code !== null)
            ? max(1, (int) $s->manual_start_code)
            : $this->autoStart($institutionId, $type);
    }

    private function nextFreeCode(int $institutionId, int $start): string
    {
        $used = User::query()
            ->where('institution_id', $institutionId)
            ->whereNotNull('institution_user_id')
            ->whereRaw("institution_user_id REGEXP '^[0-9]+$'")
            ->pluck('institution_user_id')
            ->map(fn ($c) => (int) $c)
            ->unique()
            ->flip();

        $next = $start;

        while ($used->has($next)) {
            $next++;
        }

        return (string) $next;
    }

    private function codeExists(int $institutionId, string $code): bool
    {
        return User::query()
            ->where('institution_id', $institutionId)
            ->whereNotNull('institution_user_id')
            ->whereRaw("institution_user_id REGEXP '^[0-9]+$'")
            ->whereRaw('CAST(institution_user_id AS UNSIGNED) = ?', [(int) $code])
            ->exists();
    }

    private function previewPair(int $institutionId, string $generation = 'auto'): array
    {
        $type = $this->teacherUserType();

        $userCode = null;

        if ($type) {
            $start = $generation === 'manual'
                ? $this->manualStart($institutionId, $type)
                : $this->autoStart($institutionId, $type);

            $userCode = $this->nextFreeCode($institutionId, $start);
        }

        return [
            'teacher_code' => Teacher::generateTeacherCode($institutionId),
            'user_code'    => $userCode,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX: Code preview / Code setting
    |--------------------------------------------------------------------------
    */

    public function previewCodes(Request $request)
    {
        $institutionId = $this->currentInstitutionId($request);

        if (!$institutionId || !Madrasa::whereKey($institutionId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'প্রতিষ্ঠান নির্বাচন করুন।',
            ], 422);
        }

        if (!$this->teacherUserType()) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher User Type পাওয়া যায়নি।',
            ], 422);
        }

        $generation = $request->input('generation_type') === 'manual' ? 'manual' : 'auto';

        return response()->json(
            ['success' => true] + $this->previewPair($institutionId, $generation)
        );
    }

    public function getCodeSetting(Request $request)
    {
        $institutionId = $this->currentInstitutionId($request);
        $type = $this->teacherUserType();

        if (!$institutionId || !$type) {
            return response()->json([
                'success' => false,
                'message' => 'আগে প্রতিষ্ঠান নির্বাচন করুন।',
            ], 422);
        }

        $s = $this->findCodeSetting($institutionId, $type);

        return response()->json([
            'success'           => true,
            'start_code'        => $this->autoStart($institutionId, $type),
            'manual_start_code' => $s && $s->manual_start_code !== null
                ? (int) $s->manual_start_code
                : null,
        ]);
    }

    public function saveCodeSetting(Request $request)
    {
        $institutionId = $this->currentInstitutionId($request);
        $type = $this->teacherUserType();

        if (!$institutionId || !$type) {
            return response()->json([
                'success' => false,
                'message' => 'আগে প্রতিষ্ঠান নির্বাচন করুন।',
            ], 422);
        }

        $data = $request->validate([
            'start_code'        => ['required', 'integer', 'min:1', 'max:999999999'],
            'manual_start_code' => ['nullable', 'integer', 'min:1', 'max:999999999'],
        ], [
            'start_code.required' => 'Auto Starting Code দিতে হবে।',
            'start_code.integer'  => 'Starting Code অবশ্যই সংখ্যা হতে হবে।',
        ]);

        $s = $this->findCodeSetting($institutionId, $type) ?? new UserCodeSetting();

        if (!$s->exists) {
            $s->institution_id     = $institutionId;
            $s->role_slug          = 'user_type_' . $type->id;
            $s->gender_preference  = 'male';
        }

        $s->user_type_id = $type->id;
        $s->start_code = (int) $data['start_code'];
        $s->manual_start_code = !empty($data['manual_start_code'])
            ? (int) $data['manual_start_code']
            : null;
        $s->save();

        return response()->json([
            'success' => true,
            'message' => 'Code Setting সংরক্ষণ করা হয়েছে।',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Teacher::query()->with([
            'user.role',
            'user.userType',
            'classTeacherFor',
        ]);

        $scopeId = $this->listInstitutionId();

        if ($scopeId !== null) {
            $query->where('teachers.institution_id', $scopeId);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('teacher_id', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('name_bn', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('father_name', 'like', "%{$search}%")
                            ->orWhere('institution_user_id', 'like', "%{$search}%");
                    });
            });
        }

        if (in_array($request->status, ['0', '1'], true)) {
            $status = (int) $request->status;

            $query->whereHas('user', fn ($u) => $u->where('status', $status));
        }

        $perPage = (int) $request->get('per_page', 20);

        if (!in_array($perPage, [10, 20, 50], true)) {
            $perPage = 20;
        }

        $teachers = $query->latest('id')->paginate($perPage)->withQueryString();

        return view('admin.teachers.index', $this->formData() + compact('teachers'));
    }

    /**
     * Create modal / create page এর জন্য common data।
     */
    private function formData(): array
    {
        $institutionId = $this->currentInstitutionId();
        $institutions = $this->availableInstitutions();

        $teacherCode = null;
        $userCode = null;

        if ($institutionId) {
            $pair = $this->previewPair($institutionId);
            $teacherCode = $pair['teacher_code'];
            $userCode = $pair['user_code'];
        }

        $divisions = Division::all();

        $classes = Classes::query()
            ->when($institutionId, fn ($q) => $q->where('institution_id', $institutionId))
            ->get();

        return compact(
            'institutionId',
            'institutions',
            'teacherCode',
            'userCode',
            'divisions',
            'classes'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.teachers.create', $this->formData());
    }

    /*
    |--------------------------------------------------------------------------
    | Validation rules
    |--------------------------------------------------------------------------
    */

    private function rules(?int $userId = null): array
    {
        return [
            'name_bn' => ['required', 'string', 'max:255'],
            'name'    => ['nullable', 'string', 'max:255'],
            'gender'  => ['required', 'in:male,female'],

            'date_of_birth' => ['nullable', 'date'],
            'age'           => ['nullable', 'integer', 'min:0', 'max:150'],
            'nid'           => ['nullable', 'string', 'max:100'],
            'birth_certificate' => ['nullable', 'string', 'max:100'],
            'blood_group'   => ['nullable', 'string', 'max:20'],

            'phone' => [
                'required', 'string', 'max:30',
                Rule::unique('users', 'phone')->ignore($userId),
            ],
            'phone2'      => ['nullable', 'string', 'max:30'],
            'phone_owner' => ['nullable', 'string', 'max:100'],
            'email' => [
                'nullable', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],

            'permanent_division_id' => ['nullable', 'exists:divisions,id'],
            'permanent_district_id' => ['nullable', 'exists:districts,id'],
            'permanent_upazila_id'  => ['nullable', 'exists:upazilas,id'],
            'permanent_post_office' => ['nullable', 'string', 'max:255'],
            'permanent_village_road' => ['nullable', 'string', 'max:255'],

            'present_division_id' => ['nullable', 'exists:divisions,id'],
            'present_district_id' => ['nullable', 'exists:districts,id'],
            'present_upazila_id'  => ['nullable', 'exists:upazilas,id'],
            'present_post_office' => ['nullable', 'string', 'max:255'],
            'present_village_road' => ['nullable', 'string', 'max:255'],

            'designation'  => ['required', 'string', 'max:100'],
            'joining_date' => ['required', 'date'],
            'pass_department' => ['nullable', 'string', 'max:150'],
            'pass_year' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
            'pass_date' => ['nullable', 'date'],
            'qualification' => ['nullable', 'string'],
            'completion_institution' => ['nullable', 'string', 'max:255'],
            'experience' => ['nullable', 'string'],
            'expertise_subjects' => ['nullable', 'string'],
            'salary_scale' => ['nullable', 'string', 'max:50'],
            'basic_salary' => ['nullable', 'numeric', 'min:0'],
            'is_class_teacher' => ['nullable', 'in:0,1'],
            'class_teacher_for' => ['nullable', 'required_if:is_class_teacher,1', 'exists:classes,id'],
            'remarks' => ['nullable', 'string'],

            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    private function messages(): array
    {
        return [
            'phone.unique' => 'এই মোবাইল নম্বর ইতোমধ্যে ব্যবহার করা হয়েছে।',
            'email.unique' => 'এই ইমেইল ইতোমধ্যে ব্যবহার করা হয়েছে।',
            'name_bn.required' => 'পূর্ণ নাম (বাংলা) দিন।',
            'phone.required' => 'মোবাইল ১ নম্বর দিন।',
            'gender.required' => 'লিঙ্গ নির্বাচন করুন।',
            'date_of_birth.required' => 'জন্ম তারিখ দিন।',
            'designation.required' => 'পদবী দিন।',
            'joining_date.required' => 'যোগদানের তারিখ দিন।',
            'class_teacher_for.required_if' => 'শ্রেণিশিক্ষক হলে শ্রেণি নির্বাচন করুন।',
        ];
    }

    private function addressString(array $data, string $type): string
    {
        $parts = [];

        foreach (["{$type}_village_road", "{$type}_post_office"] as $key) {
            if (!empty($data[$key])) {
                $parts[] = $data[$key];
            }
        }

        $models = [
            ["{$type}_upazila_id", Upazila::class],
            ["{$type}_district_id", District::class],
            ["{$type}_division_id", Division::class],
        ];

        foreach ($models as [$key, $model]) {
            if (!empty($data[$key]) && ($m = $model::find($data[$key]))) {
                $parts[] = $m->name_bn ?: $m->name;
            }
        }

        return implode(', ', $parts);
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $institutionId = $this->currentInstitutionId($request);

        if (!$institutionId || !Madrasa::whereKey($institutionId)->exists()) {
            throw ValidationException::withMessages([
                'institution_id' => 'প্রতিষ্ঠান নির্বাচন করুন।',
            ]);
        }

        $validated = $request->validate(
            array_merge($this->rules(), [
                'date_of_birth' => ['required', 'date'],
                'institution_id' => ['nullable', 'exists:madrasas,id'],
                'code_generation_type' => ['nullable', 'in:auto,manual'],
                'custom_id' => ['nullable', 'string', 'max:100'],
            ]),
            $this->messages()
        );

        $userType = $this->teacherUserType();

        if (!$userType) {
            throw ValidationException::withMessages([
                'user_type' => 'Teacher User Type পাওয়া যায়নি। user_types টেবিলে slug = teacher যোগ করুন।',
            ]);
        }

        $role = Role::where('slug', 'user')->first();

        if (!$role) {
            throw ValidationException::withMessages([
                'role' => 'User role পাওয়া যায়নি।',
            ]);
        }

        $photoPath = null;

        try {
            DB::transaction(function () use (
                $request, $validated, $institutionId, $userType, $role, $authUser, &$photoPath
            ) {
                // concurrent duplicate কমাতে lock
                User::query()->where('institution_id', $institutionId)->lockForUpdate()->pluck('id');
                Teacher::query()->where('institution_id', $institutionId)->lockForUpdate()->pluck('id');

                $generation = $validated['code_generation_type'] ?? 'auto';

                if ($generation === 'manual') {
                    $userCode = trim((string) ($validated['custom_id'] ?? ''));

                    if ($userCode === '') {
                        $userCode = $this->nextFreeCode(
                            $institutionId,
                            $this->manualStart($institutionId, $userType)
                        );
                    }

                    if (!ctype_digit($userCode)) {
                        throw ValidationException::withMessages([
                            'custom_id' => 'User Code শুধুমাত্র সংখ্যা হতে হবে।',
                        ]);
                    }

                    $min = $this->manualStart($institutionId, $userType);

                    if ((int) $userCode < $min) {
                        throw ValidationException::withMessages([
                            'custom_id' => "Manual Code {$min} থেকে শুরু হতে হবে।",
                        ]);
                    }

                    if ($this->codeExists($institutionId, $userCode)) {
                        throw ValidationException::withMessages([
                            'custom_id' => 'এই User Code ইতোমধ্যে ব্যবহার করা হয়েছে।',
                        ]);
                    }
                } else {
                    $userCode = $this->nextFreeCode(
                        $institutionId,
                        $this->autoStart($institutionId, $userType)
                    );
                }

                $teacherCode = Teacher::generateTeacherCode($institutionId);

                $base = 'teacher_' . $institutionId . '_' . $userCode;
                $username = $base;
                $i = 2;

                while (User::where('username', $username)->exists()) {
                    $username = $base . '_' . $i++;
                }

                if ($request->hasFile('photo')) {
                    $photoPath = $request->file('photo')->store('users', 'public');
                }

                $dob = $validated['date_of_birth'] ?? null;

                $user = User::create([
                    'institution_id' => $institutionId,
                    'role_id' => $role->id,
                    'user_type_id' => $userType->id,
                    'institution_user_id' => $userCode,
                    'code_generation_type' => $generation,
                    'username' => $username,
                    'email' => $validated['email'] ?? null,
                    'password' => Hash::make($validated['phone']),
                    'name' => $validated['name'] ?? $validated['name_bn'],
                    'name_bn' => $validated['name_bn'],
                    'photo' => $photoPath,
                    'phone' => $validated['phone'],
                    'phone2' => $validated['phone2'] ?? null,
                    'phone_owner' => $validated['phone_owner'] ?? null,
                    'gender' => $validated['gender'],
                    'date_of_birth' => $dob,
                    'age' => $dob ? Carbon::parse($dob)->age : ($validated['age'] ?? null),
                    'blood_group' => $validated['blood_group'] ?? null,
                    'nid' => $validated['nid'] ?? null,
                    'birth_certificate' => $validated['birth_certificate'] ?? null,
                    'father_name' => $validated['father_name'] ?? null,
                    'mother_name' => $validated['mother_name'] ?? null,

                    'permanent_division_id' => $validated['permanent_division_id'] ?? null,
                    'permanent_district_id' => $validated['permanent_district_id'] ?? null,
                    'permanent_upazila_id' => $validated['permanent_upazila_id'] ?? null,
                    'permanent_post_office' => $validated['permanent_post_office'] ?? null,
                    'permanent_village_road' => $validated['permanent_village_road'] ?? null,
                    'permanent_address_full' => $this->addressString($validated, 'permanent'),

                    'present_division_id' => $validated['present_division_id'] ?? null,
                    'present_district_id' => $validated['present_district_id'] ?? null,
                    'present_upazila_id' => $validated['present_upazila_id'] ?? null,
                    'present_post_office' => $validated['present_post_office'] ?? null,
                    'present_village_road' => $validated['present_village_road'] ?? null,
                    'present_address_full' => $this->addressString($validated, 'present'),

                    'status' => 1,
                    'created_by' => $authUser->id,
                ]);

                $isClassTeacher = $request->boolean('is_class_teacher');

                Teacher::create([
                    'user_id' => $user->id,
                    'institution_id' => $institutionId,
                    'teacher_id' => $teacherCode,
                    'designation' => $validated['designation'],
                    'joining_date' => $validated['joining_date'],
                    'pass_department' => $validated['pass_department'] ?? null,
                    'pass_year' => $validated['pass_year'] ?? null,
                    'pass_date' => $validated['pass_date'] ?? null,
                    'qualification' => $validated['qualification'] ?? null,
                    'completion_institution' => $validated['completion_institution'] ?? null,
                    'experience' => $validated['experience'] ?? null,
                    'expertise_subjects' => $validated['expertise_subjects'] ?? null,
                    'salary_scale' => $validated['salary_scale'] ?? null,
                    'basic_salary' => $validated['basic_salary'] ?? null,
                    'is_class_teacher' => $isClassTeacher,
                    'class_teacher_for' => $isClassTeacher
                        ? ($validated['class_teacher_for'] ?? null)
                        : null,
                    'remarks' => $validated['remarks'] ?? null,
                ]);
            });
        } catch (\Throwable $e) {
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            if ($e instanceof ValidationException) {
                throw $e;
            }

            return back()
                ->withInput()
                ->with('error', 'শিক্ষক তৈরি করতে সমস্যা হয়েছে: ' . $e->getMessage());
        }

        return redirect()
            ->route('dashboard.teacher.index')
            ->with('success', 'শিক্ষক সফলভাবে তৈরি হয়েছে।');
    }

    /*
    |--------------------------------------------------------------------------
    | Show / Edit
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $teacher = $this->findTeacher($id);

        $teacher->load(['user', 'user.role', 'classTeacherFor', 'institution']);

        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit($id)
    {
        $teacher = $this->findTeacher($id);

        $teacher->load('user');

        $divisions = Division::all();

        $classes = Classes::query()
            ->where('institution_id', $teacher->institution_id)
            ->get();

        return view('admin.teachers.edit', compact('teacher', 'divisions', 'classes'));
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $teacher = $this->findTeacher($id);
        $user = $teacher->user;

        if (!$user) {
            abort(404);
        }

        $validated = $request->validate($this->rules($user->id), $this->messages());

        $photoPath = $user->photo;
        $newPhoto = null;

        try {
            DB::transaction(function () use ($request, $validated, $teacher, $user, &$photoPath, &$newPhoto) {

                if ($request->hasFile('photo')) {
                    $newPhoto = $request->file('photo')->store('users', 'public');
                    $oldPhoto = $user->photo;
                    $photoPath = $newPhoto;

                    if ($oldPhoto && Storage::disk('public')->exists($oldPhoto)) {
                        Storage::disk('public')->delete($oldPhoto);
                    }
                }

                $dob = $validated['date_of_birth'] ?? null;

                $userData = [
                    'name' => $validated['name'] ?? $validated['name_bn'],
                    'name_bn' => $validated['name_bn'],
                    'email' => $validated['email'] ?? null,
                    'phone' => $validated['phone'],
                    'phone2' => $validated['phone2'] ?? null,
                    'phone_owner' => $validated['phone_owner'] ?? null,
                    'gender' => $validated['gender'],
                    'date_of_birth' => $dob,
                    'age' => $dob ? Carbon::parse($dob)->age : ($validated['age'] ?? null),
                    'blood_group' => $validated['blood_group'] ?? null,
                    'nid' => $validated['nid'] ?? null,
                    'birth_certificate' => $validated['birth_certificate'] ?? null,
                    'father_name' => $validated['father_name'] ?? null,
                    'mother_name' => $validated['mother_name'] ?? null,
                    'photo' => $photoPath,
                    'updated_by' => auth()->id(),
                ];

                // ঠিকানা: ফর্মে পাঠানো হলেই আপডেট হবে
                $userData += Arr::only($validated, self::ADDRESS_KEYS);

                foreach (['permanent', 'present'] as $type) {
                    $touched = collect(self::ADDRESS_KEYS)
                        ->filter(fn ($k) => str_starts_with($k, $type))
                        ->contains(fn ($k) => array_key_exists($k, $validated));

                    if ($touched) {
                        $userData["{$type}_address_full"] = $this->addressString($validated, $type);
                    }
                }

                $user->update($userData);

                $isClassTeacher = $request->boolean('is_class_teacher');

                $teacher->update([
                    'designation' => $validated['designation'],
                    'joining_date' => $validated['joining_date'],
                    'pass_department' => $validated['pass_department'] ?? null,
                    'pass_year' => $validated['pass_year'] ?? null,
                    'pass_date' => $validated['pass_date'] ?? null,
                    'qualification' => $validated['qualification'] ?? null,
                    'completion_institution' => $validated['completion_institution'] ?? null,
                    'experience' => $validated['experience'] ?? null,
                    'expertise_subjects' => $validated['expertise_subjects'] ?? null,
                    'salary_scale' => $validated['salary_scale'] ?? null,
                    'basic_salary' => $validated['basic_salary'] ?? null,
                    'is_class_teacher' => $isClassTeacher,
                    'class_teacher_for' => $isClassTeacher
                        ? ($validated['class_teacher_for'] ?? null)
                        : null,
                    'remarks' => $validated['remarks'] ?? null,
                ]);
            });
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'আপডেট করতে সমস্যা হয়েছে: ' . $e->getMessage());
        }

        return redirect()
            ->route('dashboard.teacher.index')
            ->with('success', 'শিক্ষকের তথ্য সফলভাবে আপডেট হয়েছে।');
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $teacher = $this->findTeacher($id);

        DB::transaction(function () use ($teacher) {
            $user = $teacher->user;

            if ($user && $user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $teacher->delete();

            if ($user) {
                $user->delete();
            }
        });

        return redirect()
            ->route('dashboard.teacher.index')
            ->with('success', 'শিক্ষক সফলভাবে মুছে ফেলা হয়েছে।');
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Status
    |--------------------------------------------------------------------------
    */

    public function toggleStatus($id)
    {
        $teacher = $this->findTeacher($id);

        $user = $teacher->user;

        if (!$user) {
            abort(404);
        }

        $user->update([
            'status' => $user->status ? 0 : 1,
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'শিক্ষকের স্ট্যাটাস পরিবর্তন হয়েছে।');
    }

    /*
    |--------------------------------------------------------------------------
    | Address AJAX
    |--------------------------------------------------------------------------
    */

    public function getDistricts($divisionId)
    {
        return response()->json(
            District::where('division_id', $divisionId)
                ->get()
                ->map(fn ($d) => [
                    'id' => $d->id,
                    'name' => $d->name_bn ?: $d->name,
                ])
                ->sortBy('name')
                ->values()
        );
    }

    public function getUpazilas($districtId)
    {
        return response()->json(
            Upazila::where('district_id', $districtId)
                ->get()
                ->map(fn ($u) => [
                    'id' => $u->id,
                    'name' => $u->name_bn ?: $u->name,
                ])
                ->sortBy('name')
                ->values()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Find Teacher (Institution isolation)
    |--------------------------------------------------------------------------
    */

    protected function findTeacher($id): Teacher
    {
        $query = Teacher::query()->with('user');

        $scopeId = $this->listInstitutionId();

        if ($scopeId !== null) {
            $query->where('institution_id', $scopeId);
        }

        return $query->findOrFail($id);
    }
}