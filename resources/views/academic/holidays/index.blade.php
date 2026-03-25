@extends('layouts.app')

@section('title', 'Holiday Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-calendar-check me-2 text-primary"></i>Holiday Management</h2>
                    <p class="text-muted mb-0">Manage school holidays and events</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('academic.timetable.table') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-table me-1"></i> Timetable
                    </a>
                    <a href="{{ route('academic.holidays.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add Holiday
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm h-100" style="border-radius: 14px; border: none;">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h5 class="mb-2">Total Holidays</h5>
                    <p class="display-4 fw-bold text-primary mb-0">{{ $holidays->total() ?? 0 }}</p>
                    <small class="text-muted">All time</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100" style="border-radius: 14px; border: none;">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h5 class="mb-2">Upcoming</h5>
                    <p class="h2 fw-bold text-success mb-0">{{ $holidays->where('start_date', '>=', today())->count() }}</p>
                    <small class="text-muted">Next 30 days</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100" style="border-radius: 14px; border: none;">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="fas fa-star"></i>
                    </div>
                    <h5 class="mb-2">Public Holidays</h5>
                    <p class="h2 fw-bold text-info mb-0">{{ $holidays->where('type', 'public_holiday')->count() }}</p>
                    <small class="text-muted">National & State</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100" style="border-radius: 14px; border: none;">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="fas fa-school"></i>
                    </div>
                    <h5 class="mb-2">School Holidays</h5>
                    <p class="h2 fw-bold text-warning mb-0">{{ $holidays->where('type', 'school_holiday')->count() }}</p>
                    <small class="text-muted">School events</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4" style="border-radius: 14px; border: none;">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('academic.holidays.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Search holidays..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="public_holiday" {{ request('type') == 'public_holiday' ? 'selected' : '' }}>Public Holiday</option>
                        <option value="school_holiday" {{ request('type') == 'school_holiday' ? 'selected' : '' }}>School Holiday</option>
                        <option value="event" {{ request('type') == 'event' ? 'selected' : '' }}>Event</option>
                        <option value="program" {{ request('type') == 'program' ? 'selected' : '' }}>Program</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Academic Year</label>
                    <select name="academic_year_id" class="form-select">
                        <option value="">All Years</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('academic.holidays.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Holidays Table -->
    <div class="card shadow-sm" style="border-radius: 14px; border: none;">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Holidays List</h5>
                <span class="badge bg-primary">{{ $holidays->total() ?? 0 }} Total</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4"><i class="fas fa-calendar me-2 text-muted"></i>Title</th>
                            <th class="py-3 px-4"><i class="fas fa-tag me-2 text-muted"></i>Type</th>
                            <th class="py-3 px-4"><i class="fas fa-clock me-2 text-muted"></i>Duration</th>
                            <th class="py-3 px-4"><i class="fas fa-calendar-day me-2 text-muted"></i>Dates</th>
                            <th class="py-3 px-4"><i class="fas fa-toggle-on me-2 text-muted"></i>Status</th>
                            <th class="py-3 px-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($holidays as $holiday)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3"
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $holiday->title }}</div>
                                        <small class="text-muted">{{ $holiday->description ?? 'No description' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($holiday->type === 'public_holiday')
                                    <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">Public Holiday</span>
                                @elseif($holiday->type === 'school_holiday')
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">School Holiday</span>
                                @elseif($holiday->type === 'event')
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">Event</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">Program</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($holiday->start_date != $holiday->end_date)
                                    <span class="text-muted">{{ \Carbon\Carbon::parse($holiday->start_date)->diffInDays($holiday->end_date) + 1 }} days</span>
                                @else
                                    <span class="text-muted">1 day</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="d-flex flex-column">
                                    <span class="fw-medium">{{ \Carbon\Carbon::parse($holiday->start_date)->format('M d, Y') }}</span>
                                    @if($holiday->start_date != $holiday->end_date)
                                        <small class="text-muted">to {{ \Carbon\Carbon::parse($holiday->end_date)->format('M d, Y') }}</small>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($holiday->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>Active
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                                        <i class="fas fa-times-circle me-1"></i>Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('academic.holidays.edit', $holiday) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('academic.holidays.destroy', $holiday) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this holiday?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3"
                                     style="width: 80px; height: 80px;">
                                    <i class="fas fa-calendar-times fa-2x text-muted"></i>
                                </div>
                                <h5 class="text-muted mb-2">No Holidays Found</h5>
                                <p class="text-muted mb-3">Add your first holiday to get started</p>
                                <a href="{{ route('academic.holidays.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Add Holiday
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($holidays->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing <strong>{{ $holidays->firstItem() ?? 0 }}</strong> to <strong>{{ $holidays->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $holidays->total() }}</strong> holidays
                </div>
                <div>
                    {{ $holidays->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
                    <thead class="table-light">
                        <tr>
                            <th width="60">#</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Date Range</th>
                            <th>Duration</th>
                            <th>Academic Year</th>
                            <th>Incharge</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($holidays as $index => $holiday)
                            <tr class="{{ !$holiday->is_active ? 'table-secondary' : '' }}">
                                <td>{{ $holidays->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $holiday->title }}</strong>
                                    @if($holiday->is_recurring)
                                        <br><small class="text-muted"><i class="bi bi-arrow-repeat"></i> Recurring</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $typeColors = [
                                            'public_holiday' => 'danger',
                                            'school_holiday' => 'warning',
                                            'event' => 'info',
                                            'program' => 'success'
                                        ];
                                        $typeLabels = [
                                            'public_holiday' => 'Public Holiday',
                                            'school_holiday' => 'School Holiday',
                                            'event' => 'Event',
                                            'program' => 'Program'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $typeColors[$holiday->type] ?? 'secondary' }}">
                                        {{ $typeLabels[$holiday->type] ?? ucfirst($holiday->type) }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <i class="bi bi-calendar3 text-muted me-1"></i>
                                        {{ $holiday->start_date->format('d M Y') }}
                                    </div>
                                    @if($holiday->start_date != $holiday->end_date)
                                        <div class="text-muted small">
                                            <i class="bi bi-arrow-right"></i> {{ $holiday->end_date->format('d M Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $holiday->duration }} day{{ $holiday->duration > 1 ? 's' : '' }}</span>
                                </td>
                                <td>{{ $holiday->academicYear->name ?? 'N/A' }}</td>
                                <td>
                                    @if($holiday->programIncharge)
                                        <small>{{ $holiday->programIncharge->name }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($holiday->is_active)
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('academic.holidays.edit', $holiday) }}" 
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('academic.holidays.destroy', $holiday) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this holiday?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No holidays configured
                                    </div>
                                    <a href="{{ route('academic.holidays.create') }}" class="btn btn-primary mt-2">
                                        <i class="bi bi-plus-circle"></i> Add First Holiday
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($holidays->hasPages())
            <div class="card-footer bg-white border-0 px-4 py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="text-muted pagination-info">
                        <i class="bi bi-list-ul me-2"></i>
                        Showing <strong>{{ $holidays->firstItem() ?? 0 }}</strong> to <strong>{{ $holidays->lastItem() ?? 0 }}</strong>
                        of <strong>{{ $holidays->total() }}</strong> entries
                    </div>
                    
                    <!-- Custom Pagination Component -->
                    <x-pagination :paginator="$holidays" />
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-danger">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-x fs-1 text-danger mb-2"></i>
                    <h5 class="card-title">Public Holidays</h5>
                    <p class="text-muted mb-0">{{ $holidays->where('type', 'public_holiday')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-event fs-1 text-warning mb-2"></i>
                    <h5 class="card-title">School Holidays</h5>
                    <p class="text-muted mb-0">{{ $holidays->where('type', 'school_holiday')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-info">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-check fs-1 text-info mb-2"></i>
                    <h5 class="card-title">Events</h5>
                    <p class="text-muted mb-0">{{ $holidays->where('type', 'event')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-range fs-1 text-success mb-2"></i>
                    <h5 class="card-title">Programs</h5>
                    <p class="text-muted mb-0">{{ $holidays->where('type', 'program')->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>
@endsection
