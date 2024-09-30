<?php

namespace App\Http\Resources;

use App\Models\Monitor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

/**
 * @mixin Monitor
 */
class MonitorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'host' => $this->host,
            'name' => $this->name,
            'method' => $this->method,
            'type' => $this->type,
            'interval' => $this->interval,
            'last_checked_at' => $this->last_checked_at,
            'active' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->checks->last()?->success ?? null,
            'uptime' => Number::percentage(
                number: $this->checks()->count() > 0
                    ? $this->checks()
                        ->where('success', true)
                        ->count() / $this->checks()->count() * 100
                    : 0,
                precision: 2
            ),
            'checks' => CheckResource::collection(
                $this->checks()
                    ->where('started_at', '>=', now()->subHour())
                    ->orderBy('started_at')
                    ->get()
            ),
        ];
    }
}
