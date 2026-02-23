@extends('layouts.app')

@section('title', 'Scholarship Details')
@section('page-title', 'Scholarship Details')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-award me-2"></i>Scholarship Details</h5>
                    <div>
                        <a href="{{ route('fees.scholarships.edit', $scholarship) }}" class="btn btn-light btn-sm">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <a href="{{ route('fees.scholarships.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Name:</th>
                                    <td><strong>{{ $scholarship->name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Code:</th>
                                    <td><code>{{ $scholarship->code }}</code></td>
                                </tr>
                                <tr>
                                    <th>Type:</th>
                                    <td>
                                        <span class="badge {{ $scholarship->type === 'percentage' ? 'bg-primary' : 'bg-success' }}">
                                            {{ ucfirst($scholarship->type) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Value:</th>
                                    <td>
                                        <strong class="text-success">
                                            @if($scholarship->type === 'percentage')
                                                {{ $scholarship->value }}%
                                            @else
                                                ₹{{ number_format($scholarship->value, 2) }}
                                            @endif
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Max Amount:</th>
                                    <td>
                                        @if($scholarship->max_amount)
                                            ₹{{ number_format($scholarship->max_amount, 2) }}
                                        @else
                                            <span class="text-muted">No limit</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge {{ $scholarship->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $scholarship->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if($applications->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Applications</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Roll Number</th>
                                    <th>Program</th>
                                    <th>Status</th>
                                    <th>Applied Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applications as $application)
                                <tr>
                                    <td>{{ $application->student->first_name }} {{ $application->student->last_name }}</td>
                                    <td>{{ $application->student->roll_number }}</td>
                                    <td>{{ $application->student->program->name }}</td>
                                    <td>
                                        <span class="badge {{ $application->status === 'approved' ? 'bg-success' : ($application->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                            {{ ucfirst($application->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $application->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $applications->links() }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection