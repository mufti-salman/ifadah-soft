@extends('layouts.admin')
@section('title', 'মাস সেটিং')


<style>
:root {
    --primary:     #2563eb;
    --primary-dark:#1d4ed8;
    --success:     #16a34a;
    --danger:      #dc2626;
    --warning:     #d97706;
    --light-bg:    #f8fafc;
    --border:      #e2e8f0;
    --text-muted:  #64748b;
    --shadow-sm:   0 1px 3px rgba(0,0,0,.08);
    --shadow-md:   0 4px 16px rgba(0,0,0,.12);
    --shadow-lg:   0 8px 32px rgba(0,0,0,.16);
    --radius:      10px;
    --radius-sm:   6px;
}
.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
}
.page-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}
.ms-table-wrapper {
    background: #fff;
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    border: 1px solid var(--border);
}
.ms-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .85rem;
    min-width: 1200px;
}
.ms-table thead th {
    background: #1e40af;
    color: #fff;
    padding: .7rem .9rem;
    text-align: center;
    font-weight: 600;
    white-space: nowrap;
    position: sticky;
    top: 0;
    z-index: 2;
}
.ms-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}
.ms-table tbody tr:hover { background: #eff6ff; }
.ms-table tbody td {
    padding: .65rem .9rem;
    text-align: center;
    color: #334155;
    white-space: nowrap;
}
.ms-table tbody td.action-col { width: 60px; }

.btn-action {
    width: 32px;
    height: 32px;
    border-radius: var(--radius-sm);
    border: none;
    background: var(--primary);
    color: #fff;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background .2s, transform .15s;
}
.btn-action:hover {
    background: var(--primary-dark);
    transform: scale(1.08);
}
.btn-new {
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    padding: .55rem 1.2rem;
    font-size: .9rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    transition: background .2s, transform .15s, box-shadow .2s;
    box-shadow: 0 2px 8px rgba(37,99,235,.3);
}
.btn-new:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(37,99,235,.4);
}

/* Modal */
.ms-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,.55);
    backdrop-filter: blur(3px);
    z-index: 1050;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}
.ms-overlay.active {
    display: flex;
    animation: fadeIn .2s ease;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}
