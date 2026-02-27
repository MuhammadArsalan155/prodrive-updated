@extends('layouts.master')

@section('styles')
<style>
    .student-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        background: linear-gradient(135deg, var(--pd-navy), var(--pd-blue));
        color: #fff; display: inline-flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .8rem; flex-shrink: 0;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="pd-page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="font-weight:800;"><i class="fas fa-user-graduate mr-2"></i>Students</h4>
            <p style="font-size:.85rem;">Manage all registered students and their records</p>
        </div>
        <a href="{{ route('addstudent') }}" class="btn btn-light btn-sm font-weight-bold">
            <i class="fas fa-plus mr-1"></i>Add New Student
        </a>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <!-- Student Table -->
    <div class="card shadow mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold" style="color:var(--pd-navy);">
                <i class="fas fa-list mr-2" style="color:var(--pd-blue);"></i>Student List
            </h6>
            <a href="{{ route('addstudent') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i>Add Student
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" id="dataTable" width="100%">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student</th>
                            <th>Start Date</th>
                            <th>Completion</th>
                            <th>Course</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr>
                                <td>
                                    <span class="font-weight-bold" style="color:var(--pd-blue); font-size:.82rem;">
                                        PD-{{ 1000 + $student->id }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center" style="gap:.6rem;">
                                        <div class="student-avatar">
                                            {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-weight-bold" style="font-size:.875rem; color:var(--pd-gray-800);">
                                                {{ $student->first_name }} {{ $student->last_name }}
                                            </div>
                                            <div style="font-size:.75rem; color:var(--pd-gray-500);">{{ $student->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-size:.83rem; color:var(--pd-gray-700);">
                                    @if ($student->joining_date)
                                        {{ \Carbon\Carbon::parse($student->joining_date)->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($student->completion_date == null)
                                        <span class="badge badge-warning">In Progress</span>
                                    @else
                                        <span style="font-size:.83rem; color:var(--pd-success); font-weight:600;">
                                            {{ \Carbon\Carbon::parse($student->completion_date)->format('M d, Y') }}
                                        </span>
                                    @endif
                                </td>
                                <td style="font-size:.83rem;">
                                    {{ $student->course->course_name ?? '—' }}
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center" style="gap:.35rem;">
                                        <a href="{{ route('view_student', $student->id) }}"
                                           class="btn btn-icon btn-success" title="View Student" data-toggle="tooltip">
                                            <i class="fas fa-eye fa-xs"></i>
                                        </a>
                                        <a href="{{ route('edit_student', $student->id) }}"
                                           class="btn btn-icon btn-primary" title="Edit Student" data-toggle="tooltip">
                                            <i class="fas fa-edit fa-xs"></i>
                                        </a>
                                        <a href="{{ route('delete_student', $student->id) }}"
                                           class="btn btn-icon btn-danger btn-delete"
                                           data-student-id="{{ $student->id }}"
                                           data-student-name="{{ $student->first_name }} {{ $student->last_name }}"
                                           title="Delete Student" data-toggle="tooltip">
                                            <i class="fas fa-trash-alt fa-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($students->hasPages())
            <div class="d-flex justify-content-center py-3">
                {!! $students->links() !!}
            </div>
            @endif
        </div>
    </div>

</div>

<!-- Payment Status Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-money-check-alt mr-2"></i>Change Payment Status</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Mark this student's payment as paid?</p>
                <form action="{{ route('change_payment_status') }}" method="post">
                    @csrf
                    <input id="student_id_payment" type="hidden" name="student_id_payment">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check mr-1"></i>Mark as Paid
                    </button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Course Status Modal -->
<div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-graduation-cap mr-2"></i>Mark Course Complete</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('change_course_status') }}" method="post">
                    @csrf
                    <input id="student_id_course" type="hidden" name="student_id_course">
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-book-open text-primary mr-1"></i>Theory Hours Completed</label>
                        <input type="number" class="form-control" name="hours_theory" placeholder="e.g. 4" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-tools text-primary mr-1"></i>Practical Hours Completed</label>
                        <input type="number" class="form-control" name="hours_practical" placeholder="e.g. 4" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-calendar-check text-primary mr-1"></i>Completion Date</label>
                        <input type="date" class="form-control" name="completion_date" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-check mr-1"></i>Mark as Complete
                    </button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "columnDefs": [
            { "orderable": false, "targets": [5] },
            { "searchable": true, "targets": [1, 2] }
        ],
        "language": {
            "emptyTable": "No students available",
            "zeroRecords": "No matching students found"
        },
        "order": [[0, "desc"]]
    });

    // Auto-close alerts
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function() { $(this).remove(); });
    }, 5000);

    $('[data-toggle="tooltip"]').tooltip();

    // Delete confirmation
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        const studentId   = $(this).data('student-id');
        const studentName = $(this).data('student-name');
        const deleteUrl   = $(this).attr('href');

        Swal.fire({
            title: 'Delete Student?',
            html: `<p>You are about to permanently delete:</p>
                   <strong>${studentName}</strong> (PD-${1000 + studentId})
                   <div class="alert alert-warning mt-3 text-left" style="font-size:.85rem;">
                       <i class="fas fa-exclamation-triangle mr-1"></i>
                       All invoices, installments, and payment records will also be deleted.
                   </div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: deleteUrl, type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function() {
                        Swal.fire('Deleted!', 'Student record has been deleted.', 'success')
                            .then(() => location.reload());
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to delete student record.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection
