<?php

namespace App\Models;

use App\Enums\ComparisonOperator;
use App\Enums\TriggerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 * @property int monitor_id
 * @property TriggerType type
 * @property ComparisonOperator operator
 * @property int value
 */
class Trigger extends Model
{
    use HasFactory;

    protected $fillable = [
        'monitor_id',
        'type',
        'operator',
        'value',
    ];

    protected $casts = [
        'operator' => ComparisonOperator::class,
        'type' => TriggerType::class,
    ];

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
