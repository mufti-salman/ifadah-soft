@extends('layouts.admin')

@section('title', 'প্রতিষ্ঠান ব্যবস্থাপনা')

@section('page')

<style>
    .inst-page {
        padding: 32px 28px;
        max-width: 1500px;
        margin: 0 auto;
    }

    .inst-topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
    }

    .inst-topbar__title {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .inst-topbar__icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 22px;
        background: linear-gradient(135deg, #0369a1, #0ea5e9);
    }

    .inst-topbar__text h1 {
        font-size: 22px;
        font-weight: 700;
        margin: 0;
    }

    .inst-topbar__text p {
        margin: 4px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .btn-primary-sky {
        border: none;
        color: white;
        padding: 11px 20px;
        border-radius: 10px;
        background: linear-gradient(135deg, #0369a1, #0ea5e9);
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
    }

    .btn-primary-sky:hover {
        opacity: .92;
    }

    .inst-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px;
        display: flex;
        gap: 14px;
        align-items: center;
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .si-blue {
        background: #e0f2fe;
        color: #0369a1;
    }

    .si-green {
        background: #ecfdf5;
        color: #059669;
    }

    .si-amber {
        background: #fffbeb;
        color: #d97706;
    }

    .si-rose {
        background: #fff1f2;
        color: #e11d48;
    }

    .stat-label {
        font-size: 12px;
        color: #64748b;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
    }

    .inst-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
    }

    .inst-toolbar {
        padding: 16px 20px;
        display: flex;
        gap: 12px;
        border-bottom: 1px solid #e2e8f0;
    }

    .search-wrap {
        flex: 1;
        position: relative;
    }

    .search-wrap i {
        position: absolute;
        left: 12px;
        top: 12px;
        color: #64748b;
    }

    .search-input,
    .filter-select {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 12px;
        outline: none;
    }

    .search-input:focus,
    .filter-select:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 2px rgba(2, 132, 199, .08);
    }

    .search-input {
        padding-left: 36px;
    }

    .filter-select {
        width: auto;
        min-width: 150px;
    }

    .inst-table-wrap {
        overflow-x: auto;
    }

    .inst-table {
        width: 100%;
        border-collapse: collapse;
    }

    .inst-table th {
        background: #f8fafc;
        padding: 13px 16px;
        text-align: left;
        font-size: 12px;
        color: #64748b;
        white-space: nowrap;
    }

    .inst-table td {
        padding: 14px 16px;
        border-top: 1px solid #e2e8f0;
        white-space: nowrap;
        vertical-align: middle;
    }

    .inst-name-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .inst-logo,
    .inst-logo-placeholder {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        flex-shrink: 0;
    }

    .inst-logo {
        object-fit: cover;
    }

    .inst-logo-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        background: #0284c7;
        font-weight: bold;
    }

    .inst-name {
        font-weight: 600;
    }

    .inst-name-bn {
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
    }

    .code-pill {
        background: #f1f5f9;
        padding: 5px 9px;
        border-radius: 6px;
        font-family: monospace;
    }

    .badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-active {
        background: #ecfdf5;
        color: #059669;
    }

    .badge-inactive {
        background: #f1f5f9;
        color: #64748b;
    }

    .online {
        color: #059669;
        font-weight: 600;
        font-size: 12px;
    }

    .offline {
        color: #94a3b8;
        font-weight: 600;
        font-size: 12px;
    }

    /* ================= ACTION BUTTONS ================= */

    .action-buttons {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-icon {
        width: 34px;
        height: 34px;
        border: 1px solid #e2e8f0;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: .2s;
    }

    .btn-icon:hover {
        background: #f8fafc;
    }

    .btn-icon.edit {
        color: #2563eb;
    }

    .btn-icon.del {
        color: #dc2626;
    }

    .btn-icon.switch {
        color: #0284c7;
        border-color: #bae6fd;
        background: #f0f9ff;
    }

    .btn-icon.switch:hover {
        background: #e0f2fe;
        border-color: #7dd3fc;
    }

    .btn-icon.current {
        color: #059669;
        border-color: #a7f3d0;
        background: #ecfdf5;
        cursor: default;
    }

    /* ================= CURRENT INSTITUTION BADGE ================= */

    .current-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 9px;
        border-radius: 20px;
        background: #ecfdf5;
        color: #059669;
        font-size: 11px;
        font-weight: 600;
        margin-left: 6px;
    }

    /* ================= MODAL ================= */

    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .55);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-overlay.open {
        display: flex;
    }

    .modal-box {
        width: 100%;
        max-width: 650px;
        background: white;
        border-radius: 16px;
        overflow: hidden;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-head {
        padding: 18px 24px;
        background: linear-gradient(135deg, #0369a1, #0ea5e9);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-head h5 {
        margin: 0;
        font-size: 17px;
        font-weight: 600;
    }

    .modal-close {
        border: none;
        background: transparent;
        color: white;
        font-size: 24px;
        cursor: pointer;
        line-height: 1;
    }

    .modal-body {
        padding: 24px;
    }

    .modal-foot {
        padding: 16px 24px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        display: block;
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
    }

    .form-input,
    .form-textarea,
    .form-select {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px;
        outline: none;
    }

    .form-input:focus,
    .form-textarea:focus,
    .form-select:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 2px rgba(2, 132, 199, .08);
    }

    .form-textarea {
        min-height: 80px;
        resize: vertical;
    }

    .btn-ghost {
        padding: 10px 20px;
        border: 1px solid #cbd5e1;
        background: white;
        border-radius: 8px;
        cursor: pointer;
    }

    .btn-ghost:hover {
        background: #f8fafc;
    }

    .btn-save-sky,
    .btn-delete-confirm {
        padding: 10px 22px;
        border: none;
        border-radius: 8px;
        color: white;
        cursor: pointer;
        font-weight: 600;
    }

    .btn-save-sky {
        background: #0284c7;
    }

    .btn-save-sky:hover {
        background: #0369a1;
    }

    .btn-delete-confirm {
        background: #dc2626;
    }

    .btn-delete-confirm:hover {
        background: #b91c1c;
    }

    .delete-modal-content {
        text-align: center;
        padding: 35px 25px;
    }

    .delete-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 15px;
        border-radius: 50%;
        background: #fef2f2;
        color: #dc2626;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 25px;
    }

    /* ================= SWITCH MODAL ================= */

    .switch-modal-content {
        text-align: center;
        padding: 35px 25px;
    }

    .switch-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 15px;
        border-radius: 50%;
        background: #e0f2fe;
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
    }

    .switch-institution-name {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin-top: 10px;
    }

    .switch-warning {
        margin-top: 10px;
        font-size: 13px;
        color: #64748b;
    }

    .btn-switch-confirm {
        padding: 10px 22px;
        border: none;
        border-radius: 8px;
        color: white;
        background: #0284c7;
        cursor: pointer;
        font-weight: 600;
    }

    .btn-switch-confirm:hover {
        background: #0369a1;
    }

    @media(max-width: 768px) {

        .inst-page {
            padding: 20px 15px;
        }

        .inst-topbar {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .btn-primary-sky {
            width: 100%;
        }

        .inst-toolbar {
            flex-wrap: wrap;
        }

        .search-wrap {
            width: 100%;
            flex-basis: 100%;
        }

        .filter-select {
            width: 100%;
        }

        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>


<div class="inst-page">

    {{-- =========================================================
        TOP BAR
    ========================================================== --}}

<div class="inst-topbar">

    <div class="inst-topbar__title">

        <div class="inst-topbar__icon">
            <i class="fas fa-university"></i>
        </div>

        <div class="inst-topbar__text">

        @if(session()->has('active_institution_id'))

            <h1>
                {{ session('institution_name') }}
            </h1>

            <p>
                আপনি বর্তমানে এই প্রতিষ্ঠানের প্রশাসনিক কনটেক্সটে আছেন
            </p>

            @elseif(auth()->user()->is_madrasa_admin)

                <h1>
                    মাদ্রাসার তথ্য
                </h1>

                <p>
                    আপনার প্রতিষ্ঠানের তথ্য পরিচালনা করুন
                </p>

            @else

                <h1>
                    প্রতিষ্ঠান ব্যবস্থাপনা
                </h1>

                <p>
                    সকল নিবন্ধিত প্রতিষ্ঠানের তথ্য পরিচালনা করুন
                </p>

            @endif

        </div>

    </div>


    <div style="display:flex; gap:10px; align-items:center;">

        {{-- =====================================================
            EXIT INSTITUTION
        ====================================================== --}}

        @if(
            session()->has('active_institution_id') &&
            (auth()->user()->is_super_admin || auth()->user()->is_soft_admin)
        )

            <form
                action="{{ route('dashboard.institutions.exit') }}"
                method="POST"
                style="margin:0;"
            >

                @csrf

                <button
                    type="submit"
                    class="btn-exit-institution"
                    title="প্রতিষ্ঠান থেকে বের হয়ে System Admin এ ফিরে যান"
                >

                    <i class="fas fa-sign-out-alt"></i>

                    প্রতিষ্ঠান থেকে বের হন

                </button>

            </form>

        @endif


        {{-- =====================================================
            ADD INSTITUTION
        ====================================================== --}}

@if(
    !session()->has('active_institution_id') &&
    (auth()->user()->is_super_admin || auth()->user()->is_soft_admin)
)

            <button
                type="button"
                class="btn-primary-sky"
                onclick="openCreateModal()"
            >

                <i class="fas fa-plus"></i>

                নতুন প্রতিষ্ঠান যোগ করুন

            </button>

        @endif

    </div>

</div>


    {{-- =========================================================
        STATISTICS
    ========================================================== --}}

    <div class="inst-stats">

        {{-- Total --}}

        <div class="stat-card">

            <div class="stat-icon si-blue">
                <i class="fas fa-university"></i>
            </div>

            <div>

                <div class="stat-label">
                    মোট প্রতিষ্ঠান
                </div>

                <div class="stat-value">
                    {{ $institutions->count() }}
                </div>

            </div>

        </div>


        {{-- Active --}}

        <div class="stat-card">

            <div class="stat-icon si-green">
                <i class="fas fa-check-circle"></i>
            </div>

            <div>

                <div class="stat-label">
                    সক্রিয়
                </div>

                <div class="stat-value">
                    {{ $institutions->where('status', 1)->count() }}
                </div>

            </div>

        </div>


        {{-- Online --}}

        <div class="stat-card">

            <div class="stat-icon si-amber">
                <i class="fas fa-wifi"></i>
            </div>

            <div>

                <div class="stat-label">
                    অনলাইন
                </div>

                <div class="stat-value">

                    {{ $institutions->filter(function($institution) {

                        return $institution->users->contains(function($user) {

                            return $user->last_login_at &&
                                $user->last_login_at->greaterThan(
                                    now()->subMinutes(5)
                                );

                        });

                    })->count() }}

                </div>

            </div>

        </div>


        {{-- Inactive --}}

        <div class="stat-card">

            <div class="stat-icon si-rose">
                <i class="fas fa-ban"></i>
            </div>

            <div>

                <div class="stat-label">
                    নিষ্ক্রিয়
                </div>

                <div class="stat-value">
                    {{ $institutions->where('status', 0)->count() }}
                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        TABLE
    ========================================================== --}}

    <div class="inst-card">

        {{-- Toolbar --}}

        <div class="inst-toolbar">

            <div class="search-wrap">

                <i class="fas fa-search"></i>

                <input
                    type="text"
                    class="search-input"
                    id="instSearch"
                    placeholder="নাম, কোড, ইমেইল দিয়ে খুঁজুন..."
                >

            </div>


            <select
                class="filter-select"
                id="statusFilter"
            >

                <option value="">
                    সকল অবস্থা
                </option>

                <option value="1">
                    সক্রিয়
                </option>

                <option value="0">
                    নিষ্ক্রিয়
                </option>

            </select>

        </div>


        {{-- =====================================================
            TABLE
        ====================================================== --}}

        <div class="inst-table-wrap">

            <table
                class="inst-table"
                id="instTable"
            >

                <thead>

                    <tr>

                        <th>#</th>

                        <th>প্রতিষ্ঠান</th>

                        <th>কোড</th>

                        <th>অবস্থা</th>

                        <th>কার্যক্রম</th>

                        <th>সর্বশেষ লগইন</th>

                        <th>তৈরি হয়েছে</th>

                        @if(auth()->user()->is_super_admin || auth()->user()->is_soft_admin)

                            <th>অ্যাকশন</th>

                        @endif

                    </tr>

                </thead>


                <tbody>

                    @forelse($institutions as $institution)

                        @php

                            $lastLoginUser = $institution->users
                                ->sortByDesc('last_login_at')
                                ->first();

                            $lastLogin = optional($lastLoginUser)
                                ->last_login_at;

                            $isOnline = $institution->users
                                ->contains(function($user) {

                                    return $user->last_login_at &&
                                        $user->last_login_at
                                            ->greaterThan(
                                                now()->subMinutes(5)
                                            );

                                });

                            /*
                            |--------------------------------------------------------------------------
                            | Current Selected Institution
                            |--------------------------------------------------------------------------
                            */

                    $currentInstitutionId =
                        session('active_institution_id');

                            $isCurrentInstitution =
                                $currentInstitutionId &&
                                (int) $currentInstitutionId ===
                                (int) $institution->id;

                        @endphp


                        <tr
                            data-name="{{ strtolower(
                                $institution->name . ' ' .
                                $institution->name_bn . ' ' .
                                $institution->madrasa_code . ' ' .
                                $institution->email
                            ) }}"
                            data-status="{{ $institution->status }}"
                        >

                            {{-- Serial --}}

                            <td>
                                {{ $loop->iteration }}
                            </td>


                            {{-- Institution --}}

                            <td>

                                <div class="inst-name-cell">

                                    @if($institution->logo)

                                        <img
                                            src="{{ asset('storage/' . $institution->logo) }}"
                                            class="inst-logo"
                                            alt="{{ $institution->name }}"
                                        >

                                    @else

                                        <div class="inst-logo-placeholder">

                                            {{ strtoupper(
                                                substr($institution->name, 0, 1)
                                            ) }}

                                        </div>

                                    @endif


                                    <div>

                                        <div class="inst-name">

                                            {{ $institution->name }}

                                            {{-- Current Badge --}}

                                            @if($isCurrentInstitution)

                                                <span class="current-badge">

                                                    <i class="fas fa-check-circle"></i>

                                                    বর্তমানে নির্বাচিত

                                                </span>

                                            @endif

                                        </div>


                                        @if($institution->name_bn)

                                            <div class="inst-name-bn">
                                                {{ $institution->name_bn }}
                                            </div>

                                        @endif

                                    </div>

                                </div>

                            </td>


                            {{-- Code --}}

                            <td>

                                <span class="code-pill">

                                    {{ $institution->madrasa_code ?? '—' }}

                                </span>

                            </td>


                            {{-- Status --}}

                            <td>

                                @if($institution->status == 1)

                                    <span class="badge badge-active">
                                        সক্রিয়
                                    </span>

                                @else

                                    <span class="badge badge-inactive">
                                        নিষ্ক্রিয়
                                    </span>

                                @endif

                            </td>


                            {{-- Activity --}}

                            <td>

                                @if($isOnline)

                                    <span class="online">

                                        <i class="fas fa-circle"></i>

                                        অনলাইন

                                    </span>

                                @else

                                    <span class="offline">

                                        <i class="fas fa-circle"></i>

                                        অফলাইন

                                    </span>

                                @endif

                            </td>


                            {{-- Last Login --}}

                            <td>

                                @if($lastLogin)

                                    <small>

                                        {{ $lastLogin->format('d M Y') }}

                                        <br>

                                        <span class="text-muted">

                                            {{ $lastLogin->format('h:i A') }}

                                        </span>

                                    </small>

                                @else

                                    <span class="text-muted">
                                        কখনো লগইন করেনি
                                    </span>

                                @endif

                            </td>


                            {{-- Created --}}

                            <td>

                                <small>

                                    {{ $institution->created_at->format('d M Y') }}

                                    <br>

                                    <span class="text-muted">

                                        {{ $institution->created_at->format('h:i A') }}

                                    </span>

                                </small>

                            </td>


                            {{-- =================================================
                                ACTIONS
                            ================================================== --}}

                            @if(auth()->user()->is_super_admin || auth()->user()->is_soft_admin)

                                <td>

                                    <div class="action-buttons">

                                        {{-- =====================================
                                            SWITCH INSTITUTION
                                        ====================================== --}}

                                        @if($isCurrentInstitution)

                                            <button
                                                type="button"
                                                class="btn-icon current"
                                                title="বর্তমানে নির্বাচিত প্রতিষ্ঠান"
                                                disabled
                                            >

                                                <i class="fas fa-check"></i>

                                            </button>

                                        @else

                                            <button
                                                type="button"
                                                class="btn-icon switch switchBtn"
                                                data-id="{{ $institution->id }}"
                                                data-name="{{ $institution->name }}"
                                                title="প্রতিষ্ঠান পরিবর্তন করুন"
                                            >

                                                <i class="fas fa-exchange-alt"></i>

                                            </button>

                                        @endif


                                        {{-- =====================================
                                            EDIT
                                        ====================================== --}}

                                        <button
                                            type="button"
                                            class="btn-icon edit editBtn"
                                            data-id="{{ $institution->id }}"
                                            title="সম্পাদনা"
                                        >

                                            <i class="fas fa-pen"></i>

                                        </button>


                                        {{-- =====================================
                                            DELETE
                                        ====================================== --}}

                                        <button
                                            type="button"
                                            class="btn-icon del deleteBtn"
                                            data-id="{{ $institution->id }}"
                                            data-name="{{ $institution->name }}"
                                            title="ডিলিট"
                                        >

                                            <i class="fas fa-trash-alt"></i>

                                        </button>

                                    </div>

                                </td>

                            @endif

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="{{ auth()->user()->is_super_admin || auth()->user()->is_soft_admin ? 8 : 7 }}"
                                class="text-center py-5 text-muted"
                            >

                                <i class="fas fa-university fa-2x mb-3"></i>

                                <br>

                                কোনো প্রতিষ্ঠান পাওয়া যায়নি।

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- =============================================================
    CREATE MODAL
