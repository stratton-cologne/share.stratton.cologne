<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SharedBatch extends Model
{
    protected $fillable = [
        'token',
        'uploader_email',
        'expires_at',
        'max_downloads',
        'notified_72h_at',
        'notified_24h_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'notified_72h_at' => 'datetime',
        'notified_24h_at' => 'datetime',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(SharedFile::class, 'shared_batch_id');
    }
}
