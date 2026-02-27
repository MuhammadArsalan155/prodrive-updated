@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="pd-page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="font-weight:800;"><i class="fas fa-book mr-2"></i>Courses</h4>
            <p style="font-size:.85rem;">Manage all available driving courses and their structure</p>
        </div>
        <a href="{{ route('addcourse') }}" class="btn btn-light btn-sm font-weight-bold">
            <i class="fas fa-plus mr-1"></i>Add New Course
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

    <!-- Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold" style="color:var(--pd-navy);">
                <i class="fas fa-list mr-2" style="color:var(--pd-blue);"></i>Courses List
            </h6>
            <a href="{{ route('addcourse') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i>Add Course
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Course Name</th>
                            <th>Type</th>
                            <th>Hours</th>
                            <th>Classes</th>
                            <th>Price</th>
                            <th>Installment</th>
                            <th>Status</th>
                            <th>Desc</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courses as $course)
                            <tr>
                                <td style="font-size:.8rem; color:var(--pd-gray-500);">{{ $course->id }}</td>
                                <td>
                                    <div class="font-weight-bold" style="font-size:.875rem; color:var(--pd-gray-800);">
                                        {{ $course->course_name }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $course->course_type == 'theory' ? 'info' : ($course->course_type == 'practical' ? 'warning' : 'primary') }}">
                                        {{ ucfirst($course->course_type) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($course->theory_hours > 0)
                                        <span class="badge badge-info">T: {{ $course->theory_hours }}h</span>
                                    @endif
                                    @if ($course->practical_hours > 0)
                                        <span class="badge badge-warning ml-1">P: {{ $course->practical_hours }}h</span>
                                    @endif
                                </td>
                                <td>
                                    @if (!empty($course->total_theory_classes) && $course->total_theory_classes > 0)
                                        <span class="badge badge-info">T: {{ $course->total_theory_classes }}</span>
                                    @endif
                                    @if (!empty($course->total_practical_classes) && $course->total_practical_classes > 0)
                                        <span class="badge badge-warning ml-1">P: {{ $course->total_practical_classes }}</span>
                                    @endif
                                    @if ((empty($course->total_theory_classes) || $course->total_theory_classes == 0) &&
                                         (empty($course->total_practical_classes) || $course->total_practical_classes == 0))
                                        <span class="text-muted" style="font-size:.8rem;">—</span>
                                    @endif
                                </td>
                                <td class="font-weight-bold" style="font-size:.875rem;">${{ number_format($course->course_price, 2) }}</td>
                                <td>
                                    @if ($course->has_installment_plan && $course->installmentPlan)
                                        <span class="badge badge-success" data-toggle="tooltip" data-placement="top"
                                              data-original-title="{{ $course->installmentPlan->Name }} — {{ $course->installmentPlan->number_of_installments }} installments">
                                            Available
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">None</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $course->is_active ? 'success' : 'danger' }}">
                                        {{ $course->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($course->description)
                                        <button type="button" class="btn btn-icon btn-info" data-toggle="modal"
                                                data-target="#descModal{{ $course->id }}" title="View Description">
                                            <i class="fas fa-info-circle fa-xs"></i>
                                        </button>

                                        <!-- Description Modal -->
                                        <div class="modal fade" id="descModal{{ $course->id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"><i class="fas fa-book mr-2"></i>{{ $course->course_name }}</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                                                    </div>
                                                    <div class="modal-body" style="font-size:.9rem; color:#374151;">
                                                        {{ $course->description }}
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size:.8rem;">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center" style="gap:.3rem;">
                                        <form action="{{ route('toggle_course_status', $course->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-icon btn-warning"
                                                    title="{{ $course->is_active ? 'Deactivate' : 'Activate' }}" data-toggle="tooltip">
                                                <i class="fas fa-toggle-{{ $course->is_active ? 'on' : 'off' }} fa-xs"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('edit_course', $course) }}" class="btn btn-icon btn-primary" title="Edit" data-toggle="tooltip">
                                            <i class="fas fa-edit fa-xs"></i>
                                        </a>
                                        <form action="{{ route('delete_course', $course->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this course?');">
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
    $('#dataTable').DataTable({
        columnDefs: [
            { orderable: false, targets: [9] },
            { searchable: true, targets: [1, 2] }
        ],
        language: {
            emptyTable: "No courses available",
            zeroRecords: "No matching courses found"
        },
        order: [[0, "desc"]]
    });

    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function() { $(this).remove(); });
    }, 5000);

    $('[data-toggle="tooltip"]').tooltip({ trigger: 'hover', container: 'body' });
});
</script>
@endsection
