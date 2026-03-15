@extends('layouts.master')

@push('styles')
    <style>
        .status-section {
            margin-bottom: 20px;
        }

        .status-section .card-header {
            font-weight: bold;
        }

        .calendar-day {
            height: 150px;
            border: 1px solid #e3e6f0;
            overflow-y: auto;
        }

        .calendar-day.other-month {
            background-color: #f8f9fc;
            color: #b7b9cc;
        }

        .calendar-event {
            background-color: #4e73df;
            color: white;
            border-radius: 3px;
            padding: 3px 5px;
            margin-bottom: 2px;
            font-size: 12px;
        }

        .calendar-event.theory {
            background-color: #1cc88a;
        }

        .calendar-event.practical {
            background-color: #4e73df;
        }

        .calendar-date {
            font-weight: bold;
            padding: 3px;
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            text-align: right;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Instructor Dashboard</h1>
            <div>
                <a href="{{ route('instructor.theory.calendar') }}"
                    class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                    <i class="fas fa-calendar-alt fa-sm text-white-50"></i> Theory Calendar
                </a>
                <a href="{{ route('instructor.practical.calendar') }}"
                    class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-calendar-alt fa-sm text-white-50"></i> Practical Calendar
                </a>
            </div>
        </div>

        <!-- Stats Cards Row -->
        <div class="row">
            <!-- Total Students Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Students</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['totalStudents'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Theory Students Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Pending Theory</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['pendingTheoryStudents'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-book fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Practical Students Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Pending Practical
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['pendingPracticalStudents'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-car fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Students Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Completed Students</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['completedStudents'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Announcements Section -->
        @include('components.dashboard-announcements')
        
        <!-- Student Status Sections -->
        <div class="row">
            <!-- Theory Classes -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Theory Classes</h6>
                        <a href="{{ route('instructor.theory.calendar') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-calendar-alt"></i> Full Calendar
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="status-section">
                            <h5>Pending Theory Students
                                <span class="badge badge-info">{{ $studentCounts['theory']['pending'] }}</span>
                                <a href="{{ route('instructor.students.status', 'theory-pending') }}"
                                    class="btn btn-sm btn-outline-info">View All</a>
                            </h5>
                            @if($studentCounts['theory']['in_progress'] > 0)
                            <h5 class="mt-2">Theory In Progress
                                <span class="badge badge-warning">{{ $studentCounts['theory']['in_progress'] }}</span>
                                <a href="{{ route('instructor.students.status', 'theory-in-progress') }}"
                                    class="btn btn-sm btn-outline-warning">View All</a>
                            </h5>
                            @endif

                            <!-- Theory Classes Calendar Preview -->
                            <div class="mt-3">
                                <h6>Upcoming Theory Classes</h6>
                                @if ($theoryClasses->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                    <th>Course</th>
                                                    <th>Students</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($theoryClasses as $class)
                                                    <tr>
                                                        <td>{{ $class->date->format('M d, Y') }}</td>
                                                        <td>{{ $class->start_time->format('h:i A') }} -
                                                            {{ $class->end_time->format('h:i A') }}</td>
                                                        <td>{{ $class->course->course_name }}</td>
                                                        <td>{{ $class->students->count() }}</td>
                                                        <td>
                                                            <button class="btn btn-sm btn-success" data-toggle="modal"
                                                                data-target="#completeTheoryModal"
                                                                data-schedule-id="{{ $class->id }}">
                                                                Mark Complete
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-center">No upcoming theory classes scheduled.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Practical Classes -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Practical Classes</h6>
                        <a href="{{ route('instructor.practical.calendar') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-calendar-alt"></i> Full Calendar
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="status-section">
                            <h5>Pending Practical Assignment
                                <span class="badge badge-info">{{ $studentCounts['practical']['pending'] }}</span>
                                <a href="{{ route('instructor.students.status', 'practical-pending') }}"
                                    class="btn btn-sm btn-outline-info">View All</a>
                            </h5>

                            <!-- Practical Classes Calendar Preview -->
                            <div class="mt-3">
                                <h6>Upcoming Practical Classes</h6>
                                @if ($practicalClasses->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                    <th>Course</th>
                                                    <th>Student</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($practicalClasses as $class)
                                                    <tr>
                                                        <td>{{ $class->date->format('M d, Y') }}</td>
                                                        <td>{{ $class->start_time->format('h:i A') }} -
                                                            {{ $class->end_time->format('h:i A') }}</td>
                                                        <td>{{ $class->course->course_name }}</td>
                                                        <td>
                                                            @if ($class->students->count() > 0)
                                                                {{ $class->students->first()->first_name }}
                                                                {{ $class->students->first()->last_name }}
                                                            @else
                                                                <span class="text-warning">Not assigned</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($class->students->count() > 0)
                                                                <button class="btn btn-sm btn-primary" data-toggle="modal"
                                                                    data-target="#practicalFeedbackModal"
                                                                    data-schedule-id="{{ $class->id }}"
                                                                    data-student-id="{{ $class->students->first()->id }}"
                                                                    data-student-name="{{ $class->students->first()->first_name }} {{ $class->students->first()->last_name }}">
                                                                    Submit Feedback
                                                                </button>
                                                            @else
                                                                <button class="btn btn-sm btn-info" data-toggle="modal"
                                                                    data-target="#assignPracticalModal"
                                                                    data-schedule-id="{{ $class->id }}">
                                                                    Assign Student
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-center">No upcoming practical classes scheduled.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities Row -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Student Activities</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Theory Status</th>
                                        <th>Practical Status</th>
                                        <th>Last Updated</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentActivities as $student)
                                        <tr>
                                            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                            <td>{{ $student->course->course_name }}</td>
                                            <td>
                                                <span
                                                    class="badge 
                                                @if ($student->theory_status == 'completed') badge-success
                                                @elseif($student->theory_status == 'in_progress') badge-info
                                                @else badge-warning @endif">
                                                    {{ ucfirst($student->theory_status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge 
                                                @if ($student->practical_status == 'completed') badge-success
                                                @elseif($student->practical_status == 'assigned') badge-info
                                                @elseif($student->practical_status == 'failed') badge-danger
                                                @elseif($student->practical_status == 'not_appeared') badge-secondary
                                                @else badge-warning @endif">
                                                    {{ ucfirst($student->practical_status) }}
                                                </span>
                                            </td>
                                            <td>{{ $student->updated_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mark Theory Complete Modal -->
    <div class="modal fade" id="completeTheoryModal" tabindex="-1" role="dialog"
        aria-labelledby="completeTheoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="completeTheoryModalLabel">Mark Theory Class Complete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('instructor.mark.theory.complete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="schedule_id" id="theoryScheduleId">
                    <div class="modal-body">
                        <p>Select students who completed this theory class:</p>
                        <div id="theoryStudentsList" class="student-checklist">
                            <!-- Students will be loaded dynamically -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Mark Complete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assign Practical Modal -->
    <div class="modal fade" id="assignPracticalModal" tabindex="-1" role="dialog"
        aria-labelledby="assignPracticalModalLabel" aria-hidden="true">
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
                                @foreach (App\Models\Student::where('instructor_id', Auth::guard('instructor')->id())->where('theory_status', 'completed')->where('practical_status', 'pending')->with('course')->get() as $student)
                                    <option value="{{ $student->id }}">
                                        {{ $student->first_name }} {{ $student->last_name }} -
                                        {{ $student->course->course_name }}
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
    <div class="modal fade" id="practicalFeedbackModal" tabindex="-1" role="dialog"
        aria-labelledby="practicalFeedbackModalLabel" aria-hidden="true">
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
                        <h5 id="studentName"></h5>

                        <div class="form-group">
                            <label>Status:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="statusCompleted"
                                    value="completed" checked>
                                <label class="form-check-label" for="statusCompleted">
                                    Completed
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="statusFailed"
                                    value="failed">
                                <label class="form-check-label" for="statusFailed">
                                    Failed
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="statusNotAppeared"
                                    value="not_appeared">
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
            // Theory Complete Modal
            $('#completeTheoryModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var scheduleId = button.data('schedule-id');
                var modal = $(this);

                // Set the schedule ID
                modal.find('#theoryScheduleId').val(scheduleId);

                // Load students for this schedule
                $.ajax({
                    url: '/instructor/get-theory-students/' + scheduleId,
                    type: 'GET',
                    success: function(response) {
                        var html = '';
                        if (response.students.length > 0) {
                            $.each(response.students, function(index, student) {
                                html += '<div class="form-check">';
                                html +=
                                    '<input class="form-check-input" type="checkbox" name="student_ids[]" id="student_' +
                                    student.id + '" value="' + student.id + '">';
                                html +=
                                    '<label class="form-check-label" for="student_' +
                                    student.id + '">' + student.first_name + ' ' +
                                    student.last_name + '</label>';
                                html += '</div>';
                            });
                        } else {
                            html = '<p>No students found for this class.</p>';
                        }

                        $('#theoryStudentsList').html(html);
                    }
                });
            });

            // Assign Practical Modal
            $('#assignPracticalModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var scheduleId = button.data('schedule-id');
                var modal = $(this);

                // Set the schedule ID
                modal.find('#practicalScheduleId').val(scheduleId);
            });

            // Practical Feedback Modal
            $('#practicalFeedbackModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var scheduleId = button.data('schedule-id');
                var studentId = button.data('student-id');
                var studentName = button.data('student-name');
                var modal = $(this);

                // Set the values
                modal.find('#feedbackScheduleId').val(scheduleId);
                modal.find('#feedbackStudentId').val(studentId);
                modal.find('#studentName').text(studentName);
            });
        });
    </script>
@endsection
