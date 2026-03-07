@extends('layouts.app')

@section('title', 'Timetable Management - Principal Dashboard')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-calendar-week me-2"></i>Timetable Management
            </h2>
            <p class="text-muted mb-0">Manage class schedules and timetables</p>
        </div>
        <div>
            <a href="{{ route('dashboard.principal') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('principal.timetable.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Division</label>
                    <select name="division_id" class="form-select">
                        <option value="">All Divisions</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                {{ $division->division_name }} - {{ $division->program->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Teacher</label>
                    <select name="teacher_id" class="form-select">
                        <option value="">All Teachers</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Subject</label>
                    <select name="subject_id" class="form-select">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-2"></i>Filter
                    </button>
                    <a href="{{ route('principal.timetable.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
                <i class="bi bi-plus-circle me-2"></i>Add Class
            </button>
        </div>
        <div class="text-muted">
            <i class="bi bi-info-circle me-1"></i>
            Total: {{ $timetables->total() }} entries
        </div>
    </div>

    <!-- Timetable Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th width="60">Actions</th>
                            <th width="60">ID</th>
                            <th>Division</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Room</th>
                            <th>Academic Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($timetables as $timetable)
                            <tr>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary btn-edit-timetable"
                                                data-id="{{ $timetable->id }}"
                                                data-division_id="{{ $timetable->division_id }}"
                                                data-subject_id="{{ $timetable->subject_id }}"
                                                data-teacher_id="{{ $timetable->teacher_id }}"
                                                data-day_of_week="{{ $timetable->day_of_week }}"
                                                data-date="{{ $timetable->date?->format('Y-m-d') }}"
                                                data-start_time="{{ $timetable->start_time }}"
                                                data-end_time="{{ $timetable->end_time }}"
                                                data-room_number="{{ $timetable->room_number }}"
                                                data-academic_year_id="{{ $timetable->academic_year_id }}"
                                                title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-delete-timetable"
                                                data-id="{{ $timetable->id }}"
                                                data-name="{{ $timetable->subject->name ?? 'Class' }} on {{ $timetable->date?->format('d-m-Y') }}"
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>{{ $timetable->id }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $timetable->division->division_name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <strong>{{ $timetable->subject->name ?? 'N/A' }}</strong>
                                    <br><small class="text-muted">{{ $timetable->subject->code ?? '' }}</small>
                                </td>
                                <td>{{ $timetable->teacher->name ?? 'No Teacher' }}</td>
                                <td>
                                    @if($timetable->date)
                                        {{ $timetable->date->format('d-m-Y') }}
                                        <br><small class="text-muted">{{ ucfirst($timetable->day_of_week) }}</small>
                                    @else
                                        <span class="text-muted">Recurring</span>
                                    @endif
                                </td>
                                <td>
                                    {{ substr($timetable->start_time, 0, 5) }} - {{ substr($timetable->end_time, 0, 5) }}
                                </td>
                                <td>{{ $timetable->room_number ?? 'TBA' }}</td>
                                <td>{{ $timetable->academicYear->name ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2 mb-0">No timetable entries found</p>
                                    <button class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addClassModal">
                                        <i class="bi bi-plus-circle me-1"></i>Add First Class
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($timetables->hasPages())
                <div class="mt-3">
                    {{ $timetables->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Class Modal -->
<div class="modal fade" id="addClassModal" tabindex="-1" aria-labelledby="addClassModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addClassModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Add Class to Timetable
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addClassForm" method="POST" action="{{ route('principal.timetable.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Division -->
                        <div class="col-md-6">
                            <label for="addDivisionId" class="form-label">
                                <i class="bi bi-building me-1"></i>Division <span class="text-danger">*</span>
                            </label>
                            <select name="division_id" id="addDivisionId" class="form-select @error('division_id') is-invalid @enderror" required>
                                <option value="">Choose a division...</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}">
                                        {{ $division->division_name }} - {{ $division->program->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('division_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Subject -->
                        <div class="col-md-6">
                            <label for="addSubjectId" class="form-label">
                                <i class="bi bi-book me-1"></i>Subject <span class="text-danger">*</span>
                            </label>
                            <select name="subject_id" id="addSubjectId" class="form-select @error('subject_id') is-invalid @enderror" required>
                                <option value="">Choose a subject...</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">
                                        {{ $subject->code }} - {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Teacher -->
                        <div class="col-md-6">
                            <label for="addTeacherId" class="form-label">
                                <i class="bi bi-person me-1"></i>Teacher <span class="text-danger">*</span>
                            </label>
                            <select name="teacher_id" id="addTeacherId" class="form-select @error('teacher_id') is-invalid @enderror" required>
                                <option value="">Choose a teacher...</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Day of Week -->
                        <div class="col-md-6">
                            <label for="addDayOfWeek" class="form-label">
                                <i class="bi bi-calendar3 me-1"></i>Day of Week <span class="text-danger">*</span>
                            </label>
                            <select name="day_of_week" id="addDayOfWeek" class="form-select @error('day_of_week') is-invalid @enderror" required>
                                <option value="">Select a day...</option>
                                <option value="monday">Monday</option>
                                <option value="tuesday">Tuesday</option>
                                <option value="wednesday">Wednesday</option>
                                <option value="thursday">Thursday</option>
                                <option value="friday">Friday</option>
                                <option value="saturday">Saturday</option>
                            </select>
                            @error('day_of_week')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date -->
                        <div class="col-md-6">
                            <label for="addDate" class="form-label">
                                <i class="bi bi-calendar-event me-1"></i>Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   name="date" 
                                   id="addDate" 
                                   class="form-control @error('date') is-invalid @enderror" 
                                   value="{{ old('date') }}" 
                                   required
                                   min="{{ date('Y-m-d') }}">
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="holidayWarningAdd" class="alert alert-warning mt-2 d-none">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <span id="holidayWarningAddText"></span>
                            </div>
                        </div>

                        <!-- Start Time -->
                        <div class="col-md-6">
                            <label for="addStartTime" class="form-label">
                                <i class="bi bi-clock me-1"></i>Start Time <span class="text-danger">*</span>
                            </label>
                            <input type="time" 
                                   name="start_time" 
                                   id="addStartTime" 
                                   class="form-control @error('start_time') is-invalid @enderror" 
                                   value="{{ old('start_time') }}" 
                                   required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- End Time -->
                        <div class="col-md-6">
                            <label for="addEndTime" class="form-label">
                                <i class="bi bi-clock me-1"></i>End Time <span class="text-danger">*</span>
                            </label>
                            <input type="time" 
                                   name="end_time" 
                                   id="addEndTime" 
                                   class="form-control @error('end_time') is-invalid @enderror" 
                                   value="{{ old('end_time') }}" 
                                   required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Room Number -->
                        <div class="col-md-6">
                            <label for="addRoomNumber" class="form-label">
                                <i class="bi bi-geo-alt me-1"></i>Room Number
                            </label>
                            <input type="text" 
                                   name="room_number" 
                                   id="addRoomNumber" 
                                   class="form-control" 
                                   value="{{ old('room_number') }}" 
                                   placeholder="e.g., Room 101">
                        </div>

                        <!-- Academic Year -->
                        <div class="col-md-6">
                            <label for="addAcademicYearId" class="form-label">
                                <i class="bi bi-calendar-range me-1"></i>Academic Year <span class="text-danger">*</span>
                            </label>
                            <select name="academic_year_id" id="addAcademicYearId" class="form-select" required>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $year->is_active ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Conflict Warning -->
                    <div id="conflictWarning" class="alert alert-danger mt-3 d-none">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Schedule Conflict Detected!</strong>
                        <ul id="conflictList" class="mb-0 mt-2"></ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="addSubmitBtn">
                        <i class="bi bi-check-circle"></i> Add Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Class Modal -->
<div class="modal fade" id="editClassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>Edit Class
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editClassForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="id" id="editTimetableId">
                    
                    <div class="row g-3">
                        <!-- Division -->
                        <div class="col-md-6">
                            <label for="editDivisionId" class="form-label">
                                <i class="bi bi-building me-1"></i>Division <span class="text-danger">*</span>
                            </label>
                            <select name="division_id" id="editDivisionId" class="form-select" required>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}">
                                        {{ $division->division_name }} - {{ $division->program->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subject -->
                        <div class="col-md-6">
                            <label for="editSubjectId" class="form-label">
                                <i class="bi bi-book me-1"></i>Subject <span class="text-danger">*</span>
                            </label>
                            <select name="subject_id" id="editSubjectId" class="form-select" required>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">
                                        {{ $subject->code }} - {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Teacher -->
                        <div class="col-md-6">
                            <label for="editTeacherId" class="form-label">
                                <i class="bi bi-person me-1"></i>Teacher <span class="text-danger">*</span>
                            </label>
                            <select name="teacher_id" id="editTeacherId" class="form-select" required>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Day of Week -->
                        <div class="col-md-6">
                            <label for="editDayOfWeek" class="form-label">
                                <i class="bi bi-calendar3 me-1"></i>Day of Week <span class="text-danger">*</span>
                            </label>
                            <select name="day_of_week" id="editDayOfWeek" class="form-select" required>
                                <option value="monday">Monday</option>
                                <option value="tuesday">Tuesday</option>
                                <option value="wednesday">Wednesday</option>
                                <option value="thursday">Thursday</option>
                                <option value="friday">Friday</option>
                                <option value="saturday">Saturday</option>
                            </select>
                        </div>

                        <!-- Date -->
                        <div class="col-md-6">
                            <label for="editDate" class="form-label">
                                <i class="bi bi-calendar-event me-1"></i>Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   name="date" 
                                   id="editDate" 
                                   class="form-control" 
                                   required
                                   min="{{ date('Y-m-d') }}">
                            <div id="holidayWarningEdit" class="alert alert-warning mt-2 d-none">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <span id="holidayWarningEditText"></span>
                            </div>
                        </div>

                        <!-- Start Time -->
                        <div class="col-md-6">
                            <label for="editStartTime" class="form-label">
                                <i class="bi bi-clock me-1"></i>Start Time <span class="text-danger">*</span>
                            </label>
                            <input type="time" 
                                   name="start_time" 
                                   id="editStartTime" 
                                   class="form-control" 
                                   required>
                        </div>

                        <!-- End Time -->
                        <div class="col-md-6">
                            <label for="editEndTime" class="form-label">
                                <i class="bi bi-clock me-1"></i>End Time <span class="text-danger">*</span>
                            </label>
                            <input type="time" 
                                   name="end_time" 
                                   id="editEndTime" 
                                   class="form-control" 
                                   required>
                        </div>

                        <!-- Room Number -->
                        <div class="col-md-6">
                            <label for="editRoomNumber" class="form-label">
                                <i class="bi bi-geo-alt me-1"></i>Room Number
                            </label>
                            <input type="text" 
                                   name="room_number" 
                                   id="editRoomNumber" 
                                   class="form-control" 
                                   placeholder="e.g., Room 101">
                        </div>

                        <!-- Academic Year -->
                        <div class="col-md-6">
                            <label for="editAcademicYearId" class="form-label">
                                <i class="bi bi-calendar-range me-1"></i>Academic Year <span class="text-danger">*</span>
                            </label>
                            <select name="academic_year_id" id="editAcademicYearId" class="form-select" required>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Conflict Warning -->
                    <div id="editConflictWarning" class="alert alert-danger mt-3 d-none">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Schedule Conflict Detected!</strong>
                        <ul id="editConflictList" class="mb-0 mt-2"></ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-warning" id="editSubmitBtn">
                        <i class="bi bi-check-circle"></i> Update Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteTimetableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this class?</p>
                <p class="mb-0"><strong id="deleteTimetableName"></strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <form id="deleteTimetableForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit button click handler
    document.querySelectorAll('.btn-edit-timetable').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const divisionId = this.dataset.division_id;
            const subjectId = this.dataset.subject_id;
            const teacherId = this.dataset.teacher_id;
            const dayOfWeek = this.dataset.day_of_week;
            const date = this.dataset.date;
            const startTime = this.dataset.start_time;
            const endTime = this.dataset.end_time;
            const roomNumber = this.dataset.room_number;
            const academicYearId = this.dataset.academic_year_id;
            
            document.getElementById('editTimetableId').value = id;
            document.getElementById('editDivisionId').value = divisionId;
            document.getElementById('editSubjectId').value = subjectId;
            document.getElementById('editTeacherId').value = teacherId;
            document.getElementById('editDayOfWeek').value = dayOfWeek;
            document.getElementById('editDate').value = date;
            document.getElementById('editStartTime').value = startTime ? startTime.substring(0, 5) : '';
            document.getElementById('editEndTime').value = endTime ? endTime.substring(0, 5) : '';
            document.getElementById('editRoomNumber').value = roomNumber || '';
            document.getElementById('editAcademicYearId').value = academicYearId;
            
            // Set form action
            document.getElementById('editClassForm').action = '{{ route("principal.timetable.update", "") }}/' + id;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editClassModal'));
            modal.show();
        });
    });
    
    // Delete button click handler
    document.querySelectorAll('.btn-delete-timetable').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            
            document.getElementById('deleteTimetableName').textContent = name;
            document.getElementById('deleteTimetableForm').action = '{{ route("principal.timetable.delete", "") }}/' + id;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteTimetableModal'));
            modal.show();
        });
    });
    
    // Holiday check for Add modal
    const addDateInput = document.getElementById('addDate');
    const addHolidayWarning = document.getElementById('holidayWarningAdd');
    const addHolidayWarningText = document.getElementById('holidayWarningAddText');
    const addSubmitBtn = document.getElementById('addSubmitBtn');
    
    if (addDateInput) {
        addDateInput.addEventListener('change', function() {
            const date = this.value;
            if (date) {
                checkHoliday(date, addHolidayWarning, addHolidayWarningText, addSubmitBtn);
            } else {
                addHolidayWarning.classList.add('d-none');
                addSubmitBtn.disabled = false;
            }
        });
    }
    
    // Holiday check for Edit modal
    const editDateInput = document.getElementById('editDate');
    const editHolidayWarning = document.getElementById('holidayWarningEdit');
    const editHolidayWarningText = document.getElementById('holidayWarningEditText');
    const editSubmitBtn = document.getElementById('editSubmitBtn');
    
    if (editDateInput) {
        editDateInput.addEventListener('change', function() {
            const date = this.value;
            if (date) {
                checkHoliday(date, editHolidayWarning, editHolidayWarningText, editSubmitBtn);
            } else {
                editHolidayWarning.classList.add('d-none');
                editSubmitBtn.disabled = false;
            }
        });
    }
    
    function checkHoliday(date, warningEl, warningTextEl, submitBtn) {
        fetch("{{ route('academic.timetable.ajax.check-holiday') }}?date=" + date)
            .then(response => response.json())
            .then(data => {
                if (data.is_holiday) {
                    warningTextEl.textContent = data.holiday_title || 'Cannot create timetable on holiday';
                    warningEl.classList.remove('d-none');
                    if (submitBtn) submitBtn.disabled = true;
                } else {
                    warningEl.classList.add('d-none');
                    if (submitBtn) submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Holiday check error:', error);
            });
    }
    
    // Time validation
    const addStartTime = document.getElementById('addStartTime');
    const addEndTime = document.getElementById('addEndTime');
    
    if (addStartTime && addEndTime) {
        addEndTime.addEventListener('change', function() {
            if (addStartTime.value && this.value && this.value <= addStartTime.value) {
                alert('End time must be after start time');
                this.value = '';
            }
        });
    }
    
    const editStartTime = document.getElementById('editStartTime');
    const editEndTime = document.getElementById('editEndTime');
    
    if (editStartTime && editEndTime) {
        editEndTime.addEventListener('change', function() {
            if (editStartTime.value && this.value && this.value <= editStartTime.value) {
                alert('End time must be after start time');
                this.value = '';
            }
        });
    }
    
    // Form validation before submit
    document.getElementById('addClassForm').addEventListener('submit', function(e) {
        const startTime = addStartTime.value;
        const endTime = addEndTime.value;
        
        if (startTime && endTime && endTime <= startTime) {
            e.preventDefault();
            alert('End time must be after start time');
            return false;
        }
        
        if (addHolidayWarning && !addHolidayWarning.classList.contains('d-none')) {
            e.preventDefault();
            alert('Cannot create timetable on holiday');
            return false;
        }
    });
    
    document.getElementById('editClassForm').addEventListener('submit', function(e) {
        const startTime = editStartTime.value;
        const endTime = editEndTime.value;
        
        if (startTime && endTime && endTime <= startTime) {
            e.preventDefault();
            alert('End time must be after start time');
            return false;
        }
        
        if (editHolidayWarning && !editHolidayWarning.classList.contains('d-none')) {
            e.preventDefault();
            alert('Cannot update timetable on holiday');
            return false;
        }
    });
});
</script>
@endpush
