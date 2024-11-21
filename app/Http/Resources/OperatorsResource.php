<?php

namespace App\Http\Resources;

use App\Enums\HttpStatusCode;
use App\Enums\Operator;
use App\Enums\TriggerType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TriggerType
 */
class OperatorsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'trigger' => $this->value,
            'value' => collect(Operator::casesForTrigger($this->resource))
                ->map(fn (Operator $operator) => new EnumOptionResource($operator))
                ->toArray(),
        ];
    }
}
