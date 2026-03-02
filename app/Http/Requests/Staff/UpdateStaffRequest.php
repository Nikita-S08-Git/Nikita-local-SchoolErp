<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request for Updating Staff
 */
class UpdateStaffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('staff'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $staffId = $this->route('staff')->id ?? $this->route('staff');
        $userId = $this->route('staff')->user_id ?? null;

        return [
            // Personal Details
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['nullable', 'string', 'regex:/^[6-9]\d{9}$/'],
            'date_of_birth' => ['nullable', 'date', 'before:today', 'after:' . now()->subYears(100)->format('Y-m-d')],
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'blood_group' => ['nullable', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            
            // Employment Details
            'employee_id' => ['required', 'string', 'max:50', Rule::unique('staff', 'employee_id')->ignore($staffId)],
            'department_id' => ['required', 'exists:departments,id'],
            'designation' => ['required', 'string', 'max:100'],
            'employment_type' => ['required', Rule::in(['permanent', 'contract', 'temporary', 'part-time', 'visiting'])],
            'date_of_joining' => ['required', 'date', 'before_or_equal:today'],
            'salary' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            
            // Address
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'regex:/^[0-9]{6}$/'],
            
            // Qualifications
            'qualification' => ['nullable', 'string', 'max:255'],
            'experience' => ['nullable', 'integer', 'min:0', 'max:50'],
            
            // Documents
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            
            // Status
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Staff name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email is already registered.',
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.unique' => 'This employee ID already exists.',
            'department_id.required' => 'Please select a department.',
            'designation.required' => 'Designation is required.',
            'employment_type.required' => 'Employment type is required.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'employee_id' => 'employee ID',
            'department_id' => 'department',
            'employment_type' => 'employment type',
            'date_of_joining' => 'date of joining',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->name ? trim($this->name) : null,
            'email' => $this->email ? strtolower(trim($this->email)) : null,
            'employee_id' => $this->employee_id ? strtoupper(trim($this->employee_id)) : null,
            'designation' => $this->designation ? trim($this->designation) : null,
        ]);
    }
}
