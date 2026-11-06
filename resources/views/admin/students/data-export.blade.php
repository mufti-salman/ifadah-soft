@extends('layouts.admin')

@section('title', 'ডেটা এক্সপোর্ট')

@section('page')
@php
    use Illuminate\Support\Facades\Route;

    $reportMeta = $reportMeta ?? null;
    $reportRows = $reportRows ?? [];
    $selectedReport = $selectedReport ?? null;

    $selectedColumns = old(
        'columns',
        $selectedReport && is_array($selectedReport->columns)
            ? $selectedReport->columns
            : []
    );

    $selectedColumns = is_array($selectedColumns) ? $selectedColumns : [];

    $selectedAcademicYear  = request('academic_year_id');
    $selectedSection       = request('section_id');
    $selectedAdmissionType = request('admission_type');
    $selectedResidence     = request('residence_status');
    $selectedStatus        = request('status');

    $hasReportValidationErrors = $errors->any() && (
        old('report_name') !== null ||
        old('columns') !== null ||
        old('report_id') !== null
    );

    $routeSaveValue = Route::has('dashboard.students.data-export.student-value')
        ? route('dashboard.students.data-export.student-value')
        : '';

    $routeDestroyReport = Route::has('dashboard.students.data-export.destroy')
        ? route('dashboard.students.data-export.destroy', ['__ID__'])
        : '';

    $routeClearAll = Route::has('dashboard.students.data-export.clear-all')
        ? route('dashboard.students.data-export.clear-all')
        : '';
@endphp

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    .de-page {
        --de-primary: #1a3375;
        --de-primary-soft: #edf2ff;
        --de-border: #e5eaf2;
        --de-muted: #64748b;
        --de-text: #172033;
        color: var(--de-text);
    }

    .de-page *, .de-modal *, .de-modal { box-sizing: border-box; }

    .de-page .de-header {
        display: flex; align-items: center; justify-content: space-between;
        gap: 16px; flex-wrap: wrap; margin-bottom: 22px;
    }

    .de-page .de-title { margin: 0 0 6px; font-size: 25px; font-weight: 800; color: var(--de-text); }
    .de-page .de-subtitle { margin: 0; color: var(--de-muted); font-size: 13px; line-height: 1.7; }

    .de-page .de-card {
        margin-bottom: 22px; overflow: hidden; background: var(--card, #fff);
        border: 1px solid var(--de-border); border-radius: 14px;
        box-shadow: 0 5px 22px rgba(15, 23, 42, .035);
    }

    .de-page .de-card-header {
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px; flex-wrap: wrap; padding: 17px 20px; border-bottom: 1px solid var(--de-border);
    }

    .de-page .de-card-title { margin: 0; font-size: 16px; font-weight: 800; color: var(--de-text); }
    .de-page .de-card-body { padding: 20px; }

    .de-page .de-label, .de-modal .de-label {
        display: block; margin-bottom: 7px; font-size: 13px; font-weight: 700; color: var(--de-text);
    }

    .de-page .de-required, .de-modal .de-required { color: #dc2626; }

    .de-page .form-control, .de-page .form-select,
    .de-modal .form-control, .de-modal .form-select {
        display: block; width: 100%; min-height: 43px; padding: 10px 12px;
        border: 1px solid #dbe2ed; border-radius: 9px; background-color: var(--card, #fff);
        color: var(--text, #172033); font-size: 13px; outline: none; box-shadow: none;
    }

    .de-page .form-control:focus, .de-page .form-select:focus,
    .de-modal .form-control:focus, .de-modal .form-select:focus {
        border-color: var(--de-primary); box-shadow: 0 0 0 3px rgba(26, 51, 117, .09);
    }

    .de-page .de-btn, .de-modal .de-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 7px;
        min-height: 40px; padding: 9px 14px; border: 1px solid transparent; border-radius: 9px;
        font-size: 13px; font-weight: 700; line-height: 1.4; text-decoration: none;
        white-space: nowrap; cursor: pointer; transition: background .2s ease, border-color .2s ease;
    }

    .de-page .de-btn-primary, .de-modal .de-btn-primary { border-color: #000; background: #000; color: #fff; }
    .de-page .de-btn-primary:hover, .de-modal .de-btn-primary:hover { background: #222; color: #fff; }
    .de-page .de-btn-light, .de-modal .de-btn-light { border-color: #dbe2ed; background: #fff; color: #334155; }
    .de-page .de-btn-light:hover, .de-modal .de-btn-light:hover { background: #f8fafc; }
    .de-page .de-btn-green { background: #16a34a; border-color: #16a34a; color: #fff; }
    .de-page .de-btn-green:hover { background: #15803d; }
    .de-page .de-btn-red { background: #dc2626; border-color: #dc2626; color: #fff; }
    .de-page .de-btn-red:hover { background: #b91c1c; }
    .de-page .de-btn:disabled, .de-modal .de-btn:disabled { opacity: .65; cursor: not-allowed; }

    .de-page .de-filter-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 17px; }
    .de-page .de-filter-note { margin-top: 14px; color: var(--de-muted); font-size: 12px; line-height: 1.7; }

    .de-page .de-alert {
        margin-bottom: 18px; padding: 12px 15px; border: 1px solid transparent;
        border-radius: 10px; font-size: 13px; line-height: 1.7;
    }
    .de-page .de-alert-success { border-color: #a7f3d0; background: #ecfdf5; color: #065f46; }
    .de-page .de-alert-danger { border-color: #fecdd3; background: #fff1f2; color: #9f1239; }

    .de-page .de-help, .de-modal .de-help { margin-top: 7px; color: var(--de-muted); font-size: 12px; line-height: 1.7; }

    /* placeholder */
    .de-page .de-template-placeholder {
        min-height: 230px; display: flex; align-items: center; justify-content: center;
        flex-direction: column; gap: 12px; padding: 35px 20px; text-align: center;
    }
    .de-page .de-template-placeholder-icon {
        display: flex; align-items: center; justify-content: center; width: 64px; height: 64px;
        border: 1px solid #dbe5ff; border-radius: 18px; background: var(--de-primary-soft);
        color: var(--de-primary); font-size: 25px;
    }
    .de-page .de-template-placeholder h3 { margin: 0; font-size: 17px; font-weight: 800; }
    .de-page .de-template-placeholder p { max-width: 440px; margin: 0; color: var(--de-muted); font-size: 13px; line-height: 1.8; }
    .de-page .de-report-edit-button { margin-top: 8px; }

    /* preview actions + table */
    .de-page .de-actions { display: flex; gap: 8px; flex-wrap: wrap; }

    .de-page .de-selection-bar {
        display: flex; align-items: center; justify-content: space-between;
        gap: 10px; flex-wrap: wrap; margin-bottom: 12px; font-size: 13px;
    }
    .de-page .de-selection-bar .de-ok { color: #16a34a; font-size: 12px; font-weight: 600; }

    .de-page .de-table-wrap { width: 100%; overflow-x: auto; border: 1px solid var(--de-border); border-radius: 10px; }

    .de-page .de-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .de-page .de-table th {
        padding: 11px 12px; background: #f1f5f9; border-bottom: 1px solid var(--de-border);
        text-align: left; font-weight: 800; white-space: nowrap; color: #243247;
    }
    .de-page .de-table td {
        padding: 9px 12px; border-bottom: 1px solid #eef2f7; vertical-align: middle;
    }
    .de-page .de-table tbody tr:hover { background: #f8fafc; }
    .de-page .de-table tbody tr.is-unselected { opacity: .45; }
    .de-page .de-table .de-chk { width: 42px; text-align: center; }
    .de-page .de-table input[type="checkbox"] { width: 16px; height: 16px; accent-color: #1a3375; cursor: pointer; }
    .de-page .de-table img.de-photo { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; }

    .de-page .de-cell-input {
        width: 100%; min-width: 110px; padding: 6px 9px; border: 1px solid #dbe2ed;
        border-radius: 7px; font-size: 13px; background: #fff; color: #172033; outline: none;
        resize: none;
    }
    .de-page .de-cell-input:focus { border-color: var(--de-primary); }
    .de-page .de-cell-input.is-saving { border-color: #f59e0b; }
    .de-page .de-cell-input.is-saved { border-color: #16a34a; background: #f0fdf4; }
    .de-page .de-cell-input.is-error { border-color: #dc2626; background: #fef2f2; }

    .de-page .de-pager {
        display: flex; align-items: center; justify-content: center; gap: 12px; margin-top: 14px; font-size: 13px;
    }

    /* modal */
    .de-modal {
        position: fixed !important; inset: 0 !important; z-index: 999999 !important;
        display: none; align-items: center; justify-content: center;
        width: 100%; height: 100vh; height: 100dvh; padding: 12px;
        background: rgba(15, 23, 42, .62); overflow-x: hidden; overflow-y: auto;
        overscroll-behavior: contain;
    }
    .de-modal.is-open { display: flex !important; }

    .de-modal-dialog {
        position: relative; display: flex; flex-direction: column; flex: 0 1 auto;
        width: 100%; max-width: 850px; height: auto;
        max-height: calc(100vh - 24px); max-height: calc(100dvh - 24px);
        min-height: 0; margin: auto; overflow: hidden;
        border: 1px solid rgba(255, 255, 255, .15); border-radius: 16px;
        background: #fff; color: #172033; box-shadow: 0 25px 80px rgba(0, 0, 0, .28);
        animation: deModalEnter .18s ease-out;
    }
    .de-modal-dialog.de-modal-small { max-width: 500px; }

    .de-modal-header {
        flex: 0 0 auto; display: flex; align-items: flex-start; justify-content: space-between;
        gap: 15px; padding: 16px 22px; border-bottom: 1px solid #e5eaf2; background: #fff;
    }
    .de-modal-header h3 { margin: 0; font-size: 18px; font-weight: 800; line-height: 1.5; color: #172033; }
    .de-modal-header .de-modal-subtitle { margin-top: 5px; color: #64748b; font-size: 12px; line-height: 1.7; }

    .de-modal-close {
        flex: 0 0 auto; display: inline-flex; align-items: center; justify-content: center;
        width: 36px; height: 36px; padding: 0; border: 0; border-radius: 9px;
        background: #f1f5f9; color: #334155; font-size: 24px; line-height: 1; cursor: pointer;
    }
    .de-modal-close:hover { background: #e2e8f0; }

    #reportConfigForm, #customColumnForm {
        display: flex; flex-direction: column; flex: 1 1 auto; min-height: 0; overflow: hidden;
    }

    .de-modal-body {
        flex: 1 1 auto; min-height: 0; padding: 18px 22px; overflow-x: hidden; overflow-y: auto;
        overscroll-behavior-y: contain; scrollbar-gutter: stable;
    }

    .de-modal-footer {
        flex: 0 0 auto; display: flex; align-items: center; justify-content: flex-end;
        flex-wrap: wrap; gap: 9px; padding: 13px 22px; border-top: 1px solid #e5eaf2; background: #fff;
    }

    .de-modal .de-form-group { margin-bottom: 18px; }
    .de-modal .de-form-group:last-child { margin-bottom: 0; }

    .de-modal .de-column-section { margin-top: 16px; overflow: hidden; border: 1px solid #e5eaf2; border-radius: 11px; }
    .de-modal .de-column-section-header {
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
        gap: 10px; padding: 12px 15px; border-bottom: 1px solid #e5eaf2; background: #f8fafc;
    }
    .de-modal .de-column-section-header strong { font-size: 13px; font-weight: 800; color: #172033; }
    .de-modal .de-column-count { color: #1a3375; font-size: 12px; font-weight: 800; }

    .de-modal .de-column-grid {
        display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0; width: 100%;
        max-height: min(34vh, 300px); max-height: min(34dvh, 300px); min-height: 0;
        overflow-x: hidden; overflow-y: auto; padding: 8px 12px; overscroll-behavior: contain;
    }

    .de-modal .de-column-option {
        display: flex; align-items: flex-start; gap: 9px; min-width: 0; padding: 10px 6px;
        border-radius: 6px; color: #334155; font-size: 12px; line-height: 1.6;
        overflow-wrap: anywhere; cursor: pointer;
    }
    .de-modal .de-column-option:hover { background: #f8fafc; }
    .de-modal .de-column-option input[type="checkbox"] {
        flex: 0 0 auto; width: 16px; height: 16px; margin: 3px 0 0; accent-color: #1a3375; cursor: pointer;
    }
    .de-modal .de-column-option span { min-width: 0; }
    .de-modal .de-column-option.is-disabled { opacity: .5; cursor: not-allowed; }
    .de-modal .de-column-option.is-disabled input { cursor: not-allowed; }

    .de-modal .de-empty-columns { grid-column: 1 / -1; padding: 20px 12px; color: #64748b; text-align: center; font-size: 13px; }

    .de-modal .de-custom-column-item { display: flex; align-items: center; gap: 6px; min-width: 0; border-radius: 7px; }
    .de-modal .de-custom-column-item .de-column-option { flex: 1 1 auto; min-width: 0; margin: 0; }
    .de-modal .de-delete-custom-column {
        flex: 0 0 30px; display: inline-flex; align-items: center; justify-content: center;
        width: 30px; height: 30px; padding: 0; border: 1px solid #fecaca; border-radius: 7px;
        background: #fff1f2; color: #dc2626; font-size: 12px; cursor: pointer;
    }
    .de-modal .de-delete-custom-column:hover { background: #dc2626; color: #fff; }
    .de-modal .de-delete-custom-column:disabled { opacity: .55; cursor: not-allowed; }

    .de-modal .de-form-error {
        display: none; margin-top: 15px; padding: 11px 13px; border: 1px solid #fecdd3;
        border-radius: 9px; background: #fff1f2; color: #9f1239; font-size: 12px; line-height: 1.7;
    }

    /* generated reports list (inside modal) */
    .de-modal .de-gen-list { list-style: none; margin: 0; padding: 6px 12px; max-height: 220px; overflow-y: auto; }
    .de-modal .de-gen-item {
        display: flex; align-items: center; justify-content: space-between; gap: 10px;
        padding: 10px 4px; border-bottom: 1px solid #eef2f7; font-size: 13px;
    }
    .de-modal .de-gen-item:last-child { border-bottom: 0; }
    .de-modal .de-gen-name { font-weight: 700; color: #172033; overflow-wrap: anywhere; }
    .de-modal .de-gen-actions { display: flex; gap: 6px; flex: 0 0 auto; }
    .de-modal .de-mini-btn {
        padding: 5px 11px; border: 1px solid #dbe2ed; border-radius: 7px; background: #fff;
        font-size: 12px; font-weight: 700; cursor: pointer; color: #334155;
    }
    .de-modal .de-mini-btn:hover { background: #f1f5f9; }
    .de-modal .de-mini-btn.danger { border-color: #fecaca; color: #dc2626; background: #fff1f2; }
    .de-modal .de-mini-btn.danger:hover { background: #dc2626; color: #fff; }

    /* paper modal */
    .de-modal .de-paper-text { margin: 0 0 6px; font-size: 14px; font-weight: 700; color: #172033; }
    .de-modal .de-paper-choices { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 14px; }
    .de-modal .de-paper-btn {
        display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 8px;
        padding: 18px 10px; border: 2px solid #dbe2ed; border-radius: 12px; background: #fff;
        font-size: 14px; font-weight: 800; color: #172033; cursor: pointer;
    }
    .de-modal .de-paper-btn:hover { border-color: #1a3375; background: #edf2ff; }
    .de-modal .de-paper-btn.is-recommended { border-color: #16a34a; background: #f0fdf4; }
    .de-modal .de-paper-icon { display: block; border: 2px solid #475569; border-radius: 3px; background: #fff; }
    .de-modal .de-paper-icon.portrait { width: 28px; height: 38px; }
    .de-modal .de-paper-icon.landscape { width: 38px; height: 28px; }

    body.de-modal-open { overflow: hidden !important; }

    @keyframes deModalEnter {
        from { opacity: 0; transform: translateY(10px) scale(.99); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    @media (max-width: 991.98px) {
        .de-page .de-filter-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .de-modal .de-column-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 575.98px) {
        .de-page .de-header { align-items: stretch; }
        .de-page .de-header > .de-btn { width: 100%; }
        .de-page .de-title { font-size: 21px; }
        .de-page .de-card-body { padding: 15px; }
        .de-page .de-filter-grid { grid-template-columns: 1fr; }
        .de-modal { padding: 8px; }
        .de-modal-dialog { max-height: calc(100vh - 16px); max-height: calc(100dvh - 16px); border-radius: 12px; }
        .de-modal-header, .de-modal-body, .de-modal-footer { padding-left: 15px; padding-right: 15px; }
        .de-modal .de-column-grid { grid-template-columns: 1fr; max-height: 30vh; max-height: 30dvh; }
        .de-modal-footer .de-btn { flex: 1 1 auto; }
    }
</style>


<div class="de-page">

    {{-- PAGE HEADER --}}
    <div class="de-header">
        <div>
            <h1 class="de-title">
                <i class="fas fa-file-export me-2" style="color:#1a3375"></i>
                ডেটা এক্সপোর্ট
            </h1>

            <p class="de-subtitle">
                রিপোর্ট নির্বাচন করলেই ডেটা স্বয়ংক্রিয়ভাবে দেখাবে। এরপর প্রিন্ট, PDF বা Excel করতে পারবেন।
            </p>
        </div>

        <button type="button" class="de-btn de-btn-primary" id="openReportModal">
            <i class="fas fa-plus"></i>
            রিপোর্ট তৈরি করুন
        </button>
    </div>


    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="de-alert de-alert-success">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="de-alert de-alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="de-alert de-alert-danger">
            <strong><i class="fas fa-exclamation-triangle me-1"></i> তথ্য যাচাই করুন:</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- FILTER PANEL --}}
    <div class="de-card">

        <div class="de-card-header">
            <h2 class="de-card-title">
                <i class="fas fa-filter me-2" style="color:#1a3375"></i>
                ফিল্টার
            </h2>

            <a href="{{ route('dashboard.students.data-export') }}" class="de-btn de-btn-light">
                <i class="fas fa-redo-alt"></i>
                ফিল্টার রিসেট
            </a>
        </div>

        <div class="de-card-body">

            <form method="GET" action="{{ route('dashboard.students.data-export') }}" id="deFilterForm">

                <div class="de-filter-grid">

                    {{-- Report --}}
                    <div>
                        <label for="filter_report_id" class="de-label">
                            রিপোর্টসমূহ <span class="de-required">*</span>
                        </label>

                        <select name="report_id" id="filter_report_id" class="form-select de-auto-filter">
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
                            <button type="button" class="de-btn de-btn-light de-report-edit-button" id="editSelectedReport">
                                <i class="fas fa-edit"></i>
                                নির্বাচিত রিপোর্ট সম্পাদনা
                            </button>
                        @endif
                    </div>

                    {{-- Academic year --}}
                    <div>
                        <label for="academic_year_id" class="de-label">শিক্ষাবর্ষ</label>

                        <select name="academic_year_id" id="academic_year_id" class="form-select de-auto-filter">
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
                        <label for="section_id" class="de-label">সাব ক্লাস</label>

                        <select name="section_id" id="section_id" class="form-select de-auto-filter">
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
                        <label for="admission_type" class="de-label">নতুন/পুরাতন</label>

                        <select name="admission_type" id="admission_type" class="form-select de-auto-filter">
                            <option value="">সব শিক্ষার্থী</option>
                            <option value="new" {{ $selectedAdmissionType === 'new' ? 'selected' : '' }}>নতুন ভর্তি</option>
                            <option value="old" {{ $selectedAdmissionType === 'old' ? 'selected' : '' }}>পুরাতন ভর্তি</option>
                        </select>
                    </div>

                    {{-- Residence --}}
                    <div>
                        <label for="residence_status" class="de-label">আবাসন অবস্থা</label>

                        <select name="residence_status" id="residence_status" class="form-select de-auto-filter">
                            <option value="">সকল আবাসন অবস্থা</option>
                            <option value="resident" {{ $selectedResidence === 'resident' ? 'selected' : '' }}>আবাসিক</option>
                            <option value="non-resident" {{ $selectedResidence === 'non-resident' ? 'selected' : '' }}>অনাবাসিক</option>
                            <option value="daycare" {{ $selectedResidence === 'daycare' ? 'selected' : '' }}>ডে-কেয়ার</option>
                            <option value="nightcare" {{ $selectedResidence === 'nightcare' ? 'selected' : '' }}>নাইট-কেয়ার</option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="de-label">স্ট্যাটাস</label>

                        <select name="status" id="status" class="form-select de-auto-filter">
                            <option value="">সব স্ট্যাটাস</option>
                            <option value="active" {{ in_array((string) $selectedStatus, ['active', '1'], true) ? 'selected' : '' }}>সক্রিয়</option>
                            <option value="inactive" {{ in_array((string) $selectedStatus, ['inactive', '0'], true) ? 'selected' : '' }}>নিষ্ক্রিয়</option>
                        </select>
                    </div>

                </div>

                <div class="de-filter-note">
                    <i class="fas fa-info-circle me-1"></i>
                    যেকোনো ফিল্টার বা রিপোর্ট পরিবর্তন করলে ডেটা স্বয়ংক্রিয়ভাবে আপডেট হবে।
                </div>

            </form>
        </div>
    </div>


    {{-- PREVIEW --}}
    <div class="de-card">

        <div class="de-card-header">
            <h2 class="de-card-title">
                <i class="fas fa-table me-2" style="color:#1a3375"></i>
                রিপোর্ট প্রিভিউ
                @if($reportMeta)
                    <span style="font-weight:600;color:#64748b"> — {{ $reportMeta['name'] }}</span>
                @endif
            </h2>

            @if($reportMeta)
                <div class="de-actions">
                    <button type="button" class="de-btn de-btn-primary" id="btnPrint">
                        <i class="fas fa-print"></i> প্রিন্ট
                    </button>

                    <button type="button" class="de-btn de-btn-red" id="btnPdf">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>

                    <button type="button" class="de-btn de-btn-green" id="btnExcel">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                </div>
            @endif
        </div>

        @if(!$reportMeta)

            <div class="de-template-placeholder">
                <div class="de-template-placeholder-icon">
                    <i class="fas fa-file-alt"></i>
                </div>

                <h3>ডেটা দেখতে একটি রিপোর্ট নির্বাচন করুন</h3>

                <p>
                    উপরের ফিল্টার থেকে একটি রিপোর্ট নির্বাচন করলেই ডেটা দেখাবে।
                    নতুন রিপোর্ট তৈরি করতে “রিপোর্ট তৈরি করুন” বাটনে ক্লিক করুন।
                </p>
            </div>

        @else

            <div class="de-card-body">

                <div class="de-selection-bar">
                    <strong>Selected: <span id="selCount">0</span> / <span id="totalCount">0</span></strong>

                    <span class="de-ok">
                        ✓ প্রিন্ট/এক্সেল-এ শুধুমাত্র নির্বাচিত সারিগুলোই অন্তর্ভুক্ত থাকবে।
                    </span>
                </div>

                <div class="de-table-wrap">
                    <table class="de-table" id="deTable">
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="de-pager">
                    <button type="button" class="de-btn de-btn-light" id="pagePrev">
                        <i class="fas fa-chevron-left"></i>
                    </button>

                    <span id="pageInfo">পৃষ্ঠা ১ এর ১</span>

                    <button type="button" class="de-btn de-btn-light" id="pageNext">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>

            </div>

        @endif
    </div>

</div>


{{-- =====================================================
     REPORT CONFIGURATION MODAL
===================================================== --}}
<div class="de-modal" id="reportModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="reportModalTitle">
    <div class="de-modal-dialog">

        <div class="de-modal-header">
            <div>
                <h3 id="reportModalTitle">ডেটা এক্সপোর্ট কনফিগারেশন</h3>
                <div class="de-modal-subtitle">রিপোর্টের নাম দিন এবং সর্বোচ্চ ১০টি কলাম নির্বাচন করুন।</div>
            </div>

            <button type="button" class="de-modal-close" data-close-modal="reportModal" aria-label="বন্ধ করুন">&times;</button>
        </div>

        <form method="POST" action="{{ route('dashboard.students.data-export.store') }}" id="reportConfigForm">
            @csrf

            <input type="hidden" name="report_id" id="config_report_id" value="{{ old('report_id', $selectedReport->id ?? '') }}">

            <div class="de-modal-body">

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
                        placeholder="রিপোর্টের নাম লিখুন..."
                    >
                </div>

                {{-- Standard columns --}}
                <div class="de-column-section">
                    <div class="de-column-section-header">
                        <strong><i class="fas fa-list-check me-2"></i> কলাম নির্বাচন করুন</strong>

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
                        <strong><i class="fas fa-puzzle-piece me-2"></i> কাস্টম কলাম</strong>

                        <button type="button" class="de-btn de-btn-light" id="openCustomColumnModal">
                            <i class="fas fa-plus"></i> নতুন কলাম তৈরি করুন
                        </button>
                    </div>

                    <div class="de-column-grid" id="customColumnGrid">
                        @forelse($customColumns as $column)
                            <div class="de-custom-column-item" id="custom-column-{{ $column->id }}">
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
                                        <small class="d-block text-muted">{{ $column->type }}</small>
                                    </span>
                                </label>

                                <button
                                    type="button"
                                    class="de-delete-custom-column"
                                    data-id="{{ $column->id }}"
                                    data-name="{{ $column->name }}"
                                    title="কাস্টম কলাম মুছুন"
                                    aria-label="{{ $column->name }} মুছুন"
                                >
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        @empty
                            <div class="de-empty-columns" id="noCustomColumnsMessage">
                                এখনো কোনো কাস্টম কলাম তৈরি করা হয়নি।
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="de-help">
                    <i class="fas fa-info-circle me-1"></i>
                    একই রিপোর্টে সর্বোচ্চ ১০টি আলাদা কলাম নির্বাচন করা যাবে।
                </div>

                {{-- Generated reports --}}
                <div class="de-column-section">
                    <div class="de-column-section-header">
                        <strong>
                            <i class="fas fa-folder-open me-2"></i>
                            Generated Reports
                            <span class="de-column-count" id="genCount">{{ count($reportsData) }}</span>
                        </strong>

                        <div style="display:flex;gap:6px">
                            <button type="button" class="de-mini-btn" id="genRefresh">Refresh</button>
                            <button type="button" class="de-mini-btn danger" id="genClearAll">Clear All</button>
                        </div>
                    </div>

                    <ul class="de-gen-list" id="genList">
                        @forelse($reportsData as $item)
                            <li class="de-gen-item" data-id="{{ $item['id'] }}">
                                <span class="de-gen-name">{{ $item['name'] }}</span>

                                <span class="de-gen-actions">
                                    <button type="button" class="de-mini-btn js-gen-edit" data-id="{{ $item['id'] }}">এডিট</button>
                                    <button type="button" class="de-mini-btn danger js-gen-delete" data-id="{{ $item['id'] }}">ডিলিট</button>
                                </span>
                            </li>
                        @empty
                            <li class="de-empty-columns" id="genEmpty">এখনো কোনো রিপোর্ট তৈরি করা হয়নি।</li>
                        @endforelse
                    </ul>
                </div>

                <div id="reportFormError" class="de-form-error" role="alert" aria-live="polite"></div>

            </div>

            <div class="de-modal-footer">
                <button type="button" class="de-btn de-btn-light" data-close-modal="reportModal">বাতিল</button>

                <button type="submit" class="de-btn de-btn-primary" id="saveReportButton">
                    <i class="fas fa-save"></i> সংরক্ষণ করুন
                </button>
            </div>
        </form>

    </div>
</div>


{{-- =====================================================
     CUSTOM COLUMN MODAL
===================================================== --}}
<div class="de-modal" id="customColumnModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="customColumnModalTitle">
    <div class="de-modal-dialog de-modal-small">

        <div class="de-modal-header">
            <div><h3 id="customColumnModalTitle">নতুন কাস্টম কলাম</h3></div>

            <button type="button" class="de-modal-close" data-close-modal="customColumnModal" aria-label="বন্ধ করুন">&times;</button>
        </div>

        <form id="customColumnForm" action="{{ route('dashboard.students.data-export.custom-columns.store') }}" method="POST">
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
                </div>

                <div class="de-form-group">
                    <label for="custom_column_type" class="de-label">তথ্যের ধরন</label>

                    <select name="type" id="custom_column_type" class="form-select" required>
                        <option value="text">সাধারণ লেখা</option>
                        <option value="textarea">বিস্তারিত লেখা</option>
                        <option value="number">সংখ্যা</option>
                        <option value="grade">গ্রেড</option>
                    </select>
                </div>

                <div id="customColumnFormError" class="de-form-error" role="alert" aria-live="polite"></div>

            </div>

            <div class="de-modal-footer">
                <button type="button" class="de-btn de-btn-light" data-close-modal="customColumnModal">বাতিল</button>

                <button type="submit" class="de-btn de-btn-primary" id="saveCustomColumnButton">
                    <i class="fas fa-plus"></i> কলাম তৈরি করুন
                </button>
            </div>
        </form>

    </div>
</div>


{{-- =====================================================
     PAPER ORIENTATION MODAL (Print / PDF)
===================================================== --}}
<div class="de-modal" id="paperModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="paperModalTitle">
    <div class="de-modal-dialog de-modal-small">

        <div class="de-modal-header">
            <div><h3 id="paperModalTitle">কাগজের অবস্থান নির্বাচন করুন</h3></div>

            <button type="button" class="de-modal-close" data-close-modal="paperModal" aria-label="বন্ধ করুন">&times;</button>
        </div>

        <div class="de-modal-body">
            <p class="de-paper-text" id="paperHint">—</p>

            <div class="de-paper-choices">
                <button type="button" class="de-paper-btn" data-orient="portrait" id="paperPortrait">
                    <span class="de-paper-icon portrait"></span>
                    Portrait
                </button>

                <button type="button" class="de-paper-btn" data-orient="landscape" id="paperLandscape">
                    <span class="de-paper-icon landscape"></span>
                    Landscape
                </button>
            </div>
        </div>

        <div class="de-modal-footer">
            <button type="button" class="de-btn de-btn-light" data-close-modal="paperModal">বাতিল করুন</button>
        </div>

    </div>
</div>

@endsection


@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
(function () {
    'use strict';

    /* ------------------------------------------------------------------
     * DATA FROM SERVER
     * ---------------------------------------------------------------- */

    const REPORT  = @json($reportMeta);
    const ROWS    = @json($reportRows);
    const REPORTS = @json($reportsData);
    const INSTITUTION = @json($institutionName);
    const HAS_ERRORS  = @json($hasReportValidationErrors);

    const URLS = {
        base: @json(route('dashboard.students.data-export')),
        saveValue: @json($routeSaveValue),
        destroyReport: @json($routeDestroyReport),
        clearAll: @json($routeClearAll),
        destroyColumn: @json(route('dashboard.students.data-export.custom-columns.destroy', ['column' => '__COLUMN_ID__'])),
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const PER_PAGE = 25;

    /* ------------------------------------------------------------------
     * HELPERS
     * ---------------------------------------------------------------- */

    const esc = function (s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    };

    const bn = function (s) {
        return String(s).replace(/\d/g, function (d) { return '০১২৩৪৫৬৭৮৯'[d]; });
    };

    const byId = function (id) { return document.getElementById(id); };

    /* ------------------------------------------------------------------
     * MODALS
     * ---------------------------------------------------------------- */

    const reportModal = byId('reportModal');
    const customColumnModal = byId('customColumnModal');
    const paperModal = byId('paperModal');

    let modalStack = [];

    [reportModal, customColumnModal, paperModal].forEach(function (modal) {
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });

    function openModal(modal) {
        if (!modal) return;

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');

        if (!modalStack.includes(modal)) modalStack.push(modal);

        document.body.classList.add('de-modal-open');
    }

    function closeModal(modal) {
        if (!modal) return;

        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');

        modalStack = modalStack.filter(function (m) { return m !== modal; });

        if (!modalStack.length) document.body.classList.remove('de-modal-open');
    }

    document.addEventListener('click', function (event) {
        const closeBtn = event.target.closest('[data-close-modal]');

        if (closeBtn) {
            event.preventDefault();
            closeModal(byId(closeBtn.getAttribute('data-close-modal')));
            return;
        }

        const overlay = event.target.closest('.de-modal');

        if (overlay && event.target === overlay) closeModal(overlay);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modalStack.length) {
            closeModal(modalStack[modalStack.length - 1]);
        }
    });

    function showFormError(id, message) {
        const el = byId(id);
        if (!el) return;

        el.textContent = message || '';
        el.style.display = message ? 'block' : 'none';
    }

    /* ------------------------------------------------------------------
     * FILTER AUTO SUBMIT  (report select => data loads automatically)
     * ---------------------------------------------------------------- */

    document.querySelectorAll('.de-auto-filter').forEach(function (select) {
        select.addEventListener('change', function () {
            byId('deFilterForm')?.submit();
        });
    });

    /* ------------------------------------------------------------------
     * REPORT CONFIG: COLUMN SELECTION
     * ---------------------------------------------------------------- */

    const reportForm = byId('reportConfigForm');
    const customColumnForm = byId('customColumnForm');

    function getColumnCheckboxes() {
        return Array.from(document.querySelectorAll('#reportConfigForm .js-report-column'));
    }

    function updateColumnCounter() {
        const boxes = getColumnCheckboxes();
        const count = boxes.filter(function (b) { return b.checked; }).length;

        const counter = byId('selectedColumnCount');
        if (counter) counter.textContent = count;

        boxes.forEach(function (box) {
            const disable = !box.checked && count >= 10;
            box.disabled = disable;

            box.closest('.de-column-option')?.classList.toggle('is-disabled', disable);
        });
    }

    document.addEventListener('change', function (event) {
        if (event.target.matches('#reportConfigForm .js-report-column')) {
            updateColumnCounter();
        }
    });

    function resetReportForm() {
        if (!reportForm) return;

        reportForm.reset();

        byId('config_report_id').value = '';
        byId('report_name').value = '';

        showFormError('reportFormError', '');

        getColumnCheckboxes().forEach(function (box) {
            box.checked = false;
            box.disabled = false;
        });

        const saveBtn = byId('saveReportButton');
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save"></i> সংরক্ষণ করুন';
        }

        updateColumnCounter();
    }

    function fillReportForm(report) {
        resetReportForm();

        byId('config_report_id').value = report.id || '';
        byId('report_name').value = report.name || '';

        const selected = Array.isArray(report.columns) ? report.columns : [];

        getColumnCheckboxes().forEach(function (box) {
            box.checked = selected.includes(box.value);
        });

        updateColumnCounter();
    }

    byId('openReportModal')?.addEventListener('click', function (event) {
        event.preventDefault();
        resetReportForm();
        openModal(reportModal);
    });

    byId('editSelectedReport')?.addEventListener('click', function (event) {
        event.preventDefault();

        if (!REPORT) return;

        const saved = REPORTS.find(function (r) { return r.id === REPORT.id; });

        fillReportForm(saved || {
            id: REPORT.id,
            name: REPORT.name,
            columns: REPORT.columns.map(function (c) { return c.key; })
        });

        openModal(reportModal);
    });

    reportForm?.addEventListener('submit', function (event) {
        const name = byId('report_name')?.value.trim() || '';
        const selected = getColumnCheckboxes().filter(function (b) { return b.checked; });

        if (!name) {
            event.preventDefault();
            showFormError('reportFormError', 'রিপোর্টের নাম লিখুন।');
            byId('report_name')?.focus();
            return;
        }

        if (selected.length === 0) {
            event.preventDefault();
            showFormError('reportFormError', 'অন্তত একটি কলাম নির্বাচন করুন।');
            return;
        }

        if (selected.length > 10) {
            event.preventDefault();
            showFormError('reportFormError', 'সর্বোচ্চ ১০টি কলাম নির্বাচন করা যাবে।');
            return;
        }

        showFormError('reportFormError', '');

        const saveBtn = byId('saveReportButton');
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> সংরক্ষণ হচ্ছে...';
        }
    });

    /* ------------------------------------------------------------------
     * CUSTOM COLUMN: CREATE
     * ---------------------------------------------------------------- */

    byId('openCustomColumnModal')?.addEventListener('click', function (event) {
        event.preventDefault();
        showFormError('customColumnFormError', '');
        openModal(customColumnModal);
    });

    function buildCustomItem(col) {
        const key = col.key || ('custom_' + col.id);

        const wrap = document.createElement('div');
        wrap.className = 'de-custom-column-item';
        wrap.id = 'custom-column-' + col.id;

        wrap.innerHTML =
            '<label class="de-column-option">' +
                '<input type="checkbox" class="js-report-column" name="columns[]" value="' + esc(key) + '">' +
                '<span>' + esc(col.name || 'নতুন কলাম') +
                    '<small class="d-block text-muted">' + esc(col.type || 'text') + '</small>' +
                '</span>' +
            '</label>' +
            '<button type="button" class="de-delete-custom-column" data-id="' + esc(col.id) +
                '" data-name="' + esc(col.name || '') + '" title="কাস্টম কলাম মুছুন">' +
                '<i class="fas fa-trash-alt"></i>' +
            '</button>';

        return wrap;
    }

    customColumnForm?.addEventListener('submit', async function (event) {
        event.preventDefault();

        const saveBtn = byId('saveCustomColumnButton');

        showFormError('customColumnFormError', '');

        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> তৈরি হচ্ছে...';
        }

        try {
            const response = await fetch(customColumnForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(customColumnForm),
                credentials: 'same-origin'
            });

            const result = await response.json().catch(function () { return {}; });

            if (!response.ok || !result.success || !result.column) {
                let message = result.message || 'কাস্টম কলাম তৈরি করা যায়নি।';

                if (result.errors) {
                    message = Object.values(result.errors).flat().join(' ');
                }

                throw new Error(message);
            }

            const grid = byId('customColumnGrid');
            if (!grid) throw new Error('কাস্টম কলাম প্রদর্শনের জায়গা পাওয়া যায়নি।');

            byId('noCustomColumnsMessage')?.remove();

            const item = buildCustomItem(result.column);
            grid.appendChild(item);

            const checkedCount = getColumnCheckboxes().filter(function (b) { return b.checked; }).length;

            if (checkedCount < 10) {
                item.querySelector('.js-report-column').checked = true;
            } else {
                showFormError(
                    'reportFormError',
                    'নতুন কলাম তৈরি হয়েছে। তবে ১০টি কলাম আগেই নির্বাচন করা আছে। ব্যবহার করতে আগে একটি কলাম আনচেক করুন।'
                );
            }

            customColumnForm.reset();
            closeModal(customColumnModal);
            updateColumnCounter();

        } catch (error) {
            showFormError('customColumnFormError', error.message || 'একটি সমস্যা হয়েছে। আবার চেষ্টা করুন।');

        } finally {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fas fa-plus"></i> কলাম তৈরি করুন';
            }
        }
    });

    /* ------------------------------------------------------------------
     * CUSTOM COLUMN: DELETE
     * ---------------------------------------------------------------- */

    document.addEventListener('click', async function (event) {
        const button = event.target.closest('.de-delete-custom-column');
        if (!button) return;

        event.preventDefault();

        const columnId = button.dataset.id;
        const columnName = button.dataset.name || 'এই কলাম';

        if (!columnId) return;

        if (!window.confirm('"' + columnName + '" কাস্টম কলামটি মুছে ফেলতে চান?')) return;

        const original = button.innerHTML;

        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const url = URLS.destroyColumn.replace('__COLUMN_ID__', encodeURIComponent(columnId));

            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            const result = await response.json().catch(function () { return {}; });

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'কাস্টম কলামটি মুছে ফেলা যায়নি।');
            }

            byId('custom-column-' + columnId)?.remove();

            const grid = byId('customColumnGrid');

            if (grid && !grid.querySelector('.de-custom-column-item')) {
                const empty = document.createElement('div');
                empty.className = 'de-empty-columns';
                empty.id = 'noCustomColumnsMessage';
                empty.textContent = 'এখনো কোনো কাস্টম কলাম তৈরি করা হয়নি।';
                grid.appendChild(empty);
            }

            updateColumnCounter();

        } catch (error) {
            window.alert(error.message || 'একটি সমস্যা হয়েছে। আবার চেষ্টা করুন।');

        } finally {
            if (button.isConnected) {
                button.disabled = false;
                button.innerHTML = original;
            }
        }
    });

    /* ------------------------------------------------------------------
     * GENERATED REPORTS LIST (edit / delete / clear all / refresh)
     * ---------------------------------------------------------------- */

    function refreshGenCount() {
        const count = document.querySelectorAll('#genList .de-gen-item').length;

        const counter = byId('genCount');
        if (counter) counter.textContent = count;

        if (count === 0 && !byId('genEmpty')) {
            const li = document.createElement('li');
            li.className = 'de-empty-columns';
            li.id = 'genEmpty';
            li.textContent = 'এখনো কোনো রিপোর্ট তৈরি করা হয়নি।';
            byId('genList').appendChild(li);
        }
    }

    document.addEventListener('click', async function (event) {

        const editBtn = event.target.closest('.js-gen-edit');

        if (editBtn) {
            const id = parseInt(editBtn.dataset.id, 10);
            const report = REPORTS.find(function (r) { return r.id === id; });

            if (report) fillReportForm(report);

            return;
        }

        const delBtn = event.target.closest('.js-gen-delete');

        if (delBtn) {
            const id = delBtn.dataset.id;

            if (!URLS.destroyReport) {
                window.alert('রিপোর্ট ডিলিটের route পাওয়া যায়নি। routes ফাইলে destroy route যোগ করুন।');
                return;
            }

            if (!window.confirm('এই রিপোর্টটি মুছে ফেলতে চান?')) return;

            delBtn.disabled = true;

            try {
                const response = await fetch(URLS.destroyReport.replace('__ID__', encodeURIComponent(id)), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                const result = await response.json().catch(function () { return {}; });

                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'রিপোর্ট মুছে ফেলা যায়নি।');
                }

                document.querySelector('#genList .de-gen-item[data-id="' + id + '"]')?.remove();
                document.querySelector('#filter_report_id option[value="' + id + '"]')?.remove();

                refreshGenCount();

                if (REPORT && String(REPORT.id) === String(id)) {
                    window.location.href = URLS.base;
                }

            } catch (error) {
                delBtn.disabled = false;
                window.alert(error.message || 'একটি সমস্যা হয়েছে।');
            }

            return;
        }

        if (event.target.closest('#genClearAll')) {

            if (!URLS.clearAll) {
                window.alert('Clear All এর route পাওয়া যায়নি। routes ফাইলে clear-all route যোগ করুন।');
                return;
            }

            if (!window.confirm('আপনার সব রিপোর্ট মুছে ফেলতে চান?')) return;

            try {
                const response = await fetch(URLS.clearAll, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                const result = await response.json().catch(function () { return {}; });

                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'রিপোর্ট মুছে ফেলা যায়নি।');
                }

                window.location.href = URLS.base;

            } catch (error) {
                window.alert(error.message || 'একটি সমস্যা হয়েছে।');
            }

            return;
        }

        if (event.target.closest('#genRefresh')) {
            window.location.reload();
        }
    });

    /* ------------------------------------------------------------------
     * PREVIEW TABLE
     * ---------------------------------------------------------------- */

    if (REPORT) {

        const table = byId('deTable');
        const thead = table.querySelector('thead');
        const tbody = table.querySelector('tbody');

        const rowMap = new Map();
        ROWS.forEach(function (r) { rowMap.set(r.id, r); });

        const selected = new Set(ROWS.map(function (r) { return r.id; }));

        let page = 1;

        const totalPages = function () {
            return Math.max(1, Math.ceil(ROWS.length / PER_PAGE));
        };

        function cellHtml(row, col) {
            const value = row.cells[col.key] || '';

            if (col.kind === 'custom') {
                const common =
                    ' class="de-cell-input" data-row="' + row.id + '" data-col="' + esc(col.key) + '"';

                if (col.input === 'textarea') {
                    return '<td><textarea rows="1"' + common + '>' + esc(value) + '</textarea></td>';
                }

                const type = col.input === 'number' ? 'number' : 'text';

                return '<td><input type="' + type + '"' + common + ' value="' + esc(value) + '"></td>';
            }

            if (col.key === 'photo') {
                return '<td>' + (value
                    ? '<img class="de-photo" src="' + esc(value) + '" alt="" onerror="this.style.visibility=\'hidden\'">'
                    : '—') + '</td>';
            }

            return '<td>' + (value ? esc(value) : '—') + '</td>';
        }

        function render() {
            if (page > totalPages()) page = totalPages();

            // header
            thead.innerHTML =
                '<tr><th class="de-chk"><input type="checkbox" id="checkAll"></th>' +
                REPORT.columns.map(function (c) { return '<th>' + esc(c.label) + '</th>'; }).join('') +
                '</tr>';

            // body
            const slice = ROWS.slice((page - 1) * PER_PAGE, page * PER_PAGE);

            if (!ROWS.length) {
                tbody.innerHTML =
                    '<tr><td colspan="' + (REPORT.columns.length + 1) +
                    '" style="text-align:center;padding:30px;color:#64748b">নির্বাচিত ফিল্টারে কোনো শিক্ষার্থী পাওয়া যায়নি।</td></tr>';
            } else {
                tbody.innerHTML = slice.map(function (row) {
                    const checked = selected.has(row.id);

                    return '<tr data-id="' + row.id + '" class="' + (checked ? '' : 'is-unselected') + '">' +
                        '<td class="de-chk"><input type="checkbox" class="row-check" data-id="' + row.id + '"' +
                        (checked ? ' checked' : '') + '></td>' +
                        REPORT.columns.map(function (c) { return cellHtml(row, c); }).join('') +
                        '</tr>';
                }).join('');
            }

            byId('pageInfo').textContent = 'পৃষ্ঠা ' + bn(page) + ' এর ' + bn(totalPages());
            byId('pagePrev').disabled = page <= 1;
            byId('pageNext').disabled = page >= totalPages();

            updateSelectionUi();
        }

        function updateSelectionUi() {
            byId('selCount').textContent = selected.size;
            byId('totalCount').textContent = ROWS.length;

            const all = byId('checkAll');

            if (all) {
                all.checked = ROWS.length > 0 && selected.size === ROWS.length;
                all.indeterminate = selected.size > 0 && selected.size < ROWS.length;
            }
        }

        table.addEventListener('change', function (event) {

            if (event.target.id === 'checkAll') {
                if (event.target.checked) {
                    ROWS.forEach(function (r) { selected.add(r.id); });
                } else {
                    selected.clear();
                }

                tbody.querySelectorAll('.row-check').forEach(function (box) {
                    box.checked = event.target.checked;
                    box.closest('tr').classList.toggle('is-unselected', !event.target.checked);
                });

                updateSelectionUi();
                return;
            }

            if (event.target.classList.contains('row-check')) {
                const id = parseInt(event.target.dataset.id, 10);

                if (event.target.checked) selected.add(id); else selected.delete(id);

                event.target.closest('tr').classList.toggle('is-unselected', !event.target.checked);

                updateSelectionUi();
                return;
            }

            if (event.target.classList.contains('de-cell-input')) {
                scheduleSave(event.target, true);
            }
        });

        table.addEventListener('input', function (event) {
            if (event.target.classList.contains('de-cell-input')) {
                scheduleSave(event.target, false);
            }
        });

        byId('pagePrev').addEventListener('click', function () {
            if (page > 1) { page--; render(); }
        });

        byId('pageNext').addEventListener('click', function () {
            if (page < totalPages()) { page++; render(); }
        });

        /* ---------- inline save of custom values ---------- */

        const saveTimers = {};
        let warnedNoRoute = false;

        function scheduleSave(input, immediate) {
            const rowId = parseInt(input.dataset.row, 10);
            const colKey = input.dataset.col;
            const row = rowMap.get(rowId);

            if (!row) return;

            row.cells[colKey] = input.value;

            const timerKey = rowId + '_' + colKey;

            clearTimeout(saveTimers[timerKey]);

            const run = function () { doSave(input, row, colKey); };

            if (immediate) run(); else saveTimers[timerKey] = setTimeout(run, 700);
        }

        async function doSave(input, row, colKey) {
            if (!URLS.saveValue) {
                input.classList.add('is-error');

                if (!warnedNoRoute) {
                    warnedNoRoute = true;
                    window.alert('সংরক্ষণের route পাওয়া যায়নি। routes ফাইলে student-value route যোগ করুন।');
                }

                return;
            }

            const column = REPORT.columns.find(function (c) { return c.key === colKey; });

            input.classList.remove('is-saved', 'is-error');
            input.classList.add('is-saving');

            try {
                const response = await fetch(URLS.saveValue, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        student_id: row.id,
                        custom_column_id: column.customId,
                        academic_year_id: row.academic_year_id,
                        value: input.value
                    })
                });

                const result = await response.json().catch(function () { return {}; });

                if (!response.ok || !result.success) throw new Error('save failed');

                input.classList.remove('is-saving');
                input.classList.add('is-saved');

                setTimeout(function () { input.classList.remove('is-saved'); }, 1500);

            } catch (error) {
                input.classList.remove('is-saving');
                input.classList.add('is-error');
            }
        }

        render();

        /* ------------------------------------------------------------------
         * PRINT / PDF / EXCEL
         * ---------------------------------------------------------------- */

        const REPORT_CSS =
            'body{font-family:"Noto Sans Bengali","SolaimanLipi",Arial,sans-serif;color:#111;}' +
            '.rp-head{text-align:center;margin-bottom:12px;padding-bottom:8px;border-bottom:2px solid #222;}' +
            '.rp-head h2{margin:0 0 4px;font-size:20px;}' +
            '.rp-head h3{margin:0 0 4px;font-size:15px;font-weight:700;}' +
            '.rp-head p{margin:0;font-size:11px;color:#333;}' +
            'table.rp-table{width:100%;border-collapse:collapse;font-size:11px;}' +
            'table.rp-table th,table.rp-table td{border:1px solid #333;padding:6px 5px;text-align:center;vertical-align:middle;overflow-wrap:anywhere;}' +
            'table.rp-table th{background:#eee;font-weight:700;}' +
            'table.rp-table thead{display:table-header-group;}' +
            'table.rp-table tr{page-break-inside:avoid;break-inside:avoid;}' +
            'table.rp-table img{width:32px;height:32px;border-radius:50%;object-fit:cover;}';

        function selectedRows() {
            return ROWS.filter(function (r) { return selected.has(r.id); });
        }

        function reportHtml(rows) {
            const today = bn(new Date().toLocaleDateString('en-GB').replace(/\//g, '-'));

            const head = REPORT.columns.map(function (c) { return '<th>' + esc(c.label) + '</th>'; }).join('');

            const body = rows.map(function (row) {
                return '<tr>' + REPORT.columns.map(function (c) {
                    const v = row.cells[c.key] || '';

                    if (c.key === 'photo') {
                        return '<td>' + (v ? '<img src="' + esc(v) + '" alt="">' : '') + '</td>';
                    }

                    return '<td>' + esc(v) + '</td>';
                }).join('') + '</tr>';
            }).join('');

            return '<div class="rp-head"><h2>' + esc(INSTITUTION) + '</h2><h3>' + esc(REPORT.name) +
                '</h3><p>তারিখ: ' + esc(today) + ' | মোট: ' + bn(rows.length) + ' জন</p></div>' +
                '<table class="rp-table"><thead><tr>' + head + '</tr></thead><tbody>' + body + '</tbody></table>';
        }

        function safeName() {
            return String(REPORT.name || 'report').replace(/[\\/:*?"<>|]/g, '_');
        }

        /* ---------- orientation popup ---------- */

        let pendingAction = null;

        function askOrientation(action) {
            const rows = selectedRows();

            if (!rows.length) {
                window.alert('অন্তত একটি সারি নির্বাচন করুন।');
                return;
            }

            pendingAction = action;

            const count = REPORT.columns.length;
            const recommended = count <= 6 ? 'portrait' : 'landscape';
            const recLabel = recommended === 'portrait' ? 'Portrait' : 'Landscape';

            byId('paperHint').textContent = bn(count) + 'টি কলাম আছে — ' + recLabel + ' ভালো হবে।';

            byId('paperPortrait').classList.toggle('is-recommended', recommended === 'portrait');
            byId('paperLandscape').classList.toggle('is-recommended', recommended === 'landscape');

            openModal(paperModal);
        }

        byId('btnPrint').addEventListener('click', function () { askOrientation('print'); });
        byId('btnPdf').addEventListener('click', function () { askOrientation('pdf'); });

        paperModal.addEventListener('click', function (event) {
            const btn = event.target.closest('[data-orient]');
            if (!btn) return;

            const orient = btn.getAttribute('data-orient');

            closeModal(paperModal);

            if (pendingAction === 'print') printReport(orient);
            if (pendingAction === 'pdf') downloadPdf(orient);

            pendingAction = null;
        });

        /* ---------- print ---------- */

        async function printReport(orient) {
            const rows = selectedRows();

            const w = window.open('', '_blank', 'width=1100,height=800');

            if (!w) {
                window.alert('Popup blocked হয়েছে। Browser থেকে popup allow করুন।');
                return;
            }

            const doc =
                '<!DOCTYPE html><html lang="bn"><head><meta charset="UTF-8"><title>&nbsp;</title>' +
                '<style>' +
                '@page{size:A4 ' + orient + ';margin:0;}' +
                '*{box-sizing:border-box;-webkit-print-color-adjust:exact;print-color-adjust:exact;}' +
                'html,body{margin:0;padding:0;background:#fff;}' +
                'table.wrap{width:100%;border-collapse:collapse;}' +
                'table.wrap>thead>tr>td,table.wrap>tfoot>tr>td,table.wrap>tbody>tr>td{border:0;padding:0;}' +
                '.sp{height:10mm;}.pad{padding:0 10mm;}' +
                REPORT_CSS +
                '</style></head><body>' +
                '<table class="wrap"><thead><tr><td><div class="sp"></div></td></tr></thead>' +
                '<tfoot><tr><td><div class="sp"></div></td></tr></tfoot>' +
                '<tbody><tr><td><div class="pad">' + reportHtml(rows) + '</div></td></tr></tbody></table>' +
                '</body></html>';

            w.document.open();
            w.document.write(doc);
            w.document.close();

            const images = Array.from(w.document.images);

            await Promise.all(images.map(function (img) {
                if (img.complete) return Promise.resolve();

                return new Promise(function (resolve) {
                    img.onload = resolve;
                    img.onerror = resolve;
                });
            }));

            setTimeout(function () {
                w.focus();
                w.print();
            }, 500);
        }

        /* ---------- pdf (real download) ---------- */

        function downloadPdf(orient) {
            const rows = selectedRows();

            if (typeof html2pdf === 'undefined') {
                printReport(orient);
                return;
            }

            const holder = document.createElement('div');

            holder.style.cssText =
                'position:absolute;left:0;top:0;z-index:-1;background:#fff;padding:10px;width:' +
                (orient === 'landscape' ? '1050px' : '740px') + ';';

            holder.innerHTML = '<style>' + REPORT_CSS + '</style>' + reportHtml(rows);

            document.body.appendChild(holder);

            const btn = byId('btnPdf');
            const original = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> তৈরি হচ্ছে...';

            html2pdf()
                .set({
                    margin: [8, 8, 8, 8],
                    filename: safeName() + '.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, useCORS: true, scrollX: 0, scrollY: 0 },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: orient },
                    pagebreak: { mode: ['css', 'legacy'], avoid: 'tr' }
                })
                .from(holder)
                .save()
                .catch(function () {
                    window.alert('PDF তৈরি করা যায়নি। প্রিন্ট অপশন ব্যবহার করে PDF হিসেবে সেভ করুন।');
                })
                .finally(function () {
                    holder.remove();
                    btn.disabled = false;
                    btn.innerHTML = original;
                });
        }

        /* ---------- excel ---------- */

        byId('btnExcel').addEventListener('click', function () {
            const rows = selectedRows();

            if (!rows.length) {
                window.alert('অন্তত একটি সারি নির্বাচন করুন।');
                return;
            }

            const header = REPORT.columns.map(function (c) { return c.label; });

            const data = rows.map(function (row) {
                return REPORT.columns.map(function (c) { return row.cells[c.key] || ''; });
            });

            const aoa = [header].concat(data);

            if (typeof XLSX !== 'undefined') {
                const ws = XLSX.utils.aoa_to_sheet(aoa);

                ws['!cols'] = header.map(function () { return { wch: 20 }; });

                const wb = XLSX.utils.book_new();

                XLSX.utils.book_append_sheet(wb, ws, 'Report');
                XLSX.writeFile(wb, safeName() + '.xlsx');

                return;
            }

            // Fallback: CSV (UTF-8 with BOM so Bengali opens correctly in Excel)
            const csv = aoa.map(function (line) {
                return line.map(function (cell) {
                    return '"' + String(cell).replace(/"/g, '""') + '"';
                }).join(',');
            }).join('\r\n');

            const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
            const a = document.createElement('a');

            a.href = URL.createObjectURL(blob);
            a.download = safeName() + '.csv';

            document.body.appendChild(a);
            a.click();
            a.remove();
        });
    }

    /* ------------------------------------------------------------------
     * INIT
     * ---------------------------------------------------------------- */

    updateColumnCounter();

    if (HAS_ERRORS) openModal(reportModal);

})();
</script>
@endpush