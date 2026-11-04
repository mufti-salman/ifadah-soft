@extends('layouts.admin')

@section('title', 'প্রতিষ্ঠানের তথ্য')

@section('page')

@php
    $institutionName = $institution->name_bn
        ?: ($institution->name ?: 'প্রতিষ্ঠানের নাম');

    $institutionCode = $institution->madrasa_code;
    $logoPath = $institution->logo;
    $bannerPath = $institution->banner;

    $logoUrl = $logoPath
        ? asset('storage/' . ltrim($logoPath, '/'))
        : null;

    $bannerUrl = $bannerPath
        ? asset('storage/' . ltrim($bannerPath, '/'))
        : null;

    $status = (int) $institution->status;

    $fields = [
        'name_bn',
        'name',
        's_name',
        'madrasa_code',
        'eiin_no',
        'registration_no',
        'established_year',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
        'email',
        'website',
    ];
@endphp

<div class="container-fluid">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h4 class="mb-1">
                <i class="fas fa-school text-primary me-2"></i>
                প্রতিষ্ঠানের তথ্য
            </h4>

            <p class="text-muted mb-0">
                প্রতিষ্ঠানের তথ্য সরাসরি পরিবর্তন করুন। পরিবর্তন স্বয়ংক্রিয়ভাবে সংরক্ষিত হবে।
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
                onsubmit="return confirm('আপনি কি প্রতিষ্ঠান মোড থেকে বের হতে চান?');"
            >
                @csrf

                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    প্রতিষ্ঠান থেকে বের হোন
                </button>
            </form>
        @endif

    </div>


    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
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
            ></button>
        </div>
    @endif


    {{-- GLOBAL AUTO SAVE STATUS --}}
    <div class="institution-save-notice mb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-cloud"></i>

            <span>
                পরিবর্তনগুলো স্বয়ংক্রিয়ভাবে সংরক্ষিত হবে।
            </span>
        </div>

        <span id="globalSaveStatus" class="text-muted">
            <i class="fas fa-check-circle me-1"></i>
            সর্বশেষ তথ্য লোড হয়েছে
        </span>
    </div>


    {{-- INSTITUTION SUMMARY --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex flex-wrap align-items-center gap-3">

                <div class="institution-logo-box rounded">
                    @if($logoUrl)
                        <img
                            src="{{ $logoUrl }}"
                            alt="Institution Logo"
                            id="institutionHeaderLogo"
                            class="institution-logo"
                        >
                    @else
                        <i class="fas fa-school fa-2x text-primary"></i>
                    @endif
                </div>

                <div class="flex-grow-1">

                    <h5 class="mb-1" id="institutionHeaderName">
                        {{ $institutionName }}
                    </h5>

                    <div class="d-flex flex-wrap align-items-center gap-2 mt-2">

                        @if($institutionCode)
                            <span class="badge bg-light text-dark border">
                                <i class="fas fa-barcode me-1"></i>
                                {{ $institutionCode }}
                            </span>
                        @endif

                        @if($institution->eiin_no)
                            <span class="badge bg-light text-dark border">
                                EIIN: {{ $institution->eiin_no }}
                            </span>
                        @endif

                        @if($status === 1)
                            <span class="badge bg-success">
                                সক্রিয়
                            </span>
                        @else
                            <span class="badge bg-danger">
                                নিষ্ক্রিয়
                            </span>
                        @endif

                    </div>
                </div>

                <div class="ms-auto">
                    @if(session('institution_mode') === true)
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


    {{-- TABS --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <div class="row g-2">

                <div class="col-xl-3 col-md-6">
                    <button
                        type="button"
                        class="btn btn-primary w-100 institution-tab active"
                        data-target="basic-info"
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
                    >
                        <i class="fas fa-image me-2"></i>
                        ৪. ব্র্যান্ডিং
                    </button>
                </div>

            </div>
        </div>
    </div>


    {{-- =====================================================
        TAB 1: BASIC INFORMATION
    ====================================================== --}}
    <section
        id="basic-info"
        class="institution-section"
    >

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">
                <h5 class="mb-1">
                    <i class="fas fa-id-card text-primary me-2"></i>
                    মৌলিক পরিচিতি
                </h5>

                <small class="text-muted">
                    প্রতিষ্ঠানের নাম, কোড ও নিবন্ধন সম্পর্কিত তথ্য
                </small>
            </div>

            <div class="card-body">

                <div class="row g-4">

                    {{-- Bengali Name --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            প্রতিষ্ঠানের নাম (বাংলা)
                        </label>

                        <input
                            type="text"
                            class="form-control institution-autosave"
                            data-field="name_bn"
                            value="{{ $institution->name_bn }}"
                            placeholder="প্রতিষ্ঠানের বাংলা নাম"
                        >

                        <small class="autosave-status"></small>
                    </div>


                    {{-- English Name --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Institution Name (English)
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            class="form-control institution-autosave"
                            data-field="name"
                            value="{{ $institution->name }}"
                            placeholder="Institution Name"
                            required
                        >

                        <small class="autosave-status"></small>
                    </div>


                    {{-- Short Name --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            প্রতিষ্ঠানের সংক্ষিপ্ত নাম
                        </label>

                        <input
                            type="text"
                            class="form-control institution-autosave"
                            data-field="s_name"
                            value="{{ $institution->s_name }}"
                            placeholder="সংক্ষিপ্ত নাম"
                        >

                        <small class="autosave-status"></small>
                    </div>


                    {{-- Institution Code --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            প্রাতিষ্ঠানিক কোড
                        </label>

                        <input
                            type="text"
                            class="form-control institution-autosave"
                            data-field="madrasa_code"
                            value="{{ $institution->madrasa_code }}"
                            placeholder="প্রাতিষ্ঠানিক কোড"
                        >

                        <small class="autosave-status"></small>
                    </div>


                    {{-- EIIN --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            EIIN নম্বর
                        </label>

                        <input
                            type="text"
                            class="form-control institution-autosave"
                            data-field="eiin_no"
                            value="{{ $institution->eiin_no }}"
                            placeholder="EIIN নম্বর"
                        >

                        <small class="autosave-status"></small>
                    </div>


                    {{-- Registration --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            রেজিস্ট্রেশন নম্বর
                        </label>

                        <input
                            type="text"
                            class="form-control institution-autosave"
                            data-field="registration_no"
                            value="{{ $institution->registration_no }}"
                            placeholder="রেজিস্ট্রেশন নম্বর"
                        >

                        <small class="autosave-status"></small>
                    </div>


                    {{-- Established Year --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            প্রতিষ্ঠার বছর
                        </label>

                        <input
                            type="number"
                            class="form-control institution-autosave"
                            data-field="established_year"
                            value="{{ $institution->established_year }}"
                            placeholder="যেমন: ২০০৫"
                        >

                        <small class="autosave-status"></small>
                    </div>


                    {{-- Created At --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            রেকর্ড তৈরির তারিখ
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $institution->created_at?->format('d-m-Y h:i A') ?? 'তথ্য নেই' }}"
                            readonly
                        >
                    </div>


                    {{-- Updated At --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            সর্বশেষ আপডেট
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $institution->updated_at?->format('d-m-Y h:i A') ?? 'তথ্য নেই' }}"
                            readonly
                            id="institutionUpdatedAt"
                        >
                    </div>

                </div>
            </div>
        </div>

    </section>


    {{-- =====================================================
        TAB 2: ADDRESS
    ====================================================== --}}
    <section
        id="address-info"
        class="institution-section d-none"
        hidden
    >

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">
                <h5 class="mb-1">
                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                    ঠিকানা বিবরণী
                </h5>

                <small class="text-muted">
                    প্রতিষ্ঠানের ঠিকানা পরিবর্তন করুন
                </small>
            </div>

            <div class="card-body">

                <div class="row g-4">

                    {{-- Address --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            সম্পূর্ণ ঠিকানা
                        </label>

                        <textarea
                            class="form-control institution-autosave"
                            data-field="address"
                            rows="3"
                            placeholder="প্রতিষ্ঠানের সম্পূর্ণ ঠিকানা"
                        >{{ $institution->address }}</textarea>

                        <small class="autosave-status"></small>
                    </div>


                    {{-- City --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            শহর
                        </label>

                        <input
                            type="text"
                            class="form-control institution-autosave"
                            data-field="city"
                            value="{{ $institution->city }}"
                            placeholder="শহর"
                        >

                        <small class="autosave-status"></small>
                    </div>


                    {{-- State --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            বিভাগ / জেলা
                        </label>

                        <input
                            type="text"
                            class="form-control institution-autosave"
                            data-field="state"
                            value="{{ $institution->state }}"
                            placeholder="বিভাগ / জেলা"
                        >

                        <small class="autosave-status"></small>
                    </div>


                    {{-- Postal Code --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            পোস্ট কোড
                        </label>

                        <input
                            type="text"
                            class="form-control institution-autosave"
                            data-field="postal_code"
                            value="{{ $institution->postal_code }}"
                            placeholder="পোস্ট কোড"
                        >

                        <small class="autosave-status"></small>
                    </div>


                    {{-- Country --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            দেশ
                        </label>

                        <input
                            type="text"
                            class="form-control institution-autosave"
                            data-field="country"
                            value="{{ $institution->country }}"
                            placeholder="দেশের নাম"
                        >

                        <small class="autosave-status"></small>
                    </div>

                </div>
            </div>
        </div>

    </section>


    {{-- =====================================================
        TAB 3: CONTACT
    ====================================================== --}}
    <section
        id="contact-info"
        class="institution-section d-none"
        hidden
    >

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">
                <h5 class="mb-1">
                    <i class="fas fa-address-book text-primary me-2"></i>
                    যোগাযোগের তথ্য
                </h5>

                <small class="text-muted">
                    ফোন, ই-মেইল ও ওয়েবসাইট পরিবর্তন করুন
                </small>
            </div>

            <div class="card-body">

                <div class="row g-4">

                    {{-- Phone --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            ফোন নম্বর
                        </label>

                        <input
                            type="text"
                            class="form-control institution-autosave"
                            data-field="phone"
                            value="{{ $institution->phone }}"
                            placeholder="ফোন নম্বর"
                        >

                        <small class="autosave-status"></small>
                    </div>


                    {{-- Email --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            ই-মেইল
                        </label>

                        <input
                            type="email"
                            class="form-control institution-autosave"
                            data-field="email"
                            value="{{ $institution->email }}"
                            placeholder="example@email.com"
                        >

                        <small class="autosave-status"></small>
                    </div>


                    {{-- Website --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            ওয়েবসাইট
                        </label>

                        <input
                            type="text"
                            class="form-control institution-autosave"
                            data-field="website"
                            value="{{ $institution->website }}"
                            placeholder="https://example.com"
                        >

                        <small class="autosave-status"></small>
                    </div>

                </div>
            </div>
        </div>

    </section>


{{-- =====================================================
    TAB 4: BRANDING
====================================================== --}}
<section
    id="branding-info"
    class="institution-section d-none"
    hidden
>

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <h5 class="mb-1">
                <i class="fas fa-image text-primary me-2"></i>
                লোগো ও ব্র্যান্ডিং
            </h5>

            <small class="text-muted">
                প্রতিষ্ঠানের লোগো ও ব্যানার আপলোড করুন।
                ছবি নির্বাচন করলেই স্বয়ংক্রিয়ভাবে সংরক্ষিত হবে।
            </small>

        </div>

        <div class="card-body">

            <div class="row g-4">

                {{-- LOGO --}}
                <div class="col-md-6">

                    <div class="card h-100 border">

                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-image me-2"></i>
                                প্রতিষ্ঠানের লোগো
                            </h6>
                        </div>

                        <div class="card-body text-center">

                            <div class="branding-image-preview mb-3">

                                <img
                                    src="{{ $logoUrl ?? '' }}"
                                    id="institutionLogoPreview"
                                    class="{{ $logoUrl ? '' : 'd-none' }}"
                                    alt="Institution Logo"
                                >

                                <div
                                    id="institutionLogoPlaceholder"
                                    class="{{ $logoUrl ? 'd-none' : '' }}"
                                >
                                    <i class="fas fa-image fa-3x mb-2"></i>
                                    <div>লোগো দেওয়া নেই</div>
                                </div>

                            </div>

                            <input
                                type="file"
                                id="institutionLogoInput"
                                class="d-none"
                                accept="image/jpeg,image/png,image/webp"
                            >

                            <button
                                type="button"
                                class="btn btn-outline-primary"
                                onclick="document.getElementById('institutionLogoInput').click()"
                            >
                                <i class="fas fa-upload me-2"></i>
                                লোগো আপলোড করুন
                            </button>

                            <div
                                id="institutionLogoStatus"
                                class="image-upload-status mt-3"
                            ></div>

                            <small class="text-muted d-block mt-2">
                                JPG, PNG, WEBP | সর্বোচ্চ 2 MB
                            </small>

                        </div>

                    </div>

                </div>


                {{-- BANNER --}}
                <div class="col-md-6">

                    <div class="card h-100 border">

                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-images me-2"></i>
                                প্রতিষ্ঠানের ব্যানার
                            </h6>
                        </div>

                        <div class="card-body text-center">

                            <div class="branding-image-preview banner-preview mb-3">

                                <img
                                    src="{{ $bannerUrl ?? '' }}"
                                    id="institutionBannerPreview"
                                    class="{{ $bannerUrl ? '' : 'd-none' }}"
                                    alt="Institution Banner"
                                >

                                <div
                                    id="institutionBannerPlaceholder"
                                    class="{{ $bannerUrl ? 'd-none' : '' }}"
                                >
                                    <i class="fas fa-images fa-3x mb-2"></i>
                                    <div>ব্যানার দেওয়া নেই</div>
                                </div>

                            </div>

                            <input
                                type="file"
                                id="institutionBannerInput"
                                class="d-none"
                                accept="image/jpeg,image/png,image/webp"
                            >

                            <button
                                type="button"
                                class="btn btn-outline-primary"
                                onclick="document.getElementById('institutionBannerInput').click()"
                            >
                                <i class="fas fa-upload me-2"></i>
                                ব্যানার আপলোড করুন
                            </button>

                            <div
                                id="institutionBannerStatus"
                                class="image-upload-status mt-3"
                            ></div>

                            <small class="text-muted d-block mt-2">
                                JPG, PNG, WEBP | সর্বোচ্চ 5 MB
                            </small>

                        </div>

                    </div>

                </div>


                {{-- OFFICIAL PREVIEW --}}
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

                                    <img
                                        src="{{ $logoUrl ?? '' }}"
                                        id="officialPreviewLogo"
                                        alt="Institution Logo"
                                        class="{{ $logoUrl ? '' : 'd-none' }} mb-3"
                                        style="max-height:80px;max-width:180px;"
                                    >

                                    <h4
                                        class="fw-bold mb-1"
                                        id="previewInstitutionName"
                                    >
                                        {{ $institutionName }}
                                    </h4>

                                    <div
                                        class="text-muted"
                                        id="previewInstitutionEnglishName"
                                    >
                                        {{ $institution->name && $institution->name_bn ? $institution->name : '' }}
                                    </div>

                                    <div
                                        class="mt-2"
                                        id="previewInstitutionAddress"
                                    >
                                        {{ $institution->address }}
                                    </div>

                                    <div
                                        class="mt-1"
                                        id="previewInstitutionPhone"
                                    >
                                        @if($institution->phone)
                                            <i class="fas fa-phone me-1"></i>
                                            {{ $institution->phone }}
                                        @endif
                                    </div>

                                    <div
                                        class="mt-1"
                                        id="previewInstitutionEmail"
                                    >
                                        @if($institution->email)
                                            <i class="fas fa-envelope me-1"></i>
                                            {{ $institution->email }}
                                        @endif
                                    </div>

                                    <div
                                        class="mt-1"
                                        id="previewInstitutionWebsite"
                                    >
                                        {{ $institution->website }}
                                    </div>

                                </div>

                                <hr>

                                <div class="text-muted small text-center">
                                    এটি প্রতিষ্ঠানের সংরক্ষিত তথ্যের প্রিভিউ।
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


{{-- =====================================================
    STYLES
====================================================== --}}
<style>
    .institution-logo-box {
        width: 76px;
        height: 76px;
        flex: 0 0 76px;
        overflow: hidden;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .institution-logo {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 5px;
    }

    .institution-save-notice {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        color: #475569;
        font-size: 13px;
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

    .institution-autosave {
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .institution-autosave:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .12);
    }

    .institution-autosave.is-invalid {
        border-color: #dc3545;
    }

    .autosave-status {
        display: block;
        min-height: 18px;
        margin-top: 5px;
        font-size: 12px;
    }

    .official-preview {
        min-height: 220px;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        background: #fff;
    }
/* =====================================================
   BRANDING IMAGE UPLOAD
===================================================== */

.branding-image-preview {
    min-height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    padding: 15px;
    color: #94a3b8;
}

.branding-image-preview img {
    max-width: 100%;
    max-height: 190px;
    object-fit: contain;
    border-radius: 6px;
}

.banner-preview img {
    width: 100%;
    max-height: 190px;
    object-fit: contain;
}

.image-upload-status {
    min-height: 20px;
    font-size: 13px;
}

.image-upload-status.text-success {
    color: #198754 !important;
}

.image-upload-status.text-danger {
    color: #dc3545 !important;
}

.image-upload-status.text-muted {
    color: #6c757d !important;
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

        .institution-save-notice {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>


{{-- =====================================================
    TAB + AUTO SAVE SCRIPT
====================================================== --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | CONFIGURATION
    |--------------------------------------------------------------------------
    */

    const saveUrl = @json(
        route('dashboard.institutions.autosave', $institution->id)
    );

    const csrfToken = @json(csrf_token());

    const inputs = document.querySelectorAll(
        '.institution-autosave'
    );

    const timers = new WeakMap();
    const controllers = new WeakMap();
    const versions = new WeakMap();

    const globalStatus = document.getElementById(
        'globalSaveStatus'
    );


    /*
    |--------------------------------------------------------------------------
    | TAB SYSTEM
    |--------------------------------------------------------------------------
    */

    const tabs = document.querySelectorAll('.institution-tab');

    const sections = document.querySelectorAll(
        '.institution-section'
    );

    function activateTab(selectedTab) {

        const targetId = selectedTab.dataset.target;

        const targetSection = document.getElementById(targetId);

        if (!targetSection) {
            return;
        }

        tabs.forEach(function (tab) {

            tab.classList.remove('active', 'btn-primary');

            tab.classList.add('btn-outline-primary');

        });

        selectedTab.classList.remove('btn-outline-primary');

        selectedTab.classList.add('btn-primary', 'active');

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


    /*
    |--------------------------------------------------------------------------
    | STATUS DISPLAY
    |--------------------------------------------------------------------------
    */

    function setGlobalStatus(message, type = 'normal') {

        if (!globalStatus) {
            return;
        }

        globalStatus.className = '';

        if (type === 'success') {

            globalStatus.classList.add('text-success');

        } else if (type === 'error') {

            globalStatus.classList.add('text-danger');

        } else {

            globalStatus.classList.add('text-muted');

        }

        globalStatus.textContent = message;
    }

    function setFieldStatus(input, message, type = 'normal') {

        const status = input
            .parentElement
            .querySelector('.autosave-status');

        if (!status) {
            return;
        }

        status.className = 'autosave-status';

        if (type === 'success') {

            status.classList.add('text-success');

        } else if (type === 'error') {

            status.classList.add('text-danger');

        } else {

            status.classList.add('text-muted');

        }

        status.textContent = message;
    }


    /*
    |--------------------------------------------------------------------------
    | PREVIEW UPDATE
    |--------------------------------------------------------------------------
    */

    function updatePreview(field, value) {

        const previewMap = {
            name_bn: 'previewInstitutionName',
            name: 'previewInstitutionEnglishName',
            address: 'previewInstitutionAddress',
            phone: 'previewInstitutionPhone',
            email: 'previewInstitutionEmail',
            website: 'previewInstitutionWebsite'
        };

        const previewId = previewMap[field];

        if (!previewId) {
            return;
        }

        const element = document.getElementById(previewId);

        if (!element) {
            return;
        }

        if (field === 'phone') {

            element.innerHTML = value
                ? '<i class="fas fa-phone me-1"></i> ' + escapeHtml(value)
                : '';

        } else if (field === 'email') {

            element.innerHTML = value
                ? '<i class="fas fa-envelope me-1"></i> ' + escapeHtml(value)
                : '';

        } else {

            element.textContent = value || '';
        }
    }

    function escapeHtml(value) {

        return String(value).replace(/[&<>"']/g, function (character) {

            const entities = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };

            return entities[character];

        });
    }


    /*
    |--------------------------------------------------------------------------
    | SAVE FIELD
    |--------------------------------------------------------------------------
    */

    async function saveField(input) {

        const field = input.dataset.field;

        const value = input.value;

        const currentVersion = (versions.get(input) || 0) + 1;

        versions.set(input, currentVersion);

        const previousController = controllers.get(input);

        if (previousController) {
            previousController.abort();
        }

        const controller = new AbortController();

        controllers.set(input, controller);

        setFieldStatus(input, 'সংরক্ষণ হচ্ছে...', 'normal');

        setGlobalStatus('পরিবর্তন সংরক্ষণ হচ্ছে...', 'normal');

        try {

            const response = await fetch(saveUrl, {

                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },

                body: JSON.stringify({
                    field: field,
                    value: value
                }),

                signal: controller.signal

            });

            const result = await response.json();

            if (!response.ok || !result.success) {

                throw new Error(
                    result.message || 'তথ্য সংরক্ষণ করা যায়নি।'
                );

            }

            // Ignore an older response if a newer value was entered.
            if (versions.get(input) !== currentVersion) {
                return;
            }

            input.classList.remove('is-invalid');

            setFieldStatus(
                input,
                '✓ সংরক্ষিত',
                'success'
            );

            setGlobalStatus(
                '✓ পরিবর্তন সফলভাবে সংরক্ষিত হয়েছে',
                'success'
            );

            updatePreview(field, value);

            if (field === 'name_bn' || field === 'name') {

                const headerName = document.getElementById(
                    'institutionHeaderName'
                );

                if (headerName) {

                    const banglaInput = document.querySelector(
                        '[data-field="name_bn"]'
                    );

                    const englishInput = document.querySelector(
                        '[data-field="name"]'
                    );

                    headerName.textContent =
                        banglaInput?.value ||
                        englishInput?.value ||
                        'প্রতিষ্ঠানের নাম';
                }
            }

        } catch (error) {

            if (error.name === 'AbortError') {
                return;
            }

            if (versions.get(input) !== currentVersion) {
                return;
            }

            input.classList.add('is-invalid');

            setFieldStatus(
                input,
                error.message || 'সংরক্ষণ করতে সমস্যা হয়েছে',
                'error'
            );

            setGlobalStatus(
                'তথ্য সংরক্ষণ করা যায়নি',
                'error'
            );

        } finally {

            if (controllers.get(input) === controller) {
                controllers.delete(input);
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DEBOUNCE SAVE
    |--------------------------------------------------------------------------
    */

    function scheduleSave(input, delay = 700) {

        clearTimeout(timers.get(input));

        timers.set(
            input,
            setTimeout(function () {

                saveField(input);

            }, delay)
        );
    }


    /*
    |--------------------------------------------------------------------------
    | INPUT EVENTS
    |--------------------------------------------------------------------------
    */

    inputs.forEach(function (input) {

        input.addEventListener('input', function () {

            scheduleSave(this, 700);

        });

        input.addEventListener('change', function () {

            scheduleSave(this, 200);

        });

        input.addEventListener('blur', function () {

            clearTimeout(timers.get(this));

            // Avoid sending an unnecessary request for an unchanged field.
            if (this.dataset.lastSavedValue === this.value) {
                return;
            }

            saveField(this);

        });

        input.dataset.lastSavedValue = input.value;

    });

/*
|--------------------------------------------------------------------------
| INSTITUTION IMAGE UPLOAD
|--------------------------------------------------------------------------
*/

async function uploadInstitutionImage(
    input,
    field,
    previewId,
    placeholderId,
    statusId
) {

    const file = input.files[0];

    if (!file) {
        return;
    }

    const status = document.getElementById(statusId);
    const preview = document.getElementById(previewId);
    const placeholder = document.getElementById(placeholderId);

    const maxSize = field === 'logo'
        ? 2 * 1024 * 1024
        : 5 * 1024 * 1024;

    const allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ];

    /*
    |--------------------------------------------------------------------------
    | CLIENT SIDE VALIDATION
    |--------------------------------------------------------------------------
    */

    if (!allowedTypes.includes(file.type)) {

        status.className = 'image-upload-status text-danger';

        status.textContent =
            'শুধুমাত্র JPG, PNG অথবা WEBP ছবি আপলোড করুন।';

        input.value = '';

        return;
    }

    if (file.size > maxSize) {

        status.className = 'image-upload-status text-danger';

        status.textContent = field === 'logo'
            ? 'লোগোর সাইজ সর্বোচ্চ 2 MB হতে পারবে।'
            : 'ব্যানারের সাইজ সর্বোচ্চ 5 MB হতে পারবে।';

        input.value = '';

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | FORM DATA
    |--------------------------------------------------------------------------
    */

    const formData = new FormData();

    formData.append('field', field);
    formData.append('file', file);

    status.className = 'image-upload-status text-muted';

    status.textContent = 'ছবি আপলোড হচ্ছে...';

    setGlobalStatus('ছবি আপলোড হচ্ছে...', 'normal');

    try {

        const response = await fetch(saveUrl, {

            method: 'POST',

            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },

            body: formData

        });

        const result = await response.json();

        if (!response.ok || !result.success) {

            throw new Error(
                result.message || 'ছবি আপলোড করা যায়নি।'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE IMAGE PREVIEW
        |--------------------------------------------------------------------------
        */

        const imageUrl = result.url + '?t=' + Date.now();

        preview.src = imageUrl;

        preview.classList.remove('d-none');

        if (placeholder) {
            placeholder.classList.add('d-none');
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE HEADER LOGO
        |--------------------------------------------------------------------------
        */

        if (field === 'logo') {

            const headerLogo = document.getElementById(
                'institutionHeaderLogo'
            );

            const logoBox = document.querySelector(
                '.institution-logo-box'
            );

            if (headerLogo) {

                headerLogo.src = imageUrl;

            } else if (logoBox) {

                logoBox.innerHTML = '';

                const newHeaderLogo = document.createElement('img');

                newHeaderLogo.id = 'institutionHeaderLogo';

                newHeaderLogo.className = 'institution-logo';

                newHeaderLogo.alt = 'Institution Logo';

                newHeaderLogo.src = imageUrl;

                logoBox.appendChild(newHeaderLogo);
            }

            /*
             * Official preview logo
             */

            const officialLogo = document.getElementById(
                'officialPreviewLogo'
            );

            if (officialLogo) {

                officialLogo.src = imageUrl;

                officialLogo.classList.remove('d-none');
            }

            const sessionLogo = result.path;
        }

        /*
        |--------------------------------------------------------------------------
        | SUCCESS STATUS
        |--------------------------------------------------------------------------
        */

        status.className = 'image-upload-status text-success';

        status.innerHTML =
            '<i class="fas fa-check-circle me-1"></i> ছবি সফলভাবে সংরক্ষিত হয়েছে।';

        setGlobalStatus(
            '✓ ছবি সফলভাবে সংরক্ষিত হয়েছে',
            'success'
        );

    } catch (error) {

        status.className = 'image-upload-status text-danger';

        status.textContent =
            error.message || 'ছবি আপলোড করতে সমস্যা হয়েছে।';

        setGlobalStatus(
            'ছবি আপলোড করা যায়নি',
            'error'
        );

    } finally {

        input.value = '';
    }
}


/*
|--------------------------------------------------------------------------
| LOGO UPLOAD EVENT
|--------------------------------------------------------------------------
*/

const logoInput = document.getElementById(
    'institutionLogoInput'
);

if (logoInput) {

    logoInput.addEventListener('change', function () {

        uploadInstitutionImage(
            this,
            'logo',
            'institutionLogoPreview',
            'institutionLogoPlaceholder',
            'institutionLogoStatus'
        );

    });
}


/*
|--------------------------------------------------------------------------
| BANNER UPLOAD EVENT
|--------------------------------------------------------------------------
*/

const bannerInput = document.getElementById(
    'institutionBannerInput'
);

if (bannerInput) {

    bannerInput.addEventListener('change', function () {

        uploadInstitutionImage(
            this,
            'banner',
            'institutionBannerPreview',
            'institutionBannerPlaceholder',
            'institutionBannerStatus'
        );

    });
}
    /*
    |--------------------------------------------------------------------------
    | UPDATE LAST SAVED VALUE
    |--------------------------------------------------------------------------
    */

    const originalSaveField = saveField;

    // Keep the saved value synchronized after successful requests.
    inputs.forEach(function (input) {

        input.addEventListener('focus', function () {

            this.dataset.focusValue = this.value;

        });

    });

});
</script>
@endpush

@endsection