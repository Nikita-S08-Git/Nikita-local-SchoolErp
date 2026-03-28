@extends('layouts.app')

@section('title', 'Outstanding Fees')
@section('page-title', 'Outstanding Fees')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Outstanding Fees</h2>
                    <p class="text-muted mb-0">Track unpaid and partially paid fees</p>
                </div>
                <a href="{{ route('admin.fees') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Outstanding Fees Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Outstanding Records</h5>
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
                        @forelse($outstandingFees as $fee)
                        <tr>
                            <td>
                                <strong>{{ $fee->student->first_name ?? 'N/A' }} {{ $fee->student->last_name ?? '' }}</strong>
                                <br><small class="text-muted">{{ $fee->student->admission_number ?? '' }}</small>
                            </td>
                            <td>
                                {{ $fee->feeStructure->feeHead->name ?? $fee->feeStructure->fee_head ?? 'N/A' }}
                                @if($fee->feeStructure->academic_year)
                                    <br><small class="text-muted">{{ $fee->feeStructure->academic_year }}</small>
                                @endif
                            </td>
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
                                    <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                                    <h5>No outstanding fees!</h5>
                                    <p>All students have paid their fees.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($outstandingFees->hasPages())
            <div class="card-footer bg-light">
                {{ $outstandingFees->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
