<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $success ? 'Registration Successful' : 'Registration Failed' }} - ProDrive</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6fa;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .result-card {
            background: #fff;
            border-radius: 16px;
            padding: 48px 40px;
            max-width: 520px;
            width: 100%;
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
            text-align: center;
        }
        .icon-circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            margin: 0 auto 24px;
        }
        .icon-success { background: #e8f8f0; color: #28a745; }
        .icon-error   { background: #fdecea; color: #dc3545; }
        .result-title { font-size: 1.6rem; font-weight: 700; margin-bottom: 12px; }
        .result-message { color: #555; font-size: 1rem; line-height: 1.6; }
        .info-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 16px 20px;
            margin: 20px 0;
            text-align: left;
        }
        .info-box .label { font-size: 0.8rem; color: #888; text-transform: uppercase; letter-spacing: .05em; }
        .info-box .value { font-size: 1rem; font-weight: 600; color: #333; }
        .btn-home {
            background: #1D4C5C;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 32px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-top: 8px;
            transition: background .2s;
        }
        .btn-home:hover { background: #2a6276; color: #fff; }
        .btn-retry {
            background: transparent;
            color: #1D4C5C;
            border: 2px solid #1D4C5C;
            border-radius: 8px;
            padding: 10px 28px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-top: 8px;
            transition: all .2s;
        }
        .btn-retry:hover { background: #1D4C5C; color: #fff; }
    </style>
</head>
<body>
    <div class="result-card">
        @if($success)
            <div class="icon-circle icon-success">
                <i class="fas fa-check"></i>
            </div>
            <div class="result-title text-success">Registration Successful!</div>
            <p class="result-message">{{ $message }}</p>

            @if($studentName || $courseName)
            <div class="info-box">
                @if($studentName)
                <div class="mb-2">
                    <div class="label">Student Name</div>
                    <div class="value">{{ $studentName }}</div>
                </div>
                @endif
                @if($courseName)
                <div>
                    <div class="label">Course Enrolled</div>
                    <div class="value">{{ $courseName }}</div>
                </div>
                @endif
            </div>
            @endif

            <p class="text-muted" style="font-size:.9rem;">Your login credentials have been sent to your email address. Please check your inbox.</p>
            <a href="{{ route('registerPage') }}" class="btn-home">
                <i class="fas fa-home me-2"></i>Back to Home
            </a>
        @else
            <div class="icon-circle icon-error">
                <i class="fas fa-times"></i>
            </div>
            <div class="result-title text-danger">{{ $title ?? 'Payment Failed' }}</div>
            <p class="result-message">{{ $message }}</p>

            <a href="{{ route('registerPage') }}" class="btn-retry">
                <i class="fas fa-redo me-2"></i>Try Again
            </a>
        @endif
    </div>
</body>
</html>
