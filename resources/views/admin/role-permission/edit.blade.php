@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Edit Role: {{ $role->display_name }}</h1>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Roles
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Role Details
        </div>
        <div class="card-body">
            <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $role->name) }}" 
                               required
                               placeholder="Enter unique role name (e.g., admin_manager)">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Display Name <span class="text-danger">*</span></label>
                        <input type="text" name="display_name" 
                               class="form-control @error('display_name') is-invalid @enderror" 
                               value="{{ old('display_name', $role->display_name) }}" 
                               required
                               placeholder="Enter display name (e.g., Admin Manager)">
                        @error('display_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" 
                              class="form-control @error('description') is-invalid @enderror" 
                              rows="3" 
                              placeholder="Optional: Provide a brief description of the role">{{ old('description', $role->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if(!$role->is_system_role)
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_system_role" 
                                   class="form-check-input" 
                                   id="isSystemRole" 
                                   value="1" 
                                   {{ old('is_system_role', $role->is_system_role) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isSystemRole">
                                Mark as System Role (Cannot be deleted)
                            </label>
                        </div>
                    </div>
                @endif

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(!$role->is_system_role)
        <div class="card mt-3">
            <div class="card-header bg-danger text-white">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Danger Zone
            </div>
            <div class="card-body">
                <div class="alert alert-danger">
                    <strong>Warning:</strong> Deleting this role will remove it from all assigned entities.
                </div>
                <form action="{{ route('admin.roles.destroy', $role->id) }}" 
                      method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this role? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Delete Role
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection