@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-chalkboard-teacher me-2"></i>Teacher Dashboard</h2>
                    <p class="text-muted mb-0">Manage your classes, students, and activities</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100" style="border-radius: 14px; border: none;">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h5 class="mb-2">Total Students</h5>
                    <p class="display-4 fw-bold text-primary mb-0">{{ number_format($totalStudents ?? 0) }}</p>
                    <small class="text-muted">In your assigned divisions</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100" style="border-radius: 14px; border: none;">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="mb-2">Assigned Division</h5>
                    <p class="h4 fw-bold text-success mb-0">{{ $assignedDivision->division_name ?? 'Not Assigned' }}</p>
                    <small class="text-muted">Primary assignment</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100" style="border-radius: 14px; border: none;">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="fas fa-id-badge"></i>
                    </div>
                    <h5 class="mb-2">Your Profile</h5>
                    <p class="h6 fw-bold text-info mb-0">{{ $teacher->name ?? 'N/A' }}</p>
                    <small class="text-muted">Teacher ID: {{ $teacher->id ?? 'N/A' }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 14px; border: none;">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('teacher.attendance.index') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-clipboard-check me-2"></i>Mark Attendance
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('teacher.divisions.index') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="fas fa-users me-2"></i>My Divisions
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('teacher.students.index') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="fas fa-user-graduate me-2"></i>View Students
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('teacher.results.index') }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="fas fa-chart-bar me-2"></i>Results
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Timetable -->
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm h-100" style="border-radius: 14px; border: none;">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Today's Schedule</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Subject</th>
                                    <th>Division</th>
                                    <th>Room</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($todaySchedule) && count($todaySchedule) > 0)
                                    @foreach($todaySchedule as $schedule)
                                    <tr>
                                        <td>{{ $schedule->start_time ?? 'N/A' }}</td>
                                        <td>{{ $schedule->subject->name ?? 'N/A' }}</td>
                                        <td>{{ $schedule->division->division_name ?? 'N/A' }}</td>
                                        <td>{{ $schedule->room ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-calendar-check fa-2x mb-2 d-block"></i>
                                            No classes scheduled for today
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100" style="border-radius: 14px; border: none;">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Email</label>
                        <p class="fw-semibold mb-0">{{ $teacher->email ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Department</label>
                        <p class="fw-semibold mb-0">{{ $teacherProfile->department->name ?? $teacher->department->name ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Phone</label>
                        <p class="fw-semibold mb-0">{{ $teacherProfile->phone ?? $teacher->phone ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Joined Date</label>
                        <p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse($teacher->created_at)->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
