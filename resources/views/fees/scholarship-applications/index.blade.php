@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Scholarship Applications</h2>
        <a href="{{ route('fees.scholarship-applications.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Application
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Scholarship</th>
                        <th>Applied Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr>
                            <td>
                                <strong>{{ $app->student->full_name }}</strong><br>
                                <small>{{ $app->student->admission_number }}</small>
                            </td>
                            <td>
                                {{ $app->scholarship->name }}<br>
                                <small class="text-muted">
                                    {{ ucfirst($app->scholarship->discount_type) }}: 
                                    {{ $app->scholarship->discount_value }}{{ $app->scholarship->discount_type === 'percentage' ? '%' : '₹' }}
                                </small>
                            </td>
                            <td>{{ $app->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $app->status === 'approved' ? 'success' : ($app->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($app->status) }}
                                </span>
                            </td>
                            <td>
                                @if($app->status === 'pending')
                                    <form action="{{ route('fees.scholarship-applications.approve', $app) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $app->id }}">Reject</button>
                                @else
                                    <span class="text-muted">{{ $app->status === 'approved' ? 'Applied' : 'Rejected' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">No applications found</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $applications->links() }}
        </div>
    </div>
</div>
@endsection
