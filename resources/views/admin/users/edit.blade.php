@extends('layouts.admin')

@section('page')

@php
    /*
    |--------------------------------------------------------------------------
    | Current User / Role Information
    |--------------------------------------------------------------------------
    */

    $currentRoleSlug = optional($user->role)->slug
        ?? str_replace(
            [' ', '-'],
            '_',
            strtolower(
                trim(
                    optional($user->role)->role_name ?? ''
                )
            )
        );

    $myRole = str_replace(
        [' ', '-'],
        '_',
        strtolower(
            trim(
                optional(Auth::user()->role)->role_name ?? 'user'
            )
        )
    );

    /*
    |--------------------------------------------------------------------------
    | Code Generation
    |--------------------------------------------------------------------------
    */

    $currentGenerationType = old(
        'code_generation_type',
        $user->code_generation_type ?? 'auto'
    );

    $currentCode = old(
        'custom_id',
        $user->institution_user_id ?? ''
    );

    /*
    |--------------------------------------------------------------------------
    | Current Values
    |--------------------------------------------------------------------------
    */

    $selectedInstitution = old(
        'institution_id',
        $user->institution_id
    );

    $selectedGender = old(
        'gender',
        $user->gender
    );

    $selectedStatus = old(
        'status',
        $user->status
    );

    $selectedUserType = old(
        'user_type',
        $currentRoleSlug
    );

    $codeStartMap = [
        'student'       => 101,
        'user'          => 201,
        'user_admin'    => 301,
        'support_admin' => 401,
    ];
@endphp


