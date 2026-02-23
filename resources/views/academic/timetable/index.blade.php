@extends('layouts.app')

@section('page-title', 'Timetable Management')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1"><i class="bi bi-calendar-week me-2 text-primary"></i>Timetable Management</h3>
                    <p class="text-muted mb-0">Manage weekly class schedules and room allocations</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('academic.timetable.table') }}" class="btn btn-info text-white">
                        <i class="bi bi-table"></i> Table View
                    </a>
                    <a href="{{ route('academic.timetable.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add Schedule
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Master Data Overview Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card shadow-sm border-primary">
                <div class="card-body text-center">
                    <i class="bi bi-building text-primary" style="font-size: 2rem;"></i>
                    <h5 class="mt-2 mb-0">{{ $departmentsCount }}</h5>
                    <small class="text-muted">Departments</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center">
                    <i class="bi bi-graduation-cap text-success" style="font-size: 2rem;"></i>
                    <h5 class="mt-2 mb-0">{{ $programsCount }}</h5>
                    <small class="text-muted">Programs</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-info">
                <div class="card-body text-center">
                    <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
                    <h5 class="mt-2 mb-0">{{ $divisionsCount }}</h5>
                    <small class="text-muted">Divisions</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-book text-warning" style="font-size: 2rem;"></i>
                    <h5 class="mt-2 mb-0">{{ $subjectsCount }}</h5>
                    <small class="text-muted">Subjects</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-danger">
                <div class="card-body text-center">
                    <i class="bi bi-person-badge text-danger" style="font-size: 2rem;"></i>
                    <h5 class="mt-2 mb-0">{{ $teachersCount }}</h5>
                    <small class="text-muted">Teachers</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-secondary">
                <div class="card-body text-center">
                    <i class="bi bi-clock-history text-secondary" style="font-size: 2rem;"></i>
                    <h5 class="mt-2 mb-0">{{ $timetables->count() }}</h5>
                    <small class="text-muted">Scheduled</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Division Selection -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('academic.timetable.index') }}">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="division_id" class="form-label">
                                        <i class="bi bi-funnel me-1"></i> Select Division
                                    </label>
                                    <select name="division_id" id="division_id" class="form-select" onchange="this.form.submit()">
                                        <option value="">Choose Division</option>
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
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <a href="{{ route('academic.timetable.create') }}" class="btn btn-success">
                                            <i class="bi bi-plus-lg me-1"></i>Add New Schedule
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($selectedDivision)
    <!-- Weekly Timetable Grid -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-calendar-week me-2"></i>
                        Weekly Timetable - {{ $selectedDivision->division_name }}
                        @if($selectedDivision->program)
                            <small class="text-light ms-2">({{ $selectedDivision->program->name }})</small>
                        @endif
                    </h6>
                    <span class="badge bg-light text-dark">
                        {{ $timetables->count() }} Classes Scheduled
                    </span>
                </div>
                <div class="card-body p-0">
                    @if($timetables->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="12%" class="text-center">Time Slot</th>
                                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                            <th width="14.6%" class="text-center">{{ substr($day, 0, 3) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Get unique time slots from timetables
                                        $timeSlots = $timetables->unique('start_time')
                                            ->sortBy('start_time')
                                            ->pluck('start_time');
                                        
                                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                    @endphp

                                    @foreach($timeSlots as $startTime)
                                        @php
                                            $slotClasses = $timetables->where('start_time', $startTime);
                                            $firstClass = $slotClasses->first();
                                            $endTime = $firstClass->end_time ?? '';
                                            $timeLabel = substr($startTime, 0, 5) . '-' . substr($endTime, 0, 5);
                                        @endphp
                                        <tr>
                                            <td class="text-center fw-bold bg-light align-middle">
                                                {{ $timeLabel }}
                                            </td>
                                            @foreach($days as $day)
                                                <td class="p-2" style="min-height: 120px; vertical-align: top;">
                                                    @php
                                                        $class = $slotClasses->firstWhere('day_of_week', $day);
                                                    @endphp

                                                    @if($class)
                                                        <div class="card border-primary h-100">
                                                            <div class="card-body p-2">
                                                                <h6 class="card-title text-primary mb-1 fw-bold">
                                                                    {{ $class->subject->name ?? 'N/A' }}
                                                                </h6>
                                                                <p class="card-text mb-1 small">
                                                                    <i class="bi bi-person me-1"></i>
                                                                    {{ $class->teacher->name ?? 'No Teacher' }}
                                                                </p>
                                                                @if($class->room)
                                                                    <p class="card-text mb-1 small">
                                                                        <i class="bi bi-geo-alt me-1"></i>
                                                                        Room: {{ $class->room }}
                                                                    </p>
                                                                @endif
                                                                <div class="d-flex gap-1 mt-2">
                                                                    <a href="{{ route('academic.timetable.edit', $class) }}" 
                                                                       class="btn btn-sm btn-warning flex-fill">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </a>
                                                                    <form method="POST" 
                                                                          action="{{ route('academic.timetable.destroy', $class) }}" 
                                                                          class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" 
                                                                                class="btn btn-sm btn-danger flex-fill"
                                                                                onclick="return confirm('Delete this class?')">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">No Classes Scheduled</h5>
                            <p class="text-muted">Start by adding classes to the timetable</p>
                            <a href="{{ route('academic.timetable.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i>Add First Class
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- No Division Selected -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-calendar-week text-muted" style="font-size: 5rem;"></i>
                    <h5 class="mt-3 text-muted">Select a Division to View Timetable</h5>
                    <p class="text-muted">Choose a division from the dropdown above to see its weekly schedule</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
