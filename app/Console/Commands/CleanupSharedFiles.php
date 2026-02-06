<?php

namespace App\Console\Commands;

use App\Mail\SharedFileDeleted;
use App\Mail\SharedFileExpiring;
use App\Models\SharedBatch;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CleanupSharedFiles extends Command
{
    protected $signature = 'shared-files:cleanup';

    protected $description = 'Send expiry notices and remove expired shared files.';

    public function handle(): int
    {
        $now = Carbon::now();
        $this->sendNotice($now, 72, 'notified_72h_at');
        $this->sendNotice($now, 24, 'notified_24h_at');

        SharedBatch::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->chunkById(200, function ($files) {
                foreach ($files as $batch) {
                    $batch->loadMissing('files');
                    Mail::to($batch->uploader_email)->send(new SharedFileDeleted($batch));

                    foreach ($batch->files as $file) {
                        $path = 'uploads/'.$file->stored_name;
                        if (Storage::exists($path)) {
                            Storage::delete($path);
                        }
                        $file->delete();
                    }

                    $batch->delete();
                }
            });

        return self::SUCCESS;
    }

    private function sendNotice(Carbon $now, int $thresholdHours, string $column): void
    {
        SharedBatch::query()
            ->whereNotNull('expires_at')
            ->whereNull($column)
            ->where('expires_at', '>', $now)
            ->where('expires_at', '<=', $now->copy()->addHours($thresholdHours))
            ->chunkById(200, function ($files) use ($now, $column) {
                foreach ($files as $batch) {
                    $hoursLeft = max(1, $now->diffInHours($batch->expires_at));
                    Mail::to($batch->uploader_email)->send(new SharedFileExpiring($batch, $hoursLeft));
                    $batch->forceFill([$column => now()])->save();
                }
            });
    }
}
