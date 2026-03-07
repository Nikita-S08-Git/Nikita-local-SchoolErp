@extends('layouts.app')

@section('title', 'Edit Book')
@section('page-title', 'Edit Book')

@section('content')
<div class="container-fluid p-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Book</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('library.books.update', $book) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ISBN *</label>
                        <input type="text" name="isbn" class="form-control" value="{{ $book->isbn }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" value="{{ $book->title }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Author *</label>
                        <input type="text" name="author" class="form-control" value="{{ $book->author }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Publisher</label>
                        <input type="text" name="publisher" class="form-control" value="{{ $book->publisher }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category *</label>
                        <input type="text" name="category" class="form-control" value="{{ $book->category }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Total Copies *</label>
                        <input type="number" name="total_copies" class="form-control" value="{{ $book->total_copies }}" required>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Update
                    </button>
                    <a href="{{ route('library.books.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
