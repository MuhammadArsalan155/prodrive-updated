@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="pd-page-header d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-1" style="font-weight:800;"><i class="fas fa-book mr-2"></i>Add New Course</h4>
                <p style="font-size:.85rem;">Create a new driving course with theory and practical structure</p>
            </div>
            <a href="{{ route('viewcourse') }}" class="btn btn-light btn-sm font-weight-bold">
                <i class="fas fa-list mr-1"></i>View Courses
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header bg-gradient-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="m-0 font-weight-bold">
                                <i class="fas fa-book-open mr-2"></i>Course Information
                            </h5>
                        </div>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-exclamation-triangle mr-2"></i>Validation Error!</strong> Please
                                check the following issues:
                                <ul class="mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <form action="{{ route('add_course') }}" method="POST" id="courseForm">
                            @csrf

                            <input type="hidden" name="theory_lesson_plans_placeholder" value="placeholder">
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                                    <label for="course_name" class="form-label">
                                        <i class="fas fa-tag text-primary mr-2"></i>Course Name <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-book"></i></span>
                                        </div>
                                        <input type="text" placeholder="Enter Course Name" name="course_name"
                                            id="course_name" class="form-control @error('course_name') is-invalid @enderror"
                                            value="{{ old('course_name') }}" required maxlength="255">
                                        @error('course_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                                    <label for="course_price" class="form-label">
                                        <i class="fas fa-dollar-sign text-primary mr-2"></i>Course Price ($) <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" step="0.01" placeholder="Enter Course Price"
                                            name="course_price" id="course_price"
                                            class="form-control @error('course_price') is-invalid @enderror"
                                            value="{{ old('course_price') }}" min="0" max="100000" required>
                                        @error('course_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                                    <label for="course_type" class="form-label">
                                        <i class="fas fa-list-alt text-primary mr-2"></i>Course Type <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                                        </div>
                                        <select name="course_type" id="course_type"
                                            class="form-control @error('course_type') is-invalid @enderror" required>
                                            <option value="">Select Course Type</option>
                                            <option value="theory" {{ old('course_type') == 'theory' ? 'selected' : '' }}>
                                                Theory</option>
                                            <option value="practical"
                                                {{ old('course_type') == 'practical' ? 'selected' : '' }}>Practical</option>
                                            <option value="hybrid" {{ old('course_type') == 'hybrid' ? 'selected' : '' }}>
                                                Hybrid</option>
                                        </select>
                                        @error('course_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Theory Fields Container -->
                                <div id="theory-fields" class="col-12" style="display: none;">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                            <label for="theory_hours" class="form-label">
                                                <i class="fas fa-clock text-primary mr-2"></i> Theory Class Duration <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-chalkboard"></i></span>
                                                </div>
                                                <input type="number" placeholder="Enter Theory Hours" name="theory_hours"
                                                    id="theory_hours"
                                                    class="form-control @error('theory_hours') is-invalid @enderror"
                                                    value="{{ old('theory_hours', 0) }}" min="0" max="1000">
                                                @error('theory_hours')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                            <label for="total_theory_classes" class="form-label">
                                                <i class="fas fa-calendar-alt text-primary mr-2"></i>Total Theory Classes
                                                <span class="badge badge-info ml-1" style="font-size:.7rem;font-weight:600;">
                                                    <i class="fas fa-magic mr-1"></i>Auto (hrs ÷ 5)
                                                </span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="fas fa-chalkboard-teacher"></i></span>
                                                </div>
                                                <input type="number"
                                                    name="total_theory_classes" id="total_theory_classes"
                                                    class="form-control @error('total_theory_classes') is-invalid @enderror"
                                                    value="{{ old('total_theory_classes', 0) }}" min="0"
                                                    max="1000" readonly
                                                    style="background:#f0f9ff;cursor:not-allowed;"
                                                    title="Auto-calculated: 1 class = 5 theory hours">
                                                @error('total_theory_classes')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <small class="text-muted" id="theory_classes_hint" style="font-size:.78rem;"></small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Practical Fields Container -->
                                <div id="practical-fields" class="col-12" style="display: none;">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                            <label for="practical_hours" class="form-label">
                                                <i class="fas fa-tools text-primary mr-2"></i>Practical Hours <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-wrench"></i></span>
                                                </div>
                                                <input type="number" placeholder="Enter Practical Hours"
                                                    name="practical_hours" id="practical_hours"
                                                    class="form-control @error('practical_hours') is-invalid @enderror"
                                                    value="{{ old('practical_hours', 0) }}" min="0"
                                                    max="1000">
                                                @error('practical_hours')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                            <label for="total_practical_classes" class="form-label">
                                                <i class="fas fa-calendar-check text-primary mr-2"></i>Total Practical Classes
                                                <span class="badge badge-info ml-1" style="font-size:.7rem;font-weight:600;">
                                                    <i class="fas fa-magic mr-1"></i>Auto (hrs ÷ 2)
                                                </span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="fas fa-hands-helping"></i></span>
                                                </div>
                                                <input type="number"
                                                    name="total_practical_classes" id="total_practical_classes"
                                                    class="form-control @error('total_practical_classes') is-invalid @enderror"
                                                    value="{{ old('total_practical_classes', 0) }}" min="0"
                                                    max="1000" readonly
                                                    style="background:#f0f9ff;cursor:not-allowed;"
                                                    title="Auto-calculated: 1 class = 2 practical hours">
                                                @error('total_practical_classes')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <small class="text-muted" id="practical_classes_hint" style="font-size:.78rem;"></small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="has_installment_plan"
                                            name="has_installment_plan" value="1"
                                            {{ old('has_installment_plan') ? 'checked' : '' }}
                                            onchange="toggleInstallmentSection(this)">
                                        <label class="custom-control-label" for="has_installment_plan">
                                            <i class="fas fa-money-check-alt text-primary mr-2"></i>Offer Installment Plan
                                        </label>
                                    </div>
                                </div>

                                <div id="installment-section"
                                    class="col-12 {{ old('has_installment_plan') ? '' : 'd-none' }}">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label for="course_installment_plan_id" class="form-label">
                                                        <i class="fas fa-calendar-alt text-primary mr-2"></i>Select
                                                        Installment Plan
                                                    </label>
                                                    <select name="course_installment_plan_id"
                                                        id="course_installment_plan_id"
                                                        class="form-control @error('course_installment_plan_id') is-invalid @enderror">
                                                        <option value="">Select an Installment Plan</option>
                                                        @foreach ($installmentPlans as $plan)
                                                            <option value="{{ $plan->id }}"
                                                                {{ old('course_installment_plan_id') == $plan->id ? 'selected' : '' }}>
                                                                {{ $plan->Name }} ({{ $plan->number_of_installments }}
                                                                installments)
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('course_installment_plan_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label">
                                        <i class="fas fa-align-left text-primary mr-2"></i>Course Description
                                    </label>
                                    <textarea name="description" id="description" rows="4"
                                        class="form-control @error('description') is-invalid @enderror" placeholder="Enter Course Description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_active"
                                            name="is_active" value="1"
                                            {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            <i class="fas fa-power-off text-primary mr-2"></i>Course Active Status
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-plus-circle mr-2"></i>Create Course
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
        document.addEventListener('DOMContentLoaded', function() {
            // Get necessary elements
            const courseTypeSelect = document.getElementById('course_type');
            const theoryFieldsContainer = document.getElementById('theory-fields');
            const practicalFieldsContainer = document.getElementById('practical-fields');
            const theoryHoursInput = document.getElementById('theory_hours');
            const practicalHoursInput = document.getElementById('practical_hours');
            const totalTheoryClassesInput = document.getElementById('total_theory_classes');
            const totalPracticalClassesInput = document.getElementById('total_practical_classes');
            const courseForm = document.getElementById('courseForm');

            // Function to toggle field visibility based on course type
            function toggleFieldsBasedOnCourseType() {
                const courseType = courseTypeSelect.value;

                // Hide all fields initially
                theoryFieldsContainer.style.display = 'none';
                practicalFieldsContainer.style.display = 'none';

                // Clear and reset required attributes
                resetFieldRequirements();

                switch (courseType) {
                    case 'theory':
                        // Show only theory fields
                        theoryFieldsContainer.style.display = 'block';
                        setTheoryFieldsRequired(true);
                        // Clear practical values
                        practicalHoursInput.value = 0;
                        totalPracticalClassesInput.value = 0;
                        break;

                    case 'practical':
                        // Show only practical fields
                        practicalFieldsContainer.style.display = 'block';
                        setPracticalFieldsRequired(true);
                        // Clear theory values
                        theoryHoursInput.value = 0;
                        totalTheoryClassesInput.value = 0;
                        break;

                    case 'hybrid':
                        // Show both theory and practical fields
                        theoryFieldsContainer.style.display = 'block';
                        practicalFieldsContainer.style.display = 'block';
                        setTheoryFieldsRequired(true);
                        setPracticalFieldsRequired(true);
                        break;

                    default:
                        // No course type selected - hide all and clear values
                        theoryHoursInput.value = 0;
                        totalTheoryClassesInput.value = 0;
                        practicalHoursInput.value = 0;
                        totalPracticalClassesInput.value = 0;
                        break;
                }

                // Re-render lesson plan selections after field changes
                renderLessonPlanSelections();
            }

            // Function to reset all field requirements
            function resetFieldRequirements() {
                theoryHoursInput.removeAttribute('required');
                totalTheoryClassesInput.removeAttribute('required');
                practicalHoursInput.removeAttribute('required');
                totalPracticalClassesInput.removeAttribute('required');
            }

            // Function to set theory fields as required
            function setTheoryFieldsRequired(required) {
                if (required) {
                    theoryHoursInput.setAttribute('required', 'required');
                    totalTheoryClassesInput.setAttribute('required', 'required');
                } else {
                    theoryHoursInput.removeAttribute('required');
                    totalTheoryClassesInput.removeAttribute('required');
                }
            }

            // Function to set practical fields as required
            function setPracticalFieldsRequired(required) {
                if (required) {
                    practicalHoursInput.setAttribute('required', 'required');
                    totalPracticalClassesInput.setAttribute('required', 'required');
                } else {
                    practicalHoursInput.removeAttribute('required');
                    totalPracticalClassesInput.removeAttribute('required');
                }
            }

            // Function to create the lesson plan container
            function createLessonPlanContainer() {
                let container = document.getElementById('lesson-plan-container');

                if (container) {
                    container.innerHTML = '';
                    return container;
                }

                container = document.createElement('div');
                container.id = 'lesson-plan-container';
                container.className = 'col-12 mt-4 mb-4';

                const descriptionField = document.querySelector('[name="description"]');
                if (descriptionField && descriptionField.closest('.col-12')) {
                    const parent = descriptionField.closest('.col-12').parentNode;
                    parent.insertBefore(container, descriptionField.closest('.col-12'));
                } else {
                    const formRow = courseForm.querySelector('.row');
                    if (formRow) {
                        formRow.appendChild(container);
                    } else {
                        const submitBtn = courseForm.querySelector('button[type="submit"]');
                        if (submitBtn && submitBtn.closest('.col-12')) {
                            const parent = submitBtn.closest('.col-12').parentNode;
                            parent.insertBefore(container, submitBtn.closest('.col-12'));
                        } else {
                            courseForm.appendChild(container);
                        }
                    }
                }

                return container;
            }

            // Function to render lesson plan selections
            function renderLessonPlanSelections() {
                console.log("Rendering lesson plan selections...");

                const courseType = courseTypeSelect.value;
                const theoryClasses = parseInt(totalTheoryClassesInput.value) || 0;
                const practicalClasses = parseInt(totalPracticalClassesInput.value) || 0;

                console.log(
                    `Course Type: ${courseType}, Theory Classes: ${theoryClasses}, Practical Classes: ${practicalClasses}`
                    );

                const lessonPlanContainer = createLessonPlanContainer();

                if (theoryClasses <= 0 && practicalClasses <= 0) {
                    console.log("No classes to render lesson plans for");
                    return;
                }

                const hiddenField = document.createElement('input');
                hiddenField.type = 'hidden';
                hiddenField.name = 'has_lesson_plans';
                hiddenField.value = 'true';
                lessonPlanContainer.appendChild(hiddenField);

                const card = document.createElement('div');
                card.className = 'card shadow-lg border-0 rounded-lg mb-4';
                card.innerHTML = `
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="m-0 font-weight-bold">
                            <i class="fas fa-book-open mr-2"></i>Lesson Plan Selection
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            Select a lesson plan for each class. Each class must have one lesson plan assigned.
                        </div>
                        <div id="theory-lesson-plans-container" class="mb-4"></div>
                        <div id="practical-lesson-plans-container"></div>
                    </div>
                `;

                lessonPlanContainer.appendChild(card);

                if (theoryClasses > 0 && courseType !== 'practical') {
                    renderTheoryLessonPlans(theoryClasses);
                }

                if (practicalClasses > 0 && courseType !== 'theory') {
                    renderPracticalLessonPlans(practicalClasses);
                }
            }

            // Function to render theory lesson plans
            function renderTheoryLessonPlans(theoryClasses) {
                console.log(`Rendering ${theoryClasses} theory lesson plans`);

                const theoryContainer = document.getElementById('theory-lesson-plans-container');
                if (!theoryContainer) {
                    console.error("Theory container not found");
                    return;
                }

                theoryContainer.innerHTML = `
                    <h6 class="font-weight-bold text-info mb-3">
                        <i class="fas fa-chalkboard-teacher mr-2"></i>Theory Classes
                    </h6>
                    <div class="row" id="theory-lesson-plans-row"></div>
                `;

                const theoryRow = document.getElementById('theory-lesson-plans-row');

                for (let i = 0; i < theoryClasses; i++) {
                    const classNumber = i + 1;
                    const col = document.createElement('div');
                    col.className = 'col-md-6 col-lg-4 mb-3';

                    col.innerHTML = `
                        <label for="theory_lesson_plans_${i}" class="form-label">
                            <i class="fas fa-book text-info mr-2"></i>Class ${classNumber} Lesson Plan
                        </label>
                        <select name="theory_lesson_plans[${i}]" id="theory_lesson_plans_${i}" class="form-control">
                            <option value="">Select Lesson Plan</option>
                            @foreach ($lessonPlans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->title }}</option>
                            @endforeach
                        </select>
                    `;

                    theoryRow.appendChild(col);
                }
            }

            // Function to render practical lesson plans
            function renderPracticalLessonPlans(practicalClasses) {
                console.log(`Rendering ${practicalClasses} practical lesson plans`);

                const practicalContainer = document.getElementById('practical-lesson-plans-container');
                if (!practicalContainer) {
                    console.error("Practical container not found");
                    return;
                }

                practicalContainer.innerHTML = `
                    <h6 class="font-weight-bold text-warning mb-3">
                        <i class="fas fa-hands-helping mr-2"></i>Practical Classes
                    </h6>
                    <div class="row" id="practical-lesson-plans-row"></div>
                `;

                const practicalRow = document.getElementById('practical-lesson-plans-row');

                for (let i = 0; i < practicalClasses; i++) {
                    const classNumber = i + 1;
                    const col = document.createElement('div');
                    col.className = 'col-md-6 col-lg-4 mb-3';

                    col.innerHTML = `
                        <label for="practical_lesson_plans_${i}" class="form-label">
                            <i class="fas fa-tools text-warning mr-2"></i>Class ${classNumber} Lesson Plan
                        </label>
                        <select name="practical_lesson_plans[${i}]" id="practical_lesson_plans_${i}" class="form-control">
                            <option value="">Select Lesson Plan</option>
                            @foreach ($lessonPlans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->title }}</option>
                            @endforeach
                        </select>
                    `;

                    practicalRow.appendChild(col);
                }
            }

            // ── Auto-calculate class counts from hours ─────────────────────────
            const theoryHint    = document.getElementById('theory_classes_hint');
            const practicalHint = document.getElementById('practical_classes_hint');

            function calcTheoryClasses() {
                const hrs = parseFloat(theoryHoursInput.value) || 0;
                const classes = hrs > 0 ? Math.floor(hrs / 5) : 0;
                totalTheoryClassesInput.value = classes;
                theoryHint.textContent = hrs > 0
                    ? `${hrs} hrs ÷ 5 = ${classes} class${classes !== 1 ? 'es' : ''}`
                    : '';
                renderLessonPlanSelections();
            }

            function calcPracticalClasses() {
                const hrs = parseFloat(practicalHoursInput.value) || 0;
                const classes = hrs > 0 ? Math.floor(hrs / 2) : 0;
                totalPracticalClassesInput.value = classes;
                practicalHint.textContent = hrs > 0
                    ? `${hrs} hrs ÷ 2 = ${classes} class${classes !== 1 ? 'es' : ''}`
                    : '';
                renderLessonPlanSelections();
            }

            theoryHoursInput.addEventListener('input',  calcTheoryClasses);
            theoryHoursInput.addEventListener('change', calcTheoryClasses);
            practicalHoursInput.addEventListener('input',  calcPracticalClasses);
            practicalHoursInput.addEventListener('change', calcPracticalClasses);

            // Add event listeners
            courseTypeSelect.addEventListener('change', toggleFieldsBasedOnCourseType);

            totalTheoryClassesInput.addEventListener('change', renderLessonPlanSelections);
            totalTheoryClassesInput.addEventListener('input', renderLessonPlanSelections);
            totalTheoryClassesInput.addEventListener('keyup', renderLessonPlanSelections);

            totalPracticalClassesInput.addEventListener('change', renderLessonPlanSelections);
            totalPracticalClassesInput.addEventListener('input', renderLessonPlanSelections);
            totalPracticalClassesInput.addEventListener('keyup', renderLessonPlanSelections);

            // Initialize field visibility on page load
            toggleFieldsBasedOnCourseType();

            // Form validation
            courseForm.addEventListener('submit', function(event) {
                const courseType = courseTypeSelect.value;
                const theoryHours = parseInt(theoryHoursInput.value) || 0;
                const practicalHours = parseInt(practicalHoursInput.value) || 0;
                const theoryClasses = parseInt(totalTheoryClassesInput.value) || 0;
                const practicalClasses = parseInt(totalPracticalClassesInput.value) || 0;

                console.log("Form submission attempted with:");
                console.log(`Course Type: ${courseType}`);
                console.log(`Theory Hours: ${theoryHours}, Practical Hours: ${practicalHours}`);
                console.log(`Theory Classes: ${theoryClasses}, Practical Classes: ${practicalClasses}`);

                // Enhanced validation based on course type
                if (courseType === 'theory') {
                    if (theoryHours <= 0 || theoryClasses <= 0) {
                        event.preventDefault();
                        alert('Theory courses must have theory hours and theory classes greater than 0.');
                        return false;
                    }
                } else if (courseType === 'practical') {
                    if (practicalHours <= 0 || practicalClasses <= 0) {
                        event.preventDefault();
                        alert(
                            'Practical courses must have practical hours and practical classes greater than 0.');
                        return false;
                    }
                } else if (courseType === 'hybrid') {
                    if ((theoryHours <= 0 || theoryClasses <= 0) && (practicalHours <= 0 ||
                            practicalClasses <= 0)) {
                        event.preventDefault();
                        alert(
                            'Hybrid courses must have at least one theory or practical component with hours and classes greater than 0.');
                        return false;
                    }
                }

                // Validate lesson plans
                if (theoryClasses > 0) {
                    const theorySelects = document.querySelectorAll('select[name^="theory_lesson_plans"]');
                    let emptyCount = 0;
                    let emptyClasses = [];

                    theorySelects.forEach((select, index) => {
                        if (!select.value) {
                            emptyCount++;
                            emptyClasses.push(index + 1);
                        }
                    });

                    if (emptyCount > 0) {
                        event.preventDefault();
                        alert(`Please select lesson plans for theory classes: ${emptyClasses.join(', ')}`);
                        return false;
                    }
                }

                if (practicalClasses > 0) {
                    const practicalSelects = document.querySelectorAll(
                        'select[name^="practical_lesson_plans"]');
                    let emptyCount = 0;
                    let emptyClasses = [];

                    practicalSelects.forEach((select, index) => {
                        if (!select.value) {
                            emptyCount++;
                            emptyClasses.push(index + 1);
                        }
                    });

                    if (emptyCount > 0) {
                        event.preventDefault();
                        alert(
                            `Please select lesson plans for practical classes: ${emptyClasses.join(', ')}`);
                        return false;
                    }
                }
            });
        });

        function toggleInstallmentSection(checkbox) {
            const installmentSection = document.getElementById('installment-section');
            const installmentPlanSelect = document.getElementById('course_installment_plan_id');

            if (checkbox.checked) {
                installmentSection.classList.remove('d-none');
                installmentPlanSelect.required = true;
            } else {
                installmentSection.classList.add('d-none');
                installmentPlanSelect.required = false;
                installmentPlanSelect.selectedIndex = 0;
            }
        }
    </script>
@endsection
