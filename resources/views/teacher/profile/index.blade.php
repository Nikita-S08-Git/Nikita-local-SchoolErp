@extends('layouts.teacher')

@section('title', 'My Profile')

@section('content')
<style>
    .profile-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    
    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem;
        text-align: center;
        color: white;
    }
    
    .profile-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 5px solid white;
        object-fit: cover;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .info-section {
        padding: 2rem;
    }
    
    .info-group {
        margin-bottom: 1.5rem;
    }
    
    .info-label {
        font-weight: 600;
        color: #718096;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    .info-value {
        color: #2d3748;
        font-size: 1rem;
        padding: 0.75rem;
        background: #f7fafc;
        border-radius: 8px;
    }
    
    .badge-custom {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }
</style>

<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold" style="color: #667eea;">
                        <i class="bi bi-person-circle me-2"></i>My Profile
                    </h2>
                    <p class="text-muted mb-0">View and manage your profile information</p>
                </div>
                <div>
                    <a href="{{ route('teacher.profile.edit') }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="profile-card">
                <div class="profile-header">
                    @if($teacherProfile && $teacherProfile->photo_path)
                        <img src="{{ asset('storage/' . $teacherProfile->photo_path) }}" 
                             alt="{{ $teacher->name }}" 
                             class="profile-avatar mb-3">
                    @else
                        <div class="profile-avatar mb-3 d-flex align-items-center justify-content-center mx-auto"
                             style="background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);">
                            <span class="text-white fw-bold" style="font-size: 4rem;">
                                {{ strtoupper(substr($teacher->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    <h4 class="fw-bold mb-1">{{ $teacher->name }}</h4>
                    <p class="mb-2 opacity-75">{{ $teacher->email }}</p>
                    @if($teacherProfile && $teacherProfile->designation)
                        <span class="badge bg-white text-primary badge-custom">
                            {{ $teacherProfile->designation }}
                        </span>
                    @endif
                </div>
                <div class="info-section">
                    <div class="info-group">
                        <div class="info-label">Employee ID</div>
                        <div class="info-value">{{ $teacherProfile->employee_id ?? 'N/A' }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Phone</div>
                        <div class="info-value">
                            <i class="bi bi-telephone me-2 text-primary"></i>
                            {{ $teacherProfile->phone ?? 'Not provided' }}
                        </div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Joined</div>
                        <div class="info-value">
                            <i class="bi bi-calendar-event me-2 text-primary"></i>
                            {{ $teacherProfile->joining_date ? $teacherProfile->joining_date->format('d M Y') : 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="profile-card bg-white h-100">
                <div class="info-section">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-info-circle me-2 text-primary"></i>Personal Information
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label">Date of Birth</div>
                                <div class="info-value">
                                    <i class="bi bi-calendar3 me-2"></i>
                                    {{ $teacherProfile->date_of_birth ? $teacherProfile->date_of_birth->format('d M Y') : 'Not provided' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label">Gender</div>
                                <div class="info-value">
                                    <i class="bi bi-gender-ambiguous me-2"></i>
                                    {{ ucfirst($teacherProfile->gender ?? 'Not provided') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label">Blood Group</div>
                                <div class="info-value">
                                    <i class="bi bi-droplet me-2 text-danger"></i>
                                    {{ $teacherProfile->blood_group ?? 'Not provided' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label">Marital Status</div>
                                <div class="info-value">
                                    <i class="bi bi-heart me-2"></i>
                                    {{ ucfirst($teacherProfile->marital_status ?? 'Not provided') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label">Qualification</div>
                                <div class="info-value">
                                    <i class="bi bi-mortarboard me-2"></i>
                                    {{ $teacherProfile->qualification ?? 'Not provided' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label">Specialization</div>
                                <div class="info-value">
                                    <i class="bi bi-book me-2"></i>
                                    {{ $teacherProfile->specialization ?? 'Not provided' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label">Experience</div>
                                <div class="info-value">
                                    <i class="bi bi-briefcase me-2"></i>
                                    {{ $teacherProfile ? $teacherProfile->formatted_experience : '0 months' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label">Marital Status</div>
                                <div class="info-value">
                                    <i class="bi bi-heart me-2"></i>
                                    {{ ucfirst($teacherProfile->marital_status ?? 'Not provided') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">Current Address</div>
                        <div class="info-value">
                            <i class="bi bi-geo-alt me-2"></i>
                            {{ $teacherProfile->current_address ?? 'Not provided' }}
                            @if($teacherProfile->city || $teacherProfile->state || $teacherProfile->pincode)
                                <br>
                                {{ implode(', ', array_filter([$teacherProfile->city, $teacherProfile->state, $teacherProfile->pincode])) }}
                            @endif
                        </div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">Permanent Address</div>
                        <div class="info-value">
                            <i class="bi bi-house-door me-2"></i>
                            {{ $teacherProfile->permanent_address ?? 'Not provided' }}
                        </div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">Emergency Contact</div>
                        <div class="info-value">
                            <i class="bi bi-person-fill me-2"></i>
                            {{ $teacherProfile->emergency_contact_name ?? 'Not provided' }}
                            @if($teacherProfile->emergency_contact_phone)
                                <br>
                                <i class="bi bi-telephone me-2"></i>
                                {{ $teacherProfile->emergency_contact_phone }}
                                @if($teacherProfile->emergency_contact_relation)
                                    ({{ $teacherProfile->emergency_contact_relation }})
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Divisions -->
    <div class="row">
        <div class="col-12">
            <div class="profile-card bg-white">
                <div class="info-section">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-people me-2 text-primary"></i>Assigned Divisions
                        </h5>
                        <a href="{{ route('teacher.divisions.index') }}" class="btn btn-sm btn-outline-primary">
                            View All <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>

                    @if($divisions->count() > 0)
                        <div class="row">
                            @foreach($divisions as $division)
                                <div class="col-md-4 mb-3">
                                    <div class="p-3" style="background: #f8f9ff; border-radius: 10px; border: 1px solid #e2e8f0;">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="fw-bold mb-1 text-primary">
                                                    Division {{ $division->division_name }}
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-building me-1"></i>
                                                    {{ $division->program->name ?? 'N/A' }}
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    {{ $division->session->session_name ?? 'N/A' }}
                                                </small>
                                            </div>
                                        </div>
                                        <a href="{{ route('teacher.students.index', ['division_id' => $division->id]) }}"
                                           class="btn btn-sm btn-primary mt-2">
                                            <i class="bi bi-people me-1"></i>View Students
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No divisions assigned yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
