@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="pd-page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="font-weight:800;"><i class="fas fa-user-tie mr-2"></i>Managers</h4>
            <p style="font-size:.85rem;">Manage system managers and their access permissions</p>
        </div>
        <a href="{{ route('admin.managers.create') }}" class="btn btn-light btn-sm font-weight-bold">
            <i class="fas fa-plus mr-1"></i>Add Manager
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
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold" style="color:var(--pd-navy);">
                <i class="fas fa-list mr-2" style="color:var(--pd-blue);"></i>Manager List
            </h6>
            <a href="{{ route('admin.managers.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i>Add Manager
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Manager</th>
                            <th>Email</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($managers as $manager)
                            <tr>
                                <td style="font-size:.8rem; color:var(--pd-gray-500);">{{ $manager->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center" style="gap:.6rem;">
                                        <div class="icon-circle" style="background:linear-gradient(135deg,var(--pd-navy),var(--pd-blue));color:#fff;min-width:34px;width:34px;height:34px;font-size:.8rem;">
                                            {{ strtoupper(substr($manager->name, 0, 1)) }}
                                        </div>
                                        <div class="font-weight-bold" style="font-size:.875rem; color:var(--pd-gray-800);">
                                            {{ $manager->name }}
                                        </div>
                                    </div>
                                </td>
                                <td style="font-size:.83rem; color:var(--pd-gray-700);">{{ $manager->email }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $manager->is_active ? 'success' : 'danger' }}">
                                        {{ $manager->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center" style="gap:.35rem;">
                                        <a href="{{ route('admin.managers.edit', $manager->id) }}" class="btn btn-icon btn-primary" title="Edit" data-toggle="tooltip">
                                            <i class="fas fa-edit fa-xs"></i>
                                        </a>
                                        <button type="button" class="btn btn-icon btn-danger" title="Delete" data-toggle="tooltip"
                                                data-toggle-modal="modal" onclick="$('#deleteModal{{ $manager->id }}').modal('show')">
                                            <i class="fas fa-trash-alt fa-xs"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $manager->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-sm" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"><i class="fas fa-trash mr-2"></i>Delete Manager</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                                                </div>
                                                <div class="modal-body" style="font-size:.875rem;">
                                                    Are you sure you want to delete <strong>{{ $manager->name }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('admin.managers.destroy', $manager->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash mr-1"></i>Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-user-tie fa-2x mb-2 d-block" style="opacity:.3;"></i>
                                    No managers found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($managers->hasPages())
            <div class="d-flex justify-content-center py-3">
                {{ $managers->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        paging: false,
        searching: true,
        ordering: true,
        info: false,
    });

    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function() { $(this).remove(); });
    }, 5000);

    $('[data-toggle="tooltip"]').tooltip({ trigger: 'hover', container: 'body' });
});
</script>
@endsection
