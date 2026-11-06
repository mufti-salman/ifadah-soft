@extends('layouts.admin')

@section('title', 'শিক্ষক তালিকা')

@section('page')

<style>
    .teacher-page { padding: 20px; }

    .teacher-header {
        display: flex; justify-content: space-between; align-items: center;
        gap: 15px; margin-bottom: 20px;
    }
    .teacher-title h3 { margin: 0; font-size: 24px; font-weight: 700; }
    .teacher-title p { margin: 5px 0 0; color: #777; font-size: 14px; }
    .teacher-create-btn { border: 0; padding: 11px 18px; border-radius: 8px; font-weight: 600; cursor: pointer; }

    .teacher-filter-card {
        background: #fff; border: 1px solid #eee; border-radius: 10px;
        padding: 15px; margin-bottom: 20px;
    }
    .teacher-table-card {
        background: #fff; border: 1px solid #eee; border-radius: 10px; overflow: hidden;
    }
    .teacher-table { width: 100%; border-collapse: collapse; }
    .teacher-table th, .teacher-table td {
        padding: 13px 15px; border-bottom: 1px solid #eee;
        vertical-align: middle; white-space: nowrap;
    }
    .teacher-table th { font-size: 13px; font-weight: 700; background: #fafafa; }
    .teacher-table td { font-size: 14px; }

    .teacher-photo {
        width: 42px; height: 42px; object-fit: cover;
        border-radius: 50%; border: 1px solid #ddd;
    }
    .teacher-name { font-weight: 600; }
    .teacher-sub { color: #888; font-size: 12px; margin-top: 3px; }
    .teacher-code { font-weight: 700; }

    .status-badge {
        display: inline-flex; padding: 5px 10px; border-radius: 20px;
        font-size: 12px; font-weight: 600;
    }
    .status-active { background: #e9f8ef; color: #198754; }
    .status-inactive { background: #fceaea; color: #dc3545; }

    .teacher-actions { display: flex; gap: 5px; }
    .teacher-action {
        width: 34px; height: 34px; border: 1px solid #ddd; border-radius: 7px;
        display: inline-flex; justify-content: center; align-items: center;
        text-decoration: none; background: #fff; cursor: pointer;
    }

    /* Modal */
    .teacher-modal {
        position: fixed; inset: 0; background: rgba(0, 0, 0, .55); z-index: 99999;
        display: none; align-items: center; justify-content: center; padding: 20px;
    }
    .teacher-modal.show { display: flex; }
    .teacher-modal-box {
        width: 100%; max-width: 1050px; max-height: 92vh; background: #fff;
        border-radius: 14px; overflow: hidden; display: flex; flex-direction: column;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .25);
    }
    .teacher-modal-form { display: flex; flex-direction: column; min-height: 0; flex: 1; }
    .teacher-modal-header {
        padding: 18px 22px; border-bottom: 1px solid #eee;
        display: flex; justify-content: space-between; align-items: center;
    }
    .teacher-modal-header h4 { margin: 0; font-size: 20px; font-weight: 700; }
    .teacher-modal-close {
        width: 36px; height: 36px; border: 0; background: #f5f5f5;
        border-radius: 50%; cursor: pointer; font-size: 20px;
    }
    .teacher-modal-body { overflow-y: auto; padding: 22px; flex: 1; }

    /* Wizard */
    .teacher-steps { display: flex; align-items: center; justify-content: center; margin-bottom: 25px; }
    .teacher-step { display: flex; align-items: center; }
    .teacher-step-circle {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: #eee; color: #777; font-weight: 700;
    }
    .teacher-step.active .teacher-step-circle { background: #0d6efd; color: #fff; }
    .teacher-step.completed .teacher-step-circle { background: #198754; color: #fff; }
    .teacher-step-label { margin-left: 8px; font-size: 13px; font-weight: 600; }
    .teacher-step-line { width: 70px; height: 2px; background: #ddd; margin: 0 12px; }

    .teacher-form-step { display: none; }
    .teacher-form-step.active { display: block; }

    .teacher-section-title {
        font-size: 17px; font-weight: 700; margin-bottom: 18px;
        padding-bottom: 10px; border-bottom: 1px solid #eee;
    }
    .teacher-field { margin-bottom: 15px; }
    .teacher-field > label { display: block; margin-bottom: 6px; font-size: 13px; font-weight: 600; }
    .teacher-field input:not([type=radio]),
    .teacher-field select,
    .teacher-field textarea {
        width: 100%; min-height: 42px; border: 1px solid #ddd;
        border-radius: 7px; padding: 8px 11px; outline: none;
    }
    .teacher-field textarea { min-height: 90px; resize: vertical; }
    .teacher-field input:focus,
    .teacher-field select:focus,
    .teacher-field textarea:focus { border-color: #0d6efd; }
    .teacher-field .is-invalid { border-color: #dc3545 !important; }

    .teacher-required { color: #dc3545; }
    .teacher-code-preview { background: #f6f8fa; font-weight: 700; }

    .teacher-radio-group { display: flex; gap: 18px; align-items: center; min-height: 42px; }
    .teacher-radio { display: flex; align-items: center; gap: 6px; margin: 0; font-weight: 500; }
    .teacher-radio input { width: auto; min-height: auto; }

    .teacher-setting-btn {
        border: 0; background: none; color: #0d6efd; font-size: 12px;
        font-weight: 600; cursor: pointer; padding: 0 0 0 8px;
    }
    .teacher-setting-panel {
        display: none; background: #f6f8fa; border: 1px dashed #cfd8e3;
        border-radius: 8px; padding: 14px; margin-bottom: 18px;
    }
    .teacher-setting-panel.show { display: block; }

    .teacher-modal-footer {
        padding: 15px 22px; border-top: 1px solid #eee;
        display: flex; justify-content: space-between; align-items: center;
    }
    .teacher-footer-right { display: flex; gap: 8px; }
    .teacher-btn { border: 0; border-radius: 7px; padding: 9px 17px; cursor: pointer; font-weight: 600; }
    .teacher-btn-secondary { background: #f0f0f0; }
    .teacher-btn-primary { background: #0d6efd; color: #fff; }
    .teacher-btn-success { background: #198754; color: #fff; }

    .teacher-error { color: #dc3545; font-size: 12px; margin-top: 5px; }

    @media (max-width: 768px) {
        .teacher-header { flex-direction: column; align-items: flex-start; }
        .teacher-table-card { overflow-x: auto; }
        .teacher-steps { overflow-x: auto; justify-content: flex-start; }
        .teacher-step-label { display: none; }
        .teacher-step-line { width: 30px; }
        .teacher-modal { padding: 10px; }
        .teacher-modal-box { max-height: 96vh; }
    }
</style>

@php
    $oldGeneration = old('code_generation_type', 'auto');
    $showInstitutionSelect = $institutions->count() > 1 || !$institutionId;
    $routeIf = fn ($name) => \Illuminate\Support\Facades\Route::has($name) ? route($name) : null;
@endphp

<div class="teacher-page">

    {{-- Header --}}
    <div class="teacher-header">
        <div class="teacher-title">
            <h3>শিক্ষক তালিকা</h3>
            <p>সিস্টেমের শিক্ষক পরিচালনা ও দেখুন</p>
        </div>

        <button type="button" class="teacher-create-btn btn btn-primary" onclick="openTeacherModal()">
            + শিক্ষক তৈরি করুন
        </button>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Filters --}}
    <div class="teacher-filter-card">
        <form method="GET" action="{{ route('dashboard.teacher.index') }}">
            <div class="row g-3 align-items-end">

                <div class="col-md-5">
                    <label class="form-label">অনুসন্ধান</label>
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        value="{{ request('search') }}"
                        placeholder="নাম, শিক্ষক কোড, User Code, ফোন বা পিতার নাম..."
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">স্ট্যাটাস</label>
                    <select name="status" class="form-select">
                        <option value="all">সকল স্ট্যাটাস</option>
                        <option value="1" @selected(request('status') === '1')>সক্রিয়</option>
                        <option value="0" @selected(request('status') === '0')>নিষ্ক্রিয়</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">প্রতি পেজ</label>
                    <select name="per_page" class="form-select">
                        @foreach([10, 20, 50] as $n)
                            <option value="{{ $n }}" @selected((int) request('per_page', 20) === $n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>

            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="teacher-table-card">
        <div class="table-responsive">
            <table class="teacher-table">
                <thead>
                    <tr>
                        <th>ছবি</th>
                        <th>ইউজার বিবরণ</th>
                        <th>ইউজার কোড</th>
                        <th>শিক্ষক কোড</th>
                        <th>পিতার নাম</th>
                        <th>স্ট্যাটাস</th>
                        <th>পদবী</th>
                        <th>অ্যাকশন</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($teachers as $teacher)
                    @php $user = $teacher->user; @endphp

                    <tr>
                        <td>
                            @if($user?->photo)
                                <img src="{{ asset('storage/' . $user->photo) }}" class="teacher-photo" alt="Teacher">
                            @else
                                <div class="teacher-photo d-flex align-items-center justify-content-center bg-light">
                                    <span>👤</span>
                                </div>
                            @endif
                        </td>

                        <td>
                            <div class="teacher-name">{{ $user?->name_bn ?: $user?->name }}</div>
                            <div class="teacher-sub">{{ $user?->phone }}</div>
                        </td>

                        <td><span class="teacher-code">{{ $user?->institution_user_id }}</span></td>

                        <td><span class="teacher-code">{{ $teacher->teacher_id }}</span></td>

                        <td>{{ $user?->father_name ?: '—' }}</td>

                        <td>
                            @if($user?->status)
                                <span class="status-badge status-active">সক্রিয়</span>
                            @else
                                <span class="status-badge status-inactive">নিষ্ক্রিয়</span>
                            @endif
                        </td>

                        <td>
                            {{ $teacher->designation }}
                            @if($teacher->is_class_teacher && $teacher->classTeacherFor)
                                <div class="teacher-sub">শ্রেণিশিক্ষক: {{ $teacher->classTeacherFor->name }}</div>
                            @endif
                        </td>

                        <td>
                            <div class="teacher-actions">

                                <a href="{{ route('dashboard.teacher.show', $teacher->id) }}"
                                   class="teacher-action" title="দেখুন">👁</a>

                                @can('teacher.edit')
                                    <a href="{{ route('dashboard.teacher.edit', $teacher->id) }}"
                                       class="teacher-action" title="Edit">✏</a>

                                    <form method="POST"
                                          action="{{ route('dashboard.teacher.toggle-status', $teacher->id) }}"
                                          style="display:inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="teacher-action"
                                                title="{{ $user?->status ? 'নিষ্ক্রিয় করুন' : 'সক্রিয় করুন' }}">↔</button>
                                    </form>
                                @endcan

                                @can('teacher.delete')
                                    <form method="POST"
                                          action="{{ route('dashboard.teacher.destroy', $teacher->id) }}"
                                          style="display:inline"
                                          onsubmit="return confirm('আপনি কি এই শিক্ষককে মুছে ফেলতে চান?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="teacher-action" title="Delete">🗑</button>
                                    </form>
                                @endcan

                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">কোনো শিক্ষক পাওয়া যায়নি।</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($teachers->hasPages())
            <div class="p-3">{{ $teachers->links() }}</div>
        @endif
    </div>

</div>


{{-- ===================================================================== --}}
{{-- CREATE TEACHER MODAL --}}
{{-- ===================================================================== --}}

<div id="teacherCreateModal" class="teacher-modal">
    <div class="teacher-modal-box">

        <div class="teacher-modal-header">
            <h4>শিক্ষক তৈরি করুন</h4>
            <button type="button" class="teacher-modal-close" onclick="closeTeacherModal()">×</button>
        </div>

        <form method="POST"
              action="{{ route('dashboard.teacher.store') }}"
              enctype="multipart/form-data"
              id="teacherCreateForm"
              class="teacher-modal-form">

            @csrf

            <div class="teacher-modal-body">

                {{-- Validation summary --}}
                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>ফর্মে কিছু ভুল আছে:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Wizard Steps --}}
                <div class="teacher-steps">
                    <div class="teacher-step active" data-step="1">
                        <div class="teacher-step-circle">1</div>
                        <div class="teacher-step-label">ব্যক্তিগত তথ্য</div>
                    </div>
                    <div class="teacher-step-line"></div>
                    <div class="teacher-step" data-step="2">
                        <div class="teacher-step-circle">2</div>
                        <div class="teacher-step-label">স্থায়ী ঠিকানা</div>
                    </div>
                    <div class="teacher-step-line"></div>
                    <div class="teacher-step" data-step="3">
                        <div class="teacher-step-circle">3</div>
                        <div class="teacher-step-label">অস্থায়ী ঠিকানা</div>
                    </div>
                    <div class="teacher-step-line"></div>
                    <div class="teacher-step" data-step="4">
                        <div class="teacher-step-circle">4</div>
                        <div class="teacher-step-label">শিক্ষক তথ্য</div>
                    </div>
                </div>


                {{-- ================= STEP 1 ================= --}}
                <div class="teacher-form-step active" data-form-step="1">

                    <div class="teacher-section-title">ব্যক্তিগত তথ্য</div>

                    {{-- Institution --}}
                    @if($showInstitutionSelect)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="teacher-field">
                                    <label>প্রতিষ্ঠান <span class="teacher-required">*</span></label>
                                    <select name="institution_id" id="institutionField" required
                                            class="@error('institution_id') is-invalid @enderror">
                                        <option value="">নির্বাচন করুন</option>
                                        @foreach($institutions as $institution)
                                            <option value="{{ $institution->id }}"
                                                @selected((string) old('institution_id', $institutionId) === (string) $institution->id)>
                                                {{ $institution->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('institution_id') <div class="teacher-error">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="institution_id" id="institutionField" value="{{ $institutionId }}">
                    @endif

                    {{-- Codes --}}
                    <div class="row">

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>Teacher Code</label>
                                <input type="text" id="teacherCodePreview" class="teacher-code-preview"
                                       value="{{ $teacherCode }}" readonly>
                                <small class="text-muted">এটি প্রতিষ্ঠানের শিক্ষক কোড (স্বয়ংক্রিয়)।</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>
                                    User Code তৈরির ধরন
                                    @if(\Illuminate\Support\Facades\Route::has('dashboard.teacher.code-setting.save'))
                                        <button type="button" class="teacher-setting-btn" id="toggleCodeSetting">⚙ Code Setting</button>
                                    @endif
                                </label>
                                <div class="teacher-radio-group">
                                    <label class="teacher-radio">
                                        <input type="radio" name="code_generation_type" value="auto"
                                               @checked($oldGeneration === 'auto')>
                                        Auto
                                    </label>
                                    <label class="teacher-radio">
                                        <input type="radio" name="code_generation_type" value="manual"
                                               @checked($oldGeneration === 'manual')>
                                        Manual
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>User Code</label>

                                <input type="text" id="userCodePreview" class="teacher-code-preview"
                                       value="{{ $userCode }}" readonly>

                                <input type="text" name="custom_id" id="customId" inputmode="numeric"
                                       value="{{ old('custom_id') }}"
                                       class="@error('custom_id') is-invalid @enderror"
                                       style="display:none;">

                                <small class="text-muted" id="userCodeHint">এটি User Code System-এর কোড।</small>
                                @error('custom_id') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>

                    {{-- Code setting panel --}}
                    <div class="teacher-setting-panel" id="codeSettingPanel">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <div class="teacher-field mb-2">
                                    <label>Auto Starting Code <span class="teacher-required">*</span></label>
                                    <input type="number" id="settingStartCode" min="1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="teacher-field mb-2">
                                    <label>Manual Starting Code</label>
                                    <input type="number" id="settingManualStartCode" min="1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="teacher-btn teacher-btn-primary mb-2" id="saveCodeSetting">
                                    সংরক্ষণ করুন
                                </button>
                            </div>
                        </div>
                        <div class="teacher-sub" id="codeSettingMsg"></div>
                    </div>

                    {{-- Photo + names --}}
                    <div class="row">

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>ছবি</label>
                                <input type="file" name="photo" accept="image/jpeg,image/png,image/webp"
                                       class="@error('photo') is-invalid @enderror">
                                <small class="text-muted">jpg, png, webp — সর্বোচ্চ 2MB</small>
                                @error('photo') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>পূর্ণ নাম (বাংলা) <span class="teacher-required">*</span></label>
                                <input type="text" name="name_bn" required value="{{ old('name_bn') }}"
                                       placeholder="শিক্ষকের পূর্ণ নাম"
                                       class="@error('name_bn') is-invalid @enderror">
                                @error('name_bn') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>পূর্ণ নাম (English)</label>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="Full Name"
                                       class="@error('name') is-invalid @enderror">
                                @error('name') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>লিঙ্গ <span class="teacher-required">*</span></label>
                                <div class="teacher-radio-group">
                                    <label class="teacher-radio">
                                        <input type="radio" name="gender" value="male" required
                                               @checked(old('gender') === 'male')>
                                        পুরুষ
                                    </label>
                                    <label class="teacher-radio">
                                        <input type="radio" name="gender" value="female"
                                               @checked(old('gender') === 'female')>
                                        মহিলা
                                    </label>
                                </div>
                                @error('gender') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>জন্ম তারিখ <span class="teacher-required">*</span></label>
                                <input type="date" name="date_of_birth" id="dateOfBirth" required
                                       max="{{ date('Y-m-d') }}" value="{{ old('date_of_birth') }}"
                                       class="@error('date_of_birth') is-invalid @enderror">
                                @error('date_of_birth') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>বয়স</label>
                                <input type="number" name="age" id="ageField" min="0" max="150"
                                       value="{{ old('age') }}"
                                       class="@error('age') is-invalid @enderror">
                                <small class="text-muted">জন্ম তারিখ দিলে স্বয়ংক্রিয়ভাবে হিসাব হবে।</small>
                                @error('age') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>NID</label>
                                <input type="text" name="nid" value="{{ old('nid') }}"
                                       class="@error('nid') is-invalid @enderror">
                                @error('nid') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>জন্ম নিবন্ধন</label>
                                <input type="text" name="birth_certificate" value="{{ old('birth_certificate') }}"
                                       class="@error('birth_certificate') is-invalid @enderror">
                                @error('birth_certificate') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>রক্তের গ্রুপ</label>
                                <select name="blood_group" class="@error('blood_group') is-invalid @enderror">
                                    <option value="">নির্বাচন করুন</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                        <option value="{{ $bg }}" @selected(old('blood_group') === $bg)>{{ $bg }}</option>
                                    @endforeach
                                </select>
                                @error('blood_group') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>পিতার নাম</label>
                                <input type="text" name="father_name" value="{{ old('father_name') }}"
                                       class="@error('father_name') is-invalid @enderror">
                                @error('father_name') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>মাতার নাম</label>
                                <input type="text" name="mother_name" value="{{ old('mother_name') }}"
                                       class="@error('mother_name') is-invalid @enderror">
                                @error('mother_name') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>Email</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       class="@error('email') is-invalid @enderror">
                                @error('email') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>ফোন ১ <span class="teacher-required">*</span></label>
                                <input type="text" name="phone" required value="{{ old('phone') }}"
                                       class="@error('phone') is-invalid @enderror">
                                <small class="text-muted">এটিই শিক্ষকের প্রাথমিক পাসওয়ার্ড হবে।</small>
                                @error('phone') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>ফোন ২</label>
                                <input type="text" name="phone2" value="{{ old('phone2') }}"
                                       class="@error('phone2') is-invalid @enderror">
                                @error('phone2') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>ফোনের মালিক</label>
                                <input type="text" name="phone_owner" value="{{ old('phone_owner') }}"
                                       placeholder="নিজ / পিতা / মাতা"
                                       class="@error('phone_owner') is-invalid @enderror">
                                @error('phone_owner') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>

                </div>


                {{-- ================= STEP 2 : Permanent ================= --}}
                <div class="teacher-form-step" data-form-step="2">

                    <div class="teacher-section-title">স্থায়ী ঠিকানা</div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>বিভাগ</label>
                                <select name="permanent_division_id" id="permanent_division_id">
                                    <option value="">নির্বাচন করুন</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}"
                                            @selected((string) old('permanent_division_id') === (string) $division->id)>
                                            {{ $division->name_bn ?: $division->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('permanent_division_id') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>জেলা</label>
                                <select name="permanent_district_id" id="permanent_district_id">
                                    <option value="">আগে বিভাগ নির্বাচন করুন</option>
                                </select>
                                @error('permanent_district_id') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>থানা / উপজেলা</label>
                                <select name="permanent_upazila_id" id="permanent_upazila_id">
                                    <option value="">আগে জেলা নির্বাচন করুন</option>
                                </select>
                                @error('permanent_upazila_id') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="teacher-field">
                                <label>পোস্ট অফিস</label>
                                <input type="text" name="permanent_post_office" value="{{ old('permanent_post_office') }}">
                                @error('permanent_post_office') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="teacher-field">
                                <label>গ্রাম / রোড</label>
                                <input type="text" name="permanent_village_road" value="{{ old('permanent_village_road') }}">
                                @error('permanent_village_road') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                </div>


                {{-- ================= STEP 3 : Present ================= --}}
                <div class="teacher-form-step" data-form-step="3">

                    <div class="teacher-section-title">অস্থায়ী ঠিকানা</div>

                    <div class="teacher-field">
                        <label class="teacher-radio">
                            <input type="checkbox" id="sameAsPermanent">
                            স্থায়ী ঠিকানার মতোই
                        </label>
                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>বিভাগ</label>
                                <select name="present_division_id" id="present_division_id">
                                    <option value="">নির্বাচন করুন</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}"
                                            @selected((string) old('present_division_id') === (string) $division->id)>
                                            {{ $division->name_bn ?: $division->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('present_division_id') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>জেলা</label>
                                <select name="present_district_id" id="present_district_id">
                                    <option value="">আগে বিভাগ নির্বাচন করুন</option>
                                </select>
                                @error('present_district_id') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>থানা / উপজেলা</label>
                                <select name="present_upazila_id" id="present_upazila_id">
                                    <option value="">আগে জেলা নির্বাচন করুন</option>
                                </select>
                                @error('present_upazila_id') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="teacher-field">
                                <label>পোস্ট অফিস</label>
                                <input type="text" name="present_post_office" id="present_post_office"
                                       value="{{ old('present_post_office') }}">
                                @error('present_post_office') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="teacher-field">
                                <label>গ্রাম / রোড</label>
                                <input type="text" name="present_village_road" id="present_village_road"
                                       value="{{ old('present_village_road') }}">
                                @error('present_village_road') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                </div>


                {{-- ================= STEP 4 : Teacher info ================= --}}
                <div class="teacher-form-step" data-form-step="4">

                    <div class="teacher-section-title">শিক্ষক তথ্য</div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>পদবী <span class="teacher-required">*</span></label>
                                <input type="text" name="designation" required value="{{ old('designation') }}"
                                       placeholder="সহকারী শিক্ষক"
                                       class="@error('designation') is-invalid @enderror">
                                @error('designation') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>যোগদানের তারিখ <span class="teacher-required">*</span></label>
                                <input type="date" name="joining_date" required value="{{ old('joining_date') }}"
                                       class="@error('joining_date') is-invalid @enderror">
                                @error('joining_date') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>পাসের বিভাগ</label>
                                <input type="text" name="pass_department" value="{{ old('pass_department') }}"
                                       placeholder="ইসলামিক স্টাডিজ">
                                @error('pass_department') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>পাসের সাল</label>
                                <input type="number" name="pass_year" min="1900" max="{{ date('Y') }}"
                                       value="{{ old('pass_year') }}">
                                @error('pass_year') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>পাসের তারিখ</label>
                                <input type="date" name="pass_date" value="{{ old('pass_date') }}">
                                @error('pass_date') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>যে প্রতিষ্ঠান থেকে পাস করেছেন</label>
                                <input type="text" name="completion_institution"
                                       value="{{ old('completion_institution') }}"
                                       placeholder="মাদ্রাসা / কলেজ / বিশ্ববিদ্যালয়">
                                @error('completion_institution') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="teacher-field">
                                <label>শিক্ষাগত যোগ্যতা</label>
                                <textarea name="qualification"
                                          placeholder="শিক্ষাগত যোগ্যতার বিবরণ">{{ old('qualification') }}</textarea>
                                @error('qualification') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="teacher-field">
                                <label>দক্ষতার বিষয়</label>
                                <textarea name="expertise_subjects"
                                          placeholder="কুরআন, হাদিস, আরবি...">{{ old('expertise_subjects') }}</textarea>
                                @error('expertise_subjects') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="teacher-field">
                                <label>অভিজ্ঞতা</label>
                                <textarea name="experience"
                                          placeholder="পূর্বের শিক্ষকতার অভিজ্ঞতা">{{ old('experience') }}</textarea>
                                @error('experience') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>বেতন স্কেল</label>
                                <input type="text" name="salary_scale" value="{{ old('salary_scale') }}">
                                @error('salary_scale') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>মূল বেতন</label>
                                <input type="number" name="basic_salary" min="0" step="0.01"
                                       value="{{ old('basic_salary') }}">
                                @error('basic_salary') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="teacher-field">
                                <label>শ্রেণিশিক্ষক</label>
                                <select name="is_class_teacher" id="is_class_teacher">
                                    <option value="0" @selected(old('is_class_teacher', '0') === '0')>না</option>
                                    <option value="1" @selected(old('is_class_teacher') === '1')>হ্যাঁ</option>
                                </select>
                                @error('is_class_teacher') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>

                    <div class="row" id="classTeacherField"
                         style="{{ old('is_class_teacher') === '1' ? '' : 'display:none;' }}">
                        <div class="col-md-6">
                            <div class="teacher-field">
                                <label>কোন শ্রেণির শ্রেণিশিক্ষক? <span class="teacher-required">*</span></label>
                                <select name="class_teacher_for" id="class_teacher_for">
                                    <option value="">নির্বাচন করুন</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}"
                                            @selected((string) old('class_teacher_for') === (string) $class->id)>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_teacher_for') <div class="teacher-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="teacher-field">
                        <label>মন্তব্য</label>
                        <textarea name="remarks" placeholder="অতিরিক্ত তথ্য...">{{ old('remarks') }}</textarea>
                        @error('remarks') <div class="teacher-error">{{ $message }}</div> @enderror
                    </div>

                </div>

            </div>


            {{-- Modal Footer --}}
            <div class="teacher-modal-footer">

                <button type="button" class="teacher-btn teacher-btn-secondary" onclick="resetTeacherForm()">
                    Reset
                </button>

                <div class="teacher-footer-right">
                    <button type="button" id="teacherPrevBtn" class="teacher-btn teacher-btn-secondary"
                            onclick="previousTeacherStep()" style="display:none;">
                        Previous
                    </button>

                    <button type="button" id="teacherNextBtn" class="teacher-btn teacher-btn-primary"
                            onclick="nextTeacherStep()">
                        Next
                    </button>

                    <button type="submit" id="teacherSubmitBtn" class="teacher-btn teacher-btn-success"
                            style="display:none;">
                        শিক্ষক সংরক্ষণ করুন
                    </button>
                </div>

            </div>

        </form>
    </div>
</div>


<script>
(function () {

    const TOTAL = 4;
    let step = 1;

    const $ = (id) => document.getElementById(id);
    const form = $('teacherCreateForm');

    const URLS = {
        districts: @json(route('dashboard.teacher.get.districts', ':id')),
        upazilas:  @json(route('dashboard.teacher.get.upazilas', ':id')),
        // Controller-এর previewCodes / getCodeSetting / saveCodeSetting route।
        // route name আলাদা হলে এখানে (উপরের @php-এর $routeIf দিয়ে) নাম ঠিক করুন।
        preview:     @json($routeIf('dashboard.teacher.preview-codes')),
        settingGet:  @json($routeIf('dashboard.teacher.code-setting.get')),
        settingSave: @json($routeIf('dashboard.teacher.code-setting.save')),
    };

    const CSRF = @json(csrf_token());

    const OLD = {
        permanent: {
            division: @json(old('permanent_division_id')),
            district: @json(old('permanent_district_id')),
            upazila:  @json(old('permanent_upazila_id')),
        },
        present: {
            division: @json(old('present_division_id')),
            district: @json(old('present_district_id')),
            upazila:  @json(old('present_upazila_id')),
        },
    };

    const ERROR_KEYS = @json($errors->keys());
    const OPEN_ON_LOAD = @json($errors->any() || session('error') ? true : false);


    /* ---------------- Modal ---------------- */

    window.openTeacherModal = function () {
        $('teacherCreateModal').classList.add('show');
        document.body.style.overflow = 'hidden';
        goToStep(1);
    };

    window.closeTeacherModal = function () {
        $('teacherCreateModal').classList.remove('show');
        document.body.style.overflow = '';
    };

    $('teacherCreateModal').addEventListener('click', function (e) {
        if (e.target === this) closeTeacherModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeTeacherModal();
    });


    /* ---------------- Wizard ---------------- */

    function goToStep(n) {
        step = n;

        document.querySelectorAll('[data-form-step]').forEach(function (el) {
            el.classList.toggle('active', parseInt(el.dataset.formStep, 10) === step);
        });

        document.querySelectorAll('[data-step]').forEach(function (el) {
            const num = parseInt(el.dataset.step, 10);
            el.classList.remove('active', 'completed');
            if (num === step) el.classList.add('active');
            else if (num < step) el.classList.add('completed');
        });

        $('teacherPrevBtn').style.display   = step > 1 ? 'inline-block' : 'none';
        $('teacherNextBtn').style.display   = step < TOTAL ? 'inline-block' : 'none';
        $('teacherSubmitBtn').style.display = step === TOTAL ? 'inline-block' : 'none';

        const body = document.querySelector('.teacher-modal-body');
        if (body) body.scrollTop = 0;
    }

    function validateCurrentStep() {
        const panel = document.querySelector('[data-form-step="' + step + '"]');
        if (!panel) return true;

        const fields = panel.querySelectorAll('input, select, textarea');

        for (let i = 0; i < fields.length; i++) {
            const f = fields[i];
            if (f.offsetParent === null && f.type !== 'radio') continue; // লুকানো ফিল্ড বাদ
            if (f.hasAttribute('required') && !f.checkValidity()) {
                f.reportValidity();
                return false;
            }
        }

        // Manual code হলে সংখ্যা কি না দেখা
        if (step === 1 && currentGeneration() === 'manual') {
            const v = $('customId').value.trim();
            if (v !== '' && !/^\d+$/.test(v)) {
                $('customId').setCustomValidity('User Code শুধুমাত্র সংখ্যা হতে হবে।');
                $('customId').reportValidity();
                $('customId').setCustomValidity('');
                return false;
            }
        }

        return true;
    }

    window.nextTeacherStep = function () {
        if (!validateCurrentStep()) return;
        if (step < TOTAL) goToStep(step + 1);
    };

    window.previousTeacherStep = function () {
        if (step > 1) goToStep(step - 1);
    };

    // Enter চাপলে ফর্ম আগেই submit হয়ে যাওয়া আটকানো
    form.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA' && e.target.type !== 'submit') {
            e.preventDefault();
            if (step < TOTAL) window.nextTeacherStep();
        }
    });


    /* ---------------- Address (AJAX) ---------------- */

    function resetSelect(select, text) {
        if (!select) return;
        select.innerHTML = '<option value="">' + text + '</option>';
    }

    function fillSelect(select, items, selected) {
        select.innerHTML = '<option value="">নির্বাচন করুন</option>';
        items.forEach(function (item) {
            const o = document.createElement('option');
            o.value = item.id;
            o.textContent = item.name;
            if (selected && String(item.id) === String(selected)) o.selected = true;
            select.appendChild(o);
        });
    }

    async function loadDistricts(prefix, divisionId, selected) {
        const d = $(prefix + '_district_id');
        const u = $(prefix + '_upazila_id');

        resetSelect(u, 'আগে জেলা নির্বাচন করুন');

        if (!divisionId) {
            resetSelect(d, 'আগে বিভাগ নির্বাচন করুন');
            return;
        }

        resetSelect(d, 'লোড হচ্ছে...');

        try {
            const r = await fetch(URLS.districts.replace(':id', divisionId), {
                headers: { 'Accept': 'application/json' }
            });
            fillSelect(d, await r.json(), selected);
        } catch (e) {
            resetSelect(d, 'জেলা লোড করা যায়নি');
        }
    }

    async function loadUpazilas(prefix, districtId, selected) {
        const u = $(prefix + '_upazila_id');

        if (!districtId) {
            resetSelect(u, 'আগে জেলা নির্বাচন করুন');
            return;
        }

        resetSelect(u, 'লোড হচ্ছে...');

        try {
            const r = await fetch(URLS.upazilas.replace(':id', districtId), {
                headers: { 'Accept': 'application/json' }
            });
            fillSelect(u, await r.json(), selected);
        } catch (e) {
            resetSelect(u, 'উপজেলা লোড করা যায়নি');
        }
    }

    ['permanent', 'present'].forEach(function (prefix) {
        $(prefix + '_division_id').addEventListener('change', function () {
            loadDistricts(prefix, this.value);
        });
        $(prefix + '_district_id').addEventListener('change', function () {
            loadUpazilas(prefix, this.value);
        });
    });

    // Validation fail হলে আগের নির্বাচন ফিরিয়ে আনা
    async function restoreAddresses() {
        for (const prefix of ['permanent', 'present']) {
            const o = OLD[prefix];
            if (!o.division) continue;

            await loadDistricts(prefix, o.division, o.district);

            if (o.district) {
                await loadUpazilas(prefix, o.district, o.upazila);
            }
        }
    }

    // স্থায়ী ঠিকানার মতোই
    $('sameAsPermanent').addEventListener('change', async function () {
        if (!this.checked) return;

        $('present_division_id').value = $('permanent_division_id').value;

        await loadDistricts('present', $('present_division_id').value, $('permanent_district_id').value);
        await loadUpazilas('present', $('permanent_district_id').value, $('permanent_upazila_id').value);

        $('present_post_office').value = form.elements['permanent_post_office'].value;
        $('present_village_road').value = form.elements['permanent_village_road'].value;
    });


    /* ---------------- Age from DOB ---------------- */

    $('dateOfBirth').addEventListener('change', function () {
        if (!this.value) return;

        const dob = new Date(this.value);
        const now = new Date();
        let age = now.getFullYear() - dob.getFullYear();
        const m = now.getMonth() - dob.getMonth();

        if (m < 0 || (m === 0 && now.getDate() < dob.getDate())) age--;

        $('ageField').value = age >= 0 ? age : '';
    });


    /* ---------------- Class teacher ---------------- */

    function syncClassTeacher() {
        const yes = $('is_class_teacher').value === '1';
        $('classTeacherField').style.display = yes ? 'flex' : 'none';
        $('class_teacher_for').required = yes;
        if (!yes) $('class_teacher_for').value = '';
    }

    $('is_class_teacher').addEventListener('change', syncClassTeacher);


    /* ---------------- User code (auto / manual) ---------------- */

    function currentGeneration() {
        const r = form.querySelector('input[name="code_generation_type"]:checked');
        return r ? r.value : 'auto';
    }

    function institutionValue() {
        const el = $('institutionField');
        return el ? el.value : '';
    }

    function syncGenerationUi() {
        const manual = currentGeneration() === 'manual';

        $('userCodePreview').style.display = manual ? 'none' : '';
        $('customId').style.display = manual ? '' : 'none';
        $('userCodeHint').textContent = manual
            ? 'নিজে কোড দিন (শুধু সংখ্যা)। খালি রাখলে পরবর্তী ফাঁকা কোড বসবে।'
            : 'এটি User Code System-এর কোড।';
    }

    async function refreshCodes() {
        syncGenerationUi();

        if (!URLS.preview) return;

        const inst = institutionValue();

        if (!inst) {
            $('teacherCodePreview').value = '';
            $('userCodePreview').value = '';
            $('customId').placeholder = '';
            return;
        }

        try {
            const url = URLS.preview
                + '?institution_id=' + encodeURIComponent(inst)
                + '&generation_type=' + encodeURIComponent(currentGeneration());

            const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const d = await r.json();

            if (!d.success) {
                $('userCodeHint').textContent = d.message || 'কোড লোড করা যায়নি।';
                return;
            }

            $('teacherCodePreview').value = d.teacher_code || '';
            $('userCodePreview').value = d.user_code || '';
            $('customId').placeholder = d.user_code ? ('পরবর্তী: ' + d.user_code) : '';
        } catch (e) {
            $('userCodeHint').textContent = 'কোড লোড করা যায়নি।';
        }
    }

    form.querySelectorAll('input[name="code_generation_type"]').forEach(function (r) {
        r.addEventListener('change', refreshCodes);
    });

    if ($('institutionField').tagName === 'SELECT') {
        $('institutionField').addEventListener('change', refreshCodes);
    }


    /* ---------------- Code setting panel ---------------- */

    const toggleBtn = $('toggleCodeSetting');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', async function () {
            const panel = $('codeSettingPanel');
            panel.classList.toggle('show');

            if (!panel.classList.contains('show') || !URLS.settingGet) return;

            const inst = institutionValue();

            if (!inst) {
                $('codeSettingMsg').textContent = 'আগে প্রতিষ্ঠান নির্বাচন করুন।';
                return;
            }

            try {
                const r = await fetch(URLS.settingGet + '?institution_id=' + encodeURIComponent(inst), {
                    headers: { 'Accept': 'application/json' }
                });
                const d = await r.json();

                if (d.success) {
                    $('settingStartCode').value = d.start_code ?? '';
                    $('settingManualStartCode').value = d.manual_start_code ?? '';
                    $('codeSettingMsg').textContent = '';
                } else {
                    $('codeSettingMsg').textContent = d.message || '';
                }
            } catch (e) {
                $('codeSettingMsg').textContent = 'Setting লোড করা যায়নি।';
            }
        });
    }

    const saveBtn = $('saveCodeSetting');

    if (saveBtn) {
        saveBtn.addEventListener('click', async function () {
            if (!URLS.settingSave) return;

            const msg = $('codeSettingMsg');
            const inst = institutionValue();

            if (!inst) {
                msg.textContent = 'আগে প্রতিষ্ঠান নির্বাচন করুন।';
                return;
            }

            msg.textContent = 'সংরক্ষণ হচ্ছে...';

            try {
                const r = await fetch(URLS.settingSave, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({
                        institution_id: inst,
                        start_code: $('settingStartCode').value,
                        manual_start_code: $('settingManualStartCode').value || null,
                    }),
                });

                const d = await r.json();

                if (r.ok && d.success) {
                    msg.textContent = d.message || 'সংরক্ষণ করা হয়েছে।';
                    refreshCodes();
                } else {
                    const first = d.errors ? Object.values(d.errors)[0][0] : null;
                    msg.textContent = first || d.message || 'সংরক্ষণ করা যায়নি।';
                }
            } catch (e) {
                msg.textContent = 'সংরক্ষণ করা যায়নি।';
            }
        });
    }


    /* ---------------- Reset ---------------- */

    window.resetTeacherForm = function () {
        if (!confirm('আপনি কি ফর্মটি reset করতে চান?')) return;

        form.reset();

        resetSelect($('permanent_district_id'), 'আগে বিভাগ নির্বাচন করুন');
        resetSelect($('permanent_upazila_id'), 'আগে জেলা নির্বাচন করুন');
        resetSelect($('present_district_id'), 'আগে বিভাগ নির্বাচন করুন');
        resetSelect($('present_upazila_id'), 'আগে জেলা নির্বাচন করুন');

        syncClassTeacher();
        refreshCodes();
        goToStep(1);
    };


    /* ---------------- Init ---------------- */

    syncGenerationUi();
    syncClassTeacher();
    restoreAddresses();
    goToStep(1);

    // Server-side validation error হলে modal খুলে প্রথম error-এর step-এ নিয়ে যাওয়া
    if (OPEN_ON_LOAD) {
        window.openTeacherModal();

        for (let i = 0; i < ERROR_KEYS.length; i++) {
            const field = form.querySelector('[name="' + ERROR_KEYS[i] + '"]');
            const panel = field ? field.closest('[data-form-step]') : null;

            if (panel) {
                goToStep(parseInt(panel.dataset.formStep, 10));
                break;
            }
        }
    }

})();
</script>

@endsection