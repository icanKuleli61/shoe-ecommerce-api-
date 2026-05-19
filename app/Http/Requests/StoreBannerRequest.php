<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBannerRequest extends FormRequest
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

            'title' => [

                'nullable',

                'string',

                'max:255'
            ],

            'image' => [

                'required',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:5120'
            ],

            'sort_order' => [

                'nullable',

                'integer',

                'min:0'
            ],

            'is_active' => [

                'nullable',

                'boolean'
            ]
        ];
    }

    public function messages(): array
    {
        return [

            'title.string' =>

                'Banner başlığı metin olmalı',

            'title.max' =>

                'Banner başlığı en fazla 255 karakter olabilir',



            'image.required' =>

                'Banner görseli zorunlu',

            'image.image' =>

                'Yüklenen dosya görsel olmalı',

            'image.mimes' =>

                'Sadece jpg, jpeg, png veya webp yükleyebilirsin',

            'image.max' =>

                'Görsel en fazla 5MB olabilir',



            'sort_order.integer' =>

                'Sıralama sayısal olmalı',

            'sort_order.min' =>

                'Sıralama 0’dan küçük olamaz',



            'is_active.boolean' =>

                'Aktiflik alanı true veya false olmalı',
        ];
    }
}