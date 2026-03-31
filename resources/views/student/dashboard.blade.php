@extends('student.layouts.app')

@section('title', 'Student Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Admin Notifications/Important Notes -->
    @if($notifications->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-megaphone-fill text-white me-2" style="font-size: 1.5rem;"></i>
                        <h5 class="text-white mb-0">Important Notifications</h5>
                    </div>
                    <div class="row g-3">
                        @foreach($notifications as $notification)
                        <div class="col-md-6">
                            <div class="bg-white bg-opacity-10 rounded-3 p-3">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-{{ $notification->badge_color }} me-2">
                                        <i class="bi {{ $notification->icon }}"></i>
                                    </span>
                                    <div class="flex-grow-1">
                                        <h6 class="text-white mb-1">{{ $notification->title }}</h6>
                                        <p class="text-white-50 small mb-1">{{ Str::limit($notification->message, 100) }}</p>
                                        <small class="text-white-50">
                                            <i class="bi bi-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

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
                                <i class="bi bi-building me-1"></i>
                                @if($student->division)
                                    {{ $student->division->division_name }} 
                                    @if($student->division->program)
                                        ({{ $student->division->program->name }})
                                    @endif
                                @else
                                    N/A
                                @endif
                                |
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
        <div class="col-md-3">
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
                    {{-- <div class="mt-2">
                        <small class="opacity-75"><i class="bi bi-arrow-up me-1"></i>Next class in 30 mins</small>
                    </div> --}}
                </div>
            </div>
        </div>

        <div class="col-md-3">
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

        <div class="col-md-3">
            <div class="card stats-card h-100 border-0" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Present Days</p>
                            <h2 class="mb-0 fw-bold">{{ $attendanceSummary['present'] }}/{{ $attendanceSummary['total'] }}</h2>
                        </div>
                        <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="opacity-75"><i class="bi bi-graph-up me-1"></i>{{ $attendanceSummary['total'] - $attendanceSummary['present'] }} days absent</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stats-card h-100 border-0" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Notifications</p>
                            <h2 class="mb-0 fw-bold">{{ $student->unreadNotificationsCount() }}</h2>
                        </div>
                        <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                            <i class="bi bi-bell"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="opacity-75"><i class="bi bi-envelope me-1"></i>Unread messages</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Timetable & Upcoming Classes -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
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

        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-bell me-2 text-warning"></i>Recent Notifications
                    </h5>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                <div class="list-group-item px-0 {{ !$notification->is_read ? 'bg-light' : '' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <small class="text-muted mb-1">{{ $notification->created_at->diffForHumans() }}</small>
                                        @if(!$notification->is_read)
                                            <span class="badge bg-primary">New</span>
                                        @endif
                                    </div>
                                    <p class="mb-1 small">{{ Str::limit($notification->message, 50) }}</p>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('student.notifications') }}" class="btn btn-sm btn-outline-primary mt-3 w-100">
                            View All Notifications
                        </a>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-bell-slash text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2 small">No notifications</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Results & Upcoming Exams -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-clipboard-data me-2 text-primary"></i>Recent Results
                        </h5>
                        <a href="{{ route('student.results') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentResults->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Exam</th>
                                        <th>Subject</th>
                                        <th class="text-center">Marks</th>
                                        <th class="text-center">Grade</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentResults as $result)
                                        <tr>
                                            <td>
                                                <small class="fw-bold">{{ $result->examination->name ?? 'N/A' }}</small>
                                                <br>
                                                <small class="text-muted">{{ $result->examination->type ?? '' }}</small>
                                            </td>
                                            <td>{{ $result->subject->name ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                <strong>{{ $result->marks_obtained ?? 0 }}/{{ $result->max_marks ?? 100 }}</strong>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $percentage = ($result->marks_obtained / $result->max_marks) * 100;
                                                    $grade = $percentage >= 90 ? 'A+' : ($percentage >= 80 ? 'A' : ($percentage >= 70 ? 'B+' : ($percentage >= 60 ? 'B' : ($percentage >= 50 ? 'C' : 'F'))));
                                                    $badgeClass = $percentage >= 60 ? 'bg-success' : ($percentage >= 40 ? 'bg-warning' : 'bg-danger');
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $grade }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($percentage >= 40)
                                                    <span class="badge bg-success">Pass</span>
                                                @else
                                                    <span class="badge bg-danger">Fail</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No results available yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-calendar-event me-2 text-warning"></i>Upcoming Exams
                    </h5>
                </div>
                <div class="card-body">
                    @if($upcomingExams->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($upcomingExams as $exam)
                                <div class="list-group-item px-0">
                                    <div class="d-flex w-100 justify-content-between">
                                        <strong class="mb-1">{{ $exam->name }}</strong>
                                        <span class="badge bg-primary">{{ $exam->type }}</span>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($exam->start_date)->format('M d, Y') }}
                                        @if($exam->end_date != $exam->start_date)
                                            <br><i class="bi bi-calendar-check me-1"></i>{{ \Carbon\Carbon::parse($exam->end_date)->format('M d, Y') }}
                                        @endif
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-check text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2 small">No upcoming exams</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Fee Status -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-currency-dollar me-2 text-success"></i>Fee Status
                        </h5>
                        <a href="{{ route('student.fees') }}" class="btn btn-sm btn-outline-success">View Details</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded-3">
                                <i class="bi bi-piggy-bank text-primary" style="font-size: 2.5rem;"></i>
                                <h6 class="mt-2 mb-1 text-muted">Total Fees</h6>
