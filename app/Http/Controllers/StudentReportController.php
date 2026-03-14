<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\FeedbackResponse;
use App\Models\InstructorEvaluation;
use App\Models\PracticalSession;
use App\Models\ProgressReport;
use App\Models\SessionAttendance;
use App\Models\Installment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentReportController extends Controller
{
    public function index()
    {
        $students = Student::with(['course', 'instructor'])->get();
        return view('admin.reports.students.index', compact('students'));
    }

    /**
     * Show detailed student report page
     */
    public function show($id)
    {
        $student = Student::with([
            'course',
            'instructor',
            'practicalSchedule.instructor',
            'invoices.installments',
            'invoices.payments.paymentMethod',
            'invoices.course',
            'parent',
            'certificates',
        ])->findOrFail($id);

        // Progress reports
        $progressReports = ProgressReport::where('student_id', $id)
            ->with(['instructor', 'course'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Course structure - theory and practical lesson plans
        $theoryLessonPlans = collect();
        $practicalLessonPlans = collect();
        if ($student->course) {
            $theoryLessonPlans = $student->course->theoryLessonPlans()->get();
            $practicalLessonPlans = $student->course->practicalLessonPlans()->get();
        }

        // Theory schedules for this student's course (shared group classes)
        $theorySchedules = collect();
        if ($student->course_id) {
            $query = CourseSchedule::where('course_id', $student->course_id)
                ->where('session_type', 'theory');
            if ($student->instructor_id) {
                $query->where('instructor_id', $student->instructor_id);
            }
            $theorySchedules = $query->orderBy('date')->get();
        }

        // Working hours log from course_hours table
        $courseHoursLog = DB::table('course_hours')
            ->where('student_id', $id)
            ->orderBy('date')
            ->get();

        $theoryHoursLog = $courseHoursLog->where('course_type', 1)->values();
        $practicalHoursLog = $courseHoursLog->where('course_type', 2)->values();

        // Feedback responses for this student (grouped by class_order)
        $feedbackResponses = FeedbackResponse::where('user_id', $id)
            ->where('user_type', 'student')
            ->when($student->course_id, fn($q) => $q->where('course_id', $student->course_id))
            ->with(['question'])
            ->orderBy('class_order')
            ->get()
            ->groupBy('class_order');

        // Current course certificate
        $certificate = null;
        if ($student->course_id) {
            $certificate = Certificate::where('student_id', $id)
                ->where('course_id', $student->course_id)
                ->first();
        }

        // Practical sessions (new per-session model)
        $practicalSessions = PracticalSession::where('student_id', $id)
            ->orderBy('session_number')
            ->get();

        // Session attendance records (theory class completions with class_order)
        $sessionAttendances = SessionAttendance::where('student_id', $id)
            ->where('status', 'completed')
            ->with('schedule')
            ->orderBy('class_type')
            ->orderBy('class_order')
            ->get();

        // Instructor evaluation for this student + course
        $instructorEvaluation = null;
        if ($student->course_id) {
            $instructorEvaluation = InstructorEvaluation::where('student_id', $id)
                ->where('course_id', $student->course_id)
                ->with('instructor')
                ->first();
        }

        // Practical schedule duration in hours (legacy fallback)
        $practicalDuration = null;
        if ($student->practicalSchedule) {
            $start = Carbon::parse($student->practicalSchedule->start_time);
            $end = Carbon::parse($student->practicalSchedule->end_time);
            $practicalDuration = round($end->diffInMinutes($start) / 60, 1);
        }

        // Payment calculations
        $invoices = $student->invoices;
        $totalBilled = $invoices->sum('amount');
        $totalPaid = 0;
        $pendingPayments = 0;

        foreach ($invoices as $invoice) {
            $totalPaid += $invoice->payments->where('status', 'completed')->sum('amount');
            foreach ($invoice->installments as $installment) {
                if ($installment->status === 'pending') {
                    $pendingPayments += $installment->amount;
                }
            }
        }

        // Course progress calculations
        $courseProgress = $this->calculateCourseProgress($student);

        return view('admin.reports.students.show', compact(
            'student',
            'progressReports',
            'theoryLessonPlans',
            'practicalLessonPlans',
            'theorySchedules',
            'courseHoursLog',
            'theoryHoursLog',
            'practicalHoursLog',
            'feedbackResponses',
            'certificate',
            'practicalDuration',
            'practicalSessions',
            'sessionAttendances',
            'instructorEvaluation',
            'totalBilled',
            'totalPaid',
            'pendingPayments',
            'courseProgress',
        ));
    }

    /**
     * Generate PDF report for a student
     */
    public function generatePdf($id)
    {
        $student = Student::with([
            'course',
            'instructor',
            'practicalSchedule.instructor',
            'invoices.installments',
            'invoices.payments.paymentMethod',
            'invoices.course',
            'parent',
            'certificates',
        ])->findOrFail($id);

        $progressReports = ProgressReport::where('student_id', $id)
            ->with(['instructor', 'course'])
            ->orderBy('created_at', 'desc')
            ->get();

        $theoryLessonPlans = collect();
        $practicalLessonPlans = collect();
        if ($student->course) {
            $theoryLessonPlans = $student->course->theoryLessonPlans()->get();
            $practicalLessonPlans = $student->course->practicalLessonPlans()->get();
        }

        $theorySchedules = collect();
        if ($student->course_id) {
            $query = CourseSchedule::where('course_id', $student->course_id)
                ->where('session_type', 'theory');
            if ($student->instructor_id) {
                $query->where('instructor_id', $student->instructor_id);
            }
            $theorySchedules = $query->orderBy('date')->get();
        }

        $courseHoursLog = DB::table('course_hours')
            ->where('student_id', $id)
            ->orderBy('date')
            ->get();

        $theoryHoursLog = $courseHoursLog->where('course_type', 1)->values();
        $practicalHoursLog = $courseHoursLog->where('course_type', 2)->values();

        $feedbackResponses = FeedbackResponse::where('user_id', $id)
            ->where('user_type', 'student')
            ->when($student->course_id, fn($q) => $q->where('course_id', $student->course_id))
            ->with(['question'])
            ->orderBy('class_order')
            ->get()
            ->groupBy('class_order');

        $certificate = null;
        if ($student->course_id) {
            $certificate = Certificate::where('student_id', $id)
                ->where('course_id', $student->course_id)
                ->first();
        }

        $practicalSessions = PracticalSession::where('student_id', $id)
            ->orderBy('session_number')
            ->get();

        $sessionAttendances = SessionAttendance::where('student_id', $id)
            ->where('status', 'completed')
            ->with('schedule')
            ->orderBy('class_type')
            ->orderBy('class_order')
            ->get();

        $instructorEvaluation = null;
        if ($student->course_id) {
            $instructorEvaluation = InstructorEvaluation::where('student_id', $id)
                ->where('course_id', $student->course_id)
                ->with('instructor')
                ->first();
        }

        $practicalDuration = null;
        if ($student->practicalSchedule) {
            $start = Carbon::parse($student->practicalSchedule->start_time);
            $end = Carbon::parse($student->practicalSchedule->end_time);
            $practicalDuration = round($end->diffInMinutes($start) / 60, 1);
        }

        $invoices = $student->invoices;
        $totalBilled = $invoices->sum('amount');
        $totalPaid = 0;
        $pendingPayments = 0;

        foreach ($invoices as $invoice) {
            $totalPaid += $invoice->payments->where('status', 'completed')->sum('amount');
            foreach ($invoice->installments as $installment) {
                if ($installment->status === 'pending') {
                    $pendingPayments += $installment->amount;
                }
            }
        }

        $courseProgress = $this->calculateCourseProgress($student);

        $data = compact(
            'student',
            'progressReports',
            'theoryLessonPlans',
            'practicalLessonPlans',
            'theorySchedules',
            'courseHoursLog',
            'theoryHoursLog',
            'practicalHoursLog',
            'feedbackResponses',
            'certificate',
            'practicalDuration',
            'practicalSessions',
            'sessionAttendances',
            'instructorEvaluation',
            'totalBilled',
            'totalPaid',
            'pendingPayments',
            'courseProgress',
        );

        $pdf = PDF::loadView('admin.reports.students.pdf', $data);
        $pdf->setPaper('a4', 'portrait');
        $filename = 'student_report_' . $student->id . '_' . date('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate batch reports for multiple students
     */
    public function generateBatchPdf(Request $request)
    {
        $studentIds = $request->input('student_ids', []);

        if (empty($studentIds)) {
            return redirect()->back()->with('error', 'No students selected for report generation');
        }

        $students = Student::whereIn('id', $studentIds)
            ->with([
                'course',
                'instructor',
                'invoices.installments',
                'invoices.payments',
            ])->get();

        $data = compact('students');

        $pdf = Pdf::loadView('admin.reports.students.batch-pdf', $data);
        $filename = 'student_batch_report_' . date('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Calculate course progress for a student
     */
    private function calculateCourseProgress(Student $student): array
    {
        $theoryTotal = $student->course ? ($student->course->theory_hours ?? 0) : 0;
        $practicalTotal = $student->course ? ($student->course->practical_hours ?? 0) : 0;
        $theoryCompleted = $student->hours_theory ?? 0;
        $practicalCompleted = $student->hours_practical ?? 0;

        $theoryClassesTotal = $student->course ? ($student->course->total_theory_classes ?? 0) : 0;
        $practicalClassesTotal = $student->course ? ($student->course->total_practical_classes ?? 0) : 0;

        // Estimate classes completed from hours
        $theoryHoursPerClass = ($theoryClassesTotal > 0 && $theoryTotal > 0)
            ? $theoryTotal / $theoryClassesTotal
            : 2;
        $practicalHoursPerClass = ($practicalClassesTotal > 0 && $practicalTotal > 0)
            ? $practicalTotal / $practicalClassesTotal
            : 2;

        $theoryClassesCompleted = ($theoryHoursPerClass > 0 && $theoryCompleted > 0)
            ? min($theoryClassesTotal, floor($theoryCompleted / $theoryHoursPerClass))
            : 0;
        $practicalClassesCompleted = ($practicalHoursPerClass > 0 && $practicalCompleted > 0)
            ? min($practicalClassesTotal, floor($practicalCompleted / $practicalHoursPerClass))
            : 0;

        return [
            'theory' => [
                'completed'        => $theoryCompleted,
                'total'            => $theoryTotal,
                'percentage'       => $theoryTotal > 0 ? round(($theoryCompleted / $theoryTotal) * 100) : 0,
                'classes_completed' => $theoryClassesCompleted,
                'classes_total'    => $theoryClassesTotal,
            ],
            'practical' => [
                'completed'        => $practicalCompleted,
                'total'            => $practicalTotal,
                'percentage'       => $practicalTotal > 0 ? round(($practicalCompleted / $practicalTotal) * 100) : 0,
                'classes_completed' => $practicalClassesCompleted,
                'classes_total'    => $practicalClassesTotal,
            ],
        ];
    }
}
