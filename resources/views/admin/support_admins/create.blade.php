@extends('layouts.admin')

@section('title', 'নতুন সাপোর্ট এডমিন')

@section('page')

<div class="container-fluid px-0">

{{-- =========================================================
    PAGE HEADER
========================================================== --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">

    <div>
        <div class="d-flex align-items-center gap-2 mb-1">

            <a href="{{ route('dashboard.support-admins.index') }}"
               class="btn btn-sm btn-light border">

                <i class="bi bi-arrow-left"></i>

            </a>

            <h4 class="fw-bold mb-0">
                নতুন সাপোর্ট এডমিন তৈরি
            </h4>

        </div>

        <p class="text-muted mb-0">
            সাপোর্ট এডমিনের পরিচয় এবং পৃষ্ঠাভিত্তিক অ্যাক্সেস পারমিশন নির্ধারণ করুন।
        </p>
    </div>

</div>


{{-- =========================================================
    VALIDATION ERRORS
========================================================== --}}
@if($errors->any())

    <div class="alert alert-danger shadow-sm">

        <div class="fw-bold mb-2">
            <i class="bi bi-exclamation-triangle me-1"></i>
            অনুগ্রহ করে নিচের সমস্যাগুলো ঠিক করুন:
        </div>

        <ul class="mb-0 ps-3">

            @foreach($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif


{{-- =========================================================
    CREATE FORM
========================================================== --}}
<form method="POST"
      action="{{ route('dashboard.support-admins.store') }}"
      id="supportAdminForm">

    @csrf


    {{-- =====================================================
        ADMIN INFORMATION
    ====================================================== --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-0 py-3">

            <div class="d-flex align-items-center gap-2">

                <div class="rounded-3 bg-primary bg-opacity-10
                            text-primary d-flex align-items-center
                            justify-content-center"
                     style="width:42px;height:42px;">

                    <i class="bi bi-person-badge fs-5"></i>

                </div>

                <div>

                    <h5 class="fw-bold mb-0">
                        এডমিনের পরিচয়
                    </h5>

                    <small class="text-muted">
                        সাপোর্ট এডমিনের মৌলিক তথ্য দিন
                    </small>

                </div>

            </div>

        </div>


        <div class="card-body">

            <div class="row g-3">

                {{-- Name --}}
                <div class="col-lg-6">

                    <label class="form-label fw-semibold">
                        এডমিনের নাম <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="যেমন: Tanvir Ahmed"
                           required>

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Bangla Name --}}
                <div class="col-lg-6">

                    <label class="form-label fw-semibold">
                        বাংলা নাম
                    </label>

                    <input type="text"
                           name="name_bn"
                           value="{{ old('name_bn') }}"
                           class="form-control @error('name_bn') is-invalid @enderror"
                           placeholder="যেমন: তানভীর আহমেদ">

                    @error('name_bn')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Role --}}
                <div class="col-lg-6">

                    <label class="form-label fw-semibold">
                        পদবি / রোল
                    </label>

                    <input type="text"
                           value="সাপোর্ট অ্যাডমিন"
                           class="form-control"
                           readonly>

                    <div class="form-text">
                        এই অ্যাকাউন্টের Role স্বয়ংক্রিয়ভাবে
                        Support Admin হবে।
                    </div>

                </div>


                {{-- Phone --}}
                <div class="col-lg-6">

                    <label class="form-label fw-semibold">
                        মোবাইল নম্বর (লগইন আইডি)
                        <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="phone"
                           value="{{ old('phone') }}"
                           class="form-control @error('phone') is-invalid @enderror"
                           placeholder="01XXXXXXXXX"
                           required>

                    @error('phone')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Email --}}
                <div class="col-lg-6">

                    <label class="form-label fw-semibold">
                        ইমেইল
                        <span class="text-muted fw-normal">(ঐচ্ছিক)</span>
                    </label>

                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="example@email.com">

                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Password --}}
                <div class="col-lg-6">

                    <label class="form-label fw-semibold">
                        পাসওয়ার্ড <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="কমপক্ষে ৬ অক্ষর"
                               required>

                        <button type="button"
                                class="btn btn-light border"
                                onclick="togglePassword('password', this)">

                            <i class="bi bi-eye"></i>

                        </button>

                    </div>

                    @error('password')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Confirm Password --}}
                <div class="col-lg-6">

                    <label class="form-label fw-semibold">
                        পাসওয়ার্ড নিশ্চিত করুন
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                        <input type="password"
                               name="password_confirmation"
                               id="password_confirmation"
                               class="form-control"
                               placeholder="পাসওয়ার্ড আবার লিখুন"
                               required>

                        <button type="button"
                                class="btn btn-light border"
                                onclick="togglePassword('password_confirmation', this)">

                            <i class="bi bi-eye"></i>

                        </button>

                    </div>

                </div>


                {{-- Status --}}
                <div class="col-lg-6">

                    <label class="form-label fw-semibold d-block">
                        একাউন্ট স্ট্যাটাস
                    </label>

                    <div class="d-flex gap-3 pt-1">

                        <div class="form-check">

                            <input class="form-check-input"
                                   type="radio"
                                   name="status"
                                   id="statusActive"
                                   value="1"
                                   {{ old('status', '1') == '1' ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="statusActive">

                                <span class="text-success fw-semibold">
                                    সক্রিয়
                                </span>

                            </label>

                        </div>


                        <div class="form-check">

                            <input class="form-check-input"
                                   type="radio"
                                   name="status"
                                   id="statusInactive"
                                   value="0"
                                   {{ old('status') === '0' ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="statusInactive">

                                <span class="text-danger fw-semibold">
                                    নিষ্ক্রিয়
                                </span>

                            </label>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
        PERMISSION MATRIX
    ====================================================== --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-0 py-3">

            <div class="d-flex flex-wrap justify-content-between
                        align-items-center gap-3">

                <div class="d-flex align-items-center gap-2">

                    <div class="rounded-3 bg-success bg-opacity-10
                                text-success d-flex align-items-center
                                justify-content-center"
                         style="width:42px;height:42px;">

                        <i class="bi bi-shield-check fs-5"></i>

                    </div>

                    <div>

                        <h5 class="fw-bold mb-0">
                            মডিউল ও পেজভিত্তিক পারমিশন
                        </h5>

                        <small class="text-muted">
                            এই সাপোর্ট এডমিন কোন কোন পেজে কী কাজ করতে পারবে তা নির্ধারণ করুন
                        </small>

                    </div>

                </div>


                {{-- Global Permission Controls --}}
                <div class="d-flex flex-wrap gap-2">

                    <button type="button"
                            class="btn btn-sm btn-success"
                            id="selectAllPermissions">

                        <i class="bi bi-check2-all me-1"></i>

                        সব অনুমোদন

                    </button>


                    <button type="button"
                            class="btn btn-sm btn-outline-danger"
                            id="clearAllPermissions">

                        <i class="bi bi-x-lg me-1"></i>

                        সব বাতিল

                    </button>

                </div>

            </div>

        </div>


        <div class="card-body">

            {{-- Permission Summary --}}
            <div class="alert alert-light border d-flex
                        align-items-center justify-content-between
                        flex-wrap gap-2 mb-4">

                <div>

                    <i class="bi bi-info-circle text-primary me-1"></i>

                    <span>
                        নির্বাচিত পারমিশন:
                    </span>

                    <strong id="selectedPermissionCount">
                        0
                    </strong>

                </div>

                <div class="small text-muted">

                    View = দেখা,
                    Create = যোগ,
                    Edit = সম্পাদনা,
                    Delete = মুছে ফেলা

                </div>

            </div>


            {{-- =================================================
                PANEL / MODULE GROUPS
            ================================================== --}}
            @forelse($permissionGroups as $panelSlug => $panelPermissions)

                @php

                    $panel =
                        $panelPermissions
                            ->first()
                            ->systemPanel;

                    $panelName =
                        $panel?->panel_name
                        ?? ucfirst($panelSlug);

                @endphp


                <div class="permission-panel mb-4"
                     data-panel="{{ $panelSlug }}">

                    {{-- Panel Header --}}
                    <div class="d-flex align-items-center
                                justify-content-between
                                bg-light border rounded-top px-3 py-2">

                        <div class="d-flex align-items-center gap-2">

                            <i class="bi bi-grid-1x2 text-primary"></i>

                            <span class="fw-bold">
                                {{ $panelName }}
                            </span>

                        </div>


                        <button type="button"
                                class="btn btn-sm btn-link text-decoration-none panel-toggle">

                            <i class="bi bi-chevron-up"></i>

                        </button>

                    </div>


                    {{-- Permissions --}}
                    <div class="permission-panel-body border border-top-0 rounded-bottom">

                        @php

                            $panelModules =
                                $panelPermissions->groupBy('module');

                        @endphp


                        @foreach($panelModules as $moduleName => $modulePermissions)

                            <div class="permission-module">

                                {{-- Module Header --}}
                                <div class="px-3 py-2 bg-white
                                            border-bottom d-flex
                                            align-items-center
                                            justify-content-between">

                                    <div class="d-flex align-items-center gap-2">

                                        <span class="fw-semibold">
                                            {{ $moduleName }}
                                        </span>

                                        <span class="badge bg-light text-muted border">

                                            {{ $modulePermissions->count() }}
                                            টি

                                        </span>

                                    </div>


                                    <div>

                                        <button type="button"
                                                class="btn btn-sm btn-link text-primary
                                                       module-select-all p-0 me-2">

                                            সব নির্বাচন

                                        </button>

                                        <button type="button"
                                                class="btn btn-sm btn-link text-danger
                                                       module-clear-all p-0">

                                            বাতিল

                                        </button>

                                    </div>

                                </div>


                                {{-- Permission Table --}}
                                <div class="table-responsive">

                                    <table class="table table-sm
                                                  table-hover align-middle mb-0">

                                        <thead class="table-light">

                                            <tr>

                                                <th style="min-width:280px;"
                                                    class="ps-3">

                                                    পেজ / পারমিশন

                                                </th>

                                                <th class="text-center"
                                                    style="width:110px;">

                                                    View

                                                </th>

                                                <th class="text-center"
                                                    style="width:110px;">

                                                    Insert

                                                </th>

                                                <th class="text-center"
                                                    style="width:110px;">

                                                    Edit

                                                </th>

                                                <th class="text-center"
                                                    style="width:110px;">

                                                    Delete

                                                </th>

                                                <th class="text-center"
                                                    style="width:110px;">

                                                    অন্যান্য

                                                </th>

                                            </tr>

                                        </thead>


                                        <tbody>

                                        @php

                                            /*
                                             * একই module-এর permission
                                             * action অনুযায়ী group করা হচ্ছে।
                                             */
                                            $pageGroups =
                                                $modulePermissions->groupBy(
                                                    function ($permission) {

                                                        $parts =
                                                            explode(
                                                                '.',
                                                                $permission->slug
                                                            );

                                                        array_pop($parts);

                                                        return implode(
                                                            '.',
                                                            $parts
                                                        );

                                                    }
                                                );

                                        @endphp


                                        @foreach($pageGroups as $pageSlug => $pagePermissions)

                                            @php

                                                $pagePermission =
                                                    $pagePermissions
                                                        ->first();

                                                $pageName =
                                                    $pagePermission->module;

                                                /*
                                                 * Permission name থেকে
                                                 * action বাদ দিয়ে page name
                                                 * আলাদা করার চেষ্টা।
                                                 */
                                                $pageName =
                                                    preg_replace(
                                                        '/\s+(দেখুন|যোগ করুন|সম্পাদনা করুন|মুছে ফেলুন|.* করুন)$/u',
                                                        '',
                                                        $pagePermission->permission_name
                                                    );

                                            @endphp


                                            <tr>

                                                <td class="ps-3">

                                                    <div class="fw-semibold">
                                                        {{ $pageName }}
                                                    </div>

                                                </td>


                                                @php

                                                    $standardActions = [
                                                        'view',
                                                        'create',
                                                        'edit',
                                                        'delete'
                                                    ];

                                                @endphp


                                                @foreach($standardActions as $action)

                                                    @php

                                                        $permission =
                                                            $pagePermissions
                                                                ->first(
                                                                    fn($item) =>
                                                                        str_ends_with(
                                                                            $item->slug,
                                                                            '.' . $action
                                                                        )
                                                                );

                                                    @endphp


                                                    <td class="text-center">

                                                        @if($permission)

                                                            <div class="form-check d-flex
                                                                        justify-content-center">

                                                                <input type="checkbox"
                                                                       name="permissions[]"
                                                                       value="{{ $permission->id }}"
                                                                       class="form-check-input permission-checkbox"
                                                                       data-module="{{ $moduleName }}"
                                                                       data-action="{{ $action }}"
                                                                       {{ in_array(
                                                                           $permission->id,
                                                                           old('permissions', [])
                                                                       ) ? 'checked' : '' }}>

                                                            </div>

                                                        @else

                                                            <span class="text-muted">
                                                                —
                                                            </span>

                                                        @endif

                                                    </td>

                                                @endforeach


                                                {{-- Other permissions --}}
                                                <td class="text-center">

                                                    @php

                                                        $otherPermissions =
                                                            $pagePermissions->filter(
                                                                fn($item) =>
                                                                    !in_array(
                                                                        last(
                                                                            explode(
                                                                                '.',
                                                                                $item->slug
                                                                            )
                                                                        ),
                                                                        $standardActions
                                                                    )
                                                            );

                                                    @endphp


                                                    @if($otherPermissions->count())

                                                        <div class="dropdown">

                                                            <button type="button"
                                                                    class="btn btn-sm btn-light border"
                                                                    data-bs-toggle="dropdown">

                                                                <i class="bi bi-three-dots"></i>

                                                                <span class="ms-1">
                                                                    {{ $otherPermissions->count() }}
                                                                </span>

                                                            </button>


                                                            <div class="dropdown-menu dropdown-menu-end p-2 shadow-sm">

                                                                @foreach($otherPermissions as $permission)

                                                                    @php

                                                                        $action =
                                                                            last(
                                                                                explode(
                                                                                    '.',
                                                                                    $permission->slug
                                                                                )
                                                                            );

                                                                    @endphp

                                                                    <label class="dropdown-item
                                                                                  d-flex align-items-center gap-2">

                                                                        <input type="checkbox"
                                                                               name="permissions[]"
                                                                               value="{{ $permission->id }}"
                                                                               class="form-check-input permission-checkbox"
                                                                               data-module="{{ $moduleName }}"
                                                                               data-action="{{ $action }}"
                                                                               {{ in_array(
                                                                                   $permission->id,
                                                                                   old('permissions', [])
                                                                               ) ? 'checked' : '' }}>

                                                                        <span>

                                                                            {{ $permission->permission_name }}

                                                                        </span>

                                                                    </label>

                                                                @endforeach

                                                            </div>

                                                        </div>

                                                    @else

                                                        <span class="text-muted">
                                                            —
                                                        </span>

                                                    @endif

                                                </td>

                                            </tr>

                                        @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            @empty

                <div class="text-center py-5">

                    <div class="mb-3">

                        <i class="bi bi-shield-x fs-1 text-muted"></i>

                    </div>

                    <h6 class="fw-bold">
                        কোনো সক্রিয় পারমিশন পাওয়া যায়নি
                    </h6>

                    <p class="text-muted mb-0">
                        প্রথমে Permission Seeder থেকে পারমিশন তৈরি করুন।
                    </p>

                </div>

            @endforelse

        </div>

    </div>


    {{-- =====================================================
        FORM ACTIONS
    ====================================================== --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="d-flex flex-wrap justify-content-end gap-2">

                <a href="{{ route('dashboard.support-admins.index') }}"
                   class="btn btn-light border">

                    <i class="bi bi-x-lg me-1"></i>

                    বাতিল

                </a>


                <button type="submit"
                        class="btn btn-primary"
                        id="submitButton">

                    <i class="bi bi-shield-plus me-1"></i>

                    সাপোর্ট এডমিন তৈরি করুন

                </button>

            </div>

        </div>

    </div>

</form>

</div>

{{-- =============================================================
STYLES
============================================================= --}}

<style>

    .permission-panel {
        overflow: hidden;
    }

    .permission-module:last-child {
        border-bottom: 0 !important;
    }

    .permission-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .permission-checkbox:checked {
        accent-color: var(--bs-primary);
    }

    .permission-panel-body {
        transition: all 0.2s ease;
    }

    .module-select-all,
    .module-clear-all {
        font-size: 12px;
    }

    .table > :not(caption) > * > * {
        vertical-align: middle;
    }

    .dropdown-menu {
        max-height: 300px;
        overflow-y: auto;
    }

</style>

{{-- =============================================================
JAVASCRIPT
============================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Elements
    |--------------------------------------------------------------------------
    */

    const form =
        document.getElementById('supportAdminForm');

    const selectAllButton =
        document.getElementById('selectAllPermissions');

    const clearAllButton =
        document.getElementById('clearAllPermissions');

    const countElement =
        document.getElementById('selectedPermissionCount');


    /*
    |--------------------------------------------------------------------------
    | Update Permission Count
    |--------------------------------------------------------------------------
    */

    function updatePermissionCount() {

        const checked =
            document.querySelectorAll(
                '.permission-checkbox:checked'
            );

        countElement.textContent =
            checked.length;
    }


    /*
    |--------------------------------------------------------------------------
    | Select All
    |--------------------------------------------------------------------------
    */

    selectAllButton?.addEventListener(
        'click',
        function () {

            document
                .querySelectorAll('.permission-checkbox')
                .forEach(function (checkbox) {

                    checkbox.checked = true;

                });

            updatePermissionCount();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Clear All
    |--------------------------------------------------------------------------
    */

    clearAllButton?.addEventListener(
        'click',
        function () {

            document
                .querySelectorAll('.permission-checkbox')
                .forEach(function (checkbox) {

                    checkbox.checked = false;

                });

            updatePermissionCount();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Module Select All
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.permission-module')
        .forEach(function (module) {

            const selectButton =
                module.querySelector(
                    '.module-select-all'
                );

            const clearButton =
                module.querySelector(
                    '.module-clear-all'
                );


            selectButton?.addEventListener(
                'click',
                function () {

                    module
                        .querySelectorAll('.permission-checkbox')
                        .forEach(function (checkbox) {

                            checkbox.checked = true;

                        });

                    updatePermissionCount();

                }
            );


            clearButton?.addEventListener(
                'click',
                function () {

                    module
                        .querySelectorAll('.permission-checkbox')
                        .forEach(function (checkbox) {

                            checkbox.checked = false;

                        });

                    updatePermissionCount();

                }
            );

        });


    /*
    |--------------------------------------------------------------------------
    | Permission Checkbox Change
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.permission-checkbox')
        .forEach(function (checkbox) {

            checkbox.addEventListener(
                'change',
                updatePermissionCount
            );

        });


    /*
    |--------------------------------------------------------------------------
    | Panel Collapse
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.panel-toggle')
        .forEach(function (button) {

            button.addEventListener(
                'click',
                function () {

                    const panel =
                        button.closest(
                            '.permission-panel'
                        );

                    const body =
                        panel.querySelector(
                            '.permission-panel-body'
                        );

                    const icon =
                        button.querySelector('i');


                    if (
                        body.style.display ===
                        'none'
                    ) {

                        body.style.display = '';

                        icon.className =
                            'bi bi-chevron-up';

                    } else {

                        body.style.display =
                            'none';

                        icon.className =
                            'bi bi-chevron-down';

                    }

                }
            );

        });


    /*
    |--------------------------------------------------------------------------
    | Password Toggle
    |--------------------------------------------------------------------------
    */

    window.togglePassword =
        function (inputId, button) {

            const input =
                document.getElementById(
                    inputId
                );

            const icon =
                button.querySelector('i');


            if (
                input.type ===
                'password'
            ) {

                input.type = 'text';

                icon.className =
                    'bi bi-eye-slash';

            } else {

                input.type = 'password';

                icon.className =
                    'bi bi-eye';

            }

        };


    /*
    |--------------------------------------------------------------------------
    | Submit Protection
    |--------------------------------------------------------------------------
    */

    form?.addEventListener(
        'submit',
        function () {

            const button =
                document.getElementById(
                    'submitButton'
                );

            if (button) {

                button.disabled = true;

                button.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-1"></span>' +
                    ' তৈরি হচ্ছে...';

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Initial Count
    |--------------------------------------------------------------------------
    */

    updatePermissionCount();

});

</script>

@endsection
