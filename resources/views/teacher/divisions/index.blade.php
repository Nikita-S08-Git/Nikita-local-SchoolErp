@extends('layouts.teacher')

@section('title', 'My Divisions')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold" style="color: #667eea;">
                        <i class="bi bi-people me-2"></i>My Assigned Divisions
                    </h2>
                    <p class="text-muted mb-0">View and manage your assigned divisions</p>
                </div>
                <div>
                    <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($divisions->count() > 0)
        <div class="row">
            @foreach($divisions as $division)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="fw-bold text-primary mb-1">
                                        {{ $division->academicYear->name ?? 'N/A' }} - {{ $division->division_name }}
                                    </h5>
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-people me-1"></i>
                                        {{ $division->student_count ?? 0 }} Students
                                    </p>
                                </div>
                                @if($division->is_class_teacher ?? false)
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-star me-1"></i>Class Teacher
                                    </span>
                                @endif
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <small class="text-muted">Students</small>
                                    <small class="fw-bold">{{ $division->student_count ?? 0 }}</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: {{ min(100, ($division->student_count ?? 0) / 60 * 100) }}%"></div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="{{ route('teacher.divisions.students', $division->id) }}" 
                                   class="btn btn-primary">
                                    <i class="bi bi-people me-1"></i>View Students
                                </a>
                                <a href="{{ route('teacher.attendance.create', $division->id) }}" 
                                   class="btn btn-outline-success">
                                    <i class="bi bi-calendar-check me-1"></i>Mark Attendance
                                </a>
                                <a href="{{ route('teacher.attendance.history', $division->id) }}" 
                                   class="btn btn-outline-secondary">
                                    <i class="bi bi-clock-history me-1"></i>Attendance History
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card" style="border: none; border-radius: 12px;">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 5rem;"></i>
                <h5 class="text-muted mt-3">No Divisions Assigned</h5>
                <p class="text-muted">You haven't been assigned to any divisions yet.</p>
            </div>
        </div>
    @endif
</div>
@endsection
