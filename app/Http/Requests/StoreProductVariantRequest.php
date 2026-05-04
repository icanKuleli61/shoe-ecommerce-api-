<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductVariantRequest extends FormRequest
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
            'product_id' => ['required','exists:products,id'],
            'color_id' => ['required','exists:colors,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.exists' => 'Geçersiz ürün',
            'color_id.exists' => 'Geçersiz renk',
        ];
    }
}
