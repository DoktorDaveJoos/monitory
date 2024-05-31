<?php

namespace App\Http\Resources;

use App\Models\Check;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin Check
 */
class CheckResource extends JsonResource
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
            'status_code' => $this->status_code,
            'response_time' => $this->response_time,
            'success' => $this->success,
            'response_headers' => $this->response_headers,
            'response_body' => $this->response_body,
            'created_at' => $this->created_at,
            'started_at' => Carbon::parse($this->started_at)->format('Y-m-d H:i'),
        ];
    }
}
