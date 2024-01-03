<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;


class ResetPasswordLink extends Mailable
{
    use Queueable, SerializesModels;

    public $resetLink;

    public function __construct($token)
    {
        $this->resetLink = $this->buildResetLink($token);
    }

    public function buildResetLink($token)
    {
        return url("http://localhost:3000/new-password?token={$token}");
    }

    public function build()
    {
        return $this->subject(Lang::get('Reset Password Link'))
            ->view('emails.reset_password_link')  // Specify the correct view file here
            ->with(['resetLink' => $this->resetLink]);
    }
}
