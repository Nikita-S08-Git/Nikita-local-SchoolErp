@extends('layouts.app')
@section('title', 'Teacher Dashboard')
@section('page-title', 'Teacher Dashboard')
@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="bi bi-people fa-2x mb-2"></i>
                <h5>Total Students</h5>
                <p class="mb-0">{{ number_format($totalStudents ?? 0) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="bi bi-person-badge fa-2x mb-2"></i>
                <h5>Assigned Division</h5>
                <p class="mb-0">{{ $assignedDivision->division_name ?? 'Not Assigned' }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="bi bi-person fa-2x mb-2"></i>
                <h5>Teacher</h5>
                <p class="mb-0">{{ $teacher->name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
