<?php

namespace App\Mail;

use App\Models\SharedBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SharedFileExpiring extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SharedBatch $sharedBatch, public int $hoursLeft)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('Dein Stratton Share-Link läuft bald ab')
            ->view('emails.shared-file-expiring')
            ->with([
                'sharedBatch' => $this->sharedBatch,
                'hoursLeft' => $this->hoursLeft,
            ]);
    }
}
