@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="pd-page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="font-weight:800;"><i class="fas fa-users-cog mr-2"></i>User Management</h4>
            <p style="font-size:.85rem;">Manage system users, assign roles and control access permissions</p>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-light btn-sm font-weight-bold">
            <i class="fas fa-user-tag mr-1"></i>Manage Roles
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold" style="color:var(--pd-navy);">
                <i class="fas fa-list mr-2" style="color:var(--pd-blue);"></i>All Users
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" id="usersTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Email</th>
                            <th class="text-center">Type</th>
                            <th>Current Roles</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td style="font-size:.8rem; color:var(--pd-gray-500);">{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center" style="gap:.5rem;">
                                        <div class="icon-circle" style="background:linear-gradient(135deg,var(--pd-navy),var(--pd-blue));color:#fff;width:32px;height:32px;min-width:32px;font-size:.75rem;">
                                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="font-weight-bold" style="font-size:.875rem; color:var(--pd-gray-800);">
                                            {{ $user->name }}
                                        </div>
                                    </div>
                                </td>
                                <td style="font-size:.83rem; color:var(--pd-gray-700);">{{ $user->email ?? '—' }}</td>
                                <td class="text-center">
                                    <span class="badge badge-info">{{ $user->user_type }}</span>
                                </td>
                                <td>
                                    @foreach ($user->roles as $role)
                                        <span class="badge badge-primary mr-1">{{ $role->display_name ?? $role->name }}</span>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.users.edit', ['type' => strtolower($user->user_type), 'id' => $user->id]) }}"
                                       class="btn btn-icon btn-primary edit-roles" title="Edit Roles" data-toggle="tooltip">
                                        <i class="fas fa-edit fa-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user mr-2"></i>User Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" id="userDetailsContent"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        paging: true,
        ordering: true,
        info: true,
        searching: true,
        responsive: true,
        columnDefs: [{ orderable: false, targets: [5] }]
    });

    $('[data-toggle="tooltip"]').tooltip({ trigger: 'hover', container: 'body' });

    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function() { $(this).remove(); });
    }, 5000);

    $('.view-details').on('click', function() {
        var userId   = $(this).data('user-id');
        var userType = $(this).data('user-type');
        $.ajax({
            url: `/admin/users/${userType}/${userId}`,
            method: 'GET',
            success: function(response) { $('#userDetailsContent').html(response); },
        });
    });
});
</script>
@endsection
