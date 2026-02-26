@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $statusTitle }} Students</h1>
        <div>
            <a href="{{ route('instructor.dashboard') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Students List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Students List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="studentsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Contact</th>
                            <th>Email</th>
                            @if(strpos($status, 'practical') !== false)
                                <th>Theory Completed</th>
                            @else
                                <th>Joining Date</th>
                            @endif
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>{{ $student->course->course_name }}</td>
                                <td>{{ $student->student_contact }}</td>
                                <td>{{ $student->email }}</td>
                                @if(strpos($status, 'practical') !== false)
                                    <td>{{ $student->theory_completion_date ? $student->theory_completion_date->format('M d, Y') : 'N/A' }}</td>
                                @else
                                <td>{{ $student->joining_date instanceof \Carbon\Carbon ? $student->joining_date->format('M d, Y') : $student->joining_date }}</td>
                                    {{-- <td>{{ $student->joining_date ? $student->joining_date->format('M d, Y') : 'N/A' }}</td> --}}
                                @endif
                                <td>
                                    <a href="{{ route('instructor.student.view', $student->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    
                                    @if($status == 'practical-pending')
                                        <button class="btn btn-sm btn-primary assignStudentBtn" 
                                            data-toggle="modal" 
                                            data-target="#assignPracticalModal"
                                            data-student-id="{{ $student->id }}" 
                                            data-student-name="{{ $student->first_name }} {{ $student->last_name }}">
                                            Assign to Practical
                                        </button>
                                    @endif
                                    
                                    @if($status == 'theory-pending')
                                        <button class="btn btn-sm btn-success markCompleteBtn" 
                                            data-toggle="modal" 
                                            data-target="#markTheoryCompleteModal"
                                            data-student-id="{{ $student->id }}" 
                                            data-student-name="{{ $student->first_name }} {{ $student->last_name }}">
                                            Mark Theory Complete
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Assign Practical Modal -->
@if($status == 'practical-pending')
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
                <input type="hidden" name="student_id" id="assignStudentId">
                <div class="modal-body">
                    <h5 id="assignStudentName"></h5>
                    
                    <div class="form-group">
                        <label for="scheduleSelect">Select Available Practical Slot:</label>
                        <select class="form-control" id="scheduleSelect" name="schedule_id" required>
                            <option value="">Select a slot...</option>
                            @foreach(App\Models\CourseSchedule::where('instructor_id', Auth::guard('instructor')->id())
                                ->where('session_type', 'practical')
                                ->where('date', '>=', now())
                                ->where('is_active', true)
                                ->whereDoesntHave('students')
                                ->with('course')
                                ->orderBy('date')
                                ->orderBy('start_time')
                                ->get() as $schedule)
                                <option value="{{ $schedule->id }}">
                                    {{ $schedule->date->format('M d, Y') }} - 
                                    {{ $schedule->start_time->format('h:i A') }} to {{ $schedule->end_time->format('h:i A') }} - 
                                    {{ $schedule->course->course_name }}
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
@endif

<!-- Mark Theory Complete Modal -->
@if($status == 'theory-pending')
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
                <input type="hidden" name="student_ids[]" id="theoryStudentId">
                <div class="modal-body">
                    <h5 id="theoryStudentName"></h5>
                    <p>Are you sure you want to mark this student's theory classes as complete?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Mark Complete</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#studentsTable').DataTable({
            "pageLength": 25,
            "order": [[ 0, "asc" ]],
            "stateSave": true
        });
        
        // Assign Student to Practical
        $('#assignPracticalModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var studentId = button.data('student-id');
            var studentName = button.data('student-name');
            var modal = $(this);
            
            modal.find('#assignStudentId').val(studentId);
            modal.find('#assignStudentName').text(studentName);
        });
        
        // Mark Theory Complete
        $('#markTheoryCompleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var studentId = button.data('student-id');
            var studentName = button.data('student-name');
            var modal = $(this);
            
            modal.find('#theoryStudentId').val(studentId);
            modal.find('#theoryStudentName').text(studentName);
        });
    });
</script>
@endsection