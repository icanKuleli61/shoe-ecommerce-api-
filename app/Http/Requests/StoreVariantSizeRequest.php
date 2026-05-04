<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreVariantSizeRequest extends FormRequest
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
            'variant_id' => ['required','exists:product_variants,id'],
            'size' => ['required','integer'],
            'stock' => ['required','integer','min:0'],
            'price' => ['required','numeric','min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'variant_id.required' => 'Varyant zorunlu',
            'variant_id.exists' => 'Geçersiz varyant',

            'size.required' => 'Numara zorunlu',
            'size.integer' => 'Numara sayı olmalı',

            'stock.required' => 'Stok zorunlu',
            'stock.min' => 'Stok negatif olamaz',

            'price.required' => 'Fiyat zorunlu',
            'price.min' => 'Fiyat negatif olamaz',
        ];
    }
}
