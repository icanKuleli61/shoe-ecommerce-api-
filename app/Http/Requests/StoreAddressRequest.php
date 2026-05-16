<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'neighborhood_id' => ['required', 'integer', 'exists:neighborhoods,id'],

            'address' => ['required', 'string', 'min:10'],

            'title' => ['nullable', 'string', 'min:2', 'max:50'],
            'full_name' => [

                'nullable',

                'string',

                'max:100'
            ],

            'phone_override' => [

                'nullable',

                'string',

                'max:20'
            ],
            'is_default' => ['boolean']
        ];
    }

    public function messages(): array
    {
        return [
            'city_id.integer' => 'Şehir ID sayı olmalı',
            'city_id.exists' => 'Geçersiz şehir',

            'district_id.integer' => 'İlçe ID sayı olmalı',
            'district_id.exists' => 'Geçersiz ilçe',

            'neighborhood_id.integer' => 'Mahalle ID sayı olmalı',
            'neighborhood_id.exists' => 'Geçersiz mahalle',

            'address.min' => 'Adres en az 10 karakter olmalı',

            'title.min' => 'Başlık en az 2 karakter olmalı',
            'title.max' => 'Başlık en fazla 50 karakter olabilir',

            'full_name.max' =>

                'Teslim alan kişi adı en fazla 100 karakter olabilir.',

            'phone_override.max' =>

                'Teslimat telefonu en fazla 20 karakter olabilir.',
        ];
    }
}