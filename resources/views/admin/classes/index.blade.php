@extends('layouts.admin')
@section('title', 'ক্লাস ব্যবস্থাপনা')
@section('page')

<link href="https://fonts.googleapis.com/css2?family=Tiro+Bangla&family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --ink:      #0f172a;
    --ink-2:    #334155;
    --ink-3:    #64748b;
    --surface:  #f8fafc;
    --card:     #ffffff;
    --border:   #e2e8f0;
    --border-2: #cbd5e1;
    --primary:  #2563eb;
    --primary-l:#eff6ff;
    --primary-m:#bfdbfe;
    --success:  #059669;
    --success-l:#ecfdf5;
    --danger:   #dc2626;
    --danger-l: #fef2f2;
    --warn:     #d97706;
    --warn-l:   #fffbeb;
    --purple:   #7c3aed;
    --purple-l: #f5f3ff;
    --pink:     #be185d;
    --pink-l:   #fdf2f8;
    --radius:   12px;
    --radius-lg:20px;
    --shadow:   0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.06);
    --shadow-lg:0 8px 32px rgba(0,0,0,.12);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Lexend', 'Tiro Bangla', sans-serif; background: var(--surface); color: var(--ink); }

.cl-page { padding: 32px 28px; max-width: 1300px; margin: 0 auto; min-height: 100vh; }

/* ── Top bar ── */
.cl-topbar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 28px; gap: 16px; flex-wrap: wrap; }
.cl-topbar__title { display: flex; align-items: center; gap: 14px; }
.cl-topbar__icon { width: 52px; height: 52px; background: linear-gradient(135deg, #7c3aed, #a855f7); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 22px; box-shadow: 0 4px 14px rgba(124,58,237,.35); flex-shrink: 0; }
.cl-topbar__text h1 { font-size: 1.4rem; font-weight: 700; color: var(--ink); letter-spacing: -.02em; line-height: 1.2; }
.cl-topbar__text p { font-size: 0.82rem; color: var(--ink-3); margin-top: 2px; font-weight: 300; }

.btn-primary { display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #7c3aed, #a855f7); color: white; border: none; border-radius: var(--radius); padding: 12px 22px; font-size: 0.88rem; font-weight: 600; font-family: inherit; cursor: pointer; white-space: nowrap; box-shadow: 0 3px 10px rgba(124,58,237,.3); transition: transform .15s, box-shadow .15s; }
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(124,58,237,.4); }

/* ── Stats ── */
.cl-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 28px; }
.stat-card { background: var(--card); width: 100%; border: 1px solid var(--border); border-radius: var(--radius); padding: 18px 20px; display: flex; align-items: center; gap: 14px; box-shadow: var(--shadow); transition: transform .2s; }
.stat-card:hover { transform: translateY(-2px); }
.stat-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.si-purple { background: var(--purple-l); color: var(--purple); }
.si-green  { background: var(--success-l); color: var(--success); }
.si-amber  { background: var(--warn-l); color: var(--warn); }
.si-red    { background: var(--danger-l); color: var(--danger); }
.stat-label { font-size: 0.75rem; color: var(--ink-3); font-weight: 400; }
.stat-value { font-size: 1.55rem; font-weight: 700; color: var(--ink); line-height: 1.1; }

/* ── Grid ── */
.cl-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(270px, 1fr)); gap: 20px; }

/* ── Class card ── */
.cl-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow); transition: transform .2s, box-shadow .2s; cursor: pointer; display: flex; flex-direction: column; }
.cl-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }

.cl-card__head { padding: 24px 20px 20px; color: white; text-align: center; position: relative; overflow: hidden; flex-shrink: 0; }
.cl-card__head::after  { content: ''; position: absolute; bottom: -20px; right: -20px; width: 80px; height: 80px; background: rgba(255,255,255,.08); border-radius: 50%; }
.cl-card__head::before { content: ''; position: absolute; top: -10px; left: -10px; width: 60px; height: 60px; background: rgba(255,255,255,.06); border-radius: 50%; }
.cl-card__name-bn { font-size: 1.2rem; font-weight: 700; position: relative; z-index: 1; }
.cl-card__name-en { font-size: 0.85rem; opacity: .9; font-weight: 400; position: relative; z-index: 1; margin-top: 5px; }

