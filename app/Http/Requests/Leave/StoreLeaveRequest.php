<?php

namespace App\Http\Requests\Leave;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request for Leave Applications
 */
class StoreLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('apply for leave', \App\Models\Leave::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'leave_type' => ['required', Rule::in(['sick', 'casual', 'earned', 'maternity', 'paternity', 'unpaid'])],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:1000'],
            'contact_number' => ['nullable', 'string', 'regex:/^[6-9]\d{9}$/'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'leave_type.required' => 'Leave type is required.',
            'leave_type.in' => 'Invalid leave type selected.',
            'start_date.required' => 'Start date is required.',
            'start_date.after_or_equal' => 'Start date cannot be in the past.',
            'end_date.required' => 'End date is required.',
            'end_date.after_or_equal' => 'End date must be equal to or after start date.',
            'reason.required' => 'Reason for leave is required.',
            'contact_number.regex' => 'Please enter a valid 10-digit mobile number.',
            'attachment.mimes' => 'Attachment must be a PDF or image file.',
            'attachment.max' => 'Attachment size must not exceed 2MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'leave_type' => 'leave type',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'reason' => 'reason',
            'contact_number' => 'contact number',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Calculate total leave days
            $startDate = \Carbon\Carbon::parse($this->start_date);
            $endDate = \Carbon\Carbon::parse($this->end_date);
            $totalDays = $startDate->diffInDays($endDate) + 1;

            // Check leave balance for certain types
            if ($this->leave_type === 'casual' && $totalDays > 3) {
                $validator->errors()->add('end_date', 'Casual leave cannot exceed 3 days at a time.');
            }

            if ($totalDays > 90) {
                $validator->errors()->add('end_date', 'Leave duration cannot exceed 90 days.');
            }
        });
    }
}
