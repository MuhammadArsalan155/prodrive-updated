<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #1D4C5C 0%, #2a6276 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .success-icon {
            font-size: 60px;
            margin-bottom: 15px;
            display: block;
        }
        .content {
            padding: 40px 30px;
        }
        .amount {
            font-size: 36px;
            font-weight: bold;
            color: #1D4C5C; /* Updated to match the gradient's primary color */
            text-align: center;
            margin: 20px 0;
        }
        .details-box {
            background: #f8f9fa;
            border-left: 4px solid #1D4C5C; /* Updated to match the gradient */
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
        }
        .detail-value {
            color: #1D4C5C; /* Updated to match the gradient */
            font-weight: 600;
        }
        .next-payment {
            background: #e6f0fa; /* Lighter teal-blue shade for contrast */
            border: 1px solid #b3d4e6; /* Softer border to match the palette */
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .next-payment h3 {
            color: #1D4C5C; /* Updated to match the gradient */
            margin-top: 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #1D4C5C, #2a6276); /* Updated to match the header gradient */
            color: white;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 25px;
            font-weight: 600;
            margin: 10px 5px;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .completed-message {
            background: #e6f0fa; /* Updated to match the next-payment background */
            border: 2px solid #1D4C5C; /* Updated to match the gradient */
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            margin: 25px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        .contact-info {
            background: #e6f0fa; /* Updated to match the new palette */
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .contact-info h4 {
            color: #1D4C5C; /* Updated to match the gradient */
            margin-top: 0;
        }
        @media (max-width: 600px) {
            body { padding: 10px; }
            .header, .content { padding: 25px 20px; }
            .amount { font-size: 28px; }
            .detail-row { flex-direction: column; align-items: flex-start; }
            .detail-value { margin-top: 5px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <span class="success-icon">✅</span>
            <h1>Payment Successful!</h1>
            <p>Your installment payment has been processed</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Hello {{ $studentName }},</h2>

            <p>Great news! We've successfully received your installment payment for <strong>{{ $courseName }}</strong>.</p>

            <!-- Amount Display -->
            <div class="amount">${{ number_format($paidAmount, 2) }}</div>
            <p style="text-align: center; color: #6c757d; margin-top: -10px;">Payment Amount</p>

            <!-- Payment Details -->
            <div class="details-box">
                <h3 style="margin-top: 0; color: #1D4C5C;">Payment Details</h3>

                <div class="detail-row">
                    <span class="detail-label">Course:</span>
                    <span class="detail-value">{{ $courseName }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Invoice Number:</span>
                    <span class="detail-value">{{ $invoiceNumber }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Payment Date:</span>
                    <span class="detail-value">{{ $paymentDate->format('M j, Y \a\t g:i A') }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Transaction ID:</span>
                    <span class="detail-value">{{ $transactionId }}</span>
                </div>
            </div>

            @if($isFullyPaid)
                <!-- Course Fully Paid -->
                <div class="completed-message">
                    <h3 style="color: #1D4C5C; margin-top: 0;">🎉 Congratulations!</h3>
                    <p><strong>All installments are now complete!</strong></p>
                    <p>You have full access to your course materials and can participate in all course activities.</p>
                    <a href="{{ url('/login') }}" class="btn">Access Portal</a>
                </div>
            @else
                <!-- Next Payment Info -->
                @if($nextInstallment)
                <div class="next-payment">
                    <h3>📅 Next Payment Due</h3>
                    <p><strong>${{ number_format($nextInstallment->amount, 2) }}</strong> due on <strong>{{ $nextInstallment->due_date->format('F j, Y') }}</strong></p>
                    <p>{{ $nextInstallment->notes }}</p>
                    <a href="{{ url('/student/pay-installment/' . $nextInstallment->id) }}" class="btn">
                        Pay Next Installment
                    </a>
                </div>
                @endif

                <!-- Remaining Payments Summary -->
                @if($remainingInstallments->count() > 0)
                <div style="background: #e6f0fa; border-radius: 8px; padding: 20px; margin: 25px 0;">
                    <h4 style="margin-top: 0; color: #1D4C5C;">📊 Payment Progress</h4>
                    <p>You have <strong>{{ $remainingInstallments->count() }}</strong> installment(s) remaining.</p>
                    <p>Total remaining: <strong>${{ number_format($remainingInstallments->sum('amount'), 2) }}</strong></p>
                    <a href="{{ url('/student/installments') }}" class="btn">View All Installments</a>
                </div>
                @endif
            @endif

            <!-- Contact Information -->
            <div class="contact-info">
                <h4>Questions? We're Here to Help!</h4>
                <p>If you have any questions about your payment or course:</p>
                <p>
                    📧 <strong>Email:</strong> support@prodrive.com<br>
                    📞 <strong>Phone:</strong> (555) 123-4567<br>
                    🌐 <strong>Website:</strong> {{ url('/') }}
                </p>
            </div>

            <p>Thank you for choosing our driving school. We're committed to helping you achieve your driving goals!</p>

            <p>Best regards,<br>
            <strong>ProDrive School Team</strong></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Keep this email for your records.</strong></p>
            <p>This confirmation serves as your receipt for this payment.</p>
            <hr style="border: none; border-top: 1px solid #dee2e6; margin: 15px 0;">
            <p>&copy; {{ date('Y') }} ProDrive School. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
