@extends('layouts.teacher')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="container-fluid">
    @php
        $hasDivisions = isset($divisions) && $divisions->count() > 0;
    @endphp

    @if(!$hasDivisions)
        <!-- No Division Assigned Alert -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning d-flex align-items-center" role="alert" style="border-radius: 14px; border: none; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
                    <i class="fas fa-exclamation-triangle-fill fs-2 me-3"></i>
                    <div class="flex-grow-1">
                        <h4 class="mb-2">No Division Assigned Yet</h4>
                        <p class="mb-0">You haven't been assigned to any division yet. Please contact the administrator.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-chalkboard-teacher me-2 text-primary"></i>Teacher Dashboard</h2>
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
                    <p class="display-4 fw-bold text-primary mb-0">{{ number_format($hasDivisions ? $totalStudents : 0) }}</p>
                    <small class="text-muted">{{ $hasDivisions ? 'In your assigned divisions' : 'No division assigned' }}</small>
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
                    <h5 class="mb-2">Assigned Division{{ $hasDivisions && $divisions->count() > 1 ? 's' : '' }}</h5>
                    <p class="h4 fw-bold text-success mb-0">{{ $hasDivisions ? $divisions->first()->division_name : 'Not Assigned' }}</p>
                    <small class="text-muted">{{ $hasDivisions ? ($divisions->count() > 1 ? $divisions->count() . ' divisions assigned' : 'Primary assignment') : 'Awaiting assignment' }}</small>
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

    @if($hasDivisions)
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

    <!-- Timetable & Information -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm h-100" style="border-radius: 14px; border: none;">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Today's Schedule</h5>
                        <span class="badge bg-primary">{{ count($todaySchedule ?? []) }} Classes</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(isset($todaySchedule) && count($todaySchedule) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 px-4"><i class="fas fa-clock me-2 text-muted"></i>Time</th>
                                        <th class="py-3 px-4"><i class="fas fa-book me-2 text-muted"></i>Subject</th>
                                        <th class="py-3 px-4"><i class="fas fa-users me-2 text-muted"></i>Division</th>
                                        <th class="py-3 px-4"><i class="fas fa-door-open me-2 text-muted"></i>Room</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todaySchedule as $index => $schedule)
                                    <tr class="{{ $index % 2 == 0 ? '' : 'bg-light' }}">
                                        <td class="px-4 py-3">
                                            <span class="fw-semibold text-primary">{{ $schedule->start_time ?? 'N/A' }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                                {{ $schedule->subject->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="fw-medium">{{ $schedule->division->division_name ?? 'N/A' }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $schedule->room ?? 'N/A' }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-calendar-check fa-2x text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-2">No Classes Today</h5>
                            <p class="text-muted mb-0">Enjoy your free time!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
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
                        <p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse($teacher->created_at)->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
