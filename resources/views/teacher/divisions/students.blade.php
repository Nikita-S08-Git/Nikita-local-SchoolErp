@extends('layouts.teacher')

@section('title', 'Division Students')

@section('content')
<style>
    .student-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .student-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    
    .student-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 1rem;
        color: #fff;
    }
    
    .student-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.5rem;
        border: 3px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    
    .attendance-badge {
        padding: 5px 14px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .attendance-high { 
        background: linear-gradient(135deg, #c6f6d5 0%, #9ae6b4 100%); 
        color: #22543d; 
    }
    .attendance-medium { 
        background: linear-gradient(135deg, #feebc8 0%, #fbd38d 100%); 
        color: #7c2d12; 
    }
    .attendance-low { 
        background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%); 
        color: #742a2a; 
    }
    
    .info-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0.5rem 0;
    }
    
    .info-item i {
        width: 20px;
        text-align: center;
        color: #667eea;
    }
    
    .info-item .label {
        color: #6c757d;
        font-size: 0.85rem;
    }
    
    .info-item .value {
        font-weight: 600;
        color: #212529;
    }
    
    .action-btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
    }
    
    .stats-mini {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }
    
    .stats-mini-item {
        flex: 1;
        text-align: center;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 10px;
    }
    
    .stats-mini-item i {
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
    }
    
    .stats-mini-item .value {
        display: block;
        font-weight: 700;
        font-size: 1.1rem;
        color: #212529;
    }
    
    .stats-mini-item .label {
        font-size: 0.75rem;
        color: #6c757d;
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Holiday Alert -->
    @if($todayHoliday)
        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert" style="border-radius: 12px; border: none; background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%); color: white;">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
            <div class="flex-grow-1">
                <strong>Today is a Holiday!</strong> - {{ $todayHoliday->title }}
                @if($todayHoliday->start_date != $todayHoliday->end_date)
                    <span class="ms-2">
                        (Holiday from {{ \Carbon\Carbon::parse($todayHoliday->start_date)->format('d M') }} to {{ \Carbon\Carbon::parse($todayHoliday->end_date)->format('d M Y') }})
                    </span>
                @else
                    <span class="ms-2">({{ \Carbon\Carbon::parse($todayHoliday->start_date)->format('d M Y') }})</span>
                @endif
            </div>
        </div>
    @endif

    <!-- Modern Header -->
    <div class="welcome-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('teacher.dashboard') }}" class="text-white-50">
                                <i class="bi bi-house me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('teacher.divisions.index') }}" class="text-white-50">
                                <i class="bi bi-layers me-1"></i>Divisions
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-white">
                            <i class="bi bi-people me-1"></i>Students
                        </li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center">
                    <div class="header-icon-wrapper me-3">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <h2 class="mb-1 fw-bold text-white">
                            {{ $division->division_name }} Students
                        </h2>
                        <p class="mb-0 text-white-50">
                            <i class="bi bi-calendar3 me-1"></i>{{ $division->academicYear->name ?? 'N/A' }}
                            <span class="mx-2">•</span>
                            <i class="bi bi-mortarboard me-1"></i>{{ $division->program->name ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('teacher.divisions.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left me-2"></i>Back to Divisions
                </a>
            </div>
        </div>
    </div>

    @if($students->total() > 0)
        <!-- Stats Summary -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stats-card stats-card-purple">
                    <div class="stats-card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stats-label">Total Students</p>
                                <h2 class="stats-value">{{ $students->total() }}</h2>
                                <p class="stats-change">
                                    <i class="bi bi-people"></i> In this division
                                </p>
                            </div>
                            <div class="stats-icon stats-icon-purple">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card stats-card-green">
                    <div class="stats-card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stats-label">High Attendance</p>
                                <h2 class="stats-value">{{ $students->filter(fn($s) => ($s->attendance_percentage ?? 0) >= 90)->count() }}</h2>
                                <p class="stats-change">
                                    <i class="bi bi-check-circle"></i> Above 90%
                                </p>
                            </div>
                            <div class="stats-icon stats-icon-green">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card stats-card-pink">
                    <div class="stats-card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stats-label">Needs Attention</p>
                                <h2 class="stats-value">{{ $students->filter(fn($s) => ($s->attendance_percentage ?? 0) < 75)->count() }}</h2>
                                <p class="stats-change">
                                    <i class="bi bi-exclamation-triangle"></i> Below 75%
                                </p>
                            </div>
                            <div class="stats-icon stats-icon-pink">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Grid -->
        <div class="row g-4">
            @foreach($students as $student)
                <div class="col-md-6 col-lg-4">
                    <div class="student-card h-100">
                        <div class="student-card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="student-avatar me-3">
                                        {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-white">{{ $student->first_name }} {{ $student->last_name }}</h6>
                                        <small class="text-white-50">
                                            <i class="bi bi-card-text me-1"></i>{{ $student->roll_number }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body p-3">
                            <!-- Attendance Badge -->
                            <div class="text-center mb-3">
                                @php
                                    $attendanceClass = 'attendance-high';
                                    if (($student->attendance_percentage ?? 0) < 75) $attendanceClass = 'attendance-low';
                                    elseif (($student->attendance_percentage ?? 0) < 90) $attendanceClass = 'attendance-medium';
                                @endphp
                                <span class="attendance-badge {{ $attendanceClass }}">
                                    <i class="bi bi-calendar-check"></i>{{ $student->attendance_percentage ?? 0 }}% Attendance
                                </span>
                            </div>
                            
                            <!-- Info Items -->
                            <div class="info-item">
                                <i class="bi bi-person-heart"></i>
                                <span class="label">Parent:</span>
                                <span class="value">{{ $student->studentProfile->father_name ?? $student->studentProfile->mother_name ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="info-item">
                                <i class="bi bi-telephone"></i>
                                <span class="label">Phone:</span>
                                <span class="value">{{ $student->studentProfile->father_phone ?? $student->studentProfile->mother_phone ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="info-item">
                                <i class="bi bi-droplet"></i>
                                <span class="label">Blood:</span>
                                <span class="value">{{ $student->studentProfile->blood_group ?? $student->blood_group ?? 'N/A' }}</span>
                            </div>
                            
                            @if($student->studentProfile && $student->studentProfile->date_of_birth)
                            <div class="info-item">
                                <i class="bi bi-cake"></i>
                                <span class="label">DOB:</span>
                                <span class="value">{{ \Carbon\Carbon::parse($student->studentProfile->date_of_birth)->format('d M Y') }}</span>
                            </div>
                            @endif
                        </div>
                        
                        <div class="card-footer bg-transparent border-0 p-3">
                            <a href="{{ route('teacher.students.details', $student->id) }}" 
                               class="btn btn-primary w-100 action-btn">
                                <i class="bi bi-eye me-2"></i>View Full Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if($students->hasPages())
        <div class="pagination-wrapper mt-4">
            <div class="pagination-info">
                <span>Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students</span>
            </div>
            <nav aria-label="Student pagination">
                <ul class="pagination pagination-bordered pagination-shadow">
                    {{ $students->appends(request()->query())->links() }}
                </ul>
            </nav>
        </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="empty-state-container">
            <div class="empty-state-card">
                <div class="empty-state-icon">
                    <i class="bi bi-people"></i>
                </div>
                <h3 class="fw-bold text-muted">No Students Found</h3>
                <p class="text-muted mb-4">This division doesn't have any active students.</p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ route('teacher.divisions.index') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Divisions
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    /* Welcome Header */
    .welcome-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.35);
    }
    
    .header-icon-wrapper {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .header-icon-wrapper i {
        font-size: 1.75rem;
        color: #fff;
    }
    
    .breadcrumb-item a {
        text-decoration: none;
    }
    
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    
    /* Stats Cards */
    .stats-card {
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: none;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
    }
    
    .stats-card-body {
        padding: 1.25rem;
    }
    
    .stats-label {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.8rem;
        margin-bottom: 0.25rem;
    }
    
    .stats-value {
        color: #fff;
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    
    .stats-change {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.75rem;
        margin-bottom: 0;
    }
    
    .stats-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .stats-icon i {
        font-size: 1.25rem;
        color: #fff;
    }
    
    .stats-card-purple { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stats-icon-purple { background: rgba(255, 255, 255, 0.2); }
    
    .stats-card-pink { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .stats-icon-pink { background: rgba(255, 255, 255, 0.2); }
    
    .stats-card-green { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
    .stats-icon-green { background: rgba(255, 255, 255, 0.2); }
    
    .stats-card-blue { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .stats-icon-blue { background: rgba(255, 255, 255, 0.2); }
    
    /* Pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    /* Empty State */
    .empty-state-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 50vh;
    }
    
    .empty-state-card {
        text-align: center;
        padding: 3rem;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        max-width: 450px;
    }
    
    .empty-state-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }
    
    .empty-state-icon i {
        font-size: 3rem;
        color: #adb5bd;
    }
    
    @media (max-width: 768px) {
        .pagination-wrapper {
            flex-direction: column;
            text-align: center;
        }
        
        .stats-value {
            font-size: 1.5rem;
        }
    }
</style>
@endsection
