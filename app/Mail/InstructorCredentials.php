<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InstructorCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $instructor;
    public $password;
    public $isUpdate;

    public function __construct($instructor, $password, $isUpdate = false)
    {
        $this->instructor = $instructor;
        $this->password = $password;
        $this->isUpdate = $isUpdate;
    }

    public function build()
    {
        $subject = $this->isUpdate ? 'Your Account Details Have Been Updated' : 'Welcome! Your Account Has Been Created';

        return $this->subject($subject)
                    ->view('emails.instructor-credentials')
                    ->with([
                        'instructor' => $this->instructor,
                        'password' => $this->password,
                        'isUpdate' => $this->isUpdate,
                    ]);
    }
}
