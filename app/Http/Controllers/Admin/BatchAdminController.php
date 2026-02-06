<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SharedBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BatchAdminController extends Controller
{
    public function index()
    {
        $batches = SharedBatch::withCount('files')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.index', [
            'batches' => $batches,
        ]);
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

        return redirect()->back()->with('status', 'Ablaufdatum wurde verlängert.');
    }

    public function destroy(SharedBatch $batch)
    {
        DB::transaction(function () use ($batch) {
            $batch->loadMissing('files');

            foreach ($batch->files as $file) {
                $path = 'uploads/'.$file->stored_name;
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
                $file->delete();
            }

            $batch->delete();
        });

        return redirect()->back()->with('status', 'Batch und Dateien wurden gelöscht.');
    }
}
