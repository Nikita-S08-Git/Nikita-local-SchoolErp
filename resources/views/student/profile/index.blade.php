@extends('student.layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-person-circle me-2 text-primary"></i>My Profile</h2>
                    <p class="text-muted mb-0">View and manage your profile information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('student.profile.edit') }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Edit Profile
                    </a>
                    <a href="{{ route('student.profile.change-password') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-key me-1"></i>Change Password
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Profile Card -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="position-relative d-inline-block mb-3">
                        @if($student->photo)
                            <img src="{{ asset('storage/' . $student->photo) }}" 
                                 alt="{{ $student->name }}" 
                                 class="rounded-circle mb-3" 
                                 width="150" 
                                 height="150"
                                 style="object-fit: cover; border: 4px solid #667eea;">
                        @else
                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" 
                                 style="width: 150px; height: 150px; font-size: 3rem; border: 4px solid #667eea;">
                                {{ substr($student->first_name, 0, 1) }}
                            </div>
                        @endif
                        <a href="{{ route('student.profile.edit') }}" class="position-absolute bottom-0 end-0 btn btn-primary btn-sm rounded-circle" style="width: 35px; height: 35px;">
                            <i class="bi bi-camera"></i>
                        </a>
                    </div>
                    
                    <h4 class="mb-1 fw-bold">{{ $student->name }}</h4>
                    <p class="text-muted mb-3">{{ $student->email }}</p>
                    
                    <div class="mb-3">
                        <span class="badge bg-primary me-1">
                            <i class="bi bi-mortarboard me-1"></i>{{ $student->division->division_name ?? 'N/A' }}
                        </span>
                        <span class="badge bg-success me-1">
                            <i class="bi bi-check-circle me-1"></i>{{ ucfirst($student->student_status) }}
                        </span>
                        @if($student->blood_group)
                            <span class="badge bg-danger">
                                <i class="bi bi-droplet me-1"></i>{{ $student->blood_group }}
                            </span>
                        @endif
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('student.profile.edit') }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i>Edit Profile
                        </a>
                        <a href="{{ route('student.profile.change-password') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-key me-1"></i>Change Password
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow-sm border-0 mt-3" style="border-radius: 15px;">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3"><i class="bi bi-graph-up me-2 text-primary"></i>Quick Stats</h6>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Attendance</span>
                            <strong>{{ $attendancePercentage ?? 0 }}%</strong>
                        </div>
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar bg-{{ ($attendancePercentage ?? 0) >= 75 ? 'success' : 'danger' }}" 
                                 style="width: {{ $attendancePercentage ?? 0 }}%"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Roll Number</span>
                        <strong>{{ $student->roll_number ?? 'N/A' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Admission No.</span>
                        <strong>{{ $student->admission_number ?? 'N/A' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Academic Year</span>
                        <strong>{{ $student->academic_year ?? 'N/A' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Full Name</label>
                            <p class="fw-semibold mb-0">{{ $student->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Email Address</label>
                            <p class="fw-semibold mb-0">{{ $student->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Roll Number</label>
                            <p class="fw-semibold mb-0">
                                <span class="badge bg-primary">{{ $student->roll_number ?? 'N/A' }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Admission Number</label>
                            <p class="fw-semibold mb-0">{{ $student->admission_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Division</label>
                            <p class="fw-semibold mb-0">
                                <span class="badge bg-info">{{ $student->division->division_name ?? 'N/A' }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Program</label>
                            <p class="fw-semibold mb-0">{{ $student->program->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Date of Birth</label>
                            <p class="fw-semibold mb-0">
                                @if($student->date_of_birth)
                                    <i class="bi bi-calendar me-1"></i>{{ $student->date_of_birth->format('d M Y') }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Gender</label>
                            <p class="fw-semibold mb-0">
                                @if($student->gender)
                                    <i class="bi bi-{{ $student->gender === 'male' ? 'gender-male' : ($student->gender === 'female' ? 'gender-female' : 'question') }} me-1"></i>
                                    {{ ucfirst($student->gender) }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Contact Number</label>
                            <p class="fw-semibold mb-0">
                                @if($student->mobile_number)
                                    <i class="bi bi-telephone me-1"></i>{{ $student->mobile_number }}
                                @else
                                    Not provided
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Blood Group</label>
                            <p class="fw-semibold mb-0">
                                @if($student->blood_group)
                                    <span class="badge bg-danger">{{ $student->blood_group }}</span>
                                @else
                                    Not provided
                                @endif
                            </p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small mb-1">Address</label>
                            <p class="fw-semibold mb-0">
                                @if($student->current_address)
                                    <i class="bi bi-geo-alt me-1"></i>{{ $student->current_address }}
                                @else
                                    Not provided
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="card shadow-sm border-0 mt-4" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-mortarboard me-2 text-primary"></i>Academic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Current Program</label>
                            <p class="fw-semibold mb-0">{{ $student->program->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Academic Year</label>
                            <p class="fw-semibold mb-0">{{ $student->academic_year ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Division</label>
                            <p class="fw-semibold mb-0">
                                <span class="badge bg-info">{{ $student->division->division_name ?? 'N/A' }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Student Status</label>
                            <p class="fw-semibold mb-0">
                                <span class="badge bg-{{ $student->student_status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($student->student_status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Admission Date</label>
                            <p class="fw-semibold mb-0">
                                @if($student->admission_date)
                                    <i class="bi bi-calendar-check me-1"></i>{{ $student->admission_date->format('d M Y') }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
