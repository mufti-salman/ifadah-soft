@extends('layouts.admin')

@section('title', 'শিক্ষক তালিকা')

<style>
    .teacher-page {
        padding: 20px;
    }

    .teacher-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .teacher-title h3 {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
    }

    .teacher-title p {
        margin: 5px 0 0;
        color: #777;
        font-size: 14px;
    }

    .teacher-create-btn {
        border: 0;
        padding: 11px 18px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
    }

    .teacher-filter-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .teacher-table-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 10px;
        overflow: hidden;
    }

    .teacher-table {
        width: 100%;
        border-collapse: collapse;
    }

    .teacher-table th,
    .teacher-table td {
        padding: 13px 15px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
        white-space: nowrap;
    }

    .teacher-table th {
        font-size: 13px;
        font-weight: 700;
        background: #fafafa;
    }

    .teacher-table td {
        font-size: 14px;
    }

    .teacher-photo {
        width: 42px;
        height: 42px;
        object-fit: cover;
        border-radius: 50%;
        border: 1px solid #ddd;
    }

    .teacher-name {
        font-weight: 600;
    }

    .teacher-sub {
        color: #888;
        font-size: 12px;
        margin-top: 3px;
    }

    .teacher-code {
        font-weight: 700;
    }

    .status-badge {
        display: inline-flex;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-active {
        background: #e9f8ef;
        color: #198754;
    }

    .status-inactive {
        background: #fceaea;
        color: #dc3545;
    }

    .teacher-actions {
        display: flex;
        gap: 5px;
    }

    .teacher-action {
        width: 34px;
        height: 34px;
        border: 1px solid #ddd;
        border-radius: 7px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        text-decoration: none;
        background: #fff;
    }

    /*
    |--------------------------------------------------------------------------
    | Modal
    |--------------------------------------------------------------------------
    */

    .teacher-modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .55);
        z-index: 99999;

        display: none;
        align-items: center;
        justify-content: center;

        padding: 20px;
    }

    .teacher-modal.show {
        display: flex;
    }

    .teacher-modal-box {
        width: 100%;
        max-width: 1050px;
        max-height: 92vh;
        background: #fff;
        border-radius: 14px;
        overflow: hidden;

        display: flex;
        flex-direction: column;

        box-shadow: 0 20px 60px rgba(0,0,0,.25);
    }

    .teacher-modal-header {
        padding: 18px 22px;
        border-bottom: 1px solid #eee;

        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .teacher-modal-header h4 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
    }

    .teacher-modal-close {
        width: 36px;
        height: 36px;
        border: 0;
        background: #f5f5f5;
        border-radius: 50%;
        cursor: pointer;
        font-size: 20px;
    }

    .teacher-modal-body {
        overflow-y: auto;
        padding: 22px;
    }

    /*
    |--------------------------------------------------------------------------
    | Wizard
    |--------------------------------------------------------------------------
    */

    .teacher-steps {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 25px;
        gap: 0;
    }

    .teacher-step {
        display: flex;
        align-items: center;
    }

    .teacher-step-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;

        display: flex;
        align-items: center;
        justify-content: center;

        background: #eee;
        color: #777;
        font-weight: 700;
    }

    .teacher-step.active .teacher-step-circle {
        background: #0d6efd;
        color: #fff;
    }

    .teacher-step.completed .teacher-step-circle {
        background: #198754;
        color: #fff;
    }

    .teacher-step-label {
        margin-left: 8px;
        font-size: 13px;
        font-weight: 600;
    }

    .teacher-step-line {
        width: 70px;
        height: 2px;
        background: #ddd;
        margin: 0 12px;
    }

    .teacher-form-step {
        display: none;
    }

    .teacher-form-step.active {
        display: block;
    }

    .teacher-section-title {
        font-size: 17px;
        font-weight: 700;
        margin-bottom: 18px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    .teacher-field {
        margin-bottom: 15px;
    }

    .teacher-field label {
        display: block;
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 600;
    }

    .teacher-field input,
    .teacher-field select,
    .teacher-field textarea {
        width: 100%;
        min-height: 42px;
        border: 1px solid #ddd;
        border-radius: 7px;
        padding: 8px 11px;
        outline: none;
    }

    .teacher-field textarea {
        min-height: 90px;
        resize: vertical;
    }

    .teacher-field input:focus,
    .teacher-field select:focus,
    .teacher-field textarea:focus {
        border-color: #0d6efd;
    }

    .teacher-required {
        color: #dc3545;
    }

    .teacher-code-preview {
        background: #f6f8fa;
        font-weight: 700;
    }

    .teacher-radio-group {
        display: flex;
        gap: 18px;
        align-items: center;
        min-height: 42px;
    }

    .teacher-radio {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .teacher-radio input {
        width: auto;
        min-height: auto;
    }

    .teacher-modal-footer {
        padding: 15px 22px;
        border-top: 1px solid #eee;

        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .teacher-footer-right {
        display: flex;
        gap: 8px;
    }

    .teacher-btn {
        border: 0;
        border-radius: 7px;
        padding: 9px 17px;
        cursor: pointer;
        font-weight: 600;
    }

    .teacher-btn-secondary {
        background: #f0f0f0;
    }

    .teacher-btn-primary {
        background: #0d6efd;
        color: #fff;
    }

    .teacher-btn-success {
        background: #198754;
        color: #fff;
    }

    .teacher-error {
        color: #dc3545;
        font-size: 12px;
        margin-top: 5px;
    }

    @media (max-width: 768px) {

        .teacher-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .teacher-table-card {
            overflow-x: auto;
        }

        .teacher-steps {
            overflow-x: auto;
            justify-content: flex-start;
        }

        .teacher-step-label {
            display: none;
        }

        .teacher-step-line {
            width: 30px;
        }

        .teacher-modal {
            padding: 10px;
        }

        .teacher-modal-box {
            max-height: 96vh;
        }
    }
</style>


@section('page')

<div class="teacher-page">

    {{-- Header --}}
    <div class="teacher-header">

        <div class="teacher-title">
            <h3>শিক্ষক তালিকা</h3>
            <p>সিস্টেমের শিক্ষক পরিচালনা ও দেখুন</p>
        </div>

        <button
            type="button"
            class="teacher-create-btn btn btn-primary"
            onclick="openTeacherModal()"
        >
            + শিক্ষক তৈরি করুন
        </button>

    </div>


    {{-- Filters --}}
    <div class="teacher-filter-card">

        <form
            method="GET"
            action="{{ route('dashboard.teacher.index') }}"
        >

            <div class="row g-3 align-items-end">

                <div class="col-md-5">

                    <label class="form-label">
                        অনুসন্ধান
                    </label>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        value="{{ request('search') }}"
                        placeholder="নাম, শিক্ষক কোড, User Code বা ফোন..."
                    >

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        স্ট্যাটাস
                    </label>

                    <select
                        name="status"
                        class="form-select"
                    >

                        <option value="all">
                            সকল স্ট্যাটাস
                        </option>

                        <option
                            value="1"
                            @selected(request('status') === '1')
                        >
                            সক্রিয়
                        </option>

                        <option
                            value="0"
                            @selected(request('status') === '0')
                        >
                            নিষ্ক্রিয়
                        </option>

                    </select>

                </div>


                <div class="col-md-2">

                    <label class="form-label">
                        প্রতি পেজ
                    </label>

                    <select
                        name="per_page"
                        class="form-select"
                    >

                        <option
                            value="10"
                            @selected(request('per_page', 20) == 10)
                        >
                            10
                        </option>

                        <option
                            value="20"
                            @selected(request('per_page', 20) == 20)
                        >
                            20
                        </option>

                        <option
                            value="50"
                            @selected(request('per_page', 20) == 50)
                        >
                            50
                        </option>

                    </select>

                </div>


                <div class="col-md-2">

                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                    >
                        Filter
                    </button>

                </div>

            </div>

        </form>

    </div>


    {{-- Success Message --}}
    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- Table --}}
    <div class="teacher-table-card">

        <div class="table-responsive">

            <table class="teacher-table">

                <thead>

                    <tr>

                        <th>ছবি</th>

                        <th>উইজার বিবরণ</th>

                        <th>উইজার কোড</th>

                        <th>শিক্ষক কোড</th>

                        <th>পিতার নাম</th>

                        <th>স্ট্যাটাস</th>

                        <th>পদবী</th>

                        <th>অ্যাকশন</th>

                    </tr>

                </thead>

                <tbody>

                @forelse($teachers as $teacher)

                    @php
                        $user = $teacher->user;
                    @endphp

                    <tr>

                        <td>

                            @if($user?->photo)

                                <img
                                    src="{{ asset('storage/' . $user->photo) }}"
                                    class="teacher-photo"
                                    alt="Teacher"
                                >

                            @else

                                <div
                                    class="teacher-photo d-flex align-items-center justify-content-center bg-light"
                                >
                                    <span>👤</span>
                                </div>

                            @endif

                        </td>


                        <td>

                            <div class="teacher-name">
                                {{ $user?->name_bn ?: $user?->name }}
                            </div>

                            <div class="teacher-sub">
                                {{ $user?->phone }}
                            </div>

                        </td>


                        <td>

                            <span class="teacher-code">
                                {{ $user?->institution_user_id }}
                            </span>

                        </td>


                        <td>

                            <span class="teacher-code">
                                {{ $teacher->teacher_id }}
                            </span>

                        </td>


                        <td>
                            {{ $user?->father_name ?: '—' }}
                        </td>


                        <td>

                            @if($user?->status)

                                <span class="status-badge status-active">
                                    সক্রিয়
                                </span>

                            @else

                                <span class="status-badge status-inactive">
                                    নিষ্ক্রিয়
                                </span>

                            @endif

                        </td>


                        <td>
                            {{ $teacher->designation }}
                        </td>


                        <td>

                            <div class="teacher-actions">

                                <a
                                    href="{{ route('dashboard.teacher.show', $teacher->id) }}"
                                    class="teacher-action"
                                    title="দেখুন"
                                >
                                    👁
                                </a>

                                @can('teacher.edit')

                                    <a
                                        href="{{ route('dashboard.teacher.edit', $teacher->id) }}"
                                        class="teacher-action"
                                        title="Edit"
                                    >
                                        ✏
                                    </a>

                                @endcan


                                @can('teacher.edit')

                                    <form
                                        method="POST"
                                        action="{{ route('dashboard.teacher.toggle-status', $teacher->id) }}"
                                        style="display:inline"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="teacher-action"
                                            title="Status"
                                        >
                                            ↔
                                        </button>

                                    </form>

                                @endcan


                                @can('teacher.delete')

                                    <form
                                        method="POST"
                                        action="{{ route('dashboard.teacher.destroy', $teacher->id) }}"
                                        style="display:inline"
                                        onsubmit="return confirm('আপনি কি এই শিক্ষককে মুছে ফেলতে চান?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="teacher-action"
                                            title="Delete"
                                        >
                                            🗑
                                        </button>

                                    </form>

                                @endcan

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="text-center py-5"
                        >
                            কোনো শিক্ষক পাওয়া যায়নি।
                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}

        @if($teachers->hasPages())

            <div class="p-3">

                {{ $teachers->links() }}

            </div>

        @endif

    </div>

