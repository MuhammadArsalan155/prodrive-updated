@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="pd-page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="font-weight:800;"><i class="fas fa-chalkboard-teacher mr-2"></i>Instructors</h4>
            <p style="font-size:.85rem;">Manage all driving instructors and their assignments</p>
        </div>
        <a href="{{ route('admin.addinstructor') }}" class="btn btn-light btn-sm font-weight-bold">
            <i class="fas fa-plus mr-1"></i>Add Instructor
        </a>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <!-- Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold" style="color:var(--pd-navy);">
                <i class="fas fa-list mr-2" style="color:var(--pd-blue);"></i>Instructor List
            </h6>
            <a href="{{ route('admin.addinstructor') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i>Add Instructor
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:60px;">#</th>
                            <th>Instructor</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>License</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($instructors as $instructor)
                            <tr>
                                <td class="text-center" style="font-size:.8rem; color:var(--pd-gray-500);">
                                    {{ $instructor->id }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center" style="gap:.6rem;">
                                        <div class="icon-circle" style="background:linear-gradient(135deg,var(--pd-navy),var(--pd-blue));color:#fff;min-width:34px;width:34px;height:34px;font-size:.8rem;">
                                            {{ strtoupper(substr($instructor->instructor_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-weight-bold" style="font-size:.875rem; color:var(--pd-gray-800);">
                                                {{ $instructor->instructor_name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-size:.83rem; color:var(--pd-gray-700);">{{ $instructor->email }}</td>
                                <td style="font-size:.83rem;">{{ $instructor->contact }}</td>
                                <td>
                                    @if($instructor->license_number)
                                        <span class="font-weight-bold" style="font-size:.82rem; color:var(--pd-navy);">
                                            {{ $instructor->license_number }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $instructor->is_active ? 'success' : 'warning' }}">
                                        {{ $instructor->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center" style="gap:.35rem;">
                                        <a href="{{ route('admin.edit_instructor', $instructor->id) }}"
                                           class="btn btn-icon btn-primary" title="Edit" data-toggle="tooltip">
                                            <i class="fas fa-edit fa-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.delete_instructor', $instructor->id) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this instructor?');">
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
        responsive: true,
        language: {
            emptyTable: "No instructors found",
            zeroRecords: "No matching instructors"
        },
        columnDefs: [
            { orderable: false, targets: [6] },
            { className: 'text-center', targets: [0, 5, 6] }
        ]
    });

    $('[data-toggle="tooltip"]').tooltip({ trigger: 'hover', container: 'body' });

    // Auto-close alerts
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function() { $(this).remove(); });
    }, 5000);
});
</script>
@endsection
