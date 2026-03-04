@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit text-primary mr-2"></i>Edit Course
            </h1>
            <div class="btn-group">
                <a href="{{ route('viewcourse') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye mr-1"></i>View Courses
                </a>
                <a href="{{ route('addcourse') }}" class="btn btn-outline-success btn-sm ml-2">
                    <i class="fas fa-plus mr-1"></i>Add New Course
                </a>
            </div>
        </div>
        <!-- Content Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12">
                <div class="card shadow-lg border-0 rounded-lg">
                    <!-- Card Header -->
                    <div
                        class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold">
                            <i class="fas fa-book-open mr-2"></i>Course Details
                        </h5>
                        <span class="badge {{ $course->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $course->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-exclamation-triangle mr-2"></i>Validation Error!</strong> Please
                                correct the following issues:
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

                        <form action="{{ route('update_course') }}" method="POST" id="editCourseForm">
                            @csrf
                            <input type="hidden" name="id" value="{{ $course->id }}">
                            <input type="hidden" name="theory_lesson_plans_placeholder" value="placeholder">
                            <input type="hidden" name="practical_lesson_plans_placeholder" value="placeholder">
                            <div class="row">
                                {{-- Course Name --}}
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
                                            value="{{ old('course_name', $course->course_name) }}" required maxlength="255">
                                        @error('course_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Course Price --}}
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
                                            value="{{ old('course_price', $course->course_price) }}" min="0"
                                            max="100000" required>
                                        @error('course_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Course Type --}}
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
                                            @php
                                                $courseTypes = ['theory', 'practical', 'hybrid'];
                                            @endphp
                                            @foreach ($courseTypes as $type)
                                                <option value="{{ $type }}"
                                                    {{ old('course_type', $course->course_type) == $type ? 'selected' : '' }}>
                                                    {{ ucfirst($type) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('course_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Theory Fields Container --}}
                                <div id="theory-fields-container" class="col-12" style="display: none;">
                                    <div class="row">
                                        {{-- Theory Hours --}}
                                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                            <label for="theory_hours" class="form-label">
                                                <i class="fas fa-clock text-primary mr-2"></i> Theory Class Duration <span
                                                    class="text-danger theory-required">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="fas fa-chalkboard"></i></span>
                                                </div>
                                                <input type="number" placeholder="Enter Theory Hours"
                                                    name="theory_hours" id="theory_hours"
                                                    class="form-control @error('theory_hours') is-invalid @enderror"
                                                    value="{{ old('theory_hours', $course->theory_hours) }}"
                                                    min="0" max="1000">
                                                @error('theory_hours')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Total Theory Classes --}}
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
                                                    value="{{ old('total_theory_classes', $course->total_theory_classes ?? 0) }}"
                                                    min="0" max="1000" readonly
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

                                {{-- Practical Fields Container --}}
                                <div id="practical-fields-container" class="col-12" style="display: none;">
                                    <div class="row">
                                        {{-- Practical Hours --}}
                                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                            <label for="practical_hours" class="form-label">
                                                <i class="fas fa-tools text-primary mr-2"></i>Practical Hours <span
                                                    class="text-danger practical-required">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-wrench"></i></span>
                                                </div>
                                                <input type="number" placeholder="Enter Practical Hours"
                                                    name="practical_hours" id="practical_hours"
                                                    class="form-control @error('practical_hours') is-invalid @enderror"
                                                    value="{{ old('practical_hours', $course->practical_hours) }}"
                                                    min="0" max="1000">
                                                @error('practical_hours')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Total Practical Classes --}}
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
                                                    value="{{ old('total_practical_classes', $course->total_practical_classes ?? 0) }}"
                                                    min="0" max="1000" readonly
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

                                {{-- Installment Plan Toggle --}}
                                <div class="col-12 mb-3">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="has_installment_plan"
                                            name="has_installment_plan" value="1"
                                            {{ old('has_installment_plan', $course->has_installment_plan) ? 'checked' : '' }}
                                            onchange="toggleInstallmentSection(this)">
                                        <label class="custom-control-label" for="has_installment_plan">
                                            <i class="fas fa-money-check-alt text-primary mr-2"></i>Offer Installment Plan
                                        </label>
                                    </div>
                                </div>

                                {{-- Installment Plan Selection --}}
                                <div id="installment-section"
                                    class="col-12 {{ old('has_installment_plan', $course->has_installment_plan) ? '' : 'd-none' }}">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <div class="row">
                                                {{-- Installment Plan Dropdown --}}
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
                                                                {{ old('course_installment_plan_id', $course->course_installment_plan_id) == $plan->id ? 'selected' : '' }}>
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

                                {{-- Description --}}
                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label">
                                        <i class="fas fa-align-left text-primary mr-2"></i>Course Description
                                    </label>
                                    <textarea name="description" id="description" rows="4"
                                        class="form-control @error('description') is-invalid @enderror" placeholder="Enter Course Description">{{ old('description', $course->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Active Status --}}
                                <div class="col-12 mb-3">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_active"
                                            name="is_active" value="1"
                                            {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            <i class="fas fa-power-off text-primary mr-2"></i>Course Active Status
                                        </label>
                                    </div>
                                </div>

                                {{-- Submit Buttons --}}
                                <div class="col-12 mt-3">
                                    <div class="row">
                                        <div class="col-md-6 mb-2 mb-md-0">
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-save mr-2"></i>Update Course
                                            </button>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="{{ route('viewcourse') }}" class="btn btn-secondary btn-block">
                                                <i class="fas fa-times mr-2"></i>Cancel
                                            </a>
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
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get necessary elements
            const courseTypeSelect = document.getElementById('course_type');
            const theoryHoursInput = document.getElementById('theory_hours');
            const practicalHoursInput = document.getElementById('practical_hours');
            const totalTheoryClassesInput = document.getElementById('total_theory_classes');
            const totalPracticalClassesInput = document.getElementById('total_practical_classes');
            const editCourseForm = document.getElementById('editCourseForm');

            // Get field containers
            const theoryFieldsContainer = document.getElementById('theory-fields-container');
            const practicalFieldsContainer = document.getElementById('practical-fields-container');

            // Create arrays to hold selected lesson plans
            const selectedTheoryLessonPlans = [];
            const selectedPracticalLessonPlans = [];

            // Populate arrays with existing selected lesson plans from the course
            @foreach ($course->theoryLessonPlans as $lessonPlan)
                selectedTheoryLessonPlans.push({
                    order: {{ $lessonPlan->pivot->class_order }},
                    id: {{ $lessonPlan->id }}
                });
            @endforeach

            @foreach ($course->practicalLessonPlans as $lessonPlan)
                selectedPracticalLessonPlans.push({
                    order: {{ $lessonPlan->pivot->class_order }},
                    id: {{ $lessonPlan->id }}
                });
            @endforeach

            console.log("Selected theory lesson plans:", selectedTheoryLessonPlans);
            console.log("Selected practical lesson plans:", selectedPracticalLessonPlans);

            // Function to toggle field visibility based on course type
            function toggleFieldVisibility() {
                const courseType = courseTypeSelect.value;
                console.log("Course type changed to:", courseType);

                // Hide all field containers initially
                theoryFieldsContainer.style.display = 'none';
                practicalFieldsContainer.style.display = 'none';

                // Clear required attributes and reset field values for hidden fields
                resetFieldRequirements();

                switch (courseType) {
                    case 'theory':
                        theoryFieldsContainer.style.display = 'block';
                        setTheoryFieldsRequired(true);
                        // Reset practical fields
                        resetPracticalFields();
                        break;

                    case 'practical':
                        practicalFieldsContainer.style.display = 'block';
                        setPracticalFieldsRequired(true);
                        // Reset theory fields
                        resetTheoryFields();
                        break;

                    case 'hybrid':
                        theoryFieldsContainer.style.display = 'block';
                        practicalFieldsContainer.style.display = 'block';
                        setTheoryFieldsRequired(true);
                        setPracticalFieldsRequired(true);
                        break;

                    default:
                        // No course type selected, hide all fields
                        resetTheoryFields();
                        resetPracticalFields();
                        break;
                }

                // Re-render lesson plans after field visibility changes
                renderLessonPlanSelections();
            }

            // Function to reset all field requirements
            function resetFieldRequirements() {
                theoryHoursInput.removeAttribute('required');
                practicalHoursInput.removeAttribute('required');

                // Hide required asterisks
                document.querySelectorAll('.theory-required').forEach(el => el.style.display = 'none');
                document.querySelectorAll('.practical-required').forEach(el => el.style.display = 'none');
            }

            // Function to set theory fields as required
            function setTheoryFieldsRequired(required) {
                if (required) {
                    theoryHoursInput.setAttribute('required', 'required');
                    document.querySelectorAll('.theory-required').forEach(el => el.style.display = 'inline');
                } else {
                    theoryHoursInput.removeAttribute('required');
                    document.querySelectorAll('.theory-required').forEach(el => el.style.display = 'none');
                }
            }

            // Function to set practical fields as required
            function setPracticalFieldsRequired(required) {
                if (required) {
                    practicalHoursInput.setAttribute('required', 'required');
                    document.querySelectorAll('.practical-required').forEach(el => el.style.display = 'inline');
                } else {
                    practicalHoursInput.removeAttribute('required');
                    document.querySelectorAll('.practical-required').forEach(el => el.style.display = 'none');
                }
            }

            // Function to reset theory fields
            function resetTheoryFields() {
                theoryHoursInput.value = '0';
                totalTheoryClassesInput.value = '0';
            }

            // Function to reset practical fields
            function resetPracticalFields() {
                practicalHoursInput.value = '0';
                totalPracticalClassesInput.value = '0';
            }

            // Function to create the lesson plan container
            function createLessonPlanContainer() {
                // First check if it already exists
                let container = document.getElementById('lesson-plan-container');

                // If it exists, clear its contents instead of removing it
                if (container) {
                    container.innerHTML = '';
                    return container;
                }

                // If it doesn't exist, create it
                container = document.createElement('div');
                container.id = 'lesson-plan-container';
                container.className = 'col-12 mt-4 mb-4';

                // Insert before description field
                const descriptionField = document.querySelector('[name="description"]');
                if (descriptionField && descriptionField.closest('.col-12')) {
                    const parent = descriptionField.closest('.col-12').parentNode;
                    parent.insertBefore(container, descriptionField.closest('.col-12'));
                } else {
                    // Fallback - add to the end of the form's row
                    const formRow = editCourseForm.querySelector('.row');
                    if (formRow) {
                        formRow.appendChild(container);
                    } else {
                        // Ultimate fallback - add right before the submit button
                        const submitBtn = editCourseForm.querySelector('button[type="submit"]');
                        if (submitBtn && submitBtn.closest('.col-12')) {
                            const parent = submitBtn.closest('.col-12').parentNode;
                            parent.insertBefore(container, submitBtn.closest('.col-12'));
                        } else {
                            // If all else fails, just append to the form
                            editCourseForm.appendChild(container);
                        }
                    }
                }

                return container;
            }

            // Function to get the selected lesson plan ID for a specific class
            function getSelectedTheoryPlanId(classOrder) {
                const plan = selectedTheoryLessonPlans.find(p => p.order === classOrder);
                return plan ? plan.id : '';
            }

            function getSelectedPracticalPlanId(classOrder) {
                const plan = selectedPracticalLessonPlans.find(p => p.order === classOrder);
                return plan ? plan.id : '';
            }

            // Function to render lesson plan selections
            function renderLessonPlanSelections() {
                console.log("Rendering lesson plan selections...");

                // Get current values
                const courseType = courseTypeSelect.value;
                const theoryClasses = parseInt(totalTheoryClassesInput.value) || 0;
                const practicalClasses = parseInt(totalPracticalClassesInput.value) || 0;

                console.log(
                    `Course Type: ${courseType}, Theory Classes: ${theoryClasses}, Practical Classes: ${practicalClasses}`
                );

                // Create/get container
                const lessonPlanContainer = createLessonPlanContainer();

                // If no classes or no course type selected, leave the container empty
                if (!courseType || (theoryClasses <= 0 && practicalClasses <= 0)) {
                    console.log("No classes to render lesson plans for");
                    return;
                }

                // Add a hidden field to ensure the lesson plan arrays are submitted
                const hiddenField = document.createElement('input');
                hiddenField.type = 'hidden';
                hiddenField.name = 'has_lesson_plans';
                hiddenField.value = 'true';
                lessonPlanContainer.appendChild(hiddenField);

                // Create card for lesson plans
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

                // Add theory lesson plans if needed and course type allows it
                if (theoryClasses > 0 && (courseType === 'theory' || courseType === 'hybrid')) {
                    renderTheoryLessonPlans(theoryClasses);
                }

                // Add practical lesson plans if needed and course type allows it
                if (practicalClasses > 0 && (courseType === 'practical' || courseType === 'hybrid')) {
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

                    // Get previously selected plan ID for this class
                    const selectedPlanId = getSelectedTheoryPlanId(classNumber);
                    console.log(`Class ${classNumber} selected theory plan: ${selectedPlanId}`);

                    col.innerHTML = `
                <label for="theory_lesson_plans_${i}" class="form-label">
                    <i class="fas fa-book text-info mr-2"></i>Class ${classNumber} Lesson Plan
                </label>
                <select name="theory_lesson_plans[${i}]" id="theory_lesson_plans_${i}" class="form-control theory-lesson-plan-select" data-class-order="${classNumber}">
                    <option value="">Select Lesson Plan</option>
                    @foreach ($lessonPlans as $plan)
                        <option value="{{ $plan->id }}" ${selectedPlanId == {{ $plan->id }} ? 'selected' : ''}>
                            {{ $plan->title }}
                        </option>
                    @endforeach
                </select>
            `;

                    theoryRow.appendChild(col);
                }

                // Set selected values after DOM elements are created
                setTimeout(() => {
                    document.querySelectorAll('.theory-lesson-plan-select').forEach((select) => {
                        const classOrder = parseInt(select.dataset.classOrder);
                        const selectedId = getSelectedTheoryPlanId(classOrder);
                        if (selectedId) {
                            select.value = selectedId;
                            console.log(
                                `Set theory select for class ${classOrder} to ${selectedId}`);
                        }
                    });
                }, 0);
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

                    // Get previously selected plan ID for this class
                    const selectedPlanId = getSelectedPracticalPlanId(classNumber);
                    console.log(`Class ${classNumber} selected practical plan: ${selectedPlanId}`);

                    col.innerHTML = `
                <label for="practical_lesson_plans_${i}" class="form-label">
                    <i class="fas fa-tools text-warning mr-2"></i>Class ${classNumber} Lesson Plan
                </label>
                <select name="practical_lesson_plans[${i}]" id="practical_lesson_plans_${i}" class="form-control practical-lesson-plan-select" data-class-order="${classNumber}">
                    <option value="">Select Lesson Plan</option>
                    @foreach ($lessonPlans as $plan)
                        <option value="{{ $plan->id }}" ${selectedPlanId == {{ $plan->id }} ? 'selected' : ''}>
                            {{ $plan->title }}
                        </option>
                    @endforeach
                </select>
            `;

                    practicalRow.appendChild(col);
                }

                // Set selected values after DOM elements are created
                setTimeout(() => {
                    document.querySelectorAll('.practical-lesson-plan-select').forEach((select) => {
                        const classOrder = parseInt(select.dataset.classOrder);
                        const selectedId = getSelectedPracticalPlanId(classOrder);
                        if (selectedId) {
                            select.value = selectedId;
                            console.log(
                                `Set practical select for class ${classOrder} to ${selectedId}`);
                        }
                    });
                }, 0);
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

            // Show hints for pre-loaded values on page load
            function initHints() {
                const tHrs = parseFloat(theoryHoursInput.value) || 0;
                const pHrs = parseFloat(practicalHoursInput.value) || 0;
                if (tHrs > 0 && theoryHint) {
                    const c = Math.floor(tHrs / 5);
                    theoryHint.textContent = `${tHrs} hrs ÷ 5 = ${c} class${c !== 1 ? 'es' : ''}`;
                }
                if (pHrs > 0 && practicalHint) {
                    const c = Math.floor(pHrs / 2);
                    practicalHint.textContent = `${pHrs} hrs ÷ 2 = ${c} class${c !== 1 ? 'es' : ''}`;
                }
            }

            theoryHoursInput.addEventListener('input',  calcTheoryClasses);
            theoryHoursInput.addEventListener('change', calcTheoryClasses);
            practicalHoursInput.addEventListener('input',  calcPracticalClasses);
            practicalHoursInput.addEventListener('change', calcPracticalClasses);

            // Event listeners
            courseTypeSelect.addEventListener('change', toggleFieldVisibility);

            // Add event listeners for total classes inputs to re-render lesson plans
            totalTheoryClassesInput.addEventListener('input', renderLessonPlanSelections);
            totalPracticalClassesInput.addEventListener('input', renderLessonPlanSelections);

            // Initialize on page load
            toggleFieldVisibility();
            initHints();

            // Form validation before submit
            editCourseForm.addEventListener('submit', function(e) {
                const courseType = courseTypeSelect.value;

                if (!courseType) {
                    alert('Please select a course type');
                    e.preventDefault();
                    return false;
                }

                // Validate theory fields if theory or hybrid
                if (courseType === 'theory' || courseType === 'hybrid') {
                    if (!theoryHoursInput.value || theoryHoursInput.value <= 0) {
                        alert('Please enter valid theory hours');
                        e.preventDefault();
                        return false;
                    }
                }

                // Validate practical fields if practical or hybrid
                if (courseType === 'practical' || courseType === 'hybrid') {
                    if (!practicalHoursInput.value || practicalHoursInput.value <= 0) {
                        alert('Please enter valid practical hours');
                        e.preventDefault();
                        return false;
                    }
                }

                return true;
            });
        });

        // Function for installment plan toggle (called from the HTML)
        function toggleInstallmentSection(checkbox) {
            const installmentSection = document.getElementById('installment-section');
            const installmentSelect = document.getElementById('course_installment_plan_id');

            if (checkbox.checked) {
                installmentSection.classList.remove('d-none');
                installmentSelect.setAttribute('required', 'required');
            } else {
                installmentSection.classList.add('d-none');
                installmentSelect.removeAttribute('required');
                installmentSelect.value = '';
            }
        }
    </script>
@endsection
