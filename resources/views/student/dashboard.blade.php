@extends('student.layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px;">
                <div class="card-body p-4">
                    <h2 class="mb-2 fw-bold">
                        <i class="bi bi-person-circle me-2"></i>Welcome, {{ $student->name }}!
                    </h2>
                    <p class="mb-0 opacity-75">
                        <i class="bi bi-mortarboard me-1"></i>{{ $student->division->division_name ?? 'N/A' }} | 
                        <i class="bi bi-calendar me-1"></i>{{ now()->format('l, F d, Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stats-card h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
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
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stats-card h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
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
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stats-card h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
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
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stats-card h-100" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
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
                        <div class="col-md-3">
                            <a href="{{ route('student.timetable') }}" class="btn btn-outline-primary w-100 py-3" style="border-radius: 12px;">
                                <i class="bi bi-calendar-week d-block mb-2" style="font-size: 2rem;"></i>
                                <span>View Timetable</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('student.attendance') }}" class="btn btn-outline-success w-100 py-3" style="border-radius: 12px;">
                                <i class="bi bi-calendar-check d-block mb-2" style="font-size: 2rem;"></i>
                                <span>My Attendance</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('student.profile') }}" class="btn btn-outline-info w-100 py-3" style="border-radius: 12px;">
                                <i class="bi bi-person-circle d-block mb-2" style="font-size: 2rem;"></i>
                                <span>My Profile</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('student.notifications') }}" class="btn btn-outline-secondary w-100 py-3" style="border-radius: 12px;">
                                <i class="bi bi-bell d-block mb-2" style="font-size: 2rem;"></i>
                                <span>Notifications</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
