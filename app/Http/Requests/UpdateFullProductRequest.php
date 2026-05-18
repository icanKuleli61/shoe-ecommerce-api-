<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFullProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'name' => [

                'required',

                'string',

                'min:3',

                'max:255'
            ],

            'description' => [

                'nullable',

                'string'
            ],

            'category_id' => [

                'required',

                'exists:categories,id'
            ],

            'brand_id' => [

                'required',

                'exists:brands,id'
            ],

            'gender' => [

                'required',

                'in:male,female,unisex,kids'
            ],

            'variants' => [

                'required',

                'array',

                'min:1'
            ],

            'variants.*.id' => [

                'nullable',

                'exists:product_variants,id'
            ],

            'variants.*.name' => [

                'required',

                'string',

                'min:2',

                'max:100'
            ],

            'variants.*.color_id' => [

                'required',

                'exists:colors,id',

                'distinct'
            ],

            'variants.*.images' => [

                'nullable',

                'array'
            ],

            'variants.*.images.*' => [

                'array'
            ],

            'variants.*.images.*.id' => [

                'nullable',

                'exists:product_images,id'
            ],

            'variants.*.images.*.file' => [

                'nullable',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:2048'
            ],

            'variants.*.images.*.is_main' => [

                'nullable',

                'boolean'
            ],

            'variants.*.images.*.order' => [

                'nullable',

                'integer'
            ],

            'variants.*.sizes' => [

                'required',

                'array',

                'min:1'
            ],

            'variants.*.sizes.*.id' => [

                'nullable',

                'exists:variant_sizes,id'
            ],

            'variants.*.sizes.*.size' => [

                'required',

                'integer'
            ],

            'variants.*.sizes.*.stock' => [

                'required',

                'integer',

                'min:0'
            ],

            'variants.*.sizes.*.price' => [

                'required',

                'numeric',

                'min:0'
            ],
        ];
    }



    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            foreach ($this->variants as $variantIndex => $variant) {

                $sizes = collect(

                    $variant['sizes']

                )->pluck('size');



                if (

                    $sizes->count()

                    !==

                    $sizes->unique()->count()

                ) {

                    $validator->errors()->add(

                        "variants.$variantIndex.sizes",

                        "Aynı varyant içinde aynı numara tekrar edemez"
                    );
                }
            }
        });
    }



    public function messages(): array
    {
        return [

            'name.required' =>

                'Ürün adı zorunlu',

            'name.min' =>

                'Ürün adı en az 3 karakter olmalı',

            'category_id.required' =>

                'Kategori seçmelisin',

            'category_id.exists' =>

                'Geçersiz kategori',

            'brand_id.required' =>

                'Marka seçmelisin',

            'brand_id.exists' =>

                'Geçersiz marka',

            'gender.required' =>

                'Cinsiyet seçmelisin',

            'gender.in' =>

                'Geçersiz cinsiyet',

            'variants.required' =>

                'En az 1 varyant gerekli',

            'variants.array' =>

                'Varyantlar liste olmalı',

            'variants.min' =>

                'En az 1 varyant gerekli',

            'variants.*.name.required' =>

                'Varyant adı zorunlu',

            'variants.*.name.min' =>

                'Varyant adı en az 2 karakter olmalı',

            'variants.*.color_id.required' =>

                'Varyant rengi zorunlu',

            'variants.*.color_id.exists' =>

                'Geçersiz renk',

            'variants.*.color_id.distinct' =>

                'Aynı renk 2 kere eklenemez',

            'variants.*.images.*.file.image' =>

                'Dosya resim olmalı',

            'variants.*.images.*.file.mimes' =>

                'Sadece jpg, jpeg, png, webp yüklenebilir',

            'variants.*.images.*.file.max' =>

                'Resim max 2MB olabilir',

            'variants.*.sizes.required' =>

                'Her varyantta en az 1 numara olmalı',

            'variants.*.sizes.min' =>

                'Her varyantta en az 1 numara olmalı',

            'variants.*.sizes.*.size.required' =>

                'Numara gerekli',

            'variants.*.sizes.*.size.integer' =>

                'Numara sayı olmalı',

            'variants.*.sizes.*.stock.required' =>

                'Stok gerekli',

            'variants.*.sizes.*.stock.integer' =>

                'Stok sayı olmalı',

            'variants.*.sizes.*.stock.min' =>

                'Stok negatif olamaz',

            'variants.*.sizes.*.price.required' =>

                'Fiyat gerekli',

            'variants.*.sizes.*.price.numeric' =>

                'Fiyat sayı olmalı',

            'variants.*.sizes.*.price.min' =>

                'Fiyat negatif olamaz',
        ];
    }
}