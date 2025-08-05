<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LicenseRequest extends FormRequest
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
        $licenseId = $this->route('license')?->id;

        return [
            'user_id' => [
                'required',
                'integer',
                'exists:users,id'
            ],
            'product_id' => [
                'required',
                'integer',
                'exists:products,id'
            ],
            'license_key' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Z0-9-]+$/',
                Rule::unique('licenses', 'license_key')->ignore($licenseId)
            ],
            'status' => [
                'required',
                'string',
                Rule::in(['active', 'expired', 'disabled', 'suspended'])
            ],
            'expires_at' => [
                'nullable',
                'date',
                'after:today'
            ],
            'max_activations' => 'nullable|integer|min:1|max:100',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'user',
            'product_id' => 'product',
            'license_key' => 'license key',
            'status' => 'license status',
            'expires_at' => 'expiration date',
            'max_activations' => 'maximum activations',
            'notes' => 'notes',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.exists' => 'The selected user does not exist.',
            'product_id.exists' => 'The selected product does not exist.',
            'license_key.unique' => 'This license key already exists.',
            'license_key.regex' => 'The license key may only contain uppercase letters, numbers, and dashes.',
            'status.in' => 'The status must be one of: active, expired, disabled, suspended.',
            'expires_at.after' => 'The expiration date must be in the future.',
            'max_activations.min' => 'Maximum activations must be at least 1.',
            'max_activations.max' => 'Maximum activations cannot exceed 100.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert license key to uppercase
        if ($this->has('license_key') && $this->license_key) {
            $this->merge([
                'license_key' => strtoupper($this->license_key)
            ]);
        }
    }
}