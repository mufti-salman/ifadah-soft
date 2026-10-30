@extends('layouts.admin')

@section('title', 'শিক্ষাবর্ষ ব্যবস্থাপনা')

@section('page')

<link href="https://fonts.googleapis.com/css2?family=Tiro+Bangla&family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --ink: #0f172a;
    --ink-2: #334155;
    --ink-3: #64748b;
    --surface: #f8fafc;
    --card: #ffffff;
    --border: #e2e8f0;
    --border-2: #cbd5e1;
    --primary: #2563eb;
    --primary-d: #1d4ed8;
    --primary-l: #eff6ff;
    --primary-m: #bfdbfe;
    --success: #059669;
    --success-l: #ecfdf5;
    --danger: #dc2626;
    --danger-l: #fef2f2;
    --radius: 12px;
    --radius-lg: 20px;
    --shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.06);
    --shadow-lg: 0 8px 32px rgba(0,0,0,.12);
}

*, *::before, *::after {
    box-sizing: border-box;
}

.ay-page {
    padding: 32px 28px;
    max-width: 1200px;
    margin: 0 auto;
    min-height: 100vh;
    font-family: 'Lexend', 'Tiro Bangla', sans-serif;
    color: var(--ink);
}

.ay-topbar {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 28px;
    gap: 16px;
    flex-wrap: wrap;
}

.ay-topbar__title {
    display: flex;
    align-items: center;
    gap: 14px;
}

.ay-topbar__icon {
    width: 52px;
    height: 52px;
    background: linear-gradient(135deg, #1d4ed8, #3b82f6);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 22px;
    box-shadow: 0 4px 14px rgba(37,99,235,.35);
    flex-shrink: 0;
}

.ay-topbar__text h1 {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--ink);
    line-height: 1.5;
}

.ay-topbar__text p {
    font-size: .82rem;
    color: var(--ink-3);
    margin-top: 2px;
    font-weight: 300;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    color: white;
    border: none;
    border-radius: var(--radius);
    padding: 12px 22px;
    font-size: .88rem;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    white-space: nowrap;
    box-shadow: 0 3px 10px rgba(37,99,235,.3);
    transition: .15s;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(37,99,235,.4);
}

.ay-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}

.stat-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: var(--shadow);
    transition: transform .2s;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.stat-icon.blue {
    background: var(--primary-l);
    color: var(--primary);
}

.stat-icon.green {
    background: var(--success-l);
    color: var(--success);
}

.stat-icon.red {
    background: var(--danger-l);
    color: var(--danger);
}

.stat-icon.amber {
    background: #fffbeb;
    color: #d97706;
}

.stat-label {
    font-size: .75rem;
    color: var(--ink-3);
    font-weight: 400;
}

.stat-value {
    font-size: 1.55rem;
    font-weight: 700;
    color: var(--ink);
    line-height: 1.4;
}

.ay-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.ay-table-wrap {
    overflow-x: auto;
}

table.ay-table {
    width: 100%;
    border-collapse: collapse;
}

table.ay-table thead th {
    background: #f1f5f9;
    padding: 14px 18px;
    text-align: left;
    font-size: .78rem;
    font-weight: 600;
    color: var(--ink-3);
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
}

table.ay-table tbody tr {
    transition: background .15s;
}

table.ay-table tbody tr:hover {
    background: #f8fafc;
}

table.ay-table tbody td {
    padding: 15px 18px;
    font-size: .875rem;
    color: var(--ink-2);
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
}

table.ay-table tbody tr:last-child td {
    border-bottom: none;
}

.serial {
    width: 32px;
    height: 32px;
    background: #f1f5f9;
    border-radius: 9px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .78rem;
    font-weight: 700;
    color: var(--ink-3);
}

.year-name {
    font-weight: 600;
    color: var(--ink);
}

.year-name-bn {
    color: var(--ink-3);
    font-size: .82rem;
    margin-top: 3px;
}

.arabic-name {
    direction: rtl;
    text-align: right;
    font-size: .95rem;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: .75rem;
    font-weight: 600;
    white-space: nowrap;
}

.badge-current {
    background: var(--success-l);
    color: var(--success);
}

.badge-active {
    background: var(--primary-l);
    color: var(--primary);
}

.badge-inactive {
    background: #f1f5f9;
    color: var(--ink-3);
}

.actions-cell {
    display: flex;
    gap: 7px;
    align-items: center;
}

.btn-icon {
    width: 35px;
    height: 35px;
    border: 1px solid var(--border);
    background: var(--card);
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 14px;
    transition: all .15s;
}

