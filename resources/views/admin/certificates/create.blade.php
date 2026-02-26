@extends('layouts.master')

@section('title', 'Generate Certificate')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Generate New Certificate</h1>
        <a href="{{ route('admin.certificates.index') }}" class="d-none d-sm-inline-block btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Certificates
        </a>
    </div>

    <!-- Alert Messages -->
    {{-- @include('admin.partials.alerts') --}}

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Certificate Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.certificates.store') }}" method="POST">
                @csrf
                <div class="form-group row">
                    <label for="student_id" class="col-sm-2 col-form-label">Student:</label>
                    <div class="col-sm-10">
                        <select name="student_id" id="student_id" class="form-control @error('student_id') is-invalid @enderror" required>
                            <option value="">-- Select Student --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">
                                    {{ $student->first_name }} {{ $student->last_name }} ({{ $student->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="course_id" class="col-sm-2 col-form-label">Course:</label>
                    <div class="col-sm-10">
                        <select name="course_id" id="course_id" class="form-control @error('course_id') is-invalid @enderror" required>
                            <option value="">-- Select Course --</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">
                                    {{ $course->course_name }} ({{ $course->course_type }})
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">Generate Certificate</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Enhancement: Fetch student's assigned course when student is selected
        $('#student_id').change(function() {
            let studentId = $(this).val();
            if (studentId) {
                $.ajax({
                    url: '/admin/students/' + studentId + '/course',
                    type: 'GET',
                    success: function(data) {
                        if (data.course_id) {
                            $('#course_id').val(data.course_id);
                        }
                    }
                });
            }
        });
    });
</script>
@endsection