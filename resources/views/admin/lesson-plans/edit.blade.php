@extends('layouts.master')

@section('styles')
    <style>
        .ck-editor__editable_inline {
            min-height: 375px;
            max-height: 600px;
            font-size: 16px;
        }
        .ck-content {
            background-color: white;
        }
        .ck.ck-editor {
            width: 100%;
        }
        .ck.ck-editor__main {
            border: 1px solid #ccc;
            border-top: none;
        }
        /* For validation error */
        .editor-error .ck.ck-editor__main {
            border-color: #dc3545 !important;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit text-primary mr-2"></i>Edit Lesson Plan
        </h1>
        <div>
            <a href="{{ route('admin.lesson-plans.show', $lessonPlan) }}" class="btn btn-info btn-sm mr-2">
                <i class="fas fa-eye mr-1"></i> View Details
            </a>
            <a href="{{ route('admin.lesson-plans.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back to Lesson Plans
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12">
            <div class="card shadow-lg border-0 rounded-lg">
                <!-- Card Header -->
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold">
                            <i class="fas fa-file-alt mr-2"></i>Lesson Plan Information
                        </h5>
                        <span class="badge {{ $lessonPlan->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $lessonPlan->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                
                <!-- Card Body -->
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-exclamation-triangle mr-2"></i>Validation Error!</strong> Please check the following issues:
                            <ul class="mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('admin.lesson-plans.update', $lessonPlan) }}" method="POST" id="lessonPlanForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            {{-- Lesson Plan Title --}}
                            <div class="col-12 mb-3">
                                <label for="title" class="form-label">
                                    <i class="fas fa-heading text-primary mr-2"></i>Lesson Plan Title <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-book-open"></i></span>
                                    </div>
                                    <input type="text" 
                                           placeholder="Enter Lesson Plan Title" 
                                           name="title" 
                                           id="title" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           value="{{ old('title', $lessonPlan->title) }}" 
                                           required 
                                           maxlength="255">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Lesson Plan Content (CKEditor) --}}
                            <div class="col-12 mb-4">
                                <label for="editor" class="form-label">
                                    <i class="fas fa-file-alt text-primary mr-2"></i>Lesson Plan Content <span class="text-danger">*</span>
                                </label>
                                <div class="@error('content') editor-error @enderror">
                                    <textarea id="editor" name="content">{{ old('content', $lessonPlan->content) }}</textarea>
                                </div>
                                @error('content')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Active Status --}}
                            <div class="col-12 mb-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1" 
                                           {{ old('is_active', $lessonPlan->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">
                                        <i class="fas fa-power-off text-primary mr-2"></i>Lesson Plan Active Status
                                    </label>
                                </div>
                            </div>

                            {{-- Feedback Questions Section --}}
                            <div class="col-12 mb-3">
                                <div class="card border-left-primary">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0 text-primary">
                                            <i class="fas fa-question-circle mr-2"></i>Feedback Questions
                                            <small class="text-muted ml-2">(Yes/No Questions)</small>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Edit feedback questions that students and instructors will answer after completing this lesson.
                                            All questions are Yes/No type questions.
                                        </div>
                                        
                                        <div id="questions-container">
                                            <!-- Display existing questions -->
                                            @foreach($lessonPlan->feedbackQuestions as $index => $question)
                                                <div class="question-item card mb-3">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-9">
                                                                <div class="form-group">
                                                                    <label>Question Text <span class="text-danger">*</span></label>
                                                                    <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $question->id }}">
                                                                    <input type="text" 
                                                                           name="questions[{{ $index }}][question_text]" 
                                                                           class="form-control question-text @error('questions.'.$index.'.question_text') is-invalid @enderror" 
                                                                           placeholder="Enter question text" 
                                                                           value="{{ old('questions.'.$index.'.question_text', $question->question_text) }}" 
                                                                           required>
                                                                    @error('questions.'.$index.'.question_text')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-1">
                                                                <div class="form-group">
                                                                    <label>Order</label>
                                                                    <input type="number" 
                                                                           name="questions[{{ $index }}][display_order]" 
                                                                           class="form-control display-order @error('questions.'.$index.'.display_order') is-invalid @enderror" 
                                                                           value="{{ old('questions.'.$index.'.display_order', $question->display_order) }}" 
                                                                           min="1" 
                                                                           required>
                                                                    @error('questions.'.$index.'.display_order')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-1">
                                                                <div class="form-group">
                                                                    <label>Active</label>
                                                                    <div class="custom-control custom-switch mt-2">
                                                                        <input type="checkbox" 
                                                                               class="custom-control-input" 
                                                                               id="question_active_{{ $index }}" 
                                                                               name="questions[{{ $index }}][is_active]" 
                                                                               value="1" 
                                                                               {{ old('questions.'.$index.'.is_active', $question->is_active) ? 'checked' : '' }}>
                                                                        <label class="custom-control-label" for="question_active_{{ $index }}"></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-1 d-flex align-items-center">
                                                                <button type="button" class="btn btn-danger btn-sm remove-question mt-4">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

                                            <!-- If no questions exist or all were removed -->
                                            @if(count($lessonPlan->feedbackQuestions) === 0)
                                                <div class="question-item card mb-3">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-10">
                                                                <div class="form-group">
                                                                    <label>Question Text <span class="text-danger">*</span></label>
                                                                    <input type="text" 
                                                                           name="questions[0][question_text]" 
                                                                           class="form-control question-text" 
                                                                           placeholder="Enter question text" 
                                                                           required>
                                                                </div>
                                                            </div>
                                                            <div class="col-1">
                                                                <div class="form-group">
                                                                    <label>Order</label>
                                                                    <input type="number" 
                                                                           name="questions[0][display_order]" 
                                                                           class="form-control display-order" 
                                                                           value="1" 
                                                                           min="1" 
                                                                           required>
                                                                </div>
                                                            </div>
                                                            <div class="col-1 d-flex align-items-center">
                                                                <button type="button" class="btn btn-danger btn-sm remove-question mt-4">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <button type="button" id="add-question" class="btn btn-success btn-sm">
                                            <i class="fas fa-plus mr-1"></i> Add Question
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Submit Buttons --}}
                            <div class="col-12 mt-4">
                                <div class="row">
                                    <div class="col-md-6 mb-2 mb-md-0">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-save mr-2"></i>Update Lesson Plan
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="{{ route('admin.lesson-plans.index') }}" class="btn btn-secondary btn-block">
                                            <i class="fas fa-times mr-2"></i>Cancel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // CKEditor initialization
        ClassicEditor
            .create(document.querySelector('#editor'), {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'strikethrough', 'underline', '|',
                    'bulletedList', 'numberedList', '|',
                    'outdent', 'indent', '|',
                    'alignment', '|',
                    'link', 'blockQuote', 'insertTable', '|',
                    'undo', 'redo'
                ],
                placeholder: 'Enter your lesson plan content here...',
            })
            .then(editor => {
                // Store reference to use in validation
                window.editor = editor;
            })
            .catch(error => {
                console.error(error);
            });

        // Questions Management
        const questionsContainer = document.getElementById('questions-container');
        const addQuestionBtn = document.getElementById('add-question');

        // Add new question
        addQuestionBtn.addEventListener('click', function() {
            const questionItems = document.querySelectorAll('.question-item');
            const newIndex = questionItems.length;
            const newOrder = questionItems.length + 1;
            
            const questionTemplate = `
                <div class="question-item card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-9">
                                <div class="form-group">
                                    <label>Question Text <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="questions[${newIndex}][question_text]" 
                                           class="form-control question-text" 
                                           placeholder="Enter question text" 
                                           required>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-group">
                                    <label>Order</label>
                                    <input type="number" 
                                           name="questions[${newIndex}][display_order]" 
                                           class="form-control display-order" 
                                           value="${newOrder}" 
                                           min="1" 
                                           required>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-group">
                                    <label>Active</label>
                                    <div class="custom-control custom-switch mt-2">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="question_active_${newIndex}" 
                                               name="questions[${newIndex}][is_active]" 
                                               value="1" 
                                               checked>
                                        <label class="custom-control-label" for="question_active_${newIndex}"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-1 d-flex align-items-center">
                                <button type="button" class="btn btn-danger btn-sm remove-question mt-4">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            questionsContainer.insertAdjacentHTML('beforeend', questionTemplate);
            updateQuestionIndices();
        });

        // Remove question
        questionsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-question') || e.target.closest('.remove-question')) {
                const questionItem = e.target.closest('.question-item');
                
                // Only allow removal if there's more than one question
                const questionItems = document.querySelectorAll('.question-item');
                if (questionItems.length > 1) {
                    questionItem.remove();
                    updateQuestionIndices();
                } else {
                    alert('You must have at least one question!');
                }
            }
        });

        // Update question indices and names when questions are added/removed
        function updateQuestionIndices() {
            const questionItems = document.querySelectorAll('.question-item');
            
            questionItems.forEach((item, index) => {
                // Skip existing questions with IDs
                if (!item.querySelector('input[name^="questions"][name$="[id]"]')) {
                    const questionTextInput = item.querySelector('.question-text');
                    const displayOrderInput = item.querySelector('.display-order');
                    const isActiveCheckbox = item.querySelector('input[type="checkbox"]');
                    
                    // Update input names to maintain proper array indexing
                    questionTextInput.name = `questions[${index}][question_text]`;
                    displayOrderInput.name = `questions[${index}][display_order]`;
                    
                    if (isActiveCheckbox) {
                        isActiveCheckbox.name = `questions[${index}][is_active]`;
                        isActiveCheckbox.id = `question_active_${index}`;
                        const label = item.querySelector('.custom-control-label');
                        if (label) {
                            label.setAttribute('for', `question_active_${index}`);
                        }
                    }
                }
            });
        }

        // Form validation
        document.getElementById('lessonPlanForm').addEventListener('submit', function(e) {
            // Validate title
            const titleInput = document.getElementById('title');
            if (!titleInput.value.trim()) {
                e.preventDefault();
                alert('Please enter a lesson plan title.');
                titleInput.focus();
                return;
            }

            // CKEditor content is automatically synchronized with the form textarea,
            // so no need to manually update a hidden field

            // Validate questions
            const questionTextInputs = document.querySelectorAll('.question-text');
            for (let input of questionTextInputs) {
                if (!input.value.trim()) {
                    e.preventDefault();
                    alert('Please fill in all question texts.');
                    input.focus();
                    return;
                }
            }
        });
    });
</script>
@endsection