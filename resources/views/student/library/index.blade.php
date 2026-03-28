@extends('student.layouts.app')

@section('title', 'My Library')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-1"><i class="bi bi-book me-2 text-primary"></i>My Library</h2>
            <p class="text-muted mb-0">View your issued books and library history</p>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-journal-bookmark me-2 text-primary"></i>Issued Books</h5>
        </div>
        <div class="card-body">
            @if($issuedBooks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Book</th>
                                <th>Author</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Return Date</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($issuedBooks as $book)
                                <tr>
                                    <td>
                                        <strong>{{ $book->book->title ?? 'N/A' }}</strong>
                                    </td>
                                    <td>{{ $book->book->author ?? 'N/A' }}</td>
                                    <td>{{ $book->issue_date->format('d M Y') }}</td>
                                    <td>{{ $book->due_date->format('d M Y') }}</td>
                                    <td>
                                        @if($book->return_date)
                                            {{ $book->return_date->format('d M Y') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($book->status === 'returned')
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Returned</span>
                                        @elseif($book->status === 'overdue')
                                            <span class="badge bg-danger"><i class="bi bi-clock me-1"></i>Overdue</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="bi bi-book me-1"></i>Issued</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-bookshelf text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">No books issued yet</p>
                </div>
            @endif
        </div>

        @if($issuedBooks->count() > 0 && $issuedBooks->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="text-muted">
                    Showing <strong>{{ $issuedBooks->firstItem() ?? 0 }}</strong> to 
                    <strong>{{ $issuedBooks->lastItem() ?? 0 }}</strong> of 
                    <strong>{{ $issuedBooks->total() }}</strong> books
                </div>
                <nav aria-label="Book pagination">
                    {{ $issuedBooks->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
