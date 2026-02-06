<?php

namespace App\Mail;

use App\Models\SharedBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SharedFileDeleted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SharedBatch $sharedBatch)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('Dein Stratton Share-Upload wurde gelöscht')
            ->view('emails.shared-file-deleted')
            ->with([
                'sharedBatch' => $this->sharedBatch,
            ]);
    }
}
