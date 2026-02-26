@extends('layouts.master')

@section('title', 'Certificate Details')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Certificate Details</h1>
        <div>
            <a href="{{ route('admin.certificates.download', $certificate->id) }}" class="d-sm-inline-block btn btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> Download Certificate
            </a>
            <a href="{{ route('admin.certificates.index') }}" class="d-sm-inline-block btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Certificates
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Certificate Preview -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Certificate Preview</h6>
                </div>
                <div class="card-body text-center">
                    @if($certificate->certificate_path)
                        @if(Storage::disk('public')->exists($certificate->certificate_path))
                            <iframe src="{{ asset('storage/' . $certificate->certificate_path) }}" width="100%" height="500px"></iframe>
                        @else
                            <div class="alert alert-warning">
                                Certificate file exists in database but not in storage.
                                <a href="{{ route('admin.certificates.regenerate', $certificate->id) }}" class="btn btn-info btn-sm ml-2">
                                    Regenerate Now
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning">
                            Certificate PDF not generated yet.
                            <a href="{{ route('admin.certificates.regenerate', $certificate->id) }}" class="btn btn-info btn-sm ml-2">
                                Generate Now
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Certificate Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Certificate Information</h6>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Certificate Number:</dt>
                        <dd class="col-sm-7">{{ $certificate->certificate_number }}</dd>

                        <dt class="col-sm-5">Issue Date:</dt>
                        <dd class="col-sm-7">{{ $certificate->issue_date->format('F d, Y') }}</dd>

                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7">
                            @if($certificate->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </dd>

                        <dt class="col-sm-5">Verification QR:</dt>
                        <dd class="col-sm-7 text-center">
                            <div class="verification-qr">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($certificate->verification_url) }}"
                                     alt="Verification QR Code"
                                     class="img-fluid"
                                     style="max-width: 100px;">
                                <div class="mt-2">
                                    <small class="text-muted">Scan to verify</small>
                                </div>
                                <div class="mt-1">
                                    <a href="{{ $certificate->verification_url }}" target="_blank" class="small text-primary">
                                        Open Link <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                        </dd>
                    </dl>

                    <hr>

                    <h6 class="font-weight-bold">Student Information</h6>
                    <dl class="row">
                        <dt class="col-sm-5">Name:</dt>
                        <dd class="col-sm-7">{{ $certificate->student->first_name }} {{ $certificate->student->last_name }}</dd>

                        <dt class="col-sm-5">Email:</dt>
                        <dd class="col-sm-7">{{ $certificate->student->email }}</dd>

                        <dt class="col-sm-5">Contact:</dt>
                        <dd class="col-sm-7">{{ $certificate->student->student_contact }}</dd>
                    </dl>

                    <hr>

                    <h6 class="font-weight-bold">Course Information</h6>
                    <dl class="row">
                        <dt class="col-sm-5">Course Name:</dt>
                        <dd class="col-sm-7">{{ $certificate->course->course_name }}</dd>

                        <dt class="col-sm-5">Course Type:</dt>
                        <dd class="col-sm-7">{{ $certificate->course->course_type }}</dd>

                        <dt class="col-sm-5">Theory Hours:</dt>
                        <dd class="col-sm-7">{{ $certificate->course->theory_hours }}</dd>

                        <dt class="col-sm-5">Practical Hours:</dt>
                        <dd class="col-sm-7">{{ $certificate->course->practical_hours }}</dd>
                    </dl>

                    <hr>

                    <div class="row">
                        <div class="col-sm-6">
                            <a href="{{ route('admin.certificates.regenerate', $certificate->id) }}" class="btn btn-info btn-block">
                                <i class="fas fa-sync"></i> Regenerate
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <form action="{{ route('admin.certificates.destroy', $certificate->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Are you sure you want to delete this certificate?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
