<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SharedBatchDeletedByAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public int $fileCount)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('Dein Stratton Share-Upload wurde gelöscht')
            ->view('emails.shared-batch-deleted-by-admin')
            ->with([
                'fileCount' => $this->fileCount,
            ]);
    }
}
