@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Create New Role</h1>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus me-1"></i>
            Role Details
        </div>
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" required
                               placeholder="Enter unique role name (e.g., admin_manager)">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Display Name <span class="text-danger">*</span></label>
                        <input type="text" name="display_name" class="form-control @error('display_name') is-invalid @enderror" 
                               value="{{ old('display_name') }}" required
                               placeholder="Enter display name (e.g., Admin Manager)">
                        @error('display_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="3" 
                              placeholder="Optional: Provide a brief description of the role">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_system_role" class="form-check-input" id="isSystemRole" 
                               value="1" {{ old('is_system_role') ? 'checked' : '' }}>
                        <label class="form-check-label" for="isSystemRole">
                            System Role (Cannot be deleted)
                        </label>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Roles
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection