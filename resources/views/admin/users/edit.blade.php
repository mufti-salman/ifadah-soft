@extends('layouts.admin')

@section('page')

@php
    /*
    |--------------------------------------------------------------------------
    | Current User Information
    |--------------------------------------------------------------------------
    */

    $currentRoleSlug = optional($user->role)->slug ?? '';

    $currentUserType = $user->userType ?? null;

    $currentUserTypeId = old(
        'user_type_id',
        $currentUserType->id
            ?? ($currentRoleSlug === 'student'
                ? optional($userTypes->firstWhere('slug', 'student'))->id
                : '')
    );

    $selectedUserType = $userTypes->firstWhere(
        'id',
        $currentUserTypeId
    );

    $selectedUserTypeSlug = old(
        'user_type_slug',
        optional($selectedUserType)->slug
    );

    $selectedUserTypeLabel = $selectedUserType
        ? (
            strtolower(trim((string) $selectedUserType->slug)) === 'teacher'
                ? 'শিক্ষক/স্টাফ'
                : $selectedUserType->display_name
        )
        : 'নির্ধারিত নয়';

    $currentGenerationType = old(
        'code_generation_type',
        $user->code_generation_type ?? 'auto'
    );

    $currentCode = old(
        'custom_id',
        $user->institution_user_id ?? $user->custom_id ?? ''
    );

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
@endphp


