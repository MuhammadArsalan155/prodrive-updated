<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ParentCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $parent;
    public $student;
    public $parentPassword;
    public $isUpdate;

    public function __construct($parent, $student, $parentPassword, $isUpdate = false)
    {
        $this->parent = $parent;
        $this->student = $student;
        $this->parentPassword = $parentPassword;
        $this->isUpdate = $isUpdate;
    }

    public function build()
    {
        $subject = $this->isUpdate ? 'Your Parent Account Details Have Been Updated' : 'Welcome! Your Parent Account Has Been Created';

        return $this->subject($subject)
                    ->view('emails.parent-credentials')
                    ->with([
                        'parent' => $this->parent,
                        'student' => $this->student,
                        'parentPassword' => $this->parentPassword,
                        'isUpdate' => $this->isUpdate,
                    ]);
    }
}
