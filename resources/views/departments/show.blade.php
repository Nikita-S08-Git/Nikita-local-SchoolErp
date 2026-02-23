@extends('layouts.app')

@section('title', 'Department Details')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item">
                        <a href="{{ route('web.departments.index') }}" class="text-decoration-none">Departments</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $department->name }}</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-gray-800 mb-1">
                <span class="badge bg-dark me-2">{{ $department->code }}</span>
                {{ $department->name }}
            </h1>
            <p class="text-muted mb-0">Complete department information and statistics</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('web.departments.edit', $department) }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('web.departments.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Content Section -->
        <div class="col-lg-8 mb-4">
            <!-- Basic Information Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-info-circle me-2"></i>Basic Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">
                                    <i class="fas fa-barcode me-1"></i>Department Code
                                </label>
                                <div class="fw-semibold">
                                    <span class="badge bg-dark fs-6">{{ $department->code }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">
                                    <i class="fas fa-building me-1"></i>Department Name
                                </label>
                                <div class="fw-semibold">{{ $department->name }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-item">
                                <label class="text-muted small mb-1">
                                    <i class="fas fa-align-left me-1"></i>Description
                                </label>
                                <div>
                                    @if($department->description)
                                        <p class="mb-0">{{ $department->description }}</p>
                                    @else
                                        <span class="text-muted fst-italic">No description available</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">
                                    <i class="fas fa-user-tie me-1"></i>Head of Department
                                </label>
                                <div>
                                    @if($department->hod)
                                        <div class="d-flex align-items-center">
                                            <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $department->hod->name }}</div>
                                                @if($department->hod->email)
                                                    <small class="text-muted">
                                                        <i class="fas fa-envelope"></i> {{ $department->hod->email }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">Not assigned yet</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">
                                    <i class="fas fa-toggle-on me-1"></i>Status
                                </label>
                                <div>
                                    @if($department->is_active)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-times-circle me-1"></i>Inactive
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Programs Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-graduation-cap me-2"></i>Programs in this Department
                    </h5>
                    <span class="badge bg-dark">
                        {{ $department->programs->count() }} Total
                    </span>
                </div>
                <div class="card-body p-0">
                    @if($department->programs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Code</th>
                                        <th>Program Name</th>
                                        <th>Duration</th>
                                        <th>Type</th>
                                        <th class="pe-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($department->programs as $program)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge bg-light text-dark">{{ $program->code }}</span>
                                        </td>
                                        <td class="fw-medium">{{ $program->name }}</td>
                                        <td>
                                            <i class="fas fa-clock text-muted me-1"></i>
                                            {{ $program->duration_years ?? 'N/A' }} Years
                                        </td>
                                        <td>
                                            @if(isset($program->level))
                                                <span class="badge bg-info text-dark">{{ $program->level }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="pe-4">
                                            @if($program->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-1">No programs available in this department</p>
                            <small class="text-muted">Programs can be added from the Programs section</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Section -->
        <div class="col-lg-4">
            <!-- Statistics Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-pie me-2"></i>Statistics
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="stat-item mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-graduation-cap fa-2x text-dark"></i>
                            </div>
                            <div class="text-end">
                                <h3 class="mb-0 fw-bold">{{ $department->programs->count() }}</h3>
                                <small class="text-muted">Total Programs</small>
                            </div>
                        </div>
                    </div>

                    <div class="stat-item mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                            <div class="text-end">
                                <h3 class="mb-0 fw-bold">{{ $department->programs->where('is_active', true)->count() }}</h3>
                                <small class="text-muted">Active Programs</small>
                            </div>
                        </div>
                    </div>

                    <div class="stat-item p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-times-circle fa-2x text-secondary"></i>
                            </div>
                            <div class="text-end">
                                <h3 class="mb-0 fw-bold">{{ $department->programs->where('is_active', false)->count() }}</h3>
                                <small class="text-muted">Inactive Programs</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-clock me-2"></i>Timeline
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="timeline-item mb-4">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-1 fw-semibold">Created</p>
                                <small class="text-muted d-block">
                                    {{ $department->created_at->format('d M Y, h:i A') }}
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>{{ $department->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-edit"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-1 fw-semibold">Last Updated</p>
                                <small class="text-muted d-block">
                                    {{ $department->updated_at->format('d M Y, h:i A') }}
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>{{ $department->updated_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <a href="{{ route('web.departments.edit', $department) }}" 
                           class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit Department
                        </a>
                        <button type="button" 
                                class="btn btn-danger"
                                onclick="confirmDelete()">
                            <i class="fas fa-trash me-1"></i> Delete Department
                        </button>
                        <a href="{{ route('web.departments.index') }}" 
                           class="btn btn-outline-secondary">
                            <i class="fas fa-list me-1"></i> View All Departments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="delete-form" action="{{ route('web.departments.destroy', $department) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete "{{ $department->name }}"?\n\nThis action cannot be undone and may affect related programs.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush

@push('styles')
<style>
/* Primary button */
.btn-primary {
    background-color: #000 !important;
    border-color: #000 !important;
    color: #fff !important;
}

.btn-primary:hover {
    background-color: #222 !important;
    border-color: #222 !important;
    color: #fff !important;
}

/* Secondary button */
.btn-secondary {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
    color: #fff !important;
}

.btn-secondary:hover {
    background-color: #5a6268 !important;
    border-color: #545b62 !important;
}

/* Danger button */
.btn-danger {
    background-color: #000 !important;
    border-color: #000 !important;
    color: #fff !important;
}

.btn-danger:hover {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: #fff !important;
}

/* Outline secondary button */
.btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: #fff;
}

/* Badge dark */
.badge.bg-dark {
    background-color: #000 !important;
}

/* Card styling */
.card {
    border-radius: 0.5rem;
}

/* Breadcrumb */
.breadcrumb {
    background-color: transparent;
    padding: 0;
    margin-bottom: 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    color: #6c757d;
}

/* Text styling */
.text-gray-800 {
    color: #2d3748;
}

/* Info items */
.info-item {
    padding-bottom: 0.5rem;
}

/* Table hover effect */
.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.03);
}

.badge {
    font-weight: 500;
}
</style>
@endpush

@push('head')
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-papVtJ6Qoyt7rO8zOoaJ+R/0ZoHtn+J8EYBrmPQOiEz2+qyj/MoF+jV7qZHb3T7j6xFwfsPWTf6yoA08VUcZ2g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush