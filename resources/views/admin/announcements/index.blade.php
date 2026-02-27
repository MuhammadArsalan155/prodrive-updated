@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="pd-page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="font-weight:800;"><i class="fas fa-bullhorn mr-2"></i>Announcements</h4>
            <p style="font-size:.85rem;">Manage announcements displayed to students, instructors, and staff</p>
        </div>
        <a href="{{ route('admin.announcements.create') }}" class="btn btn-light btn-sm font-weight-bold">
            <i class="fas fa-plus mr-1"></i>Create Announcement
        </a>
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
                <i class="fas fa-list mr-2" style="color:var(--pd-blue);"></i>All Announcements
            </h6>
            <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i>Create
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" id="announcementsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Created By</th>
                            <th>Target</th>
                            <th class="text-center">Status</th>
                            <th>Created</th>
                            <th>Expires</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($announcements as $announcement)
                            <tr>
                                <td>
                                    <div class="font-weight-bold" style="font-size:.875rem; color:var(--pd-gray-800);">
                                        {{ $announcement->title }}
                                    </div>
                                </td>
                                <td style="font-size:.83rem; color:var(--pd-gray-700);">
                                    {{ $announcement->creator->name }}
                                </td>
                                <td>
                                    @if($announcement->target_type === 'all')
                                        <span class="badge badge-info">All Users</span>
                                    @elseif($announcement->target_type === 'role')
                                        <span class="badge badge-primary">Specific Roles</span>
                                    @elseif($announcement->target_type === 'user')
                                        <span class="badge badge-warning">Specific Users</span>
                                    @endif
                                </td>
                                <td class="text-center">
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
                                <td style="font-size:.82rem; color:var(--pd-gray-500);">
                                    {{ $announcement->created_at->format('M d, Y H:i') }}
                                </td>
                                <td style="font-size:.82rem;">
                                    @if($announcement->expires_at)
                                        <span style="color:var(--pd-warning);">{{ $announcement->expires_at->format('M d, Y H:i') }}</span>
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center" style="gap:.3rem;">
                                        <a href="{{ route('admin.announcements.show', $announcement) }}"
                                           class="btn btn-icon btn-info" title="View" data-toggle="tooltip">
                                            <i class="fas fa-eye fa-xs"></i>
                                        </a>
                                        <a href="{{ route('admin.announcements.edit', $announcement) }}"
                                           class="btn btn-icon btn-primary" title="Edit" data-toggle="tooltip">
                                            <i class="fas fa-edit fa-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this announcement?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-danger" title="Delete" data-toggle="tooltip">
                                                <i class="fas fa-trash fa-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($announcements->hasPages())
            <div class="d-flex justify-content-center py-3">
                {{ $announcements->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#announcementsTable').DataTable({
        paging: false,
        ordering: true,
        info: false,
        searching: true,
        columnDefs: [{ orderable: false, targets: [6] }]
    });

    $('[data-toggle="tooltip"]').tooltip({ trigger: 'hover', container: 'body' });

    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function() { $(this).remove(); });
    }, 5000);
});
</script>
@endsection
