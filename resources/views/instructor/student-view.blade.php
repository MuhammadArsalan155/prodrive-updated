@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="pd-page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="font-weight:800;"><i class="fas fa-user-graduate mr-2"></i>Student Profile</h4>
            <p style="font-size:.85rem;">{{ $student->first_name }} {{ $student->last_name }} &mdash; detailed course progress</p>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-light btn-sm font-weight-bold">
            <i class="fas fa-arrow-left mr-1"></i>Back
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="row">

        <!-- ===================== LEFT COLUMN: Student Info ===================== -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-id-card mr-2"></i>Student Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($student->profile_photo)
                            <img class="img-profile rounded-circle" style="width:120px;height:120px;object-fit:cover;"
                                src="{{ asset('profile/' . $student->profile_photo) }}">
                        @else
                            <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle"
                                style="width:120px;height:120px;background:var(--pd-navy);font-size:2.2rem;font-weight:700;color:#fff;">
                                {{ strtoupper(substr($student->first_name,0,1)) }}{{ strtoupper(substr($student->last_name,0,1)) }}
                            </div>
                        @endif
                        <h5 class="mt-3 mb-1 font-weight-bold">{{ $student->first_name }} {{ $student->last_name }}</h5>
                        <span class="badge badge-pill
                            @if($student->theory_status == 'completed' && $student->practical_status == 'completed') badge-success
                            @elseif($student->theory_status == 'completed') badge-primary
                            @else badge-warning @endif">
                            @if($student->theory_status == 'completed' && $student->practical_status == 'completed')
                                Course Completed
                            @elseif($student->theory_status == 'completed')
                                Pending Practical
                            @else
                                Pending Theory
                            @endif
                        </span>
                    </div>

                    @php
                        $fields = [
                            ['icon'=>'fas fa-hashtag','label'=>'Student ID','value'=> $student->student_id ?? 'N/A'],
                            ['icon'=>'fas fa-envelope','label'=>'Email','value'=> $student->email],
                            ['icon'=>'fas fa-phone','label'=>'Contact','value'=> $student->student_contact ?? 'N/A'],
                            ['icon'=>'fas fa-birthday-cake','label'=>'DOB','value'=> $student->student_dob ? \Carbon\Carbon::parse($student->student_dob)->format('M d, Y') : 'N/A'],
                            ['icon'=>'fas fa-map-marker-alt','label'=>'Address','value'=> $student->address ?? 'N/A'],
                            ['icon'=>'fas fa-calendar-plus','label'=>'Joined','value'=> $student->joining_date ? \Carbon\Carbon::parse($student->joining_date)->format('M d, Y') : 'N/A'],
                        ];
                    @endphp

                    @foreach($fields as $f)
                        <div class="d-flex align-items-start py-2 border-bottom">
                            <i class="{{ $f['icon'] }} text-primary mt-1 mr-2" style="width:16px;"></i>
                            <div>
                                <small class="text-muted d-block" style="font-size:.72rem;">{{ $f['label'] }}</small>
                                <span style="font-size:.88rem;">{{ $f['value'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ===================== RIGHT COLUMN: Course & Sessions ===================== -->
        <div class="col-lg-8">

            <!-- Course Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-book mr-2"></i>Course Information</h6>
                </div>
                <div class="card-body">
                    @php $sc = $student->course; @endphp
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Course:</strong> {{ $sc ? $sc->course_name : 'N/A' }}</p>
                            <p class="mb-1"><strong>Type:</strong> {{ $sc ? ucfirst($sc->course_type ?? 'N/A') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1">
                                <strong>Theory:</strong>
                                {{ $sc ? ($sc->theory_hours ?? 0) : 0 }} hrs &nbsp;|&nbsp;
                                {{ $sc ? ($sc->total_theory_classes ?? 0) : 0 }} classes
                            </p>
                            <p class="mb-1">
                                <strong>Practical:</strong>
                                {{ $sc ? ($sc->practical_hours ?? 0) : 0 }} hrs &nbsp;|&nbsp;
                                {{ $sc ? ($sc->total_practical_classes ?? 0) : 0 }} classes
                            </p>
                        </div>
                    </div>
                    <hr>

                    <!-- Progress Bars -->
                    <div class="mb-2">
                        @php
                            $tTotal = $sc ? ($sc->theory_hours   ?? 1) : 1;
                            $tDone  = $student->hours_theory ?? 0;
                            $tPct   = min(100, round(($tDone / max($tTotal, 1)) * 100));
                            $pTotal = $sc ? ($sc->practical_hours ?? 1) : 1;
                            $pDone  = $student->hours_practical ?? 0;
                            $pPct   = min(100, round(($pDone / max($pTotal, 1)) * 100));
                        @endphp
                        {{-- <small class="text-muted">Theory Hours: {{ $tDone }}/{{ $tTotal }}</small>
                        <div class="progress mb-2" style="height:10px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width:{{ $tPct }}%"></div>
                        </div>
                        <small class="text-muted">Practical Hours: {{ $pDone }}/{{ $pTotal }}</small>
                        <div class="progress mb-3" style="height:10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width:{{ $pPct }}%"></div>
                        </div> --}}

                        {{-- Class Count Progress --}}
                        @if(isset($classProgress))
                        @php
                            $tcReq  = $classProgress['theory']['required'];
                            $tcDone = $classProgress['theory']['completed'];
                            $tcPct  = $tcReq > 0 ? min(100, round(($tcDone/$tcReq)*100)) : 0;
                            $pcReq  = $classProgress['practical']['required'];
                            $pcDone = $classProgress['practical']['completed'];
                            $pcPct  = $pcReq > 0 ? min(100, round(($pcDone/$pcReq)*100)) : 0;
                        @endphp
                        @if($tcReq > 0)
                        <small class="text-muted">
                            Theory Classes: <strong>{{ $tcDone }}/{{ $tcReq }}</strong>
                            @if($classProgress['theory']['pending_assigned'] > 0)
                                &nbsp;<span class="badge badge-light text-muted" style="font-size:.7rem;">+{{ $classProgress['theory']['pending_assigned'] }} upcoming</span>
                            @endif
                        </small>
                        <div class="progress mb-2" style="height:10px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width:{{ $tcPct }}%" title="{{ $tcDone }}/{{ $tcReq }} theory classes done"></div>
                        </div>
                        @endif
                        @if($pcReq > 0)
                        <small class="text-muted">
                            Practical Classes: <strong>{{ $pcDone }}/{{ $pcReq }}</strong>
                            @if($classProgress['practical']['pending_assigned'] > 0)
                                &nbsp;<span class="badge badge-light text-muted" style="font-size:.7rem;">+{{ $classProgress['practical']['pending_assigned'] }} upcoming</span>
                            @endif
                        </small>
                        <div class="progress" style="height:10px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width:{{ $pcPct }}%" title="{{ $pcDone }}/{{ $pcReq }} practical classes done"></div>
                        </div>
                        @endif
                        @endif
                    </div>
                    <hr>

                    <!-- Theory Status Row -->
                    @php
                        // "Mark Theory Complete" button only shown when:
                        // - status is not yet completed, AND
                        // - either no class count requirement OR all required classes are done
                        $theoryClassesDone = isset($classProgress) ? $classProgress['theory']['completed'] : 0;
                        $theoryRequired    = isset($classProgress) ? $classProgress['theory']['required']  : 0;
                        $allTheoryDone     = $theoryRequired === 0 || $theoryClassesDone >= $theoryRequired;
                        $showMarkTheoryBtn = in_array($student->theory_status, ['pending','in_progress']) && $allTheoryDone;

                        // "Assign Practical Sessions" (old modal) only for courses without class count requirements
                        $useNewPracticalFlow = isset($classProgress) && $classProgress['practical']['required'] > 0;
                    @endphp
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div>
                            <strong>Theory Status:</strong>
                            <span class="badge badge-pill ml-1
                                @if($student->theory_status=='completed') badge-success
                                @elseif($student->theory_status=='in_progress') badge-info
                                @else badge-warning @endif">
                                {{ ucfirst(str_replace('_',' ',$student->theory_status)) }}
                            </span>
                            @if($student->theory_status=='completed' && $student->theory_completion_date)
                                <small class="text-muted ml-2">Completed: {{ $student->theory_completion_date->format('M d, Y') }}</small>
                            @endif
                            @if($theoryRequired > 0 && !$allTheoryDone)
                                <small class="text-muted ml-2">({{ $theoryClassesDone }}/{{ $theoryRequired }} classes done)</small>
                            @endif
                        </div>
                        @if($showMarkTheoryBtn)
                            <button class="btn btn-sm btn-success mt-1 mt-md-0" data-toggle="modal" data-target="#markTheoryCompleteModal">
                                <i class="fas fa-check-circle mr-1"></i>Mark Theory Complete
                            </button>
                        @endif
                    </div>
                    <hr>

                    <!-- Practical Status Row -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div>
                            <strong>Practical Status:</strong>
                            <span class="badge badge-pill ml-1
                                @if($student->practical_status=='completed') badge-success
                                @elseif($student->practical_status=='assigned') badge-info
                                @elseif($student->practical_status=='failed') badge-danger
                                @elseif($student->practical_status=='not_appeared') badge-secondary
                                @else badge-warning @endif">
                                {{ ucfirst(str_replace('_',' ',$student->practical_status)) }}
                            </span>
                            @if($student->practical_status=='completed' && $student->practical_completion_date)
                                <small class="text-muted ml-2">Completed: {{ $student->practical_completion_date->format('M d, Y') }}</small>
                            @endif
                        </div>
                        {{-- Old "Assign Practical Sessions" modal only for courses without class count requirements --}}
                        @if(!$useNewPracticalFlow && $student->theory_status == 'completed' && in_array($student->practical_status, ['pending','assigned']))
                            <button class="btn btn-sm btn-primary mt-1 mt-md-0" data-toggle="modal" data-target="#assignPracticalModal">
                                <i class="fas fa-calendar-plus mr-1"></i>Assign Practical Sessions
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ===================== Schedule Future Session ===================== -->
            @php
                $canScheduleTheory    = in_array($student->theory_status, ['pending','in_progress']);
                $canSchedulePractical = $student->theory_status === 'completed' && in_array($student->practical_status, ['pending','assigned']);
                $showScheduleCard     = $canScheduleTheory || $canSchedulePractical;
            @endphp
            @if($showScheduleCard)
            <div class="card shadow mb-4 border-left-success">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-calendar-plus mr-2"></i>Schedule Upcoming Session</h6>
                    <span class="badge badge-success">New Session</span>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Create a scheduled session for <strong>{{ $student->first_name }}</strong>. It will appear in their Upcoming Classes tab.</p>
                    <form action="{{ route('instructor.student.create.session', $student->id) }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="small font-weight-bold">Session Type <span class="text-danger">*</span></label>
                                <select name="session_type" class="form-control form-control-sm" required>
                                    @if($canScheduleTheory)
                                        <option value="theory" selected>Theory</option>
                                    @endif
                                    @if($canSchedulePractical)
                                        <option value="practical" {{ !$canScheduleTheory ? 'selected' : '' }}>Practical</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="small font-weight-bold">Date <span class="text-danger">*</span></label>
                                <input type="date" name="session_date" class="form-control form-control-sm"
                                    value="{{ now()->addDay()->toDateString() }}" min="{{ now()->toDateString() }}" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="small font-weight-bold">Start Time <span class="text-danger">*</span></label>
                                <input type="time" name="start_time" class="form-control form-control-sm"
                                    value="{{ now()->format('H:i') }}" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="small font-weight-bold">End Time <span class="text-danger">*</span></label>
                                <input type="time" name="end_time" class="form-control form-control-sm"
                                    value="{{ now()->addHour()->format('H:i') }}" required>
                            </div>
                            <div class="form-group col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="fas fa-calendar-check mr-1"></i>Schedule
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            {{-- ===================== Log New Session (commented out — not needed) =====================
            @php
                $canLogTheory    = in_array($student->theory_status, ['pending','in_progress']);
                $canLogPractical = $student->theory_status === 'completed' && in_array($student->practical_status, ['pending','assigned']);
                $showLogCard     = $canLogTheory || $canLogPractical;
            @endphp
            @if($showLogCard)
            <div class="card shadow mb-4 border-left-primary">...</div>
            @endif
            ===================== --}}

            <!-- ===================== Theory Sessions (per-class mark complete) ===================== -->
            @if(isset($theorySchedules) && $theorySchedules->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-chalkboard-teacher mr-2"></i>Theory Sessions</h6>
                    <span class="badge badge-info">{{ $theorySchedules->count() }} scheduled</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Class&nbsp;Order</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($theorySchedules as $i => $sch)
                                @php
                                    $att = $sessionAttendances[$sch->id] ?? null;
                                    $done = $att && $att->status === 'completed';
                                @endphp
                                <tr class="{{ $done ? 'table-success' : '' }}">
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $sch->date->format('M d, Y') }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($sch->start_time)->format('h:i A') }}
                                        &ndash;
                                        {{ \Carbon\Carbon::parse($sch->end_time)->format('h:i A') }}
                                    </td>
                                    <td>
                                        @if($att)
                                            <span class="badge badge-secondary">Class {{ $att->class_order }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($done)
                                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Completed</span>
                                            @if($att->completed_at)
                                                <small class="text-muted d-block">{{ $att->completed_at->format('M d, Y') }}</small>
                                            @endif
                                        @elseif($sch->date->isFuture())
                                            <span class="badge badge-light text-muted">Upcoming</span>
                                        @else
                                            <span class="badge badge-warning">Pending Mark</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$done)
                                            <button class="btn btn-sm btn-outline-success"
                                                data-toggle="modal"
                                                data-target="#markClassModal{{ $sch->id }}">
                                                <i class="fas fa-check-circle mr-1"></i>Mark Done
                                            </button>
                                        @else
                                            <span class="text-success" style="font-size:.8rem;"><i class="fas fa-check"></i> Done</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-3 py-2">
                        <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>
                            Marking a session as done creates an attendance record and unlocks feedback for the student.
                        </small>
                    </div>
                </div>
            </div>
            @endif

            <!-- ===================== Practical Sessions (assigned — mark complete) ===================== -->
            @if(isset($practicalSchedules) && $practicalSchedules->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-warning"><i class="fas fa-car mr-2"></i>Practical Sessions</h6>
                    <span class="badge badge-warning">{{ $practicalSchedules->count() }} scheduled</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Class&nbsp;Order</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($practicalSchedules as $i => $sch)
                                @php
                                    $att  = $sessionAttendances[$sch->id] ?? null;
                                    $done = $att && $att->status === 'completed';
                                @endphp
                                <tr class="{{ $done ? 'table-success' : '' }}">
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $sch->date->format('M d, Y') }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($sch->start_time)->format('h:i A') }}
                                        &ndash;
                                        {{ \Carbon\Carbon::parse($sch->end_time)->format('h:i A') }}
                                    </td>
                                    <td>
                                        @if($att)
                                            <span class="badge badge-secondary">Class {{ $att->class_order }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($done)
                                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Completed</span>
                                            @if($att->completed_at)
                                                <small class="text-muted d-block">{{ $att->completed_at->format('M d, Y') }}</small>
                                            @endif
                                        @elseif($sch->date->isFuture())
                                            <span class="badge badge-light text-muted">Upcoming</span>
                                        @else
                                            <span class="badge badge-warning">Pending Mark</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$done)
                                            <button class="btn btn-sm btn-outline-success"
                                                data-toggle="modal"
                                                data-target="#markPracticalModal{{ $sch->id }}">
                                                <i class="fas fa-check-circle mr-1"></i>Mark Done
                                            </button>
                                        @else
                                            <span class="text-success" style="font-size:.8rem;"><i class="fas fa-check"></i> Done</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-3 py-2">
                        <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>
                            Marking a session as done creates an attendance record and unlocks feedback for the student.
                        </small>
                    </div>
                </div>
            </div>
            @endif

            <!-- ===================== Practical Sessions List ===================== -->
            @if($practicalSessions->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-car mr-2"></i>Practical Sessions</h6>
                    <span class="badge badge-info">{{ $practicalSessions->count() }} session(s)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($practicalSessions as $sess)
                                <tr>
                                    <td>{{ $sess->session_number }}</td>
                                    <td>{{ $sess->date ? $sess->date->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        {{ $sess->start_time ? \Carbon\Carbon::parse($sess->start_time)->format('h:i A') : 'N/A' }}
                                        &ndash;
                                        {{ $sess->end_time ? \Carbon\Carbon::parse($sess->end_time)->format('h:i A') : 'N/A' }}
                                    </td>
                                    <td>{{ $sess->duration_hours }} hr</td>
                                    <td>
                                        <span class="badge badge-pill
                                            @if($sess->status=='completed') badge-success
                                            @elseif($sess->status=='scheduled') badge-info
                                            @elseif($sess->status=='failed') badge-danger
                                            @elseif($sess->status=='not_appeared') badge-secondary
                                            @else badge-warning @endif">
                                            {{ ucfirst(str_replace('_',' ',$sess->status)) }}
                                        </span>
                                    </td>
                                    <td style="max-width:180px;">
                                        <small>{{ $sess->instructor_notes ?? '—' }}</small>
                                    </td>
                                    <td>
                                        @if($sess->status == 'scheduled')
                                            <button class="btn btn-sm btn-outline-primary"
                                                data-toggle="modal"
                                                data-target="#sessionFeedbackModal{{ $sess->id }}">
                                                <i class="fas fa-edit"></i> Feedback
                                            </button>
                                        @else
                                            <span class="text-muted" style="font-size:.8rem;">Done</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- ===================== Assign Next Classes ===================== -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-calendar-plus mr-2"></i>Assign Next Classes
                    </h6>
                    <span class="badge badge-success">{{ $assignedScheduleIds->count() }} assigned</span>
                </div>
                <div class="card-body">

                    {{-- Workflow Guide --}}
                    @if(isset($classProgress) && ($classProgress['theory']['required'] > 0 || $classProgress['practical']['required'] > 0))
                    <div class="mb-3 p-2 rounded" style="background:#f8f9fa;border-left:4px solid var(--pd-blue);">
                        <small class="font-weight-bold text-primary d-block mb-1"><i class="fas fa-route mr-1"></i>How to assign classes:</small>
                        <div class="d-flex flex-wrap" style="gap:.3rem;font-size:.8rem;">
                            <span class="badge {{ $classProgress['theory']['completed'] > 0 ? 'badge-primary' : 'badge-secondary' }}">
                                <i class="fas fa-{{ $classProgress['theory']['completed'] >= $classProgress['theory']['required'] && $classProgress['theory']['required'] > 0 ? 'check' : 'book' }} mr-1"></i>
                                1. Mark each theory class done below
                            </span>
                            <i class="fas fa-arrow-right text-muted mt-1"></i>
                            <span class="badge badge-secondary">2. Select &amp; assign next class from table below</span>
                            @if($classProgress['practical']['required'] > 0)
                            <i class="fas fa-arrow-right text-muted mt-1"></i>
                            <span class="badge {{ $student->theory_status === 'completed' ? 'badge-success' : 'badge-secondary' }}">
                                <i class="fas fa-{{ $student->theory_status === 'completed' ? 'check' : 'car' }} mr-1"></i>
                                3. After theory done → assign practical
                            </span>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Class requirement summary --}}
                    @if(isset($classProgress) && ($classProgress['theory']['required'] > 0 || $classProgress['practical']['required'] > 0))
                    <div class="row mb-3">
                        @if($classProgress['theory']['required'] > 0)
                        <div class="col-6">
                            <div class="p-2 rounded border text-center" style="background:#f0f8ff;">
                                <div class="font-weight-bold text-primary" style="font-size:1.1rem;">
                                    {{ $classProgress['theory']['completed'] }}<span class="text-muted">/{{ $classProgress['theory']['required'] }}</span>
                                </div>
                                <small class="text-muted">Theory Classes Done</small>
                                @if($classProgress['theory']['pending_assigned'] > 0)
                                    <br><small class="text-info">+{{ $classProgress['theory']['pending_assigned'] }} upcoming</small>
                                @endif
                                @if($classProgress['theory']['remaining_to_assign'] > 0)
                                    <br><small class="text-success font-weight-bold">{{ $classProgress['theory']['remaining_to_assign'] }} slot(s) to fill</small>
                                @else
                                    <br><small class="text-success"><i class="fas fa-check mr-1"></i>All assigned</small>
                                @endif
                            </div>
                        </div>
                        @endif
                        @if($classProgress['practical']['required'] > 0)
                        <div class="col-6">
                            <div class="p-2 rounded border text-center" style="background:#f0fff0;">
                                <div class="font-weight-bold text-success" style="font-size:1.1rem;">
                                    {{ $classProgress['practical']['completed'] }}<span class="text-muted">/{{ $classProgress['practical']['required'] }}</span>
                                </div>
                                <small class="text-muted">Practical Classes Done</small>
                                @if($classProgress['practical']['pending_assigned'] > 0)
                                    <br><small class="text-info">+{{ $classProgress['practical']['pending_assigned'] }} upcoming</small>
                                @endif
                                @if($classProgress['practical']['remaining_to_assign'] > 0)
                                    <br><small class="text-success font-weight-bold">{{ $classProgress['practical']['remaining_to_assign'] }} slot(s) to fill</small>
                                @else
                                    <br><small class="text-success"><i class="fas fa-check mr-1"></i>All assigned</small>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($availableSchedules->isEmpty())
                        @php
                            $theoryFull    = isset($classProgress) && $classProgress['theory']['required'] > 0    && $classProgress['theory']['remaining_to_assign'] <= 0;
                            $practicalFull = isset($classProgress) && $classProgress['practical']['required'] > 0 && $classProgress['practical']['remaining_to_assign'] <= 0;
                            $allFull       = isset($classProgress)
                                && ($classProgress['theory']['required'] == 0    || $theoryFull)
                                && ($classProgress['practical']['required'] == 0 || $practicalFull);
                        @endphp
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-calendar-check fa-2x mb-2 d-block {{ $allFull ? 'text-success' : '' }}"></i>
                            @if($allFull)
                                <span class="text-success font-weight-bold">All required classes have been assigned!</span>
                            @else
                                No upcoming schedules available to assign.
                                <small class="d-block">Create new course schedules to assign them here.</small>
                            @endif
                        </div>
                    @else
                        <form action="{{ route('instructor.student.assign.schedules', $student->id) }}" method="POST">
                            @csrf
                            <p class="text-muted small mb-3">Select one or more upcoming sessions to assign to <strong>{{ $student->first_name }}</strong>:</p>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="40"><input type="checkbox" id="selectAllSchedules"></th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($availableSchedules as $sch)
                                        <tr>
                                            <td><input type="checkbox" name="schedule_ids[]" value="{{ $sch->id }}" class="schedule-checkbox"></td>
                                            <td>
                                                <strong>{{ \Carbon\Carbon::parse($sch->date)->format('M d, Y') }}</strong>
                                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($sch->date)->format('l') }}</small>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($sch->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($sch->end_time)->format('h:i A') }}</td>
                                            <td><span class="badge badge-{{ $sch->session_type == 'theory' ? 'info' : 'warning' }}">{{ ucfirst($sch->session_type) }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-plus mr-1"></i>Assign Selected Classes
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- ===================== Reschedule Requests ===================== -->
            @if(isset($rescheduleRequests) && $rescheduleRequests->count() > 0)
            <div class="card shadow mb-4 border-left-warning">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-exchange-alt mr-2"></i>Reschedule Requests
                    </h6>
                    <span class="badge badge-warning">{{ $rescheduleRequests->count() }} pending</span>
                </div>
                <div class="card-body p-0">
                    @foreach($rescheduleRequests as $rr)
                    <div class="p-3 border-bottom">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <small class="text-muted text-uppercase font-weight-bold">Current Session</small>
                                <div class="font-weight-bold">
                                    {{ ucfirst($rr->schedule->session_type) }} &mdash;
                                    {{ \Carbon\Carbon::parse($rr->schedule->date)->format('M d, Y') }}
                                </div>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($rr->schedule->start_time)->format('h:i A') }}
                                    &ndash;
                                    {{ \Carbon\Carbon::parse($rr->schedule->end_time)->format('h:i A') }}
                                </small>
                            </div>
                            <div class="col-md-1 text-center">
                                <i class="fas fa-arrow-right text-warning"></i>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted text-uppercase font-weight-bold">Requested Time</small>
                                <div class="font-weight-bold text-warning">
                                    {{ \Carbon\Carbon::parse($rr->requested_date)->format('M d, Y') }}
                                </div>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($rr->requested_start_time)->format('h:i A') }}
                                    @if($rr->requested_end_time)
                                        &ndash; {{ \Carbon\Carbon::parse($rr->requested_end_time)->format('h:i A') }}
                                    @endif
                                </small>
                                @if($rr->reason)
                                    <br><small class="text-muted"><i class="fas fa-comment mr-1"></i>{{ $rr->reason }}</small>
                                @endif
                            </div>
                            <div class="col-md-2 text-right">
                                <button class="btn btn-sm btn-success mb-1 w-100"
                                    data-toggle="modal"
                                    data-target="#handleRescheduleModal{{ $rr->id }}"
                                    data-action="approve">
                                    <i class="fas fa-check mr-1"></i>Approve
                                </button>
                                <button class="btn btn-sm btn-outline-danger w-100"
                                    data-toggle="modal"
                                    data-target="#handleRescheduleModal{{ $rr->id }}"
                                    data-action="reject">
                                    <i class="fas fa-times mr-1"></i>Reject
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Handle Reschedule Modal -->
                    <div class="modal fade" id="handleRescheduleModal{{ $rr->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('instructor.reschedule.handle', $rr->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" id="rescheduleAction{{ $rr->id }}" value="approve">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="fas fa-exchange-alt mr-2"></i>Handle Reschedule Request
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-light border mb-3">
                                            <strong>{{ $student->first_name }}</strong> requests to move
                                            <strong>{{ ucfirst($rr->schedule->session_type) }}</strong> session from
                                            <strong>{{ \Carbon\Carbon::parse($rr->schedule->date)->format('M d, Y') }}</strong>
                                            to
                                            <strong>{{ \Carbon\Carbon::parse($rr->requested_date)->format('M d, Y') }}</strong>
                                            at <strong>{{ \Carbon\Carbon::parse($rr->requested_start_time)->format('h:i A') }}</strong>.
                                            @if($rr->reason)
                                                <br><em class="text-muted">Reason: {{ $rr->reason }}</em>
                                            @endif
                                        </div>
                                        <div class="form-group mb-0">
                                            <label class="small font-weight-bold">Note to student (optional)</label>
                                            <textarea name="instructor_note" class="form-control form-control-sm" rows="2"
                                                placeholder="Add a note for the student..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="close-modal btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                                        <button type="submit" id="rescheduleSubmitBtn{{ $rr->id }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-check mr-1"></i>Approve
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- ===================== Instructor Evaluation ===================== -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-star mr-2"></i>End-of-Course Evaluation
                    </h6>
                    @if(isset($evaluation) && $evaluation)
                        <span class="badge badge-success">Submitted</span>
                    @else
                        <span class="badge badge-warning">Pending</span>
                    @endif
                </div>
                <div class="card-body">
                    @if(isset($evaluation) && $evaluation)
                        {{-- Show submitted evaluation --}}
                        <div class="row mb-3">
                            @foreach([
                                ['Performance', $evaluation->performance_rating],
                                ['Behavior',    $evaluation->behavior_rating],
                                ['Attendance',  $evaluation->attendance_rating],
                                ['Overall',     $evaluation->overall_rating],
                            ] as [$label, $rating])
                            <div class="col-6 col-md-3 text-center mb-3">
                                <div style="font-size:1.6rem;font-weight:800;color:var(--pd-navy);">{{ $rating }}<span style="font-size:1rem;">/5</span></div>
                                <small class="text-muted">{{ $label }}</small>
                            </div>
                            @endforeach
                        </div>
                        @if($evaluation->performance_notes)
                            <p><strong>Performance Notes:</strong><br>{{ $evaluation->performance_notes }}</p>
                        @endif
                        @if($evaluation->behavior_notes)
                            <p><strong>Behavior Notes:</strong><br>{{ $evaluation->behavior_notes }}</p>
                        @endif
                        @if($evaluation->recommendations)
                            <p><strong>Recommendations:</strong><br>{{ $evaluation->recommendations }}</p>
                        @endif
                        <p>
                            <strong>Certificate Recommendation:</strong>
                            @if($evaluation->is_recommended_for_certificate)
                                <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Recommended</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Not Recommended</span>
                            @endif
                        </p>
                        <hr>
                        <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#evaluationModal">
                            <i class="fas fa-edit mr-1"></i>Update Evaluation
                        </button>
                    @else
                        <p class="text-muted">No evaluation submitted yet. Submit an evaluation after the student completes the course.</p>
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#evaluationModal">
                            <i class="fas fa-star mr-1"></i>Submit Evaluation
                        </button>
                    @endif
                </div>
            </div>

        </div><!-- /col-lg-8 -->
    </div><!-- /row -->
