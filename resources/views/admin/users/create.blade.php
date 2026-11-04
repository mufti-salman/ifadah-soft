@extends('layouts.admin')

@section('title', 'নতুন ইউজার তৈরি')

@section('page')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap');

    .reg-page * {
        font-family: 'Hind Siliguri', sans-serif;
        box-sizing: border-box;
    }

    .reg-page {
        background: #f0f4ff;
        min-height: 100vh;
        padding: 24px;
    }

    .reg-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 16px rgba(30,58,138,.08);
        overflow: hidden;
    }

    .reg-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 28px;
        border-bottom: 1.5px solid #eef2fb;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-icon {
        width: 38px;
        height: 38px;
        background: #eef2fb;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3b5bdb;
    }

    .reg-card-header h5 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: #1a1a2e;
    }

    .btn-code-setting {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #eef2ff;
        border: 1.5px solid #c7d2fe;
        color: #3730a3;
        border-radius: 10px;
        padding: 9px 16px;
        font-size: .85rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-code-setting:hover {
        background: #e0e7ff;
        border-color: #818cf8;
    }

    .reg-card-body {
        padding: 28px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 320px;
        gap: 24px;
        align-items: start;
    }

    .form-label {
        display: block;
        font-size: .82rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    .form-label .req {
        color: #ef4444;
        margin-left: 2px;
    }

    .form-control,
    .form-select {
        width: 100%;
        border: 1.5px solid #e5e9f5;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: .88rem;
        color: #374151;
        background: #f8faff;
        outline: none;
        transition: .2s;
    }

    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #6b8cde;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(107,140,222,.12);
    }

    .form-control::placeholder {
        color: #b0bac9;
    }

    .mini-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .gender-row {
        display: flex;
        align-items: center;
        gap: 18px;
        padding-top: 8px;
        min-height: 42px;
    }

    .radio-label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: .88rem;
        color: #374151;
        cursor: pointer;
    }

    .radio-label input {
        accent-color: #3b5bdb;
        width: 16px;
        height: 16px;
    }

    .col-fields,
    .address-col,
    .addr-fields {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .phone-group {
        display: flex;
        gap: 8px;
    }

    .phone-group .form-control {
        flex: 1;
    }

    .phone-group .form-select {
        width: 110px;
        flex-shrink: 0;
    }

    .address-col {
        gap: 16px;
    }

    .address-card {
        background: #f8faff;
        border: 1.5px solid #e5e9f5;
        border-radius: 14px;
        padding: 18px;
    }

    .address-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
    }

    .address-card-title {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: .9rem;
        font-weight: 700;
        color: #1e3a8a;
    }

    .same-check-label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: .78rem;
        color: #6b7280;
        cursor: pointer;
        background: #eef2fb;
        padding: 4px 10px;
        border-radius: 20px;
        white-space: nowrap;
    }

    .addr-mini-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .reg-card-footer {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        padding: 18px 28px;
        border-top: 1.5px solid #eef2fb;
        background: #fafbff;
        margin-top: 24px;
    }

    .btn-cancel,
    .btn-save {
        border-radius: 10px;
        padding: 10px 24px;
        font-size: .9rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-cancel {
        background: transparent;
        border: 1.5px solid #e5e9f5;
        color: #6b7280;
    }

    .btn-save {
        background: #1e3a8a;
        border: 0;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-save:hover {
        background: #1e40af;
        color: #fff;
    }

    .btn-save:disabled {
        opacity: .6;
        cursor: not-allowed;
    }

    .alert-danger-custom {
        background: #fef2f2;
        border: 1.5px solid #fca5a5;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 20px;
        font-size: .88rem;
        color: #b91c1c;
    }

    .manual-code-field {
        display: none;
        margin-top: 10px;
    }

    .manual-code-field.show {
        display: block;
    }

    .code-mode-hint {
        display: block;
        margin-top: 5px;
        color: #6b7280;
        font-size: .70rem;
    }

    .code-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,.55);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        z-index: 99999;
        backdrop-filter: blur(3px);
    }

    .code-modal-overlay.show {
        display: flex;
    }

    .code-modal {
        width: 100%;
        max-width: 650px;
        max-height: 90vh;
        overflow-y: auto;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 20px 60px rgba(0,0,0,.2);
    }

    .code-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 22px;
        border-bottom: 1px solid #eef2fb;
    }

    .code-modal-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .code-modal-title-icon {
        width: 38px;
        height: 38px;
        background: #eef2ff;
        color: #4338ca;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .code-modal-title h5 {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: #1f2937;
    }

    .code-modal-close {
        width: 34px;
        height: 34px;
        border: 0;
        background: #f3f4f6;
        border-radius: 8px;
        color: #6b7280;
        cursor: pointer;
    }

    .code-modal-body {
        padding: 22px;
    }

    .code-form-section {
        margin-bottom: 20px;
    }

    .code-form-section:last-child {
        margin-bottom: 0;
    }

    .code-section-title {
        font-size: .85rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: 9px;
    }

    .code-radio-group {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .code-radio-card {
        flex: 1;
        min-width: 130px;
        position: relative;
    }

    .code-radio-card input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .code-radio-card label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 11px 13px;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        background: #f9fafb;
        color: #4b5563;
        font-size: .84rem;
        font-weight: 600;
        cursor: pointer;
        text-align: center;
    }

    .code-radio-card input:checked + label {
        border-color: #6366f1;
        background: #eef2ff;
        color: #3730a3;
    }

    .code-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 22px;
        border-top: 1px solid #eef2fb;
        background: #fafbff;
    }

    .btn-modal-cancel,
    .btn-modal-save {
        border-radius: 9px;
        padding: 9px 20px;
        font-size: .85rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-modal-cancel {
        border: 1.5px solid #e5e7eb;
        background: #fff;
        color: #6b7280;
    }

    .btn-modal-save {
        border: 0;
        background: #3730a3;
        color: #fff;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .entry-info,
    .manual-preview-info {
        border-radius: 10px;
        padding: 11px 13px;
        font-size: .78rem;
        margin-top: 10px;
    }

    .entry-info {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        color: #6b7280;
    }

    .manual-preview-info {
        display: none;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #047857;
    }

    .manual-preview-info.show {
        display: block;
    }

    @media(max-width:992px) {
        .form-grid {
            grid-template-columns: 1fr 1fr;
        }

        .address-col {
            grid-column: 1/-1;
            flex-direction: row;
            flex-wrap: wrap;
        }

        .address-card {
            flex: 1;
            min-width: 260px;
        }
    }

    @media(max-width:640px) {
        .reg-page {
            padding: 10px;
        }

        .reg-card-header {
            padding: 15px;
            align-items: flex-start;
        }

        .reg-card-body {
            padding: 16px;
        }

        .form-grid,
        .mini-grid-2,
        .addr-mini-grid {
            grid-template-columns: 1fr;
        }

        .address-col {
            grid-column: auto;
            flex-direction: column;
        }

        .reg-card-footer {
            padding: 15px;
        }

        .code-radio-card {
            min-width: 100%;
        }
    }
</style>

<div class="reg-page">
    <div class="reg-card">

        <div class="reg-card-header">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h5>ইউজার রেজিস্ট্রেশন ফরম</h5>
            </div>

            <button type="button" class="btn-code-setting" id="openCodeSetting">
                <i class="fas fa-sliders-h"></i>
                কোড সেটিং
            </button>
        </div>

        <div class="reg-card-body">

            @if ($errors->any())
                <div class="alert-danger-custom">
                    <ul style="margin:0;padding-left:18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-danger-custom">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('dashboard.user.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  id="userCreateForm">

                @csrf

                <input type="hidden"
                       name="institution_id"
                       id="institution_id"
                       value="{{ auth()->user()->institution_id ?? auth()->user()->madrasa_id ?? '' }}">

                <input type="hidden"
                       name="code_generation_type"
                       id="code_generation_type"
                       value="{{ old('code_generation_type', 'auto') }}">

                <div class="form-grid">

                    {{-- FIRST COLUMN --}}
                    <div class="col-fields">

                        <div class="field-group">
                            <label class="form-label">
                                ব্যবহারকারীর ধরণ
                                <span class="req">*</span>
                            </label>

                       <select name="user_type_id" id="user_type_id" class="form-select" required>
                        <option value="">নির্বাচন করুন</option>

                        @foreach($userTypes as $type)
                            <option
                                value="{{ $type->id }}"
                                data-slug="{{ $type->slug }}"
                                {{ (string)old('user_type_id') === (string)$type->id ? 'selected' : '' }}
                            >
                                {{ $type->slug === 'teacher'
                                    ? 'শিক্ষক/স্টাফ'
                                    : $type->name_bn }}
                            </option>
                        @endforeach
                    </select>
                        </div>

                        <div class="mini-grid-2">

                            <div class="field-group">

                                <label class="form-label">
                                    আইডি
                                    <span class="req">*</span>
                                </label>

                                <input type="text"
                                       name="custom_id"
                                       id="custom_id"
                                       class="form-control"
                                       value="{{ old('custom_id') }}"
                                       placeholder="স্বয়ংক্রিয়ভাবে জেনারেট হবে"
                                       readonly
                                       style="background:#eef2fb;font-weight:600;color:#1e3a8a;">

                                <small id="codeModeHint" class="code-mode-hint">
                                    Auto Code
                                </small>

                                <div id="manualCodeField" class="manual-code-field">

                                    <label for="manual_user_code" class="form-label">
                                        ম্যানুয়াল ইউজার কোড
                                    </label>

                                    <input type="text"
                                           id="manual_user_code"
                                           class="form-control"
                                           inputmode="numeric"
                                           autocomplete="off"
                                           placeholder="খালি রাখলে পরবর্তী Manual Code তৈরি হবে">

                                    <small class="text-muted">
                                        এখানে কোড দিলে এই ইউজারের জন্য সেটিই ব্যবহার হবে।
                                    </small>

                                </div>

                                <div id="manualPreviewInfo" class="manual-preview-info"></div>

                            </div>

                            @php
                                $selectedGender = old(
                                    'gender',
                                    ($genderPreference ?? 'entry') === 'entry'
                                        ? null
                                        : ($genderPreference ?? null)
                                );
                            @endphp

                            <div class="field-group">

                                <label class="form-label">
                                    লিঙ্গ
                                    <span class="req">*</span>
                                </label>

                                <div class="gender-row">

                                    <label class="radio-label">
                                        <input type="radio"
                                               name="gender"
                                               value="male"
                                               {{ $selectedGender === 'male' ? 'checked' : '' }}
                                               required>
                                        পুরুষ
                                    </label>

                                    <label class="radio-label">
                                        <input type="radio"
                                               name="gender"
                                               value="female"
                                               {{ $selectedGender === 'female' ? 'checked' : '' }}
                                               required>
                                        মহিলা
                                    </label>

                                </div>
                            </div>

                        </div>

                        <div class="field-group">
                            <label class="form-label">
                                নাম
                                <span class="req">*</span>
                            </label>

                            <input type="text"
                                   name="name_bn"
                                   class="form-control"
                                   value="{{ old('name_bn') }}"
                                   placeholder="পুরো নাম লিখুন"
                                   required>
                        </div>

                        <div class="field-group">
                            <label class="form-label">পিতার নাম</label>

                            <input type="text"
                                   name="father_name"
                                   class="form-control"
                                   value="{{ old('father_name') }}"
                                   placeholder="পিতার নাম লিখুন">
                        </div>

                        <div class="field-group">
                            <label class="form-label">মাতার নাম</label>

                            <input type="text"
                                   name="mother_name"
                                   class="form-control"
                                   value="{{ old('mother_name') }}"
                                   placeholder="মাতার নাম লিখুন">
                        </div>

                    </div>

                    {{-- SECOND COLUMN --}}
                    <div class="col-fields">

                        <div class="field-group">
                            <label class="form-label">রক্তের গ্রুপ</label>

                            <select name="blood_group" class="form-select">
                                <option value="">নির্বাচন করুন</option>

                                @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
                                    <option value="{{ $bg }}"
                                            {{ old('blood_group') == $bg ? 'selected' : '' }}>
                                        {{ $bg }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mini-grid-2">

                            <div class="field-group">
                                <label class="form-label">জন্ম তারিখ</label>

                                <input type="date"
                                       name="date_of_birth"
                                       id="date_of_birth"
                                       class="form-control"
                                       value="{{ old('date_of_birth') }}">
                            </div>

                            <div class="field-group">
                                <label class="form-label">বয়স</label>

                                <input type="number"
                                       name="age"
                                       id="age"
                                       class="form-control"
                                       value="{{ old('age') }}"
                                       readonly
                                       style="background:#eef2fb;"
                                       placeholder="Auto">
                            </div>

                        </div>

                        <div class="field-group">
                            <label class="form-label">NID/জন্ম নিবন্ধন নং</label>

                            <input type="text"
                                   name="nid"
                                   class="form-control"
                                   value="{{ old('nid') }}"
                                   placeholder="নম্বর লিখুন">
                        </div>

                        <div class="field-group">
                            <label class="form-label">
                                মোবাইল 1
                                <span class="req">*</span>
                            </label>

                            <div class="phone-group">

                                <input type="text"
                                       name="phone"
                                       class="form-control"
                                       value="{{ old('phone') }}"
                                       placeholder="০১XXXXXXXXX"
                                       required>

                                <select name="phone_relation" class="form-select">
                                    <option value="">সম্পর্ক</option>

                                    @foreach([
                                        'self'=>'নিজ',
                                        'father'=>'পিতা',
                                        'mother'=>'মাতা',
                                        'guardian'=>'অভিভাবক'
                                    ] as $key=>$label)

                                        <option value="{{ $key }}"
                                                {{ old('phone_relation') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>

                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="field-group">
                            <label class="form-label">মোবাইল 2</label>

                            <div class="phone-group">

                                <input type="text"
                                       name="phone2"
                                       class="form-control"
                                       value="{{ old('phone2') }}"
                                       placeholder="০১XXXXXXXXX">

                                <select name="phone2_relation" class="form-select">
                                    <option value="">সম্পর্ক</option>

                                    @foreach([
                                        'self'=>'নিজ',
                                        'father'=>'পিতা',
                                        'mother'=>'মাতা',
                                        'guardian'=>'অভিভাবক'
                                    ] as $key=>$label)

                                        <option value="{{ $key }}"
                                                {{ old('phone2_relation') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>

                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="field-group">
                            <label class="form-label">ই-মেইল</label>

                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   value="{{ old('email') }}"
                                   placeholder="example@mail.com">
                        </div>

                    </div>

                    {{-- ADDRESS COLUMN --}}
                    <div class="address-col">

                        {{-- PERMANENT ADDRESS --}}
                        <div class="address-card">

                            <div class="address-card-header">
                                <div class="address-card-title">
                                    <i class="fas fa-map-marker-alt"></i>
                                    স্থায়ী ঠিকানা
                                </div>
                            </div>

                            <div class="addr-fields">

                                <div class="addr-mini-grid">

                                    <select name="permanent_division_id"
                                            id="permanent_division"
                                            class="form-select">

                                        <option value="">বিভাগ</option>

                                        @foreach($divisions as $div)
                                            <option value="{{ $div->id }}"
                                                    {{ old('permanent_division_id') == $div->id ? 'selected' : '' }}>
                                                {{ $div->name_bn }}
                                            </option>
                                        @endforeach

                                    </select>

                                    <select name="permanent_district_id"
                                            id="permanent_district"
                                            class="form-select">

                                        <option value="">জেলা</option>

                                    </select>

                                </div>

                                <div class="addr-mini-grid">

                                    <select name="permanent_upazila_id"
                                            id="permanent_upazila"
                                            class="form-select">

                                        <option value="">থানা</option>

                                    </select>

                                    <input type="text"
                                           name="permanent_post_office"
                                           class="form-control"
                                           value="{{ old('permanent_post_office') }}"
                                           placeholder="ডাকঘর">

                                </div>

                                <input type="text"
                                       name="permanent_village_road"
                                       class="form-control"
                                       value="{{ old('permanent_village_road') }}"
                                       placeholder="গ্রাম/রাস্তা/বাসা নং">

                            </div>
                        </div>

                        {{-- PRESENT ADDRESS --}}
                        <div class="address-card">

                            <div class="address-card-header">

                                <div class="address-card-title">
                                    <i class="fas fa-map-marker-alt"></i>
                                    অস্থায়ী ঠিকানা
                                </div>

                                <label class="same-check-label">
                                    <input type="checkbox" id="same_as_permanent">
                                    স্থায়ী ঠিকানার মতই
                                </label>

                            </div>

                            <div class="addr-fields">

                                <div class="addr-mini-grid">

                                    <select name="present_division_id"
                                            id="present_division"
                                            class="form-select">

                                        <option value="">বিভাগ</option>

                                        @foreach($divisions as $div)
                                            <option value="{{ $div->id }}"
                                                    {{ old('present_division_id') == $div->id ? 'selected' : '' }}>
                                                {{ $div->name_bn }}
                                            </option>
                                        @endforeach

                                    </select>

                                    <select name="present_district_id"
                                            id="present_district"
                                            class="form-select">

                                        <option value="">জেলা</option>

                                    </select>

                                </div>

                                <div class="addr-mini-grid">

                                    <select name="present_upazila_id"
                                            id="present_upazila"
                                            class="form-select">

                                        <option value="">থানা</option>

                                    </select>

                                    <input type="text"
                                           name="present_post_office"
                                           class="form-control"
                                           value="{{ old('present_post_office') }}"
                                           placeholder="ডাকঘর">

                                </div>

                                <input type="text"
                                       name="present_village_road"
                                       class="form-control"
                                       value="{{ old('present_village_road') }}"
                                       placeholder="গ্রাম/রাস্তা/বাসা নং">

                            </div>
                        </div>

                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="reg-card-footer">

                    <a href="{{ route('dashboard.user.index') }}"
                       class="btn-cancel">
                        বাতিল করুন
                    </a>

                    <button type="submit" class="btn-save" id="submitUserBtn">
                        <i class="fas fa-save"></i>
                        সেভ করুন
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

{{-- USER CODE SETTING MODAL --}}
<div class="code-modal-overlay" id="codeSettingModal">

    <div class="code-modal">

        <div class="code-modal-header">

            <div class="code-modal-title">

                <div class="code-modal-title-icon">
                    <i class="fas fa-cog"></i>
                </div>

                <h5>User Code Setting</h5>

            </div>

            <button type="button"
                    class="code-modal-close"
                    id="closeCodeSetting">
                <i class="fas fa-times"></i>
            </button>

        </div>

        <div class="code-modal-body">

            {{-- USER TYPE --}}
            <div class="code-form-section" id="codeUserTypeSection">

                <div class="code-section-title">
                    ব্যবহারকারীর ধরণ:
                </div>

                <select id="code_setting_user_type" class="form-select">

                    <option value="">নির্বাচন করুন</option>

                    @foreach($userTypes as $type)
                        <option
                            value="{{ $type->id }}"
                            data-slug="{{ $type->slug }}"
                            {{ (string)old('user_type_id') === (string)$type->id ? 'selected' : '' }}
                        >
                            {{ $type->slug === 'teacher'
                                ? 'শিক্ষক/স্টাফ'
                                : $type->name_bn }}
                        </option>
                    @endforeach

                </select>

            </div>

            {{-- GENERATION TYPE --}}
            <div class="code-form-section"
                 id="codeGenerationTypeSection"
                 style="display:none;">

                <div class="code-section-title">
                    ধরন:
                </div>

                <div class="code-radio-group">

                    <div class="code-radio-card">

                        <input type="radio"
                               name="modal_code_generation_type"
                               id="code_auto"
                               value="auto">

                        <label for="code_auto">
                            <i class="fas fa-magic"></i>
                            নির্দিষ্ট কোড হতে অটো
                        </label>

                    </div>

                    <div class="code-radio-card">

                        <input type="radio"
                               name="modal_code_generation_type"
                               id="code_manual"
                               value="manual">

                        <label for="code_manual">
                            <i class="fas fa-keyboard"></i>
                            ম্যানুয়াল
                        </label>

                    </div>

                </div>

            </div>

            {{-- CODE INPUT --}}
            <div class="code-form-section"
                 id="codeInputSection"
                 style="display:none;">

                <label for="setting_user_code" class="form-label">
                    ইউজার কোড:
                </label>

                <input type="text"
                       id="setting_user_code"
                       class="form-control"
                       inputmode="numeric"
                       autocomplete="off"
                       placeholder="প্রাথমিক ইউজার কোড লিখুন">

                <small id="setting_user_code_help" class="text-muted">
                    ইউজার কোড লিখুন।
                </small>

                <div id="modalManualPreviewInfo"
                     class="manual-preview-info"></div>

            </div>

            {{-- GENDER --}}
            <div class="code-form-section">

                <div class="code-section-title">
                    লিঙ্গ:
                </div>

                <div class="code-radio-group">

                    <div class="code-radio-card">
                        <input type="radio"
                               name="code_setting_gender"
                               id="code_gender_male"
                               value="male">

                        <label for="code_gender_male">পুরুষ</label>
                    </div>

                    <div class="code-radio-card">
                        <input type="radio"
                               name="code_setting_gender"
                               id="code_gender_female"
                               value="female">

                        <label for="code_gender_female">মহিলা</label>
                    </div>

                    <div class="code-radio-card">
                        <input type="radio"
                               name="code_setting_gender"
                               id="code_gender_entry"
                               value="entry">

                        <label for="code_gender_entry">এন্ট্রির মাধ্যমে</label>
                    </div>

                </div>

            </div>

        </div>

        <div class="code-modal-footer">

            <button type="button"
                    class="btn-modal-cancel"
                    id="cancelCodeSetting">
                বাতিল
            </button>

            <button type="button"
                    class="btn-modal-save"
                    id="saveCodeSetting">
                <i class="fas fa-save"></i>
                সংরক্ষণ করুন
            </button>

        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(function () {
    'use strict';

    /* ============================================================
       CONFIGURATION
    ============================================================ */

    const codeSettingsGetUrl = @json(route('dashboard.user.code-settings.get'));
    const codeSettingsSaveUrl = @json(route('dashboard.user.code-settings.save'));
    const previewIdUrl = @json(route('dashboard.user.preview-id'));

    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    const defaultStartCodes = {
        student: 101,
        teacher_staff: 201,
        donor_member: 301
    };

    let savedCodeSettings = {};

    let currentMainManualCode = '';
    let currentMainManualCodeExplicit = false;

    let mainManualPreviewOnly = false;
    let modalManualCodeTouched = false;

    let codeSettingRequestNumber = 0;

    /* ============================================================
       BASIC HELPERS
    ============================================================ */

    function currentTypeId() {
        return String($('#user_type_id').val() || '');
    }

    function modalTypeId() {
        return String($('#code_setting_user_type').val() || '');
    }

    function currentTypeSlug() {
        return $('#user_type_id option:selected').data('slug') || '';
    }

    function institutionId() {
        return $('#institution_id').val() || '';
    }

    function modalGenerationType() {
        return $('input[name="modal_code_generation_type"]:checked').val() || '';
    }

    function settingFor(typeId) {
        return savedCodeSettings[String(typeId)] || {};
    }

    function defaultStartForType(typeId) {
        const slug = $('#user_type_id option[value="' + typeId + '"]').data('slug');

        return defaultStartCodes[slug] || 101;
    }

    function defaultModalStartForType(typeId) {
        const slug = $('#code_setting_user_type option[value="' + typeId + '"]').data('slug');

        return defaultStartCodes[slug] || 101;
    }

    function updateCodeMode(type) {
        $('#code_generation_type').val(type || 'auto');
    }

    function getSessionSetting() {
        try {
            const data = JSON.parse(
                sessionStorage.getItem('user_code_setting') || 'null'
            );

            return data &&
                String(data.user_type_id || '') === currentTypeId()
                ? data
                : null;

        } catch (e) {
            return null;
        }
    }

    function getSessionManual() {
        try {
            const data = JSON.parse(
                sessionStorage.getItem('user_manual_code_setting') || 'null'
            );

            if (
                data &&
                String(data.user_type_id || '') === currentTypeId() &&
                String(data.institution_id || '') === String(institutionId()) &&
                data.manual_code
            ) {
                return String(data.manual_code);
            }

        } catch (e) {}

        return '';
    }

    /* ============================================================
       AGE CALCULATION
    ============================================================ */

    function calculateAge() {
        const value = $('#date_of_birth').val();

        if (!value) {
            $('#age').val('');
            return;
        }

        const birth = new Date(value + 'T00:00:00');
        const today = new Date();

        if (birth > today) {
            alert('জন্ম তারিখ ভবিষ্যতের হতে পারে না!');

            $('#date_of_birth, #age').val('');

            return;
        }

        let age = today.getFullYear() - birth.getFullYear();

        const monthDiff = today.getMonth() - birth.getMonth();

        if (
            monthDiff < 0 ||
            (monthDiff === 0 && today.getDate() < birth.getDate())
        ) {
            age--;
        }

        $('#age').val(Math.max(age, 0));
    }

    $('#date_of_birth')
        .off('change.userAge')
        .on('change.userAge', calculateAge);

    if ($('#date_of_birth').val()) {
        calculateAge();
    }

    /* ============================================================
       LOAD USER CODE SETTINGS
    ============================================================ */

    function loadUserCodeSettings() {

        return $.ajax({
            url: codeSettingsGetUrl,
            type: 'GET',
            dataType: 'json',

            data: {
                institution_id: institutionId()
            },

            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }

        }).then(function (response) {

            if (!response || !response.success) {
                throw new Error(
                    response && response.message
                        ? response.message
                        : 'User Code Setting লোড করা যায়নি।'
                );
            }

            savedCodeSettings = response.settings || {};

            return savedCodeSettings;
        });
    }

    /* ============================================================
       CODE PREVIEW REQUEST
    ============================================================ */

    function previewCode(typeId, generationType) {

        return $.ajax({
            url: previewIdUrl,
            type: 'GET',
            dataType: 'json',

            data: {
                user_type_id: typeId,
                institution_id: institutionId(),
                generation_type: generationType
            },

            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
    }

    /* ============================================================
       CODE UI
    ============================================================ */

    function setAutoUI() {

        $('#manualCodeField').removeClass('show');

        $('#manualPreviewInfo')
            .removeClass('show')
            .text('');

        $('#autoCodeInfo').show();

        $('#codeModeHint')
            .text('Auto Code')
            .css('color', '#6b7280');
    }

    function setManualUI() {

        $('#manualCodeField').addClass('show');

        $('#autoCodeInfo').hide();

        $('#codeModeHint')
            .text('Manual Code')
            .css('color', '#047857');
    }

    /* ============================================================
       AUTO CODE PREVIEW
    ============================================================ */

    function autoFillId(typeId) {

        if (!typeId) {
            $('#custom_id')
                .val('')
                .attr('placeholder', 'প্রথমে ব্যবহারকারীর ধরণ নির্বাচন করুন');

            return;
        }

        const requestNumber = ++codeSettingRequestNumber;

        $('#custom_id')
            .val('লোড হচ্ছে...')
            .attr('placeholder', 'অটো কোড লোড হচ্ছে...');

        previewCode(typeId, 'auto')

            .done(function (response) {

                if (requestNumber !== codeSettingRequestNumber) {
                    return;
                }

                if (
                    response &&
                    response.success &&
                    response.preview_id != null
                ) {
                    $('#custom_id')
                        .val(response.preview_id)
                        .attr('placeholder', 'Auto Code Preview');

                } else {
                    $('#custom_id')
                        .val('')
                        .attr('placeholder', 'Auto Code পাওয়া যায়নি');
                }
            })

            .fail(function (xhr) {

                if (requestNumber !== codeSettingRequestNumber) {
                    return;
                }

                console.error(
                    'Auto User Code Preview Error:',
                    xhr.responseJSON || xhr.responseText
                );

                $('#custom_id')
                    .val('')
                    .attr('placeholder', 'Auto Code লোড করা যায়নি');
            });
    }

    /* ============================================================
       MANUAL CODE PREVIEW
    ============================================================ */

    function manualPreview(typeId, target) {

        if (!typeId) return;

        previewCode(typeId, 'manual')

            .done(function (response) {

                if (
                    !response ||
                    !response.success ||
                    response.preview_id == null
                ) {
                    target.text('পরবর্তী Manual Code পাওয়া যায়নি।');
                    return;
                }

                const code = String(response.preview_id);

                target.text('পরবর্তী Manual Code: ' + code);

                if (
                    target.attr('id') === 'modalManualPreviewInfo' &&
                    !modalManualCodeTouched &&
                    !$('#setting_user_code').val()
                ) {
                    $('#setting_user_code').val(code);

                    $('#setting_user_code_help').text(
                        'পরবর্তী Manual Starting Code: ' + code
                    );
                }

                if (
                    target.attr('id') === 'manualPreviewInfo' &&
                    !currentMainManualCodeExplicit
                ) {
                    $('#custom_id').val(code);

                    mainManualPreviewOnly = true;
                }
            })

            .fail(function (xhr) {

                console.error(
                    'Manual Code Preview Error:',
                    xhr.responseJSON || xhr.responseText
                );

                target.text('Manual Code Preview লোড করা যায়নি।');
            });
    }

    function prepareManualMainCode() {

        const typeId = currentTypeId();

        if (!typeId) {
            $('#custom_id').val('');
            return;
        }

        if (currentMainManualCodeExplicit && currentMainManualCode) {

            $('#custom_id').val(currentMainManualCode);

            return;
        }

        setManualUI();

        $('#manualPreviewInfo')
            .addClass('show')
            .text('পরবর্তী Manual Code লোড হচ্ছে...');

        manualPreview(typeId, $('#manualPreviewInfo'));
    }

    /* ============================================================
       USER TYPE CHANGE
    ============================================================ */

    $('#user_type_id')
        .off('change.userType')
        .on('change.userType', function () {

            const typeId = currentTypeId();

            currentMainManualCode = '';
            currentMainManualCodeExplicit = false;
            mainManualPreviewOnly = false;

            if (!typeId) {

                updateCodeMode('auto');

                setAutoUI();

                $('#custom_id').val('');

                return;
            }

            const sessionSetting = getSessionSetting();

            const generationType = sessionSetting
                ? sessionSetting.generation_type
                : 'auto';

            updateCodeMode(generationType);

            if (generationType === 'manual') {

                const savedManual = getSessionManual();

                if (savedManual) {

                    currentMainManualCode = savedManual;

                    currentMainManualCodeExplicit = true;

                    $('#manual_user_code').val(savedManual);

                    $('#custom_id').val(savedManual);

                } else {

                    $('#manual_user_code').val('');

                    prepareManualMainCode();
                }

                setManualUI();

            } else {

                $('#manual_user_code').val('');

                setAutoUI();

                autoFillId(typeId);
            }
        });

    /* ============================================================
       MANUAL MAIN CODE INPUT
    ============================================================ */

    $('#manual_user_code')
        .off('input.manualMain')
        .on('input.manualMain', function () {

            const value = String($(this).val() || '').replace(/\D/g, '');

            $(this).val(value);

            currentMainManualCode = value;

            currentMainManualCodeExplicit = value.length > 0;

            mainManualPreviewOnly = false;

            $('#custom_id').val(value);
        });

    /* ============================================================
       DISTRICT / UPAZILA AJAX
    ============================================================ */

    function loadDistricts(
        divisionId,
        districtSelector,
        upazilaSelector,
        selectedDistrict,
        selectedUpazila
    ) {

        const district = $(districtSelector);

        const upazila = $(upazilaSelector);

        district.html('<option value="">জেলা নির্বাচন করুন</option>');

        upazila.html('<option value="">থানা নির্বাচন করুন</option>');

        if (!divisionId) return;

        $.getJSON('{{ url('/get-districts') }}/' + divisionId)

            .done(function (data) {

                $.each(data, function (_, item) {

                    district.append(
                        $('<option>', {
                            value: item.id,
                            text: item.name_bn
                        })
                    );
                });

                if (selectedDistrict) {

                    district.val(String(selectedDistrict));

                    loadUpazilas(
                        selectedDistrict,
                        upazilaSelector,
                        selectedUpazila
                    );
                }
            })

            .fail(function (xhr) {

                console.error(
                    'District Load Error:',
                    xhr.responseText
                );
            });
    }

    function loadUpazilas(
        districtId,
        upazilaSelector,
        selectedUpazila
    ) {

        const upazila = $(upazilaSelector);

        upazila.html('<option value="">থানা নির্বাচন করুন</option>');

        if (!districtId) return;

        $.getJSON('{{ url('/get-upazilas') }}/' + districtId)

            .done(function (data) {

                $.each(data, function (_, item) {

                    upazila.append(
                        $('<option>', {
                            value: item.id,
                            text: item.name_bn
                        })
                    );
                });

                if (selectedUpazila) {
                    upazila.val(String(selectedUpazila));
                }
            })

            .fail(function (xhr) {

                console.error(
                    'Upazila Load Error:',
                    xhr.responseText
                );
            });
    }

    /* ============================================================
       ADDRESS EVENTS
    ============================================================ */

    $('#permanent_division')
        .off('change.userAddress')
        .on('change.userAddress', function () {

            loadDistricts(
                $(this).val(),
                '#permanent_district',
                '#permanent_upazila'
            );
        });

    $('#permanent_district')
        .off('change.userAddress')
        .on('change.userAddress', function () {

            loadUpazilas(
                $(this).val(),
                '#permanent_upazila'
            );
        });

    $('#present_division')
        .off('change.userAddress')
        .on('change.userAddress', function () {

            loadDistricts(
                $(this).val(),
                '#present_district',
                '#present_upazila'
            );
        });

    $('#present_district')
        .off('change.userAddress')
        .on('change.userAddress', function () {

            loadUpazilas(
                $(this).val(),
                '#present_upazila'
            );
        });

    /* ============================================================
       SAME ADDRESS
    ============================================================ */

    $('#same_as_permanent')
        .off('change.sameAddress')
        .on('change.sameAddress', function () {

            if (!this.checked) return;

            const division = $('#permanent_division').val();

            const district = $('#permanent_district').val();

            const upazila = $('#permanent_upazila').val();

            $('#present_division').val(division);

            loadDistricts(
                division,
                '#present_district',
                '#present_upazila',
                district,
                upazila
            );

            $('input[name="present_post_office"]').val(
                $('input[name="permanent_post_office"]').val()
            );

            $('input[name="present_village_road"]').val(
                $('input[name="permanent_village_road"]').val()
            );
        });

    /* ============================================================
       INITIAL ADDRESS LOAD
    ============================================================ */

    loadDistricts(
        $('#permanent_division').val(),
        '#permanent_district',
        '#permanent_upazila',
        @json(old('permanent_district_id')),
        @json(old('permanent_upazila_id'))
    );

    loadDistricts(
        $('#present_division').val(),
        '#present_district',
        '#present_upazila',
        @json(old('present_district_id')),
        @json(old('present_upazila_id'))
    );

    /* ============================================================
       MODAL RESET
    ============================================================ */

    function resetCodeSettingModal() {

        $('#code_setting_user_type').val('');

        $('input[name="modal_code_generation_type"]')
            .prop('checked', false);

        $('input[name="code_setting_gender"]')
            .prop('checked', false);

        $('#setting_user_code').val('');

        $('#setting_user_code_help').text(
            'প্রথমে ব্যবহারকারীর ধরণ নির্বাচন করুন।'
        );

        $('#codeGenerationTypeSection, #codeInputSection').hide();

        $('#modalManualPreviewInfo')
            .removeClass('show')
            .text('');

        modalManualCodeTouched = false;
    }

    /* ============================================================
       OPEN MODAL
    ============================================================ */

    function openCodeSettingModal() {

        resetCodeSettingModal();

        const selectedTypeId = currentTypeId();

        if (selectedTypeId) {

            $('#code_setting_user_type').val(selectedTypeId);
        }

        const typeId = modalTypeId();

        if (typeId) {

            $('#codeGenerationTypeSection').show();

            const currentMode = $('#code_generation_type').val() || 'auto';

            $('input[name="modal_code_generation_type"][value="' + currentMode + '"]')
                .prop('checked', true);

            $('#codeInputSection').show();

            loadUserCodeSettings()

                .done(function () {

                    const setting = settingFor(typeId);

                    const mode = modalGenerationType();

                    const gender = setting.gender_preference || 'entry';

                    $('input[name="code_setting_gender"][value="' + gender + '"]')
                        .prop('checked', true);

                    if (mode === 'auto') {

                        const start = Number(setting.start_code) > 0
                            ? setting.start_code
                            : defaultModalStartForType(typeId);

                        $('#setting_user_code').val(start);

                        $('#setting_user_code_help').text(
                            'অটো জেনারেশনের প্রাথমিক কোড। প্রয়োজনে পরিবর্তন করতে পারবেন।'
                        );

                    } else {

                        const manual = Number(setting.manual_start_code) > 0
                            ? setting.manual_start_code
                            : '';

                        $('#setting_user_code').val(manual);

                        if (manual) {

                            $('#setting_user_code_help').text(
                                'সর্বশেষ সংরক্ষিত Manual Starting Code: ' + manual
                            );

                        } else {

                            $('#modalManualPreviewInfo')
                                .addClass('show');

                            manualPreview(
                                typeId,
                                $('#modalManualPreviewInfo')
                            );
                        }
                    }
                })

                .fail(function (xhr) {

                    console.error(
                        'Modal Setting Load Error:',
                        xhr.responseJSON || xhr.responseText
                    );

                    $('#setting_user_code').val('');
                });
        }

        $('#codeSettingModal').addClass('show');

        $('body').css('overflow', 'hidden');
    }

    /* ============================================================
       CLOSE MODAL
    ============================================================ */

    function closeCodeSettingModal() {

        $('#codeSettingModal').removeClass('show');

        $('body').css('overflow', '');
    }

    /* ============================================================
       MODAL OPEN EVENT
    ============================================================ */

    $(document)
        .off('click.userCodeModal', '#openCodeSetting')
        .on('click.userCodeModal', '#openCodeSetting', function (event) {

            event.preventDefault();

            event.stopPropagation();

            openCodeSettingModal();
        });

    /* ============================================================
       MODAL CLOSE EVENTS
    ============================================================ */

    $(document)
        .off('click.userCodeModal', '#closeCodeSetting, #cancelCodeSetting')
        .on(
            'click.userCodeModal',
            '#closeCodeSetting, #cancelCodeSetting',
            function (event) {

                event.preventDefault();

                closeCodeSettingModal();
            }
        );

    $(document)
        .off('click.userCodeModal', '#codeSettingModal')
        .on('click.userCodeModal', '#codeSettingModal', function (event) {

            if (event.target === this) {
                closeCodeSettingModal();
            }
        });

    $(document)
        .off('keydown.userCodeModal')
        .on('keydown.userCodeModal', function (event) {

            if (
                event.key === 'Escape' &&
                $('#codeSettingModal').hasClass('show')
            ) {
                closeCodeSettingModal();
            }
        });

    /* ============================================================
       MODAL USER TYPE CHANGE
    ============================================================ */

    $('#code_setting_user_type')
        .off('change.userCodeModal')
        .on('change.userCodeModal', function () {

            const typeId = modalTypeId();

            $('#setting_user_code').val('');

            $('input[name="modal_code_generation_type"]')
                .prop('checked', false);

            $('#modalManualPreviewInfo')
                .removeClass('show')
                .text('');

            modalManualCodeTouched = false;

            if (!typeId) {

                $('#codeGenerationTypeSection, #codeInputSection').hide();

                return;
            }

            $('#codeGenerationTypeSection').slideDown(150);

            $('#codeInputSection').hide();

            loadUserCodeSettings();
        });

    /* ============================================================
       MODAL GENERATION TYPE CHANGE
    ============================================================ */

    $('input[name="modal_code_generation_type"]')
        .off('change.userCodeModal')
        .on('change.userCodeModal', function () {

            const typeId = modalTypeId();

            const mode = modalGenerationType();

            if (!typeId || !mode) return;

            modalManualCodeTouched = false;

            $('#codeInputSection').slideDown(150);

            $('#setting_user_code').val('');

            loadUserCodeSettings()

                .done(function () {

                    const setting = settingFor(typeId);

                    if (mode === 'auto') {

                        const start = Number(setting.start_code) > 0
                            ? setting.start_code
                            : defaultModalStartForType(typeId);

                        $('#setting_user_code').val(start);

                        $('#setting_user_code_help').text(
                            'অটো জেনারেশনের প্রাথমিক কোড।'
                        );

                    } else {

                        const manual = Number(setting.manual_start_code) > 0
                            ? setting.manual_start_code
                            : '';

                        $('#setting_user_code').val(manual);

                        if (manual) {

                            $('#setting_user_code_help').text(
                                'Manual Starting Code: ' + manual
                            );

                        } else {

                            $('#modalManualPreviewInfo')
                                .addClass('show')
                                .text('পরবর্তী Manual Code লোড হচ্ছে...');

                            manualPreview(
                                typeId,
                                $('#modalManualPreviewInfo')
                            );
                        }
                    }
                })

                .fail(function (xhr) {

                    console.error(
                        'Load User Code Setting Error:',
                        xhr.responseJSON || xhr.responseText
                    );

                    $('#setting_user_code_help').text(
                        'কোড সেটিং লোড করা যায়নি।'
                    );
                });
        });

    /* ============================================================
       MODAL CODE INPUT
    ============================================================ */

    $('#setting_user_code')
        .off('input.userCodeModal')
        .on('input.userCodeModal', function () {

            const value = String($(this).val() || '').replace(/\D/g, '');

            $(this).val(value);

            modalManualCodeTouched = true;

            const mode = modalGenerationType();

            $('#setting_user_code_help').text(
                (mode === 'manual' ? 'Manual' : 'Auto') +
                ' Starting Code: ' +
                value
            );
        });

    /* ============================================================
       SAVE CODE SETTING
    ============================================================ */

    $('#saveCodeSetting')
        .off('click.userCodeModal')
        .on('click.userCodeModal', function (event) {

            event.preventDefault();

            const typeId = modalTypeId();

            const mode = modalGenerationType();

            const enteredCode = $.trim(
                $('#setting_user_code').val()
            );

            const genderPreference = $(
                'input[name="code_setting_gender"]:checked'
            ).val();

            if (!typeId) {

                alert('ব্যবহারকারীর ধরণ নির্বাচন করুন।');

                return;
            }

            if (!['auto', 'manual'].includes(mode)) {

                alert('Auto অথবা Manual নির্বাচন করুন।');

                return;
            }

            if (!['male', 'female', 'entry'].includes(genderPreference)) {

                alert('Gender Preference নির্বাচন করুন।');

                return;
            }

            if (
                !/^\d+$/.test(enteredCode) ||
                Number(enteredCode) < 1 ||
                Number(enteredCode) > 999999999
            ) {

                alert(
                    mode === 'auto'
                        ? 'সঠিক Auto Starting Code লিখুন।'
                        : 'সঠিক Manual Starting Code লিখুন।'
                );

                $('#setting_user_code').trigger('focus');

                return;
            }

            const existing = settingFor(typeId);

            const defaultStart = defaultModalStartForType(typeId);

            const startCode = mode === 'auto'
                ? Number(enteredCode)
                : (
                    Number(existing.start_code) > 0
                        ? Number(existing.start_code)
                        : defaultStart
                );

            const manualStartCode = mode === 'manual'
                ? Number(enteredCode)
                : (
                    existing.manual_start_code !== undefined
                        ? existing.manual_start_code
                        : null
                );

            const $button = $(this);

            $button.prop('disabled', true);

            $.ajax({

                url: codeSettingsSaveUrl,

                type: 'POST',

                data: JSON.stringify({

                    user_type_id: Number(typeId),

                    generation_type: mode,

                    start_code: startCode,

                    manual_start_code: manualStartCode,

                    gender_preference: genderPreference,

                    institution_id: institutionId()
                }),

                contentType: 'application/json; charset=UTF-8',

                dataType: 'json',

                headers: {

                    'X-CSRF-TOKEN': csrfToken,

                    'Accept': 'application/json',

                    'X-Requested-With': 'XMLHttpRequest'
                }

            })

            .done(function (response) {

                if (!response || !response.success) {

                    alert(
                        response && response.message
                            ? response.message
                            : 'Setting সংরক্ষণ করা যায়নি।'
                    );

                    return;
                }

                const responseSettings = response.settings || {};

                const updated = responseSettings[typeId] || {};

                savedCodeSettings[typeId] = {

                    start_code:
                        updated.start_code !== undefined
                            ? Number(updated.start_code)
                            : startCode,

                    manual_start_code:
                        updated.manual_start_code !== undefined
                            ? updated.manual_start_code
                            : manualStartCode,

                    gender_preference:
                        updated.gender_preference || genderPreference
                };

                sessionStorage.setItem(
                    'user_code_setting',
                    JSON.stringify({

                        user_type_id: typeId,

                        generation_type: mode
                    })
                );

                $('#user_type_id').val(typeId).trigger('change');

                updateCodeMode(mode);

                $('input[name="gender"]').prop('checked', false);

                if (
                    genderPreference === 'male' ||
                    genderPreference === 'female'
                ) {

                    $('input[name="gender"][value="' + genderPreference + '"]')
                        .prop('checked', true);
                }

                if (mode === 'manual') {

                    currentMainManualCode = '';

                    currentMainManualCodeExplicit = false;

                    mainManualPreviewOnly = true;

                    $('#manual_user_code').val('');

                    setManualUI();

                    $('#manualPreviewInfo')
                        .addClass('show')
                        .text('পরবর্তী Manual Code লোড হচ্ছে...');

                    manualPreview(
                        typeId,
                        $('#manualPreviewInfo')
                    );

                } else {

                    currentMainManualCode = '';

                    currentMainManualCodeExplicit = false;

                    mainManualPreviewOnly = false;

                    $('#manual_user_code').val('');

                    setAutoUI();

                    autoFillId(typeId);
                }

                closeCodeSettingModal();

                alert(
                    response.message ||
                    'User Code Setting সফলভাবে সংরক্ষণ করা হয়েছে।'
                );
            })

            .fail(function (xhr) {

                console.error(
                    'Save User Code Setting Error:',
                    xhr.responseJSON || xhr.responseText
                );

                alert(
                    (xhr.responseJSON && xhr.responseJSON.message) ||
                    'User Code Setting সংরক্ষণ করা যায়নি।'
                );
            })

            .always(function () {

                $button.prop('disabled', false);
            });
        });

    /* ============================================================
       FORM SUBMIT
    ============================================================ */

    $('#userCreateForm')
        .off('submit.userCode')
        .on('submit.userCode', function (event) {

            const typeId = currentTypeId();

            const mode = $('#code_generation_type').val() || 'auto';

            if (!typeId) {

                event.preventDefault();

                alert('অনুগ্রহ করে ব্যবহারকারীর ধরণ নির্বাচন করুন।');

                return false;
            }

            if (mode === 'auto') {

                $('#custom_id').val('');

                return true;
            }

            const manualCode = String(
                $('#manual_user_code').val() || ''
            ).trim();

            if (manualCode) {

                if (!/^\d+$/.test(manualCode)) {

                    event.preventDefault();

                    alert('ম্যানুয়াল কোড শুধুমাত্র সংখ্যা হতে হবে।');

                    return false;
                }

                $('#custom_id').val(manualCode);

                return true;
            }

            // Empty manual input means backend allocates next available code.
            $('#custom_id').val('');

            return true;
        });

    /* ============================================================
       INITIAL PAGE STATE
    ============================================================ */

    loadUserCodeSettings().always(function () {

        const typeId = currentTypeId();

        if (!typeId) return;

        const sessionSetting = getSessionSetting();

        const mode = sessionSetting
            ? sessionSetting.generation_type
            : ($('#code_generation_type').val() || 'auto');

        updateCodeMode(mode);

        if (mode === 'manual') {

            setManualUI();

            prepareManualMainCode();

        } else {

            setAutoUI();

            autoFillId(typeId);
        }
    });

});
</script>

@endsection