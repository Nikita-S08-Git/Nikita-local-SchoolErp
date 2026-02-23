@extends('layouts.app')

@section('title', 'Timetable - Table View')

@section('content')
<div class="container-fluid p-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1"><i class="bi bi-table me-2 text-primary"></i>Timetable - Table View</h3>
                    <p class="text-muted mb-0">View and manage all scheduled classes</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('academic.timetable.index') }}" class="btn btn-info text-white">
                        <i class="bi bi-calendar-week"></i> Grid View
                    </a>
                    <a href="{{ route('academic.timetable.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-lg"></i> Add Schedule
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="mb-0">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-building me-1"></i> Department</label>
                            <select name="department_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-people me-1"></i> Division</label>
                            <select name="division_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Divisions</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                        {{ $division->division_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-calendar3 me-1"></i> Day</label>
                            <select name="day" class="form-select" onchange="this.form.submit()">
                                <option value="">All Days</option>
                                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                    <option value="{{ $day }}" {{ request('day') == $day ? 'selected' : '' }}>{{ $day }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <a href="{{ route('academic.timetable.table') }}" class="btn btn-secondary w-100">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset Filters
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Timetable Table -->
    @if($timetables->count() > 0)
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th width="60">#</th>
                            <th>Department</th>
                            <th>Program</th>
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
                            <td>{{ $timetables->firstItem() + $index }}</td>
                            <td>
                                @if($schedule->division->program->department)
                                    <span class="badge bg-primary">{{ $schedule->division->program->department->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($schedule->division->program)
                                    <small>{{ $schedule->division->program->name }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $schedule->division->division_name ?? 'N/A' }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $schedule->day_of_week }}</span>
                            </td>
                            <td>
                                <i class="bi bi-clock text-muted me-1"></i>
                                {{ substr($schedule->start_time, 0, 5) }} – {{ substr($schedule->end_time, 0, 5) }}
                            </td>
                            <td>
                                <strong class="text-primary">{{ $schedule->subject->name ?? 'N/A' }}</strong>
                                @if($schedule->subject)
                                    <br><small class="text-muted">{{ $schedule->subject->code }}</small>
                                @endif
                            </td>
                            <td>
                                @if($schedule->teacher)
                                    <i class="bi bi-person-circle me-1"></i>{{ $schedule->teacher->name }}
                                @else
                                    <span class="text-muted">No Teacher</span>
                                @endif
                            </td>
                            <td>
                                @if($schedule->room)
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $schedule->room }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="dropdown">
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
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $timetables->links() }}
    </div>
    @else
    <!-- Empty State -->
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-calendar-x text-muted" style="font-size: 5rem;"></i>
            <h5 class="mt-3 text-muted">No Timetable Entries Found</h5>
            <p class="text-muted">
                @if(request()->filled(['department_id', 'division_id', 'day']))
                    No entries match your filters. Try adjusting your search criteria.
                @else
                    Start by creating your first timetable entry.
                @endif
            </p>
            <a href="{{ route('academic.timetable.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Add Schedule
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
