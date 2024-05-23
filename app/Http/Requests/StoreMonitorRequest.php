<?php

namespace App\Http\Requests;

use App\Rules\IntervalLimit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
            'type' => ['required', 'string', 'in:http'],
            'url' => ['required', 'url:http,https'],
            'method' => ['required', 'string', 'in:GET,POST,PUT,DELETE'],
            'interval' => ['required', 'integer', new IntervalLimit],
        ];
    }
}
