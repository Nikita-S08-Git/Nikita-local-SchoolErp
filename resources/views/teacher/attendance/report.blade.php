@extends('layouts.teacher')

@section('title', 'Attendance Report')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-graph-up me-2"></i>Attendance Report</h2>
            <p class="text-muted mb-0">Detailed attendance statistics and analysis</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('teacher.attendance.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-house me-2"></i>Dashboard
            </a>
            <a href="{{ route('teacher.attendance.history') }}" class="btn btn-outline-info">
                <i class="bi bi-clock-history me-2"></i>History
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Select Division</label>
                    <select name="division_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Select Division --</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                {{ $division->division_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date', $startDate) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date', $endDate) }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-2"></i>Apply
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($stats)
        <!-- Summary Statistics -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-primary">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-check text-primary" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2 mb-1">Total Lectures</h6>
                        <h3 class="mb-0">{{ $stats['total_lectures'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-success">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2 mb-1">Present</h6>
                        <h3 class="mb-0">{{ $stats['total_present'] }}</h3>
                        <small class="text-success">{{ $stats['present_percentage'] }}%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-danger">
                    <div class="card-body text-center">
                        <i class="bi bi-x-circle text-danger" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2 mb-1">Absent</h6>
                        <h3 class="mb-0">{{ $stats['total_absent'] }}</h3>
                        <small class="text-danger">{{ $stats['absent_percentage'] }}%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-warning">
                    <div class="card-body text-center">
                        <i class="bi bi-exclamation-circle text-warning" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2 mb-1">Late</h6>
                        <h3 class="mb-0">{{ $stats['total_late'] }}</h3>
                        <small class="text-warning">{{ $stats['late_percentage'] }}%</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student-wise Statistics -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-people me-2"></i>Student-wise Attendance</h5>
                <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                    <i class="bi bi-printer me-2"></i>Print
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Roll No</th>
                                <th>Student Name</th>
                                <th class="text-center">Present</th>
                                <th class="text-center">Absent</th>
                                <th class="text-center">Late</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Attendance %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentWiseStats as $student)
                                @php
                                    $total = $student->total ?? 0;
                                    $percentage = $total > 0 ? round(($student->present_count / $total) * 100, 2) : 0;
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-primary">{{ $student->roll_number ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle me-2 bg-primary bg-gradient d-flex align-items-center justify-content-center text-white fw-bold"
                                                 style="width: 35px; height: 35px; min-width: 35px;">
                                                {{ strtoupper(substr($student->name ?? 'S', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $student->name }}</div>
                                                <small class="text-muted">{{ $student->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $student->present_count ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">{{ $student->absent_count ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning">{{ $student->late_count ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-semibold">{{ $total }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($percentage >= 75)
                                            <span class="badge bg-success">{{ $percentage }}%</span>
                                        @elseif($percentage >= 50)
                                            <span class="badge bg-warning">{{ $percentage }}%</span>
                                        @else
                                            <span class="badge bg-danger">{{ $percentage }}%</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @elseif(request('division_id'))
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">No data available</h5>
                <p class="text-muted">No attendance records found for the selected period</p>
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-filter text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">Select a division to view report</h5>
                <p class="text-muted">Choose a division from the dropdown above</p>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    @media print {
        .btn, .card-header button {
            display: none !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }
    }
</style>
@endpush
@endsection
