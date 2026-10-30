@extends('layouts.admin')

@section('page-title', 'ব্যবহারকারী ভিত্তিক প্রতিবেদন')

@section('page')
<div class="ur-page">

    {{-- Header --}}
    <div class="ur-page-header">
        <div>
            <div class="ur-eyebrow">REPORT CENTER</div>
            <h1>ব্যবহারকারী ভিত্তিক প্রতিবেদন</h1>
            <p>প্রয়োজনীয় রিপোর্ট নির্বাচন করে তথ্য ফিল্টার করুন এবং প্রিভিউ তৈরি করুন।</p>
        </div>

        <div class="ur-header-icon">▤</div>
    </div>

    {{-- Report Form --}}
    <div class="ur-card">
        <div class="ur-card-heading">
            <div class="ur-heading-icon">▤</div>
            <div>
                <h2>রিপোর্ট তৈরির ফর্ম</h2>
                <p>রিপোর্টের ধরন অনুযায়ী প্রয়োজনীয় তথ্য নির্বাচন করুন।</p>
            </div>
        </div>

        <form
            id="userReportForm"
            action="{{ route('dashboard.user-reports.preview') }}"
            method="POST"
        >
            @csrf

            {{-- Report Type --}}
            <div class="ur-field ur-field-full">
                <label for="report_type">
                    রিপোর্টসমূহ <span class="ur-required">*</span>
                </label>

                <select
                    id="report_type"
                    name="report_type"
                    class="ur-input"
                    required
                >
                    <option value="">নির্বাচন করুন</option>
                    <option value="1" @selected(old('report_type') == '1')>
                        আইডি সিরিয়াল ইউজার ডাটা তালিকা
                    </option>
                    <option value="2" @selected(old('report_type') == '2')>
                        ইউজার পরিসংখ্যান
                    </option>
                    <option value="3" @selected(old('report_type') == '3')>
                        শিক্ষার্থীর ভর্তির ফরম
                    </option>
                    <option value="4" @selected(old('report_type') == '4')>
                        ভর্তি বিহীন শিক্ষার্থীর তালিকা
                    </option>
                </select>

                @error('report_type')
                    <span class="ur-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Report 1: User Data List --}}
            <div class="ur-report-fields" data-report="1" hidden>

                <div class="ur-field">
                    <label for="user_type">
                        ব্যবহারকারীর ধরন
                        <span class="ur-required">*</span>
                    </label>

                    <select
                        id="user_type"
                        name="user_type"
                        class="ur-input"
                        data-required="true"
                    >
                        <option value="">নির্বাচন করুন</option>

                        @foreach($roles as $role)
                            <option
                                value="{{ $role->slug }}"
                                @selected(old('user_type') == $role->slug)
                            >
                                {{ $role->name_bn ?? $role->name ?? $role->slug }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="ur-field">
                    <label for="gender_1">
                        লিঙ্গ <span class="ur-required">*</span>
                    </label>

                    <select
                        id="gender_1"
                        name="gender"
                        class="ur-input"
                        data-required="true"
                    >
                        <option value="all" @selected(old('gender', 'all') == 'all')>উভয়</option>
                        <option value="male" @selected(old('gender') == 'male')>পুরুষ</option>
                        <option value="female" @selected(old('gender') == 'female')>মহিলা</option>
                    </select>
                </div>

                <div class="ur-field">
                    <label for="status_1">ব্যবহারকারীর অবস্থা</label>

                    <select id="status_1" name="status" class="ur-input">
                        <option value="all" @selected(old('status', 'all') == 'all')>উভয়</option>
                        <option value="active" @selected(old('status') == 'active')>সক্রিয়</option>
                        <option value="inactive" @selected(old('status') == 'inactive')>ইনঅ্যাক্টিভ</option>
                    </select>
                </div>

                <div class="ur-field">
                    <label for="user_id_from">ইউজার আইডি হতে</label>

                    <input
                        type="number"
                        id="user_id_from"
                        name="user_id_from"
                        class="ur-input"
                        min="0"
                        value="{{ old('user_id_from') }}"
                        placeholder="ব্যবহারকারী আইডি হতে লিখুন ..."
                    >
                </div>

                <div class="ur-field">
                    <label for="user_id_to">ইউজার আইডি পর্যন্ত</label>

                    <input
                        type="number"
                        id="user_id_to"
                        name="user_id_to"
                        class="ur-input"
                        min="0"
                        value="{{ old('user_id_to') }}"
                        placeholder="ব্যবহারকারী আইডি পর্যন্ত লিখুন ..."
                    >
                </div>
            </div>

            {{-- Report 2: User Statistics --}}
            <div class="ur-report-fields" data-report="2" hidden>

                <div class="ur-field">
                    <label for="gender_2">লিঙ্গ</label>

                    <select id="gender_2" name="gender" class="ur-input">
                        <option value="all" @selected(old('gender', 'all') == 'all')>উভয়</option>
                        <option value="male" @selected(old('gender') == 'male')>পুরুষ</option>
                        <option value="female" @selected(old('gender') == 'female')>মহিলা</option>
                    </select>
                </div>

                <div class="ur-field">
                    <label for="status_2">ব্যবহারকারীর অবস্থা</label>

                    <select id="status_2" name="status" class="ur-input">
                        <option value="all" @selected(old('status', 'all') == 'all')>উভয়</option>
                        <option value="active" @selected(old('status') == 'active')>সক্রিয়</option>
                        <option value="inactive" @selected(old('status') == 'inactive')>ইনঅ্যাক্টিভ</option>
                    </select>
                </div>
            </div>

            {{-- Report 3: Admission Form --}}
            <div class="ur-report-fields" data-report="3" hidden>

                <div class="ur-field ur-field-full">
                    <label for="student_id">
                        শিক্ষার্থী নির্বাচন করুন
                        <span class="ur-required">*</span>
                    </label>

                    <select
                        id="student_id"
                        name="student_id"
                        class="ur-input"
                        data-required="true"
                    >
                        <option value="">শিক্ষার্থী নির্বাচন করুন</option>

                        @foreach($students as $student)
                            <option
                                value="{{ $student->id }}"
                                @selected(old('student_id') == $student->id)
                            >
                                {{ $student->student_id ?: $student->user?->institution_user_id ?: $student->id }}
                                —
                                {{ $student->user?->name_bn ?: $student->user?->name ?: 'নাম নেই' }}
                            </option>
                        @endforeach
                    </select>

                    <small class="ur-help">
                        নির্বাচিত শিক্ষার্থীর তথ্য দিয়ে ভর্তি ফরমের প্রিভিউ তৈরি হবে।
                    </small>
                </div>
            </div>

            {{-- Report 4: Students Without Active Admission --}}
            <div class="ur-report-fields" data-report="4" hidden>

                <div class="ur-field">
                    <label for="gender_4">লিঙ্গ</label>

                    <select id="gender_4" name="gender" class="ur-input">
                        <option value="all" @selected(old('gender', 'all') == 'all')>উভয়</option>
                        <option value="male" @selected(old('gender') == 'male')>পুরুষ</option>
                        <option value="female" @selected(old('gender') == 'female')>মহিলা</option>
                    </select>
                </div>

                <div class="ur-field">
                    <label for="status_4">ব্যবহারকারীর অবস্থা</label>

                    <select id="status_4" name="status" class="ur-input">
                        <option value="all" @selected(old('status', 'all') == 'all')>উভয়</option>
                        <option value="active" @selected(old('status') == 'active')>সক্রিয়</option>
                        <option value="inactive" @selected(old('status') == 'inactive')>ইনঅ্যাক্টিভ</option>
                    </select>
                </div>
            </div>

            @if($errors->any())
                <div class="ur-validation-box">
                    <strong>ফর্মে কিছু সমস্যা রয়েছে:</strong>

                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Actions --}}
            <div class="ur-actions">
                <button type="submit" class="ur-btn ur-btn-primary">
                    <span>▤</span>
                    প্রিভিউ তৈরি করুন
                </button>

                <button type="reset" id="resetReportForm" class="ur-btn ur-btn-light">
                    ↺ রিসেট
                </button>
            </div>
        </form>
    </div>

    {{-- Preview Information --}}
    <div class="ur-preview-info">
        <div class="ur-preview-info-icon">i</div>

        <div>
            <h3>প্রিভিউ সম্পর্কে</h3>
            <p>
                রিপোর্ট নির্বাচন করে প্রয়োজনীয় তথ্য পূরণ করার পর
                <strong>প্রিভিউ তৈরি করুন</strong> বাটনে ক্লিক করুন।
                পরবর্তী পৃষ্ঠায় রিপোর্টের তথ্য দেখা যাবে।
            </p>
        </div>
    </div>

