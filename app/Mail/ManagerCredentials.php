<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ManagerCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $manager;
    public $password;
    public $isUpdate;

    public function __construct($manager, $password, $isUpdate = false)
    {
        $this->manager = $manager;
        $this->password = $password;
        $this->isUpdate = $isUpdate;
    }

    public function build()
    {
        $subject = $this->isUpdate ? 'Your Manager Account Details Have Been Updated' : 'Welcome! Your Manager Account Has Been Created';

        return $this->subject($subject)
                    ->view('emails.manager-credentials')
                    ->with([
                        'manager' => $this->manager,
                        'password' => $this->password,
                        'isUpdate' => $this->isUpdate,
                    ]);
    }
}
