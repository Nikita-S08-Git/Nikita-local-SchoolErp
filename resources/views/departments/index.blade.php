@extends('layouts.app')

@section('title', 'Departments')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-gray-800 mb-1">Departments</h1>
            <p class="text-muted mb-0">Manage academic departments and their details</p>
        </div>
        <a href="{{ route('web.departments.create') }}" class="btn btn-primary shadow-sm">
            Add New Department
        </a>
    </div>
    
    <!-- Search and Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('web.departments.index') }}" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or code..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ route('web.departments.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>


    <!-- Departments Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($departments->isEmpty())
                <div class="text-center py-5">
                    <h5 class="text-muted">No departments found</h5>
                    <p class="text-muted mb-0">Get started by creating a new department</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="ps-4">Name</th>
                                <th scope="col">Code</th>
                                <th scope="col">HOD</th>
                                <th scope="col">Programs</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="pe-4 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($departments as $department)
                                <tr>
                                    <td class="ps-4 fw-medium">{{ $department->name }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $department->code }}</span>
                                    </td>
                                    <td>
                                        @if($department->hod)
                                            {{ $department->hod->name }}
                                        @else
                                            <span class="text-muted fst-italic">Not assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark">{{ $department->programs->count() }} Programs</span>
                                        <span class="badge bg-secondary">{{ $department->students_count ?? 0 }} Students</span>
                                    </td>
                                    <td>
                                        @if($department->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('web.departments.show', $department) }}" 
                                               class="btn btn-sm btn-info" title="View Details">
                                                View
                                            </a>
                                            <a href="{{ route('web.departments.edit', $department) }}" 
                                               class="btn btn-sm btn-warning" title="Edit Department">
                                                Edit
                                            </a>
                                            <form action="{{ route('web.departments.destroy', $department) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Delete department?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-white border-top py-3">
                    <div class="d-flex justify-content-end">
                        {{ $departments->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Primary button (Add New Department) */
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

/* Other buttons */
.btn-info {
    background-color: #000 !important;
    border-color: #000 !important;
    color: #fff !important;
}

.btn-info:hover {
    background-color: #222 !important;
    border-color: #222 !important;
    color: #fff !important;
}

.btn-warning {
    background-color: #000 !important;
    border-color: #000 !important;
    color: #fff !important;
}

.btn-warning:hover {
    background-color: #222 !important;
    border-color: #222 !important;
    color: #fff !important;
}

.btn-danger {
    background-color: #000 !important;
    border-color: #000 !important;
    color: #fff !important;
}

.btn-danger:hover {
    background-color: #222 !important;
    border-color: #222 !important;
    color: #fff !important;
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