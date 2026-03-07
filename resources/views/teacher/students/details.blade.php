@extends('layouts.teacher')

@section('title', 'Student Details')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb" class="mb-2">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('teacher.divisions.index') }}">Divisions</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('teacher.divisions.students', $student->division_id) }}">Students</a></li>
                            <li class="breadcrumb-item active">Details</li>
                        </ol>
                    </nav>
                    <h2 class="fw-bold" style="color: #667eea;">
                        <i class="bi bi-person-circle me-2"></i>Student Details
                    </h2>
                </div>
                <div>
                    <a href="{{ route('teacher.divisions.students', $student->division_id) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Student Info -->
        <div class="col-lg-4 mb-4">
            <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
                <div class="card-body p-4 text-center">
                    @if($student->photo_path)
                        <img src="{{ asset('storage/' . $student->photo_path) }}" 
                             alt="{{ $student->first_name }}" 
                             class="rounded-circle mb-3" 
                             style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #667eea;">
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                             style="width: 150px; height: 150px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <span class="text-white fw-bold" style="font-size: 4rem;">
                                {{ strtoupper(substr($student->first_name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    
                    <h4 class="fw-bold mb-1">{{ $student->first_name }} {{ $student->last_name }}</h4>
                    <p class="text-muted mb-2">{{ $student->email ?? $student->user->email ?? 'N/A' }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-primary">{{ $student->roll_number }}</span>
                        <span class="badge bg-info">{{ $student->admission_number }}</span>
                    </div>

                    <div class="text-start">
                        <div class="mb-2">
                            <small class="text-muted">Division</small>
                            <div class="fw-semibold">{{ $student->division->division_name ?? 'N/A' }}</div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Program</small>
                            <div class="fw-semibold">{{ $student->program->name ?? 'N/A' }}</div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Attendance</small>
                            <div class="fw-semibold">
                                <span class="badge {{ $attendancePercentage >= 75 ? 'bg-success' : 'bg-warning' }}">
                                    {{ $attendancePercentage }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Info -->
        <div class="col-lg-8 mb-4">
            <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-info-circle me-2 text-primary"></i>Personal Information
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Date of Birth</small>
                            <div class="fw-semibold">{{ $student->date_of_birth?->format('d M Y') ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Gender</small>
                            <div class="fw-semibold">{{ ucfirst($student->gender ?? 'N/A') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Blood Group</small>
                            <div class="fw-semibold">{{ $student->studentProfile->blood_group ?? $student->blood_group ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Phone</small>
                            <div class="fw-semibold">{{ $student->mobile_number ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-4 mb-3">
                        <i class="bi bi-people me-2 text-primary"></i>Parent/Guardian Information
                    </h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Father's Name</small>
                            <div class="fw-semibold">{{ $student->studentProfile->father_name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Father's Phone</small>
                            <div class="fw-semibold">{{ $student->studentProfile->father_phone ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Mother's Name</small>
                            <div class="fw-semibold">{{ $student->studentProfile->mother_name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Mother's Phone</small>
                            <div class="fw-semibold">{{ $student->studentProfile->mother_phone ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Emergency Contact</small>
                            <div class="fw-semibold">{{ $student->studentProfile->emergency_contact_phone ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-4 mb-3">
                        <i class="bi bi-geo-alt me-2 text-primary"></i>Address
                    </h6>
                    <p class="mb-0">{{ $student->current_address ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance -->
    <div class="row">
        <div class="col-12">
            <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-calendar-check me-2 text-primary"></i>Recent Attendance
                    </h5>
                    
                    @if($recentAttendance->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                        <th>Marked By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAttendance as $record)
                                        <tr>
                                            <td>{{ $record->date->format('d M Y') }}</td>
                                            <td>
                                                <span class="badge {{ $record->status == 'present' ? 'bg-success' : ($record->status == 'absent' ? 'bg-danger' : 'bg-warning') }}">
                                                    {{ ucfirst($record->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $record->remarks ?? '-' }}</td>
                                            <td>{{ $record->markedBy?->name ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No attendance records found</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
