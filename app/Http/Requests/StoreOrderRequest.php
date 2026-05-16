<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }


    public function rules(): array
    {
        return [

            'address_id' => [

                'required',

                'integer',

                'exists:addresses,id'
            ],

            'payment_method' => [

                'required',

                'string',

                'in:card,wallet'
            ],
        ];
    }


    public function messages(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | ADDRESS
            |--------------------------------------------------------------------------
            */

            'address_id.required' =>

                'Adres seçmelisiniz.',

            'address_id.integer' =>

                'Geçersiz adres.',

            'address_id.exists' =>

                'Adres bulunamadı.',


            /*
            |--------------------------------------------------------------------------
            | PAYMENT
            |--------------------------------------------------------------------------
            */

            'payment_method.required' =>

                'Ödeme yöntemi seçmelisiniz.',

            'payment_method.in' =>

                'Geçersiz ödeme yöntemi.',
        ];
    }
}