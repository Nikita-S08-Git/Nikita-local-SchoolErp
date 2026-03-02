@extends('layouts.app')

@section('title', 'Edit Time Slot')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-pencil me-2"></i>Edit Time Slot</h2>
                    <p class="text-muted mb-0">Update time slot details</p>
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
                    <form action="{{ route('academic.time-slots.update', $timeSlot) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Slot Identification -->
                            <div class="col-md-6">
                                <label for="slot_name" class="form-label">Slot Name <span class="text-danger">*</span></label>
                                <input type="text" name="slot_name" id="slot_name" class="form-control @error('slot_name') is-invalid @enderror"
                                       value="{{ old('slot_name', $timeSlot->slot_name) }}" required>
                                @error('slot_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="slot_code" class="form-label">Slot Code <span class="text-danger">*</span></label>
                                <input type="text" name="slot_code" id="slot_code" class="form-control @error('slot_code') is-invalid @enderror"
                                       value="{{ old('slot_code', $timeSlot->slot_code) }}" required>
                                @error('slot_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Time Details -->
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                                <input type="time" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror"
                                       value="{{ old('start_time', substr($timeSlot->start_time, 0, 5)) }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">When the slot begins</div>
                            </div>

                            <div class="col-md-6">
                                <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                                <input type="time" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror"
                                       value="{{ old('end_time', substr($timeSlot->end_time, 0, 5)) }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">When the slot ends</div>
                            </div>

                            <!-- Type and Sequence -->
                            <div class="col-md-6">
                                <label for="slot_type" class="form-label">Slot Type <span class="text-danger">*</span></label>
                                <select name="slot_type" id="slot_type" class="form-select @error('slot_type') is-invalid @enderror" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="instructional" {{ old('slot_type', $timeSlot->slot_type) == 'instructional' ? 'selected' : '' }}>Instructional</option>
                                    <option value="break" {{ old('slot_type', $timeSlot->slot_type) == 'break' ? 'selected' : '' }}>Break Time</option>
                                    <option value="assembly" {{ old('slot_type', $timeSlot->slot_type) == 'assembly' ? 'selected' : '' }}>Assembly</option>
                                    <option value="exam" {{ old('slot_type', $timeSlot->slot_type) == 'exam' ? 'selected' : '' }}>Examination</option>
                                    <option value="lab" {{ old('slot_type', $timeSlot->slot_type) == 'lab' ? 'selected' : '' }}>Laboratory</option>
                                    <option value="tutorial" {{ old('slot_type', $timeSlot->slot_type) == 'tutorial' ? 'selected' : '' }}>Tutorial</option>
                                    <option value="other" {{ old('slot_type', $timeSlot->slot_type) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('slot_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="sequence_order" class="form-label">Sequence Order <span class="text-danger">*</span></label>
                                <input type="number" name="sequence_order" id="sequence_order" class="form-control @error('sequence_order') is-invalid @enderror"
                                       value="{{ old('sequence_order', $timeSlot->sequence_order) }}" min="1" required>
                                @error('sequence_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Break Options -->
                            <div class="col-md-6" id="breakTypeContainer" style="{{ old('is_break', $timeSlot->is_break) || old('slot_type', $timeSlot->slot_type) == 'break' ? '' : 'display: none;' }}">
                                <label for="break_type" class="form-label">Break Type</label>
                                <select name="break_type" id="break_type" class="form-select">
                                    <option value="">-- Select Break Type --</option>
                                    <option value="short_break" {{ old('break_type', $timeSlot->break_type) == 'short_break' ? 'selected' : '' }}>Short Break</option>
                                    <option value="lunch" {{ old('break_type', $timeSlot->break_type) == 'lunch' ? 'selected' : '' }}>Lunch Break</option>
                                    <option value="long_break" {{ old('break_type', $timeSlot->break_type) == 'long_break' ? 'selected' : '' }}>Long Break</option>
                                </select>
                            </div>

                            <!-- Academic Session -->
                            <div class="col-md-6">
                                <label for="academic_session_id" class="form-label">Academic Session</label>
                                <select name="academic_session_id" id="academic_session_id" class="form-select">
                                    <option value="">All Sessions</option>
                                    @foreach($academicSessions as $session)
                                        <option value="{{ $session->id }}" {{ old('academic_session_id', $timeSlot->academic_session_id) == $session->id ? 'selected' : '' }}>
                                            {{ $session->session_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Applicable Days -->
                            <div class="col-12">
                                <label class="form-label d-block">Applicable Days</label>
                                <div class="d-flex gap-3 flex-wrap">
                                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" name="applicable_days[]" id="day_{{ strtolower($day) }}"
                                                   class="form-check-input" value="{{ $day }}"
                                                   {{ in_array($day, old('applicable_days', $timeSlot->applicable_days ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="day_{{ strtolower($day) }}">{{ $day }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Options -->
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $timeSlot->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Active</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="is_break" id="is_break" class="form-check-input" value="1" {{ old('is_break', $timeSlot->is_break) ? 'checked' : '' }} onchange="toggleBreakOptions()">
                                            <label class="form-check-label" for="is_break">Is Break Time</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="available_for_classes" id="available_for_classes" class="form-check-input" value="1" {{ old('available_for_classes', $timeSlot->available_for_classes) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="available_for_classes">Available for Classes</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="available_for_exams" id="available_for_exams" class="form-check-input" value="1" {{ old('available_for_exams', $timeSlot->available_for_exams) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="available_for_exams">Available for Exams</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" rows="2" class="form-control">{{ old('description', $timeSlot->description) }}</textarea>
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes', $timeSlot->notes) }}</textarea>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Time Slot
                            </button>
                            <a href="{{ route('academic.time-slots.index') }}" class="btn btn-outline-secondary">
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
                        <strong>Current Usage:</strong> {{ $timeSlot->timetables->count() }} timetable entries
                    </p>
                    <hr>
                    <h6>Tips:</h6>
                    <ul class="small mb-0">
                        <li>Changes will affect all linked timetables</li>
                        <li>Ensure no time conflicts before saving</li>
                        <li>Update sequence order carefully</li>
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

document.addEventListener('DOMContentLoaded', function() {
    toggleBreakOptions();
    
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
