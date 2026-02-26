{{-- @extends('layouts.master')
@section('content')

<!-- Begin Page Content -->
<div class="container-fluid px-4">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h1 class="h2 text-primary fw-bold">
            <i class="fas fa-calendar-alt me-2"></i> Course Schedules
        </h1>
        <div>
            <button class="btn btn-info btn-sm me-2 rounded-pill" data-toggle="modal" data-target="#copyScheduleModal">
                <i class="fas fa-copy me-1"></i> Copy Month Schedule
            </button>
            <a href="{{ route('course-schedules.create') }}" class="btn btn-primary btn-sm rounded-pill">
                <i class="fas fa-plus me-1"></i> Add New Schedule
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- DataTales Example -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold">
                <i class="fas fa-list me-2"></i> Course Schedule List
            </h5>
        </div>

        <div class="card-body p-4">
            <!-- Month Filter -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-calendar text-primary"></i></span>
                        <input type="month" id="monthFilter" class="form-control"
                               value="{{ request('month', now()->format('Y-m')) }}">
                        <button class="btn btn-primary" id="filterButton">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Course</th>
                            <th>Instructor</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Session Type</th>
                            <th>Max Students</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $schedule)
                            <tr>
                                <td>{{ $schedule->id }}</td>
                                <td>{{ $schedule->course->course_name }}</td>
                                <td>{{ $schedule->instructor->instructor_name }}</td>
                                <td>{{ $schedule->date->format('d M, Y') }}</td>
                                <td>
                                    {{ $schedule->start_time->format('h:i A') }} -
                                    {{ $schedule->end_time->format('h:i A') }}
                                </td>
                                <td>
                                    <span class="badge {{
                                        $schedule->session_type === 'theory' ? 'bg-primary' :
                                        ($schedule->session_type === 'practical' ? 'bg-info' : 'bg-secondary')
                                    }}">
                                        {{ ucfirst($schedule->session_type) }}
                                    </span>
                                </td>
                                <td>{{ $schedule->max_students }}</td>
                                <td>
                                    @if($schedule->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <form action="{{ route('course-schedules.toggle-status', $schedule) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm"
                                                    title="{{ $schedule->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas fa-toggle-{{ $schedule->is_active ? 'on' : 'off' }}"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('course-schedules.edit', $schedule->id) }}"
                                           class="btn btn-primary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('course-schedules.destroy', $schedule) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this schedule? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Copy Schedule Modal -->
<div class="modal fade" id="copyScheduleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-copy text-primary me-2"></i>Copy Month Schedule
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('course-schedules.copy-month') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="sourceMonth" class="form-label">
                            <i class="fas fa-calendar text-primary me-2"></i>Source Month
                        </label>
                        <input type="month" class="form-control" id="sourceMonth" name="month"
                               value="{{ now()->format('Y-m') }}" required>
                        <small class="form-text text-muted">
                            Select the month whose schedule you want to copy
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Copy Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable with custom settings
    $('#dataTable').DataTable({
        order: [[3, 'asc'], [4, 'asc']], // Sort by date, then time
        pageLength: 25, // Show 25 entries per page
        columnDefs: [
            { orderable: false, targets: [8] } // Disable sorting for action column
        ]
    });

    // Auto close alerts after 5 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);

    // Month filter functionality
    function filterByMonth() {
        const month = $('#monthFilter').val();
        console.log(month);

        if (month) {
            const url = new URL(window.location.href);
            console.log(url);
            url.searchParams.set('month', month);
            window.location.href = url.toString();
        }
    }

    // Button click handler
    $('#filterButton').click(function(e) {
        e.preventDefault();
        filterByMonth();
        console.log("filter button clicked");
    });

    // Enter key handler
    $('#monthFilter').keypress(function(e) {
        if (e.which == 13) { // Enter key
            e.preventDefault();
            filterByMonth();
        }
    });

    // Confirm status toggle
    $('form[action*="toggle-status"]').on('submit', function(e) {
        e.preventDefault();
        const isCurrentlyActive = $(this).find('i').hasClass('fa-toggle-on');
        const action = isCurrentlyActive ? 'deactivate' : 'activate';

        if (confirm(`Are you sure you want to ${action} this schedule?`)) {
            this.submit();
        }
    });
});
</script>
@endsection --}}


@extends('layouts.master')
@section('content')

