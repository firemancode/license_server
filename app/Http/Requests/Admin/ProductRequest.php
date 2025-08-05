<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // You can add authorization logic here
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')->ignore($productId)
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('products', 'slug')->ignore($productId)
            ],
            'description' => 'nullable|string|max:1000',
            'version' => 'nullable|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'product name',
            'slug' => 'product slug',
            'description' => 'product description',
            'version' => 'product version',
            'price' => 'product price',
            'is_active' => 'active status',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'A product with this name already exists.',
            'slug.unique' => 'A product with this slug already exists.',
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, and dashes.',
            'price.numeric' => 'The price must be a valid number.',
            'price.min' => 'The price cannot be negative.',
        ];
    }
}