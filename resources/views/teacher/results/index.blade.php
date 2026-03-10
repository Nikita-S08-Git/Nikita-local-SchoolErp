@extends('layouts.teacher')

@section('title', 'My Students Results')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb" class="mb-2">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Results</li>
                        </ol>
                    </nav>
                    <h2 class="fw-bold" style="color: #667eea;">
                        <i class="bi bi-clipboard-data me-2"></i>Student Results
                    </h2>
                    <p class="text-muted mb-0">View and manage examination results for your divisions</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
        <div class="card-body">
            <form method="GET" action="{{ route('teacher.results.index') }}">
                <div class="row">
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label for="division_id" class="form-label">Select Division</label>
                            <select name="division_id" id="division_id" class="form-select">
                                <option value="">-- Select Division --</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                        {{ $division->division_name }} - {{ $division->program->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label for="examination_id" class="form-label">Select Examination</label>
                            <select name="examination_id" id="examination_id" class="form-select">
                                <option value="">-- Select Examination --</option>
                                @foreach($examinations as $exam)
                                    <option value="{{ $exam->id }}" {{ request('examination_id') == $exam->id ? 'selected' : '' }}>
                                        {{ $exam->name }} ({{ ucfirst($exam->type) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3 d-grid">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i> Search
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    @if($selectedDivision && $selectedExam)
    <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-table me-2 text-primary"></i>
                    Results: {{ $selectedDivision->division_name }} - {{ $selectedExam->name }}
                </h5>
                <a href="{{ route('teacher.results.enter', ['examinationId' => $selectedExam->id, 'divisionId' => $selectedDivision->id]) }}" 
                   class="btn btn-primary">
                    <i class="bi bi-pencil me-1"></i> Enter/Edit Marks
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Roll No.</th>
                                <th>Student Name</th>
                                <th>Subjects</th>
                                <th class="text-center">Total Marks</th>
                                <th class="text-center">Percentage</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php
                                    $studentMarks = $results->where('student_id', $student->id);
                                    $subjectsCount = $studentMarks->count();
                                    $totalMarks = $studentMarks->sum('marks_obtained');
                                    $totalMaxMarks = $studentMarks->sum('max_marks');
                                    $hasMarks = $subjectsCount > 0;
                                    $percentage = $hasMarks && $totalMaxMarks > 0 ? ($totalMarks / $totalMaxMarks) * 100 : null;
                                    $passPercentage = 40;
                                    $isPass = $hasMarks && $percentage !== null && $percentage >= $passPercentage;
                                @endphp
                                <tr>
                                    <td>{{ $student->roll_number }}</td>
                                    <td>
                                        <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                    </td>
                                    <td>{{ $subjectsCount }}</td>
                                    <td class="text-center">
                                        @if($hasMarks)
                                            {{ $totalMarks }} / {{ $totalMaxMarks }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($hasMarks && $percentage !== null)
                                            <span class="badge bg-{{ $isPass ? 'success' : 'danger' }}">
                                                {{ number_format($percentage, 1) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(!$hasMarks)
                                            <span class="badge bg-warning"><i class="bi bi-clock me-1"></i>Pending</span>
                                        @elseif($isPass)
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Pass</span>
                                        @else
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Fail</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('teacher.students.details', $student->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($students instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-4">
                        {{ $students->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">No students found in this division</p>
                </div>
            @endif
        </div>
    </div>
    @else
    <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
        <div class="card-body text-center py-5">
            <i class="bi bi-clipboard-data text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3 text-muted">Select a division and examination to view results</h5>
            <p class="text-muted">Use the filters above to search for student results</p>
        </div>
    </div>
    @endif
</div>
@endsection
