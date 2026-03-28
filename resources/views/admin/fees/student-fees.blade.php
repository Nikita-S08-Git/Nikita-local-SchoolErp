@extends('layouts.app')

@section('title', 'Student Fees')
@section('page-title', 'Student Fees')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-user-graduate me-2 text-primary"></i>Student Fees</h2>
                    <p class="text-muted mb-0">View and manage student fee records</p>
                </div>
                <a href="{{ route('admin.fees') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filter by Student</label>
                    <select name="student_id" class="form-select">
                        <option value="">All Students</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->first_name }} {{ $student->last_name }} ({{ $student->admission_number }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.fees.student-fees') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Fees Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Fee Records</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Student</th>
                            <th>Fee Head</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Outstanding</th>
                            <th>Status</th>
                            <th>Created Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fees as $fee)
                        <tr>
                            <td>
                                <strong>{{ $fee->student->first_name ?? 'N/A' }} {{ $fee->student->last_name ?? '' }}</strong>
                                <br><small class="text-muted">{{ $fee->student->admission_number ?? '' }}</small>
                            </td>
                            <td>{{ $fee->feeStructure->feeHead->name ?? $fee->feeStructure->fee_head ?? 'N/A' }}</td>
                            <td><strong>₹{{ number_format($fee->total_amount ?? $fee->final_amount ?? 0, 2) }}</strong></td>
                            <td class="text-success"><strong>₹{{ number_format($fee->paid_amount ?? 0, 2) }}</strong></td>
                            <td class="text-danger"><strong>₹{{ number_format(($fee->total_amount ?? $fee->final_amount ?? 0) - ($fee->paid_amount ?? 0), 2) }}</strong></td>
                            <td>
                                <span class="badge bg-{{ $fee->status === 'paid' ? 'success' : ($fee->status === 'partial' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($fee->status) }}
                                </span>
                                @if($fee->status !== 'paid' && $fee->created_at && \Carbon\Carbon::parse($fee->created_at)->diffInDays() > 30)
                                    <br><small class="text-danger"><i class="fas fa-exclamation-circle"></i> Overdue</small>
                                @endif
                            </td>
                            <td>
                                {{ $fee->created_at->format('d M Y') }}
                                <br><small class="text-muted">{{ $fee->created_at->format('H:i') }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <h5>No fee records found</h5>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($fees->hasPages())
            <div class="card-footer bg-light">
                {{ $fees->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
