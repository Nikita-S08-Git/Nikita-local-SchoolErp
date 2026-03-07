<?php

namespace App\Http\Requests\Library;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request for Issuing Books
 */
class IssueBookRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'book_id' => ['required', 'exists:books,id'],
            'student_id' => ['required', 'exists:students,id'],
            'issue_date' => ['required', 'date', 'before_or_equal:today'],
            'due_date' => ['required', 'date', 'after:issue_date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'book_id.required' => 'Please select a book to issue',
            'book_id.exists' => 'The selected book does not exist',
            'student_id.required' => 'Please select a student',
            'student_id.exists' => 'The selected student does not exist',
            'issue_date.required' => 'Issue date is required',
            'issue_date.date' => 'Invalid issue date format',
            'issue_date.before_or_equal' => 'Issue date cannot be in the future',
            'due_date.required' => 'Due date is required',
            'due_date.date' => 'Invalid due date format',
            'due_date.after' => 'Due date must be after issue date',
            'notes.max' => 'Notes cannot exceed 500 characters',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'book_id' => 'book',
            'student_id' => 'student',
            'issue_date' => 'issue date',
            'due_date' => 'due date',
        ];
    }
}
