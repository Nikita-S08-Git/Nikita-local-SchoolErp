<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Base Form Request for all validation classes
 * 
 * Provides common functionality for all Form Requests:
 * - Standardized error response format
 * - Common authorization logic
 * - Reusable validation rules
 */
abstract class BaseFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Override in child classes if needed.
     */
    public function authorize(): bool
    {
        // By default, allow all authenticated users
        // Override in specific requests for fine-grained control
        return $this->user() !== null;
    }

    /**
     * Get custom error messages for validator errors.
     * Override in child classes for custom messages.
     */
    public function messages(): array
    {
        return [
            'required' => 'The :attribute field is required.',
            'email' => 'The :attribute must be a valid email address.',
            'exists' => 'The selected :attribute is invalid.',
            'numeric' => 'The :attribute must be a number.',
            'integer' => 'The :attribute must be an integer.',
            'min' => 'The :attribute must be at least :min.',
            'max' => 'The :attribute may not be greater than :max.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Override in child classes for better attribute names.
     */
    public function attributes(): array
    {
        return [];
    }

    /**
     * Handle a failed validation attempt.
     * Returns standardized JSON error response for API requests.
     */
    protected function failedValidation(Validator $validator)
    {
        // If this is an API request, return JSON response
        if ($this->expectsJson()) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422));
        }

        // For web requests, use default Laravel behavior
        parent::failedValidation($validator);
    }

    /**
     * Common validation rules that can be reused across requests.
     */
    public static function commonRules(): array
    {
        return [
            'email' => 'required|email|max:255',
            'password' => 'required|min:8',
            'name' => 'required|string|max:255',
            'status' => 'nullable|boolean',
            'date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
