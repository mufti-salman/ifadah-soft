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

    <!-- Step 1: User Type -->
    <div class="code-form-section" id="codeUserTypeSection">
        <div class="code-section-title">ব্যবহারকারীর ধরণ:</div>

        <select id="code_setting_user_type" class="form-select">
            <option value="">নির্বাচন করুন</option>
            <option value="student">শিক্ষার্থী</option>
            <option value="user">ইউজার</option>
        </select>
    </div>

    <!-- Step 2: Generation Type -->
    <div class="code-form-section" id="codeGenerationTypeSection" style="display:none;">
        <div class="code-section-title">ধরন:</div>

        <div class="code-radio-group">
            <div class="code-radio-card">
                <input type="radio"
                       name="code_generation_type"
                       id="code_auto"
                       value="auto">

                <label for="code_auto">
                    <i class="fas fa-magic"></i> নির্দিষ্ট কোড হতে অটো
                </label>
            </div>

            <div class="code-radio-card">
                <input type="radio"
                       name="code_generation_type"
                       id="code_manual"
                       value="manual">

                <label for="code_manual">
                    <i class="fas fa-keyboard"></i> ম্যানুয়াল
                </label>
            </div>
        </div>
    </div>

    <!-- Step 3: User Code -->
    <div class="code-form-section" id="codeInputSection" style="display:none;">
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
    </div>

    <!-- Gender -->
    <div class="code-form-section">
        <div class="code-section-title">লিঙ্গ:</div>

        <div class="code-radio-group">
            <div class="code-radio-card">
                <input type="radio" name="code_setting_gender"
                       id="code_gender_male" value="male">
                <label for="code_gender_male">পুরুষ</label>
            </div>

            <div class="code-radio-card">
                <input type="radio" name="code_setting_gender"
                       id="code_gender_female" value="female">
                <label for="code_gender_female">মহিলা</label>
            </div>

            <div class="code-radio-card">
                <input type="radio" name="code_setting_gender"
                       id="code_gender_entry" value="entry">
                <label for="code_gender_entry">এন্ট্রির মাধ্যমে</label>
            </div>
        </div>
    </div>

    <div class="code-modal-footer">
        <button type="button" class="btn-modal-cancel" id="cancelCodeSetting">
            বাতিল
        </button>

        <button type="button" class="btn-modal-save" id="saveCodeSetting">
            <i class="fas fa-save"></i> সংরক্ষণ করুন
        </button>
    </div>

</div>

</div>


