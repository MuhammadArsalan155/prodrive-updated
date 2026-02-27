@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="pd-page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="font-weight:800;"><i class="fas fa-book-open mr-2"></i>Lesson Plans</h4>
            <p style="font-size:.85rem;">Define feedback questionnaires and lesson topics per course class</p>
        </div>
        <a href="{{ route('admin.lesson-plans.create') }}" class="btn btn-light btn-sm font-weight-bold">
            <i class="fas fa-plus mr-1"></i>Create Lesson Plan
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold" style="color:var(--pd-navy);">
                <i class="fas fa-list mr-2" style="color:var(--pd-blue);"></i>Lesson Plans
            </h6>
            <a href="{{ route('admin.lesson-plans.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i>Create
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" id="lessonPlansTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width:5%;">#</th>
                            <th>Title</th>
                            <th class="text-center">Questions</th>
                            <th class="text-center">Courses</th>
                            <th class="text-center">Status</th>
                            <th>Created</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lessonPlans as $lessonPlan)
                            <tr>
                                <td style="font-size:.8rem; color:var(--pd-gray-500);">{{ $lessonPlan->id }}</td>
                                <td>
                                    <div class="font-weight-bold" style="font-size:.875rem; color:var(--pd-gray-800);">
                                        {{ $lessonPlan->title }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info">{{ $lessonPlan->feedbackQuestions->count() }}</span>
                                </td>
                                <td class="text-center">
                                    @php $courseCount = $lessonPlan->courses->count(); @endphp
                                    @if ($courseCount > 0)
                                        <span class="badge badge-success">{{ $courseCount }} course{{ $courseCount > 1 ? 's' : '' }}</span>
                                    @else
                                        <span class="badge badge-secondary">Unused</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $lessonPlan->is_active ? 'success' : 'danger' }}">
                                        {{ $lessonPlan->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td style="font-size:.82rem; color:var(--pd-gray-500);">
                                    {{ $lessonPlan->created_at->format('M d, Y') }}
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center" style="gap:.3rem;">
                                        <a href="{{ route('admin.lesson-plans.show', $lessonPlan) }}"
                                           class="btn btn-icon btn-info" title="View" data-toggle="tooltip">
                                            <i class="fas fa-eye fa-xs"></i>
                                        </a>
                                        <a href="{{ route('admin.lesson-plans.edit', $lessonPlan) }}"
                                           class="btn btn-icon btn-primary" title="Edit" data-toggle="tooltip">
                                            <i class="fas fa-edit fa-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.lesson-plans.toggle-status', $lessonPlan) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-icon btn-warning"
                                                    title="{{ $lessonPlan->is_active ? 'Deactivate' : 'Activate' }}" data-toggle="tooltip">
                                                <i class="fas fa-toggle-{{ $lessonPlan->is_active ? 'on' : 'off' }} fa-xs"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.lesson-plans.destroy', $lessonPlan) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-danger" title="Delete" data-toggle="tooltip">
                                                <i class="fas fa-trash-alt fa-xs"></i>
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
        columnDefs: [{ orderable: false, targets: [6] }],
        order: [[0, "desc"]],
        language: {
            emptyTable: "No lesson plans available",
            zeroRecords: "No matching lesson plans found"
        }
    });

    $('[data-toggle="tooltip"]').tooltip({ trigger: 'hover', container: 'body' });

    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function() { $(this).remove(); });
    }, 5000);

    $('.delete-form').on('submit', function(e) {
        if (!confirm('Are you sure you want to delete this lesson plan? This cannot be undone.')) {
            e.preventDefault();
        }
    });
});
</script>
@endsection
