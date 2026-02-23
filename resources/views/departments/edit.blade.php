@extends('layouts.app')

@section('title', 'Edit Department')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-gray-800 mb-1">Edit Department</h1>
            <p class="text-muted mb-0">Update department information and settings</p>
        </div>
        <a href="{{ route('web.departments.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Departments
        </a>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('web.departments.update', $department) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Department Name & Code -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">Department Name <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            value="{{ old('name', $department->name) }}"
                            placeholder="e.g., Computer Science"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="code" class="form-label fw-semibold">Department Code <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control @error('code') is-invalid @enderror"
                            id="code"
                            name="code"
                            value="{{ old('code', $department->code) }}"
                            placeholder="e.g., CS"
                            required
                        >
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Use a unique short code for identification</div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold">Description</label>
                    <textarea
                        class="form-control @error('description') is-invalid @enderror"
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Provide a brief description of the department..."
                    >{{ old('description', $department->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Head of Department -->
                <div class="mb-4">
                    <label for="hod_user_id" class="form-label fw-semibold">Head of Department (HOD)</label>
                    <select
                        class="form-select @error('hod_user_id') is-invalid @enderror"
                        id="hod_user_id"
                        name="hod_user_id"
                    >
                        <option value="">— Select HOD —</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('hod_user_id', $department->hod_user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('hod_user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Only active users are available for selection</div>
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Status</label>
                    <div class="form-check form-switch">
                        <input 
                            class="form-check-input" 
                            type="checkbox" 
                            role="switch" 
                            id="is_active" 
                            name="is_active" 
                            value="1"
                            {{ old('is_active', $department->is_active) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="is_active">
                            Active Department
                        </label>
                    </div>
                    <div class="form-text">Inactive departments won't be visible to students</div>
                </div>

                <!-- Form Actions -->
                <div class="border-top pt-4 mt-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="fas fa-save me-1"></i> Update Department
                        </button>
                        <a href="{{ route('web.departments.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Primary button */
.btn-primary {
    background-color: #000 !important;
    border-color: #000 !important;
    color: #fff !important;
}

.btn-primary:hover {
    background-color: #222 !important;
    border-color: #222 !important;
    color: #fff !important;
}

/* Secondary button */
.btn-secondary {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
    color: #fff !important;
}

.btn-secondary:hover {
    background-color: #5a6268 !important;
    border-color: #545b62 !important;
}

/* Outline secondary button */
.btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: #fff;
}

/* Form controls */
.form-control:focus,
.form-select:focus {
    border-color: #000;
    box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.1);
}

.form-check-input:checked {
    background-color: #000;
    border-color: #000;
}

.form-check-input:focus {
    border-color: #000;
    box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.1);
}

/* Text styling */
.text-gray-800 {
    color: #2d3748;
}

/* Card styling */
.card {
    border-radius: 0.5rem;
}

/* Form labels */
.form-label {
    color: #495057;
    margin-bottom: 0.5rem;
}
</style>
@endpush

@push('head')
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-papVtJ6Qoyt7rO8zOoaJ+R/0ZoHtn+J8EYBrmPQOiEz2+qyj/MoF+jV7qZHb3T7j6xFwfsPWTf6yoA08VUcZ2g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush