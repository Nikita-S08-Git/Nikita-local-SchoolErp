@extends('layouts.teacher')

@section('title', 'Student Details')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb" class="mb-2">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('teacher.divisions.index') }}">Divisions</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('teacher.divisions.students', $student->division_id) }}">Students</a></li>
                            <li class="breadcrumb-item active">Details</li>
                        </ol>
                    </nav>
                    <h2 class="fw-bold" style="color: #667eea;">
                        <i class="bi bi-person-circle me-2"></i>Student Details
                    </h2>
                </div>
                <div>
                    <a href="{{ route('teacher.divisions.students', $student->division_id) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Student Info -->
        <div class="col-lg-4 mb-4">
            <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
                <div class="card-body p-4 text-center">
                    @if($student->photo_path)
                        <img src="{{ asset('storage/' . $student->photo_path) }}" 
                             alt="{{ $student->first_name }}" 
                             class="rounded-circle mb-3" 
                             style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #667eea;">
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                             style="width: 150px; height: 150px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <span class="text-white fw-bold" style="font-size: 4rem;">
                                {{ strtoupper(substr($student->first_name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    
                    <h4 class="fw-bold mb-1">{{ $student->first_name }} {{ $student->last_name }}</h4>
                    <p class="text-muted mb-2">{{ $student->email ?? $student->user->email ?? 'N/A' }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-primary">{{ $student->roll_number }}</span>
                        <span class="badge bg-info">{{ $student->admission_number }}</span>
                    </div>

                    <div class="text-start">
                        <div class="mb-2">
                            <small class="text-muted">Division</small>
                            <div class="fw-semibold">{{ $student->division->division_name ?? 'N/A' }}</div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Program</small>
                            <div class="fw-semibold">{{ $student->program->name ?? 'N/A' }}</div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Attendance</small>
                            <div class="fw-semibold">
                                <span class="badge {{ $attendancePercentage >= 75 ? 'bg-success' : 'bg-warning' }}">
                                    {{ $attendancePercentage }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Info -->
        <div class="col-lg-8 mb-4">
            <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-info-circle me-2 text-primary"></i>Personal Information
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Date of Birth</small>
                            <div class="fw-semibold">{{ $student->date_of_birth?->format('d M Y') ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Gender</small>
                            <div class="fw-semibold">{{ ucfirst($student->gender ?? 'N/A') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Blood Group</small>
                            <div class="fw-semibold">{{ $student->studentProfile->blood_group ?? $student->blood_group ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Phone</small>
                            <div class="fw-semibold">{{ $student->mobile_number ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-4 mb-3">
                        <i class="bi bi-people me-2 text-primary"></i>Parent/Guardian Information
                    </h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Father's Name</small>
                            <div class="fw-semibold">{{ $student->studentProfile->father_name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Father's Phone</small>
                            <div class="fw-semibold">{{ $student->studentProfile->father_phone ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Mother's Name</small>
                            <div class="fw-semibold">{{ $student->studentProfile->mother_name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Mother's Phone</small>
                            <div class="fw-semibold">{{ $student->studentProfile->mother_phone ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Emergency Contact</small>
                            <div class="fw-semibold">{{ $student->studentProfile->emergency_contact_phone ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-4 mb-3">
                        <i class="bi bi-geo-alt me-2 text-primary"></i>Address
                    </h6>
                    <p class="mb-0">{{ $student->current_address ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Academic Results -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-clipboard-data me-2 text-primary"></i>Academic Results
                        @if($marks->count() > 0)
                            <span class="badge bg-primary ms-2">{{ $marks->count() }} Subjects</span>
                        @endif
                    </h5>
                    
                    @if($marks->count() > 0)
                        <!-- Overall Summary -->
                        @if($canViewFullDetails)
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="text-muted mb-1">Overall Percentage</h6>
                                        <h3 class="fw-bold {{ $overallPercentage >= 40 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($overallPercentage, 2) }}%
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="text-muted mb-1">Total Marks Obtained</h6>
                                        <h3 class="fw-bold text-primary">{{ $totalMarksObtained }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="text-muted mb-1">Total Max Marks</h6>
                                        <h3 class="fw-bold text-secondary">{{ $totalMaxMarks }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            Your overall percentage: <strong>{{ number_format($overallPercentage, 2) }}%</strong>
                        </div>
                        @endif

                        <!-- Results Table with Pagination -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Examination</th>
                                        @if($canViewFullDetails)
                                        <th class="text-center">Marks Obtained</th>
                                        <th class="text-center">Max Marks</th>
                                        @endif
                                        <th class="text-center">Percentage</th>
                                        @if($canViewFullDetails)
                                        <th class="text-center">Grade</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($marks as $mark)
                                        @php
                                            $markPercentage = $mark->max_marks > 0 ? ($mark->marks_obtained / $mark->max_marks) * 100 : 0;
                                            $grade = '';
                                            if ($markPercentage >= 90) $grade = 'A+';
                                            elseif ($markPercentage >= 80) $grade = 'A';
                                            elseif ($markPercentage >= 70) $grade = 'B+';
                                            elseif ($markPercentage >= 60) $grade = 'B';
                                            elseif ($markPercentage >= 50) $grade = 'C';
                                            elseif ($markPercentage >= 40) $grade = 'D';
                                            else $grade = 'F';
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $mark->subject->name ?? 'N/A' }}</strong></td>
                                            <td>{{ $mark->examination->name ?? 'N/A' }}</td>
                                            @if($canViewFullDetails)
                                            <td class="text-center"><strong>{{ $mark->marks_obtained }}</strong></td>
                                            <td class="text-center">{{ $mark->max_marks }}</td>
                                            @endif
                                            <td class="text-center">
                                                <span class="badge bg-{{ $markPercentage >= 40 ? 'success' : 'warning' }}">
                                                    {{ number_format($markPercentage, 1) }}%
                                                </span>
                                            </td>
                                            @if($canViewFullDetails)
                                            <td class="text-center">
                                                <span class="badge bg-{{ $markPercentage >= 40 ? 'success' : 'danger' }}">{{ $grade }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($markPercentage >= 40)
                                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Pass</span>
                                                @else
                                                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Fail</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="viewResultDetails({{ $mark->id }}); return false;">
                                                                <i class="bi bi-eye me-2 text-primary"></i>View Details
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="printResult({{ $mark->id }}); return false;">
                                                                <i class="bi bi-printer me-2 text-success"></i>Print
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="downloadMarkSheet({{ $mark->id }}); return false;">
                                                                <i class="bi bi-download me-2 text-info"></i>Download Mark Sheet
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $marks->links('pagination::bootstrap-5') }}
                        </div>

                        <!-- Pagination Info -->
                        <div class="text-center text-muted mt-2">
                            <small>
                                Showing {{ $marks->firstItem() ?? 0 }} to {{ $marks->lastItem() ?? 0 }} of {{ $marks->total() }} results
                            </small>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No academic results available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance -->
    <div class="row">
        <div class="col-12">
            <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-calendar-check me-2 text-primary"></i>Recent Attendance
                    </h5>
                    
                    @if($recentAttendance->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                        <th>Marked By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAttendance as $record)
                                        <tr>
                                            <td>{{ $record->date->format('d M Y') }}</td>
                                            <td>
                                                <span class="badge {{ $record->status == 'present' ? 'bg-success' : ($record->status == 'absent' ? 'bg-danger' : 'bg-warning') }}">
                                                    {{ ucfirst($record->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $record->remarks ?? '-' }}</td>
                                            <td>{{ $record->markedBy?->name ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No attendance records found</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Result Details Modal -->
<div class="modal fade" id="resultDetailsModal" tabindex="-1" aria-labelledby="resultDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="resultDetailsModalLabel">
                    <i class="bi bi-clipboard-data me-2"></i>Result Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Subject:</strong>
                        <p id="modalSubject" class="text-muted">-</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Examination:</strong>
                        <p id="modalExamination" class="text-muted">-</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center p-3">
                                <h6 class="text-muted mb-1">Marks Obtained</h6>
                                <h4 class="fw-bold text-primary mb-0" id="modalMarksObtained">-</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center p-3">
                                <h6 class="text-muted mb-1">Max Marks</h6>
                                <h4 class="fw-bold text-secondary mb-0" id="modalMaxMarks">-</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center p-3">
                                <h6 class="text-muted mb-1">Percentage</h6>
                                <h4 class="fw-bold text-success mb-0" id="modalPercentage">-</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printCurrentResult()">
                    <i class="bi bi-printer me-1"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentResultData = null;

function viewResultDetails(markId) {
    // Find the mark data from the table row
    const row = event.target.closest('tr');
    const subject = row.cells[0].innerText;
    const examination = row.cells[1].innerText;
    const marksObtained = row.cells[2]?.innerText || 'N/A';
    const maxMarks = row.cells[3]?.innerText || '100';
    const percentage = row.querySelector('.badge')?.innerText || '0%';

    // Store current result data
    currentResultData = { subject, examination, marksObtained, maxMarks, percentage };

    // Populate modal
    document.getElementById('modalSubject').innerText = subject;
    document.getElementById('modalExamination').innerText = examination;
    document.getElementById('modalMarksObtained').innerText = marksObtained;
    document.getElementById('modalMaxMarks').innerText = maxMarks;
    document.getElementById('modalPercentage').innerText = percentage;

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('resultDetailsModal'));
    modal.show();
}

function printResult(markId) {
    const row = event.target.closest('tr');
    const subject = row.cells[0].innerText;
    const examination = row.cells[1].innerText;
    const marksObtained = row.cells[2]?.innerText || 'N/A';
    const maxMarks = row.cells[3]?.innerText || '100';
    const percentage = row.querySelector('.badge')?.innerText || '0%';
    const grade = row.cells[5]?.innerText || 'N/A';
    const status = row.cells[6]?.innerText || 'N/A';

    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write(`
        <html><head><title>Result - ${subject}</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { padding: 20px; font-family: Arial, sans-serif; }
            .header { text-align: center; margin-bottom: 30px; }
            .result-card { border: 2px solid #667eea; border-radius: 10px; padding: 20px; margin-top: 20px; }
        </style>
        </head><body>
        <div class="header">
            <h2>Student Result Details</h2>
            <p>Generated on: ${new Date().toLocaleDateString()}</p>
        </div>
        <div class="result-card">
            <div class="row mb-3">
                <div class="col-md-6"><strong>Subject:</strong> ${subject}</div>
                <div class="col-md-6"><strong>Examination:</strong> ${examination}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4"><strong>Marks:</strong> ${marksObtained}/${maxMarks}</div>
                <div class="col-md-4"><strong>Percentage:</strong> ${percentage}</div>
                <div class="col-md-4"><strong>Grade:</strong> ${grade}</div>
            </div>
            <div class="row">
                <div class="col-md-4"><strong>Status:</strong> ${status}</div>
            </div>
        </div>
        <script>window.print();<\/script>
        </body></html>
    `);
    printWindow.document.close();
}

function printCurrentResult() {
    if (!currentResultData) return;

    const { subject, examination, marksObtained, maxMarks, percentage } = currentResultData;
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write(`
        <html><head><title>Result - ${subject}</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { padding: 20px; font-family: Arial, sans-serif; }
            .header { text-align: center; margin-bottom: 30px; }
            .result-card { border: 2px solid #667eea; border-radius: 10px; padding: 20px; margin-top: 20px; }
        </style>
        </head><body>
        <div class="header">
            <h2>Student Result Details</h2>
            <p>Generated on: ${new Date().toLocaleDateString()}</p>
        </div>
        <div class="result-card">
            <div class="row mb-3">
                <div class="col-md-6"><strong>Subject:</strong> ${subject}</div>
                <div class="col-md-6"><strong>Examination:</strong> ${examination}</div>
            </div>
            <div class="row">
                <div class="col-md-4"><strong>Marks Obtained:</strong> ${marksObtained}</div>
                <div class="col-md-4"><strong>Max Marks:</strong> ${maxMarks}</div>
                <div class="col-md-4"><strong>Percentage:</strong> ${percentage}</div>
            </div>
        </div>
        <script>window.print();<\/script>
        </body></html>
    `);
    printWindow.document.close();
}

function downloadMarkSheet(markId) {
    // Show a message that download is in progress
    alert('Mark sheet download will be available soon. This feature is under development.');
    // In production, this would redirect to a download route:
    // window.location.href = `/teacher/results/${markId}/download`;
}
</script>
@endpush

@endsection
