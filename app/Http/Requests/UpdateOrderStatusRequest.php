<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [

            'status' => [

                'required',

                'string',

                'in:pending,approved,supplying,packaging,shipped,out_for_delivery,delivered,completed,cancelled'

            ],

        ];
    }

    /**
     * Custom messages
     */
    public function messages(): array
    {
        return [

            'status.required' => 'Status zorunlu',

            'status.in' => 'Geçersiz status değeri',

        ];
    }
}