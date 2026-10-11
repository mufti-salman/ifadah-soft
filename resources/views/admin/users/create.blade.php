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
        box-shadow: 0 2px 16px rgba(30, 58, 138, 0.08);
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

    .reg-card-header .header-icon {
        width: 38px;
        height: 38px;
        background: #eef2fb;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3b5bdb;
        font-size: 1rem;
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
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
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
        font-size: 0.82rem;
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
        font-size: 0.88rem;
        color: #374151;
        background: #f8faff;
        outline: none;
        transition: border-color 0.2s, background 0.2s;
        font-family: 'Hind Siliguri', sans-serif;
    }

    .form-select {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #6b8cde;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(107, 140, 222, 0.12);
    }

    .form-control::placeholder {
        color: #b0bac9;
    }

    .field-group {
        margin-bottom: 0;
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
        height: 42px;
    }

    .radio-label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.88rem;
        color: #374151;
        cursor: pointer;
    }

    .radio-label input[type="radio"] {
        accent-color: #3b5bdb;
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    .col-fields {
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
        display: flex;
        flex-direction: column;
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
        font-size: 0.9rem;
        font-weight: 700;
        color: #1e3a8a;
    }

    .address-card-title i {
        color: #3b5bdb;
        font-size: 0.9rem;
    }

    .same-check-label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        color: #6b7280;
        cursor: pointer;
        background: #eef2fb;
        padding: 4px 10px;
        border-radius: 20px;
        white-space: nowrap;
    }

    .same-check-label input {
        accent-color: #3b5bdb;
        cursor: pointer;
    }

    .addr-fields {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .addr-mini-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .student-fields {
        display: none;
        background: #f5f7ff;
        border: 1.5px solid #dbe4ff;
        border-radius: 12px;
        padding: 14px;
    }

    .student-fields.show {
        display: block;
    }

    .student-fields-title {
        font-size: 0.84rem;
        font-weight: 700;
        color: #1e3a8a;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .student-fields-grid {
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
        border-radius: 0 0 16px 16px;
    }

    .btn-cancel {
        background: transparent;
        border: 1.5px solid #e5e9f5;
        border-radius: 10px;
        padding: 10px 24px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #6b7280;
        cursor: pointer;
        transition: background 0.15s;
        font-family: 'Hind Siliguri', sans-serif;
        text-decoration: none;
    }

    .btn-cancel:hover {
        background: #f0f4ff;
        color: #374151;
    }

    .btn-save {
        background: #1e3a8a;
        border: none;
        border-radius: 10px;
        padding: 10px 26px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.15s;
        font-family: 'Hind Siliguri', sans-serif;
    }

    .btn-save:hover {
        background: #1e40af;
    }

    .alert-danger-custom {
        background: #fef2f2;
        border: 1.5px solid #fca5a5;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 20px;
        font-size: 0.88rem;
        color: #b91c1c;
    }

    .preview-id-box {
        background: linear-gradient(135deg, #eef2ff, #e0e7ff);
        border: 2px solid #3b5bdb;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .preview-id-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e3a8a;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .preview-id-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1e3a8a;
        background: white;
        padding: 6px 16px;
        border-radius: 30px;
        letter-spacing: 1px;
        font-family: monospace;
    }

    .preview-id-note {
        font-size: 0.7rem;
        color: #4b5563;
        width: 100%;
    }

    .code-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        z-index: 9999;
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
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.20);
        animation: modalShow 0.2s ease;
    }

    @keyframes modalShow {
        from {
            opacity: 0;
            transform: translateY(-15px) scale(0.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
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
        border: none;
        background: #f3f4f6;
        border-radius: 8px;
        color: #6b7280;
        cursor: pointer;
        font-size: 16px;
    }

    .code-modal-close:hover {
        background: #fee2e2;
        color: #dc2626;
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
        font-size: 0.85rem;
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
        font-size: 0.84rem;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
        text-align: center;
    }

    .code-radio-card input:checked + label {
        border-color: #6366f1;
        background: #eef2ff;
        color: #3730a3;
    }

    .code-radio-card label:hover {
        border-color: #a5b4fc;
    }

    .code-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 22px;
        border-top: 1px solid #eef2fb;
        background: #fafbff;
        border-radius: 0 0 18px 18px;
    }

    .btn-modal-cancel {
        border: 1.5px solid #e5e7eb;
        background: #fff;
        color: #6b7280;
        border-radius: 9px;
        padding: 9px 20px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-modal-save {
        border: none;
        background: #3730a3;
        color: #fff;
        border-radius: 9px;
        padding: 9px 20px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .btn-modal-save:hover {
        background: #312e81;
    }

    .manual-code-field {
        display: none;
        margin-top: 12px;
    }

    .manual-code-field.show {
        display: block;
    }

    .entry-info {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 11px 13px;
        font-size: 0.78rem;
        color: #6b7280;
        margin-top: 10px;
    }

    .manual-preview-info {
        display: none;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #047857;
        border-radius: 10px;
        padding: 11px 13px;
        font-size: 0.78rem;
        margin-top: 10px;
    }

    .manual-preview-info.show {
        display: block;
    }

    @media (max-width: 992px) {
        .form-grid {
            grid-template-columns: 1fr 1fr;
        }

        .address-col {
            grid-column: 1 / -1;
            flex-direction: row;
            flex-wrap: wrap;
        }

        .address-card {
            flex: 1;
            min-width: 260px;
        }
    }

    @media (max-width: 640px) {
        .reg-page {
            padding: 10px;
        }

        .reg-card-header {
            padding: 15px;
            align-items: flex-start;
        }

        .reg-card-header h5 {
            font-size: 0.95rem;
        }

        .btn-code-setting {
            padding: 8px 10px;
            font-size: 0.75rem;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .mini-grid-2,
        .addr-mini-grid,
        .student-fields-grid {
            grid-template-columns: 1fr;
        }

        .address-col {
            grid-column: auto;
            flex-direction: column;
        }

        .reg-card-body {
            padding: 16px;
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

            <button type="button"
                    class="btn-code-setting"
                    id="openCodeSetting">

                <i class="fas fa-sliders-h"></i>

                কোড সেটিং

            </button>

        </div>


        <div class="reg-card-body">

            @if ($errors->any())

                <div class="alert-danger-custom">

                    <ul style="margin:0; padding-left:18px;">

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
                       value="{{ auth()->user()->institution_id ?? auth()->user()->madrasa_id ?? 1 }}">


                <input type="hidden"
                       name="code_generation_type"
                       id="code_generation_type"
                       value="{{ old('code_generation_type', 'auto') }}">


                <div class="form-grid">


                    <div class="col-fields">


                        <div class="field-group">

                            <label class="form-label">

                                ব্যবহারকারীর ধরণ

                                <span class="req">*</span>

                            </label>


                            <select name="user_type"
                                    id="user_type"
                                    class="form-select"
                                    required>

                                <option value="">
                                    নির্বাচন করুন
                                </option>


                                @foreach($userTypes as $key => $type)

                                    <option value="{{ $key }}"
                                        {{ old('user_type') == $key ? 'selected' : '' }}>

                                        {{ $type }}

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
                                       style="background:#eef2fb;
                                              font-weight:600;
                                              color:#1e3a8a;">


                                <small id="codeModeHint"
                                       style="display:block;
                                              margin-top:5px;
                                              color:#6b7280;
                                              font-size:0.70rem;">

                                    Auto Code

                                </small>

                            </div>


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
                                               {{ old('gender', 'male') == 'male' ? 'checked' : '' }}>

                                        পুরুষ

                                    </label>


                                    <label class="radio-label">

                                        <input type="radio"
                                               name="gender"
                                               value="female"
                                               {{ old('gender') == 'female' ? 'checked' : '' }}>

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

                            <label class="form-label">
                                পিতার নাম
                            </label>


                            <input type="text"
                                   name="father_name"
                                   class="form-control"
                                   value="{{ old('father_name') }}"
                                   placeholder="পিতার নাম লিখুন">

                        </div>


                        <div class="field-group">

                            <label class="form-label">
                                মাতার নাম
                            </label>


                            <input type="text"
                                   name="mother_name"
                                   class="form-control"
                                   value="{{ old('mother_name') }}"
                                   placeholder="মাতার নাম লিখুন">

                        </div>


                    </div>


                    <div class="col-fields">


                        <div class="field-group">

                            <label class="form-label">
                                রক্তের গ্রুপ
                            </label>


                            <select name="blood_group"
                                    class="form-select">

                                <option value="">
                                    নির্বাচন করুন
                                </option>


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

                                <label class="form-label">
                                    জন্ম তারিখ
                                </label>


                                <input type="date"
                                       name="date_of_birth"
                                       id="date_of_birth"
                                       class="form-control"
                                       value="{{ old('date_of_birth') }}">

                            </div>


                            <div class="field-group">

                                <label class="form-label">
                                    বয়স
                                </label>


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

                            <label class="form-label">
                                NID/জন্ম নিবন্ধন নং
                            </label>


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


                                <select name="phone_relation"
                                        class="form-select">

                                    <option value="">
                                        সম্পর্ক
                                    </option>

                                    <option value="self"
                                        {{ old('phone_relation') == 'self' ? 'selected' : '' }}>
                                        নিজ
                                    </option>

                                    <option value="father"
                                        {{ old('phone_relation') == 'father' ? 'selected' : '' }}>
                                        পিতা
                                    </option>

                                    <option value="mother"
                                        {{ old('phone_relation') == 'mother' ? 'selected' : '' }}>
                                        মাতা
                                    </option>

                                    <option value="guardian"
                                        {{ old('phone_relation') == 'guardian' ? 'selected' : '' }}>
                                        অভিভাবক
                                    </option>

                                </select>

                            </div>

                        </div>


                        <div class="field-group">

                            <label class="form-label">
                                মোবাইল 2
                            </label>


                            <div class="phone-group">

                                <input type="text"
                                       name="phone2"
                                       class="form-control"
                                       value="{{ old('phone2') }}"
                                       placeholder="০১XXXXXXXXX">


                                <select name="phone2_relation"
                                        class="form-select">

                                    <option value="">
                                        সম্পর্ক
                                    </option>

                                    <option value="self"
                                        {{ old('phone2_relation') == 'self' ? 'selected' : '' }}>
                                        নিজ
                                    </option>

                                    <option value="father"
                                        {{ old('phone2_relation') == 'father' ? 'selected' : '' }}>
                                        পিতা
                                    </option>

                                    <option value="mother"
                                        {{ old('phone2_relation') == 'mother' ? 'selected' : '' }}>
                                        মাতা
                                    </option>

                                    <option value="guardian"
                                        {{ old('phone2_relation') == 'guardian' ? 'selected' : '' }}>
                                        অভিভাবক
                                    </option>

                                </select>

                            </div>

                        </div>


                        <div class="field-group">

                            <label class="form-label">
                                ই-মেইল
                            </label>


                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   value="{{ old('email') }}"
                                   placeholder="example@mail.com">

                        </div>

                    </div>


                    <div class="address-col">


                        <div class="address-card">

                            <div class="address-card-header">

                                <div class="address-card-title">

                                    <i class="fas fa-map-marker-alt"></i>

                                    স্থায়ী ঠিকানা

                                </div>

                            </div>


                            <div class="addr-fields">


                                <div class="addr-mini-grid">


                                    <div class="field-group">

                                        <select name="permanent_division_id"
                                                id="permanent_division"
                                                class="form-select">

                                            <option value="">
                                                বিভাগ
                                            </option>


                                            @foreach($divisions as $div)

                                                <option value="{{ $div->id }}"
                                                    {{ old('permanent_division_id') == $div->id ? 'selected' : '' }}>

                                                    {{ $div->name_bn }}

                                                </option>

                                            @endforeach

                                        </select>

                                    </div>


                                    <div class="field-group">

                                        <select name="permanent_district_id"
                                                id="permanent_district"
                                                class="form-select">

                                            <option value="">
                                                জেলা
                                            </option>

                                        </select>

                                    </div>

                                </div>


                                <div class="addr-mini-grid">


                                    <div class="field-group">

                                        <select name="permanent_upazila_id"
                                                id="permanent_upazila"
                                                class="form-select">

                                            <option value="">
                                                থানা
                                            </option>

                                        </select>

                                    </div>


                                    <div class="field-group">

                                        <input type="text"
                                               name="permanent_post_office"
                                               class="form-control"
                                               value="{{ old('permanent_post_office') }}"
                                               placeholder="ডাকঘর">

                                    </div>

                                </div>


                                <div class="field-group">

                                    <input type="text"
                                           name="permanent_village_road"
                                           class="form-control"
                                           value="{{ old('permanent_village_road') }}"
                                           placeholder="গ্রাম/রাস্তা/বাসা নং">

                                </div>

                            </div>

                        </div>


                        <div class="address-card">

                            <div class="address-card-header">

                                <div class="address-card-title">

                                    <i class="fas fa-map-marker-alt"></i>

                                    অস্থায়ী ঠিকানা

                                </div>


                                <label class="same-check-label">

                                    <input type="checkbox"
                                           id="same_as_permanent">

                                    স্থায়ী ঠিকানার মতই

                                </label>

                            </div>


                            <div class="addr-fields">

                                <div class="addr-mini-grid">


                                    <div class="field-group">

                                        <select name="present_division_id"
                                                id="present_division"
                                                class="form-select">

                                            <option value="">
                                                বিভাগ
                                            </option>


                                            @foreach($divisions as $div)

                                                <option value="{{ $div->id }}"
                                                    {{ old('present_division_id') == $div->id ? 'selected' : '' }}>

                                                    {{ $div->name_bn }}

                                                </option>

                                            @endforeach

                                        </select>

                                    </div>


                                    <div class="field-group">

                                        <select name="present_district_id"
                                                id="present_district"
                                                class="form-select">

                                            <option value="">
                                                জেলা
                                            </option>

                                        </select>

                                    </div>

                                </div>


                                <div class="addr-mini-grid">


                                    <div class="field-group">

                                        <select name="present_upazila_id"
                                                id="present_upazila"
                                                class="form-select">

                                            <option value="">
                                                থানা
                                            </option>

                                        </select>

                                    </div>


                                    <div class="field-group">

                                        <input type="text"
                                               name="present_post_office"
                                               class="form-control"
                                               value="{{ old('present_post_office') }}"
                                               placeholder="ডাকঘর">

                                    </div>

                                </div>


                                <div class="field-group">

                                    <input type="text"
                                           name="present_village_road"
                                           class="form-control"
                                           value="{{ old('present_village_road') }}"
                                           placeholder="গ্রাম/রাস্তা/বাসা নং">

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <div class="reg-card-footer">

                    <a href="{{ route('dashboard.user.index') }}"
                       class="btn-cancel">

                        বাতিল করুন

                    </a>


                    <button type="submit"
                            class="btn-save">

                        <i class="fas fa-save"></i>

                        সেভ করুন

                    </button>

                </div>


            </form>

        </div>

    </div>

</div>


<div class="code-modal-overlay"
     id="codeSettingModal">

    <div class="code-modal">


        <div class="code-modal-header">

            <div class="code-modal-title">

                <div class="code-modal-title-icon">

                    <i class="fas fa-cog"></i>

                </div>

                <h5>
                    User Code Setting
                </h5>

            </div>


            <button type="button"
                    class="code-modal-close"
                    id="closeCodeSetting">

                <i class="fas fa-times"></i>

            </button>

        </div>


        <div class="code-modal-body">


            <div class="code-form-section">

                <div class="code-section-title">
                    ব্যবহারকারীর ধরণ:
                </div>


                <select id="code_setting_user_type"
                        class="form-select">

                    <option value="">
                        নির্বাচন করুন
                    </option>


                    @foreach($userTypes as $key => $type)

                        <option value="{{ $key }}">
                            {{ $type }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="code-form-section">

                <div class="code-section-title">
                    ধরন:
                </div>


                <div class="code-radio-group">


                    <div class="code-radio-card">

                        <input type="radio"
                               name="code_generation_type"
                               id="code_auto"
                               value="auto"
                               checked>

                        <label for="code_auto">

                            <i class="fas fa-magic"></i>

                            নির্দিষ্ট কোড হতে অটো

                        </label>

                    </div>


                    <div class="code-radio-card">

                        <input type="radio"
                               name="code_generation_type"
                               id="code_manual"
                               value="manual">

                        <label for="code_manual">

                            <i class="fas fa-keyboard"></i>

                            ম্যানুয়াল

                        </label>

                    </div>

                </div>


                <div class="manual-code-field"
                     id="manualCodeField">

                    <label class="form-label">

                        ইউজার কোড:

                    </label>


                    <input type="text"
                           id="manual_user_code"
                           class="form-control"
                           inputmode="numeric"
                           autocomplete="off"
                           placeholder="খালি রাখলে role অনুযায়ী পরবর্তী কোড হবে">


                    <div class="manual-preview-info"
                         id="manualPreviewInfo">

                        <i class="fas fa-check-circle"></i>

                        কোড খালি রাখলে সিস্টেম নির্বাচিত role অনুযায়ী
                        পরবর্তী available manual code তৈরি করবে।

                    </div>

                </div>


                <div class="entry-info"
                     id="autoCodeInfo">

                    <i class="fas fa-info-circle"></i>

                    অটো নির্বাচন করলে সিস্টেম নির্ধারিত
                    role অনুযায়ী serial code তৈরি করবে।

                    <br>

                    Student: <strong>101+</strong> &nbsp; |
                    User: <strong>201+</strong> &nbsp; |
                    User Admin: <strong>301+</strong> &nbsp; |
                    Support Admin: <strong>401+</strong>

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

@push('scripts')

<script>

$(document).ready(function () {

    /* ============================================================
       CODE START MAP
       ============================================================ */

    var codeStartMap = {
        student: 101,
        user: 201,
        user_admin: 301,
        support_admin: 401
    };


    /* ============================================================
       CODE SETTING STATE
       ============================================================ */

    var modalOriginalSetting = null;

    var currentMainManualCode = '';

    var currentMainManualCodeExplicit = false;

    var mainManualPreviewOnly = false;

    var modalManualCodeTouched = false;


    /* ============================================================
       AGE CALCULATE
       ============================================================ */

    function calculateAge() {

        var birthDate = $('#date_of_birth').val();

        if (!birthDate) {
            $('#age').val('');
            return;
        }

        var today = new Date();

        var birth = new Date(birthDate);

        if (birth > today) {

            $('#age').val('');

            alert(
                'জন্ম তারিখ ভবিষ্যতের হতে পারে না!'
            );

            $('#date_of_birth').val('');

            return;
        }

        var age =
            today.getFullYear() -
            birth.getFullYear();

        var m =
            today.getMonth() -
            birth.getMonth();

        if (
            m < 0 ||
            (
                m === 0 &&
                today.getDate() < birth.getDate()
            )
        ) {
            age--;
        }

        if (age < 0) {
            age = 0;
        }

        $('#age').val(age);
    }


    $('#date_of_birth').on(
        'change',
        calculateAge
    );


    if ($('#date_of_birth').val()) {
        calculateAge();
    }


    /* ============================================================
       GET INSTITUTION ID
       ============================================================ */

    function getInstitutionId() {

        return $('#institution_id').val() || '';
    }


    /* ============================================================
       GET SAVED CODE SETTING
       ============================================================ */

    function getSavedCodeSetting() {

        try {

            var saved =
                sessionStorage.getItem(
                    'user_code_setting'
                );

            if (!saved) {
                return null;
            }

            return JSON.parse(saved);

        } catch (e) {

            console.error(
                'Code setting parse error:',
                e
            );

            return null;
        }
    }


    /* ============================================================
       UPDATE MAIN HIDDEN CODE TYPE
       ============================================================ */

    function updateHiddenCodeType(type) {

        $('#code_generation_type')
            .val(type || 'auto');
    }


    /* ============================================================
       GET ROLE START CODE
       ============================================================ */

    function getRoleStart(userType) {

        return codeStartMap[userType] || 101;
    }


    /* ============================================================
       GET CURRENT MAIN CODE SETTING
       ============================================================ */

    function getCurrentMainCodeSetting() {

        return {

            user_type:
                $('#user_type').val() || '',

            generation_type:
                $('#code_generation_type').val() ||
                'auto',

            /*
             * শুধু Main Form-এর explicit manual code।
             * Modal-এর value এখানে আসবে না।
             */
            manual_code:
                currentMainManualCode || ''
        };
    }


    /* ============================================================
       SET AUTO MODE UI
       ============================================================ */

    function setAutoModeUI() {

        $('#manualCodeField')
            .removeClass('show');

        $('#manualPreviewInfo')
            .removeClass('show');

        $('#autoCodeInfo')
            .show();

        $('#codeModeHint')
            .text('Auto Code')
            .css(
                'color',
                '#6b7280'
            );
    }


    /* ============================================================
       SET MANUAL MODE UI
       ============================================================ */

    function setManualModeUI() {

        var userType =
            $('#user_type').val();

        $('#manualCodeField')
            .addClass('show');

        $('#manualPreviewInfo')
            .addClass('show');

        $('#autoCodeInfo')
            .hide();

        $('#manual_user_code')
            .attr(
                'placeholder',
                'খালি রাখলে ' +
                getRoleStart(userType) +
                ' থেকে পরবর্তী কোড হবে'
            );

        $('#codeModeHint')
            .text('Manual Code')
            .css(
                'color',
                '#047857'
            );
    }


    /* ============================================================
       AUTO USER ID PREVIEW
       ============================================================ */

    function autoFillId(
        userType,
        institutionId
    ) {

        if (!userType) {

            $('#custom_id')
                .val('')
                .attr(
                    'placeholder',
                    'প্রথমে ইউজার ধরণ নির্বাচন করুন'
                );

            return;
        }

        var roleStart =
            getRoleStart(userType);

        $('#custom_id')
            .val('লোড হচ্ছে...')
            .attr(
                'placeholder',
                'অটো কোড লোড হচ্ছে...'
            );

        $.ajax({

            url:
                '{{ route('dashboard.user.preview-id') }}',

            type: 'GET',

            data: {

                user_type: userType,

                institution_id: institutionId,

                generation_type: 'auto'
            },

            dataType: 'json',

            success: function (response) {

                console.log(
                    'Auto User Code Preview:',
                    response
                );

                if (
                    response &&
                    response.success &&
                    response.preview_id
                ) {

                    var previewId =
                        parseInt(
                            response.preview_id,
                            10
                        );

                    if (
                        !isNaN(previewId) &&
                        previewId >= roleStart
                    ) {

                        $('#custom_id')
                            .val(
                                response.preview_id
                            );

                        return;
                    }

                    $('#custom_id')
                        .val(roleStart);

                    return;
                }

                $('#custom_id')
                    .val(roleStart);
            },

            error: function (xhr) {

                console.error(
                    'Auto User Code Preview Error:',
                    xhr.responseJSON ||
                    xhr.responseText
                );

                $('#custom_id')
                    .val(roleStart);
            }
        });
    }


    /* ============================================================
       MANUAL USER ID PREVIEW - MAIN FORM
       ============================================================

       IMPORTANT:

       এখানে Manual preview শুধুমাত্র Backend থেকে আসবে।

       Backend যদি 501 দেয়:
           501

       Backend যদি 801 দেয়:
           801

       Backend error হলে আর 101 দেখানো হবে না।

       ============================================================ */

    function manualFillId(
        userType,
        institutionId
    ) {

        if (!userType) {

            $('#custom_id')
                .val('')
                .attr(
                    'placeholder',
                    'প্রথমে ইউজার ধরণ নির্বাচন করুন'
                );

            return;
        }

        $('#custom_id')
            .val('লোড হচ্ছে...')
            .attr(
                'placeholder',
                'পরবর্তী Manual Code লোড হচ্ছে...'
            );

        $.ajax({

            url:
                '{{ route('dashboard.user.preview-id') }}',

            type: 'GET',

            data: {

                user_type: userType,

                institution_id: institutionId,

                generation_type: 'manual'
            },

            dataType: 'json',

            success: function (response) {

                console.log(
                    'Manual User Code Preview:',
                    response
                );

                if (
                    response &&
                    response.success &&
                    response.preview_id
                ) {

                    var previewId =
                        parseInt(
                            response.preview_id,
                            10
                        );

                    if (
                        !isNaN(previewId) &&
                        previewId >= getRoleStart(userType)
                    ) {

                        /*
                         * শুধু Preview হিসেবে দেখানো হচ্ছে।
                         * currentMainManualCode-এ save করা হচ্ছে না।
                         */
                        $('#custom_id')
                            .val(
                                response.preview_id
                            )
                            .attr(
                                'placeholder',
                                'পরবর্তী Manual Code'
                            );

                        return;
                    }
                }

                /*
                 * Backend valid code না দিলে
                 * ভুল করে 101/201/301/401 দেখাবো না।
                 */
                $('#custom_id')
                    .val('')
                    .attr(
                        'placeholder',
                        'Manual Code পাওয়া যায়নি'
                    );
            },

            error: function (xhr) {

                console.error(
                    'Manual User Code Preview Error:',
                    xhr.responseJSON ||
                    xhr.responseText
                );

                /*
                 * AJAX fail হলে ভুল fallback code
                 * দেখানো হবে না।
                 */
                $('#custom_id')
                    .val('')
                    .attr(
                        'placeholder',
                        'পরবর্তী Manual Code লোড করা যায়নি'
                    );
            }
        });
    }


    /* ============================================================
       MANUAL CODE PREVIEW - MODAL ONLY
       ============================================================ */

    function previewManualCodeInModal(
        userType,
        institutionId
    ) {

        if (!userType) {

            $('#manualPreviewInfo')
                .text('');

            return;
        }

        $('#manualPreviewInfo')
            .text(
                'পরবর্তী Manual Code লোড হচ্ছে...'
            );

        $.ajax({

            url:
                '{{ route('dashboard.user.preview-id') }}',

            type: 'GET',

            data: {

                user_type: userType,

                institution_id: institutionId,

                generation_type: 'manual'
            },

            dataType: 'json',

            success: function (response) {

                console.log(
                    'Manual Modal Preview:',
                    response
                );

                if (
                    response &&
                    response.success &&
                    response.preview_id
                ) {

                    var previewId =
                        parseInt(
                            response.preview_id,
                            10
                        );

                    if (
                        !isNaN(previewId) &&
                        previewId >= getRoleStart(userType)
                    ) {

                        $('#manualPreviewInfo')
                            .text(
                                'পরবর্তী Manual Code: ' +
                                response.preview_id
                            );

                        return;
                    }
                }

                /*
                 * Backend valid preview না দিলে
                 * আর 101/201/301/401 দেখাবে না।
                 */
                $('#manualPreviewInfo')
                    .text(
                        'পরবর্তী Manual Code পাওয়া যায়নি।'
                    );
            },

            error: function (xhr) {

                console.error(
                    'Manual Modal Preview Error:',
                    xhr.responseJSON ||
                    xhr.responseText
                );

                $('#manualPreviewInfo')
                    .text(
                        'পরবর্তী Manual Code লোড করা যায়নি।'
                    );
            }
        });
    }


    /* ============================================================
       MANUAL MODE MAIN FORM
       ============================================================ */

    function prepareManualMainId() {

        var userType =
            $('#user_type').val();

        if (!userType) {

            $('#custom_id')
                .val('');

            return;
        }

        /*
         * Explicit Manual Code থাকলে
         * সেটাই Main Form-এ থাকবে।
         */
        if (
            currentMainManualCodeExplicit &&
            currentMainManualCode
        ) {

            $('#custom_id')
                .val(currentMainManualCode)
                .attr(
                    'placeholder',
                    'ম্যানুয়াল কোড'
                );

            mainManualPreviewOnly = false;

            return;
        }

        /*
         * Blank Manual:
         *
         * Backend থেকে last saved manual + 1
         * preview হবে।
         */
        currentMainManualCode = '';

        currentMainManualCodeExplicit = false;

        mainManualPreviewOnly = true;

        manualFillId(
            userType,
            getInstitutionId()
        );
    }


    /* ============================================================
       USER TYPE CHANGE
       ============================================================ */

    $('#user_type').on(
        'change',
        function () {

            var userType =
                $(this).val();

            var institutionId =
                getInstitutionId();

            currentMainManualCode = '';

            currentMainManualCodeExplicit = false;

            mainManualPreviewOnly = false;


            if (userType === 'student') {

                $('#studentFields')
                    .addClass('show');

            } else {

                $('#studentFields')
                    .removeClass('show');
            }


            if (!userType) {

                $('#custom_id')
                    .val('');

                $('#codeModeHint')
                    .text('User Code');

                updateHiddenCodeType('auto');

                return;
            }


            var savedSetting =
                getSavedCodeSetting();


            /*
             * Saved setting একই role হলে
             * generation type ব্যবহার হবে।
             */
            if (
                savedSetting &&
                savedSetting.user_type === userType
            ) {

                if (
                    savedSetting.generation_type ===
                    'manual'
                ) {

                    $('input[name="code_generation_type"][value="manual"]')
                        .prop(
                            'checked',
                            true
                        );

                    updateHiddenCodeType(
                        'manual'
                    );

                    setManualModeUI();

                    $('#manual_user_code')
                        .val('');

                    prepareManualMainId();

                    return;
                }
            }


            /*
             * AUTO
             */

            $('input[name="code_generation_type"][value="auto"]')
                .prop(
                    'checked',
                    true
                );

            updateHiddenCodeType(
                'auto'
            );

            setAutoModeUI();

            autoFillId(
                userType,
                institutionId
            );
        }
    );


    /* ============================================================
       GENDER
       ============================================================ */

    $('input[name="gender"]').on(
        'change',
        function () {

            /*
             * Gender-এর সাথে ID generation-এর
             * কোনো সম্পর্ক নেই।
             */

        }
    );


    /* ============================================================
       INITIAL USER TYPE
       ============================================================ */

    if ($('#user_type').val()) {

        $('#user_type')
            .trigger('change');

    } else {

        var oldCodeType =
            $('#code_generation_type').val() ||
            'auto';

        if (oldCodeType === 'manual') {

            $('input[name="code_generation_type"][value="manual"]')
                .prop(
                    'checked',
                    true
                );

            updateHiddenCodeType('manual');

            setManualModeUI();

        } else {

            $('input[name="code_generation_type"][value="auto"]')
                .prop(
                    'checked',
                    true
                );

            updateHiddenCodeType('auto');

            setAutoModeUI();
        }
    }


    /* ============================================================
       CLASS -> SECTION
       ============================================================ */

    $('#class_id').on(
        'change',
        function () {

            var classId =
                $(this).val();

            var sectionSelect =
                $('#section_id');

            sectionSelect.html(
                '<option value="">শাখা নির্বাচন করুন</option>'
            );

            if (!classId) {
                return;
            }

            $.getJSON(

                '{{ url('/dashboard/user/sections') }}/' +
                classId,

                function (data) {

                    $.each(
                        data,
                        function (
                            index,
                            section
                        ) {

                            sectionSelect.append(

                                '<option value="' +
                                section.id +
                                '">' +
                                section.section_name +
                                '</option>'

                            );
                        }
                    );
                }
            );
        }
    );


    /* ============================================================
       ADDRESS AJAX
       ============================================================ */

    function loadDistricts(
        divisionId,
        districtSelect,
        upazilaSelect
    ) {

        $(districtSelect).html(
            '<option value="">জেলা নির্বাচন করুন</option>'
        );

        $(upazilaSelect).html(
            '<option value="">থানা নির্বাচন করুন</option>'
        );

        if (!divisionId) {
            return;
        }

        $.getJSON(

            '{{ url('/get-districts') }}/' +
            divisionId,

            function (data) {

                $.each(
                    data,
                    function (
                        key,
                        value
                    ) {

                        $(districtSelect).append(

                            '<option value="' +
                            value.id +
                            '">' +
                            value.name_bn +
                            '</option>'

                        );
                    }
                );
            }
        );
    }


    function loadUpazilas(
        districtId,
        upazilaSelect
    ) {

        $(upazilaSelect).html(
            '<option value="">থানা নির্বাচন করুন</option>'
        );

        if (!districtId) {
            return;
        }

        $.getJSON(

            '{{ url('/get-upazilas') }}/' +
            districtId,

            function (data) {

                $.each(
                    data,
                    function (
                        key,
                        value
                    ) {

                        $(upazilaSelect).append(

                            '<option value="' +
                            value.id +
                            '">' +
                            value.name_bn +
                            '</option>'

                        );
                    }
                );
            }
        );
    }


    /* ============================================================
       PERMANENT ADDRESS
       ============================================================ */

    $('#permanent_division').on(
        'change',
        function () {

            loadDistricts(
                $(this).val(),
                '#permanent_district',
                '#permanent_upazila'
            );
        }
    );


    $('#permanent_district').on(
        'change',
        function () {

            loadUpazilas(
                $(this).val(),
                '#permanent_upazila'
            );
        }
    );


    /* ============================================================
       PRESENT ADDRESS
       ============================================================ */

    $('#present_division').on(
        'change',
        function () {

            loadDistricts(
                $(this).val(),
                '#present_district',
                '#present_upazila'
            );
        }
    );


    $('#present_district').on(
        'change',
        function () {

            loadUpazilas(
                $(this).val(),
                '#present_upazila'
            );
        }
    );


    /* ============================================================
       SAME ADDRESS
       ============================================================ */

    $('#same_as_permanent').on(
        'change',
        function () {

            if (!$(this).is(':checked')) {
                return;
            }

            var pDiv =
                $('#permanent_division').val();

            var pDistrict =
                $('#permanent_district').val();

            var pUpazila =
                $('#permanent_upazila').val();


            $('#present_division')
                .val(pDiv);


            if (pDiv) {

                loadDistricts(
                    pDiv,
                    '#present_district',
                    '#present_upazila'
                );


                setTimeout(
                    function () {

                        $('#present_district')
                            .val(pDistrict);


                        if (pDistrict) {

                            loadUpazilas(
                                pDistrict,
                                '#present_upazila'
                            );


                            setTimeout(
                                function () {

                                    $('#present_upazila')
                                        .val(pUpazila);

                                },
                                300
                            );
                        }

                    },
                    300
                );
            }


            $('input[name="present_post_office"]')
                .val(
                    $('input[name="permanent_post_office"]')
                        .val()
                );


            $('input[name="present_village_road"]')
                .val(
                    $('input[name="permanent_village_road"]')
                        .val()
                );
        }
    );


    /* ============================================================
       LOAD OLD PERMANENT ADDRESS
       ============================================================ */

    if ($('#permanent_division').val()) {

        var oldPermanentDistrict =
            '{{ old('permanent_district_id') }}';

        var oldPermanentUpazila =
            '{{ old('permanent_upazila_id') }}';


        loadDistricts(
            $('#permanent_division').val(),
            '#permanent_district',
            '#permanent_upazila'
        );


        setTimeout(
            function () {

                if (oldPermanentDistrict) {

                    $('#permanent_district')
                        .val(oldPermanentDistrict);


                    loadUpazilas(
                        oldPermanentDistrict,
                        '#permanent_upazila'
                    );


                    setTimeout(
                        function () {

                            $('#permanent_upazila')
                                .val(
                                    oldPermanentUpazila
                                );

                        },
                        300
                    );
                }

            },
            300
        );
    }


    /* ============================================================
       LOAD OLD PRESENT ADDRESS
       ============================================================ */

    if ($('#present_division').val()) {

        var oldPresentDistrict =
            '{{ old('present_district_id') }}';

        var oldPresentUpazila =
            '{{ old('present_upazila_id') }}';


        loadDistricts(
            $('#present_division').val(),
            '#present_district',
            '#present_upazila'
        );


        setTimeout(
            function () {

                if (oldPresentDistrict) {

                    $('#present_district')
                        .val(oldPresentDistrict);


                    loadUpazilas(
                        oldPresentDistrict,
                        '#present_upazila'
                    );


                    setTimeout(
                        function () {

                            $('#present_upazila')
                                .val(
                                    oldPresentUpazila
                                );

                        },
                        300
                    );
                }

            },
            300
        );
    }


    /* ============================================================
       CODE SETTING MODAL
       ============================================================ */

    function openCodeSettingModal() {

        /*
         * Main form-এর বর্তমান setting snapshot।
         */
        modalOriginalSetting =
            getCurrentMainCodeSetting();

        modalManualCodeTouched = false;


        var mainUserType =
            modalOriginalSetting.user_type;


        if (mainUserType) {

            $('#code_setting_user_type')
                .val(mainUserType);

        } else {

            $('#code_setting_user_type')
                .val('');
        }


        var currentGenerationType =
            modalOriginalSetting.generation_type ||
            'auto';


        $('input[name="code_generation_type"]')
            .prop(
                'checked',
                false
            );


        $(
            'input[name="code_generation_type"][value="' +
            currentGenerationType +
            '"]'
        )
            .prop(
                'checked',
                true
            );


        /*
         * শুধুমাত্র explicit main manual code।
         */
        $('#manual_user_code')
            .val(
                modalOriginalSetting.manual_code || ''
            );


        if (
            currentGenerationType === 'manual'
        ) {

            $('#manualCodeField')
                .addClass('show');

            $('#manualPreviewInfo')
                .addClass('show');

            $('#autoCodeInfo')
                .hide();


            $('#manual_user_code')
                .attr(
                    'placeholder',
                    'খালি রাখলে ' +
                    getRoleStart(mainUserType) +
                    ' থেকে পরবর্তী কোড হবে'
                );


            /*
             * Explicit code না থাকলে
             * Modal-এ শুধু backend preview।
             */
            if (
                !modalOriginalSetting.manual_code &&
                mainUserType
            ) {

                previewManualCodeInModal(
                    mainUserType,
                    getInstitutionId()
                );

            } else {

                $('#manualPreviewInfo')
                    .text(
                        'Manual Code: ' +
                        modalOriginalSetting.manual_code
                    );
            }

        } else {

            $('#manualCodeField')
                .removeClass('show');

            $('#manualPreviewInfo')
                .removeClass('show');

            $('#autoCodeInfo')
                .show();
        }


        $('#codeSettingModal')
            .addClass('show');

        $('body').css(
            'overflow',
            'hidden'
        );
    }


    function closeCodeSettingModal() {

        $('#codeSettingModal')
            .removeClass('show');

        $('body').css(
            'overflow',
            ''
        );

        modalManualCodeTouched = false;
    }


    $('#openCodeSetting').on(
        'click',
        function () {

            openCodeSettingModal();
        }
    );


    $('#closeCodeSetting').on(
        'click',
        function () {

            closeCodeSettingModal();
        }
    );


    $('#cancelCodeSetting').on(
        'click',
        function () {

            /*
             * Cancel করলে Main Form untouched।
             */
            closeCodeSettingModal();
        }
    );


    /* ============================================================
       CLICK OUTSIDE MODAL
       ============================================================ */

    $('#codeSettingModal').on(
        'click',
        function (e) {

            if (e.target === this) {

                closeCodeSettingModal();
            }
        }
    );


    /* ============================================================
       ESC
       ============================================================ */

    $(document).on(
        'keydown',
        function (e) {

            if (
                e.key === 'Escape' &&
                $('#codeSettingModal')
                    .hasClass('show')
            ) {

                closeCodeSettingModal();
            }
        }
    );


    /* ============================================================
       MODAL USER TYPE CHANGE
       ============================================================ */

    $('#code_setting_user_type').on(
        'change',
        function () {

            var userType =
                $(this).val();


            /*
             * Modal role change হলে
             * Main Form touch হবে না।
             */

            $('#code_auto')
                .prop(
                    'checked',
                    true
                );


            $('#manual_user_code')
                .val('');


            $('#manualCodeField')
                .removeClass('show');

            $('#manualPreviewInfo')
                .removeClass('show');

            $('#autoCodeInfo')
                .show();


            modalManualCodeTouched = false;


            if (userType) {

                $('#manual_user_code')
                    .attr(
                        'placeholder',
                        'খালি রাখলে ' +
                        getRoleStart(userType) +
                        ' থেকে পরবর্তী কোড হবে'
                    );
            }
        }
    );


    /* ============================================================
       MODAL CODE GENERATION TYPE
       ============================================================ */

    $('input[name="code_generation_type"]')
        .on(
            'change',
            function () {

                var type =
                    $(this).val();

                var modalUserType =
                    $('#code_setting_user_type')
                        .val();


                /*
                 * Main Form-এর কোনো value এখানে
                 * পরিবর্তন হবে না।
                 */

                if (type === 'manual') {

                    $('#manualCodeField')
                        .addClass('show');

                    $('#manualPreviewInfo')
                        .addClass('show');

                    $('#autoCodeInfo')
                        .hide();


                    $('#manual_user_code')
                        .attr(
                            'placeholder',
                            'খালি রাখলে ' +
                            getRoleStart(modalUserType) +
                            ' থেকে পরবর্তী কোড হবে'
                        );


                    if (
                        modalUserType &&
                        !$.trim(
                            $('#manual_user_code').val()
                        )
                    ) {

                        modalManualCodeTouched = false;


                        previewManualCodeInModal(
                            modalUserType,
                            getInstitutionId()
                        );
                    }

                } else {

                    $('#manualCodeField')
                        .removeClass('show');

                    $('#manualPreviewInfo')
                        .removeClass('show');

                    $('#autoCodeInfo')
                        .show();


                    $('#manual_user_code')
                        .val('');

                    modalManualCodeTouched = false;
                }
            }
        );


    /* ============================================================
       MANUAL CODE INPUT
       ============================================================ */

    $('#manual_user_code').on(
        'input',
        function () {

            var value =
                $(this).val();


            value =
                value.replace(
                    /[^0-9]/g,
                    ''
                );


            $(this).val(value);


            modalManualCodeTouched = true;


            if (value) {

                $('#manualPreviewInfo')
                    .text(
                        'Manual Code: ' +
                        value
                    );

                return;
            }


            /*
             * Blank করলে Modal-এ শুধু
             * next Manual preview।
             */
            var modalUserType =
                $('#code_setting_user_type')
                    .val();


            if (modalUserType) {

                modalManualCodeTouched = false;


                previewManualCodeInModal(
                    modalUserType,
                    getInstitutionId()
                );
            }
        }
    );


    /* ============================================================
       SAVE CODE SETTING
       ============================================================ */

    $('#saveCodeSetting').on(
        'click',
        function () {

            var userType =
                $('#code_setting_user_type')
                    .val();


            var generationType =
                $(
                    'input[name="code_generation_type"]:checked'
                )
                .val();


            var manualCode =
                $.trim(
                    $('#manual_user_code').val()
                );


            /* ROLE VALIDATION */

            if (!userType) {

                alert(
                    'অনুগ্রহ করে ব্যবহারকারীর ধরণ নির্বাচন করুন।'
                );

                return;
            }


            /* GENERATION TYPE */

            if (!generationType) {
                generationType = 'auto';
            }


            /* MANUAL VALIDATION */

            if (
                generationType === 'manual'
            ) {

                if (manualCode) {

                    if (!/^\d+$/.test(manualCode)) {

                        alert(
                            'ম্যানুয়াল কোড শুধুমাত্র সংখ্যা হতে হবে।'
                        );

                        $('#manual_user_code')
                            .focus();

                        return;
                    }


                    var roleStart =
                        getRoleStart(userType);


                    if (
                        parseInt(
                            manualCode,
                            10
                        ) < roleStart
                    ) {

                        alert(
                            'এই Role-এর Manual Code ' +
                            roleStart +
                            ' বা তার বেশি হতে হবে।'
                        );

                        $('#manual_user_code')
                            .focus();

                        return;
                    }
                }
            }


            /* ====================================================
               APPLY TO MAIN FORM ONLY AFTER SAVE
               ==================================================== */

            var mainUserType =
                $('#user_type').val();


            if (
                mainUserType !== userType
            ) {

                $('#user_type')
                    .val(userType);
            }


            /* SAVE GENERATION TYPE */

            updateHiddenCodeType(
                generationType
            );


            /* ====================================================
               MANUAL
               ==================================================== */

            if (
                generationType === 'manual'
            ) {

                setManualModeUI();


                if (manualCode) {

                    /*
                     * Explicit Manual Code
                     *
                     * Example:
                     * 500
                     */
                    currentMainManualCode =
                        manualCode;

                    currentMainManualCodeExplicit =
                        true;

                    mainManualPreviewOnly =
                        false;


                    $('#custom_id')
                        .val(manualCode)
                        .attr(
                            'placeholder',
                            'ম্যানুয়াল কোড'
                        );


                    $('#manual_user_code')
                        .val(manualCode);


                } else {

                    /*
                     * Blank Manual
                     *
                     * Backend থেকে:
                     *
                     * last saved manual + 1
                     *
                     * preview হবে।
                     */
                    currentMainManualCode =
                        '';

                    currentMainManualCodeExplicit =
                        false;

                    mainManualPreviewOnly =
                        true;


                    $('#manual_user_code')
                        .val('');


                    manualFillId(
                        userType,
                        getInstitutionId()
                    );
                }


            } else {

                /* =================================================
                   AUTO
                   ================================================= */

                currentMainManualCode =
                    '';

                currentMainManualCodeExplicit =
                    false;

                mainManualPreviewOnly =
                    false;


                $('#manual_user_code')
                    .val('');


                setAutoModeUI();


                autoFillId(
                    userType,
                    getInstitutionId()
                );
            }


            /* ====================================================
               SESSION STORAGE
               ==================================================== */

            /*
             * Manual code sessionStorage-এ রাখা হবে না।
             */
            var settings = {

                user_type:
                    userType,

                generation_type:
                    generationType
            };


            sessionStorage.setItem(
                'user_code_setting',
                JSON.stringify(settings)
            );


            /* CLOSE MODAL */

            closeCodeSettingModal();


            /* MESSAGE */

            alert(

                generationType === 'manual'

                    ? (
                        manualCode
                            ? 'Manual Code Setting সংরক্ষণ করা হয়েছে।'
                            : 'Manual Code Setting সংরক্ষণ করা হয়েছে। পরবর্তী Manual Code Backend থেকে তৈরি হবে।'
                    )

                    : 'Auto Code Setting সংরক্ষণ করা হয়েছে.'
            );
        }
    );


    /* ============================================================
       RESTORE CODE SETTING
       ============================================================ */

    try {

        var setting =
            getSavedCodeSetting();


        if (setting) {

            /*
             * Modal role
             */
            if (setting.user_type) {

                $('#code_setting_user_type')
                    .val(
                        setting.user_type
                    );
            }


            /*
             * Generation type
             */
            if (setting.generation_type) {

                $(
                    'input[name="code_generation_type"][value="' +
                    setting.generation_type +
                    '"]'
                )
                    .prop(
                        'checked',
                        true
                    );


                if (
                    $('#user_type').val() &&
                    $('#user_type').val() ===
                    setting.user_type
                ) {

                    updateHiddenCodeType(
                        setting.generation_type
                    );


                    if (
                        setting.generation_type ===
                        'manual'
                    ) {

                        setManualModeUI();

                    } else {

                        setAutoModeUI();
                    }
                }
            }


            /*
             * Manual code কখনো sessionStorage
             * থেকে restore হবে না।
             */
            $('#manual_user_code')
                .val('');


            /*
             * Main form-এর current role saved role হলে
             * setting apply হবে।
             */
            if (
                $('#user_type').val() &&
                $('#user_type').val() ===
                setting.user_type
            ) {

                /*
                 * MANUAL
                 */
                if (
                    setting.generation_type ===
                    'manual'
                ) {

                    updateHiddenCodeType(
                        'manual'
                    );


                    currentMainManualCode =
                        '';

                    currentMainManualCodeExplicit =
                        false;

                    mainManualPreviewOnly =
                        true;


                    setManualModeUI();


                    /*
                     * এখানে Backend থেকে
                     * last saved manual + 1
                     * preview হবে।
                     */
                    manualFillId(
                        setting.user_type,
                        getInstitutionId()
                    );


                } else {

                    /*
                     * AUTO
                     */
                    updateHiddenCodeType(
                        'auto'
                    );


                    currentMainManualCode =
                        '';

                    currentMainManualCodeExplicit =
                        false;

                    mainManualPreviewOnly =
                        false;


                    setAutoModeUI();


                    autoFillId(
                        $('#user_type').val(),
                        getInstitutionId()
                    );
                }
            }
        }

    } catch (e) {

        console.error(
            'Code setting restore error:',
            e
        );
    }


    /* ============================================================
       BEFORE FORM SUBMIT
       ============================================================ */

    $('#userCreateForm').on(
        'submit',
        function (e) {

            var userType =
                $('#user_type').val();


            var generationType =
                $('#code_generation_type').val() ||
                'auto';


            /*
             * USER TYPE VALIDATION
             */
            if (!userType) {

                e.preventDefault();

                alert(
                    'অনুগ্রহ করে ব্যবহারকারীর ধরণ নির্বাচন করুন।'
                );

                return false;
            }


            /* ====================================================
               AUTO
               ==================================================== */

            if (
                generationType === 'auto'
            ) {

                /*
                 * Preview value submit হবে না।
                 * Backend Auto code তৈরি করবে।
                 */
                $('#custom_id')
                    .val('');

                return true;
            }


            /* ====================================================
               MANUAL + EXPLICIT CODE
               ==================================================== */

            if (
                generationType === 'manual' &&
                currentMainManualCodeExplicit &&
                currentMainManualCode
            ) {

                var manualCode =
                    $.trim(
                        currentMainManualCode
                    );


                if (!/^\d+$/.test(manualCode)) {

                    e.preventDefault();

                    alert(
                        'ম্যানুয়াল কোড শুধুমাত্র সংখ্যা হতে হবে।'
                    );

                    return false;
                }


                var roleStart =
                    getRoleStart(userType);


                if (
                    parseInt(
                        manualCode,
                        10
                    ) < roleStart
                ) {

                    e.preventDefault();

                    alert(
                        'এই Role-এর Manual Code ' +
                        roleStart +
                        ' বা তার বেশি হতে হবে।'
                    );

                    return false;
                }


                /*
                 * User যে Manual code Save করেছে
                 * সেটাই submit হবে।
                 */
                $('#custom_id')
                    .val(manualCode);

                return true;
            }


            /* ====================================================
               MANUAL + BLANK
               ==================================================== */

            if (
                generationType === 'manual'
            ) {

                /*
                 * Main form-এ preview হিসেবে
                 * 501 / 801 দেখা গেলেও সেটা
                 * database-এ submit হবে না।
                 *
                 * Controller:
                 *
                 * generateManualCode()
                 *
                 * ব্যবহার করে last saved Manual + 1
                 * তৈরি করবে।
                 */
                $('#custom_id')
                    .val('');

                return true;
            }


            return true;
        }
    );

});

</script>

@endpush


@endsection