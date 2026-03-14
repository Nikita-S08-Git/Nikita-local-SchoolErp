@extends('layouts.teacher')

@section('title', 'Enter Marks')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb" class="mb-2">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('teacher.results.index') }}">Results</a></li>
                            <li class="breadcrumb-item active">Enter Marks</li>
                        </ol>
                    </nav>
                    <h2 class="fw-bold" style="color: #667eea;">
                        <i class="bi bi-pencil-square me-2"></i>Enter Marks
                    </h2>
                    <p class="text-muted mb-0">
                        {{ $division->division_name }} - {{ $examination->name }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Info -->
    <div class="card mb-4" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
        <div class="card-body">
            @if($subject)
                <div class="alert alert-info mb-0">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <div>
                            <strong>Exam:</strong> {{ $examination->name }} &nbsp;|&nbsp;
                            <strong>Subject:</strong> {{ $subject->name }} ({{ $subject->code }})
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Warning:</strong> This exam does not have a subject assigned. Please edit the exam and assign a subject first.
                </div>
            @endif
        </div>
    </div>

    <!-- Marks Entry Form -->
    @if($subject && $students->count() > 0)
    <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-table me-2 text-primary"></i>
                    Marks Entry: {{ $subject->name }}
                </h5>
                <span class="badge bg-primary">{{ $students->total() }} Students (Page {{ $students->currentPage() }} of {{ $students->lastPage() }})</span>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('teacher.results.store-marks') }}">
                @csrf
                <input type="hidden" name="examination_id" value="{{ $examination->id }}">
                <input type="hidden" name="division_id" value="{{ $division->id }}">
                <input type="hidden" name="max_marks" value="{{ $maxMarks ?? 100 }}">

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="80">Roll No.</th>
                                <th>Student Name</th>
                                <th width="150" class="text-center">Marks Obtained</th>
                                <th width="100" class="text-center">Percentage</th>
                                <th width="100" class="text-center">Grade</th>
                                <th width="120" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                @php
                                    $mark = $marks->get($student->id);
                                    $marksObtained = $mark ? $mark->marks_obtained : null;
                                    $percentage = $marksObtained !== null ? ($marksObtained / ($maxMarks ?? 100)) * 100 : null;
                                    $grade = '';
                                    $status = '';
                                    if ($percentage !== null) {
                                        if ($percentage >= 90) $grade = 'A+';
                                        elseif ($percentage >= 80) $grade = 'A';
                                        elseif ($percentage >= 70) $grade = 'B+';
                                        elseif ($percentage >= 60) $grade = 'B';
                                        elseif ($percentage >= 50) $grade = 'C';
                                        elseif ($percentage >= 40) $grade = 'D';
                                        else $grade = 'F';
                                        $status = $percentage >= 40 ? 'Pass' : 'Fail';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $student->roll_number }}</td>
                                    <td>
                                        <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                    </td>
                                    <td>
                                        <input type="number" 
                                               name="marks[{{ $student->id }}]" 
                                               class="form-control text-center" 
                                               value="{{ $marksObtained }}"
                                               min="0" 
                                               max="{{ $maxMarks ?? 100 }}"
                                               placeholder="0">
                                    </td>
                                    <td class="text-center">
                                        @if($percentage !== null)
                                            <span class="badge bg-{{ $percentage >= 40 ? 'success' : 'danger' }}">
                                                {{ number_format($percentage, 1) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($grade)
                                            <span class="badge bg-{{ $percentage >= 40 ? 'success' : 'danger' }}">{{ $grade }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($status)
                                            <span class="badge bg-{{ $status == 'Pass' ? 'success' : 'danger' }}">
                                                <i class="bi bi-{{ $status == 'Pass' ? 'check' : 'x' }}-circle me-1"></i>
                                                {{ $status }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No students found in this division
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($students->hasPages())
                <div class="mt-4">
                    <nav aria-label="Student pagination">
                        <ul class="pagination justify-content-center">
                            {{ $students->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </ul>
                    </nav>
                </div>
                @endif

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Marks
                    </button>
                    <a href="{{ route('teacher.results.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
        <div class="card-body text-center py-5">
            @if($subject)
                <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 text-muted">No students in this division</h5>
                <p class="text-muted">There are no students assigned to this division yet.</p>
            @else
                <i class="bi bi-exclamation-triangle text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 text-muted">Subject not assigned</h5>
                <p class="text-muted">This exam does not have a subject. Please edit the exam and assign a subject.</p>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
