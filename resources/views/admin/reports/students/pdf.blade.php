<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Report - {{ $student->first_name }} {{ $student->last_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #2d3748;
            background: #ffffff;
            padding: 15mm;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #3182ce;
        }

        .logo-section {
            display: flex;
            align-items: center;
        }

        .logo {
            width: 50px;
            height: 50px;
            background: #3182ce;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 16px;
            margin-right: 12px;
        }

        .company-info h1 {
            font-size: 20px;
            color: #3182ce;
            font-weight: 700;
            margin: 0 0 3px 0;
        }

        .company-info p {
            font-size: 10px;
            color: #718096;
            margin: 0;
        }

        .report-info {
            text-align: right;
            font-size: 9px;
            color: #718096;
        }

        /* Title */
        .report-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .report-title h1 {
            font-size: 22px;
            color: #2d3748;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .report-title p {
            color: #718096;
            font-size: 11px;
        }

        /* Sections */
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-header {
            font-size: 14px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid #3182ce;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }

        th {
            background: #f7fafc;
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
            color: #4a5568;
            border: 1px solid #e2e8f0;
            font-size: 9px;
        }

        td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }

        tr:nth-child(even) {
            background: #f8f9fa;
        }

        /* Layout */
        .two-column {
            display: flex;
            gap: 15px;
        }

        .column {
            flex: 1;
        }

        /* Progress Section */
        .progress-item {
            margin-bottom: 20px;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .progress-title {
            font-size: 12px;
            font-weight: 600;
            color: #4a5568;
        }

        .progress-percent {
            font-size: 12px;
            font-weight: 600;
            color: #3182ce;
        }

        .progress-bar-container {
            width: 100%;
            height: 16px;
            background: #edf2f7;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .progress-bar {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 8px;
            font-weight: 600;
        }

        .progress-theory {
            background: linear-gradient(90deg, #3182ce, #4299e1);
        }

        .progress-practical {
            background: linear-gradient(90deg, #38a169, #48bb78);
        }

        .progress-details {
            font-size: 9px;
            color: #718096;
            line-height: 1.3;
        }

        /* Info Cards */
        .info-cards {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }

        .info-card {
            flex: 1;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
            position: relative;
        }

        .info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 6px 6px 0 0;
        }

        .info-card.blue::before { background: #3182ce; }
        .info-card.green::before { background: #38a169; }
        .info-card.orange::before { background: #ed8936; }

        .info-card-label {
            font-size: 9px;
            color: #718096;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .info-card-value {
            font-size: 18px;
            font-weight: 700;
            color: #2d3748;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
            border-radius: 10px;
            color: white;
        }

        .badge-success { background: #38a169; }
        .badge-warning { background: #ed8936; }
        .badge-danger { background: #e53e3e; }
        .badge-primary { background: #3182ce; }
        .badge-secondary { background: #718096; }

        /* Schedule Box */
        .schedule-box {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            margin: 10px 0;
        }

        .schedule-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .schedule-item {
            display: flex;
            flex-direction: column;
        }

        .schedule-label {
            font-size: 8px;
            color: #718096;
            font-weight: 600;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .schedule-value {
            font-size: 10px;
            color: #2d3748;
            font-weight: 500;
        }

        /* Cards */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .card-header {
            background: #f7fafc;
            padding: 10px 15px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 11px;
            font-weight: 600;
            color: #2d3748;
        }

        .card-body {
            padding: 15px;
        }

        /* Callouts */
        .callout {
            border-left: 3px solid #3182ce;
            background: #ebf8ff;
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 0 3px 3px 0;
        }

        .callout-warning {
            border-left-color: #ed8936;
            background: #fffaf0;
        }

        .callout-success {
            border-left-color: #38a169;
            background: #f0fff4;
        }

        .callout h5 {
            font-size: 10px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .callout p {
            font-size: 9px;
            color: #4a5568;
            margin: 0;
            line-height: 1.4;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            font-size: 8px;
            color: #718096;
        }

        /* Utilities */
        .text-bold { font-weight: 600; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .mb-10 { margin-bottom: 10px; }
        .mb-15 { margin-bottom: 15px; }
        .mt-15 { margin-top: 15px; }

        /* Status Fixes */
        .status-display {
            text-transform: capitalize;
        }

        .amount-display {
            font-weight: 600;
            color: #2d3748;
        }

        @media print {
            body { padding: 10mm; }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="logo-section">
            <div class="logo">
                {{-- <img src="{{ asset('images/prodrive-logo.png') }}" alt="PD" style="width: 35px; height: 35px; border-radius: 3px;" onerror="this.style.display='none'; this.parentNode.innerHTML='PD';"> --}}
            </div>
            <div class="company-info">
                <h1>PRODRIVE</h1>
                <p>Professional Driving School</p>
            </div>
        </div>
        <div class="report-info">
            <div><strong>Student Progress Report</strong></div>
            <div>Generated: {{ date('M d, Y') }}</div>
            <div>Time: {{ date('h:i A') }}</div>
        </div>
    </div>

    <!-- Title -->
    <div class="report-title">
        <h1>Student Academic & Financial Report</h1>
        <p>Comprehensive Progress Overview</p>
    </div>

    <!-- Student Information -->
    <div class="section">
        <div class="section-header">Student Information</div>
        <div class="two-column">
            <div class="column">
                <table>
                    <tr>
                        <th width="40%">Student ID</th>
                        <td>{{ $student->id }}</td>
                    </tr>
                    <tr>
                        <th>Full Name</th>
                        <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $student->email }}</td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td>{{ $student->student_contact ?: 'Not provided' }}</td>
                    </tr>
                    <tr>
                        <th>Date of Birth</th>
                        <td>{{ $student->student_dob ? date('M d, Y', strtotime($student->student_dob)) : 'Not provided' }}</td>
                    </tr>
                </table>
            </div>
            <div class="column">
                <table>
                    <tr>
                        <th width="40%">Enrollment Date</th>
                        <td>{{ $student->joining_date ? date('M d, Y', strtotime($student->joining_date)) : 'Not provided' }}</td>
                    </tr>
                    <tr>
                        <th>Course Status</th>
                        <td>
                            @php
                                $courseStatus = strtolower($student->course_status ?? 'not_started');
                                $statusMap = [
                                    'completed' => ['Completed', 'badge-success'],
                                    'in_progress' => ['In Progress', 'badge-primary'],
                                    'active' => ['Active', 'badge-primary'],
                                    'pending' => ['Pending', 'badge-warning'],
                                    'not_started' => ['Not Started', 'badge-secondary']
                                ];
                                $status = $statusMap[$courseStatus] ?? ['Unknown', 'badge-secondary'];
                            @endphp
                            <span class="badge {{ $status[1] }}">{{ $status[0] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Payment Status</th>
                        <td>
                            @php
                                $paymentStatus = strtolower($student->payment_status ?? 'pending');
                                $paymentMap = [
                                    'paid' => ['Paid', 'badge-success'],
                                    'full' => ['Paid', 'badge-success'],
                                    'partial' => ['Partial', 'badge-warning'],
                                    'pending' => ['Pending', 'badge-warning'],
                                    'overdue' => ['Overdue', 'badge-danger']
                                ];
                                $payment = $paymentMap[$paymentStatus] ?? ['Pending', 'badge-warning'];
                            @endphp
                            <span class="badge {{ $payment[1] }}">{{ $payment[0] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Guardian</th>
                        <td>{{ $student->parent_name ?: 'Not provided' }}</td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td>{{ $student->address ?: 'Not provided' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Course Information -->
    @if ($student->course)
        <div class="section">
            <div class="section-header">Course Information</div>
            <table>
                <tr>
                    <th width="25%">Course Name</th>
                    <td>{{ $student->course->course_name }}</td>
                    <th width="25%">Course Type</th>
                    <td class="status-display">{{ $student->course->course_type }}</td>
                </tr>
                <tr>
                    <th>Theory Hours Required</th>
                    <td>{{ $student->course->theory_hours ?? 0 }} hours</td>
                    <th>Practical Hours Required</th>
                    <td>{{ $student->course->practical_hours ?? 0 }} hours</td>
                </tr>
                <tr>
                    <th>Total Theory Classes</th>
                    <td>{{ $student->course->total_theory_classes ?? 'Not specified' }}</td>
                    <th>Total Practical Classes</th>
                    <td>{{ $student->course->total_practical_classes ?? 'Not specified' }}</td>
                </tr>
                <tr>
                    <th>Course Fee</th>
                    <td><span class="amount-display">${{ number_format($student->course->course_price ?? 0, 2) }}</span></td>
                    <th>Assigned Instructor</th>
                    <td>{{ $student->instructor->instructor_name ?? 'Not assigned' }}</td>
                </tr>
            </table>
        </div>
    @endif

    <!-- Academic Progress -->
    <div class="section">
        <div class="section-header">Academic Progress</div>

        @php
            // Fix progress calculation issues
            $theoryTotal = max(1, $courseProgress['theory']['total'] ?? 1);
            $theoryCompleted = $courseProgress['theory']['completed'] ?? 0;
            $theoryPercent = min(100, max(0, round(($theoryCompleted / $theoryTotal) * 100)));

            $practicalTotal = max(1, $courseProgress['practical']['total'] ?? 1);
            $practicalCompleted = $courseProgress['practical']['completed'] ?? 0;
            $practicalPercent = min(100, max(0, round(($practicalCompleted / $practicalTotal) * 100)));
        @endphp

        <!-- Theory Progress -->
        <div class="progress-item">
            <div class="progress-header">
                <div class="progress-title">Theory Progress</div>
                <div class="progress-percent">{{ $theoryPercent }}%</div>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar progress-theory" style="width: {{ $theoryPercent }}%">
                    @if($theoryPercent > 15){{ $theoryPercent }}%@endif
                </div>
            </div>
            <div class="progress-details">
                <strong>Hours Completed:</strong> {{ $theoryCompleted }} of {{ $theoryTotal }} hours<br>
                <strong>Status:</strong>
                @php
                    $theoryStatus = strtolower($student->theory_status ?? 'not_started');
                    $theoryStatusMap = [
                        'completed' => ['Completed', 'badge-success'],
                        'in_progress' => ['In Progress', 'badge-primary'],
                        'active' => ['Active', 'badge-primary'],
                        'not_started' => ['Not Started', 'badge-secondary']
                    ];
                    $theoryDisplayStatus = $theoryStatusMap[$theoryStatus] ?? ['Not Started', 'badge-secondary'];
                @endphp
                <span class="badge {{ $theoryDisplayStatus[1] }}">{{ $theoryDisplayStatus[0] }}</span>
                @if ($student->theory_completion_date)
                    <span style="color: #718096;">(Completed: {{ date('M d, Y', strtotime($student->theory_completion_date)) }})</span>
                @endif
            </div>
        </div>

        <!-- Practical Progress -->
        <div class="progress-item">
            <div class="progress-header">
                <div class="progress-title">Practical Progress</div>
                <div class="progress-percent">{{ $practicalPercent }}%</div>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar progress-practical" style="width: {{ $practicalPercent }}%">
                    @if($practicalPercent > 15){{ $practicalPercent }}%@endif
                </div>
            </div>
            <div class="progress-details">
                <strong>Hours Completed:</strong> {{ $practicalCompleted }} of {{ $practicalTotal }} hours<br>
                <strong>Status:</strong>
                @php
                    $practicalStatus = strtolower($student->practical_status ?? 'not_started');
                    $practicalStatusMap = [
                        'completed' => ['Completed', 'badge-success'],
                        'in_progress' => ['In Progress', 'badge-primary'],
                        'active' => ['Active', 'badge-primary'],
                        'not_started' => ['Not Started', 'badge-secondary']
                    ];
                    $practicalDisplayStatus = $practicalStatusMap[$practicalStatus] ?? ['Not Started', 'badge-secondary'];
                @endphp
                <span class="badge {{ $practicalDisplayStatus[1] }}">{{ $practicalDisplayStatus[0] }}</span>
                @if ($student->practical_completion_date)
                    <span style="color: #718096;">(Completed: {{ date('M d, Y', strtotime($student->practical_completion_date)) }})</span>
                @endif
            </div>
        </div>

        <!-- Schedule if exists -->
        @if ($student->practicalSchedule)
            <div style="margin-top: 15px;">
                <div style="font-size: 12px; font-weight: 600; color: #4a5568; margin-bottom: 8px;">Next Practical Session</div>
                <div class="schedule-box">
                    <div class="schedule-grid">
                        <div class="schedule-item">
                            <div class="schedule-label">Date</div>
                            <div class="schedule-value">{{ date('l, M d, Y', strtotime($student->practicalSchedule->date)) }}</div>
                        </div>
                        <div class="schedule-item">
                            <div class="schedule-label">Time</div>
                            <div class="schedule-value">{{ date('h:i A', strtotime($student->practicalSchedule->start_time)) }} - {{ date('h:i A', strtotime($student->practicalSchedule->end_time)) }}</div>
                        </div>
                        <div class="schedule-item">
                            <div class="schedule-label">Instructor</div>
                            <div class="schedule-value">{{ $student->practicalSchedule->instructor->instructor_name ?? 'Not assigned' }}</div>
                        </div>
                        <div class="schedule-item">
                            <div class="schedule-label">Session Type</div>
                            <div class="schedule-value">{{ $student->practicalSchedule->session_type }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Financial Overview -->
    <div class="section">
        <div class="section-header">Financial Overview</div>

        @php
            // Fix financial calculation issues
            $totalBilled = max(0, $totalBilled ?? 0);
            $totalPaid = max(0, $totalPaid ?? 0);
            $pendingPayments = max(0, $pendingPayments ?? 0);

            // If all amounts are 0, use course price as total billed
            if ($totalBilled == 0 && isset($student->course->course_price)) {
                $totalBilled = $student->course->course_price;
                $pendingPayments = $totalBilled - $totalPaid;
            }
        @endphp

        <div class="info-cards">
            <div class="info-card blue">
                <div class="info-card-label">Total Course Fee</div>
                <div class="info-card-value">${{ number_format($totalBilled, 2) }}</div>
            </div>
            <div class="info-card green">
                <div class="info-card-label">Amount Paid</div>
                <div class="info-card-value">${{ number_format($totalPaid, 2) }}</div>
            </div>
            <div class="info-card orange">
                <div class="info-card-label">Outstanding Balance</div>
                <div class="info-card-value">${{ number_format($pendingPayments, 2) }}</div>
            </div>
        </div>

        <!-- Invoice Details -->
        <div style="font-size: 12px; font-weight: 600; color: #4a5568; margin-bottom: 10px;">Payment History</div>
        @if (count($student->invoices ?? []) > 0)
            @foreach ($student->invoices as $invoice)
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            Invoice #{{ $invoice->invoice_number }} - {{ date('M d, Y', strtotime($invoice->created_at)) }}
                        </div>
                        <span class="badge badge-{{ $invoice->status == 'paid' ? 'success' : 'warning' }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="two-column mb-10">
                            <div class="column">
                                <p><strong>Amount:</strong> <span class="amount-display">${{ number_format($invoice->amount ?? 0, 2) }}</span></p>
                                <p><strong>Course:</strong> {{ $invoice->course->course_name ?? 'Not specified' }}</p>
                            </div>
                        </div>

                        @if (count($invoice->installments ?? []) > 0)
                            <div style="font-size: 10px; font-weight: 600; margin: 10px 0 5px 0;">Installment Plan</div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Paid Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoice->installments as $installment)
                                        <tr>
                                            <td><span class="amount-display">${{ number_format($installment->amount ?? 0, 2) }}</span></td>
                                            <td>{{ date('M d, Y', strtotime($installment->due_date)) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $installment->status == 'paid' ? 'success' : ($installment->is_overdue ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($installment->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $installment->paid_at ? date('M d, Y', strtotime($installment->paid_at)) : 'Not paid' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                        @if (count($invoice->payments ?? []) > 0)
                            <div style="font-size: 10px; font-weight: 600; margin: 10px 0 5px 0;">Payment Records</div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoice->payments as $payment)
                                        <tr>
                                            <td><span class="amount-display">${{ number_format($payment->amount ?? 0, 2) }}</span></td>
                                            <td>{{ $payment->paymentMethod->name ?? 'Not specified' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $payment->status == 'completed' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                            <td>{{ date('M d, Y', strtotime($payment->created_at)) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="callout">
                <h5>Payment Information</h5>
                <p>No payment records available. Please contact the office for payment details.</p>
            </div>
        @endif
    </div>

    <!-- Performance Reports -->
    @if (count($progressReports ?? []) > 0)
        <div class="section">
            <div class="section-header">Instructor Reports</div>
            @foreach ($progressReports as $report)
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            Report - {{ date('M d, Y', strtotime($report->created_at)) }}
                        </div>
                        @if ($report->rating)
                            <span class="badge badge-primary">{{ $report->rating }}/5 Stars</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="two-column mb-10">
                            <div class="column">
                                <p><strong>Instructor:</strong> {{ $report->instructor->instructor_name ?? 'Not specified' }}</p>
                            </div>
                            <div class="column">
                                <p><strong>Course:</strong> {{ $report->course->course_name ?? 'Not specified' }}</p>
                            </div>
                        </div>

                        @if ($report->performance_notes)
                            <div class="callout">
                                <h5>Performance Notes</h5>
                                <p>{{ $report->performance_notes }}</p>
                            </div>
                        @endif

                        @if ($report->areas_of_improvement)
                            <div class="callout callout-warning">
                                <h5>Areas for Improvement</h5>
                                <p>{{ $report->areas_of_improvement }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Summary -->
    <div class="section">
        <div class="section-header">Summary</div>
        <div class="two-column">
            <div class="column">
                <div class="callout callout-success">
                    <h5>Academic Progress</h5>
                    <p><strong>Theory:</strong> {{ $theoryPercent }}% complete ({{ $theoryCompleted }}/{{ $theoryTotal }} hours)</p>
                    <p><strong>Practical:</strong> {{ $practicalPercent }}% complete ({{ $practicalCompleted }}/{{ $practicalTotal }} hours)</p>
                    <p><strong>Overall:</strong> {{ $theoryDisplayStatus[0] ?? 'Not Started' }}</p>
                </div>
            </div>
            <div class="column">
                <div class="callout">
                    <h5>Financial Status</h5>
                    <p><strong>Course Fee:</strong> ${{ number_format($totalBilled, 2) }}</p>
                    <p><strong>Amount Paid:</strong> ${{ number_format($totalPaid, 2) }}</p>
                    <p><strong>Balance Due:</strong> ${{ number_format($pendingPayments, 2) }}</p>
                    <p><strong>Status:</strong> {{ $pendingPayments <= 0 ? 'Fully Paid' : 'Payment Due' }}</p>
                </div>
            </div>
        </div>

        @if ($student->instructor)
            <div class="callout mt-15">
                <h5>Contact Information</h5>
                <p><strong>Primary Instructor:</strong> {{ $student->instructor->instructor_name }}</p>
                <p><strong>School Contact:</strong> info@prodrive.com | (555) 123-4567</p>
                <p>For questions about progress or scheduling, please contact your instructor or our office.</p>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <div><strong>© {{ date('Y') }} Prodrive Driving School</strong> | Professional Driver Education</div>
        <div>Email: info@prodrive.com | Phone: (555) 123-4567 | Website: www.prodrive.com</div>
        <div>This report was automatically generated from our student management system.</div>
    </div>
</body>
</html>
