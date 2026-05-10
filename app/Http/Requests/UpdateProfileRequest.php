<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
                'sometimes',
                'string',
                'max:50'
            ],

            'last_name' => [
                'sometimes',
                'string',
                'max:50'
            ],

            'email' => [
                'sometimes',
                'email',
                'unique:users,email,' . auth()->id()
            ],

            'phone' => [
                'sometimes',
                'string',
                'max:20'
            ],

            'gender' => [
                'sometimes',
                'in:male,female'
            ],
        ];
    }

    public function messages(): array
    {
        return [

            'first_name.string' => 'Ad metin olmalıdır.',
            'first_name.max' => 'Ad en fazla 50 karakter olabilir.',

            'last_name.string' => 'Soyad metin olmalıdır.',
            'last_name.max' => 'Soyad en fazla 50 karakter olabilir.',

            'email.email' => 'Geçerli bir email giriniz.',
            'email.unique' => 'Bu email zaten kullanılıyor.',

            'phone.string' => 'Telefon metin olmalıdır.',
            'phone.max' => 'Telefon en fazla 20 karakter olabilir.',

            'gender.in' => 'Geçersiz cinsiyet seçimi.',
        ];
    }
}
