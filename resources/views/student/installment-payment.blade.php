<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installment Payment - {{ $installment->invoice->course->course_name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .payment-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .installment-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        .amount-display {
            font-size: 2.5rem;
            font-weight: bold;
            color: #28a745;
        }
        .due-badge {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        .info-item {
            border-bottom: 1px solid #f8f9fa;
            padding: 15px 0;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .pay-button {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .pay-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }
        .loading-spinner {
            display: none;
        }
        .payment-methods {
            margin-top: 20px;
        }
        .payment-method-option {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .payment-method-option:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
        .payment-method-option.selected {
            border-color: #007bff;
            background-color: #e7f3ff;
        }
        .payment-method-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="payment-container">
            <!-- Header -->
            <div class="text-center mb-4">
                <h1 class="text-primary">
                    <i class="fas fa-credit-card me-2"></i>
                    Installment Payment
                </h1>
                <p class="text-muted">Complete your course installment payment</p>
            </div>

            <!-- Installment Details Card -->
            <div class="card installment-card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        {{ $installment->invoice->course->course_name }}
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Amount Display -->
                    <div class="text-center mb-4">
                        <div class="amount-display">${{ number_format($installment->amount, 2) }}</div>
                        <p class="text-muted">Installment Amount</p>
                        <span class="due-badge">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Due: {{ $installment->due_date->format('F j, Y') }}
                        </span>
                    </div>

                    <!-- Student & Course Info -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong><i class="fas fa-user me-2 text-primary"></i>Student:</strong>
                                <div class="mt-1">{{ $installment->invoice->student->first_name }} {{ $installment->invoice->student->last_name }}</div>
                            </div>
                            <div class="info-item">
                                <strong><i class="fas fa-envelope me-2 text-primary"></i>Email:</strong>
                                <div class="mt-1">{{ $installment->invoice->student->email }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong><i class="fas fa-receipt me-2 text-primary"></i>Invoice:</strong>
                                <div class="mt-1">{{ $installment->invoice->invoice_number }}</div>
                            </div>
                            <div class="info-item">
                                <strong><i class="fas fa-sticky-note me-2 text-primary"></i>Notes:</strong>
                                <div class="mt-1">{{ $installment->notes ?: 'Course installment payment' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Status -->
                    @if($installment->is_overdue)
                        <div class="alert alert-danger mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Overdue Payment!</strong> This installment was due on {{ $installment->due_date->format('F j, Y') }}.
                            Please make payment as soon as possible.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="card payment-methods">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>
                        Select Payment Method
                    </h5>
                </div>
                <div class="card-body">
                    <form id="installmentPaymentForm">
                        @csrf
                        <div id="paymentMethodsList">
                            <!-- Payment methods will be loaded here -->
                        </div>

                        <!-- Pay Button -->
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success pay-button" id="payButton">
                                <i class="fas fa-lock me-2"></i>
                                Pay ${{ number_format($installment->amount, 2) }} Securely
                            </button>
                            <div class="loading-spinner mt-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Processing...</span>
                                </div>
                                <p class="mt-2">Processing your payment...</p>
                            </div>
                        </div>

                        <!-- Security Notice -->
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Your payment is secured by Stripe. We don't store your card details.
                            </small>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Section -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6><i class="fas fa-question-circle me-2 text-info"></i>Need Help?</h6>
                    <p class="mb-2">If you're experiencing issues with payment, please contact our support team:</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-phone me-2 text-success"></i>Phone: (555) 123-4567</li>
                        <li><i class="fas fa-envelope me-2 text-primary"></i>Email: support@prodrive.com</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            let selectedPaymentMethod = null;

            // Load payment methods
            loadPaymentMethods();

            // Handle payment method selection
            $(document).on('click', '.payment-method-option', function() {
                $('.payment-method-option').removeClass('selected');
                $(this).addClass('selected');
                selectedPaymentMethod = $(this).data('method-id');
                $('#payButton').prop('disabled', false);
            });

            // Handle form submission
            $('#installmentPaymentForm').on('submit', function(e) {
                e.preventDefault();

                if (!selectedPaymentMethod) {
                    alert('Please select a payment method');
                    return;
                }

                processPayment();
            });

            function loadPaymentMethods() {
                $.get('/api/payment-methods', function(data) {
                    let html = '';
                    data.forEach(function(method) {
                        html += `
                            <div class="payment-method-option" data-method-id="${method.id}">
                                <div class="d-flex align-items-center">

                                    <img src="${method.logo_url || '/images/default-payment.png'}"
                                         alt="${method.name}" class="payment-method-logo me-3">
                                    <div>
                                        <strong>${method.name}</strong>
                                        <br>
                                        <small class="text-muted">Secure payment via ${method.name}</small>
                                    </div>
                                    <div class="ms-auto">
                                        <i class="fas fa-check-circle text-success" style="display: none;"></i>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $('#paymentMethodsList').html(html);
                }).fail(function() {
                    $('#paymentMethodsList').html(`
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Unable to load payment methods. Please refresh the page.
                        </div>
                    `);
                });
            }

            function processPayment() {
                // Show loading state
                $('#payButton').prop('disabled', true);
                $('.loading-spinner').show();

                const formData = {
                    payment_method_id: selectedPaymentMethod,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.ajax({
                    url: '/installment/{{ $installment->id }}/pay',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            // Redirect to Stripe checkout
                            window.location.href = response.checkout_url;
                        } else {
                            throw new Error(response.message || 'Payment processing failed');
                        }
                    },
                    error: function(xhr) {
                        let message = 'Payment processing failed. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        alert(message);

                        // Hide loading state
                        $('#payButton').prop('disabled', false);
                        $('.loading-spinner').hide();
                    }
                });
            }
        });

        // Show selected payment method check mark
        $(document).on('click', '.payment-method-option', function() {
            $('.payment-method-option .fa-check-circle').hide();
            $(this).find('.fa-check-circle').show();
        });
    </script>
</body>
</html>