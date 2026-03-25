@extends('layouts.app')

@section('title', 'Fee Reports')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Fee Reports</h2>
                <a href="{{ route('fees.payments.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Collections
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-cash-register fa-3x mb-3"></i>
                    <h5>Today's Collection</h5>
                    <h3>₹{{ number_format($todayCollection, 2) }}</h3>
                    <p class="mb-0"><small>Payments received today</small></p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-primary text-white h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                    <h5>Monthly Collection</h5>
                    <h3>₹{{ number_format($monthlyCollection, 2) }}</h3>
                    <p class="mb-0"><small>This month</small></p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-warning text-white h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h5>Total Outstanding</h5>
                    <h3>₹{{ number_format($totalOutstanding, 2) }}</h3>
                    <p class="mb-0"><small>Pending payments</small></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Payments</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $payment)
                                <tr>
                                    <td>{{ $payment->student->full_name }}</td>
                                    <td>₹{{ number_format($payment->amount_paid, 2) }}</td>
                                    <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No recent payments</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Defaulters -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-circle me-2"></i>Top Defaulters</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Outstanding</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($outstandingFees as $fee)
                                <tr>
                                    <td>{{ $fee->student->full_name }}</td>
                                    <td class="text-danger fw-bold">₹{{ number_format($fee->outstanding_amount, 2) }}</td>
                                    <td><span class="badge bg-danger">Pending</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No outstanding fees</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('fees.payments.create') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-plus-circle me-2"></i>Collect Payment
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('fees.outstanding.index') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-list me-2"></i>View Outstanding
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('fees.structures.index') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-cog me-2"></i>Fee Structures
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('fees.scholarship-applications.index') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-award me-2"></i>Scholarships
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
