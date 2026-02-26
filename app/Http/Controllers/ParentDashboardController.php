<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentParent;
use App\Models\ProgressReport;
use App\Models\Installment;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ParentDashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        // Get the authenticated parent
        $parent = Auth::guard('parent')->user();

        if (!$parent) {
            return redirect()->route('parent.login');
        }

        // Get all students linked to this parent
        $students = Student::with(['course', 'instructor', 'practicalSchedule', 'invoices.installments', 'invoices.payments'])
            ->where('parent_id', $parent->id)
            ->get();

        // If no students found, show empty dashboard
        if ($students->isEmpty()) {
            return view('dashboards.parent-dashboard', compact('parent', 'students'));
        }

        // Get the selected student or default to the first one
        $student = null;
        if ($request->has('student_id')) {
            $student = $students->firstWhere('id', $request->student_id);
        }

        if (!$student) {
            $student = $students->first();
        }

        // Calculate course progress
        $courseProgress = [
            'theory' => [
                'completed' => $student->hours_theory ?? 0,
                'total' => $student->course ? $student->course->theory_hours : 0,
                'percentage' => 0,
            ],
            'practical' => [
                'completed' => $student->hours_practical ?? 0,
                'total' => $student->course ? $student->course->practical_hours : 0,
                'percentage' => 0,
            ],
        ];

        // Calculate percentages
        if ($courseProgress['theory']['total'] > 0) {
            $courseProgress['theory']['percentage'] = round(($courseProgress['theory']['completed'] / $courseProgress['theory']['total']) * 100);
        }

        if ($courseProgress['practical']['total'] > 0) {
            $courseProgress['practical']['percentage'] = round(($courseProgress['practical']['completed'] / $courseProgress['practical']['total']) * 100);
        }

        // Get the student's recent progress reports
        $progressReports = ProgressReport::where('student_id', $student->id)
            ->with(['instructor', 'course'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('dashboards.parent-dashboard', compact('parent', 'students', 'student', 'courseProgress', 'progressReports'));
    }

    /**
     * Set active student for the parent session
     *
     * @param int $studentId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setActiveStudent($studentId)
    {
        $parent = Auth::guard('parent')->user();

        if (!$parent) {
            return redirect()->route('parent.login');
        }

        // Verify the student belongs to this parent
        $student = Student::where('id', $studentId)->where('parent_id', $parent->id)->first();

        if (!$student) {
            return back()->with('error', 'You do not have permission to view this student.');
        }

        // Set the active student in session
        session(['active_student_id' => $studentId]);

        return redirect()->route('parent.dashboard', ['student_id' => $studentId]);
    }

    /**
     * Show student academic progress
     *
     * @return \Illuminate\View\View
     */
    public function academicProgress(Request $request)
    {
        $parent = Auth::guard('parent')->user();

        if (!$parent) {
            return redirect()->route('parent.login');
        }

        // Get students linked to this parent
        $students = Student::where('parent_id', $parent->id)->get();

        if ($students->isEmpty()) {
            return redirect()->route('parent.dashboard')->with('error', 'No students found for this parent.');
        }

        // Get the selected student
        $student = null;
        if ($request->has('student_id')) {
            $student = Student::with(['course', 'instructor'])
                ->where('id', $request->student_id)
                ->where('parent_id', $parent->id)
                ->first();
        } elseif (session('active_student_id')) {
            $student = Student::with(['course', 'instructor'])
                ->where('id', session('active_student_id'))
                ->where('parent_id', $parent->id)
                ->first();
        }

        // If no student found, use the first one
        if (!$student) {
            $student = Student::with(['course', 'instructor'])
                ->where('parent_id', $parent->id)
                ->first();

            if ($student) {
                session(['active_student_id' => $student->id]);
            } else {
                return redirect()->route('parent.dashboard')->with('error', 'No student found.');
            }
        }

        // Get progress reports for this student
        $progressReports = ProgressReport::where('student_id', $student->id)
            ->with(['instructor', 'course'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate course progress
        $courseProgress = [
            'theory' => [
                'completed' => $student->hours_theory ?? 0,
                'total' => $student->course ? $student->course->theory_hours : 0,
                'percentage' => 0,
            ],
            'practical' => [
                'completed' => $student->hours_practical ?? 0,
                'total' => $student->course ? $student->course->practical_hours : 0,
                'percentage' => 0,
            ],
        ];

        // Calculate percentages
        if ($courseProgress['theory']['total'] > 0) {
            $courseProgress['theory']['percentage'] = round(($courseProgress['theory']['completed'] / $courseProgress['theory']['total']) * 100);
        }

        if ($courseProgress['practical']['total'] > 0) {
            $courseProgress['practical']['percentage'] = round(($courseProgress['practical']['completed'] / $courseProgress['practical']['total']) * 100);
        }

        return view('dashboards.parent-dashboard', compact('parent', 'students', 'student', 'progressReports', 'courseProgress'));
    }

    public function financialInfo(Request $request)
    {
        $parent = Auth::guard('parent')->user();

        if (!$parent) {
            return redirect()->route('parent.login');
        }

        // Get students linked to this parent
        $students = Student::where('parent_id', $parent->id)->get();

        if ($students->isEmpty()) {
            return redirect()->route('parent.dashboard')->with('error', 'No students found for this parent.');
        }

        // Get the selected student
        $student = null;
        if ($request->has('student_id')) {
            $student = Student::with(['course', 'instructor', 'invoices.installments', 'invoices.payments'])
                ->where('id', $request->student_id)
                ->where('parent_id', $parent->id)
                ->first();
        } elseif (session('active_student_id')) {
            $student = Student::with(['course', 'instructor', 'invoices.installments', 'invoices.payments'])
                ->where('id', session('active_student_id'))
                ->where('parent_id', $parent->id)
                ->first();
        }

        // If no student found, use the first one
        if (!$student) {
            $student = Student::with(['course', 'instructor', 'invoices.installments', 'invoices.payments'])
                ->where('parent_id', $parent->id)
                ->first();

            if ($student) {
                session(['active_student_id' => $student->id]);
            } else {
                return redirect()->route('parent.dashboard')->with('error', 'No student found.');
            }
        }

        // Calculate payment statistics
        $invoices = $student->invoices;
        $totalBilled = $invoices->sum('amount');
        $totalPaid = 0;
        $pendingPayments = 0;

        foreach ($invoices as $invoice) {
            $totalPaid += $invoice->payments->where('status', 'completed')->sum('amount');

            // Sum up pending installments
            foreach ($invoice->installments as $installment) {
                if ($installment->status === 'pending') {
                    $pendingPayments += $installment->amount;
                }
            }
        }

        // Calculate course progress (needed for the view)
        $courseProgress = [
            'theory' => [
                'completed' => $student->hours_theory ?? 0,
                'total' => $student->course ? $student->course->theory_hours : 0,
                'percentage' => 0,
            ],
            'practical' => [
                'completed' => $student->hours_practical ?? 0,
                'total' => $student->course ? $student->course->practical_hours : 0,
                'percentage' => 0,
            ],
        ];

        // Calculate percentages
        if ($courseProgress['theory']['total'] > 0) {
            $courseProgress['theory']['percentage'] = round(($courseProgress['theory']['completed'] / $courseProgress['theory']['total']) * 100);
        }

        if ($courseProgress['practical']['total'] > 0) {
            $courseProgress['practical']['percentage'] = round(($courseProgress['practical']['completed'] / $courseProgress['practical']['total']) * 100);
        }

        // Get the student's recent progress reports (needed for the view)
        $progressReports = ProgressReport::where('student_id', $student->id)
            ->with(['instructor', 'course'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('dashboards.parent-dashboard', compact('parent', 'students', 'student', 'invoices', 'totalBilled', 'totalPaid', 'pendingPayments', 'courseProgress', 'progressReports'));
    }

    /**
     * Show schedule information
     *
     * @return \Illuminate\View\View
     */
    public function scheduleInfo(Request $request)
    {
        $parent = Auth::guard('parent')->user();

        if (!$parent) {
            return redirect()->route('parent.login');
        }

        // Get students linked to this parent
        $students = Student::where('parent_id', $parent->id)->get();

        if ($students->isEmpty()) {
            return redirect()->route('parent.dashboard')->with('error', 'No students found for this parent.');
        }

        // Get the selected student
        $student = null;
        if ($request->has('student_id')) {
            $student = Student::with(['course', 'instructor', 'practicalSchedule', 'invoices.installments', 'invoices.payments'])
                ->where('id', $request->student_id)
                ->where('parent_id', $parent->id)
                ->first();
        } elseif (session('active_student_id')) {
            $student = Student::with(['course', 'instructor', 'practicalSchedule', 'invoices.installments', 'invoices.payments'])
                ->where('id', session('active_student_id'))
                ->where('parent_id', $parent->id)
                ->first();
        }

        // If no student found, use the first one
        if (!$student) {
            $student = Student::with(['course', 'instructor', 'practicalSchedule', 'invoices.installments', 'invoices.payments'])
                ->where('parent_id', $parent->id)
                ->first();

            if ($student) {
                session(['active_student_id' => $student->id]);
            } else {
                return redirect()->route('parent.dashboard')->with('error', 'No student found.');
            }
        }

        // Calculate course progress (needed for the view)
        $courseProgress = [
            'theory' => [
                'completed' => $student->hours_theory ?? 0,
                'total' => $student->course ? $student->course->theory_hours : 0,
                'percentage' => 0,
            ],
            'practical' => [
                'completed' => $student->hours_practical ?? 0,
                'total' => $student->course ? $student->course->practical_hours : 0,
                'percentage' => 0,
            ],
        ];

        // Calculate percentages
        if ($courseProgress['theory']['total'] > 0) {
            $courseProgress['theory']['percentage'] = round(($courseProgress['theory']['completed'] / $courseProgress['theory']['total']) * 100);
        }

        if ($courseProgress['practical']['total'] > 0) {
            $courseProgress['practical']['percentage'] = round(($courseProgress['practical']['completed'] / $courseProgress['practical']['total']) * 100);
        }

        // Get the student's recent progress reports (needed for the view)
        $progressReports = ProgressReport::where('student_id', $student->id)
            ->with(['instructor', 'course'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // For financial section in the view
        $invoices = $student->invoices;
        $totalBilled = $invoices->sum('amount');
        $totalPaid = 0;
        $pendingPayments = 0;

        foreach ($invoices as $invoice) {
            $totalPaid += $invoice->payments->where('status', 'completed')->sum('amount');

            // Sum up pending installments
            foreach ($invoice->installments as $installment) {
                if ($installment->status === 'pending') {
                    $pendingPayments += $installment->amount;
                }
            }
        }

        return view('dashboards.parent-dashboard', compact('parent', 'students', 'student', 'courseProgress', 'progressReports', 'totalBilled', 'totalPaid', 'pendingPayments'));
    }

    public function generatePdf(Request $request, $studentId = null)
    {
        $parent = Auth::guard('parent')->user();

        if (!$parent) {
            return redirect()->route('parent.login');
        }

        // If no studentId provided, check request or session
        if (!$studentId) {
            if ($request->has('student_id')) {
                $studentId = $request->student_id;
            } else {
                $studentId = session('active_student_id');
            }

            if (!$studentId) {
                // If still no studentId, get the first student
                $student = Student::where('parent_id', $parent->id)->first();
                if ($student) {
                    $studentId = $student->id;
                } else {
                    return redirect()->route('parent.dashboard')->with('error', 'No student found.');
                }
            }
        }

        // Verify the student belongs to this parent
        $student = Student::with(['course', 'instructor', 'practicalSchedule', 'invoices.installments', 'invoices.payments'])
            ->where('id', $studentId)
            ->where('parent_id', $parent->id)
            ->first();

        if (!$student) {
            return redirect()->route('parent.dashboard')->with('error', 'You do not have permission to view this student.');
        }

        // Get progress reports for this student
        $progressReports = ProgressReport::where('student_id', $studentId)
            ->with(['instructor', 'course'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate payment statistics
        $invoices = $student->invoices;
        $totalBilled = $invoices->sum('amount');
        $totalPaid = 0;
        $pendingPayments = 0;

        foreach ($invoices as $invoice) {
            $totalPaid += $invoice->payments->where('status', 'completed')->sum('amount');

            // Sum up pending installments
            foreach ($invoice->installments as $installment) {
                if ($installment->status === 'pending') {
                    $pendingPayments += $installment->amount;
                }
            }
        }

        // Calculate course progress
        $courseProgress = [
            'theory' => [
                'completed' => $student->hours_theory ?? 0,
                'total' => $student->course ? $student->course->theory_hours : 0,
                'percentage' => 0,
            ],
            'practical' => [
                'completed' => $student->hours_practical ?? 0,
                'total' => $student->course ? $student->course->practical_hours : 0,
                'percentage' => 0,
            ],
        ];

        // Calculate percentages
        if ($courseProgress['theory']['total'] > 0) {
            $courseProgress['theory']['percentage'] = round(($courseProgress['theory']['completed'] / $courseProgress['theory']['total']) * 100);
        }

        if ($courseProgress['practical']['total'] > 0) {
            $courseProgress['practical']['percentage'] = round(($courseProgress['practical']['completed'] / $courseProgress['practical']['total']) * 100);
        }

        $data = compact('parent', 'student', 'progressReports', 'totalBilled', 'totalPaid', 'pendingPayments', 'courseProgress');

        // Use the PDF facade
        $pdf = PDF::loadView('parent.dashboard.pdf', $data);
        $filename = 'student_report_' . $student->id . '.pdf';

        return $pdf->download($filename);
    }
}
