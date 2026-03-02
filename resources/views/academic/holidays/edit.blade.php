@extends('layouts.app')

@section('title', 'Edit Holiday')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-pencil me-2"></i>Edit Holiday</h2>
                    <p class="text-muted mb-0">Update holiday details</p>
                </div>
                <a href="{{ route('academic.holidays.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Holiday Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('academic.holidays.update', $holiday) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="title" class="form-label">Holiday Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title', $holiday->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $holiday->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                       value="{{ old('start_date', $holiday->start_date->format('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror"
                                       value="{{ old('end_date', $holiday->end_date->format('Y-m-d')) }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="public_holiday" {{ old('type', $holiday->type) == 'public_holiday' ? 'selected' : '' }}>Public Holiday</option>
                                    <option value="school_holiday" {{ old('type', $holiday->type) == 'school_holiday' ? 'selected' : '' }}>School Holiday</option>
                                    <option value="event" {{ old('type', $holiday->type) == 'event' ? 'selected' : '' }}>Event</option>
                                    <option value="program" {{ old('type', $holiday->type) == 'program' ? 'selected' : '' }}>Program</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                <select name="academic_year_id" id="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                    <option value="">-- Select Year --</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ old('academic_year_id', $holiday->academic_year_id) == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="program_incharge_id" class="form-label">Program Incharge</label>
                                <select name="program_incharge_id" id="program_incharge_id" class="form-select">
                                    <option value="">None</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('program_incharge_id', $holiday->program_incharge_id) == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" name="location" id="location" class="form-control"
                                       value="{{ old('location', $holiday->location) }}">
                            </div>

                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="is_recurring" id="is_recurring" class="form-check-input" value="1" {{ old('is_recurring', $holiday->is_recurring) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_recurring">Recurring</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $holiday->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Holiday
                            </button>
                            <a href="{{ route('academic.holidays.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Information</h5>
                </div>
                <div class="card-body">
                    <p class="small mb-0">
                        <strong>Created:</strong> {{ $holiday->created_at->format('d M Y') }}<br>
                        <strong>Duration:</strong> {{ $holiday->duration }} days
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
