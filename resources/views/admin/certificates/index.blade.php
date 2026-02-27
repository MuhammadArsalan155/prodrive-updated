@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="pd-page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="font-weight:800;"><i class="fas fa-certificate mr-2"></i>Certificates</h4>
            <p style="font-size:.85rem;">Issue and manage completion certificates for students</p>
        </div>
        <div class="d-flex" style="gap:.5rem;">
            <a href="{{ route('admin.certificates.eligible') }}" class="btn btn-light btn-sm font-weight-bold">
                <i class="fas fa-list-check mr-1"></i>Eligible Students
            </a>
            <a href="{{ route('admin.certificates.create') }}" class="btn btn-light btn-sm font-weight-bold">
                <i class="fas fa-plus mr-1"></i>Generate Certificate
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
                <i class="fas fa-list mr-2" style="color:var(--pd-blue);"></i>All Certificates
            </h6>
            <span class="badge badge-primary" style="font-size:.8rem;">{{ $certificates->count() }} total</span>
        </div>
        <div class="card-body p-0">
            @if($certificates->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0" id="certificatesTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Certificate #</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Issue Date</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certificates as $certificate)
                                <tr>
                                    <td>
                                        <span class="font-weight-bold" style="font-size:.82rem; color:var(--pd-blue);">
                                            {{ $certificate->formatted_certificate_number }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center" style="gap:.5rem;">
                                            <div class="icon-circle" style="background:linear-gradient(135deg,var(--pd-navy),var(--pd-blue));color:#fff;width:32px;height:32px;min-width:32px;font-size:.75rem;">
                                                {{ strtoupper(substr($certificate->student->first_name,0,1).substr($certificate->student->last_name,0,1)) }}
                                            </div>
                                            <div class="font-weight-bold" style="font-size:.875rem;">
                                                {{ $certificate->student->first_name }} {{ $certificate->student->last_name }}
                                            </div>
                                        </div>
                                    </td>
                                    <td style="font-size:.83rem; color:var(--pd-gray-700);">{{ $certificate->course->course_name }}</td>
                                    <td style="font-size:.83rem; color:var(--pd-gray-500);">{{ $certificate->issue_date->format('M d, Y') }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $certificate->is_active ? 'success' : 'danger' }}">
                                            {{ $certificate->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center" style="gap:.3rem;">
                                            <a href="{{ route('admin.certificates.show', $certificate->id) }}"
                                               class="btn btn-icon btn-info" title="View" data-toggle="tooltip">
                                                <i class="fas fa-eye fa-xs"></i>
                                            </a>
                                            <a href="{{ route('admin.certificates.download', $certificate->id) }}"
                                               class="btn btn-icon btn-primary" title="Download PDF" data-toggle="tooltip">
                                                <i class="fas fa-download fa-xs"></i>
                                            </a>
                                            <form action="{{ route('admin.certificates.destroy', $certificate->id) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Delete this certificate?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-icon btn-danger" title="Delete" data-toggle="tooltip">
                                                    <i class="fas fa-trash fa-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($certificates->hasPages())
                <div class="d-flex justify-content-center py-3">
                    {{ $certificates->links() }}
                </div>
                @endif
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-certificate fa-3x mb-3 d-block" style="opacity:.25;"></i>
                    <p class="mb-2">No certificates have been generated yet.</p>
                    <a href="{{ route('admin.certificates.eligible') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-list mr-1"></i>View Eligible Students
                    </a>
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
        ordering: true,
        paging: false,
        searching: true,
        info: false,
        columnDefs: [{ orderable: false, targets: [5] }]
    });

    $('[data-toggle="tooltip"]').tooltip({ trigger: 'hover', container: 'body' });

    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function() { $(this).remove(); });
    }, 5000);
});
</script>
@endsection
