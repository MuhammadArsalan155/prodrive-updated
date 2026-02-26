@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Student Profile</h1>
        <div>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Student Information Card -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Student Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($student->profile_photo)
                            <img class="img-profile rounded-circle" style="width: 150px; height: 150px; object-fit: cover;" 
                                src="{{ asset('profile/' . $student->profile_photo) }}">
                        @else
                            <img class="img-profile rounded-circle" style="width: 150px; height: 150px; object-fit: cover;" 
                                src="{{ asset('img/undraw_profile.svg') }}">
                        @endif
                        <h4 class="mt-3">{{ $student->first_name }} {{ $student->last_name }}</h4>
                        <span class="badge badge-pill 
                            @if($student->theory_status == 'completed' && $student->practical_status == 'completed') 
                                badge-success
                            @elseif($student->theory_status == 'completed') 
                                badge-primary
                            @else 
                                badge-warning 
                            @endif">
                            {{ $student->theory_status == 'completed' && $student->practical_status == 'completed' ? 'Completed' : 
                               ($student->theory_status == 'completed' ? 'Pending Practical' : 'Pending Theory') }}
                        </span>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 font-weight-bold">Student ID:</div>
                        <div class="col-sm-8">{{ $student->student_id ?? 'N/A' }}</div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-sm-4 font-weight-bold">Email:</div>
                        <div class="col-sm-8">{{ $student->email }}</div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-sm-4 font-weight-bold">Contact:</div>
                        <div class="col-sm-8">{{ $student->student_contact }}</div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-sm-4 font-weight-bold">DOB:</div>
                        <div class="col-sm-8">
                            {{ $student->student_dob ? (is_string($student->student_dob) ? $student->student_dob : $student->student_dob->format('M d, Y')) : 'N/A' }}
                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-sm-4 font-weight-bold">Address:</div>
                        <div class="col-sm-8">{{ $student->address }}</div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-sm-4 font-weight-bold">Joining Date:</div>
                        <div class="col-sm-8">
                            {{ $student->joining_date ? (is_string($student->joining_date) ? $student->joining_date : $student->joining_date->format('M d, Y')) : 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Information Card -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Course Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3 font-weight-bold">Course:</div>
                        <div class="col-sm-9">{{ $student->course->course_name }}</div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-sm-3 font-weight-bold">Course Type:</div>
                        <div class="col-sm-9">{{ $student->course->course_type }}</div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-sm-3 font-weight-bold">Theory Status:</div>
                        <div class="col-sm-9">
                            <span class="badge badge-pill 
                                @if($student->theory_status == 'completed') badge-success
                                @elseif($student->theory_status == 'in_progress') badge-info
                                @else badge-warning @endif">
                                {{ ucfirst($student->theory_status) }}
                            </span>
                            
                            @if($student->theory_status == 'completed')
                                <span class="ml-2">
                                    Completed on: {{ $student->theory_completion_date ? $student->theory_completion_date->format('M d, Y') : 'N/A' }}
                                </span>
                            @elseif($student->theory_status == 'pending')
                                <button class="btn btn-sm btn-success ml-2" data-toggle="modal" data-target="#markTheoryCompleteModal">
                                    Mark Theory Complete
                                </button>
                            @endif
                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-sm-3 font-weight-bold">Practical Status:</div>
                        <div class="col-sm-9">
                            <span class="badge badge-pill 
                                @if($student->practical_status == 'completed') badge-success
                                @elseif($student->practical_status == 'assigned') badge-info
                                @elseif($student->practical_status == 'failed') badge-danger
                                @elseif($student->practical_status == 'not_appeared') badge-secondary
                                @else badge-warning @endif">
                                {{ ucfirst($student->practical_status) }}
                            </span>
                            
                            @if($student->practical_status == 'completed')
                                <span class="ml-2">
                                    Completed on: {{ $student->practical_completion_date ? $student->practical_completion_date->format('M d, Y') : 'N/A' }}
                                </span>
                            @elseif($student->practical_status == 'pending' && $student->theory_status == 'completed')
                                <button class="btn btn-sm btn-primary ml-2" data-toggle="modal" data-target="#assignPracticalModal">
                                    Assign to Practical
                                </button>
                            @endif
                        </div>
                    </div>
                    <hr>

                    @if($student->practical_status == 'assigned')
                        <div class="row">
                            <div class="col-sm-3 font-weight-bold">Practical Schedule:</div>
                            <div class="col-sm-9">
                                @if($student->practical_schedule_id && $student->practicalSchedule)
                                    {{ $student->practicalSchedule->date->format('M d, Y') }} | 
                                    {{ $student->practicalSchedule->start_time->format('h:i A') }} - 
                                    {{ $student->practicalSchedule->end_time->format('h:i A') }}
                                    
                                    <button class="btn btn-sm btn-primary ml-2" data-toggle="modal" data-target="#practicalFeedbackModal">
                                        Submit Feedback
                                    </button>
                                @else
                                    No schedule assigned
                                @endif
                            </div>
                        </div>
                        <hr>
                    @endif

                    <div class="row">
                        <div class="col-sm-3 font-weight-bold">Hours Completed:</div>
                        <div class="col-sm-9">
                            <div class="progress mb-2">
                                <div class="progress-bar bg-info" role="progressbar" 
                                    style="width: {{ $student->hours_theory ? ($student->hours_theory / $student->course->theory_hours) * 100 : 0 }}%" 
                                    aria-valuenow="{{ $student->hours_theory ?? 0 }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="{{ $student->course->theory_hours }}">
                                    Theory: {{ $student->hours_theory ?? 0 }}/{{ $student->course->theory_hours }} hrs
                                </div>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" 
                                    style="width: {{ $student->hours_practical ? ($student->hours_practical / $student->course->practical_hours) * 100 : 0 }}%" 
                                    aria-valuenow="{{ $student->hours_practical ?? 0 }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="{{ $student->course->practical_hours }}">
                                    Practical: {{ $student->hours_practical ?? 0 }}/{{ $student->course->practical_hours }} hrs
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feedback History Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Feedback History</h6>
                </div>
                <div class="card-body">
                    @if(isset($feedbacks) && $feedbacks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Class Type</th>
                                        <th>Status</th>
                                        <th>Feedback</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($feedbacks as $feedback)
                                        <tr>
                                            <td>{{ $feedback->created_at->format('M d, Y') }}</td>
                                            <td>{{ $feedback->class_type }}</td>
                                            <td>
                                                <span class="badge badge-pill 
                                                    @if($feedback->status == 'completed') badge-success
                                                    @elseif($feedback->status == 'failed') badge-danger
                                                    @else badge-secondary @endif">
                                                    {{ ucfirst($feedback->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $feedback->feedback }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center">No feedback records found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mark Theory Complete Modal -->
<div class="modal fade" id="markTheoryCompleteModal" tabindex="-1" role="dialog" aria-labelledby="markTheoryCompleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="markTheoryCompleteModalLabel">Mark Theory Complete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('instructor.mark.theory.complete') }}" method="POST">
                @csrf
                <input type="hidden" name="student_ids[]" value="{{ $student->id }}">
                <div class="modal-body">
                    <p>Are you sure you want to mark the theory course as complete for {{ $student->first_name }} {{ $student->last_name }}?</p>
                    
                    <div class="form-group">
                        <label for="theoryHours">Theory Hours Completed:</label>
                        <input type="number" class="form-control" id="theoryHours" name="theory_hours" 
                            value="{{ $student->hours_theory ?? $student->course->theory_hours }}" 
                            min="0" max="{{ $student->course->theory_hours }}" required>
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
<div class="modal fade" id="assignPracticalModal" tabindex="-1" role="dialog" aria-labelledby="assignPracticalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignPracticalModalLabel">Assign Practical</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('instructor.assign.practical') }}" method="POST">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">
                <div class="modal-body">
                    <p>Select a practical slot for {{ $student->first_name }} {{ $student->last_name }}:</p>
                    
                    <div class="form-group">
                        <label for="scheduleSelect">Available Slots:</label>
                        <select class="form-control" id="scheduleSelect" name="schedule_id" required>
                            <option value="">Select a slot...</option>
                            @php
                                $availableSlots = App\Models\CourseSchedule::where('instructor_id', $instructor->id)
                                    ->where('course_id', $student->course_id)
                                    ->where('session_type', 'practical')
                                    ->where('date', '>=', now())
                                    ->where('is_active', true)
                                    ->whereDoesntHave('students')
                                    ->orderBy('date')
                                    ->orderBy('start_time')
                                    ->get();
                            @endphp
                            
                            @foreach($availableSlots as $slot)
                                <option value="{{ $slot->id }}">
                                    {{ $slot->date->format('M d, Y') }} | 
                                    {{ $slot->start_time->format('h:i A') }} - {{ $slot->end_time->format('h:i A') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
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
                <input type="hidden" name="student_id" value="{{ $student->id }}">
                <input type="hidden" name="schedule_id" value="{{ $student->practical_schedule_id }}">
                <div class="modal-body">
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
                        <label for="practicalHours">Practical Hours Completed:</label>
                        <input type="number" class="form-control" id="practicalHours" name="practical_hours" 
                            value="{{ $student->hours_practical ?? 0 }}" 
                            min="0" max="{{ $student->course->practical_hours }}" required>
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
        // Add any specific script functionality here
    });
</script>
@endsection