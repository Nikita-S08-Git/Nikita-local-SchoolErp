@extends('layouts.teacher')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Welcome Header with Gradient Background -->
    <div class="welcome-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="avatar-wrapper me-3">
                        @if($teacherProfile && $teacherProfile->photo_path)
                            <img src="{{ asset('storage/' . $teacherProfile->photo_path) }}" 
                                 alt="{{ $teacher->name }}" 
                                 class="teacher-avatar">
                        @else
                            <div class="teacher-avatar-placeholder">
                                <i class="bi bi-person-circle"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h2 class="mb-1 fw-bold text-white">
                            Welcome back, {{ $teacher->name }}! 👋
                        </h2>
                        <p class="mb-0 text-white-50">
                            <i class="bi bi-briefcase me-1"></i>{{ ucfirst($teacher->roles->first()->name ?? 'Teacher') }}
                            @if($teacherProfile)
                                <span class="mx-2">•</span>
                                <i class="bi bi-mortarboard me-1"></i>{{ $teacherProfile->qualification ?? 'N/A' }}
                                @if($teacherProfile->experience_years)
                                    <span class="mx-2">•</span>
                                    <i class="bi bi-award me-1"></i>{{ $teacherProfile->experience_years }} years experience
                                @endif
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="d-flex gap-2 justify-content-lg-end">
                    <a href="{{ route('teacher.profile') }}" class="btn btn-light">
                        <i class="bi bi-person me-2"></i>Profile
                    </a>
                    <button class="btn btn-outline-light" id="refreshDashboard">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards with Modern Design -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card stats-card-purple">
                <div class="stats-card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stats-label">Total Students</p>
                            <h2 class="stats-value">{{ $totalStudents }}</h2>
                            <p class="stats-change">
                                <i class="bi bi-arrow-up"></i> Active students
                            </p>
                        </div>
                        <div class="stats-icon stats-icon-purple">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card stats-card-pink">
                <div class="stats-card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stats-label">My Divisions</p>
                            <h2 class="stats-value">{{ $divisions->count() }}</h2>
                            <p class="stats-change">
                                <i class="bi bi-layers"></i> Assigned classes
                            </p>
                        </div>
                        <div class="stats-icon stats-icon-pink">
                            <i class="bi bi-layers-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card stats-card-blue">
                <div class="stats-card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stats-label">Today's Classes</p>
                            <h2 class="stats-value">{{ $todaySchedule->count() }}</h2>
                            <p class="stats-change">
                                <i class="bi bi-calendar-event"></i> Scheduled today
                            </p>
                        </div>
                        <div class="stats-icon stats-icon-blue">
                            <i class="bi bi-calendar-week"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card stats-card-green">
                <div class="stats-card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stats-label">Attendance Rate</p>
                            <h2 class="stats-value">{{ $attendanceStats['percentage'] ?? 0 }}%</h2>
                            <p class="stats-change">
                                <i class="bi bi-graph-up"></i> This month
                            </p>
                        </div>
                        <div class="stats-icon stats-icon-green">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="row g-4">
        <!-- Left Column - Today's Schedule & Quick Actions -->
        <div class="col-lg-8">
            <!-- All Students Table -->
            <div class="dashboard-card mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="bi bi-people me-2"></i>All Students
                    </h5>
                    <span class="badge bg-primary">{{ $students->count() }} Students</span>
                </div>
                <div class="card-body-custom">
                    @if($students->count() > 0)
                        <div class="table-responsive students-table-container">
                            <table class="table table-hover align-middle students-table">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Division</th>
                                        <th>Roll No</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                        <tr>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle me-2 bg-primary bg-gradient d-flex align-items-center justify-content-center text-white fw-bold"
                                                             style="width: 40px; height: 40px; min-width: 40px;">
                                                        {{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $student->full_name ?? 'N/A' }}</div>
                                                        <small class="text-muted">{{ $student->email ?? 'No email' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge bg-info">{{ $student->division->division_name ?? 'N/A' }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="text-muted">{{ $student->roll_number ?? 'N/A' }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <a href="{{ route('teacher.students.show', $student->id) }}"
                                                   class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                    <i class="bi bi-eye me-1"></i>View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-people"></i>
                            <h5>No students found</h5>
                            <p class="text-muted">You don't have any students in your divisions yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Today's Schedule -->
            <div class="dashboard-card mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-week me-2"></i>Today's Schedule
                    </h5>
                    <span class="badge bg-primary">{{ now()->format('l, F j, Y') }}</span>
                </div>
                <div class="card-body-custom">
                    @if($todaySchedule->count() > 0)
                        <div class="schedule-timeline">
                            @foreach($todaySchedule as $index => $class)
                                <div class="schedule-item">
                                    <div class="schedule-time">
                                        <span class="time-badge">{{ substr($class->start_time, 0, 5) }}</span>
                                        <div class="time-line"></div>
                                        <span class="time-badge">{{ substr($class->end_time, 0, 5) }}</span>
                                    </div>
                                    <div class="schedule-content">
                                        <div class="schedule-card">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-1 fw-bold">{{ $class->subject->name ?? 'N/A' }}</h6>
                                                    <p class="mb-0 text-muted small">{{ $class->subject->code ?? '' }}</p>
                                                </div>
                                                <span class="badge bg-primary">Period {{ $index + 1 }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex gap-3">
                                                    <span class="text-muted small">
                                                        <i class="bi bi-people me-1"></i>Div {{ $class->division->division_name ?? 'N/A' }}
                                                    </span>
                                                    <span class="text-muted small">
                                                        <i class="bi bi-geo-alt me-1"></i>{{ $class->room ?? 'TBA' }}
                                                    </span>
                                                </div>
                                                @if($class->attendance_marked)
                                                    <span class="btn btn-sm btn-success disabled">
                                                        <i class="bi bi-check-circle me-1"></i>Attendance Marked
                                                    </span>
                                                @elseif(!$class->is_active)
                                                    <span class="btn btn-sm btn-secondary disabled">
                                                        <i class="bi bi-clock me-1"></i>Not Active
                                                    </span>
                                                @else
                                                    <a href="{{ route('teacher.attendance.create', $class->id) }}"
                                                       class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-check-circle me-1"></i>Mark Attendance
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-calendar-x"></i>
                            <h5>No classes scheduled for today</h5>
                            <p class="text-muted">Enjoy your free time! ☕</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- My Divisions -->
            <div class="dashboard-card">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="bi bi-layers me-2"></i>My Divisions
                    </h5>
                    <span class="badge bg-info">{{ $divisions->count() }} Classes</span>
                </div>
                <div class="card-body-custom">
                    @if($divisions->count() > 0)
                        <div class="row g-3">
                            @foreach($divisions as $division)
                                <div class="col-md-6">
                                    <div class="division-card-modern">
                                        <div class="division-header">
                                            <div class="division-icon">
                                                <i class="bi bi-mortarboard"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold">Division {{ $division->division_name }}</h6>
                                                <p class="mb-0 text-muted small">{{ $division->program->name ?? 'N/A' }}</p>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('teacher.students.index', ['division_id' => $division->id]) }}">
                                                            <i class="bi bi-people me-2"></i>View Students
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('teacher.attendance.create', $division->timetables->first()?->id ?? '#') }}">
                                                            <i class="bi bi-calendar-check me-2"></i>Mark Attendance
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="division-body">
                                            <div class="division-info">
                                                <div class="info-item">
                                                    <i class="bi bi-calendar3"></i>
                                                    <span>{{ $division->session->session_name ?? 'N/A' }}</span>
                                                </div>
                                                <div class="info-item">
                                                    <i class="bi bi-people"></i>
                                                    <span>{{ $division->students->where('student_status', 'active')->count() ?? 0 }} Students</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="division-footer">
                                            <a href="{{ route('teacher.students.index', ['division_id' => $division->id]) }}" 
                                               class="btn btn-sm btn-primary flex-fill">
                                                <i class="bi bi-people me-1"></i>Students
                                            </a>
                                            <a href="{{ route('teacher.attendance.index', ['division_id' => $division->id]) }}" 
                                               class="btn btn-sm btn-success flex-fill">
                                                <i class="bi bi-check-circle me-1"></i>Attendance
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <h5>No divisions assigned</h5>
                            <p class="text-muted">You haven't been assigned to any divisions yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Attendance Stats & Quick Actions -->
        <div class="col-lg-4">
            <!-- Attendance Statistics -->
            <div class="dashboard-card mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>Attendance Stats
                    </h5>
                    <span class="badge bg-success">This Month</span>
                </div>
                <div class="card-body-custom">
                    <div class="attendance-chart mb-4">
                        <div class="circular-progress" data-percentage="{{ $attendanceStats['percentage'] ?? 0 }}">
                            <svg width="180" height="180">
                                <circle cx="90" cy="90" r="70" class="progress-bg"></circle>
                                <circle cx="90" cy="90" r="70" class="progress-bar" 
                                        style="stroke-dasharray: {{ 2 * 3.14159 * 70 }}; 
                                               stroke-dashoffset: {{ 2 * 3.14159 * 70 * (1 - ($attendanceStats['percentage'] ?? 0) / 100) }};"></circle>
                            </svg>
                            <div class="progress-text">
                                <span class="percentage">{{ $attendanceStats['percentage'] ?? 0 }}%</span>
                                <span class="label">Present</span>
                            </div>
                        </div>
                    </div>
                    <div class="attendance-details">
                        <div class="detail-item">
                            <div class="detail-icon bg-success-subtle">
                                <i class="bi bi-check-circle text-success"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Present</p>
                                <h6 class="mb-0 fw-bold">{{ $attendanceStats['present'] ?? 0 }}</h6>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-icon bg-danger-subtle">
                                <i class="bi bi-x-circle text-danger"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Absent</p>
                                <h6 class="mb-0 fw-bold">{{ $attendanceStats['absent'] ?? 0 }}</h6>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-icon bg-primary-subtle">
                                <i class="bi bi-calendar-check text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Total Marked</p>
                                <h6 class="mb-0 fw-bold">{{ $attendanceStats['total'] ?? 0 }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-card">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning-charge me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body-custom">
                    <div class="quick-actions-grid">
                        <a href="{{ route('teacher.attendance.index') }}" class="quick-action-item quick-action-success">
                            <div class="quick-action-icon">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <span>Mark Attendance</span>
                        </a>
                        <a href="{{ route('academic.timetable.teacher') }}" class="quick-action-item quick-action-primary">
                            <div class="quick-action-icon">
                                <i class="bi bi-calendar-week"></i>
                            </div>
                            <span>View Timetable</span>
                        </a>
                        <a href="{{ route('teacher.students.index') }}" class="quick-action-item quick-action-info">
                            <div class="quick-action-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <span>My Students</span>
                        </a>
                        <a href="{{ route('teacher.attendance.report') }}" class="quick-action-item quick-action-warning">
                            <div class="quick-action-icon">
                                <i class="bi bi-file-earmark-bar-graph"></i>
                            </div>
                            <span>Reports</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Teacher Information Table -->
            <div class="dashboard-card mt-4">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="bi bi-person-badge me-2"></i>My Information
                    </h5>
                </div>
                <div class="card-body-custom">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted" style="width: 40%;">Name</td>
                                    <td class="fw-semibold">{{ $teacher->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Email</td>
                                    <td>{{ $teacher->email }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Designation</td>
                                    <td>
                                        @if($teacherProfile && $teacherProfile->designation)
                                            <span class="badge bg-primary">{{ $teacherProfile->designation }}</span>
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Qualification</td>
                                    <td>{{ $teacherProfile->qualification ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Specialization</td>
                                    <td>{{ $teacherProfile->specialization ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Experience</td>
                                    <td>
                                        @if($teacherProfile && $teacherProfile->experience_years)
                                            {{ $teacherProfile->experience_years }} years
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Phone</td>
                                    <td>
                                        @if($teacherProfile && $teacherProfile->phone)
                                            <i class="bi bi-telephone me-1"></i>{{ $teacherProfile->phone }}
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Date of Birth</td>
                                    <td>
                                        @if($teacherProfile && $teacherProfile->date_of_birth)
                                            {{ \Carbon\Carbon::parse($teacherProfile->date_of_birth)->format('d M Y') }}
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Gender</td>
                                    <td>
                                        @if($teacherProfile && $teacherProfile->gender)
                                            {{ ucfirst($teacherProfile->gender) }}
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Joining Date</td>
                                    <td>
                                        @if($teacherProfile && $teacherProfile->joining_date)
                                            {{ \Carbon\Carbon::parse($teacherProfile->joining_date)->format('d M Y') }}
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Current Address</td>
                                    <td>{{ $teacherProfile->current_address ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Permanent Address</td>
                                    <td>{{ $teacherProfile->permanent_address ?? 'Not provided' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 text-end">
                        <a href="{{ route('teacher.profile') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil me-1"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="dashboard-card mt-4">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>Recent Activity
                    </h5>
                </div>
                <div class="card-body-custom">
                    <div class="activity-timeline">
                        <div class="activity-item">
                            <div class="activity-icon bg-success">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="activity-content">
                                <p class="mb-0 fw-semibold">Attendance Marked</p>
                                <p class="mb-0 text-muted small">Division A - Today</p>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon bg-primary">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div class="activity-content">
                                <p class="mb-0 fw-semibold">Class Scheduled</p>
                                <p class="mb-0 text-muted small">Mathematics - Tomorrow</p>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon bg-info">
                                <i class="bi bi-person-plus"></i>
                            </div>
                            <div class="activity-content">
                                <p class="mb-0 fw-semibold">New Student Added</p>
                                <p class="mb-0 text-muted small">Division B - 2 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Students Table */
.students-table-container {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.students-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.students-table thead th {
    border: none;
    padding: 1rem 0.75rem;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.students-table tbody tr {
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.2s ease;
}

.students-table tbody tr:hover {
    background-color: #f8f9ff;
    transform: scale(1.01);
}

.students-table tbody td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
}

/* Welcome Header */
.welcome-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.avatar-wrapper {
    position: relative;
}

.teacher-avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    border: 4px solid rgba(255, 255, 255, 0.3);
    object-fit: cover;
}

.teacher-avatar-placeholder {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
}

/* Stats Cards */
.stats-card {
    border-radius: 16px;
    padding: 0;
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.stats-card-body {
    padding: 1.5rem;
}

.stats-card-purple {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stats-card-pink {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.stats-card-blue {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.stats-card-green {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
}

.stats-label {
    font-size: 0.875rem;
    opacity: 0.9;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.stats-value {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    line-height: 1;
}

.stats-change {
    font-size: 0.813rem;
    opacity: 0.8;
    margin-bottom: 0;
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
}

.stats-icon-purple { background: rgba(255, 255, 255, 0.2); }
.stats-icon-pink { background: rgba(255, 255, 255, 0.2); }
.stats-icon-blue { background: rgba(255, 255, 255, 0.2); }
.stats-icon-green { background: rgba(255, 255, 255, 0.2); }

/* Dashboard Cards */
.dashboard-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.card-header-custom {
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-body-custom {
    padding: 1.5rem;
}

/* Schedule Timeline */
.schedule-timeline {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.schedule-item {
    display: flex;
    gap: 1.5rem;
}

.schedule-time {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 70px;
}

.time-badge {
    background: #f1f5f9;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #475569;
}

.time-line {
    width: 2px;
    flex-grow: 1;
    background: linear-gradient(to bottom, #e2e8f0, transparent);
    margin: 0.5rem 0;
}

.schedule-content {
    flex-grow: 1;
}

.schedule-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem;
    transition: all 0.3s ease;
}

.schedule-card:hover {
    background: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateX(5px);
}

/* Division Cards */
.division-card-modern {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.division-card-modern:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    transform: translateY(-3px);
}

.division-header {
    padding: 1rem;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.division-icon {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.division-body {
    padding: 1rem;
}

.division-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #64748b;
    font-size: 0.875rem;
}

.info-item i {
    color: #94a3b8;
}

.division-footer {
    padding: 1rem;
    background: #f8fafc;
    display: flex;
    gap: 0.5rem;
}

/* Attendance Chart */
.attendance-chart {
    display: flex;
    justify-content: center;
    align-items: center;
}

.circular-progress {
    position: relative;
}

.circular-progress svg {
    transform: rotate(-90deg);
}

.progress-bg {
    fill: none;
    stroke: #e2e8f0;
    stroke-width: 12;
}

.progress-bar {
    fill: none;
    stroke: url(#gradient);
    stroke-width: 12;
    stroke-linecap: round;
    transition: stroke-dashoffset 1s ease;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.progress-text .percentage {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
}

.progress-text .label {
    display: block;
    font-size: 0.875rem;
    color: #64748b;
}

.attendance-details {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 10px;
}

.detail-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

/* Quick Actions */
.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.quick-action-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    padding: 1.5rem 1rem;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.quick-action-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.quick-action-success {
    background: linear-gradient(135deg, #d4f4dd 0%, #b8f2c6 100%);
    color: #166534;
}

.quick-action-success:hover {
    border-color: #22c55e;
}

.quick-action-primary {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
}

.quick-action-primary:hover {
    border-color: #3b82f6;
}

.quick-action-info {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
    color: #075985;
}

.quick-action-info:hover {
    border-color: #0ea5e9;
}

.quick-action-warning {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
}

.quick-action-warning:hover {
    border-color: #f59e0b;
}

.quick-action-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
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

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.125rem;
    flex-shrink: 0;
}

.activity-content {
    flex-grow: 1;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-state i {
    font-size: 4rem;
    color: #cbd5e1;
    margin-bottom: 1rem;
}

.empty-state h5 {
    color: #64748b;
    margin-bottom: 0.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .welcome-header {
        padding: 1.5rem;
    }
    
    .teacher-avatar,
    .teacher-avatar-placeholder {
        width: 50px;
        height: 50px;
    }
    
    .stats-value {
        font-size: 2rem;
    }
    
    .quick-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .schedule-item {
        flex-direction: column;
        gap: 1rem;
    }
    
    .schedule-time {
        flex-direction: row;
        justify-content: space-between;
        width: 100%;
    }
    
    .time-line {
        width: auto;
        height: 2px;
        flex-grow: 1;
        margin: 0 0.5rem;
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
        const finalValue = parseInt(stat.textContent);
        let currentValue = 0;
        const increment = finalValue / 50;
        const timer = setInterval(() => {
            currentValue += increment;
            if (currentValue >= finalValue) {
                stat.textContent = finalValue;
                clearInterval(timer);
            } else {
                stat.textContent = Math.floor(currentValue);
            }
        }, 20);
    });
});
</script>
@endsection
