@extends('layouts.app')

@section('page-title', 'Create Timetable Entry')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-plus me-2"></i>Create Timetable Entry
                    </h5>
                    <a href="{{ route('academic.timetable.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('academic.timetable.store') }}" id="timetableForm">
                        @csrf
                        
                        <!-- Master Data Selection -->
                        <h6 class="mb-3 text-primary"><i class="bi bi-layers me-2"></i>Master Data Selection</h6>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="department_filter" class="form-label">Department</label>
                                    <select id="department_filter" class="form-select" onchange="filterDivisions()">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="program_filter" class="form-label">Program</label>
                                    <select id="program_filter" class="form-select" onchange="filterDivisions()">
                                        <option value="">All Programs</option>
                                        @foreach($programs as $prog)
                                            <option value="{{ $prog->id }}">{{ $prog->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="division_id" class="form-label">Division <span class="text-danger">*</span></label>
                                    <select name="division_id" id="division_id" class="form-select" required onchange="checkSlotAvailability()">
                                        <option value="">Select Division</option>
                                        @foreach($divisions as $division)
                                            <option value="{{ $division->id }}" 
                                                    data-dept="{{ $division->program->department_id ?? '' }}"
                                                    data-program="{{ $division->program_id ?? '' }}">
                                                {{ $division->academicYear->name ?? 'N/A' }} - {{ $division->division_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Select department and program to filter divisions</div>
                                </div>
                            </div>
                        </div>

                        <!-- Class Details -->
                        <h6 class="mb-3 text-primary"><i class="bi bi-book me-2"></i>Class Details</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                                    <select name="subject_id" id="subject_id" class="form-select" required onchange="checkSlotAvailability()">
                                        <option value="">Select Subject</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}">
                                                {{ $subject->name }} ({{ $subject->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="teacher_id" class="form-label">Teacher <span class="text-danger">*</span></label>
                                    <select name="teacher_id" id="teacher_id" class="form-select" required>
                                        <option value="">Select Teacher</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}">
                                                {{ $teacher->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule Details -->
                        <h6 class="mb-3 text-primary"><i class="bi bi-clock me-2"></i>Schedule Details</h6>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="day_of_week" class="form-label">Day of Week <span class="text-danger">*</span></label>
                                    <select name="day_of_week" id="day_of_week" class="form-select" required onchange="checkSlotAvailability()">
                                        <option value="">Select Day</option>
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                        <option value="Saturday">Saturday</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                                    <input type="time" name="start_time" id="start_time" class="form-control" required onchange="checkSlotAvailability()">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                                    <input type="time" name="end_time" id="end_time" class="form-control" required onchange="checkSlotAvailability()">
                                    <div id="timeConflict" class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Room Allocation -->
                        <h6 class="mb-3 text-primary"><i class="bi bi-door-open me-2"></i>Room Allocation</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="room" class="form-label">Room Number</label>
                                    <select name="room" id="room" class="form-select">
                                        <option value="">Select Room (Optional)</option>
                                        @foreach($rooms as $room)
                                            <option value="{{ $room }}">{{ $room }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Select from available rooms or enter custom room</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="custom_room" class="form-label">Custom Room</label>
                                    <input type="text" name="custom_room" id="custom_room" class="form-control" placeholder="Enter custom room number">
                                    <div class="form-text">Leave room dropdown empty if using custom room</div>
                                </div>
                            </div>
                        </div>

                        <!-- Slot Availability Status -->
                        <div id="slotStatus" class="alert alert-secondary d-none">
                            <i class="bi bi-info-circle me-2"></i>
                            <span id="slotStatusMessage">Checking availability...</span>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-check-lg me-1"></i>Create Entry
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
// Filter divisions based on department and program
function filterDivisions() {
    const deptId = document.getElementById('department_filter').value;
    const progId = document.getElementById('program_filter').value;
    const divisionSelect = document.getElementById('division_id');
    const options = divisionSelect.querySelectorAll('option');

    options.forEach(option => {
        if (option.value === '') {
            option.style.display = '';
            return;
        }

        const optionDept = option.getAttribute('data-dept');
        const optionProg = option.getAttribute('data-program');
        
        let show = true;
        
        if (deptId && optionDept !== deptId) show = false;
        if (progId && optionProg !== progId) show = false;
        
        option.style.display = show ? '' : 'none';
        
        if (!show && option.selected) {
            option.selected = false;
        }
    });
}

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

    // Check if all required fields are filled
    if (!divisionId || !dayOfWeek || !startTime || !endTime) {
        slotStatus.classList.add('d-none');
        return;
    }

    // Fetch availability
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
        slotStatus.classList.remove('d-none', 'alert-success', 'alert-danger');
        
        if (data.available) {
            slotStatus.classList.add('alert-success');
            slotStatusMessage.innerHTML = '<i class="bi bi-check-circle me-2"></i>' + data.message;
            timeConflict.textContent = '';
            submitBtn.disabled = false;
        } else {
            slotStatus.classList.add('alert-danger');
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

// Update room select when custom room is empty
document.getElementById('room').addEventListener('change', function() {
    if (this.value) {
        document.getElementById('custom_room').value = '';
    }
});

// Form validation before submit
document.getElementById('timetableForm').addEventListener('submit', function(e) {
    const room = document.getElementById('room').value;
    const customRoom = document.getElementById('custom_room').value;
    
    // Set the room value to custom room if provided
    if (customRoom && !room) {
        document.getElementById('room').value = customRoom;
    }
});
</script>
@endsection
