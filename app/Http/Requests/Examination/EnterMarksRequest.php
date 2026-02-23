<?php

namespace App\Http\Requests\Examination;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request for Entering Marks
 */
class EnterMarksRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('enter marks', \App\Models\Result\Exam::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'exam_id' => ['required', 'exists:exams,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'marks_data' => ['required', 'array', 'min:1'],
            'marks_data.*.student_id' => ['required', 'exists:students,id'],
            'marks_data.*.marks_obtained' => ['required', 'numeric', 'min:0', 'max:100'],
            'marks_data.*.total_marks' => ['required', 'numeric', 'min:1', 'max:100'],
            'marks_data.*.passing_marks' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'exam_id.required' => 'Please select an examination.',
            'subject_id.required' => 'Please select a subject.',
            'marks_data.required' => 'At least one student mark is required.',
            'marks_data.*.marks_obtained.required' => 'Marks obtained is required.',
            'marks_data.*.marks_obtained.min' => 'Marks cannot be negative.',
            'marks_data.*.marks_obtained.max' => 'Marks cannot exceed 100.',
            'marks_data.*.total_marks.required' => 'Total marks is required.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'exam_id' => 'examination',
            'subject_id' => 'subject',
            'marks_data' => 'marks',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach ($this->marks_data as $index => $markData) {
                if (isset($markData['marks_obtained']) && isset($markData['total_marks'])) {
                    if ($markData['marks_obtained'] > $markData['total_marks']) {
                        $validator->errors()->add(
                            "marks_data.{$index}.marks_obtained",
                            "Marks obtained cannot exceed total marks for student ID {$markData['student_id']}."
                        );
                    }
                }
            }
        });
    }
}
