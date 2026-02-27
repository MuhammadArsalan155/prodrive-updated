@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="pd-page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="font-weight:800;"><i class="fas fa-credit-card mr-2"></i>Payment Methods</h4>
            <p style="font-size:.85rem;">Configure available payment methods for student transactions</p>
        </div>
        <a href="{{ route('admin.payment-methods.create') }}" class="btn btn-light btn-sm font-weight-bold">
            <i class="fas fa-plus mr-1"></i>Add Method
        </a>
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
                <i class="fas fa-list mr-2" style="color:var(--pd-blue);"></i>Payment Methods List
            </h6>
            <a href="{{ route('admin.payment-methods.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i>Add Method
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Logo</th>
                            <th>Method Name</th>
                            <th>Code</th>
                            <th>Extra Fee</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paymentMethods as $method)
                            <tr>
                                <td style="font-size:.8rem; color:var(--pd-gray-500);">{{ $method->id }}</td>
                                <td>
                                    @if($method->logo)
                                        <img src="{{ asset('storage/' . $method->logo) }}"
                                             alt="{{ $method->name }}"
                                             style="height:36px; width:auto; object-fit:contain; border-radius:.3rem;">
                                    @else
                                        <div class="icon-circle" style="background:var(--pd-gray-200);color:var(--pd-gray-500);width:36px;height:36px;font-size:.75rem;">
                                            <i class="fas fa-credit-card"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="font-weight-bold" style="font-size:.875rem; color:var(--pd-gray-800);">
                                        {{ $method->name }}
                                    </div>
                                </td>
                                <td>
                                    <code style="font-size:.8rem; background:#f0f4f8; padding:.2rem .4rem; border-radius:.3rem; color:var(--pd-navy);">
                                        {{ $method->code }}
                                    </code>
                                </td>
                                <td>
                                    @if($method->additional_price > 0)
                                        <span class="badge badge-info">${{ number_format($method->additional_price, 2) }}</span>
                                    @else
                                        <span class="text-muted" style="font-size:.8rem;">Free</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $method->is_active ? 'success' : 'danger' }}">
                                        {{ $method->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center" style="gap:.3rem;">
                                        <form action="{{ route('admin.payment-methods.toggle-status', $method) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-icon btn-warning"
                                                    title="{{ $method->is_active ? 'Deactivate' : 'Activate' }}" data-toggle="tooltip">
                                                <i class="fas fa-toggle-{{ $method->is_active ? 'on' : 'off' }} fa-xs"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.payment-methods.edit', $method->id) }}" class="btn btn-icon btn-primary" title="Edit" data-toggle="tooltip">
                                            <i class="fas fa-edit fa-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.payment-methods.destroy', $method) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this payment method?');">
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
        language: {
            emptyTable: "No payment methods configured",
            zeroRecords: "No matching methods found"
        }
    });

    setTimeout(function() {
        $('.alert').fadeTo(500, 0).slideUp(500, function() { $(this).remove(); });
    }, 5000);

    $('[data-toggle="tooltip"]').tooltip({ trigger: 'hover', container: 'body' });
});
</script>
@endsection
