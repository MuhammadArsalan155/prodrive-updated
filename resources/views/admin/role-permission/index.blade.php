@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="pd-page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="font-weight:800;"><i class="fas fa-user-tag mr-2"></i>Roles Management</h4>
            <p style="font-size:.85rem;">Define roles and manage permission assignments for each role</p>
        </div>
        <div class="d-flex" style="gap:.5rem;">
            <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm font-weight-bold">
                <i class="fas fa-users-cog mr-1"></i>Assign Roles
            </a>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-light btn-sm font-weight-bold">
                <i class="fas fa-plus mr-1"></i>Create Role
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold" style="color:var(--pd-navy);">
                <i class="fas fa-list mr-2" style="color:var(--pd-blue);"></i>Roles List
            </h6>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i>Create Role
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" id="rolesTable">
                    <thead>
                        <tr>
                            <th>Role Name</th>
                            <th>Display Name</th>
                            <th>Description</th>
                            <th class="text-center">Total Users</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>
                                    <code style="font-size:.82rem; background:#f0f4f8; padding:.2rem .5rem; border-radius:.3rem; color:var(--pd-navy);">
                                        {{ $role->name }}
                                    </code>
                                </td>
                                <td>
                                    <div class="font-weight-bold" style="font-size:.875rem; color:var(--pd-gray-800);">
                                        {{ $role->display_name }}
                                    </div>
                                </td>
                                <td style="font-size:.83rem; color:var(--pd-gray-500);">{{ $role->description }}</td>
                                <td class="text-center">
                                    <span class="badge badge-primary">
                                        {{ $roleCounts[$role->id]['student_count'] + $roleCounts[$role->id]['user_count'] + $roleCounts[$role->id]['instructor_count'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center" style="gap:.3rem;">
                                        <a href="{{ route('admin.roles.edit-permissions', $role->id) }}"
                                           class="btn btn-icon btn-primary" title="Manage Permissions" data-toggle="tooltip">
                                            <i class="fas fa-key fa-xs"></i>
                                        </a>
                                        <a href="{{ route('admin.roles.edit', $role->id) }}"
                                           class="btn btn-icon btn-warning" title="Edit Role" data-toggle="tooltip">
                                            <i class="fas fa-edit fa-xs"></i>
                                        </a>
                                        @if(!$role->is_system_role)
                                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline delete-form"
                                                  onsubmit="return confirm('Delete this role?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-icon btn-danger" title="Delete" data-toggle="tooltip">
                                                    <i class="fas fa-trash fa-xs"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-icon btn-secondary" disabled title="System role — cannot be deleted" data-toggle="tooltip">
                                                <i class="fas fa-lock fa-xs"></i>
                                            </button>
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
    $('#rolesTable').DataTable({
        responsive: true,
        pageLength: 10,
        columnDefs: [{ targets: -1, orderable: false }]
    });

    $('[data-toggle="tooltip"]').tooltip({ trigger: 'hover', container: 'body' });

    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function() { $(this).remove(); });
    }, 5000);
});
</script>
@endsection
