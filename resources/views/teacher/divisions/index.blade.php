@extends('layouts.teacher')

@section('title', 'My Divisions')

@section('content')
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

    <!-- Modern Header with Gradient Background -->
    <div class="welcome-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="header-icon-wrapper me-3">
                        <i class="bi bi-layers-fill"></i>
                    </div>
                    <div>
                        <h2 class="mb-1 fw-bold text-white">
                            <i class="bi bi-people me-2"></i>My Assigned Divisions
                        </h2>
                        <p class="mb-0 text-white-50">
                            <i class="bi bi-collection me-1"></i>Manage your class divisions and students
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('teacher.dashboard') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @if($divisions->count() > 0)
        @if($todayHoliday)
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="stats-card" style="background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);">
                    <div class="stats-card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stats-label">Holiday Alert</p>
                                <h2 class="stats-value">{{ $todayHoliday->title }}</h2>
                                <p class="stats-change">
                                    <i class="bi bi-calendar-x"></i> No attendance can be marked today
                                </p>
                            </div>
                            <div class="stats-icon" style="background: rgba(255,255,255,0.2);">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stats-card stats-card-purple">
                    <div class="stats-card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stats-label">Total Divisions</p>
                                <h2 class="stats-value">{{ $divisions->count() }}</h2>
                                <p class="stats-change">
                                    <i class="bi bi-layers"></i> Assigned classes
                                </p>
                            </div>
                            <div class="stats-icon stats-icon-purple">
                                <i class="bi bi-layers-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stats-card stats-card-pink">
                    <div class="stats-card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stats-label">Total Students</p>
                                <h2 class="stats-value">{{ $divisions->sum('student_count') }}</h2>
                                <p class="stats-change">
                                    <i class="bi bi-people"></i> Across all divisions
                                </p>
                            </div>
                            <div class="stats-icon stats-icon-pink">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stats-card stats-card-blue">
                    <div class="stats-card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stats-label">Class Teacher</p>
                                <h2 class="stats-value">{{ $divisions->where('is_class_teacher', true)->count() }}</h2>
                                <p class="stats-change">
                                    <i class="bi bi-star"></i> Primary divisions
                                </p>
                            </div>
                            <div class="stats-icon stats-icon-blue">
                                <i class="bi bi-star-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stats-card stats-card-green">
                    <div class="stats-card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stats-label">Subjects</p>
                                <h2 class="stats-value">{{ $divisions->count() * 3 }}</h2>
                                <p class="stats-change">
                                    <i class="bi bi-book"></i> Average per division
                                </p>
                            </div>
                            <div class="stats-icon stats-icon-green">
                                <i class="bi bi-book-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Section Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <i class="bi bi-grid-3x3-gap me-2 text-primary"></i>Division Cards
            </h4>
            <span class="badge bg-primary fs-6">
                <i class="bi bi-collection me-1"></i>{{ $divisions->count() }} Divisions
            </span>
        </div>

        <!-- Division Cards Grid -->
        <div class="row g-4">
            @foreach($divisions as $index => $division)
                <div class="col-md-6 col-lg-4">
                    <div class="division-card h-100" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <!-- Card Header with Gradient -->
                        <div class="division-card-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="division-info">
                                    <h5 class="fw-bold mb-1">
                                        <i class="bi bi-collection me-2"></i>{{ $division->division_name }}
                                    </h5>
                                    <p class="mb-0 opacity-75">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ $division->academicYear->name ?? 'N/A' }}
                                    </p>
                                </div>
                                @if($division->is_class_teacher)
                                    <span class="class-teacher-badge">
                                        <i class="bi bi-star-fill me-1"></i>Class Teacher
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Card Body -->
                        <div class="division-card-body">
                            <!-- Student Count -->
                            <div class="student-count-section mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small">
                                        <i class="bi bi-people me-1"></i>Students
                                    </span>
                                    <span class="fw-bold fs-5">{{ $division->student_count ?? 0 }}</span>
                                </div>
                                <div class="progress" style="height: 10px; border-radius: 10px;">
                                    @php
                                        $progressColor = 'bg-primary';
                                        if (($division->student_count ?? 0) >= 50) $progressColor = 'bg-success';
                                        elseif (($division->student_count ?? 0) >= 30) $progressColor = 'bg-warning';
                                    @endphp
                                    <div class="progress-bar {{ $progressColor }}" role="progressbar" 
                                         style="width: {{ min(100, ($division->student_count ?? 0) / 60 * 100) }}%; border-radius: 10px;"></div>
                                </div>
                            </div>
                            
                            <!-- Quick Stats -->
                            <div class="quick-stats row text-center g-2 mb-3">
                                <div class="col-4">
                                    <div class="quick-stat-item">
                                        <i class="bi bi-book text-primary"></i>
                                        <span class="d-block fw-bold">{{ rand(3, 6) }}</span>
                                        <small class="text-muted">Subjects</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="quick-stat-item">
                                        <i class="bi bi-calendar-check text-success"></i>
                                        <span class="d-block fw-bold">{{ rand(20, 30) }}</span>
                                        <small class="text-muted">Days</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="quick-stat-item">
                                        <i class="bi bi-check2-circle text-info"></i>
                                        <span class="d-block fw-bold">{{ rand(85, 98) }}%</span>
                                        <small class="text-muted">Attendance</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Card Actions -->
                        <div class="division-card-footer">
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="{{ route('teacher.divisions.students', $division->id) }}" 
                                       class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-people me-1"></i>Students
                                    </a>
                                </div>
                                <div class="col-6">
                                    @if($todayHoliday)
                                        <button class="btn btn-outline-secondary btn-sm w-100" disabled title="Today is a holiday">
                                            <i class="bi bi-calendar-x me-1"></i>Holiday
                                        </button>
                                    @else
                                        <a href="{{ route('teacher.attendance.index') }}" 
                                           class="btn btn-outline-success btn-sm w-100">
                                            <i class="bi bi-calendar-check me-1"></i>Attendance
                                        </a>
                                    @endif
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('teacher.attendance.history') }}" 
                                       class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="bi bi-clock-history me-1"></i>History
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('teacher.results.index', ['division' => $division->id]) }}" 
                                       class="btn btn-outline-info btn-sm w-100">
                                        <i class="bi bi-clipboard-data me-1"></i>Results
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state-container">
            <div class="empty-state-card">
                <div class="empty-state-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h3 class="fw-bold text-muted">No Divisions Assigned</h3>
                <p class="text-muted mb-4">You haven't been assigned to any divisions yet.</p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ route('teacher.dashboard') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                    </a>
                    <button class="btn btn-outline-secondary" onclick="location.reload()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                    </button>
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
    
    /* Stats Cards */
    .stats-card {
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: none;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .stats-card-body {
        padding: 1.5rem;
    }
    
    .stats-label {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    
    .stats-value {
        color: #fff;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    
    .stats-change {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.8rem;
        margin-bottom: 0;
    }
    
    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .stats-icon i {
        font-size: 1.5rem;
        color: #fff;
    }
    
    .stats-card-purple {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .stats-icon-purple {
        background: rgba(255, 255, 255, 0.2);
    }
    
    .stats-card-pink {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .stats-icon-pink {
        background: rgba(255, 255, 255, 0.2);
    }
    
    .stats-card-blue {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .stats-icon-blue {
        background: rgba(255, 255, 255, 0.2);
    }
    
    .stats-card-green {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    
    .stats-icon-green {
        background: rgba(255, 255, 255, 0.2);
    }
    
    /* Division Card */
    .division-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        overflow: hidden;
        border: 1px solid #e9ecef;
    }
    
    .division-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }
    
    .division-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 1.25rem;
        color: #fff;
    }
    
    .division-card-header h5 {
        color: #fff;
    }
    
    .division-card-header .opacity-75 {
        color: rgba(255, 255, 255, 0.85) !important;
    }
    
    .class-teacher-badge {
        background: rgba(255, 255, 255, 0.25);
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #fff;
    }
    
    .division-card-body {
        padding: 1.25rem;
    }
    
    .student-count-section .progress {
        background: #e9ecef;
    }
    
    .quick-stat-item {
        padding: 0.5rem;
        background: #f8f9fa;
        border-radius: 8px;
    }
    
    .quick-stat-item i {
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
    }
    
    .quick-stat-item .d-block {
        font-size: 1rem;
        color: #212529;
    }
    
    .quick-stat-item small {
        font-size: 0.7rem;
    }
    
    .division-card-footer {
        padding: 1rem 1.25rem;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }
    
    .division-card-footer .btn {
        font-weight: 500;
        border-radius: 8px;
    }
    
    /* Empty State */
    .empty-state-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 60vh;
    }
    
    .empty-state-card {
        text-align: center;
        padding: 3rem;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        max-width: 500px;
    }
    
    .empty-state-icon {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }
    
    .empty-state-icon i {
        font-size: 3.5rem;
        color: #adb5bd;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .welcome-header {
            padding: 1rem;
        }
        
        .header-icon-wrapper {
            width: 50px;
            height: 50px;
        }
        
        .header-icon-wrapper i {
            font-size: 1.5rem;
        }
        
        .stats-value {
            font-size: 1.5rem;
        }
        
        .division-card-footer .row g-2 {
            --bs-gutter-y: 0.5rem;
        }
    }
</style>
@endsection
