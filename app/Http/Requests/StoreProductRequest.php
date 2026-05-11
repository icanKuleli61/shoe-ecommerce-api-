<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'name' => ['required', 'string', 'min:3'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['required', 'exists:brands,id'],
            'gender' => ['required', 'in:male,female,unisex,kids'],

            'variants' => ['required', 'array', 'min:1'],

            'variants.*.color_id' => ['required', 'distinct', 'exists:colors,id'],

            'variants.*.sizes' => ['required', 'array', 'min:1'],
            'variants.*.name' => [
                'required',
                'string'
            ],

            'variants.*.images' => [
                'required',
                'array',
                'min:1'
            ],



            'variants.*.images.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ],

            'variants.*.sizes.*.size' => ['required', 'integer'],
            'variants.*.sizes.*.stock' => ['required', 'integer', 'min:0'],
            'variants.*.sizes.*.price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [

            'name.required' => 'Ürün adı zorunlu',
            'name.min' => 'Ürün adı en az 3 karakter olmalı',

            'category_id.required' => 'Kategori seçmelisin',
            'category_id.exists' => 'Geçersiz kategori',

            'brand_id.required' => 'Marka seçmelisin',
            'brand_id.exists' => 'Geçersiz marka',

            'gender.required' => 'Cinsiyet seçmelisin',

            'variants.required' => 'En az 1 varyant eklemelisin',
            'variants.array' => 'Varyantlar liste olmalı',

            'variants.*.color_id.required' => 'Renk seçmelisin',
            'variants.*.color_id.exists' => 'Geçersiz renk',
            'variants.*.color_id.distinct' => 'Aynı renk 2 kere eklenemez',

            'variants.*.sizes.required' => 'Her varyantta size olmalı',

            'variants.*.sizes.*.size.required' => 'Numara gerekli',
            'variants.*.sizes.*.size.integer' => 'Numara sayı olmalı',

            'variants.*.sizes.*.stock.required' => 'Stok gerekli',
            'variants.*.sizes.*.stock.min' => 'Stok negatif olamaz',
            'variants.*.name.required' =>
                'Varyant adı gerekli',

            'variants.*.images.required' =>
                'Her varyantta fotoğraf olmalı',

            'variants.*.images.min' =>
                'En az 1 fotoğraf eklemelisin',

            'variants.*.images.*.image' =>
                'Dosya resim olmalı',

            'variants.*.images.*.mimes' =>
                'Sadece jpg, png, webp yükleyebilirsin',

            'variants.*.images.*.max' =>
                'Fotoğraf max 2MB olabilir',

            'variants.*.sizes.*.price.required' => 'Fiyat gerekli',
            'variants.*.sizes.*.price.min' => 'Fiyat negatif olamaz',
        ];
    }
}