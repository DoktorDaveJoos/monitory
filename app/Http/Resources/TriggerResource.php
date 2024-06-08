<?php

namespace App\Http\Resources;

use App\Models\Trigger;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Trigger
 */
class TriggerResource extends JsonResource
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
            'type' => $this->type->getLabel(),
            'operator' => $this->operator->getLabel(),
            'value' => $this->value,
        ];
    }
}
