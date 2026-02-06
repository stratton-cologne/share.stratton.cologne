<?php

namespace App\Http\Controllers;

use App\Mail\SharedBatchCreated;
use App\Models\SharedBatch;
use App\Models\SharedFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class SharedFileController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['file', 'max:1048576'],
            'uploader_email' => ['required', 'email', 'max:255'],
            'expires_in_days' => ['required', 'integer', 'min:1', 'max:30'],
            'max_downloads' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ]);

        $expiresAt = Carbon::now()->addDays((int) $validated['expires_in_days']);

        $batch = SharedBatch::create([
            'token' => Str::random(32),
            'uploader_email' => $validated['uploader_email'],
            'expires_at' => $expiresAt,
            'max_downloads' => $validated['max_downloads'] ?? null,
        ]);

        $uploads = [];
        foreach ($request->file('files', []) as $file) {
            $token = Str::random(32);
            $storedName = $token.'_'.$file->hashName();
            $file->storeAs('uploads', $storedName);

            $sharedFile = SharedFile::create([
                'shared_batch_id' => $batch->id,
                'token' => $token,
                'uploader_email' => $validated['uploader_email'],
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $storedName,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'max_downloads' => $validated['max_downloads'] ?? null,
                'expires_at' => $expiresAt,
            ]);

            $uploads[] = $this->transform($sharedFile);
        }

        Mail::to($batch->uploader_email)->send(new SharedBatchCreated(
            $batch,
            collect($uploads)
                ->map(fn (array $upload) => [
                    'original_name' => $upload['original_name'],
                    'size' => (int) $upload['size'],
                ])
                ->values()
                ->all()
        ));

        return response()->json([
            'batch' => $this->transformBatch($batch),
            'uploads' => $uploads,
        ]);
    }

    public function showBatch(string $token)
    {
        $batch = SharedBatch::where('token', $token)->firstOrFail();
        if ($message = $this->getBatchAvailabilityError($batch)) {
            return response()->json(['message' => $message], 410);
        }

        return response()->json([
            'batch' => $this->transformBatch($batch),
            'files' => $batch->files->map(fn (SharedFile $file) => $this->transform($file))->values(),
        ]);
    }

    public function downloadBatch(string $token)
    {
        $batch = SharedBatch::where('token', $token)->firstOrFail();
        if ($message = $this->getBatchAvailabilityError($batch)) {
            return response()->json(['message' => $message], 410);
        }

        $files = $batch->files()->get();
        if ($files->isEmpty()) {
            return response()->json(['message' => 'Keine Dateien vorhanden.'], 404);
        }

        foreach ($files as $file) {
            if ($message = $this->getAvailabilityError($file)) {
                return response()->json(['message' => $message], 410);
            }
        }

        $tmpDir = storage_path('app/tmp');
        if (!File::exists($tmpDir)) {
            File::makeDirectory($tmpDir, 0755, true);
        }

            $dateStamp = now()->format('Ymd');
        $fileCount = $files->count();
        $zipName = "StrattonShare_{$dateStamp}_{$fileCount}.zip";
        $zipPath = $tmpDir.'/'.$zipName;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return response()->json(['message' => 'ZIP konnte nicht erstellt werden.'], 500);
        }

        $nameCount = [];
        foreach ($files as $file) {
            $path = 'uploads/'.$file->stored_name;
            if (!Storage::exists($path)) {
                continue;
            }
            $originalName = $file->original_name;
            $safeName = $originalName;
            if (isset($nameCount[$originalName])) {
                $nameCount[$originalName]++;
                $dot = strrpos($originalName, '.');
                if ($dot !== false) {
                    $base = substr($originalName, 0, $dot);
                    $ext = substr($originalName, $dot);
                    $safeName = $base.'_'.$nameCount[$originalName].$ext;
                } else {
                    $safeName = $originalName.'_'.$nameCount[$originalName];
                }
            } else {
                $nameCount[$originalName] = 1;
            }

            $zip->addFile(Storage::path($path), $safeName);
        }

        $zip->close();

        foreach ($files as $file) {
            $file->increment('download_count');
        }

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }

    public function show(string $token)
    {
        $sharedFile = SharedFile::where('token', $token)->firstOrFail();
        if ($message = $this->getAvailabilityError($sharedFile)) {
            return response()->json(['message' => $message], 410);
        }

        return response()->json($this->transform($sharedFile));
    }

    public function download(string $token)
    {
        $sharedFile = SharedFile::where('token', $token)->firstOrFail();
        if ($message = $this->getAvailabilityError($sharedFile)) {
            return response()->json(['message' => $message], 410);
        }

        $path = 'uploads/'.$sharedFile->stored_name;
        if (!Storage::exists($path)) {
            return response()->json(['message' => 'Datei nicht gefunden.'], 404);
        }

        $sharedFile->increment('download_count');

        return Storage::download($path, $sharedFile->original_name, [
            'Content-Type' => $sharedFile->mime_type ?? 'application/octet-stream',
        ]);
    }

    private function getAvailabilityError(SharedFile $sharedFile): ?string
    {
        if ($sharedFile->expires_at && now()->greaterThan($sharedFile->expires_at)) {
            return 'Dieser Link ist abgelaufen.';
        }

        if ($sharedFile->max_downloads && $sharedFile->download_count >= $sharedFile->max_downloads) {
            return 'Dieses Download-Limit wurde erreicht.';
        }

        return null;
    }

    private function transform(SharedFile $sharedFile): array
    {
        $batchToken = $sharedFile->shared_batch_id
            ? optional($sharedFile->batch)->token
            : null;

        return [
            'token' => $sharedFile->token,
            'share_url' => $batchToken ? url("/share/{$batchToken}") : url("/share/{$sharedFile->token}"),
            'download_url' => url("/api/files/{$sharedFile->token}/download"),
            'original_name' => $sharedFile->original_name,
            'mime_type' => $sharedFile->mime_type,
            'size' => $sharedFile->size,
            'download_count' => $sharedFile->download_count,
            'max_downloads' => $sharedFile->max_downloads,
            'expires_at' => optional($sharedFile->expires_at)->toIso8601String(),
        ];
    }

    private function transformBatch(SharedBatch $batch): array
    {
        return [
            'token' => $batch->token,
            'share_url' => url("/share/{$batch->token}"),
            'download_url' => url("/api/batches/{$batch->token}/download"),
            'expires_at' => optional($batch->expires_at)->toIso8601String(),
            'max_downloads' => $batch->max_downloads,
        ];
    }

    private function getBatchAvailabilityError(SharedBatch $batch): ?string
    {
        if ($batch->expires_at && now()->greaterThan($batch->expires_at)) {
            return 'Dieser Link ist abgelaufen.';
        }

        return null;
    }
}
