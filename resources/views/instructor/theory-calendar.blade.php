@extends('layouts.master')

@section('styles')
<style>
    .calendar-container {
        background-color: #fff;
    }
    .calendar-header {
        background-color: #4e73df;
        color: white;
        padding: 10px;
        border-radius: 5px 5px 0 0;
    }
    .calendar-navigation {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
    }
    .calendar-day {
        height: 120px;
        border: 1px solid #e3e6f0;
        padding: 5px;
        position: relative;
        overflow-y: auto;
    }
    .calendar-day.other-month {
        background-color: #f8f9fc;
        color: #b7b9cc;
    }
    .calendar-date {
        font-weight: bold;
        padding: 2px;
        position: absolute;
        top: 5px;
        right: 5px;
    }
    .calendar-events {
        margin-top: 25px;
    }
    .theory-event {
        background-color: #1cc88a;
        color: white;
        border-radius: 3px;
        padding: 2px 5px;
        margin-bottom: 2px;
        font-size: 11px;
        cursor: pointer;
    }
    .weekday-header {
        font-weight: bold;
        text-align: center;
        padding: 10px;
        background-color: #f8f9fc;
        border: 1px solid #e3e6f0;
    }
    .student-list {
        font-size: 10px;
        margin-top: 3px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Theory Calendar</h1>
        <div>
            <a href="{{ route('instructor.dashboard') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Calendar Navigation -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Theory Classes Calendar</h6>
            <div>
                @php
                    $prevMonth = $month - 1;
                    $prevYear = $year;
                    if ($prevMonth < 1) {
                        $prevMonth = 12;
                        $prevYear--;
                    }
                    
                    $nextMonth = $month + 1;
                    $nextYear = $year;
                    if ($nextMonth > 12) {
                        $nextMonth = 1;
                        $nextYear++;
                    }
                @endphp
                <a href="{{ route('instructor.theory.calendar', ['month' => $prevMonth, 'year' => $prevYear]) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-chevron-left"></i> Previous Month
                </a>
                <span class="mx-2">{{ Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</span>
                <a href="{{ route('instructor.theory.calendar', ['month' => $nextMonth, 'year' => $nextYear]) }}" class="btn btn-sm btn-outline-primary">
                    Next Month <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Calendar -->
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <!-- Weekday Headers -->
                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $weekday)
                            <div class="col weekday-header">{{ $weekday }}</div>
                        @endforeach
                    </div>
                    
                    <!-- Calendar Days -->
                    @foreach(array_chunk($calendar, 7) as $week)
                        <div class="row">
                            @foreach($week as $day)
                                <div class="col calendar-day {{ $day['isCurrentMonth'] ? '' : 'other-month' }}">
                                    <div class="calendar-date">{{ $day['day'] }}</div>
                                    
                                    <div class="calendar-events">
                                        @if($day['hasSchedules'])
                                            @foreach($day['schedules'] as $schedule)
                                                <div class="theory-event" data-toggle="modal" data-target="#viewScheduleModal" 
                                                     data-schedule-id="{{ $schedule->id }}"
                                                     data-course="{{ $schedule->course->course_name }}"
                                                     data-time="{{ $schedule->start_time->format('h:i A') }} - {{ $schedule->end_time->format('h:i A') }}"
                                                     data-students="{{ $schedule->students->count() }}">
                                                    {{ $schedule->course->course_name }}
                                                    <div class="small">{{ $schedule->start_time->format('h:i A') }}</div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Theory Students -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Pending Theory Students</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="pendingTheoryTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Joining Date</th>
                            <th>Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(App\Models\Student::where('instructor_id', Auth::guard('instructor')->id())
                            ->where('theory_status', 'pending')
                            ->with('course')
                            ->get() as $student)
                            <tr>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>{{ $student->course->course_name }}</td>
                                <td>{{ $student->joining_date instanceof \Carbon\Carbon ? $student->joining_date->format('M d, Y') : $student->joining_date }}</td>
                                <td>{{ $student->student_contact }}</td>
                                <td>
                                    <a href="{{ route('instructor.student.view', $student->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Schedule Modal -->
<div class="modal fade" id="viewScheduleModal" tabindex="-1" role="dialog" aria-labelledby="viewScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewScheduleModalLabel">Theory Class Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Course:</h6>
                        <p id="scheduleCourseName"></p>
                        
                        <h6 class="font-weight-bold">Time:</h6>
                        <p id="scheduleTime"></p>
                        
                        <h6 class="font-weight-bold">Student Count:</h6>
                        <p id="scheduleStudentCount"></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Students:</h6>
                        <div id="scheduleStudentsList"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="markCompleteBtn">Mark Complete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#pendingTheoryTable').DataTable();
        
        // View Schedule Modal
        $('#viewScheduleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var scheduleId = button.data('schedule-id');
            var course = button.data('course');
            var time = button.data('time');
            var studentCount = button.data('students');
            var modal = $(this);
            
            // Set the basic info
            modal.find('#scheduleCourseName').text(course);
            modal.find('#scheduleTime').text(time);
            modal.find('#scheduleStudentCount').text(studentCount);
            
            // Set up the mark complete button
            modal.find('#markCompleteBtn').attr('data-schedule-id', scheduleId);
            
            // Load students for this schedule
            $.ajax({
                url: '/instructor/get-theory-students/' + scheduleId,
                type: 'GET',
                success: function(response) {
                    var html = '<ul class="list-group">';
                    if (response.students.length > 0) {
                        $.each(response.students, function(index, student) {
                            html += '<li class="list-group-item d-flex justify-content-between align-items-center">';
                            html += student.first_name + ' ' + student.last_name;
                            
                            if (student.theory_status === 'completed') {
                                html += '<span class="badge badge-success">Completed</span>';
                            } else if (student.theory_status === 'in_progress') {
                                html += '<span class="badge badge-info">In Progress</span>';
                            } else {
                                html += '<span class="badge badge-warning">Pending</span>';
                            }
                            
                            html += '</li>';
                        });
                    } else {
                        html += '<li class="list-group-item">No students assigned to this class.</li>';
                    }
                    html += '</ul>';
                    
                    $('#scheduleStudentsList').html(html);
                }
            });
        });
        
        // Mark Complete Button
        $(document).on('click', '#markCompleteBtn', function() {
            var scheduleId = $(this).data('schedule-id');
            $('#viewScheduleModal').modal('hide');
            
            // Open the complete theory modal
            $('#completeTheoryModal').modal('show');
            $('#theoryScheduleId').val(scheduleId);
            
            // Load students for this schedule
            $.ajax({
                url: '/instructor/get-theory-students/' + scheduleId,
                type: 'GET',
                success: function(response) {
                    var html = '';
                    if (response.students.length > 0) {
                        $.each(response.students, function(index, student) {
                            html += '<div class="form-check">';
                            html += '<input class="form-check-input" type="checkbox" name="student_ids[]" id="student_' + student.id + '" value="' + student.id + '">';
                            html += '<label class="form-check-label" for="student_' + student.id + '">' + student.first_name + ' ' + student.last_name + '</label>';
                            html += '</div>';
                        });
                    } else {
                        html = '<p>No students found for this class.</p>';
                    }
                    
                    $('#theoryStudentsList').html(html);
                }
            });
        });
    });
</script>
@endsection