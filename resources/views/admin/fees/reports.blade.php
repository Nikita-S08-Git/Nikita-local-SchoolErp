@extends('layouts.app')

@section('title', 'Fee Reports')
@section('page-title', 'Fee Reports')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-chart-line me-2 text-primary"></i>Fee Reports</h2>
                    <p class="text-muted mb-0">View fee collection statistics and reports</p>
                </div>
                <a href="{{ route('admin.fees') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card stats-card h-100 border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Total Collected</p>
                            <h2 class="mb-0 fw-bold">₹{{ number_format($totalCollected, 2) }}</h2>
                        </div>
                        <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stats-card h-100 border-0" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">This Month</p>
                            <h2 class="mb-0 fw-bold">₹{{ number_format($thisMonth, 2) }}</h2>
                        </div>
                        <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                            <i class="bi bi-calendar3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stats-card h-100 border-0" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Today</p>
                            <h2 class="mb-0 fw-bold">₹{{ number_format($today, 2) }}</h2>
                        </div>
                        <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                            <i class="bi bi-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Summary -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Collection Summary</h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="p-4">
                        <i class="fas fa-coins fa-3x text-primary mb-3"></i>
                        <h5>Total Fees Assigned</h5>
                        <p class="text-muted">All fee records across all students</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5>Collection Rate</h5>
                        <p class="text-muted">Percentage of fees collected</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4">
                        <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                        <h5>Outstanding</h5>
                        <p class="text-muted">Pending fee collection</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
