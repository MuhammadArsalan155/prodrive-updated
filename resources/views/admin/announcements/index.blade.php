@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Announcements</h1>
            <a href="{{ route('admin.announcements.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Create Announcement
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Announcements Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">All Announcements</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="announcementsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Created By</th>
                                <th>Target</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Expires</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($announcements as $announcement)
                                <tr>
                                    <td>{{ $announcement->title }}</td>
                                    <td>{{ $announcement->creator->name }}</td>
                                    <td>
                                        @if($announcement->target_type === 'all')
                                            <span class="badge badge-info">All Users</span>
                                        @elseif($announcement->target_type === 'role')
                                            <span class="badge badge-primary">Specific Roles</span>
                                        @elseif($announcement->target_type === 'user')
                                            <span class="badge badge-warning">Specific Users</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($announcement->is_active)
                                            @if($announcement->expires_at && $announcement->expires_at < now())
                                                <span class="badge badge-secondary">Expired</span>
                                            @else
                                                <span class="badge badge-success">Active</span>
                                            @endif
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $announcement->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if($announcement->expires_at)
                                            {{ $announcement->expires_at->format('Y-m-d H:i') }}
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.announcements.show', $announcement) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this announcement?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-4">
                    {{ $announcements->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#announcementsTable').DataTable({
            "paging": false, // Disable DataTables pagination as we're using Laravel's
            "ordering": true,
            "info": false,
            "searching": true
        });
    });
</script>
@endsection