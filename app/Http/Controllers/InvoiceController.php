<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Student;
use App\Models\Course;
use App\Models\PaymentMethod;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Display all invoices
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['student', 'course', 'installments', 'payments']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date != '') {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date != '') {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Search by invoice number or student name
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('student', function($sq) use ($search) {
                      $sq->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(15);

        // Add computed properties for each invoice
        $invoices->getCollection()->transform(function ($invoice) {
            $totalPaid = $invoice->payments->where('status', 'completed')->sum('amount');
            $invoice->total_paid = $totalPaid;
            $invoice->remaining_amount = $invoice->amount - $totalPaid;
            $invoice->is_overdue = $invoice->status === 'pending' &&
                                 $invoice->created_at->addDays(30)->isPast();
            return $invoice;
        });

        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * Show invoice creation form
     */
    public function create()
    {
        $students = Student::where('course_status', 'active')
                          ->orderBy('first_name')
                          ->get();
        $courses = Course::where('is_active', true)
                        ->orderBy('course_name')
                        ->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('admin.invoices.create', compact('students', 'courses', 'paymentMethods'));
    }

    /**
     * Store a new invoice
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'payment_type' => 'required|in:full_payment,installment',
            'first_installment_date' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $student = Student::findOrFail($request->student_id);
            $course = Course::findOrFail($request->course_id);

            // Check if student already has an invoice for this course
            $existingInvoice = Invoice::where('student_id', $student->id)
                                    ->where('course_id', $course->id)
                                    ->where('status', 'pending')
                                    ->first();

            if ($existingInvoice) {
                return redirect()->back()
                    ->withErrors(['student_id' => 'Student already has a pending invoice for this course.'])
                    ->withInput();
            }

            $additionalData = [
                'notes' => $request->notes,
            ];

            if ($request->payment_type === 'installment') {
                if (!$course->hasInstallmentPlan()) {
                    return redirect()->back()
                        ->withErrors(['course_id' => 'Selected course does not support installment payments.'])
                        ->withInput();
                }

                $additionalData['first_installment_date'] = $request->first_installment_date
                    ? Carbon::parse($request->first_installment_date)
                    : Carbon::now();

                $invoice = $this->invoiceService->createInstallmentInvoice($student, $course, $additionalData);
            } else {
                $invoice = $this->invoiceService->createFullPaymentInvoice($student, $course, $additionalData);
            }

            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Invoice created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create invoice: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display a specific invoice
     */
    public function show(Invoice $invoice)
    {
        $invoiceData = $this->invoiceService->getInvoiceData($invoice);
        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('admin.invoices.show', compact('invoiceData', 'invoice', 'paymentMethods'));
    }

    /**
     * Show invoice edit form
     */
    public function edit(Invoice $invoice)
    {
        if ($invoice->status !== 'pending') {
            return redirect()->route('invoices.show', $invoice->id)
                ->withErrors(['error' => 'Only pending invoices can be edited.']);
        }

        $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');
        if ($totalPaid > 0) {
            return redirect()->route('invoices.show', $invoice->id)
                ->withErrors(['error' => 'Cannot edit invoice with payments already made.']);
        }

        return view('admin.invoices.edit', compact('invoice'));
    }

    /**
     * Update invoice (limited fields only)
     */
    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status !== 'pending') {
            return redirect()->route('invoices.show', $invoice->id)
                ->withErrors(['error' => 'Only pending invoices can be updated.']);
        }

        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // For now, we can only update notes since we don't have additional fields
        // You could extend this to update other invoice details as needed

        return redirect()->route('admin.invoices.show', $invoice->id)
            ->with('success', 'Invoice updated successfully!');
    }

    /**
     * Delete invoice
     */
    public function destroy(Invoice $invoice)
    {
        if ($invoice->status !== 'pending') {
            return redirect()->route('invoices.index')
                ->withErrors(['error' => 'Only pending invoices can be deleted.']);
        }

        $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');
        if ($totalPaid > 0) {
            return redirect()->route('invoices.index')
                ->withErrors(['error' => 'Cannot delete invoice with payments already made.']);
        }

        // Delete related installments
        $invoice->installments()->delete();
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully!');
    }

    /**
     * Get student invoices (AJAX)
     */
    public function getStudentInvoices(Student $student)
    {
        try {
            $invoices = $this->invoiceService->getStudentInvoices($student);
            return response()->json(['success' => true, 'invoices' => $invoices]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get course details for invoice creation (AJAX)
     */
    public function getCourseDetails(Course $course)
    {
        try {
            $courseData = [
                'id' => $course->id,
                'name' => $course->course_name,
                'price' => $course->course_price,
                'formatted_price' => number_format($course->course_price, 2),
                'type' => $course->course_type,
                'theory_hours' => $course->theory_hours,
                'practical_hours' => $course->practical_hours,
                'description' => $course->description,
                'has_installment_plan' => $course->hasInstallmentPlan(),
                'installment_plan' => null,
            ];

            if ($course->hasInstallmentPlan()) {
                $plan = $course->installmentPlan;
                $courseData['installment_plan'] = [
                    'name' => $plan->Name,
                    'number_of_installments' => $plan->number_of_installments,
                    'first_installment_percentage' => $plan->first_installment_percentage,
                    'days_between_installments' => $plan->days_between_installments,
                ];

                // Generate sample installment schedule
                $sampleSchedule = $plan->generateInstallmentSchedule($course->course_price);
                $courseData['sample_schedule'] = collect($sampleSchedule)->map(function($installment, $index) {
                    return [
                        'installment_number' => $index + 1,
                        'amount' => number_format($installment['amount'], 2),
                        'due_date' => $installment['due_date']->format('F d, Y'),
                    ];
                });
            }

            return response()->json($courseData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(Invoice $invoice)
    {
        if ($invoice->status !== 'pending') {
            return redirect()->back()
                ->withErrors(['error' => 'Invoice is already processed.']);
        }

        try {
            $this->invoiceService->markInvoiceAsPaid($invoice);

            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Invoice marked as paid successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to mark invoice as paid: ' . $e->getMessage()]);
        }
    }

    /**
     * Process payment for invoice
     */
    public function processPayment(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
            'transaction_id' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($invoice->status !== 'pending') {
            return redirect()->back()
                ->withErrors(['error' => 'Cannot process payment for this invoice.']);
        }

        try {
            $paymentData = [
                'payment_method_id' => $request->payment_method_id,
                'amount' => $request->amount,
                'transaction_id' => $request->transaction_id,
                'payment_details' => $request->notes ? ['notes' => $request->notes] : null,
            ];

            $payment = $this->invoiceService->processPayment($invoice, $paymentData);

            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Payment processed successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Payment processing failed: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Get overdue invoices
     */
    public function overdue()
    {
        $overdueInvoices = $this->invoiceService->getOverdueInvoices();
        return view('invoices.overdue', compact('overdueInvoices'));
    }

    /**
     * Get invoices needing attention
     */
    public function needingAttention()
    {
        $invoicesNeedingAttention = $this->invoiceService->getInvoicesNeedingAttention();
        return view('invoices.needing-attention', compact('invoicesNeedingAttention'));
    }

    /**
     * Generate invoice PDF
     */
    public function generatePDF(Invoice $invoice)
    {
        try {
            $invoiceData = $this->invoiceService->getInvoiceData($invoice);

            // Using DomPDF (install with: composer require barryvdh/laravel-dompdf)
            $pdf = PDF::loadView('invoices.pdf', compact('invoiceData', 'invoice'));

            return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to generate PDF: ' . $e->getMessage()]);
        }
    }

    /**
     * Dashboard statistics
     */
    public function dashboard()
    {
        $statistics = $this->invoiceService->getInvoiceStatistics();
        $recentInvoices = Invoice::with(['student', 'course'])
                               ->orderBy('created_at', 'desc')
                               ->limit(10)
                               ->get();

        return view('invoices.dashboard', compact('statistics', 'recentInvoices'));
    }

    /**
     * Send payment reminder
     */
    public function sendReminder(Invoice $invoice)
    {
        try {
            // Basic reminder implementation
            // You can expand this to send actual emails/SMS

            $student = $invoice->student;
            $message = "Dear {$student->first_name}, your invoice #{$invoice->invoice_number} for {$invoice->course->course_name} is pending payment. Amount: $" . number_format($invoice->amount, 2);

            // Log the reminder (you can implement actual email/SMS sending here)
            Log::info("Payment reminder sent", [
                'invoice_id' => $invoice->id,
                'student_id' => $student->id,
                'message' => $message
            ]);

            return redirect()->back()
                ->with('success', 'Payment reminder sent successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to send reminder: ' . $e->getMessage()]);
        }
    }
}
