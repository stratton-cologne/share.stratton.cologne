<?php

namespace App\Mail;

use App\Models\SharedBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SharedBatchCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array<int, array{original_name: string, size: int}> $files
     */
    public function __construct(public SharedBatch $sharedBatch, public array $files)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('Dein Stratton Share-Link ist bereit')
            ->view('emails.shared-batch-created')
            ->with([
                'sharedBatch' => $this->sharedBatch,
                'files' => $this->files,
            ]);
    }
}
