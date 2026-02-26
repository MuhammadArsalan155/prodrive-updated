@extends('layouts.master')

@section('title', 'Financial Information')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Financial Information</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Financial Summary Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Financial Summary - {{ $student->first_name }} {{ $student->last_name }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="card bg-light h-100">
                                <div class="card-body text-center py-4">
                                    <h3 class="text-primary mb-1">{{ number_format($totalBilled, 2) }}</h3>
                                    <p class="text-muted">Total Course Fee</p>
                                    <i class="fas fa-file-invoice-dollar fa-2x text-primary mt-2"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="card bg-light h-100">
                                <div class="card-body text-center py-4">
                                    <h3 class="text-success mb-1">{{ number_format($totalPaid, 2) }}</h3>
                                    <p class="text-muted">Amount Paid</p>
                                    <i class="fas fa-check-circle fa-2x text-success mt-2"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light h-100">
                                <div class="card-body text-center py-4">
                                    <h3 class="text-danger mb-1">{{ number_format($pendingPayments, 2) }}</h3>
                                    <p class="text-muted">Pending Payments</p>
                                    <i class="fas fa-exclamation-circle fa-2x text-danger mt-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">Payment Status</h5>
                                <p class="mb-0">
                                    Current payment status: 
                                    <span class="badge bg-{{ $student->payment_status == 'paid' ? 'success' : ($student->payment_status == 'partial' ? 'warning' : 'danger') }} p-2">
                                        {{ ucfirst($student->payment_status ?? 'Not Paid') }}
                                    </span>
                                </p>
                                <p class="mb-0 mt-2">
                                    For any payment related queries, please contact the administration office.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices and Payments -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Invoices & Payment History</h5>
                </div>
                <div class="card-body">
                    @if(count($invoices) > 0)
                        <ul class="nav nav-tabs mb-4" id="financialTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices" type="button" role="tab" aria-controls="invoices" aria-selected="true">Invoices</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="installments-tab" data-bs-toggle="tab" data-bs-target="#installments" type="button" role="tab" aria-controls="installments" aria-selected="false">Installment Plan</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab" aria-controls="payments" aria-selected="false">Payment History</button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="financialTabContent">
                            <!-- Invoices Tab -->
                            <div class="tab-pane fade show active" id="invoices" role="tabpanel" aria-labelledby="invoices-tab">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Invoice #</th>
                                                <th>Date</th>
                                                <th>Description</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoices as $invoice)
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
                                                        
                                                        <span class="badge bg-{{ $status == 'paid' ? 'success' : ($status == 'partial' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary view-invoice" data-invoice-id="{{ $invoice->id }}" data-bs-toggle="modal" data-bs-target="#invoiceModal">
                                                            <i class="fas fa-eye me-1"></i> View
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Installments Tab -->
                            <div class="tab-pane fade" id="installments" role="tabpanel" aria-labelledby="installments-tab">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Installment #</th>
                                                <th>Invoice #</th>
                                                <th>Due Date</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $installmentCount = 0; @endphp
                                            @foreach($invoices as $invoice)
                                                @foreach($invoice->installments as $installment)
                                                    @php $installmentCount++; @endphp
                                                    <tr>
                                                        <td>{{ $installmentCount }}</td>
                                                        <td>{{ $invoice->invoice_number }}</td>
                                                        <td>{{ date('M d, Y', strtotime($installment->due_date)) }}</td>
                                                        <td>{{ number_format($installment->amount, 2) }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $installment->status == 'completed' ? 'success' : ($installment->status == 'pending' ? 'warning' : 'danger') }}">
                                                                {{ ucfirst($installment->status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                            
                                            @if($installmentCount == 0)
                                                <tr>
                                                    <td colspan="5" class="text-center">No installment plan available</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Payments Tab -->
                            <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Payment #</th>
                                                <th>Invoice #</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Method</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $paymentCount = 0; @endphp
                                            @foreach($invoices as $invoice)
                                                @foreach($invoice->payments as $payment)
                                                    @php $paymentCount++; @endphp
                                                    <tr>
                                                        <td>{{ $payment->id }}</td>
                                                        <td>{{ $invoice->invoice_number }}</td>
                                                        <td>{{ date('M d, Y', strtotime($payment->payment_date)) }}</td>
                                                        <td>{{ number_format($payment->amount, 2) }}</td>
                                                        <td>{{ ucfirst($payment->payment_method) }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $payment->status == 'completed' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger') }}">
                                                                {{ ucfirst($payment->status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                            
                                            @if($paymentCount == 0)
                                                <tr>
                                                    <td colspan="6" class="text-center">No payment records found</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                            <h5>No Invoice Records</h5>
                            <p class="text-muted">There are no financial records available for this student.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Modal -->
<div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="invoiceModalLabel">Invoice Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="invoiceDetails">
                    <!-- Invoice details will be loaded here -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading invoice details...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="printInvoice">
                    <i class="fas fa-print me-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle invoice view button clicks
        const viewInvoiceButtons = document.querySelectorAll('.view-invoice');
        viewInvoiceButtons.forEach(button => {
            button.addEventListener('click', function() {
                const invoiceId = this.getAttribute('data-invoice-id');
                // In a real application, you would load the invoice details via AJAX
                // For this example, we'll just show a placeholder
                const invoiceDetails = document.getElementById('invoiceDetails');
                
                // Simulate loading delay
                setTimeout(() => {
                    // This would normally be populated with data from the server
                    invoiceDetails.innerHTML = `
                        <div class="invoice-header text-center mb-4">
                            <h4>INVOICE</h4>
                            <p class="mb-0">Invoice #INV-${invoiceId}</p>
                            <p>Date: ${new Date().toLocaleDateString()}</p>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-6">
                                <h6>Billed To:</h6>
                                <p>
                                    ${document.querySelector('h5.mb-0').textContent.split('-')[1].trim()}<br>
                                    Student ID: ${document.querySelector('p.text-muted.mb-0')?.textContent.split(':')[1].trim() || 'N/A'}<br>
                                </p>
                            </div>
                            <div class="col-6 text-end">
                                <h6>From:</h6>
                                <p>
                                    Driving School<br>
                                    123 Education St<br>
                                    City, State ZIP<br>
                                    admin@drivingschool.com
                                </p>
                            </div>
                        </div>
                        
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Course Fee - ${document.querySelector('h5.card-title')?.textContent || 'Driving Course'}</td>
                                    <td class="text-end">$${Math.floor(Math.random() * 1000) + 500}.00</td>
                                </tr>
                                <tr>
                                    <td>Materials</td>
                                    <td class="text-end">$50.00</td>
                                </tr>
                                <tr>
                                    <td class="text-end"><strong>Total</strong></td>
                                    <td class="text-end"><strong>$${Math.floor(Math.random() * 1000) + 550}.00</strong></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="mt-4 pt-4 border-top">
                            <h6>Payment Terms:</h6>
                            <p>Payment is due within 30 days. Please make checks payable to Driving School or use our online payment system.</p>
                        </div>
                    `;
                }, 1000);
            });
        });
        
        // Handle print button
        document.getElementById('printInvoice').addEventListener('click', function() {
            const printContents = document.getElementById('invoiceDetails').innerHTML;
            const originalContents = document.body.innerHTML;
            
            document.body.innerHTML = `
                <div style="padding: 20px;">
                    ${printContents}
                </div>
            `;
            
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        });
    });
</script>
@endsection