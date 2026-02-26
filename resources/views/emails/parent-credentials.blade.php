<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isUpdate ? 'Parent Account Updated' : 'Welcome to Our Parent Portal' }}</title>
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
            background-image: linear-gradient(135deg, #1D4C5C 0%, #2a6276 100%);
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
        .student-info-box {
            background: #f0f9ff;
            padding: 15px;
            border-radius: 8px;
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
        .parent-badge {
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
        <h1>{{ $isUpdate ? '🔄 Account Updated' : '👨‍👩‍👧‍👦 Welcome to Parent Portal' }}</h1>
        <div class="parent-badge">PARENT ACCESS</div>
        <p>{{ $isUpdate ? 'Your parent account has been updated' : 'Monitor your child\'s learning journey' }}</p>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $parent->name }}</strong>,</p>

        @if($isUpdate)
            <p>Your parent account details have been updated. Please find your updated login credentials below:</p>
        @else
            <p>Welcome to our parent portal! Your parent account has been created to help you stay connected with your child's educational progress. You can now monitor their courses, track progress, and communicate with instructors.</p>
        @endif

        <div class="student-info-box">
            <h3>👨‍🎓 Your Child's Information</h3>
            <div class="credential-item">
                <div class="credential-label">Student Name:</div>
                <div class="credential-value">{{ $student->first_name }} {{ $student->last_name }}</div>
            </div>
            <div class="credential-item">
                <div class="credential-label">Student ID:</div>
                <div class="credential-value">{{ $student->student_id }}</div>
            </div>
            @if($student->course)
            <div class="credential-item">
                <div class="credential-label">Enrolled Course:</div>
                <div class="credential-value">{{ $student->course->course_name }}</div>
            </div>
            @endif
        </div>

        <div class="credentials-box">
            <h3>🔐 Your Parent Portal Login Credentials</h3>

            <div class="credential-item">
                <div class="credential-label">Email Address:</div>
                <div class="credential-value">{{ $parent->email }}</div>
            </div>

            <div class="credential-item">
                <div class="credential-label">Password:</div>
                <div class="credential-value">{{ $parentPassword }}</div>
            </div>
        </div>

        <div class="warning">
            <strong>⚠️ Important Security Notice:</strong>
            <br>
            Please change your password after your first login. Your parent portal provides access to sensitive information about your child's education, so please keep your credentials secure.
        </div>

        {{-- <p><strong>Parent Portal Features:</strong></p>
        <ul>
            <li>View your child's course progress and grades</li>
            <li>Access course schedules and important dates</li>
            <li>Communicate with instructors and staff</li>
            <li>Receive notifications about your child's progress</li>
            <li>Review payment history and upcoming installments</li>
            <li>Access course materials and resources</li>
            <li>View attendance records</li>
        </ul>

        <p><strong>Next Steps:</strong></p>
        <ol>
            <li>Visit the parent portal login page</li>
            <li>Enter your email and password</li>
            <li>Change your password (recommended)</li>
            <li>Set up notification preferences</li>
            <li>Review your child's current course status</li>
            <li>Update your contact information if needed</li>
        </ol> --}}

        @if(!$isUpdate)
            <p>We believe that parent involvement is crucial for student success. Thank you for being part of your child's learning journey! If you have any questions or need assistance, please don't hesitate to contact us.</p>
        @endif

        <p>Together, we can help your child achieve their educational goals!</p>
    </div>

    <div class="footer">
        <p>This email contains sensitive information about your child's education. Please keep it secure.</p>
        <p>&copy; {{ date('Y') }} Learning Platform. All rights reserved.</p>
    </div>
</body>
</html>
