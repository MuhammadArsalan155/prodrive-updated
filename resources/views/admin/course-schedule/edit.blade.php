{{-- @extends('layouts.master')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h1 class="h2 text-primary fw-bold">
            <i class="fas fa-calendar-edit me-2"></i> Edit Course Schedule
        </h1>
        <div>
            <a href="{{ route('course-schedules.index') }}" class="btn btn-primary btn-sm rounded-pill">
                <i class="fas fa-calendar me-1"></i> Back to Schedule
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12">
            <div class="card border-0 shadow-sm mb-4">
                <!-- Card Header -->
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold">
                        <i class="fas fa-edit me-2"></i> Update Schedule
                    </h5>
                </div>

                <!-- Card Body -->
                <div class="card-body p-4">
                    <form action="{{ route('course-schedules.update', $courseSchedule->id) }}" method="POST" id="scheduleForm">
                        @csrf
                        @method('PUT')
                        <div class="row g-4">
                            <!-- Course Selection -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course_id" class="form-label">
                                        <i class="fas fa-book text-primary me-2"></i>Course
                                    </label>
                                    <select name="course_id" id="course_id"
                                            class="form-control @error('course_id') is-invalid @enderror" required>
                                        <option value="">Select Course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}"
                                                    {{ old('course_id', $courseSchedule->course_id) == $course->id ? 'selected' : '' }}>
                                                {{ $course->course_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Instructor Selection -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="instructor_id" class="form-label">
                                        <i class="fas fa-user-tie text-primary me-2"></i>Instructor
                                    </label>
                                    <select name="instructor_id" id="instructor_id"
                                            class="form-control @error('instructor_id') is-invalid @enderror" required>
                                        <option value="">Select Instructor</option>
                                        @foreach($instructors as $instructor)
                                            <option value="{{ $instructor->id }}"
                                                    {{ old('instructor_id', $courseSchedule->instructor_id) == $instructor->id ? 'selected' : '' }}>
                                                {{ $instructor->instructor_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('instructor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Date Selection -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="date" class="form-label">
                                        <i class="fas fa-calendar text-primary me-2"></i>Date
                                    </label>
                                    <input type="date" name="date" id="date"
                                           class="form-control @error('date') is-invalid @enderror"
                                           value="{{ old('date', $courseSchedule->date->format('Y-m-d')) }}" required>
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Start Time Selection -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="start_time" class="form-label">
                                        <i class="fas fa-clock text-primary me-2"></i>Start Time
                                    </label>
                                    <input type="time" name="start_time" id="start_time"
                                           class="form-control @error('start_time') is-invalid @enderror"
                                           value="{{ old('start_time', $courseSchedule->start_time->format('H:i')) }}" required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- End Time Selection -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="end_time" class="form-label">
                                        <i class="fas fa-stopwatch text-primary me-2"></i>End Time
                                    </label>
                                    <input type="time" name="end_time" id="end_time"
                                           class="form-control @error('end_time') is-invalid @enderror"
                                           value="{{ old('end_time', $courseSchedule->end_time->format('H:i')) }}" required>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Session Type -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="session_type" class="form-label">
                                        <i class="fas fa-chalkboard-teacher text-primary me-2"></i>Session Type
                                    </label>
                                    <select name="session_type" id="session_type"
                                            class="form-control @error('session_type') is-invalid @enderror" required>
                                        <option value="">Select Session Type</option>
                                        <option value="theory" {{ old('session_type', $courseSchedule->session_type) == 'theory' ? 'selected' : '' }}>
                                            Lecture
                                        </option>
                                        <option value="practical" {{ old('session_type', $courseSchedule->session_type) == 'practical' ? 'selected' : '' }}>
                                            Lab
                                        </option>
                                        <option value="hybird" {{ old('session_type', $courseSchedule->session_type) == 'hybird' ? 'selected' : '' }}>
                                            Tutorial
                                        </option>
                                    </select>
                                    @error('session_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Max Students -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_students" class="form-label">
                                        <i class="fas fa-users text-primary me-2"></i>Maximum Students
                                    </label>
                                    <input type="number" name="max_students" id="max_students"
                                           class="form-control @error('max_students') is-invalid @enderror"
                                           value="{{ old('max_students', $courseSchedule->max_students) }}" min="1" required>
                                    @error('max_students')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status Switch -->
                            <div class="col-12 mb-3">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"
                                           id="is_active" name="is_active" value="1"
                                           {{ old('is_active', $courseSchedule->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Active Status</label>
                                </div>
                            </div>

                            <!-- Schedule Info Card -->
                            <div class="col-12 mb-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title font-weight-bold">
                                            <i class="fas fa-info-circle text-primary me-2"></i>Current Schedule Information
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1">
                                                    <strong><i class="fas fa-calendar-plus text-primary me-2"></i>Created:</strong>
                                                    {{ $courseSchedule->created_at->format('M d, Y H:i A') }}
                                                </p>
                                                <p class="mb-1">
                                                    <strong><i class="fas fa-calendar-check text-primary me-2"></i>Last Updated:</strong>
                                                    {{ $courseSchedule->updated_at->format('M d, Y H:i A') }}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1">
                                                    <strong><i class="fas fa-toggle-on text-primary me-2"></i>Current Status:</strong>
                                                    <span class="badge badge-{{ $courseSchedule->is_active ? 'success' : 'danger' }}">
                                                        {{ $courseSchedule->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-12 d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Schedule
                                </button>
                                <a href="{{ route('course-schedules.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize select2 for better dropdown experience
    $('#course_id, #instructor_id, #session_type').select2({
        theme: 'bootstrap4'
    });

    // Time validation
    $('#end_time').on('change', function() {
        const startTime = $('#start_time').val();
        const endTime = $(this).val();

        if (startTime && endTime && startTime >= endTime) {
            alert('End time must be after start time');
            $(this).val('');
        }
    });

    // Date validation
    $('#date').on('change', function() {
        const selectedDate = new Date($(this).val());
        const originalDate = new Date('{{ $courseSchedule->date->format('Y-m-d') }}');
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // Allow the original date or future dates
        if (selectedDate < today && selectedDate.getTime() !== originalDate.getTime()) {
            alert('Please select today or a future date');
            $(this).val('{{ $courseSchedule->date->format('Y-m-d') }}');
        }
    });

    // Form validation
    $('#scheduleForm').on('submit', function(e) {
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();

        if (startTime >= endTime) {
            e.preventDefault();
            alert('End time must be after start time');
            return false;
        }
    });

    // Confirm before making major changes
    $('form').on('submit', function(e) {
        const isActiveChanged = $('#is_active').prop('checked') !== {{ $courseSchedule->is_active ? 'true' : 'false' }};
        const dateChanged = $('#date').val() !== '{{ $courseSchedule->date->format('Y-m-d') }}';

        if (isActiveChanged || dateChanged) {
            if (!confirm('Are you sure you want to update this schedule? This may affect existing enrollments.')) {
                e.preventDefault();
                return false;
            }
        }
    });
});
</script>
@endsection --}}


@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <h1 class="h2 text-primary fw-bold">
                <i class="fas fa-calendar-edit me-2"></i> Edit Course Schedule
            </h1>
            <small class="text-muted">
                <i class="fas fa-clock me-1"></i> Oklahoma Time (Central Time Zone)
            </small>
        </div>
        <div>
            <a href="{{ route('course-schedules.index') }}" class="btn btn-primary btn-sm rounded-pill">
                <i class="fas fa-calendar me-1"></i> Back to Schedule
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12">
            <div class="card border-0 shadow-sm mb-4">
                <!-- Card Header -->
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold">
                        <i class="fas fa-edit me-2"></i> Update Schedule
                    </h5>
                </div>

                <!-- Card Body -->
                <div class="card-body p-4">
                    <!-- Oklahoma Time Info -->
                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>
                            <strong>Oklahoma Time Zone Information:</strong>
                            <br>Current Oklahoma Time: <span id="currentOklahomaTime">{{ now()->setTimezone('America/Chicago')->format('M j, Y g:i A T') }}</span>
                            <br><small class="text-muted">All schedule updates are processed in Oklahoma Time (Central Time Zone)</small>
                        </div>
                    </div>

                    <form action="{{ route('course-schedules.update', $courseSchedule->id) }}" method="POST" id="scheduleForm">
                        @csrf
                        @method('PUT')
                        <div class="row g-4">
                            <!-- Course Selection -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course_id" class="form-label">
                                        <i class="fas fa-book text-primary me-2"></i>Course
                                    </label>
                                    <select name="course_id" id="course_id"
                                            class="form-control @error('course_id') is-invalid @enderror" required>
                                        <option value="">Select Course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}"
                                                    {{ old('course_id', $courseSchedule->course_id) == $course->id ? 'selected' : '' }}>
                                                {{ $course->course_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Instructor Selection -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="instructor_id" class="form-label">
                                        <i class="fas fa-user-tie text-primary me-2"></i>Instructor
                                    </label>
                                    <select name="instructor_id" id="instructor_id"
                                            class="form-control @error('instructor_id') is-invalid @enderror" required>
                                        <option value="">Select Instructor</option>
                                        @foreach($instructors as $instructor)
                                            <option value="{{ $instructor->id }}"
                                                    {{ old('instructor_id', $courseSchedule->instructor_id) == $instructor->id ? 'selected' : '' }}>
                                                {{ $instructor->instructor_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('instructor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Date Selection -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="date" class="form-label">
                                        <i class="fas fa-calendar text-primary me-2"></i>Date
                                        <small class="text-muted">(Oklahoma Time)</small>
                                    </label>
                                    <input type="date" name="date" id="date"
                                           class="form-control @error('date') is-invalid @enderror"
                                           value="{{ old('date', $courseSchedule->date->format('Y-m-d')) }}"
                                           min="{{ now()->setTimezone('America/Chicago')->format('Y-m-d') }}"
                                           required>
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Original date: {{ \Carbon\Carbon::parse($courseSchedule->date)->setTimezone('America/Chicago')->format('M j, Y') }}
                                    </small>
                                </div>
                            </div>

                            <!-- Start Time Selection -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="start_time" class="form-label">
                                        <i class="fas fa-clock text-primary me-2"></i>Start Time
                                        <small class="text-muted">(Oklahoma Time)</small>
                                    </label>
                                    <input type="time" name="start_time" id="start_time"
                                           class="form-control @error('start_time') is-invalid @enderror"
                                           value="{{ old('start_time', $courseSchedule->start_time->format('H:i')) }}" required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- End Time Selection -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="end_time" class="form-label">
                                        <i class="fas fa-stopwatch text-primary me-2"></i>End Time
                                        <small class="text-muted">(Oklahoma Time)</small>
                                    </label>
                                    <input type="time" name="end_time" id="end_time"
                                           class="form-control @error('end_time') is-invalid @enderror"
                                           value="{{ old('end_time', $courseSchedule->end_time->format('H:i')) }}" required>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Session Type -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="session_type" class="form-label">
                                        <i class="fas fa-chalkboard-teacher text-primary me-2"></i>Session Type
                                    </label>
                                    <select name="session_type" id="session_type"
                                            class="form-control @error('session_type') is-invalid @enderror" required>
                                        <option value="">Select Session Type</option>
                                        <option value="theory" {{ old('session_type', $courseSchedule->session_type) == 'theory' ? 'selected' : '' }}>
                                            Theory
                                        </option>
                                        <option value="practical" {{ old('session_type', $courseSchedule->session_type) == 'practical' ? 'selected' : '' }}>
                                            Practical
                                        </option>
                                        <option value="hybrid" {{ old('session_type', $courseSchedule->session_type) == 'hybrid' ? 'selected' : '' }}>
                                            Hybrid
                                        </option>
                                    </select>
                                    @error('session_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Max Students -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_students" class="form-label">
                                        <i class="fas fa-users text-primary me-2"></i>Maximum Students
                                    </label>
                                    <input type="number" name="max_students" id="max_students"
                                           class="form-control @error('max_students') is-invalid @enderror"
                                           value="{{ old('max_students', $courseSchedule->max_students) }}" min="1" required>
                                    @error('max_students')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status Switch -->
                            <div class="col-12 mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input"
                                           id="is_active" name="is_active" value="1"
                                           {{ old('is_active', $courseSchedule->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active Status</label>
                                </div>
                            </div>

                            <!-- Schedule Info Card -->
                            <div class="col-12 mb-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title font-weight-bold">
                                            <i class="fas fa-info-circle text-primary me-2"></i>Current Schedule Information
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1">
                                                    <strong><i class="fas fa-calendar-plus text-primary me-2"></i>Created (Oklahoma Time):</strong>
                                                    {{ \Carbon\Carbon::parse($courseSchedule->created_at)->setTimezone('America/Chicago')->format('M d, Y H:i A T') }}
                                                </p>
                                                <p class="mb-1">
                                                    <strong><i class="fas fa-calendar-check text-primary me-2"></i>Last Updated (Oklahoma Time):</strong>
                                                    {{ \Carbon\Carbon::parse($courseSchedule->updated_at)->setTimezone('America/Chicago')->format('M d, Y H:i A T') }}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1">
                                                    <strong><i class="fas fa-toggle-on text-primary me-2"></i>Current Status:</strong>
                                                    <span class="badge badge-{{ $courseSchedule->is_active ? 'success' : 'danger' }}">
                                                        {{ $courseSchedule->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </p>
                                                <p class="mb-1">
                                                    <strong><i class="fas fa-clock text-primary me-2"></i>Schedule Time:</strong>
                                                    {{ $courseSchedule->start_time->format('g:i A') }} - {{ $courseSchedule->end_time->format('g:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-12 d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Schedule (Oklahoma Time)
                                </button>
                                <a href="{{ route('course-schedules.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Update current Oklahoma time every minute
    function updateOklahomaTime() {
        const now = new Date();
        const options = {
            timeZone: 'America/Chicago',
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            timeZoneName: 'short'
        };
        const formatter = new Intl.DateTimeFormat('en-US', options);
        document.getElementById('currentOklahomaTime').textContent = formatter.format(now);
    }

    // Update time immediately and then every minute
    updateOklahomaTime();
    setInterval(updateOklahomaTime, 60000);

    // Get current Oklahoma date for date validation
    function getCurrentOklahomaDate() {
        const now = new Date();
        const oklahomaDate = new Intl.DateTimeFormat('en-CA', {
            timeZone: 'America/Chicago'
        }).format(now);
        return oklahomaDate;
    }

    // Initialize select2 for better dropdown experience
    $('#course_id, #instructor_id, #session_type').select2({
        theme: 'bootstrap4'
    });

    // Time validation
    $('#end_time').on('change', function() {
        const startTime = $('#start_time').val();
        const endTime = $(this).val();

        if (startTime && endTime && startTime >= endTime) {
            alert('End time must be after start time (Oklahoma Time)');
            $(this).val('{{ $courseSchedule->end_time->format("H:i") }}');
            $(this).focus();
        }
    });

    $('#start_time').on('change', function() {
        const startTime = $(this).val();
        const endTime = $('#end_time').val();

        if (startTime && endTime && startTime >= endTime) {
            alert('Start time must be before end time (Oklahoma Time)');
            $(this).focus();
        }
    });

    // Enhanced date validation
    $('#date').on('change', function() {
        const selectedDate = $(this).val();
        const originalDate = '{{ $courseSchedule->date->format('Y-m-d') }}';
        const currentOklahomaDate = getCurrentOklahomaDate();

        // Allow the original date or future dates
        if (selectedDate < currentOklahomaDate && selectedDate !== originalDate) {
            alert('Please select a date that is today or in the future (Oklahoma Time). You can keep the original date or select a future date.');
            $(this).val(originalDate);
            $(this).focus();
        }
    });

    // Enhanced form validation
    $('#scheduleForm').on('submit', function(e) {
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();
        const selectedDate = $('#date').val();
        const originalDate = '{{ $courseSchedule->date->format('Y-m-d') }}';
        const currentOklahomaDate = getCurrentOklahomaDate();

        // Validate time
        if (startTime >= endTime) {
            e.preventDefault();
            alert('End time must be after start time (Oklahoma Time)');
            $('#end_time').focus();
            return false;
        }

        // Validate date
        if (selectedDate < currentOklahomaDate && selectedDate !== originalDate) {
            e.preventDefault();
            alert('Date cannot be changed to a past date (Oklahoma Time). You can keep the original date or select a future date.');
            $('#date').focus();
            return false;
        }

        // Show loading state
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Updating Schedule...');
    });

    // Confirm before making major changes
    $('form').on('submit', function(e) {
        const isActiveChanged = $('#is_active').prop('checked') !== {{ $courseSchedule->is_active ? 'true' : 'false' }};
        const dateChanged = $('#date').val() !== '{{ $courseSchedule->date->format('Y-m-d') }}';
        const instructorChanged = $('#instructor_id').val() !== '{{ $courseSchedule->instructor_id }}';
        const timeChanged = $('#start_time').val() !== '{{ $courseSchedule->start_time->format("H:i") }}' ||
                           $('#end_time').val() !== '{{ $courseSchedule->end_time->format("H:i") }}';

        if (isActiveChanged || dateChanged || instructorChanged || timeChanged) {
            if (!confirm('Are you sure you want to update this schedule? This may affect existing enrollments and student notifications. All changes will be processed in Oklahoma Time.')) {
                e.preventDefault();
                // Re-enable the submit button
                $(this).find('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Update Schedule (Oklahoma Time)');
                return false;
            }
        }
    });

    // Update min date attribute to current Oklahoma date
    const currentOklahomaDate = getCurrentOklahomaDate();
    const originalDate = '{{ $courseSchedule->date->format('Y-m-d') }}';

    // Allow original date even if it's in the past, or current/future dates
    if (originalDate >= currentOklahomaDate) {
        $('#date').attr('min', currentOklahomaDate);
    } else {
        // If original date is in the past, allow it but set min to today for new selections
        $('#date').attr('min', currentOklahomaDate);
        // Add a note about past dates
        $('#date').after('<small class="form-text text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Original date is in the past. You can keep it or select a current/future date.</small>');
    }
});
</script>
@endsection
