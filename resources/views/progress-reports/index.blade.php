@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line mr-2 text-primary"></i>
            Progress Reports
        </h1>
    </div>

    <!-- Progress Reports Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list-alt mr-2"></i>All Progress Reports
                    </h6>
                </div>
                <div class="card-body">
                    @if($progressReports->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" id="progressReportsTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>
                                            <i class="fas fa-calendar-alt mr-1 text-primary"></i>
                                            Date
                                        </th>
                                        <th>
                                            <i class="fas fa-book mr-1 text-primary"></i>
                                            Course
                                        </th>
                                        <th>
                                            <i class="fas fa-user-tie mr-1 text-primary"></i>
                                            Instructor
                                        </th>
                                        <th>
                                            <i class="fas fa-star mr-1 text-primary"></i>
                                            Rating
                                        </th>
                                        <th>
                                            <i class="fas fa-tools mr-1 text-primary"></i>
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($progressReports as $report)
                                        <tr>
                                            <td>{{ $report->created_at->format('M d, Y') }}</td>
                                            <td>{{ $report->course->course_name }}</td>
                                            <td>{{ $report->instructor->instructor_name }}</td>
                                            <td>
                                                @if($report->rating)
                                                    <span class="badge 
                                                        {{ $report->rating >= 4 ? 'badge-success' : 
                                                           ($report->rating >= 2 ? 'badge-warning' : 'badge-danger') }}">
                                                        {{ $report->rating }}/5
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('student.progress-reports.show', $report->id) }}" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye mr-1"></i>View Details
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $progressReports->links() }}
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            No progress reports available yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#progressReportsTable').DataTable({
            "paging": false,
            "ordering": true,
            "info": false
        });
    });
</script>
@endsection