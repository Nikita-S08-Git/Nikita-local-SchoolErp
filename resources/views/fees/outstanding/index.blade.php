@extends('layouts.app')

@section('title', 'Outstanding Fees')
@section('page-title', 'Outstanding Fees')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Total Outstanding</h6>
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
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Outstanding Fees</h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <select name="program_id" class="form-select">
                                <option value="">All Programs</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search student..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('fees.outstanding.index') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Program</th>
                                    <th>Fee Type</th>
                                    <th>Total Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Outstanding</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($outstandingFees as $fee)
                                <tr>
                                    <td>
                                        <strong>{{ $fee->student->first_name }} {{ $fee->student->last_name }}</strong><br>
                                        <small class="text-muted">{{ $fee->student->roll_number }}</small>
                                    </td>
                                    <td>{{ $fee->student->program->name }}</td>
                                    <td>{{ $fee->feeStructure->feeHead->name }}</td>
                                    <td>₹{{ number_format($fee->final_amount, 2) }}</td>
                                    <td>₹{{ number_format($fee->paid_amount, 2) }}</td>
                                    <td><strong class="text-danger">₹{{ number_format($fee->outstanding_amount, 2) }}</strong></td>
                                    <td>
                                        <span class="badge {{ $fee->status === 'pending' ? 'bg-danger' : 'bg-warning' }}">
                                            {{ ucfirst($fee->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-check-circle display-4 d-block mb-2 text-success"></i>
                                        No outstanding fees found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $outstandingFees->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection