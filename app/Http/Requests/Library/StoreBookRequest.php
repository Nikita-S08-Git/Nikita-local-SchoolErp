<?php

namespace App\Http\Requests\Library;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request for Creating/Updating Library Books
 */
class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Library\Book::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'isbn' => ['required', 'string', 'max:20', 'unique:books,isbn'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'publication_year' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
            'category' => ['required', 'string', 'max:100'],
            'total_copies' => ['required', 'integer', 'min:1', 'max:1000'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'isbn.required' => 'ISBN is required.',
            'isbn.unique' => 'This ISBN already exists in the library.',
            'title.required' => 'Book title is required.',
            'author.required' => 'Author name is required.',
            'category.required' => 'Book category is required.',
            'total_copies.required' => 'Number of copies is required.',
            'total_copies.min' => 'At least 1 copy must be added.',
            'publication_year.min' => 'Publication year cannot be before 1900.',
            'publication_year.max' => 'Publication year cannot be in the future.',
            'price.min' => 'Price cannot be negative.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'isbn' => 'ISBN',
            'title' => 'book title',
            'author' => 'author',
            'publisher' => 'publisher',
            'publication_year' => 'publication year',
            'category' => 'category',
            'total_copies' => 'total copies',
            'price' => 'price',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => $this->title ? trim($this->title) : null,
            'author' => $this->author ? trim($this->author) : null,
            'publisher' => $this->publisher ? trim($this->publisher) : null,
            'category' => $this->category ? strtolower(trim($this->category)) : null,
        ]);
    }
}
