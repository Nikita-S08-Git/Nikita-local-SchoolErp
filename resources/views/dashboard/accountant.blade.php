@extends('layouts.app')

@section('title', 'Accountant Dashboard')

@section('page-title', 'Accountant Dashboard')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <h3 class="mb-4"><i class="bi bi-speedometer2 me-2"></i>Accountant Dashboard</h3>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cash-stack fa-3x mb-3"></i>
                    <h5>Fee Collection (Today)</h5>
                    <h3>₹45,000</h3>
                    <p class="mb-0"><small>15 payments received</small></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle fa-3x mb-3"></i>
                    <h5>Outstanding Fees</h5>
                    <h3>₹2,50,000</h3>
                    <p class="mb-0"><small>85 students pending</small></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-receipt fa-3x mb-3"></i>
                    <h5>Receipts Generated</h5>
                    <h3>150</h3>
                    <p class="mb-0"><small>This month</small></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-award fa-3x mb-3"></i>
                    <h5>Scholarships</h5>
                    <h3>25</h3>
                    <p class="mb-0"><small>12 pending approval</small></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning-charge me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('fees.payments.create') }}" class="btn btn-outline-success w-100">
                                <i class="bi bi-plus-circle me-2"></i>Collect Fee Payment
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('fees.structures.index') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-list-columns me-2"></i>Manage Fee Structures
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('fees.outstanding.index') }}" class="btn btn-outline-warning w-100">
                                <i class="bi bi-exclamation-triangle me-2"></i>View Outstanding Fees
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('fees.scholarship-applications.index') }}" class="btn btn-outline-info w-100">
                                <i class="bi bi-file-earmark-check me-2"></i>Scholarship Applications
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Fee Collections -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Fee Collections</h5>
                    <a href="{{ route('fees.payments.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Admission No</th>
                                    <th>Amount</th>
                                    <th>Payment Mode</th>
                                    <th>Status</th>
                                    <th>Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ today()->format('d M Y') }}</td>
                                    <td>John Doe</td>
                                    <td>ADM2024001</td>
                                    <td>₹5,000</td>
                                    <td><span class="badge bg-success">Cash</span></td>
                                    <td><span class="badge bg-success">Paid</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ today()->format('d M Y') }}</td>
                                    <td>Jane Smith</td>
                                    <td>ADM2024002</td>
                                    <td>₹8,000</td>
                                    <td><span class="badge bg-info">Online</span></td>
                                    <td><span class="badge bg-success">Paid</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ today()->format('d M Y') }}</td>
                                    <td>Mike Johnson</td>
                                    <td>ADM2024003</td>
                                    <td>₹3,500</td>
                                    <td><span class="badge bg-success">Cash</span></td>
                                    <td><span class="badge bg-success">Paid</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Scholarship Applications -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-check me-2"></i>Pending Scholarship Applications</h5>
                    <a href="{{ route('fees.scholarship-applications.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Scholarship Type</th>
                                    <th>Amount</th>
                                    <th>Applied Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Student A</td>
                                    <td>Merit Scholarship</td>
                                    <td>₹10,000</td>
                                    <td>2 days ago</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-success">
                                            <i class="bi bi-check"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-danger">
                                            <i class="bi bi-x"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Student B</td>
                                    <td>SC/ST Scholarship</td>
                                    <td>₹15,000</td>
                                    <td>3 days ago</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-success">
                                            <i class="bi bi-check"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-danger">
                                            <i class="bi bi-x"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
