@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Manage Permissions for "{{ $role->display_name }}"</h1>
        <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Role Details
        </a>
    </div>

    <form action="{{ route('admin.roles.update-permissions', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        @foreach($permissions->sortKeys() as $group => $groupPermissions)
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        {{ ucfirst(str_replace('_', ' ', $group)) }} Permissions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($groupPermissions as $permission)
                            <div class="col-md-4 col-lg-3 mb-2">
                                <div class="form-check">
                                    <input 
                                        type="checkbox" 
                                        name="permissions[]" 
                                        value="{{ $permission->id }}" 
                                        id="permission-{{ $permission->id }}"
                                        class="form-check-input"
                                        {{ in_array($permission->id, $rolePermissionIds) ? 'checked' : '' }}
                                    >
                                    <label 
                                        class="form-check-label" 
                                        for="permission-{{ $permission->id }}"
                                        data-bs-toggle="tooltip"
                                        title="{{ $permission->description ?? $permission->display_name }}"
                                    >
                                        {{ $permission->display_name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i>Cancel
            </a>
            <div>
                <button type="button" id="selectAllPermissions" class="btn btn-info me-2">
                    <i class="fas fa-check-square me-1"></i>Select All
                </button>
                <button type="button" id="deselectAllPermissions" class="btn btn-warning me-2">
                    <i class="fas fa-square me-1"></i>Deselect All
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Save Permissions
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Select all permissions
    $('#selectAllPermissions').on('click', function() {
        $('input[name="permissions[]"]').prop('checked', true);
    });

    // Deselect all permissions
    $('#deselectAllPermissions').on('click', function() {
        $('input[name="permissions[]"]').prop('checked', false);
    });

    // Enable tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endsection