@extends('layouts.app')

@section('page-title', 'Mark Attendance')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-check me-2"></i>Mark Attendance
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('academic.attendance.mark') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="division_id" class="form-label">Division <span class="text-danger">*</span></label>
                                    <select name="division_id" id="division_id" class="form-select" required>
                                        <option value="">Select Division</option>
                                        @foreach($divisions as $division)
                                            <option value="{{ $division->id }}">
                                                {{ $division->academicYear->name ?? 'All Years' }} - {{ $division->division_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="academic_session_id" class="form-label">Academic Session <span class="text-danger">*</span></label>
                                    <select name="academic_session_id" id="academic_session_id" class="form-select" required>
                                        <option value="">Select Session</option>
                                        @foreach($sessions as $session)
                                            <option value="{{ $session->id }}" {{ $session->is_active ? 'selected' : '' }}>
                                                {{ $session->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                                    <input type="date" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-arrow-right me-1"></i>Proceed to Mark Attendance
                            </button>
                            <a href="{{ route('academic.attendance.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection