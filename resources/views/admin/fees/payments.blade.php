@extends('layouts.app')

@section('title', 'Fee Payments')
@section('page-title', 'Fee Payments')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-credit-card me-2 text-primary"></i>Fee Payments</h2>
                    <p class="text-muted mb-0">View all fee payment records</p>
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
                                {{ $student->first_name }} {{ $student->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-select">
                        <option value="">All Methods</option>
                        <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="online" {{ request('payment_method') === 'online' ? 'selected' : '' }}>Online</option>
                        <option value="cheque" {{ request('payment_method') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.fees.payments') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Payment History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Student</th>
                            <th>Fee Head</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Transaction ID</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td>
                                <strong>{{ $payment->studentFee->student->first_name ?? 'N/A' }} {{ $payment->studentFee->student->last_name ?? '' }}</strong>
                                <br><small class="text-muted">{{ $payment->studentFee->student->admission_number ?? '' }}</small>
                            </td>
                            <td>{{ $payment->studentFee->feeStructure->feeHead->name ?? $payment->studentFee->feeStructure->fee_head ?? 'N/A' }}</td>
                            <td><strong>₹{{ number_format($payment->amount, 2) }}</strong></td>
                            <td>
                                <span class="badge bg-{{ $payment->payment_mode === 'cash' ? 'success' : ($payment->payment_mode === 'online' ? 'primary' : 'secondary') }}">
                                    {{ ucfirst($payment->payment_mode ?? $payment->payment_method ?? 'cash') }}
                                </span>
                            </td>
                            <td><small class="font-monospace">{{ $payment->transaction_id ?? '-' }}</small></td>
                            <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : ($payment->created_at->format('d M Y') ?? 'N/A') }}</td>
                            <td>
                                <span class="badge bg-{{ $payment->status === 'success' || $payment->status === 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($payment->status ?? 'paid') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <h5>No payment records found</h5>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($payments->hasPages())
            <div class="card-footer bg-light">
                {{ $payments->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
