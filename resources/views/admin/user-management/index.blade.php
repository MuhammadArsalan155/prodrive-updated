@extends('layouts.master')

@section('title', 'User Management')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">User Management</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">User Management</li>
        </ol>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-users me-1"></i>
                All Users
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="usersTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Current Roles</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $user->user_type }}</span>
                                    </td>
                                    <td>
                                        @foreach ($user->roles as $role)
                                            <span
                                                class="badge bg-primary me-1">{{ $role->display_name ?? $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.users.edit', ['type' => strtolower($user->user_type), 'id' => $user->id]) }}"
                                                class="btn btn-sm btn-primary edit-roles">
                                                <i class="fas fa-edit"></i> Edit Roles
                                            </a>
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

    <!-- User Details Modal -->
    <div class="modal fade" id="userDetailsModal" tabindex="-1" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userDetailsModalLabel">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="userDetailsContent">
                    <!-- Dynamic user details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#usersTable').DataTable({
                paging: true,
                ordering: true,
                info: true,
                searching: true,
                responsive: true
            });

            // View User Details Modal
            $('.view-details').on('click', function() {
                var userId = $(this).data('user-id');
                var userType = $(this).data('user-type');

                $.ajax({
                    url: `/admin/users/${userType}/${userId}`,
                    method: 'GET',
                    success: function(response) {
                        $('#userDetailsContent').html(response);
                    },
                    error: function() {
                        toastr.error('Failed to load user details');
                    }
                });
            });
        });
    </script>
@endsection
