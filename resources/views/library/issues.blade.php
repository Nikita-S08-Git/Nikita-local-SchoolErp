@extends('layouts.app')

@section('title', 'Book Issues')
@section('page-title', 'Book Issues')

@section('content')
<div class="container-fluid p-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>Book Issues</h5>
            <a href="{{ route('library.issues.create') }}" class="btn btn-light btn-sm">
                <i class="bi bi-plus-circle me-1"></i>Issue Book
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Book</th>
                            <th>Student</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($issues as $issue)
                        <tr>
                            <td>{{ $issue->book->title }}</td>
                            <td>{{ $issue->student->user->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($issue->issue_date)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($issue->due_date)->format('d M Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $issue->status === 'issued' ? 'warning' : 'success' }}">
                                    {{ ucfirst($issue->status) }}
                                </span>
                            </td>
                            <td>
                                @if($issue->status === 'issued')
                                <form action="{{ route('library.issues.return', $issue) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Return</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No book issues found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $issues->links() }}
        </div>
    </div>
</div>
@endsection
