@extends('layouts.master')

@section('title', 'Permission Assignment Logs')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Permission Assignment Logs</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
        <li class="breadcrumb-item active">Permission Logs</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-history me-1"></i>
            Authorization Logs
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="logsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Admin</th>
                            <th>User</th>
                            <th>Role/Permission</th>
                            <th>Action</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->admin_name }}</td>
                                <td>{{ $log->user_name }}</td>
                                <td>
                                    @if($log->role_name)
                                        <span class="badge bg-primary">Role: {{ $log->role_name }}</span>
                                    @elseif($log->permission_name)
                                        <span class="badge bg-info">Permission: {{ $log->permission_name }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->action == 'assign')
                                        <span class="badge bg-success">Assigned</span>
                                    @else
                                        <span class="badge bg-danger">Revoked</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
    
    <div class="mb-4">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to User List
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#logsTable').DataTable({
            paging: false,
            ordering: true,
            info: false,
        });
    });
</script>
@endsection