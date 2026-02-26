@extends('layouts.master')

@section('title', 'Certificates Management')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Certificates Management</h1>
        <div>
            <a href="{{ route('admin.certificates.eligible') }}" class="d-sm-inline-block btn btn-info shadow-sm">
                <i class="fas fa-list fa-sm text-white-50"></i> Eligible Students
            </a>
            <a href="{{ route('admin.certificates.create') }}" class="d-sm-inline-block btn btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Generate New Certificate
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    {{-- @include('admin.partials.alerts') --}}

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Certificates</h6>
        </div>
        <div class="card-body">
            @if($certificates->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="certificatesTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Certificate #</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Issue Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certificates as $certificate)
                                <tr>
                                    <td>{{ $certificate->formatted_certificate_number }}</td>
                                    <td>{{ $certificate->student->first_name }} {{ $certificate->student->last_name }}</td>
                                    <td>{{ $certificate->course->course_name }}</td>
                                    <td>{{ $certificate->issue_date->format('M d, Y') }}</td>
                                    <td>
                                        @if($certificate->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.certificates.show', $certificate->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.certificates.download', $certificate->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <form action="{{ route('admin.certificates.destroy', $certificate->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this certificate?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $certificates->links() }}
            @else
                <div class="alert alert-info">
                    No certificates have been generated yet.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#certificatesTable').DataTable({
            "ordering": true,
            "paging": false,
            "searching": true,
            "info": false,
        });
    });
</script>
@endsection