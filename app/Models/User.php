<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use LemonSqueezy\Laravel\Billable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $email_verified_at
 * @property Collection<Monitor> $monitors
 */
class User extends Authenticatable
{
    use Billable, HasFactory, Notifiable;

    public const MAX_MONITORS = 3;

    public const MAX_MONITORS_WITH_SUBSCRIPTION = 25;

    public const MAX_TRIGGERS_PER_MONITOR = 1;

    public const MAX_TRIGGERS_PER_MONITOR_WITH_SUBSCRIPTION = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function monitors(): HasMany
    {
        return $this->hasMany(Monitor::class);
    }
}
