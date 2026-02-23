<?php

namespace App\Http\Requests\Leave;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request for Leave Approval/Rejection
 */
class ApproveLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('approve leave', \App\Models\Leave::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['approved', 'rejected', 'pending'])],
            'remarks' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Approval status is required.',
            'status.in' => 'Status must be approved, rejected, or pending.',
            'remarks.max' => 'Remarks cannot exceed 500 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'status' => 'status',
            'remarks' => 'remarks',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $leave = $this->route('leave');

            if ($leave && $leave->status !== 'pending') {
                $validator->errors()->add('status', 'This leave application has already been ' . $leave->status . '.');
            }

            if ($this->status === 'rejected' && empty($this->remarks)) {
                $validator->errors()->add('remarks', 'Please provide a reason for rejection.');
            }
        });
    }
}
