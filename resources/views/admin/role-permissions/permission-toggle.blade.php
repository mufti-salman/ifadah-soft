@if(isset($actions[$type]))

    @php
        $permission = $actions[$type];

        $checked = $selectedInstitution
            ->permissions
            ->contains('id', $permission->id);
    @endphp

    <input
        type="checkbox"
        class="permission-checkbox d-none"
        id="permission{{ $permission->id }}"
        name="permissions[]"
        value="{{ $permission->id }}"
        {{ $checked ? 'checked' : '' }}
    >

    <button
        type="button"
        class="permission-toggle {{ $checked ? 'permission-active' : 'permission-inactive' }}"
        data-target="permission{{ $permission->id }}"
        onclick="togglePermission(this)"
        title="{{ $permission->slug }}"
    >
        {{ $checked ? 'Active' : 'Inactive' }}
    </button>

@else

    <span class="text-muted">—</span>

@endif