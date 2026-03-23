@extends('layouts.teacher')

@section('title', 'My Students Results')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
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
                    <h2 class="fw-bold" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        <i class="bi bi-clipboard-check me-2"></i>Student Results
                    </h2>
                    <p class="text-muted mb-0">View and manage examination results for your divisions</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4" style="border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
        <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none; padding: 16px 24px;">
            <h5 class="mb-0 text-white fw-bold">
                <i class="bi bi-funnel me-2"></i>Filter Results
            </h5>
        </div>
        <div class="card-body" style="padding: 24px;">
            <form method="GET" action="{{ route('teacher.results.index') }}">
                <div class="row g-3">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="division_id" class="form-label fw-semibold text-dark">Select Division</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">
                                    <i class="bi bi-collection"></i>
                                </span>
                                <select name="division_id" id="division_id" class="form-select border-0 bg-light">
                                    <option value="">-- Select Division --</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                            {{ $division->division_name }} - {{ $division->program->name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="examination_id" class="form-label fw-semibold text-dark">Select Examination</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">
                                    <i class="bi bi-file-earmark-text"></i>
                                </span>
                                <select name="examination_id" id="examination_id" class="form-select border-0 bg-light">
                                    <option value="">-- Select Examination --</option>
                                    @foreach($examinations as $exam)
                                        <option value="{{ $exam->id }}" {{ request('examination_id') == $exam->id ? 'selected' : '' }}>
                                            {{ $exam->name }} ({{ ucfirst($exam->type) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group d-grid">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px;">
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
    <!-- Statistics Cards -->
    <div class="row mb-4 g-3">
        @php
            $pageStudents = $students->count();
            $pagePassCount = 0;
            $pageFailCount = 0;
            $pagePendingCount = 0;
            
            foreach($students as $student) {
                $studentMarks = $results->where('student_id', $student->id);
                $subjectsCount = $studentMarks->count();
                $totalMaxMarks = $studentMarks->sum('max_marks');
                $hasMarks = $subjectsCount > 0;
                
                if ($hasMarks && $totalMaxMarks > 0) {
                    $totalMarks = $studentMarks->sum('marks_obtained');
                    $percentage = ($totalMarks / $totalMaxMarks) * 100;
                    if ($percentage >= 40) {
                        $pagePassCount++;
                    } else {
                        $pageFailCount++;
                    }
                } else {
                    $pagePendingCount++;
                }
            }
        @endphp
        <div class="col-md-3">
            <div class="card h-100" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
                <div class="card-body text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="bi bi-people text-white"></i>
                    </div>
                    <h4 class="fw-bold text-dark">{{ $pageStudents }}</h4>
                    <p class="text-muted mb-0">This Page</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
                <div class="card-body text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <i class="bi bi-clock text-white"></i>
                    </div>
                    <h4 class="fw-bold text-warning">{{ $pagePendingCount }}</h4>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
                <div class="card-body text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <i class="bi bi-check-circle text-white"></i>
                    </div>
                    <h4 class="fw-bold" style="color: #4facfe;">{{ $pagePassCount }}</h4>
                    <p class="text-muted mb-0">Passed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
                <div class="card-body text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <i class="bi bi-x-circle text-white"></i>
                    </div>
                    <h4 class="fw-bold" style="color: #fa709a;">{{ $pageFailCount }}</h4>
                    <p class="text-muted mb-0">Failed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Table Card -->
    <div class="card" style="border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 16px 24px;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white fw-bold">
                    <i class="bi bi-table me-2"></i>
                    Results: {{ $selectedDivision->division_name }} - {{ $selectedExam->name }}
                </h5>
                <a href="{{ route('teacher.results.enter', ['examinationId' => $selectedExam->id, 'divisionId' => $selectedDivision->id]) }}" 
                   class="btn btn-light btn-sm fw-bold">
                    <i class="bi bi-pencil me-1"></i> Enter Marks
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th class="border-0 px-4 py-3 fw-bold text-dark">Roll No.</th>
                                <th class="border-0 py-3 fw-bold text-dark">Student Name</th>
                                <th class="border-0 py-3 fw-bold text-dark text-center">Subjects</th>
                                <th class="border-0 py-3 fw-bold text-dark text-center">Total Marks</th>
                                <th class="border-0 py-3 fw-bold text-dark text-center">Percentage</th>
                                <th class="border-0 py-3 fw-bold text-dark text-center">Status</th>
                                <th class="border-0 py-3 fw-bold text-dark text-center">Actions</th>
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
                                <tr style="transition: all 0.2s;">
                                    <td class="px-4 py-3">
                                        <span class="fw-semibold text-primary">{{ $student->roll_number }}</span>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2" style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                                <span class="text-white small fw-bold">{{ substr($student->first_name, 0, 1) }}</span>
                                            </div>
                                            <strong class="text-dark">{{ $student->first_name }} {{ $student->last_name }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-center py-3">
                                        <span class="badge bg-light text-dark">{{ $subjectsCount }}</span>
                                    </td>
                                    <td class="text-center py-3">
                                        @if($hasMarks)
                                            <span class="fw-bold text-dark">{{ $totalMarks }}</span>
                                            <span class="text-muted">/ {{ $totalMaxMarks }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center py-3">
                                        @if($hasMarks && $percentage !== null)
                                            <div class="progress mx-auto" style="width: 80px; height: 8px; border-radius: 4px;">
                                                <div class="progress-bar" role="progressbar" style="width: {{ min($percentage, 100) }}%; background: linear-gradient(90deg, {{ $isPass ? '#4facfe' : '#fa709a' }} 0%, {{ $isPass ? '#00f2fe' : '#fee140' }} 100%);"></div>
                                            </div>
                                            <span class="badge bg-{{ $isPass ? 'success' : 'danger' }} mt-1">
                                                {{ number_format($percentage, 1) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center py-3">
                                        @if(!$hasMarks)
                                            <span class="badge" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                                <i class="bi bi-clock me-1"></i>Pending
                                            </span>
                                        @elseif($isPass)
                                            <span class="badge" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                                <i class="bi bi-check-circle me-1"></i>Pass
                                            </span>
                                        @else
                                            <span class="badge" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                                <i class="bi bi-x-circle me-1"></i>Fail
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center py-3">
                                        <a href="{{ route('teacher.students.details', $student->id) }}" 
                                           class="btn btn-sm" 
                                           style="background: #f8f9fa; border: 1px solid #dee2e6; color: #667eea;">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($students instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="card-footer bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">
                                Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} results
                            </span>
                            {{ $students->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-clipboard-x text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="text-muted">No students found</h5>
                    <p class="text-muted">There are no students in this division</p>
                </div>
            @endif
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="card" style="border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
        <div class="card-body text-center py-5">
            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="bi bi-clipboard-data text-white" style="font-size: 3rem;"></i>
                </div>
            </div>
            <h4 class="fw-bold text-dark mb-2">Select Division & Examination</h4>
            <p class="text-muted mb-0">Choose a division and examination from the filters above to view student results</p>
        </div>
    </div>
    @endif
</div>

<style>
    .pagination {
        margin: 0;
    }
    .page-link {
        border: none;
        color: #667eea;
        padding: 8px 12px;
        margin: 0 2px;
        border-radius: 8px;
    }
    .page-link:hover {
        background: #f8f9fa;
        color: #764ba2;
    }
    .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .table tr:hover {
        background-color: #f8f9fa !important;
    }
</style>
@endsection
