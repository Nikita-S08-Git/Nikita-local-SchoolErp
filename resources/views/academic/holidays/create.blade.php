@extends('layouts.app')

@section('title', 'Add Holiday')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-plus-circle me-2"></i>Add Holiday</h2>
                    <p class="text-muted mb-0">Create a new holiday or event</p>
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
                    <form action="{{ route('academic.holidays.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <!-- Title -->
                            <div class="col-md-12">
                                <label for="title" class="form-label">Holiday Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title') }}" placeholder="e.g., Diwali Break, Summer Vacation, Annual Day" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror"
                                          placeholder="Brief description of the holiday/event">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date Range -->
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                       value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror"
                                       value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Same as start date for single day</div>
                            </div>

                            <!-- Type -->
                            <div class="col-md-6">
                                <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="public_holiday" {{ old('type') == 'public_holiday' ? 'selected' : '' }}>Public Holiday</option>
                                    <option value="school_holiday" {{ old('type') == 'school_holiday' ? 'selected' : '' }}>School Holiday</option>
                                    <option value="event" {{ old('type') == 'event' ? 'selected' : '' }}>Event</option>
                                    <option value="program" {{ old('type') == 'program' ? 'selected' : '' }}>Program</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Academic Year -->
                            <div class="col-md-6">
                                <label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                <select name="academic_year_id" id="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                    <option value="">-- Select Year --</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Program Incharge -->
                            <div class="col-md-6">
                                <label for="program_incharge_id" class="form-label">Program Incharge (Optional)</label>
                                <select name="program_incharge_id" id="program_incharge_id" class="form-select">
                                    <option value="">None</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('program_incharge_id') == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Location -->
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location (Optional)</label>
                                <input type="text" name="location" id="location" class="form-control"
                                       value="{{ old('location') }}" placeholder="e.g., Main Hall, School Ground">
                            </div>

                            <!-- Options -->
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="is_recurring" id="is_recurring" class="form-check-input" value="1" {{ old('is_recurring') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_recurring">Recurring Event</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create Holiday
                            </button>
                            <a href="{{ route('academic.holidays.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Information</h5>
                </div>
                <div class="card-body">
                    <h6>Holiday Types:</h6>
                    <ul class="small mb-3">
                        <li><strong>Public Holiday:</strong> National/State holidays</li>
                        <li><strong>School Holiday:</strong> School-specific breaks</li>
                        <li><strong>Event:</strong> School events/functions</li>
                        <li><strong>Program:</strong> Academic programs</li>
                    </ul>
                    <h6>Tips:</h6>
                    <ul class="small mb-0">
                        <li>Holidays are excluded from timetable</li>
                        <li>Use same start/end date for single day</li>
                        <li>Mark as recurring for annual events</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-set end date to start date if not set
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    startDate.addEventListener('change', function() {
        if (!endDate.value) {
            endDate.value = this.value;
        }
    });
});
</script>
@endpush
@endsection
