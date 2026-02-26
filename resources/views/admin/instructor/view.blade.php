@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <!-- DataTales Example -->
        <div class="card shadow-lg mb-4">
            <div class="card-header py-3" style="background: linear-gradient(to right, #2a5c68, #3a7c88);">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-users mr-2"></i>Instructors Management
                        </h6>
                    </div>
                    <div class="col text-right">
                        <a href="{{ route('admin.addinstructor') }}" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-plus-circle mr-1"></i>Add New Instructor
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center">ID</th>
                                <th>Instructor Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>License Number</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($instructors as $instructor)
                                <tr>
                                    <td class="text-center">{{ $instructor->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <div class="icon-circle bg-primary text-white">
                                                    {{ substr($instructor->instructor_name, 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $instructor->instructor_name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $instructor->email }}</td>
                                    <td>{{ $instructor->contact }}</td>
                                    <td>{{ $instructor->license_number ?? 'N/A' }}</td>
                                    
                                    <td class="text-center">
                                        <span
                                            class="badge badge-{{ $instructor->is_active ? 'success' : 'warning' }} px-2 py-1">
                                            {{ $instructor->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.edit_instructor', $instructor->id) }}"
                                                class="btn btn-primary" data-toggle="tooltip" title="Edit Instructor">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.delete_instructor', $instructor->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this instructor?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Delete Instructor">
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

    @push('styles')
        <style>
            .icon-circle {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
            }

            .table-hover tbody tr:hover {
                background-color: rgba(0, 0, 0, 0.075);
                transition: background-color 0.3s ease;
            }

            .btn-group-sm .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        </style>
    @endpush

  
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            order: [
                [0, 'desc']
            ],
            responsive: true,
            language: {
                emptyTable: "No instructors found",
                zeroRecords: "No matching instructors"
            },
            columnDefs: [{
                targets: [0, 5, 6, 7],
                className: 'text-center'
            }]
        });

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip({
            trigger: 'hover',
            container: 'body'
        });
    });
</script>
@endsection
