@extends('layouts.app')

@section('title', 'Examinations')
@section('page-title', 'Examinations')

@section('content')
<div class="container-fluid p-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Examinations</h5>
            <a href="{{ route('examinations.create') }}" class="btn btn-light btn-sm">
                <i class="bi bi-plus-circle me-1"></i>Create Exam
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Exam Name</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($examinations as $exam)
                        <tr>
                            <td>{{ $exam->name }}</td>
                            <td>{{ $exam->code ?? 'N/A' }}</td>
                            <td>{{ ucfirst($exam->type) }}</td>
                            <td>{{ \Carbon\Carbon::parse($exam->start_date)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($exam->end_date)->format('d M Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $exam->status === 'completed' ? 'success' : 'warning' }}">
                                    {{ ucfirst($exam->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('examinations.show', $exam) }}" class="btn btn-sm btn-info" title="View">
                                        👁️
                                    </a>
                                    <a href="{{ route('examinations.edit', $exam) }}" class="btn btn-sm btn-warning" title="Edit">
                                        ✏️
                                    </a>
                                    <form action="{{ route('examinations.destroy', $exam) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this exam?')" title="Delete">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No examinations found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $examinations->links() }}
        </div>
    </div>
</div>
@endsection