<div class="container-fluid py-4">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">
                <i class="bi bi-person-gear me-2"></i>
                ইউজার তথ্য সম্পাদনা
            </h4>

            <p class="text-muted mb-0">
                ইউজারের তথ্য, Role এবং User Code পরিবর্তন করুন।
            </p>
        </div>

        <div>
            <a href="{{ route('dashboard.user.index') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                ফিরে যান
            </a>
        </div>

    </div>


    {{-- =========================================================
         SUCCESS / ERROR MESSAGE
    ========================================================== --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif


    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-1"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif


    {{-- =========================================================
         VALIDATION ERRORS
    ========================================================== --}}
    @if($errors->any())
        <div class="alert alert-danger">

            <strong>
                <i class="bi bi-exclamation-triangle me-1"></i>
                কিছু তথ্য সঠিক নয়:
            </strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>
    @endif


    {{-- =========================================================
         MAIN FORM
    ========================================================== --}}
    <form method="POST"
          action="{{ route('dashboard.user.update', $user->id) }}"
          enctype="multipart/form-data">

        @csrf

        {{-- Hidden User ID --}}
        <input type="hidden"
               name="id"
               value="{{ $user->id }}">

        {{-- Hidden Code Generation Type --}}
        <input type="hidden"
               name="code_generation_type"
               id="code_generation_type"
               value="{{ $currentGenerationType }}">


        <div class="row g-4">


            {{-- =================================================
                 LEFT COLUMN
            ================================================== --}}
            <div class="col-lg-8">

                {{-- =================================================
                     BASIC INFORMATION
                ================================================== --}}
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-person me-2"></i>
                            মৌলিক তথ্য
                        </h5>
                    </div>

                    <div class="card-body">

                        <div class="row g-3">


                            {{-- Name Bangla --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    নাম (বাংলা)
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       name="name_bn"
                                       class="form-control @error('name_bn') is-invalid @enderror"
                                       value="{{ old('name_bn', $user->name_bn) }}"
                                       placeholder="বাংলায় নাম লিখুন"
                                       required>

                                @error('name_bn')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- Name English --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    নাম (English)
                                </label>

                                <input type="text"
                                       name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}"
                                       placeholder="Enter name in English">

                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- Phone --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    ফোন নম্বর
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $user->phone) }}"
                                       placeholder="01XXXXXXXXX"
                                       required>

                                @error('phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- Email --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    Email
                                </label>

                                <input type="email"
                                       name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}"
                                       placeholder="example@email.com">

                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- Username --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    Username
                                </label>

                                <input type="text"
                                       class="form-control bg-light"
                                       value="{{ $user->username }}"
                                       readonly>

                                <small class="text-muted">
                                    Username পরিবর্তন করা যাবে না।
                                </small>

                            </div>


                            {{-- Gender --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    লিঙ্গ
                                    <span class="text-danger">*</span>
                                </label>

                                <select name="gender"
                                        class="form-select @error('gender') is-invalid @enderror"
                                        required>

                                    <option value="">
                                        নির্বাচন করুন
                                    </option>

                                    <option value="male"
                                        {{ $selectedGender === 'male' ? 'selected' : '' }}>
                                        পুরুষ
                                    </option>

                                    <option value="female"
                                        {{ $selectedGender === 'female' ? 'selected' : '' }}>
                                        নারী
                                    </option>

                                </select>

                                @error('gender')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- Password --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    নতুন Password
                                </label>

                                <input type="password"
                                       name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="পরিবর্তন করতে চাইলে লিখুন">

                                <small class="text-muted">
                                    খালি রাখলে আগের password অপরিবর্তিত থাকবে।
                                </small>

                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- Blood Group --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    Blood Group
                                </label>

                                <input type="text"
                                       name="blood_group"
                                       class="form-control"
                                       value="{{ old('blood_group', $user->blood_group) }}"
                                       placeholder="যেমন: A+">

                            </div>


                            {{-- Date of Birth --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    জন্ম তারিখ
                                </label>

                                <input type="date"
                                       name="date_of_birth"
                                       class="form-control"
                                       value="{{ old('date_of_birth', $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') : '') }}">

                            </div>


                            {{-- Religion --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    ধর্ম
                                </label>

                                <input type="text"
                                       name="religion"
                                       class="form-control"
                                       value="{{ old('religion', $user->religion) }}"
                                       placeholder="ধর্ম">

                            </div>


                            {{-- NID --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    NID
                                </label>

                                <input type="text"
                                       name="nid"
                                       class="form-control"
                                       value="{{ old('nid', $user->nid) }}"
                                       placeholder="NID Number">

                            </div>


                            {{-- Birth Certificate --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    জন্ম নিবন্ধন
                                </label>

                                <input type="text"
                                       name="birth_certificate"
                                       class="form-control"
                                       value="{{ old('birth_certificate', $user->birth_certificate) }}"
                                       placeholder="Birth Certificate Number">

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     ROLE & CODE SETTINGS
                ================================================== --}}
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-white py-3">

                        <div class="d-flex justify-content-between align-items-center">

                            <h5 class="mb-0">
                                <i class="bi bi-shield-lock me-2"></i>
                                Role & User Code
                            </h5>

                            @if($currentCode)
                                <span class="badge bg-primary">
                                    Current Code:
                                    {{ $currentCode }}
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    No Code
                                </span>
                            @endif

                        </div>

                    </div>


                    <div class="card-body">

                        <div class="row g-3">


                            {{-- Role --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    User Role
                                    <span class="text-danger">*</span>
                                </label>

                                <select name="user_type"
                                        id="user_type"
                                        class="form-select @error('user_type') is-invalid @enderror"
                                        required>

                                    <option value="">
                                        Role নির্বাচন করুন
                                    </option>

                                    @foreach($roles as $role)

                                        @if(in_array($role->slug, [
                                            'super_admin',
                                            'support_admin',
                                            'user_admin',
                                            'user',
                                            'student'
                                        ]))

                                            <option value="{{ $role->slug }}"
                                                {{ $selectedUserType === $role->slug ? 'selected' : '' }}>
                                                {{ $role->role_name ?? ucfirst(str_replace('_', ' ', $role->slug)) }}
                                            </option>

                                        @endif

                                    @endforeach

                                </select>

                                @error('user_type')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- Current Code --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    বর্তমান User Code
                                </label>

                                <input type="text"
                                       id="current_code_display"
                                       class="form-control bg-light"
                                       value="{{ $currentCode ?: 'নেই' }}"
                                       readonly>

                            </div>


                            {{-- Code Generation Type --}}
                            <div class="col-12">

                                <label class="form-label">
                                    User Code Generation
                                </label>

                                <div class="row g-3">


                                    {{-- Auto --}}
                                    <div class="col-md-6">

                                        <div class="border rounded p-3 h-100 code-mode-option"
                                             id="autoModeBox">

                                            <div class="form-check">

                                                <input class="form-check-input"
                                                       type="radio"
                                                       name="code_mode_radio"
                                                       id="code_auto"
                                                       value="auto"
                                                       {{ $currentGenerationType === 'auto' ? 'checked' : '' }}>

                                                <label class="form-check-label fw-semibold"
                                                       for="code_auto">

                                                    <i class="bi bi-magic me-1"></i>
                                                    Auto Generate

                                                </label>

                                            </div>

                                            <small class="text-muted d-block mt-2">

                                                Role অনুযায়ী Code automatically তৈরি হবে।

                                                <br>

                                                <span id="autoCodeExample">
                                                    Student: 101, 102, 103...
                                                </span>

                                            </small>

                                        </div>

                                    </div>


                                    {{-- Manual --}}
                                    <div class="col-md-6">

                                        <div class="border rounded p-3 h-100 code-mode-option"
                                             id="manualModeBox">

                                            <div class="form-check">

                                                <input class="form-check-input"
                                                       type="radio"
                                                       name="code_mode_radio"
                                                       id="code_manual"
                                                       value="manual"
                                                       {{ $currentGenerationType === 'manual' ? 'checked' : '' }}>

                                                <label class="form-check-label fw-semibold"
                                                       for="code_manual">

                                                    <i class="bi bi-pencil-square me-1"></i>
                                                    Manual Code

                                                </label>

                                            </div>

                                            <small class="text-muted d-block mt-2">

                                                Code নিজে দিতে পারবেন।

                                                <br>

                                                খালি রাখলে 1001 থেকে পরবর্তী available code তৈরি হবে।

                                            </small>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            {{-- Manual Code --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    Manual User Code
                                </label>

                                <input type="text"
                                       name="custom_id"
                                       id="custom_id"
                                       class="form-control @error('custom_id') is-invalid @enderror"
                                       value="{{ $currentCode }}"
                                       placeholder="যেমন: 1001"
                                       autocomplete="off">

                                <small id="manualHelp"
                                       class="text-muted">
                                    Manual mode নির্বাচন করলে এখানে Code দিতে পারবেন।
                                    খালি রাখলে 1001 থেকে system নিজে Code তৈরি করবে।
                                </small>

                                @error('custom_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- Code Information --}}
                            <div class="col-md-6">

                                <div class="alert alert-light border mb-0">

                                    <div class="fw-semibold mb-2">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Code System
                                    </div>

                                    <ul class="mb-0 ps-3 small">

                                        <li>
                                            Student → 101, 102, 103...
                                        </li>

                                        <li>
                                            User → 201, 202, 203...
                                        </li>

                                        <li>
                                            User Admin → 301, 302, 303...
                                        </li>

                                        <li>
                                            Support Admin → 401, 402, 403...
                                        </li>

                                        <li>
                                            Manual → 1001, 1002, 1003...
                                        </li>

                                    </ul>

                                </div>

                            </div>


                            {{-- Institution --}}
                            @if(Auth::user()->is_super_admin)

                                <div class="col-md-6">

                                    <label class="form-label">
                                        প্রতিষ্ঠান
                                    </label>

                                    <select name="institution_id"
                                            id="institution_id"
                                            class="form-select @error('institution_id') is-invalid @enderror">

                                        <option value="">
                                            প্রতিষ্ঠান নির্বাচন করুন
                                        </option>

                                        @foreach($institutions as $institution)

                                            <option value="{{ $institution->id }}"
                                                {{ (string)$selectedInstitution === (string)$institution->id ? 'selected' : '' }}>

                                                {{ $institution->name_bn
                                                    ?? $institution->name
                                                    ?? $institution->title
                                                    ?? 'Institution #' . $institution->id }}

                                            </option>

                                        @endforeach

                                    </select>

                                    @error('institution_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            @else

                                <input type="hidden"
                                       name="institution_id"
                                       value="{{ $user->institution_id }}">

                            @endif

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     ADDITIONAL INFORMATION
                ================================================== --}}
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-white py-3">

                        <h5 class="mb-0">
                            <i class="bi bi-person-lines-fill me-2"></i>
                            অতিরিক্ত তথ্য
                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="row g-3">


                            <div class="col-md-6">

                                <label class="form-label">
                                    ফোনের মালিক
                                </label>

                                <input type="text"
                                       name="phone_relation"
                                       class="form-control"
                                       value="{{ old('phone_relation', $user->phone_owner) }}"
                                       placeholder="নিজের / বাবার / মায়ের">

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    বিকল্প ফোন
                                </label>

                                <input type="text"
                                       name="phone2"
                                       class="form-control"
                                       value="{{ old('phone2', $user->phone2) }}"
                                       placeholder="বিকল্প ফোন নম্বর">

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    পিতার নাম
                                </label>

                                <input type="text"
                                       name="father_name"
                                       class="form-control"
                                       value="{{ old('father_name', $user->father_name) }}">

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    পিতার ফোন
                                </label>

                                <input type="text"
                                       name="father_phone"
                                       class="form-control"
                                       value="{{ old('father_phone', $user->father_phone) }}">

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    মাতার নাম
                                </label>

                                <input type="text"
                                       name="mother_name"
                                       class="form-control"
                                       value="{{ old('mother_name', $user->mother_name) }}">

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    মাতার ফোন
                                </label>

                                <input type="text"
                                       name="mother_phone"
                                       class="form-control"
                                       value="{{ old('mother_phone', $user->mother_phone) }}">

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    Guardian Name
                                </label>

                                <input type="text"
                                       name="guardian_name"
                                       class="form-control"
                                       value="{{ old('guardian_name', $user->guardian_name) }}">

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    Guardian Phone
                                </label>

                                <input type="text"
                                       name="guardian_phone"
                                       class="form-control"
                                       value="{{ old('guardian_phone', $user->guardian_phone) }}">

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    Guardian Relation
                                </label>

                                <input type="text"
                                       name="guardian_relation"
                                       class="form-control"
                                       value="{{ old('guardian_relation', $user->guardian_relation) }}">

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     ADDRESS
                ================================================== --}}
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-white py-3">

                        <h5 class="mb-0">
                            <i class="bi bi-geo-alt me-2"></i>
                            ঠিকানা
                        </h5>

                    </div>

                    <div class="card-body">

                        <h6 class="mb-3">
                            বর্তমান ঠিকানা
                        </h6>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label class="form-label">
                                    Division
                                </label>

                                <select name="present_division_id"
                                        class="form-select">

                                    <option value="">
                                        নির্বাচন করুন
                                    </option>

                                    @foreach($divisions as $division)

                                        <option value="{{ $division->id }}"
                                            {{ (string)old('present_division_id', $user->present_division_id) === (string)$division->id ? 'selected' : '' }}>

                                            {{ $division->name_bn
                                                ?? $division->name }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    District
                                </label>

                                <select name="present_district_id"
                                        class="form-select">

                                    <option value="">
                                        নির্বাচন করুন
                                    </option>

                                    @foreach($divisions as $division)

                                        @foreach($division->districts ?? [] as $district)

                                            <option value="{{ $district->id }}"
                                                {{ (string)old('present_district_id', $user->present_district_id) === (string)$district->id ? 'selected' : '' }}>

                                                {{ $district->name_bn
                                                    ?? $district->name }}

                                            </option>

                                        @endforeach

                                    @endforeach

                                </select>

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    Upazila
                                </label>

                                <select name="present_upazila_id"
                                        class="form-select">

                                    <option value="">
                                        নির্বাচন করুন
                                    </option>

                                    @foreach($divisions as $division)

                                        @foreach($division->districts ?? [] as $district)

                                            @foreach($district->upazilas ?? [] as $upazila)

                                                <option value="{{ $upazila->id }}"
                                                    {{ (string)old('present_upazila_id', $user->present_upazila_id) === (string)$upazila->id ? 'selected' : '' }}>

                                                    {{ $upazila->name_bn
                                                        ?? $upazila->name }}

                                                </option>

                                            @endforeach

                                        @endforeach

                                    @endforeach

                                </select>

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    Union
                                </label>

                                <input type="text"
                                       name="present_union"
                                       class="form-control"
                                       value="{{ old('present_union', $user->present_union) }}">

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    Post Office
                                </label>

                                <input type="text"
                                       name="present_post_office"
                                       class="form-control"
                                       value="{{ old('present_post_office', $user->present_post_office) }}">

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    Postal Code
                                </label>

                                <input type="text"
                                       name="present_postal_code"
                                       class="form-control"
                                       value="{{ old('present_postal_code', $user->present_postal_code) }}">

                            </div>


                            <div class="col-12">

                                <label class="form-label">
                                    Village / Road
                                </label>

                                <input type="text"
                                       name="present_village_road"
                                       class="form-control"
                                       value="{{ old('present_village_road', $user->present_village_road) }}">

                            </div>

                        </div>


                        <hr class="my-4">


                        <h6 class="mb-3">
                            স্থায়ী ঠিকানা
                        </h6>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label class="form-label">
                                    Division
                                </label>

                                <select name="permanent_division_id"
                                        class="form-select">

                                    <option value="">
                                        নির্বাচন করুন
                                    </option>

                                    @foreach($divisions as $division)

                                        <option value="{{ $division->id }}"
                                            {{ (string)old('permanent_division_id', $user->permanent_division_id) === (string)$division->id ? 'selected' : '' }}>

                                            {{ $division->name_bn
                                                ?? $division->name }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    District
                                </label>

                                <select name="permanent_district_id"
                                        class="form-select">

                                    <option value="">
                                        নির্বাচন করুন
                                    </option>

                                    @foreach($divisions as $division)

                                        @foreach($division->districts ?? [] as $district)

                                            <option value="{{ $district->id }}"
                                                {{ (string)old('permanent_district_id', $user->permanent_district_id) === (string)$district->id ? 'selected' : '' }}>

                                                {{ $district->name_bn
                                                    ?? $district->name }}

                                            </option>

                                        @endforeach

                                    @endforeach

                                </select>

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    Upazila
                                </label>

                                <select name="permanent_upazila_id"
                                        class="form-select">

                                    <option value="">
                                        নির্বাচন করুন
                                    </option>

                                    @foreach($divisions as $division)

                                        @foreach($division->districts ?? [] as $district)

                                            @foreach($district->upazilas ?? [] as $upazila)

                                                <option value="{{ $upazila->id }}"
                                                    {{ (string)old('permanent_upazila_id', $user->permanent_upazila_id) === (string)$upazila->id ? 'selected' : '' }}>

                                                    {{ $upazila->name_bn
                                                        ?? $upazila->name }}

                                                </option>

                                            @endforeach

                                        @endforeach

                                    @endforeach

                                </select>

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    Union
                                </label>

                                <input type="text"
                                       name="permanent_union"
                                       class="form-control"
                                       value="{{ old('permanent_union', $user->permanent_union) }}">

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    Post Office
                                </label>

                                <input type="text"
                                       name="permanent_post_office"
                                       class="form-control"
                                       value="{{ old('permanent_post_office', $user->permanent_post_office) }}">

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    Postal Code
                                </label>

                                <input type="text"
                                       name="permanent_postal_code"
                                       class="form-control"
                                       value="{{ old('permanent_postal_code', $user->permanent_postal_code) }}">

                            </div>


                            <div class="col-12">

                                <label class="form-label">
                                    Village / Road
                                </label>

                                <input type="text"
                                       name="permanent_village_road"
                                       class="form-control"
                                       value="{{ old('permanent_village_road', $user->permanent_village_road) }}">

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     STUDENT INFORMATION
                ================================================== --}}
                @if($selectedUserType === 'student')

                    <div class="card border-0 shadow-sm mb-4">

                        <div class="card-header bg-white py-3">

                            <h5 class="mb-0">
                                <i class="bi bi-mortarboard me-2"></i>
                                শিক্ষার্থী তথ্য
                            </h5>

                        </div>

                        <div class="card-body">

                            <div class="row g-3">

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Academic Year
                                    </label>

                                    <select name="academic_year_id"
                                            class="form-select">

                                        <option value="">
                                            নির্বাচন করুন
                                        </option>

                                        @foreach($academicYears as $year)

                                            <option value="{{ $year->id }}"
                                                {{ (string)old('academic_year_id', optional($user->student)->academic_year_id) === (string)$year->id ? 'selected' : '' }}>

                                                {{ $year->name }}

                                            </option>

                                        @endforeach

                                    </select>

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Class
                                    </label>

                                    <select name="class_id"
                                            class="form-select">

                                        <option value="">
                                            নির্বাচন করুন
                                        </option>

                                        @foreach($classes as $class)

                                            <option value="{{ $class->id }}"
                                                {{ (string)old('class_id', optional($user->student)->class_id) === (string)$class->id ? 'selected' : '' }}>

                                                {{ $class->name_bn
                                                    ?? $class->name }}

                                            </option>

                                        @endforeach

                                    </select>

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Section
                                    </label>

                                    <select name="section_id"
                                            class="form-select">

                                        <option value="">
                                            নির্বাচন করুন
                                        </option>

                                        @foreach($sections as $section)

                                            <option value="{{ $section->id }}"
                                                {{ (string)old('section_id', optional($user->student)->section_id) === (string)$section->id ? 'selected' : '' }}>

                                                {{ $section->name_bn
                                                    ?? $section->name }}

                                            </option>

                                        @endforeach

                                    </select>

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Guardian User ID
                                    </label>

                                    <input type="number"
                                           name="guardian_user_id"
                                           class="form-control"
                                           value="{{ old('guardian_user_id', optional($user->student)->guardian_user_id) }}">

                                </div>


                                <div class="col-md-6">

                                    <div class="form-check mt-4">

                                        <input type="checkbox"
                                               name="is_hostel"
                                               value="1"
                                               class="form-check-input"
                                               id="is_hostel"
                                               {{ old('is_hostel', optional($user->student)->is_hostel) ? 'checked' : '' }}>

                                        <label class="form-check-label"
                                               for="is_hostel">

                                            Hostel

                                        </label>

                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <div class="form-check mt-4">

                                        <input type="checkbox"
                                               name="is_transport"
                                               value="1"
                                               class="form-check-input"
                                               id="is_transport"
                                               {{ old('is_transport', optional($user->student)->is_transport) ? 'checked' : '' }}>

                                        <label class="form-check-label"
                                               for="is_transport">

                                            Transport

                                        </label>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                @endif

            </div>


            {{-- =================================================
                 RIGHT COLUMN
            ================================================== --}}
            <div class="col-lg-4">


                {{-- =================================================
                     PROFILE PHOTO
                ================================================== --}}
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-white py-3">

                        <h5 class="mb-0">
                            <i class="bi bi-image me-2"></i>
                            Profile Photo
                        </h5>

                    </div>

                    <div class="card-body text-center">

                        @if($user->photo)

                            <img src="{{ asset('storage/' . $user->photo) }}"
                                 alt="Profile Photo"
                                 class="rounded-circle mb-3"
                                 style="width:130px;height:130px;object-fit:cover;">

                        @else

                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width:130px;height:130px;">

                                <i class="bi bi-person fs-1 text-muted"></i>

                            </div>

                        @endif


                        <input type="file"
                               name="photo"
                               class="form-control"
                               accept=".jpg,.jpeg,.png">

                        <small class="text-muted d-block mt-2">
                            JPG, JPEG অথবা PNG
                            <br>
                            Maximum 2MB
                        </small>

                    </div>

                </div>


                {{-- =================================================
                     STATUS
                ================================================== --}}
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-white py-3">

                        <h5 class="mb-0">
                            <i class="bi bi-toggle-on me-2"></i>
                            Account Status
                        </h5>

                    </div>

                    <div class="card-body">

                        <select name="status"
                                class="form-select">

                            <option value="1"
                                {{ (string)$selectedStatus === '1' ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="0"
                                {{ (string)$selectedStatus === '0' ? 'selected' : '' }}>
                                Inactive
                            </option>

                        </select>

                    </div>

                </div>


                {{-- =================================================
                     USER SUMMARY
                ================================================== --}}
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-white py-3">

                        <h5 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            User Summary
                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="mb-3">

                            <small class="text-muted d-block">
                                User ID
                            </small>

                            <strong>
                                #{{ $user->id }}
                            </strong>

                        </div>


                        <div class="mb-3">

                            <small class="text-muted d-block">
                                Username
                            </small>

                            <strong>
                                {{ $user->username ?: 'N/A' }}
                            </strong>

                        </div>


                        <div class="mb-3">

                            <small class="text-muted d-block">
                                Current Role
                            </small>

                            <strong>
                                {{ optional($user->role)->role_name
                                    ?? ucfirst(str_replace('_', ' ', $currentRoleSlug)) }}
                            </strong>

                        </div>


                        <div>

                            <small class="text-muted d-block">
                                User Code
                            </small>

                            <span class="badge bg-primary fs-6">
                                {{ $currentCode ?: 'নেই' }}
                            </span>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     ACTIONS
                ================================================== --}}
                <div class="card border-0 shadow-sm">

                    <div class="card-body">

                        <button type="submit"
                                class="btn btn-primary w-100 mb-2">

                            <i class="bi bi-check-circle me-1"></i>
                            Update User

                        </button>


                        <a href="{{ route('dashboard.user.index') }}"
                           class="btn btn-outline-secondary w-100">

                            <i class="bi bi-x-circle me-1"></i>
                            Cancel

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>


{{-- =============================================================
     STYLES
============================================================= --}}
<style>

    .code-mode-option {
        cursor: pointer;
        transition: all .2s ease;
    }

    .code-mode-option:hover {
        border-color: #0d6efd !important;
        background-color: #f8fbff;
    }

    .code-mode-option.active {
        border-color: #0d6efd !important;
        background-color: #f0f7ff;
    }

</style>


{{-- =============================================================
     JAVASCRIPT
============================================================= --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const autoRadio = document.getElementById('code_auto');
    const manualRadio = document.getElementById('code_manual');

    const hiddenType =
        document.getElementById('code_generation_type');

    const customId =
        document.getElementById('custom_id');

    const userType =
        document.getElementById('user_type');

    const autoBox =
        document.getElementById('autoModeBox');

    const manualBox =
        document.getElementById('manualModeBox');

    const autoExample =
        document.getElementById('autoCodeExample');

    const codeStartMap = {
        student: 101,
        user: 201,
        user_admin: 301,
        support_admin: 401
    };


    /*
    |--------------------------------------------------------------------------
    | Update Role Example
    |--------------------------------------------------------------------------
    */

    function updateRoleExample() {

        const role =
            userType ? userType.value : '';

        if (role === 'student') {

            autoExample.textContent =
                'Student: 101, 102, 103...';

        } else if (role === 'user') {

            autoExample.textContent =
                'User: 201, 202, 203...';

        } else if (role === 'user_admin') {

            autoExample.textContent =
                'User Admin: 301, 302, 303...';

        } else if (role === 'support_admin') {

            autoExample.textContent =
                'Support Admin: 401, 402, 403...';

        } else {

            autoExample.textContent =
                'Role অনুযায়ী Code automatically তৈরি হবে।';

        }
    }


    /*
    |--------------------------------------------------------------------------
    | Sync Code Mode
    |--------------------------------------------------------------------------
    */

    function syncCodeMode() {

        if (!autoRadio || !manualRadio) {
            return;
        }


        if (manualRadio.checked) {

            hiddenType.value = 'manual';

            customId.readOnly = false;

            customId.placeholder =
                'যেমন: 1001';

            autoBox.classList.remove('active');

            manualBox.classList.add('active');

        } else {

            hiddenType.value = 'auto';

            customId.readOnly = true;

            customId.placeholder =
                'Auto mode';

            autoBox.classList.add('active');

            manualBox.classList.remove('active');

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Radio Change
    |--------------------------------------------------------------------------
    */

    if (autoRadio) {

        autoRadio.addEventListener(
            'change',
            function () {

                syncCodeMode();

            }
        );

    }


    if (manualRadio) {

        manualRadio.addEventListener(
            'change',
            function () {

                syncCodeMode();

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Click Card To Select Mode
    |--------------------------------------------------------------------------
    */

    if (autoBox) {

        autoBox.addEventListener(
            'click',
            function (event) {

                if (
                    event.target.tagName !== 'INPUT'
                    &&
                    event.target.tagName !== 'LABEL'
                ) {
                    autoRadio.checked = true;
                    syncCodeMode();
                }

            }
        );

    }


    if (manualBox) {

        manualBox.addEventListener(
            'click',
            function (event) {

                if (
                    event.target.tagName !== 'INPUT'
                    &&
                    event.target.tagName !== 'LABEL'
                ) {
                    manualRadio.checked = true;
                    syncCodeMode();
                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Role Change
    |--------------------------------------------------------------------------
    |
    | Important:
    | Gender এখানে কোনোভাবেই Code generation-এ ব্যবহার করা হচ্ছে না।
    |
    */

    if (userType) {

        userType.addEventListener(
            'change',
            function () {

                updateRoleExample();

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Manual Code Numeric Only
    |--------------------------------------------------------------------------
    */

    if (customId) {

        customId.addEventListener(
            'input',
            function () {

                this.value =
                    this.value.replace(
                        /[^0-9]/g,
                        ''
                    );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Initial State
    |--------------------------------------------------------------------------
    */

    updateRoleExample();

    syncCodeMode();

});

</script>

@endsection