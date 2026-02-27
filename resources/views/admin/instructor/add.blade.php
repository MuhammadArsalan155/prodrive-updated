@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="pd-page-header d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-1" style="font-weight:800;"><i class="fas fa-chalkboard-teacher mr-2"></i>Add Instructor</h4>
                <p style="font-size:.85rem;">Register a new driving instructor to the system</p>
            </div>
            <a href="{{ route('admin.viewinstructor') }}" class="btn btn-light btn-sm font-weight-bold">
                <i class="fas fa-list mr-1"></i>View Instructors
            </a>
        </div>

        <!-- Content Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12">
                <div class="card shadow-lg border-0 rounded-lg">
                    <!-- Card Header -->
                    <div class="card-header bg-gradient-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="m-0 font-weight-bold">
                                <i class="fas fa-user-plus mr-2"></i>Add New Instructor
                            </h5>
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

                        <form action="{{ route('admin.add_instructor') }}" method="post">
                            @csrf
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
                                            value="{{ old('instructor_name') }}" required>
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
                                            value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <label class="form-label" for="contact">
                                        <i class="fas fa-phone mr-2 text-primary"></i>Contact Number (USA) <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                        </div>
                                        <input type="tel" placeholder="Enter USA Phone Number (e.g., (555) 123-4567)"
                                            name="contact" id="contact"
                                            class="form-control @error('contact') is-invalid @enderror"
                                            value="{{ old('contact') }}" pattern="[0-9+().\- ]{10,15}"
                                            title="Please enter a valid USA phone number"
                                            oninput="this.value = this.value.replace(/[^0-9+().\- ]/g, '')" required>
                                        @error('contact')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <label class="form-label" for="password">
                                        <i class="fas fa-key mr-2 text-primary"></i>Password <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="password" placeholder="Enter Password" name="password" id="password"
                                            class="form-control @error('password') is-invalid @enderror" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
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
                                            value="{{ old('license_number') }}">
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
                                        <option value="instructor" {{ old('role') == 'instructor' ? 'selected' : '' }}>
                                            Instructor
                                        </option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
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
                                            {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            <i class="fas fa-power-off mr-2 text-primary"></i>Active Status
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-user-plus mr-2"></i>Create Instructor
                                    </button>
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
            // Password show/hide toggle
            $('#show-password').on('change', function() {
                const passwordField = $('#password');
                passwordField.attr('type', this.checked ? 'text' : 'password');
            });


        });
    </script>
@endsection
