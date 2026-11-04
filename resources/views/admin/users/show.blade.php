@extends('layouts.admin')

@section('title', 'ইউজার তথ্য')

@section('page')

@php
    /*
    |--------------------------------------------------------------------------
    | User Type
    |--------------------------------------------------------------------------
    */

    $userType = $user->userType;

    $userTypeSlug = optional($userType)->slug ?? '';

    $userTypeName = match($userTypeSlug) {
        'teacher' => 'শিক্ষক/স্টাফ',
        default => optional($userType)->display_name
            ?? optional($userType)->name_bn
            ?? optional($userType)->name
            ?? 'নির্ধারিত নয়',
    };

    /*
    |--------------------------------------------------------------------------
    | User Type Badge Class
    |--------------------------------------------------------------------------
    */

    $userTypeClass = match($userTypeSlug) {
        'student'        => 'type-student',
        'teacher'        => 'type-teacher',
        'staff'          => 'type-teacher',
        'guardian'       => 'type-guardian',
        'donor_member'   => 'type-donor',
        'library_member' => 'type-library',
        'software_user'  => 'type-software',
        default          => 'type-default',
    };

    /*
    |--------------------------------------------------------------------------
    | Student Information
    |--------------------------------------------------------------------------
    */

    $student = $userTypeSlug === 'student'
        ? $user->student
        : null;

    /*
    |--------------------------------------------------------------------------
    | Date Formatting
    |--------------------------------------------------------------------------
    */

    $formatDate = function ($date) {
        if (!$date) {
            return '—';
        }

        return \Carbon\Carbon::parse($date)->format('d M Y');
    };

    $formatDateTime = function ($date) {
        if (!$date) {
            return '—';
        }

        return \Carbon\Carbon::parse($date)->format('d M Y, h:i A');
    };

    /*
    |--------------------------------------------------------------------------
    | Gender
    |--------------------------------------------------------------------------
    */

    $gender = match($user->gender) {
        'male'   => 'পুরুষ',
        'female' => 'মহিলা',
        default  => $user->gender ?: '—',
    };

@endphp


