<?php

namespace App\Http\Requests;

use App\Rules\IntervalLimit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMonitorRequest extends FormRequest
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
            'method' => ['string', 'in:GET,POST,PUT,DELETE'],
            'interval' => ['integer', 'in:1,5', new IntervalLimit],
        ];
    }
}
