<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\CourseInstallmentPlanController;
use App\Http\Controllers\Admin\CourseScheduleController;
use App\Http\Controllers\Admin\InstructorController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\InstallmentPaymentController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ManagerDashboardController;
use App\Http\Controllers\ParentDashboardController;
use App\Http\Controllers\ProgressReportController;
use App\Http\Controllers\RegisterStudentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentDashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Artisan;

Route::get('/run-migrations', function () {
    Artisan::call('migrate', [
        '--path' => 'database/migrations/2025_08_16_110255_add_payment_process_flag_to_students_table.php',
        '--force' => true
    ]);

    Artisan::call('migrate', [
        '--path' => 'database/migrations/2025_08_18_164114_add_additional_price_to_payment_methods_table.php',
        '--force' => true
    ]);

    return "Selected migrations executed successfully!";
});


Route::get('/', function () {
    return view('register/userregister');
    // return redirect()->route('login');
})->name('registerPage');

// Show installment payment page
Route::get('/student/pay-installment/{installment}', [InstallmentPaymentController::class, 'showInstallmentPayment'])
    ->name('installment.payment.show');

// Process installment payment
Route::post('/installment/{installment}/pay', [InstallmentPaymentController::class, 'processInstallmentPayment'])
    ->name('installment.payment.process');

// Handle successful installment payment
Route::get('/installment/payment/success', [InstallmentPaymentController::class, 'handleInstallmentPaymentSuccess'])
    ->name('installment.payment.success');

// Handle cancelled installment payment
Route::get('/installment/{installment}/payment/cancel', function($installmentId) {
    return redirect()->route('installment.payment.show', $installmentId)
        ->with('error', 'Payment was cancelled. Please try again.');
})->name('installment.payment.cancel');

// API route for payment methods (if not already exists)
Route::get('/api/payment-methods', [RegisterStudentController::class, 'getPaymentMethods']);


