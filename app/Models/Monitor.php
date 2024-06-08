<?php

namespace App\Models;

use App\Enums\ActionType;
use App\Enums\HttpMethod;
use App\Enums\Interval;
use App\Models\Scopes\MonitorScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property int id
 * @property int user_id
 * @property string name
 * @property ActionType type
 * @property string url
 * @property int interval
 * @property bool active
 * @property int alert_count
 * @property bool success
 * @property string last_checked_at
 * @property User user
 * @property Collection<Check> checks
 * @property Collection<Trigger> triggers
 */
class Monitor extends Model
{
    use HasFactory, SoftDeletes;

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'name',
        'type',
        'url',
        'expected_status_code',
        'interval',
        'timeout',
        'active',
        'method',
        'success',
        'last_checked_at',
    ];

    protected $casts = [
        'type' => ActionType::class,
        'method' => HttpMethod::class,
        'active' => 'boolean',
        'last_checked_at' => 'datetime',
        'interval' => Interval::class,
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
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
