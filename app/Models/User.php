<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\SlackRoute;
use Illuminate\Support\Collection;
use LemonSqueezy\Laravel\Billable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $email_verified_at
 * @property array $settings
 * @property Collection<Monitor> $monitors
 * @property SlackConnection $slackConnection
 */
class User extends Authenticatable implements MustVerifyEmail
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
        'settings',
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
     * Route notifications for the Slack channel.
     */
    public function routeNotificationForSlack(Notification $notification): mixed
    {
        return SlackRoute::make($this->slackConnection->channel, $this->slackConnection->token);
    }

    public function monitors(): HasMany
    {
        return $this->hasMany(Monitor::class);
    }

    public function slackConnection(): HasOne
    {
        return $this->hasOne(SlackConnection::class);
    }

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
            'settings' => 'array',
        ];
    }
}
