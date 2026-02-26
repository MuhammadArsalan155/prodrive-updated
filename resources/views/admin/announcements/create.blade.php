@extends('layouts.master')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--multiple {
        min-height: 38px;
    }
    .target-options {
        display: none;
    }
    .ck-editor__editable {
        min-height: 200px;
    }
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d1d3e2;
        border-radius: 0.35rem;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #4e73df;
        border: 1px solid #4e73df;
        color: white;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 5px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #eee;
    }
    .card-header h6 {
        font-weight: 600;
    }
    .form-group label {
        font-weight: 500;
        color: #4e73df;
    }
    .target-heading {
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 0.5rem;
        color: #5a5c69;
    }
</style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Create Announcement</h1>
            <a href="{{ route('admin.announcements.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Announcements
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Create Announcement Form -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Announcement Details</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.announcements.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="title">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="editor">Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="editor" name="content">{{ old('content') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="attachment">Attachment (Optional)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="attachment" name="attachment">
                            <label class="custom-file-label" for="attachment">Choose file</label>
                        </div>
                        <small class="form-text text-muted">Max file size: 10MB</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="expires_at">Expiry Date (Optional)</label>
                            <input type="datetime-local" class="form-control" id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                            <small class="form-text text-muted">Leave blank if the announcement should not expire</small>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="target_type">Target Audience <span class="text-danger">*</span></label>
                            <select class="form-control" id="target_type" name="target_type" required>
                                <option value="all" {{ old('target_type') == 'all' ? 'selected' : '' }}>All Users</option>
                                <option value="role" {{ old('target_type') == 'role' ? 'selected' : '' }}>Specific Roles</option>
                                <option value="user" {{ old('target_type') == 'user' ? 'selected' : '' }}>Specific Users</option>
                            </select>
                        </div>
                    </div>

                    <!-- Role-specific options -->
                    <div id="role-options" class="form-group target-options">
                        <h5 class="target-heading">Target Specific Roles</h5>
                        <label for="roles">Select Roles</label>
                        <select class="form-control select2-multiple" id="roles" name="roles[]" multiple>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ (is_array(old('roles')) && in_array($role->id, old('roles'))) ? 'selected' : '' }}>
                                    {{ $role->display_name ?? $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- User-specific options -->
                    <div id="user-options" class="target-options">
                        <h5 class="target-heading">Target Specific Users</h5>
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Users & Staff</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="users">Select Users</label>
                                    <select class="form-control select2-multiple" id="users" name="users[]" multiple>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ (is_array(old('users')) && in_array($user->id, old('users'))) ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">Students</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="students">Select Students</label>
                                    <select class="form-control select2-multiple" id="students" name="students[]" multiple>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ (is_array(old('students')) && in_array($student->id, old('students'))) ? 'selected' : '' }}>
                                                {{ $student->first_name }} {{ $student->last_name }} ({{ $student->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">Instructors</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="instructors">Select Instructors</label>
                                    <select class="form-control select2-multiple" id="instructors" name="instructors[]" multiple>
                                        @foreach($instructors as $instructor)
                                            <option value="{{ $instructor->id }}" {{ (is_array(old('instructors')) && in_array($instructor->id, old('instructors'))) ? 'selected' : '' }}>
                                                {{ $instructor->instructor_name }} ({{ $instructor->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-bullhorn mr-2"></i> Create Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2-multiple').select2({
            placeholder: 'Select options',
            width: '100%'
        });

        // Initialize CKEditor
        ClassicEditor
            .create(document.querySelector('#editor'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo']
            })
            .catch(error => {
                console.error(error);
            });

        // Show/hide target options based on selection
        $('#target_type').change(function() {
            $('.target-options').hide();

            var selectedOption = $(this).val();
            if (selectedOption === 'role') {
                $('#role-options').show();
            } else if (selectedOption === 'user') {
                $('#user-options').show();
            }
        }).trigger('change');

        // Update file input label with filename
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName || "Choose file");
        });
    });
</script>
@endsection
