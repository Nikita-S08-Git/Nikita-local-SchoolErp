@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'System Settings')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-cog me-2 text-primary"></i>System Settings</h2>
                    <p class="text-muted mb-0">Configure school information and system preferences</p>
                </div>
                <a href="{{ route('dashboard.admin') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- College Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-university me-2 text-primary"></i>College Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">College Name <span class="text-danger">*</span></label>
                            <input type="text" name="college_name" class="form-control @error('college_name') is-invalid @enderror" 
                                   value="{{ old('college_name', $settings['college_name'] ?? '') }}" required>
                            @error('college_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">College Email <span class="text-danger">*</span></label>
                                <input type="email" name="college_email" class="form-control @error('college_email') is-invalid @enderror" 
                                       value="{{ old('college_email', $settings['college_email'] ?? '') }}" required>
                                @error('college_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">College Phone</label>
                                <input type="text" name="college_phone" class="form-control @error('college_phone') is-invalid @enderror" 
                                       value="{{ old('college_phone', $settings['college_phone'] ?? '') }}" placeholder="Enter phone number">
                                @error('college_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">College Address</label>
                            <textarea name="college_address" class="form-control @error('college_address') is-invalid @enderror" 
                                      rows="3" placeholder="Enter college address">{{ old('college_address', $settings['college_address'] ?? '') }}</textarea>
                            @error('college_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Affiliation Number</label>
                            <input type="text" name="affiliation_number" class="form-control @error('affiliation_number') is-invalid @enderror" 
                                   value="{{ old('affiliation_number', $settings['affiliation_number'] ?? '') }}" placeholder="e.g., University Affiliation No.">
                            <small class="text-muted">University/Board affiliation number</small>
                            @error('affiliation_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save College Info
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Academic Settings -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-graduation-cap me-2 text-primary"></i>Academic Settings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Academic Year Start</label>
                                <input type="text" name="academic_year_start" class="form-control @error('academic_year_start') is-invalid @enderror" 
                                       value="{{ old('academic_year_start', $settings['academic_year_start']) }}" placeholder="e.g., 01-06">
                                <small class="text-muted">Format: MM-DD (e.g., 01-06 for June 1st)</small>
                                @error('academic_year_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimum Attendance Required (%)</label>
                                <input type="number" name="attendance_required" class="form-control @error('attendance_required') is-invalid @enderror" 
                                       value="{{ old('attendance_required', $settings['attendance_required']) }}" min="0" max="100">
                                <small class="text-muted">Minimum % required for exam eligibility</small>
                                @error('attendance_required')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Academic Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Fee Settings -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-rupee-sign me-2 text-primary"></i>Fee Settings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Late Fee Percentage (%)</label>
                                <input type="number" name="fee_late_fee_percent" class="form-control @error('fee_late_fee_percent') is-invalid @enderror" 
                                       value="{{ old('fee_late_fee_percent', $settings['fee_late_fee_percent']) }}" min="0" max="100">
                                <small class="text-muted">Late fee % applied on overdue payments</small>
                                @error('fee_late_fee_percent')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Library Fine Per Day (₹)</label>
                                <input type="number" name="library_fine_per_day" class="form-control @error('library_fine_per_day') is-invalid @enderror" 
                                       value="{{ old('library_fine_per_day', $settings['library_fine_per_day']) }}" min="0">
                                <small class="text-muted">Fine amount per day for overdue books</small>
                                @error('library_fine_per_day')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Fee Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- System Information Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Quick Info</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">System Version</h6>
                        <p class="mb-0 fw-semibold">School ERP v2.0</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Laravel Version</h6>
                        <p class="mb-0 fw-semibold">{{ app()->version() }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">PHP Version</h6>
                        <p class="mb-0 fw-semibold">{{ PHP_VERSION }}</p>
                    </div>
                    <hr>
                    <a href="{{ route('admin.settings.system') }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-cogs me-1"></i> View System Information
                    </a>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0 ps-3">
                        <li class="mb-2">Set academic year start date for accurate session tracking</li>
                        <li class="mb-2">Adjust minimum attendance % based on university norms</li>
                        <li class="mb-2">Configure late fees to encourage timely payments</li>
                        <li>Set library fines to ensure book returns</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
