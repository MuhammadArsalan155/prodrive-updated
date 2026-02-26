<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Models\ProgressReport;
use App\Models\Installment;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
// Remove the facade import
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
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $student = Student::with([
            'course', 
            'instructor', 
            'practicalSchedule',
            'invoices.installments',
            'invoices.payments',
            'roles'
        ])->findOrFail($id);

        // Get progress reports for this student
        $progressReports = ProgressReport::where('student_id', $id)
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
                'percentage' => 0
            ],
            'practical' => [
                'completed' => $student->hours_practical ?? 0,
                'total' => $student->course ? $student->course->practical_hours : 0,
                'percentage' => 0
            ]
        ];

        // Calculate percentages
        if ($courseProgress['theory']['total'] > 0) {
            $courseProgress['theory']['percentage'] = round(($courseProgress['theory']['completed'] / $courseProgress['theory']['total']) * 100);
        }
        
        if ($courseProgress['practical']['total'] > 0) {
            $courseProgress['practical']['percentage'] = round(($courseProgress['practical']['completed'] / $courseProgress['practical']['total']) * 100);
        }

        return view('admin.reports.students.show', compact(
            'student', 
            'progressReports', 
            'totalBilled', 
            'totalPaid', 
            'pendingPayments',
            'courseProgress'
        ));
    }

    /**
     * Generate PDF report for a student
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function generatePdf($id)
    {
        $student = Student::with([
            'course', 
            'instructor', 
            'practicalSchedule',
            'invoices.installments',
            'invoices.payments',
            'roles'
        ])->findOrFail($id);

        // Get progress reports for this student
        $progressReports = ProgressReport::where('student_id', $id)
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
                'percentage' => 0
            ],
            'practical' => [
                'completed' => $student->hours_practical ?? 0,
                'total' => $student->course ? $student->course->practical_hours : 0,
                'percentage' => 0
            ]
        ];

        // Calculate percentages
        if ($courseProgress['theory']['total'] > 0) {
            $courseProgress['theory']['percentage'] = round(($courseProgress['theory']['completed'] / $courseProgress['theory']['total']) * 100);
        }
        
        if ($courseProgress['practical']['total'] > 0) {
            $courseProgress['practical']['percentage'] = round(($courseProgress['practical']['completed'] / $courseProgress['practical']['total']) * 100);
        }

        $data = compact(
            'student', 
            'progressReports', 
            'totalBilled', 
            'totalPaid', 
            'pendingPayments',
            'courseProgress'
        );

        // Use the facade through the alias
        $pdf = PDF::loadView('admin.reports.students.pdf', $data);
        $filename = 'student_report_' . $student->id . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate batch reports for multiple students
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
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
                'invoices.payments'
            ])->get();

        $data = compact('students');
        
        // Use the facade through the alias
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.students.batch-pdf', $data);
        $filename = 'student_batch_report_' . date('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}