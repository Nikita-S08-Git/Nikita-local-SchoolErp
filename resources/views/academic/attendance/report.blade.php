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
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-calendar-range me-2"></i>Attendance Report Results
                    </h6>
                    <div class="d-flex gap-2">
                        @if(request('division_id'))
                        <button type="button" class="btn btn-light btn-sm" onclick="openStudentsModal()">
                            <i class="bi bi-people me-1"></i>View All Students
                        </button>
                        <a href="{{ route('academic.attendance.report.excel', ['division_id' => request('division_id'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="btn btn-success btn-sm">
                            <i class="bi bi-file-earmark-excel me-1"></i>Download Excel
                        </a>
                        <a href="{{ route('academic.attendance.report.download', ['division_id' => request('division_id'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="btn btn-danger btn-sm">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Download PDF
                        </a>
                        @endif
                    </div>
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

                                            // Get student names - check each record individually
                                            $presentStudents = [];
                                            foreach ($presentRecords as $r) {
                                                if ($r->student && !empty($r->student->full_name)) {
                                                    $presentStudents[] = [
                                                        'id' => $r->student->id,
                                                        'name' => $r->student->full_name,
                                                        'roll' => $r->student->roll_number ?? 'N/A'
                                                    ];
                                                } elseif ($r->student) {
                                                    $name = trim(($r->student->first_name ?? '') . ' ' . ($r->student->last_name ?? ''));
                                                    if ($name) {
                                                        $presentStudents[] = [
                                                            'id' => $r->student->id,
                                                            'name' => $name,
                                                            'roll' => $r->student->roll_number ?? 'N/A'
                                                        ];
                                                    }
                                                }
                                            }

                                            $absentStudents = [];
                                            foreach ($absentRecords as $r) {
                                                if ($r->student && !empty($r->student->full_name)) {
                                                    $absentStudents[] = [
                                                        'id' => $r->student->id,
                                                        'name' => $r->student->full_name,
                                                        'roll' => $r->student->roll_number ?? 'N/A'
                                                    ];
                                                } elseif ($r->student) {
                                                    $name = trim(($r->student->first_name ?? '') . ' ' . ($r->student->last_name ?? ''));
                                                    if ($name) {
                                                        $absentStudents[] = [
                                                            'id' => $r->student->id,
                                                            'name' => $name,
                                                            'roll' => $r->student->roll_number ?? 'N/A'
                                                        ];
                                                    }
                                                }
                                            }
                                            
                                            $safeDate = str_replace('-', '', $date);
                                        @endphp
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</td>
                                            <td class="fw-bold">{{ $total }}</td>
                                            <td><span class="badge bg-success">{{ $present }}</span></td>
                                            <td><span class="badge bg-danger">{{ $absent }}</span></td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar {{ $percentage >= 75 ? 'bg-success' : ($percentage >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $percentage }}%">
                                                        {{ $percentage }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary"
                                                        type="button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#studentsModal-{{ $safeDate }}"
                                                        aria-expanded="false">
                                                    <i class="bi bi-people-fill"></i> View Students ({{ $total }})
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Student Details Modal -->
                                        <div class="modal fade" id="studentsModal-{{ $safeDate }}" tabindex="-1" aria-labelledby="studentsModalLabel-{{ $safeDate }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title" id="studentsModalLabel-{{ $safeDate }}">
                                                            <i class="bi bi-calendar-event me-2"></i>
                                                            Student Attendance - {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row g-3">
                                                            <!-- Present Students -->
                                                            <div class="col-md-6">
                                                                <div class="card border-success h-100">
                                                                    <div class="card-header bg-success text-white">
                                                                        <h6 class="mb-0">
                                                                            <i class="bi bi-check-circle-fill"></i> Present ({{ $present }})
                                                                        </h6>
                                                                    </div>
                                                                    <div class="card-body p-2">
                                                                        @if(count($presentStudents) > 0)
                                                                            <ul class="list-group list-group-flush">
                                                                                @foreach($presentStudents as $student)
                                                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                                        <div>
                                                                                            <i class="bi bi-person-check-fill text-success me-2"></i>
                                                                                            <span class="fw-medium">{{ $student['name'] }}</span>
                                                                                        </div>
                                                                                        <span class="badge bg-secondary">{{ $student['roll'] }}</span>
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                        @else
                                                                            <p class="text-muted text-center mb-0">No present students</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Absent Students -->
                                                            <div class="col-md-6">
                                                                <div class="card border-danger h-100">
                                                                    <div class="card-header bg-danger text-white">
                                                                        <h6 class="mb-0">
                                                                            <i class="bi bi-x-circle-fill"></i> Absent ({{ $absent }})
                                                                        </h6>
                                                                    </div>
                                                                    <div class="card-body p-2">
                                                                        @if(count($absentStudents) > 0)
                                                                            <ul class="list-group list-group-flush">
                                                                                @foreach($absentStudents as $student)
                                                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                                        <div>
                                                                                            <i class="bi bi-person-x-fill text-danger me-2"></i>
                                                                                            <span class="fw-medium">{{ $student['name'] }}</span>
                                                                                        </div>
                                                                                        <span class="badge bg-secondary">{{ $student['roll'] }}</span>
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                        @else
                                                                            <p class="text-muted text-center mb-0">No absent students</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Summary Stats -->
                                                        <div class="row mt-3">
                                                            <div class="col-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body py-2">
                                                                        <div class="row text-center">
                                                                            <div class="col-4">
                                                                                <div class="stat-box">
                                                                                    <div class="fs-4 fw-bold text-primary">{{ $total }}</div>
                                                                                    <div class="small text-muted">Total</div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-4">
                                                                                <div class="stat-box">
                                                                                    <div class="fs-4 fw-bold text-success">{{ $present }}</div>
                                                                                    <div class="small text-muted">Present</div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-4">
                                                                                <div class="stat-box">
                                                                                    <div class="fs-4 fw-bold text-danger">{{ $absent }}</div>
                                                                                    <div class="small text-muted">Absent</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            <i class="bi bi-x-circle"></i> Close
                                                        </button>
                                                        <button type="button" class="btn btn-primary" onclick="window.print()">
                                                            <i class="bi bi-printer"></i> Print
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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

<style>
/* Modal Styles */
.modal-content {
    border-radius: 0.75rem;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}

.modal-header {
    border-radius: 0.75rem 0.75rem 0 0;
    border-bottom: none;
}

.modal-body {
    padding: 1.5rem;
}

.list-group-item {
    border: none;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s ease;
}

.list-group-item:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.list-group-item:last-child {
    border-bottom: none;
}

.stat-box {
    padding: 0.75rem;
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Modal animation */
.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
}

.modal.fade .modal-content {
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-dialog {
        max-width: 95%;
        margin: 1rem auto;
    }
    
    .modal-body {
        padding: 1rem;
    }
}

/* Print styles */
@media print {
    .modal-footer {
        display: none;
    }
}
</style>

<!-- Students Modal -->
<div class="modal fade" id="studentsListModal" tabindex="-1" aria-labelledby="studentsListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="studentsListModalLabel">
                    <i class="bi bi-people me-2"></i>Students in Division
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="studentsTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Roll No</th>
                                <th>Student Name</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTableBody">
                            <tr>
                                <td colspan="2" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="noStudentsMessage" class="text-center py-4" style="display: none;">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">No students found</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openStudentsModal() {
    const divisionId = document.querySelector('select[name="division_id"]').value;
    if (!divisionId) {
        alert('Please select a division first');
        return;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('studentsListModal'));
    const tbody = document.getElementById('studentsTableBody');
    const noStudentsMsg = document.getElementById('noStudentsMessage');
    
    // Show loading state
    tbody.innerHTML = '<tr><td colspan="2" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
    noStudentsMsg.style.display = 'none';
    document.getElementById('studentsTable').style.display = 'table';
    
    modal.show();
    
    // Fetch students via AJAX
    fetch(`{{ route('academic.attendance.division.students', ['division' => ':divisionId']) }}`.replace(':divisionId', divisionId))
        .then(response => response.json())
        .then(data => {
            if (data.status && data.data.length > 0) {
                tbody.innerHTML = data.data.map(student => `
                    <tr>
                        <td>${student.roll_no || 'N/A'}</td>
                        <td>${student.name}</td>
                    </tr>
                `).join('');
            } else {
                document.getElementById('studentsTable').style.display = 'none';
                noStudentsMsg.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="2" class="text-center text-danger">Error loading students</td></tr>';
        });
}
</script>
@endsection