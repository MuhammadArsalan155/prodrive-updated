@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="pd-page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="font-weight:800;"><i class="fas fa-money-bill-wave mr-2"></i>Installment Plans</h4>
            <p style="font-size:.85rem;">Configure flexible payment installment plans for courses</p>
        </div>
        <div class="d-flex" style="gap:.5rem;">
            <form action="{{ route('admin.course-installment-plans.default') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-light btn-sm font-weight-bold"
                        onclick="return confirm('Create default installment plans?');">
                    <i class="fas fa-copy mr-1"></i>Default Plans
                </button>
            </form>
            <a href="{{ route('admin.course-installment-plans.create') }}" class="btn btn-light btn-sm font-weight-bold">
                <i class="fas fa-plus mr-1"></i>Add Plan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold" style="color:var(--pd-navy);">
                <i class="fas fa-list mr-2" style="color:var(--pd-blue);"></i>Installment Plan List
            </h6>
            <a href="{{ route('admin.course-installment-plans.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i>Add Plan
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Plan Name</th>
                            <th class="text-center">Installments</th>
                            <th class="text-center">1st Payment %</th>
                            <th class="text-center">Subsequent %</th>
                            <th class="text-center">Days Between</th>
                            <th class="text-center">Duration</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courseInstallmentPlans as $plan)
                            <tr>
                                <td style="font-size:.8rem; color:var(--pd-gray-500);">{{ $plan->id }}</td>
                                <td>
                                    <div class="font-weight-bold" style="font-size:.875rem; color:var(--pd-gray-800);">
                                        {{ $plan->Name }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-primary">{{ $plan->number_of_installments }}</span>
                                </td>
                                <td class="text-center" style="font-weight:600; color:var(--pd-blue);">
                                    {{ $plan->first_installment_percentage }}%
                                </td>
                                <td class="text-center" style="color:var(--pd-gray-700);">
                                    {{ $plan->subsequent_installment_percentage }}%
                                </td>
                                <td class="text-center" style="font-size:.83rem; color:var(--pd-gray-700);">
                                    {{ $plan->days_between_installments }}d
                                </td>
                                <td class="text-center" style="font-size:.83rem; color:var(--pd-gray-700);">
                                    {{ $plan->course_duration_months }}mo
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $plan->is_active ? 'success' : 'danger' }}">
                                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center" style="gap:.3rem;">
                                        <a href="{{ route('admin.course-installment-plans.edit', $plan->id) }}"
                                           class="btn btn-icon btn-primary" title="Edit" data-toggle="tooltip">
                                            <i class="fas fa-edit fa-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.course-installment-plans.destroy', $plan) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this installment plan? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-danger" title="Delete" data-toggle="tooltip">
                                                <i class="fas fa-trash-alt fa-xs"></i>
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
    $('#dataTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 25,
        columnDefs: [{ orderable: false, targets: [8] }],
        language: {
            emptyTable: "No installment plans configured"
        }
    });

    $('[data-toggle="tooltip"]').tooltip({ trigger: 'hover', container: 'body' });

    setTimeout(function() {
        $('.alert').fadeTo(500, 0).slideUp(500, function() { $(this).remove(); });
    }, 5000);
});
</script>
@endsection