.ms-modal {
    background: #fff;
    border-radius: 14px;
    width: 100%;
    max-width: 820px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: var(--shadow-lg);
    animation: slideUp .25s ease;
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(30px) scale(.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
.ms-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.1rem 1.5rem;
    border-bottom: 1px solid var(--border);
    position: sticky;
    top: 0;
    background: #fff;
    z-index: 5;
    border-radius: 14px 14px 0 0;
}
.ms-modal-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}
.ms-modal-close {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: none;
    background: #f1f5f9;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: #64748b;
    transition: background .2s, color .2s;
}
.ms-modal-close:hover {
    background: #fee2e2;
    color: var(--danger);
}
.ms-modal-body { padding: 1.4rem 1.5rem; }
.month-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1.2rem;
}
.form-group { position: relative; }
.form-label {
    font-size: .8rem;
    font-weight: 600;
    color: #475569;
    margin-bottom: .3rem;
    display: block;
}
.ms-input {
    width: 100%;
    padding: .55rem .85rem;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: .875rem;
    color: #1e293b;
    background: #fff;
    transition: border-color .2s, box-shadow .2s;
    outline: none;
    box-sizing: border-box;
}
.ms-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37,99,235,.1);
}
.ms-input::placeholder {
    color: #94a3b8;
    font-size: .82rem;
}
.ms-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right .75rem center;
    background-size: 16px;
    padding-right: 2.2rem;
}
.suggestion-list {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #fff;
    border: 1.5px solid var(--primary);
    border-top: none;
    border-radius: 0 0 var(--radius-sm) var(--radius-sm);
    z-index: 1100;
    max-height: 140px;
    overflow-y: auto;
    box-shadow: 0 4px 12px rgba(0,0,0,.1);
    display: none;
}
.suggestion-item {
    padding: .45rem .85rem;
    cursor: pointer;
    font-size: .82rem;
    color: #334155;
    display: flex;
    align-items: center;
    gap: .4rem;
    transition: background .15s;
}
.suggestion-item:hover {
    background: #eff6ff;
    color: var(--primary);
}
.suggestion-badge {
    font-size: .68rem;
    padding: .1rem .35rem;
    border-radius: 20px;
    font-weight: 600;
}
.badge-bangla  { background: #dbeafe; color: #1d4ed8; }
.badge-english { background: #d1fae5; color: #065f46; }
.badge-arabic {
    background: #fef3c7;
    color: #92400e;
}

.calendar-type-badge {
    display: inline-block;
    padding: .25rem .55rem;
    border-radius: 20px;
    font-size: .72rem;
    font-weight: 600;
}

.arabic-input {
    direction: rtl;
    text-align: right;
    font-family: "Noto Naskh Arabic", "Amiri", serif;
}
.bottom-fields {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-top: .5rem;
}
.ms-modal-footer {
    padding: 1rem 1.5rem 1.2rem;
    border-top: 1px solid var(--border);
    display: flex;
    gap: .75rem;
    justify-content: flex-start;
}
.btn-save {
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    padding: .6rem 1.6rem;
    font-size: .9rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    transition: background .2s, transform .15s;
}
.btn-save:hover:not(:disabled) {
    background: var(--primary-dark);
    transform: translateY(-1px);
}
.btn-save:disabled { opacity: .65; cursor: not-allowed; }
.btn-cancel {
    background: #f1f5f9;
    color: #475569;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: .6rem 1.2rem;
    font-size: .9rem;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s;
}
.btn-cancel:hover { background: #e2e8f0; }
.ms-toast-container {
    position: fixed;
    top: 1.2rem;
    right: 1.2rem;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: .5rem;
}
.ms-toast {
    min-width: 280px;
    padding: .85rem 1.1rem;
    border-radius: var(--radius-sm);
    font-size: .875rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: .6rem;
    box-shadow: var(--shadow-md);
    animation: toastIn .3s ease;
    color: #fff;
}
@keyframes toastIn {
    from { opacity: 0; transform: translateX(60px); }
    to   { opacity: 1; transform: translateX(0); }
}
.ms-toast.success { background: var(--success); }
.ms-toast.error   { background: var(--danger); }
.ms-toast.warning { background: var(--warning); }
.spinner {
    width: 16px; height: 16px;
    border: 2px solid rgba(255,255,255,.4);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin .6s linear infinite;
    display: inline-block;
}
@keyframes spin { to { transform: rotate(360deg); } }
.ms-empty {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--text-muted);
}
.ms-empty svg { margin-bottom: 1rem; opacity: .4; }
.field-error {
    color: var(--danger);
    font-size: .75rem;
    margin-top: .2rem;
    display: none;
}
.ms-input.is-invalid { border-color: var(--danger); }
</style>


@section('page')
<div class="container-fluid px-4 py-3">

    {{-- Toast Container --}}
    <div class="ms-toast-container" id="toastContainer"></div>

    {{-- Page Header --}}
    <div class="page-header">
        <h1 class="page-title">মাসের তালিকা টেবিল</h1>
        <button class="btn-new" id="btnNewRecord">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            মাস তৈরি
        </button>
    </div>

    {{-- Table --}}
    <div class="ms-table-wrapper" style="overflow-x:auto;">
        <table class="ms-table" id="msTable">
            <thead>
                <tr>
                    <th>অ্যাকশন</th>
                    <th>শিক্ষাবর্ষ</th>
                    <th>ক্লাস</th>
                    <th>ক্যালেন্ডার</th>
                    <th>১ম মাস</th>
                    <th>২য় মাস</th>
                    <th>৩য় মাস</th>
                    <th>৪র্থ মাস</th>
                    <th>৫ম মাস</th>
                    <th>৬ষ্ঠ মাস</th>
                    <th>৭ম মাস</th>
                    <th>৮ম মাস</th>
                    <th>৯ম মাস</th>
                    <th>১০ম মাস</th>
                    <th>১১তম মাস</th>
                    <th>১২তম মাস</th>
                </tr>
            </thead>
          <tbody>
    @forelse($monthSettings as $ms)
        <tr id="row-{{ $ms->id }}">

            {{-- Action --}}
            <td class="action-col">
                <button class="btn-action btn-edit"
                        data-id="{{ $ms->id }}"
                        title="এডিট করুন">

                    <svg width="14" height="14" fill="none"
                         stroke="currentColor" stroke-width="2.2"
                         viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>

                    </svg>
                </button>
            </td>

            {{-- Academic Year --}}
            <td>{{ $ms->academic_year }}</td>

            {{-- Class --}}
            <td>
                {{ $ms->studentClass->name_bn ?: ($ms->studentClass->name ?? '—') }}
            </td>

            {{-- Calendar Type --}}
            <td>
                @php
                    $calendarLabels = [
                        'bangla' => 'বাংলা',
                        'english' => 'English',
                        'arabic' => 'عربي (Hijri)',
                    ];

                    $calendarType = $ms->calendar_type ?? 'english';
                @endphp

                <span class="calendar-type-badge badge-{{ $calendarType }}">
                    {{ $calendarLabels[$calendarType] ?? 'English' }}
                </span>
            </td>

            {{-- Months --}}
            <td>{{ $ms->month_1 ?? '—' }}</td>
            <td>{{ $ms->month_2 ?? '—' }}</td>
            <td>{{ $ms->month_3 ?? '—' }}</td>
            <td>{{ $ms->month_4 ?? '—' }}</td>
            <td>{{ $ms->month_5 ?? '—' }}</td>
            <td>{{ $ms->month_6 ?? '—' }}</td>
            <td>{{ $ms->month_7 ?? '—' }}</td>
            <td>{{ $ms->month_8 ?? '—' }}</td>
            <td>{{ $ms->month_9 ?? '—' }}</td>
            <td>{{ $ms->month_10 ?? '—' }}</td>
            <td>{{ $ms->month_11 ?? '—' }}</td>
            <td>{{ $ms->month_12 ?? '—' }}</td>

        </tr>

    @empty

        <tr id="emptyRow">
            <td colspan="16">

                <div class="ms-empty">

                    <svg width="64" height="64"
                         fill="none" stroke="currentColor"
                         stroke-width="1.5" viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>

                    </svg>

                    <p>কোনো মাসের তালিকা নেই। নতুন তৈরি করুন।</p>

                </div>

            </td>
        </tr>

    @endforelse
</tbody>
        </table>
    </div>

    @if($monthSettings->hasPages())
    <div class="mt-3">{{ $monthSettings->links() }}</div>
    @endif

</div>

{{-- MODAL — outside container but inside @section('page') --}}
<div class="ms-overlay" id="msOverlay">
    <div class="ms-modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">

        <div class="ms-modal-header">
            <h2 class="ms-modal-title" id="modalTitle">মাসের নাম তৈরি করুন</h2>
            <button class="ms-modal-close" id="btnCloseModal" type="button" aria-label="বন্ধ করুন">✕</button>
        </div>

        <div class="ms-modal-body">
            <div id="msForm" autocomplete="off">
                <input type="hidden" id="recordId" value="">
                <input type="hidden" id="formMethod" value="POST">
                <div class="form-group" style="margin-bottom: 1.2rem;">
                    <label class="form-label" for="calendarType">
                        ক্যালেন্ডারের ধরন
                        <span style="color:var(--danger)">*</span>
                    </label>

                    <select id="calendarType" name="calendar_type"
                            class="ms-input ms-select" required>

                        <option value="">ক্যালেন্ডার নির্বাচন করুন</option>

                        <option value="bangla">বাংলা ক্যালেন্ডার</option>

                        <option value="english">English Calendar (Gregorian)</option>

                        <option value="arabic">التقويم الهجري (Arabic / Hijri)</option>

                    </select>

                    <div class="field-error" id="err_calendar_type"></div>

                    <small id="calendarTypeHelp"
                        style="display:block; margin-top:5px; color:#64748b;">
                        ক্যালেন্ডার নির্বাচন করলে ১২ মাসের নাম স্বয়ংক্রিয়ভাবে বসবে।
                        প্রয়োজনে প্রতিটি নাম পরিবর্তন করতে পারবেন।
                    </small>
                </div>
                <div class="month-grid">
                    @php
                    $monthLabels = [
                        1=>'১ম', 2=>'২য়', 3=>'৩য়', 4=>'৪র্থ', 5=>'৫ম', 6=>'৬ষ্ঠ',
                        7=>'৭ম', 8=>'৮ম', 9=>'৯ম', 10=>'১০ম', 11=>'১১তম', 12=>'১২তম'
                    ];
                    @endphp

                    @foreach($monthLabels as $num => $label)
                    <div class="form-group">
                        <label class="form-label" for="month{{ $num }}">{{ $label }} মাস :</label>
                        <input
                            type="text"
                            id="month{{ $num }}"
                            name="month_{{ $num }}"
                            class="ms-input smart-month"
                            data-month="{{ $num }}"
                            placeholder="{{ $num }}নং মাসের নাম্বার বা নাম লিখুন"
                        >
                        <div class="suggestion-list" id="suggest{{ $num }}"></div>
                        <div class="field-error" id="err_month_{{ $num }}"></div>
                    </div>
                    @endforeach
                </div>

                <div class="bottom-fields">
                    <div class="form-group">
                        <label class="form-label" for="academicYear">শিক্ষাবর্ষ <span style="color:var(--danger)">*</span></label>
                        <select id="academicYear" name="academic_year" class="ms-input ms-select" required>
                            <option value="">শিক্ষাবর্ষ নির্বাচন করুন</option>

                            @foreach($years as $year)
                                <option
                                    value="{{ $year->name_bn ?: $year->name }}"
                                    {{ $year->id == $currentYear ? 'selected' : '' }}
                                >
                                    {{ $year->name_bn ?: $year->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="field-error" id="err_academic_year"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="classId">ক্লাস <span style="color:var(--danger)">*</span></label>
                        <select id="classId" name="class_id" class="ms-input ms-select" required>
                            <option value="">নির্বাচন করুন</option>
                            @foreach($classes as $class)
                           <option value="{{ $class->id }}">
                                {{ $class->name_bn ?: $class->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="field-error" id="err_class_id"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ms-modal-footer">
            <button class="btn-save" id="btnSave" type="button">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span id="btnSaveText">সংরক্ষণ করুন</span>
            </button>
            <button class="btn-cancel" id="btnCancelModal" type="button">বাতিল</button>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    /* ============================================================
       ELEMENTS
    ============================================================ */

    function el(id) {
        return document.getElementById(id);
    }

    const overlay     = el('msOverlay');
    const recordId    = el('recordId');
    const formMethod  = el('formMethod');
    const btnSave     = el('btnSave');
    const btnNew      = el('btnNewRecord');
    const btnClose    = el('btnCloseModal');
    const btnCancel   = el('btnCancelModal');
    const btnSaveText = el('btnSaveText');
    const modalTitle  = el('modalTitle');

    const calendarType = el('calendarType');

    if (!overlay || !btnSave || !btnNew || !calendarType) {
        console.error('Month Setting: Required elements missing.');
        return;
    }

    /* ============================================================
       ROUTES
    ============================================================ */

    const ROUTES = {
        store: '{{ route("dashboard.month.store") }}',

        edit: (id) =>
            `{{ route("dashboard.month.edit", ":id") }}`.replace(':id', id),

        update: (id) =>
            `{{ route("dashboard.month.update", ":id") }}`.replace(':id', id),

        suggest: '{{ route("dashboard.month.suggest") }}'
    };

    const CSRF =
        document.querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content') || '';

    /* ============================================================
       CALENDAR MONTHS
    ============================================================ */

    const calendarMonths = {
        bangla: [
            'বৈশাখ',
            'জ্যৈষ্ঠ',
            'আষাঢ়',
            'শ্রাবণ',
            'ভাদ্র',
            'আশ্বিন',
            'কার্তিক',
            'অগ্রহায়ণ',
            'পৌষ',
            'মাঘ',
            'ফাল্গুন',
            'চৈত্র'
        ],

        english: [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ],

        arabic: [
            'محرم',
            'صفر',
            'ربيع الأول',
            'ربيع الآخر',
            'جمادى الأولى',
            'جمادى الآخرة',
            'رجب',
            'شعبان',
            'رمضان',
            'شوال',
            'ذو القعدة',
            'ذو الحجة'
        ]
    };

    const calendarLabels = {
        bangla: 'বাংলা',
        english: 'English',
        arabic: 'Arabic / Hijri'
    };

    /* ============================================================
       HELPERS
    ============================================================ */

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char];
        });
    }

    function showToast(message, type = 'success') {
        const container = el('toastContainer');
        if (!container) return;

        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠'
        };

        const toast = document.createElement('div');
        toast.className = `ms-toast ${type}`;

        const icon = document.createElement('span');
        icon.textContent = icons[type] || '!';

        const text = document.createElement('span');
        text.textContent = message;

        toast.append(icon, text);
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity .3s';

            setTimeout(() => toast.remove(), 350);
        }, 3500);
    }

    function clearErrors() {
        document.querySelectorAll('.field-error').forEach(error => {
            error.style.display = 'none';
            error.textContent = '';
        });

        document.querySelectorAll('.ms-input.is-invalid').forEach(input => {
            input.classList.remove('is-invalid');
        });
    }

    function showErrors(errors) {
        Object.entries(errors).forEach(([field, messages]) => {
            const errorElement = el(`err_${field}`);

            const inputElement =
                document.querySelector(`[name="${field}"]`);

            if (errorElement) {
                errorElement.textContent = messages[0];
                errorElement.style.display = 'block';
            }

            if (inputElement) {
                inputElement.classList.add('is-invalid');
            }
        });
    }

    function closeSuggestions() {
        document.querySelectorAll('.suggestion-list').forEach(list => {
            list.style.display = 'none';
            list.innerHTML = '';
        });
    }

    /* ============================================================
       CALENDAR TYPE CHANGE
    ============================================================ */

    function updateMonthInputDirection(type) {
        const isArabic = type === 'arabic';

        document.querySelectorAll('.smart-month').forEach(input => {
            input.classList.toggle('arabic-input', isArabic);

            input.dir = isArabic ? 'rtl' : 'auto';

            input.placeholder = isArabic
                ? 'اسم الشهر أو رقمه'
                : 'মাসের নাম বা নাম্বার লিখুন';
        });
    }

    function fillCalendarMonths(type, overwrite = true) {
        const months = calendarMonths[type];

        if (!months) return;

        for (let i = 1; i <= 12; i++) {
            const input = el(`month${i}`);

            if (!input) continue;

            if (overwrite || !input.value.trim()) {
                input.value = months[i - 1];
            }
        }

        updateMonthInputDirection(type);
        closeSuggestions();
    }

    calendarType.addEventListener('change', function () {
        const type = this.value;

        if (!calendarMonths[type]) {
            updateMonthInputDirection('english');
            return;
        }

        fillCalendarMonths(type, true);

        const help = el('calendarTypeHelp');

        if (help) {
            help.textContent =
                `${calendarLabels[type]} ক্যালেন্ডারের ১২ মাসের নাম বসানো হয়েছে। প্রয়োজনে নামগুলো পরিবর্তন করতে পারবেন।`;
        }
    });

    /* ============================================================
       MODAL
    ============================================================ */

    function openModal(editMode = false) {
        overlay.classList.add('active');

        if (modalTitle) {
            modalTitle.textContent = editMode
                ? 'মাসের তালিকা এডিট করুন'
                : 'মাসের নাম তৈরি করুন';
        }

        if (btnSaveText) {
            btnSaveText.textContent = editMode
                ? 'আপডেট করুন'
                : 'সংরক্ষণ করুন';
        }
    }

    function resetModal() {
        for (let i = 1; i <= 12; i++) {
            const input = el(`month${i}`);

            if (input) {
                input.value = '';
                input.classList.remove('arabic-input');
                input.dir = 'auto';
            }
        }

        calendarType.value = '';

        const year = el('academicYear');
        const classId = el('classId');

        if (year) year.selectedIndex = 0;
        if (classId) classId.value = '';

        if (recordId) recordId.value = '';
        if (formMethod) formMethod.value = 'POST';

        const help = el('calendarTypeHelp');

        if (help) {
            help.textContent =
                'ক্যালেন্ডার নির্বাচন করলে ১২ মাসের নাম স্বয়ংক্রিয়ভাবে বসবে। প্রয়োজনে প্রতিটি নাম পরিবর্তন করতে পারবেন।';
        }

        clearErrors();
        closeSuggestions();
    }

    function closeModal() {
        overlay.classList.remove('active');
        resetModal();
    }

    /* ============================================================
       SMART MONTH SUGGESTION
    ============================================================ */

    let suggestTimer = null;

    const banglaDigits = {
        '০': '0',
        '১': '1',
        '২': '2',
        '৩': '3',
        '৪': '4',
        '৫': '5',
        '৬': '6',
        '৭': '7',
        '৮': '8',
        '৯': '9'
    };

    function normalizeDigits(value) {
        return value.replace(/[০-৯]/g, digit => banglaDigits[digit]);
    }

    function setupSmartInputs() {
        document.querySelectorAll('.smart-month').forEach(input => {
            const monthNum = input.dataset.month;
            const suggestionBox = el(`suggest${monthNum}`);

            if (!suggestionBox) return;

            input.addEventListener('input', function () {
                const value = this.value.trim();

                clearTimeout(suggestTimer);

                if (!value) {
                    suggestionBox.style.display = 'none';
                    suggestionBox.innerHTML = '';
                    return;
                }

                const selectedType = calendarType.value;

                if (!selectedType) {
                    suggestionBox.style.display = 'none';
                    return;
                }

                const normalizedValue = normalizeDigits(value);

                suggestTimer = setTimeout(async () => {
                    try {
                        const params = new URLSearchParams({
                            q: normalizedValue,
                            calendar_type: selectedType
                        });

                        const response = await fetch(
                            `${ROUTES.suggest}?${params.toString()}`,
                            {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': CSRF
                                }
                            }
                        );

                        if (!response.ok) {
                            throw new Error('Suggestion request failed');
                        }

                        const data = await response.json();

                        if (!Array.isArray(data) || !data.length) {
                            suggestionBox.style.display = 'none';
                            suggestionBox.innerHTML = '';
                            return;
                        }

                        suggestionBox.innerHTML = data.map(item => {
                            const type = item.type || selectedType;

                            const badgeLabel = {
                                bangla: 'বাংলা',
                                english: 'English',
                                arabic: 'عربي'
                            };

                            return `
                                <div class="suggestion-item"
                                     data-value="${escapeHtml(item.value)}">

                                    <span>${escapeHtml(item.label)}</span>

                                    <span class="suggestion-badge badge-${escapeHtml(type)}">
                                        ${badgeLabel[type] || 'Month'}
                                    </span>

                                </div>
                            `;
                        }).join('');

                        suggestionBox.style.display = 'block';

                        suggestionBox.querySelectorAll('.suggestion-item')
                            .forEach(item => {

                                item.addEventListener('mousedown', function (event) {
                                    event.preventDefault();

                                    input.value = this.dataset.value;

                                    suggestionBox.style.display = 'none';

                                    input.focus();
                                });
                            });

                    } catch (error) {
                        console.error('Month suggestion error:', error);

                        suggestionBox.style.display = 'none';
                    }
                }, 180);
            });

            input.addEventListener('blur', function () {
                setTimeout(() => {
                    suggestionBox.style.display = 'none';
                }, 220);
            });

            input.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();

                    const first =
                        suggestionBox.querySelector('.suggestion-item');

                    if (first) {
                        input.value = first.dataset.value;
                        suggestionBox.style.display = 'none';
                    }
                }

                if (event.key === 'Escape') {
                    suggestionBox.style.display = 'none';
                }
            });
        });
    }

    /* ============================================================
       TABLE HELPERS
    ============================================================ */

    function buildMonthCells(ms) {
        let html = '';

        for (let i = 1; i <= 12; i++) {
            html += `<td>${escapeHtml(ms[`month_${i}`] || '—')}</td>`;
        }

        return html;
    }

    function getClassName(ms) {
        return ms.student_class?.name_bn
            || ms.student_class?.name
            || ms.studentClass?.name_bn
            || ms.studentClass?.name
            || '—';
    }

    function getCalendarBadge(type) {
        const labels = {
            bangla: 'বাংলা',
            english: 'English',
            arabic: 'عربي (Hijri)'
        };

        const safeType = calendarMonths[type] ? type : 'english';

        return `
            <span class="calendar-type-badge badge-${safeType}">
                ${labels[safeType]}
            </span>
        `;
    }

    function buildActionButton(id) {
        return `
            <button class="btn-action btn-edit"
                    data-id="${escapeHtml(id)}"
                    title="এডিট করুন">

                <svg width="14" height="14" fill="none"
                     stroke="currentColor" stroke-width="2.2"
                     viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>

                </svg>
            </button>
        `;
    }

    function appendTableRow(ms) {
        const tbody = document.querySelector('#msTable tbody');

        if (!tbody) return;

        const emptyRow = el('emptyRow');

        if (emptyRow) emptyRow.remove();

        const tr = document.createElement('tr');

        tr.id = `row-${ms.id}`;

        tr.innerHTML = `
            <td class="action-col">
                ${buildActionButton(ms.id)}
            </td>

            <td>${escapeHtml(ms.academic_year)}</td>

            <td>${escapeHtml(getClassName(ms))}</td>

            <td>${getCalendarBadge(ms.calendar_type)}</td>

            ${buildMonthCells(ms)}
        `;

        tbody.prepend(tr);

        bindEditButtons();
    }

    function updateTableRow(ms) {
        const row = el(`row-${ms.id}`);

        if (!row) return;

        const cells = row.querySelectorAll('td');

        if (cells[1]) {
            cells[1].textContent = ms.academic_year;
        }

        if (cells[2]) {
            cells[2].textContent = getClassName(ms);
        }

        if (cells[3]) {
            cells[3].innerHTML = getCalendarBadge(ms.calendar_type);
        }

        for (let i = 1; i <= 12; i++) {
            if (cells[i + 3]) {
                cells[i + 3].textContent = ms[`month_${i}`] || '—';
            }
        }
    }

    /* ============================================================
       SAVE RECORD
    ============================================================ */

    async function saveRecord() {
        clearErrors();

        btnSave.disabled = true;

        btnSave.innerHTML =
            '<span class="spinner"></span><span>অপেক্ষা করুন...</span>';

        const id = recordId.value;
        const method = formMethod.value || 'POST';

        const url = method === 'POST'
            ? ROUTES.store
            : ROUTES.update(id);

        const payload = {
            _token: CSRF,
            calendar_type: calendarType.value,
            academic_year: el('academicYear').value,
            class_id: el('classId').value
        };

        if (method === 'PUT') {
            payload._method = 'PUT';
        }

        for (let i = 1; i <= 12; i++) {
            const input = el(`month${i}`);

            payload[`month_${i}`] = input
                ? input.value.trim()
                : '';
        }

        try {
            const response = await fetch(url, {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json'
                },

                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (!response.ok) {
                if (response.status === 422 && data.errors) {
                    showErrors(data.errors);
                } else {
                    showToast(
                        data.message || 'কিছু একটা ভুল হয়েছে!',
                        'error'
                    );
                }

                return;
            }

            showToast(data.message || 'সফলভাবে সংরক্ষণ হয়েছে।');

            closeModal();

            if (method === 'POST') {
                appendTableRow(data.data);
            } else {
                updateTableRow(data.data);
            }

        } catch (error) {
            console.error(error);

            showToast('নেটওয়ার্ক সমস্যা! আবার চেষ্টা করুন।', 'error');

        } finally {
            btnSave.disabled = false;

            btnSave.innerHTML = `
                <svg width="15" height="15" fill="none"
                     stroke="currentColor" stroke-width="2.5"
                     viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M5 13l4 4L19 7"/>

                </svg>

                <span id="btnSaveText">
                    ${formMethod.value === 'PUT'
                        ? 'আপডেট করুন'
                        : 'সংরক্ষণ করুন'}
                </span>
            `;
        }
    }

    /* ============================================================
       LOAD EDIT
    ============================================================ */

    async function loadEdit(id) {
        try {
            const response = await fetch(ROUTES.edit(id), {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                }
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                showToast('ডেটা লোড করতে ব্যর্থ!', 'error');
                return;
            }

            const ms = data.data;

            resetModal();

            recordId.value = ms.id;
            formMethod.value = 'PUT';

            el('academicYear').value = ms.academic_year;
            el('classId').value = ms.class_id;

            const type = calendarMonths[ms.calendar_type]
                ? ms.calendar_type
                : 'english';

            calendarType.value = type;

            updateMonthInputDirection(type);

            for (let i = 1; i <= 12; i++) {
                const input = el(`month${i}`);

                if (input) {
                    input.value = ms[`month_${i}`] || '';
                }
            }

            const help = el('calendarTypeHelp');

            if (help) {
                help.textContent =
                    `${calendarLabels[type]} ক্যালেন্ডার নির্বাচিত আছে। মাসের নাম পরিবর্তন করতে পারবেন।`;
            }

            openModal(true);

        } catch (error) {
            console.error(error);

            showToast('নেটওয়ার্ক সমস্যা!', 'error');
        }
    }

    /* ============================================================
       EDIT BUTTONS
    ============================================================ */

    function bindEditButtons() {
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.onclick = function () {
                loadEdit(this.dataset.id);
            };
        });
    }

    /* ============================================================
       BUTTON EVENTS
    ============================================================ */

    btnNew.addEventListener('click', function () {
        resetModal();
        openModal(false);
    });

    btnClose.addEventListener('click', closeModal);

    btnCancel.addEventListener('click', closeModal);

    btnSave.addEventListener('click', saveRecord);

    overlay.addEventListener('click', function (event) {
        if (event.target === overlay) {
            closeModal();
        }
    });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.form-group')) {
            closeSuggestions();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && overlay.classList.contains('active')) {
            closeModal();
        }
    });

    /* ============================================================
       INITIALIZE
    ============================================================ */

    setupSmartInputs();
    bindEditButtons();

});
</script>
@endpush
@endsection