<?php

namespace App\Http\Requests\Academic;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request for Creating/Updating Divisions
 */
class StoreDivisionRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'division_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('divisions')->where(function ($query) {
                    return $query->where('academic_year_id', $this->academic_year_id);
                }),
            ],
            'max_students' => ['required', 'integer', 'min:1', 'max:200'],
            'academic_year_id' => ['required', 'exists:academic_sessions,id'],
            'class_teacher_id' => [
                'nullable',
                'exists:users,id',
                Rule::unique('divisions', 'class_teacher_id')->where(function ($query) {
                    return $query->where('is_active', true);
                }),
            ],
            'classroom' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'division_name.required' => 'Division name is required.',
            'division_name.unique' => 'This division name already exists for the selected academic session.',
            'max_students.required' => 'Capacity is required.',
            'max_students.min' => 'Capacity must be at least 1.',
            'max_students.max' => 'Capacity cannot exceed 200.',
            'academic_year_id.required' => 'Please select an academic session.',
            'class_teacher_id.unique' => 'This teacher is already assigned to another active division.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'division_name' => 'division name',
            'max_students' => 'capacity',
            'academic_year_id' => 'academic session',
            'class_teacher_id' => 'class teacher',
            'classroom' => 'classroom',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'division_name' => $this->division_name ? strtoupper(trim($this->division_name)) : null,
            'classroom' => $this->classroom ? trim($this->classroom) : null,
        ]);
    }
}
