<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 * @property int monitor_id
 * @property string type
 * @property string operator
 * @property string value
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

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }

}
