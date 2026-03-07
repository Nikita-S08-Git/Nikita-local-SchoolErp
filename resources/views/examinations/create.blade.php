@extends('layouts.app')

@section('title', 'Create Examination')
@section('page-title', 'Create Examination')

@section('content')
<div class="container-fluid p-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Create New Examination</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('examinations.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Exam Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Exam Code</label>
                        <input type="text" name="code" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Exam Type *</label>
                        <select name="type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="midterm">Midterm</option>
                            <option value="final">Final</option>
                            <option value="unit_test">Unit Test</option>
                            <option value="practical">Practical</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Academic Year *</label>
                        <input type="text" name="academic_year" class="form-control" placeholder="2024-2025" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Start Date *</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">End Date *</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Create
                    </button>
                    <a href="{{ route('examinations.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
