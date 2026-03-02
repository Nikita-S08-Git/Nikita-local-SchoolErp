@extends('layouts.app')

@section('title', 'Fee Receipt - ' . $payment->receipt_number)
@section('page-title', 'Fee Payment Receipt')
@section('page-subtitle', $payment->receipt_number)

@php
// Include the numberToWords helper if not already loaded
if (!function_exists('numberToWords')) {
    require_once app_path('Helpers/NumberToWords.php');
}
@endphp

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Fee Payment Receipt</h5>
                    <div>
                        <a href="{{ route('fees.payments.download', $payment->id) }}" class="btn btn-light btn-sm me-2">
                            <i class="bi bi-download me-1"></i>Download PDF
                        </a>
                        <a href="{{ route('fees.payments.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Receipt Header -->
                    <div class="text-center border-bottom pb-3 mb-4">
                        <h4 class="mb-1">School ERP System</h4>
                        <p class="text-muted mb-0">Fee Payment Receipt</p>
                    </div>

                    <!-- Receipt Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Receipt No:</strong></p>
                            <p class="text-muted">{{ $payment->receipt_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Date:</strong></p>
                            <p class="text-muted">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</p>
                        </div>
                    </div>

                    <!-- Student Info -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="card-title mb-3"><i class="bi bi-person me-2"></i>Student Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Name:</strong></p>
                                    <p class="text-muted">{{ $payment->studentFee->student->first_name }} {{ $payment->studentFee->student->last_name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Roll Number:</strong></p>
                                    <p class="text-muted">{{ $payment->studentFee->student->roll_number }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Class/Division:</strong></p>
                                    <p class="text-muted">{{ $payment->studentFee->student->division->division_name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Payment Mode:</strong></p>
                                    <p class="text-muted">{{ strtoupper($payment->payment_mode) }}</p>
                                </div>
                            </div>
                            @if($payment->transaction_id)
                            <div class="row">
                                <div class="col-12">
                                    <p class="mb-1"><strong>Transaction ID:</strong></p>
                                    <p class="text-muted">{{ $payment->transaction_id }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Amount Paid -->
                    <div class="text-center mb-4">
                        <p class="text-muted mb-1">Amount Paid</p>
                        <h2 class="text-success mb-1">₹ {{ number_format($payment->amount, 2) }}</h2>
                        <p class="text-muted small">{{ ucwords(numberToWords($payment->amount)) }}</p>
                    </div>

                    <!-- Fee Details -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h6 class="card-title mb-3"><i class="bi bi-cash-stack me-2"></i>Fee Details</h6>
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td><strong>Fee Head:</strong></td>
                                        <td>{{ $payment->studentFee->feeStructure->feeHead->fee_head_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Fee:</strong></td>
                                        <td>₹ {{ number_format($payment->studentFee->final_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Paid Amount:</strong></td>
                                        <td>₹ {{ number_format($payment->studentFee->paid_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Outstanding:</strong></td>
                                        <td>₹ {{ number_format($payment->studentFee->outstanding_amount, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Remarks -->
                    @if($payment->remarks)
                    <div class="alert alert-info">
                        <strong>Remarks:</strong> {{ $payment->remarks }}
                    </div>
                    @endif

                    <!-- Footer -->
                    <div class="text-center pt-3 border-top">
                        <p class="text-muted small mb-0">This is a computer-generated receipt.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
