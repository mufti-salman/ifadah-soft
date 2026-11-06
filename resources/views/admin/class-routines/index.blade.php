@extends('layouts.admin')

@section('title', 'ক্লাস রুটিন')

@section('page')

<style>
    .cr-page { padding: 24px; color: #1f2937; }
    .cr-page *, .cr-modal * { box-sizing: border-box; }

    .cr-card {
        background: #fff; border-radius: 16px; padding: 24px 16px;
        box-shadow: 0 2px 14px rgba(15, 23, 42, .06);
    }

    /* Tabs + Create button */
    .cr-top {
        display: flex; align-items: center; justify-content: space-between; gap: 14px;
        padding: 0 14px 14px; border-bottom: 1px solid #e5e7eb; margin-bottom: 18px; flex-wrap: wrap;
    }
    .cr-tabs { display: flex; gap: 26px; }
    .cr-tab {
        background: none; border: 0; padding: 6px 0; font-size: 17px; font-weight: 700;
        color: #6b7280; cursor: pointer; border-bottom: 2px solid transparent;
    }
    .cr-tab.is-active { color: #2563eb; border-bottom-color: #2563eb; }

    .cr-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 7px;
        min-height: 40px; padding: 9px 22px; border: 0; border-radius: 10px;
        font-size: 14px; font-weight: 700; cursor: pointer; white-space: nowrap; text-decoration: none;
    }
    .cr-btn-blue { background: #3b82f6; color: #fff; }
    .cr-btn-blue:hover { background: #2563eb; color: #fff; }
    .cr-btn-light { background: #f1f5f9; color: #334155; }
    .cr-btn-light:hover { background: #e2e8f0; }

    .cr-pane { display: none; }
    .cr-pane.is-active { display: block; }

    /* Filters */
    .cr-filter { display: flex; gap: 16px; flex-wrap: wrap; padding: 0 14px 18px; }
    .cr-filter > div { width: 210px; max-width: 100%; }
    .cr-label { display: block; margin-bottom: 6px; font-size: 14px; font-weight: 600; }
    .cr-req { color: #ef4444; }

    .cr-control {
        display: block; width: 100%; min-height: 44px; padding: 9px 12px; border: 1px solid #d1d5db;
        border-radius: 9px; background: #fff; color: #1f2937; font-size: 14px; outline: none;
    }
    .cr-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, .12); }
    textarea.cr-control { min-height: 80px; resize: vertical; }

    /* Alerts */
    .cr-alert { margin: 0 14px 16px; padding: 12px 15px; border-radius: 10px; font-size: 13px; line-height: 1.7; border: 1px solid transparent; }
    .cr-alert-success { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; }
    .cr-alert-danger { background: #fff1f2; border-color: #fecdd3; color: #9f1239; }
    .cr-alert ul { margin: 6px 0 0; padding-left: 20px; }

    /* Table */
    .cr-table-wrap { overflow-x: auto; border-radius: 12px; }
    .cr-table { width: 100%; min-width: 900px; border-collapse: collapse; font-size: 14px; }
    .cr-table th { padding: 14px 10px; text-align: center; font-weight: 700; white-space: nowrap; background: #fff; }
    .cr-table td { padding: 12px 10px; text-align: center; vertical-align: middle; }
    .cr-table tbody tr:nth-child(odd) { background: #f8fafc; }
    .cr-table tbody tr:nth-child(even) { background: #eef0f3; }
    .cr-empty { padding: 35px 10px !important; color: #6b7280; }

    .cr-row-actions { display: flex; gap: 8px; justify-content: center; }
    .cr-row-actions form { margin: 0; }
    .cr-icon-btn {
        display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px;
        border: 0; border-radius: 9px; color: #fff; cursor: pointer; font-size: 15px;
    }
    .cr-icon-btn.edit { background: #3b82f6; }
    .cr-icon-btn.edit:hover { background: #2563eb; }
    .cr-icon-btn.delete { background: #ef4444; }
    .cr-icon-btn.delete:hover { background: #dc2626; }

    .cr-slots-table { min-width: 0; max-width: 760px; margin: 0 auto; }

    .cr-pager { padding: 14px; }

    /* Modal */
    .cr-modal {
        position: fixed; inset: 0; z-index: 999999; display: none; align-items: center; justify-content: center;
        padding: 12px; background: rgba(15, 23, 42, .6); overflow-y: auto;
    }
    .cr-modal.is-open { display: flex; }
    .cr-dialog {
        display: flex; flex-direction: column; width: 100%; max-width: 560px; max-height: calc(100vh - 24px);
        margin: auto; background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 25px 80px rgba(0, 0, 0, .3);
    }
    .cr-dialog.is-small { max-width: 460px; }
    .cr-dialog-head { display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; border-bottom: 1px solid #e5e7eb; }
    .cr-dialog-head h3 { margin: 0; font-size: 18px; font-weight: 800; }
    .cr-close { width: 34px; height: 34px; border: 0; border-radius: 9px; background: #f1f5f9; font-size: 22px; line-height: 1; cursor: pointer; }
    .cr-dialog form { display: flex; flex-direction: column; min-height: 0; flex: 1 1 auto; margin: 0; }
    .cr-dialog-body { padding: 18px 20px; overflow-y: auto; flex: 1 1 auto; }
    .cr-dialog-foot { display: flex; gap: 8px; padding: 13px 20px; border-top: 1px solid #e5e7eb; }
    .cr-group { margin-bottom: 15px; }
    .cr-group:last-child { margin-bottom: 0; }

    .cr-days { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 9px 14px; }
    .cr-day { display: flex; align-items: center; gap: 8px; font-size: 14px; cursor: pointer; }
    .cr-day input { width: 16px; height: 16px; accent-color: #3b82f6; }

    @media (max-width: 575px) {
        .cr-page { padding: 12px; }
        .cr-tab { font-size: 15px; }
    }
</style>

<div class="cr-page">
<div class="cr-card">

    {{-- Tabs + Create --}}
    <div class="cr-top">
        <div class="cr-tabs">
            <button type="button" class="cr-tab {{ $tab === 'routines' ? 'is-active' : '' }}" data-tab="routines">ক্লাস রুটিন</button>
            <button type="button" class="cr-tab {{ $tab === 'slots' ? 'is-active' : '' }}" data-tab="slots">সময়-স্লট</button>
        </div>

        <button type="button" class="cr-btn cr-btn-blue" id="openCreate">Create</button>
    </div>

    {{-- Messages --}}
    @if(session('success'))
        <div class="cr-alert cr-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="cr-alert cr-alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="cr-alert cr-alert-danger">
            <strong><i class="fas fa-exclamation-triangle"></i> তথ্য যাচাই করুন:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- ============ TAB 1: CLASS ROUTINE ============ --}}
    <div class="cr-pane {{ $tab === 'routines' ? 'is-active' : '' }}" id="pane-routines">

        <form method="GET" action="{{ route('dashboard.class-routines.index') }}">
            <div class="cr-filter">

                <div>
                    <label class="cr-label" for="f_year">শিক্ষাবর্ষ <span class="cr-req">*</span> :</label>

                    <select name="academic_year_id" id="f_year" class="cr-control" onchange="this.form.submit()">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ (int) $selectedYearId === (int) $year->id ? 'selected' : '' }}>
                                {{ $year->name_bn ?: $year->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="cr-label" for="f_section">ক্লাস <span class="cr-req">*</span> :</label>

                    <select name="section_id" id="f_section" class="cr-control" onchange="this.form.submit()">
                        <option value="">নির্বাচন করুন</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ (string) request('section_id') === (string) $section->id ? 'selected' : '' }}>
                                {{ $section->name_bn ?: $section->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>
        </form>

        <div class="cr-table-wrap">
            <table class="cr-table">
                <thead>
                    <tr>
                        <th>অ্যাকশন</th>
                        <th>আইডি</th>
                        <th>দিন</th>
                        <th>সময়</th>
                        <th>বিষয়</th>
                        <th>শিক্ষক</th>
                        <th>ক্লাস</th>
                        <th>শিক্ষাবর্ষ</th>
                        <th>মন্তব্য</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($routines as $routine)

                        @php
                            $editData = [
                                'id'               => $routine->id,
                                'academic_year_id' => $routine->academic_year_id,
                                'time_slot_id'     => $routine->time_slot_id,
                                'section_id'       => $routine->section_id,
                                'subject_id'       => $routine->subject_id,
                                'teacher_id'       => $routine->teacher_id,
                                'remarks'          => $routine->remarks,
                                'days'             => [$routine->day],
                            ];
                        @endphp

                        <tr>
                            <td>
                                <div class="cr-row-actions">
                                    <button type="button" class="cr-icon-btn edit js-edit-routine" title="এডিট"
                                            data-routine="{{ json_encode($editData) }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('dashboard.class-routines.destroy', $routine->id) }}" method="POST"
                                          onsubmit="return confirm('এই রুটিনটি মুছে ফেলতে চান?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="cr-icon-btn delete" title="ডিলিট">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>

                            <td>{{ $routine->id }}</td>
                            <td>{{ $routine->day_label }}</td>
                            <td>{{ $routine->timeSlot?->range_label ?? '—' }}</td>
                            <td>{{ $routine->subject?->name_bn ?: ($routine->subject?->name ?? '—') }}</td>
                            <td>{{ $routine->teacher?->full_name ?: '—' }}</td>
                            <td>{{ $routine->section?->name_bn ?: ($routine->section?->name ?? '—') }}</td>
                            <td>{{ $routine->academicYear?->name_bn ?: ($routine->academicYear?->name ?? '—') }}</td>
                            <td>{{ $routine->remarks ?: '—' }}</td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="9" class="cr-empty">কোনো ক্লাস রুটিন পাওয়া যায়নি।</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($routines->hasPages())
            <div class="cr-pager">{{ $routines->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>


    {{-- ============ TAB 2: TIME SLOTS ============ --}}
    <div class="cr-pane {{ $tab === 'slots' ? 'is-active' : '' }}" id="pane-slots">

        <div class="cr-table-wrap">
            <table class="cr-table cr-slots-table">
                <thead>
                    <tr>
                        <th>অ্যাকশন</th>
                        <th>আইডি</th>
                        <th>সময় হতে</th>
                        <th>সময় পর্যন্ত</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($timeSlots as $slot)
                        <tr>
                            <td>
                                <div class="cr-row-actions">
                                    <button type="button" class="cr-icon-btn edit js-edit-slot" title="এডিট"
                                            data-id="{{ $slot->id }}"
                                            data-start="{{ substr($slot->start_time, 0, 5) }}"
                                            data-end="{{ substr($slot->end_time, 0, 5) }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('dashboard.class-time-slots.destroy', $slot->id) }}" method="POST"
                                          onsubmit="return confirm('এই সময়-স্লট মুছে ফেলতে চান?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="cr-icon-btn delete" title="ডিলিট">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>

                            <td>{{ $slot->id }}</td>
                            <td>{{ $slot->start_label }}</td>
                            <td>{{ $slot->end_label }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="cr-empty">কোনো সময়-স্লট তৈরি করা হয়নি।</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>


{{-- ============ ROUTINE MODAL ============ --}}
<div class="cr-modal" id="routineModal" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="cr-dialog">

        <div class="cr-dialog-head">
            <h3 id="routineModalTitle">Create Class Routine</h3>
            <button type="button" class="cr-close" data-close aria-label="বন্ধ করুন">&times;</button>
        </div>

        <form id="routineForm" method="POST" action="{{ route('dashboard.class-routines.store') }}">
            @csrf

            <input type="hidden" name="_form" value="routine">
            <input type="hidden" name="_method" id="routineMethod" value="PUT" disabled>
            <input type="hidden" name="_edit_id" id="routineEditId" value="">

            <div class="cr-dialog-body">

                <div class="cr-group">
                    <label class="cr-label" for="m_year">শিক্ষাবর্ষ <span class="cr-req">*</span> :</label>

                    <select name="academic_year_id" id="m_year" class="cr-control" required>
                        <option value="">নির্বাচন করুন</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}">{{ $year->name_bn ?: $year->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="cr-group">
                    <label class="cr-label" for="m_slot">সময় <span class="cr-req">*</span> :</label>

                    <select name="time_slot_id" id="m_slot" class="cr-control" required>
                        <option value="">নির্বাচন করুন</option>
                        @foreach($timeSlots as $slot)
                            <option value="{{ $slot->id }}">{{ $slot->range_label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="cr-group">
                    <label class="cr-label" for="m_section">সাব ক্লাস <span class="cr-req">*</span> :</label>

                    <select name="section_id" id="m_section" class="cr-control" required>
                        <option value="">নির্বাচন করুন</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}">{{ $section->name_bn ?: $section->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="cr-group">
                    <label class="cr-label" for="m_subject">বিষয় <span class="cr-req">*</span> :</label>

                    <select name="subject_id" id="m_subject" class="cr-control" required>
                        <option value="">নির্বাচন করুন</option>
                    </select>
                </div>

                <div class="cr-group">
                    <label class="cr-label" for="m_teacher">শিক্ষক <span class="cr-req">*</span> :</label>

                    <select name="teacher_id" id="m_teacher" class="cr-control" required>
                        <option value="">নির্বাচন করুন</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="cr-group">
                    <label class="cr-label" for="m_remarks">মন্তব্য :</label>
                    <textarea name="remarks" id="m_remarks" class="cr-control" maxlength="1000"></textarea>
                </div>

                <div class="cr-group">
                    <label class="cr-label">দিন <span class="cr-req">*</span></label>

                    <div class="cr-days">
                        @foreach($days as $key => $label)
                            <label class="cr-day">
                                <input type="checkbox" name="days[]" value="{{ $key }}" class="js-day">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

            </div>

            <div class="cr-dialog-foot">
                <button type="submit" class="cr-btn cr-btn-blue" id="routineSubmit">সংরক্ষণ করুন</button>
                <button type="button" class="cr-btn cr-btn-light" data-close>বাতিল করুন</button>
            </div>
        </form>

    </div>
</div>


{{-- ============ TIME SLOT MODAL ============ --}}
<div class="cr-modal" id="slotModal" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="cr-dialog is-small">

        <div class="cr-dialog-head">
            <h3 id="slotModalTitle">Create Time Slots</h3>
            <button type="button" class="cr-close" data-close aria-label="বন্ধ করুন">&times;</button>
        </div>

        <form id="slotForm" method="POST" action="{{ route('dashboard.class-time-slots.store') }}">
            @csrf

            <input type="hidden" name="_form" value="slot">
            <input type="hidden" name="_method" id="slotMethod" value="PUT" disabled>
            <input type="hidden" name="_edit_id" id="slotEditId" value="">

            <div class="cr-dialog-body">

                <div class="cr-group">
                    <label class="cr-label" for="start_time">সময় হতে : <span class="cr-req">*</span></label>
                    <input type="time" name="start_time" id="start_time" class="cr-control" required>
                </div>

                <div class="cr-group">
                    <label class="cr-label" for="end_time">সময় পর্যন্ত : <span class="cr-req">*</span></label>
                    <input type="time" name="end_time" id="end_time" class="cr-control" required>
                </div>

            </div>

            <div class="cr-dialog-foot">
                <button type="submit" class="cr-btn cr-btn-blue" id="slotSubmit">সংরক্ষণ করুন</button>
                <button type="button" class="cr-btn cr-btn-light" data-close>বাতিল করুন</button>
            </div>
        </form>

    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ------------------------------------------------------------
     * TABS
     * ---------------------------------------------------------- */

    let currentTab = @json($tab);

    const tabButtons = document.querySelectorAll('.cr-tab');
    const panes = {
        routines: document.getElementById('pane-routines'),
        slots: document.getElementById('pane-slots')
    };

    function showTab(name) {
        currentTab = name;

        tabButtons.forEach(function (b) {
            b.classList.toggle('is-active', b.dataset.tab === name);
        });

        Object.keys(panes).forEach(function (key) {
            panes[key].classList.toggle('is-active', key === name);
        });

        // keep the tab in the URL, so a validation redirect comes back to the same tab
        const url = new URL(window.location.href);

        if (name === 'slots') url.searchParams.set('tab', 'slots');
        else url.searchParams.delete('tab');

        window.history.replaceState({}, '', url.toString());
    }

    tabButtons.forEach(function (b) {
        b.addEventListener('click', function () { showTab(b.dataset.tab); });
    });

    /* ------------------------------------------------------------
     * COMMON MODAL HELPERS
     * ---------------------------------------------------------- */

    const routineModal = document.getElementById('routineModal');
    const slotModal = document.getElementById('slotModal');

    function openModal(modal) {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modal) {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    [routineModal, slotModal].forEach(function (modal) {
        modal.addEventListener('click', function (event) {
            if (event.target === modal || event.target.closest('[data-close]')) {
                closeModal(modal);
            }
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;

        [routineModal, slotModal].forEach(function (modal) {
            if (modal.classList.contains('is-open')) closeModal(modal);
        });
    });

    /* ------------------------------------------------------------
     * ROUTINE MODAL
     * ---------------------------------------------------------- */

    const rForm = document.getElementById('routineForm');
    const rTitle = document.getElementById('routineModalTitle');
    const rMethod = document.getElementById('routineMethod');
    const rEditId = document.getElementById('routineEditId');

    const yearSel = document.getElementById('m_year');
    const slotSel = document.getElementById('m_slot');
    const sectionSel = document.getElementById('m_section');
    const subjectSel = document.getElementById('m_subject');
    const teacherSel = document.getElementById('m_teacher');
    const remarks = document.getElementById('m_remarks');

    const dayBoxes = Array.from(document.querySelectorAll('.js-day'));

    const R_STORE = @json(route('dashboard.class-routines.store'));
    const R_UPDATE = @json(route('dashboard.class-routines.update', ['routine' => '__ID__']));
    const SUBJECT_URL = @json(route('dashboard.class-routines.subjects'));
    const DEFAULT_YEAR = @json($selectedYearId);

    let editMode = false;

    function setEditMode(on) {
        editMode = on;

        // create => many days (days[]), edit => one day (day)
        dayBoxes.forEach(function (box) {
            box.name = on ? 'day' : 'days[]';
        });
    }

    async function loadSubjects(sectionId, selected) {
        subjectSel.innerHTML = '<option value="">নির্বাচন করুন</option>';

        if (!sectionId) return;

        try {
            const response = await fetch(SUBJECT_URL + '?section_id=' + encodeURIComponent(sectionId), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });

            if (!response.ok) throw new Error('failed');

            const list = await response.json();

            list.forEach(function (s) {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.name;
                subjectSel.appendChild(opt);
            });

            if (selected) subjectSel.value = String(selected);

        } catch (e) {
            subjectSel.innerHTML = '<option value="">বিষয় লোড করা যায়নি</option>';
        }
    }

    async function fillRoutine(data) {
        yearSel.value = data.academic_year_id || '';
        slotSel.value = data.time_slot_id || '';
        sectionSel.value = data.section_id || '';
        teacherSel.value = data.teacher_id || '';
        remarks.value = data.remarks || '';

        const days = Array.isArray(data.days) ? data.days : (data.day ? [data.day] : []);

        dayBoxes.forEach(function (box) {
            box.checked = days.includes(box.value);
        });

        await loadSubjects(data.section_id, data.subject_id);
    }

    function openRoutineCreate() {
        rForm.action = R_STORE;
        rMethod.disabled = true;
        rEditId.value = '';
        rTitle.textContent = 'Create Class Routine';

        setEditMode(false);

        rForm.reset();
        subjectSel.innerHTML = '<option value="">নির্বাচন করুন</option>';

        if (DEFAULT_YEAR) yearSel.value = String(DEFAULT_YEAR);

        openModal(routineModal);
    }

    async function openRoutineEdit(data) {
        rForm.action = R_UPDATE.replace('__ID__', encodeURIComponent(data.id));
        rMethod.disabled = false;
        rEditId.value = data.id;
        rTitle.textContent = 'Edit Class Routine';

        setEditMode(true);

        openModal(routineModal);

        await fillRoutine(data);
    }

    document.querySelectorAll('.js-edit-routine').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openRoutineEdit(JSON.parse(btn.dataset.routine));
        });
    });

    sectionSel.addEventListener('change', function () {
        loadSubjects(sectionSel.value, null);
    });

    // Edit mode: only one day
    dayBoxes.forEach(function (box) {
        box.addEventListener('change', function () {
            if (!editMode || !box.checked) return;

            dayBoxes.forEach(function (other) {
                if (other !== box) other.checked = false;
            });
        });
    });

    rForm.addEventListener('submit', function (event) {
        if (!dayBoxes.some(function (b) { return b.checked; })) {
            event.preventDefault();
            alert('কমপক্ষে একটি দিন নির্বাচন করুন।');
            return;
        }

        document.getElementById('routineSubmit').disabled = true;
    });

    /* ------------------------------------------------------------
     * TIME SLOT MODAL
     * ---------------------------------------------------------- */

    const sForm = document.getElementById('slotForm');
    const sTitle = document.getElementById('slotModalTitle');
    const sMethod = document.getElementById('slotMethod');
    const sEditId = document.getElementById('slotEditId');
    const startInput = document.getElementById('start_time');
    const endInput = document.getElementById('end_time');

    const S_STORE = @json(route('dashboard.class-time-slots.store'));
    const S_UPDATE = @json(route('dashboard.class-time-slots.update', ['slot' => '__ID__']));

    function openSlotCreate() {
        sForm.action = S_STORE;
        sMethod.disabled = true;
        sEditId.value = '';
        sTitle.textContent = 'Create Time Slots';
        startInput.value = '';
        endInput.value = '';

        openModal(slotModal);
    }

    function openSlotEdit(id, start, end) {
        sForm.action = S_UPDATE.replace('__ID__', encodeURIComponent(id));
        sMethod.disabled = false;
        sEditId.value = id;
        sTitle.textContent = 'Edit Time Slots';
        startInput.value = start;
        endInput.value = end;

        openModal(slotModal);
    }

    document.querySelectorAll('.js-edit-slot').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openSlotEdit(btn.dataset.id, btn.dataset.start, btn.dataset.end);
        });
    });

    sForm.addEventListener('submit', function () {
        document.getElementById('slotSubmit').disabled = true;
    });

    /* ------------------------------------------------------------
     * CREATE BUTTON (depends on active tab)
     * ---------------------------------------------------------- */

    document.getElementById('openCreate').addEventListener('click', function () {
        if (currentTab === 'slots') openSlotCreate();
        else openRoutineCreate();
    });

    /* ------------------------------------------------------------
     * RE-OPEN MODAL AFTER VALIDATION ERROR
     * ---------------------------------------------------------- */

    @if($errors->any() && old('_form'))
        (function () {
            const old = @json(session()->getOldInput());

            if (old._form === 'slot') {
                showTab('slots');

                if (old._edit_id) {
                    openSlotEdit(old._edit_id, old.start_time || '', old.end_time || '');
                } else {
                    openSlotCreate();
                    startInput.value = old.start_time || '';
                    endInput.value = old.end_time || '';
                }

                return;
            }

            showTab('routines');

            const data = {
                academic_year_id: old.academic_year_id,
                time_slot_id: old.time_slot_id,
                section_id: old.section_id,
                subject_id: old.subject_id,
                teacher_id: old.teacher_id,
                remarks: old.remarks
            };

            if (old._edit_id) {
                data.id = old._edit_id;
                data.days = old.day ? [old.day] : [];
                openRoutineEdit(data);
            } else {
                data.days = old.days || [];
                openRoutineCreate();
                fillRoutine(data);
            }
        })();
    @endif

});
</script>

@endsection