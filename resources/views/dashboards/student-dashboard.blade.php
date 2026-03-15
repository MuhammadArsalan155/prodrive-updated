@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tachometer-alt mr-2 text-primary"></i>
                Student Dashboard
            </h1>
            <div>
                {{-- <a href="#" id="sendProgressReport" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm mr-2">
                    <i class="fas fa-file-alt fa-sm text-white-50 mr-1"></i> Send Progress Report
                </a> --}}
                <button class="btn btn-sm btn-info shadow-sm" data-toggle="modal" data-target="#invoiceModal">
                    <i class="fas fa-file-invoice fa-sm text-white-50 mr-1"></i> View Invoices
                </button>
            </div>
        </div>

        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title">
                                    <i class="fas fa-user mr-2 text-primary"></i>
                                    Welcome, {{ $student->first_name }} {{ $student->last_name }}!
                                </h5>
                                <p class="card-text">
                                    You are currently enrolled in the
                                    <strong>{{ $student->course->course_name }}</strong> course
                                    with <strong>{{ $student->instructor->instructor_name }}</strong>.
                                </p>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            Joined: {{ \Carbon\Carbon::parse($student->joining_date)->format('M d, Y') }}
                                        </small>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">
                                            <i class="fas fa-graduation-cap mr-1"></i>
                                            Status: @switch($student->course_status)
                                            @case(0)
                                                <span class="badge bg-warning">Pending</span>
                                            @break

                                            @case(1)
                                                <span class="badge bg-success">In Progress</span>
                                            @break

                                            @case(2)
                                                <span class="badge bg-info">Completed</span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">Unknown</span>
                                        @endswitch
                                            {{-- <span class="badge badge-{{ $student->course_status == 'completed' ? 'success' : 'primary' }}">{{ ucfirst($student->course_status) }}</span> --}}
                                        </small>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">
                                            <i class="fas fa-credit-card mr-1"></i>
                                            Payment: @switch($student->payment_status)
                                            @case(0)
                                                <span class="badge bg-danger">Unpaid</span>
                                            @break

                                            @case(1)
                                                <span class="badge bg-info">Pending</span>
                                            @break

                                            @case(2)
                                                <span class="badge bg-danger">Failed</span>
                                            @break

                                            @case(3)
                                                <span class="badge bg-success">Paid</span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">Unknown</span>
                                        @endswitch

                                            {{-- <span class="badge badge-{{ $student->payment_status == 'paid' ? 'success' : 'warning' }}">{{ ucfirst($student->payment_status) }}</span> --}}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            @if($student->profile_photo)
                                <div>
                                    <img src="{{ asset('storage/' . $student->profile_photo) }}"
                                         alt="Profile Photo"
                                         class="rounded-circle"
                                         style="width: 80px; height: 80px; object-fit: cover;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Progress Cards -->
        <div class="row mb-4">
            <!-- Overall Progress Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Overall Progress
                                </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                            {{ $courseProgress['progress_percentage'] }}%
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="progress progress-sm mr-2">
                                            <div class="progress-bar bg-primary" role="progressbar"
                                                style="width: {{ $courseProgress['progress_percentage'] }}%"
                                                aria-valuenow="{{ $courseProgress['progress_percentage'] }}"
                                                aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Theory Classes Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Theory Classes
                                </div>
                                @if($courseProgress['theory_classes']['required'] > 0)
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $courseProgress['theory_classes']['completed'] }} /
                                    {{ $courseProgress['theory_classes']['required'] }} classes
                                </div>
                                <div class="progress progress-sm mt-2">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ $courseProgress['theory_classes']['percentage'] }}%"
                                        aria-valuenow="{{ $courseProgress['theory_classes']['percentage'] }}"
                                        aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                @else
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $courseProgress['theory_hours']['completed'] }} /
                                    {{ $courseProgress['theory_hours']['total'] }} hrs
                                </div>
                                <div class="progress progress-sm mt-2">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ $courseProgress['theory_hours']['percentage'] }}%">
                                    </div>
                                </div>
                                @endif
                                <div class="text-xs text-muted mt-1">
                                    Status: {{ $courseProgress['theory_hours']['status'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-book-reader fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Practical Classes Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Practical Classes
                                </div>
                                @if($courseProgress['practical_classes']['required'] > 0)
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $courseProgress['practical_classes']['completed'] }} /
                                    {{ $courseProgress['practical_classes']['required'] }} classes
                                </div>
                                <div class="progress progress-sm mt-2">
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: {{ $courseProgress['practical_classes']['percentage'] }}%"
                                        aria-valuenow="{{ $courseProgress['practical_classes']['percentage'] }}"
                                        aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                @else
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $courseProgress['practical_hours']['completed'] }} /
                                    {{ $courseProgress['practical_hours']['total'] }} hrs
                                </div>
                                <div class="progress progress-sm mt-2">
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: {{ $courseProgress['practical_hours']['percentage'] }}%">
                                    </div>
                                </div>
                                @endif
                                <div class="text-xs text-muted mt-1">
                                    Status: {{ $courseProgress['practical_hours']['status'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-car fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lesson Plans Progress Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Lesson Plans
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $courseProgress['lesson_plans']['completed'] }} /
                                    {{ $courseProgress['lesson_plans']['total'] }} completed
                                </div>
                                <div class="progress progress-sm mt-2">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                        style="width: {{ $courseProgress['lesson_plans']['percentage'] }}%"
                                        aria-valuenow="{{ $courseProgress['lesson_plans']['percentage'] }}"
                                        aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="text-xs text-muted mt-1">
                                    {{ $courseProgress['lesson_plans']['percentage'] }}% Complete
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-tasks fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Overview -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-dollar-sign mr-2"></i>Financial Overview
                        </h6>
                        <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#paymentHistoryModal">
                            <i class="fas fa-history mr-1"></i>Payment History
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Payment Progress -->
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Payment Progress</h6>
                                <div class="row no-gutters align-items-center mb-3">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                            {{ $invoiceDetails['payment_percentage'] }}%
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="progress progress-sm mr-2">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $invoiceDetails['payment_percentage'] }}%"
                                                aria-valuenow="{{ $invoiceDetails['payment_percentage'] }}"
                                                aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">${{ $invoiceDetails['total_paid'] }}</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">${{ $invoiceDetails['total_pending'] }}</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">${{ $invoiceDetails['total_amount'] }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Installment Summary -->
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Installment Summary</h6>
                                <div class="row text-center">
                                    <div class="col-3">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $invoiceDetails['installments']['total'] }}</div>
                                    </div>
                                    <div class="col-3">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $invoiceDetails['installments']['paid'] }}</div>
                                    </div>
                                    <div class="col-3">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $invoiceDetails['installments']['pending'] }}</div>
                                    </div>
                                    <div class="col-3">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Overdue</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $invoiceDetails['installments']['overdue'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row">
            <!-- Left Column -->
            <div class="col-xl-8 col-lg-7">
                <!-- Upcoming Schedules -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-calendar-alt mr-2"></i>Upcoming Schedules
                        </h6>
                        <button class="btn btn-sm btn-outline-primary" data-toggle="collapse" data-target="#scheduleDetails">
                            <i class="fas fa-eye mr-1"></i>View All
                        </button>
                    </div>
                    <div class="card-body">
                        @forelse($upcomingSchedules as $schedule)
                        @php $rr = $schedule['reschedule_request']; @endphp
                            <div class="alert alert-light border-left-primary shadow-sm mb-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <strong>{{ $schedule['session_type'] }} Session</strong>
                                        <p class="mb-0 text-gray-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $schedule['day_name'] }}, {{ $schedule['date'] }} |
                                            {{ $schedule['start_time'] }} - {{ $schedule['end_time'] }}
                                        </p>
                                        <small class="text-muted">
                                            <i class="fas fa-user-tie mr-1"></i>
                                            Instructor: {{ $schedule['instructor_name'] }}
                                        </small>

                                        {{-- Reschedule request status --}}
                                        @if($rr)
                                            <div class="mt-2">
                                                @if($rr['status'] === 'pending')
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-clock mr-1"></i>Reschedule Requested
                                                    </span>
                                                    <small class="text-muted ml-1">
                                                        → {{ $rr['requested_date'] }} at {{ $rr['requested_start_time'] }}
                                                    </small>
                                                @elseif($rr['status'] === 'approved')
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check mr-1"></i>Reschedule Approved
                                                    </span>
                                                @elseif($rr['status'] === 'rejected')
                                                    <span class="badge badge-danger">
                                                        <i class="fas fa-times mr-1"></i>Reschedule Rejected
                                                    </span>
                                                    @if($rr['instructor_note'])
                                                        <small class="text-muted ml-1">{{ $rr['instructor_note'] }}</small>
                                                    @endif
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3 d-flex flex-column align-items-end" style="gap:.4rem;">
                                        <span class="badge badge-primary">Enrolled</span>
                                        @if(!$rr || $rr['status'] === 'rejected')
                                            <button class="btn btn-sm btn-outline-warning"
                                                data-toggle="modal"
                                                data-target="#rescheduleModal{{ $schedule['id'] }}">
                                                <i class="fas fa-exchange-alt mr-1"></i>Request Reschedule
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Reschedule Request Modal --}}
                            @if(!$rr || $rr['status'] === 'rejected')
                            <div class="modal fade" id="rescheduleModal{{ $schedule['id'] }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('student.reschedule.request', $schedule['id']) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-exchange-alt mr-2"></i>Request Reschedule
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="text-muted small mb-3">
                                                    Current: <strong>{{ $schedule['day_name'] }}, {{ $schedule['date'] }}</strong>
                                                    at <strong>{{ $schedule['start_time'] }}</strong>
                                                </p>
                                                <div class="form-group">
                                                    <label class="small font-weight-bold">Preferred Date <span class="text-danger">*</span></label>
                                                    <input type="date" name="requested_date" class="form-control form-control-sm"
                                                        min="{{ now()->toDateString() }}" required>
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group col-6">
                                                        <label class="small font-weight-bold">Preferred Start Time <span class="text-danger">*</span></label>
                                                        <input type="time" name="requested_start_time" class="form-control form-control-sm" required>
                                                    </div>
                                                    <div class="form-group col-6">
                                                        <label class="small font-weight-bold">Preferred End Time</label>
                                                        <input type="time" name="requested_end_time" class="form-control form-control-sm">
                                                    </div>
                                                </div>
                                                <div class="form-group mb-0">
                                                    <label class="small font-weight-bold">Reason (optional)</label>
                                                    <textarea name="reason" class="form-control form-control-sm" rows="2"
                                                        placeholder="Why do you need to reschedule?"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-paper-plane mr-1"></i>Send Request
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @empty
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-calendar-times mr-2"></i>
                                No upcoming schedules
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Previous Sessions -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-secondary">
                            <i class="fas fa-history mr-2"></i>Previous Sessions
                        </h6>
                        @if($pastSchedules->count() > 5)
                        <button class="btn btn-sm btn-outline-secondary" data-toggle="collapse" data-target="#pastScheduleDetails">
                            <i class="fas fa-eye mr-1"></i>View All
                        </button>
                        @endif
                    </div>
                    <div class="card-body">
                        @forelse($pastSchedules->take(5) as $session)
                            <div class="alert alert-success border-left-success shadow-sm mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $session['session_type'] }} Session</strong>
                                        <span class="badge badge-light text-dark ml-1">Class #{{ $session['class_order'] }}</span>
                                        <p class="mb-0 text-gray-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $session['day_name'] }}, {{ $session['date'] }} |
                                            {{ $session['start_time'] }} - {{ $session['end_time'] }}
                                        </p>
                                        <small class="text-muted">
                                            <i class="fas fa-user-tie mr-1"></i>
                                            Instructor: {{ $session['instructor_name'] }}
                                            &nbsp;|&nbsp;
                                            <i class="fas fa-check-circle mr-1 text-success"></i>
                                            Completed: {{ $session['completed_at'] }}
                                        </small>
                                    </div>
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Attended</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-calendar-check mr-2"></i>
                                No previous sessions yet
                            </div>
                        @endforelse

                        @if($pastSchedules->count() > 5)
                        <div class="collapse mt-3" id="pastScheduleDetails">
                            <hr>
                            <h6 class="font-weight-bold">All Previous Sessions</h6>
                            @foreach($pastSchedules->skip(5) as $session)
                                <div class="alert alert-success border-left-success shadow-sm mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $session['session_type'] }} Session</strong>
                                            <span class="badge badge-light text-dark ml-1">Class #{{ $session['class_order'] }}</span>
                                            <p class="mb-0 text-gray-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $session['day_name'] }}, {{ $session['date'] }} |
                                                {{ $session['start_time'] }} - {{ $session['end_time'] }}
                                            </p>
                                            <small class="text-muted">
                                                <i class="fas fa-user-tie mr-1"></i>
                                                Instructor: {{ $session['instructor_name'] }}
                                                &nbsp;|&nbsp;
                                                Completed: {{ $session['completed_at'] }}
                                            </small>
                                        </div>
                                        <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Attended</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Announcements Section -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-bullhorn mr-2"></i>Announcements
                        </h6>
                    </div>
                    <div class="card-body">
                        @forelse($announcements as $announcement)
                            <div class="alert alert-light border-left-info shadow-sm mb-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <strong>{{ $announcement['title'] }}</strong>
                                        <p class="mb-1 text-gray-800">{{ $announcement['content'] }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            {{ $announcement['date'] }}
                                            @if($announcement['expires_at'])
                                                | <i class="fas fa-clock mr-1"></i>Expires: {{ $announcement['expires_at'] }}
                                            @endif
                                        </small>
                                        @if($announcement['attachment_url'])
                                            <div class="mt-2">
                                                <a href="{{ $announcement['attachment_url'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-paperclip mr-1"></i>View Attachment
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-comment-slash mr-2"></i>
                                No current announcements
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Progress Reports -->
                @if (!empty($progressReports) && count($progressReports) > 0)
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-line mr-2"></i>Recent Progress Reports
                            </h6>
                        </div>
                        <div class="card-body">
                            @foreach ($progressReports as $report)
                                <div class="alert alert-light border-left-primary shadow-sm mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $report['course_name'] }}</strong>
                                            <p class="mb-0 text-gray-800">
                                                <i class="fas fa-user-tie mr-1"></i>
                                                {{ $report['instructor_name'] }}
                                            </p>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-check mr-1"></i>
                                                {{ $report['date'] }}
                                            </small>
                                        </div>
                                        <div class="text-right">
                                            <span class="badge badge-primary mr-2">
                                                {{ $report['rating'] }}
                                            </span>
                                            <br>
                                            <button class="btn btn-sm btn-outline-primary mt-1"
                                                    onclick="viewProgressReport({{ $report['id'] }})">
                                                View Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="col-xl-4 col-lg-5">
                <!-- Pending Installments -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-dollar-sign mr-2"></i>Pending Installments
                        </h6>
                    </div>
                    <div class="card-body">
                        @forelse($pendingInstallments as $installment)
                            <div class="alert alert-{{ $installment['urgency_class'] }} border-left-{{ $installment['urgency_class'] }} shadow-sm mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Installment Payment</strong>
                                        <p class="mb-0 text-gray-800">
                                            <i class="fas fa-money-bill-wave mr-1"></i>
                                            ${{ $installment['amount'] }}
                                        </p>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-check mr-1"></i>
                                            Due: {{ $installment['due_date'] }}
                                            @if($installment['is_overdue'])
                                                <br><i class="fas fa-exclamation-triangle mr-1"></i>
                                                {{ abs($installment['days_until_due']) }} days overdue
                                            @elseif($installment['days_until_due'] <= 7)
                                                <br><i class="fas fa-clock mr-1"></i>
                                                Due in {{ $installment['days_until_due'] }} days
                                            @endif
                                        </small>
                                    </div>
                                    <span class="badge badge-{{ $installment['urgency_class'] }}">
                                        {{ $installment['status_text'] }}
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-primary pay-installment-btn"
                                            data-installment-id="{{ $installment['id'] }}"
                                            data-amount="{{ $installment['amount'] }}">
                                        <i class="fas fa-credit-card mr-1"></i>Pay Now
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-money-check-alt mr-2"></i>
                                No pending installments
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Pending Feedback -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-comments mr-2"></i>Pending Feedback
                        </h6>
                    </div>
                    <div class="card-body">
                        @forelse($pendingFeedback as $feedback)
                            <div class="alert alert-light border-left-warning shadow-sm mb-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <strong>{{ $feedback['session_type'] }} Session</strong>
                                        <p class="mb-0 text-gray-800">
                                            <i class="fas fa-user-tie mr-1"></i>
                                            {{ $feedback['instructor_name'] }}
                                        </p>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-check mr-1"></i>
                                            {{ $feedback['session_date'] }}
                                            ({{ $feedback['days_ago'] }} days ago)
                                        </small>
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-warning submit-feedback-btn"
                                                    data-attendance-id="{{ $feedback['attendance_id'] }}">
                                                <i class="fas fa-comment-dots mr-1"></i>Submit Feedback
                                                @if(isset($feedback['class_order']))
                                                    <small>(Class {{ $feedback['class_order'] }})</small>
                                                @endif
                                            </button>
                                        </div>
                                    </div>
                                    <span class="badge badge-warning">
                                        {{ $feedback['status'] }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-comment-dots mr-2"></i>
                                No pending feedback
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">
                        <i class="fas fa-credit-card mr-2"></i>Make Payment
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Amount to Pay: $<span id="paymentAmount"></span></strong>
                    </div>
                    <form id="paymentForm">
                        <div class="form-group">
                            <label for="payment_method_id">Payment Method</label>
                            <select class="form-control" id="payment_method_id" name="payment_method_id" required>
                                <option value="">Select Payment Method</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method->id }}">{{ $method->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="payment_notes">Notes (Optional)</label>
                            <textarea class="form-control" id="payment_notes" name="payment_details[notes]" rows="3" placeholder="Any additional notes..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="processPaymentBtn">
                        <i class="fas fa-credit-card mr-1"></i>Process Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Modal -->
    <div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog" aria-labelledby="feedbackModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="feedbackModalLabel">
                        <i class="fas fa-comment-dots mr-2"></i>Submit Class Feedback
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="feedbackModalBody">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Loading feedback form...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitFeedbackBtn" style="display: none;">
                        <i class="fas fa-paper-plane mr-1"></i>Submit Feedback
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Details Modal -->
    <div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="invoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="invoiceModalLabel">
                        <i class="fas fa-file-invoice mr-2"></i>Invoice Details
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Installments</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoiceDetails['invoices'] as $invoice)
                                    <tr>
                                        <td>{{ $invoice['invoice_number'] }}</td>
                                        <td>${{ $invoice['amount'] }}</td>
                                        <td>
                                            <span class="badge badge-{{ $invoice['status'] == 'paid' ? 'success' : ($invoice['status'] == 'partial' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($invoice['status']) }}
                                            </span>
                                        </td>
                                        <td>{{ $invoice['created_at'] }}</td>
                                        <td>{{ $invoice['paid_installments'] }}/{{ $invoice['installments_count'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History Modal -->
    <div class="modal fade" id="paymentHistoryModal" tabindex="-1" role="dialog" aria-labelledby="paymentHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentHistoryModalLabel">
                        <i class="fas fa-history mr-2"></i>Payment History
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Invoice</th>
                                    <th>Transaction ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentHistory as $payment)
                                    <tr>
                                        <td>{{ $payment['date'] }}</td>
                                        <td>${{ $payment['amount'] }}</td>
                                        <td>{{ $payment['method'] }}</td>
                                        <td>{{ $payment['invoice_number'] }}</td>
                                        <td><small>{{ $payment['transaction_id'] }}</small></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No payment history available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    // Global variables
    let currentAttendanceId = null;
    let currentInstallmentId = null;
    let isProcessing = false;

    // CSRF Token setup
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!csrfToken) {
        console.warn('CSRF token not found. Make sure you have <meta name="csrf-token" content="{{ csrf_token() }}"> in your layout.');
    }

    // Utility function for API calls
    async function makeApiCall(url, method = 'GET', data = null) {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        };

        if (data && method !== 'GET') {
            options.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(url, options);
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Something went wrong');
            }

            return result;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    // Show loading state for buttons
    function setButtonLoading(button, loading, originalText = null) {
        if (loading) {
            if (!originalText) {
                originalText = button.innerHTML;
            }
            button.setAttribute('data-original-text', originalText);
            button.innerHTML = '<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>Loading...';
            button.disabled = true;
        } else {
            const storedText = button.getAttribute('data-original-text');
            button.innerHTML = storedText || originalText || 'Submit';
            button.disabled = false;
            button.removeAttribute('data-original-text');
        }
    }

    // Show toast notifications
    function showNotification(type, message) {
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else {
            alert(message);
        }
    }

    // Send Progress Report functionality
    // const sendProgressReportBtn = document.getElementById('sendProgressReport');
    // if (sendProgressReportBtn) {
    //     sendProgressReportBtn.addEventListener('click', async function(e) {
    //         e.preventDefault();

    //         if (isProcessing) return;
    //         isProcessing = true;

    //         setButtonLoading(this, true);

    //         try {
    //             const result = await makeApiCall('/student/dashboard/send-progress-report', 'POST');

    //             if (result.success) {
    //                 showNotification('success', result.message || 'Progress report sent successfully!');
    //             } else {
    //                 showNotification('error', result.message || 'Failed to send progress report');
    //             }
    //         } catch (error) {
    //             showNotification('error', 'Failed to send progress report. Please try again.');
    //         } finally {
    //             setButtonLoading(this, false);
    //             isProcessing = false;
    //         }
    //     });
    // }

    // Payment functionality
    function initializePaymentSystem() {
        // Handle pay installment button clicks
        document.querySelectorAll('.pay-installment-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (isProcessing) return;

                currentInstallmentId = this.getAttribute('data-installment-id');
                const amount = this.getAttribute('data-amount');

                document.getElementById('paymentAmount').textContent = amount;
                $('#paymentModal').modal('show');
            });
        });

        // Handle payment processing
        const processPaymentBtn = document.getElementById('processPaymentBtn');
        if (processPaymentBtn) {
            processPaymentBtn.addEventListener('click', async function() {
                if (isProcessing || !currentInstallmentId) return;

                const form = document.getElementById('paymentForm');
                const formData = new FormData(form);

                // Validate form
                const paymentMethodId = formData.get('payment_method_id');
                if (!paymentMethodId) {
                    showNotification('error', 'Please select a payment method');
                    return;
                }

                isProcessing = true;
                setButtonLoading(this, true);

                try {
                    const requestData = {
                        payment_method_id: paymentMethodId,
                        payment_details: {
                            notes: formData.get('payment_details[notes]') || ''
                        }
                    };

                    const result = await makeApiCall(
                        `/student/dashboard/process-payment/${currentInstallmentId}`,
                        'POST',
                        requestData
                    );

                    if (result.success) {
                        showNotification('success', result.message || 'Payment processed successfully!');
                        $('#paymentModal').modal('hide');

                        // Refresh page after successful payment
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showNotification('error', result.message || 'Payment processing failed');
                    }
                } catch (error) {
                    showNotification('error', 'Payment processing failed. Please try again.');
                } finally {
                    setButtonLoading(this, false);
                    isProcessing = false;
                }
            });
        }
    }

    // Feedback functionality
    function initializeFeedbackSystem() {
        // Handle submit feedback button clicks
        document.querySelectorAll('.submit-feedback-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (isProcessing) return;

                currentAttendanceId = this.getAttribute('data-attendance-id');
                loadFeedbackForm(currentAttendanceId);
            });
        });

        // Load feedback form
        async function loadFeedbackForm(attendanceId) {
            const modalBody = document.getElementById('feedbackModalBody');
            const submitBtn = document.getElementById('submitFeedbackBtn');

            // Reset modal content
            modalBody.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading feedback form...</p>
                </div>
            `;
            submitBtn.style.display = 'none';

            // Show modal
            $('#feedbackModal').modal('show');

            try {
                const result = await makeApiCall(`/student/feedback/${attendanceId}`);

                if (result.success) {
                    renderFeedbackForm(result.lesson_plan, result.questions, result.schedule);
                    submitBtn.style.display = 'inline-block';
                } else {
                    modalBody.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            ${result.message || 'No feedback form available for this session.'}
                        </div>
                    `;
                }
            } catch (error) {
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Failed to load feedback form. Please try again.
                    </div>
                `;
            }
        }

        // Render feedback form
        function renderFeedbackForm(lessonPlan, questions, schedule) {
            const modalBody = document.getElementById('feedbackModalBody');

            let formHtml = `
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-8">
                            <h6><i class="fas fa-book mr-2"></i>${lessonPlan.title}</h6>
                            <p class="text-muted">${lessonPlan.content}</p>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body p-2">
                                    <small class="text-muted">
                                        <strong>Session Details:</strong><br>
                                        Type: ${schedule.session_type}<br>
                                        Date: ${schedule.date}<br>
                                        Time: ${schedule.start_time} - ${schedule.end_time}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <form id="feedbackForm">
            `;

            if (questions && questions.length > 0) {
                questions.forEach((question, index) => {
                    formHtml += `
                        <div class="form-group mb-4">
                            <label class="font-weight-bold">${index + 1}. ${question.question_text}</label>
                            <div class="mt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio"
                                           name="responses[${question.id}][answer]"
                                           id="yes_${question.id}" value="1" required>
                                    <label class="form-check-label" for="yes_${question.id}">
                                        <i class="fas fa-check text-success mr-1"></i>Yes
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio"
                                           name="responses[${question.id}][answer]"
                                           id="no_${question.id}" value="0" required>
                                    <label class="form-check-label" for="no_${question.id}">
                                        <i class="fas fa-times text-danger mr-1"></i>No
                                    </label>
                                </div>
                            </div>
                            <div class="mt-2">
                                <textarea class="form-control"
                                          name="responses[${question.id}][comments]"
                                          placeholder="Additional comments (optional)"
                                          rows="2"></textarea>
                            </div>
                        </div>
                    `;
                });
            } else {
                formHtml += `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        No specific questions are available for this lesson. Please provide general feedback below.
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">General Feedback</label>
                        <textarea class="form-control" name="general_feedback"
                                  placeholder="Please provide your feedback about this session..."
                                  rows="4" required></textarea>
                    </div>
                `;
            }

            formHtml += '</form>';
            modalBody.innerHTML = formHtml;
        }

        // Handle feedback submission
        const submitFeedbackBtn = document.getElementById('submitFeedbackBtn');
        if (submitFeedbackBtn) {
            submitFeedbackBtn.addEventListener('click', async function() {
                if (isProcessing || !currentAttendanceId) return;

                const form = document.getElementById('feedbackForm');
                const formData = new FormData(form);

                // Validate form
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value && field.type !== 'radio') {
                        isValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                // Check radio groups
                const radioGroups = {};
                form.querySelectorAll('input[type="radio"][required]').forEach(radio => {
                    const groupName = radio.name;
                    if (!radioGroups[groupName]) {
                        radioGroups[groupName] = false;
                    }
                    if (radio.checked) {
                        radioGroups[groupName] = true;
                    }
                });

                Object.values(radioGroups).forEach(groupValid => {
                    if (!groupValid) isValid = false;
                });

                if (!isValid) {
                    showNotification('error', 'Please fill in all required fields');
                    return;
                }

                isProcessing = true;
                setButtonLoading(this, true);

                try {
                    // Convert FormData to JSON
                    const responses = {};
                    for (let [key, value] of formData.entries()) {
                        const matches = key.match(/responses\[(\d+)\]\[(\w+)\]/);
                        if (matches) {
                            const questionId = matches[1];
                            const field = matches[2];

                            if (!responses[questionId]) {
                                responses[questionId] = {};
                            }
                            responses[questionId][field] = value;
                        } else if (key === 'general_feedback') {
                            responses.general_feedback = value;
                        }
                    }

                    const result = await makeApiCall(
                        `/student/feedback/${currentAttendanceId}`,
                        'POST',
                        { responses: responses }
                    );

                    if (result.success) {
                        showNotification('success', result.message || 'Feedback submitted successfully!');
                        $('#feedbackModal').modal('hide');

                        // Refresh page to update pending feedback
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showNotification('error', result.message || 'Failed to submit feedback');
                    }
                } catch (error) {
                    showNotification('error', 'Failed to submit feedback. Please try again.');
                } finally {
                    setButtonLoading(this, false);
                    isProcessing = false;
                }
            });
        }
    }

    // Modal management
    function initializeModalManagement() {
        // Reset payment modal when hidden
        $('#paymentModal').on('hidden.bs.modal', function() {
            const form = document.getElementById('paymentForm');
            if (form) {
                form.reset();
                form.querySelectorAll('.is-invalid').forEach(field => {
                    field.classList.remove('is-invalid');
                });
            }
            currentInstallmentId = null;
            isProcessing = false;
        });

        // Reset feedback modal when hidden
        $('#feedbackModal').on('hidden.bs.modal', function() {
            currentAttendanceId = null;
            const submitBtn = document.getElementById('submitFeedbackBtn');
            if (submitBtn) {
                submitBtn.style.display = 'none';
            }
            isProcessing = false;
        });

        // Handle invoice modal
        $('#invoiceModal').on('show.bs.modal', function() {
            // You can add any additional logic here when invoice modal opens
        });

        // Handle payment history modal
        $('#paymentHistoryModal').on('show.bs.modal', function() {
            // You can add any additional logic here when payment history modal opens
        });
    }

    // Progress report viewing (placeholder function)
    window.viewProgressReport = function(reportId) {
        // You can implement this to show progress report details
        // For now, just show a notification
        showNotification('info', 'Progress report details feature will be implemented soon!');

        // Example implementation:
        // window.open(`/student/progress-reports/${reportId}`, '_blank');
    };

    // Utility function to format currency
    window.formatCurrency = function(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    };

    // Auto-refresh data every 5 minutes (optional)
    function setupAutoRefresh() {
        setInterval(() => {
            // Only refresh if no modals are open and no operations in progress
            if (!isProcessing &&
                !$('.modal').hasClass('show') &&
                document.visibilityState === 'visible') {

                // You can implement silent data refresh here
                console.log('Auto-refresh triggered');
                // Example: refreshDashboardData();
            }
        }, 5 * 60 * 1000); // 5 minutes
    }

    // Progress bar animations
    function animateProgressBars() {
        document.querySelectorAll('.progress-bar').forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.transition = 'width 1s ease-in-out';
                bar.style.width = width;
            }, 100);
        });
    }

    // Collapsible sections management
    function initializeCollapsibles() {
        document.querySelectorAll('[data-toggle="collapse"]').forEach(trigger => {
            trigger.addEventListener('click', function() {
                const target = document.querySelector(this.getAttribute('data-target'));
                if (target) {
                    // Add some visual feedback
                    const icon = this.querySelector('i');
                    if (icon) {
                        target.addEventListener('shown.bs.collapse', () => {
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        });
                        target.addEventListener('hidden.bs.collapse', () => {
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        });
                    }
                }
            });
        });
    }

    // Form validation enhancement
    function enhanceFormValidation() {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const invalidFields = form.querySelectorAll(':invalid');
                if (invalidFields.length > 0) {
                    e.preventDefault();
                    invalidFields[0].focus();
                    showNotification('error', 'Please fill in all required fields correctly');
                }
            });

            // Real-time validation feedback
            form.querySelectorAll('input, select, textarea').forEach(field => {
                field.addEventListener('blur', function() {
                    if (this.hasAttribute('required') && !this.value) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });

                field.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid') && this.value) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });
            });
        });
    }

    // Initialize all functionality
    function initialize() {
        try {
            initializePaymentSystem();
            initializeFeedbackSystem();
            initializeModalManagement();
            initializeCollapsibles();
            enhanceFormValidation();

            // Animate progress bars on load
            setTimeout(animateProgressBars, 500);

            // Setup auto-refresh (optional)
            // setupAutoRefresh();

            console.log('Student dashboard initialized successfully');
        } catch (error) {
            console.error('Error initializing student dashboard:', error);
            showNotification('error', 'Some features may not work properly. Please refresh the page.');
        }
    }

    // Start initialization
    initialize();

    // Handle page visibility change
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            // Page is visible again, you can refresh data if needed
            console.log('Page is now visible');
        }
    });

    // Handle beforeunload to warn about unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (isProcessing) {
            e.preventDefault();
            e.returnValue = 'You have an operation in progress. Are you sure you want to leave?';
            return e.returnValue;
        }
    });

    // Expose useful functions globally for debugging or external use
    window.studentDashboard = {
        refresh: () => window.location.reload(),
        showNotification: showNotification,
        isProcessing: () => isProcessing,
        formatCurrency: window.formatCurrency
    };
});
     </script>
     @endsection
