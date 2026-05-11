<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AddBalanceRequest extends FormRequest
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

            'amount' => [

                'required',
                'numeric',
                'min:1'
            ],



            'description' => [

                'required',
                'string',
                'min:3',
                'max:255'
            ],



            'reference_type' => [

                'nullable',
                'string',
                'max:100'
            ],



            'reference_id' => [

                'nullable',
                'integer'
            ]
        ];
    }



    public function messages(): array
    {
        return [

            'amount.required' =>
                'Bakiye miktarı zorunludur.',

            'amount.numeric' =>
                'Bakiye sayısal olmalıdır.',

            'amount.min' =>
                'Bakiye en az 1 TL olmalıdır.',



            'description.required' =>
                'Açıklama zorunludur.',

            'description.min' =>
                'Açıklama en az 3 karakter olmalıdır.',



            'reference_id.integer' =>
                'Reference ID sayı olmalıdır.'
        ];
    }
}
