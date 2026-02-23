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
     */
    public function rules(): array
    {
        return [
            'book_id' => ['required', 'exists:books,id'],
            'student_id' => ['required', 'exists:students,id'],
            'issue_date' => ['required', 'date', 'before_or_equal:today'],
            'due_date' => ['required', 'date', 'after:issue_date'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'book_id.required' => 'Please select a book.',
            'book_id.exists' => 'Selected book does not exist.',
            'student_id.required' => 'Please select a student.',
            'student_id.exists' => 'Selected student does not exist.',
            'issue_date.required' => 'Issue date is required.',
            'issue_date.before_or_equal' => 'Issue date cannot be in the future.',
            'due_date.required' => 'Due date is required.',
            'due_date.after' => 'Due date must be after the issue date.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
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

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $book = \App\Models\Library\Book::find($this->book_id);

            if ($book && $book->available_copies <= 0) {
                $validator->errors()->add('book_id', 'No copies of this book are available for issue.');
            }

            // Check if student already has this book issued
            $existingIssue = \App\Models\Library\BookIssue::where('book_id', $this->book_id)
                ->where('student_id', $this->student_id)
                ->where('status', 'issued')
                ->exists();

            if ($existingIssue) {
                $validator->errors()->add('book_id', 'This student already has this book issued.');
            }

            // Check if student has reached maximum issue limit (optional - typically 3-5 books)
            $maxBooks = config('library.max_books_per_student', 5);
            $issuedCount = \App\Models\Library\BookIssue::where('student_id', $this->student_id)
                ->where('status', 'issued')
                ->count();

            if ($issuedCount >= $maxBooks) {
                $validator->errors()->add('student_id', "Student has already issued the maximum number of books ({$maxBooks}).");
            }
        });
    }
}
