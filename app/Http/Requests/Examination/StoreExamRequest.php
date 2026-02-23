<?php

namespace App\Http\Requests\Examination;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request for Creating Examinations
 */
class StoreExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Result\Exam::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:exams,code'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Examination name is required.',
            'code.required' => 'Examination code is required.',
            'code.unique' => 'This examination code already exists.',
            'start_date.required' => 'Start date is required.',
            'end_date.required' => 'End date is required.',
            'end_date.after_or_equal' => 'End date must be equal to or after start date.',
            'academic_session_id.required' => 'Please select an academic session.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'examination name',
            'code' => 'examination code',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'academic_session_id' => 'academic session',
            'division_id' => 'division',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->name ? trim($this->name) : null,
            'code' => $this->code ? strtoupper(trim($this->code)) : null,
        ]);
    }
}
