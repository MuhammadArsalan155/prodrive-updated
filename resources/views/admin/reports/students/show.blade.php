@extends('layouts.master')

@section('title')
    Student Report - {{ $student->first_name }} {{ $student->last_name }}
@endsection

@section('styles')
    <style media="print">
        @page {
            size: A4;
            margin: 10mm;
        }

        .no-print,
        .main-header,
        .main-sidebar,
        .main-footer,
        .card-tools,
        .btn {
            display: none !important;
        }

        .content-wrapper {
            background-color: white !important;
            margin-left: 0 !important;
            padding-top: 0 !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            margin-bottom: 15px !important;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: white;
        }

        .row,
        .card,
        .card-body {
            page-break-inside: avoid;
        }
    </style>

    <style>
        /* Modern Color Palette */
        :root {
            --primary-color: #3b82f6;
            --primary-light: #dbeafe;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --success-light: #d1fae5;
            --warning-color: #f59e0b;
            --warning-light: #fef3c7;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --info-light: #cffafe;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --border-color: #e5e7eb;
            --text-muted: #6b7280;
            --shadow-light: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-medium: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-large: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-success: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --gradient-warning: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-info: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        /* Global Improvements */
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
        }

        .content-wrapper {
            background: transparent !important;
        }

        /* Enhanced Card Styling */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: var(--shadow-medium);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 2rem;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-large);
        }

        .card-header {
            border: none;
            border-radius: 16px 16px 0 0 !important;
            padding: 1.5rem 2rem;
            background: var(--gradient-primary);
            color: white;
        }

        .card-success .card-header {
            background: var(--gradient-success);
        }

        .card-warning .card-header {
            background: var(--gradient-warning);
        }

        .card-info .card-header {
            background: var(--gradient-info);
        }

        .card-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
            /* display: flex; */
            align-items: center;
        }

        .card-body {
            padding: 2rem;
        }

        /* Action Buttons Enhancement */
        .action-buttons {
            background: white;
            border-radius: 16px;
            padding: 1.5rem 2rem;
            box-shadow: var(--shadow-light);
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }

        .btn {
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-medium);
        }

        .btn-outline-secondary {
            border: 2px solid var(--secondary-color);
            color: var(--secondary-color);
            background: transparent;
        }

        .btn-outline-secondary:hover {
            background: var(--secondary-color);
            color: white;
        }

        .btn-outline-danger {
            border: 2px solid var(--danger-color);
            color: var(--danger-color);
            background: transparent;
        }

        .btn-outline-danger:hover {
            background: var(--danger-color);
            color: white;
        }

        .btn-outline-info {
            border: 2px solid var(--info-color);
            color: var(--info-color);
            background: transparent;
        }

        .btn-outline-info:hover {
            background: var(--info-color);
            color: white;
        }

        /* Student Profile Widget Enhancement */
        .widget-user-2 {
            background: var(--gradient-primary);
            color: white;
            overflow: hidden;
            position: relative;
        }

        .widget-user-2::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 0.8; }
        }

        .widget-user-header {
            padding: 2rem;
            position: relative;
            z-index: 2;
        }

        .widget-user-image {
            position: absolute;
            top: 50%;
            left: 2rem;
            margin-top: -50px;
        }

        .widget-user-image img {
            width: 100px;
            height: 100px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            box-shadow: var(--shadow-medium);
            transition: transform 0.3s ease;
        }

        .widget-user-image img:hover {
            transform: scale(1.05);
        }

        .widget-user-username {
            margin-left: 140px;
            font-size: 1.8rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .widget-user-desc {
            margin-left: 140px;
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* List Group Enhancement */
        .list-group-item {
            border: none;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
            background: transparent;
            border-bottom: 1px solid var(--border-color);
        }

        .list-group-item:hover {
            background: var(--light-color);
            transform: translateX(4px);
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        /* Progress Section Enhancement */
        .progress-section {
            background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,250,252,0.9) 100%);
            border-radius: 16px;
            margin: 1rem 0;
            box-shadow: var(--shadow-light);
            border: 1px solid var(--border-color);
        }

        .progress {
            height: 12px;
            border-radius: 10px;
            background: var(--light-color);
            overflow: hidden;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }

        .progress-bar {
            border-radius: 10px;
            background: linear-gradient(90deg, var(--primary-color) 0%, #60a5fa 100%);
            position: relative;
            overflow: hidden;
        }

        .progress-bar.bg-success {
            background: linear-gradient(90deg, var(--success-color) 0%, #34d399 100%);
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg,
                transparent 25%,
                rgba(255,255,255,0.2) 25%,
                rgba(255,255,255,0.2) 50%,
                transparent 50%,
                transparent 75%,
                rgba(255,255,255,0.2) 75%);
            background-size: 20px 20px;
            animation: move 1s linear infinite;
        }

        @keyframes move {
            0% { background-position: 0 0; }
            100% { background-position: 20px 20px; }
        }

        /* Small Box Enhancement */
        .small-box {
            border-radius: 16px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-medium);
            transition: all 0.3s ease;
            border: none;
        }

        .small-box:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-large);
        }

        .small-box.bg-info {
            background: var(--gradient-info) !important;
        }

        .small-box.bg-success {
            background: var(--gradient-success) !important;
        }

        .small-box.bg-warning {
            background: var(--gradient-warning) !important;
        }

        .small-box .inner {
            padding: 1.5rem;
            position: relative;
            z-index: 2;
        }

        .small-box .inner h3 {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .small-box .inner p {
            font-size: 1rem;
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }

        .small-box .icon {
            position: absolute;
            top: 50%;
            right: 1.5rem;
            transform: translateY(-50%);
            font-size: 3rem;
            opacity: 0.3;
            z-index: 1;
        }

        /* Info Box Enhancement */
        .info-box {
            border-radius: 16px;
            box-shadow: var(--shadow-light);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .info-box:hover {
            box-shadow: var(--shadow-medium);
            transform: translateY(-2px);
        }

        .info-box-icon {
            border-radius: 12px;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .info-box-icon.bg-info {
            background: var(--gradient-info) !important;
        }

        .info-box-icon.bg-success {
            background: var(--gradient-success) !important;
        }

        .info-box-content {
            padding: 1rem 1.5rem;
        }

        .info-box-text {
            font-size: 0.9rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }

        .info-box-number {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-top: 0.5rem;
        }

        /* Badge Enhancement */
        .badge {
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background: var(--success-color);
            color: white;
        }

        .badge-warning {
            background: var(--warning-color);
            color: white;
        }

        .badge-primary {
            background: var(--primary-color);
            color: white;
        }

        .badge-secondary {
            background: var(--secondary-color);
            color: white;
        }

        .badge-danger {
            background: var(--danger-color);
            color: white;
        }

        /* Table Enhancement */
        .table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
        }

        .table thead th {
            background: var(--light-color);
            border: none;
            padding: 1rem;
            font-weight: 600;
            color: var(--dark-color);
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 1rem;
            border-color: var(--border-color);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: rgba(59, 130, 246, 0.05);
        }

        /* Callout Enhancement */
        .callout {
            border: none;
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            position: relative;
            overflow: hidden;
        }

        .callout::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            border-radius: 2px;
        }

        .callout-info {
            background: var(--info-light);
            border-left: 4px solid var(--info-color);
        }

        .callout-info::before {
            background: var(--info-color);
        }

        .callout-warning {
            background: var(--warning-light);
            border-left: 4px solid var(--warning-color);
        }

        .callout-warning::before {
            background: var(--warning-color);
        }

        .callout h6 {
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Alert Enhancement */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            box-shadow: var(--shadow-light);
        }

        .alert-info {
            background: var(--info-light);
            color: var(--info-color);
            border-left: 4px solid var(--info-color);
        }

        .alert-warning {
            background: var(--warning-light);
            color: var(--warning-color);
            border-left: 4px solid var(--warning-color);
        }

        /* Breadcrumb Enhancement */
        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .breadcrumb-item a:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }

        /* Page Header Enhancement */
        .page-header h1 {
            color: var(--dark-color);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Responsive Enhancements */
        @media (max-width: 768px) {
            .widget-user-2 .widget-user-image {
                position: static;
                margin-bottom: 1rem;
                text-align: center;
            }

            .widget-user-2 .widget-user-username,
            .widget-user-2 .widget-user-desc {
                margin-left: 0;
                text-align: center;
            }

            .small-box .inner {
                padding: 1rem;
            }

            .small-box .inner h3 {
                font-size: 1.8rem;
            }

            .card-body {
                padding: 1.5rem;
            }

            .action-buttons {
                padding: 1rem;
            }
        }

        /* Loading Animation */
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }

        .loading {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite;
        }

        /* Glassmorphism Effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Focus States */
        .btn:focus,
        .card:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
    </style>
