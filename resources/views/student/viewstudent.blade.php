@extends('layouts.master')

@section('content')
    <div class="container-fluid px-4">
        <!-- Student Basic Information -->
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-user-graduate me-2"></i>
                            Student Profile: {{ $student->first_name }} {{ $student->last_name }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Personal Information</h5>
                                <p><strong>Name:</strong> {{ $student->first_name }} {{ $student->last_name }}</p>
                                <p><strong>Email:</strong> {{ $student->email }}</p>
                                <p><strong>Contact:</strong> {{ $student->student_contact }}</p>
                                <p><strong>Date of Birth:</strong>
                                    {{ \Carbon\Carbon::parse($student->student_dob)->format('F j, Y') }}
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h5>Course Details</h5>
                                <p><strong>Course:</strong> {{ $student->course->course_name }}</p>
                                <p><strong>Total Course Fee:</strong>
                                    ${{ number_format($student->course->course_price, 2) }}</p>
                                <p><strong>Course Type:</strong> {{ $student->course->course_type }}</p>
                                <p><strong>Theory Hours:</strong> {{ $student->course->theory_hours }}</p>
                                <p><strong>Practical Hours:</strong> {{ $student->course->practical_hours }}</p>
                            </div>
                            <div class="col-md-4">
                                <h5>Registration Status</h5>
                                <p>
                                    <strong>Joining Date:</strong>
                                    {{ $student->joining_date ? \Carbon\Carbon::parse($student->joining_date)->format('F j, Y') : 'Not Available' }}
                                </p>
                                <p>
                                    <strong>Course Status:</strong>
                                    @switch($student->course_status)
                                        @case(0)
                                            <span class="badge bg-warning">Pending</span>
                                        @break

                                        @case(1)
                                            <span class="badge bg-success">In Progress</span>
                                        @break

                                        @case(2)
                                            <span class="badge bg-info">Completed</span>
                                        @break

                                        @default
                                            <span class="badge bg-secondary">Unknown</span>
                                    @endswitch
                                    <button class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="modal"
                                        data-bs-target="#courseStatusModal">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </button>
                                </p>
                                <p>
                                    <strong>Payment Status:</strong>
                                    @switch($student->payment_status)
                                        @case(0)
                                            <span class="badge bg-danger">Unpaid</span>
                                        @break

                                        @case(1)
                                            <span class="badge bg-info">Pending</span>
                                        @break

                                        @case(2)
                                            <span class="badge bg-danger">Failed</span>
                                        @break
                                        
                                        @case(3)
                                            <span class="badge bg-success">Paid</span>
                                        @break

                                        @default
                                            <span class="badge bg-secondary">Unknown</span>
                                    @endswitch
                                </p>
                            </div>
                        </div>
                        
                        <!-- Parent Information Section -->
                        @if($student->parent)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-user-friends me-2"></i>Parent Information
                                </h5>
                                <hr>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Parent Name:</strong> {{ $student->parent->name ?? 'Not provided' }}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Parent Email:</strong> {{ $student->parent->email ?? 'Not provided' }}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Parent Contact:</strong> {{ $student->parent->contact ?? 'Not provided' }}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Parent Address:</strong> {{ $student->parent->address ?? 'Same as student' }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Details Section -->
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-money-check-alt me-2"></i>Payment History
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($student->invoices->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Invoice Number</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Payment Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($student->invoices as $invoice)
                                            <tr>
                                                <td>{{ $invoice->invoice_number }}</td>
                                                <td>${{ number_format($invoice->amount, 2) }}</td>
                                                <td>
                                                    <span
                                                        class="badge 
                                                    {{ $invoice->status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                                                        {{ ucfirst($invoice->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $invoice->created_at->format('F j, Y') }}</td>
                                                <td>
                                                    @if ($invoice->payments->first())
                                                        {{ json_decode($invoice->payments->first()->payment_details, true)['payment_type'] ?? 'N/A' }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-muted">No payment history available</p>
                        @endif
                    </div>
                </div>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <img src="@if ($student->profile_photo == null) https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp @else {{ asset('profile') }}/{{ $student->profile_photo }} @endif"
                                alt="avatar" class="rounded-circle object-fit-cover" style="width: 150px; height: 150px;">
                        </div>
                        <h5 class="fw-bold mb-2">{{ $student->first_name }} {{ $student->last_name }}</h5>
                        <p class="text-muted mb-1">
                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                            Start Date:
                            {{ \Carbon\Carbon::parse($student->joining_date)->isoFormat('Do MMM, YYYY') }}
                        </p>

                        @if ($student->completion_date == null)
                            <span class="badge bg-danger mt-2">Incomplete</span>
                        @else
                            <p class="text-muted mb-2">Completion Date: {{ \Carbon\Carbon::parse($student->completion_date)->isoFormat('Do MMM, YYYY') }}</p>
                        @endif

                        <div class="mt-3">
                            <form method="post" action="{{ route('uplaodphoto') }}" enctype="multipart/form-data"
                                class="d-flex flex-column align-items-center">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                <div class="input-group mb-2">
                                    <input type="file" name="profile_photo" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-upload me-2"></i>Update Profile Photo
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cash-register me-2"></i>Process Cash Payment
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($student->payment_status == 0)
                            <form id="cashPaymentForm">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $student->id }}">

                                <div class="form-group mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <div class="payment-methods">
                                        @foreach ($cashPaymentMethods as $method)
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_method_id"
                                                    id="paymentMethod{{ $method->id }}" value="{{ $method->id }}"
                                                    required>
                                                <label class="form-check-label" for="paymentMethod{{ $method->id }}">
                                                    {{ $method->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Payment Type</label>
                                    <div class="payment-type-options">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_type"
                                                id="fullPayment" value="full" checked>
                                            <label class="form-check-label" for="fullPayment">
                                                Full Payment (${{ number_format($student->course->course_price, 2) }})
                                            </label>
                                        </div>
                                        @if ($student->course->hasInstallmentPlan())
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_type"
                                                    id="installmentPayment" value="installment">
                                                <label class="form-check-label" for="installmentPayment">
                                                    Installment Plan
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="paymentAmount" class="form-label">Payment Amount</label>
                                    <input type="number" class="form-control" id="paymentAmount" name="payment_amount"
                                        step="0.01" min="0.01" max="{{ $student->course->course_price }}"
                                        value="{{ $student->course->course_price }}" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="paymentDate" class="form-label">Payment Date</label>
                                    <input type="date" class="form-control" id="paymentDate" name="payment_date"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="paymentNotes" class="form-label">Payment Notes (Optional)</label>
                                    <textarea class="form-control" id="paymentNotes" name="payment_notes" rows="3"
                                        placeholder="Additional payment details"></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-money-check-alt me-2"></i>Process Cash Payment
                                </button>
                            </form>
                        @else
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>Payment for this student has been completed.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Status Update Modal -->
        <div class="modal fade" id="courseStatusModal" tabindex="-1" aria-labelledby="courseStatusModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="courseStatusModalLabel">
                            <i class="fas fa-graduation-cap me-2"></i>Update Course Status
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="courseStatusForm">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $student->id }}">

                            <div class="mb-3">
                                <label class="form-label">Select Course Status</label>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="course_status"
                                                id="statusPending" value="0"
                                                {{ $student->course_status == 0 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="statusPending">
                                                <span class="badge bg-warning">Pending</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="course_status"
                                                id="statusInProgress" value="1"
                                                {{ $student->course_status == 1 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="statusInProgress">
                                                <span class="badge bg-success">In Progress</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="course_status"
                                                id="statusCompleted" value="2"
                                                {{ $student->course_status == 2 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="statusCompleted">
                                                <span class="badge bg-info">Completed</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-primary" id="updateCourseStatusBtn">
                            <i class="fas fa-save me-2"></i>Update Status
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Installment Details (if applicable) -->
        @if ($student->course->hasInstallmentPlan() && $student->payment_plan_type === 'installment')
            <div class="row">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>Installment Schedule
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $installmentPlan = $student->course->installmentPlan;
                                $lastInvoice = $student->invoices->last();
                            @endphp

                            @if ($lastInvoice && $lastInvoice->installments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Installment</th>
                                                <th>Amount</th>
                                                <th>Due Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lastInvoice->installments as $installment)
                                                <tr>
                                                    <td>{{ $installment->installment_number }}</td>
                                                    <td>${{ number_format($installment->amount, 2) }}</td>
                                                    <td>{{ $installment->due_date->format('F j, Y') }}</td>
                                                    <td>
                                                        <span
                                                            class="badge 
                                                        {{ $installment->status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                                                            {{ ucfirst($installment->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-center text-muted">No installment schedule available</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Add this in the Installment Details section -->
        @if ($pendingInstallments->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bell me-2"></i>Pending Installments
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Installment</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingInstallments as $installment)
                                    <tr>
                                        <td>{{ $installment->invoice->invoice_number }} - Installment</td>
                                        <td>${{ number_format($installment->amount, 2) }}</td>
                                        <td>{{ $installment->due_date->format('F j, Y') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-warning send-reminder"
                                                data-installment-id="{{ $installment->id }}">
                                                <i class="fas fa-envelope me-1"></i>Send Reminder
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Course Status Update
            $('#updateCourseStatusBtn').on('click', function() {
                const form = $('#courseStatusForm');

                $.ajax({
                    url: '{{ route('update.course.status') }}',
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        // Update the status badge in the page
                        const statusBadges = {
                            0: '<span class="badge bg-warning">Pending</span>',
                            1: '<span class="badge bg-success">In Progress</span>',
                            2: '<span class="badge bg-info">Completed</span>'
                        };

                        // Update the status in the main page
                        $('[data-course-status]').html(statusBadges[response.new_status]);

                        // Show success toast
                        Swal.fire({
                            icon: 'success',
                            title: 'Status Updated',
                            text: response.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });

                        // Close the modal
                        $('#courseStatusModal').modal('hide');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Update Failed',
                            text: xhr.responseJSON.message ||
                                'Failed to update course status',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                });
            });

            $('.send-reminder').on('click', function() {
                const installmentId = $(this).data('installment-id');
                const button = $(this);

                $.ajax({
                    url: '{{ route('admin.send-installment-reminder') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        installment_id: installmentId
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Reminder Sent',
                            text: response.message
                        });

                        // Optionally disable the button after sending
                        button.prop('disabled', true)
                            .removeClass('btn-warning')
                            .addClass('btn-secondary');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON.message || 'Failed to send reminder'
                        });
                    }
                });
            });

            $('#cashPaymentForm').on('submit', function(e) {
                e.preventDefault();

                // Reset previous error states
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                $.ajax({
                    url: '{{ route('admin.cash-payment.process') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Payment Processed',
                            text: 'Cash payment processed successfully. Invoice #' +
                                response.invoice_number,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Reload the page to reflect updated status
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;

                            Object.keys(errors).forEach(function(field) {
                                const inputField = $(`[name="${field}"]`);
                                inputField.addClass('is-invalid');

                                // Create error message
                                const errorMessage = errors[field][0];
                                inputField.after(
                                    `<div class="invalid-feedback">${errorMessage}</div>`
                                );
                            });
                        } else {
                            // Generic error
                            Swal.fire({
                                icon: 'error',
                                title: 'Payment Failed',
                                text: xhr.responseJSON.message ||
                                    'An error occurred while processing the payment.',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                });
            });

            // Dynamic payment amount validation for installment
            $('input[name="payment_type"]').on('change', function() {
                const paymentAmountInput = $('#paymentAmount');
                const courseFee = parseFloat('{{ $student->course->course_price }}');

                if ($(this).val() === 'installment') {
                    // For installment, allow partial payment
                    paymentAmountInput.attr('min', (courseFee * 0.1).toFixed(2)); // Minimum 10%
                    paymentAmountInput.attr('max', courseFee.toFixed(2));

                    // Reset to a partial amount if current value is full fee
                    if (parseFloat(paymentAmountInput.val()) === courseFee) {
                        paymentAmountInput.val((courseFee * 0.5).toFixed(2)); // Default to 50%
                    }
                } else {
                    // For full payment, require exact course fee
                    paymentAmountInput.attr('min', courseFee.toFixed(2));
                    paymentAmountInput.attr('max', courseFee.toFixed(2));
                    paymentAmountInput.val(courseFee.toFixed(2));
                }
            });

            // Validate payment amount in real-time
            $('#paymentAmount').on('input', function() {
                const paymentType = $('input[name="payment_type"]:checked').val();
                const courseFee = parseFloat('{{ $student->course->course_price }}');
                const enteredAmount = parseFloat($(this).val());

                if (paymentType === 'full' && enteredAmount !== courseFee) {
                    $(this).addClass('is-invalid');
                    $(this).after(
                        `<div class="invalid-feedback">Full payment must be exactly $${courseFee.toFixed(2)}</div>`
                    );
                } else if (paymentType === 'installment') {
                    const minAmount = courseFee * 0.1;
                    const maxAmount = courseFee;

                    if (enteredAmount < minAmount || enteredAmount > maxAmount) {
                        $(this).addClass('is-invalid');
                        $(this).after(
                            `<div class="invalid-feedback">Installment amount must be between $${minAmount.toFixed(2)} and $${maxAmount.toFixed(2)}</div>`
                        );
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).siblings('.invalid-feedback').remove();
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).siblings('.invalid-feedback').remove();
                }
            });

            // Prevent form submission if there are validation errors
            $('#cashPaymentForm').on('submit', function(e) {
                if ($(this).find('.is-invalid').length > 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please correct the highlighted fields before submitting.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    </script>
@endsection