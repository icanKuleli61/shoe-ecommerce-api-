<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FilterProductRequest extends FormRequest
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

            'search' => [
                'nullable',
                'string',
                'max:100'
            ],

            'category_id' => [
                'nullable',
                'string'
            ],

            'brand_id' => [
                'nullable',
                'string'
            ],

            'gender' => [
                'nullable',
                'string'
            ],

            'color_id' => [
                'nullable',
                'string'
            ],

            'size' => [
                'nullable',
                'string'
            ],

            'min_price' => [
                'nullable',
                'numeric',
                'min:0'
            ],

            'max_price' => [
                'nullable',
                'numeric',
                'min:0'
            ],

            'sort' => [

                'nullable',

                'in:price_asc,price_desc,newest,most_reviewed,top_rated'
            ],
        ];
    }

    public function messages(): array
    {
        return [

            'search.max' =>
                'Arama en fazla 100 karakter olabilir',

            'min_price.numeric' =>
                'Minimum fiyat sayı olmalı',

            'min_price.min' =>
                'Minimum fiyat 0 dan küçük olamaz',

            'max_price.numeric' =>
                'Maximum fiyat sayı olmalı',

            'max_price.min' =>
                'Maximum fiyat 0 dan küçük olamaz',

            'sort.in' =>
                'Geçersiz sıralama tipi',
        ];
    }
}