============================================================= --}}

<div
    id="createModal"
    class="modal-overlay"
>

    <div class="modal-box">

        <form
            action="{{ route('dashboard.institutions.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf

            <div class="modal-head">

                <h5>
                    ➕ নতুন প্রতিষ্ঠান যোগ করুন
                </h5>

                <button
                    type="button"
                    class="modal-close"
                    onclick="closeCreateModal()"
                >
                    &times;
                </button>

            </div>


            <div class="modal-body">

                <div class="form-row">

                    <div class="form-group">

                        <label class="form-label">
                            প্রতিষ্ঠানের নাম *
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-input"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label class="form-label">
                            প্রতিষ্ঠানের বাংলা নাম
                        </label>

                        <input
                            type="text"
                            name="name_bn"
                            class="form-input"
                        >

                    </div>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label class="form-label">
                            মাদ্রাসা কোড
                        </label>

                        <input
                            type="text"
                            name="madrasa_code"
                            class="form-input"
                        >

                    </div>


                    <div class="form-group">

                        <label class="form-label">
                            EIIN নম্বর
                        </label>

                        <input
                            type="text"
                            name="eiin_no"
                            class="form-input"
                        >

                    </div>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label class="form-label">
                            ইমেইল
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-input"
                        >

                    </div>


                    <div class="form-group">

                        <label class="form-label">
                            ফোন নম্বর
                        </label>

                        <input
                            type="text"
                            name="phone"
                            class="form-input"
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label class="form-label">
                        ঠিকানা
                    </label>

                    <textarea
                        name="address"
                        class="form-textarea"
                    ></textarea>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label class="form-label">
                            অবস্থা
                        </label>

                        <select
                            name="status"
                            class="form-select"
                        >

                            <option value="1">
                                সক্রিয়
                            </option>

                            <option value="0">
                                নিষ্ক্রিয়
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label class="form-label">
                            প্রতিষ্ঠানের লোগো
                        </label>

                        <input
                            type="file"
                            name="logo"
                            class="form-input"
                            accept="image/*"
                        >

                    </div>

                </div>

            </div>


            <div class="modal-foot">

                <button
                    type="button"
                    class="btn-ghost"
                    onclick="closeCreateModal()"
                >
                    বাতিল
                </button>


                <button
                    type="submit"
                    class="btn-save-sky"
                >

                    <i class="fas fa-save"></i>

                    সংরক্ষণ করুন

                </button>

            </div>

        </form>

    </div>

