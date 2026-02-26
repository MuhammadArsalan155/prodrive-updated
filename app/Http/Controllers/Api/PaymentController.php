<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\PaymentConfirmationMail;
use App\Mail\StudentCredentials;
use App\Models\Course;
use App\Models\Installment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Student;
use App\Services\StripeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $stripeService;

    // IMPORTANT: Set this to when you implemented payment functionality
    const PAYMENT_FUNCTIONALITY_START_DATE = '2024-08-08'; // ADJUST THIS DATE!

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function getCoursePaymentDetails($courseId, Request $request)
    {
        try {
            $course = Course::findOrFail($courseId);
            $paymentMethodId = $request->query('payment_method_id');

            $courseWithFee = $course->toArray();
            $courseWithFee['original_price'] = $course->course_price;

            // Get payment method details if provided
            $paymentMethodFee = 0;
            $feePercentage = 0;
            $paymentMethodName = '';

            if ($paymentMethodId) {
                $paymentMethod = PaymentMethod::find($paymentMethodId);
                if ($paymentMethod) {
                    $feePercentage = $paymentMethod->additional_price ?? 0;
                    $paymentMethodFee = round(($course->course_price * $feePercentage) / 100, 2);
                    $paymentMethodName = $paymentMethod->name;
                }
            }

            $totalPrice = round($course->course_price + $paymentMethodFee, 2);

            $courseWithFee['service_charges'] = $paymentMethodFee;
            $courseWithFee['fee_percentage'] = $feePercentage;
            $courseWithFee['payment_method_name'] = $paymentMethodName;
            $courseWithFee['total_price'] = $totalPrice;

            return response()->json([
                'course' => $courseWithFee,
                'installment_plan' => $course->hasInstallmentPlan() ? $course->installmentPlan : null,
                'payment_method_fee' => $paymentMethodFee,
                'fee_percentage' => $feePercentage,
                'payment_method_name' => $paymentMethodName,
            ]);
        } catch (\Exception $e) {
            Log::error('getCoursePaymentDetails Error: ' . $e->getMessage(), [
                'course_id' => $courseId,
                'payment_method_id' => $request->query('payment_method_id'),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    'error' => 'Failed to fetch course payment details',
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'payment_type' => 'required|in:full,installment',
        ]);

        DB::beginTransaction();

        try {
            $student = Student::findOrFail($validated['student_id']);

            // SAFETY CHECK: Don't process payment for old records without proper setup
            if (!$this->isSafeForPaymentProcessing($student)) {
                throw new \Exception('This student record is not eligible for the new payment system.');
            }

            $course = Course::findOrFail($validated['course_id']);
            $paymentMethod = PaymentMethod::findOrFail($validated['payment_method_id']);

            $paymentType = $validated['payment_type'];

            // Calculate payment method fee as percentage of course price
            $feePercentage = $paymentMethod->additional_price ?? 0;
            $paymentMethodFee = round(($course->course_price * $feePercentage) / 100, 2);
            $totalCoursePrice = round($course->course_price + $paymentMethodFee, 2);

            $amount = $paymentType === 'installment' ? $this->calculateFirstInstallmentAmount($course, $paymentMethodFee) : $totalCoursePrice;

            // Round the final amount to avoid precision issues
            $amount = round($amount, 2);

            // CRITICAL FIX: Convert to cents for Stripe (must be integer)
            $amountInCents = (int) round($amount * 100);

            // Validate amount
            if ($amountInCents <= 0) {
                throw new \Exception('Invalid payment amount: ' . $amount);
            }

            // Additional validation to ensure we have a valid integer for Stripe
            if (!is_int($amountInCents)) {
                throw new \Exception('Amount conversion to cents failed: ' . $amountInCents);
            }

            // Create invoice
            $invoice = Invoice::create([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'amount' => $amount, // Store amount in dollars for database
                'status' => 'pending',
            ]);

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'payment_method_id' => $validated['payment_method_id'],
                'amount' => $amount, // Store amount in dollars for database
                'transaction_id' => new Payment()->generateTransactionId(),
                'status' => 'pending',
                'payment_details' => json_encode([
                    'payment_type' => $paymentType,
                    'course_name' => $course->course_name,
                    'student_email' => $student->email,
                    'original_course_price' => $course->course_price,
                    'payment_method_fee' => $paymentMethodFee,
                    'fee_percentage' => $feePercentage,
                    'payment_method_name' => $paymentMethod->name,
                    'total_price' => $totalCoursePrice,
                    'amount_in_cents' => $amountInCents, // Store for reference
                ]),
            ]);

            // Create Stripe Checkout Session - PASS CENTS TO STRIPE
            $checkoutSession = $this->stripeService->createCheckoutSession(
                $course,
                $amountInCents, // This is the fix - pass cents (integer) to Stripe
                $paymentType,
                $student,
                $payment,
            );

            // Update payment with Stripe session details
            $payment->update([
                'transaction_id' => $checkoutSession->id,
                'payment_details' => json_encode([
                    'payment_type' => $paymentType,
                    'checkout_session_id' => $checkoutSession->id,
                    'checkout_url' => $checkoutSession->url,
                    'course_name' => $course->course_name,
                    'student_email' => $student->email,
                    'original_course_price' => $course->course_price,
                    'payment_method_fee' => $paymentMethodFee,
                    'fee_percentage' => $feePercentage,
                    'payment_method_name' => $paymentMethod->name,
                    'total_price' => $totalCoursePrice,
                    'amount_in_cents' => $amountInCents,
                ]),
            ]);

            $invoice->update(['status' => 'processing']);
            $student->update(['payment_status' => 1]);

            DB::commit();

            return response()->json([
                'success' => true,
                'checkout_url' => $checkoutSession->url,
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'amount' => $amount, // Return dollars for frontend display
                'payment_method_fee' => $paymentMethodFee,
                'fee_percentage' => $feePercentage,
                'total_amount' => $totalCoursePrice,
                'session_id' => $checkoutSession->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // SAFE CLEANUP: Only cleanup NEW records
            if (isset($student) && $this->isSafeForCleanup($student)) {
                $this->safeImmediateCleanup($student->id, 'Payment processing failed: ' . $e->getMessage());
            }

            // Log the specific error for debugging
            Log::error('Payment Processing Error', [
                'message' => $e->getMessage(),
                'student_id' => $validated['student_id'] ?? null,
                'course_id' => $validated['course_id'] ?? null,
                'payment_method_id' => $validated['payment_method_id'] ?? null,
                'amount' => $amount ?? null,
                'amount_in_cents' => $amountInCents ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Payment processing failed. Please try registering again.',
                ],
                500,
            );
        }
    }

    public function handlePaymentSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');

        DB::beginTransaction();

        try {
            $paymentValidation = $this->stripeService->validatePayment($sessionId);

            if (!$paymentValidation['status']) {
                throw new \Exception('Payment validation failed');
            }

            $payment = Payment::where('transaction_id', $sessionId)->firstOrFail();
            $invoice = $payment->invoice;
            $student = $payment->invoice->student;
            $course = $payment->invoice->course;

            // Update Payment Status
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            // Update Invoice Status
            $invoice->update([
                'status' => 'paid',
                'due_date' => json_decode($payment->payment_details)->payment_type === 'installment' ? now()->addMonth() : null,
            ]);

            // Update Student Status
            $student->update([
                'payment_status' => 3, // Paid
                'course_status' => 1, // Active
            ]);

            // Create Installment Plan if applicable
            if (json_decode($payment->payment_details)->payment_type === 'installment') {
                $paymentDetails = json_decode($payment->payment_details);
                $paymentMethodFee = $paymentDetails->payment_method_fee ?? 0;
                $this->createInstallmentPlan($invoice, $course, $payment, $paymentMethodFee);
            }

            // Send emails
            $this->sendStudentCredentialsEmail($student);
            $this->sendPaymentConfirmationEmail($student, $invoice, $course);

            DB::commit();

            return redirect()
                ->route('registerPage')
                ->with([
                    'success' => true,
                    'message' => 'Payment successful! Your login credentials have been sent to your email.',
                    'student_name' => $student->first_name,
                    'course_name' => $course->course_name,
                ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // SAFE CLEANUP on success failure
            if (isset($payment)) {
                $payment->update(['status' => 'failed']);

                $student = $payment->invoice->student;
                if ($this->isSafeForCleanup($student)) {
                    $this->safeImmediateCleanup($student->id, 'Payment success validation failed');
                }
            }

            return redirect()
                ->route('registerPage')
                ->with([
                    'success' => false,
                    'message' => 'Payment processing failed. Please try registering again.',
                ]);
        }
    }

    public function handlePaymentFailure(Request $request)
    {
        $sessionId = $request->get('session_id');

        try {
            $payment = Payment::where('transaction_id', $sessionId)->first();

            if ($payment) {
                $student = $payment->invoice->student;

                $payment->update(['status' => 'failed']);

                // SAFE CLEANUP: Only cleanup NEW records
                if ($this->isSafeForCleanup($student)) {
                    $this->safeImmediateCleanup($student->id, 'Payment cancelled or failed by user');
                }
            }
        } catch (\Exception $e) {
            Log::error('Payment Failure Handler Error:', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('registerPage')
            ->with([
                'success' => false,
                'message' => 'Payment was cancelled. You can register again with the same email address.',
            ]);
    }

    /**
     * API endpoint for safe immediate cleanup
     */
    public function immediateCleanupEndpoint(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|integer',
            'session_id' => 'sometimes|string',
            'reason' => 'sometimes|string',
        ]);

        try {
            $student = Student::find($validated['student_id']);

            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student not found'], 404);
            }

            // SAFETY CHECK
            if (!$this->isSafeForCleanup($student)) {
                Log::warning('Cleanup blocked for safety - old record:', [
                    'student_id' => $student->id,
                    'email' => $student->email,
                    'created_at' => $student->created_at,
                ]);

                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Cannot cleanup - record predates payment system',
                    ],
                    403,
                );
            }

            $this->safeImmediateCleanup($validated['student_id'], $validated['reason'] ?? 'Manual cleanup requested');

            return response()->json([
                'success' => true,
                'message' => 'Student record cleaned up successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Cleanup failed: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function checkAbandonedPayments(Request $request)
    {
        try {
            // Find ONLY NEW payments that are stuck in pending
            $abandonedPayments = Payment::where('status', 'pending')
                ->where('created_at', '<', now()->subMinutes(30))
                ->with('invoice.student')
                ->get()
                ->filter(function ($payment) {
                    // Additional safety filter
                    return $payment->invoice && $payment->invoice->student && $this->isSafeForCleanup($payment->invoice->student);
                });

            $cleanedCount = 0;

            foreach ($abandonedPayments as $payment) {
                $this->safeImmediateCleanup($payment->invoice->student_id, 'Abandoned payment - user likely closed browser');
                $cleanedCount++;
            }

            return response()->json([
                'success' => true,
                'cleaned_count' => $cleanedCount,
                'message' => "Safely cleaned up {$cleanedCount} abandoned registrations",
            ]);
        } catch (\Exception $e) {
            Log::error('Abandoned payments cleanup error: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Cleanup failed: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Check if student is safe for payment processing
     */
    private function isSafeForPaymentProcessing($student)
    {
        // Only allow payment processing for:
        // 1. Records created after payment functionality start date, OR
        // 2. Records with has_payment_process flag

        if ($student->created_at >= self::PAYMENT_FUNCTIONALITY_START_DATE) {
            return true;
        }

        if (isset($student->has_payment_process) && $student->has_payment_process) {
            return true;
        }

        return false;
    }

    /**
     * Check if student record is safe to cleanup/delete
     */
    private function isSafeForCleanup($student)
    {
        // DON'T DELETE if:
        if ($student->created_at < self::PAYMENT_FUNCTIONALITY_START_DATE) {
            return false; // OLD RECORD - NEVER DELETE
        }

        if ($student->payment_status == 3) {
            return false; // SUCCESSFUL PAYMENT - NEVER DELETE
        }

        // Additional safety: Check if it's marked as having payment process
        if (!isset($student->has_payment_process) && $student->created_at < self::PAYMENT_FUNCTIONALITY_START_DATE) {
            return false; // OLD RECORD WITHOUT PAYMENT FLAG - NEVER DELETE
        }

        // Extra safety check: Don't delete if has_payment_process is explicitly false
        if (isset($student->has_payment_process) && $student->has_payment_process === false) {
            return false; // EXPLICITLY MARKED AS OLD RECORD - NEVER DELETE
        }

        return true; // SAFE TO DELETE
    }

    /**
     * SAFE IMMEDIATE CLEANUP with multiple safety checks
     */
    private function safeImmediateCleanup($studentId, $reason = 'Safe cleanup')
    {
        DB::beginTransaction();

        try {
            $student = Student::find($studentId);

            if (!$student) {
                DB::rollBack();
                return;
            }

            // MULTIPLE SAFETY CHECKS
            if (!$this->isSafeForCleanup($student)) {
                Log::warning('MULTIPLE SAFETY CHECKS FAILED - Aborting cleanup:', [
                    'student_id' => $studentId,
                    'email' => $student->email,
                    'created_at' => $student->created_at,
                    'payment_status' => $student->payment_status,
                    'has_payment_process' => $student->has_payment_process ?? 'not_set',
                    'reason' => $reason,
                ]);
                DB::rollBack();
                return;
            }

            Log::info('Starting SAFE cleanup with multiple protections:', [
                'student_id' => $studentId,
                'email' => $student->email,
                'created_at' => $student->created_at,
                'payment_status' => $student->payment_status,
                'reason' => $reason,
            ]);

            // Cleanup related payment records
            $invoices = Invoice::where('student_id', $studentId)->get();

            foreach ($invoices as $invoice) {
                Payment::where('invoice_id', $invoice->id)->delete();
                Installment::where('invoice_id', $invoice->id)->delete();
                $invoice->delete();
            }

            // Delete the student record
            $student->delete();

            DB::commit();

            Log::info('SAFE cleanup completed with protections:', [
                'student_id' => $studentId,
                'reason' => $reason,
                'invoices_deleted' => $invoices->count(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('SAFE cleanup failed with protections:', [
                'student_id' => $studentId,
                'reason' => $reason,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function calculateFirstInstallmentAmount(Course $course, $paymentMethodFee = 0)
    {
        $installmentPlan = $course->installmentPlan;
        $totalPriceWithFee = $course->course_price + $paymentMethodFee;
        return $totalPriceWithFee * ($installmentPlan->first_installment_percentage / 100);
    }

    protected function createInstallmentPlan(Invoice $invoice, Course $course, Payment $payment, $paymentMethodFee = 0)
    {
        $installmentPlan = $course->installmentPlan;
        $totalAmount = $course->course_price + $paymentMethodFee;
        $firstInstallmentAmount = $totalAmount * ($installmentPlan->first_installment_percentage / 100);

        // First installment is already paid
        Installment::create([
            'invoice_id' => $invoice->id,
            'amount' => $firstInstallmentAmount,
            'due_date' => Carbon::now(),
            'status' => 'paid',
            'paid_at' => Carbon::now(),
            'notes' => 'First installment paid (includes payment method fee)',
        ]);

        // Create remaining installments
        $remainingAmount = $totalAmount - $firstInstallmentAmount;
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

    private function sendStudentCredentialsEmail(Student $student)
    {
        try {
            $plainPassword = session('student_registration_password');

            if ($plainPassword) {
                Mail::to($student->email)->send(new StudentCredentials($student, $plainPassword, true));
                session()->forget('student_registration_password');

                Log::info('Student credentials email sent after successful payment:', [
                    'student_id' => $student->id,
                    'email' => $student->email,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send student credentials email after payment:', [
                'student_id' => $student->id,
                'email' => $student->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function sendPaymentConfirmationEmail(Student $student, Invoice $invoice, Course $course)
    {
        try {
            if (empty($student->email)) {
                Log::error('Email not sent: Student email is empty');
                return false;
            }

            Mail::to($student->email)->send(new PaymentConfirmationMail($student, $invoice, $course));
            Log::info('Payment confirmation email sent successfully to: ' . $student->email);
        } catch (\Exception $e) {
            Log::error('Payment Confirmation Email Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use App\Mail\PaymentConfirmationMail;
// use App\Mail\StudentCredentials;
// use App\Models\Course;
// use App\Models\Installment;
// use App\Models\Invoice;
// use App\Models\Payment;
// use App\Models\PaymentMethod;
// use App\Models\Student;
// use App\Services\StripeService;
// use Carbon\Carbon;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Str;
// use Illuminate\Support\Facades\Mail;
// use Illuminate\Support\Facades\Log;

// class PaymentController extends Controller
// {
//     protected $stripeService;
//     const REGISTRATION_FEE = 10.00;

//     // IMPORTANT: Set this to when you implemented payment functionality
//     const PAYMENT_FUNCTIONALITY_START_DATE = '2024-08-08'; // ADJUST THIS DATE!

//     public function __construct(StripeService $stripeService)
//     {
//         $this->stripeService = $stripeService;
//     }

//     public function getCoursePaymentDetails($courseId)
//     {
//         $course = Course::findOrFail($courseId);

//         $courseWithFee = $course->toArray();
//         $courseWithFee['original_price'] = $course->course_price;
//         $courseWithFee['registration_fee'] = self::REGISTRATION_FEE;
//         $courseWithFee['total_price'] = $course->course_price + self::REGISTRATION_FEE;

//         return response()->json([
//             'course' => $courseWithFee,
//             'installment_plan' => $course->hasInstallmentPlan() ? $course->installmentPlan : null,
//             'registration_fee' => self::REGISTRATION_FEE,
//         ]);
//     }

//     public function processPayment(Request $request)
//     {

//         $validated = $request->validate([
//             'student_id' => 'required|exists:students,id',
//             'course_id' => 'required|exists:courses,id',
//             'payment_method_id' => 'required|exists:payment_methods,id',
//             'payment_type' => 'required|in:full,installment',
//         ]);

//         DB::beginTransaction();

//         try {
//             $student = Student::findOrFail($validated['student_id']);

//             // SAFETY CHECK: Don't process payment for old records without proper setup
//             if (!$this->isSafeForPaymentProcessing($student)) {
//                 throw new \Exception('This student record is not eligible for the new payment system.');
//             }

//             $course = Course::findOrFail($validated['course_id']);

//             $paymentType = $validated['payment_type'];
//             $totalCoursePrice = $course->course_price + self::REGISTRATION_FEE;

//             $amount = $paymentType === 'installment' ?
//                 $this->calculateFirstInstallmentAmount($course) :
//                 $totalCoursePrice;

//             // Create invoice
//             $invoice = Invoice::create([
//                 'student_id' => $student->id,
//                 'course_id' => $course->id,
//                 'amount' => $amount,
//                 'status' => 'pending',
//             ]);

//             if ($amount <= 0) {
//                 throw new \Exception('Invalid payment amount');
//             }

//             // Create payment record
//             $payment = Payment::create([
//                 'invoice_id' => $invoice->id,
//                 'payment_method_id' => $validated['payment_method_id'],
//                 'amount' => $amount,
//                 'transaction_id' => (new Payment())->generateTransactionId(),
//                 'status' => 'pending',
//                 'payment_details' => json_encode([
//                     'payment_type' => $paymentType,
//                     'course_name' => $course->course_name,
//                     'student_email' => $student->email,
//                     'original_course_price' => $course->course_price,
//                     'registration_fee' => self::REGISTRATION_FEE,
//                     'total_price' => $totalCoursePrice,
//                 ]),
//             ]);

//             // Create Stripe Checkout Session
//             $checkoutSession = $this->stripeService->createCheckoutSession(
//                 $course,
//                 $amount,
//                 $paymentType,
//                 $student,
//                 $payment
//             );

//             // Update payment with Stripe session details
//             $payment->update([
//                 'transaction_id' => $checkoutSession->id,
//                 'payment_details' => json_encode([
//                     'payment_type' => $paymentType,
//                     'checkout_session_id' => $checkoutSession->id,
//                     'checkout_url' => $checkoutSession->url,
//                     'course_name' => $course->course_name,
//                     'student_email' => $student->email,
//                     'original_course_price' => $course->course_price,
//                     'registration_fee' => self::REGISTRATION_FEE,
//                     'total_price' => $totalCoursePrice,
//                 ]),
//             ]);

//             $invoice->update(['status' => 'processing']);
//             $student->update(['payment_status' => 1]);

//             DB::commit();

//             return response()->json([
//                 'success' => true,
//                 'checkout_url' => $checkoutSession->url,
//                 'payment_id' => $payment->id,
//                 'invoice_id' => $invoice->id,
//                 'amount' => $amount,
//                 'session_id' => $checkoutSession->id,
//             ]);

//         } catch (\Exception $e) {
//             DB::rollBack();

//             // SAFE CLEANUP: Only cleanup NEW records
//             if (isset($student) && $this->isSafeForCleanup($student)) {
//                 $this->safeImmediateCleanup($student->id, 'Payment processing failed: ' . $e->getMessage());
//             }

//             Log::error('Payment Processing Error', [
//                 'message' => $e->getMessage(),
//                 'student_id' => $validated['student_id'] ?? null,
//             ]);

//             return response()->json([
//                 'success' => false,
//                 'message' => 'Payment processing failed. Please try registering again.',
//             ], 500);
//         }
//     }

//     public function handlePaymentSuccess(Request $request)
//     {
//         $sessionId = $request->get('session_id');

//         DB::beginTransaction();

//         try {
//             $paymentValidation = $this->stripeService->validatePayment($sessionId);

//             if (!$paymentValidation['status']) {
//                 throw new \Exception('Payment validation failed');
//             }

//             $payment = Payment::where('transaction_id', $sessionId)->firstOrFail();
//             $invoice = $payment->invoice;
//             $student = $payment->invoice->student;
//             $course = $payment->invoice->course;

//             // Update Payment Status
//             $payment->update([
//                 'status' => 'completed',
//                 'paid_at' => now(),
//             ]);

//             // Update Invoice Status
//             $invoice->update([
//                 'status' => 'paid',
//                 'due_date' => json_decode($payment->payment_details)->payment_type === 'installment' ? now()->addMonth() : null,
//             ]);

//             // Update Student Status
//             $student->update([
//                 'payment_status' => 3, // Paid
//                 'course_status' => 1,  // Active
//             ]);

//             // Create Installment Plan if applicable
//             if (json_decode($payment->payment_details)->payment_type === 'installment') {
//                 $this->createInstallmentPlan($invoice, $course, $payment);
//             }

//             // Send emails
//             $this->sendStudentCredentialsEmail($student);
//             $this->sendPaymentConfirmationEmail($student, $invoice, $course);

//             DB::commit();

//             return redirect()
//                 ->route('registerPage')
//                 ->with([
//                     'success' => true,
//                     'message' => 'Payment successful! Your login credentials have been sent to your email.',
//                     'student_name' => $student->first_name,
//                     'course_name' => $course->course_name,
//                 ]);

//         } catch (\Exception $e) {
//             DB::rollBack();

//             // SAFE CLEANUP on success failure
//             if (isset($payment)) {
//                 $payment->update(['status' => 'failed']);

//                 $student = $payment->invoice->student;
//                 if ($this->isSafeForCleanup($student)) {
//                     $this->safeImmediateCleanup($student->id, 'Payment success validation failed');
//                 }
//             }

//             return redirect()
//                 ->route('registerPage')
//                 ->with([
//                     'success' => false,
//                     'message' => 'Payment processing failed. Please try registering again.',
//                 ]);
//         }
//     }

//     public function handlePaymentFailure(Request $request)
//     {
//         $sessionId = $request->get('session_id');

//         try {
//             $payment = Payment::where('transaction_id', $sessionId)->first();

//             if ($payment) {
//                 $student = $payment->invoice->student;

//                 $payment->update(['status' => 'failed']);

//                 // SAFE CLEANUP: Only cleanup NEW records
//                 if ($this->isSafeForCleanup($student)) {
//                     $this->safeImmediateCleanup($student->id, 'Payment cancelled or failed by user');
//                 }
//             }

//         } catch (\Exception $e) {
//             Log::error('Payment Failure Handler Error:', [
//                 'session_id' => $sessionId,
//                 'error' => $e->getMessage()
//             ]);
//         }

//         return redirect()
//             ->route('registerPage')
//             ->with([
//                 'success' => false,
//                 'message' => 'Payment was cancelled. You can register again with the same email address.',
//             ]);
//     }

//     /**
//      * API endpoint for safe immediate cleanup
//      */
//     public function immediateCleanupEndpoint(Request $request)
//     {
//         $validated = $request->validate([
//             'student_id' => 'required|integer',
//             'session_id' => 'sometimes|string',
//             'reason' => 'sometimes|string'
//         ]);

//         try {
//             $student = Student::find($validated['student_id']);

//             if (!$student) {
//                 return response()->json(['success' => false, 'message' => 'Student not found'], 404);
//             }

//             // SAFETY CHECK
//             if (!$this->isSafeForCleanup($student)) {
//                 Log::warning('Cleanup blocked for safety - old record:', [
//                     'student_id' => $student->id,
//                     'email' => $student->email,
//                     'created_at' => $student->created_at
//                 ]);

//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Cannot cleanup - record predates payment system'
//                 ], 403);
//             }

//             $this->safeImmediateCleanup(
//                 $validated['student_id'],
//                 $validated['reason'] ?? 'Manual cleanup requested'
//             );

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Student record cleaned up successfully'
//             ]);

//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Cleanup failed: ' . $e->getMessage()
//             ], 500);
//         }
//     }

//     public function checkAbandonedPayments(Request $request)
//     {
//         try {
//             // Find ONLY NEW payments that are stuck in pending
//             $abandonedPayments = Payment::where('status', 'pending')
//                 ->where('created_at', '<', now()->subMinutes(30))
//                 ->with('invoice.student')
//                 ->get()
//                 ->filter(function($payment) {
//                     // Additional safety filter
//                     return $payment->invoice &&
//                            $payment->invoice->student &&
//                            $this->isSafeForCleanup($payment->invoice->student);
//                 });

//             $cleanedCount = 0;

//             foreach ($abandonedPayments as $payment) {
//                 $this->safeImmediateCleanup(
//                     $payment->invoice->student_id,
//                     'Abandoned payment - user likely closed browser'
//                 );
//                 $cleanedCount++;
//             }

//             return response()->json([
//                 'success' => true,
//                 'cleaned_count' => $cleanedCount,
//                 'message' => "Safely cleaned up {$cleanedCount} abandoned registrations"
//             ]);

//         } catch (\Exception $e) {
//             Log::error('Abandoned payments cleanup error: ' . $e->getMessage());

//             return response()->json([
//                 'success' => false,
//                 'message' => 'Cleanup failed: ' . $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Check if student is safe for payment processing
//      */
//     private function isSafeForPaymentProcessing($student)
//     {
//         // Only allow payment processing for:
//         // 1. Records created after payment functionality start date, OR
//         // 2. Records with has_payment_process flag

//         if ($student->created_at >= self::PAYMENT_FUNCTIONALITY_START_DATE) {
//             return true;
//         }

//         if (isset($student->has_payment_process) && $student->has_payment_process) {
//             return true;
//         }

//         return false;
//     }

//     /**
//      * Check if student record is safe to cleanup/delete
//      */
//     private function isSafeForCleanup($student)
//     {
//         // DON'T DELETE if:
//         if ($student->created_at < self::PAYMENT_FUNCTIONALITY_START_DATE) {
//             return false; // OLD RECORD - NEVER DELETE
//         }

//         if ($student->payment_status == 3) {
//             return false; // SUCCESSFUL PAYMENT - NEVER DELETE
//         }

//         // Additional safety: Check if it's marked as having payment process
//         if (!isset($student->has_payment_process) && $student->created_at < self::PAYMENT_FUNCTIONALITY_START_DATE) {
//             return false; // OLD RECORD WITHOUT PAYMENT FLAG - NEVER DELETE
//         }

//         // Extra safety check: Don't delete if has_payment_process is explicitly false
//         if (isset($student->has_payment_process) && $student->has_payment_process === false) {
//             return false; // EXPLICITLY MARKED AS OLD RECORD - NEVER DELETE
//         }

//         return true; // SAFE TO DELETE
//     }

//     /**
//      * SAFE IMMEDIATE CLEANUP with multiple safety checks
//      */
//     private function safeImmediateCleanup($studentId, $reason = 'Safe cleanup')
//     {
//         DB::beginTransaction();

//         try {
//             $student = Student::find($studentId);

//             if (!$student) {
//                 DB::rollBack();
//                 return;
//             }

//             // MULTIPLE SAFETY CHECKS
//             if (!$this->isSafeForCleanup($student)) {
//                 Log::warning('MULTIPLE SAFETY CHECKS FAILED - Aborting cleanup:', [
//                     'student_id' => $studentId,
//                     'email' => $student->email,
//                     'created_at' => $student->created_at,
//                     'payment_status' => $student->payment_status,
//                     'has_payment_process' => $student->has_payment_process ?? 'not_set',
//                     'reason' => $reason
//                 ]);
//                 DB::rollBack();
//                 return;
//             }

//             Log::info('Starting SAFE cleanup with multiple protections:', [
//                 'student_id' => $studentId,
//                 'email' => $student->email,
//                 'created_at' => $student->created_at,
//                 'payment_status' => $student->payment_status,
//                 'reason' => $reason
//             ]);

//             // Cleanup related payment records
//             $invoices = Invoice::where('student_id', $studentId)->get();

//             foreach ($invoices as $invoice) {
//                 Payment::where('invoice_id', $invoice->id)->delete();
//                 Installment::where('invoice_id', $invoice->id)->delete();
//                 $invoice->delete();
//             }

//             // Delete the student record
//             $student->delete();

//             DB::commit();

//             Log::info('SAFE cleanup completed with protections:', [
//                 'student_id' => $studentId,
//                 'reason' => $reason,
//                 'invoices_deleted' => $invoices->count()
//             ]);

//         } catch (\Exception $e) {
//             DB::rollBack();

//             Log::error('SAFE cleanup failed with protections:', [
//                 'student_id' => $studentId,
//                 'reason' => $reason,
//                 'error' => $e->getMessage()
//             ]);
//         }
//     }

//     protected function calculateFirstInstallmentAmount(Course $course)
//     {
//         $installmentPlan = $course->installmentPlan;
//         $totalPriceWithFee = $course->course_price + self::REGISTRATION_FEE;
//         return $totalPriceWithFee * ($installmentPlan->first_installment_percentage / 100);
//     }

//     protected function createInstallmentPlan(Invoice $invoice, Course $course, Payment $payment)
//     {
//         $installmentPlan = $course->installmentPlan;
//         $totalAmount = $course->course_price + self::REGISTRATION_FEE;
//         $firstInstallmentAmount = $totalAmount * ($installmentPlan->first_installment_percentage / 100);

//         // First installment is already paid
//         Installment::create([
//             'invoice_id' => $invoice->id,
//             'amount' => $firstInstallmentAmount,
//             'due_date' => Carbon::now(),
//             'status' => 'paid',
//             'paid_at' => Carbon::now(),
//             'notes' => 'First installment paid (includes registration fee)',
//         ]);

//         // Create remaining installments
//         $remainingAmount = $totalAmount - $firstInstallmentAmount;
//         $remainingInstallments = $installmentPlan->number_of_installments - 1;

//         if ($remainingInstallments > 0) {
//             $installmentAmount = $remainingAmount / $remainingInstallments;

//             for ($i = 1; $i <= $remainingInstallments; $i++) {
//                 Installment::create([
//                     'invoice_id' => $invoice->id,
//                     'amount' => $installmentAmount,
//                     'due_date' => Carbon::now()->addMonths($i),
//                     'status' => 'pending',
//                     'notes' => "Installment {$i} of {$remainingInstallments}",
//                 ]);
//             }
//         }
//     }

//     private function sendStudentCredentialsEmail(Student $student)
//     {
//         try {
//             $plainPassword = session('student_registration_password');

//             if ($plainPassword) {
//                 Mail::to($student->email)->send(new StudentCredentials($student, $plainPassword, true));
//                 session()->forget('student_registration_password');

//                 Log::info('Student credentials email sent after successful payment:', [
//                     'student_id' => $student->id,
//                     'email' => $student->email
//                 ]);
//             }

//         } catch (\Exception $e) {
//             Log::error('Failed to send student credentials email after payment:', [
//                 'student_id' => $student->id,
//                 'email' => $student->email,
//                 'error' => $e->getMessage()
//             ]);
//         }
//     }

//     protected function sendPaymentConfirmationEmail(Student $student, Invoice $invoice, Course $course)
//     {
//         try {
//             if (empty($student->email)) {
//                 Log::error('Email not sent: Student email is empty');
//                 return false;
//             }

//             Mail::to($student->email)->send(new PaymentConfirmationMail($student, $invoice, $course));
//             Log::info('Payment confirmation email sent successfully to: ' . $student->email);

//         } catch (\Exception $e) {
//             Log::error('Payment Confirmation Email Error: ' . $e->getMessage());
//             throw $e;
//         }
//     }
// }
