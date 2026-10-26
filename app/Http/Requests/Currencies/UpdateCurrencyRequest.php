<?php

namespace App\Http\Requests\Currencies;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCurrencyRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'symbol' => ['sometimes', 'required', 'string', 'max:10'],
            'exchange_rate' => ['sometimes', 'required', 'numeric', 'gt:0'],
        ];
    }
}
