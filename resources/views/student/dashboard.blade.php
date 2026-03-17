@extends('student.layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2 fw-bold">
                                <i class="bi bi-mortarboard-fill me-2"></i>Welcome back, {{ $student->first_name }}!
                            </h2>
                            <p class="mb-0 opacity-75">
                                <i class="bi bi-building me-1"></i>{{ $student->division->division_name ?? 'N/A' }} | 
                                <i class="bi bi-person-badge me-1"></i>Roll No: {{ $student->roll_number ?? 'N/A' }} |
                                <i class="bi bi-calendar me-1"></i>{{ now()->format('l, F d, Y') }}
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="d-inline-flex align-items-center p-3 bg-white bg-opacity-20 rounded-3">
                                <i class="bi bi-clock me-2" style="font-size: 1.5rem;"></i>
                                <div>
                                    <small class="opacity-75 d-block">Current Time</small>
                                    <strong id="currentTime">{{ now()->format('h:i A') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card stats-card h-100 border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Today's Classes</p>
                            <h2 class="mb-0 fw-bold">{{ $todayClasses->count() }}</h2>
                        </div>
                        <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                            <i class="bi bi-calendar-week"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="opacity-75"><i class="bi bi-arrow-up me-1"></i>Next class in 30 mins</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card stats-card h-100 border-0" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Attendance (Month)</p>
                            <h2 class="mb-0 fw-bold">{{ $attendanceSummary['percentage'] }}%</h2>
                        </div>
                        <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        @if($attendanceSummary['percentage'] >= 75)
                            <span class="badge bg-success bg-opacity-25"><i class="bi bi-check-circle me-1"></i>Excellent</span>
                        @elseif($attendanceSummary['percentage'] >= 65)
                            <span class="badge bg-warning bg-opacity-25 text-dark"><i class="bi bi-exclamation-circle me-1"></i>Need Improvement</span>
                        @else
                            <span class="badge bg-danger bg-opacity-25"><i class="bi bi-exclamation-triangle me-1"></i>Critical</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Timetable -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-calendar-week me-2 text-primary"></i>Today's Schedule
                    </h5>
                </div>
                <div class="card-body">
                    @if($todayClasses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Subject</th>
                                        <th>Teacher</th>
                                        <th>Room</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayClasses as $class)
                                        <tr>
                                            <td>
                                                <small>{{ substr($class->start_time, 0, 5) }} - {{ substr($class->end_time, 0, 5) }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $class->subject->name ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $class->subject->code ?? '' }}</small>
                                            </td>
                                            <td>{{ $class->teacher->name ?? 'N/A' }}</td>
                                            <td><span class="badge bg-info">{{ $class->room ?? 'TBA' }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('student.timetable') }}" class="btn btn-sm btn-outline-primary">
                                View Full Timetable <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No classes scheduled for today</p>
                            <small class="text-muted">Enjoy your free time! ☕</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Summary -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-calendar-check me-2 text-success"></i>Attendance Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded-3">
                                <p class="text-muted mb-1">Total Lectures</p>
                                <h4 class="fw-bold">{{ $attendanceSummary['total'] }}</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded-3">
                                <p class="text-muted mb-1">Present</p>
                                <h4 class="fw-bold text-success">{{ $attendanceSummary['present'] }}</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded-3">
                                <p class="text-muted mb-1">Attendance %</p>
                                <h4 class="fw-bold text-primary">{{ $attendanceSummary['percentage'] }}%</h4>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('student.attendance') }}" class="btn btn-sm btn-outline-success">
                            View Detailed Attendance <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Real-time Clock Script -->
@push('scripts')
<script>
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true 
        });
        const timeElement = document.getElementById('currentTime');
        if (timeElement) {
            timeElement.textContent = timeString;
        }
    }
    setInterval(updateTime, 1000);
    updateTime();
</script>
@endpush
@endsection