<!-- Begin Page Content -->
<div class="container-fluid px-4">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <h1 class="h2 text-primary fw-bold">
                <i class="fas fa-calendar-alt me-2"></i> Course Schedules
            </h1>
            <small class="text-muted">
                <i class="fas fa-clock me-1"></i> Oklahoma Time (Central Time Zone)
            </small>
        </div>
        <div>
            <button class="btn btn-info btn-sm me-2 rounded-pill" data-toggle="modal" data-target="#copyScheduleModal">
                <i class="fas fa-copy me-1"></i> Copy Month Schedule
            </button>
            <a href="{{ route('course-schedules.create') }}" class="btn btn-primary btn-sm rounded-pill">
                <i class="fas fa-plus me-1"></i> Add New Schedule
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Error:</strong>
            @if($errors->has('conflicts'))
                <ul class="mb-0">
                    @foreach($errors->get('conflicts') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @else
                {{ $errors->first() }}
            @endif
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- DataTales Example -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold">
                <i class="fas fa-list me-2"></i> Course Schedule List
            </h5>
            <div class="text-white-50">
                <small><i class="fas fa-map-marker-alt me-1"></i> All times shown in Oklahoma Time</small>
            </div>
        </div>

        <div class="card-body p-4">
            <!-- Month Filter -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-calendar text-primary"></i></span>
                        <input type="month" id="monthFilter" class="form-control"
                               value="{{ request('month', now()->setTimezone('America/Chicago')->format('Y-m')) }}">
                        <button class="btn btn-primary" id="filterButton">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                </div>
                <div class="col-md-8 d-flex align-items-center">
                    <div class="bg-light rounded p-2">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Current Oklahoma Time: <span id="currentOklahomaTime">{{ now()->setTimezone('America/Chicago')->format('M j, Y g:i A T') }}</span>
                        </small>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Course</th>
                            <th>Instructor</th>
                            <th>Date (OK Time)</th>
                            <th>Time (OK Time)</th>
                            <th>Session Type</th>
                            <th>Max Students</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $schedule)
                            <tr class="{{ \Carbon\Carbon::parse($schedule->date)->isPast() ? 'table-secondary' : '' }}">
                                <td>{{ $schedule->id }}</td>
                                <td>{{ $schedule->course->course_name }}</td>
                                <td>{{ $schedule->instructor->instructor_name }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($schedule->date)->setTimezone('America/Chicago')->format('D, M j, Y') }}
                                    @if(\Carbon\Carbon::parse($schedule->date)->isPast())
                                        <span class="badge bg-info ms-1">Past</span>
                                    @elseif(\Carbon\Carbon::parse($schedule->date)->isToday())
                                        <span class="badge bg-warning ms-1">Today</span>
                                    @elseif(\Carbon\Carbon::parse($schedule->date)->isTomorrow())
                                        <span class="badge bg-info ms-1">Tomorrow</span>
                                    @endif
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }} -
                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
                                    <br>
                                    <small class="text-muted">
                                        Duration: {{ \Carbon\Carbon::parse($schedule->start_time)->diffForHumans(\Carbon\Carbon::parse($schedule->end_time), true) }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge {{
                                        $schedule->session_type === 'theory' ? 'bg-primary' :
                                        ($schedule->session_type === 'practical' ? 'bg-info' : 'bg-secondary')
                                    }}">
                                        {{ ucfirst($schedule->session_type) }}
                                    </span>
                                </td>
                                <td>{{ $schedule->max_students }}</td>
                                <td>
                                    @if($schedule->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <form action="{{ route('course-schedules.toggle-status', $schedule) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm"
                                                    title="{{ $schedule->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas fa-toggle-{{ $schedule->is_active ? 'on' : 'off' }}"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('course-schedules.edit', $schedule->id) }}"
                                           class="btn btn-primary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('course-schedules.destroy', $schedule) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this schedule? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Copy Schedule Modal -->
<div class="modal fade" id="copyScheduleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-copy text-primary me-2"></i>Copy Month Schedule
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('course-schedules.copy-month') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="sourceMonth" class="form-label">
                            <i class="fas fa-calendar text-primary me-2"></i>Source Month
                        </label>
                        <input type="month" class="form-control" id="sourceMonth" name="month"
                               value="{{ now()->setTimezone('America/Chicago')->format('Y-m') }}" required>
                        <small class="form-text text-muted">
                            Select the month whose schedule you want to copy to the next month (Oklahoma Time)
                        </small>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Schedules will be copied to the next month. Only schedules that don't conflict with existing ones will be created.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Copy Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Update current Oklahoma time every minute
    function updateOklahomaTime() {
        const now = new Date();
        const options = {
            timeZone: 'America/Chicago',
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            timeZoneName: 'short'
        };
        const formatter = new Intl.DateTimeFormat('en-US', options);
        document.getElementById('currentOklahomaTime').textContent = formatter.format(now);
    }

    // Update time immediately and then every minute
    updateOklahomaTime();
    setInterval(updateOklahomaTime, 60000);

    // Initialize DataTable with custom settings
    $('#dataTable').DataTable({
        order: [[3, 'asc'], [4, 'asc']], // Sort by date, then time
        pageLength: 25, // Show 25 entries per page
        columnDefs: [
            { orderable: false, targets: [8] } // Disable sorting for action column
        ]
    });

    // Auto close alerts after 5 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);

    // Month filter functionality
    function filterByMonth() {
        const month = $('#monthFilter').val();
        console.log(month);

        if (month) {
            const url = new URL(window.location.href);
            console.log(url);
            url.searchParams.set('month', month);
            window.location.href = url.toString();
        }
    }

    // Button click handler
    $('#filterButton').click(function(e) {
        e.preventDefault();
        filterByMonth();
        console.log("filter button clicked");
    });

    // Enter key handler
    $('#monthFilter').keypress(function(e) {
        if (e.which == 13) { // Enter key
            e.preventDefault();
            filterByMonth();
        }
    });

    // Confirm status toggle
    $('form[action*="toggle-status"]').on('submit', function(e) {
        e.preventDefault();
        const isCurrentlyActive = $(this).find('i').hasClass('fa-toggle-on');
        const action = isCurrentlyActive ? 'deactivate' : 'activate';

        if (confirm(`Are you sure you want to ${action} this schedule?`)) {
            this.submit();
        }
    });

    // Set minimum date for copy schedule modal to current Oklahoma date
    const today = new Date();
    const oklahomaDate = new Intl.DateTimeFormat('en-CA', {
        timeZone: 'America/Chicago'
    }).format(today);
    const yearMonth = oklahomaDate.substring(0, 7);
    $('#sourceMonth').attr('max', yearMonth);
});
</script>
@endsection
