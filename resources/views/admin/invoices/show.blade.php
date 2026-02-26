@extends('layouts.master')

@section('content')
    <div class="container-fluid px-4">
        <!-- Page Heading -->
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h1 class="h2 text-primary fw-bold">
                <i class="fas fa-file-invoice me-2"></i> Invoice Details
            </h1>
            <div>
                <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="btn btn-warning btn-sm rounded-pill me-2">
                    <i class="fas fa-edit me-1"></i> Edit Invoice
                </a>
                <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary btn-sm rounded-pill">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Invoice Information -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="m-0 font-weight-bold">
                            <i class="fas fa-info-circle me-2"></i> Invoice Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold text-muted">Invoice Number:</td>
                                        <td class="text-primary fw-bold">{{ $invoice->invoice_number }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Status:</td>
                                        <td>
                                            @if($invoice->status == 'pending')
                                                <span class="badge bg-warning text-dark px-3 py-2">
                                                    <i class="fas fa-clock me-1"></i>Pending
                                                </span>
                                            @elseif($invoice->status == 'paid')
                                                <span class="badge bg-success px-3 py-2">
                                                    <i class="fas fa-check-circle me-1"></i>Paid
                                                </span>
                                            @elseif($invoice->status == 'cancelled')
                                                <span class="badge bg-danger px-3 py-2">
                                                    <i class="fas fa-times-circle me-1"></i>Cancelled
                                                </span>
                                            @elseif($invoice->status == 'overdue')
                                                <span class="badge bg-danger px-3 py-2">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Overdue
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Total Amount:</td>
                                        <td class="text-success fw-bold fs-5">${{ number_format($invoice->amount, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold text-muted">Created Date:</td>
                                        <td>{{ $invoice->created_at->format('M d, Y h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Last Updated:</td>
                                        <td>{{ $invoice->updated_at->format('M d, Y h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Payment Progress:</td>
                                        <td>
                                            @php
                                                $totalPaid = $invoice->payments->where('status', 'completed')->sum('amount');
                                                $paymentPercentage = $invoice->amount > 0 ? ($totalPaid / $invoice->amount) * 100 : 0;
                                            @endphp
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                     style="width: {{ $paymentPercentage }}%">
                                                    {{ number_format($paymentPercentage, 1) }}%
                                                </div>
                                            </div>
                                            <small class="text-muted">${{ number_format($totalPaid, 2) }} of ${{ number_format($invoice->amount, 2) }} paid</small>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-gradient-info text-white">
                        <h5 class="m-0 font-weight-bold">
                            <i class="fas fa-user-graduate me-2"></i> Student Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold text-muted">Student Name:</td>
                                        <td>{{ $invoice->student->first_name }} {{ $invoice->student->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Email:</td>
                                        <td>{{ $invoice->student->email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Contact:</td>
                                        <td>{{ $invoice->student->student_contact ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold text-muted">Course Status:</td>
                                        <td>
                                            <span class="badge bg-{{ $invoice->student->course_status == 'active' ? 'success' : ($invoice->student->course_status == 'completed' ? 'primary' : 'secondary') }}">
                                                {{ ucfirst($invoice->student->course_status ?? 'N/A') }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Payment Status:</td>
                                        <td>
                                            <span class="badge bg-{{ $invoice->student->payment_status == 'paid' ? 'success' : ($invoice->student->payment_status == 'partial' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($invoice->student->payment_status ?? 'N/A') }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Joining Date:</td>
                                        <td>{{ $invoice->student->joining_date ? \Carbon\Carbon::parse($invoice->student->joining_date)->format('M d, Y') : 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-gradient-success text-white">
                        <h5 class="m-0 font-weight-bold">
                            <i class="fas fa-book me-2"></i> Course Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold text-muted">Course Name:</td>
                                        <td class="fw-bold">{{ $invoice->course->course_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Course Type:</td>
                                        <td>{{ $invoice->course->course_type ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Course Price:</td>
                                        <td class="text-success fw-bold">${{ number_format($invoice->course->course_price, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold text-muted">Theory Hours:</td>
                                        <td>{{ $invoice->course->theory_hours ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Practical Hours:</td>
                                        <td>{{ $invoice->course->practical_hours ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Installment Available:</td>
                                        <td>
                                            <span class="badge bg-{{ $invoice->course->has_installment_plan ? 'success' : 'secondary' }}">
                                                {{ $invoice->course->has_installment_plan ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-gradient-dark text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-cogs me-2"></i> Quick Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit Invoice
                            </a>
                            <button class="btn btn-info btn-sm" onclick="printInvoice()">
                                <i class="fas fa-print me-1"></i> Print Invoice
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="downloadPDF()">
                                <i class="fas fa-file-pdf me-1"></i> Download PDF
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-gradient-primary text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-chart-pie me-2"></i> Payment Summary
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                            $totalPaid = $invoice->payments->where('status', 'completed')->sum('amount');
                            $remainingAmount = $invoice->amount - $totalPaid;
                        @endphp
                        <div class="row text-center">
                            <div class="col-12 mb-3">
                                <div class="border rounded p-3">
                                    <h6 class="text-muted mb-1">Total Amount</h6>
                                    <h4 class="text-primary mb-0">${{ number_format($invoice->amount, 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="border rounded p-3">
                                    <h6 class="text-muted mb-1">Paid</h6>
                                    <h5 class="text-success mb-0">${{ number_format($totalPaid, 2) }}</h5>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="border rounded p-3">
                                    <h6 class="text-muted mb-1">Remaining</h6>
                                    <h5 class="text-danger mb-0">${{ number_format($remainingAmount, 2) }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-gradient-secondary text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-history me-2"></i> Recent Activity
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-plus-circle text-success"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Invoice Created</h6>
                                        <small class="text-muted">{{ $invoice->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                            @if($invoice->updated_at != $invoice->created_at)
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-edit text-warning"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Invoice Updated</h6>
                                            <small class="text-muted">{{ $invoice->updated_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @foreach($invoice->payments->take(3) as $payment)
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-money-bill-wave text-{{ $payment->status == 'completed' ? 'success' : 'warning' }}"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Payment {{ ucfirst($payment->status) }}</h6>
                                            <small class="text-muted">${{ number_format($payment->amount, 2) }} - {{ $payment->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Installments Section (if applicable) -->
        @if($invoice->installments->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-gradient-warning text-dark">
                            <h5 class="m-0 font-weight-bold">
                                <i class="fas fa-calendar-alt me-2"></i> Installment Schedule
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Installment #</th>
                                            <th>Amount</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Payment Date</th>
                                            <th>Days Until Due</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoice->installments as $installment)
                                            @php
                                                $dueDate = \Carbon\Carbon::parse($installment->due_date);
                                                $daysUntilDue = $dueDate->diffInDays(now(), false);
                                                $isOverdue = $daysUntilDue > 0 && $installment->status !== 'paid';
                                            @endphp
                                            <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                                <td>
                                                    <strong>{{ $installment->installment_number }}</strong>
                                                </td>
                                                <td class="text-success fw-bold">
                                                    ${{ number_format($installment->amount, 2) }}
                                                </td>
                                                <td>
                                                    {{ $dueDate->format('M d, Y') }}
                                                    <br>
                                                    <small class="text-muted">{{ $dueDate->format('l') }}</small>
                                                </td>
                                                <td>
                                                    @if($installment->status == 'paid')
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>Paid
                                                        </span>
                                                    @elseif($installment->status == 'pending')
                                                        @if($isOverdue)
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>Overdue
                                                            </span>
                                                        @else
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="fas fa-clock me-1"></i>Pending
                                                            </span>
                                                        @endif
                                                    @elseif($installment->status == 'cancelled')
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-times me-1"></i>Cancelled
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($installment->payment_date)
                                                        {{ \Carbon\Carbon::parse($installment->payment_date)->format('M d, Y') }}
                                                        <br>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($installment->payment_date)->diffForHumans() }}</small>
                                                    @else
                                                        <span class="text-muted">Not paid</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($installment->status !== 'paid')
                                                        @if($isOverdue)
                                                            <span class="text-danger fw-bold">
                                                                {{ abs($daysUntilDue) }} days overdue
                                                            </span>
                                                        @elseif($daysUntilDue < 0)
                                                            <span class="text-info">
                                                                {{ abs($daysUntilDue) }} days remaining
                                                            </span>
                                                        @else
                                                            <span class="text-warning fw-bold">
                                                                Due today
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="text-success">✓ Completed</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Payments Section -->
        @if($invoice->payments->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-gradient-success text-white">
                            <h5 class="m-0 font-weight-bold">
                                <i class="fas fa-credit-card me-2"></i> Payment History
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Payment Date</th>
                                            <th>Transaction ID</th>
                                            <th>Payment Method</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoice->payments as $payment)
                                            <tr>
                                                <td>
                                                    {{ $payment->created_at->format('M d, Y') }}
                                                    <br>
                                                    <small class="text-muted">{{ $payment->created_at->format('h:i A') }}</small>
                                                </td>
                                                <td>
                                                    <code>{{ $payment->transaction_id ?? 'N/A' }}</code>
                                                </td>
                                                <td>
                                                    @if($payment->paymentMethod)
                                                        <div class="d-flex align-items-center">
                                                            @if($payment->paymentMethod->logo_url)
                                                                <img src="{{ $payment->paymentMethod->logo_url }}"
                                                                     alt="{{ $payment->paymentMethod->name }}"
                                                                     class="me-2" style="height: 20px;">
                                                            @endif
                                                            {{ $payment->paymentMethod->name }}
                                                        </div>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td class="text-success fw-bold">
                                                    ${{ number_format($payment->amount, 2) }}
                                                </td>
                                                <td>
                                                    @if($payment->status == 'completed')
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check-circle me-1"></i>Completed
                                                        </span>
                                                    @elseif($payment->status == 'pending')
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="fas fa-clock me-1"></i>Pending
                                                        </span>
                                                    @elseif($payment->status == 'failed')
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times-circle me-1"></i>Failed
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($payment->payment_details)
                                                        <button class="btn btn-sm btn-outline-info"
                                                                data-bs-toggle="tooltip"
                                                                title="{{ json_encode($payment->payment_details) }}">
                                                            <i class="fas fa-info-circle"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-muted">No details</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Payments Yet</h5>
                            <p class="text-muted">No payments have been made for this invoice.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
<script>
    function printInvoice() {
        window.print();
    }

    function downloadPDF() {
        // You can implement PDF generation here
        // For now, we'll show an alert
        alert('PDF download functionality will be implemented soon.');

        // Example implementation would be:
        // window.location.href = "{{ route('admin.invoices.pdf', $invoice->id) }}";
    }

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Print styles
    const printStyles = `
        <style media="print">
            .btn, .dropdown, .border-bottom, nav, .sidebar { display: none !important; }
            .card { border: 1px solid #ddd !important; margin-bottom: 20px; }
            .container-fluid { margin: 0; padding: 0; }
            body { font-size: 12px; }
            .h2 { font-size: 18px; }
            .card-header { background-color: #f8f9fa !important; color: #000 !important; }
        </style>
    `;

    document.head.insertAdjacentHTML('beforeend', printStyles);
</script>
@endsection
