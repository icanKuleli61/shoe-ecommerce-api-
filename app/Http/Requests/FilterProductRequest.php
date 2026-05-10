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

            'search' => ['nullable', 'string', 'max:100'],

            'category_id' => ['nullable', 'integer', 'exists:categories,id'],

            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],

            'gender' => ['nullable', 'in:male,female,unisex'],

            'color_id' => ['nullable', 'integer', 'exists:colors,id'],

            'min_price' => ['nullable', 'numeric', 'min:0'],

            'max_price' => ['nullable', 'numeric', 'min:0'],

            'top_rated' => ['nullable', 'boolean'],

            'most_reviewed' => ['nullable', 'boolean'],

            'newest' => ['nullable', 'boolean'],
        ];
    }

    
}
