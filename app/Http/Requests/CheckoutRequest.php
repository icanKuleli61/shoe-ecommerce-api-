<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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

            'address_id' => [

                'required',

                'exists:addresses,id'
            ],

            'payment_method' => [

                'required',

                'in:wallet,card'
            ]
        ];
    }



    public function messages(): array
    {
        return [

            'address_id.required' =>

                'Adres seçmelisiniz',

            'address_id.exists' =>

                'Adres bulunamadı',

            'payment_method.required' =>

                'Ödeme yöntemi seçmelisiniz',

            'payment_method.in' =>

                'Geçersiz ödeme yöntemi'
        ];
    }
}