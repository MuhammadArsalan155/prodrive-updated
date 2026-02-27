@extends('layouts.master')

@section('title')
    Student Report - {{ $student->first_name }} {{ $student->last_name }}
@endsection

@section('styles')
<style>
    :root {
        --primary: #3b82f6;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #06b6d4;
        --purple: #8b5cf6;
        --orange: #f97316;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-500: #64748b;
        --gray-700: #374151;
        --gray-900: #111827;
    }

    body { background: #f0f4f8; }
    .content-wrapper { background: transparent !important; }

    /* Report Header */
    .report-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #3b82f6 100%);
        color: white;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .report-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }

    /* Status badges */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .pill-success { background: #d1fae5; color: #065f46; }
    .pill-warning { background: #fef3c7; color: #92400e; }
    .pill-danger  { background: #fee2e2; color: #991b1b; }
    .pill-primary { background: #dbeafe; color: #1e40af; }
    .pill-info    { background: #cffafe; color: #155e75; }
    .pill-secondary { background: #f1f5f9; color: #475569; }
    .pill-purple  { background: #ede9fe; color: #5b21b6; }

    /* Section cards */
    .section-card {
        background: white;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        margin-bottom: 1.5rem;
        overflow: hidden;
        border: 1px solid var(--gray-200);
    }
    .section-card .card-header-custom {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        gap: 10px;
        background: var(--gray-50);
    }
    .card-header-custom .section-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: white;
        flex-shrink: 0;
    }
    .card-header-custom .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
    }
    .card-header-custom .section-subtitle {
        font-size: 0.78rem;
        color: var(--gray-500);
        margin: 0;
    }
    .section-card .card-body-custom { padding: 1.5rem; }

    /* Summary stat boxes */
    .stat-box {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: 12px;
        padding: 1.2rem;
        text-align: center;
        border-top: 3px solid transparent;
    }
    .stat-box.blue  { border-top-color: var(--primary); }
    .stat-box.green { border-top-color: var(--success); }
    .stat-box.yellow{ border-top-color: var(--warning); }
    .stat-box.red   { border-top-color: var(--danger); }
    .stat-box.purple{ border-top-color: var(--purple); }
    .stat-box.orange{ border-top-color: var(--orange); }
    .stat-box .stat-value { font-size: 1.6rem; font-weight: 700; color: var(--gray-900); line-height: 1.2; }
    .stat-box .stat-label { font-size: 0.75rem; color: var(--gray-500); margin-top: 4px; font-weight: 500; }

    /* Info rows */
    .info-table td:first-child { font-weight: 600; color: var(--gray-700); width: 40%; white-space: nowrap; }
    .info-table td { padding: 6px 12px 6px 0; font-size: 0.9rem; vertical-align: top; border: none; }
    .info-table tr { border-bottom: 1px solid var(--gray-100); }
    .info-table tr:last-child { border-bottom: none; }

    /* Progress bars */
    .prog-wrap { margin-bottom: 1.2rem; }
    .prog-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
    .prog-label { font-weight: 600; font-size: 0.9rem; color: var(--gray-700); }
    .prog-pct { font-weight: 700; font-size: 0.9rem; }
    .prog-bar-bg { height: 12px; background: var(--gray-100); border-radius: 6px; overflow: hidden; }
    .prog-bar-fill { height: 100%; border-radius: 6px; transition: width 0.6s ease; }
    .prog-bar-theory   { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .prog-bar-practical{ background: linear-gradient(90deg, #10b981, #34d399); }
    .prog-meta { font-size: 0.78rem; color: var(--gray-500); margin-top: 4px; }

    /* Class timeline */
    .class-timeline { position: relative; }
    .class-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--gray-100);
    }
    .class-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    .class-num {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        flex-shrink: 0;
        border: 2px solid;
    }
    .class-num.done   { background: #d1fae5; color: #065f46; border-color: #10b981; }
    .class-num.active { background: #dbeafe; color: #1e40af; border-color: #3b82f6; }
    .class-num.pending{ background: var(--gray-100); color: var(--gray-500); border-color: var(--gray-200); }
    .class-content { flex: 1; min-width: 0; }
    .class-title { font-weight: 600; color: var(--gray-900); font-size: 0.9rem; margin-bottom: 2px; }
    .class-meta  { font-size: 0.78rem; color: var(--gray-500); }

    /* Schedule table */
    .sched-table { width: 100%; font-size: 0.85rem; }
    .sched-table th { background: var(--gray-50); color: var(--gray-700); font-weight: 600; padding: 8px 12px; border-bottom: 2px solid var(--gray-200); }
    .sched-table td { padding: 8px 12px; border-bottom: 1px solid var(--gray-100); vertical-align: middle; }
    .sched-table tr:last-child td { border-bottom: none; }
    .sched-table tr:hover td { background: var(--gray-50); }

    /* Practical slot */
    .practical-slot {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid #bbf7d0;
        border-radius: 12px;
        padding: 1.5rem;
    }
    .practical-slot .slot-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; margin-top: 1rem; }
    .slot-item .slot-label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.08em; color: #047857; font-weight: 600; margin-bottom: 4px; }
    .slot-item .slot-value { font-size: 0.95rem; font-weight: 600; color: var(--gray-900); }

    /* Feedback cards */
    .feedback-class {
        border: 1px solid var(--gray-200);
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 1rem;
    }
    .feedback-class-header {
        background: var(--gray-50);
        padding: 10px 14px;
        font-weight: 600;
        font-size: 0.88rem;
        color: var(--gray-700);
        border-bottom: 1px solid var(--gray-200);
    }
    .feedback-item { padding: 10px 14px; display: flex; align-items: flex-start; gap: 12px; border-bottom: 1px solid var(--gray-100); }
    .feedback-item:last-child { border-bottom: none; }
    .fb-q { flex: 1; font-size: 0.85rem; color: var(--gray-700); }
    .fb-response { font-weight: 600; font-size: 0.82rem; }
    .fb-yes { color: var(--success); }
    .fb-no  { color: var(--danger); }
    .fb-comment { font-size: 0.78rem; color: var(--gray-500); margin-top: 4px; font-style: italic; }

    /* Hours log */
    .hours-row { display: flex; align-items: center; gap: 14px; padding: 10px 0; border-bottom: 1px solid var(--gray-100); }
    .hours-row:last-child { border-bottom: none; }
    .hours-date { font-size: 0.82rem; color: var(--gray-500); min-width: 100px; }
    .hours-type-chip {
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    .chip-theory    { background: #dbeafe; color: #1e40af; }
    .chip-practical { background: #d1fae5; color: #065f46; }
    .hours-val { font-weight: 700; font-size: 0.95rem; color: var(--gray-900); margin-left: auto; }
    .hours-unit { font-size: 0.78rem; color: var(--gray-500); }

    /* Payment cards */
    .payment-invoice {
        border: 1px solid var(--gray-200);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 1rem;
    }
    .invoice-header {
        background: var(--gray-50);
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--gray-200);
    }
    .invoice-title { font-weight: 700; font-size: 0.9rem; color: var(--gray-900); }
    .invoice-body { padding: 1rem 1.25rem; }

    /* Completion timeline */
    .completion-step {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 1.5rem;
    }
    .step-indicator {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0;
        flex-shrink: 0;
    }
    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        border: 2px solid;
        background: white;
    }
    .step-circle.done   { border-color: var(--success); color: var(--success); background: #d1fae5; }
    .step-circle.active { border-color: var(--primary); color: var(--primary); background: #dbeafe; }
    .step-circle.pending{ border-color: var(--gray-200); color: var(--gray-500); background: var(--gray-50); }
    .step-line { width: 2px; height: 30px; background: var(--gray-200); margin-top: 4px; }
    .step-body { flex: 1; padding-top: 6px; }
    .step-title { font-weight: 700; font-size: 0.95rem; color: var(--gray-900); }
    .step-desc  { font-size: 0.82rem; color: var(--gray-500); margin-top: 2px; }

    /* Navigation pills */
    .report-nav { background: white; border-radius: 12px; padding: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); margin-bottom: 1.5rem; display: flex; flex-wrap: wrap; gap: 4px; border: 1px solid var(--gray-200); }
    .report-nav a {
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--gray-500);
        text-decoration: none;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }
    .report-nav a:hover { background: var(--gray-100); color: var(--gray-900); }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 2rem;
        color: var(--gray-500);
    }
    .empty-state i { font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.4; display: block; }

    @media print {
        .no-print, .sidebar, #sidebar-wrapper, .topbar { display: none !important; }
        body { background: white !important; }
        .section-card { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">

    {{-- Report Header --}}
    <div class="report-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-3 mb-2">
                    @if($student->profile_photo)
                        <img src="{{ asset('storage/'.$student->profile_photo) }}" alt="Photo"
                            style="width:60px;height:60px;border-radius:50%;border:3px solid rgba(255,255,255,0.5);object-fit:cover;">
                    @else
                        <div style="width:60px;height:60px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;border:3px solid rgba(255,255,255,0.3);">
                            {{ strtoupper(substr($student->first_name,0,1).substr($student->last_name,0,1)) }}
                        </div>
                    @endif
                    <div>
                        <h2 class="mb-0" style="font-weight:800;font-size:1.6rem;">
                            {{ $student->first_name }} {{ $student->last_name }}
                        </h2>
                        <div style="opacity:0.8;font-size:0.9rem;">Student ID #{{ $student->id }} &bull; {{ $student->email }}</div>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    @php
                        $statusLabels = ['0'=>['Pending','pill-warning'],'1'=>['In Progress','pill-primary'],'2'=>['Completed','pill-success']];
                        $cs = $statusLabels[$student->course_status] ?? ['Unknown','pill-secondary'];
                    @endphp
                    <span class="status-pill {{ $cs[1] }}"><i class="fas fa-graduation-cap"></i> {{ $cs[0] }}</span>

                    @if($student->theory_status == 'completed')
                        <span class="status-pill pill-success"><i class="fas fa-book"></i> Theory Done</span>
                    @elseif($student->theory_status == 'in_progress')
                        <span class="status-pill pill-primary"><i class="fas fa-book"></i> Theory In Progress</span>
                    @else
                        <span class="status-pill pill-secondary"><i class="fas fa-book"></i> Theory Pending</span>
                    @endif

                    @if($student->practical_status == 'completed')
                        <span class="status-pill pill-success"><i class="fas fa-car"></i> Practical Done</span>
                    @elseif($student->practical_status == 'assigned')
                        <span class="status-pill pill-primary"><i class="fas fa-car"></i> Practical Assigned</span>
                    @elseif($student->practical_status == 'failed')
                        <span class="status-pill pill-danger"><i class="fas fa-car"></i> Practical Failed</span>
                    @else
                        <span class="status-pill pill-secondary"><i class="fas fa-car"></i> Practical Pending</span>
                    @endif

                    @if($certificate)
                        <span class="status-pill pill-purple"><i class="fas fa-certificate"></i> Certified</span>
                    @endif
                </div>
            </div>
            <div class="no-print d-flex flex-column gap-2" style="text-align:right;">
                <a href="{{ route('admin.reports.students.pdf', $student->id) }}" class="btn btn-sm" style="background:rgba(255,255,255,0.2);color:white;border-radius:8px;" target="_blank">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </a>
                <a href="{{ route('admin.reports.students.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,0.15);color:white;border-radius:8px;">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <div style="font-size:0.75rem;opacity:0.6;margin-top:4px;">Generated: {{ date('M d, Y h:i A') }}</div>
            </div>
        </div>
    </div>

    {{-- Quick Navigation --}}
    <div class="report-nav no-print">
        <a href="#sec-profile"><i class="fas fa-user"></i> Profile</a>
        <a href="#sec-course"><i class="fas fa-book-open"></i> Course Structure</a>
        <a href="#sec-instructor"><i class="fas fa-chalkboard-teacher"></i> Instructor</a>
        <a href="#sec-theory"><i class="fas fa-book"></i> Theory Flow</a>
        <a href="#sec-practical"><i class="fas fa-car"></i> Practical Flow</a>
        <a href="#sec-feedback"><i class="fas fa-comments"></i> Feedback</a>
        <a href="#sec-hours"><i class="fas fa-clock"></i> Working Hours</a>
        <a href="#sec-payment"><i class="fas fa-credit-card"></i> Payment</a>
        <a href="#sec-completion"><i class="fas fa-flag-checkered"></i> Completion</a>
    </div>

    {{-- Summary Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="stat-box blue">
                <div class="stat-value">{{ $courseProgress['theory']['percentage'] }}%</div>
                <div class="stat-label">Theory Progress</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-box green">
                <div class="stat-value">{{ $courseProgress['practical']['percentage'] }}%</div>
                <div class="stat-label">Practical Progress</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-box yellow">
                <div class="stat-value">{{ $courseProgress['theory']['completed'] + $courseProgress['practical']['completed'] }}h</div>
                <div class="stat-label">Total Hours Done</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-box orange">
                <div class="stat-value">${{ number_format($totalPaid, 0) }}</div>
                <div class="stat-label">Amount Paid</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-box red">
                <div class="stat-value">${{ number_format($pendingPayments, 0) }}</div>
                <div class="stat-label">Balance Due</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-box purple">
                <div class="stat-value">{{ $certificate ? 'Yes' : 'No' }}</div>
                <div class="stat-label">Certificate</div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         SECTION 1 — STUDENT PROFILE & REGISTRATION
    ============================================================ --}}
    <div class="section-card" id="sec-profile">
        <div class="card-header-custom">
            <div class="section-icon" style="background:#3b82f6;"><i class="fas fa-user"></i></div>
            <div>
                <div class="section-title">Student Profile & Registration</div>
                <div class="section-subtitle">Personal information and enrollment details</div>
            </div>
        </div>
        <div class="card-body-custom">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;font-weight:700;">Personal Information</h6>
                    <table class="info-table w-100">
                        <tr><td>Full Name</td><td><strong>{{ $student->first_name }} {{ $student->last_name }}</strong></td></tr>
                        <tr><td>Email</td><td>{{ $student->email }}</td></tr>
                        <tr><td>Phone</td><td>{{ $student->student_contact ?: '—' }}</td></tr>
                        <tr><td>Date of Birth</td><td>{{ $student->student_dob ? \Carbon\Carbon::parse($student->student_dob)->format('M d, Y') : '—' }}</td></tr>
                        <tr><td>Address</td><td>{{ $student->address ?: '—' }}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-3" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;font-weight:700;">Enrollment Information</h6>
                    <table class="info-table w-100">
                        <tr><td>Enrollment Date</td><td>{{ $student->joining_date ? \Carbon\Carbon::parse($student->joining_date)->format('M d, Y') : '—' }}</td></tr>
                        <tr><td>Completion Date</td><td>{{ $student->completion_date ? \Carbon\Carbon::parse($student->completion_date)->format('M d, Y') : 'Not completed' }}</td></tr>
                        <tr><td>Course Status</td>
                            <td>
                                @php $s = $statusLabels[$student->course_status] ?? ['Unknown','pill-secondary']; @endphp
                                <span class="status-pill {{ $s[1] }}">{{ $s[0] }}</span>
                            </td>
                        </tr>
                        <tr><td>Payment Status</td>
                            <td>
                                @php
                                    $payMap = ['0'=>['Unpaid','pill-danger'],'1'=>['Pending','pill-warning'],'2'=>['Failed','pill-danger'],'3'=>['Paid','pill-success'],'paid'=>['Paid','pill-success'],'partial'=>['Partial','pill-warning'],'pending'=>['Pending','pill-warning']];
                                    $ps = $payMap[(string)$student->payment_status] ?? ['Unknown','pill-secondary'];
                                @endphp
                                <span class="status-pill {{ $ps[1] }}">{{ $ps[0] }}</span>
                            </td>
                        </tr>
                        <tr><td>Student ID #</td><td><strong>#{{ $student->id }}</strong></td></tr>
                    </table>
                </div>
                @if($student->parent)
                <div class="col-12">
                    <hr style="border-color:var(--gray-200);">
                    <h6 class="text-muted mb-3" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;font-weight:700;">Parent / Guardian</h6>
                    <div class="row g-3">
                        <div class="col-md-3"><div class="text-muted" style="font-size:0.78rem;">Name</div><div class="fw-600">{{ $student->parent->name }}</div></div>
                        <div class="col-md-3"><div class="text-muted" style="font-size:0.78rem;">Email</div><div>{{ $student->parent->email }}</div></div>
                        <div class="col-md-3"><div class="text-muted" style="font-size:0.78rem;">Phone</div><div>{{ $student->parent->contact ?? '—' }}</div></div>
                        <div class="col-md-3"><div class="text-muted" style="font-size:0.78rem;">Address</div><div>{{ $student->parent->address ?? '—' }}</div></div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ============================================================
         SECTION 2 — COURSE STRUCTURE MAPPING
    ============================================================ --}}
    <div class="section-card" id="sec-course">
        <div class="card-header-custom">
            <div class="section-icon" style="background:#8b5cf6;"><i class="fas fa-book-open"></i></div>
            <div>
                <div class="section-title">Course Structure Mapping</div>
                <div class="section-subtitle">Theory & practical class breakdown and lesson plan curriculum</div>
            </div>
        </div>
        <div class="card-body-custom">
            @if($student->course)
            {{-- Course Overview --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="stat-box blue">
                        <div class="stat-value">{{ $student->course->total_theory_classes ?? '—' }}</div>
                        <div class="stat-label">Total Theory Classes</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-box green">
                        <div class="stat-value">{{ $student->course->total_practical_classes ?? '—' }}</div>
                        <div class="stat-label">Total Practical Classes</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-box yellow">
                        <div class="stat-value">{{ $student->course->theory_hours ?? 0 }}h</div>
                        <div class="stat-label">Theory Hours Required</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box orange">
                        <div class="stat-value">{{ $student->course->practical_hours ?? 0 }}h</div>
                        <div class="stat-label">Practical Hours Required</div>
                    </div>
                </div>
            </div>

            {{-- Course Details --}}
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <table class="info-table w-100">
                        <tr><td>Course Name</td><td><strong>{{ $student->course->course_name }}</strong></td></tr>
                        <tr><td>Course Type</td><td class="text-capitalize">{{ $student->course->course_type }}</td></tr>
                        <tr><td>Course Fee</td><td><strong>${{ number_format($student->course->course_price, 2) }}</strong></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="info-table w-100">
                        <tr><td>Theory Completed</td><td><strong>{{ $student->hours_theory ?? 0 }}h</strong> / {{ $student->course->theory_hours ?? 0 }}h</td></tr>
                        <tr><td>Practical Completed</td><td><strong>{{ $student->hours_practical ?? 0 }}h</strong> / {{ $student->course->practical_hours ?? 0 }}h</td></tr>
                        <tr><td>Classes Taken</td><td>
                            Theory: <strong>{{ $courseProgress['theory']['classes_completed'] }}</strong>/{{ $courseProgress['theory']['classes_total'] }} &nbsp;|&nbsp;
                            Practical: <strong>{{ $courseProgress['practical']['classes_completed'] }}</strong>/{{ $courseProgress['practical']['classes_total'] }}
                        </td></tr>
                    </table>
                </div>
            </div>

            {{-- Lesson Plan Curriculum --}}
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 style="font-weight:700;color:var(--gray-700);margin-bottom:1rem;">
                        <i class="fas fa-book text-primary me-2"></i>Theory Classes Curriculum
                        <span class="ms-2 status-pill pill-primary" style="font-size:0.7rem;">{{ $theoryLessonPlans->count() }} classes</span>
                    </h6>
                    @forelse($theoryLessonPlans as $plan)
                        @php
                            $classNum = $plan->pivot->class_order;
                            $classState = $student->theory_status == 'completed' ? 'done'
                                : ($classNum <= $courseProgress['theory']['classes_completed'] ? 'done'
                                    : ($classNum == $courseProgress['theory']['classes_completed'] + 1 ? 'active' : 'pending'));
                        @endphp
                        <div class="class-item">
                            <div class="class-num {{ $classState }}">{{ $classNum }}</div>
                            <div class="class-content">
                                <div class="class-title">{{ $plan->title }}</div>
                                <div class="class-meta">
                                    Theory Class #{{ $classNum }}
                                    &nbsp;&bull;&nbsp;
                                    @if($classState == 'done') <span style="color:var(--success);font-weight:600;">Completed</span>
                                    @elseif($classState == 'active') <span style="color:var(--primary);font-weight:600;">In Progress</span>
                                    @else <span style="color:var(--gray-500);">Upcoming</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state"><i class="fas fa-book-open"></i><small>No theory lesson plans configured for this course</small></div>
                    @endforelse
                </div>
                <div class="col-md-6">
                    <h6 style="font-weight:700;color:var(--gray-700);margin-bottom:1rem;">
                        <i class="fas fa-car text-success me-2"></i>Practical Classes Curriculum
                        <span class="ms-2 status-pill pill-success" style="font-size:0.7rem;">{{ $practicalLessonPlans->count() }} classes</span>
                    </h6>
                    @forelse($practicalLessonPlans as $plan)
                        @php
                            $classNum = $plan->pivot->class_order;
                            $classState = $student->practical_status == 'completed' ? 'done'
                                : ($classNum <= $courseProgress['practical']['classes_completed'] ? 'done'
                                    : ($classNum == $courseProgress['practical']['classes_completed'] + 1 ? 'active' : 'pending'));
                        @endphp
                        <div class="class-item">
                            <div class="class-num {{ $classState }}">{{ $classNum }}</div>
                            <div class="class-content">
                                <div class="class-title">{{ $plan->title }}</div>
                                <div class="class-meta">
                                    Practical Class #{{ $classNum }}
                                    @if($student->course->practical_hours && $student->course->total_practical_classes)
                                        &nbsp;&bull;&nbsp; Duration: {{ round($student->course->practical_hours / $student->course->total_practical_classes, 1) }}h
                                    @endif
                                    &nbsp;&bull;&nbsp;
                                    @if($classState == 'done') <span style="color:var(--success);font-weight:600;">Completed</span>
                                    @elseif($classState == 'active') <span style="color:var(--primary);font-weight:600;">In Progress</span>
                                    @else <span style="color:var(--gray-500);">Upcoming</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state"><i class="fas fa-car"></i><small>No practical lesson plans configured for this course</small></div>
                    @endforelse
                </div>
            </div>
            @else
                <div class="empty-state"><i class="fas fa-book-open"></i><p>No course assigned to this student.</p></div>
            @endif
        </div>
    </div>

    {{-- ============================================================
         SECTION 3 — INSTRUCTOR ASSIGNMENT
    ============================================================ --}}
    <div class="section-card" id="sec-instructor">
        <div class="card-header-custom">
            <div class="section-icon" style="background:#f97316;"><i class="fas fa-chalkboard-teacher"></i></div>
            <div>
                <div class="section-title">Instructor Assignment</div>
                <div class="section-subtitle">Assigned instructor details and assignment information</div>
            </div>
        </div>
        <div class="card-body-custom">
            @if($student->instructor)
            <div class="row g-4 align-items-center">
                <div class="col-auto">
                    <div style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,#f97316,#fb923c);display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:700;color:white;">
                        {{ strtoupper(substr($student->instructor->instructor_name,0,1)) }}
                    </div>
                </div>
                <div class="col">
                    <h4 style="font-weight:700;margin-bottom:4px;">{{ $student->instructor->instructor_name }}</h4>
                    <div style="color:var(--gray-500);font-size:0.9rem;">
                        @if($student->instructor->email) <span><i class="fas fa-envelope me-1"></i>{{ $student->instructor->email }}</span> @endif
                        @if($student->instructor->contact) &nbsp;&bull;&nbsp; <span><i class="fas fa-phone me-1"></i>{{ $student->instructor->contact }}</span> @endif
                        @if($student->instructor->license_number) &nbsp;&bull;&nbsp; <span><i class="fas fa-id-card me-1"></i>License: {{ $student->instructor->license_number }}</span> @endif
                    </div>
                </div>
            </div>
            <hr style="margin:1.5rem 0;border-color:var(--gray-200);">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-muted mb-1" style="font-size:0.78rem;font-weight:600;text-transform:uppercase;">Assignment Date</div>
                    <div style="font-weight:600;">{{ $student->joining_date ? \Carbon\Carbon::parse($student->joining_date)->format('M d, Y') : 'Not recorded' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted mb-1" style="font-size:0.78rem;font-weight:600;text-transform:uppercase;">Instructor Status</div>
                    <span class="status-pill {{ $student->instructor->is_active ? 'pill-success' : 'pill-danger' }}">
                        {{ $student->instructor->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="col-md-4">
                    <div class="text-muted mb-1" style="font-size:0.78rem;font-weight:600;text-transform:uppercase;">Course Assigned</div>
                    <div style="font-weight:600;">{{ $student->course->course_name ?? '—' }}</div>
                </div>
            </div>
            @else
                <div class="empty-state"><i class="fas fa-chalkboard-teacher"></i><p>No instructor assigned to this student yet.</p></div>
            @endif
        </div>
    </div>

    {{-- ============================================================
         SECTION 4 — THEORY CLASSES FLOW
    ============================================================ --}}
    <div class="section-card" id="sec-theory">
        <div class="card-header-custom">
            <div class="section-icon" style="background:#3b82f6;"><i class="fas fa-book"></i></div>
            <div>
                <div class="section-title">Theory Classes Flow</div>
                <div class="section-subtitle">Scheduled theory sessions, class completion and theory status</div>
            </div>
        </div>
        <div class="card-body-custom">
            {{-- Theory Progress Bar --}}
            <div class="prog-wrap mb-4">
                <div class="prog-header">
                    <span class="prog-label"><i class="fas fa-book me-1 text-primary"></i> Theory Progress</span>
                    <span class="prog-pct" style="color:var(--primary);">{{ $courseProgress['theory']['percentage'] }}%</span>
                </div>
                <div class="prog-bar-bg">
                    <div class="prog-bar-fill prog-bar-theory" style="width:{{ $courseProgress['theory']['percentage'] }}%;"></div>
                </div>
                <div class="prog-meta">
                    {{ $courseProgress['theory']['completed'] }}h completed of {{ $courseProgress['theory']['total'] }}h required
                    &nbsp;&bull;&nbsp; {{ $courseProgress['theory']['classes_completed'] }} of {{ $courseProgress['theory']['classes_total'] }} classes done
                    &nbsp;&bull;&nbsp;
                    Status:
                    @if($student->theory_status == 'completed') <span style="color:var(--success);font-weight:600;">Completed{{ $student->theory_completion_date ? ' on '.\Carbon\Carbon::parse($student->theory_completion_date)->format('M d, Y') : '' }}</span>
                    @elseif($student->theory_status == 'in_progress') <span style="color:var(--primary);font-weight:600;">In Progress</span>
                    @else <span style="color:var(--gray-500);">Pending</span>
                    @endif
                </div>
            </div>

            {{-- Theory Schedule Table --}}
            <h6 style="font-weight:700;color:var(--gray-700);margin-bottom:1rem;">
                <i class="fas fa-calendar-alt me-2 text-primary"></i>Theory Class Schedule
                <span class="ms-2 status-pill pill-primary" style="font-size:0.7rem;">{{ $theorySchedules->count() }} sessions</span>
            </h6>
            @if($theorySchedules->isNotEmpty())
                <div class="table-responsive">
                    <table class="sched-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Duration</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($theorySchedules as $idx => $sched)
                                @php
                                    $schedDate = \Carbon\Carbon::parse($sched->date);
                                    $isPast = $schedDate->isPast();
                                    $isToday = $schedDate->isToday();
                                    $startT = \Carbon\Carbon::parse($sched->start_time);
                                    $endT = \Carbon\Carbon::parse($sched->end_time);
                                    $dur = $endT->diffInMinutes($startT);
                                    $durH = round($dur / 60, 1);
                                @endphp
                                <tr>
                                    <td><strong>{{ $idx + 1 }}</strong></td>
                                    <td>{{ $schedDate->format('M d, Y') }}</td>
                                    <td>{{ $schedDate->format('l') }}</td>
                                    <td>{{ $startT->format('h:i A') }}</td>
                                    <td>{{ $endT->format('h:i A') }}</td>
                                    <td>{{ $durH }}h</td>
                                    <td>
                                        @if($student->theory_status == 'completed')
                                            <span class="status-pill pill-success" style="font-size:0.72rem;">Completed</span>
                                        @elseif($isPast)
                                            @if($idx < $courseProgress['theory']['classes_completed'])
                                                <span class="status-pill pill-success" style="font-size:0.72rem;">Done</span>
                                            @else
                                                <span class="status-pill pill-warning" style="font-size:0.72rem;">Attended?</span>
                                            @endif
                                        @elseif($isToday)
                                            <span class="status-pill pill-info" style="font-size:0.72rem;">Today</span>
                                        @else
                                            <span class="status-pill pill-secondary" style="font-size:0.72rem;">Upcoming</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state"><i class="fas fa-calendar-alt"></i><small>No theory schedules found for this course</small></div>
            @endif

            @if($student->theory_status == 'completed' && $student->theory_completion_date)
                <div class="mt-3 p-3" style="background:#d1fae5;border-radius:10px;border-left:4px solid var(--success);">
                    <strong><i class="fas fa-check-circle text-success me-2"></i>Theory Completed</strong>
                    <div style="font-size:0.85rem;margin-top:4px;">All theory classes completed on {{ \Carbon\Carbon::parse($student->theory_completion_date)->format('M d, Y') }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================================
         SECTION 5 — PRACTICAL ASSIGNMENT & FLOW
    ============================================================ --}}
    <div class="section-card" id="sec-practical">
        <div class="card-header-custom">
            <div class="section-icon" style="background:#10b981;"><i class="fas fa-car"></i></div>
            <div>
                <div class="section-title">Practical Assignment & Flow</div>
                <div class="section-subtitle">Assigned practical session, duration, and completion status</div>
            </div>
        </div>
        <div class="card-body-custom">
            {{-- Practical Progress Bar --}}
            <div class="prog-wrap mb-4">
                <div class="prog-header">
                    <span class="prog-label"><i class="fas fa-car me-1 text-success"></i> Practical Progress</span>
                    <span class="prog-pct" style="color:var(--success);">{{ $courseProgress['practical']['percentage'] }}%</span>
                </div>
                <div class="prog-bar-bg">
                    <div class="prog-bar-fill prog-bar-practical" style="width:{{ $courseProgress['practical']['percentage'] }}%;"></div>
                </div>
                <div class="prog-meta">
                    {{ $courseProgress['practical']['completed'] }}h completed of {{ $courseProgress['practical']['total'] }}h required
                    &nbsp;&bull;&nbsp;
                    Status:
                    @php
                        $practicalStatusMap = [
                            'pending'      => ['Pending', 'var(--gray-500)'],
                            'assigned'     => ['Assigned', 'var(--primary)'],
                            'completed'    => ['Completed', 'var(--success)'],
                            'failed'       => ['Failed', 'var(--danger)'],
                            'not_appeared' => ['Not Appeared', 'var(--warning)'],
                        ];
                        $ps2 = $practicalStatusMap[$student->practical_status] ?? ['Unknown', 'var(--gray-500)'];
                    @endphp
                    <span style="color:{{ $ps2[1] }};font-weight:600;">{{ $ps2[0] }}</span>
                    @if($student->practical_completion_date)
                        on {{ \Carbon\Carbon::parse($student->practical_completion_date)->format('M d, Y') }}
                    @endif
                </div>
            </div>

            {{-- Assigned Practical Slot --}}
            @if($student->practicalSchedule)
                @php $slot = $student->practicalSchedule; @endphp
                <div class="practical-slot">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 style="font-weight:700;margin:0;color:#065f46;"><i class="fas fa-calendar-check me-2"></i>Assigned Practical Slot</h6>
                        @php
                            $slotPillMap = ['pending'=>'pill-warning','assigned'=>'pill-primary','completed'=>'pill-success','failed'=>'pill-danger','not_appeared'=>'pill-warning'];
                        @endphp
                        <span class="status-pill {{ $slotPillMap[$student->practical_status] ?? 'pill-secondary' }}">
                            {{ ucfirst(str_replace('_', ' ', $student->practical_status)) }}
                        </span>
                    </div>
                    <div class="slot-grid">
                        <div class="slot-item">
                            <div class="slot-label">Date</div>
                            <div class="slot-value">{{ \Carbon\Carbon::parse($slot->date)->format('l, M d, Y') }}</div>
                        </div>
                        <div class="slot-item">
                            <div class="slot-label">Start Time</div>
                            <div class="slot-value">{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}</div>
                        </div>
                        <div class="slot-item">
                            <div class="slot-label">End Time</div>
                            <div class="slot-value">{{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}</div>
                        </div>
                        <div class="slot-item">
                            <div class="slot-label">Duration</div>
                            <div class="slot-value" style="color:#047857;">{{ $practicalDuration }}h ({{ $practicalDuration * 60 }} min)</div>
                        </div>
                        <div class="slot-item">
                            <div class="slot-label">Instructor</div>
                            <div class="slot-value">{{ $slot->instructor->instructor_name ?? '—' }}</div>
                        </div>
                        <div class="slot-item">
                            <div class="slot-label">Session Type</div>
                            <div class="slot-value text-capitalize">{{ $slot->session_type }}</div>
                        </div>
                    </div>
                </div>
            @else
                <div class="empty-state" style="background:var(--gray-50);border-radius:12px;border:1px dashed var(--gray-200);">
                    <i class="fas fa-car"></i>
                    <p style="margin-bottom:4px;font-weight:600;">No Practical Slot Assigned</p>
                    <small>
                        @if($student->theory_status !== 'completed')
                            Student must complete theory classes before practical assignment.
                        @else
                            Theory completed. Awaiting practical slot assignment by instructor.
                        @endif
                    </small>
                </div>
            @endif

            @if($student->practical_status == 'completed' && $student->practical_completion_date)
                <div class="mt-3 p-3" style="background:#d1fae5;border-radius:10px;border-left:4px solid var(--success);">
                    <strong><i class="fas fa-check-circle text-success me-2"></i>Practical Completed</strong>
                    <div style="font-size:0.85rem;margin-top:4px;">Practical session completed on {{ \Carbon\Carbon::parse($student->practical_completion_date)->format('M d, Y') }}</div>
                </div>
            @elseif($student->practical_status == 'failed')
                <div class="mt-3 p-3" style="background:#fee2e2;border-radius:10px;border-left:4px solid var(--danger);">
                    <strong><i class="fas fa-times-circle text-danger me-2"></i>Practical Not Passed</strong>
                    <div style="font-size:0.85rem;margin-top:4px;">Student did not pass the practical session. May need re-scheduling.</div>
                </div>
            @elseif($student->practical_status == 'not_appeared')
                <div class="mt-3 p-3" style="background:#fef3c7;border-radius:10px;border-left:4px solid var(--warning);">
                    <strong><i class="fas fa-exclamation-triangle text-warning me-2"></i>Did Not Appear</strong>
                    <div style="font-size:0.85rem;margin-top:4px;">Student did not appear for the scheduled practical session.</div>
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================================
         SECTION 6 — FEEDBACK RECORDING
    ============================================================ --}}
    <div class="section-card" id="sec-feedback">
        <div class="card-header-custom">
            <div class="section-icon" style="background:#06b6d4;"><i class="fas fa-comments"></i></div>
            <div>
                <div class="section-title">Feedback Recording</div>
                <div class="section-subtitle">Student feedback responses per class and instructor progress reports</div>
            </div>
        </div>
        <div class="card-body-custom">
            {{-- Class Feedback Responses --}}
            @if($feedbackResponses->isNotEmpty())
                <h6 style="font-weight:700;color:var(--gray-700);margin-bottom:1rem;">
                    <i class="fas fa-poll me-2 text-info"></i>Class Feedback Responses
                </h6>
                @foreach($feedbackResponses as $classOrder => $responses)
                    <div class="feedback-class">
                        <div class="feedback-class-header">
                            <i class="fas fa-book me-2 text-primary"></i>Class #{{ $classOrder }} Feedback
                            <span class="ms-2 text-muted" style="font-weight:400;">({{ $responses->count() }} responses)</span>
                        </div>
                        @foreach($responses as $resp)
                            <div class="feedback-item">
                                <div class="fb-q">
                                    {{ $resp->question->question_text ?? 'Question not found' }}
                                    @if($resp->comments)
                                        <div class="fb-comment">"{{ $resp->comments }}"</div>
                                    @endif
                                </div>
                                <div class="fb-response {{ $resp->response ? 'fb-yes' : 'fb-no' }}">
                                    <i class="fas {{ $resp->response ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                    {{ $resp->response ? 'Yes' : 'No' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @else
                <div class="empty-state" style="padding:1.5rem;background:var(--gray-50);border-radius:12px;margin-bottom:1.5rem;">
                    <i class="fas fa-poll"></i>
                    <p>No feedback responses recorded for this student yet.</p>
                </div>
            @endif

            {{-- Instructor Progress Reports --}}
            @if($progressReports->isNotEmpty())
                <h6 style="font-weight:700;color:var(--gray-700);margin:1.5rem 0 1rem;">
                    <i class="fas fa-clipboard-check me-2 text-success"></i>Instructor Progress Reports
                    <span class="ms-2 status-pill pill-success" style="font-size:0.7rem;">{{ $progressReports->count() }} reports</span>
                </h6>
                @foreach($progressReports as $report)
                    <div class="feedback-class mb-3">
                        <div class="feedback-class-header d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-file-alt me-2"></i>Report — {{ $report->created_at->format('M d, Y') }}</span>
                            <div class="d-flex align-items-center gap-2">
                                @if($report->instructor)
                                    <span class="text-muted" style="font-size:0.8rem;">by {{ $report->instructor->instructor_name }}</span>
                                @endif
                                @if($report->rating)
                                    <span class="status-pill pill-primary" style="font-size:0.7rem;"><i class="fas fa-star"></i> {{ $report->rating }}/5</span>
                                @endif
                            </div>
                        </div>
                        <div style="padding:12px 14px;">
                            @if($report->performance_notes)
                                <div class="mb-2">
                                    <div style="font-size:0.75rem;font-weight:700;color:var(--primary);text-transform:uppercase;margin-bottom:4px;">Performance Notes</div>
                                    <div style="font-size:0.88rem;color:var(--gray-700);">{{ $report->performance_notes }}</div>
                                </div>
                            @endif
                            @if($report->areas_of_improvement)
                                <div>
                                    <div style="font-size:0.75rem;font-weight:700;color:var(--warning);text-transform:uppercase;margin-bottom:4px;">Areas for Improvement</div>
                                    <div style="font-size:0.88rem;color:var(--gray-700);">{{ $report->areas_of_improvement }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state" style="padding:1.5rem;background:var(--gray-50);border-radius:12px;">
                    <i class="fas fa-clipboard-check"></i>
                    <p>No instructor progress reports submitted yet.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================================
         SECTION 7 — WORKING HOUR TRACKING
    ============================================================ --}}
    <div class="section-card" id="sec-hours">
        <div class="card-header-custom">
            <div class="section-icon" style="background:#f59e0b;"><i class="fas fa-clock"></i></div>
            <div>
                <div class="section-title">Working Hour Tracking</div>
                <div class="section-subtitle">Session-by-session hour log for theory and practical classes</div>
            </div>
        </div>
        <div class="card-body-custom">
            {{-- Total hours summary --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-box blue">
                        <div class="stat-value">{{ $courseProgress['theory']['completed'] }}h</div>
                        <div class="stat-label">Theory Hours Logged</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-box green">
                        <div class="stat-value">{{ $courseProgress['practical']['completed'] }}h</div>
                        <div class="stat-label">Practical Hours Logged</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-box yellow">
                        <div class="stat-value">{{ $courseProgress['theory']['completed'] + $courseProgress['practical']['completed'] }}h</div>
                        <div class="stat-label">Total Hours Done</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-box orange">
                        @php
                            $hoursRemaining = max(0,
                                ($courseProgress['theory']['total'] - $courseProgress['theory']['completed']) +
                                ($courseProgress['practical']['total'] - $courseProgress['practical']['completed'])
                            );
                        @endphp
                        <div class="stat-value">{{ $hoursRemaining }}h</div>
                        <div class="stat-label">Hours Remaining</div>
                    </div>
                </div>
            </div>

            {{-- Session Log --}}
            @if($courseHoursLog->isNotEmpty())
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 style="font-weight:700;color:var(--gray-700);margin-bottom:1rem;">
                            <i class="fas fa-book me-2 text-primary"></i>Theory Sessions Log
                            @if($theoryHoursLog->isNotEmpty())
                                <span class="ms-1 status-pill pill-primary" style="font-size:0.7rem;">{{ $theoryHoursLog->sum('hours') }}h total</span>
                            @endif
                        </h6>
                        @forelse($theoryHoursLog as $entry)
                            <div class="hours-row">
                                <div class="hours-date"><i class="far fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($entry->date)->format('M d, Y') }}</div>
                                <span class="hours-type-chip chip-theory">Theory</span>
                                <div style="flex:1;text-align:right;font-weight:600;color:var(--primary);">{{ $entry->hours }}h</div>
                            </div>
                        @empty
                            <div class="empty-state" style="padding:1rem;background:var(--gray-50);border-radius:8px;"><i class="fas fa-book"></i><small>No theory hours logged</small></div>
                        @endforelse
                    </div>
                    <div class="col-md-6">
                        <h6 style="font-weight:700;color:var(--gray-700);margin-bottom:1rem;">
                            <i class="fas fa-car me-2 text-success"></i>Practical Sessions Log
                            @if($practicalHoursLog->isNotEmpty())
                                <span class="ms-1 status-pill pill-success" style="font-size:0.7rem;">{{ $practicalHoursLog->sum('hours') }}h total</span>
                            @endif
                        </h6>
                        @forelse($practicalHoursLog as $entry)
                            <div class="hours-row">
                                <div class="hours-date"><i class="far fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($entry->date)->format('M d, Y') }}</div>
                                <span class="hours-type-chip chip-practical">Practical</span>
                                <div style="flex:1;text-align:right;font-weight:600;color:var(--success);">{{ $entry->hours }}h</div>
                            </div>
                        @empty
                            <div class="empty-state" style="padding:1rem;background:var(--gray-50);border-radius:8px;"><i class="fas fa-car"></i><small>No practical hours logged</small></div>
                        @endforelse
                    </div>
                </div>
            @else
                <div class="empty-state" style="background:var(--gray-50);border-radius:12px;border:1px dashed var(--gray-200);">
                    <i class="fas fa-clock"></i>
                    <p style="margin-bottom:4px;">No hour log entries found.</p>
                    <small>Hours are tracked manually. Current totals: Theory {{ $courseProgress['theory']['completed'] }}h, Practical {{ $courseProgress['practical']['completed'] }}h</small>
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================================
         SECTION 8 — PAYMENT FLOW
    ============================================================ --}}
    <div class="section-card" id="sec-payment">
        <div class="card-header-custom">
            <div class="section-icon" style="background:#6366f1;"><i class="fas fa-credit-card"></i></div>
            <div>
                <div class="section-title">Payment Flow</div>
                <div class="section-subtitle">Invoices, payments, and installment plan details</div>
            </div>
        </div>
        <div class="card-body-custom">
            {{-- Payment Summary --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-box blue">
                        <div class="stat-value">${{ number_format($totalBilled, 0) }}</div>
                        <div class="stat-label">Total Billed</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-box green">
                        <div class="stat-value">${{ number_format($totalPaid, 0) }}</div>
                        <div class="stat-label">Amount Paid</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-box red">
                        <div class="stat-value">${{ number_format($pendingPayments, 0) }}</div>
                        <div class="stat-label">Balance Due</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-box {{ $pendingPayments <= 0 ? 'green' : 'yellow' }}">
                        <div class="stat-value" style="font-size:1rem;">{{ $pendingPayments <= 0 ? 'Paid ✓' : 'Pending' }}</div>
                        <div class="stat-label">Payment Status</div>
                    </div>
                </div>
            </div>

            {{-- Invoices --}}
            @forelse($student->invoices as $invoice)
                <div class="payment-invoice">
                    <div class="invoice-header">
                        <div>
                            <div class="invoice-title">Invoice #{{ $invoice->invoice_number }}</div>
                            <div style="font-size:0.78rem;color:var(--gray-500);">{{ $invoice->created_at->format('M d, Y') }} &bull; {{ $invoice->course->course_name ?? '—' }}</div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <strong style="font-size:1rem;">${{ number_format($invoice->amount, 2) }}</strong>
                            <span class="status-pill {{ $invoice->status == 'paid' ? 'pill-success' : 'pill-warning' }}">{{ ucfirst($invoice->status) }}</span>
                        </div>
                    </div>
                    <div class="invoice-body">
                        {{-- Payments --}}
                        @if($invoice->payments->isNotEmpty())
                            <h6 style="font-size:0.8rem;font-weight:700;text-transform:uppercase;color:var(--gray-500);margin-bottom:8px;">Payment Records</h6>
                            <table class="sched-table mb-3">
                                <thead>
                                    <tr>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Transaction ID</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->payments as $pay)
                                        <tr>
                                            <td><strong>${{ number_format($pay->amount, 2) }}</strong></td>
                                            <td>{{ $pay->paymentMethod->name ?? '—' }}</td>
                                            <td><code style="font-size:0.78rem;">{{ $pay->transaction_id ?? '—' }}</code></td>
                                            <td><span class="status-pill {{ $pay->status == 'completed' ? 'pill-success' : 'pill-warning' }}" style="font-size:0.72rem;">{{ ucfirst($pay->status) }}</span></td>
                                            <td>{{ $pay->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                        {{-- Installments --}}
                        @if($invoice->installments->isNotEmpty())
                            <h6 style="font-size:0.8rem;font-weight:700;text-transform:uppercase;color:var(--gray-500);margin-bottom:8px;">Installment Plan</h6>
                            <table class="sched-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Paid Date</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->installments as $idx => $inst)
                                        @php
                                            $instStatus = $inst->status == 'paid' ? 'pill-success'
                                                : ($inst->is_overdue ? 'pill-danger' : 'pill-warning');
                                            $instLabel  = $inst->status == 'paid' ? 'Paid'
                                                : ($inst->is_overdue ? 'Overdue' : ucfirst($inst->status));
                                        @endphp
                                        <tr>
                                            <td>{{ $idx + 1 }}</td>
                                            <td><strong>${{ number_format($inst->amount, 2) }}</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($inst->due_date)->format('M d, Y') }}</td>
                                            <td><span class="status-pill {{ $instStatus }}" style="font-size:0.72rem;">{{ $instLabel }}</span></td>
                                            <td>{{ $inst->paid_at ? \Carbon\Carbon::parse($inst->paid_at)->format('M d, Y') : '—' }}</td>
                                            <td style="font-size:0.78rem;color:var(--gray-500);">{{ $inst->notes ?: '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state" style="background:var(--gray-50);border-radius:12px;border:1px dashed var(--gray-200);">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <p>No payment records found for this student.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ============================================================
         SECTION 9 — COURSE COMPLETION
    ============================================================ --}}
    <div class="section-card" id="sec-completion">
        <div class="card-header-custom">
            <div class="section-icon" style="background:#8b5cf6;"><i class="fas fa-flag-checkered"></i></div>
            <div>
                <div class="section-title">Course Completion</div>
                <div class="section-subtitle">Theory completion, practical completion, overall status and certificate</div>
            </div>
        </div>
        <div class="card-body-custom">
            {{-- Completion Steps --}}
            <div class="row g-4">
                <div class="col-md-7">
                    <h6 style="font-weight:700;color:var(--gray-700);margin-bottom:1.5rem;">Course Progress Milestones</h6>

                    {{-- Step 1: Registration --}}
                    <div class="completion-step">
                        <div class="step-indicator">
                            <div class="step-circle done"><i class="fas fa-user-plus"></i></div>
                            <div class="step-line"></div>
                        </div>
                        <div class="step-body">
                            <div class="step-title">Student Enrolled</div>
                            <div class="step-desc">{{ $student->joining_date ? \Carbon\Carbon::parse($student->joining_date)->format('M d, Y') : 'Enrollment date not recorded' }}</div>
                        </div>
                    </div>

                    {{-- Step 2: Instructor Assigned --}}
                    <div class="completion-step">
                        <div class="step-indicator">
                            <div class="step-circle {{ $student->instructor ? 'done' : 'pending' }}">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="step-line"></div>
                        </div>
                        <div class="step-body">
                            <div class="step-title">Instructor Assigned</div>
                            <div class="step-desc">
                                {{ $student->instructor ? $student->instructor->instructor_name . ' — assigned on enrollment' : 'No instructor assigned' }}
                            </div>
                        </div>
                    </div>

                    {{-- Step 3: Theory --}}
                    <div class="completion-step">
                        <div class="step-indicator">
                            @php $theoryStepClass = $student->theory_status == 'completed' ? 'done' : ($student->theory_status == 'in_progress' ? 'active' : 'pending'); @endphp
                            <div class="step-circle {{ $theoryStepClass }}"><i class="fas fa-book"></i></div>
                            <div class="step-line"></div>
                        </div>
                        <div class="step-body">
                            <div class="step-title">Theory Classes</div>
                            <div class="step-desc">
                                {{ $courseProgress['theory']['completed'] }}h / {{ $courseProgress['theory']['total'] }}h completed
                                ({{ $courseProgress['theory']['percentage'] }}%)
                                @if($student->theory_status == 'completed')
                                    &bull; <span style="color:var(--success);font-weight:600;">Completed {{ $student->theory_completion_date ? 'on '.\Carbon\Carbon::parse($student->theory_completion_date)->format('M d, Y') : '' }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Step 4: Practical --}}
                    <div class="completion-step">
                        <div class="step-indicator">
                            @php
                                $practStepClass = $student->practical_status == 'completed' ? 'done'
                                    : ($student->practical_status == 'assigned' ? 'active' : 'pending');
                            @endphp
                            <div class="step-circle {{ $practStepClass }}"><i class="fas fa-car"></i></div>
                            <div class="step-line"></div>
                        </div>
                        <div class="step-body">
                            <div class="step-title">Practical Session</div>
                            <div class="step-desc">
                                Status: {{ ucfirst(str_replace('_', ' ', $student->practical_status)) }}
                                @if($student->practical_completion_date)
                                    &bull; <span style="color:var(--success);font-weight:600;">Completed on {{ \Carbon\Carbon::parse($student->practical_completion_date)->format('M d, Y') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Step 5: Course Completion --}}
                    <div class="completion-step">
                        <div class="step-indicator">
                            @php $courseStepClass = $student->course_status == '2' ? 'done' : ($student->course_status == '1' ? 'active' : 'pending'); @endphp
                            <div class="step-circle {{ $courseStepClass }}"><i class="fas fa-graduation-cap"></i></div>
                            <div class="step-line"></div>
                        </div>
                        <div class="step-body">
                            <div class="step-title">Course Completion</div>
                            <div class="step-desc">
                                @if($student->course_status == '2')
                                    <span style="color:var(--success);font-weight:600;">Course completed{{ $student->completion_date ? ' on '.\Carbon\Carbon::parse($student->completion_date)->format('M d, Y') : '' }}</span>
                                @elseif($student->course_status == '1')
                                    Course in progress
                                @else
                                    Course not yet started
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Step 6: Certificate --}}
                    <div class="completion-step">
                        <div class="step-indicator">
                            <div class="step-circle {{ $certificate ? 'done' : 'pending' }}"><i class="fas fa-certificate"></i></div>
                        </div>
                        <div class="step-body">
                            <div class="step-title">Certificate Issued</div>
                            <div class="step-desc">
                                @if($certificate)
                                    <span style="color:var(--success);font-weight:600;">Issued on {{ \Carbon\Carbon::parse($certificate->issue_date)->format('M d, Y') }}</span>
                                    &bull; Cert # {{ $certificate->certificate_number }}
                                @else
                                    Certificate not yet issued
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Summary box --}}
                <div class="col-md-5">
                    <div style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);border:1px solid var(--gray-200);border-radius:14px;padding:1.5rem;">
                        <h6 style="font-weight:700;color:var(--gray-900);margin-bottom:1.2rem;">Completion Summary</h6>
                        <div class="prog-wrap">
                            <div class="prog-header">
                                <span class="prog-label">Theory</span>
                                <span class="prog-pct" style="color:var(--primary);">{{ $courseProgress['theory']['percentage'] }}%</span>
                            </div>
                            <div class="prog-bar-bg"><div class="prog-bar-fill prog-bar-theory" style="width:{{ $courseProgress['theory']['percentage'] }}%;"></div></div>
                        </div>
                        <div class="prog-wrap">
                            <div class="prog-header">
                                <span class="prog-label">Practical</span>
                                <span class="prog-pct" style="color:var(--success);">{{ $courseProgress['practical']['percentage'] }}%</span>
                            </div>
                            <div class="prog-bar-bg"><div class="prog-bar-fill prog-bar-practical" style="width:{{ $courseProgress['practical']['percentage'] }}%;"></div></div>
                        </div>
                        @php
                            $overallPct = round(($courseProgress['theory']['percentage'] + $courseProgress['practical']['percentage']) / 2);
                        @endphp
                        <div class="prog-wrap">
                            <div class="prog-header">
                                <span class="prog-label">Overall</span>
                                <span class="prog-pct" style="color:var(--purple);">{{ $overallPct }}%</span>
                            </div>
                            <div class="prog-bar-bg">
                                <div class="prog-bar-fill" style="width:{{ $overallPct }}%;background:linear-gradient(90deg,#8b5cf6,#a78bfa);"></div>
                            </div>
                        </div>

                        <hr style="margin:1rem 0;border-color:var(--gray-200);">

                        @if($certificate)
                            <div style="background:#ede9fe;border-radius:10px;padding:1rem;text-align:center;">
                                <div style="font-size:2rem;margin-bottom:6px;">🏆</div>
                                <div style="font-weight:700;color:#5b21b6;font-size:0.95rem;">Certificate Issued</div>
                                <div style="font-size:0.8rem;color:#7c3aed;margin-top:4px;">#{{ $certificate->certificate_number }}</div>
                                <div style="font-size:0.78rem;color:#8b5cf6;margin-top:2px;">{{ \Carbon\Carbon::parse($certificate->issue_date)->format('M d, Y') }}</div>
                                @if($certificate->verification_url)
                                    <a href="{{ $certificate->verification_url }}" class="btn btn-sm mt-2" style="background:#8b5cf6;color:white;border-radius:8px;font-size:0.78rem;" target="_blank">
                                        <i class="fas fa-external-link-alt me-1"></i>Verify
                                    </a>
                                @endif
                            </div>
                        @else
                            <div style="background:var(--gray-100);border-radius:10px;padding:1rem;text-align:center;">
                                <div style="font-size:1.5rem;margin-bottom:6px;opacity:0.4;">🏆</div>
                                <div style="font-weight:600;color:var(--gray-500);font-size:0.88rem;">Certificate Not Yet Issued</div>
                                <div style="font-size:0.78rem;color:var(--gray-500);margin-top:4px;">
                                    @if($student->course_status != '2')
                                        Complete the course to be eligible for certificate.
                                    @else
                                        Course completed. Certificate pending issuance.
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
// Smooth scroll for navigation
document.querySelectorAll('.report-nav a').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        var target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
</script>
@endsection
