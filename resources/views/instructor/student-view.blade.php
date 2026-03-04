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
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Course:</strong> {{ $student->course->course_name }}</p>
                            <p class="mb-1"><strong>Type:</strong> {{ ucfirst($student->course->course_type ?? 'N/A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1">
                                <strong>Theory:</strong>
                                {{ $student->course->theory_hours ?? 0 }} hrs &nbsp;|&nbsp;
                                {{ $student->course->total_theory_classes ?? 0 }} classes
                            </p>
                            <p class="mb-1">
                                <strong>Practical:</strong>
                                {{ $student->course->practical_hours ?? 0 }} hrs &nbsp;|&nbsp;
                                {{ $student->course->total_practical_classes ?? 0 }} classes
                            </p>
                        </div>
                    </div>
                    <hr>

                    <!-- Progress Bars -->
                    <div class="mb-2">
                        @php
                            $tTotal = $student->course->theory_hours ?? 1;
                            $tDone  = $student->hours_theory ?? 0;
                            $tPct   = min(100, round(($tDone/$tTotal)*100));
                            $pTotal = $student->course->practical_hours ?? 1;
                            $pDone  = $student->hours_practical ?? 0;
                            $pPct   = min(100, round(($pDone/$pTotal)*100));
                        @endphp
                        <small class="text-muted">Theory Hours: {{ $tDone }}/{{ $tTotal }}</small>
                        <div class="progress mb-2" style="height:10px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width:{{ $tPct }}%"></div>
                        </div>
                        <small class="text-muted">Practical Hours: {{ $pDone }}/{{ $pTotal }}</small>
                        <div class="progress" style="height:10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width:{{ $pPct }}%"></div>
                        </div>
                    </div>
                    <hr>

                    <!-- Theory Status Row -->
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
                        </div>
                        @if($student->theory_status == 'pending' || $student->theory_status == 'in_progress')
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
                        @if($student->theory_status == 'completed' && in_array($student->practical_status, ['pending','assigned']))
                            <button class="btn btn-sm btn-primary mt-1 mt-md-0" data-toggle="modal" data-target="#assignPracticalModal">
                                <i class="fas fa-calendar-plus mr-1"></i>Assign Practical Sessions
                            </button>
                        @endif
                    </div>
                </div>
            </div>

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

        </div><!-- /col-lg-8 -->
    </div><!-- /row -->
</div><!-- /container-fluid -->

<!-- =========================================================
     MODAL: Mark Theory Complete
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
                            value="{{ $student->hours_theory ?? $student->course->theory_hours }}"
                            min="0" max="{{ $student->course->theory_hours }}" step="0.5" required>
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
                        Practical total: <strong>{{ $student->course->practical_hours ?? 0 }} hours</strong>
                        &nbsp;|&nbsp; Each session = <strong>2 hours</strong>
                    </p>

                    <div class="form-group">
                        <label for="totalHoursInput">Total Practical Hours to Assign</label>
                        <input type="number" class="form-control" id="totalHoursInput" name="total_hours"
                            min="2" max="{{ $student->course->practical_hours ?? 20 }}" step="2"
                            value="{{ $student->course->practical_hours ?? 6 }}"
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
</script>
@endsection
