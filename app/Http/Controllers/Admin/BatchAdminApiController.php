<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SharedBatchDeletedByAdmin;
use App\Models\SharedBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class BatchAdminApiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 20);
        $batches = SharedBatch::withCount('files')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json($batches);
    }

    public function extend(Request $request, SharedBatch $batch)
    {
        $validated = $request->validate([
            'extend_days' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        $baseDate = $batch->expires_at ?? Carbon::now();
        $newExpiry = $baseDate->copy()->addDays((int) $validated['extend_days']);

        $batch->forceFill([
            'expires_at' => $newExpiry,
            'notified_72h_at' => null,
            'notified_24h_at' => null,
        ])->save();

        $batch->files()->update([
            'expires_at' => $newExpiry,
            'notified_72h_at' => null,
            'notified_24h_at' => null,
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function destroy(SharedBatch $batch)
    {
        $batch->loadMissing('files');
        $uploaderEmail = $batch->uploader_email;
        $fileCount = $batch->files->count();

        DB::transaction(function () use ($batch) {
            foreach ($batch->files as $file) {
                $path = 'uploads/'.$file->stored_name;
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
                $file->delete();
            }

            $batch->delete();
        });

        if ($uploaderEmail) {
            Mail::to($uploaderEmail)->send(new SharedBatchDeletedByAdmin($fileCount));
        }

        return response()->json(['status' => 'deleted']);
    }
}
