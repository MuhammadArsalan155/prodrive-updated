<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isUpdate ? 'Student Account Updated' : 'Welcome to Our Learning Platform' }}</title>
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
        .course-info-box {
            background: #e0f2f1;
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
        .student-badge {
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
        <h1>{{ $isUpdate ? '🔄 Account Updated' : '🎓 Welcome to Our ProDrive Academy Platform' }}</h1>
        <div class="student-badge">STUDENT ACCESS</div>
        <p>{{ $isUpdate ? 'Your student account has been updated' : 'Your learning journey begins here!' }}</p>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>,</p>

        @if($isUpdate)
            <p>Your student account details have been updated. Please find your updated login credentials below:</p>
        @else
            <p>Welcome to our ProDrive Academy Platform! Your student account has been successfully created. You can now access your courses, track your progress, and connect with instructors.</p>
        @endif

        <div class="credentials-box">
            <h3>🔐 Your Student Login Credentials</h3>



            <div class="credential-item">
                <div class="credential-label">Email Address:</div>
                <div class="credential-value">{{ $student->email }}</div>
            </div>

            <div class="credential-item">
                <div class="credential-label">Password:</div>
                <div class="credential-value">{{ $password }}</div>
            </div>
        </div>

        @if($student->course)
        <div class="course-info-box">
            <h3>📚 Your Course Information</h3>
            <div class="credential-item">
                <div class="credential-label">Course:</div>
                <div class="credential-value">{{ $student->course->course_name }}</div>
            </div>
            <div class="credential-item">
                <div class="credential-label">Course Type:</div>
                <div class="credential-value">{{ $student->course->course_type }}</div>
            </div>
            @if($student->instructor)
            <div class="credential-item">
                <div class="credential-label">Instructor:</div>
                <div class="credential-value">{{ $student->instructor->instructor_name }}</div>
            </div>
            @endif
            <div class="credential-item">
                <div class="credential-label">Start Date:</div>
                <div class="credential-value">{{ $student->course_date }}</div>
            </div>
        </div>
        @endif

        <div class="warning">
            <strong>⚠️ Important Security Notice:</strong>
            <br>
            Please change your password after your first login for security purposes. Keep your credentials safe and never share them with anyone except your parents/guardians.
        </div>

        {{-- <p><strong>What You Can Do:</strong></p>
        <ul>
            <li>Access your course materials and schedules</li>
            <li>Track your learning progress</li>
            <li>Communicate with instructors</li>
            <li>View your grades and assessments</li>
            <li>Access practice materials and resources</li>
        </ul>

        <p><strong>Next Steps:</strong></p>
        <ol>
            <li>Visit the student portal login page</li>
            <li>Enter your email and password</li>
            <li>Change your password (recommended)</li>
            <li>Complete your profile setup</li>
            <li>Review your course schedule</li>
            <li>Download any required course materials</li>
        </ol> --}}

        @if(!$isUpdate)
            <p>We're excited to have you join our learning community! If you have any questions or need help getting started, please don't hesitate to contact our support team.</p>
        @endif

        <p>Good luck with your studies!</p>
    </div>

    <div class="footer">
        <p>This email contains your personal login information. Please keep it secure.</p>
        <p>&copy; {{ date('Y') }} ProDrive Portal. All rights reserved.</p>
    </div>
</body>
</html>
