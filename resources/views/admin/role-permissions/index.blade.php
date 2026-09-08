@extends('layouts.admin')

@section('title', 'Institution Permission Manager')

@section('page')

@push('styles')

<style>

/* =========================================================
   INSTITUTION PERMISSION MANAGER
========================================================= */

.permission-manager-card {
    border: 0 !important;
    border-radius: 14px;
    overflow: hidden;
}


/* =========================================================
   MAIN HEADER
========================================================= */

.permission-manager-header {
    padding: 20px 24px;
    background: var(--card, #ffffff) !important;
    border-bottom: 1px solid var(--border, #e5e7eb) !important;
}

.permission-manager-header h4 {
    font-size: 21px;
    font-weight: 700;
    color: var(--text, #111827);
}

.permission-manager-header small {
    font-size: 13px;
}


/* Current Institution Badge */

.current-institution-badge {
    padding: 8px 15px;
    border-radius: 30px;
    font-size: 13px !important;
    font-weight: 600;
    background: linear-gradient(135deg, #4f46e5, #6366f1) !important;
    box-shadow: 0 3px 8px rgba(79, 70, 229, .25);
}


/* =========================================================
   FILTER AREA
========================================================= */

.permission-filter {
    padding: 4px 0 8px;
}

.permission-filter label {
    font-size: 13px;
    color: var(--text, #374151);
    margin-bottom: 7px;
}

.permission-filter .form-select,
.permission-filter .form-control {
    min-height: 43px;
    border-radius: 8px;
    border: 1px solid #dfe3e8;
    box-shadow: none;
    font-size: 14px;
    transition: .2s ease;
}

.permission-filter .form-select:focus,
.permission-filter .form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, .08);
}


/* Search */

#permissionSearch {
    padding-left: 14px;
}


/* =========================================================
   GLOBAL TOGGLE
========================================================= */

#toggleEverything {
    min-height: 43px;
    border-radius: 8px;
    padding: 8px 18px;
    font-size: 13px;
    font-weight: 600;
    transition: all .2s ease;
    border: 0;
}

#toggleEverything:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, .12);
}


/* =========================================================
   PERMISSION PANEL
========================================================= */

.permission-panel {
    border: 1px solid var(--border, #e5e7eb) !important;
    border-radius: 12px !important;
    overflow: hidden;
    background: var(--card, #fff);
    box-shadow: 0 2px 10px rgba(0, 0, 0, .05) !important;
    transition: .2s ease;
}

.permission-panel:hover {
    box-shadow: 0 5px 18px rgba(0, 0, 0, .07) !important;
}


/* =========================================================
   PANEL HEADER
========================================================= */

.permission-panel .card-header {
    padding: 14px 18px;
    background: #f8f9fa !important;
    border-bottom: 1px solid #e5e7eb !important;
}

.permission-panel h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #263238;
}

.permission-panel h5 i {
    font-size: 15px;
    color: #4f46e5;
}


/* =========================================================
   PANEL TOGGLE BUTTON
========================================================= */

.panel-toggle {
    min-width: 105px;
    border-radius: 20px;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 600;
    border: 0;
    transition: all .2s ease;
}

.panel-toggle:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, .12);
}


/* =========================================================
   PERMISSION TABLE
========================================================= */

.permission-table {
    margin: 0 !important;
    border-color: #e8ebef;
}

.permission-table thead th {
    background: #f4f6f8 !important;
    color: #495057;
    text-align: center;
    vertical-align: middle;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .25px;
    padding: 12px 10px;
    border-color: #e5e7eb;
}

.permission-table thead th:first-child {
    width: 60px;
}

.permission-table thead th:nth-child(2) {
    text-align: left;
}

.permission-table tbody td {
    text-align: center;
    vertical-align: middle;
    padding: 12px 10px;
    border-color: #edf0f2;
    font-size: 13px;
}

.permission-table tbody td:first-child {
    color: #6c757d;
    font-size: 12px;
    font-weight: 600;
}

.permission-table tbody td:nth-child(2) {
    text-align: left;
}

.permission-table tbody td:nth-child(2) strong {
    color: #374151;
    font-weight: 600;
}

.permission-row {
    transition: background-color .15s ease;
}

.permission-row:hover {
    background: #f8fafc !important;
}


/* =========================================================
   ACTIVE / INACTIVE BUTTON
========================================================= */

.permission-toggle {
    min-width: 82px;
    border: 0;
    border-radius: 20px;
    padding: 6px 13px;
    font-size: 11px;
    font-weight: 700;
    line-height: 1.4;
    color: #fff;
    cursor: pointer;
    transition: all .2s ease;
    box-shadow: none;
    font-family: inherit;
    letter-spacing: .2px;
}

.permission-toggle.permission-active {
    background: #198754 !important;
    color: #fff !important;
}

.permission-toggle.permission-active:hover {
    background: #157347 !important;
    transform: translateY(-1px);
}

.permission-toggle.permission-inactive {
    background: #dc3545 !important;
    color: #fff !important;
}

.permission-toggle.permission-inactive:hover {
    background: #bb2d3b !important;
    transform: translateY(-1px);
}


/* =========================================================
   SAVE BUTTON
========================================================= */

.permission-save-btn {
    min-width: 180px;
    border-radius: 8px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 600;
    transition: all .2s ease;
}

.permission-save-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 14px rgba(13, 110, 253, .25);
}

.permission-divider {
    margin: 22px 0;
    border-color: #e5e7eb;
    opacity: 1;
}


/* =========================================================
   RESPONSIVE
========================================================= */

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

@media (max-width: 767.98px) {

    .permission-manager-header {
        padding: 16px;
    }

    .permission-manager-header h4 {
        font-size: 17px;
    }

    .current-institution-badge {
        font-size: 11px !important;
        padding: 6px 10px;
    }

    .permission-table {
        min-width: 760px;
    }

    .permission-save-btn {
        width: 100%;
    }
}


/* =========================================================
   DARK MODE
========================================================= */

body.dark-mode .permission-panel {
    background: #0f172a;
    border-color: rgba(255,255,255,.08) !important;
}

body.dark-mode .permission-panel .card-header {
    background: #111827 !important;
    border-color: rgba(255,255,255,.08) !important;
}

body.dark-mode .permission-panel h5 {
    color: #e5e7eb;
}

body.dark-mode .permission-table {
    color: #e5e7eb;
    border-color: rgba(255,255,255,.08);
}

body.dark-mode .permission-table thead th {
    background: #172033 !important;
    color: #d1d5db;
    border-color: rgba(255,255,255,.08);
}

body.dark-mode .permission-table tbody td {
    border-color: rgba(255,255,255,.07);
}

body.dark-mode .permission-table tbody td:nth-child(2) strong {
    color: #e5e7eb;
}

body.dark-mode .permission-row:hover {
    background: #151f32 !important;
}

body.dark-mode .permission-filter .form-select,
body.dark-mode .permission-filter .form-control {
    background: #111827;
    color: #e5e7eb;
    border-color: rgba(255,255,255,.12);
}

body.dark-mode .permission-filter .form-select option {
    background: #111827;
    color: #e5e7eb;
}

</style>

@endpush


