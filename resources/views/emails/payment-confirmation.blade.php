<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-image: linear-gradient(135deg, #1D4C5C 0%, #2a6276 100%);
            min-height: 100vh;
        }
        .email-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #1D4C5C;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1D4C5C;
            margin-bottom: 10px;
        }
        h1 {
            color: #1D4C5C;
            margin-bottom: 20px;
        }
        .payment-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .credentials-section {
            background-color: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
        }
        .credentials-box {
            background-color: #ffffff;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border: 1px solid #ddd;
        }
        .login-button {
            display: inline-block;
            background-image: linear-gradient(135deg, #1D4C5C 0%, #2a6276 100%);
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 15px 0;
            font-weight: bold;
            transition: transform 0.2s ease;
        }
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(29, 76, 92, 0.3);
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 5px 0;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .detail-value {
            color: #333;
        }
        .success-icon {
            color: #28a745;
            font-size: 20px;
            margin-right: 10px;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
            color: #856404;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .email-container {
                padding: 20px;
            }
            .detail-row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">ProDrive Academy</div>
            <p>Driving Excellence, Building Confidence</p>
        </div>

        <h1>
            <span class="success-icon">✅</span>
            Payment Confirmation
        </h1>

        <p>Dear {{ $studentName }},</p>

        <p>Thank you for your payment! We're excited to confirm that your payment has been successfully processed.</p>

        <div class="payment-details">
            <h3 style="margin-top: 0; color: #28a745;">Payment Details</h3>

            <div class="detail-row">
                <span class="detail-label">Course:</span>
                <span class="detail-value">{{ $courseName }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Invoice Number:</span>
                <span class="detail-value">#{{ $invoiceNumber }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Amount Paid:</span>
                <span class="detail-value">${{ number_format($amount, 2) }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Payment Date:</span>
                <span class="detail-value">{{ $paymentDate }}</span>
            </div>
        </div>

        @if($hasNewPassword)
        <div class="credentials-section">
            <h3 style="margin-top: 0; color: #2196f3;">🔐 Your Student Account Details</h3>

            <p>Your student account has been created! Use the following credentials to access your student portal:</p>

            <div class="credentials-box">
                <div class="detail-row">
                    <span class="detail-label">Email/Username:</span>
                    <span class="detail-value">{{ $student->email }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Password:</span>
                    <span class="detail-value" style="font-family: monospace; background-color: #f8f9fa; padding: 3px 6px; border-radius: 3px;">{{ $password }}</span>
                </div>
            </div>

            <div class="warning">
                <strong>Important:</strong> Please keep your login credentials safe and consider changing your password after your first login for security purposes.
            </div>

            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="login-button">Login to Student Portal</a>
            </div>
        </div>
        @else
        <div class="credentials-section">
            <h3 style="margin-top: 0; color: #2196f3;">🔐 Student Portal Access</h3>

            <p>You can access your student portal using your existing credentials:</p>

            <div class="credentials-box">
                <div class="detail-row">
                    <span class="detail-label">Email/Username:</span>
                    <span class="detail-value">{{ $student->email }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Password:</span>
                    <span class="detail-value">Use your existing password</span>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="login-button">Login to Student Portal</a>
            </div>
        </div>
        @endif

        <h3>What's Next?</h3>
        <ul>
            <li>Log in to your student portal to view your course materials</li>
            <li>Check your course schedule and upcoming classes</li>
            <li>Access learning resources and track your progress</li>
            <li>Connect with your instructor for any questions</li>
        </ul>

        <p>If you have any questions about your payment or course, please don't hesitate to contact us.</p>

        <p>Welcome to ProDrive Academy! We look forward to helping you achieve your driving goals.</p>

        <p>Best regards,<br>
        <strong>The ProDrive Academy Team</strong></p>

        <div class="footer">
            <p>This is an automated email. Please do not reply to this email address.</p>
            <p>If you need assistance, please contact us at support@prodriveacademy.com</p>
            <p>&copy; {{ date('Y') }} ProDrive Academy. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