</div>


{{-- =============================================================
    EDIT MODAL
============================================================= --}}

<div
    id="editModal"
    class="modal-overlay"
>

    <div class="modal-box">

        <form
            id="editInstitutionForm"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf

            @method('PUT')


            <div class="modal-head">

                <h5>
                    ✏️ প্রতিষ্ঠান সম্পাদনা
                </h5>

                <button
                    type="button"
                    class="modal-close"
                    onclick="closeEditModal()"
                >
                    &times;
                </button>

            </div>


            <div class="modal-body">

                <div class="form-row">

                    <div class="form-group">

                        <label class="form-label">
                            প্রতিষ্ঠানের নাম *
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="edit_name"
                            class="form-input"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label class="form-label">
                            প্রতিষ্ঠানের বাংলা নাম
                        </label>

                        <input
                            type="text"
                            name="name_bn"
                            id="edit_name_bn"
                            class="form-input"
                        >

                    </div>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label class="form-label">
                            মাদ্রাসা কোড
                        </label>

                        <input
                            type="text"
                            name="madrasa_code"
                            id="edit_madrasa_code"
                            class="form-input"
                        >

                    </div>


                    <div class="form-group">

                        <label class="form-label">
                            EIIN নম্বর
                        </label>

                        <input
                            type="text"
                            name="eiin_no"
                            id="edit_eiin_no"
                            class="form-input"
                        >

                    </div>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label class="form-label">
                            ইমেইল
                        </label>

                        <input
                            type="email"
                            name="email"
                            id="edit_email"
                            class="form-input"
                        >

                    </div>


                    <div class="form-group">

                        <label class="form-label">
                            ফোন নম্বর
                        </label>

                        <input
                            type="text"
                            name="phone"
                            id="edit_phone"
                            class="form-input"
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label class="form-label">
                        ঠিকানা
                    </label>

                    <textarea
                        name="address"
                        id="edit_address"
                        class="form-textarea"
                    ></textarea>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label class="form-label">
                            অবস্থা
                        </label>

                        <select
                            name="status"
                            id="edit_status"
                            class="form-select"
                        >

                            <option value="1">
                                সক্রিয়
                            </option>

                            <option value="0">
                                নিষ্ক্রিয়
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label class="form-label">
                            লোগো পরিবর্তন করুন
                        </label>

                        <input
                            type="file"
                            name="logo"
                            class="form-input"
                            accept="image/*"
                        >

                    </div>

                </div>

            </div>


            <div class="modal-foot">

                <button
                    type="button"
                    class="btn-ghost"
                    onclick="closeEditModal()"
                >
                    বাতিল
                </button>


                <button
                    type="submit"
                    class="btn-save-sky"
                >

                    <i class="fas fa-save"></i>

                    তথ্য আপডেট করুন

                </button>

            </div>

        </form>

    </div>

