<?php

namespace App\Http\Requests;

use App\Enums\Operator;
use App\Enums\TriggerType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $type
 */
class StoreTriggerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:'.implode(',', TriggerType::values()),
            'operator' => [
                Rule::when(self::notPing($this->type), 'required|in:'.implode(',', Operator::values())),
            ],
            'value' => [
                Rule::when(self::notPing($this->type), 'required|numeric'),
            ],
        ];
    }

    public static function notPing(string $type): bool
    {
        return $type !== TriggerType::PING->value;
    }
}
