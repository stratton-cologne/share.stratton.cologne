<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminMfaCode extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('Dein Stratton Share Admin Code')
            ->view('emails.admin-mfa-code')
            ->with([
                'code' => $this->code,
            ]);
    }
}
