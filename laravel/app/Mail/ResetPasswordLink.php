<?php


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordLink extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        $resetLink = url('password/reset', $this->token);

        return $this->subject('Reset Password Link')
            ->view('emails.reset_password_link')  // Specify the correct view file here
            ->with(['resetLink' => $resetLink]);
    }
}

