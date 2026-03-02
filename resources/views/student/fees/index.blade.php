@extends('layouts.app')

@section('title', 'My Fees')
@section('page-title', 'My Fees')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Total Fees</h6>
                            <h4 class="mb-0">₹{{ number_format($totalFees, 2) }}</h4>
                        </div>
                        <i class="bi bi-credit-card display-6"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Paid Amount</h6>
                            <h4 class="mb-0">₹{{ number_format($totalPaid, 2) }}</h4>
                        </div>
                        <i class="bi bi-check-circle display-6"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Outstanding</h6>
                            <h4 class="mb-0">₹{{ number_format($totalOutstanding, 2) }}</h4>
                        </div>
                        <i class="bi bi-exclamation-triangle display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>My Fee Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Fee Type</th>
                                    <th>Total Amount</th>
                                    <th>Discount</th>
                                    <th>Final Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Outstanding</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($studentFees as $fee)
                                <tr>
                                    <td><strong>{{ $fee->feeStructure->feeHead->name }}</strong></td>
                                    <td>₹{{ number_format($fee->total_amount, 2) }}</td>
                                    <td>₹{{ number_format($fee->discount_amount, 2) }}</td>
                                    <td>₹{{ number_format($fee->final_amount, 2) }}</td>
                                    <td>₹{{ number_format($fee->paid_amount, 2) }}</td>
                                    <td>
                                        <strong class="{{ $fee->outstanding_amount > 0 ? 'text-danger' : 'text-success' }}">
                                            ₹{{ number_format($fee->outstanding_amount, 2) }}
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="badge {{ $fee->status === 'paid' ? 'bg-success' : ($fee->status === 'partial' ? 'bg-warning' : 'bg-danger') }}">
                                            {{ ucfirst($fee->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($fee->outstanding_amount > 0)
                                            <a href="{{ route('student.fees.payment', $fee) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-credit-card me-1"></i>Pay Now
                                            </a>
                                        @else
                                            <span class="text-success"><i class="bi bi-check-circle"></i> Paid</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-credit-card display-4 d-block mb-2"></i>
                                        No fees assigned yet
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if($studentFees->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Payment History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Receipt No.</th>
                                    <th>Fee Type</th>
                                    <th>Amount</th>
                                    <th>Payment Mode</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($studentFees as $fee)
                                    @foreach($fee->payments as $payment)
                                    <tr>
                                        <td><strong>{{ $payment->receipt_number }}</strong></td>
                                        <td>{{ $fee->feeStructure->feeHead->name }}</td>
                                        <td>₹{{ number_format($payment->amount, 2) }}</td>
                                        <td><span class="badge bg-info">{{ ucfirst($payment->payment_mode) }}</span></td>
                                        <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                    </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection