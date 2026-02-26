@extends('layouts.master')

@section('title', 'Edit User Roles')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Edit User Roles and Permissions</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
            <li class="breadcrumb-item active">Edit User Roles</li>
        </ol>

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-user-tag me-1"></i>
                Manage Roles and Permissions for {{ $userName }}
                <span class="badge bg-success ms-2">{{ $type }}</span>
            </div>
            <div class="card-body">
                <form id="editRolesForm" action="{{ route('admin.users.update', ['type' => $type, 'id' => $user->id]) }}"
                    method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="user_type" value="{{ $type }}">

                    <div class="mb-4">
                        <h4>User Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p>
                                    <strong>Name:</strong>
                                    {{ $userName }}
                                </p>
                                <p><strong>Email:</strong> {{ $user->email }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>ID:</strong> {{ $user->id }}</p>
                                <p><strong>Created:</strong> {{ $user->created_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h4>Assign Roles</h4>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Assigning roles will automatically grant all permissions associated with those roles.
                        </div>

                        <div class="row mt-3">
                            @foreach ($roles as $role)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input role-checkbox" type="checkbox" name="roles[]"
                                            value="{{ $role->id }}" id="role-{{ $role->id }}"
                                            @if (in_array($role->id, $userRoleIds)) checked @endif>
                                        <label class="form-check-label" for="role-{{ $role->id }}">
                                            <strong>{{ $role->display_name }}</strong>
                                            <small class="text-muted d-block">{{ $role->description }}</small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-4">
                        <h4>Current Permissions</h4>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            These permissions are inherited from the user's roles. You cannot directly assign individual
                            permissions.
                        </div>

                        <div class="row mt-3">
                            @if (count($userPermissions) > 0)
                                @foreach ($permissions as $permission)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                @if (in_array($permission->id, $userPermissions)) checked @endif disabled>
                                            <label
                                                class="form-check-label 
                                            @if (in_array($permission->id, $userPermissions)) fw-bold @else text-muted @endif">
                                                {{ $permission->display_name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <p class="text-muted">No permissions assigned yet. Assign a role to grant permissions.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('scripts')
    <!-- jQuery (if not already included) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // Configure Toastr default options
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "timeOut": "2000"
            };

            // Handle form submission via AJAX
            $('#editRolesForm').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);

                            setTimeout(function() {
                                window.location.href = response.redirectUrl;
                            }, 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'An error occurred while updating roles.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        toastr.error(errorMessage);
                    }
                });
            });

            // Optional: Dynamic permission preview when roles are selected
            $('.role-checkbox').on('change', function() {
                var selectedRoles = $('.role-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                // AJAX call to get permissions for selected roles
                $.ajax({
                    url: '{{ route("admin.roles.index") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        role_ids: selectedRoles
                    },
                    success: function(response) {
                        // Update permissions display if needed
                        console.log(response.permissions);
                    }
                });
            });
        });
    </script>
@endsection