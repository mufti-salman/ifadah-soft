@extends('layouts.admin')

@section('title', 'ভর্তি তালিকা')

@section('page')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap');

    body, .admission-page * {
        font-family: 'Hind Siliguri', sans-serif;
    }

    .admission-page {
        background: #f0f4ff;
        min-height: 100vh;
        padding: 20px;
    }

    /* ── Top Panel ── */
    .top-panel {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(30,58,138,.08);
        padding: 20px;
        margin-bottom: 16px;
        display: grid;
        grid-template-columns: 320px 1fr 280px;
        gap: 24px;
    }

    @media (max-width: 992px) {
        .top-panel {
            grid-template-columns: 1fr;
        }
    }

    /* ── Left Form ── */
    .tab-nav {
        display: flex;
        gap: 0;
        border-bottom: 2px solid #e5e9f5;
        margin-bottom: 16px;
        align-items: center;
        justify-content: space-between;
    }

    .tab-btn {
        background: none;
        border: none;
        font-size: .88rem;
        font-weight: 600;
        color: #9ca3af;
        padding: 6px 0;
        margin-right: 18px;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        font-family: 'Hind Siliguri', sans-serif;
    }

    .tab-btn.active {
        color: #1e3a8a;
        border-bottom-color: #1e3a8a;
    }

    .tab-icon {
        color: #9ca3af;
        cursor: pointer;
    }

    .left-form .form-label {
        font-size: .78rem;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 4px;
        display: block;
    }

    .left-form .form-control,
    .left-form .form-select {
        border: 1.5px solid #e5e9f5;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: .85rem;
        background: #f8faff;
        font-family: 'Hind Siliguri', sans-serif;
        width: 100%;
        outline: none;
    }

    .left-form .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        padding-right: 30px;
    }

    .left-form .form-control:focus,
    .left-form .form-select:focus {
        border-color: #6b8cde;
        box-shadow: 0 0 0 3px rgba(107,140,222,.1);
        background: #fff;
    }

    .id-search {
        display: flex;
        gap: 8px;
        position: relative;
    }

    .id-search input {
        flex: 1;
    }

    .btn-search {
        background: #1e3a8a;
        border: none;
        border-radius: 8px;
        color: #fff;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
    }

    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e5e9f5;
        border-radius: 8px;
        max-height: 250px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .search-result-item {
        padding: 10px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f0f4ff;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .search-result-item:hover {
        background: #f0f4ff;
    }

    .search-result-item img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        object-fit: cover;
    }

    .search-result-item .info {
        flex: 1;
    }

    .search-result-item strong {
        display: block;
        font-size: .85rem;
    }

    .search-result-item small {
        font-size: .7rem;
        color: #6b7280;
    }

    .radio-group {
        display: flex;
        gap: 16px;
        margin-bottom: 4px;
    }

    .radio-label {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: .82rem;
        color: #374151;
        cursor: pointer;
    }

    .radio-label input {
        accent-color: #1e3a8a;
    }

    .input-with-btn {
        display: flex;
        gap: 6px;
    }

    .input-with-btn .form-select {
        flex: 1;
    }

    .btn-plus {
        background: #1e3a8a;
        border: none;
        border-radius: 8px;
        color: #fff;
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        font-size: 1rem;
    }

    .serial-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .serial-row span {
        font-size: .85rem;
        color: #374151;
        font-weight: 600;
    }

    .btn-serial-opt {
        background: none;
        border: 1.5px solid #e5e9f5;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: .8rem;
        cursor: pointer;
        color: #6b7280;
    }

    .field-row {
        margin-bottom: 12px;
    }

    /* ── Middle Student Info ── */
    .student-info {
        display: flex;
        gap: 18px;
    }

    .student-photo {
        width: 110px;
        height: 110px;
        border-radius: 10px;
        object-fit: cover;
        flex-shrink: 0;
        background: #e5e9f5;
    }

    .student-photo-placeholder {
        width: 110px;
        height: 110px;
        border-radius: 10px;
        background: #e5e9f5;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 2rem;
    }

    .student-meta {
        flex: 1;
    }

    .student-name {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 6px;
    }

    .student-name span {
        color: #1e3a8a;
    }

    .student-detail {
        font-size: .85rem;
        color: #374151;
        margin-bottom: 4px;
    }

    .student-detail strong {
        color: #1a1a2e;
    }

    .badge-inactive {
        background: #d1fae5;
        color: #065f46;
        border-radius: 20px;
        padding: 4px 14px;
        font-size: .8rem;
        font-weight: 600;
        display: inline-block;
        margin-top: 6px;
    }

    .badge-active-s {
        background: #dbeafe;
        color: #1e3a8a;
        border-radius: 20px;
        padding: 4px 14px;
        font-size: .8rem;
        font-weight: 600;
        display: inline-block;
        margin-top: 6px;
    }

    .options-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-top: 14px;
    }

    .option-section-title {
        font-size: .78rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: 8px;
    }

    .radio-opt {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: .82rem;
        color: #374151;
        margin-bottom: 6px;
        cursor: pointer;
    }

    .radio-opt input {
        accent-color: #1e3a8a;
    }

    /* ── Right Panel ── */
    .right-panel {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .video-link {
        background: #f0f4ff;
        border-radius: 10px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .video-link a {
        font-size: .82rem;
        color: #1e3a8a;
        font-weight: 600;
        text-decoration: underline;
    }

    .btn-play {
        background: #e5e9f5;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1e3a8a;
        cursor: pointer;
        flex-shrink: 0;
    }

    .stat-box {
        background: #f8faff;
        border: 1.5px solid #e5e9f5;
        border-radius: 10px;
        padding: 12px 16px;
    }

    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: .83rem;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .stat-row:last-child {
        margin-bottom: 0;
    }

    .stat-val {
        font-weight: 700;
        color: #1a1a2e;
    }

    .action-btns {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: auto;
    }

    .btn-save-main {
        background: #1e3a8a;
        border: none;
        border-radius: 10px;
        color: #fff;
        padding: 11px;
        font-size: .92rem;
        font-weight: 700;
        width: 100%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-family: 'Hind Siliguri', sans-serif;
        transition: background 0.2s;
    }

    .btn-save-main:hover {
        background: #1e40af;
    }

    .btn-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .btn-new {
        background: #2563eb;
        border: none;
        border-radius: 10px;
        color: #fff;
        padding: 10px;
        font-size: .9rem;
        font-weight: 700;
        width: 100%;
        cursor: pointer;
        font-family: 'Hind Siliguri', sans-serif;
        text-align: center;
        display: inline-block;
        text-decoration: none;
    }

    .btn-new:hover {
        background: #1d4ed8;
        color: #fff;
    }

    .btn-close-r {
        background: #ef4444;
        border: none;
        border-radius: 10px;
        color: #fff;
        padding: 10px;
        font-size: .9rem;
        font-weight: 700;
        width: 100%;
        cursor: pointer;
        font-family: 'Hind Siliguri', sans-serif;
    }

    /* ── Filter Bar ── */
    .filter-bar {
        background: #1e8a3a;
        border-radius: 12px 12px 0 0;
        padding: 12px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .filter-bar .filter-label {
        color: #fff;
        font-weight: 700;
        font-size: .9rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .filter-bar .form-select-sm {
        border: none;
        border-radius: 8px;
        padding: 7px 30px 7px 12px;
        font-size: .85rem;
        font-family: 'Hind Siliguri', sans-serif;
        outline: none;
        min-width: 130px;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
    }

    .filter-search {
        flex: 1;
        position: relative;
        min-width: 200px;
    }

    .filter-search input {
        width: 100%;
        border: none;
        border-radius: 8px;
        padding: 8px 14px 8px 36px;
        font-size: .85rem;
        font-family: 'Hind Siliguri', sans-serif;
        outline: none;
    }

    .filter-search .si {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: .85rem;
    }

    .btn-find {
        background: #1a1a2e;
        border: none;
        border-radius: 8px;
        color: #fff;
        padding: 8px 20px;
        font-size: .88rem;
        font-weight: 700;
        cursor: pointer;
        font-family: 'Hind Siliguri', sans-serif;
        white-space: nowrap;
        text-decoration: none;
        display: inline-block;
    }

    .btn-find:hover {
        background: #2d2d44;
        color: #fff;
    }

    /* ── Table ── */
    .admission-table-wrap {
        background: #fff;
        border-radius: 0 0 14px 14px;
        box-shadow: 0 2px 12px rgba(30,58,138,.08);
        overflow-x: auto;
    }

    .adm-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .adm-table thead tr {
        background: #1a1a2e;
    }

    .adm-table thead th {
        color: #d1d5db;
        font-size: .78rem;
        font-weight: 600;
        padding: 11px 12px;
        text-align: center;
        white-space: nowrap;
    }

    .adm-table thead th:nth-child(4) {
        text-align: left;
    }

    .adm-table tbody tr {
        border-bottom: 1px solid #f0f4ff;
        transition: background .12s;
        cursor: pointer;
    }

    .adm-table tbody tr:hover {
        background: #f8faff;
    }

    .adm-table td {
        padding: 11px 12px;
        font-size: .85rem;
        color: #374151;
        text-align: center;
        vertical-align: middle;
    }

    .adm-table td.name-td {
        text-align: left;
        font-weight: 700;
        color: #1a1a2e;
    }

    .td-remove {
        color: #ef4444;
        font-size: 1rem;
        cursor: pointer;
        background: none;
        border: none;
        transition: transform 0.2s;
    }

    .td-remove:hover {
        transform: scale(1.1);
    }

    .class-badge {
        display: inline-block;
        background: #ede9fe;
        color: #6d28d9;
        border-radius: 20px;
        padding: 3px 12px;
        font-size: .78rem;
        font-weight: 600;
    }

    .class-badge.kg {
        background: #fce7f3;
        color: #be185d;
    }

    .class-badge.c3 {
        background: #d1fae5;
        color: #065f46;
    }

    .class-badge.c4 {
        background: #fef3c7;
        color: #92400e;
    }

    .class-badge.c5 {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-active {
        color: #16a34a;
        font-weight: 600;
        font-size: .83rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .status-inactive {
        color: #ef4444;
        font-weight: 600;
        font-size: .83rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .status-transferred {
        color: #f59e0b;
        font-weight: 600;
        font-size: .83rem;
    }

    .status-graduated {
        color: #8b5cf6;
        font-weight: 600;
        font-size: .83rem;
    }

    .status-dropped {
        color: #6b7280;
        font-weight: 600;
        font-size: .83rem;
    }

    /* Context Menu */
    .ctx-menu {
        position: absolute;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(30,58,138,.15);
        min-width: 200px;
        z-index: 999;
        border: 1px solid #eef2fb;
        display: none;
        overflow: hidden;
    }

    .ctx-menu.show {
        display: block;
        animation: fadeIn .12s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-4px)
        }

        to {
            opacity: 1;
            transform: translateY(0)
        }
    }

    .ctx-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 18px;
        font-size: .87rem;
        color: #374151;
        cursor: pointer;
        transition: background .1s;
    }

    .ctx-item:hover {
        background: #f8faff;
    }

    .ctx-item i {
        width: 16px;
        color: #9ca3af;
        font-size: .85rem;
    }

    .pagination-bar {
        padding: 12px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #f0f4ff;
        flex-wrap: wrap;
        gap: 10px;
    }

    .pagination-bar small {
        color: #9ca3af;
        font-size: .82rem;
    }

    .loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #1e3a8a;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .text-muted {
        color: #6b7280;
        font-size: .75rem;
        margin-top: 4px;
        display: block;
    }
</style>

<div class="admission-page">

    {{-- ══ TOP PANEL ══ --}}
    <div class="top-panel">

        {{-- LEFT: Form --}}
        <div class="left-form">

            <div class="tab-nav">
                <div style="display:flex">
                    <button class="tab-btn active" type="button">
                        ভর্তি সেটিংস
                    </button>

                    <button class="tab-btn" type="button">
                        ভর্তি ফি সেটিংস
                    </button>
                </div>

                <i class="fas fa-sliders-h tab-icon"></i>
            </div>

            <form
                id="admissionForm"
                action="{{ route('dashboard.admissions.store') }}"
                method="POST"
            >
                @csrf

                {{-- Student Search --}}
                <div class="field-row">

                    <label class="form-label">
                        শিক্ষার্থী খুঁজুন
                        <span class="text-danger">*</span>
                    </label>

                    <div class="id-search">

                        <input
                            type="text"
                            id="student_search_input"
                            class="form-control"
                            placeholder="শিক্ষার্থীর নাম, আইডি বা মোবাইল লিখুন..."
                            autocomplete="off"
                        >

                        <input
                            type="hidden"
                            name="student_id"
                            id="student_id"
                        >

                        <button
                            type="button"
                            class="btn-search"
                            id="searchStudentBtn"
                        >
                            <i class="fas fa-search"></i>
                        </button>

                    </div>

                </div>

                {{-- এন্ট্রি তারিখ --}}
                <div class="field-row">

                    <label class="form-label">
                        এন্ট্রি তারিখ
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="date"
                        name="entry_date"
                        class="form-control"
                        value="{{ date('Y-m-d') }}"
                        required
                    >

                </div>

                {{-- শিক্ষাবর্ষ --}}
                <div class="field-row">

                    <label class="form-label">
                        শিক্ষাবর্ষ
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-with-btn">

                        <select
                            name="academic_year_id"
                            id="academic_year_id"
                            class="form-select"
                            required
                        >
                            <option value="">নির্বাচন করুন</option>

                            @foreach($academicYears as $year)

                                <option
                                    value="{{ $year->id }}"
                                    {{ $year->is_current ? 'selected' : '' }}
                                >
                                    {{ $year->name_bn }}
                                </option>

                            @endforeach

                        </select>

                        <button
                            type="button"
                            class="btn-plus"
                            title="নতুন শিক্ষাবর্ষ যোগ করুন"
                        >
                            <i class="fas fa-plus"></i>
                        </button>

                    </div>

                </div>

                {{-- ভর্তি ইচ্ছুক ক্লাস --}}
                <div class="field-row">

                    <label class="form-label">
                        ভর্তি ইচ্ছুক ক্লাস
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-with-btn">

                        <select
                            name="class_id"
                            id="class_id"
                            class="form-select"
                            required
                        >
                            <option value="">নির্বাচন করুন</option>

                            @foreach($classes as $class)

                                <option value="{{ $class->id }}">
                                    {{ $class->name_bn }}
                                </option>

                            @endforeach

                        </select>

                        <button
                            type="button"
                            class="btn-plus"
                            title="নতুন ক্লাস যোগ করুন"
                        >
                            <i class="fas fa-plus"></i>
                        </button>

                    </div>

                </div>

                {{-- ভর্তি সিরিয়াল --}}
                <div class="field-row">

                    <label class="form-label">
                        ভর্তি সিরিয়াল
                    </label>

                    <div class="serial-row">

                        <span id="admission_serial">
                            {{ $admissions->count() + 1 }}
                        </span>

                        <button
                            type="button"
                            class="btn-serial-opt"
                            title="সিরিয়াল সেটিংস"
                        >
                            <i class="fas fa-cog"></i>
                        </button>

                    </div>

                </div>

                <input
                    type="hidden"
                    name="admission_fee"
                    id="admission_fee"
                    value="5000"
                >

                <input
                    type="hidden"
                    name="monthly_fee"
                    id="monthly_fee"
                    value="1000"
                >

                <input
                    type="hidden"
                    name="transport_fee"
                    id="transport_fee"
                    value="500"
                >

                <input
                    type="hidden"
                    name="hostel_fee"
                    id="hostel_fee"
                    value="2000"
                >

            </form>

        </div>

        {{-- MIDDLE: Student Info --}}
        <div id="studentInfoSection">

            <div class="student-info mb-3">

                <div
                    id="studentPhoto"
                    class="student-photo-placeholder"
                >
                    <i class="fas fa-user"></i>
                </div>

                <div class="student-meta">

                    <div class="student-name">
                        নাম :
                        <span id="studentName">
                            শিক্ষার্থী নির্বাচন করুন
                        </span>
                    </div>

                    <div class="student-detail">
                        পিতার নাম :
                        <strong id="fatherName">—</strong>
                    </div>

                    <div class="student-detail">
                        মোবাইল :
                        <strong id="studentPhone">—</strong>
                    </div>

                    <div class="student-detail">
                        শিক্ষার্থী আইডি :
                        <strong id="studentID">—</strong>
                    </div>

                    <span
                        id="studentStatusBadge"
                        class="badge-inactive"
                    >
                        নতুন শিক্ষার্থী
                    </span>

                </div>

            </div>

            <div class="options-grid">

                {{-- আর্থিক অবস্থা --}}
                <div>

                    <div class="option-section-title">
                        আর্থিক অবস্থা
                    </div>

                    <label class="radio-opt">
                        <input
                            type="radio"
                            name="financial_status"
                            form="admissionForm"
                            value="solvent"
                            checked
                        >
                        সচ্ছল
                    </label>

                    <label class="radio-opt">
                        <input
                            type="radio"
                            name="financial_status"
                            form="admissionForm"
                            value="insolvent"
                        >
                        অসচ্ছল
                    </label>

                    <label class="radio-opt">
                        <input
                            type="radio"
                            name="financial_status"
                            form="admissionForm"
                            value="orphan"
                        >
                        এতিম
                    </label>

                    <label class="radio-opt">
                        <input
                            type="radio"
                            name="financial_status"
                            form="admissionForm"
                            value="helpless"
                        >
                        অসহায়
                    </label>

                </div>

                {{-- আবাসন অবস্থা --}}
                <div>

                    <div class="option-section-title">
                        আবাসন অবস্থা
                    </div>

                    <label class="radio-opt">
                        <input
                            type="radio"
                            name="residence_status"
                            form="admissionForm"
                            value="resident"
                        >
                        আবাসিক
                    </label>

                    <label class="radio-opt">
                        <input
                            type="radio"
                            name="residence_status"
                            form="admissionForm"
                            value="non-resident"
                            checked
                        >
                        অনাবাসিক
                    </label>

                    <label class="radio-opt">
                        <input
                            type="radio"
                            name="residence_status"
                            form="admissionForm"
                            value="daycare"
                        >
                        ডে-কেয়ার
                    </label>

                    <label class="radio-opt">
                        <input
                            type="radio"
                            name="residence_status"
                            form="admissionForm"
                            value="nightcare"
                        >
                        নাইট কেয়ার
                    </label>

                </div>

            </div>

            <div class="mt-3">

                <div class="option-section-title">
                    ভর্তি ধরণ
                </div>

                <div style="display:flex; gap:18px;">

                    <label class="radio-opt">
                        <input
                            type="radio"
                            name="admission_type"
                            form="admissionForm"
                            value="new"
                            checked
                        >
                        নতুন
                    </label>

                    <label class="radio-opt">
                        <input
                            type="radio"
                            name="admission_type"
                            form="admissionForm"
                            value="old"
                        >
                        পুরাতন
                    </label>

                </div>

            </div>

        </div>

        {{-- RIGHT: Stats + Actions --}}
        <div class="right-panel">

            <div class="video-link">

                <a href="#">
                    ভিডিওর মাধ্যমে কাজ দেখতে এখানে ক্লিক করুন
                </a>

                <button
                    class="btn-play"
                    type="button"
                >
                    <i class="fas fa-play"></i>
                </button>

            </div>

            <div class="stat-box">

                <div class="stat-row">

                    <span>
                        এই বছরের মোট শিক্ষার্থী :
                    </span>

                    <span class="stat-val">
                        {{ $currentYearStudents ?? 0 }}
                    </span>

                </div>

                <div class="stat-row">

                    <span>
                        মোট শিক্ষার্থী :
                    </span>

                    <span class="stat-val">
                        {{ $totalStudents ?? 0 }}
                    </span>

                </div>

            </div>

            <div class="action-btns">

                <button
                    type="submit"
                    form="admissionForm"
                    class="btn-save-main"
                >
                    <i class="fas fa-save"></i>
                    সংরক্ষণ করুন
                </button>

                <div class="btn-row">

                    <a
                        href="{{ route('dashboard.admissions.create') }}"
                        class="btn-new"
                    >
                        নতুন
                    </a>

                    <button
                        type="button"
                        class="btn-close-r"
                        onclick="window.location.href='{{ route('dashboard.admissions.index') }}'"
                    >
                        বন্ধ
                    </button>

                </div>

            </div>

        </div>

    </div>

    {{-- end top-panel --}}

    {{-- ══ FILTER BAR ══ --}}
    <form
        action="{{ route('dashboard.admissions.index') }}"
        method="GET"
    >

        <div class="filter-bar">

            <span class="filter-label">
                <i class="fas fa-filter"></i>
                ফিল্টার
            </span>

            <select
                name="academic_year_id"
                class="form-select-sm"
                onchange="this.form.submit()"
            >

                <option value="">
                    সকল বছর
                </option>

                @foreach($academicYears as $year)

                    <option
                        value="{{ $year->id }}"
                        {{ request('academic_year_id') == $year->id ? 'selected' : '' }}
                    >
                        {{ $year->name_bn }}
                    </option>

                @endforeach

            </select>

            <select
                name="class_id"
                class="form-select-sm"
                onchange="this.form.submit()"
            >

                <option value="">
                    সকল ক্লাস
                </option>

                @foreach($classes as $class)

                    <option
                        value="{{ $class->id }}"
                        {{ request('class_id') == $class->id ? 'selected' : '' }}
                    >
                        {{ $class->name_bn }}
                    </option>

                @endforeach

            </select>

            <div class="filter-search">

                <i class="fas fa-search si"></i>

                <input
                    type="text"
                    name="search"
                    placeholder="নাম, আইডি বা মোবাইল..."
                    value="{{ request('search') }}"
                >

            </div>

            <button
                class="btn-find"
                type="submit"
            >
                খুঁজুন
            </button>

            @if(request()->anyFilled([
                'academic_year_id',
                'class_id',
                'search'
            ]))

                <a
                    href="{{ route('dashboard.admissions.index') }}"
                    class="btn-find"
                    style="background:#6b7280;"
                >
                    রিসেট
                </a>

            @endif

        </div>

    </form>

    {{-- ══ TABLE ══ --}}
    <div class="admission-table-wrap">

        <table class="adm-table">

            <thead>

                <tr>

                    <th>::</th>
                    <th>আইডি</th>
                    <th style="text-align:left">
                        শিক্ষার্থীর নাম
                    </th>
                    <th>পিতার নাম</th>
                    <th>মোবাইল</th>
                    <th>ক্লাস</th>
                    <th>শিক্ষাবর্ষ</th>
                    <th>এন্ট্রি তারিখ</th>
                    <th>অবস্থান</th>

                </tr>

            </thead>

            <tbody>

                @forelse($admissions as $admission)

                    <tr
                        class="ctx-row"
                        data-id="{{ $admission->id }}"
                        data-student-id="{{ $admission->student_id }}"
                    >

                        <td class="text-center">

                            <form
                                action="{{ route('dashboard.admissions.destroy', $admission->id) }}"
                                method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('এই ভর্তি তথ্য ডিলিট করবেন?')"
                            >

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="td-remove"
                                    title="মুছুন"
                                >
                                    ✕
                                </button>

                            </form>

                        </td>

                        <td class="text-center">

                            <strong>
                                {{ $admission->student->user->institution_user_id ?? '' }}
                            </strong>

                        </td>

                        <td class="name-td">

                            <div
                                style="display:flex;align-items:center;gap:8px;"
                            >

                                @if(
                                    $admission->student
                                    && $admission->student->user
                                    && $admission->student->user->photo
                                )

                                    <img
                                        src="{{ asset('storage/'.$admission->student->user->photo) }}"
                                        style="width:32px;height:32px;border-radius:50%;object-fit:cover;"
                                    >

                                @endif

                                <span>
                                    {{
                                        $admission->student->user->name_bn
                                        ?? (
                                            $admission->student->user->name
                                            ?? 'N/A'
                                        )
                                    }}
                                </span>

                            </div>

                        </td>

                        <td class="text-center">
                            {{ $admission->student->user->father_name ?? '—' }}
                        </td>

                        <td class="text-center">
                            {{ $admission->student->user->phone ?? '—' }}
                        </td>

                        <td class="text-center">

                            @php

                                $cls =
                                    $admission->class->name_bn
                                    ?? 'N/A';

                                $clsMap = [
                                    'নার্সারি' => '',
                                    'কেজি' => 'kg',
                                    '১ম শ্রেণী' => '',
                                    '২য় শ্রেণী' => '',
                                    '৩য় শ্রেণী' => 'c3',
                                    '৪র্থ শ্রেণী' => 'c4',
                                    '৫ম শ্রেণী' => 'c5',
                                ];

                                $clsClass =
                                    $clsMap[$cls] ?? '';

                            @endphp

                            <span class="class-badge {{ $clsClass }}">
                                {{ $cls }}
                            </span>

                        </td>

                        <td class="text-center">
                            {{ $admission->academicYear->name_bn ?? 'N/A' }}
                        </td>

                        <td class="text-center">

                            {{
                                $admission->admission_date
                                    ? \Carbon\Carbon::parse(
                                        $admission->admission_date
                                    )->format('d/m/Y')
                                    : '—'
                            }}

                        </td>

                        <td class="text-center">

                            @if($admission->status == 'active')

                                <span class="status-active">
                                    <i
                                        class="fas fa-circle"
                                        style="font-size:8px;"
                                    ></i>
                                    এক্টিভ
                                </span>

                            @elseif($admission->status == 'transferred')

                                <span class="status-transferred">
                                    <i class="fas fa-exchange-alt"></i>
                                    স্থানান্তরিত
                                </span>

                            @elseif($admission->status == 'graduated')

                                <span class="status-graduated">
                                    <i class="fas fa-graduation-cap"></i>
                                    পাস
                                </span>

                            @elseif($admission->status == 'dropped')

                                <span class="status-dropped">
                                    <i class="fas fa-times-circle"></i>
                                    ছাড়পত্র
                                </span>

                            @else

                                <span class="status-inactive">
                                    <i
                                        class="fas fa-circle"
                                        style="font-size:8px;"
                                    ></i>
                                    ইনেক্টিভ
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="10"
                            class="text-center py-5"
                            style="color:#9ca3af;"
                        >

                            <i
                                class="fas fa-user-graduate fa-2x mb-2 d-block"
                            ></i>

                            <h5>
                                কোনো ভর্তি তথ্য নেই
                            </h5>

                            <div class="mt-2">

                                <a
                                    href="{{ route('dashboard.admissions.create') }}"
                                    class="btn btn-primary btn-sm"
                                >
                                    নতুন ভর্তি করুন
                                </a>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

        <div class="pagination-bar">

            <small>
                মোট {{ $admissions->total() }} জনের মধ্যে
                {{ $admissions->firstItem() }} -
                {{ $admissions->lastItem() }}
                দেখানো হচ্ছে
            </small>

            {{ $admissions->withQueryString()->links('pagination::bootstrap-5') }}

        </div>

    </div>

    {{-- Context Menu --}}
    <div class="ctx-menu" id="ctxMenu">

        <div
            class="ctx-item"
            id="ctx_fee_collection"
        >
            <i class="fas fa-money-bill-wave"></i>
            ফি গ্রহণ
        </div>

        <div
            class="ctx-item"
            id="ctx_print_form"
        >
            <i class="fas fa-print"></i>
            এডমিশন ফরম (প্রিন্ট)
        </div>

        <div
            class="ctx-item"
            id="ctx_view_profile"
        >
            <i class="fas fa-eye"></i>
            প্রোফাইল ভিউ
        </div>

        <div
            class="ctx-item"
            id="ctx_change_class"
        >
            <i class="fas fa-exchange-alt"></i>
            শিক্ষার্থীর ক্লাস চেঞ্জ
        </div>

    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput =
        document.getElementById('student_search_input');

    const searchBtn =
        document.getElementById('searchStudentBtn');

    const studentIdInput =
        document.getElementById('student_id');


    /*
    |--------------------------------------------------------------------------
    | Update Selected Student
    |--------------------------------------------------------------------------
    |
    | Search করলেও এই function ব্যবহার হবে।
    | URL থেকে selectedStudent আসলেও এই function ব্যবহার হবে।
    |
    |--------------------------------------------------------------------------
    */

    function setSelectedStudent(student) {

        if (!student) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Hidden Student ID
        |--------------------------------------------------------------------------
        */

        studentIdInput.value = student.id || '';


        /*
        |--------------------------------------------------------------------------
        | Search Input
        |--------------------------------------------------------------------------
        */

        searchInput.value =
            student.name
            || student.student_id
            || student.institution_user_id
            || '';


        /*
        |--------------------------------------------------------------------------
        | Middle Student Information
        |--------------------------------------------------------------------------
        */

        document.getElementById('studentName').innerHTML =
            student.name || '—';

        document.getElementById('fatherName').innerHTML =
            student.father_name || '—';

        document.getElementById('studentPhone').innerHTML =
            student.mobile || '—';

        document.getElementById('studentID').innerHTML =
            student.institution_user_id
            || student.student_id
            || '—';


        /*
        |--------------------------------------------------------------------------
        | Student Photo
        |--------------------------------------------------------------------------
        */

        const studentPhotoDiv =
            document.getElementById('studentPhoto');

        if (student.photo) {

            studentPhotoDiv.innerHTML = `
                <img
                    src="${student.photo}"
                    class="student-photo"
                    style="width:110px;height:110px;border-radius:10px;object-fit:cover;"
                    alt="Student Photo"
                >
            `;

        } else {

            studentPhotoDiv.innerHTML =
                '<i class="fas fa-user"></i>';
        }


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        const statusBadge =
            document.getElementById(
                'studentStatusBadge'
            );

        statusBadge.innerHTML =
            'নতুন শিক্ষার্থী';

        statusBadge.className =
            'badge-inactive';
    }


    /*
    |--------------------------------------------------------------------------
    | AJAX Search
    |--------------------------------------------------------------------------
    */

    function searchStudents(query) {

        query = query.trim();

        if (query.length < 2) {

            alert(
                'কমপক্ষে ২ অক্ষর লিখুন'
            );

            return;
        }

        searchBtn.innerHTML =
            '<div class="loading"></div>';

        fetch(
            '{{ route("dashboard.admissions.search") }}?q='
            + encodeURIComponent(query)
        )

        .then(response => response.json())

        .then(data => {

            searchBtn.innerHTML =
                '<i class="fas fa-search"></i>';

            if (data.length > 0) {

                /*
                |--------------------------------------------------------------------------
                | Existing behavior:
                | First search result automatically selected
                |--------------------------------------------------------------------------
                */

                setSelectedStudent(data[0]);

            } else {

                alert(
                    'কোন শিক্ষার্থী পাওয়া যায়নি'
                );
            }

        })

        .catch(err => {

            searchBtn.innerHTML =
                '<i class="fas fa-search"></i>';

            console.error(err);

            alert(
                'সার্চ করতে সমস্যা হয়েছে'
            );
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Search Button
    |--------------------------------------------------------------------------
    */

    searchBtn.addEventListener(
        'click',
        function () {

            searchStudents(
                searchInput.value
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | URL থেকে Selected Student
    |--------------------------------------------------------------------------
    |
    | Controller থেকে $selectedStudent পাঠানো হলে
    | page load হওয়ার সময় student automatically select হবে।
    |
    |--------------------------------------------------------------------------
    */

    @if(isset($selectedStudent) && $selectedStudent)

        setSelectedStudent({

            id: @json($selectedStudent->id),

            institution_user_id:
                @json(
                    $selectedStudent->user->institution_user_id
                    ?? ''
                ),

            student_id:
                @json(
                    $selectedStudent->student_id
                    ?? ''
                ),

            name:
                @json(
                    $selectedStudent->user->name_bn
                    ?? $selectedStudent->user->name
                    ?? ''
                ),

            father_name:
                @json(
                    $selectedStudent->user->father_name
                    ?? ''
                ),

            mobile:
                @json(
                    $selectedStudent->user->phone
                    ?? ''
                ),

            class_name:
                @json(
                    optional($selectedStudent->class)->name_bn
                    ?? ''
                ),

            photo:
                @json(
                    $selectedStudent->user->photo
                    ? asset(
                        'storage/' .
                        $selectedStudent->user->photo
                    )
                    : asset(
                        'images/default-user.png'
                    )
                )
        });

    @endif


    /*
    |--------------------------------------------------------------------------
    | Context Menu
    |--------------------------------------------------------------------------
    */

    const ctxMenu =
        document.getElementById('ctxMenu');

    let currentRowId = null;


    document
        .querySelectorAll('.ctx-row')
        .forEach(function (row) {

            row.addEventListener(
                'contextmenu',
                function (e) {

                    e.preventDefault();

                    currentRowId =
                        this.dataset.id;

                    ctxMenu.style.left =
                        e.pageX + 'px';

                    ctxMenu.style.top =
                        e.pageY + 'px';

                    ctxMenu.classList.add(
                        'show'
                    );
                }
            );

        });


    document.addEventListener(
        'click',
        function () {

            ctxMenu.classList.remove(
                'show'
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Fee Collection
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('ctx_fee_collection')
        ?.addEventListener(
            'click',
            function () {

                if (currentRowId) {

                    window.location.href =
                        '/dashboard/fee-collections/create?admission_id='
                        + currentRowId;
                }

            }
        );


    /*
    |--------------------------------------------------------------------------
    | Print Admission Form
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('ctx_print_form')
        ?.addEventListener(
            'click',
            function () {

                if (currentRowId) {

                    window.open(
                        '/dashboard/admissions/'
                        + currentRowId
                        + '/print',
                        '_blank'
                    );
                }

            }
        );


    /*
    |--------------------------------------------------------------------------
    | View Profile
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('ctx_view_profile')
        ?.addEventListener(
            'click',
            function () {

                if (currentRowId) {

                    window.location.href =
                        '/dashboard/admissions/'
                        + currentRowId;
                }

            }
        );


    /*
    |--------------------------------------------------------------------------
    | Change Class
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('ctx_change_class')
        ?.addEventListener(
            'click',
            function () {

                if (currentRowId) {

                    window.location.href =
                        '/dashboard/admissions/'
                        + currentRowId
                        + '/change-class';
                }

            }
        );


    /*
    |--------------------------------------------------------------------------
    | Admission Form Submit Debug
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('admissionForm')
        .addEventListener(
            'submit',
            function () {

                console.log(
                    'student_id:',
                    document.getElementById(
                        'student_id'
                    ).value
                );

                console.log(
                    'academic_year_id:',
                    document.getElementById(
                        'academic_year_id'
                    ).value
                );

            }
        );

});
</script>

@endsection
