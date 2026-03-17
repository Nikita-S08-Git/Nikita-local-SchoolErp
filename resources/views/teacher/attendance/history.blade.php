@extends('layouts.teacher')

@section('title', 'Attendance History')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-clock-history me-2"></i>Attendance History</h2>
            <p class="text-muted mb-0">View past attendance records</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('teacher.attendance.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-house me-2"></i>Dashboard
            </a>
            <a href="{{ route('teacher.attendance.report') }}" class="btn btn-outline-info">
                <i class="bi bi-graph-up me-2"></i>Reports
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Select Division</label>
                    <select name="division_id" class="form-select">
                        <option value="">All Divisions</option>
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
                        <i class="bi bi-search me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Records -->
    @if($selectedDivision && $attendances->count() > 0)
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Attendance Records</h5>
                <span class="badge bg-primary">{{ $attendances->total() }} records</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Student Name</th>
                                <th>Roll No</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Remarks</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold">{{ $attendance->date->format('d M Y') }}</div>
                                        <small class="text-muted">{{ $attendance->date->format('l') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle me-2 bg-primary bg-gradient d-flex align-items-center justify-content-center text-white fw-bold"
                                                 style="width: 35px; height: 35px; min-width: 35px;">
                                                {{ strtoupper(substr($attendance->student->first_name ?? 'S', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $attendance->student->full_name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $attendance->student->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $attendance->student->roll_number ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div>{{ $attendance->timetable->subject->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $attendance->timetable->subject->code ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $attendance->status_color }}">
                                            <i class="bi bi-{{ $attendance->status_icon }} me-1"></i>
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $attendance->remarks ?? '-' }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        @if($attendance->marked_by === auth()->id())
                                            <a href="{{ route('teacher.attendance.edit', $attendance->id) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil me-1"></i>Edit
                                            </a>
                                        @else
                                            <span class="text-muted small">Marked by another teacher</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 p-3">
                {{ $attendances->links() }}
            </div>
        </div>
    @elseif($selectedDivision)
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">No attendance records found</h5>
                <p class="text-muted">Try adjusting your date range or filters</p>
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-filter text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">Select a division to view history</h5>
                <p class="text-muted">Use the filters above to view attendance records</p>
            </div>
        </div>
    @endif
</div>
@endsection
