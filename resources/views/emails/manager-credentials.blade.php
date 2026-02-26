<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isUpdate ? 'Manager Account Updated' : 'Welcome to Management Portal' }}</title>
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
        .manager-badge {
            display: inline-block;
            background: #1D4C5C;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $isUpdate ? '🔄 Manager Account Updated' : '🎉 Welcome to Management Portal' }}</h1>
        <div class="manager-badge">MANAGER ACCESS</div>
        <p>{{ $isUpdate ? 'Your manager account details have been updated' : 'Your manager account has been created' }}</p>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $manager->name }}</strong>,</p>

        @if($isUpdate)
            <p>Your manager account details have been updated by the administrator. Please find your updated login credentials below:</p>
        @else
            <p>Welcome to our ProDrive Academy portal! Your manager account has been successfully created with administrative privileges. You can now log in using the credentials below:</p>
        @endif

        <div class="credentials-box">
            <h3>🔐 Your Manager Login Credentials</h3>

            <div class="credential-item">
                <div class="credential-label">Email Address:</div>
                <div class="credential-value">{{ $manager->email }}</div>
            </div>

            <div class="credential-item">
                <div class="credential-label">Password:</div>
                <div class="credential-value">{{ $password }}</div>
            </div>

            <div class="credential-item">
                <div class="credential-label">Role:</div>
                <div class="credential-value">Manager</div>
            </div>

            <div class="credential-item">
                <div class="credential-label">Account Status:</div>
                <div class="credential-value">{{ $manager->is_active ? 'Active' : 'Inactive' }}</div>
            </div>
        </div>

        <div class="warning">
            <strong>⚠️ Important Security Notice:</strong>
            <br>
            As a manager, you have elevated privileges in the system. Please change your password after your first login and keep your credentials highly confidential. Never share your manager credentials with anyone.
        </div>

        {{-- <p><strong>Manager Access Includes:</strong></p>
        <ul>
            <li>Administrative dashboard access</li>
            <li>User management capabilities</li>
            <li>System configuration access</li>
            <li>Reports and analytics</li>
        </ul>

        <p><strong>Next Steps:</strong></p>
        <ol>
            <li>Visit the manager login portal</li>
            <li>Enter your email and password</li>
            <li>Change your password immediately (required)</li>
            <li>Review your manager permissions</li>
            <li>Set up two-factor authentication (recommended)</li>
        </ol> --}}

        @if(!$isUpdate)
            <p>If you have any questions about your manager responsibilities or need assistance, please contact the system administrator.</p>
        @endif
    </div>

    <div class="footer">
        <p>This email contains sensitive information. Please handle with care.</p>
        <p>&copy; {{ date('Y') }} ProDrive Portal. All rights reserved.</p>
    </div>
</body>
</html>