</div>


{{-- =============================================================
    DELETE MODAL
============================================================= --}}

<div
    id="deleteModal"
    class="modal-overlay"
>

    <div
        class="modal-box"
        style="max-width:450px"
    >

        <div class="delete-modal-content">

            <div class="delete-icon">

                <i class="fas fa-trash-alt"></i>

            </div>


            <h4>
                প্রতিষ্ঠানটি ডিলিট করবেন?
            </h4>


            <p class="text-muted mt-2">

                <strong id="deleteInstitutionName"></strong>

                <br>

                এই প্রতিষ্ঠানটি ডিলিট করলে পুনরায় ফিরিয়ে আনা যাবে না।

            </p>

        </div>


        <div class="modal-foot">

            <button
                type="button"
                class="btn-ghost"
                onclick="closeDeleteModal()"
            >
                বাতিল
            </button>


            <form
                id="deleteInstitutionForm"
                method="POST"
            >

                @csrf

                @method('DELETE')


                <button
                    type="submit"
                    class="btn-delete-confirm"
                >

                    <i class="fas fa-trash-alt"></i>

                    হ্যাঁ, ডিলিট করুন

                </button>

            </form>

        </div>

    </div>

</div>


{{-- =============================================================
    SWITCH INSTITUTION MODAL
============================================================= --}}

