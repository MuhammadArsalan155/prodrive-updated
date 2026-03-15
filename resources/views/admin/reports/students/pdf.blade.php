<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Report - {{ $student->first_name }} {{ $student->last_name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #1f2937;
            background: #fff;
        }

        /* Page layout */
        .page { padding: 12mm 15mm; }

        /* Header */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 2px solid #3b82f6;
        }
        .header-left  { display: table-cell; vertical-align: middle; width: 60%; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }
        .brand { font-size: 20px; font-weight: 700; color: #1e3a5f; letter-spacing: 1px; }
        .brand-sub { font-size: 9px; color: #6b7280; margin-top: 2px; }
        .report-meta { font-size: 8px; color: #6b7280; }
        .report-meta strong { color: #374151; }

        /* Student name banner */
        .student-banner {
            background: #1e3a5f;
            color: white;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            display: table;
            width: 100%;
        }
        .banner-name { font-size: 16px; font-weight: 700; }
        .banner-meta { font-size: 9px; opacity: 0.8; margin-top: 3px; }

        /* Status pills */
        .pill {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .pill-success { background: #d1fae5; color: #065f46; }
        .pill-warning { background: #fef3c7; color: #92400e; }
        .pill-danger  { background: #fee2e2; color: #991b1b; }
        .pill-primary { background: #dbeafe; color: #1e40af; }
        .pill-secondary { background: #f1f5f9; color: #475569; }
        .pill-purple  { background: #ede9fe; color: #5b21b6; }
        .pill-info    { background: #cffafe; color: #155e75; }

        /* Sections */
        .section { margin-bottom: 18px; page-break-inside: avoid; }
        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #1e3a5f;
            border-bottom: 1.5px solid #3b82f6;
            padding-bottom: 5px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .section-number {
            display: inline-block;
            background: #3b82f6;
            color: white;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            text-align: center;
            line-height: 18px;
            font-size: 9px;
            font-weight: 700;
            margin-right: 6px;
        }

        /* Two column layout */
        .two-col { display: table; width: 100%; table-layout: fixed; }
        .col-l  { display: table-cell; width: 50%; padding-right: 10px; vertical-align: top; }
        .col-r  { display: table-cell; width: 50%; padding-left: 10px; vertical-align: top; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 10px; }
        th { background: #f1f5f9; padding: 6px 8px; text-align: left; font-weight: 700; color: #374151; border: 1px solid #e2e8f0; font-size: 8px; }
        td { padding: 6px 8px; border: 1px solid #e2e8f0; }
        tr:nth-child(even) td { background: #f8fafc; }

        /* Info table (key-value) */
        .kv-table td:first-child { font-weight: 700; color: #374151; width: 38%; background: #f8fafc; }
        .kv-table td { border: none; border-bottom: 1px solid #f1f5f9; padding: 5px 8px; }

        /* Summary stats */
        .stats-row { display: table; width: 100%; margin-bottom: 14px; }
        .stat-cell { display: table-cell; text-align: center; border: 1px solid #e2e8f0; border-radius: 4px; padding: 8px; }
        .stat-val { font-size: 16px; font-weight: 700; color: #1f2937; }
        .stat-lbl { font-size: 7.5px; color: #6b7280; margin-top: 2px; }

        /* Progress bars */
        .prog-wrap { margin-bottom: 10px; }
        .prog-row { display: table; width: 100%; margin-bottom: 3px; }
        .prog-lbl { display: table-cell; font-size: 9px; font-weight: 700; color: #374151; width: 30%; }
        .prog-pct { display: table-cell; text-align: right; font-size: 9px; font-weight: 700; width: 10%; }
        .prog-bg { height: 10px; background: #f1f5f9; border-radius: 5px; margin-bottom: 8px; overflow: hidden; }
        .prog-fill { height: 100%; border-radius: 5px; }
        .fill-theory    { background: #3b82f6; }
        .fill-practical { background: #10b981; }
        .fill-overall   { background: #8b5cf6; }
        .prog-meta { font-size: 8px; color: #6b7280; margin-bottom: 10px; }

        /* Class list */
        .class-row { display: table; width: 100%; border-bottom: 1px solid #f1f5f9; padding: 5px 0; }
        .class-num-cell { display: table-cell; width: 28px; vertical-align: middle; }
        .class-num-badge {
            width: 22px; height: 22px; border-radius: 50%; text-align: center; line-height: 22px;
            font-size: 8px; font-weight: 700; display: inline-block;
        }
        .badge-done    { background: #d1fae5; color: #065f46; }
        .badge-active  { background: #dbeafe; color: #1e40af; }
        .badge-pending { background: #f1f5f9; color: #6b7280; }
        .class-body-cell { display: table-cell; vertical-align: middle; }
        .class-title-text { font-size: 9px; font-weight: 700; color: #1f2937; }
        .class-meta-text  { font-size: 8px; color: #6b7280; }

        /* Practical slot box */
        .slot-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 10px; }
        .slot-grid { display: table; width: 100%; }
        .slot-cell { display: table-cell; width: 33%; padding-right: 8px; }
        .slot-lbl { font-size: 7.5px; font-weight: 700; text-transform: uppercase; color: #047857; margin-bottom: 2px; }
        .slot-val { font-size: 9px; font-weight: 700; color: #1f2937; }

        /* Feedback */
        .fb-block { border: 1px solid #e2e8f0; border-radius: 4px; margin-bottom: 8px; overflow: hidden; }
        .fb-header { background: #f1f5f9; padding: 5px 8px; font-weight: 700; font-size: 9px; color: #374151; border-bottom: 1px solid #e2e8f0; }
        .fb-item { display: table; width: 100%; border-bottom: 1px solid #f1f5f9; padding: 5px 8px; }
        .fb-item:last-child { border-bottom: none; }
        .fb-q-cell { display: table-cell; font-size: 9px; }
        .fb-r-cell { display: table-cell; text-align: right; width: 40px; font-size: 9px; font-weight: 700; }
        .fb-yes { color: #10b981; }
        .fb-no  { color: #ef4444; }

        /* Payment invoice box */
        .invoice-box { border: 1px solid #e2e8f0; border-radius: 5px; margin-bottom: 10px; overflow: hidden; }
        .invoice-hdr { background: #f8fafc; padding: 7px 10px; display: table; width: 100%; border-bottom: 1px solid #e2e8f0; }
        .invoice-title-cell { display: table-cell; font-size: 10px; font-weight: 700; }
        .invoice-amount-cell { display: table-cell; text-align: right; font-size: 10px; font-weight: 700; }
        .invoice-body { padding: 8px 10px; }

        /* Completion steps */
        .step-row { display: table; width: 100%; margin-bottom: 10px; }
        .step-dot-cell { display: table-cell; width: 26px; vertical-align: top; text-align: center; }
        .step-dot { width: 18px; height: 18px; border-radius: 50%; border: 2px solid; display: inline-block; text-align: center; line-height: 14px; font-size: 8px; font-weight: 700; }
        .step-dot.done    { border-color: #10b981; color: #10b981; background: #d1fae5; }
        .step-dot.active  { border-color: #3b82f6; color: #3b82f6; background: #dbeafe; }
        .step-dot.pending { border-color: #d1d5db; color: #9ca3af; background: #f9fafb; }
        .step-body-cell { display: table-cell; vertical-align: top; padding-left: 8px; }
        .step-title { font-size: 10px; font-weight: 700; color: #1f2937; }
        .step-desc  { font-size: 8px; color: #6b7280; margin-top: 1px; }

        /* Hours log */
        .hour-row { display: table; width: 100%; border-bottom: 1px solid #f1f5f9; padding: 4px 0; }
        .hour-date { display: table-cell; font-size: 9px; color: #6b7280; width: 90px; }
        .hour-type { display: table-cell; font-size: 8px; font-weight: 700; }
        .hour-val  { display: table-cell; text-align: right; font-size: 9px; font-weight: 700; }

        /* Callout box */
        .callout { border-left: 3px solid #3b82f6; background: #eff6ff; padding: 8px 10px; border-radius: 0 4px 4px 0; margin: 8px 0; }
        .callout-success { border-left-color: #10b981; background: #f0fdf4; }
        .callout-warning { border-left-color: #f59e0b; background: #fffbeb; }
        .callout-title { font-size: 9px; font-weight: 700; color: #1f2937; margin-bottom: 3px; }
        .callout-text  { font-size: 8.5px; color: #4b5563; }

        /* Footer */
        .footer { text-align: center; padding-top: 12px; border-top: 1px solid #e2e8f0; margin-top: 20px; font-size: 8px; color: #9ca3af; }

        /* Utils */
        .text-success { color: #10b981; }
        .text-danger  { color: #ef4444; }
        .text-primary { color: #3b82f6; }
        .text-muted   { color: #6b7280; }
        .fw-700 { font-weight: 700; }
        .mb-8   { margin-bottom: 8px; }
    </style>
</head>
<body>
<div class="page">

    {{-- ========== HEADER ========== --}}
    <div class="header">
        <div class="header-left">
            <div class="brand">PRODRIVE</div>
            <div class="brand-sub">Professional Driving School Management</div>
        </div>
        <div class="header-right">
            <div class="report-meta">
                <strong>Student Progress Report</strong><br>
                Generated: {{ date('M d, Y h:i A') }}<br>
                Report ID: SR-{{ $student->id }}-{{ date('Ymd') }}
            </div>
        </div>
    </div>

    {{-- ========== STUDENT BANNER ========== --}}
    <div class="student-banner">
        <div class="banner-name">{{ $student->first_name }} {{ $student->last_name }}</div>
        <div class="banner-meta">
            Student ID #{{ $student->id }} &bull; {{ $student->email }}
            @if($student->student_contact) &bull; {{ $student->student_contact }} @endif
            &bull;
            @php
                $statusLabels = ['0'=>['Pending','pill-warning'],'1'=>['In Progress','pill-primary'],'2'=>['Completed','pill-success']];
                $cs = $statusLabels[(string)$student->course_status] ?? ['Unknown','pill-secondary'];
            @endphp
            Course: <strong>{{ $cs[0] }}</strong>
        </div>
    </div>

    {{-- ========== SUMMARY STATS ========== --}}
    <div class="stats-row mb-8" style="border:1px solid #e2e8f0;border-radius:6px;overflow:hidden;">
        <div class="stat-cell" style="border-right:1px solid #e2e8f0;">
            <div class="stat-val" style="color:#3b82f6;">{{ $courseProgress['theory']['percentage'] }}%</div>
            <div class="stat-lbl">Theory Progress</div>
        </div>
        <div class="stat-cell" style="border-right:1px solid #e2e8f0;">
            <div class="stat-val" style="color:#10b981;">{{ $courseProgress['practical']['percentage'] }}%</div>
            <div class="stat-lbl">Practical Progress</div>
        </div>
        <div class="stat-cell" style="border-right:1px solid #e2e8f0;">
            <div class="stat-val">{{ $courseProgress['theory']['completed'] + $courseProgress['practical']['completed'] }}h</div>
            <div class="stat-lbl">Total Hours Done</div>
        </div>
        <div class="stat-cell" style="border-right:1px solid #e2e8f0;">
            <div class="stat-val" style="color:#10b981;">${{ number_format($totalPaid, 0) }}</div>
            <div class="stat-lbl">Amount Paid</div>
        </div>
        <div class="stat-cell" style="border-right:1px solid #e2e8f0;">
            <div class="stat-val" style="color:#ef4444;">${{ number_format($pendingPayments, 0) }}</div>
            <div class="stat-lbl">Balance Due</div>
        </div>
        <div class="stat-cell">
            <div class="stat-val" style="color:#8b5cf6;">{{ $certificate ? 'Yes' : 'No' }}</div>
            <div class="stat-lbl">Certified</div>
        </div>
    </div>

    {{-- ========== SECTION 1: STUDENT PROFILE ========== --}}
    <div class="section">
        <div class="section-title"><span class="section-number">1</span> Student Profile & Registration</div>
        <div class="two-col">
            <div class="col-l">
                <table class="kv-table">
                    <tr><td>Full Name</td><td>{{ $student->first_name }} {{ $student->last_name }}</td></tr>
                    <tr><td>Email</td><td>{{ $student->email }}</td></tr>
                    <tr><td>Phone</td><td>{{ $student->student_contact ?: '—' }}</td></tr>
                    <tr><td>Date of Birth</td><td>{{ $student->student_dob ? \Carbon\Carbon::parse($student->student_dob)->format('M d, Y') : '—' }}</td></tr>
                    <tr><td>Address</td><td>{{ $student->address ?: '—' }}</td></tr>
                </table>
            </div>
            <div class="col-r">
                <table class="kv-table">
                    <tr><td>Enrollment Date</td><td>{{ $student->joining_date ? \Carbon\Carbon::parse($student->joining_date)->format('M d, Y') : '—' }}</td></tr>
                    <tr><td>Completion Date</td><td>{{ $student->completion_date ? \Carbon\Carbon::parse($student->completion_date)->format('M d, Y') : 'Pending' }}</td></tr>
                    <tr><td>Course Status</td><td><span class="pill {{ $cs[1] }}">{{ $cs[0] }}</span></td></tr>
                    <tr><td>Parent/Guardian</td><td>{{ $student->parent->name ?? '—' }}</td></tr>
                    <tr><td>Parent Contact</td><td>{{ $student->parent->contact ?? '—' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    {{-- ========== SECTION 2: COURSE STRUCTURE ========== --}}
    @if($student->course)
    <div class="section">
        <div class="section-title"><span class="section-number">2</span> Course Structure Mapping</div>
        <table class="kv-table mb-8">
            <tr>
                <td>Course Name</td><td>{{ $student->course->course_name }}</td>
                <td>Course Type</td><td>{{ $student->course->course_type }}</td>
            </tr>
            <tr>
                <td>Theory Classes</td><td>{{ $student->course->total_theory_classes ?? '—' }} total / {{ $courseProgress['theory']['classes_completed'] }} completed</td>
                <td>Practical Classes</td><td>{{ $student->course->total_practical_classes ?? '—' }} total / {{ $courseProgress['practical']['classes_completed'] }} completed</td>
            </tr>
            <tr>
                <td>Theory Hours</td><td>{{ $courseProgress['theory']['completed'] }}h done / {{ $courseProgress['theory']['total'] }}h required</td>
                <td>Practical Hours</td><td>{{ $courseProgress['practical']['completed'] }}h done / {{ $courseProgress['practical']['total'] }}h required</td>
            </tr>
            <tr>
                <td>Course Fee</td><td>${{ number_format($student->course->course_price, 2) }}</td>
                <td>Installment Plan</td><td>{{ $student->course->has_installment_plan ? 'Yes' : 'No' }}</td>
            </tr>
        </table>

        <div class="two-col">
            <div class="col-l">
                <div style="font-size:9px;font-weight:700;color:#374151;margin-bottom:6px;">Theory Curriculum ({{ $theoryLessonPlans->count() }} classes)</div>
                @forelse($theoryLessonPlans as $plan)
                    @php
                        $cn = $plan->pivot->class_order;
                        $st = $student->theory_status == 'completed' ? 'done'
                            : ($cn <= $courseProgress['theory']['classes_completed'] ? 'done'
                                : ($cn == $courseProgress['theory']['classes_completed'] + 1 ? 'active' : 'pending'));
                    @endphp
                    <div class="class-row">
                        <div class="class-num-cell">
                            <span class="class-num-badge badge-{{ $st }}">{{ $cn }}</span>
                        </div>
                        <div class="class-body-cell">
                            <div class="class-title-text">{{ $plan->title }}</div>
                            <div class="class-meta-text">Class #{{ $cn }} &bull;
                                @if($st=='done') <span class="text-success">Done</span>
                                @elseif($st=='active') <span class="text-primary">In Progress</span>
                                @else Upcoming @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="font-size:9px;color:#9ca3af;padding:8px 0;">No theory lesson plans configured.</div>
                @endforelse
            </div>
            <div class="col-r">
                <div style="font-size:9px;font-weight:700;color:#374151;margin-bottom:6px;">Practical Curriculum ({{ $practicalLessonPlans->count() }} classes)</div>
                @forelse($practicalLessonPlans as $plan)
                    @php
                        $cn = $plan->pivot->class_order;
                        $st = $student->practical_status == 'completed' ? 'done'
                            : ($cn <= $courseProgress['practical']['classes_completed'] ? 'done'
                                : ($cn == $courseProgress['practical']['classes_completed'] + 1 ? 'active' : 'pending'));
                    @endphp
                    <div class="class-row">
                        <div class="class-num-cell">
                            <span class="class-num-badge badge-{{ $st }}">{{ $cn }}</span>
                        </div>
                        <div class="class-body-cell">
                            <div class="class-title-text">{{ $plan->title }}</div>
                            <div class="class-meta-text">
                                Practical #{{ $cn }}
                                @if($student->course->practical_hours && $student->course->total_practical_classes)
                                    &bull; {{ round($student->course->practical_hours / $student->course->total_practical_classes, 1) }}h/class
                                @endif
                                &bull;
                                @if($st=='done') <span class="text-success">Done</span>
                                @elseif($st=='active') <span class="text-primary">In Progress</span>
                                @else Upcoming @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="font-size:9px;color:#9ca3af;padding:8px 0;">No practical lesson plans configured.</div>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    {{-- ========== SECTION 3: INSTRUCTOR ASSIGNMENT ========== --}}
    <div class="section">
        <div class="section-title"><span class="section-number">3</span> Instructor Assignment</div>
        @if($student->instructor)
            <table class="kv-table">
                <tr><td>Instructor Name</td><td><strong>{{ $student->instructor->instructor_name }}</strong></td>
                    <td>Email</td><td>{{ $student->instructor->email ?? '—' }}</td></tr>
                <tr><td>Contact</td><td>{{ $student->instructor->contact ?? '—' }}</td>
                    <td>License #</td><td>{{ $student->instructor->license_number ?? '—' }}</td></tr>
                <tr><td>Assignment Date</td><td>{{ $student->joining_date ? \Carbon\Carbon::parse($student->joining_date)->format('M d, Y') : '—' }}</td>
                    <td>Status</td><td><span class="pill {{ $student->instructor->is_active ? 'pill-success' : 'pill-danger' }}">{{ $student->instructor->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
            </table>
        @else
            <div style="font-size:9px;color:#9ca3af;padding:8px 0;">No instructor assigned.</div>
        @endif
    </div>

    {{-- ========== SECTION 4: THEORY CLASSES FLOW ========== --}}
    <div class="section">
        <div class="section-title"><span class="section-number">4</span> Theory Classes Flow</div>

        {{-- Progress --}}
        <div class="prog-wrap">
            <div class="prog-row">
                <div class="prog-lbl">Theory Progress</div>
                <div class="prog-pct">{{ $courseProgress['theory']['percentage'] }}%</div>
            </div>
            <div class="prog-bg"><div class="prog-fill fill-theory" style="width:{{ $courseProgress['theory']['percentage'] }}%;"></div></div>
            <div class="prog-meta">
                {{ $courseProgress['theory']['classes_completed'] }}/{{ $courseProgress['theory']['classes_total'] }} classes completed &bull;
                Status:
                @if($student->theory_status == 'completed') <strong class="text-success">Completed{{ $student->theory_completion_date ? ' on '.\Carbon\Carbon::parse($student->theory_completion_date)->format('M d, Y') : '' }}</strong>
                @elseif($student->theory_status == 'in_progress') <strong class="text-primary">In Progress</strong>
                @else Pending @endif
            </div>
        </div>

        {{-- Completed theory attendance --}}
        @php $theoryAttendances = $sessionAttendances->where('class_type', 'theory'); @endphp
        @if($theoryAttendances->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th>#</th><th>Date</th><th>Day</th><th>Start</th><th>End</th><th>Duration</th><th>Completed At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($theoryAttendances as $idx => $att)
                        @php
                            $sched = $att->schedule;
                            $sd = $sched ? \Carbon\Carbon::parse($sched->date) : null;
                            $st = $sched ? \Carbon\Carbon::parse($sched->start_time) : null;
                            $et = $sched ? \Carbon\Carbon::parse($sched->end_time) : null;
                            $dur = ($st && $et) ? round($et->diffInMinutes($st)/60, 1) : '—';
                        @endphp
                        <tr style="background:#d1fae5;">
                            <td style="font-weight:700;">{{ $idx+1 }}</td>
                            <td>{{ $sd ? $sd->format('M d, Y') : '—' }}</td>
                            <td>{{ $sd ? $sd->format('D') : '—' }}</td>
                            <td>{{ $st ? $st->format('h:i A') : '—' }}</td>
                            <td>{{ $et ? $et->format('h:i A') : '—' }}</td>
                            <td>{{ is_numeric($dur) ? $dur.'h' : $dur }}</td>
                            <td>{{ $att->completed_at ? \Carbon\Carbon::parse($att->completed_at)->format('M d, Y h:i A') : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="font-size:9px;color:#9ca3af;padding:4px 0;">No theory classes completed yet.</div>
        @endif

        @if($student->theory_status == 'completed')
            <div class="callout callout-success">
                <div class="callout-title">&#10003; Theory Completed</div>
                <div class="callout-text">All theory classes completed{{ $student->theory_completion_date ? ' on '.\Carbon\Carbon::parse($student->theory_completion_date)->format('M d, Y') : '' }}.</div>
            </div>
        @endif
    </div>

    {{-- ========== SECTION 5: PRACTICAL FLOW ========== --}}
    <div class="section">
        <div class="section-title"><span class="section-number">5</span> Practical Assignment & Flow</div>

        <div class="prog-wrap">
            <div class="prog-row">
                <div class="prog-lbl">Practical Progress</div>
                <div class="prog-pct">{{ $courseProgress['practical']['percentage'] }}%</div>
            </div>
            <div class="prog-bg"><div class="prog-fill fill-practical" style="width:{{ $courseProgress['practical']['percentage'] }}%;"></div></div>
            <div class="prog-meta">
                {{ $courseProgress['practical']['classes_completed'] }}/{{ $courseProgress['practical']['classes_total'] }} classes completed &bull;
                Status: <strong>{{ ucfirst(str_replace('_',' ',$student->practical_status)) }}</strong>
                @if($student->practical_completion_date)
                    &bull; <strong class="text-success">Completed {{ \Carbon\Carbon::parse($student->practical_completion_date)->format('M d, Y') }}</strong>
                @endif
            </div>
        </div>

        @php $practicalAttendances = $sessionAttendances->where('class_type', 'practical'); @endphp
        @if($practicalAttendances->isNotEmpty())
            <table style="width:100%;border-collapse:collapse;font-size:8.5px;margin-bottom:8px;">
                <thead>
                    <tr style="background:#1a2e4a;color:#fff;">
                        <th style="padding:4px 6px;text-align:left;">#</th>
                        <th style="padding:4px 6px;text-align:left;">Date</th>
                        <th style="padding:4px 6px;text-align:left;">Day</th>
                        <th style="padding:4px 6px;text-align:left;">Start</th>
                        <th style="padding:4px 6px;text-align:left;">End</th>
                        <th style="padding:4px 6px;text-align:left;">Duration</th>
                        <th style="padding:4px 6px;text-align:left;">Completed At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($practicalAttendances as $idx => $att)
                        @php
                            $sched = $att->schedule;
                            $sd = $sched ? \Carbon\Carbon::parse($sched->date) : null;
                            $st = $sched ? \Carbon\Carbon::parse($sched->start_time) : null;
                            $et = $sched ? \Carbon\Carbon::parse($sched->end_time) : null;
                            $dur = ($st && $et) ? round($et->diffInMinutes($st)/60, 1) : '—';
                        @endphp
                        <tr style="background:#d1fae5;">
                            <td style="padding:3px 6px;border:1px solid #e5e7eb;font-weight:700;">{{ $idx+1 }}</td>
                            <td style="padding:3px 6px;border:1px solid #e5e7eb;">{{ $sd ? $sd->format('M d, Y') : '—' }}</td>
                            <td style="padding:3px 6px;border:1px solid #e5e7eb;">{{ $sd ? $sd->format('D') : '—' }}</td>
                            <td style="padding:3px 6px;border:1px solid #e5e7eb;">{{ $st ? $st->format('h:i A') : '—' }}</td>
                            <td style="padding:3px 6px;border:1px solid #e5e7eb;">{{ $et ? $et->format('h:i A') : '—' }}</td>
                            <td style="padding:3px 6px;border:1px solid #e5e7eb;">{{ is_numeric($dur) ? $dur.'h' : $dur }}</td>
                            <td style="padding:3px 6px;border:1px solid #e5e7eb;">{{ $att->completed_at ? \Carbon\Carbon::parse($att->completed_at)->format('M d, Y h:i A') : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="font-size:9px;color:#9ca3af;padding:6px 0;">
                No practical classes completed yet.
                @if($student->theory_status !== 'completed')
                    Theory classes must be completed before practical assignment.
                @endif
            </div>
        @endif

        @if($student->practical_status == 'failed')
            <div class="callout callout-warning"><div class="callout-title">Practical Not Passed</div><div class="callout-text">Student did not pass the practical sessions. Re-scheduling may be required.</div></div>
        @elseif($student->practical_status == 'not_appeared')
            <div class="callout callout-warning"><div class="callout-title">Did Not Appear</div><div class="callout-text">Student did not appear for the scheduled practical sessions.</div></div>
        @endif
    </div>

    {{-- ========== SECTION 6: FEEDBACK ========== --}}
    @if($feedbackResponses->isNotEmpty() || $progressReports->isNotEmpty())
    <div class="section">
        <div class="section-title"><span class="section-number">6</span> Feedback Recording</div>

        @if($feedbackResponses->isNotEmpty())
            <div style="font-size:9px;font-weight:700;color:#374151;margin-bottom:6px;">Class Feedback Responses</div>
            @foreach($feedbackResponses as $classOrder => $responses)
                <div class="fb-block">
                    <div class="fb-header">Class #{{ $classOrder }} ({{ $responses->count() }} responses)</div>
                    @foreach($responses as $resp)
                        <div class="fb-item">
                            <div class="fb-q-cell">
                                {{ $resp->question->question_text ?? 'Question' }}
                                @if($resp->comments) <div style="font-size:8px;color:#9ca3af;font-style:italic;">"{{ $resp->comments }}"</div> @endif
                            </div>
                            <div class="fb-r-cell {{ $resp->response ? 'fb-yes' : 'fb-no' }}">
                                {{ $resp->response ? 'Yes' : 'No' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif

        @if($progressReports->isNotEmpty())
            <div style="font-size:9px;font-weight:700;color:#374151;margin:10px 0 6px;">Instructor Progress Reports</div>
            @foreach($progressReports as $report)
                <div class="callout mb-8">
                    <div class="callout-title" style="display:table;width:100%;">
                        <span style="display:table-cell;">Report — {{ $report->created_at->format('M d, Y') }} ({{ $report->instructor->instructor_name ?? '—' }})</span>
                        @if($report->rating)<span style="display:table-cell;text-align:right;"><span class="pill pill-primary">{{ $report->rating }}/5 stars</span></span>@endif
                    </div>
                    @if($report->performance_notes)
                        <div class="callout-text"><strong>Performance:</strong> {{ $report->performance_notes }}</div>
                    @endif
                    @if($report->areas_of_improvement)
                        <div class="callout-text" style="margin-top:3px;"><strong>Improvements:</strong> {{ $report->areas_of_improvement }}</div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
    @endif

    {{-- ========== SECTION 7: PAYMENT FLOW ========== --}}
    <div class="section">
        <div class="section-title"><span class="section-number">7</span> Payment Flow</div>

        <table class="kv-table mb-8">
            <tr>
                <td>Total Billed</td><td><strong>${{ number_format($totalBilled, 2) }}</strong></td>
                <td>Amount Paid</td><td><strong class="text-success">${{ number_format($totalPaid, 2) }}</strong></td>
            </tr>
            <tr>
                <td>Balance Due</td><td><strong class="text-danger">${{ number_format($pendingPayments, 2) }}</strong></td>
                <td>Payment Status</td><td>
                    @php $payMap2 = ['0'=>['Unpaid','pill-danger'],'1'=>['Pending','pill-warning'],'2'=>['Failed','pill-danger'],'3'=>['Paid','pill-success'],'paid'=>['Paid','pill-success'],'partial'=>['Partial','pill-warning'],'pending'=>['Pending','pill-warning']]; $ps2 = $payMap2[(string)$student->payment_status] ?? ['—','pill-secondary']; @endphp
                    <span class="pill {{ $ps2[1] }}">{{ $ps2[0] }}</span>
                </td>
            </tr>
        </table>

        @forelse($student->invoices as $invoice)
            <div class="invoice-box">
                <div class="invoice-hdr">
                    <div class="invoice-title-cell">Invoice #{{ $invoice->invoice_number }} — {{ $invoice->created_at->format('M d, Y') }}</div>
                    <div class="invoice-amount-cell">
                        <strong>${{ number_format($invoice->amount, 2) }}</strong>
                        &nbsp;<span class="pill {{ $invoice->status == 'paid' ? 'pill-success' : 'pill-warning' }}">{{ ucfirst($invoice->status) }}</span>
                    </div>
                </div>
                <div class="invoice-body">
                    @if($invoice->payments->isNotEmpty())
                        <div style="font-size:8px;font-weight:700;text-transform:uppercase;color:#6b7280;margin-bottom:5px;">Payment Records</div>
                        <table>
                            <thead><tr><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr></thead>
                            <tbody>
                                @foreach($invoice->payments as $pay)
                                    <tr>
                                        <td>${{ number_format($pay->amount, 2) }}</td>
                                        <td>{{ $pay->paymentMethod->name ?? '—' }}</td>
                                        <td><span class="pill {{ $pay->status == 'completed' ? 'pill-success' : 'pill-warning' }}">{{ ucfirst($pay->status) }}</span></td>
                                        <td>{{ $pay->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    @if($invoice->installments->isNotEmpty())
                        <div style="font-size:8px;font-weight:700;text-transform:uppercase;color:#6b7280;margin:8px 0 5px;">Installment Plan</div>
                        <table>
                            <thead><tr><th>#</th><th>Amount</th><th>Due Date</th><th>Status</th><th>Paid Date</th></tr></thead>
                            <tbody>
                                @foreach($invoice->installments as $idx => $inst)
                                    @php $ip = $inst->status=='paid'?'pill-success':($inst->is_overdue?'pill-danger':'pill-warning'); @endphp
                                    <tr>
                                        <td>{{ $idx+1 }}</td>
                                        <td>${{ number_format($inst->amount, 2) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($inst->due_date)->format('M d, Y') }}</td>
                                        <td><span class="pill {{ $ip }}">{{ $inst->status=='paid'?'Paid':($inst->is_overdue?'Overdue':ucfirst($inst->status)) }}</span></td>
                                        <td>{{ $inst->paid_at ? \Carbon\Carbon::parse($inst->paid_at)->format('M d, Y') : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        @empty
            <div style="font-size:9px;color:#9ca3af;padding:4px 0;">No payment records found.</div>
        @endforelse
    </div>

    {{-- ========== SECTION 8: COURSE COMPLETION ========== --}}
    <div class="section">
        <div class="section-title"><span class="section-number">8</span> Course Completion</div>

        {{-- Overall progress --}}
        <div class="prog-wrap">
            <div class="prog-row"><div class="prog-lbl">Theory</div><div class="prog-pct">{{ $courseProgress['theory']['percentage'] }}%</div></div>
            <div class="prog-bg"><div class="prog-fill fill-theory" style="width:{{ $courseProgress['theory']['percentage'] }}%;"></div></div>
            <div class="prog-row"><div class="prog-lbl">Practical</div><div class="prog-pct">{{ $courseProgress['practical']['percentage'] }}%</div></div>
            <div class="prog-bg"><div class="prog-fill fill-practical" style="width:{{ $courseProgress['practical']['percentage'] }}%;"></div></div>
            @php $op = round(($courseProgress['theory']['percentage'] + $courseProgress['practical']['percentage']) / 2); @endphp
            <div class="prog-row"><div class="prog-lbl">Overall</div><div class="prog-pct">{{ $op }}%</div></div>
            <div class="prog-bg"><div class="prog-fill fill-overall" style="width:{{ $op }}%;"></div></div>
        </div>

        {{-- Milestones --}}
        <div class="step-row">
            <div class="step-dot-cell"><div class="step-dot done">&#10003;</div></div>
            <div class="step-body-cell">
                <div class="step-title">Student Enrolled</div>
                <div class="step-desc">{{ $student->joining_date ? \Carbon\Carbon::parse($student->joining_date)->format('M d, Y') : '—' }}</div>
            </div>
        </div>
        <div class="step-row">
            <div class="step-dot-cell"><div class="step-dot {{ $student->instructor ? 'done' : 'pending' }}">{{ $student->instructor ? '&#10003;' : '&#8212;' }}</div></div>
            <div class="step-body-cell">
                <div class="step-title">Instructor Assigned</div>
                <div class="step-desc">{{ $student->instructor ? $student->instructor->instructor_name : 'Not assigned' }}</div>
            </div>
        </div>
        <div class="step-row">
            @php $ts = $student->theory_status=='completed'?'done':($student->theory_status=='in_progress'?'active':'pending'); @endphp
            <div class="step-dot-cell"><div class="step-dot {{ $ts }}">{{ $ts=='done'?'&#10003;':($ts=='active'?'&#9654;':'&#8212;') }}</div></div>
            <div class="step-body-cell">
                <div class="step-title">Theory Classes</div>
                <div class="step-desc">{{ $courseProgress['theory']['percentage'] }}% complete ({{ $courseProgress['theory']['classes_completed'] }}/{{ $courseProgress['theory']['classes_total'] }} classes)
                    @if($student->theory_status=='completed') &bull; <strong class="text-success">Completed {{ $student->theory_completion_date ? \Carbon\Carbon::parse($student->theory_completion_date)->format('M d, Y') : '' }}</strong> @endif
                </div>
            </div>
        </div>
        <div class="step-row">
            @php $ps = $student->practical_status=='completed'?'done':($student->practical_status=='assigned'?'active':'pending'); @endphp
            <div class="step-dot-cell"><div class="step-dot {{ $ps }}">{{ $ps=='done'?'&#10003;':($ps=='active'?'&#9654;':'&#8212;') }}</div></div>
            <div class="step-body-cell">
                <div class="step-title">Practical Session</div>
                <div class="step-desc">{{ ucfirst(str_replace('_',' ',$student->practical_status)) }}
                    @if($student->practical_completion_date) &bull; <strong class="text-success">Completed {{ \Carbon\Carbon::parse($student->practical_completion_date)->format('M d, Y') }}</strong> @endif
                </div>
            </div>
        </div>
        <div class="step-row">
            @php $cst = $student->course_status=='2'?'done':($student->course_status=='1'?'active':'pending'); @endphp
            <div class="step-dot-cell"><div class="step-dot {{ $cst }}">{{ $cst=='done'?'&#10003;':($cst=='active'?'&#9654;':'&#8212;') }}</div></div>
            <div class="step-body-cell">
                <div class="step-title">Course Completion</div>
                <div class="step-desc">
                    @if($student->course_status=='2') <strong class="text-success">Completed{{ $student->completion_date ? ' '.\Carbon\Carbon::parse($student->completion_date)->format('M d, Y') : '' }}</strong>
                    @elseif($student->course_status=='1') In Progress
                    @else Not started @endif
                </div>
            </div>
        </div>
        <div class="step-row">
            <div class="step-dot-cell"><div class="step-dot {{ $certificate ? 'done' : 'pending' }}">{{ $certificate ? '&#10003;' : '&#8212;' }}</div></div>
            <div class="step-body-cell">
                <div class="step-title">Certificate</div>
                <div class="step-desc">
                    @if($certificate) <strong class="text-success">Issued {{ \Carbon\Carbon::parse($certificate->issue_date)->format('M d, Y') }}</strong> &bull; #{{ $certificate->certificate_number }}
                    @else Not yet issued @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ========== FOOTER ========== --}}
    <div class="footer">
        <strong>&copy; {{ date('Y') }} Prodrive Driving School</strong> &bull; Professional Driver Education<br>
        This report was automatically generated from the student management system on {{ date('M d, Y h:i A') }}.<br>
        Student: {{ $student->first_name }} {{ $student->last_name }} (ID #{{ $student->id }}) &bull; Report: SR-{{ $student->id }}-{{ date('Ymd') }}
    </div>

</div>
</body>
</html>
