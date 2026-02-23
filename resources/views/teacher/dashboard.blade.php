@extends('layouts.app')

@section('page-title', 'Teacher Dashboard')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="bi bi-person-circle text-white" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <h3 class="mb-1">Welcome, {{ $teacher->name }}</h3>
                            <p class="text-muted mb-2">{{ $teacher->email }}</p>
                            @if($assignedDivision)
                                <span class="badge bg-success">Class Teacher - {{ $assignedDivision->division_name }}</span>
                            @else
                                <span class="badge bg-secondary">Subject Teacher</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $totalStudents }}</h4>
                            <small>Total Students</small>
                        </div>
                        <i class="bi bi-people fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $totalSubjects }}</h4>
                            <small>Subjects Assigned</small>
                        </div>
                        <i class="bi bi-book fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $todayAttendance }}</h4>
                            <small>Today's Attendance</small>
                        </div>
                        <i class="bi bi-calendar-check fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">{{ $activeSession->name ?? 'N/A' }}</h6>
                            <small>Academic Session</small>
                        </div>
                        <i class="bi bi-calendar-range fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Today's Schedule -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-clock me-2"></i>Today's Schedule</h6>
                </div>
                <div class="card-body">
                    @if($todaySchedule->count() > 0)
                        @foreach($todaySchedule as $schedule)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div>
                                    <strong>{{ $schedule->subject->name }}</strong><br>
                                    <small class="text-muted">{{ $schedule->division->division_name }}</small>
                                </div>
                                <span class="badge bg-secondary">{{ $schedule->start_time }} - {{ $schedule->end_time }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No classes scheduled for today</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Students -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-people me-2"></i>My Students</h6>
                    @if($assignedDivision)
                        <a href="{{ route('teacher.students') }}" class="btn btn-sm btn-light">View All</a>
                    @endif
                </div>
                <div class="card-body">
                    @if($recentStudents->count() > 0)
                        @foreach($recentStudents as $student)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div>
                                    <strong>{{ $student->name }}</strong><br>
                                    <small class="text-muted">Roll: {{ $student->roll_number }}</small>
                                </div>
                                <span class="badge bg-primary">{{ $student->division->division_name }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">
                            @if($assignedDivision)
                                No students found
                            @else
                                You are not assigned as a class teacher
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    @if($assignedDivision)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <a href="{{ route('teacher.students') }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="bi bi-people me-2"></i>View My Students
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('teacher.attendance') }}" class="btn btn-outline-success w-100 mb-2">
                                <i class="bi bi-calendar-check me-2"></i>View Attendance
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('academic.timetable.index') }}" class="btn btn-outline-info w-100 mb-2">
                                <i class="bi bi-table me-2"></i>View Timetable
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection