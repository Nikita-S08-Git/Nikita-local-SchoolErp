@extends('layouts.app')

@section('title', 'Students Management')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1"><i class="bi bi-people me-2 text-primary"></i> Students Management</h3>
                    <p class="text-muted mb-0">Manage student records, admissions, and academic information</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        <i class="bi bi-printer"></i> Print List
                    </button>
                    <a href="{{ route('dashboard.students.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Add Student
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Students</h6>
                            <h3 class="mb-0">{{ $students->total() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people fs-1 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Active Students</h6>
                            <h3 class="mb-0">{{ $students->where('student_status', 'active')->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-check fs-1 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Programs</h6>
                            <h3 class="mb-0">{{ $programs->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-mortarboard fs-1 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">This Page</h6>
                            <h3 class="mb-0">{{ $students->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-list-ul fs-1 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Students</h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Program</label>
                    <select name="program_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Programs</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                {{ $program->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Academic Year</label>
                    <select name="academic_year" class="form-select" onchange="this.form.submit()">
                        <option value="">All Years</option>
                        <option value="FY" {{ request('academic_year') == 'FY' ? 'selected' : '' }}>First Year (FY)</option>
                        <option value="SY" {{ request('academic_year') == 'SY' ? 'selected' : '' }}>Second Year (SY)</option>
                        <option value="TY" {{ request('academic_year') == 'TY' ? 'selected' : '' }}>Third Year (TY)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                        <option value="dropped" {{ request('status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Name, Email, Roll No..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-table me-2"></i>Students List</h6>
            <small class="text-muted">Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} students</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">Photo</th>
                            <th>Student Details</th>
                            <th>Academic Info</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th class="text-end" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td class="text-center">
                                    @if($student->photo_path)
                                        <img src="{{ asset('storage/' . $student->photo_path) }}" 
                                             class="rounded-circle" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-person text-white"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-hash"></i> {{ $student->admission_number }}
                                            @if($student->roll_number)
                                                | <i class="bi bi-bookmark"></i> {{ $student->roll_number }}
                                            @endif
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="badge bg-primary mb-1">{{ $student->program?->name ?? '—' }}</span>
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-diagram-3"></i> {{ $student->division?->division_name ?? '—' }}
                                            | <i class="bi bi-calendar"></i> {{ $student->academic_year }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        @if($student->email)
                                            <small><i class="bi bi-envelope"></i> {{ $student->email }}</small><br>
                                        @endif
                                        @if($student->mobile_number)
                                            <small><i class="bi bi-phone"></i> {{ $student->mobile_number }}</small>
                                        @else
                                            <small class="text-muted">No contact info</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $student->student_status === 'active' ? 'success' : ($student->student_status === 'graduated' ? 'info' : 'secondary') }}">
                                        {{ ucfirst($student->student_status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('dashboard.students.show', $student) }}" 
                                           class="btn btn-sm btn-primary" title="View Details">
                                            👁️
                                        </a>
                                        <a href="{{ route('dashboard.students.edit', $student) }}" 
                                           class="btn btn-sm btn-warning" title="Edit Student">
                                            ✏️
                                        </a>
                                        <form action="{{ route('dashboard.students.destroy', $student) }}" 
                                              method="POST" class="d-inline" 
                                              onsubmit="return confirm('Delete {{ $student->first_name }} {{ $student->last_name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete Student">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-people fs-1 mb-3 d-block"></i>
                                        <h5>No students found</h5>
                                        <p>Try adjusting your filters or add a new student.</p>
                                        <a href="{{ route('dashboard.students.create') }}" class="btn btn-primary">
                                            <i class="bi bi-plus-circle"></i> Add First Student
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($students->hasPages())
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} results
                            </small>
                        </div>
                        <div>
                            {{ $students->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection