<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>ProDrive Academy Certificate</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        body {
            font-family: 'Montserrat', 'Arial', sans-serif;
            color: #0c4059;
            margin: 0;
            padding: 0;
            background: white;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
        }

        .certificate-container {
            width: 297mm;
            height: 210mm;
            position: relative;
            box-sizing: border-box;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        .certificate-border {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 8mm solid #0c4059;
            box-sizing: border-box;
        }

        .inner-border {
            position: absolute;
            top: 8mm;
            left: 8mm;
            right: 8mm;
            bottom: 8mm;
            border: 1.5mm solid #FFD700;
            padding: 8mm;
            box-sizing: border-box;
        }

        .header {
            position: relative;
            height: 30mm;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .title {
            font-family: 'Cormorant Garamond', 'Georgia', serif;
            font-size: 32pt;
            font-weight: 700;
            color: #0c4059;
            text-transform: uppercase;
            margin: 0;
            text-align: center;
        }

        .content {
            position: relative;
            height: 120mm;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .subtitle {
            font-size: 12pt;
            color: #555;
            font-style: italic;
            margin: 0 0 3mm 0;
        }

        .name {
            font-family: 'Cormorant Garamond', 'Georgia', serif;
            font-size: 26pt;
            font-weight: 700;
            color: #0c4059;
            margin: 0 0 4mm 0;
            padding-bottom: 2mm;
            display: inline-block;
            border-bottom: 1mm solid #FFD700;
            min-width: 60mm;
        }

        .details {
            font-size: 12pt;
            margin: 1mm 0;
            color: #0c4059;
            line-height: 1.2;
        }

        .course-name {
            font-family: 'Cormorant Garamond', 'Georgia', serif;
            font-size: 20pt;
            font-weight: 700;
            color: #0c4059;
            margin: 2mm 0;
        }

        .date {
            font-size: 12pt;
            color: #0c4059;
            margin: 2mm 0;
        }

        .footer {
            position: absolute;
            bottom: 8mm;
            left: 8mm;
            right: 8mm;
            height: 40mm;
            display: flex;
            align-items: flex-end;
        }

        .certificate-info {
            flex: 1;
            text-align: center;
        }

        .certificate-id {
            font-size: 10pt;
            color: #666;
            margin: 0 0 1mm 0;
        }

        .footer-text {
            font-size: 8pt;
            color: #666;
            margin: 0;
            font-style: italic;
            line-height: 1.3;
        }

        .qr-code {
            width: 20mm;
            height: 20mm;
            margin: 2mm auto 0;
        }

        .seal {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 28mm;
            height: 45mm;
        }

        .seal-outer {
            position: absolute;
            width: 28mm;
            height: 28mm;
            border: 1mm solid #FFD700;
            border-radius: 50%;
        }

        .seal-inner {
            position: absolute;
            top: 3mm;
            left: 3mm;
            width: 22mm;
            height: 22mm;
            border: 0.5mm dashed #0c4059;
            border-radius: 50%;
        }

        .seal-text {
            position: absolute;
            width: 100%;
            text-align: center;
            top: 10mm;
            font-family: 'Arial', sans-serif;
            font-size: 8pt;
            color: #0c4059;
            font-weight: bold;
        }

        .logo {
            display: none;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="certificate-border">
            <div class="inner-border">
                <div class="header">
                    <h1 class="title">Certificate of Completion</h1>
                </div>
                <div class="content">
                    <p class="subtitle">This is to certify that</p>
                    <h2 class="name">{{$certificate->student->first_name}} {{$certificate->student->last_name}}</h2>
                    <p class="details">has successfully completed the course requirements for:</p>
                    <h3 class="course-name">{{$course->name}}</h3>
                    <p class="details">with {{$course->theory_hours ?? 4}} hours of theory and {{$course->practical_hours ?? 0}} hours of practical training as prescribed by ProDrive Academy.</p>
                    <p class="date">Issued on: {{$issue_date}}</p>
                </div>
                <div class="footer">
                    <div class="certificate-info">
                        {{-- <p class="certificate-id">Certificate Number: {{$certificate->certificate_number}}</p>
                        <p class="footer-text">ProDrive Academy - Professional Driving Education</p> --}}
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{urlencode($certificate->verification_url)}}" class="qr-code" alt="Verification QR Code">
                        <p class="footer-text">Scan to verify certificate</p>
                    </div>
                    <div class="seal">
                        <div class="seal-outer"></div>
                        <div class="seal-inner"></div>
                        <div class="seal-text">Pro-Drive<br>Academy</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
