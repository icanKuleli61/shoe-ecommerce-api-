<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
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
            'city_id' => ['sometimes','integer','exists:cities,id'],
            'district_id' => ['sometimes','integer','exists:districts,id'],
            'neighborhood_id' => ['sometimes','integer','exists:neighborhoods,id'],

            'address' => ['sometimes','string','min:10'],

            'title' => ['nullable','string','min:2','max:50'],

            'is_default' => ['sometimes','boolean']
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $hasCity = $this->has('city_id');
            $hasDistrict = $this->has('district_id');
            $hasNeighborhood = $this->has('neighborhood_id');

            if ($hasCity || $hasDistrict || $hasNeighborhood) {
                if (!($hasCity && $hasDistrict && $hasNeighborhood)) {
                    $validator->errors()->add(
                        'location',
                        'Şehir, ilçe ve mahalle birlikte gönderilmelidir'
                    );
                }
            }
        });
    }

    public function messages() :array{

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
        ];
    }
}