.btn-icon.edit {
    color: var(--primary);
}

.btn-icon.edit:hover {
    background: var(--primary-l);
    border-color: var(--primary-m);
}

.btn-icon.del {
    color: var(--danger);
}

.btn-icon.del:hover {
    background: var(--danger-l);
    border-color: #fecaca;
}

.empty-state {
    text-align: center;
    padding: 60px 24px;
    color: var(--ink-3);
}

.empty-state i {
    font-size: 42px;
    opacity: .25;
    margin-bottom: 14px;
    display: block;
}

.empty-state p {
    font-size: .9rem;
}

.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,.55);
    backdrop-filter: blur(4px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.modal-overlay.open {
    display: flex;
}

.modal-box {
    background: var(--card);
    border-radius: var(--radius-lg);
    width: 100%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: var(--shadow-lg);
    animation: popIn .25s ease both;
}

@keyframes popIn {
    from {
        opacity: 0;
        transform: scale(.96) translateY(12px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.modal-head {
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    padding: 20px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-head h5 {
    color: white;
    font-size: 1rem;
    font-weight: 600;
}

.modal-close {
    width: 32px;
    height: 32px;
    background: rgba(255,255,255,.15);
    border: none;
    border-radius: 8px;
    color: white;
    cursor: pointer;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    background: rgba(255,255,255,.3);
}

.modal-body {
    padding: 26px 24px;
}

.modal-foot {
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    background: #f8fafc;
}

.form-group {
    margin-bottom: 19px;
}

.form-label {
    display: block;
    font-size: .82rem;
    font-weight: 600;
    color: var(--ink-2);
    margin-bottom: 8px;
}

.req {
    color: var(--danger);
}

.form-input,
.form-select {
    width: 100%;
    border: 1.5px solid var(--border-2);
    border-radius: 9px;
    padding: 11px 13px;
    font-size: .875rem;
    font-family: inherit;
    color: var(--ink);
    background: var(--card);
    transition: border-color .15s, box-shadow .15s;
}

.form-input:focus,
.form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37,99,235,.12);
}

.checkbox-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 13px 14px;
    background: var(--primary-l);
    border: 1.5px solid var(--primary-m);
    border-radius: 9px;
    cursor: pointer;
}

.checkbox-row input[type="checkbox"] {
    width: 17px;
    height: 17px;
    accent-color: var(--primary);
    cursor: pointer;
}

.checkbox-row span {
    font-size: .85rem;
    font-weight: 500;
    color: var(--primary-d);
}

.btn-ghost {
    background: none;
    border: 1.5px solid var(--border-2);
    border-radius: 9px;
    padding: 10px 22px;
    font-size: .875rem;
    font-family: inherit;
    font-weight: 500;
    color: var(--ink-3);
    cursor: pointer;
}

.btn-ghost:hover {
    background: var(--border);
}

.btn-save {
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    color: white;
    border: none;
    border-radius: 9px;
    padding: 10px 26px;
    font-size: .875rem;
    font-family: inherit;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 3px 10px rgba(37,99,235,.28);
}

.btn-save:hover {
    box-shadow: 0 6px 16px rgba(37,99,235,.38);
}

@media (max-width: 640px) {
    .ay-page {
        padding: 20px 14px;
    }

    .ay-topbar {
        flex-direction: column;
        align-items: flex-start;
    }

    .ay-topbar__text h1 {
        font-size: 1.15rem;
    }

    .modal-body {
        padding: 20px 16px;
    }

    .modal-head,
    .modal-foot {
        padding-left: 16px;
        padding-right: 16px;
    }
}
</style>

<div class="ay-page">

    {{-- Page Header --}}
    <div class="ay-topbar">
        <div class="ay-topbar__title">
            <div class="ay-topbar__icon">
                <i class="fas fa-calendar-alt"></i>
            </div>

            <div class="ay-topbar__text">
                <h1>শিক্ষাবর্ষ ব্যবস্থাপনা</h1>
                <p>প্রতিষ্ঠানের শিক্ষাবর্ষ তৈরি, সম্পাদনা ও পরিচালনা করুন</p>
            </div>
        </div>

        <button type="button" class="btn-primary" onclick="openModal()">
            <i class="fas fa-plus"></i>
            নতুন শিক্ষাবর্ষ যোগ করুন
        </button>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div style="padding:12px 16px;margin-bottom:18px;border-radius:10px;background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Messages --}}
    @if($errors->any())
        <div style="padding:14px 18px;margin-bottom:18px;border-radius:10px;background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;">
            <strong>ফর্মে কিছু সমস্যা আছে:</strong>

            <ul style="margin:8px 0 0 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Statistics --}}
    <div class="ay-stats">

        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <div class="stat-label">মোট শিক্ষাবর্ষ</div>
                <div class="stat-value">{{ $academicYears->count() }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <div class="stat-label">সক্রিয়</div>
                <div class="stat-value">
                    {{ $academicYears->where('status', 'active')->count() }}
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon amber">
                <i class="fas fa-star"></i>
            </div>
            <div>
                <div class="stat-label">বর্তমান শিক্ষাবর্ষ</div>
                <div class="stat-value">
                    {{ $academicYears->where('is_current', true)->count() }}
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon red">
                <i class="fas fa-ban"></i>
            </div>
            <div>
                <div class="stat-label">নিষ্ক্রিয়</div>
                <div class="stat-value">
                    {{ $academicYears->where('status', 'inactive')->count() }}
                </div>
            </div>
        </div>

    </div>

    {{-- Academic Year Table --}}
    <div class="ay-card">

        <div style="padding:20px 22px;border-bottom:1px solid var(--border);">
            <h3 style="font-size:1rem;font-weight:700;">
                <i class="fas fa-calendar-check" style="color:var(--primary);margin-right:7px;"></i>
                শিক্ষাবর্ষের তালিকা
            </h3>

            <p style="font-size:.8rem;color:var(--ink-3);margin-top:5px;">
                শিক্ষাবর্ষের বাংলা, ইংরেজি ও আরবি নাম এবং বর্তমান অবস্থা দেখুন।
            </p>
        </div>

        <div class="ay-table-wrap">
            <table class="ay-table">
                <thead>
                    <tr>
                        <th>আইডি</th>
                        <th>Session Name</th>
                        <th>ইংরেজী</th>
                        <th>আরবি</th>
                        <th>স্ট্যাটাস</th>
                        <th>অ্যাকশন</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($academicYears as $year)
                        <tr>
                            {{-- Database ID --}}
                            <td>
                                <span class="serial">{{ $year->id }}</span>
                            </td>

                            {{-- Session Name: Bengali name --}}
                            <td>
                                <div class="year-name">
                                    {{ $year->name_bn ? : '' }}
                                </div>
                            </td>

                            {{-- English Name --}}
                            <td>
                                {{ filled($year->name) && $year->name !== $year->name_bn
                                    ? $year->name
                                    : '—' }}
                            </td>

                            {{-- Arabic Name --}}
                            <td>
                                <div class="arabic-name">
                                    {{ $year->name_ar ?: '—' }}
                                </div>
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($year->status === 'active')
                                    <span class="badge badge-active">
                                        <i class="fas fa-circle" style="font-size:7px;"></i>
                                        সক্রিয়
                                    </span>
                                @else
                                    <span class="badge badge-inactive">
                                        <i class="fas fa-circle" style="font-size:7px;"></i>
                                        নিষ্ক্রিয়
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="actions-cell">

                                    <button
                                        type="button"
                                        class="btn-icon edit"
                                        onclick="editYear({{ $year->id }})"
                                        title="সম্পাদনা"
                                        aria-label="শিক্ষাবর্ষ সম্পাদনা করুন"
                                    >
                                        <i class="fas fa-pen"></i>
                                    </button>

                                    <form
                                        action="{{ route('dashboard.academic-years.destroy', $year->id) }}"
                                        method="POST"
                                        style="display:inline;"
                                        onsubmit="return confirm('এই শিক্ষাবর্ষটি মুছে ফেলতে চান?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            class="btn-icon del"
                                            type="submit"
                                            title="মুছে ফেলুন"
                                            aria-label="শিক্ষাবর্ষ মুছে ফেলুন"
                                        >
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-calendar-times"></i>
                                    <p>কোনো শিক্ষাবর্ষ এখনো যোগ করা হয়নি।</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>


{{-- Academic Year Modal --}}
<div id="yearModal" class="modal-overlay">

    <div class="modal-box">

        <form
            id="yearForm"
            action="{{ route('dashboard.academic-years.store') }}"
            method="POST"
        >
            @csrf

            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="modal-head">
                <h5 id="modalTitle">
                    <i class="fas fa-calendar-plus" style="margin-right:8px;"></i>
                    নতুন শিক্ষাবর্ষ যোগ করুন
                </h5>

                <button
                    type="button"
                    class="modal-close"
                    onclick="closeModal()"
                    aria-label="বন্ধ করুন"
                >
                    &times;
                </button>
            </div>

            <div class="modal-body">

                {{-- English Name --}}
                <div class="form-group">
                    <label for="name" class="form-label">
                        ইংরেজি নাম
                        <!-- <span class="req">*</span> -->
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-input"
                        placeholder="Academic Year 2026"
                        maxlength="100"
                        
                    >
                </div>

                {{-- Bengali Session Name --}}
                <div class="form-group">
                    <label for="name_bn" class="form-label">
                        Session Name (বাংলা)
                    </label>

                    <input
                        type="text"
                        name="name_bn"
                        id="name_bn"
                        class="form-input"
                        placeholder="২০২৬ শিক্ষাবর্ষ"
                        maxlength="100"
                    >
                </div>

                {{-- Arabic Name --}}
                <div class="form-group">
                    <label for="name_ar" class="form-label">
                        আরবি নাম
                    </label>

                    <input
                        type="text"
                        name="name_ar"
                        id="name_ar"
                        class="form-input"
                        placeholder="العام الدراسي ٢٠٢٦"
                        maxlength="100"
                        dir="rtl"
                    >
                </div>

                {{-- Status --}}
                <div class="form-group">
                    <label for="status" class="form-label">
                        স্ট্যাটাস
                    </label>

                    <select name="status" id="status" class="form-select">
                        <option value="active">সক্রিয়</option>
                        <option value="inactive">নিষ্ক্রিয়</option>
                    </select>
                </div>

                {{-- Current Academic Year --}}
                <div class="form-group" style="margin-bottom:0;">
                    <label class="checkbox-row" for="is_current">
                        <input
                            type="checkbox"
                            name="is_current"
                            id="is_current"
                            value="1"
                        >

                        <span>এটিকে বর্তমান শিক্ষাবর্ষ হিসেবে সেট করুন</span>
                    </label>
                </div>

            </div>

            <div class="modal-foot">
                <button
                    type="button"
                    class="btn-ghost"
                    onclick="closeModal()"
                >
                    বাতিল
                </button>

                <button type="submit" class="btn-save" id="saveButton">
                    <i class="fas fa-save" style="margin-right:6px;"></i>
                    সংরক্ষণ করুন
                </button>
            </div>

        </form>

    </div>

</div>


<script>
(function () {
    const modal = document.getElementById('yearModal');
    const form = document.getElementById('yearForm');
    const saveButton = document.getElementById('saveButton');

    const storeUrl = @json(route('dashboard.academic-years.store'));
    const updateUrlTemplate = @json(url('/dashboard/academic-years'));

    function field(id) {
        return document.getElementById(id);
    }

    function setModalTitle(isEdit) {
        field('modalTitle').innerHTML = isEdit
            ? '<i class="fas fa-pen" style="margin-right:8px;"></i>শিক্ষাবর্ষ সম্পাদনা করুন'
            : '<i class="fas fa-calendar-plus" style="margin-right:8px;"></i>নতুন শিক্ষাবর্ষ যোগ করুন';
    }

    function resetYearForm() {
        form.reset();

        field('formMethod').value = 'POST';
        form.action = storeUrl;

        field('name').value = '';
        field('name_bn').value = '';
        field('name_ar').value = '';
        field('status').value = 'active';
        field('is_current').checked = false;

        saveButton.disabled = false;
        saveButton.innerHTML =
            '<i class="fas fa-save" style="margin-right:6px;"></i>সংরক্ষণ করুন';

        setModalTitle(false);
    }

    window.openModal = function () {
        resetYearForm();
        modal.classList.add('open');
    };

    window.closeModal = function () {
        modal.classList.remove('open');
    };

    window.editYear = async function (id) {
        try {
            const response = await fetch(
                `${updateUrlTemplate}/${id}/edit`,
                {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                }
            );

            if (!response.ok) {
                throw new Error('শিক্ষাবর্ষের তথ্য লোড করা যায়নি।');
            }

            const data = await response.json();

            /*
             * Reset before assigning values.
             * Do not call openModal() here, because it resets the form.
             */
            resetYearForm();

            field('name').value = data.name ?? '';
            field('name_bn').value = data.name_bn ?? '';
            field('name_ar').value = data.name_ar ?? '';
            field('status').value = data.status ?? 'active';
            field('is_current').checked =
                data.is_current === true ||
                data.is_current === 1 ||
                data.is_current === '1';

            field('formMethod').value = 'PUT';
            form.action = `${updateUrlTemplate}/${id}`;

            setModalTitle(true);
            modal.classList.add('open');

        } catch (error) {
            alert(error.message || 'একটি সমস্যা হয়েছে।');
        }
    };

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });
})();
</script>

@endsection