<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="bi bi-person-gear me-2"></i>
                ইউজার তথ্য সম্পাদনা
            </h4>

            <p class="text-muted mb-0">
                ইউজারের তথ্য, User Type এবং User Code পরিবর্তন করুন।
            </p>
        </div>

        <a href="{{ route('dashboard.user.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            ফিরে যান
        </a>
    </div>


    {{-- SUCCESS / ERROR --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-1"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

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


    {{-- MAIN FORM --}}
    <form method="POST"
          id="editUserForm"
          action="{{ route('dashboard.user.update', $user->username) }}"
          enctype="multipart/form-data">

        @csrf

        <input type="hidden" name="id" value="{{ $user->id }}">

        {{-- Current Role Preserve --}}
        <input type="hidden"
               name="role_id"
               value="{{ old('role_id', $user->role_id) }}">

        <input type="hidden"
               name="code_generation_type"
               id="code_generation_type"
               value="{{ $currentGenerationType }}">


        <div class="row g-4">

            {{-- LEFT COLUMN --}}
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
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            {{-- Name English --}}
                            <div class="col-md-6">
                                <label class="form-label">নাম (English)</label>

                                <input type="text"
                                       name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}"
                                       placeholder="Enter name in English">

                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            {{-- Email --}}
                            <div class="col-md-6">
                                <label class="form-label">Email</label>

                                <input type="email"
                                       name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}"
                                       placeholder="example@email.com">

                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            {{-- Username --}}
                            <div class="col-md-6">
                                <label class="form-label">Username</label>

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

                                    <option value="">নির্বাচন করুন</option>

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
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            {{-- Password --}}
                            <div class="col-md-6">
                                <label class="form-label">নতুন Password</label>

                                <input type="password"
                                       name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="পরিবর্তন করতে চাইলে লিখুন">

                                <small class="text-muted">
                                    খালি রাখলে আগের password অপরিবর্তিত থাকবে।
                                </small>

                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            {{-- Blood Group --}}
                            <div class="col-md-6">
                                <label class="form-label">Blood Group</label>

                                <input type="text"
                                       name="blood_group"
                                       class="form-control"
                                       value="{{ old('blood_group', $user->blood_group) }}"
                                       placeholder="যেমন: A+">
                            </div>


                            {{-- Date of Birth --}}
                            <div class="col-md-6">
                                <label class="form-label">জন্ম তারিখ</label>

                                <input type="date"
                                       name="date_of_birth"
                                       class="form-control"
                                       value="{{ old('date_of_birth', $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') : '') }}">
                            </div>


                            {{-- Religion --}}
                            <div class="col-md-6">
                                <label class="form-label">ধর্ম</label>

                                <input type="text"
                                       name="religion"
                                       class="form-control"
                                       value="{{ old('religion', $user->religion) }}"
                                       placeholder="ধর্ম">
                            </div>


                            {{-- NID --}}
                            <div class="col-md-6">
                                <label class="form-label">NID</label>

                                <input type="text"
                                       name="nid"
                                       class="form-control"
                                       value="{{ old('nid', $user->nid) }}"
                                       placeholder="NID Number">
                            </div>


                            {{-- Birth Certificate --}}
                            <div class="col-md-6">
                                <label class="form-label">জন্ম নিবন্ধন</label>

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
                     USER TYPE & USER CODE
                ================================================== --}}
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-person-badge me-2"></i>
                            User Information & Code Setting
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">

                            {{-- USER TYPE --}}
                            <div class="col-md-6">
                                <label class="form-label">
                                    ব্যবহারকারীর ধরণ (User Type)
                                    <span class="text-danger">*</span>
                                </label>

                                <select name="user_type_id"
                                        id="user_type_id"
                                        class="form-select @error('user_type_id') is-invalid @enderror"
                                        required>

                                    <option value="">User Type নির্বাচন করুন</option>

                                    @foreach($userTypes as $type)
                                        @php
                                            $typeSlug = strtolower(trim((string) $type->slug));

                                            $typeLabel = $typeSlug === 'teacher'
                                                ? 'শিক্ষক/স্টাফ'
                                                : $type->display_name;
                                        @endphp

                                        <option value="{{ $type->id }}"
                                                data-slug="{{ $type->slug }}"
                                                data-label="{{ $typeLabel }}"
                                            {{ (string) $currentUserTypeId === (string) $type->id ? 'selected' : '' }}>

                                            {{ $typeLabel }}

                                        </option>
                                    @endforeach

                                </select>

                                @error('user_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <small class="text-muted">
                                    ব্যক্তি কোন ধরনের ব্যবহারকারী তা নির্বাচন করুন।
                                </small>
                            </div>


                            {{-- CURRENT CODE --}}
                            <div class="col-md-6">
                                <label class="form-label">বর্তমান User Code</label>

                                <input type="text"
                                       id="current_code_display"
                                       class="form-control bg-light"
                                       value="{{ $currentCode ?: 'নেই' }}"
                                       readonly>
                            </div>


                            {{-- CODE GENERATION --}}
                            <div class="col-12">
                                <label class="form-label">
                                    User Code Generation
                                </label>

                                <div class="row g-3">

                                    {{-- AUTO --}}
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
                                                User Type অনুযায়ী Code automatically তৈরি হবে।

                                                <br>

                                                <span id="autoCodeExample">
                                                    Code automatically generated.
                                                </span>
                                            </small>
                                        </div>
                                    </div>


                                    {{-- MANUAL --}}
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
                                                Code নিজে নির্ধারণ করতে পারবেন।
                                            </small>
                                        </div>
                                    </div>

                                </div>
                            </div>


                            {{-- CUSTOM ID --}}
                            <div class="col-md-6">
                                <label class="form-label">Manual User Code</label>

                                <input type="text"
                                       name="custom_id"
                                       id="custom_id"
                                       class="form-control @error('custom_id') is-invalid @enderror"
                                       value="{{ $currentCode }}"
                                       placeholder="যেমন: 500"
                                       autocomplete="off">

                                <small id="manualHelp" class="text-muted">
                                    Manual mode নির্বাচন করলে Code পরিবর্তন করতে পারবেন।
                                    Auto mode-এ এই ফিল্ড পরিবর্তন করা যাবে না।
                                </small>

                                @error('custom_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            {{-- CODE INFORMATION --}}
                            <div class="col-md-6">
                                <div class="alert alert-light border mb-0">

                                    <div class="fw-semibold mb-2">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Code System
                                    </div>

                                    <ul class="mb-0 ps-3 small">
                                        <li>প্রতিটি User Type-এর Code Setting আলাদা।</li>
                                        <li>Auto mode-এ ধারাবাহিক Code তৈরি হবে।</li>
                                        <li>Manual mode-এ নির্দিষ্ট Code দেওয়া যাবে।</li>
                                        <li>আগে ব্যবহৃত Code পুনরায় ব্যবহার করা যাবে না।</li>
                                    </ul>

                                </div>
                            </div>


                            {{-- INSTITUTION --}}
                            @if(Auth::user()->is_super_admin)

                                <div class="col-md-6">
                                    <label class="form-label">প্রতিষ্ঠান</label>

                                    <select name="institution_id"
                                            id="institution_id"
                                            class="form-select @error('institution_id') is-invalid @enderror">

                                        <option value="">প্রতিষ্ঠান নির্বাচন করুন</option>

                                        @foreach($institutions as $institution)
                                            <option value="{{ $institution->id }}"
                                                {{ (string) $selectedInstitution === (string) $institution->id ? 'selected' : '' }}>

                                                {{ $institution->name_bn
                                                    ?? $institution->name
                                                    ?? $institution->title
                                                    ?? 'Institution #' . $institution->id }}

                                            </option>
                                        @endforeach

                                    </select>

                                    @error('institution_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
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
                                <label class="form-label">ফোনের মালিক</label>

                                <input type="text"
                                       name="phone_relation"
                                       class="form-control"
                                       value="{{ old('phone_relation', $user->phone_owner) }}"
                                       placeholder="নিজের / বাবার / মায়ের">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">বিকল্প ফোন</label>

                                <input type="text"
                                       name="phone2"
                                       class="form-control"
                                       value="{{ old('phone2', $user->phone2) }}"
                                       placeholder="বিকল্প ফোন নম্বর">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">পিতার নাম</label>

                                <input type="text"
                                       name="father_name"
                                       class="form-control"
                                       value="{{ old('father_name', $user->father_name) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">পিতার ফোন</label>

                                <input type="text"
                                       name="father_phone"
                                       class="form-control"
                                       value="{{ old('father_phone', $user->father_phone) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">মাতার নাম</label>

                                <input type="text"
                                       name="mother_name"
                                       class="form-control"
                                       value="{{ old('mother_name', $user->mother_name) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">মাতার ফোন</label>

                                <input type="text"
                                       name="mother_phone"
                                       class="form-control"
                                       value="{{ old('mother_phone', $user->mother_phone) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Guardian Name</label>

                                <input type="text"
                                       name="guardian_name"
                                       class="form-control"
                                       value="{{ old('guardian_name', $user->guardian_name) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Guardian Phone</label>

                                <input type="text"
                                       name="guardian_phone"
                                       class="form-control"
                                       value="{{ old('guardian_phone', $user->guardian_phone) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Guardian Relation</label>

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

                        <h6 class="mb-3">বর্তমান ঠিকানা</h6>

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Division</label>

                                <select name="present_division_id"
                                        class="form-select">

                                    <option value="">নির্বাচন করুন</option>

                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}"
                                            {{ (string) old('present_division_id', $user->present_division_id) === (string) $division->id ? 'selected' : '' }}>
                                            {{ $division->name_bn ?? $division->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>


                            <div class="col-md-6">
                                <label class="form-label">District</label>

                                <select name="present_district_id"
                                        class="form-select">

                                    <option value="">নির্বাচন করুন</option>

                                    @foreach($divisions as $division)
                                        @foreach($division->districts ?? [] as $district)
                                            <option value="{{ $district->id }}"
                                                {{ (string) old('present_district_id', $user->present_district_id) === (string) $district->id ? 'selected' : '' }}>
                                                {{ $district->name_bn ?? $district->name }}
                                            </option>
                                        @endforeach
                                    @endforeach

                                </select>
                            </div>


                            <div class="col-md-6">
                                <label class="form-label">Upazila</label>

                                <select name="present_upazila_id"
                                        class="form-select">

                                    <option value="">নির্বাচন করুন</option>

                                    @foreach($divisions as $division)
                                        @foreach($division->districts ?? [] as $district)
                                            @foreach($district->upazilas ?? [] as $upazila)
                                                <option value="{{ $upazila->id }}"
                                                    {{ (string) old('present_upazila_id', $user->present_upazila_id) === (string) $upazila->id ? 'selected' : '' }}>
                                                    {{ $upazila->name_bn ?? $upazila->name }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    @endforeach

                                </select>
                            </div>


                            <div class="col-md-6">
                                <label class="form-label">Union</label>

                                <input type="text"
                                       name="present_union"
                                       class="form-control"
                                       value="{{ old('present_union', $user->present_union) }}">
                            </div>


                            <div class="col-md-6">
                                <label class="form-label">Post Office</label>

                                <input type="text"
                                       name="present_post_office"
                                       class="form-control"
                                       value="{{ old('present_post_office', $user->present_post_office) }}">
                            </div>


                            <div class="col-md-6">
                                <label class="form-label">Postal Code</label>

                                <input type="text"
                                       name="present_postal_code"
                                       class="form-control"
                                       value="{{ old('present_postal_code', $user->present_postal_code) }}">
                            </div>


                            <div class="col-12">
                                <label class="form-label">Village / Road</label>

                                <input type="text"
                                       name="present_village_road"
                                       class="form-control"
                                       value="{{ old('present_village_road', $user->present_village_road) }}">
                            </div>

                        </div>


                        <hr class="my-4">

                        <h6 class="mb-3">স্থায়ী ঠিকানা</h6>

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Division</label>

                                <select name="permanent_division_id"
                                        class="form-select">

                                    <option value="">নির্বাচন করুন</option>

                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}"
                                            {{ (string) old('permanent_division_id', $user->permanent_division_id) === (string) $division->id ? 'selected' : '' }}>
                                            {{ $division->name_bn ?? $division->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>


                            <div class="col-md-6">
                                <label class="form-label">District</label>

                                <select name="permanent_district_id"
                                        class="form-select">

                                    <option value="">নির্বাচন করুন</option>

                                    @foreach($divisions as $division)
                                        @foreach($division->districts ?? [] as $district)
                                            <option value="{{ $district->id }}"
                                                {{ (string) old('permanent_district_id', $user->permanent_district_id) === (string) $district->id ? 'selected' : '' }}>
                                                {{ $district->name_bn ?? $district->name }}
                                            </option>
                                        @endforeach
                                    @endforeach

                                </select>
                            </div>


                            <div class="col-md-6">
                                <label class="form-label">Upazila</label>

                                <select name="permanent_upazila_id"
                                        class="form-select">

                                    <option value="">নির্বাচন করুন</option>

                                    @foreach($divisions as $division)
                                        @foreach($division->districts ?? [] as $district)
                                            @foreach($district->upazilas ?? [] as $upazila)
                                                <option value="{{ $upazila->id }}"
                                                    {{ (string) old('permanent_upazila_id', $user->permanent_upazila_id) === (string) $upazila->id ? 'selected' : '' }}>
                                                    {{ $upazila->name_bn ?? $upazila->name }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    @endforeach

                                </select>
                            </div>


                            <div class="col-md-6">
                                <label class="form-label">Union</label>

                                <input type="text"
                                       name="permanent_union"
                                       class="form-control"
                                       value="{{ old('permanent_union', $user->permanent_union) }}">
                            </div>


                            <div class="col-md-6">
                                <label class="form-label">Post Office</label>

                                <input type="text"
                                       name="permanent_post_office"
                                       class="form-control"
                                       value="{{ old('permanent_post_office', $user->permanent_post_office) }}">
                            </div>


                            <div class="col-md-6">
                                <label class="form-label">Postal Code</label>

                                <input type="text"
                                       name="permanent_postal_code"
                                       class="form-control"
                                       value="{{ old('permanent_postal_code', $user->permanent_postal_code) }}">
                            </div>


                            <div class="col-12">
                                <label class="form-label">Village / Road</label>

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
                <div class="card border-0 shadow-sm mb-4"
                     id="studentInformationCard"
                     style="{{ $selectedUserTypeSlug === 'student' ? '' : 'display:none;' }}">

                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-mortarboard me-2"></i>
                            শিক্ষার্থী তথ্য
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Academic Year</label>

                                <select name="academic_year_id"
                                        class="form-select">

                                    <option value="">নির্বাচন করুন</option>

                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}"
                                            {{ (string) old('academic_year_id', optional($user->student)->academic_year_id) === (string) $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>


                            <div class="col-md-6">
                                <label class="form-label">Class</label>

                                <select name="class_id"
                                        class="form-select">

                                    <option value="">নির্বাচন করুন</option>

                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}"
                                            {{ (string) old('class_id', optional($user->student)->class_id) === (string) $class->id ? 'selected' : '' }}>
                                            {{ $class->name_bn ?? $class->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>


                            <div class="col-md-6">
                                <label class="form-label">Section</label>

                                <select name="section_id"
                                        class="form-select">

                                    <option value="">নির্বাচন করুন</option>

                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ (string) old('section_id', optional($user->student)->section_id) === (string) $section->id ? 'selected' : '' }}>
                                            {{ $section->name_bn ?? $section->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>


                            <div class="col-md-6">
                                <label class="form-label">Guardian User ID</label>

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

                                    <label class="form-check-label" for="is_hostel">
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

                                    <label class="form-check-label" for="is_transport">
                                        Transport
                                    </label>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>


            {{-- =================================================
                 RIGHT COLUMN
            ================================================== --}}
            <div class="col-lg-4">


                {{-- PROFILE PHOTO --}}
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


                {{-- STATUS --}}
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-toggle-on me-2"></i>
                            Account Status
                        </h5>
                    </div>

                    <div class="card-body">

                        <select name="status" class="form-select">

                            <option value="1"
                                {{ (string) $selectedStatus === '1' ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="0"
                                {{ (string) $selectedStatus === '0' ? 'selected' : '' }}>
                                Inactive
                            </option>

                        </select>

                    </div>
                </div>


                {{-- USER SUMMARY --}}
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            User Summary
                        </h5>
                    </div>

                    <div class="card-body">

                        <div class="mb-3">
                            <small class="text-muted d-block">User ID</small>
                            <strong>#{{ $user->id }}</strong>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">Username</small>
                            <strong>{{ $user->username ?: 'N/A' }}</strong>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">User Type</small>

                            <strong id="summaryUserType">
                                {{ $selectedUserTypeLabel }}
                            </strong>
                        </div>

                        <div>
                            <small class="text-muted d-block">User Code</small>

                            <span class="badge bg-primary fs-6">
                                {{ $currentCode ?: 'নেই' }}
                            </span>
                        </div>

                    </div>
                </div>


                {{-- ACTIONS --}}
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

    'use strict';

    const autoRadio = document.getElementById('code_auto');
    const manualRadio = document.getElementById('code_manual');

    const hiddenType = document.getElementById('code_generation_type');
    const customId = document.getElementById('custom_id');

    const userTypeSelect = document.getElementById('user_type_id');

    const autoBox = document.getElementById('autoModeBox');
    const manualBox = document.getElementById('manualModeBox');

    const autoExample = document.getElementById('autoCodeExample');
    const studentCard = document.getElementById('studentInformationCard');

    const summaryUserType = document.getElementById('summaryUserType');

    const form = document.getElementById('editUserForm');


    /*
    |--------------------------------------------------------------------------
    | USER TYPE CODE START MAP
    |--------------------------------------------------------------------------
    */

    const codeStartMap = {
        student: 101,
        teacher: 201,
        donor_member: 301,

        // Legacy User Types
        staff: 401,
        guardian: 501,
        library_member: 601,
        software_user: 701,
        other: 801
    };


    /*
    |--------------------------------------------------------------------------
    | GET SELECTED USER TYPE
    |--------------------------------------------------------------------------
    */

    function getSelectedUserType() {

        if (!userTypeSelect) {
            return null;
        }

        const option = userTypeSelect.options[
            userTypeSelect.selectedIndex
        ];

        if (!option || !option.value) {
            return null;
        }

        return {
            id: option.value,
            slug: option.dataset.slug || '',
            label: option.dataset.label || option.text.trim()
        };
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE USER TYPE UI
    |--------------------------------------------------------------------------
    */

    function updateUserTypeUI() {

        const type = getSelectedUserType();

        if (!type) {

            if (studentCard) {
                studentCard.style.display = 'none';
            }

            if (summaryUserType) {
                summaryUserType.textContent = 'নির্ধারিত নয়';
            }

            if (autoExample) {
                autoExample.textContent =
                    'User Type অনুযায়ী Code automatically তৈরি হবে।';
            }

            return;
        }


        // Student Information Visibility
        if (studentCard) {

            studentCard.style.display =
                type.slug === 'student' ? '' : 'none';

        }


        // Update User Summary
        if (summaryUserType) {

            summaryUserType.textContent = type.label;

        }


        // Update Auto Code Example
        const startCode = codeStartMap[type.slug];

        if (startCode && autoExample) {

            autoExample.textContent =
                type.label + ': ' +
                startCode + ', ' +
                (startCode + 1) + ', ' +
                (startCode + 2) + '...';

        } else if (autoExample) {

            autoExample.textContent =
                type.label + ' অনুযায়ী Code automatically তৈরি হবে।';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | SYNC CODE GENERATION MODE
    |--------------------------------------------------------------------------
    */

    function syncCodeMode() {

        if (!autoRadio || !manualRadio || !hiddenType || !customId) {
            return;
        }

        const isManual = manualRadio.checked;

        hiddenType.value = isManual ? 'manual' : 'auto';

        customId.readOnly = !isManual;

        customId.placeholder = isManual
            ? 'যেমন: 500'
            : 'Auto mode';


        // Active Card
        if (autoBox) {

            autoBox.classList.toggle(
                'active',
                !isManual
            );

        }

        if (manualBox) {

            manualBox.classList.toggle(
                'active',
                isManual
            );

        }


        // Help Text
        const manualHelp = document.getElementById('manualHelp');

        if (manualHelp) {

            manualHelp.textContent = isManual
                ? 'Manual mode-এ User Code পরিবর্তন করতে পারবেন।'
                : 'Auto mode-এ User Code system থেকে নির্ধারিত হবে।';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | RADIO EVENTS
    |--------------------------------------------------------------------------
    */

    if (autoRadio) {

        autoRadio.addEventListener('change', syncCodeMode);

    }

    if (manualRadio) {

        manualRadio.addEventListener('change', syncCodeMode);

    }


    /*
    |--------------------------------------------------------------------------
    | CLICK CODE MODE CARDS
    |--------------------------------------------------------------------------
    */

    if (autoBox && autoRadio) {

        autoBox.addEventListener('click', function (event) {

            if (
                event.target.tagName !== 'INPUT' &&
                event.target.tagName !== 'LABEL'
            ) {

                autoRadio.checked = true;

                syncCodeMode();

            }

        });

    }


    if (manualBox && manualRadio) {

        manualBox.addEventListener('click', function (event) {

            if (
                event.target.tagName !== 'INPUT' &&
                event.target.tagName !== 'LABEL'
            ) {

                manualRadio.checked = true;

                syncCodeMode();

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | USER TYPE CHANGE
    |--------------------------------------------------------------------------
    */

    if (userTypeSelect) {

        userTypeSelect.addEventListener('change', function () {

            updateUserTypeUI();

        });

    }


    /*
    |--------------------------------------------------------------------------
    | MANUAL CODE NUMERIC ONLY
    |--------------------------------------------------------------------------
    */

    if (customId) {

        customId.addEventListener('input', function () {

            this.value = this.value.replace(/[^0-9]/g, '');

        });

    }


    /*
    |--------------------------------------------------------------------------
    | FORM VALIDATION
    |--------------------------------------------------------------------------
    */

    if (form) {

        form.addEventListener('submit', function (event) {

            const type = getSelectedUserType();

            if (!type) {

                event.preventDefault();

                alert('অনুগ্রহ করে User Type নির্বাচন করুন।');

                userTypeSelect.focus();

                return;

            }

            syncCodeMode();

        });

    }


    /*
    |--------------------------------------------------------------------------
    | INITIAL STATE
    |--------------------------------------------------------------------------
    */

    updateUserTypeUI();

    syncCodeMode();

});
</script>

@endsection