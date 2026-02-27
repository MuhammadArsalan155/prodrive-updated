@extends('layouts.master')

@section('styles')
<style>
    /* ── Page background ── */
    body { background: #f0f4f8; }
    .content-wrapper { background: #f0f4f8 !important; }

    /* ── Page header ── */
    .page-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
        border-radius: 16px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
        color: white;
    }
    .page-header::after {
        content: '';
        position: absolute;
        top: -40%;
        right: -5%;
        width: 260px;
        height: 260px;
        background: rgba(255,255,255,.06);
        border-radius: 50%;
        pointer-events: none;
    }
    .page-header h1 {
        font-size: 1.55rem;
        font-weight: 800;
        margin: 0 0 4px;
        letter-spacing: -.2px;
    }
    .page-header .sub { font-size: .82rem; opacity: .75; }
    .header-actions .btn {
        border-radius: 10px;
        padding: .5rem 1.1rem;
        font-size: .82rem;
        font-weight: 600;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: transform .15s, box-shadow .15s;
    }
    .header-actions .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.2); }
    .btn-white-outline {
        background: rgba(255,255,255,.15);
        color: white;
        border: 1.5px solid rgba(255,255,255,.4) !important;
    }
    .btn-white-outline:hover { background: rgba(255,255,255,.25); color: white; }
    .btn-white-solid { background: white; color: #1e3a5f; }
    .btn-white-solid:hover { background: #f0f4f8; color: #1e3a5f; }

    /* ── Stat cards ── */
    .stat-card {
        background: white;
        border-radius: 14px;
        padding: 1.15rem 1.3rem;
        border: 1px solid #e2e8f0;
        border-top: 3px solid transparent;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        transition: transform .2s, box-shadow .2s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(0,0,0,.1); }
    .stat-card.blue   { border-top-color: #3b82f6; }
    .stat-card.indigo { border-top-color: #6366f1; }
    .stat-card.cyan   { border-top-color: #06b6d4; }
    .stat-card.green  { border-top-color: #10b981; }
    .stat-card.red    { border-top-color: #ef4444; }
    .stat-card.orange { border-top-color: #f97316; }
    .stat-icon {
        width: 42px; height: 42px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; color: white; flex-shrink: 0;
    }
    .stat-val { font-size: 1.7rem; font-weight: 800; color: #111827; line-height: 1.1; }
    .stat-lbl { font-size: .72rem; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; margin-top: 2px; }

    /* ── Filter bar ── */
    .filter-bar {
        background: white;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,.05);
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .filter-bar .filter-group { display: flex; align-items: center; gap: 8px; }
    .filter-bar .input-group-text {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-right: none;
        color: #3b82f6;
        border-radius: 8px 0 0 8px;
    }
    .filter-bar .form-control {
        border: 1px solid #e2e8f0;
        border-left: none;
        border-radius: 0 8px 8px 0;
        font-size: .85rem;
        padding: .45rem .75rem;
        background: #f8fafc;
    }
    .filter-bar .form-control:focus { box-shadow: none; border-color: #3b82f6; background: white; }
    .btn-filter {
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 8px;
        padding: .45rem 1rem;
        font-size: .82rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: background .15s;
    }
    .btn-filter:hover { background: #2563eb; }
    .oklahoma-clock {
        margin-left: auto;
        background: #f1f5f9;
        border-radius: 8px;
        padding: .4rem .9rem;
        font-size: .78rem;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .oklahoma-clock i { color: #3b82f6; }

    /* ── Main card ── */
    .main-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        overflow: hidden;
    }
    .main-card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: .9rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .main-card-header h5 {
        font-size: .95rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .main-card-header h5 i { color: #3b82f6; }
    .main-card-body { padding: 1.5rem; }

    /* ── Table ── */
    #dataTable { width: 100% !important; }
    #dataTable thead th {
        background: #1e3a5f;
        color: white;
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        padding: 11px 14px;
        border: none;
        white-space: nowrap;
    }
    #dataTable thead th:first-child { border-radius: 8px 0 0 0; }
    #dataTable thead th:last-child  { border-radius: 0 8px 0 0; }
    #dataTable tbody tr { transition: background .15s; }
    #dataTable tbody tr:hover td { background: #f0f7ff !important; }
    #dataTable tbody td {
        padding: 11px 14px;
        font-size: .855rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #374151;
    }
    #dataTable tbody tr:last-child td { border-bottom: none; }

    /* Past row tinting */
    #dataTable tbody tr.row-past td { background: #fafafa; color: #9ca3af; }
    #dataTable tbody tr.row-today td { background: #eff6ff; }

    /* ── Badges ── */
    .tag {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 9px; border-radius: 999px;
        font-size: .72rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .04em;
        white-space: nowrap;
    }
    .tag-theory    { background: #dbeafe; color: #1e40af; }
    .tag-practical { background: #d1fae5; color: #065f46; }
    .tag-hybrid    { background: #fef3c7; color: #92400e; }
    .tag-active    { background: #d1fae5; color: #065f46; }
    .tag-inactive  { background: #fee2e2; color: #991b1b; }
    .tag-past      { background: #f1f5f9; color: #64748b; }
    .tag-today     { background: #fef3c7; color: #92400e; }
    .tag-tomorrow  { background: #cffafe; color: #155e75; }
    .tag-upcoming  { background: #ede9fe; color: #5b21b6; }

    /* ── Action buttons ── */
    .action-cell { white-space: nowrap; }
    .btn-action {
        width: 32px; height: 32px;
        border: none; border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: .8rem;
        transition: transform .15s, box-shadow .15s;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-action:hover { transform: translateY(-1px); box-shadow: 0 3px 8px rgba(0,0,0,.15); }
    .btn-toggle-on  { background: #d1fae5; color: #065f46; }
    .btn-toggle-off { background: #fee2e2; color: #991b1b; }
    .btn-edit       { background: #dbeafe; color: #1e40af; }
    .btn-delete     { background: #fee2e2; color: #991b1b; }

    /* ── Time display ── */
    .time-main { font-weight: 600; color: #111827; }
    .time-dur  { font-size: .72rem; color: #9ca3af; margin-top: 1px; }

    /* ── Date display ── */
    .date-main { font-weight: 600; }
    .date-sub  { font-size: .72rem; color: #9ca3af; }

    /* ── DataTables override ── */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter { margin-bottom: .75rem; }
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: .35rem .75rem;
        font-size: .83rem;
    }
    .dataTables_wrapper .dataTables_filter input:focus { outline: none; border-color: #3b82f6; }
    .dataTables_paginate .paginate_button {
        border-radius: 6px !important;
        border: none !important;
        padding: .3rem .65rem !important;
        font-size: .82rem;
    }
    .dataTables_paginate .paginate_button.current {
        background: #3b82f6 !important;
        color: white !important;
        border: none !important;
    }
    .dataTables_paginate .paginate_button:hover {
        background: #f1f5f9 !important;
        color: #111827 !important;
    }
    .dataTables_paginate .paginate_button.current:hover {
        background: #2563eb !important;
        color: white !important;
    }

    /* ── Alerts ── */
    .alert {
        border: none;
        border-radius: 12px;
        border-left: 4px solid transparent;
        padding: .85rem 1.1rem;
        font-size: .875rem;
    }
    .alert-success { background: #f0fdf4; border-left-color: #10b981; color: #065f46; }
    .alert-danger  { background: #fef2f2; border-left-color: #ef4444; color: #991b1b; }

    /* ── Modal ── */
    .modal-content { border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,.2); }
    .modal-header {
        background: linear-gradient(135deg, #1e3a5f, #2563eb);
        color: white;
        border: none;
        padding: 1.25rem 1.5rem;
    }
    .modal-header .modal-title { font-weight: 700; font-size: 1rem; display: flex; align-items: center; gap: 8px; }
    .modal-header .close { color: white; opacity: .8; font-size: 1.3rem; }
    .modal-header .close:hover { opacity: 1; }
    .modal-body { padding: 1.5rem; }
    .modal-footer { border-top: 1px solid #f1f5f9; padding: 1rem 1.5rem; }
    .modal-footer .btn { border-radius: 8px; padding: .5rem 1.1rem; font-size: .85rem; font-weight: 600; }
    .form-control-modal {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: .5rem .85rem;
        font-size: .875rem;
        background: #f8fafc;
        transition: border-color .15s, background .15s;
    }
    .form-control-modal:focus { border-color: #3b82f6; background: white; box-shadow: 0 0 0 3px rgba(59,130,246,.12); outline: none; }
    .form-label { font-size: .82rem; font-weight: 700; color: #374151; margin-bottom: 5px; }
    .modal-info-box {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: .85rem 1rem;
        font-size: .82rem;
        color: #1e40af;
    }
    .modal-info-box i { margin-right: 6px; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">

    {{-- ══════════════ PAGE HEADER ══════════════ --}}
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:1rem;">
            <div>
                <h1><i class="fas fa-calendar-alt" style="margin-right:10px;opacity:.85;"></i>Course Schedules</h1>
                <div class="sub">
                    <i class="fas fa-map-marker-alt" style="margin-right:4px;"></i>All times shown in Oklahoma (Central) Time
                </div>
            </div>
            <div class="header-actions d-flex" style="gap:.6rem;">
                <button class="btn btn-white-outline" data-toggle="modal" data-target="#copyScheduleModal">
                    <i class="fas fa-copy"></i> Copy Month
                </button>
                <a href="{{ route('course-schedules.create') }}" class="btn btn-white-solid">
                    <i class="fas fa-plus"></i> Add Schedule
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════════ ALERTS ══════════════ --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="position:absolute;right:1rem;top:.75rem;background:none;border:none;font-size:1.1rem;opacity:.6;">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            @if($errors->has('conflicts'))
                <ul class="mb-0 mt-1 ps-3">
                    @foreach($errors->get('conflicts') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @else
                {{ $errors->first() }}
            @endif
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="position:absolute;right:1rem;top:.75rem;background:none;border:none;font-size:1.1rem;opacity:.6;">&times;</button>
        </div>
    @endif

    {{-- ══════════════ STAT CARDS ══════════════ --}}
    @php
        $total    = $schedules->count();
        $theory   = $schedules->where('session_type', 'theory')->count();
        $practical= $schedules->where('session_type', 'practical')->count();
        $active   = $schedules->where('is_active', true)->count();
        $inactive = $schedules->where('is_active', false)->count();
        $upcoming = $schedules->filter(fn($s) => \Carbon\Carbon::parse($s->date)->isFuture())->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="stat-card blue d-flex align-items-center" style="gap:.9rem;">
                <div class="stat-icon" style="background:#3b82f6;"><i class="fas fa-calendar-alt"></i></div>
                <div>
                    <div class="stat-val">{{ $total }}</div>
                    <div class="stat-lbl">Total</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card indigo d-flex align-items-center" style="gap:.9rem;">
                <div class="stat-icon" style="background:#6366f1;"><i class="fas fa-book"></i></div>
                <div>
                    <div class="stat-val">{{ $theory }}</div>
                    <div class="stat-lbl">Theory</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card cyan d-flex align-items-center" style="gap:.9rem;">
                <div class="stat-icon" style="background:#06b6d4;"><i class="fas fa-car"></i></div>
                <div>
                    <div class="stat-val">{{ $practical }}</div>
                    <div class="stat-lbl">Practical</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card green d-flex align-items-center" style="gap:.9rem;">
                <div class="stat-icon" style="background:#10b981;"><i class="fas fa-check-circle"></i></div>
                <div>
                    <div class="stat-val">{{ $active }}</div>
                    <div class="stat-lbl">Active</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card red d-flex align-items-center" style="gap:.9rem;">
                <div class="stat-icon" style="background:#ef4444;"><i class="fas fa-times-circle"></i></div>
                <div>
                    <div class="stat-val">{{ $inactive }}</div>
                    <div class="stat-lbl">Inactive</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card orange d-flex align-items-center" style="gap:.9rem;">
                <div class="stat-icon" style="background:#f97316;"><i class="fas fa-arrow-right"></i></div>
                <div>
                    <div class="stat-val">{{ $upcoming }}</div>
                    <div class="stat-lbl">Upcoming</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════ FILTER BAR ══════════════ --}}
    <div class="filter-bar">
        <div class="filter-group">
            <div class="input-group" style="width:auto;">
                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                <input type="month" id="monthFilter" class="form-control"
                       value="{{ request('month', now()->setTimezone('America/Chicago')->format('Y-m')) }}"
                       style="min-width:160px;">
            </div>
            <button class="btn-filter" id="filterButton">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>
        <div class="oklahoma-clock">
            <i class="fas fa-clock"></i>
            Oklahoma Time: <strong id="currentOklahomaTime">{{ now()->setTimezone('America/Chicago')->format('M j, Y g:i A T') }}</strong>
        </div>
    </div>

    {{-- ══════════════ MAIN TABLE CARD ══════════════ --}}
    <div class="main-card">
        <div class="main-card-header">
            <h5>
                <i class="fas fa-list"></i>
                Schedule List
                <span style="font-size:.75rem;font-weight:400;color:#6b7280;margin-left:4px;">
                    — {{ now()->setTimezone('America/Chicago')->format('F Y') }}
                </span>
            </h5>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="tag tag-theory">
                    <i class="fas fa-book" style="font-size:.65rem;"></i> Theory
                </span>
                <span class="tag tag-practical">
                    <i class="fas fa-car" style="font-size:.65rem;"></i> Practical
                </span>
            </div>
        </div>
        <div class="main-card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Course</th>
                            <th>Instructor</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Type</th>
                            <th>Capacity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $schedule)
                            @php
                                $schedDate = \Carbon\Carbon::parse($schedule->date);
                                $isPast    = $schedDate->isPast();
                                $isToday   = $schedDate->isToday();
                                $isTomorrow= $schedDate->isTomorrow();
                                $startC    = \Carbon\Carbon::parse($schedule->start_time);
                                $endC      = \Carbon\Carbon::parse($schedule->end_time);
                                $durMins   = $endC->diffInMinutes($startC);
                                $durLabel  = $durMins >= 60 ? floor($durMins/60).'h '.($durMins%60 ? ($durMins%60).'m' : '') : $durMins.'m';
                                $rowClass  = $isPast ? 'row-past' : ($isToday ? 'row-today' : '');
                            @endphp
                            <tr class="{{ $rowClass }}">
                                {{-- ID --}}
                                <td>
                                    <span style="font-size:.78rem;color:#9ca3af;font-weight:600;">#{{ $schedule->id }}</span>
                                </td>

                                {{-- Course --}}
                                <td>
                                    <span style="font-weight:600;color:#111827;">{{ $schedule->course->course_name }}</span>
                                </td>

                                {{-- Instructor --}}
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:700;color:white;flex-shrink:0;">
                                            {{ strtoupper(substr($schedule->instructor->instructor_name, 0, 1)) }}
                                        </div>
                                        <span style="font-size:.855rem;">{{ $schedule->instructor->instructor_name }}</span>
                                    </div>
                                </td>

                                {{-- Date --}}
                                <td>
                                    <div class="date-main">
                                        {{ $schedDate->setTimezone('America/Chicago')->format('M j, Y') }}
                                    </div>
                                    <div class="date-sub">{{ $schedDate->format('l') }}</div>
                                    <div style="margin-top:3px;">
                                        @if($isPast && !$isToday)
                                            <span class="tag tag-past" style="font-size:.65rem;">Past</span>
                                        @elseif($isToday)
                                            <span class="tag tag-today" style="font-size:.65rem;">Today</span>
                                        @elseif($isTomorrow)
                                            <span class="tag tag-tomorrow" style="font-size:.65rem;">Tomorrow</span>
                                        @else
                                            <span class="tag tag-upcoming" style="font-size:.65rem;">Upcoming</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Time --}}
                                <td>
                                    <div class="time-main">
                                        {{ $startC->format('g:i A') }} – {{ $endC->format('g:i A') }}
                                    </div>
                                    <div class="time-dur">{{ trim($durLabel) }}</div>
                                </td>

                                {{-- Session Type --}}
                                <td>
                                    @if($schedule->session_type === 'theory')
                                        <span class="tag tag-theory"><i class="fas fa-book" style="font-size:.65rem;"></i> Theory</span>
                                    @elseif($schedule->session_type === 'practical')
                                        <span class="tag tag-practical"><i class="fas fa-car" style="font-size:.65rem;"></i> Practical</span>
                                    @else
                                        <span class="tag tag-hybrid"><i class="fas fa-layer-group" style="font-size:.65rem;"></i> {{ ucfirst($schedule->session_type) }}</span>
                                    @endif
                                </td>

                                {{-- Capacity --}}
                                <td>
                                    <div style="display:flex;align-items:center;gap:5px;">
                                        <i class="fas fa-users" style="color:#9ca3af;font-size:.75rem;"></i>
                                        <span style="font-weight:600;">{{ $schedule->max_students }}</span>
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if($schedule->is_active)
                                        <span class="tag tag-active"><i class="fas fa-circle" style="font-size:.45rem;"></i> Active</span>
                                    @else
                                        <span class="tag tag-inactive"><i class="fas fa-circle" style="font-size:.45rem;"></i> Inactive</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="action-cell">
                                    <div style="display:flex;align-items:center;gap:5px;">
                                        {{-- Toggle --}}
                                        <form action="{{ route('course-schedules.toggle-status', $schedule) }}" method="POST" class="toggle-form" style="margin:0;">
                                            @csrf
                                            <button type="submit"
                                                    class="btn-action {{ $schedule->is_active ? 'btn-toggle-on' : 'btn-toggle-off' }}"
                                                    title="{{ $schedule->is_active ? 'Deactivate' : 'Activate' }}"
                                                    data-action="{{ $schedule->is_active ? 'deactivate' : 'activate' }}">
                                                <i class="fas fa-toggle-{{ $schedule->is_active ? 'on' : 'off' }}"></i>
                                            </button>
                                        </form>

                                        {{-- Edit --}}
                                        <a href="{{ route('course-schedules.edit', $schedule->id) }}"
                                           class="btn-action btn-edit" title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route('course-schedules.destroy', $schedule) }}" method="POST"
                                              class="delete-form" style="margin:0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete" title="Delete">
                                                <i class="fas fa-trash"></i>
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

{{-- ══════════════ COPY SCHEDULE MODAL ══════════════ --}}
<div class="modal fade" id="copyScheduleModal" tabindex="-1" role="dialog" aria-labelledby="copyModalTitle">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="copyModalTitle">
                    <i class="fas fa-copy"></i> Copy Month Schedule
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="background:none;border:none;color:white;font-size:1.4rem;line-height:1;opacity:.8;padding:0 4px;">
                    &times;
                </button>
            </div>
            <form action="{{ route('course-schedules.copy-month') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-4">
                        <label for="sourceMonth" class="form-label">
                            <i class="fas fa-calendar text-primary" style="margin-right:5px;"></i>Source Month
                        </label>
                        <input type="month"
                               class="form-control form-control-modal"
                               id="sourceMonth"
                               name="month"
                               value="{{ now()->setTimezone('America/Chicago')->format('Y-m') }}"
                               required>
                        <small style="font-size:.78rem;color:#6b7280;margin-top:4px;display:block;">
                            Schedules from this month will be copied to the following month
                        </small>
                    </div>
                    <div class="modal-info-box">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Only schedules that don't conflict with existing ones will be created. Duplicate time slots for the same instructor are automatically skipped.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-copy" style="margin-right:5px;"></i>Copy Schedules
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

<script>
$(function () {

    /* ── DataTable ── */
    $('#dataTable').DataTable({
        order: [[3, 'asc'], [4, 'asc']],
        pageLength: 25,
        columnDefs: [{ orderable: false, targets: [8] }],
        language: {
            search:         '<i class="fas fa-search" style="margin-right:5px;color:#9ca3af;"></i>',
            searchPlaceholder: 'Search schedules…',
            emptyTable:     'No schedules found for this period.',
            zeroRecords:    'No matching schedules found.',
            lengthMenu:     'Show _MENU_ entries',
        },
    });

    /* ── Live Oklahoma clock ── */
    function updateOklahomaTime() {
        const fmt = new Intl.DateTimeFormat('en-US', {
            timeZone: 'America/Chicago',
            year: 'numeric', month: 'short', day: 'numeric',
            hour: 'numeric', minute: '2-digit', timeZoneName: 'short'
        });
        $('#currentOklahomaTime').text(fmt.format(new Date()));
    }
    updateOklahomaTime();
    setInterval(updateOklahomaTime, 60000);

    /* ── Month filter ── */
    function applyFilter() {
        const m = $('#monthFilter').val();
        if (m) {
            const url = new URL(window.location.href);
            url.searchParams.set('month', m);
            window.location.href = url.toString();
        }
    }
    $('#filterButton').on('click', function (e) { e.preventDefault(); applyFilter(); });
    $('#monthFilter').on('keypress', function (e) { if (e.which === 13) { e.preventDefault(); applyFilter(); } });

    /* ── Toggle status confirmation ── */
    $('.toggle-form').on('submit', function (e) {
        e.preventDefault();
        const action = $(this).find('[data-action]').data('action');
        if (confirm('Are you sure you want to ' + action + ' this schedule?')) {
            this.submit();
        }
    });

    /* ── Delete confirmation ── */
    $('.delete-form').on('submit', function (e) {
        e.preventDefault();
        if (confirm('Are you sure you want to permanently delete this schedule? This cannot be undone.')) {
            this.submit();
        }
    });

    /* ── Auto-dismiss alerts ── */
    setTimeout(function () { $('.alert').fadeOut(400, function () { $(this).remove(); }); }, 5000);

    /* ── Cap source month picker to current ── */
    const okMonth = new Intl.DateTimeFormat('en-CA', { timeZone: 'America/Chicago' })
        .format(new Date()).substring(0, 7);
    $('#sourceMonth').attr('max', okMonth);
});
</script>
@endsection
