<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        :root {
            --primary-color: #1D4C5C;
            --primary-hover: #2a6276;
            --primary-light: rgba(29, 76, 92, 0.1);
            --text-light: rgba(255, 255, 255, 0.95);
        }

        body {
            background-color: #fff;
            font-family: 'Roboto', sans-serif;
        }

        .form-body {
            min-height: 100vh;
            overflow: hidden;
        }

        .iofrm-layout {
            display: flex;
            min-height: 100vh;
        }

        .img-holder {
            background-color: var(--primary-color);
            background-image: linear-gradient(135deg, #1D4C5C 0%, #2a6276 100%);
            width: 40%;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .img-holder .bg {
            position: absolute;
            opacity: 0.1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
        }

        .img-holder .info-holder {
            position: relative;
            text-align: center;
            color: var(--text-light);
            max-width: 400px;
            z-index: 1;
        }

        .form-holder {
            flex: 1;
            padding: 40px;
            display: flex;
            align-items: center;
            background-color: #f8f9fa;
        }

        .form-content {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        .form-items {
            background-color: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .form-control,
        .form-select {
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(29, 76, 92, 0.15);
        }

        .payment-methods {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .payment-method-card {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: calc(33.333% - 10px);
            text-align: center;
        }

        .payment-method-card:hover {
            border-color: var(--primary-color);
            background-color: var(--primary-light);
        }

        .payment-method-card.selected {
            border-color: var(--primary-color);
            background-color: var(--primary-light);
        }

        .payment-method-card img {
            width: 50px;
            height: 50px;
            margin-bottom: 10px;
        }

        .course-info {
            background-color: var(--primary-light);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .ibtn {
            background-color: var(--primary-color);
            color: #fff;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .ibtn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .step {
            text-align: center;
            flex: 1;
            position: relative;
        }

        .step:not(:last-child):after {
            content: '';
            position: absolute;
            top: 15px;
            left: 50%;
            width: 100%;
            height: 2px;
            background-color: #e9ecef;
        }

        .step.active .step-number {
            background-color: var(--primary-color);
            color: white;
        }

        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 992px) {
            .img-holder {
                width: 100%;
                min-height: 300px;
            }

            .iofrm-layout {
                flex-direction: column;
            }

            .payment-method-card {
                width: calc(50% - 10px);
            }
        }
    </style>
    <style>
        .payment-options-container {
            background-color: var(--primary-light);
            border-radius: 8px;
            padding: 20px;
            margin-top: 15px;
            transition: all 0.3s ease;
        }

        .payment-option {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        .payment-option:hover {
            background-color: rgba(29, 76, 92, 0.05);
        }

        .payment-selection-container h5 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .total-course-price {
            font-size: 1.1em;
            color: var(--primary-color);
        }

        #installmentDetailsContainer {
            background-color: rgba(255, 255, 255, 0.7);
            border-radius: 6px;
            padding: 10px;
            margin-top: 10px;
        }

        #installmentDetailsContainer ul {
            margin-bottom: 0;
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }

        .is-invalid {
            border-color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="form-body">
        <div class="iofrm-layout">
            <div class="img-holder">
                <div class="bg"></div>
                <div class="info-holder">
                    <img src="{{ asset('admin/img/Prodrive 4.png') }}" height="300" width="300" alt="logo">
                    {{-- <img src="/api/placeholder/300/300" alt="logo" /> --}}
                    <h3>Student Registration</h3>
                    <p>Please complete all steps to register for your course.</p>
                </div>
            </div>




            <div class="form-holder">
                <div class="form-content">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert" style="display:none">
                        <strong></strong>
                        {{-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button> --}}
                    </div>
                    <div class="form-items">
                        <div class="step-indicator">
                            <div class="step active" data-step="1">
                                <div class="step-number">1</div>
                                <div>Personal Info</div>
                            </div>
                            <div class="step" data-step="2">
                                <div class="step-number">2</div>
                                <div>Course Selection</div>
                            </div>
                            <div class="step" data-step="3">
                                <div class="step-number">3</div>
                                <div>Payment</div>
                            </div>
                        </div>

                        <form id="registrationForm">
                            <!-- Step 1: Personal Information -->
                            <div class="form-step" id="step1">
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <label>First Name</label>
                                        <input type="text" class="form-control" name="first_name"
                                            placeholder="First name" required>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label>Last Name</label>
                                        <input type="text" class="form-control" name="last_name"
                                            placeholder="Last name" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <label>Email</label>
                                        <input type="email" class="form-control" name="email"
                                            placeholder="Email Address" required>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label>Contact Number</label>
                                        <input type="tel" class="form-control" name="student_contact"
                                            placeholder="Contact Number" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <label>Date Of Birth</label>
                                        <input type="date" class="form-control" name="student_dob" required>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label>Address</label>
                                        <input type="text" class="form-control" name="address" placeholder="Address"
                                            required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-button text-end">
                                            <button type="button" class="ibtn next-step">Next</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Course Selection -->
                            <div class="form-step" id="step2" style="display: none;">
                                <div class="course-info" id="selectedCourseInfo" style="display: none;">
                                    <h5>Selected Course Details</h5>
                                    <div id="courseDetails"></div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <select class="form-select" name="course_id" id="courseSelect" required>
                                            <option value="">Select Course</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <select class="form-select" name="instructor_id" id="instructorSelect" required>
                                            <option value="">Select Instructor</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="datePicker"
                                            placeholder="Select Date" readonly>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <select class="form-select" name="schedule_id" id="scheduleSelect" required
                                            disabled>
                                            <option value="">Select Time Slot</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-button text-end">
                                            <button type="button" class="ibtn prev-step me-2">Previous</button>
                                            <button type="button" class="ibtn next-step">Next</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3: Payment -->
                            <div class="form-step" id="step3" style="display: none;">
                                <h5>Select Payment Method</h5>
                                <div class="payment-methods" id="paymentMethods">
                                    <!-- Payment methods will be loaded here -->
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-button text-end">
                                            <button type="button" class="ibtn prev-step me-2">Previous</button>
                                            <button type="submit" class="ibtn">Complete Registration</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Initialize variables
            let currentStep = 1;
            const form = document.getElementById('registrationForm');
            const maxSteps = 3;
            let availableSchedules = [];
            let flatpickrInstance = null;

            // Initial setup
            loadInitialData();

            // Load all required data
            async function loadInitialData() {
                await fetchCourses();
                await fetchPaymentMethods();
                setupEventListeners();
            }

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

                        // Show error using your existing error display system
                        showError(errorMessage);

                        // Add error styling
                        this.classList.add('is-invalid');

                        // Remove any existing error message
                        const existingError = this.nextElementSibling;
                        if (existingError && existingError.classList.contains('invalid-feedback')) {
                            existingError.remove();
                        }

                        // Add error message
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback d-block';
                        errorDiv.textContent = errorMessage;
                        this.parentNode.appendChild(errorDiv);
                    } else {
                        // Remove error styling if date is valid
                        this.classList.remove('is-invalid');
                        const existingError = this.nextElementSibling;
                        if (existingError && existingError.classList.contains('invalid-feedback')) {
                            existingError.remove();
                        }
                    }
                });
            }
            // Setup all event listeners
            function setupEventListeners() {
                // Next step buttons
                document.querySelectorAll('.next-step').forEach(button => {
                    button.addEventListener('click', () => {
                        if (validateCurrentStep()) {
                            navigateToStep(currentStep + 1);
                        }
                    });
                });
                const dobInput = document.querySelector('input[name="student_dob"]');
                if (dobInput) {
                    setupDOBValidation(dobInput);
                }

                // Previous step buttons
                document.querySelectorAll('.prev-step').forEach(button => {
                    button.addEventListener('click', () => {
                        navigateToStep(currentStep - 1);
                    });
                });



                // Course selection change
                document.getElementById('courseSelect').addEventListener('change', handleCourseSelection);

                // Instructor selection change
                document.getElementById('instructorSelect').addEventListener('change', handleInstructorSelection);

                // Payment method selection
                document.getElementById('paymentMethods').addEventListener('click', handlePaymentMethodSelection);

                // Form submission
                form.addEventListener('submit', handleFormSubmission);
            }

            // Navigation Functions
            function navigateToStep(stepNumber) {
                if (stepNumber < 1 || stepNumber > maxSteps) return;

                // Hide all steps
                document.querySelectorAll('.form-step').forEach(step => {
                    step.style.display = 'none';
                });

                // Show current step
                document.getElementById(`step${stepNumber}`).style.display = 'block';

                // Update step indicators
                updateStepIndicators(stepNumber);

                currentStep = stepNumber;
            }

            function updateStepIndicators(activeStep) {
                document.querySelectorAll('.step').forEach(step => {
                    const stepNum = parseInt(step.dataset.step);
                    step.classList.toggle('active', stepNum <= activeStep);
                });
            }

            // Validation Functions
            function validateCurrentStep() {
                const currentStepElement = document.getElementById(`step${currentStep}`);
                const requiredFields = currentStepElement.querySelectorAll('input[required], select[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        highlightInvalidField(field);
                    } else if (field.name === 'student_dob') {
                        // Additional validation for DOB
                        const selectedDate = new Date(field.value);
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        selectedDate.setHours(0, 0, 0, 0);

                        if (selectedDate >= today) {
                            isValid = false;
                            highlightInvalidField(field);
                            showError('Please select a valid date of birth.');
                        } else {
                            removeInvalidHighlight(field);
                        }
                    } else {
                        removeInvalidHighlight(field);
                    }
                });

                // Additional validation for specific steps
                if (currentStep === 3 && !document.querySelector('.payment-method-card.selected')) {
                    isValid = false;
                    showError('Please select a payment method before proceeding.');
                }

                return isValid;
            }

            function highlightInvalidField(field) {
                field.classList.add('is-invalid');
                field.addEventListener('input', function() {
                    if (field.value.trim()) {
                        removeInvalidHighlight(field);
                    }
                }, {
                    once: true
                });
            }

            function removeInvalidHighlight(field) {
                field.classList.remove('is-invalid');
            }

            // Data Fetching Functions
            async function fetchInstructors(courseId) {
                try {
                    const response = await fetch(`/api/courses/${courseId}/instructors`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Failed to fetch instructors');
                    }

                    const instructors = await response.json();
                    populateInstructorSelect(instructors);
                } catch (error) {
                    console.error('Error fetching instructors:', error);
                    showError('Failed to load instructors. Please try again later.');
                }
            }



            async function fetchSchedules(courseId, instructorId) {
                try {
                    const response = await fetch(
                        `/api/courses/${courseId}/instructors/${instructorId}/schedules`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                    if (!response.ok) {
                        throw new Error('Failed to fetch schedules');
                    }

                    availableSchedules = await response.json();
                    console.log("Available Schedules:", availableSchedules); // Debugging

                    initializeDatePicker();
                } catch (error) {
                    console.error('Error fetching schedules:', error);
                    showError('Failed to load schedules. Please try again later.');
                }
            }

            function initializeDatePicker() {
                if (flatpickrInstance) {
                    flatpickrInstance.destroy();
                }

                // ADD DEBUG LINE:
                console.log("Initializing date picker with schedules:", availableSchedules);

                // Get available dates from schedules
                const availableDates = [...new Set(availableSchedules.map(schedule => schedule.date))];

                // ADD DEBUG LINE:
                console.log("Available dates for picker:", availableDates);

                flatpickrInstance = flatpickr("#datePicker", {
                    enableTime: false,
                    dateFormat: "Y-m-d",
                    minDate: "today",
                    enable: availableDates,
                    locale: {
                        firstDayOfWeek: 0
                    },
                    onChange: function(selectedDates, dateStr) {
                        console.log("Date selected:", dateStr); // DEBUG
                        if (dateStr) {
                            updateTimeSlots(dateStr);
                        }
                    }
                });

                document.getElementById('datePicker').disabled = false;
            }

            function updateTimeSlots(selectedDate) {
                const scheduleSelect = document.getElementById('scheduleSelect');
                scheduleSelect.innerHTML = '<option value="">Select Time Slot</option>';

                console.log("Selected Date (Formatted):", selectedDate); // Debug log

                const dateSchedules = availableSchedules.filter(schedule => {
                    console.log("Comparing:", schedule.date, selectedDate); // Debug log
                    return schedule.date === selectedDate;
                });

                console.log("Schedules for selected date:", dateSchedules); // Debugging

                if (dateSchedules.length === 0) {
                    console.warn("No schedules available for this date.");
                    scheduleSelect.disabled = true;
                    return;
                }

                dateSchedules.forEach(schedule => {
                    const option = document.createElement('option');
                    option.value = schedule.id;
                    option.textContent =
                        `${schedule.start_time} - ${schedule.end_time}`;
                    scheduleSelect.appendChild(option);
                });

                scheduleSelect.disabled = false;
            }

            // Event Listeners
            document.getElementById('instructorSelect').addEventListener('change', function(e) {
                const instructorId = e.target.value;
                const courseId = document.getElementById('courseSelect').value;

                if (instructorId && courseId) {
                    // Reset date and time slot selection
                    document.getElementById('datePicker').value = '';
                    document.getElementById('scheduleSelect').innerHTML =
                        '<option value="">Select Time Slot</option>';
                    document.getElementById('scheduleSelect').disabled = true;

                    fetchSchedules(courseId, instructorId);
                } else {
                    if (flatpickrInstance) {
                        flatpickrInstance.destroy();
                    }
                    document.getElementById('datePicker').disabled = true;
                    document.getElementById('scheduleSelect').disabled = true;
                }
            });

            function showError(message, detail = null) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    footer: detail ? `<small class="text-muted">${detail}</small>` : '',
                    confirmButtonColor: '#1D4C5C'
                });
            }
            // Update other fetch calls similarly
            async function fetchCourses() {
                try {
                    const response = await fetch('/api/courses', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!response.ok) throw new Error('Failed to fetch courses');

                    const courses = await response.json();
                    populateCourseSelect(courses);
                } catch (error) {
                    console.error('Error fetching courses:', error);
                    showError('Failed to load courses. Please try again later.');
                }
            }



            async function fetchPaymentMethods() {
                try {
                    const response = await fetch('/api/payment-methods', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!response.ok) throw new Error('Failed to fetch payment methods');

                    const paymentMethods = await response.json();
                    populatePaymentMethods(paymentMethods);
                } catch (error) {
                    console.error('Error fetching payment methods:', error);
                    showError('Failed to load payment methods. Please try again later.');
                }
            }

            // Population Functions
            function populateCourseSelect(courses) {
                const courseSelect = document.getElementById('courseSelect');
                courseSelect.innerHTML = '<option value="">Select Course</option>';

                courses.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.id;
                    option.textContent = `${course.course_name} - $${course.course_price}`;
                    option.dataset.details = JSON.stringify(course);
                    courseSelect.appendChild(option);
                });
            }

            function populateInstructorSelect(instructors) {
                const instructorSelect = document.getElementById('instructorSelect');
                instructorSelect.innerHTML = '<option value="">Select Instructor</option>';

                instructors.forEach(instructor => {
                    const option = document.createElement('option');
                    option.value = instructor.id;
                    option.textContent = instructor.instructor_name;
                    instructorSelect.appendChild(option);
                });

                instructorSelect.disabled = false;
            }

            function populateScheduleSelect(schedules) {
                const scheduleSelect = document.getElementById('scheduleSelect');
                scheduleSelect.innerHTML = '<option value="">Select Time Slot</option>';

                schedules.forEach(schedule => {
                    const option = document.createElement('option');
                    option.value = schedule.id;
                    const dateObj = new Date(schedule.date);
                    const formattedDate = dateObj.toLocaleDateString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    option.textContent =
                        `${formattedDate} (${schedule.start_time} - ${schedule.end_time}) - ${schedule.available_slots} slots available`;
                    scheduleSelect.appendChild(option);
                });

                scheduleSelect.disabled = false;
            }

            function populatePaymentMethods(paymentMethods) {
                const container = document.getElementById('paymentMethods');
                container.innerHTML = '';

                paymentMethods.forEach(method => {
                    const card = document.createElement('div');
                    card.className = 'payment-method-card';
                    card.dataset.id = method.id;

                    // Handle relative paths for logos
                    const logoPath = method.logo ?
                        method.logo.startsWith('http') ?
                        method.logo :
                        `/storage/${method.logo}` // Assuming logos are in storage/app/public
                        :
                        '/api/placeholder/50/50';

                    card.innerHTML = `
            <img src="${logoPath}"
                 alt="${method.name}"
                 onerror="this.src='/api/placeholder/50/50'; this.onerror=null;">
            <div class="payment-name">${method.name}</div>
        `;

                    container.appendChild(card);
                });
            }

            // Event Handlers
            // Updated payment method selection handler
            function handlePaymentMethodSelection(event) {
                const clickedCard = event.target.closest('.payment-method-card');
                if (!clickedCard) return;

                // Remove selection from all payment method cards
                document.querySelectorAll('.payment-method-card').forEach(card => {
                    card.classList.remove('selected');
                });

                // Add selection to clicked card
                clickedCard.classList.add('selected');

                // Get the selected course ID from the course select element
                const courseSelect = document.getElementById('courseSelect');
                const selectedCourseId = courseSelect.value;
                const selectedPaymentMethodId = clickedCard.dataset.id;

                console.log('Payment method selected:', {
                    courseId: selectedCourseId,
                    paymentMethodId: selectedPaymentMethodId
                });

                // Validate course selection
                if (!selectedCourseId) {
                    showError('Please select a course first.');
                    clickedCard.classList.remove('selected');
                    return;
                }

                // Fetch course payment details with the selected payment method
                fetchCoursePaymentDetails(selectedCourseId, selectedPaymentMethodId);
            }

            // Fixed function to fetch course payment details
            async function fetchCoursePaymentDetails(courseId, paymentMethodId = null) {
                try {
                    console.log('Fetching payment details for:', {
                        courseId,
                        paymentMethodId
                    });

                    // Build URL with course ID as path parameter and payment method as query parameter
                    let url = `/api/course-payment-details/${courseId}`;
                    if (paymentMethodId) {
                        url += `?payment_method_id=${paymentMethodId}`;
                    }

                    console.log('Request URL:', url);

                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        }
                    });

                    console.log('Response status:', response.status);
                    console.log('Response headers:', Object.fromEntries(response.headers));

                    if (!response.ok) {
                        const errorData = await response.text();
                        console.error('Error response:', errorData);
                        throw new Error(`HTTP ${response.status}: Failed to fetch course payment details`);
                    }

                    const data = await response.json();
                    console.log('Payment details received:', data);

                    displayPaymentOptions(data);
                } catch (error) {
                    console.error('Error fetching payment details:', error);
                    showError('Failed to retrieve payment details.', error.message);
                }
            }

            // Also update the course selection handler to trigger payment details when course changes
            async function handleCourseSelection(event) {
                const courseId = event.target.value;
                const instructorSelect = document.getElementById('instructorSelect');
                const scheduleSelect = document.getElementById('scheduleSelect');

                // Reset dependent selects
                instructorSelect.innerHTML = '<option value="">Select Instructor</option>';
                scheduleSelect.innerHTML = '<option value="">Select Time Slot</option>';
                instructorSelect.disabled = true;
                scheduleSelect.disabled = true;

                // Clear payment options when course changes
                const paymentOptionsContainer = document.getElementById('paymentOptionsContainer');
                if (paymentOptionsContainer) {
                    paymentOptionsContainer.innerHTML = '';
                }

                // Clear payment method selection
                document.querySelectorAll('.payment-method-card').forEach(card => {
                    card.classList.remove('selected');
                });

                if (courseId) {
                    // Display course details
                    const courseDetails = JSON.parse(event.target.selectedOptions[0].dataset.details);
                    displayCourseDetails(courseDetails);

                    // Fetch instructors for selected course
                    await fetchInstructors(courseId);

                    // If a payment method is already selected, fetch payment details
                    const selectedPaymentMethod = document.querySelector('.payment-method-card.selected');
                    if (selectedPaymentMethod) {
                        fetchCoursePaymentDetails(courseId, selectedPaymentMethod.dataset.id);
                    }
                } else {
                    hideCourseDetails();
                }
            }
            async function handleInstructorSelection(event) {
                const instructorId = event.target.value;
                const courseId = document.getElementById('courseSelect').value;
                const scheduleSelect = document.getElementById('scheduleSelect');

                scheduleSelect.innerHTML = '<option value="">Select Time Slot</option>';
                scheduleSelect.disabled = true;

                if (instructorId && courseId) {
                    await fetchSchedules(courseId, instructorId);
                }
            }

            function handlePaymentMethodSelection(event) {
                const clickedCard = event.target.closest('.payment-method-card');
                if (!clickedCard) return;

                // Remove selection from all payment method cards
                document.querySelectorAll('.payment-method-card').forEach(card => {
                    card.classList.remove('selected');
                });

                // Add selection to clicked card
                clickedCard.classList.add('selected');

                // Get the selected course ID from the course select element
                const courseSelect = document.getElementById('courseSelect');
                const selectedCourseId = courseSelect.value;
                const selectedPaymentMethodId = clickedCard.dataset.id;

                console.log('Payment method selected:', {
                    courseId: selectedCourseId,
                    paymentMethodId: selectedPaymentMethodId
                });

                // Validate course selection
                if (!selectedCourseId) {
                    showError('Please select a course first.');
                    clickedCard.classList.remove('selected');
                    return;
                }

                // Fetch course payment details with the selected payment method
                fetchCoursePaymentDetails(selectedCourseId, selectedPaymentMethodId);
            }

            // Fixed function to fetch course payment details
            async function fetchCoursePaymentDetails(courseId, paymentMethodId = null) {
                try {
                    console.log('Fetching payment details for:', {
                        courseId,
                        paymentMethodId
                    });

                    // Build URL with course ID as path parameter and payment method as query parameter
                    let url = `/api/course-payment-details/${courseId}`;
                    if (paymentMethodId) {
                        url += `?payment_method_id=${paymentMethodId}`;
                    }

                    console.log('Request URL:', url);

                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        }
                    });

                    console.log('Response status:', response.status);
                    console.log('Response headers:', Object.fromEntries(response.headers));

                    if (!response.ok) {
                        const errorData = await response.text();
                        console.error('Error response:', errorData);
                        throw new Error(`HTTP ${response.status}: Failed to fetch course payment details`);
                    }

                    const data = await response.json();
                    console.log('Payment details received:', data);

                    displayPaymentOptions(data);
                } catch (error) {
                    console.error('Error fetching payment details:', error);
                    showError('Failed to retrieve payment details.', error.message);
                }
            }

            // Updated function to display payment options with detailed breakdown
            function displayPaymentOptions(paymentData) {
                const paymentMethodsContainer = document.getElementById('paymentMethods');

                let paymentOptionsContainer = document.getElementById('paymentOptionsContainer');
                if (!paymentOptionsContainer) {
                    paymentOptionsContainer = document.createElement('div');
                    paymentOptionsContainer.id = 'paymentOptionsContainer';
                    paymentOptionsContainer.className = 'payment-options-container mt-3';
                    paymentMethodsContainer.after(paymentOptionsContainer);
                }

                paymentOptionsContainer.innerHTML = '';

                const course = paymentData.course;
                const originalPrice = course.original_price || course.course_price;
                const serviceCharges = course.service_charges || 0;
                const totalPrice = course.total_price || originalPrice;
                const paymentMethodName = course.payment_method_name || '';

                const paymentOptionsHTML = `
        <div class="payment-selection-container">
            <h5 class="mb-3">Payment Options for ${course.course_name}</h5>

            <!-- Price Breakdown -->
            <div class="price-breakdown mb-4 p-3 border rounded bg-light">
                <h6 class="mb-3">Price Breakdown</h6>
                <div class="row">
                    <div class="col-8">Course Price:</div>
                    <div class="col-4 text-end">$${originalPrice.toFixed(2)}</div>
                </div>
                ${serviceCharges > 0 ? `
                            <div class="row">
                                <div class="col-8">Service Charges${paymentMethodName ? ` (${paymentMethodName})` : ''}:</div>
                                <div class="col-4 text-end">$${serviceCharges.toFixed(2)}</div>
                            </div>
                            <hr class="my-2">
                            ` : ''}
                <div class="row fw-bold">
                    <div class="col-8">Total Amount:</div>
                    <div class="col-4 text-end">$${totalPrice.toFixed(2)}</div>
                </div>
            </div>

            ${paymentData.installment_plan ? `
                            <div class="payment-option mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_type"
                                           id="fullPaymentOption" value="full" checked>
                                    <label class="form-check-label" for="fullPaymentOption">
                                        <strong>Full Payment: $${totalPrice.toFixed(2)}</strong>
                                        <small class="text-muted d-block">Pay the complete amount now</small>
                                    </label>
                                </div>
                            </div>
                            <div class="payment-option mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_type"
                                           id="installmentPaymentOption" value="installment">
                                    <label class="form-check-label" for="installmentPaymentOption">
                                        <strong>Installment Plan</strong>
                                        <small class="text-muted d-block">Pay in ${paymentData.installment_plan.number_of_installments} installments</small>
                                    </label>
                                </div>
                                ${renderInstallmentDetails(paymentData.installment_plan, totalPrice, serviceCharges)}
                            </div>
                        ` : `
                            <div class="payment-option mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_type"
                                           id="fullPaymentOption" value="full" checked>
                                    <label class="form-check-label" for="fullPaymentOption">
                                        <strong>Full Payment: $${totalPrice.toFixed(2)}</strong>
                                        <small class="text-muted d-block">Pay the complete amount now</small>
                                    </label>
                                </div>
                            </div>
                        `}
        </div>
    `;

                paymentOptionsContainer.innerHTML = paymentOptionsHTML;

                // Add event listeners for payment type changes
                document.querySelectorAll('input[name="payment_type"]').forEach(radio => {
                    radio.addEventListener('change', handlePaymentTypeChange);
                });
            }

            // Updated function to render installment details
            function renderInstallmentDetails(installmentPlan, totalPrice, serviceCharges) {
                if (!installmentPlan) return '';

                const firstInstallmentPercentage = installmentPlan.first_installment_percentage;
                const numberOfInstallments = installmentPlan.number_of_installments;

                const firstInstallmentAmount = totalPrice * (firstInstallmentPercentage / 100);
                const remainingAmount = totalPrice - firstInstallmentAmount;
                const remainingInstallments = numberOfInstallments - 1;
                const remainingInstallmentAmount = remainingInstallments > 0 ?
                    remainingAmount / remainingInstallments : 0;

                const installmentDetails = [{
                        number: 1,
                        amount: firstInstallmentAmount,
                        percentage: firstInstallmentPercentage,
                        dueDate: calculateDueDate(0),
                        isFirst: true
                    },
                    ...Array.from({
                        length: remainingInstallments
                    }, (_, index) => ({
                        number: index + 2,
                        amount: remainingInstallmentAmount,
                        percentage: ((100 - firstInstallmentPercentage) / remainingInstallments)
                            .toFixed(2),
                        dueDate: calculateDueDate(index + 1),
                        isFirst: false
                    }))
                ];

                const installmentHTML = installmentDetails.map(installment => `
        <div class="row mb-2">
            <div class="col-6">
                <strong>Installment ${installment.number}${installment.isFirst ? ' (Today)' : ''}:</strong>
            </div>
            <div class="col-3 text-end">$${installment.amount.toFixed(2)}</div>
            <div class="col-3 text-end">
                <small class="text-muted">${installment.dueDate}</small>
            </div>
        </div>
    `).join('');

                return `
        <div id="installmentDetailsContainer" style="display: none;" class="mt-3 p-3 border rounded bg-light">
            <h6 class="mb-3">Installment Plan Details</h6>
            <div class="mb-3">
                <div class="row mb-2">
                    <div class="col-6"><strong>Total Amount:</strong></div>
                    <div class="col-6 text-end"><strong>$${totalPrice.toFixed(2)}</strong></div>
                </div>
                <div class="row mb-2">
                    <div class="col-6">Number of Installments:</div>
                    <div class="col-6 text-end">${numberOfInstallments}</div>
                </div>
            </div>
            <div class="installment-breakdown">
                <h6 class="mb-2">Payment Schedule:</h6>
                ${installmentHTML}
            </div>
            <div class="mt-3 p-2 bg-info bg-opacity-10 rounded">
                <small class="text-info">
                    <i class="fas fa-info-circle"></i>
                    ${serviceCharges > 0 ? 'Service charges are included in the first installment.' : 'No additional service charges.'}
                </small>
            </div>
        </div>
    `;
            }

            // Helper function to calculate due dates
            function calculateDueDate(installmentIndex) {
                const baseDate = new Date();
                baseDate.setMonth(baseDate.getMonth() + installmentIndex);

                return baseDate.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }

            function handlePaymentTypeChange(event) {
                const installmentDetailsContainer = document.getElementById('installmentDetailsContainer');

                if (event.target.value === 'installment') {
                    if (installmentDetailsContainer) {
                        installmentDetailsContainer.style.display = 'block';
                    }
                } else {
                    if (installmentDetailsContainer) {
                        installmentDetailsContainer.style.display = 'none';
                    }
                }
            }

            // Function to handle payment method change and refresh payment details
            function handlePaymentMethodChange(courseId) {
                const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked');
                if (selectedPaymentMethod && courseId) {
                    fetchCoursePaymentDetails(courseId, selectedPaymentMethod.value);
                }
            }

            // Add event listeners to payment method radio buttons
            document.addEventListener('DOMContentLoaded', function() {
                const paymentMethodInputs = document.querySelectorAll('input[name="payment_method"]');
                paymentMethodInputs.forEach(input => {
                    input.addEventListener('change', function() {
                        const courseId = document.querySelector('input[name="course_id"]')
                            ?.value;
                        if (courseId) {
                            handlePaymentMethodChange(courseId);
                        }
                    });
                });
            });
            // Modify form submission to include payment details
            // async function handleFormSubmission(event) {
            //     event.preventDefault();

            //     if (!validateCurrentStep()) return;

            //     const formData = new FormData(form);
            //     const selectedPayment = document.querySelector('.payment-method-card.selected');
            //     const paymentType = document.querySelector('input[name="payment_type"]:checked').value;

            //     if (selectedPayment) {
            //         formData.append('payment_method_id', selectedPayment.dataset.id);
            //         formData.append('payment_type', paymentType);
            //     }

            //     try {
            //         const response = await fetch('/api/process-payment', {
            //             method: 'POST',
            //             headers: {
            //                 'Content-Type': 'application/json',
            //                 'Accept': 'application/json',
            //                 'X-Requested-With': 'XMLHttpRequest'
            //             },
            //             body: JSON.stringify(Object.fromEntries(formData))
            //         });

            //         const result = await response.json();

            //         if (response.ok) {
            //             if (result.checkout_url) {
            //                 // Redirect to payment gateway
            //                 window.location.href = result.checkout_url;
            //             } else {
            //                 showSuccess('Registration successful!');
            //                 setTimeout(() => {
            //                     window.location.href = '/';
            //                 }, 2000);
            //             }
            //         } else {
            //             throw new Error(result.message || 'Registration failed');
            //         }
            //     } catch (error) {
            //         console.error('Error submitting form:', error);
            //         showError(error.message || 'Registration failed. Please try again.');
            //     }
            // }


            async function handleFormSubmission(event) {
                event.preventDefault();

                clearAllErrorMessages();

                if (!validateCurrentStep()) {
                    console.log('Current step validation failed');
                    return;
                }

                const formData = new FormData(form);
                const selectedPayment = document.querySelector('.payment-method-card.selected');
                const paymentType = document.querySelector('input[name="payment_type"]:checked').value;

                try {
                    // Step 1: Submit Student Record
                    const studentResponse = await fetch('/api/submit-student', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            first_name: formData.get('first_name'),
                            last_name: formData.get('last_name'),
                            email: formData.get('email'),
                            student_contact: formData.get('student_contact'),
                            student_dob: formData.get('student_dob'),
                            address: formData.get('address'),
                            course_id: formData.get('course_id'),
                            instructor_id: formData.get('instructor_id'),
                            schedule_id: formData.get('schedule_id'),
                            payment_type: formData.get('payment_type')
                        })
                    });
                    console.log('Full Student Registration Response:', {
                        status: studentResponse.status,
                        ok: studentResponse.ok,
                        headers: Object.fromEntries(studentResponse.headers),
                    });

                    const studentResult = await studentResponse.json();
                    console.log('Student Registration Result:', studentResult);

                    if (!studentResponse.ok || studentResult.success === false) {

                        if (studentResponse.status === 422 && studentResult.errors) {
                            displayValidationErrors(studentResult.errors);
                            return;
                        }

                        const errMsg = studentResult.message || 'Student registration failed. Please try again.';
                        const errDetail = studentResult.error_details || null;
                        showError(errMsg, errDetail);
                        return;
                    }

                    // Store the student ID for subsequent payment processing
                    const studentId = studentResult.student_id;

                    // Prepare payment data
                    if (selectedPayment) {
                        formData.append('student_id', studentId);
                        formData.append('payment_method_id', selectedPayment.dataset.id);
                        formData.append('payment_type', paymentType);
                    }

                    // Step 2: Process Payment
                    const paymentResponse = await fetch('/api/process-payment', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            ...Object.fromEntries(formData),
                            student_id: studentId
                        })
                    });

                    const paymentResult = await paymentResponse.json();

                    if (!paymentResponse.ok || paymentResult.success === false) {
                        const errMsg = paymentResult.message || 'Payment processing failed. Please try again.';
                        const errDetail = paymentResult.error_details || null;
                        showError(errMsg, errDetail);
                        return;
                    }

                    // Handle successful payment
                    if (paymentResult.checkout_url) {
                        // Redirect to payment gateway
                        window.location.href = paymentResult.checkout_url;
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Successful!',
                            html: '<p>Your registration and payment were completed successfully.</p><p class="text-muted mt-2">Your login credentials have been sent to your email.</p>',
                            confirmButtonColor: '#1D4C5C',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = '/';
                        });
                    }

                } catch (error) {
                    console.error('Error during registration:', error);
                    showError(error.message || 'Registration failed. Please try again.');
                }
            }

            function displayValidationErrors(errors) {
                console.log('Displaying Validation Errors:', errors);

                // Clear any existing error messages first
                clearAllErrorMessages();

                // Collect all error messages for the alert
                const allErrorMessages = [];

                // Detailed mapping of error fields
                const fieldMap = {
                    'first_name': 'input[name="first_name"]',
                    'last_name': 'input[name="last_name"]',
                    'email': 'input[name="email"]',
                    'student_contact': 'input[name="student_contact"]',
                    'student_dob': 'input[name="student_dob"]',
                    'address': 'input[name="address"]',
                    'course_id': '#courseSelect',
                    'instructor_id': '#instructorSelect',
                    'schedule_id': '#scheduleSelect'
                };

                // Prepare a friendly field name mapping
                const friendlyFieldNames = {
                    'first_name': 'First Name',
                    'last_name': 'Last Name',
                    'email': 'Email',
                    'student_contact': 'Contact Number',
                    'student_dob': 'Date of Birth',
                    'address': 'Address',
                    'course_id': 'Course',
                    'instructor_id': 'Instructor',
                    'schedule_id': 'Schedule'
                };

                // Iterate through validation errors and display them
                Object.keys(errors).forEach(field => {
                    console.log(`Processing error for field: ${field}`);

                    const selector = fieldMap[field];
                    if (selector) {
                        const inputElement = document.querySelector(selector);
                        if (inputElement) {
                            console.log(`Found input element for ${field}:`, inputElement);

                            // Add error styling
                            inputElement.classList.add('is-invalid');

                            // Collect error messages
                            const errorMessages = errors[field];
                            const friendlyFieldName = friendlyFieldNames[field] || field;

                            // Format error messages
                            const formattedErrors = Array.isArray(errorMessages) ?
                                errorMessages.map(msg => `${friendlyFieldName}: ${msg}`) : [
                                    `${friendlyFieldName}: ${errorMessages}`
                                ];

                            allErrorMessages.push(...formattedErrors);

                            // Create and append error message
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-feedback d-block';
                            errorDiv.textContent = formattedErrors[0];

                            // Insert error message after the input
                            const parentContainer = inputElement.closest('.col-12') || inputElement
                                .parentNode;
                            parentContainer.appendChild(errorDiv);

                            console.log(`Added error message for ${field}:`, errorDiv);
                        } else {
                            console.warn(`No input element found for selector: ${selector}`);
                        }
                    } else {
                        console.warn(`No selector found for field: ${field}`);
                    }
                });

                // Update the alert with all error messages
                const alertContainer = document.querySelector('.alert-warning');
                if (alertContainer) {
                    // Create a list of error messages
                    const errorList = document.createElement('ul');
                    errorList.style.paddingLeft = '20px';
                    errorList.style.marginTop = '10px';

                    allErrorMessages.forEach(msg => {
                        const listItem = document.createElement('li');
                        listItem.textContent = msg;
                        errorList.appendChild(listItem);
                    });

                    // Update alert content
                    alertContainer.innerHTML = `
                    <strong>Validation Errors!</strong> Please check the following issues:
                    `;
                    alertContainer.appendChild(errorList);
                    alertContainer.style.display = 'block';
                }

                // Scroll to the first error
                const firstErrorElement = document.querySelector('.is-invalid');
                if (firstErrorElement) {
                    console.log('Scrolling to first error element');
                    firstErrorElement.focus();
                    firstErrorElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                } else {
                    console.warn('No error elements found to scroll to');
                }
            }

            function clearAllErrorMessages() {
                // Remove all existing invalid styling and error messages
                document.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });

                // Remove all error message divs
                document.querySelectorAll('.invalid-feedback').forEach(el => {
                    el.remove();
                });

                // Hide the alert container
                const alertContainer = document.querySelector('.alert-warning');
                if (alertContainer) {
                    alertContainer.style.display = 'none';
                }
            }

            // Utility Functions
            function displayCourseDetails(course) {
                const courseInfo = document.getElementById('selectedCourseInfo');
                const courseDetails = document.getElementById('courseDetails');

                // Capitalize first letter of course type
                const courseType = course.course_type.charAt(0).toUpperCase() + course.course_type.slice(1)
                    .toLowerCase();

                // Build duration string based on available hours
                let durationText = '';
                if (course.theory_hours > 0 && course.practical_hours > 0) {
                    durationText = `Theory: ${course.theory_hours}hrs, Practical: ${course.practical_hours}hrs`;
                } else if (course.theory_hours > 0) {
                    durationText = `Theory: ${course.theory_hours}hrs`;
                } else if (course.practical_hours > 0) {
                    durationText = `Practical: ${course.practical_hours}hrs`;
                } else {
                    durationText = 'Duration not specified';
                }

                courseDetails.innerHTML = `
        <p><strong>Course Type:</strong> ${courseType}</p>
        <p><strong>Duration:</strong> ${durationText}</p>
        <p><strong>Price:</strong> $${course.course_price}</p>
        <p><strong>Description:</strong> ${course.description || 'No description available'}</p>
    `;

                courseInfo.style.display = 'block';
            }

            function hideCourseDetails() {
                document.getElementById('selectedCourseInfo').style.display = 'none';
            }

            function showError(message, detail = null) {
                Swal.fire({
                    icon: 'error',
                    title: 'Something went wrong',
                    text: message,
                    footer: detail ? `<small class="text-muted">${detail}</small>` : '',
                    confirmButtonColor: '#1D4C5C'
                });
            }

            function showSuccess(message, subtext = '') {
                Swal.fire({
                    icon: 'success',
                    title: 'Registration Successful!',
                    text: message,
                    html: subtext ? `<p>${message}</p><p class="text-muted mt-2">${subtext}</p>` : message,
                    confirmButtonColor: '#1D4C5C',
                    confirmButtonText: 'OK'
                });
            }
        });
    </script>

</body>

</html>
