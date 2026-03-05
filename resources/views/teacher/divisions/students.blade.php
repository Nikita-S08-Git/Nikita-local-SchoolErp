@extends('layouts.teacher')

@section('title', 'Division Students')

@section('content')
<style>
    .student-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.2s ease;
    }
    
    .student-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.12);
    }
    
    .student-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.5rem;
    }
    
    .attendance-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    
    .attendance-high { background: #c6f6d5; color: #22543d; }
    .attendance-medium { background: #feebc8; color: #7c2d12; }
    .attendance-low { background: #fed7d7; color: #742a2a; }
</style>

<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb" class="mb-2">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('teacher.divisions.index') }}">Divisions</a></li>
                            <li class="breadcrumb-item active">Students</li>
                        </ol>
                    </nav>
                    <h2 class="fw-bold" style="color: #667eea;">
                        <i class="bi bi-people me-2"></i>{{ $division->academicYear->name ?? '' }} - {{ $division->division_name }}
                    </h2>
                    <p class="text-muted mb-0">
                        <i class="bi bi-mortarboard me-1"></i>{{ $division->program->name ?? 'N/A' }}
                        &bull; <i class="bi bi-person me-1"></i>{{ $students->total() }} Students
                    </p>
                </div>
                <div>
                    <a href="{{ route('teacher.divisions.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($students->total() > 0)
        <div class="row">
            @foreach($students as $student)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="student-card bg-white p-3 h-100">
                        <div class="d-flex align-items-start mb-3">
                            <div class="student-avatar me-3">
                                {{ strtoupper(substr($student->first_name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1">{{ $student->first_name }} {{ $student->last_name }}</h6>
                                <p class="text-muted mb-1 small">
                                    <i class="bi bi-card-text me-1"></i>{{ $student->roll_number }}
                                </p>
                                @php
                                    $attendanceClass = 'attendance-high';
                                    if (($student->attendance_percentage ?? 0) < 75) $attendanceClass = 'attendance-low';
                                    elseif (($student->attendance_percentage ?? 0) < 90) $attendanceClass = 'attendance-medium';
                                @endphp
                                <span class="attendance-badge {{ $attendanceClass }}">
                                    <i class="bi bi-calendar-check me-1"></i>{{ $student->attendance_percentage ?? 0 }}%
                                </span>
                            </div>
                        </div>
                        
                        <hr class="my-2">
                        
                        <div class="small">
                            <div class="mb-2">
                                <span class="text-muted"><i class="bi bi-person me-1"></i>Parent:</span>
                                <span class="fw-semibold">{{ $student->studentProfile->father_name ?? $student->studentProfile->mother_name ?? 'N/A' }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted"><i class="bi bi-telephone me-1"></i>Phone:</span>
                                <span class="fw-semibold">{{ $student->studentProfile->father_phone ?? $student->studentProfile->mother_phone ?? 'N/A' }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted"><i class="bi bi-droplet me-1"></i>Blood Group:</span>
                                <span class="fw-semibold">{{ $student->studentProfile->blood_group ?? $student->blood_group ?? 'N/A' }}</span>
                            </div>
                        </div>
                        
                        <div class="d-grid mt-3">
                            <a href="{{ route('teacher.students.details', $student->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if($students->hasPages())
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Student pagination">
                <ul class="pagination">
                    {{ $students->appends(request()->query())->links() }}
                </ul>
            </nav>
        </div>
        @endif
    @else
        <div class="card" style="border: none; border-radius: 12px;">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 5rem;"></i>
                <h5 class="text-muted mt-3">No Students Found</h5>
                <p class="text-muted">This division doesn't have any active students.</p>
            </div>
        </div>
    @endif
</div>
@endsection
