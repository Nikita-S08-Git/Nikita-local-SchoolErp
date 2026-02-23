@extends('layouts.app')

@section('title', 'Principal Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">Principal Dashboard</h2>
            <p class="text-muted">Welcome back, {{ auth()->user()->name }}</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Students -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Students</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalStudents) }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people-fill text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Teachers -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Teachers</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalTeachers) }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-person-badge-fill text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Programs -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Programs</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalPrograms) }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-mortarboard-fill text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Classes -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Classes</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalClasses) }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-door-open-fill text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fee Collection & Attendance Summary -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Fee Collection</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted mb-1">This Month</p>
                        <h4 class="fw-bold text-success">₹{{ number_format($feeCollection->total_collected ?? 0, 2) }}</h4>
                        <small class="text-muted">{{ $feeCollection->total_transactions ?? 0 }} transactions</small>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Pending Fees</p>
                        <h4 class="fw-bold text-danger">₹{{ number_format($pendingFees, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Attendance Today</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h2 class="fw-bold text-primary">{{ $attendancePercentage }}%</h2>
                        <p class="text-muted mb-0">Overall Attendance</p>
                    </div>
                    <div class="row text-center">
                        <div class="col-6">
                            <h5 class="text-success">{{ $attendanceToday->present ?? 0 }}</h5>
                            <small class="text-muted">Present</small>
                        </div>
                        <div class="col-6">
                            <h5 class="text-danger">{{ $attendanceToday->absent ?? 0 }}</h5>
                            <small class="text-muted">Absent</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Active Programs</span>
                        <strong>{{ $totalPrograms }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Subjects</span>
                        <strong>{{ $totalSubjects }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Active Classes</span>
                        <strong>{{ $totalClasses }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Teaching Staff</span>
                        <strong>{{ $totalTeachers }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Today's Attendance</span>
                        <strong>{{ $todayAttendance }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Students</span>
                        <strong>{{ $totalStudents }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Recent Activities</h5>
                </div>
                <div class="card-body">
                    @if(count($recentActivities) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentActivities as $activity)
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="{{ $activity['icon'] }} fs-4"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $activity['title'] }}</h6>
                                            <p class="mb-0 text-muted small">{{ $activity['description'] }}</p>
                                        </div>
                                        <div>
                                            <small class="text-muted">{{ $activity['time'] }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No recent activities</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('dashboard.students.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-people me-2"></i>Manage Students
                        </a>
                        <a href="{{ route('dashboard.teachers.index') }}" class="btn btn-outline-success">
                            <i class="bi bi-person-badge me-2"></i>Manage Teachers
                        </a>
                        <a href="{{ route('academic.programs.index') }}" class="btn btn-outline-warning">
                            <i class="bi bi-mortarboard me-2"></i>Manage Programs
                        </a>
                        <a href="{{ route('academic.divisions.index') }}" class="btn btn-outline-info">
                            <i class="bi bi-diagram-3 me-2"></i>Manage Divisions
                        </a>
                        <a href="{{ route('academic.sessions.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-calendar-event me-2"></i>Academic Sessions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection