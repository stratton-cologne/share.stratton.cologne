<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SharedFile extends Model
{
    protected $fillable = [
        'shared_batch_id',
        'token',
        'uploader_email',
        'original_name',
        'stored_name',
        'mime_type',
        'size',
        'download_count',
        'max_downloads',
        'expires_at',
        'notified_at',
        'notified_72h_at',
        'notified_24h_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'notified_at' => 'datetime',
        'notified_72h_at' => 'datetime',
        'notified_24h_at' => 'datetime',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(SharedBatch::class, 'shared_batch_id');
    }
}
