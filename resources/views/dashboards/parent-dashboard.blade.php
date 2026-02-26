@extends('layouts.master')

@section('title', 'Parent Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Overview</li>
@endsection

@section('content')
    <div class="container-fluid">
        @if (count($parent->students) == 0)
            <!-- No students found -->
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> You don't have any students associated with your account. Please
                contact the administration if you believe this is an error.
            </div>
        @else
            @if (count($parent->students) > 1)
                <!-- Student selector dropdown for multiple students -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Your Students</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($parent->students as $studentItem)
                                        <div class="col-md-4 mb-3">
                                            <div
                                                class="card h-100 {{ isset($student) && $student->id == $studentItem->id ? 'border-primary' : '' }}">
                                                <div class="card-body text-center">
                                                    @if ($studentItem->profile_photo)
                                                        <img src="{{ asset('storage/' . $studentItem->profile_photo) }}"
                                                            alt="Profile Photo" class="img-fluid rounded-circle mb-3"
                                                            style="max-width: 100px; max-height: 100px;">
                                                    @else
                                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white mb-3 mx-auto"
                                                            style="width: 100px; height: 100px; font-size: 2rem;">
                                                            {{ strtoupper(substr($studentItem->first_name, 0, 1)) }}{{ strtoupper(substr($studentItem->last_name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <h5>{{ $studentItem->first_name }} {{ $studentItem->last_name }}</h5>
                                                    <p class="text-muted mb-2">ID: {{ $studentItem->id }}</p>
                                                    <p class="mb-3">
                                                        <span
                                                            class="badge bg-{{ $studentItem->course_status == 'completed' ? 'success' : ($studentItem->course_status == 'in_progress' ? 'warning' : 'secondary') }}">
                                                            {{ ucfirst(str_replace('_', ' ', $studentItem->course_status ?? 'Not Started')) }}
                                                        </span>
                                                    </p>
                                                    <a href="{{ route('parent.dashboard', ['student_id' => $studentItem->id]) }}"
                                                        class="btn btn-{{ isset($student) && $student->id == $studentItem->id ? 'primary' : 'outline-primary' }} btn-sm">
                                                        {{ isset($student) && $student->id == $studentItem->id ? 'Currently Viewing' : 'View Details' }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Student Information Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Student Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center mb-3 mb-md-0">
                                    @if ($student->profile_photo)
                                        <img src="{{ asset('storage/' . $student->profile_photo) }}" alt="Profile Photo"
                                            class="img-fluid rounded-circle mb-3"
                                            style="max-width: 150px; max-height: 150px;">
                                    @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white mb-3"
                                            style="width: 150px; height: 150px; font-size: 3rem; margin: 0 auto;">
                                            {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <h4>{{ $student->first_name }} {{ $student->last_name }}</h4>
                                    <p class="text-muted mb-0">Student ID: {{ $student->id }}</p>
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <h6 class="text-muted">Email</h6>
                                            <p class="mb-0">{{ $student->email }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <h6 class="text-muted">Contact</h6>
                                            <p class="mb-0">{{ $student->student_contact ?: 'Not provided' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <h6 class="text-muted">Date of Birth</h6>
                                            <p class="mb-0">
                                                {{ $student->student_dob ? date('M d, Y', strtotime($student->student_dob)) : 'Not provided' }}
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <h6 class="text-muted">Address</h6>
                                            <p class="mb-0">{{ $student->address ?: 'Not provided' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <h6 class="text-muted">Joining Date</h6>
                                            <p class="mb-0">
                                                {{ $student->joining_date ? date('M d, Y', strtotime($student->joining_date)) : 'Not provided' }}
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <h6 class="text-muted">Expected Completion</h6>
                                            <p class="mb-0">
                                                {{ $student->completion_date ? date('M d, Y', strtotime($student->completion_date)) : 'Not set' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Announcements Section -->
            @include('components.dashboard-announcements')
            <!-- Course Information and Progress -->
            <div class="row mb-4">
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Course Information</h5>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $student->course->title ?? 'No Course Assigned' }}</h5>
                            <p class="card-text">{{ $student->course->description ?? '' }}</p>

                            @if ($student->course)
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Course Status:</strong></p>
                                        <span class="badge bg-{{ $student->course_status == 2 ? 'info' : ($student->course_status == 1 ? 'success' : ($student->course_status == 0 ? 'warning' : 'secondary')) }} p-2">
                                            {{ $student->course_status == 2 ? 'Completed' : ($student->course_status == 1 ? 'In Progress' : ($student->course_status == 0 ? 'Pending' : 'Unknown')) }}
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Payment Status:</strong></p>
                                        <span class="badge bg-{{ $student->payment_status == 3 ? 'success' : ($student->payment_status == 2 ? 'danger' : ($student->payment_status == 1 ? 'info' : ($student->payment_status == 0 ? 'danger' : 'secondary'))) }} p-2">
                                            {{ $student->payment_status == 3 ? 'Paid' : ($student->payment_status == 2 ? 'Failed' : ($student->payment_status == 1 ? 'Pending' : ($student->payment_status == 0 ? 'Unpaid' : 'Unknown'))) }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <h6 class="mt-4 mb-3">Instructor Information</h6>
                            @if ($student->instructor)
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 50px; height: 50px; font-size: 1.5rem;">
                                            {{ strtoupper(substr($student->instructor->instructor_name, 0, 2)) }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">{{ $student->instructor->instructor_name }}</h6>
                                        <p class="text-muted mb-0">{{ $student->instructor->email ?? '' }}</p>
                                    </div>
                                </div>
                            @else
                                <p>No instructor assigned</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Course Progress</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <h6>Theory Progress</h6>
                                    <span>{{ $courseProgress['theory']['completed'] }} /
                                        {{ $courseProgress['theory']['total'] }} hours</span>
                                </div>
                                <div class="progress mb-1">
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: {{ $courseProgress['theory']['percentage'] }}%">
                                        {{ $courseProgress['theory']['percentage'] }}%
                                    </div>
                                </div>
                                <p class="text-muted small">
                                    @if ($student->theory_status == 'completed')
                                        Completed on {{ date('M d, Y', strtotime($student->theory_completion_date)) }}
                                    @elseif($student->theory_status == 'in_progress')
                                        In progress
                                    @else
                                        Not started
                                    @endif
                                </p>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <h6>Practical Progress</h6>
                                    <span>{{ $courseProgress['practical']['completed'] }} /
                                        {{ $courseProgress['practical']['total'] }} hours</span>
                                </div>
                                <div class="progress mb-1">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                        style="width: {{ $courseProgress['practical']['percentage'] }}%">
                                        {{ $courseProgress['practical']['percentage'] }}%
                                    </div>
                                </div>
                                <p class="text-muted small">
                                    @if ($student->practical_status == 'completed')
                                        Completed on {{ date('M d, Y', strtotime($student->practical_completion_date)) }}
                                    @elseif($student->practical_status == 'in_progress')
                                        In progress
                                    @else
                                        Not started
                                    @endif
                                </p>
                            </div>

                            <div class="mb-2">
                                <div class="d-flex justify-content-between mb-2">
                                    <h6>Overall Progress</h6>
                                    <span>
                                        {{ $courseProgress['theory']['completed'] + $courseProgress['practical']['completed'] }}
                                        / {{ $courseProgress['theory']['total'] + $courseProgress['practical']['total'] }}
                                        hours
                                    </span>
                                </div>
                                <div class="progress mb-1">
                                    <div class="progress-bar bg-primary" role="progressbar"
                                        style="width: {{ $courseProgress['theory']['total'] + $courseProgress['practical']['total'] > 0
                                            ? round(
                                                (($courseProgress['theory']['completed'] + $courseProgress['practical']['completed']) /
                                                    ($courseProgress['theory']['total'] + $courseProgress['practical']['total'])) *
                                                    100,
                                            )
                                            : 0 }}%">
                                        {{ $courseProgress['theory']['total'] + $courseProgress['practical']['total'] > 0
                                            ? round(
                                                (($courseProgress['theory']['completed'] + $courseProgress['practical']['completed']) /
                                                    ($courseProgress['theory']['total'] + $courseProgress['practical']['total'])) *
                                                    100,
                                            )
                                            : 0 }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary and Next Schedule -->
            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">Financial Summary</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $totalBilled = 0;
                                $totalPaid = 0;
                                $pendingPayments = 0;

                                foreach ($student->invoices as $invoice) {
                                    $totalBilled += $invoice->amount;
                                    $totalPaid += $invoice->payments->where('status', 'completed')->sum('amount');

                                    foreach ($invoice->installments as $installment) {
                                        if ($installment->status === 'pending') {
                                            $pendingPayments += $installment->amount;
                                        }
                                    }
                                }
                            @endphp

                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="border rounded p-3 mb-3">
                                        <h3 class="text-primary">{{ number_format($totalBilled, 2) }}</h3>
                                        <p class="text-muted mb-0">Total Billed</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-3 mb-3">
                                        <h3 class="text-success">{{ number_format($totalPaid, 2) }}</h3>
                                        <p class="text-muted mb-0">Total Paid</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-3 mb-3">
                                        <h3 class="text-danger">{{ number_format($pendingPayments, 2) }}</h3>
                                        <p class="text-muted mb-0">Pending</p>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-3">
                                <a href="{{ route('parent.financial') }}" class="btn btn-outline-primary">View Financial
                                    Details</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">Next Scheduled Sessions</h5>
                        </div>
                        <div class="card-body">
                            @if ($student->practicalSchedule)
                                <div class="border rounded p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-3 text-center mb-3 mb-md-0">
                                            <div class="bg-light rounded p-2 text-center">
                                                <h5 class="mb-0">
                                                    {{ $student->practicalSchedule->start_date ? date('d', strtotime($student->practicalSchedule->start_date)) : 'N/A' }}
                                                </h5>
                                                <p class="small mb-0">
                                                    {{ $student->practicalSchedule->start_date ? date('M', strtotime($student->practicalSchedule->start_date)) : '' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <h6>Practical Session</h6>
                                            <p class="mb-1">
                                                <i class="fas fa-clock text-muted me-2"></i>
                                                {{ $student->practicalSchedule->start_time ? date('h:i A', strtotime($student->practicalSchedule->start_time)) : 'TBD' }}
                                                -
                                                {{ $student->practicalSchedule->end_time ? date('h:i A', strtotime($student->practicalSchedule->end_time)) : 'TBD' }}
                                            </p>
                                            <p class="mb-0">
                                                <i class="fas fa-user text-muted me-2"></i>
                                                With:
                                                {{ $student->instructor->instructor_name ?? 'No instructor assigned' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <h6>No upcoming scheduled sessions</h6>
                                    <p class="text-muted">Check back later for updates.</p>
                                </div>
                            @endif

                            <div class="text-center mt-3">
                                <a href="{{ route('parent.schedule') }}" class="btn btn-outline-primary">View Full
                                    Schedule</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Progress Reports -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">Recent Progress Reports</h5>
                        </div>
                        <div class="card-body">
                            @if (isset($progressReports) && count($progressReports) > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Instructor</th>
                                                <th>Rating</th>
                                                <th>Type</th>
                                                <th>Comments</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($progressReports as $report)
                                                <tr>
                                                    <td>{{ date('M d, Y', strtotime($report->created_at)) }}</td>
                                                    <td>{{ $report->instructor->instructor_name ?? 'N/A' }}</td>
                                                    <td>
                                                        <div class="d-flex">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <i
                                                                    class="fas fa-star {{ $i <= $report->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                            @endfor
                                                        </div>
                                                    </td>
                                                    <td>{{ ucfirst($report->type ?? 'General') }}</td>
                                                    <td>{{ \Illuminate\Support\Str::limit($report->comments, 100) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                    <h6>No progress reports available</h6>
                                    <p class="text-muted">Reports will appear here as they are added by instructors.</p>
                                </div>
                            @endif

                            <div class="text-center mt-3">
                                <a href="{{ route('parent.academic.progress') }}" class="btn btn-outline-primary">View
                                    All Progress Reports</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        // Any dashboard-specific JavaScript can be added here
    </script>
@endsection
