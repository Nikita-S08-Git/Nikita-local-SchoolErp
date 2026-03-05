@extends('layouts.app')

@section('title', 'Leave Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Leave Application Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('leaves.my-leaves') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to My Leaves
                        </a>
                        @if($leave->status === 'pending' && $leave->user_id === auth()->id())
                            <a href="{{ route('leaves.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> New Application
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="35%">Leave Type</th>
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
                                                {{ ucfirst($leave->leave_type) }}
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th>Start Date</th>
                                    <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d-m-Y') }}</td>
                                </tr>
                                <tr>
                                    <th>End Date</th>
                                    <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d-m-Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Total Days</th>
                                    <td>{{ $leave->total_days }} day(s)</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
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
                                                {{ ucfirst($leave->status) }}
                                        @endswitch
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="35%">Applied By</th>
                                    <td>{{ $leave->user->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Applied On</th>
                                    <td>{{ $leave->created_at->format('d-m-Y H:i:s') }}</td>
                                </tr>
                                @if($leave->approved_by)
                                <tr>
                                    <th>Approved/Rejected By</th>
                                    <td>{{ $leave->approver->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Processed On</th>
                                    <td>{{ \Carbon\Carbon::parse($leave->approved_at)->format('d-m-Y H:i:s') }}</td>
                                </tr>
                                @endif
                                @if($leave->rejection_reason)
                                <tr>
                                    <th>Rejection Reason</th>
                                    <td class="text-danger">{{ $leave->rejection_reason }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Reason</h5>
                                </div>
                                <div class="card-body">
                                    <p>{{ $leave->reason }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->hasRole(['admin', 'principal']) && $leave->status === 'pending')
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">Actions</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('leaves.approve', $leave->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this leave?')">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                    
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reject Modal -->
                    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('leaves.reject', $leave->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Reject Leave Application</h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="rejection_reason">Rejection Reason <span class="text-danger">*</span></label>
                                            <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="4" required placeholder="Please provide a reason for rejection..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Reject</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
