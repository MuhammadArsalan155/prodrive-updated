@extends('layouts.master')

@section('content')
    <div class="container-fluid px-4">
        <!-- Page Heading -->
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h1 class="h2 text-primary fw-bold">
                <i class="fas fa-user-graduate me-2"></i> Student Registration
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
                            <i class="fas fa-user-plus me-2"></i> Add New Student
                        </h5>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form action="{{ route('add_student') }}" method="post">
                            @csrf
                            <div class="row g-4">
                                <!-- Student Information Section -->
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-user-graduate me-2"></i> Student Information
                                    </h5>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="student_id" class="form-label">
                                            <i class="fas fa-id-card text-primary me-2"></i> Student ID
                                        </label>
                                        <input type="text" placeholder="Student ID" name="student_id" id="student_id"
                                            class="form-control" value="PDA-{{ \Carbon\Carbon::now()->isoFormat('YYYY') }}-"
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">
                                            <i class="fas fa-user text-primary me-2"></i> First Name
                                        </label>
                                        <input type="text" placeholder="First Name" name="first_name" id="first_name"
                                            class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="last_name" class="form-label">
                                            <i class="fas fa-user text-primary me-2"></i> Last Name
                                        </label>
                                        <input type="text" placeholder="Last Name" name="last_name" id="last_name"
                                            class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope text-primary me-2"></i> Student Email
                                        </label>
                                        <input type="email" placeholder="Student Email" name="email" id="email"
                                            class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="student_password" class="form-label">
                                            <i class="fas fa-lock text-primary me-2"></i> Student Password
                                        </label>
                                        <input type="password" placeholder="Password" name="student_password" id="student_password"
                                            class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="student_contact" class="form-label">
                                            <i class="fas fa-phone text-primary me-2"></i> Student Contact
                                        </label>
                                        <input type="text" placeholder="Student Contact" name="student_contact"
                                            id="student_contact" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="student_dob" class="form-label">
                                            <i class="fas fa-birthday-cake text-primary me-2"></i> Date of Birth
                                        </label>
                                        <input type="date" name="student_dob" id="student_dob" class="form-control"
                                            required>
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
                                        <input type="text" placeholder="Parent Full Name" name="parent_name" id="parent_name"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="parent_email" class="form-label">
                                            <i class="fas fa-envelope text-primary me-2"></i> Parent Email
                                        </label>
                                        <input type="email" placeholder="Parent Email" name="parent_email" id="parent_email"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="parent_password" class="form-label">
                                            <i class="fas fa-lock text-primary me-2"></i> Parent Password
                                        </label>
                                        <input type="password" placeholder="Parent Password" name="parent_password" id="parent_password"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="parent_contact" class="form-label">
                                            <i class="fas fa-phone text-primary me-2"></i> Parent Contact
                                        </label>
                                        <input type="text" placeholder="Parent Contact Number" name="parent_contact" id="parent_contact"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="parent_address" class="form-label">
                                            <i class="fas fa-map-marker-alt text-primary me-2"></i> Parent Address
                                        </label>
                                        <textarea placeholder="Parent Address (leave blank to use student address)" name="parent_address" id="parent_address" class="form-control"></textarea>
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
                                            <i class="fas fa-book text-primary me-2"></i> Select Course
                                        </label>
                                        <select name="course_id" id="course_id" class="form-control" required>
                                            <option value="">Select Course</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="instructor_id" class="form-label">
                                            <i class="fas fa-chalkboard-teacher text-primary me-2"></i> Select Instructor
                                        </label>
                                        <select name="instructor_id" id="instructor_id" class="form-control" required
                                            disabled>
                                            <option value="">Select Instructor</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="course_date" class="form-label">
                                            <i class="fas fa-calendar text-primary me-2"></i> Select Date
                                        </label>
                                        <select name="course_date" id="course_date" class="form-control" required
                                            disabled>
                                            <option value="">Select Date</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="course_slot" class="form-label">
                                            <i class="fas fa-clock text-primary me-2"></i> Select Time Slot
                                        </label>
                                        <select name="course_slot" id="course_slot" class="form-control" required
                                            disabled>
                                            <option value="">Select Time Slot</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">
                                            <i class="fas fa-map-marker-alt text-primary me-2"></i> Student Address
                                        </label>
                                        <textarea placeholder="Enter Student Address" name="address" id="address" class="form-control" required></textarea>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100" id="submit_btn">
                                        <i class="fas fa-user-plus me-2"></i> Add Student
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

            // Load Courses on Page Load
            function loadCourses() {
                $.ajax({
                    url: "{{ url('get-courses') }}",
                    type: 'GET',
                    success: function(response) {
                        var courseOptions = '<option value="">Select Course</option>';
                        response.forEach(function(course) {
                            courseOptions += `<option value="${course.id}">
                        ${course.course_name} (${course.course_type})
                    </option>`;
                        });
                        $('#course_id').html(courseOptions);
                    },
                    error: function() {
                        alert('Error loading courses');
                    }
                });
            }
            loadCourses();

            // Course selection triggers instructor load
            $('#course_id').change(function() {
                var courseId = $(this).val();

                // Reset dependent fields
                $('#instructor_id').html('<option value="">Select Instructor</option>').prop('disabled',
                    true);
                $('#course_date').html('<option value="">Select Date</option>').prop('disabled', true);
                $('#course_slot').html('<option value="">Select Time Slot</option>').prop('disabled', true);

                if (courseId) {
                    $.ajax({
                        url: "{{ url('get-course-instructors') }}/" + courseId,
                        type: 'GET',
                        success: function(response) {
                            $('#instructor_id').prop('disabled', false);
                            var instructorOptions =
                                '<option value="">Select Instructor</option>';
                            response.forEach(function(instructor) {
                                instructorOptions +=
                                    `<option value="${instructor.id}">${instructor.instructor_name}</option>`;
                            });
                            $('#instructor_id').html(instructorOptions);
                        },
                        error: function(xhr) {
                            console.error(xhr);
                            alert('Error loading instructors');
                        }
                    });
                }
            });

            // Instructor selection triggers schedule load
            $('#instructor_id').change(function() {
                var courseId = $('#course_id').val();
                var instructorId = $(this).val();

                // Reset dependent fields
                $('#course_date').html('<option value="">Select Date</option>').prop('disabled', true);
                $('#course_slot').html('<option value="">Select Time Slot</option>').prop('disabled', true);

                if (courseId && instructorId) {
                    $.ajax({
                        url: "{{ url('get-available-schedules') }}/" + courseId + '/' +
                            instructorId,
                        type: 'GET',
                        success: function(response) {
                            $('#course_date').prop('disabled', false);
                            var dateOptions = '<option value="">Select Date</option>';
                            var uniqueDates = [...new Set(response.map(schedule => schedule
                                .date))];

                            uniqueDates.forEach(function(date) {
                                dateOptions +=
                                    `<option value="${date}">${date}</option>`;
                            });

                            $('#course_date').html(dateOptions);
                        },
                        error: function(xhr) {
                            console.error(xhr);
                            alert('Error loading course schedules');
                        }
                    });
                }
            });

            // Date selection triggers slot load
            $('#course_date').change(function() {
                var courseId = $('#course_id').val();
                var instructorId = $('#instructor_id').val();
                var selectedDate = $(this).val();

                // Reset slot field
                $('#course_slot').html('<option value="">Select Time Slot</option>').prop('disabled', true);

                if (courseId && instructorId && selectedDate) {
                    $.ajax({
                        url: "{{ url('get-available-schedules') }}/" + courseId + '/' +
                            instructorId,
                        type: 'GET',
                        success: function(response) {
                            var filteredSchedules = response.filter(schedule => schedule
                                .date === selectedDate);

                            $('#course_slot').prop('disabled', false);
                            var slotOptions = '<option value="">Select Time Slot</option>';

                            filteredSchedules.forEach(function(schedule) {
                                slotOptions += `<option value="${schedule.id}">
                            ${schedule.start_time} - ${schedule.end_time}
                            (Available Slots: ${schedule.available_slots})
                        </option>`;
                            });

                            $('#course_slot').html(slotOptions);
                        },
                        error: function(xhr) {
                            console.error(xhr);
                            alert('Error loading time slots');
                        }
                    });
                }
            });

            // Copy student address to parent address if parent address is empty
            $('#address').on('blur', function() {
                if ($('#parent_address').val() === '') {
                    // Add checkbox for "Same as student address"
                    if (!$('#same_address_container').length) {
                        $('#parent_address').after('<div id="same_address_container" class="form-check mt-2"><input class="form-check-input" type="checkbox" id="same_address" checked><label class="form-check-label" for="same_address">Same as student address</label></div>');

                        // Add event listener for the checkbox
                        $('#same_address').on('change', function() {
                            if ($(this).is(':checked')) {
                                $('#parent_address').val($('#address').val()).prop('readonly', true);
                            } else {
                                $('#parent_address').prop('readonly', false);
                            }
                        });
                    }
                    $('#parent_address').val($(this).val()).prop('readonly', true);
                }
            });
        });
    </script>
@endsection
