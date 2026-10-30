@extends('layouts.admin')

@section('title', 'প্রতিষ্ঠানের তথ্য')

@section('page')

@php
    /*
    |--------------------------------------------------------------------------
    | Database fields
    |--------------------------------------------------------------------------
    | এখানে শুধু madrasas টেবিলে বিদ্যমান column ব্যবহার করা হয়েছে।
    */

    $institutionName = $institution->name_bn
        ?: ($institution->name ?: 'প্রতিষ্ঠানের নাম দেওয়া নেই');

    $institutionCode = $institution->madrasa_code ?: null;

    $establishedYear = $institution->established_year ?? null;
    $registrationNo = $institution->registration_no ?? null;
    $postalCode = $institution->postal_code ?? null;
    $city = $institution->city ?? null;
    $state = $institution->state ?? null;
    $country = $institution->country ?? null;
    $website = $institution->website ?? null;
    $eiinNo = $institution->eiin_no ?? null;
    $email = $institution->email ?? null;
    $phone = $institution->phone ?? null;
    $address = $institution->address ?? null;
    $shortName = $institution->s_name ?? null;

    $logoPath = $institution->logo ?? null;
    $bannerPath = $institution->banner ?? null;

    $status = (int) ($institution->status ?? 0);

    $logoUrl = $logoPath
        ? asset('storage/' . ltrim($logoPath, '/'))
        : null;

    $bannerUrl = $bannerPath
        ? asset('storage/' . ltrim($bannerPath, '/'))
        : null;
@endphp


