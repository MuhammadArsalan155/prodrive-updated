@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users mr-2 text-primary"></i>Managers
        </h1>
        <a href="{{ route('admin.managers.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Add New Manager
        </a>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center">
                    <i class="fas fa-list mr-2 text-primary"></i>
                    <h6 class="m-0 font-weight-bold text-primary">Manager List</h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag mr-1 text-primary"></i>ID</th>
                                    <th><i class="fas fa-user mr-1 text-primary"></i>Name</th>
                                    <th><i class="fas fa-envelope mr-1 text-primary"></i>Email</th>
                                    <th><i class="fas fa-toggle-on mr-1 text-primary"></i>Status</th>
                                    <th><i class="fas fa-tools mr-1 text-primary"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($managers as $manager)
                                    <tr>
                                        <td>{{ $manager->id }}</td>
                                        <td>{{ $manager->name }}</td>
                                        <td>{{ $manager->email }}</td>
                                        <td>
                                            @if($manager->is_active)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle mr-1"></i>Active
                                                </span>
                                            @else
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-times-circle mr-1"></i>Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.managers.edit', $manager->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit mr-1"></i> Edit
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal{{ $manager->id }}">
                                                    <i class="fas fa-trash mr-1"></i> Delete
                                                </button>
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal{{ $manager->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel">
                                                                <i class="fas fa-exclamation-triangle mr-2 text-danger"></i>Confirm Delete
                                                            </h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to delete manager <strong>{{ $manager->name }}</strong>?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                                <i class="fas fa-times mr-1"></i>Cancel
                                                            </button>
                                                            <form action="{{ route('admin.managers.destroy', $manager->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">
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
                                        <td colspan="6" class="text-center">
                                            <i class="fas fa-info-circle mr-2 text-muted"></i>No managers found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $managers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "paging": false,
            "searching": true,
            "ordering": true,
            "info": false,
        });
    });
</script>
@endsection