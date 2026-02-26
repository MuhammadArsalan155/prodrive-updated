@extends('layouts.master')

@section('title', 'Student Reports')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Student Reports</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Student Reports</li>
                </ol>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-users mr-2"></i>Student List
            </h3>
            <div class="card-tools">
                <form id="batch-report-form" action="{{ route('admin.reports.students.batch-pdf') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm" id="generate-batch-report" disabled>
                        <i class="fas fa-file-pdf mr-1"></i>Generate Batch Report
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="students-table" class="table table-hover table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">
                                <div class="icheck-primary">
                                    <input type="checkbox" id="select-all">
                                    <label for="select-all"></label>
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Instructor</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr>
                            <td class="text-center">
                                <div class="icheck-primary">
                                    <input type="checkbox" class="student-checkbox" id="student-{{ $student->id }}" 
                                        name="student_ids[]" value="{{ $student->id }}" form="batch-report-form">
                                    <label for="student-{{ $student->id }}"></label>
                                </div>
                            </td>
                            <td>{{ $student->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary text-white">
                                            {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->course ? $student->course->course_name : 'N/A' }}</td>
                            <td>{{ $student->instructor ? $student->instructor->instructor_name : 'N/A' }}</td>
                            <td>
                                @switch($student->course_status)
                                    @case('2')
                                        <span class="badge badge-success">Completed</span>
                                        @break
                                    @case('1')
                                        <span class="badge badge-primary">In Progress</span>
                                        @break
                                    @case('0')
                                        <span class="badge badge-info">Pending</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">{{ $student->course_status }}</span>
                                @endswitch
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.reports.students.show', $student->id) }}" 
                                       class="btn btn-info" title="View Report">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.reports.students.pdf', $student->id) }}" 
                                       class="btn btn-primary" title="Download PDF" target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
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

@section('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">

<style>
    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,0.075);
        transition: background-color 0.3s ease;
    }

    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1.5;
    }

    /* Custom DataTables Pagination Styles */
    .dataTables_paginate .paginate_button {
        padding: 0.5rem 0.75rem;
        margin: 0 2px;
        border-radius: 3px;
    }

    .dataTables_paginate .paginate_button.current {
        background-color: #007bff;
        color: white !important;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 10px;
    }
</style>
@endsection

@section('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>


@section('scripts')
<script>
    $(function () {
        const studentsTable = $('#students-table').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "emptyTable": "No students found",
                "zeroRecords": "No matching students found",
                "search": "Quick Search:"
            },
            "columnDefs": [
                { "orderable": false, "targets": [0, 7] }
            ]
        });

        // Select All Checkbox
        $('#select-all').click(function() {
            const isChecked = this.checked;
            $('.student-checkbox').prop('checked', isChecked);
            updateBatchReportButton();
        });

        // Individual checkboxes
        $('.student-checkbox').click(function() {
            updateBatchReportButton();
            
            // If any checkbox is unchecked, uncheck "Select All" as well
            if (!this.checked) {
                $('#select-all').prop('checked', false);
            }
            // If all checkboxes are checked, check "Select All" too
            else if ($('.student-checkbox:checked').length === $('.student-checkbox').length) {
                $('#select-all').prop('checked', true);
            }
        });

        // Update batch report button state
        function updateBatchReportButton() {
            $('#generate-batch-report').prop('disabled', $('.student-checkbox:checked').length === 0);
        }
    });
</script>
@stop