@extends('layouts.master')

@section('title', 'Academic Progress')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Academic Progress</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Student Information Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Academic Progress - {{ $student->first_name }} {{ $student->last_name }}</h5>
                </div>
                <div class="card-body">
                    <!-- Course Progress -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">Course Progress Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="card-title">{{ $student->course->title ?? 'No Course Assigned' }}</h6>
                                            <p class="mb-2">Instructor: {{ $student->instructor->instructor_name ?? 'Not Assigned' }}</p>
                                            <p class="mb-0">
                                                <span class="badge bg-{{ $student->course_status == 'completed' ? 'success' : ($student->course_status == 'in_progress' ? 'warning' : 'secondary') }} p-2">
                                                    {{ ucfirst(str_replace('_', ' ', $student->course_status ?? 'Not Started')) }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <h6>Overall Progress</h6>
                                                    <span>
                                                        {{ 
                                                            $courseProgress['theory']['completed'] + $courseProgress['practical']['completed'] 
                                                        }} / {{ 
                                                            $courseProgress['theory']['total'] + $courseProgress['practical']['total'] 
                                                        }} hours
                                                    </span>
                                                </div>
                                                <div class="progress mb-1">
                                                    <div class="progress-bar bg-primary" role="progressbar" 
                                                        style="width: {{ 
                                                            ($courseProgress['theory']['total'] + $courseProgress['practical']['total'] > 0) 
                                                            ? round((($courseProgress['theory']['completed'] + $courseProgress['practical']['completed']) / 
                                                            ($courseProgress['theory']['total'] + $courseProgress['practical']['total'])) * 100) 
                                                            : 0 
                                                        }}%">
                                                        {{ 
                                                            ($courseProgress['theory']['total'] + $courseProgress['practical']['total'] > 0) 
                                                            ? round((($courseProgress['theory']['completed'] + $courseProgress['practical']['completed']) / 
                                                            ($courseProgress['theory']['total'] + $courseProgress['practical']['total'])) * 100) 
                                                            : 0 
                                                        }}%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Progress -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">Theory Progress</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <h6>Completed Hours</h6>
                                        <span>{{ $courseProgress['theory']['completed'] }} / {{ $courseProgress['theory']['total'] }} hours</span>
                                    </div>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $courseProgress['theory']['percentage'] }}%">
                                            {{ $courseProgress['theory']['percentage'] }}%
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6>Theory Status</h6>
                                        <span class="badge bg-{{ $student->theory_status == 'completed' ? 'success' : ($student->theory_status == 'in_progress' ? 'warning' : 'secondary') }} p-2">
                                            {{ ucfirst(str_replace('_', ' ', $student->theory_status ?? 'Not Started')) }}
                                        </span>
                                    </div>
                                    
                                    @if($student->theory_status == 'completed' && $student->theory_completion_date)
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle me-2"></i> Completed on {{ date('M d, Y', strtotime($student->theory_completion_date)) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">Practical Progress</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <h6>Completed Hours</h6>
                                        <span>{{ $courseProgress['practical']['completed'] }} / {{ $courseProgress['practical']['total'] }} hours</span>
                                    </div>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $courseProgress['practical']['percentage'] }}%">
                                            {{ $courseProgress['practical']['percentage'] }}%
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6>Practical Status</h6>
                                        <span class="badge bg-{{ $student->practical_status == 'completed' ? 'success' : ($student->practical_status == 'in_progress' ? 'warning' : 'secondary') }} p-2">
                                            {{ ucfirst(str_replace('_', ' ', $student->practical_status ?? 'Not Started')) }}
                                        </span>
                                    </div>
                                    
                                    @if($student->practical_status == 'completed' && $student->practical_completion_date)
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle me-2"></i> Completed on {{ date('M d, Y', strtotime($student->practical_completion_date)) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Reports -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Progress Reports</h5>
                        </div>
                        <div class="card-body">
                            @if(count($progressReports) > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Instructor</th>
                                                <th>Rating</th>
                                                <th>Type</th>
                                                <th>Comments</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($progressReports as $report)
                                                <tr>
                                                    <td>{{ date('M d, Y', strtotime($report->created_at)) }}</td>
                                                    <td>{{ $report->instructor->instructor_name ?? 'N/A' }}</td>
                                                    <td>
                                                        <div class="d-flex">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="fas fa-star {{ $i <= $report->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                            @endfor
                                                        </div>
                                                    </td>
                                                    <td>{{ ucfirst($report->type ?? 'General') }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-info view-comments" data-bs-toggle="modal" data-bs-target="#reportModal" data-comments="{{ $report->comments }}" data-date="{{ date('M d, Y', strtotime($report->created_at)) }}">
                                                            View Comments
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                    <h5>No Progress Reports Yet</h5>
                                    <p class="text-muted">The instructor has not submitted any progress reports for this student.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Comments Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="reportModalLabel">Progress Report Comments</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="fw-bold mb-1" id="reportDate"></p>
                <p id="reportComments"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Setup for viewing comments in modal
        const viewButtons = document.querySelectorAll('.view-comments');
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const comments = this.getAttribute('data-comments');
                const date = this.getAttribute('data-date');
                document.getElementById('reportComments').textContent = comments;
                document.getElementById('reportDate').textContent = date;
            });
        });
    });
</script>
@endsection