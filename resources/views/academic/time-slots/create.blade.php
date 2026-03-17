@extends('layouts.app')

@section('title', 'Create Time Slot')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-plus-circle me-2"></i>Create Time Slot</h2>
                    <p class="text-muted mb-0">Add a new class period or break time</p>
                </div>
                <a href="{{ route('academic.time-slots.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-clock me-2"></i>Time Slot Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('academic.time-slots.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <!-- Slot Identification -->
                            <div class="col-md-6">
                                <label for="slot_name" class="form-label">Slot Name <span class="text-danger">*</span></label>
                                <input type="text" name="slot_name" id="slot_name" class="form-control @error('slot_name') is-invalid @enderror"
                                       value="{{ old('slot_name') }}" placeholder="e.g., Period 1, Morning Assembly" required>
                                @error('slot_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="slot_code" class="form-label">Slot Code <span class="text-danger">*</span></label>
                                <input type="text" name="slot_code" id="slot_code" class="form-control @error('slot_code') is-invalid @enderror"
                                       value="{{ old('slot_code') }}" placeholder="e.g., P1, MA, LB" required>
                                @error('slot_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Unique code for reference</div>
                            </div>

                            <!-- Time Details -->
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                                <input type="time" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror"
                                       value="{{ old('start_time', '09:00') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">When the slot begins (e.g., 09:00)</div>
                            </div>

                            <div class="col-md-6">
                                <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                                <input type="time" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror"
                                       value="{{ old('end_time', '10:00') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">When the slot ends (e.g., 10:00)</div>
                            </div>

                            <!-- Type and Sequence -->
                            <div class="col-md-6">
                                <label for="slot_type" class="form-label">Slot Type <span class="text-danger">*</span></label>
                                <select name="slot_type" id="slot_type" class="form-select @error('slot_type') is-invalid @enderror" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="instructional" {{ old('slot_type') == 'instructional' ? 'selected' : '' }}>Instructional (Class Period)</option>
                                    <option value="break" {{ old('slot_type') == 'break' ? 'selected' : '' }}>Break Time</option>
                                    <option value="assembly" {{ old('slot_type') == 'assembly' ? 'selected' : '' }}>Assembly</option>
                                    <option value="exam" {{ old('slot_type') == 'exam' ? 'selected' : '' }}>Examination</option>
                                    <option value="lab" {{ old('slot_type') == 'lab' ? 'selected' : '' }}>Laboratory Session</option>
                                    <option value="tutorial" {{ old('slot_type') == 'tutorial' ? 'selected' : '' }}>Tutorial/Remedial</option>
                                    <option value="other" {{ old('slot_type') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('slot_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="sequence_order" class="form-label">Sequence Order <span class="text-danger">*</span></label>
                                <input type="number" name="sequence_order" id="sequence_order" class="form-control @error('sequence_order') is-invalid @enderror"
                                       value="{{ old('sequence_order', 1) }}" min="1" required>
                                @error('sequence_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Order in daily schedule (1 = first slot)</div>
                            </div>

                            <!-- Break Options (shown if break type selected) -->
                            <div class="col-md-6" id="breakTypeContainer" style="display: none;">
                                <label for="break_type" class="form-label">Break Type</label>
                                <select name="break_type" id="break_type" class="form-select">
                                    <option value="">-- Select Break Type --</option>
                                    <option value="short_break" {{ old('break_type') == 'short_break' ? 'selected' : '' }}>Short Break (5-15 min)</option>
                                    <option value="lunch" {{ old('break_type') == 'lunch' ? 'selected' : '' }}>Lunch Break</option>
                                    <option value="long_break" {{ old('break_type') == 'long_break' ? 'selected' : '' }}>Long Break</option>
                                </select>
                            </div>

                            <!-- Academic Session -->
                            <div class="col-md-6">
                                <label for="academic_session_id" class="form-label">Academic Session</label>
                                <select name="academic_session_id" id="academic_session_id" class="form-select">
                                    <option value="">All Sessions</option>
                                    @foreach($academicSessions as $session)
                                        <option value="{{ $session->id }}" {{ old('academic_session_id') == $session->id ? 'selected' : '' }}>
                                            {{ $session->session_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Leave empty for all sessions</div>
                            </div>

                            <!-- Applicable Days -->
                            <div class="col-12">
                                <label class="form-label d-block">Applicable Days</label>
                                <div class="d-flex gap-3 flex-wrap">
                                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" name="applicable_days[]" id="day_{{ strtolower($day) }}"
                                                   class="form-check-input" value="{{ $day }}"
                                                   {{ in_array($day, old('applicable_days', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="day_{{ strtolower($day) }}">{{ $day }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-text">Leave empty for all days</div>
                            </div>

                            <!-- Options -->
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Active</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="is_break" id="is_break" class="form-check-input" value="1" {{ old('is_break') ? 'checked' : '' }} onchange="toggleBreakOptions()">
                                            <label class="form-check-label" for="is_break">Is Break Time</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="available_for_classes" id="available_for_classes" class="form-check-input" value="1" {{ old('available_for_classes', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="available_for_classes">Available for Classes</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="available_for_exams" id="available_for_exams" class="form-check-input" value="1" {{ old('available_for_exams', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="available_for_exams">Available for Exams</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" rows="2" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create Time Slot
                            </button>
                            <a href="{{ route('academic.time-slots.index') }}" class="btn btn-outline-secondary">
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
                    <h6>Slot Types:</h6>
                    <ul class="small mb-3">
                        <li><strong>Instructional:</strong> Regular class periods</li>
                        <li><strong>Break:</strong> Lunch/recess breaks</li>
                        <li><strong>Assembly:</strong> Morning assembly</li>
                        <li><strong>Exam:</strong> Examination periods</li>
                        <li><strong>Lab:</strong> Laboratory sessions</li>
                    </ul>
                    <h6>Tips:</h6>
                    <ul class="small mb-0">
                        <li>Set sequence order for proper scheduling</li>
                        <li>Mark breaks to exclude from classes</li>
                        <li>Use slot codes for quick reference</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleBreakOptions() {
    const isBreak = document.getElementById('is_break').checked;
    const breakTypeContainer = document.getElementById('breakTypeContainer');
    breakTypeContainer.style.display = isBreak ? 'block' : 'none';
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    toggleBreakOptions();
    
    // Show break options if slot type is break
    const slotType = document.getElementById('slot_type');
    slotType.addEventListener('change', function() {
        if (this.value === 'break') {
            document.getElementById('is_break').checked = true;
            toggleBreakOptions();
        }
    });
});
</script>
@endpush
@endsection
