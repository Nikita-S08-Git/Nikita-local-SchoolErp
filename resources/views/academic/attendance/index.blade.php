@extends('layouts.app')

@section('title', 'Attendance Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1"><i class="bi bi-check-square me-2 text-primary"></i> Attendance Management</h3>
                    <p class="text-muted mb-0">Mark and manage student attendance</p>
                </div>
                <a href="{{ route('academic.attendance.report') }}" class="btn btn-info">
                    <i class="bi bi-graph-up"></i> View Reports
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Mark Attendance</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('academic.attendance.mark') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="academic_session_id" class="form-label">Academic Session <span class="text-danger">*</span></label>
                                <select class="form-select @error('academic_session_id') is-invalid @enderror" 
                                        id="academic_session_id" name="academic_session_id" required>
                                    <option value="">Select Academic Session</option>
                                    @foreach($sessions as $session)
                                        <option value="{{ $session->id }}" {{ old('academic_session_id') == $session->id ? 'selected' : '' }}>
                                            {{ $session->session_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_session_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="division_id" class="form-label">Division <span class="text-danger">*</span></label>
                                <select class="form-select @error('division_id') is-invalid @enderror" 
                                        id="division_id" name="division_id" required>
                                    <option value="">Select Division</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                            {{ $division->division_name }} - {{ $division->academicYear->session_name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('division_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                       id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-arrow-right-circle"></i> Proceed to Mark Attendance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Summary -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-calendar-day me-2"></i>Today's Attendance Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-primary mb-1">{{ \App\Models\Academic\Attendance::whereDate('date', today())->count() }}</h4>
                                <small class="text-muted">Total Marked</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-success mb-1">{{ \App\Models\Academic\Attendance::whereDate('date', today())->where('status', 'present')->count() }}</h4>
                                <small class="text-muted">Present</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-danger mb-1">{{ \App\Models\Academic\Attendance::whereDate('date', today())->where('status', 'absent')->count() }}</h4>
                                <small class="text-muted">Absent</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                @php
                                    $total = \App\Models\Academic\Attendance::whereDate('date', today())->count();
                                    $present = \App\Models\Academic\Attendance::whereDate('date', today())->where('status', 'present')->count();
                                    $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;
                                @endphp
                                <h4 class="text-info mb-1">{{ $percentage }}%</h4>
                                <small class="text-muted">Attendance Rate</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection