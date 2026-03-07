@extends('layouts.app')

@section('title', 'My Leave Applications')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Leave Applications</h3>
                    <div class="card-tools">
                        <a href="{{ route('leaves.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Apply for Leave
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Total Days</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaves as $leave)
                                    <tr>
                                        <td>
                                            @switch($leave->leave_type)
                                                @case('sick')
                                                    <span class="badge badge-warning">Sick Leave</span>
                                                    @break
                                                @case('casual')
                                                    <span class="badge badge-info">Casual Leave</span>
                                                    @break
                                                @case('earned')
                                                    <span class="badge badge-success">Earned Leave</span>
                                                    @break
                                                @case('maternity')
                                                    <span class="badge badge-primary">Maternity Leave</span>
                                                    @break
                                                @case('unpaid')
                                                    <span class="badge badge-secondary">Unpaid Leave</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">{{ ucfirst($leave->leave_type) }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d-m-Y') }}</td>
                                        <td>{{ $leave->total_days }} day(s)</td>
                                        <td>{{ Str::limit($leave->reason, 50) }}</td>
                                        <td>
                                            @switch($leave->status)
                                                @case('pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                    @break
                                                @case('approved')
                                                    <span class="badge badge-success">Approved</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="badge badge-danger">Rejected</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">{{ ucfirst($leave->status) }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($leave->status === 'pending')
                                                <form action="{{ route('leaves.destroy', $leave->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this leave application?')">
                                                        <i class="fas fa-trash"></i> Cancel
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('leaves.show', $leave->id) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No leave applications found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $leaves->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
