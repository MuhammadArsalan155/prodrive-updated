@extends('layouts.master')

@section('title', 'User Details')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">User Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
        <li class="breadcrumb-item active">User Details</li>
    </ol>

    <div class="row">
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-1"></i>
                    User Information
                    <span class="badge bg-info float-end">{{ ucfirst($type) }}</span>
                </div>
                <div class="card-body">
                    @switch($type)
                        @case('students')
                            <p><strong>First Name:</strong> {{ $user->first_name }}</p>
                            <p><strong>Last Name:</strong> {{ $user->last_name }}</p>
                            <p><strong>Student Contact:</strong> {{ $user->student_contact }}</p>
                            <p><strong>Date of Birth:</strong> {{ $user->student_dob }}</p>
                            @if($user->course)
                                <p><strong>Course:</strong> {{ $user->course->name }}</p>
                            @endif
                            @if($user->instructor)
                                <p><strong>Instructor:</strong> {{ $user->instructor->instructor_name }}</p>
                            @endif
                            @break

                        @case('instructors')
                            <p><strong>Name:</strong> {{ $user->instructor_name }}</p>
                            <p><strong>Email:</strong> {{ $user->email }}</p>
                            <p><strong>Contact:</strong> {{ $user->contact }}</p>
                            <p><strong>License Number:</strong> {{ $user->license_number }}</p>
                            @break

                        @default
                            <p><strong>Name:</strong> {{ $user->name }}</p>
                            <p><strong>Email:</strong> {{ $user->email }}</p>
                    @endswitch

                    <p><strong>ID:</strong> {{ $user->id }}</p>
                    <p><strong>Created At:</strong> {{ $user->created_at->format('Y-m-d H:i') }}</p>
                    <p><strong>Last Updated:</strong> {{ $user->updated_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>

            @if($user->profile_photo)
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-image me-1"></i>
                        Profile Photo
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ asset('storage/' . $user->profile_photo) }}" 
                             alt="Profile Photo" 
                             class="img-fluid rounded-circle" 
                             style="max-width: 250px;">
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-tag me-1"></i>
                    Assigned Roles
                </div>
                <div class="card-body">
                    @if(count($user->roles) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Role Name</th>
                                        <th>Description</th>
                                        <th>Assigned At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->roles as $role)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">{{ $role->display_name }}</span>
                                            </td>
                                            <td>{{ $role->description }}</td>
                                            <td>{{ $role->pivot->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            This user has no roles assigned.
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-key me-1"></i>
                    Effective Permissions
                </div>
                <div class="card-body">
                    @if(count($userPermissions) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" id="permissionsTable">
                                <thead>
                                    <tr>
                                        <th>Permission Name</th>
                                        <th>Description</th>
                                        <th>Source Role</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($userPermissions as $permission)
                                        <tr>
                                            <td>
                                                <span class="badge bg-info">{{ $permission->display_name }}</span>
                                            </td>
                                            <td>{{ $permission->description }}</td>
                                            <td>
                                                @foreach($user->roles as $role)
                                                    @if($role->permissions->contains($permission->id))
                                                        <span class="badge bg-secondary me-1">
                                                            {{ $role->display_name }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            This user has no permissions assigned.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="mb-4">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left"></i> Back to User List
        </a>
        <a href="{{ route('admin.users.edit', ['type' => $type, 'id' => $user->id]) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Roles and Permissions
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#permissionsTable').DataTable({
            paging: true,
            pageLength: 10,
            ordering: true,
            info: true,
            searching: true
        });
    });
</script>
@endsection