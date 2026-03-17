@extends('layouts.teacher')

@section('title', 'Mark Attendance')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-calendar-check me-2 text-success"></i>Mark Attendance</h2>
                    <p class="text-muted mb-0">Mark attendance for your students</p>
                </div>
                <a href="{{ route('teacher.attendance.history', $divisionId) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-clock-history me-1"></i>View History
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('teacher.attendance.store', $divisionId) }}" method="POST">
        @csrf
        
        <!-- Selection Form -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-funnel me-2 text-primary"></i>Select Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="division_id" class="form-label">Division <span class="text-danger">*</span></label>
                        <select name="division_id" id="division_id" class="form-select @error('division_id') is-invalid @enderror" required onchange="this.form.submit()">
                            <option value="">-- Select Division --</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ old('division_id', request('division_id')) == $division->id ? 'selected' : '' }}>
                                    {{ $division->division_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('division_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" id="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required onchange="this.form.submit()">
                            <option value="">-- Select Subject --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', request('subject_id')) == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="attendance_date" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="attendance_date" id="attendance_date" 
                               class="form-control @error('attendance_date') is-invalid @enderror" 
                               value="{{ old('attendance_date', request('date', today()->format('Y-m-d'))) }}" 
                               max="{{ today()->format('Y-m-d') }}" required onchange="checkHoliday()">
                        @error('attendance_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="holiday-warning" class="form-text text-danger mt-2" style="display: none;"></div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100" id="loadStudentsBtn" disabled>
                            <i class="bi bi-search me-1"></i>Load Students
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if($students->count() > 0 && $selectedDivision && !$isHoliday)
            <!-- Student List -->
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-people me-2 text-primary"></i>
                            Students - {{ $selectedDivision->division_name }}
                        </h5>
                        <div>
                            <button type="button" class="btn btn-success btn-sm me-2" onclick="markAllPresent()">
                                <i class="bi bi-check-circle me-1"></i>Mark All Present
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-save me-1"></i>Save Attendance
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="80">Roll No</th>
                                    <th>Student Name</th>
                                    <th width="150" class="text-center">Present</th>
                                    <th width="150" class="text-center">Absent</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $index => $student)
                                    @php
                                        $existing = $existingAttendance->get($student->id);
                                        $status = $existing ? $existing->status : 'present';
                                    @endphp
                                    <tr class="{{ $existing ? 'table-success' : '' }}">
                                        <td class="align-middle">
                                            <span class="badge bg-primary">{{ $student->roll_number ?? ($index + 1) }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle me-2 bg-primary bg-gradient d-flex align-items-center justify-content-center text-white fw-bold"
                                                     style="width: 40px; height: 40px; min-width: 40px;">
                                                    {{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $student->full_name ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $student->user->email ?? 'No email' }}</small>
                                                    @if($existing)
                                                        <small class="text-success d-block">
                                                            <i class="bi bi-check-circle me-1"></i>Already marked ({{ $existing->status }})
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <input type="radio"
                                                   name="attendance[{{ $index }}][student_id]"
                                                   value="{{ $student->id }}"
                                                   class="btn-check"
                                                   id="present_{{ $student->id }}"
                                                   {{ $status === 'present' ? 'checked' : '' }}
                                                   required>
                                            <label class="btn btn-outline-success btn-sm" for="present_{{ $student->id }}">
                                                <i class="bi bi-check-circle"></i> Present
                                            </label>
                                        </td>
                                        <td class="text-center align-middle">
                                            <input type="radio"
                                                   name="attendance[{{ $index }}][student_id]"
                                                   value="{{ $student->id }}"
                                                   class="btn-check"
                                                   id="absent_{{ $student->id }}"
                                                   {{ $status === 'absent' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger btn-sm" for="absent_{{ $student->id }}">
                                                <i class="bi bi-x-circle"></i> Absent
                                            </label>
                                        </td>
                                        <td class="align-middle">
                                            <input type="text"
                                                   name="attendance[{{ $index }}][remarks]"
                                                   class="form-control form-control-sm"
                                                   placeholder="Optional remarks"
                                                   value="{{ $existing->remarks ?? '' }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Total Students: {{ $students->count() }}
                        </small>
                        <div>
                            <a href="{{ route('teacher.attendance.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Save Attendance
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </form>
</div>

@push('scripts')
<script>
function markAllPresent() {
    document.querySelectorAll('input[id^="present_"]').forEach(radio => {
        radio.checked = true;
    });
}

function checkHoliday() {
    const dateInput = document.getElementById('attendance_date');
    const date = dateInput.value;
    const warningDiv = document.getElementById('holiday-warning');
    const loadBtn = document.getElementById('loadStudentsBtn');
    
    if (!date) {
        warningDiv.style.display = 'none';
        loadBtn.disabled = true;
        return;
    }
    
    fetch("{{ route('holidays.check-date') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ date: date })
    })
    .then(response => response.json())
    .then(data => {
        if (data.is_holiday) {
            warningDiv.textContent = '⚠️ ' + data.message;
            warningDiv.style.display = 'block';
            loadBtn.disabled = true;
        } else {
            warningDiv.style.display = 'none';
            loadBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        loadBtn.disabled = false;
    });
}

// Auto-submit form when selection changes
document.querySelectorAll('select').forEach(element => {
    element.addEventListener('change', function() {
        // Form will submit via onchange in HTML
    });
});
</script>
@endpush
@endsection
