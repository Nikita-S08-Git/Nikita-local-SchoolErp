@extends('layouts.app')

@section('title', 'Library Books')
@section('page-title', 'Library Books')

@section('content')
<div class="container-fluid p-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-book me-2"></i>Library Books</h5>
            <a href="{{ route('library.books.create') }}" class="btn btn-light btn-sm">
                <i class="bi bi-plus-circle me-1"></i>Add Book
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ISBN</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Available</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                        <tr>
                            <td>{{ $book->isbn }}</td>
                            <td>{{ $book->title }}</td>
                            <td>{{ $book->author }}</td>
                            <td>{{ $book->category }}</td>
                            <td>
                                <span class="badge bg-{{ $book->available_copies > 0 ? 'success' : 'danger' }}">
                                    {{ $book->available_copies }}/{{ $book->total_copies }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('library.books.edit', $book) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('library.books.destroy', $book) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No books found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $books->links() }}
        </div>
    </div>
</div>
@endsection
