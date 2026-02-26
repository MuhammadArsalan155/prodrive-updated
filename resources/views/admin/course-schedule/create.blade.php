{{-- @extends('layouts.master')

@section('content')
    <div class="container-fluid px-4">
        <!-- Page Heading -->
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h1 class="h2 text-primary fw-bold">
                <i class="fas fa-calendar-alt me-2"></i> Course Schedule
            </h1>
            <div>
                <button class="btn btn-info btn-sm me-2 rounded-pill" data-toggle="modal" data-target="#copyScheduleModal">
                    <i class="fas fa-copy me-1"></i> Copy Month Schedule
                </button>
                <a href="{{ route('course-schedules.index') }}" class="btn btn-primary btn-sm rounded-pill">
                    <i class="fas fa-calendar me-1"></i> View Schedule
                </a>
            </div>
        </div>

        <!-- Content Row -->
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12">
                <div class="card border-0 shadow-sm mb-4">
                    <!-- Card Header -->
                    <div
                        class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold">
                            <i class="fas fa-plus-circle me-2"></i> Create Multiple Schedules
                        </h5>
                        <div>
                            <button type="button" class="btn btn-light btn-sm" id="addScheduleBtn">
                                <i class="fas fa-plus me-1"></i> Add Schedule
                            </button>
                            <span class="badge bg-light text-dark ms-2">
                                Total: <span id="scheduleCount">1</span>
                            </span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form action="{{ route('course-schedules.store-multiple') }}" method="POST"
                            id="multipleScheduleForm">
                            @csrf

                            <!-- Schedules Container -->
                            <div id="schedulesContainer">
                                <!-- Initial Schedule Form -->
                                <div class="schedule-form-group border rounded p-4 mb-4" data-index="0">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 text-primary">
                                            <i class="fas fa-calendar-plus me-2"></i>Schedule #<span
                                                class="schedule-number">1</span>
                                        </h6>
                                        <button type="button" class="btn btn-danger btn-sm remove-schedule-btn"
                                            style="display: none;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <div class="row g-3">
                                        <!-- Course Selection -->
                                        <div class="col-md-6">
                                            <label for="schedules_0_course_id" class="form-label">Course</label>
                                            <select name="schedules[0][course_id]" id="schedules_0_course_id"
                                                class="form-control course-select" required>
                                                <option value="">Select Course</option>
                                                @foreach ($courses as $course)
                                                    <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Instructor Selection -->
                                        <div class="col-md-6">
                                            <label for="schedules_0_instructor_id" class="form-label">Instructor</label>
                                            <select name="schedules[0][instructor_id]" id="schedules_0_instructor_id"
                                                class="form-control instructor-select" required>
                                                <option value="">Select Instructor</option>
                                                @foreach ($instructors as $instructor)
                                                    <option value="{{ $instructor->id }}">{{ $instructor->instructor_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Date Selection -->
                                        <div class="col-md-4">
                                            <label for="schedules_0_date" class="form-label">Date</label>
                                            <input type="date" name="schedules[0][date]" id="schedules_0_date"
                                                class="form-control date-input" required>
                                        </div>

                                        <!-- Start Time Selection -->
                                        <div class="col-md-4">
                                            <label for="schedules_0_start_time" class="form-label">Start Time</label>
                                            <input type="time" name="schedules[0][start_time]"
                                                id="schedules_0_start_time" class="form-control start-time-input" required>
                                        </div>

                                        <!-- End Time Selection -->
                                        <div class="col-md-4">
                                            <label for="schedules_0_end_time" class="form-label">End Time</label>
                                            <input type="time" name="schedules[0][end_time]" id="schedules_0_end_time"
                                                class="form-control end-time-input" required>
                                        </div>

                                        <!-- Session Type -->
                                        <div class="col-md-6">
                                            <label for="schedules_0_session_type" class="form-label">Session Type</label>
                                            <select name="schedules[0][session_type]" id="schedules_0_session_type"
                                                class="form-control session-type-select" required>
                                                <option value="">Select Session Type</option>
                                                <option value="theory">Theory</option>
                                                <option value="practical">Practical</option>
                                                <option value="hybird">Hybird</option>
                                            </select>
                                        </div>

                                        <!-- Max Students -->
                                        <div class="col-md-6">
                                            <label for="schedules_0_max_students" class="form-label">Maximum
                                                Students</label>
                                            <input type="number" name="schedules[0][max_students]"
                                                id="schedules_0_max_students" class="form-control max-students-input"
                                                min="1" required>
                                        </div>

                                        <!-- Status Switch -->
                                        <div class="col-12">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input is-active-input"
                                                    id="schedules_0_is_active" name="schedules[0][is_active]"
                                                    value="1" checked>
                                                <label class="form-check-label" for="schedules_0_is_active">Active
                                                    Status</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bulk Actions -->
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-tools text-primary me-2"></i>Bulk Actions
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-outline-primary btn-sm w-100"
                                                id="copyFirstToAll">
                                                <i class="fas fa-copy me-1"></i>Copy First to All
                                            </button>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-outline-success btn-sm w-100"
                                                id="setAllActive">
                                                <i class="fas fa-check me-1"></i>Set All Active
                                            </button>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-outline-secondary btn-sm w-100"
                                                id="clearAll">
                                                <i class="fas fa-eraser me-1"></i>Clear All
                                            </button>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-outline-danger btn-sm w-100"
                                                id="removeAll">
                                                <i class="fas fa-trash me-1"></i>Remove All
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Create All Schedules (<span id="submitCount">1</span>)
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Copy Schedule Modal -->
    <div class="modal fade" id="copyScheduleModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Copy Month Schedule</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('course-schedules.copy-month') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="month">Select Month to Copy</label>
                            <input type="month" class="form-control" id="month" name="month" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Copy Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let scheduleIndex = 0;

            // Template for new schedule form
            function getScheduleTemplate(index) {
                return `
            <div class="schedule-form-group border rounded p-4 mb-4" data-index="${index}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 text-primary">
                        <i class="fas fa-calendar-plus me-2"></i>Schedule #<span class="schedule-number">${index + 1}</span>
                    </h6>
                    <button type="button" class="btn btn-danger btn-sm remove-schedule-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="schedules_${index}_course_id" class="form-label">Course</label>
                        <select name="schedules[${index}][course_id]" id="schedules_${index}_course_id"
                                class="form-control course-select" required>
                            <option value="">Select Course</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="schedules_${index}_instructor_id" class="form-label">Instructor</label>
                        <select name="schedules[${index}][instructor_id]" id="schedules_${index}_instructor_id"
                                class="form-control instructor-select" required>
                            <option value="">Select Instructor</option>
                            @foreach ($instructors as $instructor)
                                <option value="{{ $instructor->id }}">{{ $instructor->instructor_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="schedules_${index}_date" class="form-label">Date</label>
                        <input type="date" name="schedules[${index}][date]" id="schedules_${index}_date"
                               class="form-control date-input" required>
                    </div>

                    <div class="col-md-4">
                        <label for="schedules_${index}_start_time" class="form-label">Start Time</label>
                        <input type="time" name="schedules[${index}][start_time]" id="schedules_${index}_start_time"
                               class="form-control start-time-input" required>
                    </div>

                    <div class="col-md-4">
                        <label for="schedules_${index}_end_time" class="form-label">End Time</label>
                        <input type="time" name="schedules[${index}][end_time]" id="schedules_${index}_end_time"
                               class="form-control end-time-input" required>
                    </div>

                    <div class="col-md-6">
                        <label for="schedules_${index}_session_type" class="form-label">Session Type</label>
                        <select name="schedules[${index}][session_type]" id="schedules_${index}_session_type"
                                class="form-control session-type-select" required>
                            <option value="">Select Session Type</option>
                            <option value="theory">Theory</option>
                            <option value="practical">Practical</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="schedules_${index}_max_students" class="form-label">Maximum Students</label>
                        <input type="number" name="schedules[${index}][max_students]" id="schedules_${index}_max_students"
                               class="form-control max-students-input" min="1" required>
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input is-active-input"
                                   id="schedules_${index}_is_active" name="schedules[${index}][is_active]" value="1" checked>
                            <label class="form-check-label" for="schedules_${index}_is_active">Active Status</label>
                        </div>
                    </div>
                </div>
            </div>
        `;
            }

            // Add new schedule
            $('#addScheduleBtn').click(function() {
                scheduleIndex++;
                const newSchedule = getScheduleTemplate(scheduleIndex);
                $('#schedulesContainer').append(newSchedule);
                updateScheduleNumbers();
                updateCounters();

                // Show remove buttons if more than one schedule
                if ($('.schedule-form-group').length > 1) {
                    $('.remove-schedule-btn').show();
                }
            });

            // Remove schedule
            $(document).on('click', '.remove-schedule-btn', function() {
                $(this).closest('.schedule-form-group').remove();
                updateScheduleNumbers();
                updateCounters();

                // Hide remove buttons if only one schedule remains
                if ($('.schedule-form-group').length <= 1) {
                    $('.remove-schedule-btn').hide();
                }
            });

            // Update schedule numbers
            function updateScheduleNumbers() {
                $('.schedule-form-group').each(function(index) {
                    $(this).find('.schedule-number').text(index + 1);
                });
            }

            // Update counters
            function updateCounters() {
                const count = $('.schedule-form-group').length;
                $('#scheduleCount').text(count);
                $('#submitCount').text(count);
            }

            // Copy first schedule to all others
            $('#copyFirstToAll').click(function() {
                const firstForm = $('.schedule-form-group').first();
                const firstValues = {
                    course_id: firstForm.find('.course-select').val(),
                    instructor_id: firstForm.find('.instructor-select').val(),
                    date: firstForm.find('.date-input').val(),
                    start_time: firstForm.find('.start-time-input').val(),
                    end_time: firstForm.find('.end-time-input').val(),
                    session_type: firstForm.find('.session-type-select').val(),
                    max_students: firstForm.find('.max-students-input').val(),
                    is_active: firstForm.find('.is-active-input').is(':checked')
                };

                $('.schedule-form-group').not(':first').each(function() {
                    $(this).find('.course-select').val(firstValues.course_id);
                    $(this).find('.instructor-select').val(firstValues.instructor_id);
                    $(this).find('.date-input').val(firstValues.date);
                    $(this).find('.start-time-input').val(firstValues.start_time);
                    $(this).find('.end-time-input').val(firstValues.end_time);
                    $(this).find('.session-type-select').val(firstValues.session_type);
                    $(this).find('.max-students-input').val(firstValues.max_students);
                    $(this).find('.is-active-input').prop('checked', firstValues.is_active);
                });
            });

            // Set all active
            $('#setAllActive').click(function() {
                $('.is-active-input').prop('checked', true);
            });

            // Clear all forms
            $('#clearAll').click(function() {
                if (confirm('Are you sure you want to clear all form data?')) {
                    $('.schedule-form-group').find('input, select').val('');
                    $('.is-active-input').prop('checked', true);
                }
            });

            // Remove all except first
            $('#removeAll').click(function() {
                if (confirm('Are you sure you want to remove all schedules except the first one?')) {
                    $('.schedule-form-group').not(':first').remove();
                    $('.remove-schedule-btn').hide();
                    updateScheduleNumbers();
                    updateCounters();
                }
            });

            // Time validation
            $(document).on('change', '.end-time-input', function() {
                const container = $(this).closest('.schedule-form-group');
                const startTime = container.find('.start-time-input').val();
                const endTime = $(this).val();

                if (startTime && endTime && startTime >= endTime) {
                    alert('End time must be after start time');
                    $(this).val('');
                }
            });

            // Date validation (prevent past dates)
            $(document).on('change', '.date-input', function() {
                const selectedDate = new Date($(this).val());
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (selectedDate < today) {
                    alert('Please select a future date');
                    $(this).val('');
                }
            });

            // Form validation
            $('#multipleScheduleForm').on('submit', function(e) {
                let hasError = false;

                $('.schedule-form-group').each(function() {
                    const startTime = $(this).find('.start-time-input').val();
                    const endTime = $(this).find('.end-time-input').val();

                    if (startTime >= endTime) {
                        hasError = true;
                        alert('End time must be after start time in all schedules');
                        return false;
                    }
                });

                if (hasError) {
                    e.preventDefault();
                    return false;
                }
            });

            // Initialize
            updateCounters();
        });
    </script>
@endsection --}}
@extends('layouts.master')

