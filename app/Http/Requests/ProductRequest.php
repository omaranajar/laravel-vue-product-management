<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
            'price' => 'required|numeric|between:0.01,999999.99',
            'size' => 'nullable|string|max:50',
            'type' => [
                'required',
                'string',
                Rule::in(['electronics', 'clothing', 'furniture', 'appliances', 'books', 'toys', 'footwear']),
            ],
        ];
    }
}