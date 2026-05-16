<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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

            'first_name' => [
                'required',
                'string',
                'max:50'
            ],

            'last_name' => [
                'required',
                'string',
                'max:50'
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email'
            ],

            'gender' => [
                'nullable',
                'in:male,female'
            ],

            'password' => [
                'required',
                'string',
                'min:6'
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20'
            ],
            'city_id' => [
                'required',
                'integer',
                'exists:cities,id'
            ],

            'district_id' => [
                'required',
                'integer',
                'exists:districts,id'
            ],

            'neighborhood_id' => [
                'required',
                'integer',
                'exists:neighborhoods,id'
            ],

            'address' => [
                'required',
                'string',
                'min:10'
            ],
            'title' => [
                'required',
                'string',
                'max:50'
            ],

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
        ];
    }

    public function messages(): array
    {
        return [

            'first_name.required' => 'Ad zorunludur.',
            'first_name.string' => 'Ad metin olmalıdır.',
            'first_name.max' => 'Ad en fazla 50 karakter olabilir.',

            'last_name.required' => 'Soyad zorunludur.',
            'last_name.string' => 'Soyad metin olmalıdır.',
            'last_name.max' => 'Soyad en fazla 50 karakter olabilir.',

            'email.required' => 'Email zorunludur.',
            'email.email' => 'Geçerli bir email giriniz.',
            'email.unique' => 'Bu email zaten kayıtlı.',

            'gender.in' => 'Geçersiz cinsiyet seçimi.',

            'password.required' => 'Şifre zorunludur.',
            'password.string' => 'Şifre metin olmalıdır.',
            'password.min' => 'Şifre en az 6 karakter olmalıdır.',

            'phone.string' => 'Telefon metin olmalıdır.',
            'phone.max' => 'Telefon en fazla 20 karakter olabilir.',
            'city_id.required' =>
                'Şehir zorunludur.',

            'city_id.exists' =>
                'Geçersiz şehir.',



            'district_id.required' =>
                'İlçe zorunludur.',

            'district_id.exists' =>
                'Geçersiz ilçe.',



            'neighborhood_id.required' =>
                'Mahalle zorunludur.',

            'neighborhood_id.exists' =>
                'Geçersiz mahalle.',



            'address.required' =>
                'Adres zorunludur.',

            'address.min' =>
                'Adres en az 10 karakter olmalıdır.',

            'title.required' =>
                'Adres başlığı zorunludur.',

            'title.max' =>
                'Adres başlığı en fazla 50 karakter olabilir.',

            'full_name.max' =>

                'Teslim alan kişi adı en fazla 100 karakter olabilir.',

            'phone_override.max' =>

                'Teslimat telefonu en fazla 20 karakter olabilir.',
        ];
    }
}
