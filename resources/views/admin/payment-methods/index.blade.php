@extends('layouts.master')
@section('content')

      <!-- Begin Page Content -->
      <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Payment Methods</h1>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3" style="background: #2a5c68;">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-0 font-weight-bold text-white">Payment Methods List</h6>
                    </div>
                    <div class="col text-right">
                        <a href="{{ route('admin.payment-methods.create') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus"></i> Add New Method
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr class="bg-dark text-white">
                                <th>ID</th>
                                <th>Logo</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Additional Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Logo</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Additional Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($paymentMethods as $method)
                                <tr>
                                    <td>{{ $method->id }}</td>
                                    <td>
                                        @if($method->logo)
                                            <img src="{{ asset('storage/' . $method->logo) }}"
                                                 alt="{{ $method->name }}"
                                                 class="img-fluid"
                                                 style="max-height: 50px;">
                                        @else
                                            <span class="text-muted">No logo</span>
                                        @endif
                                    </td>
                                    <td>{{ $method->name }}</td>
                                    <td>{{ $method->code }}</td>
                                    <td>
                                        @if($method->additional_price > 0)
                                            <span class="badge badge-info">${{ number_format($method->additional_price, 2) }}</span>
                                        @else
                                            <span class="text-muted">Free</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($method->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.payment-methods.toggle-status', $method) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm" title="{{ $method->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas fa-toggle-{{ $method->is_active ? 'on' : 'off' }}"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.payment-methods.edit', $method->id) }}"
                                           class="btn btn-primary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.payment-methods.destroy', $method) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this payment method?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();

        // Auto close alerts after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    });
</script>
@endsection
