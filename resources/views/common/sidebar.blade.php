<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion no-print" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <img src="{{ asset('admin/img/Prodrive 4.png') }}" style="height: 60px;width:auto;" alt="">
        </div>
        <div class="sidebar-brand-text">ProDrive <sup>Academy</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    @php
        // Determine the current authenticated user and their guard
        $currentUser = null;
        $currentGuard = null;

        if (Auth::guard('web')->check()) {
            $currentUser = Auth::guard('web')->user();
            $currentGuard = 'web';
        } elseif (Auth::guard('student')->check()) {
            $currentUser = Auth::guard('student')->user();
            $currentGuard = 'student';
        } elseif (Auth::guard('instructor')->check()) {
            $currentUser = Auth::guard('instructor')->user();
            $currentGuard = 'instructor';
        } elseif (Auth::guard('parent')->check()) {
            $currentUser = Auth::guard('parent')->user();
            $currentGuard = 'parent';
        }

        // Check for admin role
        $isAdmin = false;
        if ($currentUser && $currentGuard == 'web') {
            $isAdmin = $currentUser->hasRole('admin');
        }
    @endphp

    @if ($currentUser)
        @if ($isAdmin)
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Admin Dashboard</span>
                </a>
            </li>
        @elseif ($currentGuard == 'instructor' && $currentUser->hasRole('instructor'))
            @if ($currentUser->hasPermission('access-instructor-dashboard'))
                <li class="nav-item active">
                    <a class="nav-link" href="{{ route('instructor.dashboard') }}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Instructor Dashboard</span>
                    </a>
                </li>
            @endif
        @elseif ($currentGuard == 'student' && $currentUser->hasRole('student'))
            @if ($currentUser->hasPermission('access-student-dashboard'))
                <li class="nav-item active">
                    <a class="nav-link" href="{{ route('student.dashboard') }}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Student Dashboard</span>
                    </a>
                </li>
            @endif
        @elseif ($currentGuard == 'web' && $currentUser->hasRole('manager'))
            @if ($currentUser->hasPermission('access-manager-dashboard'))
                <li class="nav-item active">
                    <a class="nav-link" href="{{ route('manager.dashboard') }}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Manager Dashboard</span>
                    </a>
                </li>
            @endif
        @elseif ($currentGuard == 'parent')
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('parent.dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Parent Dashboard</span>
                </a>
            </li>
        @endif

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Interface
        </div>

        <!-- Announcements Section for Admin and Manager with permission -->
        @if (
            $isAdmin ||
                ($currentGuard == 'web' &&
                    $currentUser->hasRole('manager') &&
                    $currentUser->hasPermission('manage-announcements')))
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAnnouncements"
                    aria-expanded="true" aria-controls="collapseAnnouncements">
                    <i class="fas fa-fw fa-bullhorn"></i>
                    <span>Announcements</span>
                </a>
                <div id="collapseAnnouncements" class="collapse" aria-labelledby="headingAnnouncements"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Manage Announcements:</h6>
                        <a class="collapse-item" href="{{ route('admin.announcements.create') }}">Create</a>
                        <a class="collapse-item" href="{{ route('admin.announcements.index') }}">View</a>
                    </div>
                </div>
            </li>
        @endif

        @if ($isAdmin)
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseStudent"
                    aria-expanded="true" aria-controls="collapseStudent">
                    <i class="fas fa-fw fa-user-graduate"></i>
                    <span>Student</span>
                </a>
                <div id="collapseStudent" class="collapse" aria-labelledby="headingStudent"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Student:</h6>
                        <a class="collapse-item" href="{{ route('addstudent') }}">Add Student</a>
                        <a class="collapse-item" href="{{ route('viewstudent') }}">View Student</a>
                    </div>
                </div>
            </li>
            {{-- Invoices Section --}}
            {{-- Invoices Section --}}
            {{-- <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInvoices"
                    aria-expanded="true" aria-controls="collapseInvoices">
                    <i class="fas fa-fw fa-file-invoice"></i>
                    <span>Invoices</span>
                </a>
                <div id="collapseInvoices" class="collapse" aria-labelledby="headingInvoices"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Manage Invoices:</h6>
                        <a class="collapse-item" href="{{ route('admin.invoices.create') }}">Create Invoice</a>
                        <a class="collapse-item" href="{{ route('admin.invoices.index') }}">View All Invoices</a>
                    </div>
                </div>
            </li> --}}
            {{-- Courses Section --}}
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCourses"
                    aria-expanded="true" aria-controls="collapseCourses">
                    <i class="fas fa-fw fa-book"></i>
                    <span>Courses</span>
                </a>
                <div id="collapseCourses" class="collapse" aria-labelledby="headingCourses"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Courses:</h6>
                        <a class="collapse-item" href="{{ route('addcourse') }}">Add Course</a>
                        <a class="collapse-item" href="{{ route('viewcourse') }}">View Course</a>
                    </div>
                </div>
            </li>

            {{-- Instructor Section --}}
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse"
                    data-target="#collapseInstructor" aria-expanded="true" aria-controls="collapseInstructor">
                    <i class="fas fa-fw fa-chalkboard-teacher"></i>
                    <span>Instructor</span>
                </a>
                <div id="collapseInstructor" class="collapse" aria-labelledby="headingInstructor"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Instructor:</h6>
                        <a class="collapse-item" href="{{ route('admin.addinstructor') }}">Add Instructor</a>
                        <a class="collapse-item" href="{{ route('admin.viewinstructor') }}">View Instructors</a>
                    </div>
                </div>
            </li>

            {{-- Manager Section --}}
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseManager"
                    aria-expanded="true" aria-controls="collapseManager">
                    <i class="fas fa-fw fa-user-tie"></i>
                    <span>Manager</span>
                </a>
                <div id="collapseManager" class="collapse" aria-labelledby="headingManager"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Manager:</h6>
                        <a class="collapse-item" href="{{ route('admin.managers.create') }}">Add Manager</a>
                        <a class="collapse-item" href="{{ route('admin.managers.index') }}">View Managers</a>
                    </div>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReports"
                    aria-expanded="true" aria-controls="collapseReports">
                    <i class="fas fa-fw fa-file-alt"></i>
                    <span>Reports</span>
                </a>
                <div id="collapseReports" class="collapse" aria-labelledby="headingReports"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Report Types:</h6>
                        <a class="collapse-item" href="{{ route('admin.reports.students.index') }}">Student
                            Reports</a>
                        {{-- <a class="collapse-item" href="{{ route('admin.reports.finance') }}">Financial Reports</a> --}}
                        {{-- <a class="collapse-item" href="{{ route('admin.reports.courses') }}">Course Analytics</a> --}}
                    </div>
                </div>
            </li>

            {{-- Payment Methods Section --}}
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse"
                    data-target="#collapsePaymentMethods" aria-expanded="true"
                    aria-controls="collapsePaymentMethods">
                    <i class="fas fa-fw fa-credit-card"></i>
                    <span>Payment Methods</span>
                </a>
                <div id="collapsePaymentMethods" class="collapse" aria-labelledby="headingPaymentMethods"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Manage Payment Methods:</h6>
                        <a class="collapse-item" href="{{ route('admin.payment-methods.create') }}">Add</a>
                        <a class="collapse-item" href="{{ route('admin.payment-methods.index') }}">View</a>
                    </div>
                </div>
            </li>

            {{-- Course Schedules Section --}}
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse"
                    data-target="#collapseCourseSchedules" aria-expanded="true"
                    aria-controls="collapseCourseSchedules">
                    <i class="fas fa-fw fa-calendar-alt"></i>
                    <span>Course Schedules</span>
                </a>
                <div id="collapseCourseSchedules" class="collapse" aria-labelledby="headingCourseSchedules"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Manage Schedules:</h6>
                        <a class="collapse-item" href="{{ route('course-schedules.create') }}">Add Schedule</a>
                        <a class="collapse-item" href="{{ route('course-schedules.index') }}">View Calendar</a>
                    </div>
                </div>
            </li>

            {{-- Installment Plans Section --}}
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse"
                    data-target="#collapseInstallmentPlans" aria-expanded="true"
                    aria-controls="collapseInstallmentPlans">
                    <i class="fas fa-fw fa-money-bill-wave"></i>
                    <span>Installment Plans</span>
                </a>
                <div id="collapseInstallmentPlans" class="collapse" aria-labelledby="headingInstallmentPlans"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Manage Installment Plans:</h6>
                        <a class="collapse-item" href="{{ route('admin.course-installment-plans.create') }}">Add</a>
                        <a class="collapse-item" href="{{ route('admin.course-installment-plans.index') }}">View</a>
                    </div>
                </div>
            </li>
            {{-- Lesson Plans Section --}}
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse"
                    data-target="#collapseLessonPlans" aria-expanded="true" aria-controls="collapseLessonPlans">
                    <i class="fas fa-fw fa-book-open"></i>
                    <span>Lesson Plans</span>
                </a>
                <div id="collapseLessonPlans" class="collapse" aria-labelledby="headingLessonPlans"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Manage Lesson Plans:</h6>
                        <a class="collapse-item" href="{{ route('admin.lesson-plans.create') }}">Add Lesson Plan</a>
                        <a class="collapse-item" href="{{ route('admin.lesson-plans.index') }}">View Lesson Plans</a>
                    </div>
                </div>
            </li>
            {{-- User & Permissions Section --}}
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUserRoles"
                    aria-expanded="true" aria-controls="collapseUserRoles">
                    <i class="fas fa-fw fa-users-cog"></i>
                    <span>User & Permissions</span>
                </a>
                <div id="collapseUserRoles" class="collapse" aria-labelledby="headingUserRoles"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">User Management:</h6>
                        <a class="collapse-item" href="{{ route('admin.users.index') }}">Manage Users</a>
                        <div class="collapse-divider"></div>
                        <h6 class="collapse-header">Roles & Permissions:</h6>
                        <a class="collapse-item" href="{{ route('admin.users.logs') }}">Permission Logs</a>
                    </div>
                </div>
            </li>

            {{-- Roles Management Section --}}
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRoles"
                    aria-expanded="true" aria-controls="collapseRoles">
                    <i class="fas fa-fw fa-user-tag"></i>
                    <span>Roles Management</span>
                </a>
                <div id="collapseRoles" class="collapse" aria-labelledby="headingRoles"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Manage Roles:</h6>
                        <a class="collapse-item" href="{{ route('admin.roles.index') }}">View All Roles</a>
                    </div>
                </div>
            </li>

            {{-- Certificates Section --}}
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse"
                    data-target="#collapseCertificates" aria-expanded="true" aria-controls="collapseCertificates">
                    <i class="fas fa-fw fa-certificate"></i>
                    <span>Certificates</span>
                </a>
                <div id="collapseCertificates" class="collapse" aria-labelledby="headingCertificates"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Manage Certificates:</h6>
                        <a class="collapse-item" href="{{ route('admin.certificates.index') }}">All Certificates</a>
                        <a class="collapse-item" href="{{ route('admin.certificates.eligible') }}">Eligible
                            Students</a>
                        <a class="collapse-item" href="{{ route('admin.certificates.create') }}">Generate
                            Certificate</a>
                    </div>
                </div>
            </li>
        @endif

        {{-- Instructor Specific Sections with Permission Checks --}}
        @if ($currentGuard == 'instructor' && $currentUser->hasRole('instructor'))
            {{-- Check if instructor has any course-related permissions --}}
            @php
                $hasCoursePermissions =
                    $currentUser->hasPermission('access-instructor-courses') ||
                    $currentUser->hasPermission('access-instructor-course-schedules');
                $hasFeedbackPermission = $currentUser->hasPermission('access-instructor-feedback');
            @endphp

            @if ($hasFeedbackPermission)
                {{-- Feedback Section for Instructor --}}
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse"
                        data-target="#collapseInstructorFeedback" aria-expanded="true"
                        aria-controls="collapseInstructorFeedback">
                        <i class="fas fa-fw fa-comment-dots"></i>
                        <span>Lesson Feedback</span>
                    </a>
                    <div id="collapseInstructorFeedback" class="collapse" aria-labelledby="headingInstructorFeedback"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Feedback:</h6>
                            <a class="collapse-item" href="{{ route('instructor.feedback.pending') }}">Pending
                                Feedback</a>
                            <a class="collapse-item" href="{{ route('instructor.feedback.completed') }}">Completed
                                Feedback</a>
                        </div>
                    </div>
                </li>
            @endif
            @if ($hasCoursePermissions)
                {{-- Courses Section for Instructor --}}
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse"
                        data-target="#collapseInstructorCourses" aria-expanded="true"
                        aria-controls="collapseInstructorCourses">
                        <i class="fas fa-fw fa-book"></i>
                        <span>My Courses</span>
                    </a>
                    <div id="collapseInstructorCourses" class="collapse" aria-labelledby="headingInstructorCourses"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Courses:</h6>
                            @if ($currentUser->hasPermission('access-instructor-courses'))
                                <a class="collapse-item" href="{{ route('instructor.courses') }}">View Courses</a>
                            @endif
                            @if ($currentUser->hasPermission('access-instructor-course-schedules'))
                                <a class="collapse-item" href="{{ route('instructor.course-schedules') }}">My
                                    Schedules</a>
                            @endif
                        </div>
                    </div>
                </li>
            @endif

            {{-- Check if instructor has student-related permissions --}}
            @if ($currentUser->hasPermission('access-instructor-students'))
                {{-- Students Section for Instructor --}}
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse"
                        data-target="#collapseInstructorStudents" aria-expanded="true"
                        aria-controls="collapseInstructorStudents">
                        <i class="fas fa-fw fa-users"></i>
                        <span>My Students</span>
                    </a>
                    <div id="collapseInstructorStudents" class="collapse" aria-labelledby="headingInstructorStudents"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Students:</h6>
                            <a class="collapse-item" href="{{ route('instructor.students') }}">View Students</a>
                        </div>
                    </div>
                </li>
            @endif
        @endif

        {{-- Manager Specific Sections with Permission Checks --}}
        @if ($currentGuard == 'web' && $currentUser->hasRole('manager'))
            {{-- Check if manager has course management permissions --}}
            @php
                $hasCourseManagement =
                    $currentUser->hasPermission('manage-courses') || $currentUser->hasPermission('view-courses');
            @endphp

            @if ($currentUser->hasPermission('access-student-progress-reports-index'))
                {{-- Reports Section for Manager --}}
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse"
                        data-target="#collapseManagerReports" aria-expanded="true"
                        aria-controls="collapseManagerReports">
                        <i class="fas fa-fw fa-file-alt"></i>
                        <span>Reports</span>
                    </a>
                    <div id="collapseManagerReports" class="collapse" aria-labelledby="headingManagerReports"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Reports:</h6>
                            <a class="collapse-item" href="{{ route('student.progress-reports.index') }}">View
                                Reports</a>
                        </div>
                    </div>
                </li>
            @endif

            @if ($hasCourseManagement)
                {{-- Course Management for Manager --}}
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse"
                        data-target="#collapseCourses" aria-expanded="true" aria-controls="collapseCourses">
                        <i class="fas fa-fw fa-book"></i>
                        <span>Courses</span>
                    </a>
                    <div id="collapseCourses" class="collapse" aria-labelledby="headingCourses"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Courses:</h6>
                            @if ($currentUser->hasPermission('view-courses'))
                                <a class="collapse-item" href="{{ route('addcourse') }}">Add Course</a>
                            @endif
                            @if ($currentUser->hasPermission('manage-courses'))
                                <a class="collapse-item" href="{{ route('viewcourse') }}">View Course</a>
                            @endif
                        </div>
                    </div>
                </li>
            @endif

            @php
                $hasStudentManagement =
                    $currentUser->hasPermission('access-addstudent') ||
                    $currentUser->hasPermission('access-viewstudent') ||
                    $currentUser->hasPermission('access-view_student') ||
                    $currentUser->hasPermission('access-add_student') ||
                    $currentUser->hasPermission('access-edit_student') ||
                    $currentUser->hasPermission('access-update_student');
            @endphp

            @if ($hasStudentManagement)
                {{-- Student Management for Manager --}}
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse"
                        data-target="#collapseManagerStudents" aria-expanded="true"
                        aria-controls="collapseManagerStudents">
                        <i class="fas fa-fw fa-user-graduate"></i>
                        <span>Student Management</span>
                    </a>
                    <div id="collapseManagerStudents" class="collapse" aria-labelledby="headingManagerStudents"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Students:</h6>
                            @if ($currentUser->hasPermission('access-addstudent'))
                                <a class="collapse-item" href="{{ route('addstudent') }}">Add Student</a>
                            @endif
                            @if ($currentUser->hasPermission('access-view_student'))
                                <a class="collapse-item" href="{{ route('viewstudent') }}">View Student</a>
                            @endif
                        </div>
                    </div>
                </li>
            @endif

            {{-- Check if manager has instructor management permissions --}}
            @php
                $hasInstructorManagement =
                    $currentUser->hasPermission('access-admin-addinstructor') ||
                    $currentUser->hasPermission('access-admin-viewinstructor') ||
                    $currentUser->hasPermission('access-admin-add_instructor') ||
                    $currentUser->hasPermission('access-admin-edit_instructor') ||
                    $currentUser->hasPermission('access-admin-delete_instructor') ||
                    $currentUser->hasPermission('access-admin-update_instructor');
            @endphp

            @if ($hasInstructorManagement)
                {{-- Instructor Management for Manager --}}
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse"
                        data-target="#collapseManagerInstructors" aria-expanded="true"
                        aria-controls="collapseManagerInstructors">
                        <i class="fas fa-fw fa-chalkboard-teacher"></i>
                        <span>Instructor Management</span>
                    </a>
                    <div id="collapseManagerInstructors" class="collapse" aria-labelledby="headingManagerInstructors"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Instructors:</h6>
                            @if ($currentUser->hasPermission('access-admin-addinstructor'))
                                <a class="collapse-item" href="{{ route('admin.addinstructor') }}">Add Instructor</a>
                            @endif
                            @if ($currentUser->hasPermission('access-admin-viewinstructor'))
                                <a class="collapse-item" href="{{ route('admin.viewinstructor') }}">View
                                    Instructors</a>
                            @endif
                        </div>
                    </div>
                </li>
            @endif

            {{-- Check if manager has Installment Plan management permissions --}}
            @php
                $hasInstructorManagement =
                    $currentUser->hasPermission('access-admin-course-installment-plans-index') ||
                    $currentUser->hasPermission('access-admin-course-installment-plans-create') ||
                    $currentUser->hasPermission('access-admin-course-installment-plans-store') ||
                    $currentUser->hasPermission('access-admin-course-installment-plans-show') ||
                    $currentUser->hasPermission('access-admin-course-installment-plans-edit') ||
                    $currentUser->hasPermission('access-admin-course-installment-plans-update') ||
                    $currentUser->hasPermission('access-admin-course-installment-plans-default') ||
                    $currentUser->hasPermission('access-admin-course-installment-plans-toggle-status');
            @endphp

            @if ($hasInstructorManagement)
                {{-- Instructor Management for Manager --}}
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse"
                        data-target="#collapseManagerInstallmentPlan" aria-expanded="true"
                        aria-controls="collapseManagerInstallmentPlan">
                        <i class="fas fa-fw fa-chalkboard-teacher"></i>
                        <span>Installment Plan Management</span>
                    </a>
                    <div id="collapseManagerInstallmentPlan" class="collapse"
                        aria-labelledby="headingManagerInstructors" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Installment Plan:</h6>
                            @if ($currentUser->hasPermission('access-admin-course-installment-plans-create'))
                                <a class="collapse-item"
                                    href="{{ route('admin.course-installment-plans.create') }}">Add</a>
                            @endif
                            @if ($currentUser->hasPermission('access-admin-course-installment-plans-index'))
                                <a class="collapse-item"
                                    href="{{ route('admin.course-installment-plans.index') }}">View</a>
                            @endif
                        </div>
                    </div>
                </li>
            @endif

            {{-- Check if manager has report permissions --}}
            @if (
                $currentUser->hasPermission('access-admin-reports-students-index') ||
                    $currentUser->hasPermission('access-admin-reports-students-show') ||
                    $currentUser->hasPermission('access-admin-reports-students-pdf') ||
                    $currentUser->hasPermission('access-admin-reports-students-batch-pdf'))
                {{-- Reports Section for Manager --}}
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse"
                        data-target="#collapseManagerReports" aria-expanded="true"
                        aria-controls="collapseManagerReports">
                        <i class="fas fa-fw fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                    <div id="collapseManagerReports" class="collapse" aria-labelledby="headingManagerReports"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Reports:</h6>
                            @if ($currentUser->hasPermission('access-admin-reports-students-index'))
                                <a class="collapse-item" href="{{ route('student.progress-reports.index') }}">View
                                    Reports</a>
                            @endif
                        </div>
                    </div>
                </li>
            @endif

            {{-- Check if manager has report permissions --}}
            @if (
                $currentUser->hasPermission('access-admin-announcements-index') ||
                    $currentUser->hasPermission('access-admin-announcements-create') ||
                    $currentUser->hasPermission('access-admin-announcements-store') ||
                    $currentUser->hasPermission('access-admin-announcements-show') ||
                    $currentUser->hasPermission('access-admin-announcements-update') ||
                    $currentUser->hasPermission('access-admin-announcements-destroy') ||
                    $currentUser->hasPermission('access-admin-announcements-download') ||
                    $currentUser->hasPermission('access-admin-announcements-edit'))
                {{-- Reports Section for Manager --}}
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse"
                        data-target="#collapseManagerAnnouncements" aria-expanded="true"
                        aria-controls="collapseManagerAnnouncements">
                        <i class="fas fa-fw fa-bullhorn"></i>
                        <span>Manage Announcements</span>
                    </a>
                    <div id="collapseManagerAnnouncements" class="collapse" aria-labelledby="headingManagerReports"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Manage Announcements:</h6>
                            @if ($currentUser->hasPermission('access-admin-announcements-create'))
                                <a class="collapse-item" href="{{ route('admin.announcements.create') }}">Create
                                </a>
                            @endif
                            @if ($currentUser->hasPermission('access-admin-announcements-index'))
                                <a class="collapse-item" href="{{ route('admin.announcements.index') }}">View </a>
                            @endif

                        </div>
                    </div>
                </li>
            @endif

            {{-- Check if manager has report permissions --}}
            @if (
                $currentUser->hasPermission('access-course-schedules-index') ||
                    $currentUser->hasPermission('access-course-schedules-create') ||
                    $currentUser->hasPermission('access-course-schedules-store') ||
                    $currentUser->hasPermission('access-course-schedules-edit') ||
                    $currentUser->hasPermission('access-course-schedules-update') ||
                    $currentUser->hasPermission('access-course-schedules-destroy') ||
                    $currentUser->hasPermission('access-course-schedules-toggle-status') ||
                    $currentUser->hasPermission('access-course-schedules-show'))
                {{-- Reports Section for Manager --}}
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse"
                        data-target="#collapseManagerCourseSchedules" aria-expanded="true"
                        aria-controls="collapseManagerCourseSchedules">
                        <i class="fas fa-fw fa-calendar-alt"></i>
                        <span>Course Schedules</span>
                    </a>
                    <div id="collapseManagerCourseSchedules" class="collapse" aria-labelledby="headingManagerReports"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">

                            <h6 class="collapse-header">Manage Schedules:</h6>
                            @if ($currentUser->hasPermission('access-course-schedules-create'))
                                <a class="collapse-item" href="{{ route('course-schedules.create') }}">Add
                                    Schedule</a>
                            @endif
                            @if ($currentUser->hasPermission('access-course-schedules-index'))
                                <a class="collapse-item" href="{{ route('course-schedules.index') }}">View
                                    Calendar</a>
                            @endif

                        </div>
                    </div>
                </li>
            @endif

            {{-- Check if manager has lesson plan permissions --}}
            @if (
                $currentUser->hasPermission('access-admin-lesson-plans-index') ||
                    $currentUser->hasPermission('access-admin-lesson-plans-create') ||
                    $currentUser->hasPermission('access-admin-lesson-plans-store') ||
                    $currentUser->hasPermission('access-admin-lesson-plans-show') ||
                    $currentUser->hasPermission('access-admin-lesson-plans-edit') ||
                    $currentUser->hasPermission('access-admin-lesson-plans-update') ||
                    $currentUser->hasPermission('access-admin-lesson-plans-destroy') ||
                    $currentUser->hasPermission('access-admin-lesson-plans-toggle-status'))
                {{-- Lesson Plans Section for Manager --}}
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse"
                        data-target="#collapseManagerLessonPlans" aria-expanded="true"
                        aria-controls="collapseManagerLessonPlans">
                        <i class="fas fa-fw fa-book-open"></i>
                        <span>Lesson Plans</span>
                    </a>
                    <div id="collapseManagerLessonPlans" class="collapse" aria-labelledby="headingManagerLessonPlans"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Lesson Plans:</h6>
                            <a class="collapse-item" href="{{ route('admin.lesson-plans.create') }}">Add Lesson
                                Plan</a>
                            <a class="collapse-item" href="{{ route('admin.lesson-plans.index') }}">View Lesson
                                Plans</a>
                        </div>
                    </div>
                </li>
            @endif
        @endif

        {{-- Student Specific Sections with Permission Checks --}}
        @if ($currentGuard == 'student' && $currentUser->hasRole('student'))
            {{-- Student menu items... --}}
        @endif

        {{-- Parent Specific Menu Items --}}
        @if ($currentGuard == 'parent')
            <!-- Academic Progress -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('parent.academic.progress') }}">
                    <i class="fas fa-fw fa-chart-line"></i>
                    <span>Academic Progress</span>
                </a>
            </li>

            <!-- Financial Information -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('parent.financial') }}">
                    <i class="fas fa-fw fa-money-bill-wave"></i>
                    <span>Financial Information</span>
                </a>
            </li>

            <!-- Schedule Information -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('parent.schedule') }}">
                    <i class="fas fa-fw fa-calendar-alt"></i>
                    <span>Schedule</span>
                </a>
            </li>

            <!-- Generate Report -->
            <li class="nav-item">
                {{-- <a class="nav-link" href="{{ route('parent.generate.pdf') }}">
                    <i class="fas fa-fw fa-file-pdf"></i>
                    <span>Generate Report</span>
                </a> --}}
            </li>
        @endif
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

    <!-- User Info Card -->
    @if ($currentUser)
        <div class="sidebar-card d-none d-lg-flex">
            <div class="text-center text-white">
                <div class="mt-2 mb-1">
                    <i class="fas fa-user-circle fa-2x"></i>
                </div>
                <div class="small">Logged in as:</div>
                <div class="font-weight-bold">
                    @if ($currentGuard == 'parent')
                        {{ $currentUser->name }}
                    @elseif($currentGuard == 'instructor')
                        {{ $currentUser->instructor_name }}
                    @elseif($currentGuard == 'student')
                        {{ $currentUser->first_name . ' ' . $currentUser->last_name }}
                    @else
                        {{ $currentUser->name }}
                    @endif
                </div>
                <div>
                    <span class="badge badge-light mt-1">
                        {{-- @dd($currentGuard); --}}
                        @if ($isAdmin)
                            Admin
                        @elseif($currentGuard == 'instructor')
                            Instructor
                        @elseif($currentGuard == 'student')
                            Student
                        @elseif($currentGuard == 'parent')
                            Parent
                        @elseif($currentUser->hasRole('manager'))
                            Manager
                        @else
                            User
                        @endif
                    </span>
                </div>
            </div>
        </div>
    @endif
</ul>
