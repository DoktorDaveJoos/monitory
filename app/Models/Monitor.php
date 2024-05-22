<?php

namespace App\Models;

use App\Enums\ActionType;
use App\Enums\HttpMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int id
 * @property string name
 * @property ActionType type
 * @property string url
 * @property int expected_status_code
 * @property int frequency
 * @property int timeout
 * @property bool active
 * @property string last_checked_at
 * @property User user
 * @property Collection<Check> checks
 * @property Collection<Trigger> triggers
 */
class Monitor extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'name',
        'type',
        'url',
        'expected_status_code',
        'frequency',
        'timeout',
        'active',
        'last_checked_at',
    ];

    protected $casts = [
        'type' => ActionType::class,
        'method' => HttpMethod::class,
        'active' => 'boolean',
        'last_checked_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checks(): HasMany
    {
        return $this->hasMany(Check::class);
    }

    public function triggers(): HasMany
    {
        return $this->hasMany(Trigger::class);
    }

}
