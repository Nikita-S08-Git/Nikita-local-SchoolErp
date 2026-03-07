<?php

namespace App\Http\Requests\Academic;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request for Updating Divisions
 */
class UpdateDivisionRequest extends FormRequest
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
        $divisionId = $this->route('division')->id ?? $this->route('division');

        return [
            'division_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('divisions')->where(function ($query) {
                    return $query->where('academic_year_id', $this->academic_year_id);
                })->ignore($divisionId),
            ],
            'max_students' => ['required', 'integer', 'min:1', 'max:200'],
            'academic_year_id' => ['required', 'exists:academic_sessions,id'],
            'class_teacher_id' => [
                'required',
                'exists:users,id',
                Rule::unique('divisions', 'class_teacher_id')->ignore($divisionId),
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
            'class_teacher_id.required' => 'Please assign a class teacher.',
            'class_teacher_id.unique' => 'This teacher is already assigned to another division.',
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
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $division = $this->route('division');

            // Check if reducing capacity below current student count
            if ($division && $this->max_students) {
                $currentStudentCount = $division->students()->where('student_status', 'active')->count();

                if ($this->max_students < $currentStudentCount) {
                    $validator->errors()->add('max_students', "Capacity cannot be less than current student count ({$currentStudentCount}).");
                }
            }
        });
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