.head-preschool        { background: linear-gradient(135deg, #be185d, #f472b6); }
.head-primary          { background: linear-gradient(135deg, #059669, #34d399); }
.head-middle           { background: linear-gradient(135deg, #d97706, #fbbf24); }
.head-high             { background: linear-gradient(135deg, #1d4ed8, #60a5fa); }
.head-higher_secondary { background: linear-gradient(135deg, #7c3aed, #c084fc); }
.head-default          { background: linear-gradient(135deg, #475569, #94a3b8); }

.cl-card__body { padding: 14px 18px; flex: 1; }
.cl-meta { display: flex; align-items: center; justify-content: space-between; padding: 7px 0; font-size: 0.82rem; border-bottom: 1px solid #f1f5f9; gap: 8px; }
.cl-meta:last-of-type { border-bottom: none; }
.cl-meta__label { color: var(--ink-3); font-weight: 400; white-space: nowrap; flex-shrink: 0; }
.cl-meta__val { font-weight: 600; color: var(--ink-2); text-align: right; font-size: 0.8rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 160px; }

.lvl { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 600; }
.lvl-preschool        { background: var(--pink-l);    color: var(--pink); }
.lvl-primary          { background: var(--success-l); color: var(--success); }
.lvl-middle           { background: var(--warn-l);    color: var(--warn); }
.lvl-high             { background: var(--primary-l); color: var(--primary); }
.lvl-higher_secondary { background: var(--purple-l);  color: var(--purple); }

.badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 600; }
.badge-active   { background: var(--success-l); color: var(--success); }
.badge-inactive { background: #f1f5f9; color: var(--ink-3); }

.cl-card__foot { padding: 12px 18px; border-top: 1px solid var(--border); background: #fafbfc; display: flex; gap: 8px; flex-shrink: 0; }
.btn-card { height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer; font-size: 0.78rem; font-family: inherit; font-weight: 600; transition: all .15s; border: 1px solid; padding: 0 12px; flex: 1; background: none; }
.btn-card.edit { color: var(--primary); background: var(--primary-l); border-color: var(--primary-m); }
.btn-card.edit:hover { background: #dbeafe; }
.btn-card.del  { color: var(--danger); background: var(--danger-l); border-color: #fecaca; }
.btn-card.del:hover { background: #fee2e2; }

/* ── Empty state ── */
.empty-state { grid-column: 1/-1; text-align: center; padding: 60px 24px; color: var(--ink-3); }
.empty-state i { font-size: 42px; opacity: .2; margin-bottom: 14px; display: block; }

/* ═══════════════════ MODAL ═══════════════════ */
.cl-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,.6);
    backdrop-filter: blur(4px);
    z-index: 99999;
    align-items: center;
    justify-content: center;
    padding: 20px;
    overflow-y: auto;
}
.cl-modal-overlay.open { display: flex; }

.cl-modal-box {
    background: #fff;
    border-radius: var(--radius-lg);
    width: 100%;
    max-width: 560px;
    box-shadow: 0 20px 60px rgba(0,0,0,.25);
    animation: popIn .25s cubic-bezier(.34,1.56,.64,1) both;
    overflow: hidden;
    margin: auto;
    position: relative;
}
@keyframes popIn { from { opacity: 0; transform: scale(.92) translateY(20px); } to { opacity: 1; transform: scale(1) translateY(0); } }

.cl-modal-head { background: linear-gradient(135deg, #7c3aed, #a855f7); padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; }
.cl-modal-head h5 { color: white; font-size: 1rem; font-weight: 600; margin: 0; font-family: 'Lexend', sans-serif; }
.cl-modal-close { width: 32px; height: 32px; background: rgba(255,255,255,.15); border: none; border-radius: 8px; color: white; cursor: pointer; font-size: 20px; line-height: 1; display: flex; align-items: center; justify-content: center; transition: background .15s; flex-shrink: 0; }
.cl-modal-close:hover { background: rgba(255,255,255,.3); }

.cl-modal-body { padding: 24px; max-height: 65vh; overflow-y: auto; }
.cl-modal-foot { padding: 16px 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px; background: #f8fafc; }

/* ── Form elements inside modal ── */
.cl-form-row   { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.cl-form-group { margin-bottom: 16px; }
.cl-form-group:last-child { margin-bottom: 0; }
.cl-form-label { display: block; font-size: 0.75rem; font-weight: 600; color: #334155; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 7px; font-family: 'Lexend', sans-serif; }
.cl-req { color: #dc2626; }
.cl-form-input, .cl-form-select {
    width: 100%;
    border: 1.5px solid #cbd5e1;
    border-radius: 9px;
    padding: 10px 13px;
    font-size: 0.875rem;
    font-family: 'Lexend', 'Tiro Bangla', sans-serif;
    color: #0f172a;
    background: #fff;
    transition: border-color .15s, box-shadow .15s;
    appearance: none;
    -webkit-appearance: none;
    box-sizing: border-box;
}
.cl-form-input:focus, .cl-form-select:focus {
    outline: none;
    border-color: #7c3aed;
    box-shadow: 0 0 0 3px rgba(124,58,237,.12);
}
.cl-form-select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2364748b' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 36px;
    cursor: pointer;
}
.cl-sep { font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .08em; padding-bottom: 8px; border-bottom: 1px solid #e2e8f0; margin-bottom: 16px; margin-top: 4px; }

.cl-btn-ghost { background: none; border: 1.5px solid #cbd5e1; border-radius: 9px; padding: 10px 22px; font-size: 0.875rem; font-family: 'Lexend', sans-serif; font-weight: 500; color: #64748b; cursor: pointer; transition: background .15s; }
.cl-btn-ghost:hover { background: #e2e8f0; }
.cl-btn-save { background: linear-gradient(135deg, #7c3aed, #a855f7); color: white; border: none; border-radius: 9px; padding: 10px 26px; font-size: 0.875rem; font-family: 'Lexend', sans-serif; font-weight: 600; cursor: pointer; box-shadow: 0 3px 10px rgba(124,58,237,.28); transition: transform .15s, box-shadow .15s; }
.cl-btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(124,58,237,.38); }

@media (max-width: 640px) {
    .cl-page { padding: 20px 14px; }
    .cl-form-row { grid-template-columns: 1fr; }
    .cl-topbar { flex-direction: column; align-items: flex-start; }
}
</style>

<div class="cl-page">

    {{-- Top bar --}}
    <div class="cl-topbar">
        <div class="cl-topbar__title">
            <div class="cl-topbar__icon"><i class="fas fa-chalkboard"></i></div>
            <div class="cl-topbar__text">
                <h1>ক্লাস ব্যবস্থাপনা</h1>
                <p>সকল ক্লাস পরিচালনা ও আপডেট করুন</p>
            </div>
        </div>
        <button class="btn-primary" onclick="openClassModal()">
            <i class="fas fa-plus"></i> নতুন ক্লাস যোগ করুন
        </button>
    </div>

    {{-- Stats --}}
    <div class="cl-stats">
        <div class="stat-card">
            <div class="stat-icon si-purple"><i class="fas fa-chalkboard"></i></div>
            <div><div class="stat-label">মোট ক্লাস</div><div class="stat-value">{{ $classes->count() }}</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon si-green"><i class="fas fa-check-circle"></i></div>
            <div><div class="stat-label">সক্রিয়</div><div class="stat-value">{{ $classes->where('status','active')->count() }}</div></div>
        </div>

        <div class="stat-card">
            <div class="stat-icon si-red"><i class="fas fa-ban"></i></div>
            <div><div class="stat-label">নিষ্ক্রিয়</div><div class="stat-value">{{ $classes->where('status','inactive')->count() }}</div></div>
        </div>
    </div>

{{-- ═══════════════ TABLE VIEW ═══════════════ --}}
<div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">

    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-table text-primary me-2"></i>
            ক্লাস তালিকা
        </h5>
    </div>

    <div class="table-responsive">

        <table class="table align-middle mb-0">

            <thead style="background:#f8fafc;">
                <tr>
                    <th width="70">#</th>
                    <th>বাংলা নাম</th>
                    <th>English Name</th>
                    <th width="130">স্ট্যাটাস</th>
                    <th width="160" class="text-center">অ্যাকশন</th>
                </tr>
            </thead>

            <tbody>

                @forelse($classes as $key => $class)

                <tr>

                    {{-- Serial --}}
                    <td class="fw-semibold text-muted">
                        {{ $key + 1 }}
                    </td>

                    {{-- Bangla Name --}}
                    <td>
                        <div class="fw-bold text-dark">
                            {{ $class->name_bn }}
                        </div>
                    </td>

                    {{-- English Name --}}
                    <td>
                        <span class="text-secondary">
                            {{ $class->name }}
                        </span>
                    </td>

                    {{-- Status --}}
                    <td>

                        @if($class->status == 'active')

                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                <i class="fas fa-check-circle me-1"></i>
                                সক্রিয়
                            </span>

                        @else

                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                                <i class="fas fa-times-circle me-1"></i>
                                নিষ্ক্রিয়
                            </span>

                        @endif

                    </td>

                    {{-- Action --}}
                    <td class="text-center">

                        <div class="d-flex justify-content-center gap-2">

                            <button
                                class="btn btn-sm btn-primary"
                                onclick="editClass({{ $class->id }})">

                                <i class="fas fa-edit"></i>
                            </button>

                            <form
                                action="{{ route('dashboard.classes.destroy', $class->id) }}"
                                method="POST"
                                onsubmit="return confirm('এই ক্লাসটি মুছে ফেলতে চান?')">

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn btn-sm btn-danger">

                                    <i class="fas fa-trash"></i>
                                </button>

                            </form>

                        </div>

                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">

                        <i class="fas fa-inbox fa-2x mb-3 d-block opacity-50"></i>

                        কোনো ক্লাস পাওয়া যায়নি।

                    </td>
                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>


</div>

{{-- ═══════════════════ MODAL ═══════════════════ --}}
<div id="classModal" class="cl-modal-overlay">
    <div class="cl-modal-box">
        <form id="classForm" action="{{ route('dashboard.classes.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="classFormMethod" value="POST">
            <input type="hidden" name="id" id="classId">

            <div class="cl-modal-head">
                <h5 id="classModalTitle"><i class="fas fa-chalkboard" style="margin-right:8px;"></i>নতুন ক্লাস যোগ করুন</h5>
                <button type="button" class="cl-modal-close" onclick="closeClassModal()">&#215;</button>
            </div>

            <div class="cl-modal-body">

                <div class="cl-form-row">
                    <div class="cl-form-group">
                        <label class="cl-form-label">নাম (ইংরেজি) <span class="cl-req">*</span></label>
                        <input type="text" name="name" id="class_name" class="cl-form-input" placeholder="Class 1" required>
                    </div>
                    <div class="cl-form-group">
                        <label class="cl-form-label">নাম (বাংলা) <span class="cl-req">*</span></label>
                        <input type="text" name="name_bn" id="class_name_bn" class="cl-form-input" placeholder="১ম শ্রেণী" required>
                    </div>
                </div>

                <div class="cl-form-group">

                    <div class="cl-form-group">
                        <label class="cl-form-label">স্ট্যাটাস</label>
                        <select name="status" id="class_status" class="cl-form-select">
                            <option value="active">✅ সক্রিয়</option>
                            <option value="inactive">🚫 নিষ্ক্রিয়</option>
                        </select>
                    </div>
                </div>

            </div>

            <div class="cl-modal-foot">
                <button type="button" class="cl-btn-ghost" onclick="closeClassModal()">বাতিল</button>
                <button type="submit" class="cl-btn-save"><i class="fas fa-save" style="margin-right:6px;"></i>সংরক্ষণ করুন</button>
            </div>
        </form>
    </div>
</div>

<script>
function openClassModal() {
    document.getElementById('classModal').classList.add('open');
}

function closeClassModal() {
    document.getElementById('classModal').classList.remove('open');

    // Reset form
    document.getElementById('classForm').reset();
    document.getElementById('classId').value = '';
    document.getElementById('classFormMethod').value = 'POST';
    document.getElementById('classForm').action = `{{ route('dashboard.classes.store') }}`;

    document.getElementById('classModalTitle').innerHTML =
        '<i class="fas fa-chalkboard" style="margin-right:8px;"></i>নতুন ক্লাস যোগ করুন';
}

document.getElementById('classModal').addEventListener('click', function(e) {
    if (e.target === this) closeClassModal();
});

function editClass(id) {

    const url = @json(url('/dashboard/classes'));

    fetch(`${url}/${id}/edit`)
        .then(response => {
            if (!response.ok) {
                throw new Error('ক্লাস তথ্য পাওয়া যায়নি।');
            }

            return response.json();
        })
        .then(data => {

            document.getElementById('classId').value = data.id;

            document.getElementById('class_name').value = data.name ?? '';

            document.getElementById('class_name_bn').value = data.name_bn ?? '';

            document.getElementById('class_status').value =
                data.status ?? 'active';

            document.getElementById('classModalTitle').innerHTML =
                '<i class="fas fa-pen" style="margin-right:8px;"></i>ক্লাস এডিট করুন';

            document.getElementById('classFormMethod').value = 'PUT';

            document.getElementById('classForm').action =
                `${url}/${id}`;

            openClassModal();
        })
        .catch(error => {
            console.error(error);
            alert('ক্লাসের তথ্য লোড করা যায়নি।');
        });
}
</script>

@endsection