<div
    id="switchModal"
    class="modal-overlay"
>

    <div
        class="modal-box"
        style="max-width:450px"
    >

        <div class="switch-modal-content">

            <div class="switch-icon">

                <i class="fas fa-exchange-alt"></i>

            </div>


            <h4>
                প্রতিষ্ঠান পরিবর্তন করুন?
            </h4>


            <div
                class="switch-institution-name"
                id="switchInstitutionName"
            ></div>


            <p class="switch-warning">

                এই প্রতিষ্ঠানটি নির্বাচন করলে আপনার বর্তমান
                ড্যাশবোর্ড এই প্রতিষ্ঠানের তথ্য অনুযায়ী দেখাবে।

            </p>

        </div>


        <div class="modal-foot">

            <button
                type="button"
                class="btn-ghost"
                onclick="closeSwitchModal()"
            >
                বাতিল
            </button>


            <form
                id="switchInstitutionForm"
                method="POST"
                action="{{ route('dashboard.institutions.switch') }}"
            >

                @csrf

                <input
                    type="hidden"
                    name="institution_id"
                    id="switchInstitutionId"
                >


                <button
                    type="submit"
                    class="btn-switch-confirm"
                >

                    <i class="fas fa-exchange-alt"></i>

                    হ্যাঁ, পরিবর্তন করুন

                </button>

            </form>

        </div>

    </div>

