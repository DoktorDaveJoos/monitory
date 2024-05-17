<?php

namespace App\Models;

use App\Enums\ActionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitor extends Model
{
    use HasFactory;

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
        'active' => 'boolean',
        'last_checked_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

}
