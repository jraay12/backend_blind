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
        $expiration = Carbon::now()->addMinutes(60); // Set expiration time (e.g., 1 hour)
        $tokenWithExpiration = $token . '|' . $expiration->timestamp;

        $this->resetLink = url('http://localhost:3000/new-password?token=' . $tokenWithExpiration);
    }

    public function build()
    {   
        return $this->subject('Reset Password Link')
            ->view('emails.reset_password_link')  // Specify the correct view file here
            ->with(['resetLink' => $this->resetLink]);
    }
}
