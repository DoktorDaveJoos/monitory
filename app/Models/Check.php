<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Check extends Model
{
    use HasFactory;

    protected $fillable = [
        'monitor_id',
        'status_code',
        'response_time',
        'response_body',
        'response_headers',
        'started_at',
        'finished_at',
        'created_at',
    ];

    protected $casts = [
        'response_headers' => 'array',
        'response_time' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
