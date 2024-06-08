<?php

namespace App\Http\Resources;

use App\Enums\HttpStatusCode;
use App\Enums\Operator;
use App\Enums\TriggerType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Operator|HttpStatusCode|TriggerType
 */
class EnumOptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'value' => $this->value,
            'label' => $this->getLabel(),
        ];
    }
}
