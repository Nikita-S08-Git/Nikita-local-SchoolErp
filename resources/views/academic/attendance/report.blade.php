@extends('layouts.app')

@section('page-title', 'Attendance Report')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>Attendance Report
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('academic.attendance.report') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="division_id" class="form-label">Division <span class="text-danger">*</span></label>
                                    <select name="division_id" id="division_id" class="form-select" required>
                                        <option value="">Select Division</option>
                                        @forelse($divisions as $division)
                                            <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                                {{ $division->division_name }}
                                            </option>
                                        @empty
                                            <option value="" disabled>No divisions available</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-search me-1"></i>Generate Report
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($attendanceData)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-calendar-range me-2"></i>Attendance Report Results
                    </h6>
                </div>
                <div class="card-body">
                    @if($attendanceData->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Total Students</th>
                                        <th>Present</th>
                                        <th>Absent</th>
                                        <th>Attendance %</th>
                                        <th>Students</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendanceData as $date => $records)
                                        @php
                                            $total = $records->count();
                                            $presentRecords = $records->filter(fn($r) => strtolower($r->status) === 'present');
                                            $absentRecords = $records->filter(fn($r) => strtolower($r->status) === 'absent');
                                            $present = $presentRecords->count();
                                            $absent = $absentRecords->count();
                                            $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;
                                            
                                            // Debug: Check first record
                                            $firstRecord = $records->first();
                                            $debugInfo = '';
                                            if ($firstRecord) {
                                                $debugInfo = 'StudentID:' . $firstRecord->student_id . '|';
                                                if ($firstRecord->student) {
                                                    $debugInfo .= 'HasStudent|Name:' . ($firstRecord->student->full_name ?? 'NULL') . '|First:' . ($firstRecord->student->first_name ?? 'NULL');
                                                } else {
                                                    $debugInfo .= 'NoStudentRelation';
                                                }
                                            }
                                            
                                            // Get student names - check each record individually
                                            $presentNames = [];
                                            foreach ($presentRecords as $r) {
                                                if ($r->student && !empty($r->student->full_name)) {
                                                    $presentNames[] = $r->student->full_name;
                                                } elseif ($r->student) {
                                                    $name = trim(($r->student->first_name ?? '') . ' ' . ($r->student->last_name ?? ''));
                                                    if ($name) {
                                                        $presentNames[] = $name;
                                                    }
                                                }
                                            }
                                            $presentStudents = implode(', ', array_unique($presentNames));
                                            
                                            $absentNames = [];
                                            foreach ($absentRecords as $r) {
                                                if ($r->student && !empty($r->student->full_name)) {
                                                    $absentNames[] = $r->student->full_name;
                                                } elseif ($r->student) {
                                                    $name = trim(($r->student->first_name ?? '') . ' ' . ($r->student->last_name ?? ''));
                                                    if ($name) {
                                                        $absentNames[] = $name;
                                                    }
                                                }
                                            }
                                            $absentStudents = implode(', ', array_unique($absentNames));
                                        @endphp
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</td>
                                            <td>{{ $total }}</td>
                                            <td><span class="badge bg-success">{{ $present }}</span></td>
                                            <td><span class="badge bg-danger">{{ $absent }}</span></td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%">
                                                        {{ $percentage }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info text-white" 
                                                        type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#students-{{ str_replace('-', '', $date) }}"
                                                        aria-expanded="false"
                                                        aria-controls="students-{{ str_replace('-', '', $date) }}">
                                                    <i class="bi bi-people"></i> View Students
                                                </button>
                                                <small class="d-block text-muted" style="font-size:10px;">{{ $debugInfo }}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="p-0">
                                                <div class="collapse" id="students-{{ str_replace('-', '', $date) }}">
                                                    <div class="p-3 bg-light">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <strong><i class="bi bi-check-circle text-success"></i> Present ({{ $present }}):</strong>
                                                                @if(!empty($presentStudents))
                                                                    <ul class="list-unstyled mb-0 mt-2 ms-3">
                                                                        @foreach(explode(', ', $presentStudents) as $name)
                                                                            <li><i class="bi bi-person-check text-success me-1"></i>{{ $name }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                @else
                                                                    <p class="mb-0 ms-3 text-muted">No present students</p>
                                                                @endif
                                                            </div>
                                                            <div class="col-md-6">
                                                                <strong><i class="bi bi-x-circle text-danger"></i> Absent ({{ $absent }}):</strong>
                                                                @if(!empty($absentStudents))
                                                                    <ul class="list-unstyled mb-0 mt-2 ms-3">
                                                                        @foreach(explode(', ', $absentStudents) as $name)
                                                                            <li><i class="bi bi-person-x text-danger me-1"></i>{{ $name }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                @else
                                                                    <p class="mb-0 ms-3 text-muted">No absent students</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>No attendance records found for the selected criteria.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection