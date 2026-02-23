<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Library\StoreBookRequest;
use App\Http\Requests\Library\UpdateBookRequest;
use App\Http\Requests\Library\IssueBookRequest;
use App\Models\Library\Book;
use App\Models\Library\BookIssue;
use App\Models\User\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LibraryController extends Controller
{
    public function index()
    {
        $books = Book::where('is_active', true)->paginate(15);
        return view('library.books.index', compact('books'));
    }

    public function create()
    {
        return view('library.books.create');
    }

    public function store(StoreBookRequest $request)
    {
        $validated = $request->validated();
        $validated['available_copies'] = $validated['total_copies'];

        Book::create($validated);

        return redirect()->route('library.books.index')
            ->with('success', 'Book added successfully!');
    }

    public function edit(Book $book)
    {
        return view('library.books.edit', compact('book'));
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $validated = $request->validated();
        $book->update($validated);

        return redirect()->route('library.books.index')
            ->with('success', 'Book updated successfully!');
    }

    public function issueForm()
    {
        $books = Book::where('is_active', true)
            ->where('available_copies', '>', 0)
            ->get();
        $students = Student::where('student_status', 'active')
            ->with('user')
            ->get();
        return view('library.issue-create', compact('books', 'students'));
    }

    public function issue(IssueBookRequest $request)
    {
        $validated = $request->validated();

        $book = Book::findOrFail($validated['book_id']);

        BookIssue::create([
            'book_id' => $validated['book_id'],
            'student_id' => $validated['student_id'],
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'status' => 'issued',
        ]);

        $book->decrement('available_copies');

        return redirect()->route('library.issues.index')
            ->with('success', 'Book issued successfully!');
    }

    public function issuesIndex()
    {
        $issues = BookIssue::with(['book', 'student'])
            ->latest()
            ->paginate(15);
        return view('library.issues', compact('issues'));
    }

    public function returnBook(BookIssue $issue)
    {
        $issue->update([
            'return_date' => now(),
            'status' => 'returned',
            'fine_amount' => $this->calculateFine($issue),
        ]);

        $issue->book->increment('available_copies');

        return redirect()->route('library.issues.index')
            ->with('success', 'Book returned successfully!');
    }

    private function calculateFine(BookIssue $issue)
    {
        $dueDate = Carbon::parse($issue->due_date);
        $returnDate = Carbon::now();

        if ($returnDate->greaterThan($dueDate)) {
            $daysLate = $returnDate->diffInDays($dueDate);
            return $daysLate * 5; // ₹5 per day fine
        }

        return 0;
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('library.books.index')
            ->with('success', 'Book deleted successfully!');
    }
}
