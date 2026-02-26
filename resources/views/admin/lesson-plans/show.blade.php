@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-book text-primary mr-2"></i>Lesson Plan Details
        </h1>
        <div>
            <a href="{{ route('admin.lesson-plans.edit', $lessonPlan) }}" class="btn btn-primary btn-sm mr-2">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            <a href="{{ route('admin.lesson-plans.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back to Lesson Plans
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Lesson Plan Details Card -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Card Header -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-gradient-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-file-alt mr-1"></i> {{ $lessonPlan->title }}
                    </h6>
                    <span class="badge {{ $lessonPlan->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $lessonPlan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="text-primary">Content</h5>
                        <div class="lesson-content p-3 border rounded bg-light">
                            {!! $lessonPlan->content !!}
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5 class="text-primary">Created By</h5>
                        <p>{{ $lessonPlan->creator ? $lessonPlan->creator->name : 'Unknown' }}</p>
                        
                        <h5 class="text-primary mt-3">Created On</h5>
                        <p>{{ $lessonPlan->created_at->format('F d, Y \a\t h:i A') }}</p>
                        
                        <h5 class="text-primary mt-3">Last Updated</h5>
                        <p>{{ $lessonPlan->updated_at->format('F d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feedback Questions & Course Usage Card -->
        <div class="col-xl-4 col-lg-5">
            <!-- Feedback Questions Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-question-circle mr-1"></i> Feedback Questions
                    </h6>
                </div>
                <div class="card-body">
                    @if($lessonPlan->feedbackQuestions->count() > 0)
                        <div class="list-group">
                            @foreach($lessonPlan->feedbackQuestions as $question)
                                <div class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Question #{{ $question->display_order }}</h6>
                                        <small class="text-{{ $question->is_active ? 'success' : 'danger' }}">
                                            {{ $question->is_active ? 'Active' : 'Inactive' }}
                                        </small>
                                    </div>
                                    <p class="mb-1">{{ $question->question_text }}</p>
                                    <small class="text-muted">Type: Yes/No</small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i> No feedback questions found.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Course Usage Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-success">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-graduation-cap mr-1"></i> Usage in Courses
                    </h6>
                </div>
                <div class="card-body">
                    @if($lessonPlan->courses->count() > 0)
                        <div class="list-group">
                            @foreach($lessonPlan->courses as $course)
                                <a href="{{ route('viewcourse') }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $course->course_name }}</h6>
                                        <small>
                                            <span class="badge badge-{{ $course->is_active ? 'success' : 'danger' }}">
                                                {{ $course->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </small>
                                    </div>
                                    <p class="mb-1">
                                        <small>
                                            Class #{{ $course->pivot->class_order }} 
                                            ({{ ucfirst($course->pivot->class_type) }})
                                        </small>
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i> This lesson plan is not being used in any courses yet.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-secondary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-cogs mr-1"></i> Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.lesson-plans.edit', $lessonPlan) }}" class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-edit mr-1"></i> Edit Lesson Plan
                        </a>
                        
                        <form action="{{ route('admin.lesson-plans.toggle-status', $lessonPlan) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-block mb-2">
                                <i class="fas fa-toggle-{{ $lessonPlan->is_active ? 'on' : 'off' }} mr-1"></i>
                                {{ $lessonPlan->is_active ? 'Deactivate' : 'Activate' }} Lesson Plan
                            </button>
                        </form>
                        
                        @if($lessonPlan->courses->count() === 0)
                            <form action="{{ route('admin.lesson-plans.destroy', $lessonPlan) }}" method="POST" id="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-trash-alt mr-1"></i> Delete Lesson Plan
                                </button>
                            </form>
                        @else
                            <button class="btn btn-danger btn-block" disabled title="Cannot delete lesson plan that is in use">
                                <i class="fas fa-trash-alt mr-1"></i> Delete Lesson Plan
                            </button>
                            <small class="text-muted d-block text-center mt-1">
                                Cannot delete a lesson plan that is used in courses
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Confirm deletion
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForm = document.getElementById('delete-form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to delete this lesson plan? This action cannot be undone.')) {
                    e.preventDefault();
                    return false;
                }
                return true;
            });
        }
    });
</script>
@endsection