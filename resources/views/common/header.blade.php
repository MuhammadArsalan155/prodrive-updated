<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top no-print">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars text-gray-500"></i>
    </button>

    <!-- Brand name (mobile) -->
    <span class="d-md-none font-weight-bold text-primary mr-auto" style="font-size:.95rem;">ProDrive Academy</span>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto align-items-center">

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
               role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                @php
                    $currentUser = null;
                    $userName = 'User';
                    $userRole = '';

                    if (Auth::guard('web')->check()) {
                        $currentUser = Auth::guard('web')->user();
                        $userName = $currentUser->name;
                        $userRole = $currentUser->hasRole('admin') ? 'Administrator' : 'Manager';
                    } elseif (Auth::guard('student')->check()) {
                        $currentUser = Auth::guard('student')->user();
                        $userName = $currentUser->first_name . ' ' . $currentUser->last_name;
                        $userRole = 'Student';
                    } elseif (Auth::guard('instructor')->check()) {
                        $currentUser = Auth::guard('instructor')->user();
                        $userName = $currentUser->instructor_name;
                        $userRole = 'Instructor';
                    } elseif (Auth::guard('parent')->check()) {
                        $currentUser = Auth::guard('parent')->user();
                        $userName = $currentUser->name;
                        $userRole = 'Parent';
                    }

                    $initials = '';
                    $parts = explode(' ', trim($userName));
                    foreach (array_slice($parts, 0, 2) as $p) {
                        $initials .= strtoupper(substr($p, 0, 1));
                    }
                @endphp

                <div class="d-none d-lg-block text-right mr-3">
                    <div style="font-size:.83rem; font-weight:700; color:#374151; line-height:1.2;">{{ $userName }}</div>
                    <div style="font-size:.7rem; color:#64748b; line-height:1.2;">{{ $userRole }}</div>
                </div>
                <div class="topbar-user-avatar">{{ $initials ?: 'U' }}</div>
            </a>

            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <div class="px-3 py-2 mb-1" style="border-bottom:1px solid #e2e8f0;">
                    <div style="font-size:.8rem; font-weight:700; color:#374151;">{{ $userName }}</div>
                    <div style="font-size:.72rem; color:#64748b;">{{ $userRole }}</div>
                </div>
                <a class="dropdown-item" href="{{ route('profile') }}">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>My Profile
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i>Logout
                </a>
            </div>
        </li>

    </ul>

</nav>