<h3 class="mb-0 fw-bold text-primary">₹{{ number_format($totalFees, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded-3">
                                <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                                <h6 class="mt-2 mb-1 text-muted">Total Paid</h6>
                                <h3 class="mb-0 fw-bold text-success">₹{{ number_format($totalPaid, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded-3">
                                <i class="bi bi-exclamation-circle {{ $totalOutstanding > 0 ? 'text-danger' : 'text-success' }}" style="font-size: 2.5rem;"></i>
                                <h6 class="mt-2 mb-1 text-muted">Outstanding</h6>
                                <h3 class="mb-0 fw-bold {{ $totalOutstanding > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format($totalOutstanding, 2) }}</h3>
                                @if($totalOutstanding > 0)
                                    <a href="{{ route('student.fees') }}" class="btn btn-sm btn-danger mt-2">Pay Now</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-lightning-charge me-2 text-warning"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-2 col-6">
                            <a href="{{ route('student.timetable') }}" class="btn btn-outline-primary w-100 py-3" style="border-radius: 12px;">
                                <i class="bi bi-calendar-week d-block mb-2" style="font-size: 2rem;"></i>
                                <span>Timetable</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-6">
                            <a href="{{ route('student.attendance') }}" class="btn btn-outline-success w-100 py-3" style="border-radius: 12px;">
                                <i class="bi bi-calendar-check d-block mb-2" style="font-size: 2rem;"></i>
                                <span>Attendance</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-6">
                            <a href="{{ route('student.profile') }}" class="btn btn-outline-info w-100 py-3" style="border-radius: 12px;">
                                <i class="bi bi-person-circle d-block mb-2" style="font-size: 2rem;"></i>
                                <span>Profile</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-6">
                            <a href="{{ route('student.notifications') }}" class="btn btn-outline-warning w-100 py-3" style="border-radius: 12px;">
                                <i class="bi bi-bell d-block mb-2" style="font-size: 2rem;"></i>
                                <span>Notifications</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-6">
                            <a href="{{ route('student.fees') }}" class="btn btn-outline-danger w-100 py-3" style="border-radius: 12px;">
                                <i class="bi bi-currency-dollar d-block mb-2" style="font-size: 2rem;"></i>
                                <span>Fees</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-6">
                            <a href="{{ route('student.results') }}" class="btn btn-outline-secondary w-100 py-3" style="border-radius: 12px;">
                                <i class="bi bi-clipboard-data d-block mb-2" style="font-size: 2rem;"></i>
                                <span>Results</span>
                            </a>
                        </div>
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
