@extends('layouts.app')

@section('title', 'Principal Dashboard')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Welcome Header -->
    <div class="principal-welcome-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="principal-avatar-wrapper me-3">
                        <div class="principal-avatar">
                            <i class="bi bi-person-badge"></i>
                        </div>
                    </div>
                    <div>
                        <h2 class="mb-1 fw-bold text-white">
                            Welcome back, {{ auth()->user()->name }}! 👋
                        </h2>
                        <p class="mb-0 text-white-50">
                            <i class="bi bi-shield-check me-1"></i>Principal Dashboard
                            <span class="mx-2">•</span>
                            <i class="bi bi-calendar me-1"></i>{{ now()->format('l, F j, Y') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="d-flex gap-2 justify-content-lg-end">
                    <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#assignDivisionModal">
                        <i class="bi bi-person-plus me-2"></i>Assign Division
                    </button>
                    <button class="btn btn-outline-light" id="refreshDashboard">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards - Row 1 -->
    <div class="row g-4 mb-4 justify-content-center">
        <div class="col-xl-4">
            <div class="principal-stats-card principal-stats-blue text-center h-100">
                <div class="stats-card-body py-4">
                    <i class="bi bi-people-fill fs-2 mb-2 d-block" style="color: rgba(255,255,255,0.9);"></i>
                    <p class="stats-label mb-1">Total Students</p>
                    <h2 class="stats-value mb-1">{{ number_format($totalStudents) }}</h2>
                    <p class="stats-change mb-0"><small>Active Students</small></p>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="principal-stats-card principal-stats-green text-center h-100">
                <div class="stats-card-body py-4">
                    <i class="bi bi-person-badge-fill fs-2 mb-2 d-block" style="color: rgba(255,255,255,0.9);"></i>
                    <p class="stats-label mb-1">Total Teachers</p>
                    <h2 class="stats-value mb-1">{{ number_format($totalTeachers) }}</h2>
                    <p class="stats-change mb-0"><small>Active Teachers</small></p>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="principal-stats-card principal-stats-red text-center h-100">
                <div class="stats-card-body py-4">
                    <i class="bi bi-building-fill fs-2 mb-2 d-block" style="color: rgba(255,255,255,0.9);"></i>
                    <p class="stats-label mb-1">Total Departments</p>
                    <h2 class="stats-value mb-1">{{ number_format($totalDepartments) }}</h2>
                    <p class="stats-change mb-0"><small>Departments</small></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards - Row 2 -->
    <div class="row g-4 mb-4 justify-content-center">
        <div class="col-xl-4">
            <div class="principal-stats-card principal-stats-orange text-center h-100">
                <div class="stats-card-body py-4">
                    <i class="bi bi-mortarboard-fill fs-2 mb-2 d-block" style="color: rgba(255,255,255,0.9);"></i>
                    <p class="stats-label mb-1">Total Programs</p>
                    <h2 class="stats-value mb-1">{{ number_format($totalPrograms) }}</h2>
                    <p class="stats-change mb-0"><small>Academic Programs</small></p>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="principal-stats-card principal-stats-purple text-center h-100">
                <div class="stats-card-body py-4">
                    <i class="bi bi-book-fill fs-2 mb-2 d-block" style="color: rgba(255,255,255,0.9);"></i>
                    <p class="stats-label mb-1">Total Subjects</p>
                    <h2 class="stats-value mb-1">{{ number_format($totalSubjects) }}</h2>
                    <p class="stats-change mb-0"><small>Available Subjects</small></p>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="principal-stats-card principal-stats-teal text-center h-100">
                <div class="stats-card-body py-4">
                    <i class="bi bi-clipboard-check-fill fs-2 mb-2 d-block" style="color: rgba(255,255,255,0.9);"></i>
                    <p class="stats-label mb-1">Total Exams</p>
                    <h2 class="stats-value mb-1">{{ number_format($totalExaminations) }}</h2>
                    <p class="stats-change mb-0"><small>Scheduled Exams</small></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Attendance & Fee Overview -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="principal-card">
                        <div class="card-header-principal">
                            <h5 class="mb-0">
                                <i class="bi bi-calendar-check me-2"></i>Today's Attendance
                            </h5>
                            <span class="badge bg-success">{{ $attendancePercentage }}%</span>
                        </div>
                        <div class="card-body-principal">
                            <div class="attendance-summary">
                                <div class="attendance-item">
                                    <div class="attendance-icon bg-success-subtle">
                                        <i class="bi bi-check-circle text-success"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0 text-muted small">Present</p>
                                        <h5 class="mb-0 fw-bold">{{ $attendanceToday->present ?? 0 }}</h5>
                                    </div>
                                </div>
                                <div class="attendance-item">
                                    <div class="attendance-icon bg-danger-subtle">
                                        <i class="bi bi-x-circle text-danger"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0 text-muted small">Absent</p>
                                        <h5 class="mb-0 fw-bold">{{ $attendanceToday->absent ?? 0 }}</h5>
                                    </div>
                                </div>
                                <div class="attendance-item">
                                    <div class="attendance-icon bg-primary-subtle">
                                        <i class="bi bi-people text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0 text-muted small">Total</p>
                                        <h5 class="mb-0 fw-bold">{{ $attendanceToday->total ?? 0 }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="principal-card">
                        <div class="card-header-principal">
                            <h5 class="mb-0">
                                <i class="bi bi-cash-coin me-2"></i>Fee Collection
                            </h5>
                            <span class="badge bg-info">This Month</span>
                        </div>
                        <div class="card-body-principal">
                            <div class="fee-summary">
                                <div class="fee-item">
                                    <div class="fee-icon bg-success-subtle">
                                        <i class="bi bi-arrow-down-circle text-success"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0 text-muted small">Collected</p>
                                        <h5 class="mb-0 fw-bold text-success">₹{{ number_format($feeCollection->total_collected ?? 0, 2) }}</h5>
                                        <small class="text-muted">{{ $feeCollection->total_transactions ?? 0 }} transactions</small>
                                    </div>
                                </div>
                                <div class="fee-item mt-3">
                                    <div class="fee-icon bg-danger-subtle">
                                        <i class="bi bi-exclamation-circle text-danger"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0 text-muted small">Pending</p>
                                        <h5 class="mb-0 fw-bold text-danger">₹{{ number_format($pendingFees, 2) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timetable Management -->
            <div class="principal-card mb-4">
                <div class="card-header-principal d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-week me-2"></i>Timetable Management
                    </h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTimetableModal">
                        <i class="bi bi-plus-circle me-1"></i>Add Class
                    </button>
                </div>
                <div class="card-body-principal">
                    <!-- Division Selector -->
                    <form method="GET" action="{{ route('dashboard.principal') }}" class="mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Select Division</label>
                                <select name="division_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">-- Select Division --</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                            {{ $division->division_name }} - {{ $division->program->name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if($selectedDivision)
                                <div class="col-md-8">
                                    <p class="mb-0 text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Viewing timetable for <strong>{{ $selectedDivision->division_name }}</strong>
                                        <span class="mx-1">•</span>
                                        {{ $selectedDivision->timetables->count() }} classes scheduled
                                    </p>
                                </div>
                            @endif
                        </div>
                    </form>

                    @if($selectedDivision)
                        <!-- Timetable Grid -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="100">Time/Day</th>
                                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                            <th class="text-center">{{ ucfirst($day) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $timeSlots = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00'];
                                    @endphp
                                    @foreach($timeSlots as $time)
                                        <tr>
                                            <td class="fw-bold bg-light">{{ $time }}</td>
                                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                                <td style="min-width: 150px; vertical-align: top;">
                                                    @php
                                                        $dayClasses = $timetables[$day] ?? collect();
                                                        $matchingClass = $dayClasses->first(function($c) use ($time) {
                                                            return substr($c->start_time, 0, 5) === $time;
                                                        });
                                                    @endphp
                                                    @if($matchingClass)
                                                        <div class="p-2 bg-primary bg-opacity-10 rounded">
                                                            <strong class="text-primary">{{ $matchingClass->subject->code ?? 'N/A' }}</strong><br>
                                                            <small class="fw-semibold">{{ $matchingClass->subject->name ?? 'N/A' }}</small><br>
                                                            <small class="text-muted d-block">{{ $matchingClass->teacher->name ?? 'N/A' }}</small>
                                                            <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $matchingClass->room_number ?? 'N/A' }}</small>
                                                            <div class="mt-1">
                                                                <button type="button" 
                                                                        class="btn btn-sm btn-outline-primary py-0 px-1 btn-edit-timetable" 
                                                                        style="font-size: 0.75rem;"
                                                                        data-id="{{ $matchingClass->id }}"
                                                                        data-division_id="{{ $matchingClass->division_id }}"
                                                                        data-subject_id="{{ $matchingClass->subject_id }}"
                                                                        data-teacher_id="{{ $matchingClass->teacher_id }}"
                                                                        data-day_of_week="{{ $matchingClass->day_of_week }}"
                                                                        data-date="{{ $matchingClass->date ? $matchingClass->date->format('Y-m-d') : '' }}"
                                                                        data-start_time="{{ $matchingClass->start_time }}"
                                                                        data-end_time="{{ $matchingClass->end_time }}"
                                                                        data-room_number="{{ $matchingClass->room_number }}"
                                                                        data-academic_year_id="{{ $matchingClass->academic_year_id }}"
                                                                        title="Edit">
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>
                                                                <button type="button" 
                                                                        class="btn btn-sm btn-outline-danger py-0 px-1 btn-delete-timetable" 
                                                                        style="font-size: 0.75rem;"
                                                                        data-id="{{ $matchingClass->id }}"
                                                                        data-name="{{ $matchingClass->subject->name ?? 'Class' }} on {{ $matchingClass->day_of_week }}">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($selectedDivision->timetables->isEmpty())
                            <div class="text-center py-4">
                                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2 mb-0">No classes scheduled for this division</p>
                                <button class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addTimetableModal">
                                    <i class="bi bi-plus-circle me-1"></i>Add First Class
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-hand-index-thumb text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2 mb-0">Select a division to view its timetable</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="principal-card mb-4">
                <div class="card-header-principal">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning-charge me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body-principal p-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('dashboard.students.index') }}#create" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-person-plus me-1"></i>Add Student
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('dashboard.teachers.index') }}#create" class="btn btn-success w-100 py-2">
                                <i class="bi bi-person-badge me-1"></i>Add Teacher
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('web.departments.create') }}" class="btn btn-info w-100 py-2">
                                <i class="bi bi-building me-1"></i>Add Department
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('academic.programs.create') }}" class="btn btn-warning w-100 py-2">
                                <i class="bi bi-mortarboard me-1"></i>Add Program
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('academic.subjects.create') }}" class="btn btn-secondary w-100 py-2">
                                <i class="bi bi-book me-1"></i>Add Subject
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('examinations.create') }}" class="btn btn-danger w-100 py-2">
                                <i class="bi bi-clipboard-plus me-1"></i>Create Exam
                            </a>
                        </div>
                        <div class="col-12">
                            <a href="{{ route('principal.results') }}" class="btn btn-dark w-100 py-2">
                                <i class="bi bi-graph-up me-1"></i>View Results
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
                                <small class="text-muted">Generate and view reports</small>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="principal-card">
                <div class="card-header-principal">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>Recent Activities
                    </h5>
                </div>
                <div class="card-body-principal">
                    <div class="activity-timeline">
                        @forelse($recentActivities as $activity)
                            <div class="activity-item">
                                <div class="activity-icon-wrapper">
                                    <i class="{{ $activity['icon'] }}"></i>
                                </div>
                                <div class="activity-content">
                                    <p class="mb-0 fw-semibold">{{ $activity['title'] }}</p>
                                    <p class="mb-0 text-muted small">{{ $activity['description'] }}</p>
                                    <small class="text-muted">{{ $activity['time'] }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">No recent activities</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Division Modal -->
<div class="modal fade" id="assignDivisionModal" tabindex="-1" aria-labelledby="assignDivisionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignDivisionModalLabel">
                    <i class="bi bi-person-plus me-2"></i>Assign Division to Teacher
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('principal.assign-division') }}" method="POST" id="assignDivisionForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="teacher_id" class="form-label fw-semibold">Select Teacher *</label>
                            <select class="form-select" id="teacher_id" name="teacher_id" required>
                                <option value="">Choose a teacher...</option>
                                @php
                                    $teachers = \App\Models\User::role('teacher')->orderBy('name')->get();
                                @endphp
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="division_id" class="form-label fw-semibold">Select Division *</label>
                            <select class="form-select" id="division_id" name="division_id" required>
                                <option value="">Choose a division...</option>
                                @php
                                    $divisions = \App\Models\Academic\Division::with('program')->where('is_active', true)->get();
                                @endphp
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}">
                                        {{ $division->division_name }} - {{ $division->program->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="assignment_type" class="form-label fw-semibold">Assignment Type *</label>
                            <select class="form-select" id="assignment_type" name="assignment_type" required>
                                <option value="division">Class Teacher</option>
                                <option value="subject">Subject Teacher</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="is_active" class="form-label fw-semibold">Status *</label>
                            <select class="form-select" id="is_active" name="is_active" required>
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="notes" class="form-label fw-semibold">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any additional notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Assign Division
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Timetable Modal -->
<div class="modal fade" id="addTimetableModal" tabindex="-1" aria-labelledby="addTimetableModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addTimetableModalLabel">
                    <i class="bi bi-calendar-plus me-2"></i>Add Class to Timetable
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('principal.timetable.store') }}" method="POST" id="addTimetableForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="timetable_division_id" class="form-label fw-semibold">Select Division *</label>
                            <select class="form-select" id="timetable_division_id" name="division_id" required onchange="updateSelectedDivision()">
                                <option value="">Choose a division...</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                        {{ $division->division_name }} - {{ $division->program->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="subject_id" class="form-label fw-semibold">Select Subject *</label>
                            <select class="form-select" id="subject_id" name="subject_id" required>
                                <option value="">Choose a subject...</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="teacher_id" class="form-label fw-semibold">Select Teacher *</label>
                            <select class="form-select" id="teacher_id" name="teacher_id" required>
                                <option value="">Choose a teacher...</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="day_of_week" class="form-label fw-semibold">Day of Week *</label>
                            <select class="form-select" id="day_of_week" name="day_of_week" required>
                                <option value="">Select a day...</option>
                                @foreach($days as $key => $day)
                                    <option value="{{ $key }}">{{ $day }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="timetable_date" class="form-label fw-semibold">Date *</label>
                            <input type="date" class="form-control" id="timetable_date" name="date" required min="{{ date('Y-m-d') }}">
                            <div class="form-text">Select a specific date for this class</div>
                            <div id="holidayWarning" class="alert alert-warning mt-2 d-none">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <span id="holidayWarningText"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="academic_year_id" class="form-label fw-semibold">Academic Year *</label>
                            <select class="form-select" id="academic_year_id" name="academic_year_id" required>
                                <option value="">Choose academic year...</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="start_time" class="form-label fw-semibold">Start Time *</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" required>
                        </div>
                        <div class="col-md-6">
                            <label for="end_time" class="form-label fw-semibold">End Time *</label>
                            <input type="time" class="form-control" id="end_time" name="end_time" required>
                            <div class="form-text">Must be after start time</div>
                        </div>
                        <div class="col-md-6">
                            <label for="room_number" class="form-label fw-semibold">Room Number</label>
                            <input type="text" class="form-control" id="room_number" name="room_number" placeholder="e.g., Room 101">
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="addClassSubmitBtn">
                        <i class="bi bi-check-circle me-1"></i>Add Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-select division in modal based on current selection
function updateSelectedDivision() {
    const urlParams = new URLSearchParams(window.location.search);
    const currentDivision = urlParams.get('division_id');
    if (currentDivision) {
        document.getElementById('timetable_division_id').value = currentDivision;
    }
}

// Initialize on modal open
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('addTimetableModal');
    modal.addEventListener('show.bs.modal', function() {
        updateSelectedDivision();
    });
    
    // Holiday check for date field
    const dateInput = document.getElementById('timetable_date');
    const holidayWarning = document.getElementById('holidayWarning');
    const holidayWarningText = document.getElementById('holidayWarningText');
    const submitBtn = document.getElementById('addClassSubmitBtn');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const conflictWarning = document.getElementById('conflictWarning');
    const conflictList = document.getElementById('conflictList');
    
    // Auto-populate day from date
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            const selectedDate = this.value;
            if (selectedDate) {
                const dateObj = new Date(selectedDate + 'T00:00:00');
                const dayName = dateObj.toLocaleDateString('en-US', { weekday: 'long' });
                const dayValue = dayName.toLowerCase();
                
                // Auto-select day of week
                const daySelect = document.getElementById('day_of_week');
                if (daySelect) {
                    daySelect.value = dayValue;
                }
                
                // Check for holiday
                checkHoliday(selectedDate);
            }
        });
    }
    
    // Check for holiday
    function checkHoliday(date) {
        if (!date || !holidayWarning || !submitBtn) return;
        
        fetch("{{ route('academic.timetable.ajax.check-holiday') }}?date=" + date)
            .then(response => response.json())
            .then(data => {
                if (data.is_holiday) {
                    holidayWarningText.textContent = data.holiday_title || 'Cannot create timetable on holiday';
                    holidayWarning.classList.remove('d-none');
                    submitBtn.disabled = true;
                } else {
                    holidayWarning.classList.add('d-none');
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Holiday check error:', error);
            });
    }
    
    // Validate end time is after start time
    if (startTimeInput && endTimeInput) {
        endTimeInput.addEventListener('change', function() {
            if (startTimeInput.value && this.value) {
                if (this.value <= startTimeInput.value) {
                    alert('End time must be after start time');
                    this.value = '';
                }
            }
        });
    }
    
    // Form submission validation
    const form = document.getElementById('addTimetableForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const startTime = startTimeInput?.value;
            const endTime = endTimeInput?.value;
            const date = dateInput?.value;
            
            if (startTime && endTime && endTime <= startTime) {
                e.preventDefault();
                alert('End time must be after start time');
                endTimeInput.focus();
                return false;
            }
            
            if (holidayWarning && !holidayWarning.classList.contains('d-none')) {
                e.preventDefault();
                alert('Cannot create timetable on holiday');
                return false;
            }
        });
    }
});
</script>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteClassModal" tabindex="-1" aria-labelledby="deleteClassModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteClassModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this class from the timetable?</p>
                <p class="mb-0"><strong id="deleteClassName"></strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <form id="deleteClassForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Delete button handler for timetable entries
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-delete-timetable').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const id = this.dataset.id;
            const name = this.dataset.name || 'this class';

            document.getElementById('deleteClassName').textContent = name;
            document.getElementById('deleteClassForm').action = "/dashboard/principal/timetable/delete/" + id;

            const modal = new bootstrap.Modal(document.getElementById('deleteClassModal'));
            modal.show();
        });
    });
    
    // Edit button handler for timetable entries
    document.querySelectorAll('.btn-edit-timetable').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

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

            document.getElementById('editTimetableForm').action = "/dashboard/principal/timetable/update/" + id;

            const modal = new bootstrap.Modal(document.getElementById('editTimetableModal'));
            modal.show();
        });
    });
});
</script>