</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    'use strict';

    /* ============================================================
       CONFIGURATION
       ============================================================ */

    const codeStartMap = {
        student: 101,
        user: 201,
        user_admin: 301,
        support_admin: 401
    };

    const codeSettingsGetUrl =
        @json(route('dashboard.user.code-settings.get'));

    const codeSettingsSaveUrl =
        @json(route('dashboard.user.code-settings.save'));

    const previewIdUrl =
        @json(route('dashboard.user.preview-id'));

    const csrfToken =
        $('meta[name="csrf-token"]').attr('content');

    let savedCodeSettings = {};

    let currentMainManualCode = '';

    let currentMainManualCodeExplicit = false;

    let mainManualPreviewOnly = false;

    let modalOriginalSetting = null;

    let modalManualCodeTouched = false;

    let codeSettingRequestNumber = 0;


    /* ============================================================
       AGE CALCULATE
       ============================================================ */

    function calculateAge() {
        const birthDate = $('#date_of_birth').val();

        if (!birthDate) {
            $('#age').val('');
            return;
        }

        const today = new Date();
        const birth = new Date(birthDate);

        if (birth > today) {
            $('#age').val('');

            alert('জন্ম তারিখ ভবিষ্যতের হতে পারে না!');

            $('#date_of_birth').val('');

            return;
        }

        let age =
            today.getFullYear() -
            birth.getFullYear();

        const monthDifference =
            today.getMonth() -
            birth.getMonth();

        if (
            monthDifference < 0 ||
            (
                monthDifference === 0 &&
                today.getDate() < birth.getDate()
            )
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
       INSTITUTION ID
       ============================================================ */

    function getInstitutionId() {
        return $('#institution_id').val() || '';
    }


    /* ============================================================
       USER TYPE
       ============================================================ */

    function getCurrentUserType() {
        return $('#user_type').val() || '';
    }

    function getModalUserType() {
        return $('#code_setting_user_type').val() || '';
    }

    function getSelectedGenerationType() {
    return $('input[name="code_generation_type"]:checked').val() || '';
}

    function getRoleStart(userType) {
        const saved = savedCodeSettings[userType];

        if (
            saved &&
            Number.isInteger(Number(saved.start_code)) &&
            Number(saved.start_code) > 0
        ) {
            return Number(saved.start_code);
        }

        return codeStartMap[userType] || 101;
    }


    /* ============================================================
       SESSION STORAGE
       ============================================================ */

    function getSavedCodeSetting() {
        try {
            const value =
                sessionStorage.getItem('user_code_setting');

            return value ? JSON.parse(value) : null;
        } catch (error) {
            console.error('Code setting parse error:', error);
            return null;
        }
    }

    function saveCodeSettingToSession(setting) {
        try {
            sessionStorage.setItem(
                'user_code_setting',
                JSON.stringify(setting)
            );
        } catch (error) {
            console.error('Code setting session error:', error);
        }
    }

    function getSavedManualCode() {
        try {
            const value =
                sessionStorage.getItem('user_manual_code_setting');

            return value ? JSON.parse(value) : null;
        } catch (error) {
            console.error('Manual code restore error:', error);
            return null;
        }
    }

    function saveManualCode(userType, institutionId, manualCode) {
        const data = {
            user_type: userType,
            institution_id: String(institutionId || ''),
            manual_code: String(manualCode || '')
        };

        sessionStorage.setItem(
            'user_manual_code_setting',
            JSON.stringify(data)
        );
    }

    function clearSavedManualCode() {
        sessionStorage.removeItem('user_manual_code_setting');
    }

    function restoreSavedManualCode(userType, institutionId) {
        const saved = getSavedManualCode();

        if (!saved) {
            return false;
        }

        if (
            saved.user_type !== userType ||
            saved.institution_id !== String(institutionId || '') ||
            !saved.manual_code
        ) {
            return false;
        }

        currentMainManualCode = String(saved.manual_code);
        currentMainManualCodeExplicit = true;
        mainManualPreviewOnly = false;

        $('#custom_id').val(currentMainManualCode);

        $('#manual_user_code').val(currentMainManualCode);

        return true;
    }


    /* ============================================================
       MAIN FORM CODE TYPE
       ============================================================ */

    function updateHiddenCodeType(type) {
        $('#code_generation_type').val(type || 'auto');
    }

    function getCurrentMainCodeSetting() {
        return {
            user_type: getCurrentUserType(),
            generation_type:
                $('#code_generation_type').val() || 'auto',
            manual_code: currentMainManualCode || ''
        };
    }


    /* ============================================================
       AUTO MODE UI
       ============================================================ */

    function setAutoModeUI() {
        $('#manualCodeField').removeClass('show');
        $('#manualPreviewInfo').removeClass('show');
        $('#autoCodeInfo').show();

        $('#codeModeHint')
            .text('Auto Code')
            .css('color', '#6b7280');
    }


    /* ============================================================
       MANUAL MODE UI
       ============================================================ */

    function setManualModeUI() {
        const userType = getCurrentUserType();

        $('#manualCodeField').addClass('show');
        $('#manualPreviewInfo').addClass('show');
        $('#autoCodeInfo').hide();

        $('#manual_user_code').attr(
            'placeholder',
            'খালি রাখলে পরবর্তী Manual Code তৈরি হবে'
        );

        $('#codeModeHint')
            .text('Manual Code')
            .css('color', '#047857');
    }


    /* ============================================================
       LOAD SETTINGS FROM DATABASE
       ============================================================ */

    function loadUserCodeSettings() {
        return $.ajax({
            url: codeSettingsGetUrl,
            type: 'GET',
            dataType: 'json',
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

    function loadSettingForRole(userType) {
        const requestNumber = ++codeSettingRequestNumber;

        if (!userType) {
            $('#setting_user_code').val('');
            return;
        }

        $('#setting_user_code').val('');

        loadUserCodeSettings()
            .done(function (settings) {
                if (requestNumber !== codeSettingRequestNumber) {
                    return;
                }

                const setting = settings[userType];

                $('#setting_user_code').val(
                    setting
                        ? setting.start_code
                        : (codeStartMap[userType] || 101)
                );
            })
            .fail(function (xhr) {
                if (requestNumber !== codeSettingRequestNumber) {
                    return;
                }

                console.error(
                    'Load User Code Setting Error:',
                    xhr.responseJSON || xhr.responseText
                );

                $('#setting_user_code').val(
                    codeStartMap[userType] || 101
                );
            });
    }


    /* ============================================================
       AUTO ID PREVIEW
       ============================================================ */

    function autoFillId(userType, institutionId) {
        if (!userType) {
            $('#custom_id')
                .val('')
                .attr('placeholder', 'প্রথমে ইউজার ধরণ নির্বাচন করুন');

            return;
        }

        $('#custom_id')
            .val('লোড হচ্ছে...')
            .attr('placeholder', 'অটো কোড লোড হচ্ছে...');

        $.ajax({
            url: previewIdUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                user_type: userType,
                institution_id: institutionId,
                generation_type: 'auto'
            },
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .done(function (response) {
            console.log('Auto User Code Preview:', response);

            if (
                response &&
                response.success &&
                response.preview_id !== undefined &&
                response.preview_id !== null
            ) {
                $('#custom_id')
                    .val(response.preview_id)
                    .attr('placeholder', 'Auto Code Preview');

                return;
            }

            $('#custom_id')
                .val('')
                .attr('placeholder', 'Auto Code পাওয়া যায়নি');
        })
        .fail(function (xhr) {
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
       MANUAL ID PREVIEW - MAIN FORM
       ============================================================ */

    function manualFillId(userType, institutionId) {
        if (!userType) {
            $('#custom_id')
                .val('')
                .attr('placeholder', 'প্রথমে ইউজার ধরণ নির্বাচন করুন');

            return;
        }

        $('#custom_id')
            .val('লোড হচ্ছে...')
            .attr('placeholder', 'পরবর্তী Manual Code লোড হচ্ছে...');

        $.ajax({
            url: previewIdUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                user_type: userType,
                institution_id: institutionId,
                generation_type: 'manual'
            },
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .done(function (response) {
            console.log('Manual User Code Preview:', response);

            if (
                response &&
                response.success &&
                response.preview_id !== undefined &&
                response.preview_id !== null
            ) {
                $('#custom_id')
                    .val(response.preview_id)
                    .attr('placeholder', 'পরবর্তী Manual Code');

                mainManualPreviewOnly = true;

                return;
            }

            $('#custom_id')
                .val('')
                .attr('placeholder', 'Manual Code পাওয়া যায়নি');
        })
        .fail(function (xhr) {
            console.error(
                'Manual User Code Preview Error:',
                xhr.responseJSON || xhr.responseText
            );

            $('#custom_id')
                .val('')
                .attr(
                    'placeholder',
                    'পরবর্তী Manual Code লোড করা যায়নি'
                );
        });
    }


    /* ============================================================
       MANUAL ID PREVIEW - MODAL
       ============================================================ */

    function previewManualCodeInModal(userType, institutionId) {
        if (!userType) {
            $('#manualPreviewInfo').text('');
            return;
        }

        $('#manualPreviewInfo')
            .text('পরবর্তী Manual Code লোড হচ্ছে...');

        $.ajax({
            url: previewIdUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                user_type: userType,
                institution_id: institutionId,
                generation_type: 'manual'
            },
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .done(function (response) {
            console.log('Manual Modal Preview:', response);

            if (
                response &&
                response.success &&
                response.preview_id !== undefined &&
                response.preview_id !== null
            ) {
                $('#manualPreviewInfo').text(
                    'পরবর্তী Manual Code: ' + response.preview_id
                );

                return;
            }

            $('#manualPreviewInfo').text(
                'পরবর্তী Manual Code পাওয়া যায়নি।'
            );
        })
        .fail(function (xhr) {
            console.error(
                'Manual Modal Preview Error:',
                xhr.responseJSON || xhr.responseText
            );

$('#setting_user_code_help').text(
    'পরবর্তী Manual Code: ' + response.preview_id
);
        });
    }


    /* ============================================================
       PREPARE MANUAL MAIN FORM
       ============================================================ */

    function prepareManualMainId() {
        const userType = getCurrentUserType();
        const institutionId = getInstitutionId();

        if (!userType) {
            $('#custom_id').val('');
            return;
        }

        if (
            currentMainManualCodeExplicit &&
            currentMainManualCode
        ) {
            $('#custom_id').val(currentMainManualCode);
            return;
        }

        if (restoreSavedManualCode(userType, institutionId)) {
            $('#custom_id').val(currentMainManualCode);
            return;
        }

        currentMainManualCode = '';
        currentMainManualCodeExplicit = false;
        mainManualPreviewOnly = true;

        manualFillId(userType, institutionId);
    }


    /* ============================================================
       USER TYPE CHANGE - MAIN FORM
       ============================================================ */

    $('#user_type')
        .off('change.userCode')
        .on('change.userCode', function () {
            const userType = $(this).val();
            const institutionId = getInstitutionId();

            currentMainManualCode = '';
            currentMainManualCodeExplicit = false;
            mainManualPreviewOnly = false;

            if (userType === 'student') {
                $('#studentFields').addClass('show');
            } else {
                $('#studentFields').removeClass('show');
            }

            if (!userType) {
                $('#custom_id').val('');
                updateHiddenCodeType('auto');
                setAutoModeUI();
                return;
            }

            const setting = getSavedCodeSetting();

            if (
                setting &&
                setting.user_type === userType &&
                setting.generation_type === 'manual'
            ) {
                updateHiddenCodeType('manual');
                setManualModeUI();
                prepareManualMainId();
                return;
            }

            updateHiddenCodeType('auto');
            setAutoModeUI();
            autoFillId(userType, institutionId);
        });


    /* ============================================================
       GENERATION TYPE CHANGE - MAIN FORM
       ============================================================ */

    $('#code_generation_type')
        .off('change.userCode')
        .on('change.userCode', function () {
            const type = $(this).val();

            if (type === 'manual') {
                setManualModeUI();
                prepareManualMainId();
            } else {
                currentMainManualCode = '';
                currentMainManualCodeExplicit = false;
                mainManualPreviewOnly = false;

                setAutoModeUI();

                autoFillId(
                    getCurrentUserType(),
                    getInstitutionId()
                );
            }
        });


    /* ============================================================
       AGE AND GENDER
       ============================================================ */

    $('input[name="gender"]')
        .off('change.userGender')
        .on('change.userGender', function () {
            // Gender selection does not affect code generation.
        });


    /* ============================================================
       CLASS -> SECTION AJAX
       ============================================================ */

    $('#class_id')
        .off('change.userSection')
        .on('change.userSection', function () {
            const classId = $(this).val();

            const sectionSelect = $('#section_id');

            sectionSelect.html(
                '<option value="">শাখা নির্বাচন করুন</option>'
            );

            if (!classId) {
                return;
            }

            $.getJSON(
                '{{ url('/dashboard/user/sections') }}/' + classId
            )
            .done(function (data) {
                $.each(data, function (index, section) {
                    sectionSelect.append(
                        $('<option>', {
                            value: section.id,
                            text: section.section_name
                        })
                    );
                });
            })
            .fail(function (xhr) {
                console.error(
                    'Section Load Error:',
                    xhr.responseText
                );
            });
        });


    /* ============================================================
       ADDRESS AJAX HELPERS
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
            '{{ url('/get-districts') }}/' + divisionId
        )
        .done(function (data) {
            $.each(data, function (key, value) {
                $(districtSelect).append(
                    $('<option>', {
                        value: value.id,
                        text: value.name_bn
                    })
                );
            });
        })
        .fail(function (xhr) {
            console.error(
                'District Load Error:',
                xhr.responseText
            );
        });
    }

    function loadUpazilas(districtId, upazilaSelect) {
        $(upazilaSelect).html(
            '<option value="">থানা নির্বাচন করুন</option>'
        );

        if (!districtId) {
            return;
        }

        $.getJSON(
            '{{ url('/get-upazilas') }}/' + districtId
        )
        .done(function (data) {
            $.each(data, function (key, value) {
                $(upazilaSelect).append(
                    $('<option>', {
                        value: value.id,
                        text: value.name_bn
                    })
                );
            });
        })
        .fail(function (xhr) {
            console.error(
                'Upazila Load Error:',
                xhr.responseText
            );
        });
    }


    /* ============================================================
       PERMANENT ADDRESS
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


    /* ============================================================
       PRESENT ADDRESS
       ============================================================ */

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
        .off('change.userAddress')
        .on('change.userAddress', function () {
            if (!$(this).is(':checked')) {
                return;
            }

            const permanentDivision =
                $('#permanent_division').val();

            const permanentDistrict =
                $('#permanent_district').val();

            const permanentUpazila =
                $('#permanent_upazila').val();

            $('#present_division').val(permanentDivision);

            if (permanentDivision) {
                loadDistricts(
                    permanentDivision,
                    '#present_district',
                    '#present_upazila'
                );

                setTimeout(function () {
                    $('#present_district').val(permanentDistrict);

                    if (permanentDistrict) {
                        loadUpazilas(
                            permanentDistrict,
                            '#present_upazila'
                        );

                        setTimeout(function () {
                            $('#present_upazila').val(
                                permanentUpazila
                            );
                        }, 300);
                    }
                }, 300);
            }

            $('input[name="present_post_office"]').val(
                $('input[name="permanent_post_office"]').val()
            );

            $('input[name="present_village_road"]').val(
                $('input[name="permanent_village_road"]').val()
            );
        });


    /* ============================================================
       RESTORE OLD ADDRESS VALUES
       ============================================================ */

    const oldPermanentDistrict =
        @json(old('permanent_district_id'));

    const oldPermanentUpazila =
        @json(old('permanent_upazila_id'));

    if ($('#permanent_division').val()) {
        loadDistricts(
            $('#permanent_division').val(),
            '#permanent_district',
            '#permanent_upazila'
        );

        setTimeout(function () {
            if (oldPermanentDistrict) {
                $('#permanent_district').val(
                    oldPermanentDistrict
                );

                loadUpazilas(
                    oldPermanentDistrict,
                    '#permanent_upazila'
                );

                setTimeout(function () {
                    $('#permanent_upazila').val(
                        oldPermanentUpazila
                    );
                }, 300);
            }
        }, 300);
    }

    const oldPresentDistrict =
        @json(old('present_district_id'));

    const oldPresentUpazila =
        @json(old('present_upazila_id'));

    if ($('#present_division').val()) {
        loadDistricts(
            $('#present_division').val(),
            '#present_district',
            '#present_upazila'
        );

        setTimeout(function () {
            if (oldPresentDistrict) {
                $('#present_district').val(
                    oldPresentDistrict
                );

                loadUpazilas(
                    oldPresentDistrict,
                    '#present_upazila'
                );

                setTimeout(function () {
                    $('#present_upazila').val(
                        oldPresentUpazila
                    );
                }, 300);
            }
        }, 300);
    }

/* ============================================================
   CODE SETTING MODAL OPEN / CLOSE
   ============================================================ */

function resetCodeSettingModal() {

    $('#code_setting_user_type').val('');

    $('input[name="code_generation_type"]')
        .prop('checked', false);

    $('#setting_user_code').val('');

    $('#setting_user_code_help').text(
        'প্রথমে ব্যবহারকারীর ধরণ নির্বাচন করুন।'
    );

    $('#codeGenerationTypeSection').hide();

    $('#codeInputSection').hide();

    modalManualCodeTouched = false;
}


function openCodeSettingModal() {

    modalOriginalSetting = getCurrentMainCodeSetting();

    modalManualCodeTouched = false;

    resetCodeSettingModal();

    const userType =
        modalOriginalSetting.user_type || getCurrentUserType();

    if (userType) {

        $('#code_setting_user_type').val(userType);

        $('#codeGenerationTypeSection').show();

        const generationType =
            modalOriginalSetting.generation_type || 'auto';

        $(
            'input[name="code_generation_type"][value="' +
            generationType +
            '"]'
        ).prop('checked', true);

        $('#codeInputSection').show();

        if (generationType === 'auto') {

            loadSettingForRole(userType);

            $('#setting_user_code_help').text(
                'অটো জেনারেশনের প্রাথমিক কোড। প্রয়োজনে পরিবর্তন করতে পারবেন।'
            );

        } else {

            const manualCode =
                modalOriginalSetting.manual_code || '';

            $('#setting_user_code').val(manualCode);

            $('#setting_user_code').attr(
                'placeholder',
                'ম্যানুয়াল কোড লিখুন অথবা ফাঁকা রাখুন'
            );

            $('#setting_user_code_help').text(
                'নিজের পছন্দমতো Manual Code লিখুন।'
            );

            if (!manualCode) {

                previewManualCodeInModal(
                    userType,
                    getInstitutionId()
                );
            }
        }
    }

    $('#codeSettingModal').addClass('show');

    $('body').css('overflow', 'hidden');

    console.log('Code Setting Modal Opened');
}


function closeCodeSettingModal() {

    $('#codeSettingModal').removeClass('show');

    $('body').css('overflow', '');

    modalManualCodeTouched = false;
}


// OPEN BUTTON
$(document)
    .off('click.userCodeModal', '#openCodeSetting')
    .on('click.userCodeModal', '#openCodeSetting', function (event) {

        event.preventDefault();
        event.stopPropagation();

        openCodeSettingModal();
    });


// CLOSE AND CANCEL BUTTON
$(document)
    .off(
        'click.userCodeModal',
        '#closeCodeSetting, #cancelCodeSetting'
    )
    .on(
        'click.userCodeModal',
        '#closeCodeSetting, #cancelCodeSetting',
        function (event) {

            event.preventDefault();

            closeCodeSettingModal();
        }
    );


// OUTSIDE CLICK
$(document)
    .off('click.userCodeModal', '#codeSettingModal')
    .on('click.userCodeModal', '#codeSettingModal', function (event) {

        if (event.target === this) {
            closeCodeSettingModal();
        }
    });


// ESCAPE KEY
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

$(document)
    .off('change.userCodeModal', '#code_setting_user_type')
    .on(
        'change.userCodeModal',
        '#code_setting_user_type',
        function () {

            const userType = $(this).val();

            // আগের কোড পরিষ্কার
            $('#setting_user_code').val('');

            $('input[name="code_generation_type"]')
                .prop('checked', false);

            modalManualCodeTouched = false;

            // User Type না থাকলে পরবর্তী Section Hide
            if (!userType) {

                $('#codeGenerationTypeSection').hide();

                $('#codeInputSection').hide();

                return;
            }

            // Generation Type দেখাও
            $('#codeGenerationTypeSection').slideDown(200);

            // Auto/Manual নির্বাচন না করা পর্যন্ত Input Hide
            $('#codeInputSection').hide();

            $('#setting_user_code_help').text(
                'এখন Auto অথবা Manual নির্বাচন করুন।'
            );
        }
    );


/* ============================================================
   MODAL GENERATION TYPE CHANGE
   ============================================================ */

$(document)
    .off(
        'change.userCodeModal',
        'input[name="code_generation_type"]'
    )
    .on(
        'change.userCodeModal',
        'input[name="code_generation_type"]',
        function () {

            const type = getSelectedGenerationType();

            const userType = getModalUserType();

            const institutionId = getInstitutionId();

            if (!userType || !type) {
                return;
            }

            // Input সঙ্গে সঙ্গে দেখাও
            $('#codeInputSection').slideDown(200);

            modalManualCodeTouched = false;

            // AUTO MODE
            if (type === 'auto') {

                $('#setting_user_code')
                    .val('')
                    .attr(
                        'placeholder',
                        'প্রাথমিক ইউজার কোড'
                    );

                $('#setting_user_code_help').text(
                    'অটো কোড লোড হচ্ছে...'
                );

                // Database থেকে Start Code Load
                loadSettingForRole(userType);

                return;
            }


            // MANUAL MODE
            if (type === 'manual') {

                $('#setting_user_code')
                    .val('')
                    .attr(
                        'placeholder',
                        'ম্যানুয়াল কোড লিখুন অথবা ফাঁকা রাখুন'
                    );

                $('#setting_user_code_help').text(
                    'আপনার পছন্দমতো Manual Code লিখুন।'
                );

                // পরবর্তী Manual Code Preview
                previewManualCodeInModal(
                    userType,
                    institutionId
                );

                return;
            }
        }
    );


/* ============================================================
   MODAL CODE INPUT
   ============================================================ */

$(document)
    .off('input.userCodeModal', '#setting_user_code')
    .on(
        'input.userCodeModal',
        '#setting_user_code',
        function () {

            const value = $(this).val();

            modalManualCodeTouched = true;

            // শুধু Numeric Code
            const numericValue = value.replace(/[^0-9]/g, '');

            if (value !== numericValue) {
                $(this).val(numericValue);
            }

            const generationType =
                getSelectedGenerationType();

            if (generationType === 'manual') {

                if (numericValue) {

                    $('#setting_user_code_help').text(
                        'Manual Code: ' + numericValue
                    );

                } else {

                    $('#setting_user_code_help').text(
                        'ম্যানুয়াল কোড লিখুন অথবা ফাঁকা রাখুন।'
                    );
                }
            }

            if (generationType === 'auto') {

                $('#setting_user_code_help').text(
                    'প্রাথমিক কোড: ' + numericValue
                );
            }
        }
    );

    /* ============================================================
       SAVE CODE SETTING
       ============================================================ */

    $('#saveCodeSetting')
        .off('click.userCodeModal')
        .on('click.userCodeModal', function (event) {
            event.preventDefault();

            const userType = getModalUserType();

            const generationType =
                getSelectedGenerationType();

            const enteredCode =
                $.trim($('#setting_user_code').val());

            if (!['student', 'user'].includes(userType)) {
                alert(
                    'শুধুমাত্র Student অথবা User নির্বাচন করুন।'
                );

                return;
            }

            let startCode;

            /*
             * Auto mode: input must be the starting code.
             * Manual mode: preserve the configured start code;
             * entered code is the optional manual ID.
             */
            if (generationType === 'auto') {
                if (
                    !/^\d+$/.test(enteredCode) ||
                    Number(enteredCode) < 1 ||
                    Number(enteredCode) > 999999999
                ) {
                    alert(
                        'সঠিক প্রাথমিক ইউজার কোড লিখুন।'
                    );

                    $('#setting_user_code').focus();

                    return;
                }

                startCode = Number(enteredCode);
            } else {
                const existingSetting =
                    savedCodeSettings[userType];

                startCode = existingSetting
                    ? Number(existingSetting.start_code)
                    : (codeStartMap[userType] || 101);

                if (enteredCode && !/^\d+$/.test(enteredCode)) {
                    alert(
                        'ম্যানুয়াল কোড শুধুমাত্র সংখ্যা হতে হবে।'
                    );

                    $('#setting_user_code').focus();

                    return;
                }
            }

            const $button = $('#saveCodeSetting');

            $button.prop('disabled', true);

            /*
             * Save starting code in database.
             */
            $.ajax({
                url: codeSettingsSaveUrl,
                type: 'POST',
                data: JSON.stringify({
                    user_type: userType,
                    start_code: startCode
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

                savedCodeSettings[userType] = {
                    start_code: startCode
                };

                /*
                 * Save the selected UI mode in sessionStorage.
                 * The numeric starting code is saved in database.
                 */
                saveCodeSettingToSession({
                    user_type: userType,
                    generation_type: generationType
                });

                /*
                 * Apply the selected type and mode to main form.
                 */
                $('#user_type').val(userType);

                updateHiddenCodeType(generationType);

                if (generationType === 'auto') {
                    currentMainManualCode = '';
                    currentMainManualCodeExplicit = false;
                    mainManualPreviewOnly = false;

                    clearSavedManualCode();

                    $('#manual_user_code').val('');

                    setAutoModeUI();

                    /*
                     * Preview is only a preview.
                     * Form submit clears custom_id for auto mode.
                     */
                    autoFillId(
                        userType,
                        getInstitutionId()
                    );
                } else {
                    const manualCode = enteredCode;

                    setManualModeUI();

                    if (manualCode) {
                        currentMainManualCode = manualCode;
                        currentMainManualCodeExplicit = true;
                        mainManualPreviewOnly = false;

                        saveManualCode(
                            userType,
                            getInstitutionId(),
                            manualCode
                        );

                        $('#manual_user_code').val(manualCode);
                        $('#custom_id').val(manualCode);
                    } else {
                        currentMainManualCode = '';
                        currentMainManualCodeExplicit = false;
                        mainManualPreviewOnly = true;

                        clearSavedManualCode();

                        $('#manual_user_code').val('');

                        manualFillId(
                            userType,
                            getInstitutionId()
                        );
                    }
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

                const response = xhr.responseJSON || {};

                alert(
                    response.message ||
                    'User Code Setting সংরক্ষণ করা যায়নি।'
                );
            })
            .always(function () {
                $button.prop('disabled', false);
            });
        });


    /* ============================================================
       INITIAL CODE SETTING
       ============================================================ */

    const initialSetting = getSavedCodeSetting();

    if (initialSetting) {
        if (
            initialSetting.generation_type === 'manual'
        ) {
            updateHiddenCodeType('manual');
        } else {
            updateHiddenCodeType('auto');
        }
    }

    if (getCurrentUserType()) {
        if (
            $('#code_generation_type').val() === 'manual'
        ) {
            setManualModeUI();
            prepareManualMainId();
        } else {
            setAutoModeUI();

            autoFillId(
                getCurrentUserType(),
                getInstitutionId()
            );
        }
    } else {
        setAutoModeUI();
    }


    /* ============================================================
       BEFORE FORM SUBMIT
       ============================================================ */

    $('#userCreateForm')
        .off('submit.userCode')
        .on('submit.userCode', function (event) {
            const userType = getCurrentUserType();

            const generationType =
                $('#code_generation_type').val() || 'auto';

            if (!userType) {
                event.preventDefault();

                alert(
                    'অনুগ্রহ করে ব্যবহারকারীর ধরণ নির্বাচন করুন।'
                );

                return false;
            }

            /*
             * AUTO:
             * Backend generates the final code.
             */
            if (generationType === 'auto') {
                $('#custom_id').val('');
                return true;
            }

            /*
             * MANUAL with explicit code:
             * Submit the exact code entered by the user.
             */
            if (
                currentMainManualCodeExplicit &&
                currentMainManualCode
            ) {
                const manualCode =
                    String(currentMainManualCode).trim();

                if (!/^\d+$/.test(manualCode)) {
                    event.preventDefault();

                    alert(
                        'ম্যানুয়াল কোড শুধুমাত্র সংখ্যা হতে হবে।'
                    );

                    return false;
                }

                $('#custom_id').val(manualCode);

                return true;
            }

            /*
             * MANUAL with blank code:
             * Backend must generate the next manual code.
             * Preview must not be submitted as an explicit ID.
             */
            $('#custom_id').val('');

            return true;
        });

});

</script>


@endsection