<div class="container-fluid">

    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h4 class="mb-1">
                <i class="fas fa-school text-primary me-2"></i>
                প্রতিষ্ঠানের তথ্য
            </h4>

            <p class="text-muted mb-0">
                প্রতিষ্ঠানের ডেটাবেজে সংরক্ষিত তথ্য দেখুন।
            </p>
        </div>

        @if(
            auth()->user()->is_super_admin
            && session('institution_mode') === true
            && session()->has('active_institution_id')
        )
            <form
                action="{{ route('dashboard.institutions.exit') }}"
                method="POST"
                onsubmit="return confirm('আপনি কি প্রতিষ্ঠান মোড থেকে বের হয়ে Super Admin System Mode-এ ফিরে যেতে চান?');"
            >
                @csrf

                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    প্রতিষ্ঠান থেকে বের হোন
                </button>
            </form>
        @endif

    </div>


    {{-- =========================================================
        FLASH MESSAGES
    ========================================================== --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>
    @endif


    {{-- =========================================================
        INSTITUTION SUMMARY
    ========================================================== --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex flex-wrap align-items-center gap-3">

                {{-- Institution Logo --}}
                <div
                    class="institution-logo-box rounded d-flex align-items-center justify-content-center"
                >
                    @if($logoUrl)
                        <img
                            src="{{ $logoUrl }}"
                            alt="Institution Logo"
                            class="institution-logo"
                        >
                    @else
                        <i class="fas fa-school fa-2x text-primary"></i>
                    @endif
                </div>

                {{-- Institution Details --}}
                <div class="flex-grow-1">

                    <h5 class="mb-1">
                        {{ $institutionName }}
                    </h5>

                    @if($institution->name_bn && $institution->name)
                        <div class="text-muted small">
                            {{ $institution->name }}
                        </div>
                    @endif

                    <div class="d-flex flex-wrap align-items-center gap-2 mt-2">

                        @if($institutionCode)
                            <span class="badge bg-light text-dark border">
                                <i class="fas fa-barcode me-1"></i>
                                {{ $institutionCode }}
                            </span>
                        @endif

                        @if($eiinNo)
                            <span class="badge bg-light text-dark border">
                                EIIN: {{ $eiinNo }}
                            </span>
                        @endif

                        @if($status === 1)
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i>
                                সক্রিয়
                            </span>
                        @else
                            <span class="badge bg-danger">
                                <i class="fas fa-times-circle me-1"></i>
                                নিষ্ক্রিয়
                            </span>
                        @endif

                    </div>

                </div>

                {{-- Institution Mode --}}
                <div class="ms-auto">

                    @if(
                        session('institution_mode') === true
                        && session()->has('active_institution_id')
                    )
                        <span class="badge bg-success p-2">
                            <i class="fas fa-building me-1"></i>
                            Institution Mode
                        </span>
                    @else
                        <span class="badge bg-secondary p-2">
                            System Mode
                        </span>
                    @endif

                </div>

            </div>

        </div>
    </div>


    {{-- =========================================================
        TAB NAVIGATION
    ========================================================== --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <div
                class="row g-2"
                role="tablist"
                aria-label="প্রতিষ্ঠানের তথ্যের বিভাগ"
            >

                <div class="col-xl-3 col-md-6">
                    <button
                        type="button"
                        class="btn btn-primary w-100 institution-tab active"
                        data-target="basic-info"
                        role="tab"
                        aria-selected="true"
                    >
                        <i class="fas fa-id-card me-2"></i>
                        ১. মৌলিক পরিচিতি
                    </button>
                </div>

                <div class="col-xl-3 col-md-6">
                    <button
                        type="button"
                        class="btn btn-outline-primary w-100 institution-tab"
                        data-target="address-info"
                        role="tab"
                        aria-selected="false"
                    >
                        <i class="fas fa-map-marker-alt me-2"></i>
                        ২. ঠিকানা
                    </button>
                </div>

                <div class="col-xl-3 col-md-6">
                    <button
                        type="button"
                        class="btn btn-outline-primary w-100 institution-tab"
                        data-target="contact-info"
                        role="tab"
                        aria-selected="false"
                    >
                        <i class="fas fa-address-book me-2"></i>
                        ৩. যোগাযোগ
                    </button>
                </div>

                <div class="col-xl-3 col-md-6">
                    <button
                        type="button"
                        class="btn btn-outline-primary w-100 institution-tab"
                        data-target="branding-info"
                        role="tab"
                        aria-selected="false"
                    >
                        <i class="fas fa-image me-2"></i>
                        ৪. ব্র্যান্ডিং
                    </button>
                </div>

            </div>

        </div>
    </div>


    {{-- =========================================================
        TAB 1: BASIC INFORMATION
    ========================================================== --}}
    <section
        id="basic-info"
        class="institution-section"
        role="tabpanel"
    >

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">
                <h5 class="mb-1">
                    <i class="fas fa-id-card text-primary me-2"></i>
                    মৌলিক পরিচিতি
                </h5>

                <small class="text-muted">
                    madrasas টেবিলে সংরক্ষিত পরিচিতির তথ্য
                </small>
            </div>

            <div class="card-body">

                <div class="row g-4">

                    {{-- Bengali Name --}}
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                প্রতিষ্ঠানের নাম (বাংলা)
                            </div>

                            <div class="info-value">
                                {{ $institution->name_bn ?: 'তথ্য দেওয়া নেই' }}
                            </div>
                        </div>
                    </div>

                    {{-- English Name --}}
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                Institution Name (English)
                            </div>

                            <div class="info-value">
                                {{ $institution->name ?: 'তথ্য দেওয়া নেই' }}
                            </div>
                        </div>
                    </div>

                    {{-- Short Name --}}
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                প্রতিষ্ঠানের সংক্ষিপ্ত নাম
                            </div>

                            <div class="info-value">
                                {{ $shortName ?: 'তথ্য দেওয়া নেই' }}
                            </div>
                        </div>
                    </div>

                    {{-- Institution Code --}}
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                প্রাতিষ্ঠানিক কোড
                            </div>

                            <div class="info-value">
                                {{ $institutionCode ?: 'তথ্য দেওয়া নেই' }}
                            </div>
                        </div>
                    </div>

                    {{-- EIIN --}}
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                EIIN নম্বর
                            </div>

                            <div class="info-value">
                                {{ $eiinNo ?: 'তথ্য দেওয়া নেই' }}
                            </div>
                        </div>
                    </div>

                    {{-- Registration --}}
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                রেজিস্ট্রেশন নম্বর
                            </div>

                            <div class="info-value">
                                {{ $registrationNo ?: 'তথ্য দেওয়া নেই' }}
                            </div>
                        </div>
                    </div>

                    {{-- Established Year --}}
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                প্রতিষ্ঠার বছর
                            </div>

                            <div class="info-value">
                                {{ $establishedYear ?: 'তথ্য দেওয়া নেই' }}
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                প্রতিষ্ঠানের অবস্থা
                            </div>

                            <div class="info-value">
                                @if($status === 1)
                                    <span class="badge bg-success">সক্রিয়</span>
                                @else
                                    <span class="badge bg-danger">নিষ্ক্রিয়</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Created Date --}}
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                রেকর্ড তৈরির তারিখ
                            </div>

                            <div class="info-value">
                                {{ $institution->created_at
                                    ? $institution->created_at->format('d-m-Y h:i A')
                                    : 'তথ্য দেওয়া নেই' }}
                            </div>
                        </div>
                    </div>

                    {{-- Updated Date --}}
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                সর্বশেষ আপডেট
                            </div>

                            <div class="info-value">
                                {{ $institution->updated_at
                                    ? $institution->updated_at->format('d-m-Y h:i A')
                                    : 'তথ্য দেওয়া নেই' }}
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </section>


    {{-- =========================================================
        TAB 2: ADDRESS
    ========================================================== --}}
    <section
        id="address-info"
        class="institution-section d-none"
        role="tabpanel"
        hidden
    >

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">
                <h5 class="mb-1">
                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                    ঠিকানা বিবরণী
                </h5>

                <small class="text-muted">
                    প্রতিষ্ঠানের ডেটাবেজে সংরক্ষিত ঠিকানা
                </small>
            </div>

            <div class="card-body">

                <div class="row g-4">

                    {{-- Address --}}
                    <div class="col-md-12">
                        <div class="info-item">
                            <div class="info-label">
                                সম্পূর্ণ ঠিকানা
                            </div>

                            <div class="info-value">
                                {{ $address ?: 'তথ্য দেওয়া নেই' }}
                            </div>
                        </div>
                    </div>

                    {{-- City --}}
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                শহর
                            </div>

                            <div class="info-value">
                                {{ $city ?: 'তথ্য দেওয়া নেই' }}
                            </div>
                        </div>
                    </div>

                    {{-- State --}}
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                বিভাগ / রাজ্য
                            </div>

                            <div class="info-value">
                                {{ $state ?: 'তথ্য দেওয়া নেই' }}
                            </div>
                        </div>
                    </div>

                    {{-- Postal Code --}}
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                পোস্ট কোড
                            </div>

                            <div class="info-value">
                                {{ $postalCode ?: 'তথ্য দেওয়া নেই' }}
                            </div>
                        </div>
                    </div>

                    {{-- Country --}}
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                দেশ
                            </div>

                            <div class="info-value">
                                {{ $country ?: 'তথ্য দেওয়া নেই' }}
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </section>


    {{-- =========================================================
        TAB 3: CONTACT
    ========================================================== --}}
    <section
        id="contact-info"
        class="institution-section d-none"
        role="tabpanel"
        hidden
    >

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">
                <h5 class="mb-1">
                    <i class="fas fa-address-book text-primary me-2"></i>
                    যোগাযোগের তথ্য
                </h5>

                <small class="text-muted">
                    প্রতিষ্ঠানের সংরক্ষিত যোগাযোগ ও ওয়েবসাইট
                </small>
            </div>

            <div class="card-body">

                <div class="row g-4">

                    {{-- Phone --}}
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                ফোন নম্বর
                            </div>

                            <div class="info-value">
                                @if($phone)
                                    <a href="tel:{{ $phone }}">
                                        <i class="fas fa-phone me-2"></i>
                                        {{ $phone }}
                                    </a>
                                @else
                                    তথ্য দেওয়া নেই
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                ই-মেইল
                            </div>

                            <div class="info-value">
                                @if($email)
                                    <a href="mailto:{{ $email }}">
                                        <i class="fas fa-envelope me-2"></i>
                                        {{ $email }}
                                    </a>
                                @else
                                    তথ্য দেওয়া নেই
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Website --}}
                    <div class="col-md-12">
                        <div class="info-item">
                            <div class="info-label">
                                ওয়েবসাইট
                            </div>

                            <div class="info-value">
                                @if($website)
                                    <a
                                        href="{{ \Illuminate\Support\Str::startsWith($website, ['http://', 'https://']) ? $website : 'https://' . $website }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <i class="fas fa-globe me-2"></i>
                                        {{ $website }}
                                        <i class="fas fa-external-link-alt ms-1 small"></i>
                                    </a>
                                @else
                                    তথ্য দেওয়া নেই
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </section>


    {{-- =========================================================
        TAB 4: BRANDING
    ========================================================== --}}
    <section
        id="branding-info"
        class="institution-section d-none"
        role="tabpanel"
        hidden
    >

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">
                <h5 class="mb-1">
                    <i class="fas fa-image text-primary me-2"></i>
                    লোগো ও ব্র্যান্ডিং
                </h5>

                <small class="text-muted">
                    ডেটাবেজে সংরক্ষিত লোগো ও ব্যানার
                </small>
            </div>

            <div class="card-body">

                <div class="row g-4">

                    {{-- Logo --}}
                    <div class="col-md-6">

                        <div class="card h-100 border">

                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-image me-2"></i>
                                    প্রতিষ্ঠানের লোগো
                                </h6>
                            </div>

                            <div class="card-body d-flex align-items-center justify-content-center text-center">

                                @if($logoUrl)
                                    <div>
                                        <img
                                            src="{{ $logoUrl }}"
                                            alt="Institution Logo"
                                            class="img-fluid rounded"
                                            style="max-height:220px;max-width:100%;"
                                        >

                                        <div class="small text-muted mt-3">
                                            {{ $logoPath }}
                                        </div>
                                    </div>
                                @else
                                    <div class="text-muted py-4">
                                        <i class="fas fa-image fa-3x mb-3"></i>

                                        <div>
                                            লোগো দেওয়া নেই
                                        </div>
                                    </div>
                                @endif

                            </div>

                        </div>

                    </div>


                    {{-- Banner --}}
                    <div class="col-md-6">

                        <div class="card h-100 border">

                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-images me-2"></i>
                                    প্রতিষ্ঠানের ব্যানার
                                </h6>
                            </div>

                            <div class="card-body d-flex align-items-center justify-content-center text-center">

                                @if($bannerUrl)
                                    <div>
                                        <img
                                            src="{{ $bannerUrl }}"
                                            alt="Institution Banner"
                                            class="img-fluid rounded"
                                            style="max-height:220px;max-width:100%;"
                                        >

                                        <div class="small text-muted mt-3">
                                            {{ $bannerPath }}
                                        </div>
                                    </div>
                                @else
                                    <div class="text-muted py-4">
                                        <i class="fas fa-images fa-3x mb-3"></i>

                                        <div>
                                            ব্যানার দেওয়া নেই
                                        </div>
                                    </div>
                                @endif

                            </div>

                        </div>

                    </div>


                    {{-- Official Pad Preview --}}
                    <div class="col-12">

                        <div class="card border">

                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-file-alt me-2"></i>
                                    প্রতিষ্ঠানের পরিচিতি প্রিভিউ
                                </h6>
                            </div>

                            <div class="card-body">

                                <div class="official-preview p-4">

                                    <div class="text-center">

                                        @if($logoUrl)
                                            <img
                                                src="{{ $logoUrl }}"
                                                alt="Institution Logo"
                                                style="max-height:80px;max-width:180px;"
                                                class="mb-3"
                                            >
                                        @endif

                                        <h4 class="fw-bold mb-1">
                                            {{ $institutionName }}
                                        </h4>

                                        @if($institution->name && $institution->name_bn)
                                            <div class="text-muted">
                                                {{ $institution->name }}
                                            </div>
                                        @endif

                                        @if($address)
                                            <div class="mt-2">
                                                {{ $address }}
                                            </div>
                                        @endif

                                        @if($phone)
                                            <div class="mt-1">
                                                <i class="fas fa-phone me-1"></i>
                                                {{ $phone }}
                                            </div>
                                        @endif

                                        @if($email)
                                            <div class="mt-1">
                                                <i class="fas fa-envelope me-1"></i>
                                                {{ $email }}
                                            </div>
                                        @endif

                                        @if($website)
                                            <div class="mt-1">
                                                {{ $website }}
                                            </div>
                                        @endif

                                    </div>

                                    <hr>

                                    <div class="text-muted small text-center">
                                        এটি সংরক্ষিত তথ্যের একটি প্রিভিউ।
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

</div>


{{-- =========================================================
    STYLES
========================================================== --}}
<style>
    .institution-logo-box {
        width: 76px;
        height: 76px;
        flex: 0 0 76px;
        overflow: hidden;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
    }

    .institution-logo {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 5px;
    }

    .institution-tab {
        min-height: 44px;
        white-space: normal;
        transition: all .2s ease;
    }

    .institution-section {
        animation: institutionFadeIn .2s ease;
    }

    .institution-section.d-none {
        display: none !important;
    }

    .info-item {
        height: 100%;
        padding: 15px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
        overflow-wrap: anywhere;
    }

    .info-label {
        margin-bottom: 8px;
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
    }

    .info-value {
        color: #0f172a;
        font-size: 15px;
        font-weight: 500;
        white-space: pre-line;
    }

    .info-value a {
        text-decoration: none;
        overflow-wrap: anywhere;
    }

    .info-value a:hover {
        text-decoration: underline;
    }

    .official-preview {
        min-height: 220px;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        background: #fff;
    }

    @keyframes institutionFadeIn {
        from {
            opacity: 0;
            transform: translateY(4px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 576px) {
        .institution-logo-box {
            width: 60px;
            height: 60px;
            flex-basis: 60px;
        }

        .info-item {
            padding: 12px;
        }
    }
</style>


{{-- =========================================================
    TAB SCRIPT
========================================================== --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.institution-tab');
    const sections = document.querySelectorAll('.institution-section');

    function activateTab(selectedTab) {
        const targetId = selectedTab.dataset.target;
        const targetSection = document.getElementById(targetId);

        if (!targetSection) {
            console.error('Institution section not found:', targetId);
            return;
        }

        tabs.forEach(function (tab) {
            tab.classList.remove('active', 'btn-primary');
            tab.classList.add('btn-outline-primary');
            tab.setAttribute('aria-selected', 'false');
        });

        selectedTab.classList.remove('btn-outline-primary');
        selectedTab.classList.add('btn-primary', 'active');
        selectedTab.setAttribute('aria-selected', 'true');

        sections.forEach(function (section) {
            section.classList.add('d-none');
            section.setAttribute('hidden', 'hidden');
        });

        targetSection.classList.remove('d-none');
        targetSection.removeAttribute('hidden');
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            activateTab(this);
        });
    });

    const initialTab = document.querySelector(
        '.institution-tab[data-target="basic-info"]'
    );

    if (initialTab) {
        activateTab(initialTab);
    }
});
</script>
@endpush

@endsection
