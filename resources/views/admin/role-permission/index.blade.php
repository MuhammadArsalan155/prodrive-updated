@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Roles Management</h1>
        <div>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary me-2">
                <i class="fas fa-plus me-1"></i>Create New Role
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-success">
                <i class="fas fa-user-tag me-1"></i>Assign Roles
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Roles List
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="rolesTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Display Name</th>
                            <th>Description</th>
                            <th>Total Users</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->display_name }}</td>
                                <td>{{ $role->description }}</td>
                                <td>
                                    {{ 
                                        $roleCounts[$role->id]['student_count'] + 
                                        $roleCounts[$role->id]['user_count'] + 
                                        $roleCounts[$role->id]['instructor_count'] 
                                    }}
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.roles.edit-permissions', $role->id) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-key"></i> Manage Permissions
                                        </a>
                                        <a href="{{ route('admin.roles.edit', $role->id) }}" 
                                           class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        @if(!$role->is_system_role)
                                            <form action="{{ route('admin.roles.destroy', $role->id) }}" 
                                                  method="POST" 
                                                  class="d-inline delete-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this role?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // DataTable initialization
    $('#rolesTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        columnDefs: [
            { 
                targets: -1, 
                orderable: false 
            }
        ]
    });
});
</script>
@endsection