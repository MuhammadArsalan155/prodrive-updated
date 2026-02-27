<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- In your HTML head -->

    <script src="{{ asset('admin/vendor/jquery/jquery.min.js') }}"></script>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ProDrive Academy</title>

    <!-- Custom fonts for this template-->

    <link href="{{ asset('admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">

    {{-- <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet"> --}}

    <!-- Custom styles for this template-->
    <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
     <link href="https://fonts.googleapis.com/css2?family=Gothic+A1:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
    /* ============================================================
       PRODRIVE GLOBAL DESIGN SYSTEM
    ============================================================ */

    /* Design tokens */
    :root {
        --pd-navy:        #1a2e4a;
        --pd-navy-dark:   #0f1f33;
        --pd-navy-light:  #243b55;
        --pd-blue:        #2563eb;
        --pd-blue-light:  #3b82f6;
        --pd-teal:        #0ea5e9;
        --pd-success:     #10b981;
        --pd-warning:     #f59e0b;
        --pd-danger:      #ef4444;
        --pd-info:        #06b6d4;
        --pd-gray-100:    #f8fafc;
        --pd-gray-200:    #e2e8f0;
        --pd-gray-500:    #64748b;
        --pd-gray-700:    #374151;
        --pd-gray-800:    #1f2937;
        --pd-shadow-sm:   0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.06);
        --pd-shadow:      0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -1px rgba(0,0,0,.06);
        --pd-shadow-lg:   0 10px 15px -3px rgba(0,0,0,.1), 0 4px 6px -2px rgba(0,0,0,.05);
        --pd-radius:      0.5rem;
        --pd-radius-lg:   0.75rem;
        --pd-transition:  all 0.18s ease;
    }

    /* ── Base typography ── */
    body, .sidebar, .navbar, .card, .btn, .form-control,
    .modal, .table, .dropdown-menu {
        font-family: "Century Gothic", CenturyGothic, "Gothic A1", sans-serif !important;
    }
    body { background: #f0f4f8; color: var(--pd-gray-800); }

    /* ── Page wrapper ── */
    #content-wrapper { background: #f0f4f8; }
    .container-fluid { padding-top: 1.5rem; padding-bottom: 1.5rem; }

    /* ── Cards ── */
    .card {
        border: none !important;
        border-radius: var(--pd-radius-lg) !important;
        box-shadow: var(--pd-shadow) !important;
        transition: var(--pd-transition);
        overflow: hidden;
    }
    .card:hover { box-shadow: var(--pd-shadow-lg) !important; }
    .card-header {
        background: #fff !important;
        border-bottom: 1px solid var(--pd-gray-200) !important;
        padding: 1rem 1.25rem !important;
        font-weight: 600;
    }
    .card-header.bg-primary,
    .card-header.bg-gradient-primary,
    .card-header[style*="gradient"] {
        border-bottom: none !important;
    }

    /* Standard page header card */
    .pd-page-header {
        background: linear-gradient(135deg, var(--pd-navy) 0%, var(--pd-navy-light) 60%, #2d6a8a 100%) !important;
        color: #fff;
        padding: 1.5rem 1.75rem;
        border-radius: var(--pd-radius-lg) !important;
        margin-bottom: 1.5rem;
        box-shadow: var(--pd-shadow-lg) !important;
    }
    .pd-page-header h1, .pd-page-header h2, .pd-page-header h3,
    .pd-page-header h4, .pd-page-header h5 { color: #fff; margin: 0; }
    .pd-page-header p { color: rgba(255,255,255,.75); margin: 0; }

    /* ── Stat cards ── */
    .pd-stat-card {
        border-radius: var(--pd-radius-lg) !important;
        border: none !important;
        box-shadow: var(--pd-shadow) !important;
        transition: var(--pd-transition);
        overflow: hidden;
    }
    .pd-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--pd-shadow-lg) !important;
    }
    .pd-stat-card .stat-icon {
        width: 54px; height: 54px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem;
        opacity: .9;
    }
    .pd-stat-card .stat-label {
        font-size: .7rem; font-weight: 700; letter-spacing: .08em;
        text-transform: uppercase; opacity: .7;
    }
    .pd-stat-card .stat-value { font-size: 1.75rem; font-weight: 800; line-height: 1; }

    /* ── DataTables / Tables ── */
    .table { font-size: .875rem; }
    .table thead th {
        background: var(--pd-navy) !important;
        color: #fff !important;
        font-weight: 600;
        font-size: .75rem;
        letter-spacing: .06em;
        text-transform: uppercase;
        border: none !important;
        padding: .75rem 1rem;
    }
    .table tbody tr {
        transition: background .12s ease;
        border-bottom: 1px solid var(--pd-gray-200);
    }
    .table tbody tr:hover { background: #f0f7ff !important; }
    .table tbody td { padding: .65rem 1rem; vertical-align: middle; border-top: none; }
    .table-bordered, .table-bordered td, .table-bordered th { border-color: var(--pd-gray-200) !important; }
    .table-striped tbody tr:nth-of-type(odd) { background: rgba(37,99,235,.03); }

    /* DataTable controls */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid var(--pd-gray-200);
        border-radius: 0.4rem;
        padding: .3rem .6rem;
        font-size: .85rem;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: var(--pd-blue) !important;
        border-color: var(--pd-blue) !important;
        color: #fff !important;
        border-radius: .4rem;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--pd-gray-100) !important;
        border-color: var(--pd-gray-200) !important;
        border-radius: .4rem;
    }

    /* ── Buttons ── */
    .btn {
        font-weight: 600;
        letter-spacing: .02em;
        border-radius: .45rem !important;
        transition: var(--pd-transition) !important;
    }
    .btn:hover { transform: translateY(-1px); }
    .btn:active { transform: translateY(0); }
    .btn-primary {
        background: var(--pd-blue) !important;
        border-color: var(--pd-blue) !important;
    }
    .btn-primary:hover {
        background: #1d4ed8 !important;
        border-color: #1d4ed8 !important;
    }
    .btn-success { background: var(--pd-success) !important; border-color: var(--pd-success) !important; }
    .btn-success:hover { background: #059669 !important; border-color: #059669 !important; }
    .btn-danger  { background: var(--pd-danger)  !important; border-color: var(--pd-danger)  !important; }
    .btn-danger:hover  { background: #dc2626 !important; border-color: #dc2626 !important; }
    .btn-warning { background: var(--pd-warning) !important; border-color: var(--pd-warning) !important; color:#fff !important; }
    .btn-warning:hover { background: #d97706 !important; border-color: #d97706 !important; }
    .btn-info    { background: var(--pd-info) !important; border-color: var(--pd-info) !important; }
    .btn-sm { padding: .3rem .65rem !important; font-size: .8rem !important; }
    .btn-icon { width: 32px; height: 32px; padding: 0 !important; display: inline-flex; align-items: center; justify-content: center; border-radius: .4rem !important; }

    /* ── Badges ── */
    .badge {
        font-weight: 600 !important;
        font-size: .72rem !important;
        padding: .3em .6em !important;
        border-radius: .35rem !important;
        letter-spacing: .03em;
    }
    /* BS4 badge classes */
    .badge-primary   { background: var(--pd-blue) !important; color: #fff !important; }
    .badge-success   { background: var(--pd-success) !important; color: #fff !important; }
    .badge-danger    { background: var(--pd-danger) !important; color: #fff !important; }
    .badge-warning   { background: var(--pd-warning) !important; color: #fff !important; }
    .badge-info      { background: var(--pd-info) !important; color: #fff !important; }
    .badge-secondary { background: var(--pd-gray-500) !important; color: #fff !important; }
    .badge-light     { background: var(--pd-gray-200) !important; color: var(--pd-gray-700) !important; }
    /* BS5 badge classes */
    .bg-primary  { background: var(--pd-blue) !important; }
    .bg-success  { background: var(--pd-success) !important; }
    .bg-danger   { background: var(--pd-danger) !important; }
    .bg-warning  { background: var(--pd-warning) !important; }
    .bg-info     { background: var(--pd-info) !important; }
    .bg-secondary{ background: var(--pd-gray-500) !important; }

    /* ── Forms ── */
    .form-control {
        border: 1.5px solid var(--pd-gray-200) !important;
        border-radius: .45rem !important;
        font-size: .875rem !important;
        padding: .45rem .75rem !important;
        transition: var(--pd-transition) !important;
    }
    .form-control:focus {
        border-color: var(--pd-blue) !important;
        box-shadow: 0 0 0 3px rgba(37,99,235,.12) !important;
    }
    .form-group label, .form-label { font-weight: 600; font-size: .83rem; color: var(--pd-gray-700); margin-bottom: .35rem; }
    select.form-control { cursor: pointer; }

    /* ── Alerts ── */
    .alert {
        border: none !important;
        border-radius: var(--pd-radius) !important;
        font-size: .875rem;
        border-left: 4px solid transparent !important;
    }
    .alert-success { background: #ecfdf5 !important; color: #065f46 !important; border-left-color: var(--pd-success) !important; }
    .alert-danger  { background: #fef2f2 !important; color: #7f1d1d !important; border-left-color: var(--pd-danger)  !important; }
    .alert-warning { background: #fffbeb !important; color: #78350f !important; border-left-color: var(--pd-warning) !important; }
    .alert-info    { background: #ecfeff !important; color: #164e63 !important; border-left-color: var(--pd-info)    !important; }

    /* ── Modals ── */
    .modal-content {
        border: none !important;
        border-radius: var(--pd-radius-lg) !important;
        box-shadow: 0 25px 50px rgba(0,0,0,.2) !important;
        overflow: hidden;
    }
    .modal-header {
        background: linear-gradient(135deg, var(--pd-navy), var(--pd-navy-light)) !important;
        color: #fff !important;
        border-bottom: none !important;
        padding: 1.1rem 1.5rem !important;
    }
    .modal-header .modal-title { color: #fff !important; font-weight: 700; font-size: 1rem; }
    .modal-header .close, .modal-header .btn-close { color: #fff !important; opacity: .8; }
    .modal-header .close:hover, .modal-header .btn-close:hover { opacity: 1; }
    .modal-footer {
        background: var(--pd-gray-100) !important;
        border-top: 1px solid var(--pd-gray-200) !important;
        padding: .85rem 1.25rem !important;
    }

    /* ── Topbar ── */
    .topbar {
        background: #fff !important;
        border-bottom: 1px solid var(--pd-gray-200) !important;
        box-shadow: var(--pd-shadow-sm) !important;
        height: 60px;
        padding: 0 1.5rem !important;
    }
    .topbar .nav-link { color: var(--pd-gray-700) !important; font-weight: 600; font-size: .875rem; }
    .topbar .nav-link:hover { color: var(--pd-blue) !important; }
    .topbar .dropdown-menu {
        border: none !important;
        border-radius: var(--pd-radius) !important;
        box-shadow: var(--pd-shadow-lg) !important;
        padding: .5rem !important;
    }
    .topbar .dropdown-item {
        border-radius: .35rem !important;
        font-size: .875rem;
        padding: .45rem .75rem !important;
        transition: var(--pd-transition);
        color: var(--pd-gray-700) !important;
    }
    .topbar .dropdown-item:hover { background: var(--pd-gray-100) !important; color: var(--pd-blue) !important; }
    .topbar-divider { border-color: var(--pd-gray-200) !important; }
    .img-profile { width: 34px !important; height: 34px !important; object-fit: cover; border: 2px solid var(--pd-gray-200); }

    /* User avatar in topbar */
    .topbar-user-avatar {
        width: 34px; height: 34px;
        background: linear-gradient(135deg, var(--pd-navy), var(--pd-blue));
        color: #fff; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .8rem; letter-spacing: .02em;
    }

    /* ── Sidebar ── */
    .sidebar {
        background: linear-gradient(180deg, var(--pd-navy-dark) 0%, var(--pd-navy) 100%) !important;
        width: 14rem !important;
    }
    .sidebar .sidebar-brand {
        background: rgba(0,0,0,.2) !important;
        padding: 1.1rem 1rem !important;
        border-bottom: 1px solid rgba(255,255,255,.08) !important;
    }
    .sidebar .sidebar-brand-text { font-size: 1.05rem !important; font-weight: 700; letter-spacing: .05em; }
    .sidebar .sidebar-brand-text sup { font-size: .5rem; opacity: .7; }
    .sidebar-dark .nav-item .nav-link {
        color: rgba(255,255,255,.72) !important;
        font-size: .83rem !important;
        font-weight: 500;
        padding: .6rem 1rem !important;
        border-radius: .4rem !important;
        margin: .1rem .6rem !important;
        transition: var(--pd-transition) !important;
    }
    .sidebar-dark .nav-item .nav-link:hover {
        color: #fff !important;
        background: rgba(255,255,255,.1) !important;
    }
    .sidebar-dark .nav-item .nav-link.active,
    .sidebar-dark .nav-item.active .nav-link {
        color: #fff !important;
        background: rgba(37,99,235,.55) !important;
    }
    .sidebar-dark .nav-item .nav-link i { color: rgba(255,255,255,.55) !important; margin-right: .5rem; width: 1.1rem; }
    .sidebar-dark .nav-item .nav-link:hover i,
    .sidebar-dark .nav-item.active .nav-link i { color: rgba(255,255,255,.9) !important; }
    .sidebar .sidebar-heading {
        font-size: .65rem !important;
        font-weight: 700 !important;
        letter-spacing: .12em !important;
        color: rgba(255,255,255,.4) !important;
        padding: .75rem 1.6rem .3rem !important;
        text-transform: uppercase;
    }
    .sidebar-dark hr.sidebar-divider { border-color: rgba(255,255,255,.1) !important; margin: .5rem 1rem !important; }
    .sidebar .collapse-inner {
        background: rgba(0,0,0,.25) !important;
        border-radius: .4rem !important;
        margin: .1rem .6rem !important;
        border: 1px solid rgba(255,255,255,.07) !important;
    }
    .sidebar .collapse-item {
        color: rgba(255,255,255,.65) !important;
        font-size: .8rem !important;
        padding: .4rem .9rem !important;
        border-radius: .35rem !important;
        transition: var(--pd-transition) !important;
        display: block;
    }
    .sidebar .collapse-item:hover { background: rgba(255,255,255,.1) !important; color: #fff !important; }
    .sidebar .collapse-header {
        font-size: .65rem !important;
        font-weight: 700 !important;
        letter-spacing: .1em !important;
        color: rgba(255,255,255,.35) !important;
        text-transform: uppercase;
        padding: .5rem .9rem .25rem !important;
    }
    .sidebar-card {
        background: rgba(0,0,0,.2) !important;
        border-top: 1px solid rgba(255,255,255,.1) !important;
        padding: 1rem !important;
        margin-top: auto;
    }
    #sidebarToggle { background: rgba(255,255,255,.1) !important; }
    #sidebarToggle:hover { background: rgba(255,255,255,.2) !important; }

    /* ── Scroll to top ── */
    .scroll-to-top {
        background: var(--pd-blue) !important;
        border-radius: 50% !important;
    }

    /* ── Page heading utilities ── */
    .page-title {
        font-size: 1.4rem; font-weight: 700; color: var(--pd-gray-800);
    }
    .page-subtitle { font-size: .875rem; color: var(--pd-gray-500); }
    .section-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 1.25rem; padding-bottom: .75rem;
        border-bottom: 2px solid var(--pd-gray-200);
    }
    .section-title { font-size: 1rem; font-weight: 700; color: var(--pd-gray-800); margin: 0; }

    /* ── Icon circle ── */
    .icon-circle {
        width: 34px; height: 34px;
        border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .8rem;
    }

    /* ── Logout modal specific ── */
    #logoutModal .modal-header { background: linear-gradient(135deg, var(--pd-navy), var(--pd-blue)) !important; }

    /* ── Print ── */
    @media print { .no-print { display: none !important; } }

    /* ── Responsive tweaks ── */
    @media (max-width: 768px) {
        .pd-page-header { padding: 1rem 1.25rem; }
        .container-fluid { padding-top: 1rem; padding-bottom: 1rem; }
    }
    </style>
    @yield('styles')
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('common.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @include('common.header')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                @yield('content')
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @include('common.footer')
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('admin/js/sb-admin-2.min.js') }}"></script>

    <!-- Page level plugins -->
    <script src="{{ asset('admin/vendor/chart.js/Chart.min.js') }}"></script>

    <!-- Page level custom scripts -->
    <script src="{{ asset('admin/js/demo/chart-area-demo.js') }}"></script>
    <script src="{{ asset('admin/js/demo/chart-pie-demo.js') }}"></script>
    @yield('scripts')
</body>

</html>
