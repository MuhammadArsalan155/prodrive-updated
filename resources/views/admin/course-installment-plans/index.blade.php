@extends('layouts.master')
@section('content')

<!-- Begin Page Content -->
<div class="container-fluid px-4">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h1 class="h2 text-primary fw-bold">
            <i class="fas fa-calendar-alt me-2"></i>Course Installment Plans
        </h1>
        <div>
            <form action="{{ route('admin.course-installment-plans.default') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-info btn-sm me-2 rounded-pill" 
                        onclick="return confirm('Are you sure you want to create default installment plans?');">
                    <i class="fas fa-copy me-1"></i> Create Default Plans
                </button>
            </form>
            <a href="{{ route('admin.course-installment-plans.create') }}" class="btn btn-primary btn-sm rounded-pill">
                <i class="fas fa-plus me-1"></i> Add New Plan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- DataTales Example -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold">
                <i class="fas fa-list me-2"></i>Installment Plan List
            </h5>
        </div>
        
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Installments</th>
                            <th>First Installment %</th>
                            <th>Subsequent Installment %</th>
                            <th>Days Between</th>
                            <th>Course Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courseInstallmentPlans as $plan)
                            <tr>
                                <td>{{ $plan->id }}</td>
                                <td>{{ $plan->Name }}</td>
                                <td>{{ $plan->number_of_installments }}</td>
                                <td>{{ $plan->first_installment_percentage }}%</td>
                                <td>{{ $plan->subsequent_installment_percentage }}%</td>
                                <td>{{ $plan->days_between_installments }} days</td>
                                <td>{{ $plan->course_duration_months }} months</td>
                                <td>
                                    @if($plan->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.course-installment-plans.edit', $plan->id) }}" 
                                           class="btn btn-primary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.course-installment-plans.destroy', $plan) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this installment plan? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable with custom settings
    $('#dataTable').DataTable({
        order: [[0, 'desc']], // Sort by ID descending by default
        pageLength: 25, // Show 25 entries per page
        columnDefs: [
            { orderable: false, targets: [8] } // Disable sorting for action column
        ]
    });
    
    // Auto close alerts after 5 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
});
</script>
@endsection