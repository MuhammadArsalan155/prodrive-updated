<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification - ProDrive Academy</title>
    <link href="{{ asset('admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        .verification-box {
            max-width: 700px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .verification-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .verification-logo {
            width: 150px;
            margin-bottom: 20px;
        }
        .verification-title {
            font-size: 24px;
            font-weight: 600;
            color: #0c5460;
        }
        .verification-result {
            text-align: center;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .certificate-details {
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .certificate-detail-item {
            margin-bottom: 15px;
        }
        .certificate-detail-label {
            font-weight: 600;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="verification-box bg-white">
            <div class="verification-header">
                <img src="{{ asset('admin/img/Prodrive 4.png') }}" alt="ProDrive Academy Logo" class="verification-logo">
                <h1 class="verification-title">Certificate Verification</h1>
            </div>

            @if(isset($valid))
                @if($valid)
                    <div class="verification-result bg-success text-white">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h4>Valid Certificate</h4>
                        <p>This certificate has been verified as authentic and valid.</p>
                    </div>

                    <div class="certificate-details">
                        <h5 class="mb-3">Certificate Information</h5>
                        
                        <div class="row certificate-detail-item">
                            <div class="col-md-4 certificate-detail-label">Certificate Number:</div>
                            <div class="col-md-8">{{ $certificate->certificate_number }}</div>
                        </div>
                        
                        <div class="row certificate-detail-item">
                            <div class="col-md-4 certificate-detail-label">Issue Date:</div>
                            <div class="col-md-8">{{ $certificate->issue_date->format('F d, Y') }}</div>
                        </div>
                        
                        <div class="row certificate-detail-item">
                            <div class="col-md-4 certificate-detail-label">Student Name:</div>
                            <div class="col-md-8">{{ $certificate->student->first_name }} {{ $certificate->student->last_name }}</div>
                        </div>
                        
                        <div class="row certificate-detail-item">
                            <div class="col-md-4 certificate-detail-label">Course:</div>
                            <div class="col-md-8">{{ $certificate->course->course_name }}</div>
                        </div>
                        
                        <div class="row certificate-detail-item">
                            <div class="col-md-4 certificate-detail-label">Course Type:</div>
                            <div class="col-md-8">{{ $certificate->course->course_type }}</div>
                        </div>
                        
                        <div class="row certificate-detail-item">
                            <div class="col-md-4 certificate-detail-label">Training Hours:</div>
                            <div class="col-md-8">
                                Theory: {{ $certificate->course->theory_hours }} hours<br>
                                Practical: {{ $certificate->course->practical_hours }} hours
                            </div>
                        </div>
                    </div>
                @else
                    <div class="verification-result bg-danger text-white">
                        <i class="fas fa-times-circle fa-3x mb-3"></i>
                        <h4>Invalid Certificate</h4>
                        <p>The certificate number provided could not be verified. Please check the certificate number and try again.</p>
                    </div>
                @endif
            @else
                <form action="{{ route('certificate.verify') }}" method="GET" class="mb-4">
                    <div class="form-group">
                        <label for="certificate_number">Enter Certificate Number:</label>
                        <input type="text" name="certificate_number" id="certificate_number" class="form-control" 
                               placeholder="e.g. CERT-ABC12345" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Verify Certificate</button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Enter the certificate number to verify its authenticity.
                    </p>
                </div>
            @endif
            
            <div class="text-center mt-4">
                <p><a href="{{ url('/') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-home"></i> Return to Homepage
                </a></p>
            </div>
        </div>
    </div>

    <script src="{{ asset('admin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>