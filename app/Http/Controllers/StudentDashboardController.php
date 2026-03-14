<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\Installment;
use App\Models\ProgressReport;
use App\Models\SessionAttendance;
use App\Models\Student;
use App\Models\Announcement;
use App\Models\FeedbackResponse;
use App\Models\FeedbackQuestion;
use App\Models\LessonPlan;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Notifications\InstructorProgressReportNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentDashboardController extends Controller
{
    public function index()
    {
        try {
            $student = Auth::guard('student')->user();

            if (!$student) {
                return redirect()->route('login')->with('error', 'Please log in to access your dashboard.');
            }

            $student = Student::with([
                'course',
                'instructor',
                'invoices.installments',
                'invoices.payments',
                'course.lessonPlans'
            ])->find(Auth::guard('student')->id());

            // Get all dashboard data
            $upcomingSchedules = $this->getUpcomingSchedules($student);
            $pastSchedules     = $this->getPastSchedules($student);
            $pendingInstallments = $this->getPendingInstallments($student);
            $courseProgress = $this->calculateCourseProgress($student);
            $announcements = $this->getRecentAnnouncements($student);
            $pendingFeedback = $this->getPendingFeedback($student);
            $progressReports = $this->getProgressReports($student);
            $invoiceDetails = $this->getInvoiceDetails($student);
            $paymentHistory = $this->getPaymentHistory($student);
            $paymentMethods = PaymentMethod::where('is_active', true)->get();

            return view('dashboards.student-dashboard', compact(
                'student',
                'upcomingSchedules',
                'pastSchedules',
                'pendingInstallments',
                'courseProgress',
                'announcements',
                'pendingFeedback',
                'progressReports',
                'invoiceDetails',
                'paymentHistory',
                'paymentMethods'
            ));
        } catch (\Exception $e) {
            Log::error('Student Dashboard Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'An error occurred while loading your dashboard.');
        }
    }

    protected function getProgressReports(Student $student)
    {
        return ProgressReport::with(['instructor', 'course'])
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'instructor_name' => $report->instructor->instructor_name,
                    'course_name' => $report->course->course_name,
                    'rating' => $report->rating ? round($report->rating, 1) . '/5' : 'N/A',
                    'date' => $report->created_at->format('M d, Y'),
                    'performance_notes' => Str::limit($report->performance_notes, 100),
                ];
            });
    }

    protected function getUpcomingSchedules(Student $student)
    {
        // Only show schedules explicitly assigned to this student (via pivot table).
        // Populated at registration and when instructor assigns next classes.
        $attendedIds = SessionAttendance::where('student_id', $student->id)
            ->pluck('course_schedule_id');

        return $student->assignedSchedules()
            ->with('instructor')
            ->where('date', '>=', now()->toDateString())
            ->where('is_active', true)
            ->whereNotIn('course_schedules.id', $attendedIds)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id'             => $schedule->id,
                    'date'           => Carbon::parse($schedule->date)->format('M d, Y'),
                    'start_time'     => Carbon::parse($schedule->start_time)->format('h:i A'),
                    'end_time'       => Carbon::parse($schedule->end_time)->format('h:i A'),
                    'session_type'   => ucfirst($schedule->session_type),
                    'instructor_name'=> $schedule->instructor->instructor_name ?? 'N/A',
                    'day_name'       => Carbon::parse($schedule->date)->format('l'),
                ];
            });
    }

    protected function getPastSchedules(Student $student)
    {
        // Only show sessions the student actually attended (via session_attendance)
        return SessionAttendance::where('student_id', $student->id)
            ->where('status', 'completed')
            ->with(['schedule.instructor'])
            ->orderBy('completed_at', 'desc')
            ->take(20)
            ->get()
            ->map(function ($attendance) {
                $schedule = $attendance->schedule;
                if (!$schedule) return null;
                return [
                    'id'             => $schedule->id,
                    'date'           => Carbon::parse($schedule->date)->format('M d, Y'),
                    'start_time'     => Carbon::parse($schedule->start_time)->format('h:i A'),
                    'end_time'       => Carbon::parse($schedule->end_time)->format('h:i A'),
                    'session_type'   => ucfirst($attendance->class_type ?? $schedule->session_type),
                    'instructor_name'=> $schedule->instructor->instructor_name ?? 'N/A',
                    'day_name'       => Carbon::parse($schedule->date)->format('l'),
                    'class_order'    => $attendance->class_order,
                    'completed_at'   => Carbon::parse($attendance->completed_at)->format('M d, Y'),
                ];
            })
            ->filter() // remove nulls from missing schedules
            ->values();
    }

    protected function getPendingInstallments(Student $student)
    {
        return Installment::whereHas('invoice', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->get()
            ->map(function ($installment) {
                $dueDate = Carbon::parse($installment->due_date);
                $isOverdue = $dueDate->isPast();
                $daysUntilDue = $dueDate->diffInDays(now(), false);

                return [
                    'id' => $installment->id,
                    'amount' => number_format($installment->amount, 2),
                    'due_date' => $dueDate->format('M d, Y'),
                    'is_overdue' => $isOverdue,
                    'days_until_due' => $daysUntilDue,
                    'status_text' => $isOverdue ? 'Overdue' : 'Pending',
                    'urgency_class' => $isOverdue ? 'danger' : ($daysUntilDue <= 7 ? 'warning' : 'info'),
                ];
            });
    }

    protected function calculateCourseProgress(Student $student)
    {
        $course = $student->course;
        $totalHours = $course->theory_hours + $course->practical_hours;
        $attendedHours = $student->hours_theory + $student->hours_practical;
        $progressPercentage = $totalHours > 0 ? round(($attendedHours / $totalHours) * 100, 2) : 0;

        // Calculate lesson plan progress
        $totalLessonPlans = $course->lessonPlans()->count();
        $completedLessonPlans = $this->getCompletedLessonPlansCount($student);
        $lessonPlanProgress = $totalLessonPlans > 0 ? round(($completedLessonPlans / $totalLessonPlans) * 100, 2) : 0;

        // Class count progress (from session_attendance)
        $completedTheoryClasses   = SessionAttendance::where('student_id', $student->id)
            ->where('class_type', 'theory')
            ->where('status', 'completed')
            ->count();
        $completedPracticalClasses = SessionAttendance::where('student_id', $student->id)
            ->where('class_type', 'practical')
            ->where('status', 'completed')
            ->count();
        $totalTheoryClasses   = $course->total_theory_classes   ?? 0;
        $totalPracticalClasses = $course->total_practical_classes ?? 0;

        return [
            'total_hours' => $totalHours,
            'attended_hours' => $attendedHours,
            'progress_percentage' => $progressPercentage,
            'theory_hours' => [
                'total' => $course->theory_hours,
                'completed' => $student->hours_theory,
                'status' => $student->theory_status ?? 'In Progress',
                'percentage' => $course->theory_hours > 0 ? round(($student->hours_theory / $course->theory_hours) * 100, 2) : 0,
            ],
            'practical_hours' => [
                'total' => $course->practical_hours,
                'completed' => $student->hours_practical,
                'status' => $student->practical_status ?? 'In Progress',
                'percentage' => $course->practical_hours > 0 ? round(($student->hours_practical / $course->practical_hours) * 100, 2) : 0,
            ],
            'lesson_plans' => [
                'total' => $totalLessonPlans,
                'completed' => $completedLessonPlans,
                'percentage' => $lessonPlanProgress,
            ],
            'theory_classes' => [
                'completed'  => $completedTheoryClasses,
                'required'   => $totalTheoryClasses,
                'percentage' => $totalTheoryClasses > 0 ? min(100, round(($completedTheoryClasses / $totalTheoryClasses) * 100)) : 0,
            ],
            'practical_classes' => [
                'completed'  => $completedPracticalClasses,
                'required'   => $totalPracticalClasses,
                'percentage' => $totalPracticalClasses > 0 ? min(100, round(($completedPracticalClasses / $totalPracticalClasses) * 100)) : 0,
            ],
        ];
    }

    protected function getCompletedLessonPlansCount(Student $student)
    {
        return FeedbackResponse::where('user_id', $student->id)
            ->where('user_type', 'student')
            ->where('course_id', $student->course_id)
            ->distinct('class_order')
            ->count();
    }

    protected function getRecentAnnouncements(Student $student)
    {
        return Announcement::visibleTo($student)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($announcement) {
                return [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => Str::limit($announcement->content, 150),
                    'date' => $announcement->created_at->format('M d, Y'),
                    'expires_at' => $announcement->expires_at ? $announcement->expires_at->format('M d, Y') : null,
                    'attachment_url' => $announcement->attachment_url,
                ];
            });
    }

    protected function getPendingFeedback(Student $student)
    {
        // Source of truth: session_attendance records marked as completed for this student
        $completedSessions = SessionAttendance::where('student_id', $student->id)
            ->where('status', 'completed')
            ->with('schedule.instructor')
            ->get();

        $pendingFeedback = [];

        foreach ($completedSessions as $attendance) {
            // Check if student already submitted feedback for this class_order + class_type
            $feedbackExists = FeedbackResponse::where('user_id', $student->id)
                ->where('user_type', 'student')
                ->where('course_id', $student->course_id)
                ->where('class_order', $attendance->class_order)
                ->where('class_type', $attendance->class_type)
                ->exists();

            if (!$feedbackExists) {
                $schedule = $attendance->schedule;
                if (!$schedule) continue;

                $pendingFeedback[] = [
                    'attendance_id'  => $attendance->id,
                    'schedule_id'    => $schedule->id,
                    'course_name'    => optional($student->course)->course_name ?? 'N/A',
                    'instructor_name'=> optional(optional($schedule)->instructor)->instructor_name ?? 'N/A',
                    'session_date'   => Carbon::parse($schedule->date)->format('M d, Y'),
                    'session_type'   => ucfirst($attendance->class_type ?? $schedule->session_type),
                    'class_order'    => $attendance->class_order,
                    'status'         => 'Feedback Pending',
                    'days_ago'       => Carbon::parse($schedule->date)->diffInDays(now()),
                ];
            }
        }

        return collect($pendingFeedback)->take(10);
    }

    protected function getInvoiceDetails(Student $student)
    {
        $invoices = Invoice::with(['installments', 'payments'])
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalAmount = $invoices->sum('amount');
        $totalPaid = $invoices->sum(function ($invoice) {
            return $invoice->payments->where('status', 'completed')->sum('amount');
        });
        $totalPending = $totalAmount - $totalPaid;

        $installmentSummary = Installment::whereHas('invoice', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })->selectRaw('
            COUNT(*) as total_installments,
            SUM(CASE WHEN status = "paid" THEN 1 ELSE 0 END) as paid_installments,
            SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_installments,
            SUM(CASE WHEN status = "pending" AND due_date < NOW() THEN 1 ELSE 0 END) as overdue_installments
        ')->first();

        return [
            'total_amount' => number_format($totalAmount, 2),
            'total_paid' => number_format($totalPaid, 2),
            'total_pending' => number_format($totalPending, 2),
            'payment_percentage' => $totalAmount > 0 ? round(($totalPaid / $totalAmount) * 100, 2) : 0,
            'installments' => [
                'total' => $installmentSummary->total_installments ?? 0,
                'paid' => $installmentSummary->paid_installments ?? 0,
                'pending' => $installmentSummary->pending_installments ?? 0,
                'overdue' => $installmentSummary->overdue_installments ?? 0,
            ],
            'invoices_count' => $invoices->count(),
            'invoices' => $invoices->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'amount' => number_format($invoice->amount, 2),
                    'status' => $invoice->status,
                    'created_at' => $invoice->created_at->format('M d, Y'),
                    'installments_count' => $invoice->installments->count(),
                    'paid_installments' => $invoice->installments->where('status', 'paid')->count(),
                ];
            }),
        ];
    }

    protected function getPaymentHistory(Student $student)
    {
        return Payment::whereHas('invoice', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
            ->with(['invoice', 'paymentMethod'])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => number_format($payment->amount, 2),
                    'date' => $payment->created_at->format('M d, Y'),
                    'method' => $payment->paymentMethod->name ?? 'N/A',
                    'transaction_id' => $payment->transaction_id,
                    'invoice_number' => $payment->invoice->invoice_number,
                ];
            });
    }

    // Progress Report Methods
    public function sendProgressReport()
    {
        try {
            $student = Auth::guard('student')->user();
            $progressData = $this->calculateCourseProgress($student);

            $student->instructor->notify(new InstructorProgressReportNotification($student, $progressData));

            return response()->json([
                'success' => true,
                'message' => 'Progress report sent successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Progress Report Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send progress report.',
            ], 500);
        }
    }

    // Feedback Methods

    /**
     * Load feedback questions for a specific attendance record (identified by attendanceId).
     * The attendance record tells us class_order + class_type so we can find the right lesson plan.
     */
    public function getAvailableFeedback($attendanceId)
    {
        try {
            $student   = Auth::guard('student')->user();
            $attendance = SessionAttendance::findOrFail($attendanceId);

            if ($attendance->student_id !== $student->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.']);
            }

            // Find the lesson plan for this class_order + class_type
            $lessonPlan = $student->course->lessonPlans()
                ->wherePivot('class_type', $attendance->class_type)
                ->wherePivot('class_order', $attendance->class_order)
                ->first();

            if (!$lessonPlan) {
                return response()->json([
                    'success' => false,
                    'message' => 'No lesson plan found for class #' . $attendance->class_order . ' (' . $attendance->class_type . ').',
                ]);
            }

            $feedbackQuestions = $lessonPlan->feedbackQuestions()
                ->where('is_active', true)
                ->orderBy('display_order')
                ->get();

            $schedule = $attendance->schedule;

            return response()->json([
                'success'     => true,
                'lesson_plan' => $lessonPlan,
                'questions'   => $feedbackQuestions,
                'schedule'    => $schedule,
                'class_order' => $attendance->class_order,
                'class_type'  => $attendance->class_type,
            ]);
        } catch (\Exception $e) {
            Log::error('Get Available Feedback Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load feedback form.'], 500);
        }
    }

    /**
     * Submit student feedback for an attendance record.
     * Derives class_order, class_type, and course_lesson_plan_id from the attendance record.
     */
    public function submitFeedback(Request $request, $attendanceId)
    {
        try {
            $student    = Auth::guard('student')->user();
            $attendance = SessionAttendance::findOrFail($attendanceId);

            if ($attendance->student_id !== $student->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.']);
            }

            // Look up the course_lesson_plan pivot row so we can store its ID
            $clp = DB::table('course_lesson_plan')
                ->where('course_id', $student->course_id)
                ->where('class_type', $attendance->class_type)
                ->where('class_order', $attendance->class_order)
                ->first();

            $responses = $request->input('responses', []);

            DB::beginTransaction();
            foreach ($responses as $questionId => $response) {
                FeedbackResponse::create([
                    'feedback_question_id' => $questionId,
                    'course_id'            => $student->course_id,
                    'user_id'              => $student->id,
                    'user_type'            => 'student',
                    'response'             => $response['answer'] ?? null,
                    'comments'             => $response['comments'] ?? null,
                    'class_order'          => $attendance->class_order,
                    'class_type'           => $attendance->class_type,
                    'course_lesson_plan_id'=> $clp ? $clp->id : null,
                ]);
            }
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Feedback submitted successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Submit Feedback Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to submit feedback.'], 500);
        }
    }

    // Payment Methods
    public function processPayment(Request $request, $installmentId)
    {
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'payment_details' => 'array'
        ]);

        $student = Auth::guard('student')->user();
        $installment = Installment::findOrFail($installmentId);

        if ($installment->invoice->student_id !== $student->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this installment.'
            ]);
        }

        if ($installment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This installment has already been paid.'
            ]);
        }

        DB::beginTransaction();
        try {
            // Create payment record
            $payment = Payment::create([
                'invoice_id' => $installment->invoice_id,
                'payment_method_id' => $request->payment_method_id,
                'amount' => $installment->amount,
                'transaction_id' => $this->generateTransactionId(),
                'status' => 'completed',
                'payment_details' => $request->payment_details ?? []
            ]);

            // Mark installment as paid
            $installment->update([
                'status' => 'paid',
                'paid_at' => now()
            ]);

            // Update invoice status if all installments are paid
            $this->updateInvoiceStatus($installment->invoice);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully!',
                'payment_id' => $payment->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment Processing Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed. Please try again.'
            ], 500);
        }
    }

    private function generateTransactionId()
    {
        return 'TRX-' . strtoupper(uniqid()) . '-' . time();
    }

    private function updateInvoiceStatus($invoice)
    {
        $totalInstallments = $invoice->installments->count();
        $paidInstallments = $invoice->installments->where('status', 'paid')->count();

        if ($totalInstallments === $paidInstallments) {
            $invoice->update(['status' => 'paid']);
        } elseif ($paidInstallments > 0) {
            $invoice->update(['status' => 'partial']);
        }
    }
}