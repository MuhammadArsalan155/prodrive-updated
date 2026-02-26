@extends('layouts.master')

@push('styles')
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
    .practical-event {
        background-color: #4e73df;
        color: white;
        border-radius: 3px;
        padding: 2px 5px;
        margin-bottom: 2px;
        font-size: 11px;
        cursor: pointer;
    }
    .practical-event.assigned {
        background-color: #36b9cc;
    }
    .practical-event.available {
        background-color: #1cc88a;
    }
    .weekday-header {
        font-weight: bold;
        text-align: center;
        padding: 10px;
        background-color: #f8f9fc;
        border: 1px solid #e3e6f0;
    }
    .student-name {
        font-size: 10px;
        margin-top: 3px;
        font-weight: bold;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Practical Calendar</h1>
        <div>
            <a href="{{ route('instructor.dashboard') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Calendar Navigation -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Practical Classes Calendar</h6>
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
                <a href="{{ route('instructor.practical.calendar', ['month' => $prevMonth, 'year' => $prevYear]) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-chevron-left"></i> Previous Month
                </a>
                <span class="mx-2">{{ Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</span>
                <a href="{{ route('instructor.practical.calendar', ['month' => $nextMonth, 'year' => $nextYear]) }}" class="btn btn-sm btn-outline-primary">
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
                                                <div class="practical-event {{ $schedule->students->count() > 0 ? 'assigned' : 'available' }}" 
                                                     data-toggle="modal" 
                                                     data-target="{{ $schedule->students->count() > 0 ? '#viewPracticalModal' : '#assignPracticalModal' }}" 
                                                     data-schedule-id="{{ $schedule->id }}"
                                                     data-course="{{ $schedule->course->course_name }}"
                                                     data-time="{{ $schedule->start_time->format('h:i A') }} - {{ $schedule->end_time->format('h:i A') }}"
                                                     @if($schedule->students->count() > 0)
                                                     data-student-id="{{ $schedule->students->first()->id }}"
                                                     data-student-name="{{ $schedule->students->first()->first_name }} {{ $schedule->students->first()->last_name }}"
                                                     @endif
                                                     >
                                                    {{ $schedule->course->course_name }}
                                                    <div class="small">{{ $schedule->start_time->format('h:i A') }}</div>
                                                    @if($schedule->students->count() > 0)
                                                        <div class="student-name">{{ $schedule->students->first()->first_name }} {{ $schedule->students->first()->last_name }}</div>
                                                    @else
                                                        <div class="small font-weight-bold text-white">Available</div>
                                                    @endif
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

    <!-- Pending Practical Students -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Pending Practical Students</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="pendingPracticalTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Theory Completed</th>
                            <th>Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingStudents as $student)
                            <tr>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>{{ $student->course->course_name }}</td>
                                <td>{{ $student->theory_completion_date ? $student->theory_completion_date->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $student->student_contact }}</td>
                                <td>
                                    <a href="{{ route('instructor.student.view', $student->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <button class="btn btn-sm btn-primary assignStudentBtn" data-student-id="{{ $student->id }}" data-student-name="{{ $student->first_name }} {{ $student->last_name }}">
                                        Assign to Practical
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Practical Modal -->
<div class="modal fade" id="viewPracticalModal" tabindex="-1" role="dialog" aria-labelledby="viewPracticalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewPracticalModalLabel">Practical Class Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6 class="font-weight-bold">Course:</h6>
                <p id="practicalCourseName"></p>
                
                <h6 class="font-weight-bold">Time:</h6>
                <p id="practicalTime"></p>
                
                <h6 class="font-weight-bold">Student:</h6>
                <p id="practicalStudentName"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitFeedbackBtn" data-toggle="modal" data-target="#practicalFeedbackModal">
                    Submit Feedback
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Practical Modal -->
<div class="modal fade" id="assignPracticalModal" tabindex="-1" role="dialog" aria-labelledby="assignPracticalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignPracticalModalLabel">Assign Student to Practical</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('instructor.assign.practical') }}" method="POST">
                @csrf
                <input type="hidden" name="schedule_id" id="practicalScheduleId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="studentSelect">Select Student:</label>
                        <select class="form-control" id="studentSelect" name="student_id" required>
                            <option value="">Select a student...</option>
                            @foreach($pendingStudents as $student)
                                <option value="{{ $student->id }}">
                                    {{ $student->first_name }} {{ $student->last_name }} - {{ $student->course->course_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Practical Feedback Modal -->
<div class="modal fade" id="practicalFeedbackModal" tabindex="-1" role="dialog" aria-labelledby="practicalFeedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="practicalFeedbackModalLabel">Submit Practical Feedback</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('instructor.submit.practical.feedback') }}" method="POST">
                @csrf
                <input type="hidden" name="student_id" id="feedbackStudentId">
                <input type="hidden" name="schedule_id" id="feedbackScheduleId">
                <div class="modal-body">
                    <h5 id="feedbackStudentName"></h5>
                    
                    <div class="form-group">
                        <label>Status:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="statusCompleted" value="completed" checked>
                            <label class="form-check-label" for="statusCompleted">
                                Completed
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="statusFailed" value="failed">
                            <label class="form-check-label" for="statusFailed">
                                Failed
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="statusNotAppeared" value="not_appeared">
                            <label class="form-check-label" for="statusNotAppeared">
                                Did Not Appear
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="feedback">Feedback:</label>
                        <textarea class="form-control" id="feedback" name="feedback" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Feedback</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#pendingPracticalTable').DataTable();
        
        // View Practical Modal
        $('#viewPracticalModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var scheduleId = button.data('schedule-id');
            var course = button.data('course');
            var time = button.data('time');
            var studentId = button.data('student-id');
            var studentName = button.data('student-name');
            var modal = $(this);
            
            // Set the basic info
            modal.find('#practicalCourseName').text(course);
            modal.find('#practicalTime').text(time);
            modal.find('#practicalStudentName').text(studentName);
            
            // Pass info to feedback button
            modal.find('#submitFeedbackBtn').attr('data-schedule-id', scheduleId);
            modal.find('#submitFeedbackBtn').attr('data-student-id', studentId);
            modal.find('#submitFeedbackBtn').attr('data-student-name', studentName);
        });
        
        // Assign Practical Modal
        $('#assignPracticalModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var scheduleId = button.data('schedule-id');
            var modal = $(this);
            
            // Set the schedule ID
            modal.find('#practicalScheduleId').val(scheduleId);
        });
        
        // Practical Feedback Modal
        $('#practicalFeedbackModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var scheduleId = button.data('schedule-id');
            var studentId = button.data('student-id');
            var studentName = button.data('student-name');
            var modal = $(this);
            
            // Set the values
            modal.find('#feedbackScheduleId').val(scheduleId);
            modal.find('#feedbackStudentId').val(studentId);
            modal.find('#feedbackStudentName').text(studentName);
        });
        
        // Assign Student Button in table
        $('.assignStudentBtn').on('click', function() {
            var studentId = $(this).data('student-id');
            var studentName = $(this).data('student-name');
            
            // Open modal to select a schedule
            $('#selectScheduleModal').modal('show');
            $('#selectedStudentId').val(studentId);
            $('#selectedStudentName').text(studentName);
        });
    });
</script>
@endsection