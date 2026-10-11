@extends('layouts.admin')

@section('title', 'সাপোর্ট এডমিন সম্পাদনা')

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
                সাপোর্ট এডমিন সম্পাদনা
            </h4>

        </div>

        <p class="text-muted mb-0">
            সাপোর্ট এডমিনের তথ্য এবং পৃষ্ঠাভিত্তিক অ্যাক্সেস পারমিশন পরিবর্তন করুন।
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
    UPDATE FORM
========================================================== --}}

<form method="POST"
      action="{{ route('dashboard.support-admins.update', $supportAdmin->id) }}"
      id="supportAdminForm">

    @csrf

    @method('PUT')


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
                        সাপোর্ট এডমিনের তথ্য আপডেট করুন
                    </small>

                </div>

            </div>

        </div>


        <div class="card-body">

            <div class="row g-3">


                {{-- Name --}}

                <div class="col-lg-6">

                    <label class="form-label fw-semibold">

                        এডমিনের নাম

                        <span class="text-danger">*</span>

                    </label>

                    <input type="text"
                           name="name"
                           value="{{ old('name', $supportAdmin->name) }}"
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
                           value="{{ old('name_bn', $supportAdmin->name_bn) }}"
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

                        এই অ্যাকাউন্টের Role
                        Support Admin হিসেবে নির্ধারিত।

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
                           value="{{ old('phone', $supportAdmin->phone) }}"
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

                        <span class="text-muted fw-normal">
                            (ঐচ্ছিক)
                        </span>

                    </label>

                    <input type="email"
                           name="email"
                           value="{{ old('email', $supportAdmin->email) }}"
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

                        নতুন পাসওয়ার্ড

                        <span class="text-muted fw-normal">
                            (ঐচ্ছিক)
                        </span>

                    </label>

                    <div class="input-group">

                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="পরিবর্তন করতে চাইলে নতুন পাসওয়ার্ড দিন">

                        <button type="button"
                                class="btn btn-light border"
                                onclick="togglePassword('password', this)">

                            <i class="bi bi-eye"></i>

                        </button>

                    </div>

                    <div class="form-text">
                        পাসওয়ার্ড পরিবর্তন না করলে এই ঘরটি ফাঁকা রাখুন।
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

                        নতুন পাসওয়ার্ড নিশ্চিত করুন

                        <span class="text-muted fw-normal">
                            (ঐচ্ছিক)
                        </span>

                    </label>

                    <div class="input-group">

                        <input type="password"
                               name="password_confirmation"
                               id="password_confirmation"
                               class="form-control"
                               placeholder="নতুন পাসওয়ার্ড আবার লিখুন">

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

                    @php

                        $currentStatus = old(
                            'status',
                            (string) $supportAdmin->status
                        );

                    @endphp

                    <div class="d-flex gap-3 pt-1">

                        <div class="form-check">

                            <input class="form-check-input"
                                   type="radio"
                                   name="status"
                                   id="statusActive"
                                   value="1"
                                   {{ $currentStatus == '1' ? 'checked' : '' }}>

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
                                   {{ $currentStatus == '0' ? 'checked' : '' }}>

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
        PERMISSION DATA
    ====================================================== --}}

    @php

        /*
         * Support Admin-এর বর্তমানে assigned permission ID.
         */
        $assignedPermissionIds =
            $supportAdmin->permissions
                ? $supportAdmin->permissions->pluck('id')->toArray()
                : [];


        /*
         * Validation fail করলে old permissions ব্যবহার করবো।
         */
        $selectedPermissions =
            old('permissions', $assignedPermissionIds);


        if (!is_array($selectedPermissions)) {
            $selectedPermissions = [];
        }


        /*
         * Standard permission actions.
         */
        $standardActions = [
            'view',
            'create',
            'edit',
            'delete',
        ];

    @endphp



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
                PANEL GROUPS
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

                    $panelModules =
                        $panelPermissions->groupBy('module');

                @endphp


                <div class="permission-panel mb-4"
                     data-panel="{{ $panelSlug }}">


                    {{-- =================================================
                        PANEL HEADER
                    ================================================== --}}

                    <div class="d-flex align-items-center
                                justify-content-between
                                bg-light border rounded-top px-3 py-3">

                        <div class="d-flex align-items-center gap-2">

                            <div class="panel-icon">

                                <i class="bi bi-grid-1x2 text-primary"></i>

                            </div>

                            <div>

                                <div class="fw-bold">

                                    {{ $panelName }}

                                </div>

                                <small class="text-muted">

                                    {{ $panelPermissions->count() }}
                                    টি permission

                                </small>

                            </div>

                        </div>


                        <button type="button"
                                class="btn btn-sm btn-link
                                       text-decoration-none
                                       panel-toggle">

                            <i class="bi bi-chevron-up"></i>

                        </button>

                    </div>



                    {{-- =================================================
                        PANEL TABLE
                    ================================================== --}}

                    <div class="permission-panel-body
                                border border-top-0
                                rounded-bottom">

                        <table class="table table-hover
                                      align-middle
                                      mb-0 permission-matrix">

                            <thead>

                                <tr>

                                    <th class="permission-number">
                                        #
                                    </th>

                                    <th class="permission-name">
                                        পেজ / পারমিশন
                                    </th>

                                    <th class="permission-action">
                                        View
                                    </th>

                                    <th class="permission-action">
                                        Insert
                                    </th>

                                    <th class="permission-action">
                                        Edit
                                    </th>

                                    <th class="permission-action">
                                        Delete
                                    </th>

                                </tr>

                            </thead>



                            {{-- =================================================
                                MODULE GROUPS
                            ================================================== --}}

                            @php
                                $rowNumber = 0;
                            @endphp


                            @foreach($panelModules as $moduleName => $modulePermissions)

                                @php

                                    $rowNumber++;

                                    /*
                                     * Standard permissions.
                                     */
                                    $permissionMap = [];

                                    foreach ($standardActions as $action) {

                                        $permissionMap[$action] =
                                            $modulePermissions->first(
                                                fn($item) =>
                                                    str_ends_with(
                                                        $item->slug,
                                                        '.' . $action
                                                    )
                                            );

                                    }


                                    /*
                                     * Standard permission IDs.
                                     */
                                    $standardPermissionIds =
                                        collect($permissionMap)
                                            ->filter()
                                            ->pluck('id')
                                            ->toArray();


                                    /*
                                     * Non-standard permissions.
                                     *
                                     * এগুলো আলাদা "Others" column-এ
                                     * যাবে না।
                                     *
                                     * নিচে আলাদা row হিসেবে দেখানো হবে।
                                     */
                                    $extraPermissions =
                                        $modulePermissions->filter(
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


                                <tbody class="permission-module">


                                    {{-- =================================================
                                        MODULE ROW
                                    ================================================== --}}

                                    <tr class="permission-module-row">

                                        <td class="text-center fw-semibold text-muted">
                                            {{ $rowNumber }}
                                        </td>


                                        <td>

                                            <div class="d-flex flex-wrap
                                                        align-items-center
                                                        gap-2">

                                                <span class="fw-semibold">

                                                    {{ $moduleName }}

                                                </span>

                                                <span class="badge
                                                             bg-light
                                                             text-muted
                                                             border">

                                                    {{ $modulePermissions->count() }}
                                                    permission

                                                </span>

                                            </div>

                                        </td>


                                        {{-- View --}}

                                        @foreach($standardActions as $action)

                                            @php
                                                $permission =
                                                    $permissionMap[$action] ?? null;
                                            @endphp

                                            <td class="text-center">

                                                @if($permission)

                                                    <label class="permission-check">

                                                        <input type="checkbox"
                                                               name="permissions[]"
                                                               value="{{ $permission->id }}"
                                                               class="form-check-input permission-checkbox"
                                                               data-module="{{ $moduleName }}"
                                                               data-action="{{ $action }}"
                                                               {{ in_array(
                                                                    $permission->id,
                                                                    $selectedPermissions
                                                                ) ? 'checked' : '' }}>

                                                        <span class="permission-status">
                                                            Active
                                                        </span>

                                                    </label>

                                                @else

                                                    <span class="text-muted">
                                                        —
                                                    </span>

                                                @endif

                                            </td>

                                        @endforeach

                                    </tr>



                                    {{-- =================================================
                                        EXTRA PERMISSIONS
                                    ================================================== --}}

                                    @foreach($extraPermissions as $extraPermission)

                                        @php

                                            $extraAction =
                                                last(
                                                    explode(
                                                        '.',
                                                        $extraPermission->slug
                                                    )
                                                );

                                            $rowNumber++;

                                        @endphp


                                        <tr class="permission-extra-row">

                                            <td class="text-center text-muted">
                                                {{ $rowNumber }}
                                            </td>


                                            <td>

                                                <div class="d-flex align-items-center
                                                            gap-2 ps-3">

                                                    <span class="text-muted">
                                                        ↳
                                                    </span>

                                                    <div>

                                                        <div class="fw-medium">

                                                            {{ $extraPermission->permission_name }}

                                                        </div>

                                                        <small class="text-muted">

                                                            {{ $extraAction }}

                                                        </small>

                                                    </div>

                                                </div>

                                            </td>


                                            <td class="text-center">

                                                <span class="text-muted">
                                                    —
                                                </span>

                                            </td>


                                            <td class="text-center">

                                                <span class="text-muted">
                                                    —
                                                </span>

                                            </td>


                                            <td class="text-center">

                                                <span class="text-muted">
                                                    —
                                                </span>

                                            </td>


                                            <td class="text-center">

                                                <label class="permission-check">

                                                    <input type="checkbox"
                                                           name="permissions[]"
                                                           value="{{ $extraPermission->id }}"
                                                           class="form-check-input permission-checkbox"
                                                           data-module="{{ $moduleName }}"
                                                           data-action="{{ $extraAction }}"
                                                           {{ in_array(
                                                                $extraPermission->id,
                                                                $selectedPermissions
                                                            ) ? 'checked' : '' }}>

                                                    <span class="permission-status">
                                                        Active
                                                    </span>

                                                </label>

                                            </td>

                                        </tr>

                                    @endforeach


                                </tbody>

                            @endforeach


                        </table>

                    </div>

                </div>

            @endforeach


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

                    <i class="bi bi-shield-check me-1"></i>

                    সাপোর্ট এডমিন আপডেট করুন

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


    .permission-panel-body {
        background: #fff;
    }


    /*
    |--------------------------------------------------------------------------
    | Permission Matrix
    |--------------------------------------------------------------------------
    */

    .permission-matrix {
        width: 100%;
        table-layout: fixed;
    }


    .permission-matrix thead th {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        font-size: 13px;
        font-weight: 700;
        color: #495057;
        padding: 12px 10px;
        vertical-align: middle;
    }


    .permission-matrix tbody td {
        padding: 11px 10px;
        vertical-align: middle;
        border-color: #edf0f2;
    }


    .permission-number {
        width: 55px;
        text-align: center;
    }


    .permission-name {
        width: 46%;
        text-align: left;
    }


    .permission-action {
        width: 12%;
        text-align: center;
    }


    /*
    |--------------------------------------------------------------------------
    | Module Row
    |--------------------------------------------------------------------------
    */

    .permission-module-row {
        background: #fff;
    }


    .permission-module-row:hover {
        background: #f8fbff;
    }


    .permission-module-row td {
        font-size: 14px;
    }


    /*
    |--------------------------------------------------------------------------
    | Extra Permission Row
    |--------------------------------------------------------------------------
    */

    .permission-extra-row {
        background: #fafafa;
    }


    .permission-extra-row td {
        font-size: 13px;
    }


    .permission-extra-row .fw-medium {
        font-weight: 500;
    }


    /*
    |--------------------------------------------------------------------------
    | Checkbox
    |--------------------------------------------------------------------------
    */

    .permission-check {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        cursor: pointer;
        margin: 0;
    }


    .permission-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
        margin: 0;
    }


    .permission-checkbox:checked {
        accent-color: var(--bs-primary);
    }


    /*
    |--------------------------------------------------------------------------
    | Active Status
    |--------------------------------------------------------------------------
    */

    .permission-status {
        display: none;
        font-size: 11px;
        font-weight: 600;
        color: var(--bs-success);
        white-space: nowrap;
    }


    .permission-checkbox:checked + .permission-status {
        display: inline-flex;
    }


    /*
    |--------------------------------------------------------------------------
    | Panel
    |--------------------------------------------------------------------------
    */

    .permission-panel-body {
        transition: all 0.2s ease;
    }


    .panel-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }


    /*
    |--------------------------------------------------------------------------
    | Mobile
    |--------------------------------------------------------------------------
    */

    @media (max-width: 768px) {

        .permission-matrix thead th,
        .permission-matrix tbody td {
            padding: 8px 5px;
            font-size: 12px;
        }


        .permission-number {
            width: 35px;
        }


        .permission-name {
            width: 40%;
        }


        .permission-action {
            width: 14%;
        }


        .permission-status {
            display: none !important;
        }


        .permission-checkbox {
            width: 16px;
            height: 16px;
        }


        .permission-extra-row .ps-3 {
            padding-left: 5px !important;
        }

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
    | Module Select / Clear
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.permission-module')
        .forEach(function (module) {


            /*
             * Module row-এ select / clear button নেই।
             *
             * তাই ভবিষ্যতে যদি module controls যোগ করা হয়,
             * এই existing logic কাজ করবে।
             */

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
                    ' আপডেট হচ্ছে...';

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