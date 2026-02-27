@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="pd-page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="font-weight:800;"><i class="fas fa-file-alt mr-2"></i>Student Reports</h4>
            <p style="font-size:.85rem;">View and download individual or batch student progress reports</p>
        </div>
        <form id="batch-report-form" action="{{ route('admin.reports.students.batch-pdf') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-light btn-sm font-weight-bold" id="generate-batch-report" disabled>
                <i class="fas fa-file-pdf mr-1"></i>Batch PDF Report
            </button>
        </form>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold" style="color:var(--pd-navy);">
                <i class="fas fa-users mr-2" style="color:var(--pd-blue);"></i>Student List
            </h6>
            <div class="d-flex align-items-center" style="gap:.5rem; font-size:.8rem; color:var(--pd-gray-500);">
                <input type="checkbox" id="select-all" style="width:14px;height:14px;cursor:pointer;">
                <label for="select-all" style="margin:0;cursor:pointer;font-weight:600;">Select All</label>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="students-table" class="table mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:50px;"></th>
                            <th>#</th>
                            <th>Student</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Instructor</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="student-checkbox"
                                       id="student-{{ $student->id }}"
                                       name="student_ids[]"
                                       value="{{ $student->id }}"
                                       form="batch-report-form"
                                       style="width:14px;height:14px;cursor:pointer;">
                            </td>
                            <td style="font-size:.8rem; color:var(--pd-gray-500);">{{ $student->id }}</td>
                            <td>
                                <div class="d-flex align-items-center" style="gap:.5rem;">
                                    <div class="icon-circle" style="background:linear-gradient(135deg,var(--pd-navy),var(--pd-blue));color:#fff;width:32px;height:32px;min-width:32px;font-size:.75rem;">
                                        {{ strtoupper(substr($student->first_name,0,1).substr($student->last_name,0,1)) }}
                                    </div>
                                    <div class="font-weight-bold" style="font-size:.875rem; color:var(--pd-gray-800);">
                                        {{ $student->first_name }} {{ $student->last_name }}
                                    </div>
                                </div>
                            </td>
                            <td style="font-size:.83rem; color:var(--pd-gray-700);">{{ $student->email }}</td>
                            <td style="font-size:.83rem;">{{ $student->course ? $student->course->course_name : '—' }}</td>
                            <td style="font-size:.83rem; color:var(--pd-gray-500);">{{ $student->instructor ? $student->instructor->instructor_name : '—' }}</td>
                            <td class="text-center">
                                @switch($student->course_status)
                                    @case('2')
                                        <span class="badge badge-success">Completed</span>
                                        @break
                                    @case('1')
                                        <span class="badge badge-primary">In Progress</span>
                                        @break
                                    @case('0')
                                        <span class="badge badge-warning">Pending</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">{{ $student->course_status }}</span>
                                @endswitch
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center" style="gap:.3rem;">
                                    <a href="{{ route('admin.reports.students.show', $student->id) }}"
                                       class="btn btn-icon btn-info" title="View Report" data-toggle="tooltip">
                                        <i class="fas fa-eye fa-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.reports.students.pdf', $student->id) }}"
                                       class="btn btn-icon btn-primary" title="Download PDF" data-toggle="tooltip" target="_blank">
                                        <i class="fas fa-file-pdf fa-xs"></i>
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

@section('scripts')
<script>
$(function () {
    const studentsTable = $('#students-table').DataTable({
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        responsive: true,
        language: {
            emptyTable: "No students found",
            zeroRecords: "No matching students found",
            search: "Search:"
        },
        columnDefs: [{ orderable: false, targets: [0, 7] }]
    });

    $('[data-toggle="tooltip"]').tooltip({ trigger: 'hover', container: 'body' });

    // Select All
    $('#select-all').click(function() {
        $('.student-checkbox').prop('checked', this.checked);
        updateBatchBtn();
    });

    // Individual checkboxes
    $(document).on('change', '.student-checkbox', function() {
        updateBatchBtn();
        if (!this.checked) {
            $('#select-all').prop('checked', false);
        } else if ($('.student-checkbox:checked').length === $('.student-checkbox').length) {
            $('#select-all').prop('checked', true);
        }
    });

    function updateBatchBtn() {
        $('#generate-batch-report').prop('disabled', $('.student-checkbox:checked').length === 0);
    }
});
</script>
@endsection
