@extends('layouts.admin')

@section('title', 'ডেটা এক্সপোর্ট')

@section('page')
@php
    $standardColumns = [
        'photo'              => 'ছবি',
        'student_id'         => 'আইডি',
        'name'               => 'নাম',
        'father_name'        => 'বাবার নাম',
        'mother_name'        => 'মাতার নাম',
        'phone'              => 'মোবাইল এক',
        'phone2'             => 'মোবাইল ২',
        'email'              => 'ই-মেইল',
        'academic_year'      => 'শিক্ষাবর্ষ',
        'class'              => 'ক্লাশ',
        'section'            => 'সাব ক্লাস',
        'admission_no'       => 'ভর্তি সিরিয়াল',
        'gender'             => 'লিঙ্গ',
        'residence'          => 'আবাসিক',
        'admission_type'     => 'নতুন/পুরাতন',
        'date_of_birth'      => 'জন্ম তারিখ',
        'national_id'        => 'জাতীয় পরিচয়পত্র/জন্ম নিবন্ধন',
        'blood_group'        => 'রক্তের গ্রুপ',
        'permanent_division' => 'স্থায়ী বিভাগ',
        'permanent_district' => 'স্থায়ী জেলা',
        'permanent_thana'    => 'স্থায়ী থানা',
        'permanent_post'     => 'স্থায়ী ডাক',
        'permanent_village'  => 'স্থায়ী গ্রাম',
        'present_division'   => 'বর্তমান বিভাগ',
        'present_district'   => 'বর্তমান জেলা',
        'present_thana'      => 'বর্তমান থানা',
        'present_post'       => 'বর্তমান ডাক',
        'present_village'    => 'বর্তমান গ্রাম',
        'financial_status'   => 'আর্থিক অবস্থা',
        'user_status'        => 'ইউজার সক্রিয়',
    ];

   /*
    |--------------------------------------------------------------------------
    | SELECTED REPORT
    |--------------------------------------------------------------------------
    */

    $selectedReport = $selectedReport ?? null;


    /*
    |--------------------------------------------------------------------------
    | SELECTED COLUMNS
    |--------------------------------------------------------------------------
    */

    $selectedColumns = old(
        'columns',
        $selectedReport && is_array($selectedReport->columns)
            ? $selectedReport->columns
            : ['student_id', 'name', 'class']
    );

    $selectedColumns = is_array($selectedColumns)
        ? $selectedColumns
        : [];


    /*
    |--------------------------------------------------------------------------
    | FILTER VALUES
    |--------------------------------------------------------------------------
    */

    $selectedAcademicYear = request('academic_year_id');
    $selectedSection = request('section_id');
    $selectedAdmissionType = request('admission_type');
    $selectedResidence = request('residence_status');
    $selectedStatus = request('status');


    /*
    |--------------------------------------------------------------------------
    | SELECTED REPORT DATA FOR JAVASCRIPT
    |--------------------------------------------------------------------------
    */

    $selectedReportData = null;

    if ($selectedReport) {
        $selectedReportData = [
            'id' => $selectedReport->id,
            'name' => $selectedReport->report_name,
            'columns' => is_array($selectedReport->columns)
                ? $selectedReport->columns
                : [],
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | REPORT VALIDATION ERRORS
    |--------------------------------------------------------------------------
    */

    $hasReportValidationErrors = $errors->any() && (
        old('report_name') !== null ||
        old('columns') !== null ||
        old('report_id') !== null
    );
@endphp

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    /* =====================================================
       DATA EXPORT PAGE
    ===================================================== */

    .de-page {
        --de-primary: #1a3375;
        --de-primary-hover: #12275c;
        --de-primary-soft: #edf2ff;
        --de-border: #e5eaf2;
        --de-muted: #64748b;
        --de-text: #172033;

        color: var(--de-text);
    }

    .de-page *,
    .de-modal *,
    .de-modal {
        box-sizing: border-box;
    }

    .de-page .de-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 22px;
    }

    .de-page .de-title {
        margin: 0 0 6px;
        color: var(--de-text);
        font-size: 25px;
        font-weight: 800;
    }

    .de-page .de-subtitle {
        margin: 0;
        color: var(--de-muted);
        font-size: 13px;
        line-height: 1.7;
    }

    .de-page .de-card {
        margin-bottom: 22px;
        overflow: hidden;
        background: var(--card, #fff);
        border: 1px solid var(--de-border);
        border-radius: 14px;
        box-shadow: 0 5px 22px rgba(15, 23, 42, .035);
    }

    .de-page .de-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        padding: 17px 20px;
        border-bottom: 1px solid var(--de-border);
    }

    .de-page .de-card-title {
        margin: 0;
        color: var(--de-text);
        font-size: 16px;
        font-weight: 800;
    }

    .de-page .de-card-body {
        padding: 20px;
    }

    .de-page .de-label,
    .de-modal .de-label {
        display: block;
        margin-bottom: 7px;
        color: var(--de-text);
        font-size: 13px;
        font-weight: 700;
    }

    .de-page .de-required,
    .de-modal .de-required {
        color: #dc2626;
    }

    .de-page .form-control,
    .de-page .form-select,
    .de-modal .form-control,
    .de-modal .form-select {
        display: block;
        width: 100%;
        min-height: 43px;
        padding: 10px 12px;
        border: 1px solid #dbe2ed;
        border-radius: 9px;
        background-color: var(--card, #fff);
        color: var(--text, #172033);
        font-size: 13px;
        outline: none;
        box-shadow: none;
    }

    .de-page .form-control:focus,
    .de-page .form-select:focus,
    .de-modal .form-control:focus,
    .de-modal .form-select:focus {
        border-color: var(--de-primary);
        box-shadow: 0 0 0 3px rgba(26, 51, 117, .09);
    }

    .de-page .de-btn,
    .de-modal .de-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 40px;
        padding: 9px 14px;
        border: 1px solid transparent;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.4;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition: background .2s ease, border-color .2s ease;
    }

    .de-page .de-btn-primary,
    .de-modal .de-btn-primary {
        border-color: var(--de-primary);
        background: var(--de-primary);
        color: #fff;
    }

    .de-page .de-btn-primary:hover,
    .de-modal .de-btn-primary:hover {
        border-color: var(--de-primary-hover);
        background: var(--de-primary-hover);
        color: #fff;
    }

    .de-page .de-btn-light,
    .de-modal .de-btn-light {
        border-color: #dbe2ed;
        background: #fff;
        color: #334155;
    }

    .de-page .de-btn-light:hover,
    .de-modal .de-btn-light:hover {
        background: #f8fafc;
    }

    .de-page .de-btn:disabled,
    .de-modal .de-btn:disabled {
        opacity: .65;
        cursor: not-allowed;
    }

    .de-page .de-filter-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 17px;
    }

    .de-page .de-filter-note {
        margin-top: 14px;
        color: var(--de-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .de-page .de-alert {
        margin-bottom: 18px;
        padding: 12px 15px;
        border: 1px solid transparent;
        border-radius: 10px;
        font-size: 13px;
        line-height: 1.7;
    }

    .de-page .de-alert-success {
        border-color: #a7f3d0;
        background: #ecfdf5;
        color: #065f46;
    }

    .de-page .de-alert-danger {
        border-color: #fecdd3;
        background: #fff1f2;
        color: #9f1239;
    }

    .de-page .de-alert-warning {
        border-color: #fde68a;
        background: #fffbeb;
        color: #92400e;
    }

    .de-page .de-help,
    .de-modal .de-help {
        margin-top: 7px;
        color: var(--de-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    /* =====================================================
       FILTER RESULT PLACEHOLDER
    ===================================================== */

    .de-page .de-template-placeholder {
        min-height: 230px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 12px;
        padding: 35px 20px;
        text-align: center;
    }

    .de-page .de-template-placeholder-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 64px;
        height: 64px;
        border: 1px solid #dbe5ff;
        border-radius: 18px;
        background: var(--de-primary-soft);
        color: var(--de-primary);
        font-size: 25px;
    }

    .de-page .de-template-placeholder h3 {
        margin: 0;
        color: var(--de-text);
        font-size: 17px;
        font-weight: 800;
    }

    .de-page .de-template-placeholder p {
        max-width: 440px;
        margin: 0;
        color: var(--de-muted);
        font-size: 13px;
        line-height: 1.8;
    }

    .de-page .de-report-edit-button {
        margin-top: 8px;
    }

    /* =====================================================
       MODAL
       IMPORTANT: These selectors are global because the
       modal elements are outside .de-page.
    ===================================================== */

    .de-modal {
        position: fixed !important;
        inset: 0 !important;
        z-index: 999999 !important;

        display: none;
        align-items: center;
        justify-content: center;

        width: 100%;
        height: 100%;
        height: 100dvh;

        padding: 18px;

        background: rgba(15, 23, 42, .62);
        overflow-x: hidden;
        overflow-y: auto;
        overscroll-behavior: contain;

        -webkit-overflow-scrolling: touch;
    }

    .de-modal.is-open {
        display: flex !important;
    }

    .de-modal-dialog {
        position: relative;
        flex: 0 0 auto;

        width: 100%;
        max-width: 850px;

        max-height: calc(100vh - 36px);
        max-height: calc(100dvh - 36px);

        display: flex;
        flex-direction: column;

        margin: auto;
        overflow: hidden;

        border: 1px solid rgba(255, 255, 255, .15);
        border-radius: 16px;

        background: #fff;
        color: #172033;

        box-shadow: 0 25px 80px rgba(0, 0, 0, .28);

        animation: deModalEnter .18s ease-out;
    }

    .de-modal-dialog.de-modal-small {
        max-width: 500px;
    }

    .de-modal-header {
        flex: 0 0 auto;

        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 15px;

        padding: 19px 22px;

        border-bottom: 1px solid #e5eaf2;
        background: #fff;
    }

    .de-modal-header h3 {
        margin: 0;
        color: #172033;
        font-size: 18px;
        font-weight: 800;
        line-height: 1.5;
    }

    .de-modal-header .de-modal-subtitle {
        margin-top: 5px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.7;
    }

    .de-modal-close {
        flex: 0 0 auto;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        width: 36px;
        height: 36px;

        padding: 0;

        border: 0;
        border-radius: 9px;

        background: #f1f5f9;
        color: #334155;

        font-size: 24px;
        line-height: 1;

        cursor: pointer;
    }

    .de-modal-close:hover {
        background: #e2e8f0;
    }

    .de-modal-body {
        flex: 1 1 auto;

        min-height: 0;
        padding: 22px;

        overflow-x: hidden;
        overflow-y: auto;
        overscroll-behavior: contain;

        -webkit-overflow-scrolling: touch;
    }

    .de-modal-footer {
        flex: 0 0 auto;

        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 9px;

        padding: 15px 22px;

        border-top: 1px solid #e5eaf2;
        background: #fff;
    }

    .de-modal .de-form-group {
        margin-bottom: 18px;
    }

    .de-modal .de-form-group:last-child {
        margin-bottom: 0;
    }

    /* Column sections */

    .de-modal .de-column-section {
        margin-top: 19px;
        overflow: hidden;
        border: 1px solid #e5eaf2;
        border-radius: 11px;
    }

    .de-modal .de-column-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;

        padding: 12px 15px;

        border-bottom: 1px solid #e5eaf2;
        background: #f8fafc;
    }

    .de-modal .de-column-section-header strong {
        color: #172033;
        font-size: 13px;
        font-weight: 800;
    }

    .de-modal .de-column-count {
        color: var(--de-primary, #1a3375);
        font-size: 12px;
        font-weight: 800;
    }

    .de-modal .de-column-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0;

        max-height: 300px;
        overflow-y: auto;

        padding: 8px 12px;
    }

    .de-modal .de-column-option {
        display: flex;
        align-items: flex-start;
        gap: 9px;

        min-width: 0;
        padding: 10px 6px;

        border-radius: 6px;

        color: #334155;
        font-size: 12px;
        line-height: 1.6;

        overflow-wrap: anywhere;
        cursor: pointer;
    }

    .de-modal .de-column-option:hover {
        background: #f8fafc;
    }

    .de-modal .de-column-option input[type="checkbox"] {
        flex: 0 0 auto;
        width: 16px;
        height: 16px;
        margin: 3px 0 0;
        accent-color: #1a3375;
        cursor: pointer;
    }

    .de-modal .de-column-option span {
        min-width: 0;
    }

    .de-modal .de-column-option.is-disabled {
        opacity: .5;
        cursor: not-allowed;
    }

    .de-modal .de-column-option.is-disabled input {
        cursor: not-allowed;
    }

    .de-modal .de-empty-columns {
        grid-column: 1 / -1;
        padding: 20px 12px;
        color: #64748b;
        text-align: center;
        font-size: 13px;
    }

    .de-modal .de-form-error {
        display: none;
        margin-top: 15px;
        padding: 11px 13px;
        border: 1px solid #fecdd3;
        border-radius: 9px;
        background: #fff1f2;
        color: #9f1239;
        font-size: 12px;
        line-height: 1.7;
    }

    body.de-modal-open {
        overflow: hidden !important;
    }

    @keyframes deModalEnter {
        from {
            opacity: 0;
            transform: translateY(10px) scale(.99);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Dark theme compatibility */

    body.dark-mode .de-page .de-card {
        background: var(--card, #0f172a);
    }

    body.dark-mode .de-page .de-title,
    body.dark-mode .de-page .de-card-title {
        color: var(--text, #e5e7eb);
    }

    body.dark-mode .de-page .form-control,
    body.dark-mode .de-page .form-select {
        background: var(--card, #0f172a);
        color: var(--text, #e5e7eb);
    }

    /* Responsive */

    @media (max-width: 991.98px) {
        .de-page .de-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .de-modal .de-column-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .de-page .de-header {
            align-items: stretch;
        }

        .de-page .de-header > .de-btn {
            width: 100%;
        }

        .de-page .de-title {
            font-size: 21px;
        }

        .de-page .de-card-body {
            padding: 15px;
        }

        .de-page .de-filter-grid {
            grid-template-columns: 1fr;
        }

        .de-page .de-template-placeholder {
            min-height: 190px;
            padding: 28px 15px;
        }

        .de-modal {
            align-items: center;
            padding: 8px;
        }

        .de-modal-dialog {
            max-height: calc(100vh - 16px);
            max-height: calc(100dvh - 16px);
            border-radius: 12px;
        }

        .de-modal-header,
        .de-modal-body,
        .de-modal-footer {
            padding-left: 15px;
            padding-right: 15px;
        }

        .de-modal .de-column-grid {
            grid-template-columns: 1fr;
            max-height: 250px;
        }

        .de-modal-footer .de-btn {
            flex: 1 1 auto;
        }
    }
</style>


<div class="de-page">

    {{-- =================================================
         PAGE HEADER
    ================================================== --}}
    <div class="de-header">
        <div>
            <h1 class="de-title">
                <i class="fas fa-file-export me-2" style="color:#1a3375"></i>
                ডেটা এক্সপোর্ট
            </h1>

            <p class="de-subtitle">
                প্রয়োজনীয় ফিল্টার নির্বাচন করুন এবং আপনার পছন্দ অনুযায়ী রিপোর্ট তৈরি করুন।
            </p>
        </div>

        <button
            type="button"
            class="de-btn de-btn-primary"
            id="openReportModal"
        >
            <i class="fas fa-plus"></i>
            রিপোর্ট তৈরি করুন
        </button>
    </div>


    {{-- =================================================
         FLASH MESSAGES
    ================================================== --}}
    @if(session('success'))
        <div class="de-alert de-alert-success">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="de-alert de-alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="de-alert de-alert-danger">
            <strong>
                <i class="fas fa-exclamation-triangle me-1"></i>
                তথ্য যাচাই করুন:
            </strong>

            <ul class="mb-0 mt-2 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- =================================================
         FILTER PANEL
    ================================================== --}}
    <div class="de-card">

        <div class="de-card-header">
            <h2 class="de-card-title">
                <i class="fas fa-filter me-2" style="color:#1a3375"></i>
                রিপোর্ট ফিল্টার
            </h2>

            <a
                href="{{ route('dashboard.students.data-export') }}"
                class="de-btn de-btn-light"
            >
                <i class="fas fa-redo-alt"></i>
                ফিল্টার রিসেট
            </a>
        </div>

        <div class="de-card-body">

            <form
                method="GET"
                action="{{ route('dashboard.students.data-export') }}"
                id="deFilterForm"
            >

                <div class="de-filter-grid">

                    {{-- Saved report selection --}}
                    <div>
                        <label for="filter_report_id" class="de-label">
                            রিপোর্ট নির্বাচন
                        </label>

                        <select
                            name="report_id"
                            id="filter_report_id"
                            class="form-select de-auto-filter"
                        >
                            <option value="">রিপোর্ট নির্বাচন করুন</option>

                            @foreach($reports as $report)
                                <option
                                    value="{{ $report->id }}"
                                    {{ (string) request('report_id') === (string) $report->id ? 'selected' : '' }}
                                >
                                    {{ $report->report_name }}
                                </option>
                            @endforeach
                        </select>

                        @if($selectedReport)
                            <button
                                type="button"
                                class="de-btn de-btn-light de-report-edit-button"
                                id="editSelectedReport"
                            >
                                <i class="fas fa-edit"></i>
                                নির্বাচিত রিপোর্ট সম্পাদনা
                            </button>
                        @endif
                    </div>


                    {{-- Academic year --}}
                    <div>
                        <label for="academic_year_id" class="de-label">
                            শিক্ষাবর্ষ
                        </label>

                        <select
                            name="academic_year_id"
                            id="academic_year_id"
                            class="form-select de-auto-filter"
                        >
                            <option value="">সকল শিক্ষাবর্ষ</option>

                            @foreach($academicYears as $year)
                                <option
                                    value="{{ $year->id }}"
                                    {{ (string) $selectedAcademicYear === (string) $year->id ? 'selected' : '' }}
                                >
                                    {{ $year->name_bn ?? $year->name ?? ('শিক্ষাবর্ষ #' . $year->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Section --}}
                    <div>
                        <label for="section_id" class="de-label">
                            সাব ক্লাস
                        </label>

                        <select
                            name="section_id"
                            id="section_id"
                            class="form-select de-auto-filter"
                        >
                            <option value="">সকল সাব ক্লাস</option>

                            @foreach($sections as $section)
                                <option
                                    value="{{ $section->id }}"
                                    {{ (string) $selectedSection === (string) $section->id ? 'selected' : '' }}
                                >
                                    {{ $section->name_bn ?? $section->name ?? ('সাব ক্লাস #' . $section->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Admission type --}}
                    <div>
                        <label for="admission_type" class="de-label">
                            নতুন/পুরাতন
                        </label>

                        <select
                            name="admission_type"
                            id="admission_type"
                            class="form-select de-auto-filter"
                        >
                            <option value="">সব শিক্ষার্থী</option>

                            <option
                                value="new"
                                {{ $selectedAdmissionType === 'new' ? 'selected' : '' }}
                            >
                                নতুন ভর্তি
                            </option>

                            <option
                                value="old"
                                {{ $selectedAdmissionType === 'old' ? 'selected' : '' }}
                            >
                                পুরাতন ভর্তি
                            </option>
                        </select>
                    </div>


                    {{-- Residence --}}
                    <div>
                        <label for="residence_status" class="de-label">
                            আবাসন অবস্থা
                        </label>

                        <select
                            name="residence_status"
                            id="residence_status"
                            class="form-select de-auto-filter"
                        >
                            <option value="">সকল আবাসন অবস্থা</option>

                            <option
                                value="resident"
                                {{ $selectedResidence === 'resident' ? 'selected' : '' }}
                            >
                                আবাসিক
                            </option>

                            <option
                                value="non-resident"
                                {{ $selectedResidence === 'non-resident' ? 'selected' : '' }}
                            >
                                অনাবাসিক
                            </option>

                            <option
                                value="daycare"
                                {{ $selectedResidence === 'daycare' ? 'selected' : '' }}
                            >
                                ডে-কেয়ার
                            </option>

                            <option
                                value="nightcare"
                                {{ $selectedResidence === 'nightcare' ? 'selected' : '' }}
                            >
                                নাইট-কেয়ার
                            </option>
                        </select>
                    </div>


                    {{-- Student status --}}
                    <div>
                        <label for="status" class="de-label">
                            স্ট্যাটাস
                        </label>

                        <select
                            name="status"
                            id="status"
                            class="form-select de-auto-filter"
                        >
                            <option value="">সব স্ট্যাটাস</option>

                            <option
                                value="1"
                                {{ (string) $selectedStatus === '1' ? 'selected' : '' }}
                            >
                                সক্রিয়
                            </option>

                            <option
                                value="0"
                                {{ (string) $selectedStatus === '0' ? 'selected' : '' }}
                            >
                                নিষ্ক্রিয়
                            </option>
                        </select>
                    </div>

                </div>

                <div class="de-filter-note">
                    <i class="fas fa-info-circle me-1"></i>
                    যেকোনো ফিল্টার পরিবর্তন করলে পেজ স্বয়ংক্রিয়ভাবে আপডেট হবে।
                </div>

            </form>
        </div>
    </div>


    {{-- =================================================
         TEMPLATE DISPLAY AREA
         Student table, summary cards and saved reports list
         have intentionally been removed.
    ================================================== --}}
    <div class="de-card">

        <div class="de-card-header">
            <h2 class="de-card-title">
                <i class="fas fa-table me-2" style="color:#1a3375"></i>
                রিপোর্ট প্রিভিউ
            </h2>
        </div>

        <div class="de-template-placeholder">

            <div class="de-template-placeholder-icon">
                <i class="fas fa-file-alt"></i>
            </div>

            <h3>Select a template to display data</h3>

            <p>
                ডেটা দেখতে ফিল্টার থেকে একটি রিপোর্ট নির্বাচন করুন।
                নতুন রিপোর্ট তৈরি করতে “রিপোর্ট তৈরি করুন” বাটনে ক্লিক করুন।
            </p>

        </div>
    </div>

</div>


{{-- =====================================================
     REPORT CONFIGURATION MODAL
     Kept outside .de-page intentionally.
===================================================== --}}
<div
    class="de-modal"
    id="reportModal"
    aria-hidden="true"
    role="dialog"
    aria-modal="true"
    aria-labelledby="reportModalTitle"
>
    <div class="de-modal-dialog">

        <div class="de-modal-header">

            <div>
                <h3 id="reportModalTitle">
                    ডেটা এক্সপোর্ট কনফিগারেশন
                </h3>

                <div class="de-modal-subtitle">
                    রিপোর্টের নাম দিন এবং সর্বোচ্চ ১০টি কলাম নির্বাচন করুন।
                </div>
            </div>

            <button
                type="button"
                class="de-modal-close"
                data-close-modal="reportModal"
                aria-label="বন্ধ করুন"
            >&times;</button>

        </div>


        <form
            method="POST"
            action="{{ route('dashboard.students.data-export.store') }}"
            id="reportConfigForm"
        >
            @csrf

            <input
                type="hidden"
                name="report_id"
                id="config_report_id"
                value="{{ old('report_id', $selectedReport->id ?? '') }}"
            >

            <div class="de-modal-body">

                {{-- Report name --}}
                <div class="de-form-group">

                    <label for="report_name" class="de-label">
                        রিপোর্টের নাম <span class="de-required">*</span>
                    </label>

                    <input
                        type="text"
                        name="report_name"
                        id="report_name"
                        class="form-control"
                        maxlength="150"
                        required
                        value="{{ old('report_name', $selectedReport->report_name ?? '') }}"
                        placeholder="যেমন: ২০২৬ সালের শিক্ষার্থী রিপোর্ট"
                    >

                </div>


                {{-- Standard columns --}}
                <div class="de-column-section">

                    <div class="de-column-section-header">

                        <strong>
                            <i class="fas fa-list-check me-2"></i>
                            স্ট্যান্ডার্ড কলাম
                        </strong>

                        <span class="de-column-count">
                            <span id="selectedColumnCount">0</span> / 10
                        </span>

                    </div>

                    <div class="de-column-grid" id="standardColumnGrid">

                        @foreach($standardColumns as $key => $label)

                            <label class="de-column-option">

                                <input
                                    type="checkbox"
                                    class="js-report-column"
                                    name="columns[]"
                                    value="{{ $key }}"
                                    {{ in_array($key, $selectedColumns, true) ? 'checked' : '' }}
                                >

                                <span>{{ $label }}</span>

                            </label>

                        @endforeach

                    </div>
                </div>


                {{-- Custom columns --}}
                <div class="de-column-section">

                    <div class="de-column-section-header">

                        <strong>
                            <i class="fas fa-puzzle-piece me-2"></i>
                            কাস্টম কলাম
                        </strong>

                        <button
                            type="button"
                            class="de-btn de-btn-light"
                            id="openCustomColumnModal"
                        >
                            <i class="fas fa-plus"></i>
                            নতুন কলাম
                        </button>

                    </div>

                    <div class="de-column-grid" id="customColumnGrid">

                        @forelse($customColumns as $column)

                            <label class="de-column-option">

                                <input
                                    type="checkbox"
                                    class="js-report-column"
                                    name="columns[]"
                                    value="custom_{{ $column->id }}"
                                    {{ in_array('custom_' . $column->id, $selectedColumns, true) ? 'checked' : '' }}
                                >

                                <span>
                                    {{ $column->name }}

                                    <small class="d-block text-muted">
                                        {{ $column->type }}
                                    </small>
                                </span>

                            </label>

                        @empty

                            <div
                                class="de-empty-columns"
                                id="noCustomColumnsMessage"
                            >
                                এখনো কোনো কাস্টম কলাম তৈরি করা হয়নি।
                            </div>

                        @endforelse

                    </div>
                </div>


                <div class="de-help">
                    <i class="fas fa-info-circle me-1"></i>
                    একই রিপোর্টে সর্বোচ্চ ১০টি আলাদা কলাম নির্বাচন করা যাবে।
                </div>


                <div
                    id="reportFormError"
                    class="de-form-error"
                    role="alert"
                    aria-live="polite"
                ></div>

            </div>


            <div class="de-modal-footer">

                <button
                    type="button"
                    class="de-btn de-btn-light"
                    data-close-modal="reportModal"
                >
                    বাতিল
                </button>

                <button
                    type="submit"
                    class="de-btn de-btn-primary"
                    id="saveReportButton"
                >
                    <i class="fas fa-save"></i>
                    রিপোর্ট সংরক্ষণ করুন
                </button>

            </div>

        </form>

    </div>
</div>


{{-- =====================================================
     CUSTOM COLUMN MODAL
===================================================== --}}
<div
    class="de-modal"
    id="customColumnModal"
    aria-hidden="true"
    role="dialog"
    aria-modal="true"
    aria-labelledby="customColumnModalTitle"
>
    <div class="de-modal-dialog de-modal-small">

        <div class="de-modal-header">

            <div>
                <h3 id="customColumnModalTitle">
                    নতুন কাস্টম কলাম
                </h3>
            </div>

            <button
                type="button"
                class="de-modal-close"
                data-close-modal="customColumnModal"
                aria-label="বন্ধ করুন"
            >&times;</button>

        </div>


        <form
            id="customColumnForm"
            action="{{ route('dashboard.students.data-export.custom-columns.store') }}"
            method="POST"
        >
            @csrf

            <div class="de-modal-body">

                <div class="de-form-group">

                    <label for="custom_column_name" class="de-label">
                        কলামের নাম <span class="de-required">*</span>
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="name"
                        id="custom_column_name"
                        maxlength="100"
                        required
                        placeholder="যেমন: হস্তলিপি"
                    >

                    <div class="de-help">
                        হস্তলিপি বা স্পোকেনের তথ্য সংরক্ষণের জন্য এই নামগুলো ব্যবহার করতে পারো।
                    </div>

                </div>


                <div class="de-form-group">

                    <label for="custom_column_type" class="de-label">
                        তথ্যের ধরন
                    </label>

                    <select
                        name="type"
                        id="custom_column_type"
                        class="form-select"
                        required
                    >
                        <option value="text">সাধারণ লেখা</option>
                        <option value="textarea">বিস্তারিত লেখা</option>
                        <option value="number">সংখ্যা</option>
                        <option value="grade">গ্রেড</option>
                    </select>

                </div>


                <div
                    id="customColumnFormError"
                    class="de-form-error"
                    role="alert"
                    aria-live="polite"
                ></div>

            </div>


            <div class="de-modal-footer">

                <button
                    type="button"
                    class="de-btn de-btn-light"
                    data-close-modal="customColumnModal"
                >
                    বাতিল
                </button>

                <button
                    type="submit"
                    class="de-btn de-btn-primary"
                    id="saveCustomColumnButton"
                >
                    <i class="fas fa-plus"></i>
                    কলাম তৈরি করুন
                </button>

            </div>

        </form>

    </div>
</div>

@endsection


@push('scripts')
<script>
(function () {
    'use strict';

    /*
    |--------------------------------------------------------------------------
    | ELEMENTS
    |--------------------------------------------------------------------------
    */

    const reportModal = document.getElementById('reportModal');
    const customColumnModal = document.getElementById('customColumnModal');

    const reportForm = document.getElementById('reportConfigForm');
    const customColumnForm = document.getElementById('customColumnForm');

    const csrfToken =
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const selectedReportData = @json($selectedReportData);
    const hasReportValidationErrors = @json($hasReportValidationErrors);

    let activeModal = null;
    let modalStack = [];


    /*
    |--------------------------------------------------------------------------
    | MOVE MODALS TO BODY
    |--------------------------------------------------------------------------
    */

    [reportModal, customColumnModal].forEach(function (modal) {
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });


    /*
    |--------------------------------------------------------------------------
    | MODAL OPEN
    |--------------------------------------------------------------------------
    */

    function openModal(modal) {
        if (!modal) {
            return;
        }

        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        if (modal.classList.contains('is-open')) {
            if (!modalStack.includes(modal)) {
                modalStack.push(modal);
            }

            activeModal = modal;
            return;
        }

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');

        if (!modalStack.includes(modal)) {
            modalStack.push(modal);
        }

        activeModal = modal;

        document.body.classList.add('de-modal-open');

        const focusable = modal.querySelector(
            'input:not([type="hidden"]):not([disabled]), ' +
            'select:not([disabled]), ' +
            'textarea:not([disabled]), ' +
            'button:not([disabled])'
        );

        if (focusable) {
            setTimeout(function () {
                focusable.focus({
                    preventScroll: true
                });
            }, 80);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | MODAL CLOSE
    |--------------------------------------------------------------------------
    */

    function closeModal(modal) {
        if (!modal) {
            return;
        }

        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');

        modalStack = modalStack.filter(function (item) {
            return item !== modal;
        });

        activeModal = modalStack.length
            ? modalStack[modalStack.length - 1]
            : null;

        if (modalStack.length === 0) {
            document.body.classList.remove('de-modal-open');
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE BUTTONS AND BACKDROP
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', function (event) {
        const closeButton = event.target.closest('[data-close-modal]');

        if (closeButton) {
            event.preventDefault();

            const modalId = closeButton.getAttribute('data-close-modal');

            closeModal(document.getElementById(modalId));

            return;
        }

        const overlay = event.target.closest('.de-modal');

        if (overlay && event.target === overlay) {
            closeModal(overlay);
        }
    });


    /*
    |--------------------------------------------------------------------------
    | ESCAPE KEY
    |--------------------------------------------------------------------------
    */

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && activeModal) {
            closeModal(activeModal);
        }
    });


    /*
    |--------------------------------------------------------------------------
    | FORM ERROR HELPERS
    |--------------------------------------------------------------------------
    */

    function showFormError(elementId, message) {
        const element = document.getElementById(elementId);

        if (!element) {
            return;
        }

        element.textContent = message || '';
        element.style.display = message ? 'block' : 'none';
    }


    /*
    |--------------------------------------------------------------------------
    | REPORT COLUMN SELECTION
    |--------------------------------------------------------------------------
    */

    function getColumnCheckboxes() {
        return Array.from(
            document.querySelectorAll(
                '#reportConfigForm .js-report-column'
            )
        );
    }


    function updateColumnCounter() {
        const checkboxes = getColumnCheckboxes();

        const selectedCount = checkboxes.filter(function (checkbox) {
            return checkbox.checked;
        }).length;

        const counter = document.getElementById('selectedColumnCount');

        if (counter) {
            counter.textContent = selectedCount;
        }

        checkboxes.forEach(function (checkbox) {
            const label = checkbox.closest('.de-column-option');

            const shouldDisable =
                !checkbox.checked && selectedCount >= 10;

            checkbox.disabled = shouldDisable;

            if (label) {
                label.classList.toggle(
                    'is-disabled',
                    shouldDisable
                );
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | COLUMN CHECKBOX CHANGE
    |--------------------------------------------------------------------------
    */

    document.addEventListener('change', function (event) {
        if (
            event.target.matches(
                '#reportConfigForm .js-report-column'
            )
        ) {
            updateColumnCounter();
        }
    });


    /*
    |--------------------------------------------------------------------------
    | RESET REPORT FORM
    |--------------------------------------------------------------------------
    */

    function resetReportForm() {
        if (!reportForm) {
            return;
        }

        reportForm.reset();

        const reportId = document.getElementById('config_report_id');
        const reportName = document.getElementById('report_name');

        if (reportId) {
            reportId.value = '';
        }

        if (reportName) {
            reportName.value = '';
        }

        showFormError('reportFormError', '');

        getColumnCheckboxes().forEach(function (checkbox) {
            checkbox.checked = false;
            checkbox.disabled = false;
        });

        const saveButton = document.getElementById('saveReportButton');

        if (saveButton) {
            saveButton.disabled = false;
            saveButton.innerHTML =
                '<i class="fas fa-save"></i> রিপোর্ট সংরক্ষণ করুন';
        }

        updateColumnCounter();
    }


    /*
    |--------------------------------------------------------------------------
    | OPEN NEW REPORT MODAL
    |--------------------------------------------------------------------------
    */

    document.getElementById('openReportModal')?.addEventListener(
        'click',
        function (event) {
            event.preventDefault();

            resetReportForm();

            openModal(reportModal);
        }
    );


    /*
    |--------------------------------------------------------------------------
    | EDIT SELECTED REPORT
    |--------------------------------------------------------------------------
    */

    const editSelectedReportButton =
        document.getElementById('editSelectedReport');

    if (editSelectedReportButton) {
        editSelectedReportButton.addEventListener(
            'click',
            function (event) {
                event.preventDefault();

                if (!selectedReportData) {
                    return;
                }

                resetReportForm();

                const reportId =
                    document.getElementById('config_report_id');

                const reportName =
                    document.getElementById('report_name');

                if (reportId) {
                    reportId.value = selectedReportData.id || '';
                }

                if (reportName) {
                    reportName.value = selectedReportData.name || '';
                }

                const selected = Array.isArray(
                    selectedReportData.columns
                )
                    ? selectedReportData.columns
                    : [];

                getColumnCheckboxes().forEach(function (checkbox) {
                    checkbox.checked = selected.includes(checkbox.value);
                });

                updateColumnCounter();

                openModal(reportModal);
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FILTER AUTO SUBMIT
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.de-auto-filter').forEach(
        function (select) {
            select.addEventListener('change', function () {
                document.getElementById('deFilterForm')?.submit();
            });
        }
    );


    /*
    |--------------------------------------------------------------------------
    | REPORT FORM VALIDATION
    |--------------------------------------------------------------------------
    */

    reportForm?.addEventListener('submit', function (event) {
        const reportName =
            document.getElementById('report_name')?.value.trim() || '';

        const selectedColumns = getColumnCheckboxes().filter(
            function (checkbox) {
                return checkbox.checked;
            }
        );

        if (!reportName) {
            event.preventDefault();

            showFormError(
                'reportFormError',
                'রিপোর্টের নাম লিখুন।'
            );

            document.getElementById('report_name')?.focus();

            return;
        }

        if (selectedColumns.length === 0) {
            event.preventDefault();

            showFormError(
                'reportFormError',
                'অন্তত একটি কলাম নির্বাচন করুন।'
            );

            return;
        }

        if (selectedColumns.length > 10) {
            event.preventDefault();

            showFormError(
                'reportFormError',
                'সর্বোচ্চ ১০টি কলাম নির্বাচন করা যাবে।'
            );

            return;
        }

        showFormError('reportFormError', '');

        const saveButton =
            document.getElementById('saveReportButton');

        if (saveButton) {
            saveButton.disabled = true;

            saveButton.innerHTML =
                '<i class="fas fa-spinner fa-spin"></i> সংরক্ষণ হচ্ছে...';
        }
    });


    /*
    |--------------------------------------------------------------------------
    | OPEN CUSTOM COLUMN MODAL
    |--------------------------------------------------------------------------
    */

    document.getElementById('openCustomColumnModal')?.addEventListener(
        'click',
        function (event) {
            event.preventDefault();

            showFormError('customColumnFormError', '');

            openModal(customColumnModal);
        }
    );


    /*
    |--------------------------------------------------------------------------
    | CUSTOM COLUMN FORM SUBMISSION
    |--------------------------------------------------------------------------
    */

    customColumnForm?.addEventListener(
        'submit',
        async function (event) {
            event.preventDefault();

            const saveButton =
                document.getElementById('saveCustomColumnButton');

            const formData = new FormData(customColumnForm);

            showFormError('customColumnFormError', '');

            if (saveButton) {
                saveButton.disabled = true;

                saveButton.innerHTML =
                    '<i class="fas fa-spinner fa-spin"></i> তৈরি হচ্ছে...';
            }

            try {
                const response = await fetch(customColumnForm.action, {
                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },

                    body: formData,

                    credentials: 'same-origin'
                });

                const result = await response.json().catch(
                    function () {
                        return {};
                    }
                );

                if (
                    !response.ok ||
                    !result.success ||
                    !result.column
                ) {
                    let message =
                        result.message ||
                        'কাস্টম কলাম তৈরি করা যায়নি।';

                    if (result.errors) {
                        message = Object.values(result.errors)
                            .flat()
                            .join(' ');
                    }

                    throw new Error(message);
                }

                const column = result.column;

                const grid =
                    document.getElementById('customColumnGrid');

                if (!grid) {
                    throw new Error(
                        'কাস্টম কলাম প্রদর্শনের জায়গা পাওয়া যায়নি।'
                    );
                }

                /*
                 * Determine the checkbox key.
                 */

                const columnKey =
                    column.key || ('custom_' + column.id);

                /*
                 * Prevent duplicate checkboxes.
                 */

                const existingCheckbox = Array.from(
                    grid.querySelectorAll('.js-report-column')
                ).find(function (checkbox) {
                    return checkbox.value === columnKey;
                });

                if (existingCheckbox) {
                    closeModal(customColumnModal);
                    return;
                }

                /*
                 * Remove empty-state message.
                 */

                document
                    .getElementById('noCustomColumnsMessage')
                    ?.remove();

                /*
                 * Create the checkbox safely.
                 */

                const label = document.createElement('label');

                label.className = 'de-column-option';

                const checkbox = document.createElement('input');

                checkbox.type = 'checkbox';
                checkbox.className = 'js-report-column';
                checkbox.name = 'columns[]';
                checkbox.value = columnKey;

                const text = document.createElement('span');

                text.appendChild(
                    document.createTextNode(
                        column.name || 'নতুন কলাম'
                    )
                );

                const typeLabel = document.createElement('small');

                typeLabel.className = 'd-block text-muted';
                typeLabel.textContent = column.type || 'text';

                text.appendChild(typeLabel);

                label.appendChild(checkbox);
                label.appendChild(text);

                grid.appendChild(label);

                /*
                 * Select the new column if fewer than 10 are selected.
                 */

                const checkedCount = getColumnCheckboxes().filter(
                    function (item) {
                        return item.checked;
                    }
                ).length;

                if (checkedCount < 10) {
                    checkbox.checked = true;
                } else {
                    showFormError(
                        'reportFormError',
                        'নতুন কলাম তৈরি হয়েছে। তবে ইতিমধ্যে ১০টি কলাম নির্বাচন করা আছে। নতুন কলামটি ব্যবহার করতে আগে একটি কলাম আনচেক করুন।'
                    );
                }

                customColumnForm.reset();

                closeModal(customColumnModal);

                updateColumnCounter();

            } catch (error) {
                showFormError(
                    'customColumnFormError',
                    error.message ||
                    'একটি সমস্যা হয়েছে। আবার চেষ্টা করুন।'
                );

            } finally {
                if (saveButton) {
                    saveButton.disabled = false;

                    saveButton.innerHTML =
                        '<i class="fas fa-plus"></i> কলাম তৈরি করুন';
                }
            }
        }
    );


    /*
    |--------------------------------------------------------------------------
    | INITIALIZE
    |--------------------------------------------------------------------------
    */

    updateColumnCounter();

    /*
     * Reopen report modal if report validation failed.
     */

    if (hasReportValidationErrors) {
        openModal(reportModal);
    }

})();
</script>
@endpush