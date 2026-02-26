@extends('layouts.master')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid px-4">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h1 class="h2 text-primary fw-bold">
            <i class="fas fa-user-graduate me-2"></i>Students
        </h1>
        <a href="{{ route('addstudent') }}" class="btn btn-primary btn-sm rounded-pill">
            <i class="fas fa-plus me-1"></i> Add New Student
        </a>
    </div>

    <!-- DataTales Example -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold">
                <i class="fas fa-list me-2"></i>Student List
            </h5>
        </div>
        
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>Student ID</th>
                            <th>Student Info</th>
                            <th>Start Date</th>
                            <th>Completion Date</th>
                            <th>Course Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr>
                                <td>{{ 'PD-'.(1000+$student->id) }}</td>
                                <td>
                                    {{ $student->first_name }} {{ $student->last_name }} <br>
                                </td>
                                <td>
                                    @if ($student->joining_date != null)
                                    {{ \Carbon\Carbon::parse($student->joining_date)->isoFormat('Do MMM, YYYY') }}
                                    @endif
                                </td>
                                <td>
                                    @if ($student->completion_date == null)
                                        <span class="badge bg-warning text-dark">In Progress</span>
                                    @else
                                        {{ \Carbon\Carbon::parse($student->completion_date)->isoFormat('Do MMM, YYYY') }}
                                    @endif
                                </td>
                                <td>{{ $student->course->course_name?? '' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('view_student',$student->id) }}" class="btn btn-success btn-sm" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('edit_student',$student->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('delete_student', $student->id) }}" 
                                           class="btn btn-danger btn-sm btn-delete" 
                                           data-student-id="{{ $student->id }}"
                                           data-student-name="{{ $student->first_name }} {{ $student->last_name }}"
                                           title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {!! $students->links() !!}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Status Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="paymentStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentStatusModalLabel">
                    <i class="fas fa-money-check-alt text-primary me-2"></i>Change Payment Status
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Want to change payment status for this student?
                <form action="{{ route('change_payment_status') }}" method="post">
                    @csrf
                    <input id="student_id_payment" type="hidden" name="student_id_payment">
                    <button type="submit" class="btn btn-success btn-sm mt-3">
                        <i class="fas fa-check me-1"></i>Mark Paid
                    </button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Course Status Modal -->
<div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="courseStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="courseStatusModalLabel">
                    <i class="fas fa-graduation-cap text-primary me-2"></i>Change Course Status
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Want to change course status for this student?
                <form action="{{ route('change_course_status') }}" method="post">
                    @csrf
                    <input id="student_id_course" type="hidden" name="student_id_course">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="hours_theory" class="form-label">
                                <i class="fas fa-book-open text-primary me-2"></i>Theory Hours Completed
                            </label>
                            <input type="number" class="form-control" name="hours_theory" placeholder="e.g 4" required>
                        </div>
                        <div class="col-12">
                            <label for="hours_practical" class="form-label">
                                <i class="fas fa-tools text-primary me-2"></i>Practical Hours Completed
                            </label>
                            <input type="number" class="form-control" name="hours_practical" placeholder="e.g 4" required>
                        </div>
                        <div class="col-12">
                            <label for="completion_date" class="form-label">
                                <i class="fas fa-calendar-check text-primary me-2"></i>Completion Date
                            </label>
                            <input type="date" class="form-control" name="completion_date" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm mt-3">
                        <i class="fas fa-check me-1"></i>Mark Complete
                    </button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
            "columnDefs": [{
                    "orderable": true,
                    "targets": [5]
                }, 
                {
                    "searchable": true,
                    "targets": [1, 2]
                } 
            ],
            "language": {
                "emptyTable": "No students available",
                "zeroRecords": "No matching students found"
            },
            "order": [
                [0, "desc"]
            ] // Default sort by ID descending
        });
        // Auto close alerts
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function() {
                $(this).remove();
            });
        }, 5000);

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            const studentId = $(this).data('student-id');
            const studentName = $(this).data('student-name');
            const deleteUrl = $(this).attr('href');

            Swal.fire({
                title: 'Confirm Deletion',
                html: `
                    <div class="text-left">
                        <p>You are about to permanently delete the student record:</p>
                        <strong>Student Name:</strong> ${studentName}<br>
                        <strong>Student ID:</strong> PD-${1000 + studentId}<br><br>
                        <div class="alert alert-warning">
                            <strong>Warning:</strong> The following records will also be deleted:
                            <ul class="text-left mt-2">
                                <li>All associated Invoices</li>
                                <li>All Installment records</li>
                                <li>All Payment records</li>
                            </ul>
                        </div>
                        <p class="text-danger">This action cannot be undone.</p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // AJAX call to delete the student
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Student record has been deleted successfully.',
                                icon: 'success'
                            }).then(() => {
                                // Reload the page or remove the row
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to delete student record.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });

    });
</script>
@endsection