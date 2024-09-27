<?php

namespace App\Http\Requests;

use App\Enums\ActionType;
use App\Enums\AuthType;
use App\Rules\IntervalLimit;
use App\Rules\IpOrHost;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $name
 * @property string $type
 */
class StoreMonitorRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:http,ping'],
            'url' => [
                Rule::requiredIf(fn () => $this->type === ActionType::HTTP->value),
                'url:http,https',
                'present_if:type,http',
            ],
            'host' => [
                Rule::requiredIf(fn () => $this->type === ActionType::PING->value),
                new IpOrHost,
                'present_if:type,ping',
            ],
            'method' => [
                Rule::requiredIf(fn () => $this->type === ActionType::HTTP->value),
                'present_if:type,http',
                'string',
                'in:GET,POST,PUT,DELETE',
            ],
            'interval' => ['required', 'integer', new IntervalLimit],
            'auth' => [
                Rule::prohibitedIf(fn () => $this->type === ActionType::PING->value),
                'sometimes',
                'nullable',
                'string',
                'in:'.implode(',', AuthType::values()),
            ],
            'auth_username' => [
                Rule::prohibitedIf(fn () => $this->type === ActionType::PING->value),
                'sometimes',
                'nullable',
                'string',
            ],
            'auth_password' => [
                Rule::prohibitedIf(fn () => $this->type === ActionType::PING->value),
                'sometimes',
                'nullable',
                'string',
            ],
            'auth_token' => [
                Rule::prohibitedIf(fn () => $this->type === ActionType::PING->value),
                'sometimes',
                'nullable',
                'string',
            ],
        ];
    }
}
