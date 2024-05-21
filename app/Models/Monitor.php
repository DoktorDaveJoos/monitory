<?php

namespace App\Models;

use App\Enums\ActionType;
use App\Enums\HttpMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

}
