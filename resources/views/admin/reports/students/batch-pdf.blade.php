<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Batch Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 1140px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3490dc;
        }
        h1, h2, h3, h4 {
            color: #3490dc;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .student-section {
            margin-bottom: 40px;
            page-break-after: always;
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
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .progress-container {
            width: 100%;
            background-color: #f2f2f2;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .progress-bar {
            height: 24px;
            border-radius: 4px;
            line-height: 24px;
            color: white;
            text-align: center;
        }
        .progress-theory {
            background-color: #3490dc;
        }
        .progress-practical {
            background-color: #38c172;
        }
        .column {
            float: left;
            width: 50%;
        }
        .row:after {
            content: "";
            display: table;
            clear: both;
        }
        .badge {
            display: inline-block;
            padding: 3px 7px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 4px;
            color: white;
        }
        .badge-success {
            background-color: #38c172;
        }
        .badge-warning {
            background-color: #ffed4a;
            color: #333;
        }
        .badge-danger {
            background-color: #e3342f;
        }
        .badge-primary {
            background-color: #3490dc;
        }
        .badge-secondary {
            background-color: #6c757d;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        .summary-table {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Student Batch Report</h1>
            <p>Generated on: {{ date('F d, Y') }}</p>
            <p>Total Students: {{ count($students) }}</p>
        </div>

        <!-- Summary Table -->
        <div class="summary-table">
            <h2>Student Summary</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Status</th>
                        <th>Theory Progress</th>
                        <th>Practical Progress</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td>{{ $student->id }}</td>
                        <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                        <td>{{ $student->course ? $student->course->course_name : 'N/A' }}</td>
                        <td>{{ ucfirst($student->course_status ?: 'N/A') }}</td>
                        <td>
                            @php
                                $theoryPercentage = 0;
                                if ($student->course && $student->course->theory_hours > 0) {
                                    $theoryPercentage = round(($student->hours_theory / $student->course->theory_hours) * 100);
                                }
                            @endphp
                            {{ $theoryPercentage }}%
                        </td>
                        <td>
                            @php
                                $practicalPercentage = 0;
                                if ($student->course && $student->course->practical_hours > 0) {
                                    $practicalPercentage = round(($student->hours_practical / $student->course->practical_hours) * 100);
                                }
                            @endphp
                            {{ $practicalPercentage }}%
                        </td>
                        <td>{{ ucfirst($student->payment_status ?: 'N/A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Individual Student Details -->
        @foreach($students as $student)
        <div class="student-section">
            <h2>Student: {{ $student->first_name }} {{ $student->last_name }} (ID: {{ $student->id }})</h2>

            <!-- Student Information -->
            <h3>Student Information</h3>
            <div class="row">
                <div class="column">
                    <table>
                        <tr>
                            <th style="width:40%">ID:</th>
                            <td>{{ $student->id }}</td>
                        </tr>
                        <tr>
                            <th>Name:</th>
                            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $student->email }}</td>
                        </tr>
                        <tr>
                            <th>Contact:</th>
                            <td>{{ $student->student_contact }}</td>
                        </tr>
                    </table>
                </div>
                
                <div class="column">
                    <table>
                        <tr>
                            <th style="width:40%">Joining Date:</th>
                            <td>{{ $student->joining_date ? date('M d, Y', strtotime($student->joining_date)) : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Course Status:</th>
                            <td>{{ ucfirst($student->course_status ?: 'N/A') }}</td>
                        </tr>
                        <tr>
                            <th>Payment Status:</th>
                            <td>{{ ucfirst($student->payment_status ?: 'N/A') }}</td>
                        </tr>
                        <tr>
                            <th>Instructor:</th>
                            <td>{{ $student->instructor ? $student->instructor->instructor_name : 'Not Assigned' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Course Information -->
            @if($student->course)
            <h3>Course Details</h3>
            <table>
                <tr>
                    <th style="width:30%">Course Name:</th>
                    <td>{{ $student->course->course_name }}</td>
                </tr>
                <tr>
                    <th>Course Type:</th>
                    <td>{{ $student->course->course_type }}</td>
                </tr>
                <tr>
                    <th>Theory/Practical Hours:</th>
                    <td>{{ $student->course->theory_hours }} / {{ $student->course->practical_hours }}</td>
                </tr>
            </table>
            @endif

            <!-- Progress Information -->
            <h3>Course Progress</h3>
            
            @php
                $theoryPercentage = 0;
                $practicalPercentage = 0;
                
                if ($student->course && $student->course->theory_hours > 0) {
                    $theoryPercentage = round(($student->hours_theory / $student->course->theory_hours) * 100);
                }
                
                if ($student->course && $student->course->practical_hours > 0) {
                    $practicalPercentage = round(($student->hours_practical / $student->course->practical_hours) * 100);
                }
            @endphp
            
            <p><strong>Theory Progress:</strong></p>
            <div class="progress-container">
                <div class="progress-bar progress-theory" style="width: {{ $theoryPercentage }}%">
                    {{ $theoryPercentage }}%
                </div>
            </div>
            <p>
                <strong>Hours Completed:</strong> {{ $student->hours_theory }} / {{ $student->course ? $student->course->theory_hours : 0 }} hours
            </p>
            
            <p><strong>Practical Progress:</strong></p>
            <div class="progress-container">
                <div class="progress-bar progress-practical" style="width: {{ $practicalPercentage }}%">
                    {{ $practicalPercentage }}%
                </div>
            </div>
            <p>
                <strong>Hours Completed:</strong> {{ $student->hours_practical }} / {{ $student->course ? $student->course->practical_hours : 0 }} hours
            </p>

            <!-- Payment Information -->
            <h3>Payment Summary</h3>
            <table>
                <tr>
                    <th style="width:30%">Total Course Fee:</th>
                    <td>{{ $student->course ? '$' . number_format($student->course->course_price, 2) : 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Total Invoiced:</th>
                    <td>${{ number_format($student->invoices->sum('amount'), 2) }}</td>
                </tr>
                <tr>
                    <th>Total Paid:</th>
                    <td>
                        @php
                            $totalPaid = 0;
                            foreach ($student->invoices as $invoice) {
                                $totalPaid += $invoice->payments->where('status', 'completed')->sum('amount');
                            }
                        @endphp
                        ${{ number_format($totalPaid, 2) }}
                    </td>
                </tr>
                <tr>
                    <th>Pending Amount:</th>
                    <td>
                        @php
                            $pendingAmount = 0;
                            foreach ($student->invoices as $invoice) {
                                foreach ($invoice->installments as $installment) {
                                    if ($installment->status === 'pending') {
                                        $pendingAmount += $installment->amount;
                                    }
                                }
                            }
                        @endphp
                        ${{ number_format($pendingAmount, 2) }}
                    </td>
                </tr>
            </table>
        </div>
        @endforeach

        <div class="footer">
            <p>This report was generated automatically from the system. Please contact the admin for any queries.</p>
            <p>© {{ date('Y') }} Driving School Management System</p>
        </div>
    </div>
</body>
</html>