<!-- Edit Timetable Modal -->
<div class="modal fade" id="editTimetableModal" tabindex="-1" aria-labelledby="editTimetableModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="editTimetableModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Class
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTimetableForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="id" id="editTimetableId">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="editDivisionId" class="form-label fw-semibold">Select Division *</label>
                            <select class="form-select" id="editDivisionId" name="division_id" required>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}">{{ $division->division_name }} - {{ $division->program->name ?? 'N/A' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editSubjectId" class="form-label fw-semibold">Select Subject *</label>
                            <select class="form-select" id="editSubjectId" name="subject_id" required>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editTeacherId" class="form-label fw-semibold">Select Teacher *</label>
                            <select class="form-select" id="editTeacherId" name="teacher_id" required>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editDayOfWeek" class="form-label fw-semibold">Day of Week *</label>
                            <select class="form-select" id="editDayOfWeek" name="day_of_week" required>
                                @foreach($days as $key => $day)
                                    <option value="{{ $key }}">{{ $day }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editDate" class="form-label fw-semibold">Date *</label>
                            <input type="date" class="form-control" id="editDate" name="date" required min="{{ date('Y-m-d') }}">
                            <div id="editHolidayWarning" class="alert alert-warning mt-2 d-none">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <span id="editHolidayWarningText"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="editAcademicYearId" class="form-label fw-semibold">Academic Year *</label>
                            <select class="form-select" id="editAcademicYearId" name="academic_year_id" required>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editStartTime" class="form-label fw-semibold">Start Time *</label>
                            <input type="time" class="form-control" id="editStartTime" name="start_time" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editEndTime" class="form-label fw-semibold">End Time *</label>
                            <input type="time" class="form-control" id="editEndTime" name="end_time" required>
                            <div class="form-text">Must be after start time</div>
                        </div>
                        <div class="col-md-6">
                            <label for="editRoomNumber" class="form-label fw-semibold">Room Number</label>
                            <input type="text" class="form-control" id="editRoomNumber" name="room_number" placeholder="e.g., Room 101">
                        </div>
                    </div>
                    
                    <div id="editConflictWarning" class="alert alert-danger mt-3 d-none">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Schedule Conflict Detected!</strong>
                        <ul id="editConflictList" class="mb-0 mt-2"></ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="editClassSubmitBtn">
                        <i class="bi bi-check-circle me-1"></i>Update Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Principal Dashboard Styles */
.principal-welcome-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(30, 58, 138, 0.3);
}

