@extends('layouts.teacher')

@section('title', 'Attendance Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-calendar-check me-2"></i>Attendance Management</h2>
            <p class="text-muted mb-0">Mark and track attendance for your lectures</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('teacher.attendance.history') }}" class="btn btn-outline-primary">
                <i class="bi bi-clock-history me-2"></i>History
            </a>
            <a href="{{ route('teacher.attendance.report') }}" class="btn btn-outline-info">
                <i class="bi bi-graph-up me-2"></i>Reports
            </a>
        </div>
    </div>

    <!-- Today's Schedule -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-calendar-day me-2"></i>Today's Lectures</h5>
            <span class="badge bg-primary">{{ $todaySchedule->count() }} lectures</span>
        </div>
        <div class="card-body">
            @if($todaySchedule->count() > 0)
                <div class="row g-4">
                    @foreach($todaySchedule as $lecture)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="mb-1">{{ $lecture->subject->name ?? 'N/A' }}</h6>
                                            <p class="text-muted small mb-0">{{ $lecture->subject->code ?? '' }}</p>
                                        </div>
                                        <span class="badge bg-{{ $lecture->attendance_marked ? 'success' : 'warning' }}">
                                            {{ $lecture->attendance_marked ? '✓ Marked' : '◌ Pending' }}
                                        </span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-people text-muted me-2"></i>
                                            <span class="small">{{ $lecture->division->division_name }}</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-clock text-muted me-2"></i>
                                            <span class="small">{{ substr($lecture->start_time, 0, 5) }} - {{ substr($lecture->end_time, 0, 5) }}</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-geo-alt text-muted me-2"></i>
                                            <span class="small">{{ $lecture->room_number ?? 'TBA' }}</span>
                                        </div>
                                    </div>
                                    
                                    @if($lecture->attendance_marked)
                                        <div class="alert alert-success mb-0 py-2">
                                            <small><i class="bi bi-check-circle"></i> {{ $lecture->attendance_count }} students marked</small>
                                        </div>
                                    @else
                                        <a href="{{ route('teacher.attendance.create', $lecture->id) }}" class="btn btn-primary w-100">
                                            <i class="bi bi-calendar-check me-2"></i>Mark Attendance
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mt-3">No lectures scheduled for today</h5>
                    <p class="text-muted">Enjoy your free time! ☕</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-primary">
                <div class="card-body text-center p-4">
                    <i class="bi bi-clock-history text-primary" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">View History</h5>
                    <p class="text-muted">Check past attendance records</p>
                    <a href="{{ route('teacher.attendance.history') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-right me-2"></i>Go to History
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-info">
                <div class="card-body text-center p-4">
                    <i class="bi bi-graph-up text-info" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Attendance Reports</h5>
                    <p class="text-muted">View detailed statistics and reports</p>
                    <a href="{{ route('teacher.attendance.report') }}" class="btn btn-outline-info">
                        <i class="bi bi-arrow-right me-2"></i>View Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
