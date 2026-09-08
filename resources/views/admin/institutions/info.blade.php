@extends('layouts.admin')

@section('title', 'প্রতিষ্ঠানের তথ্য')

@section('page')

<div class="container-fluid">

    {{-- ==========================================================
        PAGE HEADER
    =========================================================== --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">
                <i class="fas fa-school me-2"></i>
                প্রতিষ্ঠানের তথ্য
            </h4>

            <div class="text-muted">
                প্রতিষ্ঠানের সকল তথ্য এখানে সংরক্ষণ ও ব্যবস্থাপনা করুন।
            </div>
        </div>


        {{-- ======================================================
            EXIT INSTITUTION MODE
            শুধু Super Admin + Institution Mode
        ======================================================= --}}
        @if(
            auth()->user()->is_super_admin
            && session()->has('active_institution_id')
        )

            <form
                action="{{ route('dashboard.institutions.exit') }}"
                method="POST"
                onsubmit="return confirm('আপনি কি প্রতিষ্ঠান মোড থেকে বের হয়ে Super Admin System Mode-এ ফিরে যেতে চান?');"
            >

                @csrf

                <button
                    type="submit"
                    class="btn btn-danger"
                >
                    <i class="fas fa-sign-out-alt me-2"></i>
                    প্রতিষ্ঠান থেকে বের হোন
                </button>

            </form>

        @endif

    </div>


    {{-- ==========================================================
        CURRENT INSTITUTION
    =========================================================== --}}
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <div class="d-flex align-items-center">

                <div
                    class="rounded-circle bg-primary text-white d-flex
                           align-items-center justify-content-center me-3"
                    style="width:55px;height:55px;"
                >
                    <i class="fas fa-school fa-lg"></i>
                </div>

                <div>

                    <h5 class="mb-1">
                        {{ $institution->name_bn ?? $institution->name ?? 'প্রতিষ্ঠানের নাম নেই' }}
                    </h5>

                    <div class="text-muted small">

                        @if(session('active_institution_id'))

                            Institution ID:
                            {{ session('active_institution_id') }}

                            <span class="badge bg-success ms-2">
                                Institution Mode
                            </span>

                        @else

                            System Mode

                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>



    {{-- ==========================================================
        TABS
    =========================================================== --}}
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <div class="row g-2">

                {{-- BASIC --}}
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


                {{-- ADDRESS --}}
                <div class="col-xl-3 col-md-6">

                    <button
                        type="button"
                        class="btn btn-outline-primary w-100 institution-tab"
                        data-target="address-info"
                    >

                        <i class="fas fa-map-marker-alt me-2"></i>

                        ২. ঠিকানা বিবরণী

                    </button>

                </div>


                {{-- CONTACT --}}
                <div class="col-xl-3 col-md-6">

                    <button
                        type="button"
                        class="btn btn-outline-primary w-100 institution-tab"
                        data-target="contact-info"
                    >

                        <i class="fas fa-phone-alt me-2"></i>

                        ৩. যোগাযোগ ও অনলাইন

                    </button>

                </div>


                {{-- BRANDING --}}
                <div class="col-xl-3 col-md-6">

                    <button
                        type="button"
                        class="btn btn-outline-primary w-100 institution-tab"
                        data-target="branding-info"
                    >

                        <i class="fas fa-stamp me-2"></i>

                        ৪. লোগো, সিল ও স্বাক্ষর

                    </button>

                </div>

            </div>

        </div>

    </div>



    {{-- ==========================================================
        1. BASIC INFORMATION
    =========================================================== --}}
    <div
        id="basic-info"
        class="institution-section"
    >

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white py-3">

                <h5 class="mb-0">

                    <i class="fas fa-id-card me-2 text-primary"></i>

                    মৌলিক পরিচিতি

                </h5>

                <small class="text-muted">
                    প্রতিষ্ঠানের বাংলা, ইংরেজি ও আরবি পরিচিতি
                </small>

            </div>


            <div class="card-body">

                <div class="row g-4">


                    {{-- BANGLA NAME --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">

                            প্রতিষ্ঠানের নাম (বাংলা)

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="name_bn"
                            value="{{ old('name_bn', $institution->name_bn ?? '') }}"
                            placeholder="প্রতিষ্ঠানের বাংলা নাম"
                        >

                    </div>


                    {{-- ENGLISH NAME --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">

                            Institution Name (English)

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="name_en"
                            value="{{ old('name_en', $institution->name_en ?? '') }}"
                            placeholder="Institution Name"
                        >

                    </div>


                    {{-- ARABIC NAME --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">

                            اسم المؤسسة (بالعربية)

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            class="form-control text-end"
                            dir="rtl"
                            name="name_ar"
                            value="{{ old('name_ar', $institution->name_ar ?? '') }}"
                            placeholder="اسم المؤسسة"
                        >

                    </div>



                    {{-- BANGLA SLOGAN --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">

                            স্লোগান / মূলমন্ত্র (বাংলা)

                        </label>

                        <textarea
                            class="form-control"
                            name="slogan_bn"
                            rows="3"
                            placeholder="প্রতিষ্ঠানের স্লোগান বা মূলমন্ত্র"
                        >{{ old('slogan_bn', $institution->slogan_bn ?? '') }}</textarea>

                    </div>



                    {{-- ARABIC SLOGAN --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">

                            شعار المؤسسة (بالعربية)

                        </label>

                        <textarea
                            class="form-control text-end"
                            dir="rtl"
                            name="slogan_ar"
                            rows="3"
                            placeholder="شعار المؤسسة"
                        >{{ old('slogan_ar', $institution->slogan_ar ?? '') }}</textarea>

                    </div>



                    {{-- ESTABLISHED YEAR --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">

                            স্থাপিত সন (Est. Year)

                        </label>

                        <input
                            type="number"
                            class="form-control"
                            name="established_year"
                            value="{{ old('established_year', $institution->established_year ?? '') }}"
                            placeholder="যেমন: ২০০৫"
                            min="1800"
                            max="{{ date('Y') }}"
                        >

                    </div>



                    {{-- REGISTRATION --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">

                            বোর্ড / সরকারি রেজিস্ট্রেশন নম্বর

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="registration_no"
                            value="{{ old('registration_no', $institution->registration_no ?? '') }}"
                            placeholder="রেজিস্ট্রেশন নম্বর"
                        >

                    </div>



                    {{-- INSTITUTION CODE --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">

                            প্রাতিষ্ঠানিক কোড (Code)

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="code"
                            value="{{ old('code', $institution->code ?? '') }}"
                            placeholder="প্রাতিষ্ঠানিক কোড"
                        >

                    </div>

                </div>

            </div>

        </div>

    </div>



    {{-- ==========================================================
        2. ADDRESS INFORMATION
    =========================================================== --}}
    <div
        id="address-info"
        class="institution-section d-none"
    >

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white py-3">

                <h5 class="mb-0">

                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>

                    ঠিকানা বিবরণী

                </h5>

                <small class="text-muted">
                    প্রতিষ্ঠানের সম্পূর্ণ ঠিকানা
                </small>

            </div>


            <div class="card-body">

                <div class="alert alert-info">

                    <i class="fas fa-info-circle me-2"></i>

                    এখানে প্রতিষ্ঠানের বাংলা, ইংরেজি ও আরবি ঠিকানা
                    সংরক্ষণ করা যাবে।

                </div>


                <div class="row g-4">


                    <div class="col-md-12">

                        <label class="form-label fw-semibold">
                            ঠিকানা (বাংলা)
                        </label>

                        <textarea
                            class="form-control"
                            rows="3"
                            name="address_bn"
                            placeholder="গ্রাম, ডাকঘর, উপজেলা, জেলা..."
                        >{{ old('address_bn', $institution->address_bn ?? '') }}</textarea>

                    </div>


                    <div class="col-md-12">

                        <label class="form-label fw-semibold">
                            Address (English)
                        </label>

                        <textarea
                            class="form-control"
                            rows="3"
                            name="address_en"
                            placeholder="Village, Post Office, Upazila, District..."
                        >{{ old('address_en', $institution->address_en ?? '') }}</textarea>

                    </div>


                    <div class="col-md-12">

                        <label class="form-label fw-semibold">
                            العنوان (بالعربية)
                        </label>

                        <textarea
                            class="form-control text-end"
                            dir="rtl"
                            rows="3"
                            name="address_ar"
                            placeholder="عنوان المؤسسة"
                        >{{ old('address_ar', $institution->address_ar ?? '') }}</textarea>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            পোস্ট কোড
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="postal_code"
                            value="{{ old('postal_code', $institution->postal_code ?? '') }}"
                            placeholder="পোস্ট কোড"
                        >

                    </div>


                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            উপজেলা
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="upazila"
                            value="{{ old('upazila', $institution->upazila ?? '') }}"
                            placeholder="উপজেলা"
                        >

                    </div>


                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            জেলা
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="district"
                            value="{{ old('district', $institution->district ?? '') }}"
                            placeholder="জেলা"
                        >

                    </div>

                </div>

            </div>

        </div>

    </div>



    {{-- ==========================================================
        3. CONTACT & ONLINE
    =========================================================== --}}
    <div
        id="contact-info"
        class="institution-section d-none"
    >

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white py-3">

                <h5 class="mb-0">

                    <i class="fas fa-globe me-2 text-primary"></i>

                    যোগাযোগ ও অনলাইন

                </h5>

                <small class="text-muted">
                    প্রতিষ্ঠানের যোগাযোগ ও অনলাইন তথ্য
                </small>

            </div>


            <div class="card-body">

                <div class="row g-4">


                    {{-- PHONE --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">

                            ফোন নম্বর

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="phone"
                            value="{{ old('phone', $institution->phone ?? '') }}"
                            placeholder="01XXXXXXXXX"
                        >

                    </div>


                    {{-- MOBILE --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">

                            মোবাইল নম্বর

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="mobile"
                            value="{{ old('mobile', $institution->mobile ?? '') }}"
                            placeholder="01XXXXXXXXX"
                        >

                    </div>


                    {{-- EMAIL --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">

                            ই-মেইল

                        </label>

                        <input
                            type="email"
                            class="form-control"
                            name="email"
                            value="{{ old('email', $institution->email ?? '') }}"
                            placeholder="institution@example.com"
                        >

                    </div>


                    {{-- WEBSITE --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">

                            ওয়েবসাইট

                        </label>

                        <input
                            type="url"
                            class="form-control"
                            name="website"
                            value="{{ old('website', $institution->website ?? '') }}"
                            placeholder="https://example.com"
                        >

                    </div>


                    {{-- FACEBOOK --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">

                            Facebook Page

                        </label>

                        <input
                            type="url"
                            class="form-control"
                            name="facebook"
                            value="{{ old('facebook', $institution->facebook ?? '') }}"
                            placeholder="Facebook Page URL"
                        >

                    </div>


                    {{-- YOUTUBE --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">

                            YouTube Channel

                        </label>

                        <input
                            type="url"
                            class="form-control"
                            name="youtube"
                            value="{{ old('youtube', $institution->youtube ?? '') }}"
                            placeholder="YouTube Channel URL"
                        >

                    </div>


                    {{-- GOOGLE MAP --}}
                    <div class="col-md-12">

                        <label class="form-label fw-semibold">

                            Google Maps Location

                        </label>

                        <textarea
                            class="form-control"
                            name="google_map"
                            rows="3"
                            placeholder="Google Maps embed/link"
                        >{{ old('google_map', $institution->google_map ?? '') }}</textarea>

                    </div>

                </div>

            </div>

        </div>

    </div>



    {{-- ==========================================================
        4. LOGO / STAMP / SIGNATURE
    =========================================================== --}}
    <div
        id="branding-info"
        class="institution-section d-none"
    >

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white py-3">

                <h5 class="mb-0">

                    <i class="fas fa-stamp me-2 text-primary"></i>

                    লোগো, সিল ও স্বাক্ষর

                </h5>

                <small class="text-muted">
                    প্রতিষ্ঠানের অফিসিয়াল ব্র্যান্ডিং তথ্য
                </small>

            </div>


            <div class="card-body">

                <div class="row g-4">


                    {{-- LOGO --}}
                    <div class="col-md-4">

                        <div class="card h-100 border">

                            <div class="card-body text-center">

                                <h6 class="fw-bold mb-3">

                                    <i class="fas fa-image me-2"></i>

                                    প্রতিষ্ঠানের লোগো

                                </h6>


                                <div
                                    class="border rounded d-flex
                                           align-items-center
                                           justify-content-center mb-3"
                                    style="height:180px;"
                                >

                                    @if(!empty($institution->logo))

                                        <img
                                            src="{{ asset('storage/' . $institution->logo) }}"
                                            alt="Institution Logo"
                                            style="max-height:150px;max-width:200px;"
                                        >

                                    @else

                                        <div class="text-muted">

                                            <i class="fas fa-image fa-3x mb-2"></i>

                                            <div>
                                                লোগো নেই
                                            </div>

                                        </div>

                                    @endif

                                </div>


                                <input
                                    type="file"
                                    class="form-control"
                                    name="logo"
                                    accept="image/*"
                                >

                            </div>

                        </div>

                    </div>



                    {{-- STAMP --}}
                    <div class="col-md-4">

                        <div class="card h-100 border">

                            <div class="card-body text-center">

                                <h6 class="fw-bold mb-3">

                                    <i class="fas fa-stamp me-2"></i>

                                    অফিসিয়াল সিল

                                </h6>


                                <div
                                    class="border rounded d-flex
                                           align-items-center
                                           justify-content-center mb-3"
                                    style="height:180px;"
                                >

                                    @if(!empty($institution->stamp))

                                        <img
                                            src="{{ asset('storage/' . $institution->stamp) }}"
                                            alt="Institution Stamp"
                                            style="max-height:150px;max-width:200px;"
                                        >

                                    @else

                                        <div class="text-muted">

                                            <i class="fas fa-stamp fa-3x mb-2"></i>

                                            <div>
                                                সিল নেই
                                            </div>

                                        </div>

                                    @endif

                                </div>


                                <input
                                    type="file"
                                    class="form-control"
                                    name="stamp"
                                    accept="image/*"
                                >

                            </div>

                        </div>

                    </div>



                    {{-- SIGNATURE --}}
                    <div class="col-md-4">

                        <div class="card h-100 border">

                            <div class="card-body text-center">

                                <h6 class="fw-bold mb-3">

                                    <i class="fas fa-signature me-2"></i>

                                    অফিসিয়াল স্বাক্ষর

                                </h6>


                                <div
                                    class="border rounded d-flex
                                           align-items-center
                                           justify-content-center mb-3"
                                    style="height:180px;"
                                >

                                    @if(!empty($institution->signature))

                                        <img
                                            src="{{ asset('storage/' . $institution->signature) }}"
                                            alt="Institution Signature"
                                            style="max-height:150px;max-width:200px;"
                                        >

                                    @else

                                        <div class="text-muted">

                                            <i class="fas fa-signature fa-3x mb-2"></i>

                                            <div>
                                                স্বাক্ষর নেই
                                            </div>

                                        </div>

                                    @endif

                                </div>


                                <input
                                    type="file"
                                    class="form-control"
                                    name="signature"
                                    accept="image/*"
                                >

                            </div>

                        </div>

                    </div>



                    {{-- OFFICIAL PAD PREVIEW --}}
                    <div class="col-12">

                        <div class="card border mt-2">

                            <div class="card-header bg-light">

                                <h6 class="mb-0">

                                    <i class="fas fa-file-alt me-2"></i>

                                    অফিসিয়াল প্যাড প্রিভিউ

                                </h6>

                            </div>


                            <div class="card-body">

                                <div
                                    class="border rounded p-4"
                                    style="min-height:250px;"
                                >

                                    <div class="text-center">

                                        @if(!empty($institution->logo))

                                            <img
                                                src="{{ asset('storage/' . $institution->logo) }}"
                                                style="height:70px;"
                                                class="mb-2"
                                            >

                                        @endif


                                        <h4 class="fw-bold mb-1">

                                            {{ $institution->name_bn ?? $institution->name ?? 'প্রতিষ্ঠানের নাম' }}

                                        </h4>


                                        @if(!empty($institution->name_en))

                                            <div class="text-muted">

                                                {{ $institution->name_en }}

                                            </div>

                                        @endif


                                        @if(!empty($institution->slogan_bn))

                                            <div class="mt-2">

                                                {{ $institution->slogan_bn }}

                                            </div>

                                        @endif

                                    </div>


                                    <hr>


                                    <div class="text-muted small">

                                        অফিসিয়াল প্যাডের মূল কনটেন্ট এখানে
                                        প্রিভিউ করা হবে।

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ==========================================================
    TAB SCRIPT
=========================================================== --}}
@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const tabs = document.querySelectorAll('.institution-tab');

    const sections = document.querySelectorAll(
        '.institution-section'
    );


    tabs.forEach(function (tab) {

        tab.addEventListener('click', function () {

            const target = this.dataset.target;


            /*
            |--------------------------------------------------------------------------
            | Reset all tabs
            |--------------------------------------------------------------------------
            */

            tabs.forEach(function (item) {

                item.classList.remove(
                    'btn-primary'
                );

                item.classList.add(
                    'btn-outline-primary'
                );

            });


            /*
            |--------------------------------------------------------------------------
            | Activate clicked tab
            |--------------------------------------------------------------------------
            */

            this.classList.remove(
                'btn-outline-primary'
            );

            this.classList.add(
                'btn-primary'
            );


            /*
            |--------------------------------------------------------------------------
            | Hide all sections
            |--------------------------------------------------------------------------
            */

            sections.forEach(function (section) {

                section.classList.add(
                    'd-none'
                );

            });


            /*
            |--------------------------------------------------------------------------
            | Show selected section
            |--------------------------------------------------------------------------
            */

            const selectedSection =
                document.getElementById(target);


            if (selectedSection) {

                selectedSection.classList.remove(
                    'd-none'
                );

            }

        });

    });

});

</script>

@endpush

@endsection