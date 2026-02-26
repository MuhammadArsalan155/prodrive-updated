@extends('layouts.master')

@section('title', 'Schedule Information')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Schedule</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Schedule Information - {{ $student->first_name }} {{ $student->last_name }}</h5>
                </div>
                <div class="card-body">
                    <!-- Current Course Status -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5 class="card-title">{{ $student->course->title ?? 'No Course Assigned' }}</h5>
                                            <p class="mb-2">Instructor: {{ $student->instructor->instructor_name ?? 'Not Assigned' }}</p>
                                            <p class="mb-0">
                                                <span class="badge bg-{{ $student->course_status == 'completed' ? 'success' : ($student->course_status == 'in_progress' ? 'warning' : 'secondary') }} p-2">
                                                    {{ ucfirst(str_replace('_', ' ', $student->course_status ?? 'Not Started')) }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <p class="mb-2">
                                                <strong>Start Date:</strong> {{ $student->joining_date ? date('M d, Y', strtotime($student->joining_date)) : 'Not set' }}
                                            </p>
                                            <p class="mb-0">
                                                <strong>Expected Completion:</strong> {{ $student->completion_date ? date('M d, Y', strtotime($student->completion_date)) : 'Not set' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Schedule -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">Upcoming Practical Session</h5>
                                </div>
                                <div class="card-body">
                                    @if($student->practicalSchedule)
                                        <div class="row">
                                            <div class="col-md-3 text-center mb-3 mb-md-0">
                                                <div class="bg-light rounded p-4 text-center">
                                                    <h2 class="mb-0">{{ $student->practicalSchedule->start_date ? date('d', strtotime($student->practicalSchedule->start_date)) : 'N/A' }}</h2>
                                                    <p class="mb-0">{{ $student->practicalSchedule->start_date ? date('M Y', strtotime($student->practicalSchedule->start_date)) : '' }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="ps-md-4">
                                                    <h5>Practical Driving Session</h5>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <p class="mb-2">
                                                                <i class="fas fa-clock text-muted me-2"></i>
                                                                <strong>Time:</strong> 
                                                                {{ $student->practicalSchedule->start_time ? date('h:i A', strtotime($student->practicalSchedule->start_time)) : 'TBD' }} - 
                                                                {{ $student->practicalSchedule->end_time ? date('h:i A', strtotime($student->practicalSchedule->end_time)) : 'TBD' }}
                                                            </p>
                                                            <p class="mb-2">
                                                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                                                <strong>Location:</strong> 
                                                                {{ $student->practicalSchedule->location ?? 'School Premises' }}
                                                            </p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-2">
                                                                <i class="fas fa-user text-muted me-2"></i>
                                                                <strong>Instructor:</strong> 
                                                                {{ $student->instructor->instructor_name ?? 'Not Assigned' }}
                                                            </p>
                                                            <p class="mb-2">
                                                                <i class="fas fa-car text-muted me-2"></i>
                                                                <strong>Vehicle:</strong> 
                                                                {{ $student->practicalSchedule->vehicle ?? 'School Vehicle' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="alert alert-info mb-0">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        <strong>Important:</strong> Please ensure {{ $student->first_name }} arrives 15 minutes before the scheduled time. 
                                                        If you need to reschedule, please contact us at least 24 hours in advance.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                            <h5>No Upcoming Scheduled Sessions</h5>
                                            <p class="text-muted">There are no practical sessions scheduled at this time.</p>
                                            <p>Please contact the school administration to schedule a practical session.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Theory Schedule -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">Theory Classes Schedule</h5>
                                </div>
                                <div class="card-body">
                                    @if($student->theory_status == 'completed')
                                        <div class="text-center py-4">
                                            <i class="fas fa-graduation-cap fa-3x text-success mb-3"></i>
                                            <h5>Theory Course Completed</h5>
                                            <p>Completed on: {{ date('M d, Y', strtotime($student->theory_completion_date)) }}</p>
                                        </div>
                                    @elseif($student->course && isset($student->course->theory_schedule))
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Day</th>
                                                        <th>Time</th>
                                                        <th>Topic</th>
                                                        <th>Instructor</th>
                                                        <th>Location</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        // This would normally come from the database
                                                        // For demonstration, we'll create some example theory classes
                                                        $theorySchedule = [
                                                            [
                                                                'day' => 'Monday',
                                                                'time' => '10:00 AM - 12:00 PM',
                                                                'topic' => 'Traffic Rules & Regulations',
                                                                'instructor' => $student->instructor->instructor_name ?? 'John Doe',
                                                                'location' => 'Classroom A'
                                                            ],
                                                            [
                                                                'day' => 'Wednesday',
                                                                'time' => '10:00 AM - 12:00 PM',
                                                                'topic' => 'Road Signs & Markings',
                                                                'instructor' => $student->instructor->instructor_name ?? 'John Doe',
                                                                'location' => 'Classroom A'
                                                            ],
                                                            [
                                                                'day' => 'Friday',
                                                                'time' => '10:00 AM - 12:00 PM',
                                                                'topic' => 'Defensive Driving Techniques',
                                                                'instructor' => $student->instructor->instructor_name ?? 'John Doe',
                                                                'location' => 'Classroom B'
                                                            ]
                                                        ];
                                                    @endphp
                                                    
                                                    @foreach($theorySchedule as $class)
                                                        <tr>
                                                            <td>{{ $class['day'] }}</td>
                                                            <td>{{ $class['time'] }}</td>
                                                            <td>{{ $class['topic'] }}</td>
                                                            <td>{{ $class['instructor'] }}</td>
                                                            <td>{{ $class['location'] }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                            <h5>No Theory Classes Scheduled</h5>
                                            <p class="text-muted">Theory classes have not been scheduled yet.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Calendar View -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Monthly Calendar</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info mb-4">
                                        <i class="fas fa-info-circle me-2"></i>
                                        This calendar shows all scheduled sessions for {{ $student->first_name }} {{ $student->last_name }}.
                                    </div>
                                    
                                    <div class="calendar-placeholder text-center py-5">
                                        <!-- In a real application, you would implement a proper calendar here -->
                                        <!-- This is just a placeholder for demonstration -->
                                        <div class="mb-4">
                                            <h4 class="mb-3">{{ date('F Y') }}</h4>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-outline-secondary">
                                                    <i class="fas fa-chevron-left"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary">
                                                    <i class="fas fa-chevron-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="calendar-grid">
                                            <div class="row mb-2">
                                                <div class="col">Sun</div>
                                                <div class="col">Mon</div>
                                                <div class="col">Tue</div>
                                                <div class="col">Wed</div>
                                                <div class="col">Thu</div>
                                                <div class="col">Fri</div>
                                                <div class="col">Sat</div>
                                            </div>
                                            
                                            @php
                                                $daysInMonth = date('t');
                                                $firstDayOfMonth = date('N', strtotime(date('Y-m-01')));
                                                $firstDayOfMonth = $firstDayOfMonth % 7; // Convert to 0-6 range (Sun-Sat)
                                                $currentDay = 1;
                                                $weeks = ceil(($daysInMonth + $firstDayOfMonth) / 7);
                                                
                                                // For demonstration purposes, let's mark a few days as having events
                                                $eventDays = [5, 12, 19, 26]; // Example days with theory classes
                                                $practicalDay = 15; // Example day with practical session
                                            @endphp
                                            
                                            @for($week = 0; $week < $weeks; $week++)
                                                <div class="row mb-2">
                                                    @for($day = 0; $day < 7; $day++)
                                                        <div class="col">
                                                            @if(($week == 0 && $day < $firstDayOfMonth) || ($currentDay > $daysInMonth))
                                                                &nbsp;
                                                            @else
                                                                <div class="calendar-day p-1 border rounded {{ in_array($currentDay, $eventDays) ? 'bg-warning text-dark' : '' }} {{ $currentDay == $practicalDay ? 'bg-info text-white' : '' }}">
                                                                    {{ $currentDay }}
                                                                    @if(in_array($currentDay, $eventDays))
                                                                        <div class="small">Theory</div>
                                                                    @endif
                                                                    @if($currentDay == $practicalDay)
                                                                        <div class="small">Practical</div>
                                                                    @endif
                                                                </div>
                                                                @php $currentDay++; @endphp
                                                            @endif
                                                        </div>
                                                    @endfor
                                                </div>
                                            @endfor
                                        </div>
                                        
                                        <div class="mt-4">
                                            <div class="d-inline-block me-3">
                                                <span class="badge bg-warning p-2">&nbsp;</span> Theory Class
                                            </div>
                                            <div class="d-inline-block">
                                                <span class="badge bg-info p-2">&nbsp;</span> Practical Session
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .calendar-day {
        min-height: 50px;
    }
</style>
@endsection