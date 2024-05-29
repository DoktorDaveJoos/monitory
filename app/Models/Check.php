<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int id
 * @property int monitor_id
 * @property int status_code
 * @property int response_time
 * @property array response_body
 * @property array response_headers
 * @property bool success
 * @property Carbon started_at
 * @property Carbon finished_at
 * @property Carbon created_at
 * @property Monitor monitor
 */
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

    public static function labels(Carbon $from, Carbon $to): array
    {
        $labels = [];
        $from = $from->copy();
        while ($from->lte($to)) {
            $labels[] = $from->format('Y-m-d H:i');
            $from->addMinute();
        }

        return $labels;
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
