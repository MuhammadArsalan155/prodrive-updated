<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $password;
    public $isUpdate;

    public function __construct($student, $password, $isUpdate = false)
    {
        $this->student = $student;
        $this->password = $password;
        $this->isUpdate = $isUpdate;
    }

    public function build()
    {
        $subject = $this->isUpdate ? 'Your Student Account Details Have Been Updated' : 'Welcome! Your Student Account Has Been Created';

        return $this->subject($subject)
                    ->view('emails.student-credentials')
                    ->with([
                        'student' => $this->student,
                        'password' => $this->password,
                        'isUpdate' => $this->isUpdate,
                    ]);
    }
}
