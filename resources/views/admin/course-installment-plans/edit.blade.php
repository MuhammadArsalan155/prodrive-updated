@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h1 class="h2 text-primary fw-bold">
            <i class="fas fa-calendar-edit me-2"></i>Edit Course Installment Plan
        </h1>
        <div>
            <a href="{{ route('admin.course-installment-plans.index') }}" class="btn btn-primary btn-sm rounded-pill">
                <i class="fas fa-list me-1"></i> Back to Installment Plans
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12">
            <div class="card border-0 shadow-sm mb-4">
                <!-- Card Header -->
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold">
                        <i class="fas fa-edit me-2"></i>Update Installment Plan
                    </h5>
                </div>
                
                <!-- Card Body -->
                <div class="card-body p-4">
                    <form 
                        action="{{ route('admin.course-installment-plans.update', $courseInstallmentPlan->id) }}" 
                        method="POST" 
                        id="installmentPlanForm"
                    >
                        @csrf
                        @method('PUT')
                        <div class="row g-4">
                            <!-- Plan Name -->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="Name" class="form-label">
                                        <i class="fas fa-tag text-primary me-2"></i>Plan Name
                                    </label>
                                    <input 
                                        type="text" 
                                        name="Name" 
                                        id="Name" 
                                        class="form-control @error('Name') is-invalid @enderror"
                                        value="{{ old('Name', $courseInstallmentPlan->Name) }}"
                                        required
                                    >
                                    @error('Name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Number of Installments -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="number_of_installments" class="form-label">
                                        <i class="fas fa-calculator text-primary me-2"></i>Number of Installments
                                    </label>
                                    <input 
                                        type="number" 
                                        name="number_of_installments" 
                                        id="number_of_installments" 
                                        class="form-control @error('number_of_installments') is-invalid @enderror"
                                        value="{{ old('number_of_installments', $courseInstallmentPlan->number_of_installments) }}"
                                        min="1" 
                                        max="12" 
                                        required
                                    >
                                    @error('number_of_installments')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- First Installment Percentage -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_installment_percentage" class="form-label">
                                        <i class="fas fa-percent text-primary me-2"></i>First Installment %
                                    </label>
                                    <input 
                                        type="number" 
                                        name="first_installment_percentage" 
                                        id="first_installment_percentage" 
                                        class="form-control @error('first_installment_percentage') is-invalid @enderror"
                                        value="{{ old('first_installment_percentage', $courseInstallmentPlan->first_installment_percentage) }}"
                                        min="0" 
                                        max="100" 
                                        step="0.01" 
                                        required
                                    >
                                    @error('first_installment_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Subsequent Installment Percentage -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subsequent_installment_percentage" class="form-label">
                                        <i class="fas fa-percentage text-primary me-2"></i>Subsequent Installment %
                                    </label>
                                    <input 
                                        type="number" 
                                        name="subsequent_installment_percentage" 
                                        id="subsequent_installment_percentage" 
                                        class="form-control @error('subsequent_installment_percentage') is-invalid @enderror"
                                        value="{{ old('subsequent_installment_percentage', $courseInstallmentPlan->subsequent_installment_percentage) }}"
                                        min="0" 
                                        max="100" 
                                        step="0.01" 
                                        required
                                    >
                                    @error('subsequent_installment_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Days Between Installments -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="days_between_installments" class="form-label">
                                        <i class="fas fa-calendar-alt text-primary me-2"></i>Days Between Installments
                                    </label>
                                    <input 
                                        type="number" 
                                        name="days_between_installments" 
                                        id="days_between_installments" 
                                        class="form-control @error('days_between_installments') is-invalid @enderror"
                                        value="{{ old('days_between_installments', $courseInstallmentPlan->days_between_installments) }}"
                                        min="0" 
                                        required
                                    >
                                    @error('days_between_installments')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Course Duration -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course_duration_months" class="form-label">
                                        <i class="fas fa-clock text-primary me-2"></i>Course Duration (Months)
                                    </label>
                                    <input 
                                        type="number" 
                                        name="course_duration_months" 
                                        id="course_duration_months" 
                                        class="form-control @error('course_duration_months') is-invalid @enderror"
                                        value="{{ old('course_duration_months', $courseInstallmentPlan->course_duration_months) }}"
                                        min="1" 
                                        max="36" 
                                        required
                                    >
                                    @error('course_duration_months')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status Switch -->
                            <div class="col-md-6 mb-3">
                                <div class="custom-control custom-switch">
                                    <input 
                                        type="checkbox" 
                                        class="custom-control-input" 
                                        id="is_active" 
                                        name="is_active" 
                                        value="1" 
                                        {{ old('is_active', $courseInstallmentPlan->is_active) ? 'checked' : '' }}
                                    >
                                    <label class="custom-control-label" for="is_active">Active Status</label>
                                </div>
                            </div>

                            <!-- Installment Plan Info Card -->
                            <div class="col-12 mb-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title font-weight-bold">
                                            <i class="fas fa-info-circle text-primary me-2"></i>Current Installment Plan Information
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1">
                                                    <strong><i class="fas fa-calendar-plus text-primary me-2"></i>Created:</strong> 
                                                    {{ $courseInstallmentPlan->created_at->format('M d, Y H:i A') }}
                                                </p>
                                                <p class="mb-1">
                                                    <strong><i class="fas fa-calendar-check text-primary me-2"></i>Last Updated:</strong> 
                                                    {{ $courseInstallmentPlan->updated_at->format('M d, Y H:i A') }}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1">
                                                    <strong><i class="fas fa-toggle-on text-primary me-2"></i>Current Status:</strong> 
                                                    <span class="badge badge-{{ $courseInstallmentPlan->is_active ? 'success' : 'danger' }}">
                                                        {{ $courseInstallmentPlan->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-12 d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Installment Plan
                                </button>
                                <a href="{{ route('admin.course-installment-plans.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Percentage validation
    $('#first_installment_percentage, #subsequent_installment_percentage').on('input', function() {
        const value = parseFloat($(this).val());
        if (value < 0 || value > 100) {
            alert('Percentage must be between 0 and 100');
            $(this).val('');
        }
    });

    // Form validation
    $('#installmentPlanForm').on('submit', function(e) {
        const firstPercentage = parseFloat($('#first_installment_percentage').val());
        const subsequentPercentage = parseFloat($('#subsequent_installment_percentage').val());

        if (firstPercentage + subsequentPercentage !== 100) {
            e.preventDefault();
            alert('Total installment percentages must sum up to 100%');
            return false;
        }

        // Confirm before making major changes
        const isActiveChanged = $('#is_active').prop('checked') !== {{ $courseInstallmentPlan->is_active ? 'true' : 'false' }};
        
        if (isActiveChanged) {
            if (!confirm('Are you sure you want to update this installment plan?')) {
                e.preventDefault();
                return false;
            }
        }
    });
});
</script>
@endsection