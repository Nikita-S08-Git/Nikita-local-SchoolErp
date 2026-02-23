@extends('layouts.app')

@section('title', 'Edit Timetable Entry')

@section('content')
<style>
    .form-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }
    
    .form-card-header {
        background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);
        border-radius: 12px 12px 0 0;
        border: none;
        padding: 1.25rem 1.5rem;
    }
    
    .form-card-header h5 {
        color: white;
        font-weight: 600;
        margin: 0;
    }
    
    .form-section {
        background: #f8f9ff;
        border-radius: 10px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }
    
    .form-section-title {
        color: #667eea;
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }
    
    .form-section-title i {
        margin-right: 8px;
        font-size: 1.1rem;
    }
    
    .form-label {
        font-weight: 500;
        color: #4a5568;
        margin-bottom: 0.5rem;
    }
    
    .form-select, .form-control {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.625rem 1rem;
        transition: all 0.2s ease;
    }
    
    .form-select:focus, .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
    }
    
    .form-text {
        font-size: 0.8rem;
        color: #718096;
        margin-top: 0.35rem;
    }
    
    .required-mark {
        color: #e53e3e;
        margin-left: 2px;
    }
    
    .btn-warning {
        background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    
    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(237, 137, 54, 0.4);
    }
    
    .btn-secondary {
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
    }
    
    .alert {
        border-radius: 10px;
        border: none;
        padding: 1rem 1.25rem;
    }
    
    .slot-available {
        background: linear-gradient(135deg, #c6f6d5 0%, #9ae6b4 100%);
        color: #22543d;
    }
    
    .slot-conflict {
        background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%);
        color: #742a2a;
    }
</style>