</div>

<script>

    /* =========================================================
       CREATE MODAL
    ========================================================== */

    function openCreateModal() {

        document
            .getElementById('createModal')
            .classList
            .add('open');

    }


    function closeCreateModal() {

        document
            .getElementById('createModal')
            .classList
            .remove('open');

    }


    /* =========================================================
       EDIT MODAL
    ========================================================== */

    function openEditModal() {

        document
            .getElementById('editModal')
            .classList
            .add('open');

    }


    function closeEditModal() {

        document
            .getElementById('editModal')
            .classList
            .remove('open');

    }


    /* =========================================================
       DELETE MODAL
    ========================================================== */

    function openDeleteModal() {

        document
            .getElementById('deleteModal')
            .classList
            .add('open');

    }


    function closeDeleteModal() {

        document
            .getElementById('deleteModal')
            .classList
            .remove('open');

    }


    /* =========================================================
       SWITCH MODAL
    ========================================================== */

    function openSwitchModal() {

        document
            .getElementById('switchModal')
            .classList
            .add('open');

    }


    function closeSwitchModal() {

        document
            .getElementById('switchModal')
            .classList
            .remove('open');

    }


    /* =========================================================
       SWITCH INSTITUTION
    ========================================================== */

    document
        .querySelectorAll('.switchBtn')
        .forEach(button => {

            button.addEventListener('click', function() {

                const id =
                    this.dataset.id;

                const name =
                    this.dataset.name;


                document
                    .getElementById('switchInstitutionId')
                    .value = id;


                document
                    .getElementById('switchInstitutionName')
                    .textContent = name;


                openSwitchModal();

            });

        });


    /* =========================================================
       EDIT INSTITUTION
    ========================================================== */

    document
        .querySelectorAll('.editBtn')
        .forEach(button => {

            button.addEventListener('click', async function() {

                const id = this.dataset.id;


                try {

                    const response = await fetch(
                        `/dashboard/institutions/${id}/edit`,
                        {
                            method: 'GET',

                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }
                    );


                    if (!response.ok) {

                        throw new Error(
                            'Failed to load institution data'
                        );

                    }


                    const result =
                        await response.json();

                    const data =
                        result.data;


                    document
                        .getElementById('edit_name')
                        .value =
                        data.name ?? '';


                    document
                        .getElementById('edit_name_bn')
                        .value =
                        data.name_bn ?? '';


                    document
                        .getElementById('edit_madrasa_code')
                        .value =
                        data.madrasa_code ?? '';


                    document
                        .getElementById('edit_eiin_no')
                        .value =
                        data.eiin_no ?? '';


                    document
                        .getElementById('edit_email')
                        .value =
                        data.email ?? '';


                    document
                        .getElementById('edit_phone')
                        .value =
                        data.phone ?? '';


                    document
                        .getElementById('edit_address')
                        .value =
                        data.address ?? '';


                    document
                        .getElementById('edit_status')
                        .value =
                        data.status ?? 1;


                    document
                        .getElementById('editInstitutionForm')
                        .action =
                        `/dashboard/institutions/${id}`;


                    openEditModal();

                }

                catch (error) {

                    console.error(error);

                    alert(
                        'প্রতিষ্ঠানের তথ্য লোড করা যায়নি। আবার চেষ্টা করুন।'
                    );

                }

            });

        });


    /* =========================================================
       DELETE INSTITUTION
    ========================================================== */

    document
        .querySelectorAll('.deleteBtn')
        .forEach(button => {

            button.addEventListener('click', function() {

                const id =
                    this.dataset.id;

                const name =
                    this.dataset.name;


                document
                    .getElementById('deleteInstitutionName')
                    .textContent =
                    name;


                document
                    .getElementById('deleteInstitutionForm')
                    .action =
                    `/dashboard/institutions/${id}`;


                openDeleteModal();

            });

        });


    /* =========================================================
       SEARCH + FILTER
    ========================================================== */

    function filterTable() {

        const search =
            document
                .getElementById('instSearch')
                .value
                .toLowerCase()
                .trim();


        const status =
            document
                .getElementById('statusFilter')
                .value;


        document
            .querySelectorAll(
                '#instTable tbody tr[data-name]'
            )
            .forEach(row => {

                const rowName =
                    row.dataset.name || '';


                const rowStatus =
                    row.dataset.status || '';


                const matchSearch =
                    !search ||
                    rowName.includes(search);


                const matchStatus =
                    !status ||
                    rowStatus === status;


                row.style.display =
                    matchSearch && matchStatus
                        ? ''
                        : 'none';

            });

    }


    document
        .getElementById('instSearch')
        .addEventListener(
            'input',
            filterTable
        );


    document
        .getElementById('statusFilter')
        .addEventListener(
            'change',
            filterTable
        );


    /* =========================================================
       CLOSE MODALS WHEN CLICKING OUTSIDE
    ========================================================== */

    [
        'createModal',
        'editModal',
        'deleteModal',
        'switchModal'
    ].forEach(id => {

        const modal =
            document.getElementById(id);


        if (!modal) {
            return;
        }


        modal.addEventListener(
            'click',
            function(e) {

                if (e.target === this) {

                    this.classList.remove('open');

                }

            }
        );

    });


    /* =========================================================
       ESC KEY
    ========================================================== */

    document.addEventListener(
        'keydown',
        function(e) {

            if (e.key !== 'Escape') {
                return;
            }


            closeCreateModal();

            closeEditModal();

            closeDeleteModal();

            closeSwitchModal();

        }
    );

</script>

@endsection