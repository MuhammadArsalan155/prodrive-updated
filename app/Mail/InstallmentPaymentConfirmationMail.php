<?php

namespace App\Mail;

use App\Models\Student;
use App\Models\Installment;
use App\Models\Course;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InstallmentPaymentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $installment;
    public $course;
    public $payment;
    public $remainingInstallments;

    /**
     * Create a new message instance.
     *
     * @param Student $student
     * @param Installment $installment
     * @param Course $course
     * @param Payment $payment
     */
    public function __construct(Student $student, Installment $installment, Course $course, Payment $payment)
    {
        $this->student = $student;
        $this->installment = $installment;
        $this->course = $course;
        $this->payment = $payment;

        // Get remaining installments
        $this->remainingInstallments = Installment::where('invoice_id', $installment->invoice_id)
            ->where('status', 'pending')
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Installment Payment Confirmation - ' . $this->course->course_name)
                    ->view('emails.installment-payment-confirmation')
                    ->with([
                        'studentName' => $this->student->first_name . ' ' . $this->student->last_name,
                        'courseName' => $this->course->course_name,
                        'paidAmount' => $this->installment->amount,
                        'paymentDate' => $this->installment->paid_at,
                        'transactionId' => $this->payment->transaction_id,
                        'invoiceNumber' => $this->installment->invoice->invoice_number,
                        'remainingInstallments' => $this->remainingInstallments,
                        'nextInstallment' => $this->remainingInstallments->first(),
                        'isFullyPaid' => $this->remainingInstallments->isEmpty(),
                    ]);
    }
}