<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Report - {{ $student->first_name }} {{ $student->last_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #ddd;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .report-title {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .report-date {
            font-size: 14px;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 15px;
        }
        .student-info {
            display: flex;
            margin-bottom: 20px;
        }
        .student-info-left {
            width: 30%;
        }
        .student-info-right {
            width: 70%;
        }
        .student-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .student-id {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        .info-row {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .progress-bar-container {
            background-color: #f0f0f0;
            height: 20px;
            border-radius: 10px;
            margin-bottom: 5px;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            background-color: #4CAF50;
            text-align: center;
            color: white;
            font-size: 12px;
            line-height: 20px;
        }
        .progress-bar.theory {
            background-color: #2196F3;
        }
        .progress-bar.practical {
            background-color: #FFC107;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .financial-summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .financial-box {
            width: 30%;
            text-align: center;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }
        .financial-box-amount {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .financial-box-label {
            font-size: 14px;
            color: #666;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .badge-success {
            background-color: #4CAF50;
        }
        .badge-warning {
            background-color: #FFC107;
            color: #333;
        }
        .badge-secondary {
            background-color: #6c757d;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Driving School Management System</div>
            <div class="report-title">Student Progress Report</div>
            <div class="report-date">Generated on: {{ date('F d, Y') }}</div>
        </div>

        <div class="section">
            <div class="section-title">Student Information</div>
            <div class="student-info">
                <div class="student-info-left">
                    <div class="student-name">{{ $student->first_name }} {{ $student->last_name }}</div>
                    <div class="student-id">Student ID: {{ $student->id }}</div>
                </div>
                <div class="student-info-right">
                    <div class="info-row">
                        <span class="info-label">Email:</span> {{ $student->email }}
                    </div>
                    <div class="info-row">
                        <span class="info-label">Contact:</span> {{ $student->student_contact ?: 'Not provided' }}
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date of Birth:</span> {{ $student->student_dob ? date('M d, Y', strtotime($student->student_dob)) : 'Not provided' }}
                    </div>
                    <div class="info-row">
                        <span class="info-label">Address:</span> {{ $student->address ?: 'Not provided' }}
                    </div>
                    <div class="info-row">
                        <span class="info-label">Joining Date:</span> {{ $student->joining_date ? date('M d, Y', strtotime($student->joining_date)) : 'Not provided' }}
                    </div>
                    <div class="info-row">
                        <span class="info-label">Expected Completion:</span> {{ $student->completion_date ? date('M d, Y', strtotime($student->completion_date)) : 'Not set' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Course Information</div>
            <div class="info-row">
                <span class="info-label">Course:</span> {{ $student->course->title ?? 'No Course Assigned' }}
            </div>
            <div class="info-row">
                <span class="info-label">Course Description:</span> {{ $student->course->description ?? 'No description available' }}
            </div>
            <div class="info-row">
                <span class="info-label">Instructor:</span> {{ $student->instructor->instructor_name ?? 'Not Assigned' }}
            </div>
            <div class="info-row">
                <span class="info-label">Course Status:</span> 
                <span class="badge badge-{{ $student->course_status == 'completed' ? 'success' : ($student->course_status == 'in_progress' ? 'warning' : 'secondary') }}">
                    {{ ucfirst(str_replace('_', ' ', $student->course_status ?? 'Not Started')) }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Status:</span>
                <span class="badge badge-{{ $student->payment_status == 'paid' ? 'success' : ($student->payment_status == 'partial' ? 'warning' : 'secondary') }}">
                    {{ ucfirst($student->payment_status ?? 'Not Paid') }}
                </span>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Course Progress</div>
            
            <div class="info-row">
                <span class="info-label">Theory Progress:</span> {{ $courseProgress['theory']['completed'] }} / {{ $courseProgress['theory']['total'] }} hours
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar theory" style="width: {{ $courseProgress['theory']['percentage'] }}%">
                    {{ $courseProgress['theory']['percentage'] }}%
                </div>
            </div>
            <div class="info-row">
                <span class="info-label">Theory Status:</span>
                @if($student->theory_status == 'completed')
                    Completed on {{ date('M d, Y', strtotime($student->theory_completion_date)) }}
                @elseif($student->theory_status == 'in_progress')
                    In progress
                @else
                    Not started
                @endif
            </div>
            
            <div class="info-row" style="margin-top: 20px;">
                <span class="info-label">Practical Progress:</span> {{ $courseProgress['practical']['completed'] }} / {{ $courseProgress['practical']['total'] }} hours
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar practical" style="width: {{ $courseProgress['practical']['percentage'] }}%">
                    {{ $courseProgress['practical']['percentage'] }}%
                </div>
            </div>
            <div class="info-row">
                <span class="info-label">Practical Status:</span>
                @if($student->practical_status == 'completed')
                    Completed on {{ date('M d, Y', strtotime($student->practical_completion_date)) }}
                @elseif($student->practical_status == 'in_progress')
                    In progress
                @else
                    Not started
                @endif
            </div>
            
            <div class="info-row" style="margin-top: 20px;">
                <span class="info-label">Overall Progress:</span> 
                {{ $courseProgress['theory']['completed'] + $courseProgress['practical']['completed'] }} / 
                {{ $courseProgress['theory']['total'] + $courseProgress['practical']['total'] }} hours
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width: 
                    {{ ($courseProgress['theory']['total'] + $courseProgress['practical']['total'] > 0) 
                    ? round((($courseProgress['theory']['completed'] + $courseProgress['practical']['completed']) / 
                    ($courseProgress['theory']['total'] + $courseProgress['practical']['total'])) * 100) 
                    : 0 }}%">
                    {{ ($courseProgress['theory']['total'] + $courseProgress['practical']['total'] > 0) 
                    ? round((($courseProgress['theory']['completed'] + $courseProgress['practical']['completed']) / 
                    ($courseProgress['theory']['total'] + $courseProgress['practical']['total'])) * 100) 
                    : 0 }}%
                </div>
            </div>
        </div>

        <div class="page-break"></div>

        <div class="section">
            <div class="section-title">Financial Summary</div>
            
            <div class="financial-summary">
                <div class="financial-box">
                    <div class="financial-box-amount">{{ number_format($totalBilled, 2) }}</div>
                    <div class="financial-box-label">Total Billed</div>
                </div>
                <div class="financial-box">
                    <div class="financial-box-amount">{{ number_format($totalPaid, 2) }}</div>
                    <div class="financial-box-label">Total Paid</div>
                </div>
                <div class="financial-box">
                    <div class="financial-box-amount">{{ number_format($pendingPayments, 2) }}</div>
                    <div class="financial-box-label">Pending Payments</div>
                </div>
            </div>
            
            @if(count($student->invoices) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($student->invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ date('M d, Y', strtotime($invoice->created_at)) }}</td>
                                <td>{{ $invoice->description }}</td>
                                <td>{{ number_format($invoice->amount, 2) }}</td>
                                <td>
                                    @php
                                        $paidAmount = $invoice->payments->where('status', 'completed')->sum('amount');
                                        $status = 'unpaid';
                                        
                                        if ($paidAmount >= $invoice->amount) {
                                            $status = 'paid';
                                        } elseif ($paidAmount > 0) {
                                            $status = 'partial';
                                        }
                                    @endphp
                                    
                                    {{ ucfirst($status) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No invoices have been generated for this student.</p>
            @endif
        </div>

        <div class="section">
            <div class="section-title">Progress Reports</div>
            
            @if(count($progressReports) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Instructor</th>
                            <th>Rating</th>
                            <th>Type</th>
                            <th>Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($progressReports as $report)
                            <tr>
                                <td>{{ date('M d, Y', strtotime($report->created_at)) }}</td>
                                <td>{{ $report->instructor->instructor_name ?? 'N/A' }}</td>
                                <td>{{ $report->rating }}/5</td>
                                <td>{{ ucfirst($report->type ?? 'General') }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($report->comments, 100) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No progress reports have been submitted for this student.</p>
            @endif
        </div>

        <div class="footer">
            <p>This report was generated automatically by the Driving School Management System on {{ date('F d, Y') }}.</p>
            <p>For any inquiries, please contact the administration office.</p>
            <p>&copy; {{ date('Y') }} Driving School Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>