@extends('layouts.teacher')

@section('title', 'My Students')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-people me-2"></i>My Students</h2>
            <p class="text-muted mb-0">{{ is_object($students) && method_exists($students, 'total') ? $students->total() : $students->count() }} students found</p>
        </div>
        <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filter by Division</label>
                    <select name="division_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Divisions</option>
                        @foreach($assignedDivisions as $division)
                            <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                {{ $division->division_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                               placeholder="Search by name or admission number..."
                               value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i> Search
                        </button>
                        @if(request()->hasAny(['search', 'division_id']))
                            <a href="{{ route('teacher.students.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Admission No</th>
                            <th>Student Name</th>
                            <th>Division</th>
                            <th>Roll No</th>
                            <th>Parent Contact</th>
                            <th>Attendance</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-primary">
                                        {{ $student->admission_number ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle me-2 bg-primary bg-gradient d-flex align-items-center justify-content-center text-white fw-bold"
                                             style="width: 40px; height: 40px; min-width: 40px;">
                                            {{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $student->full_name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $student->user->email ?? 'No email' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $student->division->division_name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $student->roll_number ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($student->studentProfile && $student->studentProfile->father_phone)
                                        <div><i class="bi bi-telephone me-1"></i>{{ $student->studentProfile->father_phone }}</div>
                                        <small class="text-muted">Father</small>
                                    @elseif($student->studentProfile && $student->studentProfile->mother_phone)
                                        <div><i class="bi bi-telephone me-1"></i>{{ $student->studentProfile->mother_phone }}</div>
                                        <small class="text-muted">Mother</small>
                                    @elseif($student->mobile_number)
                                        <div><i class="bi bi-telephone me-1"></i>{{ $student->mobile_number }}</div>
                                        <small class="text-muted">Student</small>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $totalDays = $student->attendances->count();
                                        $presentDays = $student->attendances->where('status', 'present')->count();
                                        $percentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="progress me-2" style="width: 80px; height: 8px;">
                                            <div class="progress-bar bg-{{ $percentage >= 75 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger') }}"
                                                 role="progressbar"
                                                 style="width: {{ $percentage }}%"
                                                 aria-valuenow="{{ $percentage }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="small fw-semibold">{{ $percentage }}%</span>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('teacher.students.show', $student->id) }}"
                                       class="btn btn-sm btn-primary rounded-pill px-3">
                                        <i class="bi bi-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    @if(isset($assignedDivisions) && $assignedDivisions->count() > 0)
                                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                        <h5 class="text-muted mt-3">No students found</h5>
                                        <p class="text-muted">Try adjusting your search or filters</p>
                                    @else
                                        <i class="bi bi-exclamation-circle text-muted" style="font-size: 3rem;"></i>
                                        <h5 class="text-muted mt-3">No divisions assigned</h5>
                                        <p class="text-muted">You are not assigned to any divisions yet.</p>
                                        <small class="text-muted">Please contact the administrator to assign you to divisions.</small>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($students->hasPages())
            <div class="card-footer bg-white border-0 px-4 py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="text-muted pagination-info">
                        <i class="bi bi-list-ul me-2"></i>
                        Showing <strong>{{ is_object($students) && method_exists($students, 'firstItem') ? $students->firstItem() : 0 }}</strong> to <strong>{{ is_object($students) && method_exists($students, 'lastItem') ? $students->lastItem() : 0 }}</strong>
                        of <strong>{{ is_object($students) && method_exists($students, 'total') ? $students->total() : $students->count() }}</strong> students
                    </div>
                    
                    <!-- Custom Pagination Component -->
                    <x-pagination :paginator="$students" />
                </div>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: translateX(2px);
    }
    
    .badge {
        padding: 0.35rem 0.65rem;
        font-weight: 500;
    }
    
    .progress {
        border-radius: 4px;
        background-color: #e9ecef;
    }
</style>
@endpush
@endsection
