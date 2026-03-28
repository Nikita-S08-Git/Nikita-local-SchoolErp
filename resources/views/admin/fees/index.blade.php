@extends('layouts.app')

@section('title', 'Fee Management')
@section('page-title', 'Fee Management Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stats-card h-100 border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Total Fees</p>
                            <h2 class="mb-0 fw-bold">₹{{ number_format($totalFees, 2) }}</h2>
                        </div>
                        <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stats-card h-100 border-0" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Total Paid</p>
                            <h2 class="mb-0 fw-bold">₹{{ number_format($totalPaid, 2) }}</h2>
                        </div>
                        <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stats-card h-100 border-0" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Outstanding</p>
                            <h2 class="mb-0 fw-bold">₹{{ number_format($totalOutstanding, 2) }}</h2>
                        </div>
                        <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                            <i class="bi bi-exclamation-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stats-card h-100 border-0" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Active Students</p>
                            <h2 class="mb-0 fw-bold">{{ $totalStudents }}</h2>
                        </div>
                        <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('admin.fees.structures') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="bi bi-list-task d-block mb-2" style="font-size: 2rem;"></i>
                                <span>Fee Structures</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.fees.student-fees') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="bi bi-person-badge d-block mb-2" style="font-size: 2rem;"></i>
                                <span>Student Fees</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.fees.payments') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="bi bi-credit-card d-block mb-2" style="font-size: 2rem;"></i>
                                <span>Payments</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.fees.outstanding') }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="bi bi-exclamation-triangle d-block mb-2" style="font-size: 2rem;"></i>
                                <span>Outstanding</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="card shadow-sm border-0" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Payments</h5>
                <a href="{{ route('admin.fees.payments') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
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
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $payment)
                        <tr>
                            <td>
                                <strong>{{ $payment->student->first_name ?? 'N/A' }} {{ $payment->student->last_name ?? '' }}</strong>
                                <br><small class="text-muted">{{ $payment->student->admission_number ?? '' }}</small>
                            </td>
                            <td>{{ $payment->feeStructure->fee_head ?? 'N/A' }}</td>
                            <td><strong>₹{{ number_format($payment->amount, 2) }}</strong></td>
                            <td>
                                <span class="badge bg-{{ $payment->payment_method === 'cash' ? 'success' : ($payment->payment_method === 'online' ? 'primary' : 'secondary') }}">
                                    {{ ucfirst($payment->payment_method) }}
                                </span>
                            </td>
                            <td>{{ $payment->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $payment->status === 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fa-2x mb-2"></i>
                                    <p>No recent payments</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
