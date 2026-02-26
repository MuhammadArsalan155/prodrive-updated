<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Installment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminCashPaymentController extends Controller
{
    public function processCashPayment(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'payment_type' => 'required|in:full,installment',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Retrieve student and course
            $student = Student::findOrFail($validated['student_id']);
            $course = $student->course;

            // Validate payment amount
            $totalCoursePrice = $course->course_price;
            $this->validatePaymentAmount($validated['payment_type'], $validated['payment_amount'], $totalCoursePrice);

            // Create invoice
            $invoice = Invoice::create([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'amount' => $validated['payment_amount'],
                'status' => 'paid',
            ]);

            // Create payment record
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'payment_method_id' => $validated['payment_method_id'],
                'amount' => $validated['payment_amount'],
                'transaction_id' => $this->generateCashTransactionId(),
                'status' => 'completed',
                'payment_details' => json_encode([
                    'payment_type' => $validated['payment_type'],
                    'payment_method' => 'cash',
                    'payment_date' => $validated['payment_date'],
                    'notes' => $validated['payment_notes'] ?? '',
                ]),
                'paid_at' => Carbon::parse($validated['payment_date']),
            ]);

            // Handle installment plan if applicable
            if ($validated['payment_type'] === 'installment') {
                $this->createInstallmentPlan($invoice, $course, $validated['payment_amount']);
            }

            // Update student payment status
            $this->updateStudentPaymentStatus($student, $validated['payment_type'], $totalCoursePrice);

            try {
                $this->sendPaymentConfirmationEmail($student, $invoice, $course);
            } catch (\Exception $emailError) {
                // Log email sending error but continue with the transaction
                Log::error('Failed to send payment confirmation email: ' . $emailError->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cash payment processed successfully',
                'invoice_number' => $invoice->invoice_number,
                'payment_id' => $payment->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Admin Cash Payment Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'student_id' => $validated['student_id'] ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') 
                    ? 'Payment processing failed: ' . $e->getMessage() 
                    : 'Payment processing failed. Please try again.',
            ], 500);
        }
    }

    protected function validatePaymentAmount($paymentType, $paymentAmount, $totalCoursePrice)
    {
        if ($paymentType === 'full' && $paymentAmount != $totalCoursePrice) {
            throw new \Exception('Full payment must be exactly the course price');
        }

        if ($paymentType === 'installment') {
            $minAmount = $totalCoursePrice * 0.1; // Minimum 10%
            if ($paymentAmount < $minAmount || $paymentAmount > $totalCoursePrice) {
                throw new \Exception("Installment amount must be between $minAmount and $totalCoursePrice");
            }
        }
    }

    protected function generateCashTransactionId()
    {
        return 'CASH-' . strtoupper(uniqid()) . '-' . time();
    }

    protected function createInstallmentPlan(Invoice $invoice, Course $course, $paidAmount)
    {
        $installmentPlan = $course->installmentPlan;
        $totalAmount = $course->course_price;

        // Create first installment (the current payment)
        Installment::create([
            'invoice_id' => $invoice->id,
            'amount' => $paidAmount,
            'due_date' => Carbon::now(),
            'status' => 'paid',
            'paid_at' => Carbon::now(),
            'notes' => 'First installment paid (cash)',
        ]);

        // Calculate remaining installments
        $remainingAmount = $totalAmount - $paidAmount;
        $remainingInstallments = $installmentPlan->number_of_installments - 1;

        if ($remainingInstallments > 0) {
            $installmentAmount = $remainingAmount / $remainingInstallments;

            for ($i = 1; $i <= $remainingInstallments; $i++) {
                Installment::create([
                    'invoice_id' => $invoice->id,
                    'amount' => $installmentAmount,
                    'due_date' => Carbon::now()->addMonths($i),
                    'status' => 'pending',
                    'notes' => "Installment {$i} of {$remainingInstallments}",
                ]);
            }
        }
    }

    protected function updateStudentPaymentStatus(Student $student, $paymentType, $totalCoursePrice)
    {
        $student->update([
            'payment_status' => $paymentType === 'full' ? 3 : 1, // Fully paid or first installment paid
            'course_status' => 1, // Active enrollment
        ]);
    }
}