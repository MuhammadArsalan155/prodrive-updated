<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

   public function createCheckoutSession($course, $amountInCents, $paymentType, $student, $payment)
{
    try {
        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'unit_amount' => $amountInCents, // REMOVED * 100 - already in cents
                        'product_data' => [
                            'name' => $paymentType . ': ' . $course->course_name,
                        ],
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel') . '?session_id={CHECKOUT_SESSION_ID}',
            'metadata' => [
                'student_id' => $student->id,
                'course_id' => $course->id,
                'payment_id' => $payment->id,
                'payment_type' => $paymentType,
            ],
            'customer_email' => $student->email,
            'expires_at' => now()->addHours(2)->timestamp,
        ]);

        return $checkoutSession;
    } catch (ApiErrorException $e) {
        Log::error('Stripe Checkout Session Error: ' . $e->getMessage());
        throw $e;
    }
}

    public function validatePayment($sessionId)
    {
        try {
            $session = Session::retrieve($sessionId);

            Log::info('Stripe Payment Validation:', [
                'session_id' => $sessionId,
                'payment_status' => $session->payment_status,
                'status' => $session->status,
            ]);

            // Verify payment status
            if ($session->payment_status === 'paid') {
                // Retrieve metadata
                $metadata = $session->metadata;

                return [
                    'status' => true,
                    'course_id' => $metadata->course_id,
                    'payment_type' => $metadata->payment_type,
                    'payment_intent' => $session->payment_intent,
                    'student_id' => $metadata->student_id,
                    'payment_id' => $metadata->payment_id,
                ];
            }

            return [
                'status' => false,
                'reason' => 'Payment not completed',
                'payment_status' => $session->payment_status,
                'session_status' => $session->status,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe Payment Validation Error: ' . $e->getMessage(), [
                'session_id' => $sessionId,
            ]);
            return [
                'status' => false,
                'reason' => 'Stripe API Error: ' . $e->getMessage(),
            ];
        }
    }

    public function getPaymentDetails($sessionId)
    {
        try {
            // Retrieve the Checkout Session
            $session = Session::retrieve($sessionId);

            // Retrieve the PaymentIntent
            $paymentIntent = PaymentIntent::retrieve($session->payment_intent);

            // Extract relevant payment details
            return [
                'session_id' => $sessionId,
                'payment_intent' => $session->payment_intent,
                'amount' => $paymentIntent->amount / 100, // Convert back to dollars
                'currency' => $paymentIntent->currency,
                'payment_method' => $paymentIntent->payment_method,
                'status' => $paymentIntent->status,
                'created' => $paymentIntent->created,
                'metadata' => $session->metadata->toArray(),
                'full_details' => $paymentIntent->toArray(),
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe Payment Details Error: ' . $e->getMessage(), [
                'session_id' => $sessionId,
            ]);
            throw $e;
        }
    }

    /**
     * Check if a checkout session has expired
     */
    public function isSessionExpired($sessionId)
    {
        try {
            $session = Session::retrieve($sessionId);
            return $session->status === 'expired' || $session->expires_at < time();
        } catch (ApiErrorException $e) {
            Log::error('Stripe Session Expiry Check Error: ' . $e->getMessage());
            return true; // Assume expired if we can't check
        }
    }

    /**
     * Get session status for cleanup purposes
     */
    public function getSessionStatus($sessionId)
    {
        try {
            $session = Session::retrieve($sessionId);
            return [
                'status' => $session->status,
                'payment_status' => $session->payment_status,
                'expires_at' => $session->expires_at,
                'metadata' => $session->metadata->toArray(),
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe Session Status Error: ' . $e->getMessage());
            return null;
        }
    }

    private function getInstallmentNumber($installment)
    {
        $installmentNumber = \App\Models\Installment::where('invoice_id', $installment->invoice_id)->where('id', '<=', $installment->id)->orderBy('due_date', 'asc')->count();

        return $installmentNumber;
    }

    public function createInstallmentCheckoutSession($installment, $student, $payment)
    {
        try {
            $course = $installment->invoice->course;
            $installmentNumber = $this->getInstallmentNumber($installment);
            $amountInCents = (int) round($installment->amount * 100);

            $checkoutSession = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'unit_amount' => $amountInCents, // Convert to cents
                            'product_data' => [
                                'name' => "Installment #{$installmentNumber} - {$course->course_name}",
                                'description' => "Due: {$installment->due_date->format('M d, Y')}",
                            ],
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => route('installment.payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('installment.payment.cancel', $installment->id),
                'metadata' => [
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'payment_id' => $payment->id,
                    'installment_id' => $installment->id,
                    'payment_type' => 'installment',
                    'installment_number' => $installmentNumber,
                ],
                'customer_email' => $student->email,
                'billing_address_collection' => 'auto',
                'phone_number_collection' => [
                    'enabled' => false,
                ],
                'expires_at' => now()->addHours(24)->timestamp,
            ]);

            return $checkoutSession;
        } catch (ApiErrorException $e) {
            Log::error('Stripe Installment Checkout Session Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieve a checkout session
     */
    public function retrieveCheckoutSession($sessionId)
    {
        try {
            return Session::retrieve($sessionId);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Session Retrieval Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a refund for a payment
     */
    public function createRefund($paymentIntentId, $amount = null)
    {
        try {
            $refundData = ['payment_intent' => $paymentIntentId];

            if ($amount) {
                $refundData['amount'] = $amount * 100; // Convert to cents
            }

            return \Stripe\Refund::create($refundData);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Refund Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cancel a checkout session (for cleanup purposes)
     */
    public function cancelCheckoutSession($sessionId)
    {
        try {
            // Note: Stripe doesn't allow canceling sessions directly
            // But we can check if it's still active/pending
            $session = Session::retrieve($sessionId);

            if ($session->status === 'open') {
                // Session is still active but we can't cancel it directly
                // The expiration will handle it, or webhook will notify us
                Log::info('Cannot cancel active Stripe session, will expire naturally:', [
                    'session_id' => $sessionId,
                    'expires_at' => date('Y-m-d H:i:s', $session->expires_at),
                ]);
            }

            return $session;
        } catch (ApiErrorException $e) {
            Log::error('Stripe Session Cancel Error: ' . $e->getMessage());
            return null;
        }
    }
}
