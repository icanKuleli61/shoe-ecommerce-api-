<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
    return [
            'full_name' => ['required', 'string', 'min:3'],
            'phone' => ['required', 'string'],
            'city' => ['required', 'string'],
            'district' => ['required', 'string'],
            'neighborhood' => ['required', 'string'],
            'address_text' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Ad soyad zorunlu',
            'phone.required' => 'Telefon zorunlu',
            'city.required' => 'Şehir zorunlu',
            'district.required' => 'İlçe zorunlu',
            'neighborhood.required' => 'Mahalle zorunlu',
            'address_text.required' => 'Adres zorunlu',
        ];
    }
}
