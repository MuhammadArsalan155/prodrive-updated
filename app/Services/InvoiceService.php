<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Student;
use App\Models\Course;
use App\Models\Installment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Create a full payment invoice for a student
     */
    public function createFullPaymentInvoice(Student $student, Course $course, array $additionalData = [])
    {
        return DB::transaction(function () use ($student, $course, $additionalData) {
            $invoice = Invoice::create([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'amount' => $course->course_price,
                'status' => 'pending',
            ]);

            return $invoice;
        });
    }

    /**
     * Create an installment-based invoice for a student
     */
    public function createInstallmentInvoice(Student $student, Course $course, array $additionalData = [])
    {
        if (!$course->hasInstallmentPlan()) {
            throw new \Exception('Course does not have an installment plan');
        }

        return DB::transaction(function () use ($student, $course, $additionalData) {
            $invoice = Invoice::create([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'amount' => $course->course_price,
                'status' => 'pending',
            ]);

            // Generate installment schedule
            $installmentPlan = $course->installmentPlan;
            $firstInstallmentDate = $additionalData['first_installment_date'] ?? Carbon::now();

            $installmentSchedule = $installmentPlan->generateInstallmentSchedule(
                $course->course_price,
                $firstInstallmentDate
            );

            // Create installment records
            foreach ($installmentSchedule as $index => $installmentData) {
                Installment::create([
                    'invoice_id' => $invoice->id,
                    'amount' => $installmentData['amount'],
                    'due_date' => $installmentData['due_date'],
                    'status' => 'pending',
                    'notes' => 'Installment ' . ($index + 1) . ' of ' . count($installmentSchedule),
                ]);
            }

            return $invoice->load('installments');
        });
    }

    /**
     * Get invoice data formatted for display/PDF generation
     */
    public function getInvoiceData(Invoice $invoice)
    {
        $invoice->load(['student', 'course', 'installments', 'payments.paymentMethod']);

        // Calculate payment summary
        $totalPaid = $invoice->payments->where('status', 'completed')->sum('amount');
        $remainingAmount = $invoice->amount - $totalPaid;

        // Determine if invoice is overdue (basic check since we don't have due_date field)
        $isOverdue = $invoice->status === 'pending' &&
                    $invoice->created_at->addDays(30)->isPast(); // Assume 30 days payment term

        return [
            'invoice' => [
                'number' => $invoice->invoice_number,
                'date' => $invoice->created_at->format('F d, Y'),
                'status' => ucfirst($invoice->status),
                'type' => $invoice->installments->count() > 0 ? 'Installment Payment' : 'Full Payment',
                'amount' => number_format($invoice->amount, 2),
                'is_overdue' => $isOverdue,
            ],
            'student' => [
                'name' => $invoice->student->first_name . ' ' . $invoice->student->last_name,
                'email' => $invoice->student->email,
                'contact' => $invoice->student->student_contact,
                'address' => $invoice->student->address,
                'joining_date' => $invoice->student->joining_date ?
                    Carbon::parse($invoice->student->joining_date)->format('F d, Y') : null,
            ],
            'course' => [
                'name' => $invoice->course->course_name,
                'type' => $invoice->course->course_type,
                'price' => number_format($invoice->course->course_price, 2),
                'theory_hours' => $invoice->course->theory_hours,
                'practical_hours' => $invoice->course->practical_hours,
                'description' => $invoice->course->description,
            ],
            'installments' => $invoice->installments->map(function ($installment, $index) {
                return [
                    'number' => $index + 1,
                    'amount' => number_format($installment->amount, 2),
                    'due_date' => Carbon::parse($installment->due_date)->format('F d, Y'),
                    'status' => ucfirst($installment->status),
                    'is_overdue' => $installment->status === 'pending' &&
                                  Carbon::parse($installment->due_date)->isPast(),
                ];
            }),
            'payments' => $invoice->payments->map(function ($payment) {
                return [
                    'amount' => number_format($payment->amount, 2),
                    'date' => $payment->created_at->format('F d, Y'),
                    'method' => $payment->paymentMethod->name ?? 'Unknown',
                    'transaction_id' => $payment->transaction_id,
                    'status' => ucfirst($payment->status),
                ];
            }),
            'summary' => [
                'total_amount' => number_format($invoice->amount, 2),
                'paid_amount' => number_format($totalPaid, 2),
                'remaining_amount' => number_format($remainingAmount, 2),
                'payment_progress' => $invoice->amount > 0 ?
                    round(($totalPaid / $invoice->amount) * 100, 2) : 0,
            ]
        ];
    }

    /**
     * Get all invoices for a student
     */
    public function getStudentInvoices(Student $student)
    {
        return $student->invoices()
            ->with(['course', 'installments', 'payments.paymentMethod'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($invoice) {
                return $this->getInvoiceData($invoice);
            });
    }

    /**
     * Mark invoice as paid (for full payment invoices)
     */
    public function markInvoiceAsPaid(Invoice $invoice, array $paymentData = [])
    {
        return DB::transaction(function () use ($invoice, $paymentData) {
            $invoice->update(['status' => 'paid']);

            // If it has installments, mark all as paid
            if ($invoice->installments()->count() > 0) {
                $invoice->installments()->update([
                    'status' => 'paid',
                    'paid_at' => now()
                ]);
            }

            return $invoice;
        });
    }

    /**
     * Get overdue invoices (basic implementation)
     */
    public function getOverdueInvoices()
    {
        // Get pending invoices older than 30 days
        return Invoice::where('status', 'pending')
            ->where('created_at', '<', Carbon::now()->subDays(30))
            ->with(['student', 'course'])
            ->get();
    }

    /**
     * Get invoices that need attention (recent unpaid)
     */
    public function getInvoicesNeedingAttention($days = 7)
    {
        return Invoice::where('status', 'pending')
            ->whereBetween('created_at', [
                Carbon::now()->subDays($days + 7),
                Carbon::now()->subDays($days)
            ])
            ->with(['student', 'course'])
            ->get();
    }

    /**
     * Process a payment for an invoice
     */
    public function processPayment(Invoice $invoice, array $paymentData)
    {
        return DB::transaction(function () use ($invoice, $paymentData) {
            // Create payment record
            $payment = $invoice->payments()->create([
                'payment_method_id' => $paymentData['payment_method_id'],
                'amount' => $paymentData['amount'],
                'transaction_id' => $paymentData['transaction_id'] ?? 'TXN-' . uniqid(),
                'status' => 'completed',
                'payment_details' => $paymentData['payment_details'] ?? null,
            ]);

            // Check if invoice is fully paid
            $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');

            if ($totalPaid >= $invoice->amount) {
                $invoice->update(['status' => 'paid']);

                // Mark all installments as paid if applicable
                $invoice->installments()->update([
                    'status' => 'paid',
                    'paid_at' => now()
                ]);
            }

            return $payment;
        });
    }

    /**
     * Process installment payment
     */
    public function processInstallmentPayment(Installment $installment, array $paymentData)
    {
        return DB::transaction(function () use ($installment, $paymentData) {
            if ($installment->status !== 'pending') {
                throw new \Exception('Installment has already been processed');
            }

            // Create payment record
            $payment = $installment->invoice->payments()->create([
                'payment_method_id' => $paymentData['payment_method_id'],
                'amount' => $paymentData['amount'],
                'transaction_id' => $paymentData['transaction_id'] ?? 'TXN-' . uniqid(),
                'status' => 'completed',
                'payment_details' => array_merge($paymentData['payment_details'] ?? [], [
                    'installment_id' => $installment->id,
                    'installment_note' => $installment->notes,
                ]),
            ]);

            // Mark installment as paid
            $installment->markAsPaid();

            // Check if all installments are paid
            $invoice = $installment->invoice;
            $pendingInstallments = $invoice->installments()->where('status', 'pending')->count();

            if ($pendingInstallments === 0) {
                $invoice->update(['status' => 'paid']);
            }

            return $payment;
        });
    }

    /**
     * Get invoice statistics
     */
    public function getInvoiceStatistics()
    {
        return [
            'total_invoices' => Invoice::count(),
            'pending_invoices' => Invoice::where('status', 'pending')->count(),
            'paid_invoices' => Invoice::where('status', 'paid')->count(),
            'overdue_invoices' => $this->getOverdueInvoices()->count(),
            'total_revenue' => Invoice::where('status', 'paid')->sum('amount'),
            'pending_revenue' => Invoice::where('status', 'pending')->sum('amount'),
            'this_month_invoices' => Invoice::whereMonth('created_at', Carbon::now()->month)
                                          ->whereYear('created_at', Carbon::now()->year)
                                          ->count(),
            'this_month_revenue' => Invoice::where('status', 'paid')
                                          ->whereMonth('created_at', Carbon::now()->month)
                                          ->whereYear('created_at', Carbon::now()->year)
                                          ->sum('amount'),
        ];
    }
}
