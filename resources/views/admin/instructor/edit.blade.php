@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-edit text-primary mr-2"></i>Edit Instructor
            </h1>
            <div class="btn-group">
                <a href="{{ route('admin.viewinstructor') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye mr-1"></i> View Instructors
                </a>
                <a href="{{ route('admin.addinstructor') }}" class="btn btn-outline-success btn-sm ml-2">
                    <i class="fas fa-plus mr-1"></i> Add New
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
                                <i class="fas fa-user-edit mr-2"></i>Instructor Details
                            </h5>
                            <span class="badge {{ $instructor->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $instructor->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Validation Error!</strong> Please correct the following issues:
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

                        <form action="{{ route('admin.update_instructor') }}" method="post" id="editInstructorForm">
                            @csrf
                            <input type="hidden" name="id" value="{{ $instructor->id }}">

                            <div class="row">
                                {{-- Instructor Name --}}
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <label class="form-label" for="instructor_name">
                                        <i class="fas fa-user mr-2 text-primary"></i>Instructor Name <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-signature"></i></span>
                                        </div>
                                        <input type="text" placeholder="Enter Full Name" name="instructor_name"
                                            id="instructor_name"
                                            class="form-control @error('instructor_name') is-invalid @enderror"
                                            value="{{ old('instructor_name', $instructor->instructor_name) }}" required>
                                        @error('instructor_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <label class="form-label" for="email">
                                        <i class="fas fa-envelope mr-2 text-primary"></i>Email Address <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-at"></i></span>
                                        </div>
                                        <input type="email" placeholder="Enter Email Address" name="email"
                                            id="email" class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', $instructor->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <label class="form-label" for="contact">
                                        <i class="fas fa-phone mr-2 text-primary"></i>Contact Number <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                        </div>
                                        <input type="tel" placeholder="Enter Contact Number" name="contact"
                                            id="contact" class="form-control @error('contact') is-invalid @enderror"
                                            value="{{ old('contact', $instructor->contact) }}" pattern="[0-9+().\- ]{10,15}"
                                             oninput="this.value = this.value.replace(/[^0-9+().\- ]/g, '')"
                                            required>
                                        @error('contact')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <label class="form-label" for="password">
                                        <i class="fas fa-key mr-2 text-primary"></i>Password
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="password" placeholder="Leave blank to keep current password"
                                            name="password" id="password"
                                            class="form-control @error('password') is-invalid @enderror">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">
                                        Leave blank if you do not want to change the password
                                    </small>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <label class="form-label" for="license_number">
                                        <i class="fas fa-id-card mr-2 text-primary"></i>License Number
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-certificate"></i></span>
                                        </div>
                                        <input type="text" placeholder="Enter License Number" name="license_number"
                                            id="license_number"
                                            class="form-control @error('license_number') is-invalid @enderror"
                                            value="{{ old('license_number', $instructor->license_number) }}">
                                        @error('license_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Role --}}
                                {{-- <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label class="form-label" for="role">
                                    <i class="fas fa-user-tag mr-2 text-primary"></i>Role <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user-shield"></i></span>
                                    </div>
                                    <select name="role"
                                            id="role"
                                            class="form-control @error('role') is-invalid @enderror"
                                            required>
                                        <option value="">Select Role</option>
                                        <option value="instructor" {{ old('role', $instructor->role) == 'instructor' ? 'selected' : '' }}>
                                            Instructor
                                        </option>
                                        <option value="admin" {{ old('role', $instructor->role) == 'admin' ? 'selected' : '' }}>
                                            Admin
                                        </option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}

                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3 d-flex align-items-center">
                                    <div class="custom-control custom-switch mt-4">
                                        <input type="checkbox" class="custom-control-input" id="is_active"
                                            name="is_active" value="1"
                                            {{ old('is_active', $instructor->is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            <i class="fas fa-power-off mr-2 text-primary"></i>Active Status
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-save mr-2"></i>Update Instructor
                                            </button>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="{{ route('admin.viewinstructor') }}"
                                                class="btn btn-secondary btn-block">
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
    <script>
        $(document).ready(function() {
            // Password toggle visibility
            $('#togglePassword').on('click', function() {
                const passwordField = $('#password');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });

            // Form validation
            // $('#editInstructorForm').on('submit', function(event) {
            //     const contactInput = $('#contact');
            //     const contactPattern = /^[0-9]{10}$/;

            //     if (!contactPattern.test(contactInput.val())) {
            //         event.preventDefault();
            //         contactInput.addClass('is-invalid');
            //         contactInput.next('.invalid-feedback').text(
            //             'Please enter a valid 10-digit contact number');
            //     }
            // });
        });
    </script>
@endsection
