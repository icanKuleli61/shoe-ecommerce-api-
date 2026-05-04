<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVariantSizeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'stock' => ['sometimes','integer','min:0'],
            'price' => ['sometimes','numeric','min:0'],
        ];     
    }

    public function messages(): array
    {
        return [
            'stock.integer' => 'Stok sayı olmalı',
            'stock.min' => 'Stok 0 veya daha büyük olmalı',

            'price.numeric' => 'Fiyat sayı olmalı',
            'price.min' => 'Fiyat negatif olamaz',
        ];
    }
}