<style>
    @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap');

    .user-view-page {
        font-family: 'Hind Siliguri', sans-serif;
        background: #f0f4ff;
        min-height: 100vh;
        padding: 28px 24px;
    }

    .user-view-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(30, 58, 138, 0.07);
        overflow: hidden;
    }

    .user-view-header {
        padding: 18px 22px;
        border-bottom: 1px solid #eef2fb;
        background: #fff;
    }

    .user-view-header-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
    }

    .user-view-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-view-title i {
        color: #3b5bdb;
        font-size: 1.15rem;
    }

    .user-view-title h5 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 700;
        color: #1a1a2e;
    }

    .btn-all-users {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #1e3a8a;
        color: #fff;
        border: none;
        border-radius: 9px;
        padding: 9px 16px;
        font-size: 0.88rem;
        font-weight: 600;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .btn-all-users:hover {
        background: #1e40af;
        color: #fff;
    }

    .user-view-body {
        padding: 28px 22px;
    }

    .user-profile-wrapper {
        max-width: 850px;
        margin: 0 auto;
    }

    .profile-top {
        display: flex;
        align-items: center;
        gap: 18px;
        padding: 20px;
        margin-bottom: 20px;
        background: #f8faff;
        border: 1px solid #eef2fb;
        border-radius: 12px;
    }

    .profile-photo {
        width: 82px;
        height: 82px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 3px 12px rgba(30, 58, 138, 0.12);
    }

    .profile-name {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 3px;
    }

    .profile-name-en {
        color: #9ca3af;
        font-size: 0.85rem;
        margin-bottom: 7px;
    }

    .profile-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 8px;
    }

    .user-type-badge {
        display: inline-block;
        padding: 5px 13px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* User Type */

    .type-student {
        background: #ede9fe;
        color: #6d28d9;
    }

    .type-teacher {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .type-guardian {
        background: #fef3c7;
        color: #92400e;
    }

    .type-donor {
        background: #fce7f3;
        color: #9d174d;
    }

    .type-library {
        background: #cffafe;
        color: #155e75;
    }

    .type-software {
        background: #e0e7ff;
        color: #3730a3;
    }

    .type-default {
        background: #e5e7eb;
        color: #374151;
    }

    .info-section {
        margin-bottom: 22px;
        border: 1px solid #eef2fb;
        border-radius: 10px;
        overflow: hidden;
    }

    .info-section-title {
        background: #f8faff;
        padding: 12px 15px;
        font-weight: 700;
        color: #1e3a8a;
        border-bottom: 1px solid #eef2fb;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .info-table tr {
        border-bottom: 1px solid #eef2fb;
    }

    .info-table tr:last-child {
        border-bottom: none;
    }

    .info-table td {
        padding: 13px 15px;
        font-size: 0.9rem;
        vertical-align: middle;
    }

    .info-table td:first-child {
        width: 28%;
        font-weight: 600;
        color: #6b7280;
        background: #f8faff;
    }

    .info-table td:nth-child(2) {
        width: 3%;
        text-align: center;
        color: #c4cad8;
    }

    .info-table td:last-child {
        color: #374151;
        font-weight: 500;
    }

    .id-badge {
        display: inline-block;
        background: #eef2fb;
        color: #3b5bdb;
        font-weight: 700;
        padding: 4px 11px;
        border-radius: 7px;
        font-size: 0.83rem;
    }

    .status-active {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #16a34a;
        font-weight: 600;
    }

    .status-inactive {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #dc2626;
        font-weight: 600;
    }

    .user-photo-large {
        width: 130px;
        height: 130px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #eef2fb;
        box-shadow: 0 3px 12px rgba(30, 58, 138, 0.08);
    }

    .empty-value {
        color: #b0b7c5;
        font-weight: 400;
    }

    @media (max-width: 768px) {
        .user-view-page {
            padding: 18px 12px;
        }

        .user-view-header-inner {
            flex-direction: column;
            align-items: flex-start;
        }

        .user-view-body {
            padding: 18px 12px;
        }

        .profile-top {
            padding: 15px;
        }

        .info-table td {
            padding: 11px 10px;
            font-size: 0.85rem;
        }

        .info-table td:first-child {
            width: 35%;
        }
    }
</style>


<div class="user-view-page">

    <div class="user-view-card">

        {{-- HEADER --}}
        <div class="user-view-header">

            <div class="user-view-header-inner">

                <div class="user-view-title">
                    <i class="fab fa-gg-circle"></i>
                    <h5>ইউজার তথ্য দেখুন</h5>
                </div>

                <a href="{{ route('dashboard.user.index') }}"
                   class="btn-all-users">
                    <i class="fas fa-th"></i>
                    সকল ইউজার
                </a>

            </div>

        </div>


        {{-- BODY --}}
        <div class="user-view-body">

            <div class="user-profile-wrapper">


                {{-- PROFILE TOP --}}
                <div class="profile-top">

                    @if($user->photo)

                        <img src="{{ asset('uploads/users/' . $user->photo) }}"
                             alt="{{ $user->name }}"
                             class="profile-photo">

                    @else

                        <img src="{{ asset('contents/admin/images/avatar.jpg') }}"
                             alt="Default"
                             class="profile-photo">

                    @endif


                    <div>

                        <div class="profile-name">
                            {{ $user->name_bn ?: $user->name }}
                        </div>

                        @if($user->name_bn && $user->name)
                            <div class="profile-name-en">
                                {{ $user->name }}
                            </div>
                        @endif

                        <div class="profile-badges">

                            <span class="user-type-badge {{ $userTypeClass }}">
                                <i class="fas fa-user-tag me-1"></i>
                                {{ $userTypeName }}
                            </span>

                        </div>

                    </div>

                </div>


                {{-- BASIC INFORMATION --}}
                <div class="info-section">

                    <div class="info-section-title">
                        <i class="fas fa-user me-2"></i>
                        সাধারণ তথ্য
                    </div>

                    <table class="info-table">

                        <tr>
                            <td>ইউজার আইডি</td>
                            <td>:</td>
                            <td>
                                <span class="id-badge">{{ $user->id }}</span>
                            </td>
                        </tr>

                        @if($user->institution_user_id)
                            <tr>
                                <td>প্রতিষ্ঠানের ইউজার আইডি</td>
                                <td>:</td>
                                <td>
                                    <span class="id-badge">
                                        {{ $user->institution_user_id }}
                                    </span>
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <td>নাম</td>
                            <td>:</td>
                            <td>
                                {{ $user->name_bn ?: $user->name ?: '—' }}

                                @if($user->name_bn && $user->name)
                                    <div style="font-size:.8rem;color:#9ca3af;">
                                        {{ $user->name }}
                                    </div>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td>User Type</td>
                            <td>:</td>
                            <td>
                                <span class="user-type-badge {{ $userTypeClass }}">
                                    {{ $userTypeName }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>মোবাইল নাম্বার</td>
                            <td>:</td>
                            <td>{{ $user->phone ?: '—' }}</td>
                        </tr>

                        @if($user->phone2)
                            <tr>
                                <td>বিকল্প মোবাইল</td>
                                <td>:</td>
                                <td>{{ $user->phone2 }}</td>
                            </tr>
                        @endif

                        @if($user->phone_owner)
                            <tr>
                                <td>ফোনের মালিক</td>
                                <td>:</td>
                                <td>{{ $user->phone_owner }}</td>
                            </tr>
                        @endif

                        <tr>
                            <td>ইমেইল</td>
                            <td>:</td>
                            <td>{{ $user->email ?: '—' }}</td>
                        </tr>

                        <tr>
                            <td>ইউজারনেম</td>
                            <td>:</td>
                            <td>{{ $user->username ?: '—' }}</td>
                        </tr>

                        <tr>
                            <td>লিঙ্গ</td>
                            <td>:</td>
                            <td>{{ $gender }}</td>
                        </tr>

                        @if($user->date_of_birth)
                            <tr>
                                <td>জন্ম তারিখ</td>
                                <td>:</td>
                                <td>{{ $formatDate($user->date_of_birth) }}</td>
                            </tr>
                        @endif

                        @if($user->age)
                            <tr>
                                <td>বয়স</td>
                                <td>:</td>
                                <td>{{ $user->age }}</td>
                            </tr>
                        @endif

                        @if($user->blood_group)
                            <tr>
                                <td>রক্তের গ্রুপ</td>
                                <td>:</td>
                                <td>{{ $user->blood_group }}</td>
                            </tr>
                        @endif

                        @if($user->religion)
                            <tr>
                                <td>ধর্ম</td>
                                <td>:</td>
                                <td>{{ $user->religion }}</td>
                            </tr>
                        @endif

                        @if($user->nid)
                            <tr>
                                <td>NID</td>
                                <td>:</td>
                                <td>{{ $user->nid }}</td>
                            </tr>
                        @endif

                        @if($user->birth_certificate)
                            <tr>
                                <td>জন্ম নিবন্ধন</td>
                                <td>:</td>
                                <td>{{ $user->birth_certificate }}</td>
                            </tr>
                        @endif

                    </table>

                </div>


                {{-- INSTITUTION INFORMATION --}}
                <div class="info-section">

                    <div class="info-section-title">
                        <i class="fas fa-school me-2"></i>
                        প্রতিষ্ঠান তথ্য
                    </div>

                    <table class="info-table">

                        <tr>
                            <td>প্রতিষ্ঠান</td>
                            <td>:</td>
                            <td>
                                @if($user->institution)
                                    {{ $user->institution->name_bn
                                        ?: $user->institution->name }}
                                @else
                                    <span class="empty-value">
                                        কোনো প্রতিষ্ঠান নেই
                                    </span>
                                @endif
                            </td>
                        </tr>

                    </table>

                </div>


                {{-- FAMILY INFORMATION --}}
                @if(
                    $user->father_name ||
                    $user->mother_name ||
                    $user->father_phone ||
                    $user->mother_phone ||
                    $user->guardian_name ||
                    $user->guardian_phone ||
                    $user->guardian_relation
                )

                    <div class="info-section">

                        <div class="info-section-title">
                            <i class="fas fa-users me-2"></i>
                            পারিবারিক তথ্য
                        </div>

                        <table class="info-table">

                            @if($user->father_name)
                                <tr>
                                    <td>পিতার নাম</td>
                                    <td>:</td>
                                    <td>{{ $user->father_name }}</td>
                                </tr>
                            @endif

                            @if($user->father_phone)
                                <tr>
                                    <td>পিতার ফোন</td>
                                    <td>:</td>
                                    <td>{{ $user->father_phone }}</td>
                                </tr>
                            @endif

                            @if($user->mother_name)
                                <tr>
                                    <td>মাতার নাম</td>
                                    <td>:</td>
                                    <td>{{ $user->mother_name }}</td>
                                </tr>
                            @endif

                            @if($user->mother_phone)
                                <tr>
                                    <td>মাতার ফোন</td>
                                    <td>:</td>
                                    <td>{{ $user->mother_phone }}</td>
                                </tr>
                            @endif

                            @if($user->guardian_name)
                                <tr>
                                    <td>অভিভাবকের নাম</td>
                                    <td>:</td>
                                    <td>{{ $user->guardian_name }}</td>
                                </tr>
                            @endif

                            @if($user->guardian_phone)
                                <tr>
                                    <td>অভিভাবকের ফোন</td>
                                    <td>:</td>
                                    <td>{{ $user->guardian_phone }}</td>
                                </tr>
                            @endif

                            @if($user->guardian_relation)
                                <tr>
                                    <td>অভিভাবকের সম্পর্ক</td>
                                    <td>:</td>
                                    <td>{{ $user->guardian_relation }}</td>
                                </tr>
                            @endif

                        </table>

                    </div>

                @endif


                {{-- STUDENT INFORMATION --}}
                @if($student)

                    <div class="info-section">

                        <div class="info-section-title">
                            <i class="fas fa-graduation-cap me-2"></i>
                            শিক্ষার্থী তথ্য
                        </div>

                        <table class="info-table">

                            <tr>
                                <td>Academic Year</td>
                                <td>:</td>
                                <td>
                                    {{ optional($student->academicYear)->name ?: '—' }}
                                </td>
                            </tr>

                            <tr>
                                <td>Class</td>
                                <td>:</td>
                                <td>
                                    {{ optional($student->class)->name_bn
                                        ?? optional($student->class)->name
                                        ?? '—' }}
                                </td>
                            </tr>

                            <tr>
                                <td>Section</td>
                                <td>:</td>
                                <td>
                                    {{ optional($student->section)->name_bn
                                        ?? optional($student->section)->name
                                        ?? '—' }}
                                </td>
                            </tr>

                            <tr>
                                <td>Guardian User ID</td>
                                <td>:</td>
                                <td>
                                    {{ $student->guardian_user_id ?: '—' }}
                                </td>
                            </tr>

                            <tr>
                                <td>Hostel</td>
                                <td>:</td>
                                <td>
                                    {{ $student->is_hostel ? 'হ্যাঁ' : 'না' }}
                                </td>
                            </tr>

                            <tr>
                                <td>Transport</td>
                                <td>:</td>
                                <td>
                                    {{ $student->is_transport ? 'হ্যাঁ' : 'না' }}
                                </td>
                            </tr>

                        </table>

                    </div>

                @endif


                {{-- ADDRESS INFORMATION --}}
                @if(
                    $user->full_present_address ||
                    $user->full_permanent_address ||
                    $user->present_village_road ||
                    $user->permanent_village_road
                )

                    <div class="info-section">

                        <div class="info-section-title">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            ঠিকানা
                        </div>

                        <table class="info-table">

                            @if($user->full_present_address || $user->present_village_road)
                                <tr>
                                    <td>বর্তমান ঠিকানা</td>
                                    <td>:</td>
                                    <td>
                                        {{ $user->full_present_address
                                            ?: $user->present_village_road }}
                                    </td>
                                </tr>
                            @endif

                            @if($user->full_permanent_address || $user->permanent_village_road)
                                <tr>
                                    <td>স্থায়ী ঠিকানা</td>
                                    <td>:</td>
                                    <td>
                                        {{ $user->full_permanent_address
                                            ?: $user->permanent_village_road }}
                                    </td>
                                </tr>
                            @endif

                        </table>

                    </div>

                @endif


                {{-- ACCOUNT INFORMATION --}}
                <div class="info-section">

                    <div class="info-section-title">
                        <i class="fas fa-history me-2"></i>
                        Account Information
                    </div>

                    <table class="info-table">

                        <tr>
                            <td>তৈরি করেছেন</td>
                            <td>:</td>
                            <td>
                                @if($user->creator)
                                    {{ $user->creator->name_bn
                                        ?: $user->creator->name }}
                                @else
                                    <span class="empty-value">
                                        নিজে নিবন্ধিত
                                    </span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td>তৈরি করার সময়</td>
                            <td>:</td>
                            <td>
                                {{ $formatDateTime($user->created_at) }}
                            </td>
                        </tr>

                        <tr>
                            <td>সর্বশেষ আপডেট করেছেন</td>
                            <td>:</td>
                            <td>
                                @if($user->updater)
                                    {{ $user->updater->name_bn
                                        ?: $user->updater->name }}
                                @else
                                    <span class="empty-value">—</span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td>সর্বশেষ আপডেট</td>
                            <td>:</td>
                            <td>
                                {{ $formatDateTime($user->updated_at) }}
                            </td>
                        </tr>

                        <tr>
                            <td>স্ট্যাটাস</td>
                            <td>:</td>
                            <td>
                                @if($user->status == 1)
                                    <span class="status-active">
                                        <i class="fas fa-circle" style="font-size:7px;"></i>
                                        সক্রিয়
                                    </span>
                                @else
                                    <span class="status-inactive">
                                        <i class="fas fa-circle" style="font-size:7px;"></i>
                                        নিষ্ক্রিয়
                                    </span>
                                @endif
                            </td>
                        </tr>

                    </table>

                </div>


                {{-- PROFILE PHOTO --}}
                <div class="info-section">

                    <div class="info-section-title">
                        <i class="fas fa-image me-2"></i>
                        Profile Photo
                    </div>

                    <div class="p-3">

                        @if($user->photo)
                            <img src="{{ asset('uploads/users/' . $user->photo) }}"
                                 alt="{{ $user->name }}"
                                 class="user-photo-large">
                        @else
                            <img src="{{ asset('contents/admin/images/avatar.jpg') }}"
                                 alt="Default"
                                 class="user-photo-large">
                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
