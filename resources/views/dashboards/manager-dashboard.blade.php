@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            {{-- <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dashboardFilterDropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <div class="dropdown-menu" aria-labelledby="dashboardFilterDropdown">
                    <a class="dropdown-item" href="#" data-filter="today">Today</a>
                    <a class="dropdown-item" href="#" data-filter="week">This Week</a>
                    <a class="dropdown-item" href="#" data-filter="month">This Month</a>
                    <a class="dropdown-item" href="#" data-filter="year">This Year</a>
                </div>
            </div> --}}
        </div>

        <!-- Content Row -->
        <div class="row">
            <!-- Courses Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Courses</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalCoursesCount">
                                    {{ App\Models\Course::count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Students</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalStudentsCount">
                                    {{ App\Models\Student::count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Completion Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Course Completion
                                </div>
                                @php
                                    $total_students = App\Models\Student::count();
                                    $completed_students = App\Models\Student::where('course_status', 1)->count();
                                    $completion_percentage =
                                        $total_students > 0
                                            ? round(($completed_students / $total_students) * 100, 2)
                                            : 0;
                                @endphp
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                            {{ $completion_percentage }}%
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="progress progress-sm mr-2">
                                            <div class="progress-bar bg-info" role="progressbar"
                                                style="width: {{ $completion_percentage }}%"
                                                aria-valuenow="{{ $completion_percentage }}" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructors Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Total Instructors</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ App\Models\Instructor::count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

         <!-- Announcements Section -->
         @include('components.dashboard-announcements')
         
        <!-- Content Row -->
        <div class="row">
            <!-- Area Chart -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Student Enrollment Overview</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="studentEnrollmentChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pie Chart -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Course Distribution</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="courseDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row -->
        <div class="row">
            <!-- Recent Activities -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @php
                                $recentActivities = collect([
                                    [
                                        'icon' => 'user-plus',
                                        'text' => 'New student enrolled in Driving Course',
                                        'time' => '2 hours ago',
                                    ],
                                    [
                                        'icon' => 'calendar-check',
                                        'text' => 'Course schedule updated',
                                        'time' => '4 hours ago',
                                    ],
                                    [
                                        'icon' => 'chalkboard-teacher',
                                        'text' => 'Instructor John Doe added',
                                        'time' => '1 day ago',
                                    ],
                                    [
                                        'icon' => 'money-bill-wave',
                                        'text' => 'New payment received',
                                        'time' => '2 days ago',
                                    ],
                                ]);
                            @endphp
                            @foreach ($recentActivities as $activity)
                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <i class="fas fa-{{ $activity['icon'] }}"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <p>{{ $activity['text'] }}</p>
                                        <span class="time">{{ $activity['time'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Schedules -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Upcoming Course Schedules</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $upcomingSchedules = App\Models\CourseSchedule::with(['course', 'instructor'])
                                ->where('date', '>=', now())
                                ->orderBy('date')
                                ->limit(5)
                                ->get();
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>Instructor</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($upcomingSchedules as $schedule)
                                        <tr>
                                            <td>{{ $schedule->course->course_name }}</td>
                                            <td>{{ $schedule->instructor->instructor_name }}</td>
                                            <td>{{ $schedule->date->format('M d, Y') }}</td>
                                            <td>{{ $schedule->start_time->format('H:i') }} -
                                                {{ $schedule->end_time->format('H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline-item {
            display: flex;
            margin-bottom: 15px;
            align-items: center;
        }

        .timeline-icon {
            width: 50px;
            height: 50px;
            background-color: #4e73df;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .timeline-content {
            flex-grow: 1;
        }

        .timeline-content .time {
            color: #888;
            font-size: 0.8em;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add to existing script in dashboard view

            // Attach event listeners to filter dropdown
            document.querySelectorAll('[data-filter]').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const filter = this.getAttribute('data-filter');
                    updateDashboardData(filter);
                });
            });
            // Student Enrollment Chart
            const studentEnrollmentCtx = document.getElementById('studentEnrollmentChart').getContext('2d');
            const studentEnrollmentChart = new Chart(studentEnrollmentCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Student Enrollments',
                        data: [12, 19, 3, 5, 2, 3],
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Monthly Student Enrollment'
                        }
                    }
                }
            });

            // Course Distribution Chart
            const courseDistributionCtx = document.getElementById('courseDistributionChart').getContext('2d');
            const courseDistributionChart = new Chart(courseDistributionCtx, {
                type: 'pie',
                data: {
                    labels: ['Theory', 'Practical', 'Advanced'],
                    datasets: [{
                        data: [300, 50, 100],
                        backgroundColor: [
                            'rgb(255, 99, 132)',
                            'rgb(54, 162, 235)',
                            'rgb(255, 205, 86)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Course Type Distribution'
                        }
                    }
                }
            });

            
        });
    </script>
@endsection