@endsection

@section('page-header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 page-header">
                    <i class="fas fa-user-graduate text-primary"></i>
                    Student Report
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"> Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.students.index') }}"> Student Reports</a></li>
                    <li class="breadcrumb-item active">{{ $student->first_name }} {{ $student->last_name }}</li>
                </ol>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Enhanced Action Buttons -->
        <div class="action-buttons">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.reports.students.index') }}" class="btn btn-outline-secondary m-1">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </a>
                    <a href="{{ route('admin.reports.students.pdf', $student->id) }}" class="btn btn-outline-danger m-1" target="_blank">
                        <i class="fas fa-file-pdf"></i>
                        Download PDF
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-info m-1">
                        <i class="fas fa-print"></i>
                        Print Report
                    </button>
                </div>
                <div class="text-muted">
                    <i class="fas fa-clock me-2"></i>
                    Last Updated: {{ $student->updated_at->diffForHumans() }}
                </div>
            </div>
        </div>

        <!-- Student and Course Information -->
        <div class="row">
            <!-- Enhanced Student Information Card -->
            <div class="col-lg-6 mb-4">
                <div class="card widget-user-2">
                    <div class="widget-user-header">
                        <div class="widget-user-image">
                            @if ($student->profile_photo)
                                <img class="img-circle elevation-3" src="{{ asset('profile/' . $student->profile_photo) }}" alt="Student Photo">
                            @else
                                <img class="img-circle elevation-3" src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp" alt="Default Photo">
                            @endif
                        </div>
                        <h3 class="widget-user-username">{{ $student->first_name }} {{ $student->last_name }}</h3>
                        <h5 class="widget-user-desc">Student ID: {{ $student->id }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong><i class="fas fa-envelope me-3 text-primary"></i> Email</strong>
                                <span>{{ $student->email }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong><i class="fas fa-phone me-3 text-success"></i> Contact</strong>
                                <span>{{ $student->student_contact ?: 'Not Provided' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong><i class="fas fa-birthday-cake me-3 text-warning"></i> Date of Birth</strong>
                                <span>{{ $student->student_dob ? date('M d, Y', strtotime($student->student_dob)) : 'N/A' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong><i class="fas fa-calendar-plus me-3 text-info"></i> Joining Date</strong>
                                <span>{{ $student->joining_date ? date('M d, Y', strtotime($student->joining_date)) : 'N/A' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong><i class="fas fa-map-marker-alt me-3 text-danger"></i> Address</strong>
                                <span>{{ $student->address ?: 'Not Provided' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Enhanced Course Information Card -->
            <div class="col-lg-6 mb-4">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-graduation-cap"></i>
                            Course Details
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        @if ($student->course)
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong><i class="fas fa-book me-3 text-primary"></i> Course Name</strong>
                                    <span>{{ $student->course->course_name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong><i class="fas fa-tag me-3 text-info"></i> Course Type</strong>
                                    <span>{{ $student->course->course_type }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong><i class="fas fa-chalkboard-teacher me-3 text-warning"></i> Theory Hours</strong>
                                    <span>{{ $student->course->theory_hours }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong><i class="fas fa-tools me-3 text-success"></i> Practical Hours</strong>
                                    <span>{{ $student->course->practical_hours }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong><i class="fas fa-user-tie me-3 text-secondary"></i> Instructor</strong>
                                    <span>{{ $student->instructor ? $student->instructor->instructor_name : 'Not Assigned' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong><i class="fas fa-dollar-sign me-3 text-success"></i> Course Price</strong>
                                    <span class="badge badge-success">${{ number_format($student->course->course_price, 2) }}</span>
                                </li>
                            </ul>
                        @else
                            <div class="alert alert-warning m-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No course information available.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Course Progress Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line"></i>
                             Course Progress Tracking
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Enhanced Theory Progress -->
                            <div class="col-lg-6">
                                <div class="progress-section">
                                    <div class="d-flex justify-content-between align-items-center m-3">
                                        <h5 class="text-primary mb-0">
                                            <i class="fas fa-book-open me-3"></i> Theory Progress
                                        </h5>
                                        <span class="badge badge-primary">{{ $courseProgress['theory']['percentage'] }}%</span>
                                    </div>
                                    <div class="progress m-3">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                             style="width: {{ $courseProgress['theory']['percentage'] }}%"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h4 class="text-primary">{{ $courseProgress['theory']['completed'] }}</h4>
                                                <small class="text-muted">Hours Completed</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                @if ($student->theory_status == 'completed')
                                                    <span class="badge badge-success">Completed</span>
                                                @elseif($student->theory_status == 'in_progress')
                                                    <span class="badge badge-primary">In Progress</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $student->theory_status ?: 'Not Started' }}</span>
                                                @endif
                                                <small class="text-muted d-block">Status</small>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($student->theory_completion_date)
                                        <div class="mt-3 text-center">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-check me-1"></i>
                                                Completed: {{ date('M d, Y', strtotime($student->theory_completion_date)) }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Enhanced Practical Progress -->
                            <div class="col-lg-6">
                                <div class="progress-section">
                                    <div class="d-flex justify-content-between align-items-center m-3">
                                        <h5 class="text-success mb-0">
                                            <i class="fas fa-tools me-3"></i> Practical Progress
                                        </h5>
                                        <span class="badge badge-success">{{ $courseProgress['practical']['percentage'] }}%</span>
                                    </div>
                                    <div class="progress m-3">
                                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                             style="width: {{ $courseProgress['practical']['percentage'] }}%"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h4 class="text-success">{{ $courseProgress['practical']['completed'] }}</h4>
                                                <small class="text-muted">Hours Completed</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                @if ($student->practical_status == 'completed')
                                                    <span class="badge badge-success">Completed</span>
                                                @elseif($student->practical_status == 'in_progress')
                                                    <span class="badge badge-primary">In Progress</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $student->practical_status ?: 'Not Started' }}</span>
                                                @endif
                                                <small class="text-muted d-block">Status</small>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($student->practical_completion_date)
                                        <div class="mt-3 text-center">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-check me-1"></i>
                                                Completed: {{ date('M d, Y', strtotime($student->practical_completion_date)) }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Practical Schedule -->
                        @if ($student->practicalSchedule)
                            <div class="mt-4 pt-4 border-top">
                                <h5 class="text-dark mb-4">
                                    <i class="fas fa-calendar-alt text-info me-3"></i>
                                    Upcoming Practical Schedule
                                </h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info">
                                                <i class="fas fa-clock text-white"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Schedule</span>
                                                <span class="info-box-number">
                                                    {{ date('M d, Y', strtotime($student->practicalSchedule->date)) }}
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ date('h:i A', strtotime($student->practicalSchedule->start_time)) }} -
                                                        {{ date('h:i A', strtotime($student->practicalSchedule->end_time)) }}
                                                    </small>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success">
                                                <i class="fas fa-user-tie text-white"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Instructor</span>
                                                <span class="info-box-number">
                                                    {{ $student->practicalSchedule->instructor ? $student->practicalSchedule->instructor->instructor_name : 'Not Assigned' }}
                                                    <br>
                                                    <small class="text-muted">{{ $student->practicalSchedule->session_type }}</small>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Financial Overview Section -->
        <div class="row">
            <div class="col-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-money-bill-wave"></i>
                            Financial Overview
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Financial Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>${{ number_format($totalBilled, 2) }}</h3>
                                        <p>Total Billed</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>${{ number_format($totalPaid, 2) }}</h3>
                                        <p>Total Paid</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>${{ number_format($pendingPayments, 2) }}</h3>
                                        <p>Pending Payments</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Invoices and Payments -->
                        <div class="mt-4">
                            <h5 class="mb-4">
                                <i class="fas fa-receipt me-3"></i>
                                Invoices and Payments
                            </h5>
                            @if (count($student->invoices) > 0)
                                @foreach ($student->invoices as $invoice)
                                    <div class="card mb-4 shadow-sm">
                                        <div class="card-header bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-file-invoice me-3"></i>
                                                    Invoice #{{ $invoice->invoice_number }}
                                                </h6>
                                                <span class="badge badge-{{ $invoice->status == 'paid' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($invoice->status) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <h4 class="text-primary">${{ number_format($invoice->amount, 2) }}</h4>
                                                        <small class="text-muted">Invoice Amount</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Course:</strong><br>
                                                    <span class="text-muted">{{ $invoice->course ? $invoice->course->course_name : 'N/A' }}</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <strong>Created:</strong><br>
                                                    <span class="text-muted">{{ date('M d, Y', strtotime($invoice->created_at)) }}</span>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="text-center">
                                                        @if($invoice->status == 'paid')
                                                            <i class="fas fa-check-circle text-success fa-2x"></i>
                                                        @else
                                                            <i class="fas fa-clock text-warning fa-2x"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Enhanced Installments -->
                                            @if (count($invoice->installments) > 0)
                                                <div class="mt-4">
                                                    <h6 class="text-primary mb-3">
                                                        <i class="fas fa-list-ol me-3"></i>Installments
                                                    </h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th><i class="fas fa-dollar-sign me-2"></i>Amount</th>
                                                                    <th><i class="fas fa-calendar me-2"></i>Due Date</th>
                                                                    <th><i class="fas fa-info-circle me-2"></i>Status</th>
                                                                    <th><i class="fas fa-check me-2"></i>Paid Date</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($invoice->installments as $installment)
                                                                    <tr>
                                                                        <td>
                                                                            <strong>${{ number_format($installment->amount, 2) }}</strong>
                                                                        </td>
                                                                        <td>{{ date('M d, Y', strtotime($installment->due_date)) }}</td>
                                                                        <td>
                                                                            <span class="badge badge-{{ $installment->status == 'paid' ? 'success' : ($installment->is_overdue ? 'danger' : 'warning') }}">
                                                                                {{ ucfirst($installment->status) }}
                                                                                {{ $installment->is_overdue ? '(Overdue)' : '' }}
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            {{ $installment->paid_at ? date('M d, Y', strtotime($installment->paid_at)) : '-' }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Enhanced Payments -->
                                            @if (count($invoice->payments) > 0)
                                                <div class="mt-4">
                                                    <h6 class="text-success mb-3">
                                                        <i class="fas fa-credit-card me-3"></i>Payments
                                                    </h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th><i class="fas fa-dollar-sign me-2"></i>Amount</th>
                                                                    <th><i class="fas fa-credit-card me-2"></i>Method</th>
                                                                    <th><i class="fas fa-info-circle me-2"></i>Status</th>
                                                                    <th><i class="fas fa-calendar me-2"></i>Date</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($invoice->payments as $payment)
                                                                    <tr>
                                                                        <td>
                                                                            <strong>${{ number_format($payment->amount, 2) }}</strong>
                                                                        </td>
                                                                        <td>{{ $payment->paymentMethod ? $payment->paymentMethod->name : 'N/A' }}</td>
                                                                        <td>
                                                                            <span class="badge badge-{{ $payment->status == 'completed' ? 'success' : 'warning' }}">
                                                                                {{ ucfirst($payment->status) }}
                                                                            </span>
                                                                        </td>
                                                                        <td>{{ date('M d, Y', strtotime($payment->created_at)) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle me-3"></i>
                                    No invoices found for this student.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Performance Reports Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-list"></i>
                            Student Performance Reports
                        </h3>
                    </div>
                    <div class="card-body">
                        @if (count($progressReports) > 0)
                            @foreach ($progressReports as $report)
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="fas fa-file-alt text-primary me-3"></i>
                                                Report from {{ date('M d, Y', strtotime($report->created_at)) }}
                                            </h6>
                                            @if ($report->rating)
                                                <div class="rating-display">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $report->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                    @endfor
                                                    <span class="badge badge-primary ms-2">{{ $report->rating }}/5</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="info-item">
                                                    <i class="fas fa-user-tie text-primary me-3"></i>
                                                    <strong>Instructor:</strong>
                                                    <span class="text-muted">{{ $report->instructor ? $report->instructor->instructor_name : 'N/A' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-item">
                                                    <i class="fas fa-graduation-cap text-success me-3"></i>
                                                    <strong>Course:</strong>
                                                    <span class="text-muted">{{ $report->course ? $report->course->course_name : 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($report->performance_notes)
                                            <div class="callout callout-info">
                                                <h6 class="text-info">
                                                    <i class="fas fa-sticky-note"></i>
                                                    Performance Notes
                                                </h6>
                                                <p class="mb-0">{{ $report->performance_notes }}</p>
                                            </div>
                                        @endif

                                        @if ($report->areas_of_improvement)
                                            <div class="callout callout-warning">
                                                <h6 class="text-warning">
                                                    <i class="fas fa-chart-line"></i>
                                                    Areas for Improvement
                                                </h6>
                                                <p class="mb-0">{{ $report->areas_of_improvement }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle me-3"></i>
                                No progress reports available for this student.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(function() {
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Add smooth scrolling for anchor links
        $('a[href^="#"]').on('click', function(event) {
            var target = $(this.getAttribute('href'));
            if( target.length ) {
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 100
                }, 1000);
            }
        });

        // Add loading state for buttons
        $('.btn').on('click', function() {
            var $btn = $(this);
            if (!$btn.hasClass('no-loading')) {
                $btn.addClass('loading').prop('disabled', true);
                setTimeout(function() {
                    $btn.removeClass('loading').prop('disabled', false);
                }, 2000);
            }
        });

        // Animate progress bars on scroll
        function animateProgressBars() {
            $('.progress-bar').each(function() {
                var $this = $(this);
                var width = $this.attr('style').match(/width:\s*(\d+)%/);
                if (width) {
                    $this.css('width', '0%').animate({
                        width: width[1] + '%'
                    }, 1500, 'easeOutCubic');
                }
            });
        }

        // Trigger animation when progress section comes into view
        $(window).on('scroll', function() {
            $('.progress-section').each(function() {
                var $this = $(this);
                var scrollTop = $(window).scrollTop();
                var elementTop = $this.offset().top;
                var elementHeight = $this.height();
                var windowHeight = $(window).height();

                if (scrollTop + windowHeight > elementTop + elementHeight / 2 && !$this.hasClass('animated')) {
                    $this.addClass('animated');
                    animateProgressBars();
                }
            });
        });

        // Add hover effects for cards
        $('.card').hover(
            function() {
                $(this).addClass('shadow-lg');
            },
            function() {
                $(this).removeClass('shadow-lg');
            }
        );

        // Print functionality enhancement
        window.printReport = function() {
            window.print();
        };

        // Add fade-in animation for cards on load
        $('.card').each(function(index) {
            $(this).css('opacity', '0').delay(index * 100).animate({
                opacity: 1
            }, 500);
        });
    });

    // Custom easing function
    $.easing.easeOutCubic = function (x, t, b, c, d) {
        return c*((t=t/d-1)*t*t + 1) + b;
    };
</script>
@endsection
