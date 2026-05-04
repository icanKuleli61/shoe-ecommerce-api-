<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => ['sometimes','string','min:3'],
            'description' => ['sometimes','string'],

            'category_id' => ['sometimes','exists:categories,id'],
            'brand_id' => ['sometimes','exists:brands,id'],

            'gender' => ['sometimes','in:male,female,unisex,kids'],

            'active' => ['sometimes','boolean'],

            'discount_rate' => ['sometimes','numeric','min:0','max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.min' => 'Ürün adı en az 3 karakter olmalı',

            'category_id.exists' => 'Geçersiz kategori seçildi',
            'brand_id.exists' => 'Geçersiz marka seçildi',

            'gender.in' => 'Geçersiz cinsiyet seçimi',

            'active.boolean' => 'Aktif alanı true/false olmalı',

            'discount_rate.numeric' => 'İndirim sayı olmalı',
            'discount_rate.min' => 'İndirim negatif olamaz',
            'discount_rate.max' => 'İndirim 100’den büyük olamaz',
        ];
    }
}
