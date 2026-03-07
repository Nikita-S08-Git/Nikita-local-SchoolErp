# Student Dashboard Module - Remaining Views

## 1. Student Timetable View
File: resources/views/student/timetable/index.blade.php

@extends('student.layouts.app')
@section('title', 'My Timetable')
@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="bi bi-calendar-week me-2"></i>My Timetable</h2>
    
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Time/Day</th>
                            @foreach($days as $day)
                                <th class="text-center">{{ ucfirst($day) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($timeSlots as $slot)
                            <tr>
                                <td class="fw-bold">{{ substr($slot->start_time, 0, 5) }} - {{ substr($slot->end_time, 0, 5) }}</td>
                                @foreach($days as $day)
                                    <td>
                                        @if(isset($timetable[$day]))
                                            @php
                                                $class = $timetable[$day]->firstWhere('start_time', $slot->start_time);
                                            @endphp
                                            @if($class)
                                                <div class="text-center">
                                                    <strong class="d-block">{{ $class->subject->name ?? '' }}</strong>
                                                    <small class="text-muted d-block">{{ $class->teacher->name ?? '' }}</small>
                                                    <span class="badge bg-info mt-1">{{ $class->room ?? '' }}</span>
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

## 2. Student Attendance View
File: resources/views/student/attendance/index.blade.php

@extends('student.layouts.app')
@section('title', 'My Attendance')
@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="bi bi-calendar-check me-2"></i>My Attendance</h2>
    
    <!-- Overall Attendance -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="card-body text-center">
                    <h2 class="mb-0">{{ $overallPercentage }}%</h2>
                    <p class="mb-0 opacity-75">Overall Attendance</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                <div class="card-body text-center">
                    <h2 class="mb-0">{{ $presentDays }}</h2>
                    <p class="mb-0 opacity-75">Present Days</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                <div class="card-body text-center">
                    <h2 class="mb-0">{{ $absentDays }}</h2>
                    <p class="mb-0 opacity-75">Absent Days</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                <div class="card-body text-center">
                    <h2 class="mb-0">{{ $lateDays }}</h2>
                    <p class="mb-0 opacity-75">Late Arrivals</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject-wise Attendance -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-book me-2"></i>Subject-wise Attendance</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Total Lectures</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Percentage</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceBySubject as $item)
                            <tr>
                                <td>{{ $item->subject->name ?? 'N/A' }}</td>
                                <td>{{ $item->total }}</td>
                                <td>{{ $item->present }}</td>
                                <td>{{ $item->absent }}</td>
                                <td>
                                    <div class="progress" style="width: 100px;">
                                        <div class="progress-bar bg-{{ $item->percentage >= 75 ? 'success' : ($item->percentage >= 65 ? 'warning' : 'danger') }}" 
                                             role="progressbar" 
                                             style="width: {{ $item->percentage }}%">
                                            {{ $item->percentage }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($item->percentage >= 75)
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Safe</span>
                                    @elseif($item->percentage >= 65)
                                        <span class="badge bg-warning"><i class="bi bi-exclamation-circle me-1"></i>Warning</span>
                                    @else
                                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Low</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No attendance records</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Attendance -->
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Recent Attendance</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Subject</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAttendance as $attendance)
                            <tr>
                                <td>{{ $attendance->attendance_date->format('d M Y') }}</td>
                                <td>{{ $attendance->subject->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $attendance->status === 'present' ? 'success' : ($attendance->status === 'absent' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">No recent records</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

## 3. Student Profile View
File: resources/views/student/profile/index.blade.php

@extends('student.layouts.app')
@section('title', 'My Profile')
@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="bi bi-person-circle me-2"></i>My Profile</h2>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center" style="border-radius: 15px;">
                <div class="card-body">
                    @if($student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" 
                             alt="{{ $student->name }}" 
                             class="rounded-circle mb-3" 
                             width="150" 
                             height="150"
                             style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 150px; height: 150px; font-size: 3rem;">
                            {{ substr($student->name, 0, 1) }}
                        </div>
                    @endif
                    <h4 class="mb-1">{{ $student->name }}</h4>
                    <p class="text-muted mb-3">{{ $student->email }}</p>
                    <a href="{{ route('student.profile.edit') }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Edit Profile
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">Personal Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Roll Number</label>
                            <p class="fw-semibold">{{ $student->roll_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Division</label>
                            <p class="fw-semibold">{{ $student->division->division_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Email</label>
                            <p class="fw-semibold">{{ $student->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Contact</label>
                            <p class="fw-semibold">{{ $student->contact_no ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

## 4. Student Notifications View
File: resources/views/student/notifications/index.blade.php

@extends('student.layouts.app')
@section('title', 'Notifications')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-bell me-2"></i>Notifications</h2>
        <form action="{{ route('student.notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-check-double me-1"></i>Mark All as Read
            </button>
        </form>
    </div>
    
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body">
            <div class="list-group list-group-flush">
                @forelse($notifications as $notification)
                    <div class="list-group-item {{ !$notification->is_read ? 'bg-light' : '' }}">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                @if(!$notification->is_read)
                                    <span class="badge bg-primary me-2">New</span>
                                @endif
                                {{ ucfirst($notification->type) }}
                            </h6>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1">{{ $notification->message }}</p>
                        @if(!$notification->is_read)
                            <form action="{{ route('student.notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-check me-1"></i>Mark as Read
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-bell-slash text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">No notifications</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
