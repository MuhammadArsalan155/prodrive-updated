@extends('layouts.master')

@section('styles')
<style>
    .dash-stat-card {
        background: #fff;
        border-radius: .75rem !important;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,.08), 0 2px 4px -1px rgba(0,0,0,.05) !important;
        transition: all .18s ease;
        border: none !important;
        overflow: hidden;
    }
    .dash-stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,.1) !important; }
    .dash-stat-card .stat-accent {
        width: 5px; position: absolute; top: 0; left: 0; bottom: 0; border-radius: .75rem 0 0 .75rem;
    }
    .dash-stat-card .card-body { padding: 1.25rem 1.25rem 1.25rem 1.5rem; position: relative; }
    .dash-stat-card .stat-icon {
        width: 48px; height: 48px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
    }
    .dash-stat-card .stat-label { font-size: .7rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
    .dash-stat-card .stat-number { font-size: 2rem; font-weight: 800; line-height: 1.1; }

    /* Timeline */
    .timeline { position: relative; padding: 0; }
    .timeline-item { display: flex; align-items: flex-start; margin-bottom: 1rem; gap: .9rem; }
    .timeline-icon {
        width: 38px; height: 38px; min-width: 38px;
        background: var(--pd-blue); color: #fff;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: .85rem;
    }
    .timeline-content p { margin: 0; font-size: .875rem; color: #374151; }
    .timeline-content .time { color: #94a3b8; font-size: .75rem; }

    /* Chart cards */
    .chart-card .card-header { background: #fff !important; border-bottom: 1px solid #e2e8f0 !important; }
    .chart-card .card-header h6 { font-weight: 700; font-size: .9rem; color: #1a2e4a; }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="pd-page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="font-weight:800;">Admin Dashboard</h4>
            <p style="font-size:.85rem;">Welcome back — here's what's happening at ProDrive Academy</p>
        </div>
        <div class="d-none d-md-block text-right">
            <div style="font-size:1.5rem; font-weight:800; opacity:.9;">
                {{ now()->format('M d, Y') }}
            </div>
            <div style="font-size:.8rem; opacity:.65;">{{ now()->format('l') }}</div>
        </div>
    </div>

    @php
        $totalStudents    = App\Models\Student::count();
        $completedStudents= App\Models\Student::where('course_status', 2)->count();
        $activeStudents   = App\Models\Student::where('course_status', 1)->count();
        $completionPct    = $totalStudents > 0 ? round(($completedStudents / $totalStudents) * 100, 1) : 0;
        $totalCourses     = App\Models\Course::count();
        $totalInstructors = App\Models\Instructor::count();
    @endphp

    <!-- Stat Cards Row -->
    <div class="row mb-4">
        <!-- Courses -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dash-stat-card position-relative">
                <div class="stat-accent" style="background:var(--pd-blue);"></div>
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label text-primary mb-1">Total Courses</div>
                        <div class="stat-number text-gray-800">{{ $totalCourses }}</div>
                    </div>
                    <div class="stat-icon" style="background:rgba(37,99,235,.1); color:var(--pd-blue);">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dash-stat-card position-relative">
                <div class="stat-accent" style="background:var(--pd-success);"></div>
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label text-success mb-1">Total Students</div>
                        <div class="stat-number text-gray-800">{{ $totalStudents }}</div>
                        <div style="font-size:.72rem; color:#64748b;">{{ $activeStudents }} active</div>
                    </div>
                    <div class="stat-icon" style="background:rgba(16,185,129,.1); color:var(--pd-success);">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Completion -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dash-stat-card position-relative">
                <div class="stat-accent" style="background:var(--pd-info);"></div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="stat-label" style="color:var(--pd-info);">Completion Rate</div>
                        <div class="stat-icon" style="background:rgba(6,182,212,.1); color:var(--pd-info); width:36px;height:36px;font-size:1rem;">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                    </div>
                    <div class="stat-number text-gray-800">{{ $completionPct }}%</div>
                    <div class="progress mt-2" style="height:5px; border-radius:3px; background:#e2e8f0;">
                        <div class="progress-bar" style="width:{{ $completionPct }}%; background:var(--pd-info); border-radius:3px;"></div>
                    </div>
                    <div style="font-size:.72rem; color:#64748b; margin-top:.3rem;">{{ $completedStudents }} of {{ $totalStudents }} completed</div>
                </div>
            </div>
        </div>

        <!-- Instructors -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dash-stat-card position-relative">
                <div class="stat-accent" style="background:var(--pd-warning);"></div>
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label text-warning mb-1">Total Instructors</div>
                        <div class="stat-number text-gray-800">{{ $totalInstructors }}</div>
                    </div>
                    <div class="stat-icon" style="background:rgba(245,158,11,.1); color:var(--pd-warning);">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Area Chart -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card chart-card shadow">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0"><i class="fas fa-chart-line mr-2" style="color:var(--pd-blue);"></i>Student Enrollment Overview</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="studentEnrollmentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card chart-card shadow">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0"><i class="fas fa-chart-pie mr-2" style="color:var(--pd-teal);"></i>Course Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-2 pb-2">
                        <canvas id="courseDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="row">
        <!-- Recent Activities -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0"><i class="fas fa-bell mr-2" style="color:var(--pd-warning);"></i>Recent Activities</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @php
                            $recentActivities = collect([
                                ['icon' => 'user-plus',         'text' => 'New student enrolled in Driving Course',   'time' => '2 hours ago',   'color' => 'var(--pd-blue)'],
                                ['icon' => 'calendar-check',    'text' => 'Course schedule updated',                  'time' => '4 hours ago',   'color' => 'var(--pd-teal)'],
                                ['icon' => 'chalkboard-teacher','text' => 'Instructor John Doe added',                'time' => '1 day ago',     'color' => 'var(--pd-success)'],
                                ['icon' => 'money-bill-wave',   'text' => 'New payment received',                     'time' => '2 days ago',    'color' => 'var(--pd-warning)'],
                            ]);
                        @endphp
                        @foreach ($recentActivities as $activity)
                            <div class="timeline-item">
                                <div class="timeline-icon" style="background:{{ $activity['color'] }};">
                                    <i class="fas fa-{{ $activity['icon'] }}"></i>
                                </div>
                                <div class="timeline-content pt-1">
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
            <div class="card shadow">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0"><i class="fas fa-calendar-alt mr-2" style="color:var(--pd-blue);"></i>Upcoming Course Schedules</h6>
                    <a href="{{ route('course-schedules.index') }}" class="btn btn-primary btn-sm" style="font-size:.75rem;">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @php
                        $upcomingSchedules = App\Models\CourseSchedule::with(['course', 'instructor'])
                            ->where('date', '>=', now())
                            ->orderBy('date')
                            ->limit(5)
                            ->get();
                    @endphp
                    @if($upcomingSchedules->count())
                    <div class="table-responsive">
                        <table class="table mb-0">
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
                                    <td>
                                        <span class="font-weight-bold" style="font-size:.83rem;">{{ $schedule->course->course_name }}</span>
                                    </td>
                                    <td style="font-size:.83rem;">{{ $schedule->instructor->instructor_name }}</td>
                                    <td>
                                        <span class="badge badge-primary" style="font-size:.72rem;">
                                            {{ $schedule->date->format('M d') }}
                                        </span>
                                    </td>
                                    <td style="font-size:.8rem; color:#64748b;">
                                        {{ $schedule->start_time->format('H:i') }}–{{ $schedule->end_time->format('H:i') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-calendar-times fa-2x mb-2 d-block" style="opacity:.3;"></i>
                            No upcoming schedules
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Student Enrollment Chart
    new Chart(document.getElementById('studentEnrollmentChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Student Enrollments',
                data: [12, 19, 3, 5, 2, 3],
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,.08)',
                tension: .4,
                fill: true,
                pointBackgroundColor: '#2563eb',
                pointRadius: 5,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true, position: 'top' } },
            scales: {
                y: { grid: { color: 'rgba(0,0,0,.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Course Distribution Chart
    new Chart(document.getElementById('courseDistributionChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Theory', 'Practical', 'Advanced'],
            datasets: [{
                data: [300, 50, 100],
                backgroundColor: ['#2563eb', '#10b981', '#f59e0b'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>
@endsection
