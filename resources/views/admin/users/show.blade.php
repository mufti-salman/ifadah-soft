@extends('layouts.admin')

@section('title', 'ইউজার তথ্য')

@section('page')

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

    /* Header */
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

    /* Body */
    .user-view-body {
        padding: 28px 22px;
    }

    .user-profile-wrapper {
        max-width: 850px;
        margin: 0 auto;
    }

    /* Profile top */
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

    .profile-role {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
        background: #eef2fb;
        color: #3b5bdb;
    }

    /* Information table */
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

    /* ID */
    .id-badge {
        display: inline-block;
        background: #eef2fb;
        color: #3b5bdb;
        font-weight: 700;
        padding: 4px 11px;
        border-radius: 7px;
        font-size: 0.83rem;
    }

    /* Role badges */
    .role-badge {
        display: inline-block;
        padding: 5px 13px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .role-super-admin {
        background: #fee2e2;
        color: #b91c1c;
    }

    .role-support-admin {
        background: #fef3c7;
        color: #92400e;
    }

    .role-user-admin {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .role-user {
        background: #d1fae5;
        color: #065f46;
    }

    .role-student {
        background: #ede9fe;
        color: #6d28d9;
    }

    .role-default {
        background: #e5e7eb;
        color: #374151;
    }

    /* Status */
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

    /* Photo */
    .user-photo-large {
        width: 130px;
        height: 130px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #eef2fb;
        box-shadow: 0 3px 12px rgba(30, 58, 138, 0.08);
    }

    /* Empty value */
    .empty-value {
        color: #b0b7c5;
        font-weight: 400;
    }

    /* Responsive */
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

    {{-- ==========================================================
         HEADER
    =========================================================== --}}
    <div class="user-view-header">

        <div class="user-view-header-inner">

            <div class="user-view-title">
                <i class="fab fa-gg-circle"></i>

                <h5>
                    ইউজার তথ্য দেখুন
                </h5>
            </div>

            <a
                href="{{ route('dashboard.user.index') }}"
                class="btn-all-users"
            >
                <i class="fas fa-th"></i>
                সকল ইউজার
            </a>

        </div>

    </div>


    {{-- ==========================================================
         BODY
    =========================================================== --}}
    <div class="user-view-body">

        <div class="user-profile-wrapper">


            {{-- ==================================================
                 PROFILE TOP
            =================================================== --}}
            <div class="profile-top">

                @if($user->photo)

                    <img
                        src="{{ asset('uploads/users/' . $user->photo) }}"
                        alt="{{ $user->name }}"
                        class="profile-photo"
                    >

                @else

                    <img
                        src="{{ asset('contents/admin/images/avatar.jpg') }}"
                        alt="Default"
                        class="profile-photo"
                    >

                @endif


                <div>

                    <div class="profile-name">
                        {{ $user->name_bn ?: $user->name }}
                    </div>

                    @if($user->name_bn)

                        <div class="profile-name-en">
                            {{ $user->name }}
                        </div>

                    @endif


                    @php

                        $roleSlug = $user->role->slug ?? '';

                        $roleClass = match($roleSlug) {

                            'super_admin'
                                => 'role-super-admin',

                            'support_admin'
                                => 'role-support-admin',

                            'user_admin'
                                => 'role-user-admin',

                            'user'
                                => 'role-user',

                            'student'
                                => 'role-student',

                            default
                                => 'role-default',
                        };

                    @endphp


                    <span class="role-badge {{ $roleClass }}">
                        {{ $user->role->role_name ?? 'N/A' }}
                    </span>

                </div>

            </div>


            {{-- ==================================================
                 USER INFORMATION
            =================================================== --}}
            <table class="info-table">

                {{-- User ID --}}
                <tr>

                    <td>
                        ইউজার আইডি
                    </td>

                    <td>:</td>

                    <td>
                        <span class="id-badge">
                            {{ $user->id }}
                        </span>
                    </td>

                </tr>


                {{-- Institution User ID --}}
                @if($user->institution_user_id)

                <tr>

                    <td>
                        প্রতিষ্ঠানের ইউজার আইডি
                    </td>

                    <td>:</td>

                    <td>
                        <span class="id-badge">
                            {{ $user->institution_user_id }}
                        </span>
                    </td>

                </tr>

                @endif


                {{-- Name --}}
                <tr>

                    <td>
                        নাম
                    </td>

                    <td>:</td>

                    <td>

                        {{ $user->name_bn ?: $user->name }}

                        @if($user->name_bn)

                            <div style="font-size: 0.8rem; color:#9ca3af;">
                                {{ $user->name }}
                            </div>

                        @endif

                    </td>

                </tr>


                {{-- Phone --}}
                <tr>

                    <td>
                        মোবাইল নাম্বার
                    </td>

                    <td>:</td>

                    <td>
                        {{ $user->phone ?: '—' }}
                    </td>

                </tr>


                {{-- Phone 2 --}}
                @if($user->phone2)

                <tr>

                    <td>
                        বিকল্প মোবাইল
                    </td>

                    <td>:</td>

                    <td>
                        {{ $user->phone2 }}
                    </td>

                </tr>

                @endif


                {{-- Email --}}
                <tr>

                    <td>
                        ইমেইল
                    </td>

                    <td>:</td>

                    <td>
                        {{ $user->email ?: '—' }}
                    </td>

                </tr>


                {{-- Username --}}
                <tr>

                    <td>
                        ইউজারনেম
                    </td>

                    <td>:</td>

                    <td>
                        {{ $user->username ?: '—' }}
                    </td>

                </tr>


                {{-- Role --}}
                <tr>

                    <td>
                        ইউজার ধরণ
                    </td>

                    <td>:</td>

                    <td>

                        <span class="role-badge {{ $roleClass }}">
                            {{ $user->role->role_name ?? 'N/A' }}
                        </span>

                    </td>

                </tr>


                {{-- Institution --}}
                <tr>

                    <td>
                        প্রতিষ্ঠান
                    </td>

                    <td>:</td>

                    <td>

                        @if($user->institution)

                            {{ $user->institution->name_bn ?: $user->institution->name }}

                        @else

                            <span class="empty-value">
                                কোনো প্রতিষ্ঠান নেই
                            </span>

                        @endif

                    </td>

                </tr>


                {{-- Father Name --}}
                @if($user->father_name)

                <tr>

                    <td>
                        পিতার নাম
                    </td>

                    <td>:</td>

                    <td>
                        {{ $user->father_name }}
                    </td>

                </tr>

                @endif


                {{-- Mother Name --}}
                @if($user->mother_name)

                <tr>

                    <td>
                        মাতার নাম
                    </td>

                    <td>:</td>

                    <td>
                        {{ $user->mother_name }}
                    </td>

                </tr>

                @endif


                {{-- Guardian --}}
                @if($user->guardian_name)

                <tr>

                    <td>
                        অভিভাবকের নাম
                    </td>

                    <td>:</td>

                    <td>
                        {{ $user->guardian_name }}

                        @if($user->guardian_relation)

                            <div style="font-size:0.8rem;color:#9ca3af;">
                                সম্পর্ক:
                                {{ $user->guardian_relation }}
                            </div>

                        @endif

                    </td>

                </tr>

                @endif


                {{-- Gender --}}
                @if($user->gender)

                <tr>

                    <td>
                        লিঙ্গ
                    </td>

                    <td>:</td>

                    <td>
                        {{ $user->gender }}
                    </td>

                </tr>

                @endif


                {{-- Date of Birth --}}
                @if($user->date_of_birth)

                <tr>

                    <td>
                        জন্ম তারিখ
                    </td>

                    <td>:</td>

                    <td>
                        {{ $user->date_of_birth->format('d M Y') }}
                    </td>

                </tr>

                @endif


                {{-- NID --}}
                @if($user->nid)

                <tr>

                    <td>
                        NID
                    </td>

                    <td>:</td>

                    <td>
                        {{ $user->nid }}
                    </td>

                </tr>

                @endif


                {{-- Birth Certificate --}}
                @if($user->birth_certificate)

                <tr>

                    <td>
                        জন্ম নিবন্ধন
                    </td>

                    <td>:</td>

                    <td>
                        {{ $user->birth_certificate }}
                    </td>

                </tr>

                @endif


                {{-- Present Address --}}
                @if($user->full_present_address)

                <tr>

                    <td>
                        বর্তমান ঠিকানা
                    </td>

                    <td>:</td>

                    <td>
                        {{ $user->full_present_address }}
                    </td>

                </tr>

                @endif


                {{-- Permanent Address --}}
                @if($user->full_permanent_address)

                <tr>

                    <td>
                        স্থায়ী ঠিকানা
                    </td>

                    <td>:</td>

                    <td>
                        {{ $user->full_permanent_address }}
                    </td>

                </tr>

                @endif


                {{-- Creator --}}
                <tr>

                    <td>
                        তৈরি করেছেন
                    </td>

                    <td>:</td>

                    <td>

                        @if($user->creator)

                            {{ $user->creator->name_bn ?: $user->creator->name }}

                        @else

                            <span class="empty-value">
                                নিজে নিবন্ধিত
                            </span>

                        @endif

                    </td>

                </tr>


                {{-- Created Time --}}
                <tr>

                    <td>
                        তৈরি করার সময়
                    </td>

                    <td>:</td>

                    <td>

                        @if($user->created_at)

                            {{ $user->created_at->format('d M Y, h:i A') }}

                        @else

                            —

                        @endif

                    </td>

                </tr>


                {{-- Updater --}}
                <tr>

                    <td>
                        সর্বশেষ আপডেট করেছেন
                    </td>

                    <td>:</td>

                    <td>

                        @if($user->updater)

                            {{ $user->updater->name_bn ?: $user->updater->name }}

                        @else

                            <span class="empty-value">
                                —
                            </span>

                        @endif

                    </td>

                </tr>


                {{-- Updated Time --}}
                <tr>

                    <td>
                        সর্বশেষ আপডেট
                    </td>

                    <td>:</td>

                    <td>

                        @if($user->updated_at)

                            {{ $user->updated_at->format('d M Y, h:i A') }}

                        @else

                            —

                        @endif

                    </td>

                </tr>


                {{-- Status --}}
                <tr>

                    <td>
                        স্ট্যাটাস
                    </td>

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


                {{-- Photo --}}
                <tr>

                    <td>
                        ছবি
                    </td>

                    <td>:</td>

                    <td>

                        @if($user->photo)

                            <img
                                src="{{ asset('uploads/users/' . $user->photo) }}"
                                alt="{{ $user->name }}"
                                class="user-photo-large"
                            >

                        @else

                            <img
                                src="{{ asset('contents/admin/images/avatar.jpg') }}"
                                alt="Default"
                                class="user-photo-large"
                            >

                        @endif

                    </td>

                </tr>

            </table>

        </div>

    </div>

</div>
</div>

@endsection