.principal-avatar-wrapper {
    position: relative;
}

.principal-avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    border: 4px solid rgba(255, 255, 255, 0.3);
}

/* Stats Cards */
.principal-stats-card {
    border-radius: 16px;
    padding: 0;
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
    color: white;
}

.principal-stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.principal-stats-blue {
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
}

.principal-stats-green {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.principal-stats-orange {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.principal-stats-purple {
    background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
}

.principal-stats-red {
    background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
}

.principal-stats-teal {
    background: linear-gradient(135deg, #14b8a6 0%, #0f766e 100%);
}

/* Stats Icon Colors */
.principal-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.card-header-principal {
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

.card-body-principal {
    padding: 1.5rem;
}

/* Attendance & Fee Summary */
.attendance-summary,
.fee-summary {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.attendance-item,
.fee-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 10px;
}

.attendance-icon,
.fee-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

/* Teacher Avatar Small */
.teacher-avatar-small {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
}

/* Quick Actions List */
.quick-actions-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.quick-action-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 10px;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
}

.quick-action-link:hover {
    background: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateX(5px);
}

.quick-action-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

/* Activity Timeline */
.activity-timeline {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-item {
    display: flex;
    align-items: start;
    gap: 1rem;
}

.activity-icon-wrapper {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.125rem;
    flex-shrink: 0;
}

.activity-content {
    flex-grow: 1;
}

/* Utility Classes */
.bg-purple-subtle {
    background-color: rgba(139, 92, 246, 0.1);
}

.text-purple {
    color: #8b5cf6;
}

/* Responsive */
@media (max-width: 768px) {
    .principal-welcome-header {
        padding: 1.5rem;
    }
    
    .principal-avatar {
        width: 50px;
        height: 50px;
        font-size: 1.75rem;
    }
    
    .stats-value {
        font-size: 2rem !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Refresh Dashboard
    document.getElementById('refreshDashboard')?.addEventListener('click', function() {
        location.reload();
    });
    
    // Animate stats on load
    const statsValues = document.querySelectorAll('.stats-value');
    statsValues.forEach(stat => {
        const text = stat.textContent.replace(/,/g, '');
        const finalValue = parseInt(text);
        if (!isNaN(finalValue)) {
            animateValue(stat, 0, finalValue, 1500);
        }
    });
    
    function animateValue(element, start, end, duration) {
        const startTime = performance.now();
        
        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const easeOutQuart = 1 - Math.pow(1 - progress, 4);
            const current = start + (end - start) * easeOutQuart;
            
            element.textContent = Math.floor(current).toLocaleString();
            
            if (progress < 1) {
                requestAnimationFrame(update);
            }
        }
        
        requestAnimationFrame(update);
    }
});
</script>
@endsection
