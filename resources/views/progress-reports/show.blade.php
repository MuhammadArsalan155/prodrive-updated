@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-alt mr-2 text-primary"></i>
            Progress Report Details
        </h1>
        <a href="{{ route('student.progress-reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i>Back to Reports
        </a>
    </div>

    <!-- Progress Report Details -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle mr-2"></i>
                        Report Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary">
                                <i class="fas fa-book mr-2"></i>Course Information
                            </h5>
                            <p>
                                <strong>Course:</strong> {{ $report->course->course_name }}
                            </p>
                            <p>
                                <strong>Instructor:</strong> {{ $report->instructor->instructor_name }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-primary">
                                <i class="fas fa-calendar-alt mr-2"></i>Report Metadata
                            </h5>
                            <p>
                                <strong>Created On:</strong> {{ $report->created_at->format('M d, Y h:i A') }}
                                @if($report->created_at != $report->updated_at)
                                    <br>
                                    <strong>Last Updated:</strong> {{ $report->updated_at->format('M d, Y h:i A') }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary">
                                <i class="fas fa-star-half-alt mr-2"></i>Performance Rating
                            </h5>
                            <div class="rating-display">
                                @if($report->rating)
                                    <span class="badge 
                                        {{ $report->rating >= 4 ? 'badge-success' : 
                                           ($report->rating >= 2 ? 'badge-warning' : 'badge-danger') }} 
                                        h4 p-2">
                                        {{ $report->rating }}/5
                                    </span>
                                @else
                                    <span class="badge badge-secondary">No Rating Provided</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-primary">
                                <i class="fas fa-chart-line mr-2"></i>Performance Overview
                            </h5>
                            <div class="performance-notes">
                                <strong>Performance Notes:</strong>
                                <p class="text-justify">
                                    {{ $report->performance_notes }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($report->areas_of_improvement)
                        <hr>

                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-lightbulb mr-2"></i>Areas of Improvement
                                </h5>
                                <div class="improvement-areas">
                                    <p class="text-justify">
                                        {{ $report->areas_of_improvement }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection