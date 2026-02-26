<?php

namespace App\Http\Controllers;

use App\Mail\InstallmentPaymentConfirmationMail;
use App\Models\Installment;
use App\Models\Payment;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InstallmentPaymentController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Show installment payment page
     */
    public function showInstallmentPayment($installmentId)
    {

        try {
            $installment = Installment::with(['invoice.student', 'invoice.course'])->findOrFail($installmentId);

            // Verify installment is pending
            if ($installment->status !== 'pending') {
                return redirect()->route('student.dashboard')
                    ->with('error', 'This installment has already been processed.');
            }

            // Check if installment belongs to the authenticated student (if using auth)
            // Uncomment if you have student authentication
            // if (auth('student')->check() && $installment->invoice->student_id !== auth('student')->id()) {
            //     abort(403, 'Unauthorized access to this installment.');
            // }

            return view('student.installment-payment', compact('installment'));

        } catch (\Exception $e) {
            Log::error('Error loading installment payment page: ' . $e->getMessage());
            return redirect()->route('student.dashboard')
                ->with('error', 'Installment not found or invalid.');
        }
    }

    /**
     * Process installment payment
     */
    public function processInstallmentPayment(Request $request, $installmentId)
    {
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id'
        ]);

        DB::beginTransaction();

        try {
            $installment = Installment::with(['invoice.student', 'invoice.course'])->findOrFail($installmentId);

            // Verify installment is pending
            if ($installment->status !== 'pending') {
                throw new \Exception('This installment has already been processed.');
            }

            $student = $installment->invoice->student;
            $course = $installment->invoice->course;

            // Create payment record
            $payment = Payment::create([
                'invoice_id' => $installment->invoice_id,
                'payment_method_id' => $request->payment_method_id,
                'amount' => $installment->amount,
                'transaction_id' => (new Payment())->generateTransactionId(),
                'status' => 'pending',
                'payment_details' => json_encode([
                    'payment_type' => 'installment',
                    'installment_id' => $installment->id,
                    'installment_number' => $this->getInstallmentNumber($installment),
                    'course_name' => $course->course_name,
                    'student_email' => $student->email,
                ]),
            ]);

            // Create Stripe Checkout Session for installment
            $checkoutSession = $this->stripeService->createInstallmentCheckoutSession(
                $installment,
                $student,
                $payment
            );

            // Update payment with Stripe session details
            $payment->update([
                'transaction_id' => $checkoutSession->id,
                'payment_details' => json_encode([
                    'payment_type' => 'installment',
                    'installment_id' => $installment->id,
                    'installment_number' => $this->getInstallmentNumber($installment),
                    'checkout_session_id' => $checkoutSession->id,
                    'checkout_url' => $checkoutSession->url,
                    'course_name' => $course->course_name,
                    'student_email' => $student->email,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'checkout_url' => $checkoutSession->url,
                'payment_id' => $payment->id,
                'installment_id' => $installment->id,
                'amount' => $installment->amount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Installment Payment Processing Error: ' . $e->getMessage(), [
                'installment_id' => $installmentId,
                'student_id' => $installment->invoice->student_id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle successful installment payment
     */
    public function handleInstallmentPaymentSuccess(Request $request)
    {

        $sessionId = $request->get('session_id');

        DB::beginTransaction();

        try {
            // Validate payment with Stripe
            $paymentValidation = $this->stripeService->validatePayment($sessionId);

            if (!$paymentValidation['status']) {
                throw new \Exception('Payment validation failed');
            }

            // Find payment by session ID
            $payment = Payment::where('transaction_id', $sessionId)->firstOrFail();
            $paymentDetails = json_decode($payment->payment_details, true);
            $installment = Installment::findOrFail($paymentDetails['installment_id']);
            $student = $payment->invoice->student;
            $course = $payment->invoice->course;

            // Update payment status
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            // Update installment status
            $installment->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            // Check if all installments are paid to update invoice status
            $remainingInstallments = Installment::where('invoice_id', $installment->invoice_id)
                ->where('status', 'pending')
                ->count();

            if ($remainingInstallments === 0) {
                $installment->invoice->update(['status' => 'fully_paid']);

                // Update student course status if needed
                if ($student->course_status == 0) {
                    $student->update(['course_status' => 1]); // Course active
                }
            }

            // Send confirmation email
            $this->sendInstallmentConfirmationEmail($student, $installment, $course, $payment);

            DB::commit();

            return redirect()->route('login')
                ->with([
                    'success' => true,
                    'message' => 'Installment payment successful!',
                    'amount_paid' => $installment->amount,
                    'course_name' => $course->course_name,
                ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Installment Payment Success Handler Error: ' . $e->getMessage());

            return redirect()->route('student.dashboard')
                ->with([
                    'success' => false,
                    'message' => 'Payment processing failed: ' . $e->getMessage(),
                ]);
        }
    }


    /**
     * Get installment number in sequence
     */
    private function getInstallmentNumber(Installment $installment)
    {
        $installmentNumber = Installment::where('invoice_id', $installment->invoice_id)
            ->where('id', '<=', $installment->id)
            ->orderBy('due_date', 'asc')
            ->count();

        return $installmentNumber;
    }

    /**
     * Send installment payment confirmation email
     */
    private function sendInstallmentConfirmationEmail($student, $installment, $course, $payment)
    {
        try {
            // You can create a specific mail class for installment confirmations
            Mail::to($student->email)->send(new InstallmentPaymentConfirmationMail($student, $installment, $course, $payment));

            Log::info('Installment payment confirmation email sent', [
                'student_id' => $student->id,
                'installment_id' => $installment->id,
                'amount' => $installment->amount,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send installment confirmation email: ' . $e->getMessage());
        }
    }
}