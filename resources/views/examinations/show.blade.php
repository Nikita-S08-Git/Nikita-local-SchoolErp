@extends('layouts.app')

@section('title', 'Examination Details')

@section('content')
<div class="container-fluid p-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📋 {{ $examination->name }}</h5>
            <a href="{{ route('examinations.index') }}" class="btn btn-light btn-sm">Back</a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Code:</strong> {{ $examination->code ?? 'N/A' }}</p>
                    <p><strong>Type:</strong> <span class="badge bg-info">{{ ucfirst($examination->type) }}</span></p>
                    <p><strong>Academic Year:</strong> {{ $examination->academic_year }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Start Date:</strong> {{ $examination->start_date->format('d M Y') }}</p>
                    <p><strong>End Date:</strong> {{ $examination->end_date->format('d M Y') }}</p>
                    <p><strong>Status:</strong> <span class="badge bg-{{ $examination->status == 'completed' ? 'success' : 'warning' }}">{{ ucfirst($examination->status) }}</span></p>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('examinations.marks-entry', $examination) }}" class="btn btn-primary">✏️ Enter Marks</a>
                <a href="{{ route('examinations.edit', $examination) }}" class="btn btn-warning">Edit</a>
            </div>
        </div>
    </div>
</div>
@endsection
