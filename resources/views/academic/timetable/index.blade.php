@extends('layouts.app')

@section('page-title', 'Timetable Management')

@section('content')
<style>
    .timetable-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    
    .timetable-card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    
    .timetable-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px 12px 0 0;
        border: none;
        padding: 1.25rem 1.5rem;
    }
    
    .time-slot-cell {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
        font-weight: 600;
        font-size: 0.85rem;
        color: #4a5568;
        border-right: 3px solid #667eea;
    }
    
    .class-card {
        border: none;
        border-radius: 10px;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
        transition: all 0.2s ease;
        height: 100%;
    }
    
    .class-card:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.25);
    }
    
    .class-card.subject {
        color: #667eea;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .class-card .teacher {
        color: #4a5568;
        font-size: 0.75rem;
    }
    
    .class-card .room {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        display: inline-block;
        margin-top: 4px;
    }
    
    .day-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.75rem;
        padding: 12px 8px;
    }
    
    .empty-slot {
        color: #cbd5e0;
        font-size: 1.5rem;
    }
    
    .btn-action {
        padding: 4px 8px;
        font-size: 0.75rem;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    
    .btn-action:hover {
        transform: scale(1.1);
    }
    
    .division-select-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 1.5rem;
        color: white;
    }
    
    .division-select-card label {
        color: rgba(255,255,255,0.9);
        font-weight: 500;
    }
    
    .division-select-card .form-select {
        border: 2px solid rgba(255,255,255,0.3);
        background: rgba(255,255,255,0.95);
        border-radius: 8px;
        font-weight: 500;
    }
    
    .stats-badge {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 8px 16px;
        font-weight: 600;
        font-size: 0.85rem;
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="mb-1 fw-bold" style="color: #667eea;">
                        <i class="bi bi-calendar-week me-2"></i>Timetable Management
                    </h2>
                    <p class="text-muted mb-0">Manage weekly class schedules and room allocations</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('academic.timetable.table') }}" class="btn btn-outline-primary">
                        <i class="bi bi-table me-1"></i> Table View
                    </a>
                    <a href="{{ route('academic.timetable.create') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        <i class="bi bi-plus-lg me-1"></i> Add Schedule
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Division Selection -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="division-select-card shadow">
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
                                    <a href="{{ route('academic.timetable.create') }}" class="btn btn-light text-primary fw-semibold">
                                        <i class="bi bi-plus-lg me-1"></i>Add New Schedule
                                    </a>
                                </div>
                            </div>
                        </div>
                        @if($selectedDivision)
                        <div class="col-md-4 text-md-end">
                            <span class="stats-badge">
                                <i class="bi bi-clock-history me-1"></i>
                                {{ $timetables->count() }} Classes Scheduled
                            </span>
                        </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($selectedDivision)
    <!-- Weekly Timetable Grid -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm timetable-card">
                <div class="timetable-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-white">
                        <i class="bi bi-calendar-week me-2"></i>
                        Weekly Timetable - <span class="fw-bold">{{ $selectedDivision->division_name }}</span>
                        @if($selectedDivision->academicYear)
                            <span class="opacity-75 ms-2">({{ $selectedDivision->academicYear->name }})</span>
                        @endif
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($timetables->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" style="min-width: 100%;">
                                <thead>
                                    <tr>
                                        <th width="10%" class="time-slot-cell text-center py-3">Time Slot</th>
                                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                            <th width="15%" class="day-header text-center">{{ $day }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
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
                                            $timeLabel = substr($startTime, 0, 5) . '<br><small class="fw-normal text-muted">to</small><br>' . substr($endTime, 0, 5);
                                        @endphp
                                        <tr>
                                            <td class="time-slot-cell text-center align-middle py-3">
                                                {!! $timeLabel !!}
                                            </td>
                                            @foreach($days as $day)
                                                <td class="p-3" style="min-height: 140px; vertical-align: top; background: #fafbfc;">
                                                    @php
                                                        $class = $slotClasses->firstWhere('day_of_week', $day);
                                                    @endphp

                                                    @if($class)
                                                        <div class="class-card p-3">
                                                            <h6 class="subject mb-2">
                                                                <i class="bi bi-book me-1"></i>
                                                                {{ $class->subject->name ?? 'N/A' }}
                                                            </h6>
                                                            <p class="teacher mb-2">
                                                                <i class="bi bi-person-circle me-1"></i>
                                                                {{ $class->teacher->name ?? 'No Teacher' }}
                                                            </p>
                                                            @if($class->room)
                                                                <span class="room">
                                                                    <i class="bi bi-geo-alt me-1"></i>{{ $class->room }}
                                                                </span>
                                                            @endif
                                                            <div class="d-flex gap-1 mt-3">
                                                                <a href="{{ route('academic.timetable.edit', $class) }}"
                                                                   class="btn btn-sm btn-warning btn-action"
                                                                   title="Edit">
                                                                    <i class="bi bi-pencil"></i>
                                                                </a>
                                                                <form method="POST"
                                                                      action="{{ route('academic.timetable.destroy', $class) }}"
                                                                      class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                            class="btn btn-sm btn-danger btn-action"
                                                                            title="Delete"
                                                                            onclick="return confirm('Delete this class?')">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="empty-slot text-center" style="margin-top: 50px;">
                                                            <i class="bi bi-dash"></i>
                                                        </div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5" style="background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 5rem; opacity: 0.5;"></i>
                            <h5 class="mt-3 text-muted">No Classes Scheduled</h5>
                            <p class="text-muted mb-4">Start by adding classes to the timetable</p>
                            <a href="{{ route('academic.timetable.create') }}" class="btn btn-primary btn-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
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
            <div class="card shadow-sm timetable-card">
                <div class="card-body text-center py-5" style="background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);">
                    <i class="bi bi-calendar-week text-muted" style="font-size: 6rem; opacity: 0.3;"></i>
                    <h4 class="mt-4 text-muted fw-semibold">Select a Division to View Timetable</h4>
                    <p class="text-muted">Choose a division from the dropdown above to see its weekly schedule</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
