<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isUpdate ? 'Account Updated' : 'Welcome to Our Platform' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #1D4C5C 0%, #2a6276 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            border: 1px solid #e9ecef;
        }
        .credentials-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #1D4C5C;
            margin: 20px 0;
        }
        .credential-item {
            margin: 10px 0;
            padding: 10px;
            background: #f1f3f4;
            border-radius: 5px;
        }
        .credential-label {
            font-weight: bold;
            color: #495057;
        }
        .credential-value {
            font-family: monospace;
            color: #1D4C5C;
            font-size: 16px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: #e9ecef;
            border-radius: 8px;
            color: #6c757d;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $isUpdate ? '🔄 Account Updated' : '🎉 Welcome to Our Platform' }}</h1>
        <p>{{ $isUpdate ? 'Your account details have been updated' : 'Your instructor account has been created' }}</p>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $instructor->instructor_name }}</strong>,</p>

        @if($isUpdate)
            <p>Your account details have been updated by the administrator. Please find your updated login credentials below:</p>
        @else
            <p>Welcome to our platform! Your instructor account has been successfully created. You can now log in using the credentials below:</p>
        @endif

        <div class="credentials-box">
            <h3>🔐 Your Login Credentials</h3>

            <div class="credential-item">
                <div class="credential-label">Email Address:</div>
                <div class="credential-value">{{ $instructor->email }}</div>
            </div>

            <div class="credential-item">
                <div class="credential-label">Password:</div>
                <div class="credential-value">{{ $password }}</div>
            </div>

            @if($instructor->license_number)
            <div class="credential-item">
                <div class="credential-label">License Number:</div>
                <div class="credential-value">{{ $instructor->license_number }}</div>
            </div>
            @endif
        </div>

        <div class="warning">
            <strong>⚠️ Important Security Notice:</strong>
            <br>
            Please change your password after your first login for security purposes. Keep your credentials confidential and do not share them with anyone.
        </div>

        <p><strong>Next Steps:</strong></p>
        <ol>
            <li>Visit the login page</li>
            <li>Enter your email and password</li>
            <li>Change your password (recommended)</li>
            <li>Complete your profile setup</li>
        </ol>

        @if(!$isUpdate)
            <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
        @endif
    </div>

    <div class="footer">
        <p>This email was sent automatically. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} Our Platform. All rights reserved.</p>
    </div>
</body>
</html>
