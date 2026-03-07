@extends('layouts.app')

@section('title', 'Edit Program')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-pencil-square me-2"></i>Edit Program</h1>
        <a href="{{ route('academic.programs.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Programs
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('academic.programs.update', $program) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Program Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name', $program->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Short Name *</label>
                        <input type="text" class="form-control @error('short_name') is-invalid @enderror" 
                               name="short_name" value="{{ old('short_name', $program->short_name) }}" required>
                        @error('short_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Program Code *</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" 
                               name="code" value="{{ old('code', $program->code) }}" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Department *</label>
                        <select class="form-select @error('department_id') is-invalid @enderror" 
                                name="department_id" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $program->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Duration (Years) *</label>
                        <input type="number" class="form-control @error('duration_years') is-invalid @enderror" 
                               name="duration_years" value="{{ old('duration_years', $program->duration_years) }}" min="1" max="5" required>
                        @error('duration_years')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Total Semesters</label>
                        <input type="number" class="form-control" name="total_semesters" 
                               value="{{ old('total_semesters', $program->total_semesters) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Program Type *</label>
                        <select class="form-select @error('program_type') is-invalid @enderror" 
                                name="program_type" required>
                            <option value="">Select Type</option>
                            <option value="undergraduate" {{ old('program_type', $program->program_type) == 'undergraduate' ? 'selected' : '' }}>
                                Undergraduate
                            </option>
                            <option value="postgraduate" {{ old('program_type', $program->program_type) == 'postgraduate' ? 'selected' : '' }}>
                                Postgraduate
                            </option>
                            <option value="diploma" {{ old('program_type', $program->program_type) == 'diploma' ? 'selected' : '' }}>
                                Diploma
                            </option>
                        </select>
                        @error('program_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">University Affiliation</label>
                        <input type="text" class="form-control" name="university_affiliation" 
                               value="{{ old('university_affiliation', $program->university_affiliation) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">University Program Code</label>
                        <input type="text" class="form-control" name="university_program_code" 
                               value="{{ old('university_program_code', $program->university_program_code) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Default Grade Scale *</label>
                    <input type="text" class="form-control @error('default_grade_scale_name') is-invalid @enderror" 
                           name="default_grade_scale_name" 
                           value="{{ old('default_grade_scale_name', $program->default_grade_scale_name) }}" required>
                    @error('default_grade_scale_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                           {{ old('is_active', $program->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active (Allow new admissions)</label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Program
                    </button>
                    <a href="{{ route('academic.programs.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection