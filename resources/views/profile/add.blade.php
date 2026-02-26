@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Profile</h1>
    </div>

    <!-- Content Row -->

    <div class="row">

        <!-- Area Chart -->
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div
                style="background: #2a5c68;" class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 text-white font-weight-bold" >Update Profile</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <form action="{{ route('update_profile') }}" method="post" >
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <label class="label" for="student_name">Name</label>
                                    <input type="text" placeholder="Enter Name" name="name" id="name" value="{{ $user->name }}" class="form-control" required>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <label class="label" for="student_name">Email</label>
                                    <input type="email" placeholder="Email" name="email" id="email" value="{{ $user->email }}" class="form-control" required>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <label class="label" for="student_name">New Password (optional)</label>
                                    <input type="password" placeholder="Password" name="password" id="password" class="form-control">
                                </div>
                                <div class="col-lg-12 col-sm-12 mt-2">
                                    <input type="submit" value="Update Profile" class="btn btn-info">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
