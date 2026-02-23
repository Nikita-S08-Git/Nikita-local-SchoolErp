@extends('layouts.app')

@section('title', 'Staff Details')
@section('page-title', 'Staff Details')

@section('content')
<div class="container-fluid p-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-person me-2"></i>Staff Details</h5>
            <a href="{{ route('staff.edit', $staff) }}" class="btn btn-light btn-sm">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Employee ID:</strong> {{ $staff->employee_id }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Name:</strong> {{ $staff->first_name }} {{ $staff->last_name }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Email:</strong> {{ $staff->user->email }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Phone:</strong> {{ $staff->phone }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Designation:</strong> {{ $staff->designation }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Department:</strong> {{ $staff->department->name ?? 'N/A' }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Employment Type:</strong> {{ ucfirst($staff->employment_type) }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Status:</strong> 
                    <span class="badge bg-{{ $staff->status === 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($staff->status) }}
                    </span>
                </div>
            </div>
            <a href="{{ route('staff.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