<div class="container-fluid">

    {{-- Success Message --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- Error Message --}}
    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    <div class="card shadow border-0 permission-manager-card">

        {{-- Header --}}
        <div class="card-header bg-white permission-manager-header">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h4 class="mb-1">

                        <i class="fas fa-building-shield text-primary me-2"></i>

                        Institution Permission Manager

                    </h4>

                    <small class="text-muted">

                        Manage permissions for each institution separately.

                    </small>

                </div>

                @if($selectedInstitution)

                    <span
                        class="badge current-institution-badge text-white">

                        {{ $selectedInstitution->name }}

                    </span>

                @endif

            </div>

        </div>


        <div class="card-body">

            @if($selectedInstitution)

                {{-- ============================================
                    FILTER
                ============================================= --}}

                <form method="GET">

                    <div class="row g-3 align-items-end permission-filter">

                        {{-- Institution Select --}}
                        <div class="col-md-4">

                            <label class="form-label fw-bold">

                                Select Institution

                            </label>

                            <select
                                class="form-select"
                                name="institution"
                                onchange="this.form.submit()">

                                @foreach($institutions as $institution)

                                    <option
                                        value="{{ $institution->id }}"
                                        {{ $selectedInstitution->id == $institution->id ? 'selected' : '' }}>

                                        {{ $institution->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Search --}}
                        <div class="col-md-5">

                            <label class="form-label fw-bold">

                                Search Permission

                            </label>

                            <input
                                type="text"
                                id="permissionSearch"
                                class="form-control"
                                placeholder="Search Student, Teacher, Admission...">

                        </div>


                        {{-- Global Toggle --}}
                        <div class="col-md-3 text-end">

                            <label class="form-label d-block">
                                &nbsp;
                            </label>

                            <button
                                type="button"
                                id="toggleEverything"
                                class="btn btn-success w-100">

                                <i class="fas fa-check-double me-1"></i>
                                Enable All

                            </button>

                        </div>

                    </div>

                </form>


                <hr class="permission-divider">


                {{-- ============================================
                    PERMISSION FORM
                ============================================= --}}

                <form
                    action="{{ route('dashboard.role-permissions.update') }}"
                    method="POST"
                    id="permissionForm">

                    @csrf

                    <input
                        type="hidden"
                        name="institution_id"
                        value="{{ $selectedInstitution->id }}">


                    {{-- ============================================
                        PERMISSION MATRIX
                    ============================================= --}}

                    @foreach($panels as $panel)

                        @php

                            $matrix = [];

                            foreach($panel->permissions as $permission) {

                                $parts = explode('.', $permission->slug);

                                $action = strtolower(end($parts));

                                $action = match($action) {
                                    'create' => 'insert',
                                    'add'    => 'insert',
                                    'update' => 'edit',
                                    'remove' => 'delete',
                                    default  => $action
                                };

                                $matrix[$permission->module][$action] =
                                    $permission;
                            }

                        @endphp


                        @if(count($matrix))

                            <div
                                class="card border shadow-sm mb-4 permission-panel">

                                {{-- Panel Header --}}
                                <div class="card-header bg-light">

                                    <div
                                        class="d-flex justify-content-between align-items-center">

                                        <div>

                                            <h5 class="mb-0">

                                                @if($panel->icon)

                                                    <i
                                                        class="{{ $panel->icon }} me-2">
                                                    </i>

                                                @endif

                                                {{ $panel->panel_name }}

                                            </h5>

                                        </div>


                                        <button
                                            type="button"
                                            class="btn btn-success btn-sm panel-toggle">

                                            <i class="fas fa-check-double me-1"></i>

                                            Enable All

                                        </button>

                                    </div>

                                </div>


                                <div class="table-responsive">

                                    <table
                                        class="table table-bordered table-hover align-middle permission-table mb-0">

                                        <thead class="table-light">

                                            <tr>

                                                <th width="70">#</th>

                                                <th>Power Name</th>

                                                <th width="120">
                                                    View
                                                </th>

                                                <th width="120">
                                                    Insert
                                                </th>

                                                <th width="120">
                                                    Edit
                                                </th>

                                                <th width="120">
                                                    Delete
                                                </th>

                                            </tr>

                                        </thead>


                                        <tbody>

                                            @foreach($matrix as $moduleName => $actions)

                                                <tr
                                                    class="permission-row"
                                                    data-name="{{ strtolower($moduleName) }}">

                                                    <td>
                                                        {{ $loop->iteration }}
                                                    </td>


                                                    <td>
                                                        <strong>
                                                            {{ $moduleName }}
                                                        </strong>
                                                    </td>


                                                    {{-- VIEW --}}
                                                    <td class="text-center">

                                                        @include(
                                                            'admin.role-permissions.permission-toggle',
                                                            [
                                                                'actions' =>
                                                                    $actions,

                                                                'type' =>
                                                                    'view',

                                                                'selectedInstitution' =>
                                                                    $selectedInstitution
                                                            ]
                                                        )

                                                    </td>


                                                    {{-- INSERT --}}
                                                    <td class="text-center">

                                                        @include(
                                                            'admin.role-permissions.permission-toggle',
                                                            [
                                                                'actions' =>
                                                                    $actions,

                                                                'type' =>
                                                                    'insert',

                                                                'selectedInstitution' =>
                                                                    $selectedInstitution
                                                            ]
                                                        )

                                                    </td>


                                                    {{-- EDIT --}}
                                                    <td class="text-center">

                                                        @include(
                                                            'admin.role-permissions.permission-toggle',
                                                            [
                                                                'actions' =>
                                                                    $actions,

                                                                'type' =>
                                                                    'edit',

                                                                'selectedInstitution' =>
                                                                    $selectedInstitution
                                                            ]
                                                        )

                                                    </td>


                                                    {{-- DELETE --}}
                                                    <td class="text-center">

                                                        @include(
                                                            'admin.role-permissions.permission-toggle',
                                                            [
                                                                'actions' =>
                                                                    $actions,

                                                                'type' =>
                                                                    'delete',

                                                                'selectedInstitution' =>
                                                                    $selectedInstitution
                                                            ]
                                                        )

                                                    </td>

                                                </tr>

                                            @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        @endif

                    @endforeach


                    {{-- Save --}}
                    <div class="text-end mt-4">

                        <button
                            type="submit"
                            class="btn btn-primary btn-lg permission-save-btn">

                            <i class="fas fa-save me-2"></i>

                            Save Permissions

                        </button>

                    </div>

                </form>

            @else

                {{-- No Institution --}}
                <div class="text-center py-5 text-muted">

                    <i class="fas fa-building fa-3x mb-3"></i>

                    <h5>No Institution Found</h5>

                    <p>
                        Please create an institution first.
                    </p>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {


    /* =========================================================
       GET CHECKBOX
    ========================================================= */

    function getCheckbox(button) {

        if (!button) {
            return null;
        }

        const target = button.dataset.target;

        if (!target) {
            return null;
        }

        return document.getElementById(target);
    }


    /* =========================================================
       REFRESH BUTTON
    ========================================================= */

    function refreshButton(button) {

        const checkbox = getCheckbox(button);

        if (!checkbox) {
            return;
        }

        if (checkbox.checked) {

            button.classList.remove('permission-inactive');

            button.classList.add('permission-active');

            button.innerHTML = 'Active';

        } else {

            button.classList.remove('permission-active');

            button.classList.add('permission-inactive');

            button.innerHTML = 'Inactive';

        }
    }


    /* =========================================================
       SINGLE TOGGLE
    ========================================================= */

    window.togglePermission = function(button) {

        const checkbox = getCheckbox(button);

        if (!checkbox) {
            return;
        }

        checkbox.checked = !checkbox.checked;

        refreshButton(button);

        syncPanelButtons();
    };


    /* =========================================================
       PANEL TOGGLE
    ========================================================= */

    document
        .querySelectorAll('.panel-toggle')
        .forEach(function(panelButton) {

            panelButton.addEventListener('click', function() {

                const panel =
                    this.closest('.permission-panel');

                if (!panel) {
                    return;
                }

                const buttons =
                    panel.querySelectorAll(
                        '.permission-toggle'
                    );

                let allActive = true;

                buttons.forEach(function(button) {

                    const checkbox =
                        getCheckbox(button);

                    if (
                        checkbox &&
                        !checkbox.checked
                    ) {
                        allActive = false;
                    }

                });

                const newState = !allActive;

                buttons.forEach(function(button) {

                    const checkbox =
                        getCheckbox(button);

                    if (!checkbox) {
                        return;
                    }

                    checkbox.checked = newState;

                    refreshButton(button);
                });

                syncPanelButtons();
            });

        });


    /* =========================================================
       GLOBAL TOGGLE
    ========================================================= */

    const globalButton =
        document.getElementById('toggleEverything');

    if (globalButton) {

        globalButton.addEventListener(
            'click',
            function() {

                const buttons =
                    document.querySelectorAll(
                        '.permission-toggle'
                    );

                let allActive = true;

                buttons.forEach(function(button) {

                    const checkbox =
                        getCheckbox(button);

                    if (
                        checkbox &&
                        !checkbox.checked
                    ) {
                        allActive = false;
                    }

                });

                const newState = !allActive;

                buttons.forEach(function(button) {

                    const checkbox =
                        getCheckbox(button);

                    if (!checkbox) {
                        return;
                    }

                    checkbox.checked = newState;

                    refreshButton(button);
                });

                syncPanelButtons();
            }
        );

    }


    /* =========================================================
       UPDATE PANEL BUTTON
    ========================================================= */

    function updatePanelButton(button, active) {

        if (!button) {
            return;
        }

        if (active) {

            button.classList.remove('btn-success');

            button.classList.add('btn-danger');

            button.innerHTML =
                '<i class="fas fa-times me-1"></i> Disable All';

        } else {

            button.classList.remove('btn-danger');

            button.classList.add('btn-success');

            button.innerHTML =
                '<i class="fas fa-check-double me-1"></i> Enable All';
        }
    }


    /* =========================================================
       SYNC PANEL BUTTONS
    ========================================================= */

    function syncPanelButtons() {

        document
            .querySelectorAll('.permission-panel')
            .forEach(function(panel) {

                const buttons =
                    panel.querySelectorAll(
                        '.permission-toggle'
                    );

                if (!buttons.length) {
                    return;
                }

                let allActive = true;

                buttons.forEach(function(button) {

                    const checkbox =
                        getCheckbox(button);

                    if (
                        checkbox &&
                        !checkbox.checked
                    ) {
                        allActive = false;
                    }

                });

                const panelButton =
                    panel.querySelector('.panel-toggle');

                updatePanelButton(
                    panelButton,
                    allActive
                );
            });

        syncGlobalButton();
    }


    /* =========================================================
       SYNC GLOBAL BUTTON
    ========================================================= */

    function syncGlobalButton() {

        if (!globalButton) {
            return;
        }

        const buttons =
            document.querySelectorAll(
                '.permission-toggle'
            );

        if (!buttons.length) {
            return;
        }

        let allActive = true;

        buttons.forEach(function(button) {

            const checkbox =
                getCheckbox(button);

            if (
                checkbox &&
                !checkbox.checked
            ) {
                allActive = false;
            }

        });

        updateGlobalButton(allActive);
    }


    /* =========================================================
       UPDATE GLOBAL BUTTON
    ========================================================= */

    function updateGlobalButton(active) {

        if (!globalButton) {
            return;
        }

        if (active) {

            globalButton.classList.remove(
                'btn-success'
            );

            globalButton.classList.add(
                'btn-danger'
            );

            globalButton.innerHTML =
                '<i class="fas fa-times me-1"></i> Disable All';

        } else {

            globalButton.classList.remove(
                'btn-danger'
            );

            globalButton.classList.add(
                'btn-success'
            );

            globalButton.innerHTML =
                '<i class="fas fa-check-double me-1"></i> Enable All';
        }
    }


    /* =========================================================
       SEARCH
    ========================================================= */

    const searchBox =
        document.getElementById('permissionSearch');

    if (searchBox) {

        searchBox.addEventListener(
            'keyup',
            function() {

                const keyword =
                    this.value
                        .trim()
                        .toLowerCase();

                document
                    .querySelectorAll(
                        '.permission-panel'
                    )
                    .forEach(function(panel) {

                        let visibleRows = 0;

                        panel
                            .querySelectorAll(
                                '.permission-row'
                            )
                            .forEach(function(row) {

                                const name =
                                    (
                                        row.dataset.name ||
                                        ''
                                    ).toLowerCase();

                                if (
                                    name.includes(keyword)
                                ) {

                                    row.style.display = '';

                                    visibleRows++;

                                } else {

                                    row.style.display = 'none';
                                }

                            });

                        panel.style.display =
                            visibleRows > 0
                                ? ''
                                : 'none';
                    });

            }
        );

    }


    /* =========================================================
       INITIAL SYNC
    ========================================================= */

    document
        .querySelectorAll('.permission-toggle')
        .forEach(function(button) {
            refreshButton(button);
        });

    syncPanelButtons();

});

</script>

@endpush

