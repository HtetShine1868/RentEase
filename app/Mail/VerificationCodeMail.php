<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationCode;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->verificationCode = $user->verification_code;
    }

    public function build()
    {
        return $this->subject('RMS - Email Verification Code')
                    ->view('emails.verification-code')
                    ->with([
                        'user' => $this->user,
                        'verificationCode' => $this->verificationCode,
                    ]);
    }
}