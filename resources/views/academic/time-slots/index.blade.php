@extends('layouts.app')

@section('title', 'Time Slot Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-clock me-2"></i>Time Slot Management</h2>
                    <p class="text-muted mb-0">Manage class periods and break times</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('academic.timetable.table') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Timetable
                    </a>
                    <a href="{{ route('academic.time-slots.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Time Slot
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Time Slots Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-task me-2"></i>Time Slots</h5>
            <span class="badge bg-light text-primary">{{ $timeSlots->total() }} Total Slots</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">#</th>
                            <th>Slot Code</th>
                            <th>Slot Name</th>
                            <th>Time Range</th>
                            <th>Duration</th>
                            <th>Type</th>
                            <th>Sequence</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($timeSlots as $index => $slot)
                            <tr class="{{ $slot->is_break ? 'table-warning' : '' }}">
                                <td>{{ $timeSlots->firstItem() + $index }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $slot->slot_code }}</span>
                                </td>
                                <td>
                                    <strong>{{ $slot->slot_name }}</strong>
                                    @if($slot->is_break)
                                        <br><small class="text-muted"><i class="bi bi-pause-circle"></i> Break</small>
                                    @endif
                                </td>
                                <td>
                                    <i class="bi bi-clock text-muted me-1"></i>
                                    {{ substr($slot->start_time, 0, 5) }} - {{ substr($slot->end_time, 0, 5) }}
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $slot->duration }} min</span>
                                </td>
                                <td>
                                    @php
                                        $typeColors = [
                                            'instructional' => 'success',
                                            'break' => 'warning',
                                            'assembly' => 'info',
                                            'exam' => 'danger',
                                            'lab' => 'primary',
                                            'tutorial' => 'secondary',
                                            'other' => 'dark'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $typeColors[$slot->slot_type] ?? 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $slot->slot_type)) }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $slot->sequence_order }}</td>
                                <td>
                                    @if($slot->is_active)
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('academic.time-slots.show', $slot) }}" class="btn btn-outline-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('academic.time-slots.edit', $slot) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('academic.time-slots.destroy', $slot) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this time slot?');">
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
                                        No time slots configured
                                    </div>
                                    <a href="{{ route('academic.time-slots.create') }}" class="btn btn-primary mt-2">
                                        <i class="bi bi-plus-circle"></i> Add First Time Slot
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($timeSlots->hasPages())
            <div class="card-footer bg-white border-0 px-4 py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="text-muted pagination-info">
                        <i class="bi bi-list-ul me-2"></i>
                        Showing <strong>{{ $timeSlots->firstItem() ?? 0 }}</strong> to <strong>{{ $timeSlots->lastItem() ?? 0 }}</strong>
                        of <strong>{{ $timeSlots->total() }}</strong> entries
                    </div>
                    <div class="pagination-wrapper">
                        {{ $timeSlots->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Info Cards -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-check fs-1 text-success mb-2"></i>
                    <h5 class="card-title">Class Periods</h5>
                    <p class="text-muted mb-0">{{ $timeSlots->where('slot_type', 'instructional')->count() }} instructional slots configured</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-pause-circle fs-1 text-warning mb-2"></i>
                    <h5 class="card-title">Break Times</h5>
                    <p class="text-muted mb-0">{{ $timeSlots->where('is_break', true)->count() }} break slots configured</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-info">
                <div class="card-body text-center">
                    <i class="bi bi-clock-history fs-1 text-info mb-2"></i>
                    <h5 class="card-title">Total Duration</h5>
                    <p class="text-muted mb-0">{{ $timeSlots->sum(fn($s) => $s->duration) }} minutes total</p>
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
    
    .card-footer {
        border-top: 1px solid #e9ecef !important;
    }
</style>
@endpush
@endsection
