@extends('layouts.teacher')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Check if teacher has assigned divisions -->
    @if(!isset($divisions) || $divisions->count() === 0)
        <!-- No Division Assigned Alert -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning d-flex align-items-center" role="alert" style="border-radius: 14px; border: none; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
                    <i class="fas fa-exclamation-triangle-fill fs-2 me-3"></i>
                    <div class="flex-grow-1">
                        <h4 class="mb-2">No Division Assigned Yet</h4>
                        <p class="mb-0">You haven't been assigned to any division yet. Please contact the administrator to assign you to a division. Once assigned, you'll be able to:</p>
                        <ul class="mb-0 mt-2">
                            <li>Mark attendance for your students</li>
                            <li>View student details and performance</li>
                            <li>Access your teaching schedule</li>
                            <li>Manage division-related activities</li>
                        </ul>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('teacher.profile') }}" class="btn btn-light">
                            <i class="fas fa-user me-1"></i> My Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Limited Dashboard -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm h-100" style="border-radius: 14px; border: none; opacity: 0.6;">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 70px; height: 70px; font-size: 2rem;">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5 class="mb-2">Students</h5>
                        <p class="h2 text-muted mb-0">0</p>
                        <small class="text-muted">No division assigned</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm h-100" style="border-radius: 14px; border: none; opacity: 0.6;">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 70px; height: 70px; font-size: 2rem;">
                            <i class="fas fa-chalkboard"></i>
                        </div>
                        <h5 class="mb-2">Divisions</h5>
                        <p class="h2 text-muted mb-0">0</p>
                        <small class="text-muted">Awaiting assignment</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm" style="border-radius: 14px; border: none;">
                    <div class="card-body text-center py-5">
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 100px; height: 100px;">
                            <i class="fas fa-user-clock fa-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted mb-2">Awaiting Division Assignment</h5>
                        <p class="text-muted mb-0">Your teaching assignments will appear here once the administrator assigns you to a division.</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Normal Dashboard (when divisions are assigned) -->
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1"><i class="fas fa-chalkboard-teacher me-2 text-primary"></i>Teacher Dashboard</h2>
                        <p class="text-muted mb-0">Manage your classes, students, and activities</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm h-100" style="border-radius: 14px; border: none;">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 70px; height: 70px; font-size: 2rem;">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h5 class="mb-2">Total Students</h5>
                        <p class="display-4 fw-bold text-primary mb-0">{{ number_format($totalStudents ?? 0) }}</p>
                        <small class="text-muted">In your assigned divisions</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm h-100" style="border-radius: 14px; border: none;">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 70px; height: 70px; font-size: 2rem;">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5 class="mb-2">Assigned Division</h5>
                        <p class="h4 fw-bold text-success mb-0">{{ $divisions->first()->division_name ?? 'Not Assigned' }}</p>
                        <small class="text-muted">Primary assignment</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm h-100" style="border-radius: 14px; border: none;">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-info bg-opacity-10 text-info d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 70px; height: 70px; font-size: 2rem;">
                            <i class="fas fa-id-badge"></i>
                        </div>
                        <h5 class="mb-2">Your Profile</h5>
                        <p class="h6 fw-bold text-info mb-0">{{ $teacher->name ?? 'N/A' }}</p>
                        <small class="text-muted">Teacher ID: {{ $teacher->id ?? 'N/A' }}</small>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

