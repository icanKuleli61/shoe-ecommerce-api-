<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
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

            'name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'unique:brands,name'
            ]
        ];
    }



    public function messages(): array
    {
        return [

            'name.required' =>
                'Marka adı zorunlu',

            'name.min' =>
                'Marka adı en az 2 karakter olmalı',

            'name.max' =>
                'Marka adı max 50 karakter olabilir',

            'name.unique' =>
                'Bu marka zaten mevcut',
        ];
    }
}