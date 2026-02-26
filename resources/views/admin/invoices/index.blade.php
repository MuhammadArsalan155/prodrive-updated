{{-- resources/views/invoices/index.blade.php --}}
@extends('layouts.master')

@section('content')
    <div class="container-fluid px-4">
        <!-- Page Heading -->
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h1 class="h2 text-primary fw-bold">
                <i class="fas fa-file-invoice-dollar me-2"></i> Invoice Management
            </h1>
            <div class="btn-group">
                <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary btn-sm rounded-pill">
                    <i class="fas fa-plus me-1"></i> Create New Invoice
                </a>
                {{-- <a href="{{ route('admin.invoices.overdue') }}" class="btn btn-outline-danger btn-sm rounded-pill">
                    <i class="fas fa-exclamation-triangle me-1"></i> Overdue
                </a>
                <a href="{{ route('invoices.dashboard') }}" class="btn btn-outline-info btn-sm rounded-pill">
                    <i class="fas fa-chart-bar me-1"></i> Dashboard
                </a> --}}
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-0">{{ $invoices->total() }}</h5>
                                <p class="mb-0 opacity-75">Total Invoices</p>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-file-invoice fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-0">{{ $invoices->where('status', 'pending')->count() }}</h5>
                                <p class="mb-0 opacity-75">Pending</p>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-clock fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-0">{{ $invoices->where('status', 'paid')->count() }}</h5>
                                <p class="mb-0 opacity-75">Paid</p>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-0">{{ $invoices->where('is_overdue', true)->count() }}</h5>
                                <p class="mb-0 opacity-75">Overdue</p>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <!-- Card Header with Filters -->
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="m-0 font-weight-bold">
                            <i class="fas fa-list me-2"></i> All Invoices
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        <!-- Filters -->
                        <form method="GET" action="{{ route('admin.invoices.index') }}" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="search" class="form-label">Search</label>
                                    <input type="text" name="search" id="search" class="form-control"
                                           placeholder="Invoice #, student name..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="from_date" class="form-label">From Date</label>
                                    <input type="date" name="from_date" id="from_date" class="form-control"
                                           value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="to_date" class="form-label">To Date</label>
                                    <input type="date" name="to_date" id="to_date" class="form-control"
                                           value="{{ request('to_date') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-outline-primary">
                                            <i class="fas fa-search me-1"></i> Filter
                                        </button>
                                        <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Invoices Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoices as $invoice)
                                        <tr class="{{ $invoice->is_overdue ? 'table-warning' : '' }}">
                                            <td>
                                                <strong class="text-primary">{{ $invoice->invoice_number }}</strong>
                                                @if($invoice->is_overdue)
                                                    <span class="badge bg-danger ms-1">Overdue</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $invoice->student->first_name }} {{ $invoice->student->last_name }}</strong>
                                                    <br><small class="text-muted">{{ $invoice->student->email }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-semibold">{{ $invoice->course->course_name }}</span>
                                                <br><small class="text-muted">{{ $invoice->course->course_type }}</small>
                                            </td>
                                            <td>
                                                <strong class="text-success">${{ number_format($invoice->amount, 2) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge {{ $invoice->is_installment_invoice ? 'bg-info' : 'bg-secondary' }}">
                                                    {{ $invoice->is_installment_invoice ? 'Installment' : 'Full Payment' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $invoice->status_badge_class }}">
                                                    {{ $invoice->status_display }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                         style="width: {{ $invoice->payment_progress_percentage }}%">
                                                    </div>
                                                </div>
                                                <small class="text-muted">
                                                    ${{ number_format($invoice->total_paid, 2) }} / ${{ number_format($invoice->amount, 2) }}
                                                </small>
                                            </td>
                                            <td>
                                                <small>{{ $invoice->created_at->format('M d, Y') }}</small>
                                                {{-- <br><small class="text-muted">{{ $invoice->getAgeInDays() }} days ago</small> --}}
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.invoices.show', $invoice->id) }}"
                                                       class="btn btn-outline-primary btn-sm" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    {{-- @if($invoice->canBeEdited())
                                                        <a href="{{ route('admin.invoices.edit', $invoice->id) }}"
                                                           class="btn btn-outline-secondary btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('invoices.pdf', $invoice->id) }}"
                                                       class="btn btn-outline-success btn-sm" title="Download PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a> --}}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                                    <p>No invoices found.</p>
                                                    <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary">
                                                        Create Your First Invoice
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($invoices->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $invoices->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
