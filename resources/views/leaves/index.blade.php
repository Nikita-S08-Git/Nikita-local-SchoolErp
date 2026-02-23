@extends('layouts.app')

@section('title', 'Leave Management')
@section('page-title', 'Leave Management')

@section('content')
<div class="container-fluid p-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-calendar-x me-2"></i>Leave Applications</h5>
            <a href="{{ route('leaves.create') }}" class="btn btn-light btn-sm">
                <i class="bi bi-plus-circle me-1"></i>Apply Leave
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Leave Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaves as $leave)
                        <tr>
                            <td>{{ $leave->user->name }}</td>
                            <td>{{ ucfirst($leave->leave_type) }}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->start_date)->diffInDays($leave->end_date) + 1 }}</td>
                            <td>
                                <span class="badge bg-{{ $leave->status === 'approved' ? 'success' : ($leave->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </td>
                            <td>
                                @if($leave->status === 'pending')
                                <form action="{{ route('leaves.approve', $leave) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                                <form action="{{ route('leaves.reject', $leave) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No leave applications found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $leaves->links() }}
        </div>
    </div>
</div>
@endsection
