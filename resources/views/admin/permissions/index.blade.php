@extends('layouts.admin')

@section('title', 'Permissions')

@section('page')

<div class="container-fluid">

    <div class="card shadow-sm border-0">

        <div class="card-header bg-white d-flex justify-content-between align-items-center">

            <div>
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-key me-2"></i>
                    Permissions
                </h5>

                <small class="text-muted">
                    Manage all system permissions.
                </small>
            </div>

            <button
                class="btn btn-primary"
                id="addPermissionBtn">

                <i class="fas fa-plus me-1"></i>
                Add Permission

            </button>

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th width="60">#</th>

                            <th>System Panel</th>

                            <th>Permission</th>

                            <th>Slug</th>

                            <th width="90">Serial</th>

                            <th width="120">Status</th>

                            <th width="170">Action</th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($permissions as $permission)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>


                                <td>

                                    {{ $permission->systemPanel?->panel_name }}

                                </td>


                                <td>

                                    <strong>
                                        {{ $permission->permission_name }}
                                    </strong>

                                </td>


                                <td>

                                    <code>
                                        {{ $permission->slug }}
                                    </code>

                                </td>


                                <td>
                                    {{ $permission->serial }}
                                </td>


                                <td>

                                    @if($permission->is_active)

                                        <span class="badge bg-success">
                                            Active
                                        </span>

                                    @else

                                        <span class="badge bg-danger">
                                            Inactive
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    <button
                                        type="button"
                                        class="btn btn-warning btn-sm editPermissionBtn"

                                        data-id="{{ $permission->id }}"

                                        data-panel="{{ $permission->system_panel_id }}"

                                        data-name="{{ $permission->permission_name }}"

                                        data-slug="{{ $permission->slug }}"

                                        data-serial="{{ $permission->serial }}"

                                        data-description="{{ $permission->description }}"

                                        data-status="{{ $permission->is_active }}">

                                        <i class="fas fa-edit"></i>

                                    </button>


                                    <form
                                        action="{{ route('dashboard.permissions.destroy', $permission->id) }}"
                                        method="POST"
                                        class="d-inline">

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete this permission?')">

                                            <i class="fas fa-trash"></i>

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="text-center py-5 text-muted">

                                    No Permission Found.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<!-- Add / Edit Permission Modal -->

<div
    class="modal fade"
    id="permissionModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-lg">

        <form
            id="permissionForm"
            method="POST">

            @csrf

            <input
                type="hidden"
                name="_method"
                id="formMethod"
                value="POST">


            <div class="modal-content">

                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="modalTitle">

                        Add Permission

                    </h5>


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">

                    </button>

                </div>


                <div class="modal-body">

                    <div class="row">


                        <!-- System Panel -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                System Panel
                            </label>

                            <select
                                class="form-select"
                                name="system_panel_id"
                                id="system_panel_id"
                                required>

                                <option value="">
                                    Select Panel
                                </option>

                                @foreach($panels as $panel)

                                    <option
                                        value="{{ $panel->id }}">

                                        {{ $panel->panel_name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <!-- Permission Name -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Permission Name
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="permission_name"
                                id="permission_name"
                                required>

                        </div>


                        <!-- Slug -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Slug
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="slug"
                                id="slug"
                                placeholder="student.view"
                                required>

                        </div>


                        <!-- Serial -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Serial
                            </label>

                            <input
                                type="number"
                                class="form-control"
                                name="serial"
                                id="serial"
                                value="0">

                        </div>


                        <!-- Description -->

                        <div class="col-12 mb-3">

                            <label class="form-label">
                                Description
                            </label>

                            <textarea
                                class="form-control"
                                rows="3"
                                name="description"
                                id="description"></textarea>

                        </div>


                        <!-- Status -->

                        <div class="col-md-4">

                            <label class="form-label d-block">
                                Status
                            </label>

                            <div class="form-check form-switch">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="is_active"
                                    name="is_active"
                                    value="1"
                                    checked>

                                <label
                                    class="form-check-label"
                                    for="is_active">

                                    Active

                                </label>

                            </div>

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Close

                    </button>


                    <button
                        class="btn btn-primary"
                        type="submit">

                        Save Permission

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const modalElement =
        document.getElementById('permissionModal');

    const modal =
        new bootstrap.Modal(modalElement);

    const form =
        document.getElementById('permissionForm');


    /*
    |--------------------------------------------------------------------------
    | Add Permission
    |--------------------------------------------------------------------------
    */

    const addButton =
        document.getElementById('addPermissionBtn');

    if (addButton) {

        addButton.addEventListener('click', function () {

            form.reset();

            document.getElementById('modalTitle').innerHTML =
                'Add Permission';

            document.getElementById('formMethod').value =
                'POST';

            form.action =
                "{{ route('dashboard.permissions.store') }}";

            document.getElementById('is_active').checked =
                true;

            modal.show();

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Edit Permission
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.editPermissionBtn')
        .forEach(function (btn) {

            btn.addEventListener('click', function () {

                form.action =
                    "/dashboard/permissions/" +
                    this.dataset.id;

                document.getElementById('formMethod').value =
                    'PUT';

                document.getElementById('modalTitle').innerHTML =
                    'Edit Permission';


                document.getElementById('system_panel_id').value =
                    this.dataset.panel;


                document.getElementById('permission_name').value =
                    this.dataset.name;


                document.getElementById('slug').value =
                    this.dataset.slug;


                document.getElementById('serial').value =
                    this.dataset.serial;


                document.getElementById('description').value =
                    this.dataset.description;


                document.getElementById('is_active').checked =
                    this.dataset.status == 1;


                modal.show();

            });

        });

});

</script>

@endpush

@endsection