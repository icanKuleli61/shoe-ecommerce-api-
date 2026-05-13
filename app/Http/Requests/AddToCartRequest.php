<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
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
    public function rules(): array{
        return [
            'variant_id' => ['required', 'exists:product_variants,id'],

            'size_id' => ['required', 'exists:variant_sizes,id'],

            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array{
        return [
            'variant_id.required' => 'Varyant zorunlu',
            'variant_id.exists' => 'Geçersiz varyant',

            'size_id.required' => 'Beden zorunlu',
            'size_id.exists' => 'Geçersiz beden',

            'quantity.required' => 'Adet zorunlu',
            'quantity.integer' => 'Adet sayı olmalı',
            'quantity.min' => 'En az 1 olmalı',
        ];
    }
}