// Auth::routes();
Route::middleware(['auth:parent'])->prefix('parent')->name('parent.')->group(function () {

    Route::get('/dashboard', [ParentDashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/academic-progress', [ParentDashboardController::class, 'academicProgress'])->name('academic.progress');

    // Financial info page (Same template, different data)
    Route::get('/financial', [ParentDashboardController::class, 'financialInfo'])->name('financial');

    // Schedule info page (Same template, different data)
    Route::get('/schedule', [ParentDashboardController::class, 'scheduleInfo'])->name('schedule');

    // Set active student (redirects back to dashboard with student_id parameter)
    Route::get('/set-active-student/{studentId}', [ParentDashboardController::class, 'setActiveStudent'])->name('set.active.student');

    // Generate PDF report
    Route::get('/generate-pdf/{studentId?}', [ParentDashboardController::class, 'generatePdf'])->name('generate.pdf');
});


Route::get('/user/roles', [LoginController::class, 'getRoles'])->name('user.roles');
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('admin.dashboard');

Route::middleware(['web']) //,'throttle:6,1'
    ->group(function () {
        Route::get('/login', [LoginController::class, 'loginpage'])->name('login');
        Route::post('/login/validate', [LoginController::class, 'validateLogin'])->name('user.login.validate');

        Route::post('/login', [LoginController::class, 'login'])->name('user.login');

        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::post('/forgot-password/validate', [ForgotPasswordController::class, 'validateForgotPassword'])->name('password.validate');
        Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

    });

Route::group(['middleware' => 'auth', 'role.permission'], function () {
    Route::post('course-schedules/copy-month', [CourseScheduleController::class, 'copyMonth'])->name('course-schedules.copy-month');
    Route::post('course-schedules/{courseSchedule}/toggle-status', [CourseScheduleController::class, 'toggleStatus'])->name('course-schedules.toggle-status');

    // Resource route last
    Route::resource('course-schedules', CourseScheduleController::class);
    Route::prefix('course-schedules')->name('course-schedules.')->group(function () {

        // Store multiple schedules at once
        Route::post('store-multiple', [CourseScheduleController::class, 'storeMultiple'])
            ->name('store-multiple');


        // Bulk status toggle
        Route::post('bulk-toggle-status', [CourseScheduleController::class, 'bulkToggleStatus'])
            ->name('bulk-toggle-status');
    });

    //Admin
    Route::get('getprofile', [AdminController::class, 'openProfile'])->name('profile');
    Route::post('update_profile', [AdminController::class, 'updateProfile'])->name('update_profile');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('addstudent', [StudentController::class, 'addstudent'])->name('addstudent');
    Route::get('get-courses', [StudentController::class, 'getCourses']);
    Route::get('get-course-instructors/{courseId}', [StudentController::class, 'getCourseInstructors']);
    Route::get('get-available-schedules/{courseId}/{instructorId}', [StudentController::class, 'getAvailableSchedules']);

    Route::get('viewstudent', [StudentController::class, 'viewstudent'])->name('viewstudent');
    Route::get('view_student/{student}', [StudentController::class, 'viewSingleStudent'])->name('view_student');
    Route::post('add_student', [StudentController::class, 'add_student'])->name('add_student');
    Route::get('/edit-student/{student}', [StudentController::class, 'edit_student'])->name('edit_student');
    //Route::get('delete_student/{id}', [StudentController::class, 'delete_student'])->name('delete_student');
    Route::post('/update-student', [StudentController::class, 'update_student'])->name('update_student');
    Route::post('change_course_status', [StudentController::class, 'change_course_status'])->name('change_course_status');
    Route::post('change_payment_status', [StudentController::class, 'change_payment_status'])->name('change_payment_status');
    Route::post('uplaodphoto', [StudentController::class, 'uploadPhoto'])->name('uplaodphoto');
    Route::post('add_course_hours', [StudentController::class, 'addCourseHours'])->name('add_course_hours');
    Route::post('update_roadtest_info', [StudentController::class, 'addRoadTestInfo'])->name('update_roadtest_info');
    Route::get('deletecoursehours/{id}', [StudentController::class, 'deleteCourseHour'])->name('deletecoursehours');

    Route::resource('payment-methods', PaymentMethodController::class)->names([
        'index' => 'admin.payment-methods.index',
        'create' => 'admin.payment-methods.create',
        'store' => 'admin.payment-methods.store',
        'edit' => 'admin.payment-methods.edit',
        'update' => 'admin.payment-methods.update',
        'destroy' => 'admin.payment-methods.destroy',
    ]);

    Route::post('payment-methods/{paymentMethod}/toggle-status', [PaymentMethodController::class, 'toggleStatus'])->name('admin.payment-methods.toggle-status');
    Route::post('/admin/process-cash-payment', [App\Http\Controllers\Admin\AdminCashPaymentController::class, 'processCashPayment'])->name('admin.cash-payment.process');
    Route::post('/send-installment-reminder', [StudentController::class, 'sendReminder'])->name('admin.send-installment-reminder');
    Route::get('/students/{student}/view', ['uses' => 'StudentController@viewSingleStudent', 'as' => 'students.view']);
    Route::post('/update-course-status', [App\Http\Controllers\StudentController::class, 'updateCourseStatus'])->name('update.course.status');
    Route::resource('students', 'StudentController')->except(['show']);
    Route::delete('/delete-student/{id}', [StudentController::class, 'delete_student'])->name('delete_student');

    Route::get('get-schedules-for-edit/{courseId}/{instructorId}/{currentScheduleId?}', [StudentController::class, 'getSchedulesForEdit']);

});

// In routes/web.php
Route::middleware(['web'])
    //->prefix('instructor')
    ->group(function () {
        Route::get('/manager/dashboard', [ManagerDashboardController::class, 'index'])->name('manager.dashboard');

        Route::get('/instructor/dashboard', [DashboardController::class, 'index'])->name('instructor.dashboard');

        Route::get('/instructor/theory-calendar', [DashboardController::class, 'theoryCalendar'])->name('instructor.theory.calendar');
        Route::get('/instructor/practical-calendar', [DashboardController::class, 'practicalCalendar'])->name('instructor.practical.calendar');

        Route::get('/instructor/students/{status}', [DashboardController::class, 'studentsByStatus'])->name('instructor.students.status');

        Route::post('/instructor/mark-theory-complete', [DashboardController::class, 'markTheoryComplete'])->name('instructor.mark.theory.complete');
        Route::post('/instructor/assign-practical-slot', [DashboardController::class, 'assignPracticalSlot'])->name('instructor.assign.practical');
        Route::post('/instructor/submit-practical-feedback', [DashboardController::class, 'submitPracticalFeedback'])->name('instructor.submit.practical.feedback');
        Route::post('/instructor/assign-practical-sessions', [DashboardController::class, 'assignPracticalSessions'])->name('instructor.assign.practical.sessions');
        Route::post('/instructor/practical-sessions/{session}/feedback', [DashboardController::class, 'submitSessionFeedback'])->name('instructor.practical.session.feedback');

        // Mark a theory/practical schedule session as complete (creates session_attendance records)
        Route::post('/instructor/schedule/{schedule}/mark-complete', [DashboardController::class, 'markClassComplete'])->name('instructor.schedule.mark.complete');

        // Instructor submits end-of-course evaluation for a student
        Route::post('/instructor/students/{student}/evaluation', [DashboardController::class, 'submitEvaluation'])->name('instructor.student.evaluation');
        Route::post('/instructor/students/{student}/assign-schedules', [DashboardController::class, 'assignSchedulesToStudent'])->name('instructor.student.assign.schedules');
        Route::post('/instructor/students/{student}/log-session', [DashboardController::class, 'logSession'])->name('instructor.student.log.session');

        Route::get('/instructor/student/{student}', [DashboardController::class, 'viewStudent'])->name('instructor.student.view');

        Route::get('addcourse', [CourseController::class, 'addcourse'])->name('addcourse');
        Route::get('viewcourse', [CourseController::class, 'viewcourse'])->name('viewcourse');
        Route::post('add_course', [CourseController::class, 'add_course'])->name('add_course');
        Route::get('edit_course/{course}', [CourseController::class, 'edit_course'])->name('edit_course');
        Route::get('delete_course/{id}', [CourseController::class, 'delete_course'])->name('delete_course');
        Route::post('update_course', [CourseController::class, 'update_course'])->name('update_course');
        Route::post('/courses/{id}/toggle-status', [CourseController::class, 'toggleCourseStatus'])->name('toggle_course_status');

        Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');

        // Send Progress Report
        Route::post('/student/send-progress-report', [StudentDashboardController::class, 'sendProgressReport'])->name('student.send-progress-report');

        // Student feedback — keyed by attendance ID (class_order + class_type encoded in attendance)
        Route::get('/student/feedback/{attendanceId}', [StudentDashboardController::class, 'getAvailableFeedback'])->name('student.feedback.load');
        Route::post('/student/feedback/{attendanceId}', [StudentDashboardController::class, 'submitFeedback'])->name('student.feedback.submit');

        Route::get('/student/progress-reports', [ProgressReportController::class, 'index'])->name('student.progress-reports.index');

        // View specific progress report
        Route::get('/student/progress-reports/{reportId}', [ProgressReportController::class, 'show'])->name('student.progress-reports.show');
        Route::post('/instructor/progress-reports', [ProgressReportController::class, 'store'])->name('instructor.progress-reports.store');

        // Update progress report
        Route::put('/instructor/progress-reports/{reportId}', [ProgressReportController::class, 'update'])->name('instructor.progress-reports.update');
    });

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role.permission'])
    ->group(function () {
        Route::get('instructor/add', [InstructorController::class, 'addinstructor'])->name('addinstructor');
        Route::get('instructor/view', [InstructorController::class, 'viewinstructor'])->name('viewinstructor');
        Route::post('instructor/add', [InstructorController::class, 'add_instructor'])->name('add_instructor');
        Route::get('instructor/edit/{instructor}', [InstructorController::class, 'edit_instructor'])->name('edit_instructor');
        Route::delete('instructor/delete/{id}', [InstructorController::class, 'delete_instructor'])->name('delete_instructor');
        Route::post('instructor/update', [InstructorController::class, 'update_instructor'])->name('update_instructor');

        Route::resource('course-installment-plans', CourseInstallmentPlanController::class);
        Route::post('course-installment-plans/{courseInstallmentPlan}/toggle-status', [CourseInstallmentPlanController::class, 'toggleStatus'])->name('course-installment-plans.toggle-status');
        Route::post('course-installment-plans/default', [CourseInstallmentPlanController::class, 'createDefaultPlans'])->name('course-installment-plans.default');

        Route::resource('managers', 'App\Http\Controllers\Admin\ManagerController');

        Route::resource('lesson-plans', App\Http\Controllers\Admin\LessonPlanController::class);

        // Extra route for toggling lesson plan status
        Route::post('lesson-plans/{lessonPlan}/toggle-status', [App\Http\Controllers\Admin\LessonPlanController::class, 'toggleStatus'])->name('lesson-plans.toggle-status');
    });

Route::get('/register', [RegisterStudentController::class, 'index'])->name('register');
Route::get('/payment/success', [PaymentController::class, 'handlePaymentSuccess'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'handlePaymentCancel'])->name('payment.cancel');
Route::get('/payment/result', function () {
    abort_unless(session()->has('payment_result'), 404);
    return view('register.payment-result', session('payment_result'));
})->name('payment.result');
Route::get('/certificate/verify', [App\Http\Controllers\Admin\CertificateController::class, 'verify'])->name('certificate.verify');

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
         Route::resource('invoices', InvoiceController::class);

    // AJAX routes for invoice creation
    //  Route::get('invoices/get-student-details/{id}', [InvoiceController::class, 'getStudentDetails'])->name('invoices.get-student-details');
    //     Route::get('invoices/get-course-details/{id}', [InvoiceController::class, 'getCourseDetails'])->name('invoices.get-course-details');
    //     Route::get('invoices/get-installment-plans', [InvoiceController::class, 'getInstallmentPlans'])->name('invoices.get-installment-plans');
    //     Route::post('invoices/preview-installment-schedule', [InvoiceController::class, 'previewInstallmentSchedule'])->name('invoices.preview-installment-schedule');
        Route::prefix('reports/students')
            ->name('reports.students.')
            ->group(function () {
                Route::get('/', [App\Http\Controllers\StudentReportController::class, 'index'])->name('index');
                Route::get('/{id}', [App\Http\Controllers\StudentReportController::class, 'show'])->name('show');
                Route::get('/{id}/pdf', [App\Http\Controllers\StudentReportController::class, 'generatePdf'])->name('pdf');
                Route::post('/batch-pdf', [App\Http\Controllers\StudentReportController::class, 'generateBatchPdf'])->name('batch-pdf');
            });
        Route::get('/certificates', [App\Http\Controllers\Admin\CertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/create', [App\Http\Controllers\Admin\CertificateController::class, 'create'])->name('certificates.create');
        Route::post('/certificates', [App\Http\Controllers\Admin\CertificateController::class, 'store'])->name('certificates.store');
        Route::get('/certificates/eligible', [App\Http\Controllers\Admin\CertificateController::class, 'eligibleStudents'])->name('certificates.eligible');
        Route::post('/certificates/bulk', [App\Http\Controllers\Admin\CertificateController::class, 'generateBulk'])->name('certificates.bulk');
        Route::get('/certificates/{id}', [App\Http\Controllers\Admin\CertificateController::class, 'show'])->name('certificates.show');
        Route::get('/certificates/{id}/download', [App\Http\Controllers\Admin\CertificateController::class, 'download'])->name('certificates.download');
        Route::get('/certificates/{id}/regenerate', [App\Http\Controllers\Admin\CertificateController::class, 'regenerate'])->name('certificates.regenerate');
        Route::delete('/certificates/{id}', [App\Http\Controllers\Admin\CertificateController::class, 'destroy'])->name('certificates.destroy');
        // Add this route for single certificate generation
        Route::post('/certificates/generate-single/{student}', [CertificateController::class, 'generateSingle'])->name('certificates.generate-single');

        Route::resource('announcements', AnnouncementController::class);
        Route::get('announcements/{announcement}/download', [AnnouncementController::class, 'downloadAttachment'])->name('announcements.download');
    });

Route::get('announcements/{announcement}/download', [App\Http\Controllers\Api\AnnouncementController::class, 'downloadAttachment'])
    ->middleware(['auth'])
    ->name('announcements.download');

// API routes
Route::group(['middleware' => ['auth'], 'prefix' => 'api'], function () {
    Route::get('announcements', [App\Http\Controllers\Api\AnnouncementController::class, 'getAnnouncements']);
});
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role.permission'])
    ->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/{type}/{id}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{type}/{id}', [UserManagementController::class, 'update'])->name('users.update');
        Route::get('/users/{type}/{id}/details', [UserManagementController::class, 'show'])->name('users.show');
        Route::get('/users/logs', [UserManagementController::class, 'logs'])->name('users.logs');
    });

// Role Management Routes
Route::prefix('admin/roles')
    ->name('admin.')
    ->middleware(['auth', 'role.permission'])
    ->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/{id}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');

        Route::get('/{id}/edit-permissions', [RoleController::class, 'editPermissions'])->name('roles.edit-permissions');
        Route::put('/{id}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.update-permissions');

        Route::get('/assign', [RoleController::class, 'showAssignRoles'])->name('roles.assign');
        Route::post('/assign', [RoleController::class, 'assignRoles'])->name('roles.assign-roles');

        Route::get('/get-models', [RoleController::class, 'getModels'])->name('roles.get-models');
        Route::get('/get-model-roles', [RoleController::class, 'getModelRoles'])->name('roles.get-model-roles');
    });
