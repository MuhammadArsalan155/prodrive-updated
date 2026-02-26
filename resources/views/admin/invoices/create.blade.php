@extends('layouts.master')

@section('content')
    <div class="container-fluid px-4">
        <!-- Page Heading -->
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h1 class="h2 text-primary fw-bold">
                <i class="fas fa-file-invoice-dollar me-2"></i> Create New Invoice
            </h1>
            <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary btn-sm rounded-pill">
                <i class="fas fa-arrow-left me-1"></i> Back to Invoices
            </a>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Please correct the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Content Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12">
                <div class="card border-0 shadow-sm mb-4">
                    <!-- Card Header -->
                    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold">
                            <i class="fas fa-plus-circle me-2"></i> Invoice Details
                        </h5>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form action="{{ route('admin.invoices.store') }}" method="POST" id="invoiceForm">
                            @csrf
                            <div class="row g-4">
                                <!-- Student Information Section -->
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-user-graduate me-2"></i> Student Information
                                    </h5>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="student_id" class="form-label">
                                            <i class="fas fa-user text-primary me-2"></i> Select Student <span class="text-danger">*</span>
                                        </label>
                                        <select name="student_id" id="student_id" class="form-control" required>
                                            <option value="">Choose a student...</option>
                                            @foreach($students as $student)
                                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                                    {{ $student->first_name }} {{ $student->last_name }}
                                                    @if($student->course)
                                                        - {{ $student->course->course_name }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="student_info" class="form-label">
                                            <i class="fas fa-info-circle text-primary me-2"></i> Student Details
                                        </label>
                                        <div id="student_info" class="form-control-plaintext p-3 bg-light rounded">
                                            <small class="text-muted">Select a student to view details</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Course Information Section -->
                                <div class="col-12">
                                    <hr>
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-book me-2"></i> Course & Amount
                                    </h5>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="course_id" class="form-label">
                                            <i class="fas fa-graduation-cap text-primary me-2"></i> Select Course <span class="text-danger">*</span>
                                        </label>
                                        <select name="course_id" id="course_id" class="form-control" required>
                                            <option value="">Choose a course...</option>
                                            @foreach($courses as $course)
                                                <option value="{{ $course->id }}"
                                                        data-price="{{ $course->course_price }}"
                                                        data-has-installment="{{ $course->has_installment_plan ? 'true' : 'false' }}"
                                                        data-installment-plan-id="{{ $course->course_installment_plan_id }}"
                                                        {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                    {{ $course->course_name }} - ${{ number_format($course->course_price, 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-info-circle text-primary me-2"></i> Course Details
                                        </label>
                                        <div id="course_info" class="form-control-plaintext p-3 bg-light rounded">
                                            <small class="text-muted">Select a course to view details</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Plan Section -->
                                <div class="col-12">
                                    <hr>
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-credit-card me-2"></i> Payment Plan
                                    </h5>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-money-check text-primary me-2"></i> Payment Type <span class="text-danger">*</span>
                                        </label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_type" id="full_payment"
                                                   value="full_payment" {{ old('payment_type', 'full_payment') == 'full_payment' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="full_payment">
                                                <strong>Full Payment</strong>
                                                <br><small class="text-muted">Pay the entire amount at once</small>
                                            </label>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="radio" name="payment_type" id="installment_payment"
                                                   value="installment" {{ old('payment_type') == 'installment' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="installment_payment">
                                                <strong>Installment Plan</strong>
                                                <br><small class="text-muted">Split payment into multiple installments</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Installment Options -->
                                <div class="col-md-6" id="installment_options" style="display: none;">
                                    <div class="mb-3">
                                        <label for="first_installment_date" class="form-label">
                                            <i class="fas fa-calendar text-primary me-2"></i> First Installment Date
                                        </label>
                                        <input type="date" name="first_installment_date" id="first_installment_date"
                                               class="form-control" value="{{ old('first_installment_date', date('Y-m-d')) }}">
                                        <small class="text-muted">When should the first installment be due?</small>
                                    </div>
                                </div>

                                <!-- Installment Preview -->
                                <div class="col-12" id="installment_preview" style="display: none;">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <h6 class="m-0">
                                                <i class="fas fa-eye me-2"></i> Installment Schedule Preview
                                            </h6>
                                        </div>
                                        <div class="card-body" id="installment_schedule">
                                            <!-- Preview will be loaded here -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes Section -->
                                <div class="col-12">
                                    <hr>
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">
                                            <i class="fas fa-sticky-note text-primary me-2"></i> Notes (Optional)
                                        </label>
                                        <textarea name="notes" id="notes" class="form-control" rows="3"
                                                  placeholder="Additional notes about this invoice...">{{ old('notes') }}</textarea>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-1"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-lg" id="submit_btn">
                                            <i class="fas fa-save me-2"></i> Create Invoice
                                        </button>
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
$(document).ready(function() {
    // Student selection handler
    $('#student_id').change(function() {
        const studentId = $(this).val();
        if (studentId) {
            $.ajax({
                url: `/api/students/${studentId}/invoices`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const student = response.student;
                        let studentInfo = `
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Email:</strong> ${student.email}<br>
                                    <strong>Contact:</strong> ${student.contact || 'Not provided'}
                                </div>
                                <div class="col-md-6">
                                    <strong>Status:</strong> ${student.course_status || 'Not enrolled'}<br>
                                    <strong>Current Course:</strong> ${student.course ? student.course.course_name : 'None'}
                                </div>
                            </div>
                        `;
                        $('#student_info').html(studentInfo);

                        // Auto-select student's current course if available
                        if (student.course && student.course.id) {
                            $('#course_id').val(student.course.id).trigger('change');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    $('#student_info').html('<small class="text-danger">Error loading student details</small>');
                }
            });
        } else {
            $('#student_info').html('<small class="text-muted">Select a student to view details</small>');
        }
    });

    // Course selection handler
    $('#course_id').change(function() {
        const courseId = $(this).val();
        if (courseId) {
            $.ajax({
                url: `/api/courses/${courseId}/details`,
                type: 'GET',
                success: function(response) {
                    let courseInfo = `
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Price:</strong> $${parseFloat(response.price).toFixed(2)}<br>
                                <strong>Type:</strong> ${response.type}
                            </div>
                            <div class="col-md-6">
                                <strong>Theory Hours:</strong> ${response.theory_hours}<br>
                                <strong>Practical Hours:</strong> ${response.practical_hours}
                            </div>
                        </div>
                        ${response.has_installment_plan ? '<div class="mt-2"><span class="badge bg-success">Installment Plan Available</span></div>' : '<div class="mt-2"><span class="badge bg-secondary">Full Payment Only</span></div>'}
                    `;
                    $('#course_info').html(courseInfo);

                    // Enable/disable installment option based on course
                    if (response.has_installment_plan) {
                        $('#installment_payment').prop('disabled', false);
                        $('#installment_payment').parent().removeClass('text-muted');
                    } else {
                        $('#installment_payment').prop('disabled', true);
                        $('#installment_payment').parent().addClass('text-muted');
                        $('#full_payment').prop('checked', true);
                        $('#installment_options, #installment_preview').hide();
                    }

                    // Update installment preview if installment is selected
                    if ($('#installment_payment').is(':checked') && response.has_installment_plan) {
                        updateInstallmentPreview(response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading course details:', error);
                    $('#course_info').html('<small class="text-danger">Error loading course details</small>');
                }
            });
        } else {
            $('#course_info').html('<small class="text-muted">Select a course to view details</small>');
        }
    });

    // Payment type change handler
    $('input[name="payment_type"]').change(function() {
        if ($(this).val() === 'installment') {
            const courseId = $('#course_id').val();
            if (courseId) {
                $('#installment_options').show();
                updateInstallmentPreview();
            }
        } else {
            $('#installment_options, #installment_preview').hide();
        }
    });

    // Date change handler
    $('#first_installment_date').change(function() {
        if ($('#installment_payment').is(':checked')) {
            updateInstallmentPreview();
        }
    });

    function updateInstallmentPreview(courseData = null) {
        const courseId = $('#course_id').val();
        const firstInstallmentDate = $('#first_installment_date').val();

        if (courseId && firstInstallmentDate) {
            $.ajax({
                url: `/api/courses/${courseId}/details`,
                type: 'GET',
                success: function(response) {
                    if (response.has_installment_plan && response.sample_schedule) {
                        let scheduleHtml = `
                            <div class="mb-3">
                                <strong>Plan:</strong> ${response.installment_plan.name}<br>
                                <strong>Total Installments:</strong> ${response.installment_plan.number_of_installments}<br>
                                <strong>First Payment:</strong> ${response.installment_plan.first_installment_percentage}% of total amount
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Installment #</th>
                                            <th>Amount</th>
                                            <th>Due Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;

                        response.sample_schedule.forEach(function(installment) {
                            scheduleHtml += `
                                <tr>
                                    <td>Installment ${installment.installment_number}</td>
                                    <td class="text-success fw-bold">$${installment.amount}</td>
                                    <td>${installment.due_date}</td>
                                </tr>
                            `;
                        });

                        scheduleHtml += `
                                    </tbody>
                                </table>
                            </div>
                        `;

                        $('#installment_schedule').html(scheduleHtml);
                        $('#installment_preview').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading installment preview:', error);
                }
            });
        } else {
            $('#installment_preview').hide();
        }
    }

    // Form validation
    $('#invoiceForm').on('submit', function(e) {
        const paymentType = $('input[name="payment_type"]:checked').val();
        const courseId = $('#course_id').val();

        if (paymentType === 'installment') {
            if (!$('#first_installment_date').val()) {
                e.preventDefault();
                alert('Please select the first installment date.');
                return false;
            }

            // Check if course supports installments
            const selectedCourse = $('#course_id option:selected');
            const hasInstallment = selectedCourse.data('has-installment') === 'true';

            if (!hasInstallment) {
                e.preventDefault();
                alert('Selected course does not support installment payments.');
                return false;
            }
        }
    });

    // Initialize payment type visibility
    if ($('#installment_payment').is(':checked')) {
        $('#installment_options').show();
    }

    // Auto-hide alerts after 8 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 8000);
});
</script>
@endsection