</div><!-- /container-fluid -->

<!-- =========================================================
     MODALS: Mark individual theory session as complete
========================================================= -->
@if(isset($theorySchedules))
@foreach($theorySchedules as $sch)
@php $att = $sessionAttendances[$sch->id] ?? null; @endphp
@if(!($att && $att->status === 'completed'))
<div class="modal fade" id="markClassModal{{ $sch->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--pd-navy);color:#fff;">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle mr-2"></i>Mark Theory Session as Complete
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('instructor.schedule.mark.complete', $sch->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>
                        Mark session on <strong>{{ $sch->date->format('M d, Y') }}</strong>
                        ({{ \Carbon\Carbon::parse($sch->start_time)->format('h:i A') }}
                        &ndash; {{ \Carbon\Carbon::parse($sch->end_time)->format('h:i A') }}) as completed.
                    </p>
                    <p class="text-muted" style="font-size:.85rem;">
                        <i class="fas fa-info-circle mr-1"></i>
                        This will create an attendance record for all enrolled students and unlock feedback for them.
                    </p>
                    <div class="form-group">
                        <label for="notes_{{ $sch->id }}">Session Notes <small class="text-muted">(optional)</small></label>
                        <textarea class="form-control" id="notes_{{ $sch->id }}" name="notes" rows="2"
                            placeholder="Any notes about this session..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check mr-1"></i>Confirm Complete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
@endif

<!-- =========================================================
     MODALS: Mark individual practical session as complete
========================================================= -->
@if(isset($practicalSchedules))
@foreach($practicalSchedules as $sch)
@php $att = $sessionAttendances[$sch->id] ?? null; @endphp
@if(!($att && $att->status === 'completed'))
<div class="modal fade" id="markPracticalModal{{ $sch->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--pd-teal);color:#fff;">
                <h5 class="modal-title">
                    <i class="fas fa-car mr-2"></i>Mark Practical Session as Complete
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('instructor.schedule.mark.complete', $sch->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>
                        Mark practical session on <strong>{{ $sch->date->format('M d, Y') }}</strong>
                        ({{ \Carbon\Carbon::parse($sch->start_time)->format('h:i A') }}
                        &ndash; {{ \Carbon\Carbon::parse($sch->end_time)->format('h:i A') }}) as completed
                        for <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>.
                    </p>
                    <div class="form-group">
                        <label for="practical_notes_{{ $sch->id }}">Session Notes <small class="text-muted">(optional)</small></label>
                        <textarea class="form-control" id="practical_notes_{{ $sch->id }}" name="notes" rows="2"
                            placeholder="Any notes about this session..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check mr-1"></i>Confirm Complete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
@endif

<!-- =========================================================
     MODAL: Instructor Evaluation
========================================================= -->
<div class="modal fade" id="evaluationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--pd-navy);color:#fff;">
                <h5 class="modal-title"><i class="fas fa-star mr-2"></i>Instructor Evaluation</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('instructor.student.evaluation', $student->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">Rate <strong>{{ $student->first_name }} {{ $student->last_name }}</strong> from 1 (Poor) to 5 (Excellent).</p>

                    <div class="row">
                        @foreach([
                            ['performance_rating', 'Performance', 'How well the student performed in sessions'],
                            ['behavior_rating',    'Behavior',    'Student conduct and attitude'],
                            ['attendance_rating',  'Attendance',  'Punctuality and session attendance'],
                            ['overall_rating',     'Overall',     'Overall evaluation rating'],
                        ] as [$name, $label, $hint])
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">{{ $label }} <span class="text-danger">*</span></label>
                            <small class="text-muted d-block mb-1">{{ $hint }}</small>
                            <div class="d-flex" style="gap:.5rem;">
                                @for($r = 1; $r <= 5; $r++)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio"
                                        name="{{ $name }}"
                                        id="{{ $name }}_{{ $r }}"
                                        value="{{ $r }}"
                                        {{ (isset($evaluation) && $evaluation && $evaluation->$name == $r) ? 'checked' : ($r == 3 ? 'checked' : '') }}
                                        required>
                                    <label class="form-check-label" for="{{ $name }}_{{ $r }}">{{ $r }}</label>
                                </div>
                                @endfor
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <hr>
                    <div class="form-group">
                        <label class="font-weight-bold">Performance Notes</label>
                        <textarea class="form-control" name="performance_notes" rows="2"
                            placeholder="Describe student's technical performance...">{{ isset($evaluation) && $evaluation ? $evaluation->performance_notes : '' }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Behavior Notes</label>
                        <textarea class="form-control" name="behavior_notes" rows="2"
                            placeholder="Describe student's behavior and attitude...">{{ isset($evaluation) && $evaluation ? $evaluation->behavior_notes : '' }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Recommendations</label>
                        <textarea class="form-control" name="recommendations" rows="2"
                            placeholder="Any recommendations for the student...">{{ isset($evaluation) && $evaluation ? $evaluation->recommendations : '' }}</textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="recommendCert" name="is_recommended_for_certificate"
                            value="1"
                            {{ (!isset($evaluation) || !$evaluation || $evaluation->is_recommended_for_certificate) ? 'checked' : '' }}>
                        <label class="form-check-label font-weight-bold" for="recommendCert">
                            <i class="fas fa-certificate mr-1 text-warning"></i>Recommend for Certificate
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>Submit Evaluation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================
     MODAL: Mark Theory Complete (bulk — legacy)
========================================================= -->
<div class="modal fade" id="markTheoryCompleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--pd-navy);color:#fff;">
                <h5 class="modal-title"><i class="fas fa-check-circle mr-2"></i>Mark Theory Complete</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('instructor.mark.theory.complete') }}" method="POST">
                @csrf
                <input type="hidden" name="student_ids[]" value="{{ $student->id }}">
                <div class="modal-body">
                    <p>Confirm that <strong>{{ $student->first_name }} {{ $student->last_name }}</strong> has completed all theory classes.</p>
                    <div class="form-group">
                        <label for="theoryHours">Theory Hours Completed</label>
                        <input type="number" class="form-control" id="theoryHours" name="theory_hours"
                            value="{{ $student->hours_theory ?? ($sc ? $sc->theory_hours : 0) }}"
                            min="0" max="{{ $sc ? $sc->theory_hours : 0 }}" step="0.5" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check mr-1"></i>Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================
     MODAL: Assign Practical Sessions
     Instructor enters total hours → JS splits into 2-hr sessions
     and renders date/time inputs for each.
========================================================= -->
<div class="modal fade" id="assignPracticalModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--pd-navy);color:#fff;">
                <h5 class="modal-title"><i class="fas fa-calendar-plus mr-2"></i>Assign Practical Sessions</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('instructor.assign.practical.sessions') }}" method="POST" id="assignPracticalForm">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">
                <div class="modal-body">
                    <p>
                        Practical total: <strong>{{ $sc ? ($sc->practical_hours ?? 0) : 0 }} hours</strong>
                        &nbsp;|&nbsp; Each session = <strong>2 hours</strong>
                    </p>

                    <div class="form-group">
                        <label for="totalHoursInput">Total Practical Hours to Assign</label>
                        <input type="number" class="form-control" id="totalHoursInput" name="total_hours"
                            min="2" max="{{ $sc ? ($sc->practical_hours ?? 20) : 20 }}" step="2"
                            value="{{ $sc ? ($sc->practical_hours ?? 6) : 6 }}"
                            placeholder="e.g. 6"
                            required>
                        <small class="form-text text-muted">Must be a multiple of 2. Example: 6 hours → 3 sessions of 2 hours each.</small>
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="generateSessionsBtn">
                        <i class="fas fa-magic mr-1"></i>Generate Session Slots
                    </button>

                    <div id="sessionSlotsContainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveSessionsBtn" disabled>
                        <i class="fas fa-save mr-1"></i>Save Sessions
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================
     MODALS: Per-Session Feedback (one per session)
========================================================= -->
@foreach($practicalSessions as $sess)
@if($sess->status == 'scheduled')
<div class="modal fade" id="sessionFeedbackModal{{ $sess->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--pd-navy);color:#fff;">
                <h5 class="modal-title">
                    <i class="fas fa-clipboard-check mr-2"></i>Session {{ $sess->session_number }} Feedback
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('instructor.practical.session.feedback', $sess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>
                        <strong>Date:</strong> {{ $sess->date ? $sess->date->format('M d, Y') : 'N/A' }}
                        &nbsp;&nbsp;
                        <strong>Time:</strong>
                        {{ $sess->start_time ? \Carbon\Carbon::parse($sess->start_time)->format('h:i A') : '' }}
                        &ndash;
                        {{ $sess->end_time ? \Carbon\Carbon::parse($sess->end_time)->format('h:i A') : '' }}
                    </p>
                    <div class="form-group">
                        <label class="font-weight-bold">Session Outcome</label>
                        <div class="d-flex flex-wrap" style="gap:.5rem;">
                            @foreach(['completed'=>['success','Completed'],'failed'=>['danger','Failed'],'not_appeared'=>['secondary','Did Not Appear'],'cancelled'=>['warning','Cancelled']] as $val=>[$cls,$lbl])
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status"
                                    id="s{{ $sess->id }}_{{ $val }}" value="{{ $val }}"
                                    {{ $val=='completed' ? 'checked' : '' }}>
                                <label class="form-check-label badge badge-{{ $cls }} px-2 py-1" for="s{{ $sess->id }}_{{ $val }}">
                                    {{ $lbl }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="notes{{ $sess->id }}">Instructor Notes <small class="text-muted">(optional)</small></label>
                        <textarea class="form-control" id="notes{{ $sess->id }}" name="instructor_notes" rows="3"
                            placeholder="Any observations, student performance, areas to improve..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Save Feedback</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

@endsection

@section('scripts')
<script>
$(document).ready(function () {

    // ── Generate session date/time slots from total hours ─────────────────
    $('#generateSessionsBtn').on('click', function () {
        var totalHours = parseInt($('#totalHoursInput').val()) || 0;

        if (totalHours < 2 || totalHours % 2 !== 0) {
            alert('Total hours must be a positive multiple of 2 (e.g. 2, 4, 6...).');
            return;
        }

        var numSessions = totalHours / 2;
        var html = '<hr><h6 class="font-weight-bold text-primary mb-3">'
                 + numSessions + ' session(s) &times; 2 hours each</h6>';

        for (var i = 0; i < numSessions; i++) {
            html += '<div class="card mb-2">'
                  + '<div class="card-body py-2 px-3">'
                  + '<strong>Session ' + (i + 1) + '</strong>'
                  + '<div class="row mt-2">'
                  + '<div class="col-6">'
                  + '<label style="font-size:.82rem;">Date <span class="text-danger">*</span></label>'
                  + '<input type="date" class="form-control form-control-sm" '
                  + 'name="sessions[' + i + '][date]" required>'
                  + '</div>'
                  + '<div class="col-6">'
                  + '<label style="font-size:.82rem;">Start Time <span class="text-danger">*</span></label>'
                  + '<input type="time" class="form-control form-control-sm" '
                  + 'name="sessions[' + i + '][start_time]" required>'
                  + '<small class="text-muted">End time auto: +2 hrs</small>'
                  + '</div>'
                  + '</div>'
                  + '</div>'
                  + '</div>';
        }

        $('#sessionSlotsContainer').html(html);
        $('#saveSessionsBtn').prop('disabled', false);
    });

    // Auto-regenerate when hours value changes
    $('#totalHoursInput').on('change', function () {
        $('#sessionSlotsContainer').html('');
        $('#saveSessionsBtn').prop('disabled', true);
    });
});

// Select all schedules checkbox
document.getElementById('selectAllSchedules')?.addEventListener('change', function () {
    document.querySelectorAll('.schedule-checkbox').forEach(cb => cb.checked = this.checked);
});

// Reschedule request modals — set approve/reject action and button style
$('[data-target^="#handleRescheduleModal"]').on('click', function () {
    var action  = $(this).data('action');
    var modalId = $(this).data('target').replace('#handleRescheduleModal', '');
    $('#rescheduleAction' + modalId).val(action);
    var btn = $('#rescheduleSubmitBtn' + modalId);
    if (action === 'reject') {
        btn.removeClass('btn-success').addClass('btn-danger').html('<i class="fas fa-times mr-1"></i>Reject');
    } else {
        btn.removeClass('btn-danger').addClass('btn-success').html('<i class="fas fa-check mr-1"></i>Approve');
    }
});
</script>
@endsection
