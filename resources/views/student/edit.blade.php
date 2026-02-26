@extends('layouts.master')

@section('content')
    <div class="container-fluid px-4">
        <!-- Page Heading -->
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h1 class="h2 text-primary fw-bold">
                <i class="fas fa-user-graduate me-2"></i> Student Update
            </h1>
            <a href="{{ route('viewstudent') }}" class="btn btn-primary btn-sm rounded-pill">
                <i class="fas fa-eye me-1"></i> View Students
            </a>
        </div>

        <!-- Content Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12">
                <div class="card border-0 shadow-sm mb-4">
                    <!-- Card Header -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div
                        class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold">
                            <i class="fas fa-user-edit me-2"></i> Edit Student
                        </h5>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form action="{{ route('update_student') }}" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{ $student->id }}">
                            <div class="row g-4">
                                <!-- Student Information Section -->
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-user-graduate me-2"></i> Student Information
                                    </h5>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">
                                            <i class="fas fa-user text-primary me-2"></i> First Name
                                        </label>
                                        <input type="text" placeholder="First Name" name="first_name" id="first_name"
                                            class="form-control" value="{{ $student->first_name }}" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="last_name" class="form-label">
                                            <i class="fas fa-user text-primary me-2"></i> Last Name
                                        </label>
                                        <input type="text" placeholder="Last Name" name="last_name" id="last_name"
                                            class="form-control" value="{{ $student->last_name }}" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope text-primary me-2"></i> Student Email
                                        </label>
                                        <input type="email" placeholder="Student Email" name="email" id="email"
                                            class="form-control" value="{{ $student->email }}" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="student_password" class="form-label">
                                            <i class="fas fa-lock text-primary me-2"></i> Student Password
                                        </label>
                                        <input type="password" placeholder="Leave blank to keep current password"
                                            name="student_password" id="student_password" class="form-control">
                                        <small class="text-muted">Leave blank to keep current password</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="student_contact" class="form-label">
                                            <i class="fas fa-phone text-primary me-2"></i> Student Contact
                                        </label>
                                        <input type="text" placeholder="Student Contact" name="student_contact"
                                            id="student_contact" class="form-control" value="{{ $student->student_contact }}" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="student_dob" class="form-label">
                                            <i class="fas fa-birthday-cake text-primary me-2"></i> Date of Birth
                                        </label>
                                        <input type="date" name="student_dob" id="student_dob" class="form-control"
                                            value="{{ $student->student_dob }}" required>
                                        <div id="age_error" class="text-danger mt-1" style="display: none;">
                                            Student must be at least 13 years old.
                                        </div>
                                        <div id="age_display" class="text-muted mt-1" style="display: none;">
                                            Age: <span id="calculated_age"></span> years
                                        </div>
                                    </div>
                                </div>

                                <!-- Parent Information Section -->
                                <div class="col-12">
                                    <hr>
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-user-friends me-2"></i> Parent Information
                                    </h5>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="parent_name" class="form-label">
                                            <i class="fas fa-user text-primary me-2"></i> Parent Name
                                        </label>
                                        <input type="text" placeholder="Parent Full Name" name="parent_name"
                                            id="parent_name" class="form-control" value="{{ $parent ? $parent->name : '' }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="parent_email" class="form-label">
                                            <i class="fas fa-envelope text-primary me-2"></i> Parent Email
                                        </label>
                                        <input type="email" placeholder="Parent Email" name="parent_email"
                                            id="parent_email" class="form-control" value="{{ $parent ? $parent->email : '' }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="parent_password" class="form-label">
                                            <i class="fas fa-lock text-primary me-2"></i> Parent Password
                                        </label>
                                        <input type="password" placeholder="Leave blank to keep current password"
                                            name="parent_password" id="parent_password" class="form-control">
                                        <small class="text-muted">Leave blank to keep current password</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="parent_contact" class="form-label">
                                            <i class="fas fa-phone text-primary me-2"></i> Parent Contact
                                        </label>
                                        <input type="text" placeholder="Parent Contact Number" name="parent_contact"
                                            id="parent_contact" class="form-control" value="{{ $parent ? $parent->contact : '' }}">
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="parent_address" class="form-label">
                                            <i class="fas fa-map-marker-alt text-primary me-2"></i> Parent Address
                                        </label>
                                        <textarea placeholder="Parent Address (leave blank to use student address)"
                                            name="parent_address" id="parent_address" class="form-control">{{ $parent ? $parent->address : '' }}</textarea>
                                    </div>
                                </div>

                                <!-- Course Information Section -->
                                <div class="col-12">
                                    <hr>
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-book me-2"></i> Course Information
                                    </h5>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="course_id" class="form-label">
                                            <i class="fas fa-book text-primary me-2"></i> Course
                                        </label>
                                        <input type="text" class="form-control"
                                            value="{{ $student->course->course_name }} ({{ $student->course->course_type }})"
                                            readonly>
                                        <input type="hidden" name="course_id" value="{{ $student->course_id }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="instructor_id" class="form-label">
                                            <i class="fas fa-chalkboard-teacher text-primary me-2"></i> Select Instructor
                                        </label>
                                        <select name="instructor_id" id="instructor_id" class="form-control" required>
                                            @foreach ($instructors as $instructor)
                                                <option value="{{ $instructor->id }}"
                                                    {{ $student->instructor_id == $instructor->id ? 'selected' : '' }}>
                                                    {{ $instructor->instructor_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="course_date" class="form-label">
                                            <i class="fas fa-calendar text-primary me-2"></i> Select Date
                                        </label>
                                        <select name="course_date" id="course_date" class="form-control" required>
                                            <option value="">Select Date</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="course_slot" class="form-label">
                                            <i class="fas fa-clock text-primary me-2"></i> Select Time Slot
                                        </label>
                                        <select name="course_slot" id="course_slot" class="form-control" required>
                                            <option value="">Select Time Slot</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">
                                            <i class="fas fa-map-marker-alt text-primary me-2"></i> Student Address
                                        </label>
                                        <textarea placeholder="Enter Address" name="address" id="address" class="form-control" required>{{ $student->address }}</textarea>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100" id="submit_btn">
                                        <i class="fas fa-save me-2"></i> Update Student
                                    </button>
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
    // Function to setup DOB validation
    function setupDOBValidation(dobInput) {
        // Calculate dates
        const today = new Date();

        // Calculate minimum age date (13 years ago)
        const minAgeDate = new Date();
        minAgeDate.setFullYear(today.getFullYear() - 13);

        // Format dates for input attributes
        const formatDate = (date) => {
            const yyyy = date.getFullYear();
            let mm = date.getMonth() + 1;
            let dd = date.getDate();

            if (dd < 10) dd = '0' + dd;
            if (mm < 10) mm = '0' + mm;

            return yyyy + '-' + mm + '-' + dd;
        };

        // Set max date to 13 years ago (prevents selecting dates that would make user younger than 13)
        dobInput.setAttribute('max', formatDate(minAgeDate));

        // Add change event listener
        dobInput.addEventListener('change', function(e) {
            const selectedDate = new Date(this.value);
            const today = new Date();
            const minAgeDate = new Date();
            minAgeDate.setFullYear(today.getFullYear() - 13);

            // Reset time part for accurate date comparison
            today.setHours(0, 0, 0, 0);
            selectedDate.setHours(0, 0, 0, 0);
            minAgeDate.setHours(0, 0, 0, 0);

            let isInvalid = false;
            let errorMessage = '';

            if (selectedDate >= today) {
                // Future date or today
                isInvalid = true;
                errorMessage = 'Date of birth cannot be today or a future date.';
            } else if (selectedDate > minAgeDate) {
                // Date would make user younger than 13
                isInvalid = true;
                errorMessage = 'You must be at least 13 years old to register.';
            }

            if (isInvalid) {
                // Reset the input
                this.value = '';

                // Add error styling
                this.classList.add('is-invalid');
                $('#submit_btn').prop('disabled', true);

                // Remove any existing error message
                $('#age_error').hide();
                $('#age_display').hide();

                // Show custom error message
                const existingError = $(this).siblings('.invalid-feedback');
                if (existingError.length) {
                    existingError.remove();
                }

                // Add error message
                $(this).after('<div class="invalid-feedback d-block">' + errorMessage + '</div>');

                // Show alert
                alert(errorMessage);
            } else {
                // Remove error styling if date is valid
                this.classList.remove('is-invalid');
                $(this).siblings('.invalid-feedback').remove();
                $('#submit_btn').prop('disabled', false);

                // Calculate and display age
                if (this.value) {
                    const age = calculateAge(this.value);
                    $('#calculated_age').text(age);
                    $('#age_display').show();
                    $('#age_error').hide();
                }
            }
        });
    }

    // Function to calculate age
    function calculateAge(birthDate) {
        const today = new Date();
        const birth = new Date(birthDate);
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }

        return age;
    }

    // Initialize DOB validation
    const dobInput = document.getElementById('student_dob');
    if (dobInput) {
        setupDOBValidation(dobInput);

        // Show age on page load if date exists
        if (dobInput.value) {
            const age = calculateAge(dobInput.value);
            $('#calculated_age').text(age);
            $('#age_display').show();
        }
    }

    // Form submission validation
    $('form').on('submit', function(e) {
        const dobValue = $('#student_dob').val();
        if (!dobValue) {
            e.preventDefault();
            alert('Please enter a valid date of birth.');
            return false;
        }

        const selectedDate = new Date(dobValue);
        const today = new Date();
        const minAgeDate = new Date();
        minAgeDate.setFullYear(today.getFullYear() - 13);

        today.setHours(0, 0, 0, 0);
        selectedDate.setHours(0, 0, 0, 0);
        minAgeDate.setHours(0, 0, 0, 0);

        if (selectedDate >= today || selectedDate > minAgeDate) {
            e.preventDefault();
            alert('Please ensure the student meets the age requirements (minimum 13 years old).');
            return false;
        }
    });

    // Get current course and instructor IDs
    var currentCourseId = {{ $student->course_id }};
    var currentInstructorId = {{ $student->instructor_id }};
    var currentScheduleId = {{ $student->practical_schedule_id ?? 'null' }};

    // Get current schedule details from the SQL query result
    var currentDate = @if(isset($currentSchedule) && $currentSchedule && $currentSchedule->schedule_date)'{{ $currentSchedule->schedule_date }}'@else null @endif;

    console.log('Current Schedule Info:', {
        scheduleId: currentScheduleId,
        date: currentDate,
        courseId: currentCourseId,
        instructorId: currentInstructorId
    });

    // Add the same address functionality
    $('#address').on('blur', function() {
        if ($('#parent_address').val() === '') {
            if (!$('#same_address_container').length) {
                $('#parent_address').after(`
                    <div id="same_address_container" class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="same_address">
                        <label class="form-check-label" for="same_address">Same as student address</label>
                    </div>
                `);

                $('#same_address').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('#parent_address').val($('#address').val()).prop('readonly', true);
                    } else {
                        $('#parent_address').prop('readonly', false);
                    }
                });
            }
        }
    });

    // Initialize schedules with proper selection
    function loadSchedules() {
        var url = "{{ url('get-available-schedules') }}/" + currentCourseId + '/' + currentInstructorId;

        // Add current schedule ID as query parameter for edit mode
        if (currentScheduleId) {
            url += '?currentScheduleId=' + currentScheduleId;
        }

        console.log('Loading schedules from URL:', url);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                console.log('Available schedules for edit:', response);

                // Store all schedules for later use
                window.allSchedules = response;

                // Find the current schedule in the response
                var currentScheduleInResponse = response.find(schedule =>
                    schedule.id == currentScheduleId || schedule.is_current
                );

                console.log('Current schedule found in response:', currentScheduleInResponse);

                // If current schedule is found, use its date, otherwise use the passed date
                var actualCurrentDate = currentScheduleInResponse ? currentScheduleInResponse.date : currentDate;

                console.log('Using date for selection:', actualCurrentDate);

                // Populate dates
                var uniqueDates = [...new Set(response.map(schedule => schedule.date))];
                uniqueDates.sort(); // Sort dates

                var dateOptions = '<option value="">Select Date</option>';

                uniqueDates.forEach(function(date) {
                    var selected = (actualCurrentDate && date === actualCurrentDate) ? 'selected' : '';
                    dateOptions += `<option value="${date}" ${selected}>${date}</option>`;
                });

                $('#course_date').html(dateOptions);

                // If there's a current date, load its time slots immediately
                if (actualCurrentDate) {
                    $('#course_date').val(actualCurrentDate);
                    loadTimeSlots(actualCurrentDate, true);
                } else if (currentScheduleId) {
                    // If we have a schedule ID but no matching date, something is wrong
                    console.warn('Current schedule ID exists but no matching date found in schedules');
                    console.warn('Current Schedule ID:', currentScheduleId);
                    console.warn('Available dates:', uniqueDates);

                    // Try to find any schedule with the current ID regardless of date
                    var foundSchedule = response.find(s => s.id == currentScheduleId);
                    if (foundSchedule) {
                        console.log('Found schedule by ID:', foundSchedule);
                        $('#course_date').val(foundSchedule.date);
                        loadTimeSlots(foundSchedule.date, true);
                    }
                }
            },
            error: function(xhr) {
                console.error('Error loading schedules:', xhr);
                console.error('Response:', xhr.responseText);
                alert('Error loading course schedules. Please check the console for details.');
            }
        });
    }

    // Load time slots for a specific date using stored schedules
    function loadTimeSlots(selectedDate, isInitialLoad = false) {
        console.log('loadTimeSlots called with:', {
            selectedDate: selectedDate,
            isInitialLoad: isInitialLoad,
            currentScheduleId: currentScheduleId
        });

        if (!selectedDate) {
            $('#course_slot').html('<option value="">Select Time Slot</option>');
            return;
        }

        // Use stored schedules instead of making another AJAX call
        if (window.allSchedules) {
            var filteredSchedules = window.allSchedules.filter(schedule => schedule.date === selectedDate);
            console.log('Filtered schedules for date ' + selectedDate + ':', filteredSchedules);

            if (filteredSchedules.length === 0) {
                console.warn('No schedules found for date:', selectedDate);
                $('#course_slot').html('<option value="">No time slots available for this date</option>');
                return;
            }

            var slotOptions = '<option value="">Select Time Slot</option>';
            filteredSchedules.forEach(function(schedule) {
                var selected = (currentScheduleId && schedule.id == currentScheduleId) ? 'selected' : '';

                // Add indicator if this is the current schedule
                var indicator = (schedule.is_current || schedule.id == currentScheduleId) ? ' (Current)' : '';

                slotOptions += `<option value="${schedule.id}" ${selected}>
                    ${schedule.start_time} - ${schedule.end_time}${indicator}
                    (Available Slots: ${schedule.available_slots})
                </option>`;

                console.log('Added option:', {
                    id: schedule.id,
                    selected: selected,
                    isCurrent: schedule.id == currentScheduleId
                });
            });

            $('#course_slot').html(slotOptions);

            // Force set the current schedule if this is initial load
            if (isInitialLoad && currentScheduleId) {
                setTimeout(function() {
                    $('#course_slot').val(currentScheduleId);
                    console.log('Force set current schedule ID:', currentScheduleId);
                    console.log('Actual selected value:', $('#course_slot').val());
                }, 100);
            }
        } else {
            console.error('No schedules stored in window.allSchedules');
        }
    }

    // Date selection triggers slot load
    $('#course_date').change(function() {
        var selectedDate = $(this).val();
        console.log('Date changed to:', selectedDate);

        if (selectedDate) {
            // Check if this is selecting the current schedule's date
            var isCurrentDate = (selectedDate === currentDate) ||
                               (window.allSchedules && window.allSchedules.find(s => s.id == currentScheduleId && s.date === selectedDate));
            loadTimeSlots(selectedDate, isCurrentDate);
        } else {
            $('#course_slot').html('<option value="">Select Time Slot</option>');
        }
    });

    // Instructor change reloads schedules
    $('#instructor_id').change(function() {
        var newInstructorId = $(this).val();
        console.log('Instructor changed to:', newInstructorId);

        currentInstructorId = newInstructorId;

        // Reset current selections since instructor changed
        currentDate = null;
        currentScheduleId = null;
        window.allSchedules = null;

        // Reset date and slot dropdowns
        $('#course_date').html('<option value="">Select Date</option>');
        $('#course_slot').html('<option value="">Select Time Slot</option>');

        // Reload schedules if instructor is selected
        if (currentInstructorId) {
            loadSchedules();
        }
    });

    // Load initial schedules
    console.log('Loading initial schedules...');
    loadSchedules();
});
   </script>
@endsection
