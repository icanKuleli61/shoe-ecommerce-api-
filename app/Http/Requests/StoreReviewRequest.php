<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
            'product_id' => ['required', 'exists:products,id'],

            'rating' => ['required', 'integer', 'min:1', 'max:5'],

            'comment' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Ürün zorunlu',
            'product_id.exists' => 'Geçersiz ürün',

            'rating.required' => 'Puan zorunlu',
            'rating.integer' => 'Puan sayı olmalı',
            'rating.min' => 'Puan en az 1 olmalı',
            'rating.max' => 'Puan en fazla 5 olmalı',

            'comment.required' => 'Yorum zorunlu',
            'comment.min' => 'Yorum en az 5 karakter olmalı',
            'comment.max' => 'Yorum en fazla 500 karakter olmalı',
        ];
    }
}
