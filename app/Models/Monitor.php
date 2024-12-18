<?php

namespace App\Models;

use App\Enums\ActionType;
use App\Enums\AuthType;
use App\Enums\HttpMethod;
use App\Enums\Interval;
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
 * @property string host
 * @property int interval
 * @property bool active
 * @property int alert_count
 * @property bool success
 * @property AuthType auth
 * @property string auth_username
 * @property string auth_password
 * @property string auth_token
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
        'host',
        'expected_status_code',
        'interval',
        'timeout',
        'active',
        'method',
        'success',
        'auth',
        'auth_username',
        'auth_password',
        'auth_token',
        'last_checked_at',
    ];

    protected $casts = [
        'type' => ActionType::class,
        'method' => HttpMethod::class,
        'active' => 'boolean',
        'last_checked_at' => 'datetime',
        'interval' => Interval::class,
        'auth' => AuthType::class,
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
