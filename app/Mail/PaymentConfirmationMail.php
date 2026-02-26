<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\Invoice;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $invoice;
    public $course;
    public $password;
    public $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Student $student, Invoice $invoice, Course $course)
    {
        $this->student = $student;
        $this->invoice = $invoice;
        $this->course = $course;

        $this->password = session('student_registration_password');


        session()->forget('student_registration_password');

        $this->loginUrl = route('login');
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Payment Confirmation & Account Details - ' . $this->course->course_name)
            ->view('emails.payment-confirmation')
            ->with([
                'studentName' => $this->student->first_name . ' ' . $this->student->last_name,
                'courseName' => $this->course->course_name,
                'invoiceNumber' => $this->invoice->invoice_number,
                'amount' => $this->invoice->amount,
                'paymentDate' => now()->format('F d, Y'),
                'student' => $this->student,
                'password' => $this->password,
                'loginUrl' => $this->loginUrl,
                'hasNewPassword' => !empty($this->password),
            ]);
    }
}
