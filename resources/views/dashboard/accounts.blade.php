@extends('layouts.app')
@section('title', 'Accounts Dashboard')
@section('page-title', 'Accounts Dashboard')
@section('content')
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="bi bi-credit-card fa-2x mb-2"></i>
                <h5>Total Fees Collected</h5>
                <p class="mb-0">₹{{ number_format($totalFeesCollected ?? 0) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="bi bi-exclamation-triangle fa-2x mb-2"></i>
                <h5>Pending Fees</h5>
                <p class="mb-0">₹{{ number_format($pendingFees ?? 0) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="bi bi-calendar-month fa-2x mb-2"></i>
                <h5>Monthly Collection</h5>
                <p class="mb-0">₹{{ number_format($monthlyCollection ?? 0) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="bi bi-people fa-2x mb-2"></i>
                <h5>Total Students</h5>
                <p class="mb-0">{{ number_format($totalStudents ?? 0) }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
