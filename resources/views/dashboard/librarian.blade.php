@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-book"></i> Librarian Dashboard</h2>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Books</h5>
                    <h2 class="mb-0">{{ $totalBooks ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Available Books</h5>
                    <h2 class="mb-0">{{ $availableBooks ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Issued Books</h5>
                    <h2 class="mb-0">{{ $issuedBooks ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Students</h5>
                    <h2 class="mb-0">{{ $totalStudents ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="{{ route('library.books.index') }}" class="btn btn-primary">
                            <i class="bi bi-book"></i> Manage Books
                        </a>
                        <a href="{{ route('library.issues.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Issue Book
                        </a>
                        <a href="{{ route('library.issues.index') }}" class="btn btn-warning">
                            <i class="bi bi-arrow-return-left"></i> Return Books
                        </a>
                        <a href="{{ route('library.students') }}" class="btn btn-info">
                            <i class="bi bi-people"></i> View Students
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Issues -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Book Issues</h5>
                </div>
                <div class="card-body">
                    @if(isset($recentIssues) && $recentIssues->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>Student</th>
                                        <th>Issue Date</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentIssues as $issue)
                                    <tr>
                                        <td>{{ $issue->book->title ?? 'N/A' }}</td>
                                        <td>{{ $issue->student->user->name ?? 'N/A' }}</td>
                                        <td>{{ $issue->issue_date ? \Carbon\Carbon::parse($issue->issue_date)->format('d-m-Y') : 'N/A' }}</td>
                                        <td>{{ $issue->due_date ? \Carbon\Carbon::parse($issue->due_date)->format('d-m-Y') : 'N/A' }}</td>
                                        <td>
                                            @if($issue->status == 'returned')
                                                <span class="badge bg-success">Returned</span>
                                            @elseif(\Carbon\Carbon::parse($issue->due_date)->isPast())
                                                <span class="badge bg-danger">Overdue</span>
                                            @else
                                                <span class="badge bg-warning">Issued</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent book issues.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
