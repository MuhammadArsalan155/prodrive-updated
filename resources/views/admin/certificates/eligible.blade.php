@extends('layouts.master')

@section('title', 'Eligible Students for Certificates')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Eligible Students for Certificates</h1>
        <a href="{{ route('admin.certificates.index') }}" class="d-none d-sm-inline-block btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Certificates
        </a>
    </div>

    <!-- Alert Messages -->
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

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Students with Completed Courses</h6>
            <div class="dropdown no-arrow">
                <button id="bulkGenerateBtn" class="btn btn-primary btn-sm" disabled>
                    <i class="fas fa-certificate fa-sm"></i> Generate Selected Certificates
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($eligibleStudents->count() > 0)
                <form action="{{ route('admin.certificates.bulk') }}" method="POST" id="bulkGenerateForm">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered" id="eligibleStudentsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Course</th>
                                    <th>Completion Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($eligibleStudents as $student)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox">
                                        </td>
                                        <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                        <td>{{ $student->email }}</td>
                                        <td>{{ $student->course ? $student->course->course_name : 'N/A' }}</td>
                                        <td>{{ $student->completion_date ? \Carbon\Carbon::parse($student->completion_date)->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <!-- Simplified form with unique ID to avoid conflicts -->
                                            <form action="{{ route('admin.certificates.generate-single', $student->id) }}" method="POST" id="generate-single-{{ $student->id }}">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Generate certificate for {{ $student->first_name }} {{ $student->last_name }}?')">
                                                    <i class="fas fa-certificate"></i> Generate
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $eligibleStudents->links() }}
                    </div>
                </form>
            @else
                <div class="alert alert-info">
                    No eligible students found. Students must have completed their courses to be eligible for certificates.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable with minimal interference
        const table = $('#eligibleStudentsTable').DataTable({
            ordering: true,
            paging: false, // Let Laravel handle pagination
            searching: true,
            info: false,
            columnDefs: [
                { orderable: false, targets: [0, 5] } // Disable sorting on checkbox and action columns
            ]
        });

        // Select All checkbox functionality
        $('#selectAll').on('change', function() {
            $('.student-checkbox').prop('checked', $(this).prop('checked'));
            updateBulkGenerateButton();
        });

        // Individual checkbox change
        $('.student-checkbox').on('change', function() {
            updateBulkGenerateButton();
            const totalCheckboxes = $('.student-checkbox').length;
            const checkedCheckboxes = $('.student-checkbox:checked').length;
            $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
            $('#selectAll').prop('checked', checkedCheckboxes === totalCheckboxes);
        });

        // Update bulk generate button state
        function updateBulkGenerateButton() {
            const selected = $('.student-checkbox:checked').length;
            $('#bulkGenerateBtn').prop('disabled', selected === 0);
        }

        // Handle bulk generation
        $('#bulkGenerateBtn').on('click', function(e) {
            e.preventDefault(); // Prevent default button behavior
            const selectedCount = $('.student-checkbox:checked').length;
            if (selectedCount === 0) {
                alert('Please select at least one student.');
                return false;
            }
            if (confirm(`Are you sure you want to generate certificates for ${selectedCount} students?`)) {
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');
                $('#bulkGenerateForm').submit();
            }
        });

        // Ensure individual form submissions work
        $('form[id^="generate-single-"]').on('submit', function(e) {
            console.log('Form submitted for student ID: ' + $(this).attr('id')); // Debug
        });
    });
</script>
@endsection
