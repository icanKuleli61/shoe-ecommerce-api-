<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AdminOrderFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'search' => [

                'nullable',

                'string',

                'max:255'
            ],

            'status' => [

                'nullable',

                'string',

                'in:pending,approved,supplying,packaging,shipped,out_for_delivery,delivered,completed,cancelled'
            ],
        ];
    }

    public function messages(): array
    {
        return [

            'search.string' =>

                'Arama değeri metin olmalıdır.',

            'search.max' =>

                'Arama değeri en fazla 255 karakter olabilir.',

            'status.string' =>

                'Durum değeri metin olmalıdır.',

            'status.in' =>

                'Geçersiz sipariş durumu.',
        ];
    }
}