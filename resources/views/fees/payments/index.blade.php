@extends('layouts.app')

@section('title', 'Fee Payments')
@section('page-title', 'Fee Payments')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Fee Payments</h5>
                    <a href="{{ route('fees.payments.create') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle me-1"></i>Record Payment
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Receipt No.</th>
                                    <th>Student</th>
                                    <th>Fee Type</th>
                                    <th>Amount</th>
                                    <th>Payment Mode</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                <tr>
                                    <td><strong>{{ $payment->receipt_number }}</strong></td>
                                    <td>{{ $payment->studentFee->student->first_name }} {{ $payment->studentFee->student->last_name }}</td>
                                    <td>{{ $payment->studentFee->feeStructure->feeHead->name }}</td>
                                    <td>₹{{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($payment->payment_mode) }}</span>
                                    </td>
                                    <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge {{ $payment->status === 'success' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-receipt display-4 d-block mb-2"></i>
                                        No payments recorded yet
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection