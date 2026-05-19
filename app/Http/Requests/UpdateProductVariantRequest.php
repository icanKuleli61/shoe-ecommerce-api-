<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductVariantRequest
    extends FormRequest
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

                'min:2',

                'max:100'
            ],

            'color_id' => [

                'required',

                'integer',

                'exists:colors,id'
            ],
        ];
    }

    public function messages(): array
    {
        return [

            'name.required' =>

                'Varyant adı zorunlu',

            'name.string' =>

                'Varyant adı metin olmalı',

            'name.min' =>

                'Varyant adı en az 2 karakter olmalı',

            'name.max' =>

                'Varyant adı en fazla 100 karakter olabilir',

            'color_id.required' =>

                'Renk seçmelisin',

            'color_id.integer' =>

                'Geçersiz renk',

            'color_id.exists' =>

                'Seçilen renk bulunamadı',
        ];
    }
}