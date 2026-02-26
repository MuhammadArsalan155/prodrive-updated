@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Announcement Details</h1>
            <div>
                <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-sm btn-primary shadow-sm mr-2">
                    <i class="fas fa-edit fa-sm text-white-50"></i> Edit Announcement
                </a>
                <a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Announcements
                </a>
            </div>
        </div>

        <!-- Announcement Details -->
        <div class="row">
            <div class="col-lg-8">
                <!-- Content -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Announcement Content</h6>
                        <span class="badge {{ $announcement->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $announcement->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <h4 class="mb-3">{{ $announcement->title }}</h4>
                        <div class="border-bottom pb-3 mb-3">
                            {!! $announcement->content !!}
                        </div>
                        
                        @if($announcement->attachment)
                            <div class="mt-3">
                                <strong>Attachment:</strong>
                                <a href="{{ route('admin.announcements.download', $announcement) }}" class="btn btn-sm btn-outline-primary ml-2">
                                    <i class="fas fa-download"></i> {{ basename($announcement->attachment) }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Announcement Metadata -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Announcement Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Created By:</strong> {{ $announcement->creator->name }}
                        </div>
                        <div class="mb-3">
                            <strong>Created At:</strong> {{ $announcement->created_at->format('Y-m-d H:i:s') }}
                        </div>
                        <div class="mb-3">
                            <strong>Last Updated:</strong> {{ $announcement->updated_at->format('Y-m-d H:i:s') }}
                        </div>
                        <div class="mb-3">
                            <strong>Expiry Date:</strong> 
                            @if($announcement->expires_at)
                                {{ $announcement->expires_at->format('Y-m-d H:i:s') }}
                                @if($announcement->expires_at < now())
                                    <span class="badge badge-warning">Expired</span>
                                @endif
                            @else
                                <span class="text-muted">Never</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <strong>Target Audience:</strong> 
                            @if($announcement->target_type === 'all')
                                <span class="badge badge-info">All Users</span>
                            @elseif($announcement->target_type === 'role')
                                <span class="badge badge-primary">Specific Roles</span>
                            @elseif($announcement->target_type === 'user')
                                <span class="badge badge-warning">Specific Users</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Target Details -->
                @if($announcement->target_type === 'role' && $announcement->targetRoles->count() > 0)
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Target Roles</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                @foreach($announcement->targetRoles as $role)
                                    <li class="list-group-item">
                                        {{ $role->display_name ?? $role->name }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                
                @if($announcement->target_type === 'user')
                    @if($announcement->targetUsers->count() > 0)
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Target Users</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    @foreach($announcement->targetUsers as $user)
                                        <li class="list-group-item">
                                            {{ $user->name }} <small class="text-muted">({{ $user->email }})</small>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    
                    @if($announcement->targetStudents->count() > 0)
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Target Students</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    @foreach($announcement->targetStudents as $student)
                                        <li class="list-group-item">
                                            {{ $student->first_name }} {{ $student->last_name }} 
                                            <small class="text-muted">({{ $student->email }})</small>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    
                    @if($announcement->targetInstructors->count() > 0)
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Target Instructors</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    @foreach($announcement->targetInstructors as $instructor)
                                        <li class="list-group-item">
                                            {{ $instructor->name ?? $instructor->first_name . ' ' . $instructor->last_name }} 
                                            <small class="text-muted">({{ $instructor->email }})</small>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection