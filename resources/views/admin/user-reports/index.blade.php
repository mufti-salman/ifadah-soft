@extends('layouts.admin')

@section('title', 'ইউজার রিপোর্ট')

@section('page')

@php
    $previewGenerated = $previewGenerated ?? false;

    $reportType = (int) old('report_type', $reportType ?? 1);

    $userTypeId = old('user_type_id', $userTypeId ?? '');

    $gender = old('gender', $gender ?? '');
    $status = old('status', $status ?? '');

    $userIdFrom = old('user_id_from', $userIdFrom ?? '');
    $userIdTo = old('user_id_to', $userIdTo ?? '');

    $institutionName = $institution->display_name
        ?? $institution->name
        ?? 'মাদরাসার নাম';

    $institutionAddress = $institution->address ?? '';

    $institutionPhone = $institution->phone ?? '';

    $reportTitle = $reportTitle ?? '';

    $userTypes = $userTypes ?? collect();

    $users = $users ?? collect();

    $statistics = $statistics ?? [];

    $byRole = $byRole ?? collect();

    $studentsWithoutAdmission = $studentsWithoutAdmission ?? collect();

    $genderLabels = [
        'male' => 'পুরুষ',
        'female' => 'মহিলা',
        'other' => 'অন্যান্য',
    ];

    $statusLabels = [
        'active' => 'সক্রিয়',
        'inactive' => 'নিষ্ক্রিয়',
    ];

    $selectedUserType = $userTypes->firstWhere('id', (int) $userTypeId);

    $isStudentReport = $selectedUserType?->slug === 'student';

    $reportHeading = $isStudentReport
        ? 'শিক্ষার্থীদের তালিকা'
        : ($reportTitle ?: 'ইউজার তালিকা');

    /*
    |--------------------------------------------------------------------------
    | Helper: Student information
    |--------------------------------------------------------------------------
    */

    $getStudentValue = function ($student, array $keys, $default = '—') {

        if (!$student) {
            return $default;
        }

        foreach ($keys as $key) {

            $value = data_get($student, $key);

            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return $default;
    };

    /*
    |--------------------------------------------------------------------------
    | Helper: first non-empty value from several models
    |--------------------------------------------------------------------------
    */

    $firstFilled = function (array $sources, array $keys, $default = '—') {

        foreach ($sources as $source) {

            if (!$source) {
                continue;
            }

            foreach ($keys as $key) {

                $value = data_get($source, $key);

                if (is_scalar($value) && trim((string) $value) !== '') {
                    return $value;
                }
            }
        }

        return $default;
    };

    $nameKeys = [
        'name',
        'name_bn',
        'full_name',
        'display_name',
        'user_name',
        'student_name',
        'teacher_name',
        'first_name',
        'username',
    ];

    $userTypeLabelFor = function ($type) {

        return match ($type->slug ?? null) {
            'student' => 'শিক্ষার্থী',
            'teacher' => 'শিক্ষক/স্টাফ',
            'donor_member' => 'দাতা সদস্য',
            default => ($type->name_bn ?? $type->name ?? $type->slug ?? 'ইউজার'),
        };
    };

    /*
    |--------------------------------------------------------------------------
    | Helper: Bengali date
    |--------------------------------------------------------------------------
    */

    $bengaliDigits = [
        '0' => '০',
        '1' => '১',
        '2' => '২',
        '3' => '৩',
        '4' => '৪',
        '5' => '৫',
        '6' => '৬',
        '7' => '৭',
        '8' => '৮',
        '9' => '৯',
    ];

    $toBengaliNumber = function ($value) use ($bengaliDigits) {
        return strtr((string) $value, $bengaliDigits);
    };

    $formatBengaliDate = function ($date) use ($toBengaliNumber) {

        if (!$date) {
            return '—';
        }

        try {

            return $toBengaliNumber(
                \Carbon\Carbon::parse($date)->format('d-m-Y')
            );

        } catch (\Throwable $e) {

            return '—';
        }
    };

    $reportDate = $formatBengaliDate(now());

@endphp


<div class="ur-page">

    {{-- PAGE HEADER --}}
    <div class="ur-page-header no-print">

        <div>

            <h2 class="ur-page-title">
                <i class="fa fa-file-text-o"></i>
                ব্যবহারকারী ভিত্তিক প্রতিবেদন
            </h2>

            <p class="ur-page-subtitle">
                রিপোর্ট নির্বাচন করলেই নিচে স্বয়ংক্রিয়ভাবে প্রিভিউ দেখাবে।
            </p>

        </div>

        @if($previewGenerated)

            <button
                type="button"
                class="ur-btn ur-btn-dark"
                onclick="printUserReport()"
            >
                <i class="fa fa-print"></i>
                প্রিন্ট করুন
            </button>

        @endif

    </div>


    {{-- VALIDATION ERRORS --}}
    @if($errors->any())

        <div class="ur-alert ur-alert-danger no-print">

            <strong>
                <i class="fa fa-exclamation-circle"></i>
                অনুগ্রহ করে নিচের তথ্যগুলো যাচাই করুন।
            </strong>

            <ul>

                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif


    {{-- REPORT FILTER FORM --}}
    <div class="ur-card ur-filter-card no-print">

        <div class="ur-card-header">

            <div class="ur-card-header-icon">
                <i class="fa fa-sliders"></i>
            </div>

            <div>

                <h3>রিপোর্ট সেটিংস</h3>

                <p>
                    আপনার প্রয়োজন অনুযায়ী রিপোর্টের ধরন ও ফিল্টার নির্বাচন করুন।
                </p>

            </div>

        </div>


        <form
            id="userReportForm"
            action="{{ route('dashboard.user-reports.preview') }}"
            method="POST"
        >

            @csrf

            <div class="ur-form-grid">

                {{-- REPORT TYPE --}}
                <div class="ur-form-group ur-form-group-full">

                    <label for="report_type">
                        রিপোর্টসমূহ
                        <span class="ur-required">*</span>
                    </label>

                    <select
                        name="report_type"
                        id="report_type"
                        class="ur-form-control"
                        required
                    >

                        <option value="1" {{ $reportType === 1 ? 'selected' : '' }}>
                            আইডি সিরিয়াল ইউজার ডাটা তালিকা
                        </option>

                        <option value="2" {{ $reportType === 2 ? 'selected' : '' }}>
                            ইউজার পরিসংখ্যান
                        </option>

                        <option value="3" {{ $reportType === 3 ? 'selected' : '' }}>
                            শিক্ষার্থীর ভর্তির ফরম
                        </option>

                        <option value="4" {{ $reportType === 4 ? 'selected' : '' }}>
                            ভর্তি বিহীন শিক্ষার্থীর তালিকা
                        </option>

                    </select>

                </div>


                {{-- USER TYPE --}}
                <div
                    class="ur-form-group ur-filter-field"
                    id="userTypeField"
                >

                    <label for="user_type_id" class="ur-form-label">
                        ব্যবহারকারীর ধরণ
                    </label>

                    <select
                        name="user_type_id"
                        id="user_type_id"
                        class="ur-form-control"
                    >

                        <option value="">
                            সকল ইউজার
                        </option>

                        @foreach($userTypes as $type)

                            <option
                                value="{{ $type->id }}"
                                data-slug="{{ $type->slug }}"
                                {{ (string) $userTypeId === (string) $type->id ? 'selected' : '' }}
                            >
                                {{ $userTypeLabelFor($type) }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- GENDER --}}
                <div
                    class="ur-form-group ur-filter-field"
                    id="genderField"
                >

                    <label for="gender">
                        লিঙ্গ
                    </label>

                    <select
                        name="gender"
                        id="gender"
                        class="ur-form-control"
                    >

                        <option value="">উভয়</option>

                        <option value="male" {{ $gender === 'male' ? 'selected' : '' }}>
                            পুরুষ
                        </option>

                        <option value="female" {{ $gender === 'female' ? 'selected' : '' }}>
                            মহিলা
                        </option>

                        <option value="other" data-only-list="1" {{ $gender === 'other' ? 'selected' : '' }}>
                            অন্যান্য
                        </option>

                    </select>

                </div>


                {{-- STATUS --}}
                <div
                    class="ur-form-group ur-filter-field"
                    id="statusField"
                >

                    <label for="status">
                        ব্যবহারকারীর অবস্থা
                    </label>

                    <select
                        name="status"
                        id="status"
                        class="ur-form-control"
                    >

                        <option value="">উভয়</option>

                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>
                            সক্রিয়
                        </option>

                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>
                            নিষ্ক্রিয়
                        </option>

                    </select>

                </div>


                {{-- USER ID FROM --}}
                <div
                    class="ur-form-group ur-filter-field"
                    id="userIdFromField"
                >

                    <label for="user_id_from">
                        ইউজার আইডি — শুরু
                    </label>

                    <input
                        type="number"
                        name="user_id_from"
                        id="user_id_from"
                        class="ur-form-control"
                        min="1"
                        value="{{ $userIdFrom }}"
                        placeholder="যেমন: 101"
                    >

                </div>


                {{-- USER ID TO --}}
                <div
                    class="ur-form-group ur-filter-field"
                    id="userIdToField"
                >

                    <label for="user_id_to">
                        ইউজার আইডি — শেষ
                    </label>

                    <input
                        type="number"
                        name="user_id_to"
                        id="user_id_to"
                        class="ur-form-control"
                        min="1"
                        value="{{ $userIdTo }}"
                        placeholder="যেমন: 200"
                    >

                </div>

            </div>


            {{-- ADMISSION FORM NOTICE --}}
            <div
                id="admissionFormNotice"
                class="ur-info-box"
                style="display:none;"
            >

                <i class="fa fa-info-circle"></i>

                <div>

                    <strong>শিক্ষার্থীর ভর্তি ফরম</strong>

                    <p>
                        এই রিপোর্টে একটি ফাঁকা ভর্তি ফরম তৈরি হবে।
                    </p>

                </div>

            </div>


            {{-- FORM ACTIONS --}}
            <div class="ur-form-actions">

                <button
                    type="submit"
                    class="ur-btn ur-btn-primary"
                >

                    <i class="fa fa-eye"></i>
                    রিপোর্ট প্রিভিউ দেখুন

                </button>


                <a
                    href="{{ route('dashboard.user-reports.index') }}"
                    class="ur-btn ur-btn-light"
                >

                    <i class="fa fa-refresh"></i>
                    রিসেট

                </a>

            </div>

        </form>

    </div>


    {{-- REPORT PREVIEW --}}
    @if($previewGenerated)

        <div id="userReportPreview" class="ur-preview-section">

            {{-- PREVIEW TOOLBAR --}}
            <div class="ur-preview-toolbar no-print">

                <div>

                    <h3>
                        <i class="fa fa-file-text-o"></i>
                        রিপোর্ট প্রিভিউ
                    </h3>

                    <p>
                        নিচে নির্বাচিত রিপোর্টটি দেখুন। প্রয়োজনে প্রিন্ট করতে পারবেন।
                    </p>

                </div>

                <button
                    type="button"
                    class="ur-btn ur-btn-dark"
                    onclick="printUserReport()"
                >

                    <i class="fa fa-print"></i>
                    প্রিন্ট / PDF

                </button>

            </div>


            {{-- ================================================= --}}
            {{-- REPORT 1: USER LIST --}}
            {{-- ================================================= --}}

            @if($reportType === 1)

                <div class="ur-report-paper">

                    <div class="ur-report-heading">

                        <h2>{{ $institutionName }}</h2>

                        @if($institutionAddress)
                            <p>{{ $institutionAddress }}</p>
                        @endif

                        @if($institutionPhone)
                            <p>যোগাযোগ: {{ $institutionPhone }}</p>
                        @endif

                        <h3>
                            {{ $reportHeading }}
                        </h3>

                    </div>


                    <div class="ur-table-responsive">

                        <table class="ur-report-table ur-student-list-table">

                            <thead>

                                <tr>

                                    <th>ক্রমিক</th>

                                    <th>আইডি নং</th>

                                    <th>ইউজার নাম</th>

                                    <th>পিতার নাম</th>

                                    <th>মাতার নাম</th>

                                    <th>মোবাইল</th>

                                    <th>এন্ট্রি তারিখ</th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse($users as $index => $user)

                                    @php

                                        $student = $user->student ?? null;
                                        $teacher = $user->teacher ?? null;

                                        $userTypeSlug = $user->userType?->slug;

                                        $userRoleLabel = $user->user_type_label
                                            ?? (
                                                $userTypeSlug === 'teacher'
                                                    ? 'শিক্ষক/স্টাফ'
                                                    : (
                                                        $user->userType?->name_bn
                                                        ?? $user->userType?->name
                                                        ?? 'ইউজার'
                                                    )
                                            );

                                        $fatherName = $getStudentValue(
                                            $student,
                                            [
                                                'father_name',
                                                'father_name_bn',
                                                'father',
                                                'guardian_name',
                                            ]
                                        );

                                        $motherName = $getStudentValue(
                                            $student,
                                            [
                                                'mother_name',
                                                'mother_name_bn',
                                                'mother',
                                            ]
                                        );

                                        $entryDate = $user->created_at
                                            ?? $student?->created_at;

                                        $mobile = $firstFilled(
                                            [$user, $student, $teacher],
                                            ['phone', 'mobile', 'phone_number', 'guardian_mobile']
                                        );

                                        $userName = $firstFilled(
                                            [$user, $student, $teacher],
                                            $nameKeys
                                        );

                                        $userId = $user->institution_user_id
                                            ?? '—';

                                    @endphp

                                    <tr>

                                        <td>
                                            {{ $toBengaliNumber($index + 1) }}
                                        </td>

                                        <td>
                                            {{ $toBengaliNumber($userId) }}
                                        </td>

                                        <td>

                                            <strong>
                                                {{ $userName }}
                                            </strong>

                                            @if(!$isStudentReport && $userTypeId === '')
                                                <small class="ur-user-type-sub">
                                                    {{ $userRoleLabel }}
                                                </small>
                                            @endif

                                        </td>

                                        <td>
                                            {{ $fatherName }}
                                        </td>

                                        <td>
                                            {{ $motherName }}
                                        </td>

                                        <td>
                                            {{ $mobile }}
                                        </td>

                                        <td>
                                            {{ $formatBengaliDate($entryDate) }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="7" class="ur-empty-cell">

                                            নির্বাচিত শর্ত অনুযায়ী কোনো ইউজার পাওয়া যায়নি।

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>


                    <div class="ur-report-footer">

                        মোট ইউজার:
                        {{ $toBengaliNumber($users->count()) }}

                    </div>


                    <div class="ur-report-signatures">

                        <div>
                            প্রস্তুতকারীর স্বাক্ষর
                        </div>

                        <div>
                            যাচাইকারীর স্বাক্ষর
                        </div>

                        <div>
                            অনুমোদনকারীর স্বাক্ষর
                        </div>

                    </div>

                </div>

            @endif


            {{-- ================================================= --}}
            {{-- REPORT 2: USER STATISTICS --}}
            {{-- ================================================= --}}

            @if($reportType === 2)

                @php
                    $statFilterParts = [];

                    if (!empty($gender) && isset($genderLabels[$gender])) {
                        $statFilterParts[] = 'লিঙ্গ: ' . $genderLabels[$gender];
                    }

                    if (!empty($status) && isset($statusLabels[$status])) {
                        $statFilterParts[] = 'অবস্থা: ' . $statusLabels[$status];
                    }
                @endphp

                <div class="ur-report-paper">

                    <div class="ur-report-heading">

                        <h2>{{ $institutionName }}</h2>

                        @if($institutionAddress)
                            <p>{{ $institutionAddress }}</p>
                        @endif

                        @if($institutionPhone)
                            <p>{{ $institutionPhone }}</p>
                        @endif

                        <h3>
                            {{ $reportTitle ?: 'সকল ইউজারের পরিসংখ্যান' }}
                        </h3>

                        @if(count($statFilterParts))
                            <p class="ur-report-date">
                                {{ implode(' | ', $statFilterParts) }}
                            </p>
                        @endif

                    </div>


                    <div class="ur-table-responsive">

                        <table class="ur-report-table ur-stat-table">

                            <thead>

                                <tr>

                                    <th>ক্রমিক</th>

                                    <th>ইউজারের ধরন</th>

                                    <th>ইউজারের সংখ্যা</th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse($byRole as $item)

                                    <tr>

                                        <td>
                                            {{ data_get($item, 'serial') }}
                                        </td>

                                        <td>
                                            {{ data_get($item, 'name_bn')
                                                ?? data_get($item, 'name')
                                                ?? data_get($item, 'slug')
                                                ?? 'অন্যান্য' }}
                                        </td>

                                        <td>
                                            {{ data_get($item, 'total', 0) }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="3" class="ur-empty-cell">
                                            পরিসংখ্যানের কোনো তথ্য পাওয়া যায়নি।
                                        </td>

                                    </tr>

                                @endforelse

                                <tr class="ur-stat-total-row">

                                    <th colspan="2" class="ur-stat-total-label">
                                        মোট =
                                    </th>

                                    <th>
                                        {{ data_get($statistics, 'total', 0) }}
                                    </th>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            @endif


            {{-- ================================================= --}}
            {{-- REPORT 3: BLANK STUDENT ADMISSION FORM --}}
            {{-- ================================================= --}}

            @if($reportType === 3)

                <div class="ur-admission-paper">

                    <div class="ur-admission-header">

                        <div class="ur-admission-logo">

                            @if(!empty($institution->logo))

                                <img
                                    src="{{ asset($institution->logo) }}"
                                    alt="মাদরাসার লোগো"
                                    onerror="this.style.display='none'"
                                >

                            @else

                                <div class="ur-logo-placeholder">
                                    <i class="fa fa-graduation-cap"></i>
                                </div>

                            @endif

                        </div>


                        <div class="ur-admission-institution">

                            <h2>{{ $institutionName }}</h2>

                            @if($institutionAddress)
                                <p>{{ $institutionAddress }}</p>
                            @endif

                            @if($institutionPhone)
                                <p>ফোন: {{ $institutionPhone }}</p>
                            @endif

                        </div>


                        <div class="ur-admission-photo">

                            <span>শিক্ষার্থীর</span>
                            <span>ছবি</span>

                        </div>

                    </div>


                    <div class="ur-previous-info">

                        <h3>বিগত তথ্য</h3>

                        <div class="ur-admission-grid ur-grid-three">

                            <div>শ্রেণি/জামাত: <span></span></div>

                            <div>শিক্ষাবর্ষ: <span></span></div>

                            <div>আইডি নং: <span></span></div>

                        </div>

                        <div class="ur-checkbox-row">

                            <span>আবাসিক ☐</span>

                            <span>অনাবাসিক ☐</span>

                            <span>ডে কেয়ার ☐</span>

                        </div>

                    </div>


                    <div class="ur-current-info">

                        <div class="ur-admission-grid ur-grid-three">

                            <div>বর্তমান শ্রেণি/জামাত: <span></span></div>

                            <div>শিক্ষাবর্ষ: <span></span></div>

                            <div>আইডি নং: <span></span></div>

                        </div>

                    </div>


                    <div class="ur-admission-title">
                        <h2>ভর্তি ফরম</h2>
                    </div>


                    <div class="ur-application-text">

                        <p>বরাবর</p>

                        <p>
                            মুহতারাম, হযরত মুহতামিম সাহেব (দা. বা.)
                        </p>

                        <p>{{ $institutionName }}</p>

                        <p>
                            বিষয়: মাদরাসায় ভর্তি হওয়ার আবেদন।
                        </p>

                        <p>
                            আসসালামু আলাইকুম ওয়া রহমাতুল্লাহ।
                        </p>

                        <p>
                            বিনীত নিবেদন এই যে, আমি আপনার মাদরাসায় ভর্তি হয়ে
                            দ্বীনি শিক্ষা অর্জন করতে ইচ্ছুক। অতএব, আমাকে
                            আপনার মাদরাসায় ভর্তি হওয়ার সুযোগ দিয়ে বাধিত করবেন।
                        </p>

                        <p>নিবেদক</p>

                    </div>


                    <div class="ur-admission-section">

                        <h3>ব্যক্তিগত তথ্য</h3>

                        <div class="ur-line-field">
                            <label>১। শিক্ষার্থীর নাম:</label>
                            <span></span>
                        </div>

                        <div class="ur-admission-grid ur-grid-two">

                            <div class="ur-line-field">
                                <label>২। পিতার নাম:</label>
                                <span></span>
                            </div>

                            <div class="ur-line-field">
                                <label>৩। মাতার নাম:</label>
                                <span></span>
                            </div>

                        </div>

                        <div class="ur-admission-grid ur-grid-two">

                            <div class="ur-line-field">
                                <label>৪। জন্ম তারিখ:</label>
                                <span></span>
                            </div>

                            <div class="ur-line-field">
                                <label>৫। NID/জন্ম নিবন্ধন নং:</label>
                                <span></span>
                            </div>

                        </div>

                        <div class="ur-line-field">
                            <label>৬। অভিভাবকের মোবাইল নম্বর:</label>
                            <span></span>
                        </div>

                    </div>


                    <div class="ur-admission-section">

                        <h3>স্থায়ী ঠিকানা</h3>

                        <div class="ur-admission-grid ur-grid-two">

                            <div class="ur-line-field">
                                <label>গ্রাম/মহল্লা:</label>
                                <span></span>
                            </div>

                            <div class="ur-line-field">
                                <label>থানা:</label>
                                <span></span>
                            </div>

                        </div>

                        <div class="ur-admission-grid ur-grid-two">

                            <div class="ur-line-field">
                                <label>ডাক:</label>
                                <span></span>
                            </div>

                            <div class="ur-line-field">
                                <label>জেলা:</label>
                                <span></span>
                            </div>

                        </div>

                    </div>


                    <div class="ur-admission-section">

                        <h3>বর্তমান ঠিকানা</h3>

                        <div class="ur-admission-grid ur-grid-two">

                            <div class="ur-line-field">
                                <label>গ্রাম/মহল্লা:</label>
                                <span></span>
                            </div>

                            <div class="ur-line-field">
                                <label>থানা:</label>
                                <span></span>
                            </div>

                        </div>

                        <div class="ur-admission-grid ur-grid-two">

                            <div class="ur-line-field">
                                <label>ডাক:</label>
                                <span></span>
                            </div>

                            <div class="ur-line-field">
                                <label>জেলা:</label>
                                <span></span>
                            </div>

                        </div>

                    </div>


                    <div class="ur-admission-section">

                        <h3>অভিভাবকের তথ্য</h3>

                        <div class="ur-admission-grid ur-grid-two">

                            <div class="ur-line-field">
                                <label>অভিভাবকের নাম:</label>
                                <span></span>
                            </div>

                            <div class="ur-line-field">
                                <label>শিক্ষার্থীর সঙ্গে সম্পর্ক:</label>
                                <span></span>
                            </div>

                        </div>

                        <div class="ur-signature-row">

                            <div>
                                অভিভাবকের স্বাক্ষর: __________________
                            </div>

                            <div>
                                তারিখ: ______________
                            </div>

                        </div>

                    </div>


                    <div class="ur-office-section">

                        <h3>অফিসের অংশ</h3>

                        <div class="ur-admission-grid ur-grid-two">

                            <div class="ur-line-field">
                                <label>তালিমী মুরুব্বির নাম:</label>
                                <span></span>
                            </div>

                            <div class="ur-line-field">
                                <label>শ্রেণি/জামাত:</label>
                                <span></span>
                            </div>

                        </div>

                        <div class="ur-signature-row">

                            <div>
                                তালিমী মুরুব্বির স্বাক্ষর: ______________
                            </div>

                            <div>
                                তারিখ: ______________
                            </div>

                        </div>

                        <div class="ur-line-field">
                            <label>দারুল ইকামা/শ্রেণি শিক্ষকের মতামত:</label>
                            <span></span>
                        </div>

                        <div class="ur-signature-row">

                            <div>
                                স্বাক্ষর: __________________________
                            </div>

                            <div>
                                তারিখ: ______________
                            </div>

                        </div>

                        <div class="ur-line-field">
                            <label>পরীক্ষকের মন্তব্য:</label>
                            <span></span>
                        </div>

                        <div class="ur-signature-row">

                            <div>
                                পরীক্ষকের স্বাক্ষর: __________________
                            </div>

                            <div>
                                তারিখ: ______________
                            </div>

                        </div>

                    </div>


                    <div class="ur-admission-section">

                        <h3>ফলাফল</h3>

                        <div class="ur-admission-grid ur-grid-three">

                            <div class="ur-line-field">
                                <label>পূর্ববর্তী পরীক্ষার ফলাফল:</label>
                                <span></span>
                            </div>

                            <div class="ur-line-field">
                                <label>মোট নম্বর:</label>
                                <span></span>
                            </div>

                            <div class="ur-line-field">
                                <label>গড়:</label>
                                <span></span>
                            </div>

                        </div>

                        <div class="ur-admission-grid ur-grid-two">

                            <div class="ur-line-field">
                                <label>বিভাগ:</label>
                                <span></span>
                            </div>

                            <div class="ur-line-field">
                                <label>মেধাস্থান:</label>
                                <span></span>
                            </div>

                        </div>

                    </div>


                    <div class="ur-admission-section">

                        <h3>আর্থিক অবস্থা</h3>

                        <div class="ur-checkbox-row">

                            <span>সচ্ছল ☐</span>
                            <span>এতিম ☐</span>
                            <span>গরিব ☐</span>
                            <span>অসহায় ☐</span>

                        </div>

                        <div class="ur-line-field">
                            <label>নাজিমে তালিমাতের মন্তব্য:</label>
                            <span></span>
                        </div>

                        <div class="ur-signature-row">

                            <div>
                                নাজিমে তালিমাতের স্বাক্ষর: ______________
                            </div>

                            <div>
                                তারিখ: ______________
                            </div>

                        </div>

                    </div>


                    <div class="ur-admission-section">

                        <h3>ফি সংক্রান্ত তথ্য</h3>

                        <div class="ur-admission-grid ur-grid-two">

                            <div class="ur-line-field">
                                <label>ভর্তি ফি:</label>
                                <span></span>
                            </div>

                            <div class="ur-line-field">
                                <label>মাসিক বেতন:</label>
                                <span></span>
                            </div>

                        </div>

                        <div class="ur-admission-grid ur-grid-two">

                            <div class="ur-line-field">
                                <label>আবাসিক ফি:</label>
                                <span></span>
                            </div>

                            <div class="ur-line-field">
                                <label>অন্যান্য ফি:</label>
                                <span></span>
                            </div>

                        </div>

                    </div>


                    <div class="ur-final-approval">

                        <h3>মুহতামিম সাহেবের অনুমোদন</h3>

                        <p>
                            ভর্তি অনুমোদিত / অনুমোদিত নয়:
                            ☐ অনুমোদিত
                            &nbsp;&nbsp;
                            ☐ অনুমোদিত নয়
                        </p>

                        <div class="ur-signature-row">

                            <div>
                                স্বাক্ষর: __________________________
                            </div>

                            <div>
                                তারিখ: ______________
                            </div>

                        </div>

                        <div class="ur-seal-area">
                            <span>অফিসের সিল</span>
                        </div>

                    </div>

                </div>

            @endif


            {{-- ================================================= --}}
            {{-- REPORT 4: STUDENTS WITHOUT ACTIVE ADMISSION --}}
            {{-- ================================================= --}}

            @if($reportType === 4)

                <div class="ur-report-paper">

                    <div class="ur-report-heading">

                        <h2>{{ $institutionName }}</h2>

                        @if($institutionAddress)
                            <p>{{ $institutionAddress }}</p>
                        @endif

                        @if($institutionPhone)
                            <p>যোগাযোগ: {{ $institutionPhone }}</p>
                        @endif

                        <h3>
                            {{ $reportTitle ?: 'ভর্তি সম্পন্ন হয়নি এমন শিক্ষার্থীর তালিকা' }}
                        </h3>

                    </div>


                    <div class="ur-table-responsive">

                        <table class="ur-report-table">

                            <thead>

                                <tr>

                                    <th>ক্রমিক</th>

                                    <th>শিক্ষার্থীর আইডি</th>

                                    <th>শিক্ষার্থীর নাম</th>

                                    <th>মোবাইল</th>

                                    <th>শ্রেণি/জামাত</th>

                                    <th>শিক্ষাবর্ষ</th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse($studentsWithoutAdmission as $index => $student)

                                    @php
                                        $studentUser = $student->user;

                                        $studentName = $firstFilled(
                                            [$studentUser, $student],
                                            $nameKeys
                                        );

                                        $studentMobile = $firstFilled(
                                            [$studentUser, $student],
                                            ['phone', 'mobile', 'phone_number', 'guardian_mobile']
                                        );
                                    @endphp

                                    <tr>

                                        <td>
                                            {{ $toBengaliNumber($index + 1) }}
                                        </td>

                                        <td>
                                            {{ $toBengaliNumber(
                                                $studentUser->institution_user_id
                                                ?? $student->institution_user_id
                                                ?? '—'
                                            ) }}
                                        </td>

                                        <td>
                                            {{ $studentName }}
                                        </td>

                                        <td>
                                            {{ $studentMobile }}
                                        </td>

                                        <td>
                                            {{ $student->class->name
                                                ?? $student->class_name
                                                ?? '—' }}
                                        </td>

                                        <td>
                                            {{ $student->academicYear->name
                                                ?? $student->academic_year
                                                ?? '—' }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="6" class="ur-empty-cell">
                                            নির্বাচিত শর্ত অনুযায়ী কোনো শিক্ষার্থী পাওয়া যায়নি।
                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>


                    <div class="ur-report-footer">
                        মোট শিক্ষার্থী:
                        {{ $toBengaliNumber($studentsWithoutAdmission->count()) }}
                    </div>

                </div>

            @endif

        </div>

    @endif

</div>


{{-- ================================================= --}}
{{-- CSS --}}
{{-- ================================================= --}}

<style>

    .ur-page {
        padding: 20px;
        color: #243247;
        font-family: inherit;
    }

    .ur-page-header,
    .ur-card-header,
    .ur-preview-toolbar,
    .ur-form-actions,
    .ur-signature-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .ur-page-header {
        margin-bottom: 22px;
        flex-wrap: wrap;
    }

    .ur-page-title {
        margin: 0 0 7px;
        font-size: 25px;
        font-weight: 700;
        color: #172b4d;
    }

    .ur-page-title i {
        margin-right: 8px;
        color: #2563eb;
    }

    .ur-page-subtitle {
        margin: 0;
        color: #718096;
        font-size: 14px;
    }

    .ur-card {
        background: #fff;
        border: 1px solid #e7edf5;
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(26, 42, 70, .045);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .ur-card-header {
        justify-content: flex-start;
        padding: 22px 24px;
        border-bottom: 1px solid #edf1f7;
    }

    .ur-card-header-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eaf1ff;
        color: #2563eb;
        font-size: 20px;
        flex-shrink: 0;
    }

    .ur-card-header h3 {
        margin: 0 0 5px;
        font-size: 18px;
        font-weight: 700;
    }

    .ur-card-header p {
        margin: 0;
        color: #718096;
        font-size: 13px;
    }

    .ur-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        padding: 24px;
    }

    .ur-form-group {
        min-width: 0;
    }

    .ur-form-group-full {
        grid-column: 1 / -1;
    }

    .ur-form-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #344256;
    }

    .ur-required {
        color: #dc2626;
    }

    .ur-form-control {
        width: 100%;
        min-height: 44px;
        padding: 10px 13px;
        border: 1px solid #dce3ed;
        border-radius: 8px;
        background: #fff;
        color: #243247;
        font-size: 14px;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }

    .ur-form-control:focus {
        border-color: #5286f5;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .10);
    }

    .ur-form-actions {
        justify-content: flex-start;
        padding: 0 24px 24px;
        flex-wrap: wrap;
    }

    .ur-btn {
        min-height: 42px;
        padding: 10px 17px;
        border: 1px solid transparent;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none !important;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
    }

    .ur-btn-primary {
        background: #2563eb;
        border-color: #2563eb;
        color: #fff !important;
    }

    .ur-btn-primary:hover {
        background: #1d4ed8;
    }

    .ur-btn-light {
        background: #f4f6fa;
        border-color: #e2e8f0;
        color: #344256 !important;
    }

    .ur-btn-dark {
        background: #172b4d;
        color: #fff !important;
    }

    .ur-info-box {
        display: flex;
        gap: 12px;
        margin: 0 24px 20px;
        padding: 15px 17px;
        border: 1px solid #bfdbfe;
        border-radius: 9px;
        background: #eff6ff;
        color: #1e40af;
    }

    .ur-info-box > i {
        margin-top: 3px;
        font-size: 18px;
    }

    .ur-info-box strong {
        display: block;
        margin-bottom: 4px;
    }

    .ur-info-box p {
        margin: 0;
        font-size: 13px;
        line-height: 1.6;
    }

    .ur-alert {
        margin-bottom: 20px;
        padding: 15px 18px;
        border-radius: 9px;
    }

    .ur-alert-danger {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }

    .ur-alert ul {
        margin: 8px 0 0;
        padding-left: 22px;
    }

    .ur-preview-section {
        margin-top: 30px;
    }

    .ur-preview-toolbar {
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .ur-preview-toolbar h3 {
        margin: 0 0 5px;
        font-size: 21px;
        color: #172b4d;
    }

    .ur-preview-toolbar h3 i {
        color: #2563eb;
        margin-right: 6px;
    }

    .ur-preview-toolbar p {
        margin: 0;
        color: #718096;
        font-size: 13px;
    }

    .ur-report-paper,
    .ur-admission-paper {
        max-width: 1100px;
        margin: 0 auto 25px;
        padding: 30px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 4px 18px rgba(26, 42, 70, .05);
    }

    .ur-report-heading {
        text-align: center;
        padding-bottom: 18px;
        margin-bottom: 20px;
        border-bottom: 2px solid #27364b;
    }

    .ur-report-heading h2 {
        margin: 0 0 7px;
        font-size: 25px;
        font-weight: 800;
        color: #172b4d;
    }

    .ur-report-heading p {
        margin: 4px 0;
        color: #4b5563;
        font-size: 13px;
    }

    .ur-report-heading h3 {
        display: inline-block;
        margin: 12px 0 0;
        padding: 7px 22px;
        border: 1px solid #cbd5e1;
        border-radius: 5px;
        font-size: 18px;
        font-weight: 700;
    }

    .ur-table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .ur-report-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .ur-report-table th,
    .ur-report-table td {
        padding: 10px 9px;
        border: 1px solid #d9e1ec;
        text-align: center;
        vertical-align: middle;
    }

    .ur-report-table th {
        background: #f1f5f9;
        color: #243247;
        font-weight: 700;
        white-space: nowrap;
    }

    .ur-report-table tbody tr:nth-child(even) {
        background: #fafcff;
    }

    .ur-student-list-table {
        table-layout: fixed;
    }

    .ur-student-list-table th:nth-child(1) {
        width: 7%;
    }

    .ur-student-list-table th:nth-child(2) {
        width: 9%;
    }

    .ur-student-list-table th:nth-child(3) {
        width: 19%;
    }

    .ur-student-list-table th:nth-child(4),
    .ur-student-list-table th:nth-child(5) {
        width: 17%;
    }

    .ur-student-list-table th:nth-child(6) {
        width: 15%;
    }

    .ur-student-list-table th:nth-child(7) {
        width: 16%;
    }

    .ur-student-list-table td {
        overflow-wrap: anywhere;
    }

    /* Statistics table */

    .ur-stat-table {
        table-layout: fixed;
        font-size: 15px;
    }

    .ur-stat-table th:nth-child(1) {
        width: 15%;
    }

    .ur-stat-table th:nth-child(2) {
        width: 55%;
    }

    .ur-stat-table th:nth-child(3) {
        width: 30%;
    }

    .ur-stat-table td,
    .ur-stat-table th {
        padding: 12px 10px;
    }

    .ur-stat-table .ur-stat-total-row th {
        background: #f1f5f9;
        font-size: 16px;
    }

    .ur-stat-table .ur-stat-total-label {
        text-align: right;
        padding-right: 18px;
    }

    .ur-user-type-sub {
        display: block;
        margin-top: 3px;
        color: #64748b;
        font-size: 10px;
    }

    .ur-empty-cell {
        padding: 25px !important;
        text-align: center !important;
        color: #718096;
    }

    .ur-report-footer {
        padding-top: 15px;
        margin-top: 15px;
        border-top: 1px solid #e2e8f0;
        text-align: right;
        font-size: 13px;
        font-weight: 700;
    }

    .ur-report-signatures {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin-top: 65px;
        text-align: center;
        font-size: 12px;
    }

    .ur-report-signatures > div {
        min-width: 150px;
        padding-top: 8px;
        border-top: 1px solid #333;
    }

    .ur-section-heading {
        margin: 0 0 14px;
        font-size: 17px;
        font-weight: 700;
    }


    /* ADMISSION FORM */

    .ur-admission-paper {
        max-width: 900px;
        padding: 28px 35px;
        color: #111827;
        font-family: "Noto Sans Bengali", "SolaimanLipi", Arial, sans-serif;
        font-size: 13px;
        line-height: 1.65;
        border: 1px solid #cbd5e1;
    }

    .ur-admission-header {
        display: grid;
        grid-template-columns: 75px 1fr 95px;
        align-items: center;
        gap: 12px;
        padding-bottom: 15px;
        border-bottom: 2px solid #111827;
    }

    .ur-admission-logo {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ur-admission-logo img {
        max-width: 100%;
        max-height: 70px;
        object-fit: contain;
    }

    .ur-logo-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        border: 1px solid #9ca3af;
        border-radius: 50%;
        font-size: 25px;
    }

    .ur-admission-institution {
        text-align: center;
    }

    .ur-admission-institution h2 {
        margin: 0 0 5px;
        font-size: 23px;
        font-weight: 800;
    }

    .ur-admission-institution p {
        margin: 2px 0;
        font-size: 12px;
    }

    .ur-admission-photo {
        height: 95px;
        border: 1px solid #6b7280;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #4b5563;
        line-height: 1.8;
    }

    .ur-previous-info,
    .ur-current-info,
    .ur-admission-section,
    .ur-office-section {
        margin-top: 16px;
    }

    .ur-previous-info {
        padding: 12px;
        border: 1px solid #9ca3af;
    }

    .ur-previous-info h3,
    .ur-admission-section h3,
    .ur-office-section h3,
    .ur-final-approval h3 {
        margin: 0 0 12px;
        padding: 5px 8px;
        font-size: 15px;
        font-weight: 800;
        background: #f3f4f6;
        border-left: 3px solid #374151;
    }

    .ur-admission-grid {
        display: grid;
        gap: 12px 18px;
    }

    .ur-grid-three {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .ur-grid-two {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .ur-admission-grid > div {
        min-width: 0;
    }

    .ur-admission-grid .ur-line-field {
        margin-bottom: 0;
    }

    .ur-checkbox-row {
        display: flex;
        flex-wrap: wrap;
        gap: 22px;
        margin-top: 10px;
    }

    .ur-admission-title {
        text-align: center;
        margin: 18px 0;
    }

    .ur-admission-title h2 {
        display: inline-block;
        margin: 0;
        padding: 5px 35px;
        border: 1px solid #374151;
        font-size: 20px;
        font-weight: 800;
    }

    .ur-application-text {
        margin: 10px 0 18px;
        line-height: 1.9;
    }

    .ur-application-text p {
        margin: 3px 0;
    }

    .ur-line-field {
        display: flex;
        align-items: flex-end;
        gap: 6px;
        min-height: 31px;
        margin-bottom: 8px;
    }

    .ur-line-field label {
        flex-shrink: 0;
        font-weight: 600;
    }

    .ur-line-field > span {
        flex: 1;
        min-width: 20px;
        height: 23px;
        border-bottom: 1px dotted #6b7280;
    }

    .ur-signature-row {
        margin-top: 12px;
        flex-wrap: wrap;
        font-size: 12px;
    }

    .ur-office-section {
        padding: 12px;
        border: 1px solid #9ca3af;
    }

    .ur-final-approval {
        position: relative;
        margin-top: 20px;
        padding: 14px;
        border: 1px solid #6b7280;
    }

    .ur-final-approval p {
        margin: 12px 0;
    }

    .ur-seal-area {
        position: absolute;
        right: 15px;
        bottom: 15px;
        width: 75px;
        height: 65px;
        border: 1px dashed #9ca3af;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        font-size: 11px;
    }


    /* RESPONSIVE */

    @media (max-width: 768px) {

        .ur-page {
            padding: 12px;
        }

        .ur-form-grid {
            grid-template-columns: 1fr;
            padding: 17px;
            gap: 16px;
        }

        .ur-form-group-full {
            grid-column: auto;
        }

        .ur-card-header {
            padding: 17px;
        }

        .ur-form-actions {
            padding: 0 17px 17px;
        }

        .ur-report-paper,
        .ur-admission-paper {
            padding: 16px;
        }

        .ur-admission-header {
            grid-template-columns: 50px 1fr 65px;
            gap: 7px;
        }

        .ur-admission-logo {
            width: 48px;
            height: 48px;
        }

        .ur-admission-institution h2 {
            font-size: 16px;
        }

        .ur-admission-institution p {
            font-size: 10px;
        }

        .ur-admission-photo {
            height: 70px;
            font-size: 10px;
        }

        .ur-grid-three,
        .ur-grid-two {
            grid-template-columns: 1fr;
        }

        .ur-signature-row {
            align-items: flex-start;
            flex-direction: column;
        }

    }


    @media (max-width: 480px) {

        .ur-page-title {
            font-size: 21px;
        }

        .ur-admission-paper {
            padding: 10px;
            font-size: 11px;
        }

        .ur-admission-institution h2 {
            font-size: 13px;
        }

        .ur-line-field {
            flex-wrap: wrap;
        }

    }

</style>


{{-- ================================================= --}}
{{-- PRINT JAVASCRIPT --}}
{{-- ================================================= --}}

<script>

    /**
     * Print only the report (A4 Portrait).
     *
     * - Dashboard, sidebar, navbar and filter form are NOT included.
     * - @page margin is 0, so the browser's own header / footer /
     *   page number / URL are not printed.
     * - Page margins are created with a repeating spacer table
     *   (thead / tfoot), so every page keeps top & bottom space.
     */
    async function printUserReport() {

        const report = document.getElementById('userReportPreview');

        if (!report) {

            alert('প্রিন্ট করার মতো কোনো রিপোর্ট পাওয়া যায়নি।');

            return;
        }

        const printWindow = window.open(
            '',
            '_blank',
            'width=1100,height=800'
        );

        if (!printWindow) {

            alert('Popup blocked হয়েছে। Browser থেকে popup allow করুন।');

            return;
        }


        /*
         * Copy external stylesheets.
         */
        const stylesheetLinks = Array.from(
            document.querySelectorAll('link[rel="stylesheet"]')
        );

        const styles = stylesheetLinks.map(link => {

            return `<link rel="stylesheet" href="${link.href}">`;

        }).join('');


        /*
         * Copy inline styles (the report CSS above).
         */
        const inlineStyles = Array.from(
            document.querySelectorAll('style')
        ).map(style => {

            return style.innerHTML;

        }).join('\n');


        /*
         * Only the report content, without the toolbar.
         */
        const clone = report.cloneNode(true);

        clone.querySelectorAll('.no-print, .ur-preview-toolbar')
            .forEach(function (el) {
                el.remove();
            });

        const reportHTML = clone.innerHTML;


        /*
         * Open isolated document.
         */
        printWindow.document.open();

        printWindow.document.write(`

            <!DOCTYPE html>

            <html lang="bn">

            <head>

                <meta charset="UTF-8">

                <meta
                    name="viewport"
                    content="width=device-width, initial-scale=1.0"
                >

                <title>&nbsp;</title>

                ${styles}

                <style>

                    ${inlineStyles}

                </style>

                <style>

                    /*
                     * margin: 0  =>  browser header/footer/page number hidden.
                     */
                    @page {
                        size: A4 portrait;
                        margin: 0;
                    }

                    * {
                        box-sizing: border-box;
                    }

                    html,
                    body {
                        width: 100%;
                        margin: 0 !important;
                        padding: 0 !important;
                        background: #fff !important;
                        color: #111827 !important;
                        font-family: "Noto Sans Bengali", "SolaimanLipi", Arial, sans-serif;
                    }

                    body,
                    body * {
                        visibility: visible !important;
                    }

                    table.print-wrap {
                        width: 100%;
                        border-collapse: collapse;
                        border: 0 !important;
                    }

                    table.print-wrap > thead > tr > td,
                    table.print-wrap > tfoot > tr > td,
                    table.print-wrap > tbody > tr > td {
                        border: 0 !important;
                        padding: 0 !important;
                        background: #fff !important;
                    }

                    table.print-wrap > thead { display: table-header-group; }
                    table.print-wrap > tfoot { display: table-footer-group; }

                    .print-spacer {
                        height: 12mm;
                    }

                    .print-body {
                        padding: 0 14mm;
                    }

                    #userReportPreview {
                        display: block !important;
                        width: 100% !important;
                        max-width: 100% !important;
                        margin: 0 !important;
                        padding: 0 !important;
                        position: static !important;
                        background: #fff !important;
                    }

                    .no-print,
                    .ur-preview-toolbar,
                    button,
                    .ur-btn {
                        display: none !important;
                    }

                    .ur-report-paper,
                    .ur-admission-paper {
                        width: 100% !important;
                        max-width: 100% !important;
                        margin: 0 auto !important;
                        padding: 0 !important;
                        border: none !important;
                        border-radius: 0 !important;
                        box-shadow: none !important;
                        background: #fff !important;
                    }

                    .ur-admission-paper {
                        font-size: 12px !important;
                        line-height: 1.55 !important;
                    }

                    .ur-report-heading {
                        text-align: center !important;
                        margin-bottom: 15px !important;
                        padding-bottom: 12px !important;
                        border-bottom: 2px solid #222 !important;
                    }

                    .ur-report-heading h2 {
                        font-size: 21px !important;
                        color: #111 !important;
                    }

                    .ur-report-heading h3 {
                        font-size: 16px !important;
                        border: none !important;
                        padding: 5px 0 !important;
                    }

                    .ur-report-heading p {
                        font-size: 11px !important;
                        color: #222 !important;
                    }

                    .ur-table-responsive {
                        width: 100% !important;
                        overflow: visible !important;
                    }

                    .ur-report-table {
                        width: 100% !important;
                        border-collapse: collapse !important;
                        table-layout: fixed !important;
                        font-size: 10px !important;
                    }

                    .ur-report-table th,
                    .ur-report-table td {
                        padding: 7px 4px !important;
                        border: 1px solid #333 !important;
                        color: #111 !important;
                        text-align: center !important;
                        vertical-align: middle !important;
                        overflow-wrap: anywhere !important;
                    }

                    .ur-report-table th {
                        background: #eee !important;
                        font-weight: bold !important;
                    }

                    .ur-report-table thead {
                        display: table-header-group !important;
                    }

                    .ur-report-table tr {
                        break-inside: avoid !important;
                        page-break-inside: avoid !important;
                    }

                    .ur-student-list-table th,
                    .ur-student-list-table td {
                        font-size: 9px !important;
                    }

                    .ur-stat-table {
                        font-size: 14px !important;
                    }

                    .ur-stat-table th,
                    .ur-stat-table td {
                        padding: 10px 8px !important;
                        font-size: 14px !important;
                    }

                    .ur-stat-table .ur-stat-total-label {
                        text-align: right !important;
                        padding-right: 16px !important;
                    }

                    .ur-user-type-sub {
                        display: block !important;
                        font-size: 8px !important;
                    }

                    .ur-report-footer {
                        margin-top: 12px !important;
                        padding-top: 8px !important;
                        border-top: 1px solid #333 !important;
                        font-size: 11px !important;
                    }

                    .ur-report-signatures {
                        margin-top: 55px !important;
                        display: flex !important;
                        justify-content: space-between !important;
                    }

                    .ur-admission-section,
                    .ur-office-section,
                    .ur-previous-info,
                    .ur-final-approval {
                        break-inside: avoid !important;
                        page-break-inside: avoid !important;
                    }

                    .ur-report-paper {
                        break-after: auto !important;
                        page-break-after: auto !important;
                    }

                    * {
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }

                </style>

            </head>

            <body>

                <table class="print-wrap">

                    <thead>
                        <tr><td><div class="print-spacer"></div></td></tr>
                    </thead>

                    <tfoot>
                        <tr><td><div class="print-spacer"></div></td></tr>
                    </tfoot>

                    <tbody>
                        <tr>
                            <td>
                                <div class="print-body">
                                    <div id="userReportPreview">
                                        ${reportHTML}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>

                </table>

            </body>

            </html>

        `);

        printWindow.document.close();


        const waitForStylesheets = async function () {

            const links = Array.from(
                printWindow.document.querySelectorAll('link[rel="stylesheet"]')
            );

            await Promise.all(

                links.map(function (link) {

                    return new Promise(function (resolve) {

                        if (link.sheet) {
                            resolve();
                            return;
                        }

                        link.onload = resolve;
                        link.onerror = resolve;

                        setTimeout(resolve, 4000);

                    });

                })

            );

        };


        const waitForImages = async function () {

            const images = Array.from(
                printWindow.document.images
            );

            await Promise.all(

                images.map(function (img) {

                    if (img.complete) {
                        return Promise.resolve();
                    }

                    return new Promise(function (resolve) {

                        img.onload = resolve;

                        img.onerror = resolve;

                    });

                })

            );

        };


        await waitForStylesheets();

        await waitForImages();

        if (printWindow.document.fonts) {

            try {

                await printWindow.document.fonts.ready;

            } catch (error) {

                console.warn('Font loading issue:', error);

            }

        }


        setTimeout(function () {

            printWindow.focus();

            printWindow.print();

        }, 800);


        printWindow.onafterprint = function () {

            printWindow.close();

        };

    }


    /*
    |--------------------------------------------------------------------------
    | Report Filter Controls + Auto Preview
    |--------------------------------------------------------------------------
    |
    | Report 1 : all filters
    | Report 2 : gender + status only (auto update on change)
    | Report 3 : no filters (blank admission form)
    | Report 4 : all filters
    |
    | Selecting a report from "রিপোর্টসমূহ" submits the form automatically.
    | Hidden fields are also disabled, so they are not submitted.
    */

    document.addEventListener('DOMContentLoaded', function () {

        const form = document.getElementById('userReportForm');

        const reportTypeSelect = document.getElementById('report_type');

        const fields = {
            userType: document.getElementById('userTypeField'),
            gender: document.getElementById('genderField'),
            status: document.getElementById('statusField'),
            idFrom: document.getElementById('userIdFromField'),
            idTo: document.getElementById('userIdToField'),
        };

        const admissionNotice = document.getElementById('admissionFormNotice');

        const genderSelect = document.getElementById('gender');

        const statusSelect = document.getElementById('status');


        function toggleField(field, show) {

            if (!field) {
                return;
            }

            field.style.display = show ? '' : 'none';

            field.querySelectorAll('input, select').forEach(function (el) {
                el.disabled = !show;
            });

        }


        function submitReportForm() {

            if (!form) {
                return;
            }

            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.submit();
            }

        }


        function updateReportFields() {

            if (!reportTypeSelect) {
                return;
            }

            const type = parseInt(reportTypeSelect.value, 10);

            let visible = {
                userType: true,
                gender: true,
                status: true,
                idFrom: true,
                idTo: true,
            };

            if (type === 2) {

                visible = {
                    userType: false,
                    gender: true,
                    status: true,
                    idFrom: false,
                    idTo: false,
                };

            }

            if (type === 3) {

                visible = {
                    userType: false,
                    gender: false,
                    status: false,
                    idFrom: false,
                    idTo: false,
                };

            }

            Object.keys(fields).forEach(function (key) {
                toggleField(fields[key], visible[key]);
            });


            /*
             * "অন্যান্য" gender option only for list reports.
             */
            if (genderSelect) {

                const otherOption = genderSelect.querySelector('option[value="other"]');

                if (otherOption) {

                    const hideOther = (type === 2);

                    otherOption.hidden = hideOther;
                    otherOption.disabled = hideOther;

                    if (hideOther && genderSelect.value === 'other') {
                        genderSelect.value = '';
                    }

                }

            }


            if (admissionNotice) {
                admissionNotice.style.display = (type === 3) ? 'flex' : 'none';
            }

        }


        if (reportTypeSelect) {

            updateReportFields();

            /*
             * Select a report => preview opens automatically.
             */
            reportTypeSelect.addEventListener('change', function () {

                updateReportFields();

                submitReportForm();

            });

        }


        /*
         * Statistics report: gender / status change => auto refresh.
         */
        [genderSelect, statusSelect].forEach(function (el) {

            if (!el) {
                return;
            }

            el.addEventListener('change', function () {

                if (reportTypeSelect && parseInt(reportTypeSelect.value, 10) === 2) {
                    submitReportForm();
                }

            });

        });


        /*
         * Scroll to preview after generation.
         */
        @if($previewGenerated)

            const preview = document.getElementById('userReportPreview');

            if (preview) {

                setTimeout(function () {

                    preview.scrollIntoView({

                        behavior: 'smooth',

                        block: 'start'

                    });

                }, 150);

            }

        @endif

    });

</script>

@endsection