@section('content')
    <style>
        .date-input[readonly] {
            background-color: #ffffff;
            cursor: pointer;
        }

        .date-input[readonly]:focus {
            background-color: #ffffff;
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
    <div class="container-fluid px-4">
        <!-- Page Heading -->
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div>
                <h1 class="h2 text-primary fw-bold">
                    <i class="fas fa-calendar-alt me-2"></i> Course Schedule
                </h1>
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i> Oklahoma Time (Central Time Zone)
                </small>
            </div>
            <div>
                <button class="btn btn-info btn-sm me-2 rounded-pill" data-toggle="modal" data-target="#copyScheduleModal">
                    <i class="fas fa-copy me-1"></i> Copy Month Schedule
                </button>
                <a href="{{ route('course-schedules.index') }}" class="btn btn-primary btn-sm rounded-pill">
                    <i class="fas fa-calendar me-1"></i> View Schedule
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Error:</strong>
                @if ($errors->has('conflicts'))
                    <ul class="mb-0">
                        @foreach ($errors->get('conflicts') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @else
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                @endif
                <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Content Row -->
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12">
                <div class="card border-0 shadow-sm mb-4">
                    <!-- Card Header -->
                    <div
                        class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold">
                            <i class="fas fa-plus-circle me-2"></i> Create Multiple Schedules
                        </h5>
                        <div>
                            <button type="button" class="btn btn-light btn-sm" id="addScheduleBtn">
                                <i class="fas fa-plus me-1"></i> Add Schedule
                            </button>
                            <span class="badge bg-light text-dark ms-2">
                                Total: <span id="scheduleCount">1</span>
                            </span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <!-- Oklahoma Time Info -->
                        <div class="alert alert-info d-flex align-items-center mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>
                                <strong>Oklahoma Time Zone Information:</strong>
                                <br>Current Oklahoma Time: <span
                                    id="currentOklahomaTime">{{ now()->setTimezone('America/Chicago')->format('M j, Y g:i A T') }}</span>
                                <br><small class="text-muted">All schedules are created and stored in Oklahoma Time (Central
                                    Time Zone)</small>
                            </div>
                        </div>

                        <form action="{{ route('course-schedules.store-multiple') }}" method="POST"
                            id="multipleScheduleForm">
                            @csrf

                            <!-- Schedules Container -->
                            <div id="schedulesContainer">
                                <!-- Initial Schedule Form -->
                                <div class="schedule-form-group border rounded p-4 mb-4" data-index="0">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 text-primary">
                                            <i class="fas fa-calendar-plus me-2"></i>Schedule #<span
                                                class="schedule-number">1</span>
                                        </h6>
                                        <button type="button" class="btn btn-danger btn-sm remove-schedule-btn"
                                            style="display: none;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <div class="row g-3">
                                        <!-- Course Selection -->
                                        <div class="col-md-6">
                                            <label for="schedules_0_course_id" class="form-label">Course</label>
                                            <select name="schedules[0][course_id]" id="schedules_0_course_id"
                                                class="form-control course-select" required>
                                                <option value="">Select Course</option>
                                                @foreach ($courses as $course)
                                                    <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Instructor Selection -->
                                        <div class="col-md-6">
                                            <label for="schedules_0_instructor_id" class="form-label">Instructor</label>
                                            <select name="schedules[0][instructor_id]" id="schedules_0_instructor_id"
                                                class="form-control instructor-select" required>
                                                <option value="">Select Instructor</option>
                                                @foreach ($instructors as $instructor)
                                                    <option value="{{ $instructor->id }}">{{ $instructor->instructor_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Date Selection -->

                                        <div class="col-md-4">
                                            <label for="schedules_0_date" class="form-label">
                                                Date <small class="text-muted">(Oklahoma Time)</small>
                                            </label>
                                            <input type="date" name="schedules[0][date]" id="schedules_0_date"
                                                class="form-control date-input"
                                                min="{{ now()->setTimezone('America/Chicago')->format('Y-m-d') }}"
                                                onkeydown="return false" onpaste="return false" ondrop="return false"
                                                autocomplete="off" required>
                                        </div>

                                        <!-- Start Time Selection -->
                                        <div class="col-md-4">
                                            <label for="schedules_0_start_time" class="form-label">
                                                Start Time <small class="text-muted">(Oklahoma Time)</small>
                                            </label>
                                            <input type="time" name="schedules[0][start_time]"
                                                id="schedules_0_start_time" class="form-control start-time-input" required>
                                        </div>

                                        <!-- End Time Selection -->
                                        <div class="col-md-4">
                                            <label for="schedules_0_end_time" class="form-label">
                                                End Time <small class="text-muted">(Oklahoma Time)</small>
                                            </label>
                                            <input type="time" name="schedules[0][end_time]" id="schedules_0_end_time"
                                                class="form-control end-time-input" required>
                                        </div>

                                        <!-- Session Type -->
                                        <div class="col-md-6">
                                            <label for="schedules_0_session_type" class="form-label">Session Type</label>
                                            <select name="schedules[0][session_type]" id="schedules_0_session_type"
                                                class="form-control session-type-select" required>
                                                <option value="">Select Session Type</option>
                                                <option value="theory">Theory</option>
                                                <option value="practical">Practical</option>
                                                <option value="hybrid">Hybrid</option>
                                            </select>
                                        </div>

                                        <!-- Max Students -->
                                        <div class="col-md-6">
                                            <label for="schedules_0_max_students" class="form-label">Maximum
                                                Students</label>
                                            <input type="number" name="schedules[0][max_students]"
                                                id="schedules_0_max_students" class="form-control max-students-input"
                                                min="1" required>
                                        </div>

                                        <!-- Status Switch -->
                                        <div class="col-12">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input is-active-input"
                                                    id="schedules_0_is_active" name="schedules[0][is_active]"
                                                    value="1" checked>
                                                <label class="form-check-label" for="schedules_0_is_active">Active
                                                    Status</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bulk Actions -->
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-tools text-primary me-2"></i>Bulk Actions
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-outline-primary btn-sm w-100"
                                                id="copyFirstToAll">
                                                <i class="fas fa-copy me-1"></i>Copy First to All
                                            </button>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-outline-success btn-sm w-100"
                                                id="setAllActive">
                                                <i class="fas fa-check me-1"></i>Set All Active
                                            </button>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-outline-secondary btn-sm w-100"
                                                id="clearAll">
                                                <i class="fas fa-eraser me-1"></i>Clear All
                                            </button>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-outline-danger btn-sm w-100"
                                                id="removeAll">
                                                <i class="fas fa-trash me-1"></i>Remove All
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Create All Schedules (<span id="submitCount">1</span>)
                                    - Oklahoma Time
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Copy Schedule Modal -->
    <div class="modal fade" id="copyScheduleModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Copy Month Schedule</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('course-schedules.copy-month') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="month">Select Month to Copy (Oklahoma Time)</label>
                            <input type="month" class="form-control" id="month" name="month"
                                value="{{ now()->setTimezone('America/Chicago')->format('Y-m') }}"
                                max="{{ now()->setTimezone('America/Chicago')->format('Y-m') }}" required>
                            <small class="form-text text-muted">
                                Schedule will be copied to the next month in Oklahoma Time
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Copy Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let scheduleIndex = 0;

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

            // Template for new schedule form
            function getScheduleTemplate(index) {
                const minDate = getCurrentOklahomaDate();
                return `
            <div class="schedule-form-group border rounded p-4 mb-4" data-index="${index}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 text-primary">
                        <i class="fas fa-calendar-plus me-2"></i>Schedule #<span class="schedule-number">${index + 1}</span>
                    </h6>
                    <button type="button" class="btn btn-danger btn-sm remove-schedule-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="schedules_${index}_course_id" class="form-label">Course</label>
                        <select name="schedules[${index}][course_id]" id="schedules_${index}_course_id"
                                class="form-control course-select" required>
                            <option value="">Select Course</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="schedules_${index}_instructor_id" class="form-label">Instructor</label>
                        <select name="schedules[${index}][instructor_id]" id="schedules_${index}_instructor_id"
                                class="form-control instructor-select" required>
                            <option value="">Select Instructor</option>
                            @foreach ($instructors as $instructor)
                                <option value="{{ $instructor->id }}">{{ $instructor->instructor_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="schedules_${index}_date" class="form-label">
                            Date <small class="text-muted">(Oklahoma Time)</small>
                        </label>
                        <input type="date" name="schedules[${index}][date]" id="schedules_${index}_date"
                               class="form-control date-input" min="${minDate}" onkeydown="return false" onpaste="return false" ondrop="return false" required>
                    </div>

                    <div class="col-md-4">
                        <label for="schedules_${index}_start_time" class="form-label">
                            Start Time <small class="text-muted">(Oklahoma Time)</small>
                        </label>
                        <input type="time" name="schedules[${index}][start_time]" id="schedules_${index}_start_time"
                               class="form-control start-time-input" required>
                    </div>

                    <div class="col-md-4">
                        <label for="schedules_${index}_end_time" class="form-label">
                            End Time <small class="text-muted">(Oklahoma Time)</small>
                        </label>
                        <input type="time" name="schedules[${index}][end_time]" id="schedules_${index}_end_time"
                               class="form-control end-time-input" required>
                    </div>

                    <div class="col-md-6">
                        <label for="schedules_${index}_session_type" class="form-label">Session Type</label>
                        <select name="schedules[${index}][session_type]" id="schedules_${index}_session_type"
                                class="form-control session-type-select" required>
                            <option value="">Select Session Type</option>
                            <option value="theory">Theory</option>
                            <option value="practical">Practical</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="schedules_${index}_max_students" class="form-label">Maximum Students</label>
                        <input type="number" name="schedules[${index}][max_students]" id="schedules_${index}_max_students"
                               class="form-control max-students-input" min="1" required>
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input is-active-input"
                                   id="schedules_${index}_is_active" name="schedules[${index}][is_active]" value="1" checked>
                            <label class="form-check-label" for="schedules_${index}_is_active">Active Status</label>
                        </div>
                    </div>
                </div>
            </div>
        `;
            }

            // Add new schedule
            $('#addScheduleBtn').click(function() {
                scheduleIndex++;
                const newSchedule = getScheduleTemplate(scheduleIndex);
                $('#schedulesContainer').append(newSchedule);
                updateScheduleNumbers();
                updateCounters();

                // Show remove buttons if more than one schedule
                if ($('.schedule-form-group').length > 1) {
                    $('.remove-schedule-btn').show();
                }
            });

            // Remove schedule
            $(document).on('click', '.remove-schedule-btn', function() {
                $(this).closest('.schedule-form-group').remove();
                updateScheduleNumbers();
                updateCounters();

                // Hide remove buttons if only one schedule remains
                if ($('.schedule-form-group').length <= 1) {
                    $('.remove-schedule-btn').hide();
                }
            });

            // Update schedule numbers
            function updateScheduleNumbers() {
                $('.schedule-form-group').each(function(index) {
                    $(this).find('.schedule-number').text(index + 1);
                });
            }

            // Update counters
            function updateCounters() {
                const count = $('.schedule-form-group').length;
                $('#scheduleCount').text(count);
                $('#submitCount').text(count);
            }

            // Copy first schedule to all others
            $('#copyFirstToAll').click(function() {
                const firstForm = $('.schedule-form-group').first();
                const firstValues = {
                    course_id: firstForm.find('.course-select').val(),
                    instructor_id: firstForm.find('.instructor-select').val(),
                    date: firstForm.find('.date-input').val(),
                    start_time: firstForm.find('.start-time-input').val(),
                    end_time: firstForm.find('.end-time-input').val(),
                    session_type: firstForm.find('.session-type-select').val(),
                    max_students: firstForm.find('.max-students-input').val(),
                    is_active: firstForm.find('.is-active-input').is(':checked')
                };

                $('.schedule-form-group').not(':first').each(function() {
                    $(this).find('.course-select').val(firstValues.course_id);
                    $(this).find('.instructor-select').val(firstValues.instructor_id);
                    $(this).find('.date-input').val(firstValues.date);
                    $(this).find('.start-time-input').val(firstValues.start_time);
                    $(this).find('.end-time-input').val(firstValues.end_time);
                    $(this).find('.session-type-select').val(firstValues.session_type);
                    $(this).find('.max-students-input').val(firstValues.max_students);
                    $(this).find('.is-active-input').prop('checked', firstValues.is_active);
                });

                alert('First schedule copied to all other schedules!');
            });

            // Set all active
            $('#setAllActive').click(function() {
                $('.is-active-input').prop('checked', true);
                alert('All schedules set to active!');
            });

            // Clear all forms
            $('#clearAll').click(function() {
                if (confirm('Are you sure you want to clear all form data?')) {
                    $('.schedule-form-group').find('input, select').val('');
                    $('.is-active-input').prop('checked', true);
                    alert('All form data cleared!');
                }
            });

            // Remove all except first
            $('#removeAll').click(function() {
                if (confirm('Are you sure you want to remove all schedules except the first one?')) {
                    $('.schedule-form-group').not(':first').remove();
                    $('.remove-schedule-btn').hide();
                    updateScheduleNumbers();
                    updateCounters();
                    alert('All schedules removed except the first one!');
                }
            });

            // Time validation
            $(document).on('change', '.end-time-input', function() {
                const container = $(this).closest('.schedule-form-group');
                const startTime = container.find('.start-time-input').val();
                const endTime = $(this).val();

                if (startTime && endTime && startTime >= endTime) {
                    alert('End time must be after start time (Oklahoma Time)');
                    $(this).val('');
                    $(this).focus();
                }
            });

            // Date validation (prevent past dates in Oklahoma time)
            $(document).on('change', '.date-input', function() {
                const selectedDate = $(this).val();
                const currentOklahomaDate = getCurrentOklahomaDate();

                if (selectedDate < currentOklahomaDate) {
                    alert('Please select a date that is today or in the future (Oklahoma Time)');
                    $(this).val('');
                    $(this).focus();
                }
            });

            // Enhanced form validation
            $('#multipleScheduleForm').on('submit', function(e) {
                let hasError = false;
                const currentOklahomaDate = getCurrentOklahomaDate();

                $('.schedule-form-group').each(function(index) {
                    const scheduleNum = index + 1;
                    const startTime = $(this).find('.start-time-input').val();
                    const endTime = $(this).find('.end-time-input').val();
                    const selectedDate = $(this).find('.date-input').val();

                    // Validate time
                    if (startTime >= endTime) {
                        hasError = true;
                        alert(
                            `Schedule #${scheduleNum}: End time must be after start time (Oklahoma Time)`
                            );
                        return false;
                    }

                    // Validate date
                    if (selectedDate < currentOklahomaDate) {
                        hasError = true;
                        alert(
                            `Schedule #${scheduleNum}: Please select a date that is today or in the future (Oklahoma Time)`
                            );
                        return false;
                    }
                });

                if (hasError) {
                    e.preventDefault();
                    return false;
                }

                // Show loading state
                $(this).find('button[type="submit"]').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>Creating Schedules...');
            });

            // Initialize
            updateCounters();

            // Set minimum date for all date inputs to current Oklahoma date
            const currentOklahomaDate = getCurrentOklahomaDate();
            $('.date-input').attr('min', currentOklahomaDate);
        });
    </script>
@endsection