</div>

<style>
    .ur-page {
        --ur-primary: #2563eb;
        --ur-primary-dark: #1d4ed8;
        --ur-text: #172033;
        --ur-muted: #64748b;
        --ur-border: #e2e8f0;

        color: var(--ur-text);
        padding-bottom: 30px;
    }

    .ur-page * {
        box-sizing: border-box;
    }

    .ur-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
        padding: 25px 28px;
        background: linear-gradient(120deg, #eff6ff, #f8fafc);
        border: 1px solid #dbeafe;
        border-radius: 16px;
    }

    .ur-eyebrow {
        margin-bottom: 8px;
        color: var(--ur-primary);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 2px;
    }

    .ur-page-header h1 {
        margin: 0 0 8px;
        font-size: clamp(21px, 2vw, 28px);
        font-weight: 800;
        line-height: 1.4;
    }

    .ur-page-header p {
        margin: 0;
        color: var(--ur-muted);
        font-size: 14px;
        line-height: 1.8;
    }

    .ur-header-icon {
        display: grid;
        place-items: center;
        flex: 0 0 58px;
        height: 58px;
        border-radius: 16px;
        background: #dbeafe;
        color: var(--ur-primary);
        font-size: 29px;
    }

    .ur-card {
        overflow: hidden;
        background: #fff;
        border: 1px solid var(--ur-border);
        border-radius: 16px;
        box-shadow: 0 4px 18px rgba(15, 23, 42, .035);
    }

    .ur-card-heading {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 23px 26px;
        border-bottom: 1px solid #edf1f5;
    }

    .ur-heading-icon {
        display: grid;
        place-items: center;
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        border-radius: 12px;
        background: #eff6ff;
        color: var(--ur-primary);
        font-size: 23px;
    }

    .ur-card-heading h2 {
        margin: 0 0 5px;
        font-size: 18px;
        font-weight: 800;
    }

    .ur-card-heading p {
        margin: 0;
        color: var(--ur-muted);
        font-size: 13px;
        line-height: 1.7;
    }

    #userReportForm {
        padding: 27px;
    }

    .ur-report-fields {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 22px;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px dashed #dbe3ee;
    }

    .ur-report-fields[hidden] {
        display: none !important;
    }

    .ur-field {
        min-width: 0;
    }

    .ur-field-full {
        grid-column: 1 / -1;
    }

    .ur-field label {
        display: block;
        margin-bottom: 9px;
        color: #334155;
        font-size: 13px;
        font-weight: 700;
    }

    .ur-required {
        color: #dc2626;
    }

    .ur-input {
        display: block;
        width: 100%;
        min-height: 46px;
        padding: 11px 13px;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        outline: none;
        background: #fff;
        color: #0f172a;
        font-family: inherit;
        font-size: 14px;
        transition: border-color .2s, box-shadow .2s;
    }

    .ur-input:focus {
        border-color: var(--ur-primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .11);
    }

    .ur-input:hover {
        border-color: #94a3b8;
    }

    .ur-help {
        display: block;
        margin-top: 8px;
        color: var(--ur-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .ur-error {
        display: block;
        margin-top: 6px;
        color: #dc2626;
        font-size: 12px;
    }

    .ur-validation-box {
        margin-top: 24px;
        padding: 14px 17px;
        border: 1px solid #fecaca;
        border-radius: 10px;
        background: #fef2f2;
        color: #991b1b;
        font-size: 13px;
    }

    .ur-validation-box ul {
        margin: 8px 0 0;
        padding-left: 20px;
    }

    .ur-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 28px;
        padding-top: 23px;
        border-top: 1px solid #edf1f5;
    }

    .ur-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        min-height: 44px;
        padding: 11px 20px;
        border: 1px solid transparent;
        border-radius: 9px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: .2s ease;
    }

    .ur-btn-primary {
        background: var(--ur-primary);
        color: #fff;
    }

    .ur-btn-primary:hover {
        background: var(--ur-primary-dark);
        box-shadow: 0 5px 14px rgba(37, 99, 235, .2);
    }

    .ur-btn-light {
        border-color: #cbd5e1;
        background: #fff;
        color: #475569;
    }

    .ur-btn-light:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }

    .ur-preview-info {
        display: flex;
        align-items: flex-start;
        gap: 13px;
        margin-top: 20px;
        padding: 19px 21px;
        border: 1px solid #dbeafe;
        border-radius: 13px;
        background: #f8fbff;
    }

    .ur-preview-info-icon {
        display: grid;
        place-items: center;
        flex-shrink: 0;
        width: 27px;
        height: 27px;
        border-radius: 50%;
        background: #dbeafe;
        color: #1d4ed8;
        font-weight: 800;
    }

    .ur-preview-info h3 {
        margin: 2px 0 6px;
        font-size: 14px;
        font-weight: 800;
    }

    .ur-preview-info p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.9;
    }

    @media (max-width: 640px) {
        .ur-page-header {
            padding: 20px;
        }

        .ur-header-icon {
            display: none;
        }

        .ur-card-heading {
            padding: 19px;
        }

        #userReportForm {
            padding: 20px;
        }

        .ur-report-fields {
            grid-template-columns: 1fr;
            gap: 18px;
        }

        .ur-field-full {
            grid-column: auto;
        }

        .ur-actions {
            align-items: stretch;
        }

        .ur-btn {
            flex: 1 1 auto;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('userReportForm');
    const reportSelect = document.getElementById('report_type');
    const reportSections = document.querySelectorAll('.ur-report-fields');
    const resetButton = document.getElementById('resetReportForm');

    function updateReportFields() {
        const selectedReport = reportSelect.value;

        reportSections.forEach(function (section) {
            const isVisible = section.dataset.report === selectedReport;

            section.hidden = !isVisible;

            section.querySelectorAll('input, select, textarea').forEach(function (field) {
                field.disabled = !isVisible;

                if (field.dataset.required === 'true') {
                    field.required = isVisible;
                }
            });
        });
    }

    reportSelect.addEventListener('change', updateReportFields);

    resetButton.addEventListener('click', function () {
        window.setTimeout(function () {
            updateReportFields();
        }, 0);
    });

    form.addEventListener('submit', function (event) {
        if (!reportSelect.value) {
            event.preventDefault();
            reportSelect.focus();
            reportSelect.reportValidity();
        }
    });

    updateReportFields();
});
</script>
@endsection