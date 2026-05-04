<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductImageRequest extends FormRequest
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
            'variant_id' => ['required', 'exists:product_variants,id'],

            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'is_main' => ['sometimes', 'boolean'],

            'order' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'variant_id.required' => 'Varyant zorunlu',
            'variant_id.exists' => 'Geçersiz varyant',

            'image.required' => 'Resim zorunlu',
            'image.image' => 'Geçerli bir resim yükleyin',
            'image.mimes' => 'Sadece jpg, png, webp yükleyebilirsiniz',
            'image.max' => 'Resim max 2MB olabilir',

            'is_main.boolean' => 'Ana foto alanı true/false olmalı',

            'order.integer' => 'Sıra sayı olmalı',
            'order.min' => 'Sıra 0’dan küçük olamaz',
        ];
    }
}