</div>


{{-- ===================================================================== --}}
{{-- CREATE TEACHER MODAL --}}
{{-- ===================================================================== --}}

<div
    id="teacherCreateModal"
    class="teacher-modal"
>

    <div class="teacher-modal-box">

        <div class="teacher-modal-header">

            <h4>
                শিক্ষক তৈরি করুন
            </h4>

            <button
                type="button"
                class="teacher-modal-close"
                onclick="closeTeacherModal()"
            >
                ×
            </button>

        </div>


        <form
            method="POST"
            action="{{ route('dashboard.teacher.store') }}"
            enctype="multipart/form-data"
            id="teacherCreateForm"
        >

            @csrf


            <div class="teacher-modal-body">

                {{-- Wizard Steps --}}

                <div class="teacher-steps">

                    <div
                        class="teacher-step active"
                        data-step="1"
                    >

                        <div class="teacher-step-circle">
                            1
                        </div>

                        <div class="teacher-step-label">
                            ব্যক্তিগত তথ্য
                        </div>

                    </div>


                    <div class="teacher-step-line"></div>


                    <div
                        class="teacher-step"
                        data-step="2"
                    >

                        <div class="teacher-step-circle">
                            2
                        </div>

                        <div class="teacher-step-label">
                            স্থায়ী ঠিকানা
                        </div>

                    </div>


                    <div class="teacher-step-line"></div>


                    <div
                        class="teacher-step"
                        data-step="3"
                    >

                        <div class="teacher-step-circle">
                            3
                        </div>

                        <div class="teacher-step-label">
                            অস্থায়ী ঠিকানা
                        </div>

                    </div>


                    <div class="teacher-step-line"></div>


                    <div
                        class="teacher-step"
                        data-step="4"
                    >

                        <div class="teacher-step-circle">
                            4
                        </div>

                        <div class="teacher-step-label">
                            শিক্ষক তথ্য
                        </div>

                    </div>

                </div>


                {{-- ===================================================== --}}
                {{-- STEP 1 --}}
                {{-- ===================================================== --}}

                <div
                    class="teacher-form-step active"
                    data-form-step="1"
                >

                    <div class="teacher-section-title">
                        ব্যক্তিগত তথ্য
                    </div>


                    <div class="row">

                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    Teacher Code
                                </label>

                                <input
                                    type="text"
                                    class="teacher-code-preview"
                                    value="{{ $teacherCode }}"
                                    readonly
                                >

                                <small class="text-muted">
                                    এটি প্রতিষ্ঠানের শিক্ষক কোড।
                                </small>

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    User Code
                                </label>

                                <input
                                    type="text"
                                    class="teacher-code-preview"
                                    value="{{ $userCode }}"
                                    readonly
                                >

                                <small class="text-muted">
                                    এটি User Code System-এর কোড।
                                </small>

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    ছবি
                                </label>

                                <input
                                    type="file"
                                    name="photo"
                                    accept="image/*"
                                >

                            </div>

                        </div>

                    </div>


                    <div class="row">

                        <div class="col-md-6">

                            <div class="teacher-field">

                                <label>
                                    পূর্ণ নাম (বাংলা)
                                    <span class="teacher-required">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="name_bn"
                                    required
                                    value="{{ old('name_bn') }}"
                                    placeholder="শিক্ষকের পূর্ণ নাম"
                                >

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="teacher-field">

                                <label>
                                    পূর্ণ নাম (English)
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    placeholder="Full Name"
                                >

                            </div>

                        </div>

                    </div>


                    <div class="row">

                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    লিঙ্গ
                                    <span class="teacher-required">*</span>
                                </label>

                                <div class="teacher-radio-group">

                                    <label class="teacher-radio">

                                        <input
                                            type="radio"
                                            name="gender"
                                            value="male"
                                            required
                                        >

                                        পুরুষ

                                    </label>


                                    <label class="teacher-radio">

                                        <input
                                            type="radio"
                                            name="gender"
                                            value="female"
                                        >

                                        মহিলা

                                    </label>

                                </div>

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    জন্ম তারিখ
                                </label>

                                <input
                                    type="date"
                                    name="date_of_birth"
                                >

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    বয়স
                                </label>

                                <input
                                    type="number"
                                    name="age"
                                    min="0"
                                    max="150"
                                >

                            </div>

                        </div>

                    </div>


                    <div class="row">

                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    NID
                                </label>

                                <input
                                    type="text"
                                    name="nid"
                                >

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    জন্ম নিবন্ধন
                                </label>

                                <input
                                    type="text"
                                    name="birth_certificate"
                                >

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    রক্তের গ্রুপ
                                </label>

                                <select name="blood_group">

                                    <option value="">
                                        নির্বাচন করুন
                                    </option>

                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>

                                </select>

                            </div>

                        </div>

                    </div>


                    <div class="row">

                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    পিতার নাম
                                    <span class="teacher-required">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="father_name"
                                    required
                                >

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    মাতার নাম
                                </label>

                                <input
                                    type="text"
                                    name="mother_name"
                                >

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    Email
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                >

                            </div>

                        </div>

                    </div>


                    <div class="row">

                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    ফোন ১
                                    <span class="teacher-required">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    required
                                >

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    ফোন ২
                                </label>

                                <input
                                    type="text"
                                    name="phone2"
                                >

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    ফোনের মালিক
                                </label>

                                <input
                                    type="text"
                                    name="phone_owner"
                                    placeholder="নিজ / পিতা / মাতা"
                                >

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ===================================================== --}}
                {{-- STEP 2 --}}
                {{-- ===================================================== --}}

                <div
                    class="teacher-form-step"
                    data-form-step="2"
                >

                    <div class="teacher-section-title">
                        স্থায়ী ঠিকানা
                    </div>


                    <div class="row">

                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>বিভাগ</label>

                                <select
                                    name="permanent_division_id"
                                    id="permanent_division_id"
                                >

                                    <option value="">
                                        নির্বাচন করুন
                                    </option>

                                    @foreach($divisions as $division)

                                        <option
                                            value="{{ $division->id }}"
                                        >
                                            {{ $division->name }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>জেলা</label>

                                <select
                                    name="permanent_district_id"
                                    id="permanent_district_id"
                                >

                                    <option value="">
                                        আগে বিভাগ নির্বাচন করুন
                                    </option>

                                </select>

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>থানা / উপজেলা</label>

                                <select
                                    name="permanent_upazila_id"
                                    id="permanent_upazila_id"
                                >

                                    <option value="">
                                        আগে জেলা নির্বাচন করুন
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>


                    <div class="row">

                        <div class="col-md-6">

                            <div class="teacher-field">

                                <label>
                                    পোস্ট অফিস
                                </label>

                                <input
                                    type="text"
                                    name="permanent_post_office"
                                >

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="teacher-field">

                                <label>
                                    গ্রাম / রোড
                                </label>

                                <input
                                    type="text"
                                    name="permanent_village_road"
                                >

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ===================================================== --}}
                {{-- STEP 3 --}}
                {{-- ===================================================== --}}

                <div
                    class="teacher-form-step"
                    data-form-step="3"
                >

                    <div class="teacher-section-title">
                        অস্থায়ী ঠিকানা
                    </div>


                    <div class="row">

                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>বিভাগ</label>

                                <select
                                    name="present_division_id"
                                    id="present_division_id"
                                >

                                    <option value="">
                                        নির্বাচন করুন
                                    </option>

                                    @foreach($divisions as $division)

                                        <option
                                            value="{{ $division->id }}"
                                        >
                                            {{ $division->name }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>জেলা</label>

                                <select
                                    name="present_district_id"
                                    id="present_district_id"
                                >

                                    <option value="">
                                        আগে বিভাগ নির্বাচন করুন
                                    </option>

                                </select>

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>থানা / উপজেলা</label>

                                <select
                                    name="present_upazila_id"
                                    id="present_upazila_id"
                                >

                                    <option value="">
                                        আগে জেলা নির্বাচন করুন
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>


                    <div class="row">

                        <div class="col-md-6">

                            <div class="teacher-field">

                                <label>
                                    পোস্ট অফিস
                                </label>

                                <input
                                    type="text"
                                    name="present_post_office"
                                >

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="teacher-field">

                                <label>
                                    গ্রাম / রোড
                                </label>

                                <input
                                    type="text"
                                    name="present_village_road"
                                >

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ===================================================== --}}
                {{-- STEP 4 --}}
                {{-- ===================================================== --}}

                <div
                    class="teacher-form-step"
                    data-form-step="4"
                >

                    <div class="teacher-section-title">
                        শিক্ষক তথ্য
                    </div>


                    <div class="row">

                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label for="teacher_id">শিক্ষক কোড</label>

                                <input
                                    type="text"
                                    name="teacher_id"
                                    id="teacher_id"
                                    value="{{ $teacherCode ?? '' }}"
                                    class="teacher-code-preview"
                                    readonly
                                >

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    পদবী
                                    <span class="teacher-required">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="designation"
                                    required
                                    placeholder="সহকারী শিক্ষক"
                                >

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    যোগদানের তারিখ
                                    <span class="teacher-required">*</span>
                                </label>

                                <input
                                    type="date"
                                    name="joining_date"
                                    required
                                >

                            </div>

                        </div>

                    </div>


                    <div class="row">

                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    পাসের বিভাগ
                                </label>

                                <input
                                    type="text"
                                    name="pass_department"
                                    placeholder="ইসলামিক স্টাডিজ"
                                >

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    পাসের সাল
                                </label>

                                <input
                                    type="number"
                                    name="pass_year"
                                    min="1900"
                                    max="{{ date('Y') }}"
                                >

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    পাসের তারিখ
                                </label>

                                <input
                                    type="date"
                                    name="pass_date"
                                >

                            </div>

                        </div>

                    </div>


                    <div class="row">

                        <div class="col-md-6">

                            <div class="teacher-field">

                                <label>
                                    শিক্ষাগত যোগ্যতা
                                </label>

                                <textarea
                                    name="qualification"
                                    placeholder="শিক্ষাগত যোগ্যতার বিবরণ"
                                ></textarea>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="teacher-field">

                                <label>
                                    যে প্রতিষ্ঠান থেকে পাস করেছেন
                                </label>

                                <input
                                    type="text"
                                    name="completion_institution"
                                    placeholder="মাদ্রাসা / কলেজ / বিশ্ববিদ্যালয়"
                                >

                            </div>

                        </div>

                    </div>


                    <div class="row">

                        <div class="col-md-6">

                            <div class="teacher-field">

                                <label>
                                    অভিজ্ঞতা
                                </label>

                                <textarea
                                    name="experience"
                                    placeholder="পূর্বের শিক্ষকতার অভিজ্ঞতা"
                                ></textarea>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="teacher-field">

                                <label>
                                    দক্ষতার বিষয়
                                </label>

                                <textarea
                                    name="expertise_subjects"
                                    placeholder="কুরআন, হাদিস, আরবি..."
                                ></textarea>

                            </div>

                        </div>

                    </div>


                    <div class="row">

                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    বেতন স্কেল
                                </label>

                                <input
                                    type="text"
                                    name="salary_scale"
                                >

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    মূল বেতন
                                </label>

                                <input
                                    type="number"
                                    name="basic_salary"
                                    min="0"
                                    step="0.01"
                                >

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="teacher-field">

                                <label>
                                    শ্রেণিশিক্ষক
                                </label>

                                <select
                                    name="is_class_teacher"
                                    id="is_class_teacher"
                                >

                                    <option value="0">
                                        না
                                    </option>

                                    <option value="1">
                                        হ্যাঁ
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>


                    <div
                        class="row"
                        id="classTeacherField"
                        style="display:none;"
                    >

                        <div class="col-md-6">

                            <div class="teacher-field">

                                <label>
                                    কোন শ্রেণির শ্রেণিশিক্ষক?
                                </label>

                                <select
                                    name="class_teacher_for"
                                >

                                    <option value="">
                                        নির্বাচন করুন
                                    </option>

                                    @foreach($classes as $class)

                                        <option
                                            value="{{ $class->id }}"
                                        >
                                            {{ $class->name }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>

                    </div>


                    <div class="teacher-field">

                        <label>
                            মন্তব্য
                        </label>

                        <textarea
                            name="remarks"
                            placeholder="অতিরিক্ত তথ্য..."
                        ></textarea>

                    </div>

                </div>

            </div>


            {{-- Modal Footer --}}

            <div class="teacher-modal-footer">

                <button
                    type="button"
                    class="teacher-btn teacher-btn-secondary"
                    onclick="resetTeacherForm()"
                >
                    Reset
                </button>


                <div class="teacher-footer-right">

                    <button
                        type="button"
                        id="teacherPrevBtn"
                        class="teacher-btn teacher-btn-secondary"
                        onclick="previousTeacherStep()"
                        style="display:none;"
                    >
                        Previous
                    </button>


                    <button
                        type="button"
                        id="teacherNextBtn"
                        class="teacher-btn teacher-btn-primary"
                        onclick="nextTeacherStep()"
                    >
                        Next
                    </button>


                    <button
                        type="submit"
                        id="teacherSubmitBtn"
                        class="teacher-btn teacher-btn-success"
                        style="display:none;"
                    >
                        শিক্ষক সংরক্ষণ করুন
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
        |--------------------------------------------------------------------------
        | Modal
        |--------------------------------------------------------------------------
        */

        window.openTeacherModal = function () {

            const modal =
                document.getElementById(
                    'teacherCreateModal'
                );

            modal.classList.add('show');

            document.body.style.overflow = 'hidden';

            currentTeacherStep = 1;

            updateTeacherWizard();
        };


        window.closeTeacherModal = function () {

            const modal =
                document.getElementById(
                    'teacherCreateModal'
                );

            modal.classList.remove('show');

            document.body.style.overflow = '';
        };


        /*
        |--------------------------------------------------------------------------
        | Close on Overlay
        |--------------------------------------------------------------------------
        */

        document
            .getElementById('teacherCreateModal')
            .addEventListener(
                'click',
                function (event) {

                    if (
                        event.target === this
                    ) {

                        closeTeacherModal();
                    }

                }
            );


        /*
        |--------------------------------------------------------------------------
        | Wizard
        |--------------------------------------------------------------------------
        */

        window.currentTeacherStep = 1;

        const totalTeacherSteps = 4;


        window.updateTeacherWizard = function () {

            document
                .querySelectorAll(
                    '[data-form-step]'
                )
                .forEach(function (step) {

                    step.classList.remove(
                        'active'
                    );

                    if (
                        parseInt(
                            step.dataset.formStep
                        ) === currentTeacherStep
                    ) {

                        step.classList.add(
                            'active'
                        );
                    }
                });


            document
                .querySelectorAll(
                    '[data-step]'
                )
                .forEach(function (step) {

                    const number =
                        parseInt(
                            step.dataset.step
                        );

                    step.classList.remove(
                        'active',
                        'completed'
                    );

                    if (
                        number ===
                        currentTeacherStep
                    ) {

                        step.classList.add(
                            'active'
                        );

                    } else if (
                        number <
                        currentTeacherStep
                    ) {

                        step.classList.add(
                            'completed'
                        );
                    }

                });


            document
                .getElementById(
                    'teacherPrevBtn'
                )
                .style.display =
                    currentTeacherStep > 1
                        ? 'inline-block'
                        : 'none';


            document
                .getElementById(
                    'teacherNextBtn'
                )
                .style.display =
                    currentTeacherStep <
                    totalTeacherSteps
                        ? 'inline-block'
                        : 'none';


            document
                .getElementById(
                    'teacherSubmitBtn'
                )
                .style.display =
                    currentTeacherStep ===
                    totalTeacherSteps
                        ? 'inline-block'
                        : 'none';
        };


        function validateCurrentStep() {

            const step =
                document.querySelector(
                    '[data-form-step="' +
                    currentTeacherStep +
                    '"]'
                );

            if (!step) {
                return true;
            }

            const fields =
                step.querySelectorAll(
                    'input, select, textarea'
                );

            for (
                let i = 0;
                i < fields.length;
                i++
            ) {

                const field =
                    fields[i];

                if (
                    field.hasAttribute(
                        'required'
                    )
                    &&
                    !field.checkValidity()
                ) {

                    field.reportValidity();

                    return false;
                }
            }

            return true;
        }


        window.nextTeacherStep = function () {

            if (
                !validateCurrentStep()
            ) {
                return;
            }

            if (
                currentTeacherStep <
                totalTeacherSteps
            ) {

                currentTeacherStep++;

                updateTeacherWizard();
            }
        };


        window.previousTeacherStep =
            function () {

                if (
                    currentTeacherStep >
                    1
                ) {

                    currentTeacherStep--;

                    updateTeacherWizard();
                }
            };


        /*
        |--------------------------------------------------------------------------
        | Reset
        |--------------------------------------------------------------------------
        */

        window.resetTeacherForm =
            function () {

                if (
                    !confirm(
                        'আপনি কি ফর্মটি reset করতে চান?'
                    )
                ) {
                    return;
                }

                document
                    .getElementById(
                        'teacherCreateForm'
                    )
                    .reset();

                currentTeacherStep = 1;

                updateTeacherWizard();

                resetDistrictSelect(
                    'permanent_district_id',
                    'আগে বিভাগ নির্বাচন করুন'
                );

                resetUpazilaSelect(
                    'permanent_upazila_id',
                    'আগে জেলা নির্বাচন করুন'
                );

                resetDistrictSelect(
                    'present_district_id',
                    'আগে বিভাগ নির্বাচন করুন'
                );

                resetUpazilaSelect(
                    'present_upazila_id',
                    'আগে জেলা নির্বাচন করুন'
                );

                document
                    .getElementById(
                        'classTeacherField'
                    )
                    .style.display =
                        'none';
            };


        /*
        |--------------------------------------------------------------------------
        | District AJAX
        |--------------------------------------------------------------------------
        */

        function loadDistricts(
            divisionId,
            districtSelector,
            upazilaSelector
        ) {

            resetDistrictSelect(
                districtSelector,
                'লোড হচ্ছে...'
            );

            resetUpazilaSelect(
                upazilaSelector,
                'আগে জেলা নির্বাচন করুন'
            );

            if (!divisionId) {

                resetDistrictSelect(
                    districtSelector,
                    'আগে বিভাগ নির্বাচন করুন'
                );

                return;
            }

            fetch(
                '{{ route("dashboard.teacher.get.districts", ":id") }}'
                    .replace(
                        ':id',
                        divisionId
                    )
            )
            .then(
                response =>
                    response.json()
            )
            .then(
                data => {

                    const select =
                        document.getElementById(
                            districtSelector
                        );

                    select.innerHTML =
                        '<option value="">নির্বাচন করুন</option>';

                    data.forEach(
                        function (district) {

                            const option =
                                document.createElement(
                                    'option'
                                );

                            option.value =
                                district.id;

                            option.textContent =
                                district.name;

                            select.appendChild(
                                option
                            );
                        }
                    );
                }
            )
            .catch(
                function () {

                    resetDistrictSelect(
                        districtSelector,
                        'জেলা লোড করা যায়নি'
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Upazila AJAX
        |--------------------------------------------------------------------------
        */

        function loadUpazilas(
            districtId,
            upazilaSelector
        ) {

            resetUpazilaSelect(
                upazilaSelector,
                'লোড হচ্ছে...'
            );

            if (!districtId) {

                resetUpazilaSelect(
                    upazilaSelector,
                    'আগে জেলা নির্বাচন করুন'
                );

                return;
            }

            fetch(
                '{{ route("dashboard.teacher.get.upazilas", ":id") }}'
                    .replace(
                        ':id',
                        districtId
                    )
            )
            .then(
                response =>
                    response.json()
            )
            .then(
                data => {

                    const select =
                        document.getElementById(
                            upazilaSelector
                        );

                    select.innerHTML =
                        '<option value="">নির্বাচন করুন</option>';

                    data.forEach(
                        function (upazila) {

                            const option =
                                document.createElement(
                                    'option'
                                );

                            option.value =
                                upazila.id;

                            option.textContent =
                                upazila.name;

                            select.appendChild(
                                option
                            );
                        }
                    );
                }
            )
            .catch(
                function () {

                    resetUpazilaSelect(
                        upazilaSelector,
                        'উপজেলা লোড করা যায়নি'
                    );
                }
            );
        }


        function resetDistrictSelect(
            selector,
            text
        ) {

            const select =
                document.getElementById(
                    selector
                );

            if (!select) return;

            select.innerHTML =
                '<option value="">' +
                text +
                '</option>';
        }


        function resetUpazilaSelect(
            selector,
            text
        ) {

            const select =
                document.getElementById(
                    selector
                );

            if (!select) return;

            select.innerHTML =
                '<option value="">' +
                text +
                '</option>';
        }


        /*
        |--------------------------------------------------------------------------
        | Permanent Address
        |--------------------------------------------------------------------------
        */

        document
            .getElementById(
                'permanent_division_id'
            )
            .addEventListener(
                'change',
                function () {

                    loadDistricts(
                        this.value,
                        'permanent_district_id',
                        'permanent_upazila_id'
                    );
                }
            );


        document
            .getElementById(
                'permanent_district_id'
            )
            .addEventListener(
                'change',
                function () {

                    loadUpazilas(
                        this.value,
                        'permanent_upazila_id'
                    );
                }
            );


        /*
        |--------------------------------------------------------------------------
        | Present Address
        |--------------------------------------------------------------------------
        */

        document
            .getElementById(
                'present_division_id'
            )
            .addEventListener(
                'change',
                function () {

                    loadDistricts(
                        this.value,
                        'present_district_id',
                        'present_upazila_id'
                    );
                }
            );


        document
            .getElementById(
                'present_district_id'
            )
            .addEventListener(
                'change',
                function () {

                    loadUpazilas(
                        this.value,
                        'present_upazila_id'
                    );
                }
            );


        /*
        |--------------------------------------------------------------------------
        | Class Teacher
        |--------------------------------------------------------------------------
        */

        document
            .getElementById(
                'is_class_teacher'
            )
            .addEventListener(
                'change',
                function () {

                    document
                        .getElementById(
                            'classTeacherField'
                        )
                        .style.display =
                            this.value === '1'
                                ? 'block'
                                : 'none';
                }
            );


        /*
        |--------------------------------------------------------------------------
        | Escape Key
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key === 'Escape'
                ) {

                    closeTeacherModal();
                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Initial Wizard
        |--------------------------------------------------------------------------
        */

        updateTeacherWizard();

    });

</script>

@endsection