<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm form-card">
                <div class="form-card-header d-flex justify-content-between align-items-center">
                    <h5>
                        <i class="bi bi-pencil-square me-2"></i>Edit Timetable Entry
                    </h5>
                    <a href="{{ route('academic.timetable.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('academic.timetable.update', $timetable) }}" id="timetableForm">
                        @csrf
                        @method('PUT')

                        <!-- Division Selection -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-people"></i>
                                Division Selection
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="division_id" class="form-label">
                                            Division <span class="required-mark">*</span>
                                        </label>
                                        <select name="division_id" id="division_id" class="form-select" required onchange="checkSlotAvailability()">
                                            <option value="">Select Division</option>
                                            @foreach($divisions as $division)
                                                <option value="{{ $division->id }}" {{ $timetable->division_id == $division->id ? 'selected' : '' }}>
                                                    {{ $division->academicYear->name ?? 'N/A' }} - {{ $division->division_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Class Details -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-book"></i>
                                Class Details
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="subject_id" class="form-label">
                                            Subject <span class="required-mark">*</span>
                                        </label>
                                        <select name="subject_id" id="subject_id" class="form-select" required>
                                            <option value="">Select Subject</option>
                                            @foreach($subjects as $subject)
                                                <option value="{{ $subject->id }}" {{ $timetable->subject_id == $subject->id ? 'selected' : '' }}>
                                                    {{ $subject->name }} ({{ $subject->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="teacher_id" class="form-label">
                                            Teacher <span class="required-mark">*</span>
                                        </label>
                                        <select name="teacher_id" id="teacher_id" class="form-select" required>
                                            <option value="">Select Teacher</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}" {{ $timetable->teacher_id == $teacher->id ? 'selected' : '' }}>
                                                    {{ $teacher->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule Details -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-clock"></i>
                                Schedule Details
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="day_of_week" class="form-label">
                                            Day of Week <span class="required-mark">*</span>
                                        </label>
                                        <select name="day_of_week" id="day_of_week" class="form-select" required onchange="checkSlotAvailability()">
                                            <option value="">Select Day</option>
                                            <option value="Monday" {{ $timetable->day_of_week == 'Monday' ? 'selected' : '' }}>Monday</option>
                                            <option value="Tuesday" {{ $timetable->day_of_week == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                                            <option value="Wednesday" {{ $timetable->day_of_week == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                                            <option value="Thursday" {{ $timetable->day_of_week == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                                            <option value="Friday" {{ $timetable->day_of_week == 'Friday' ? 'selected' : '' }}>Friday</option>
                                            <option value="Saturday" {{ $timetable->day_of_week == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="start_time" class="form-label">
                                            Start Time <span class="required-mark">*</span>
                                        </label>
                                        <input type="time" name="start_time" id="start_time" class="form-control"
                                               value="{{ substr($timetable->start_time, 0, 5) }}" required onchange="checkSlotAvailability()">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="end_time" class="form-label">
                                            End Time <span class="required-mark">*</span>
                                        </label>
                                        <input type="time" name="end_time" id="end_time" class="form-control"
                                               value="{{ substr($timetable->end_time, 0, 5) }}" required onchange="checkSlotAvailability()">
                                        <div id="timeConflict" class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Room Allocation -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-geo-alt"></i>
                                Room Allocation
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="room" class="form-label">Room Number</label>
                                        <select name="room" id="room" class="form-select">
                                            <option value="">Select Room (Optional)</option>
                                            @foreach($rooms as $room)
                                                <option value="{{ $room }}" {{ $timetable->room == $room ? 'selected' : '' }}>{{ $room }}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">Select from available rooms or enter custom room</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="custom_room" class="form-label">Custom Room</label>
                                        <input type="text" name="custom_room" id="custom_room" class="form-control"
                                               placeholder="Enter custom room number" value="{{ $timetable->room && !in_array($timetable->room, $rooms) ? $timetable->room : '' }}">
                                        <div class="form-text">Leave room dropdown empty if using custom room</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slot Availability Status -->
                        <div id="slotStatus" class="alert d-none">
                            <i class="bi bi-info-circle me-2"></i>
                            <span id="slotStatusMessage">Checking availability...</span>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-warning" id="submitBtn">
                                <i class="bi bi-check-lg me-1"></i>Update Entry
                            </button>
                            <a href="{{ route('academic.timetable.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-1"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Check slot availability via AJAX
function checkSlotAvailability() {
    const divisionId = document.getElementById('division_id').value;
    const dayOfWeek = document.getElementById('day_of_week').value;
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;
    const slotStatus = document.getElementById('slotStatus');
    const slotStatusMessage = document.getElementById('slotStatusMessage');
    const timeConflict = document.getElementById('timeConflict');
    const submitBtn = document.getElementById('submitBtn');

    if (!divisionId || !dayOfWeek || !startTime || !endTime) {
        slotStatus.classList.add('d-none');
        return;
    }

    fetch("{{ route('academic.timetable.check-availability') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            division_id: divisionId,
            day_of_week: dayOfWeek,
            start_time: startTime,
            end_time: endTime
        })
    })
    .then(response => response.json())
    .then(data => {
        slotStatus.classList.remove('d-none', 'slot-available', 'slot-conflict');

        if (data.available) {
            slotStatus.classList.add('slot-available');
            slotStatusMessage.innerHTML = '<i class="bi bi-check-circle me-2"></i>' + data.message;
            timeConflict.textContent = '';
            submitBtn.disabled = false;
        } else {
            slotStatus.classList.add('slot-conflict');
            slotStatusMessage.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>' + data.message;
            timeConflict.textContent = data.message;
            submitBtn.disabled = true;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        slotStatus.classList.remove('d-none');
        slotStatus.classList.add('alert-warning');
        slotStatusMessage.textContent = 'Unable to check availability. Please proceed with caution.';
    });
}

// Handle custom room input
document.getElementById('custom_room').addEventListener('input', function() {
    const roomSelect = document.getElementById('room');
    if (this.value) {
        roomSelect.value = '';
    }
});

document.getElementById('room').addEventListener('change', function() {
    if (this.value) {
        document.getElementById('custom_room').value = '';
    }
});

document.getElementById('timetableForm').addEventListener('submit', function(e) {
    const room = document.getElementById('room').value;
    const customRoom = document.getElementById('custom_room').value;

    if (customRoom && !room) {
        document.getElementById('room').value = customRoom;
    }
});

// Check availability on page load
document.addEventListener('DOMContentLoaded', function() {
    checkSlotAvailability();
});
</script>
@endsection
