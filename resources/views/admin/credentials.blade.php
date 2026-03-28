@extends('layouts.app')

@section('title', 'User Credentials Management')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-key me-2"></i>User Credentials Management</h2>
            <p class="text-muted mb-0">View and manage user passwords</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('dashboard.students.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-people me-2"></i>Students
            </a>
            <a href="{{ route('dashboard.teachers.index') }}" class="btn btn-outline-info">
                <i class="bi bi-person-badge me-2"></i>Teachers
            </a>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="credentialsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button">
                <i class="bi bi-mortarboard me-2"></i>Students
                <span class="badge bg-primary ms-2">{{ $students->total() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="teachers-tab" data-bs-toggle="tab" data-bs-target="#teachers" type="button">
                <i class="bi bi-person-badge me-2"></i>Teachers
                <span class="badge bg-info ms-2">{{ $teachers->total() }}</span>
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="credentialsTabContent">
        <!-- Students Tab -->
        <div class="tab-pane fade show active" id="students" role="tabpanel">
            <!-- Search and Filter -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.credentials.index') }}" class="row g-3">
                        <input type="hidden" name="tab" value="students">
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Search Students</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" name="student_search" class="form-control" 
                                       placeholder="Name, Email, Admission No, Roll No..." 
                                       value="{{ $studentSearch }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Filter by Division</label>
                            <select name="division_filter" class="form-select">
                                <option value="">All Divisions</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}" {{ $divisionFilter == $division->id ? 'selected' : '' }}>
                                        {{ $division->program->name ?? '' }} - {{ $division->division_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-filter me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.credentials.index', ['tab' => 'students']) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end justify-content-end">
                            <button type="button" class="btn btn-success" onclick="exportTable('students')">
                                <i class="bi bi-download me-1"></i>Export CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Info -->
            @if($studentSearch || $divisionFilter)
            <div class="alert alert-info d-flex align-items-center mb-3">
                <i class="bi bi-info-circle me-2"></i>
                <span>Showing {{ $students->count() }} of {{ $students->total() }} students
                @if($studentSearch) with "{{ $studentSearch }}" @endif
                @if($divisionFilter) in selected division @endif</span>
            </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Student Credentials</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="studentsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Student Name</th>
                                    <th>Email</th>
                                    <th>Admission No</th>
                                    <th>Roll No</th>
                                    <th>Division</th>
                                    <th>Password</th>
                                    <th>Generated On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $index => $student)
                                <tr>
                                    <td class="ps-4">{{ $students->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($student->photo_path)
                                                <img src="{{ asset('storage/' . $student->photo_path) }}" 
                                                     class="rounded-circle me-2" width="32" height="32" alt="Photo">
                                            @else
                                                <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center text-white fw-bold"
                                                     style="width: 32px; height: 32px; min-width: 32px;">
                                                    {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $student->first_name }} {{ $student->last_name }}</div>
                                                <small class="text-muted">{{ $student->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $student->user->email ?? 'N/A' }}</span>
                                    </td>
                                    <td><span class="badge bg-primary">{{ $student->admission_number }}</span></td>
                                    <td><span class="badge bg-secondary">{{ $student->roll_number }}</span></td>
                                    <td>{{ $student->division->division_name ?? 'N/A' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="input-group input-group-sm" style="max-width: 200px;">
                                                <input type="password" class="form-control font-monospace" value="{{ $student->user->temp_password ?? 'Not Set' }}" 
                                                       id="password-{{ $student->id }}" readonly style="background-color: #f8f9fa; letter-spacing: 2px;">
                                                <button class="btn btn-outline-success" type="button" 
                                                        onclick="togglePassword('password-{{ $student->id }}')" title="Show/Hide Password">
                                                    <i class="bi bi-eye" id="eye-icon-{{ $student->id }}"></i>
                                                </button>
                                            </div>
                                            <button class="btn btn-sm btn-outline-primary" onclick="copyPassword('password-{{ $student->id }}')" title="Copy Password">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info text-white" onclick="viewPasswordModal('{{ $student->first_name }} {{ $student->last_name }}', '{{ $student->user->email ?? '' }}', '{{ $student->user->temp_password ?? 'Not Set' }}', '{{ $student->admission_number }}')" title="View Full Details">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                        </div>
                                        <small class="text-muted">Generated: {{ $student->user->password_generated_at ? $student->user->password_generated_at->diffForHumans() : 'N/A' }}</small>
                                    </td>
                                    <td>{{ $student->user->password_generated_at ? $student->user->password_generated_at->format('d M Y') : 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="resetPassword('student', {{ $student->user->id }})">
                                            <i class="bi bi-arrow-clockwise"></i> Reset
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                        <h5 class="text-muted mt-3">No students found</h5>
                                        @if($studentSearch || $divisionFilter)
                                        <p class="text-muted">Try adjusting your search or filter criteria</p>
                                        <a href="{{ route('admin.credentials.index', ['tab' => 'students']) }}" class="btn btn-outline-primary">Clear Filters</a>
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($students->hasPages())
                <div class="card-footer bg-white border-0 p-3">
                    {{ $students->appends(['student_search' => $studentSearch, 'division_filter' => $divisionFilter])->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Teachers Tab -->
        <div class="tab-pane fade" id="teachers" role="tabpanel">
            <!-- Search -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.credentials.index') }}" class="row g-3">
                        <input type="hidden" name="tab" value="teachers">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Search Teachers</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" name="teacher_search" class="form-control" 
                                       placeholder="Name or Email..." 
                                       value="{{ $teacherSearch }}">
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-filter me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.credentials.index', ['tab' => 'teachers']) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end justify-content-end">
                            <button type="button" class="btn btn-success" onclick="exportTable('teachers')">
                                <i class="bi bi-download me-1"></i>Export CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Info -->
            @if($teacherSearch)
            <div class="alert alert-info d-flex align-items-center mb-3">
                <i class="bi bi-info-circle me-2"></i>
                <span>Showing {{ $teachers->count() }} of {{ $teachers->total() }} teachers with "{{ $teacherSearch }}"</span>
            </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Teacher Credentials</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="teachersTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Teacher Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Password</th>
                                    <th>Generated On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teachers as $index => $teacher)
                                <tr>
                                    <td class="ps-4">{{ $teachers->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center text-white fw-bold"
                                                 style="width: 32px; height: 32px; min-width: 32px;">
                                                {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $teacher->name }}</div>
                                                <small class="text-muted">{{ $teacher->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-info">{{ $teacher->email }}</span></td>
                                    <td>
                                        @if($teacher->roles->count() > 0)
                                            <span class="badge bg-primary">{{ $teacher->roles->first()->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">No Role</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="input-group input-group-sm" style="max-width: 200px;">
                                                <input type="password" class="form-control font-monospace" value="{{ $teacher->temp_password ?? 'Not Set' }}" 
                                                       id="teacher-password-{{ $teacher->id }}" readonly style="background-color: #f8f9fa; letter-spacing: 2px;">
                                                <button class="btn btn-outline-success" type="button" 
                                                        onclick="togglePassword('teacher-password-{{ $teacher->id }}')" title="Show/Hide Password">
                                                    <i class="bi bi-eye" id="teacher-eye-icon-{{ $teacher->id }}"></i>
                                                </button>
                                            </div>
                                            <button class="btn btn-sm btn-outline-primary" onclick="copyPassword('teacher-password-{{ $teacher->id }}')" title="Copy Password">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info text-white" onclick="viewPasswordModal('{{ $teacher->name }}', '{{ $teacher->email }}', '{{ $teacher->temp_password ?? 'Not Set' }}', 'Teacher')" title="View Full Details">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                        </div>
                                        <small class="text-muted">Generated: {{ $teacher->password_generated_at ? $teacher->password_generated_at->diffForHumans() : 'N/A' }}</small>
                                    </td>
                                    <td>{{ $teacher->password_generated_at ? $teacher->password_generated_at->format('d M Y') : 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="resetPassword('teacher', {{ $teacher->id }})">
                                            <i class="bi bi-arrow-clockwise"></i> Reset
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                        <h5 class="text-muted mt-3">No teachers found</h5>
                                        @if($teacherSearch)
                                        <p class="text-muted">Try adjusting your search criteria</p>
                                        <a href="{{ route('admin.credentials.index', ['tab' => 'teachers']) }}" class="btn btn-outline-primary">Clear Filters</a>
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($teachers->hasPages())
                <div class="card-footer bg-white border-0 p-3">
                    {{ $teachers->appends(['teacher_search' => $teacherSearch])->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="studentsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Student Name</th>
                                    <th>Email</th>
                                    <th>Admission No</th>
                                    <th>Roll No</th>
                                    <th>Division</th>
                                    <th>Password</th>
                                    <th>Generated On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $index => $student)
                                <tr>
                                    <td class="ps-4">{{ $students->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($student->photo_path)
                                                <img src="{{ asset('storage/' . $student->photo_path) }}" 
                                                     class="rounded-circle me-2" width="32" height="32" alt="Photo">
                                            @else
                                                <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center text-white fw-bold"
                                                     style="width: 32px; height: 32px; min-width: 32px;">
                                                    {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $student->first_name }} {{ $student->last_name }}</div>
                                                <small class="text-muted">{{ $student->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $student->user->email ?? 'N/A' }}</span>
                                    </td>
                                    <td><span class="badge bg-primary">{{ $student->admission_number }}</span></td>
                                    <td><span class="badge bg-secondary">{{ $student->roll_number }}</span></td>
                                    <td>{{ $student->division->division_name ?? 'N/A' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="input-group input-group-sm" style="max-width: 200px;">
                                                <input type="password" class="form-control font-monospace" value="{{ $student->user->temp_password ?? 'Not Set' }}" 
                                                       id="password-{{ $student->id }}" readonly style="background-color: #f8f9fa; letter-spacing: 2px;">
                                                <button class="btn btn-outline-success" type="button" 
                                                        onclick="togglePassword('password-{{ $student->id }}')" title="Show/Hide Password">
                                                    <i class="bi bi-eye" id="eye-icon-{{ $student->id }}"></i>
                                                </button>
                                            </div>
                                            <button class="btn btn-sm btn-outline-primary" onclick="copyPassword('password-{{ $student->id }}')" title="Copy Password">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info text-white" onclick="viewPasswordModal('{{ $student->first_name }} {{ $student->last_name }}', '{{ $student->user->email ?? '' }}', '{{ $student->user->temp_password ?? 'Not Set' }}', '{{ $student->admission_number }}')" title="View Full Details">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                        </div>
                                        <small class="text-muted">Generated: {{ $student->user->password_generated_at ? $student->user->password_generated_at->diffForHumans() : 'N/A' }}</small>
                                    </td>
                                    <td>{{ $student->user->password_generated_at ? $student->user->password_generated_at->format('d M Y') : 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="resetPassword('student', {{ $student->user->id }})">
                                            <i class="bi bi-arrow-clockwise"></i> Reset
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                        <h5 class="text-muted mt-3">No students found</h5>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($students->hasPages())
                <div class="card-footer bg-white border-0 p-3">
                    {{ $students->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Teachers Tab -->
        <div class="tab-pane fade" id="teachers" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Teacher Credentials</h5>
                    <button class="btn btn-sm btn-success" onclick="exportTable('teachers')">
                        <i class="bi bi-download me-1"></i>Export
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="teachersTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Teacher Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Password</th>
                                    <th>Generated On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teachers as $index => $teacher)
                                <tr>
                                    <td class="ps-4">{{ $teachers->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center text-white fw-bold"
                                                 style="width: 32px; height: 32px; min-width: 32px;">
                                                {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $teacher->name }}</div>
                                                <small class="text-muted">{{ $teacher->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-info">{{ $teacher->email }}</span></td>
                                    <td>
                                        @if($teacher->roles->count() > 0)
                                            <span class="badge bg-primary">{{ $teacher->roles->first()->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">No Role</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="input-group input-group-sm" style="max-width: 200px;">
                                                <input type="password" class="form-control font-monospace" value="{{ $teacher->temp_password ?? 'Not Set' }}" 
                                                       id="teacher-password-{{ $teacher->id }}" readonly style="background-color: #f8f9fa; letter-spacing: 2px;">
                                                <button class="btn btn-outline-success" type="button" 
                                                        onclick="togglePassword('teacher-password-{{ $teacher->id }}')" title="Show/Hide Password">
                                                    <i class="bi bi-eye" id="teacher-eye-icon-{{ $teacher->id }}"></i>
                                                </button>
                                            </div>
                                            <button class="btn btn-sm btn-outline-primary" onclick="copyPassword('teacher-password-{{ $teacher->id }}')" title="Copy Password">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info text-white" onclick="viewPasswordModal('{{ $teacher->name }}', '{{ $teacher->email }}', '{{ $teacher->temp_password ?? 'Not Set' }}', 'Teacher')" title="View Full Details">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                        </div>
                                        <small class="text-muted">Generated: {{ $teacher->password_generated_at ? $teacher->password_generated_at->diffForHumans() : 'N/A' }}</small>
                                    </td>
                                    <td>{{ $teacher->password_generated_at ? $teacher->password_generated_at->format('d M Y') : 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="resetPassword('teacher', {{ $teacher->id }})">
                                            <i class="bi bi-arrow-clockwise"></i> Reset
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                        <h5 class="text-muted mt-3">No teachers found</h5>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($teachers->hasPages())
                <div class="card-footer bg-white border-0 p-3">
                    {{ $teachers->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Handle tab persistence from URL
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    
    if (tab === 'teachers') {
        const teachersTab = document.getElementById('teachers-tab');
        const teachersPane = document.getElementById('teachers');
        const studentsTab = document.getElementById('students-tab');
        const studentsPane = document.getElementById('students');
        
        if (teachersTab && teachersPane) {
            studentsTab.classList.remove('active');
            studentsPane.classList.remove('show', 'active');
            teachersTab.classList.add('active');
            teachersPane.classList.add('show', 'active');
        }
    }
});

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const eyeIcon = document.getElementById('eye-icon-' + inputId.replace('password-', '').replace('teacher-password-', ''));
    
    if (input.type === 'password') {
        input.type = 'text';
        if (eyeIcon) {
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        }
    } else {
        input.type = 'password';
        if (eyeIcon) {
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    }
}

function copyPassword(inputId) {
    const input = document.getElementById(inputId);
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices
    navigator.clipboard.writeText(input.value).then(function() {
        // Show success toast
        showToast('Password copied to clipboard!', 'success');
    }, function(err) {
        showToast('Failed to copy password', 'danger');
    });
}

function viewPasswordModal(name, email, password, admissionOrRole) {
    const modal = new bootstrap.Modal(document.getElementById('passwordViewModal'));
    document.getElementById('modalUserName').textContent = name;
    document.getElementById('modalUserEmail').textContent = email;
    document.getElementById('modalUserAdmission').textContent = admissionOrRole;
    document.getElementById('modalUserPassword').value = password;
    modal.show();
}

function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show mb-2`;
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    toastContainer.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'position-fixed bottom-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

function resetPassword(type, userId) {
    if (confirm('Are you sure you want to reset the password? A new password will be generated and the old one will be lost.')) {
        // Create a form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/credentials/reset-password/${userId}`;
        
        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = '{{ csrf_token() }}';
        
        form.appendChild(token);
        document.body.appendChild(form);
        form.submit();
    }
}

function exportTable(type) {
    window.location.href = `/admin/credentials/export?type=${type}`;
}

function toggleModalPassword() {
    const input = document.getElementById('modalUserPassword');
    const eyeIcon = document.getElementById('modalEyeIcon');
    
    if (input.type === 'password') {
        input.type = 'text';
        eyeIcon.classList.remove('bi-eye');
        eyeIcon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        eyeIcon.classList.remove('bi-eye-slash');
        eyeIcon.classList.add('bi-eye');
    }
}

function copyModalPassword() {
    const input = document.getElementById('modalUserPassword');
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value).then(function() {
        showToast('Password copied to clipboard!', 'success');
    }, function(err) {
        showToast('Failed to copy password', 'danger');
    });
}
</script>
@endpush

<!-- Password View Modal -->
<div class="modal fade" id="passwordViewModal" tabindex="-1" aria-labelledby="passwordViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="passwordViewModalLabel">
                    <i class="bi bi-key me-2"></i>User Credentials
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="text-muted small">User Name</label>
                    <div class="fw-semibold" id="modalUserName"></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Email / ID</label>
                    <div class="fw-semibold" id="modalUserEmail"></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Admission No / Role</label>
                    <div class="fw-semibold" id="modalUserAdmission"></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Password</label>
                    <div class="input-group">
                        <input type="text" class="form-control font-monospace" id="modalUserPassword" readonly 
                               style="background-color: #f8f9fa; letter-spacing: 2px; font-size: 1.1rem;">
                        <button class="btn btn-outline-success" type="button" onclick="toggleModalPassword()" title="Show/Hide">
                            <i class="bi bi-eye" id="modalEyeIcon"></i>
                        </button>
                        <button class="btn btn-outline-primary" type="button" onclick="copyModalPassword()" title="Copy">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    <small>Keep this password secure. Share it only with the user.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
