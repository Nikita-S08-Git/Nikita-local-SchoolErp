@extends('layouts.app')

@section('title', 'Timetable - Table View')

@section('content')
<style>
    .filter-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 1.5rem;
    }
    
    .filter-card label {
        color: rgba(255,255,255,0.9);
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .filter-card .form-select {
        border: 2px solid rgba(255,255,255,0.3);
        background: rgba(255,255,255,0.95);
        border-radius: 8px;
        font-weight: 500;
    }
    
    .filter-card .btn-light {
        background: rgba(255,255,255,0.95);
        border: none;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    
    .filter-card .btn-light:hover {
        background: white;
        transform: translateY(-2px);
    }
    
    .data-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    
    .table thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.75rem;
        padding: 1rem 0.75rem;
        border: none;
    }
    
    .table tbody td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background: #f8f9ff;
        transform: scale(1.005);
    }
    
    .badge-division {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    
    .badge-day {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    
    .badge-room {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    .subject-name {
        color: #667eea;
        font-weight: 700;
        font-size: 0.9rem;
    }
    
    .subject-code {
        color: #718096;
        font-size: 0.75rem;
    }
    
    .teacher-name {
        color: #4a5568;
        font-weight: 500;
    }
    
    .time-display {
        color: #4a5568;
        font-weight: 600;
        font-size: 0.85rem;
    }
    
    .btn-action-group .dropdown-toggle {
        border-radius: 8px;
        font-weight: 600;
        padding: 6px 12px;
        transition: all 0.2s ease;
    }
    
    .btn-action-group .dropdown-toggle:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .dropdown-menu {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    
    .dropdown-item {
        padding: 0.625rem 1rem;
        transition: all 0.2s ease;
        border-radius: 6px;
        margin: 2px 8px;
    }
    
    .dropdown-item:hover {
        background: #f8f9ff;
        transform: translateX(4px);
    }
    
    .empty-state {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
        border-radius: 12px;
        padding: 4rem 2rem;
        text-align: center;
    }
    
    .empty-state i {
        opacity: 0.3;
    }
    
    .pagination .page-link {
        border: none;
        border-radius: 8px;
        margin: 0 2px;
        color: #667eea;
        font-weight: 600;
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .pagination .page-link:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .pagination .page-item.disabled .page-link {
        cursor: not-allowed;
        transform: none;
    }
</style>

<div class="container-fluid p-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="mb-1 fw-bold" style="color: #667eea;">
                        <i class="bi bi-table me-2"></i>Timetable - Table View
                    </h2>
                    <p class="text-muted mb-0">View and manage all scheduled classes</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('academic.timetable.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-calendar-week me-1"></i> Grid View
                    </a>
                    <a href="{{ route('academic.timetable.create') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        <i class="bi bi-plus-lg me-1"></i> Add Schedule
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card shadow mb-4">
        <form method="GET" class="mb-0">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-people me-1"></i> Division
                        </label>
                        <select name="division_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Divisions</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                    {{ $division->academicYear->name ?? 'N/A' }} - {{ $division->division_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-calendar3 me-1"></i> Day
                        </label>
                        <select name="day" class="form-select" onchange="this.form.submit()">
                            <option value="">All Days</option>
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                <option value="{{ $day }}" {{ request('day') == $day ? 'selected' : '' }}>{{ $day }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <a href="{{ route('academic.timetable.table') }}" class="btn btn-light w-100">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Timetable Table -->
    @if($timetables->count() > 0)
    <div class="card data-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="60" class="text-center">#</th>
                        <th>Division</th>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Room</th>
                        <th width="100">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($timetables as $index => $schedule)
                    <tr>
                        <td class="text-center text-muted">{{ $timetables->firstItem() + $index }}</td>
                        <td>
                            <span class="badge-division">
                                {{ $schedule->division->division_name ?? 'N/A' }}
                            </span>
                            @if($schedule->division->academicYear)
                                <br><small class="text-muted">{{ $schedule->division->academicYear->name }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge-day">{{ $schedule->day_of_week }}</span>
                        </td>
                        <td>
                            <span class="time-display">
                                <i class="bi bi-clock me-1"></i>
                                {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                            </span>
                        </td>
                        <td>
                            <div class="subject-name">{{ $schedule->subject->name ?? 'N/A' }}</div>
                            @if($schedule->subject)
                                <small class="subject-code">{{ $schedule->subject->code }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="teacher-name">
                                <i class="bi bi-person-circle me-1"></i>
                                {{ $schedule->teacher->name ?? 'No Teacher' }}
                            </span>
                        </td>
                        <td>
                            @if($schedule->room)
                                <span class="badge-room">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $schedule->room }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-action-group dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('academic.timetable.edit', $schedule) }}">
                                            <i class="bi bi-pencil me-2"></i>Edit
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('academic.timetable.destroy', $schedule) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Delete this schedule?')">
                                                <i class="bi bi-trash me-2"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        @if($timetables->hasPages())
        <nav aria-label="Timetable Pagination">
            <ul class="pagination pagination-lg justify-content-center">
                {{-- Previous Button --}}
                @if($timetables->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link" style="border-radius: 10px; margin: 0 4px; border: 2px solid #e2e8f0; color: #cbd5e0;">
                            <i class="bi bi-chevron-double-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $timetables->previousPageUrl() }}" style="border-radius: 10px; margin: 0 4px; border: 2px solid #667eea; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 600;">
                            <i class="bi bi-chevron-double-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Numbers --}}
                @foreach($timetables->getUrlRange(1, $timetables->lastPage()) as $page => $url)
                    @if($page == $timetables->currentPage())
                        <li class="page-item active">
                            <span class="page-link" style="border-radius: 10px; margin: 0 4px; border: 2px solid #667eea; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 700; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);">
                                {{ $page }}
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}" style="border-radius: 10px; margin: 0 4px; border: 2px solid #e2e8f0; background: white; color: #667eea; font-weight: 600; transition: all 0.2s ease;">
                                {{ $page }}
                            </a>
                        </li>
                    @endif
                @endforeach

                {{-- Next Button --}}
                @if($timetables->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $timetables->nextPageUrl() }}" style="border-radius: 10px; margin: 0 4px; border: 2px solid #667eea; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 600;">
                            <i class="bi bi-chevron-double-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link" style="border-radius: 10px; margin: 0 4px; border: 2px solid #e2e8f0; color: #cbd5e0;">
                            <i class="bi bi-chevron-double-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
        @endif
    </div>
    @else
    <!-- Empty State -->
    <div class="empty-state shadow-sm">
        <i class="bi bi-calendar-x" style="font-size: 6rem; color: #667eea;"></i>
        <h4 class="mt-4 text-muted fw-semibold">No Timetable Entries Found</h4>
        <p class="text-muted mb-4">
            @if(request()->filled(['division_id', 'day']))
                No entries match your filters. Try adjusting your search criteria.
            @else
                Start by creating your first timetable entry.
            @endif
        </p>
        <a href="{{ route('academic.timetable.create') }}" class="btn btn-primary btn-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
            <i class="bi bi-plus-lg me-1"></i>Add Schedule
        </a>
    </div>
    @endif
</div>
@endsection
