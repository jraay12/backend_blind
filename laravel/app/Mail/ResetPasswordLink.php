<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ResetPasswordLink extends Mailable
{
    use Queueable, SerializesModels;

    public $resetLink;

    public function __construct($token)
    {
        $this->resetLink = url('http://localhost:3000/new-password?token=' . $token);
    }

    public function build()
    {   
        return $this->subject('Reset Password Link')
            ->view('emails.reset_password_link')  // Specify the correct view file here
            ->with(['resetLink' => $this->resetLink]);
    }
}
