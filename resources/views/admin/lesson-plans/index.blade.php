@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-2 text-gray-800">Lesson Plans Management</h1>
        <a href="{{ route('admin.lesson-plans.create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i>Create New Lesson Plan
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-check-circle mr-2"></i>Success!</strong> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-exclamation-triangle mr-2"></i>Error!</strong> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background: #2a5c68;">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-white">Lesson Plans</h6>
                </div>
                <div class="col text-right">
                    <a href="{{ route('admin.lesson-plans.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> Create New
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="lessonPlansTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="25%">Title</th>
                            <th width="15%">Questions</th>
                            <th width="15%">Used In Courses</th>
                            <th width="10%">Status</th>
                            <th width="15%">Created</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lessonPlans as $lessonPlan)
                            <tr>
                                <td>{{ $lessonPlan->id }}</td>
                                <td>{{ $lessonPlan->title }}</td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $lessonPlan->feedbackQuestions->count() }} Questions
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $courseCount = $lessonPlan->courses->count();
                                    @endphp
                                    @if ($courseCount > 0)
                                        <span class="badge badge-success">Used in {{ $courseCount }} course(s)</span>
                                    @else
                                        <span class="badge badge-secondary">Not used</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $lessonPlan->is_active ? 'badge-success' : 'badge-danger' }}">
                                        {{ $lessonPlan->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $lessonPlan->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.lesson-plans.show', $lessonPlan) }}" class="btn btn-info btn-sm mr-1" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.lesson-plans.edit', $lessonPlan) }}" class="btn btn-primary btn-sm mr-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.lesson-plans.toggle-status', $lessonPlan) }}" method="POST" class="d-inline mr-1">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm" title="{{ $lessonPlan->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas fa-toggle-{{ $lessonPlan->is_active ? 'on' : 'off' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.lesson-plans.destroy', $lessonPlan) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
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
        $('#lessonPlansTable').DataTable({
            "columnDefs": [
                {
                    "orderable": false,
                    "targets": [6] 
                }
            ],
            "order": [[0, "desc"]], 
            "language": {
                "emptyTable": "No lesson plans available",
                "zeroRecords": "No matching lesson plans found"
            }
        });

        
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function() {
                $(this).remove();
            });
        }, 5000);

        $('.delete-form').on('submit', function(e) {
            if (!confirm('Are you sure you want to delete this lesson plan? This action cannot be undone.')) {
                e.preventDefault();
                return false;
            }
            return true;
        });
    });
</script>
@endsection