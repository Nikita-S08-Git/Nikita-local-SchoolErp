@extends('layouts.app')

@section('title', 'Timetable - Grid View')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 bg-gradient-primary">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 text-white">
                                <i class="bi bi-calendar-week me-2"></i>Timetable Management
                            </h4>
                            <p class="text-white-50 mb-0 small">View and manage weekly timetable per division</p>
                        </div>
                        <div class="btn-group" role="group">
                            @if($selectedDivision)
                            <a href="{{ route('academic.timetable.export.pdf', ['division_id' => $selectedDivision->id]) }}"
                               class="btn btn-sm btn-light" target="_blank">
                                <i class="bi bi-file-earmark-pdf"></i> Export PDF
                            </a>
                            @endif
                            @role('admin|principal')
                            <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addClassModal">
                                <i class="bi bi-plus-circle"></i> Add Class
                            </button>
                            @endrole
                            <button type="button" class="btn btn-sm btn-light" onclick="window.print()">
                                <i class="bi bi-printer"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Division Selection & Date Filter -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('academic.timetable.grid') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">
                        <i class="bi bi-calendar-event"></i> Select Date
                    </label>
                    <input type="date" name="date" id="dateFilter" 
                           class="form-select form-select-sm" 
                           value="{{ request('date') ?? date('Y-m-d') }}"
                           onchange="checkSunday(this); this.form.submit();">
                    <div class="form-text text-danger small mt-1" id="sundayWarning" style="display: none;">
                        <i class="bi bi-exclamation-triangle"></i> Sundays are off days
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">
                        <i class="bi bi-building"></i> Select Division
                    </label>
                    <select name="division_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Choose Division --</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                {{ $division->division_name }} — {{ $division->program->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">
                        <i class="bi bi-calendar3"></i> Academic Year
                    </label>
                    <select name="academic_year_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Current Year</option>
                    </select>
                </div>
                <div class="col-md-3">
                    @role('admin|principal')
                    <label class="form-label small text-muted mb-1 d-block">&nbsp;</label>
                    <button type="button" class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#addClassModal">
                        <i class="bi bi-plus-circle"></i> Add Class
                    </button>
                    @endrole
                </div>
            </form>
        </div>
    </div>

    @if($selectedDivision && $selectedDate)
    <!-- Selected Date Display -->
    <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
        <i class="bi bi-calendar-check fs-4 me-3"></i>
        <div>
            <strong>Showing timetable for:</strong> {{ \Carbon\Carbon::parse($selectedDate)->format('l, F d, Y') }}
            @if($isHoliday)
                <span class="badge bg-danger ms-2">Holiday: {{ $holidayTitle }}</span>
            @endif
        </div>
    </div>
    @endif

    @if($selectedDivision)
    <!-- Weekly Timetable Grid -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-event me-2 text-primary"></i>
                        {{ $selectedDivision->division_name }}
                        <span class="text-muted fw-normal">| Weekly Timetable</span>
                    </h5>
                    <small class="text-muted">{{ $selectedDivision->program->name ?? '' }}</small>
                </div>
                <div class="d-flex gap-2">
                    @role('admin|principal')
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
                        <i class="bi bi-plus-circle me-2"></i>Add Class
                    </button>
                    @endrole
                    <a href="{{ route('academic.timetable.export.pdf', ['division_id' => $selectedDivision->id]) }}"
                       class="btn btn-outline-danger" target="_blank">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 timetable-grid">
                    <thead class="table-light">
                        <tr>
                            <th width="110" class="text-center align-middle bg-white border-bottom">
                                <i class="bi bi-clock text-primary"></i>
                                <div class="small text-muted">Time</div>
                            </th>
                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                <th class="text-center align-middle border-bottom" 
                                    style="min-width: 160px; background: linear-gradient(135deg, {{ \App\Models\Academic\Timetable::DAY_COLORS[$day] }}15 0%, {{ \App\Models\Academic\Timetable::DAY_COLORS[$day] }}05 100%);">
                                    <span class="badge px-3 py-2" 
                                          style="background-color: {{ \App\Models\Academic\Timetable::DAY_COLORS[$day] }}; color: white; font-weight: 500;">
                                        {{ ucfirst($day) }}
                                    </span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($timeSlots as $slot)
                            <tr class="timetable-row">
                                <td class="text-center align-middle bg-light border-end">
                                    <div class="time-display">
                                        <div class="fw-bold text-primary">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}</div>
                                        <div class="text-muted small">
                                            <i class="bi bi-arrow-down-short"></i>
                                        </div>
                                        <div class="fw-bold text-primary">{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</div>
                                        @if($slot->name)
                                            <div class="badge bg-secondary mt-1">{{ $slot->name }}</div>
                                        @endif
                                    </div>
                                </td>
                                @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                    @php
                                        $dayClasses = $timetables[$day] ?? collect();
                                        $class = $dayClasses->first(function($c) use ($slot) {
                                            return \Carbon\Carbon::parse($c->start_time)->format('H:i') === \Carbon\Carbon::parse($slot->start_time)->format('H:i');
                                        });
                                        $slotColor = $class ? ($class->day_color ?? '#6c757d') : '#dee2e6';
                                    @endphp
                                    <td class="align-middle p-2 timetable-cell"
                                        style="min-width: 160px; min-height: 90px; vertical-align: top;">
                                        @if($class)
                                            @if($class->status === 'cancelled')
                                                <div class="timetable-slot cancelled h-100 p-2 rounded-3 border border-danger position-relative"
                                                     style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); opacity: 0.85;">
                                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                                        <span class="badge bg-danger">{{ $class->subject->code ?? 'N/A' }}</span>
                                                        <i class="bi bi-x-circle-fill text-danger"></i>
                                                    </div>
                                                    <div class="small text-muted mb-1">{{ Str::limit($class->subject->name ?? '', 20) }}</div>
                                                    <div class="small text-muted">
                                                        <i class="bi bi-person"></i> {{ Str::limit($class->teacher->name ?? 'N/A', 15) }}
                                                    </div>
                                                    <div class="small text-muted">
                                                        <i class="bi bi-geo-alt"></i> {{ $class->room->room_number ?? $class->room_number ?? 'TBA' }}
                                                    </div>
                                                    
                                                    @role('admin|principal')
                                                    <div class="action-buttons mt-2 d-flex gap-1">
                                                        <button type="button"
                                                                class="btn btn-warning btn-sm btn-edit-class"
                                                                data-id="{{ $class->id }}"
                                                                title="Edit"
                                                                data-bs-toggle="tooltip">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button type="button"
                                                                class="btn btn-danger btn-sm btn-delete-class"
                                                                data-id="{{ $class->id }}"
                                                                data-name="{{ $class->subject->name ?? 'Class' }} ({{ $class->day_name }})"
                                                                title="Delete"
                                                                data-bs-toggle="tooltip">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                    @endrole
                                                </div>
                                            @else
                                                <div class="timetable-slot h-100 p-2 rounded-3 border-0 position-relative"
                                                     style="background: linear-gradient(135deg, {{ $slotColor }}20 0%, {{ $slotColor }}10 100%);
                                                            border-left: 4px solid {{ $slotColor }};
                                                            box-shadow: 0 2px 4px rgba(0,0,0,0.08);">
                                                    @php
                                                        $colorEmoji = match($slotColor) {
                                                            '#3B82F6' => '📘',  // Blue - Monday
                                                            '#8B5CF6' => '📒',  // Purple - Tuesday
                                                            '#10B981' => '📗',  // Green - Wednesday
                                                            '#F59E0B' => '📙',  // Amber - Thursday
                                                            '#EF4444' => '📕',  // Red - Friday
                                                            '#6366F1' => '📓',  // Indigo - Saturday
                                                            '#198754' => '📘',  // Green - Accounts
                                                            '#dc3545' => '📗',  // Red - Business Law
                                                            '#fd7e14' => '📙',  // Orange - Marketing
                                                            '#0dcaf0' => '📕',  // Cyan
                                                            '#6f42c1' => '📒',  // Purple
                                                            '#e83e8c' => '📓',  // Pink
                                                            default => '📚'
                                                        };
                                                    @endphp
                                                    <div class="small fw-bold mb-1">{{ $colorEmoji }} <strong>{{ Str::limit($class->subject->name ?? 'N/A', 22) }}</strong></div>
                                                    <div class="small text-muted mb-1">
                                                        {{ Str::limit($class->teacher->name ?? 'No Teacher', 18) }}
                                                    </div>
                                                    <div class="small text-muted mb-2">
                                                        {{ $class->room->room_number ?? $class->room_number ?? 'TBA' }}
                                                    </div>
                                                    
                                                    @role('admin|principal')
                                                    <div class="action-buttons mt-1 d-flex gap-2">
                                                        <a href="{{ route('academic.timetable.edit', $class->id) }}"
                                                           class="btn btn-warning btn-sm"
                                                           title="Edit"
                                                           data-bs-toggle="tooltip">
                                                            ✏ Edit
                                                        </a>
                                                        <button type="button"
                                                                class="btn btn-danger btn-sm btn-delete-class"
                                                                data-id="{{ $class->id }}"
                                                                data-name="{{ $class->subject->name ?? 'Class' }} ({{ $class->day_name }})"
                                                                title="Delete"
                                                                data-bs-toggle="tooltip">
                                                            🗑 Delete
                                                        </button>
                                                    </div>
                                                    @endrole
                                                </div>
                                            @endif
                                        @else
                                            <div class="h-100 d-flex align-items-center justify-content-center">
                                                @role('admin|principal')
                                                <button type="button" 
                                                        class="btn btn-outline-primary btn-sm add-class-btn"
                                                        onclick="openAddClassModal('{{ ucfirst($day) }}', '{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}', '{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}', '{{ $selectedDivision->id }}')"
                                                        title="Add Class"
                                                        data-bs-toggle="tooltip">
                                                    <i class="bi bi-plus-circle"></i> Add Class
                                                </button>
                                                @else
                                                <span class="badge bg-light text-muted border">
                                                    <i class="bi bi-check-circle"></i> Free
                                                </span>
                                                @endrole
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-clock-history fs-1 d-block mb-3"></i>
                                        <h5>No Time Slots Configured</h5>
                                        <p class="mb-3">Please configure time slots to build your timetable</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Legend & Stats -->
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom py-2">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Legend</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                            <span class="badge me-2" style="background: linear-gradient(135deg, #198754 0%, #146c43 100%);">
                                <i class="bi bi-check-circle"></i>
                            </span>
                            <span class="small">Active Class</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge me-2 bg-danger">
                                <i class="bi bi-x-circle"></i>
                            </span>
                            <span class="small">Cancelled</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge me-2 bg-light text-dark border">
                                <i class="bi bi-circle"></i>
                            </span>
                            <span class="small">Free Slot</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom py-2">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-2">
                                <div class="fw-bold text-primary">{{ $totalClasses ?? 0 }}</div>
                                <div class="small text-muted">Classes</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-success bg-opacity-10 rounded-3 p-2">
                                <div class="fw-bold text-success">{{ $totalHours ?? 0 }}</div>
                                <div class="small text-muted">Hours/Week</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-info bg-opacity-10 rounded-3 p-2">
                                <div class="fw-bold text-info">{{ count($timeSlots) }}</div>
                                <div class="small text-muted">Time Slots</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- No Division Selected -->
    <div class="card shadow-sm border-0">
        <div class="card-body text-center py-5">
            <div class="mb-4">
                <i class="bi bi-calendar-week fs-1 text-primary d-block mb-3"></i>
                <h4 class="text-muted">Select a Division</h4>
                <p class="text-muted">Choose a division from the dropdown above to view its weekly timetable</p>
            </div>
            <div class="row justify-content-center g-3">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-eye text-primary fs-3 d-block mb-2"></i>
                            <h6 class="small">View Schedule</h6>
                            <p class="small text-muted mb-0">See weekly timetable at a glance</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-plus-circle text-success fs-3 d-block mb-2"></i>
                            <h6 class="small">Add Classes</h6>
                            <p class="small text-muted mb-0">Schedule new classes easily</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-file-earmark-pdf text-danger fs-3 d-block mb-2"></i>
                            <h6 class="small">Export PDF</h6>
                            <p class="small text-muted mb-0">Download printable timetable</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
/* Timetable Grid Styles */
.timetable-grid {
    border-collapse: separate;
    border-spacing: 0;
}

.timetable-grid thead th {
    font-weight: 600;
    font-size: 0.875rem;
    padding: 0.75rem 0.5rem;
}

.timetable-grid tbody td {
    transition: all 0.2s ease;
}

.timetable-row:hover td {
    background-color: rgba(0, 0, 0, 0.02);
}

.timetable-cell:hover {
    background-color: rgba(0, 0, 0, 0.03);
}

/* Timetable Slot Cards */
.timetable-slot {
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.timetable-slot::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.timetable-slot:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.timetable-slot:hover::before {
    opacity: 1;
}

.timetable-slot.cancelled:hover {
    transform: none;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
}

/* Time Display */
.time-display {
    line-height: 1.4;
}

/* Action buttons on timetable slots */
.action-buttons {
    opacity: 0;
    transition: opacity 0.2s ease;
}

.timetable-slot:hover .action-buttons {
    opacity: 1;
}

.action-buttons .btn {
    padding: 0.2rem 0.4rem;
    font-size: 0.75rem;
}

/* Responsive adjustments */
@media (max-width: 1400px) {
    .timetable-grid {
        font-size: 0.85rem;
    }
    
    .timetable-cell {
        min-width: 140px !important;
    }
}

@media (max-width: 1200px) {
    .timetable-grid {
        font-size: 0.8rem;
    }
    
    .timetable-cell {
        min-width: 120px !important;
    }
    
    .timetable-slot .small {
        font-size: 0.75rem;
    }
}

/* Print Styles */
@media print {
    .card-header .btn-group,
    .btn-group {
        display: none !important;
    }
    
    .timetable-slot {
        box-shadow: none !important;
        transform: none !important;
    }
}

/* Custom scrollbar for table */
.table-responsive::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Gradient header background */
.bg-gradient-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
}
</style>

<script>
function checkSunday(input) {
    const selectedDate = new Date(input.value + 'T00:00:00');
    const day = selectedDate.getDay();
    const warning = document.getElementById('sundayWarning');
    
    if (day === 0) { // Sunday
        warning.style.display = 'block';
        // Auto-select next Monday
        const nextMonday = new Date(selectedDate);
        nextMonday.setDate(selectedDate.getDate() + 1);
        input.value = nextMonday.toISOString().split('T')[0];
    } else {
        warning.style.display = 'none';
    }
}

// Check on page load
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('dateFilter');
    if (dateInput && dateInput.value) {
        checkSunday(dateInput);
    }
});
</script>

@include('academic.timetable.timetable-modals')
@endsection
