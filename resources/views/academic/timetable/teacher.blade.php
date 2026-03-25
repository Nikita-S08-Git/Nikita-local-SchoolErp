@extends('layouts.teacher')

@section('title', 'My Timetable')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-calendar-alt me-2 text-primary"></i>My Timetable</h2>
                    <p class="text-muted mb-0">View your teaching schedule</p>
                </div>
                <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Holiday Alert -->
    @if($todayHoliday)
    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert" style="border-radius: 14px; border: none; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">
        <i class="fas fa-exclamation-triangle-fill fs-3 me-3"></i>
        <div class="flex-grow-1">
            <strong class="fs-5">Today is a Holiday!</strong> - {{ $todayHoliday->title }}
            @if($todayHoliday->start_date != $todayHoliday->end_date)
                <span class="ms-2">
                    ({{ \Carbon\Carbon::parse($todayHoliday->start_date)->format('d M') }} to {{ \Carbon\Carbon::parse($todayHoliday->end_date)->format('d M Y') }})
                </span>
            @else
                <span class="ms-2">({{ \Carbon\Carbon::parse($todayHoliday->start_date)->format('d M Y') }})</span>
            @endif
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100" style="border-radius: 14px; border: none;">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <h5 class="mb-2">Today's Classes</h5>
                    <p class="display-4 fw-bold text-primary mb-0">{{ $todayClasses->count() }}</p>
                    <small class="text-muted">{{ ucfirst($today) }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100" style="border-radius: 14px; border: none;">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="fas fa-book"></i>
                    </div>
                    <h5 class="mb-2">Total Subjects</h5>
                    <p class="h2 fw-bold text-success mb-0">{{ $weekClasses->flatMap(fn($classes) => $classes->pluck('subject_id')->toArray())->unique()->count() }}</p>
                    <small class="text-muted">This week</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100" style="border-radius: 14px; border: none;">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="mb-2">Divisions Taught</h5>
                    <p class="h2 fw-bold text-info mb-0">{{ $weekClasses->flatMap(fn($classes) => $classes->pluck('division_id')->toArray())->unique()->count() }}</p>
                    <small class="text-muted">This week</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Classes -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm h-100" style="border-radius: 14px; border: none;">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-sun me-2 text-warning"></i>Today's Schedule</h5>
                        <span class="badge bg-primary">{{ $todayClasses->count() }} Classes</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($todayClasses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 px-4"><i class="fas fa-clock me-2 text-muted"></i>Time</th>
                                        <th class="py-3 px-4"><i class="fas fa-book me-2 text-muted"></i>Subject</th>
                                        <th class="py-3 px-4"><i class="fas fa-users me-2 text-muted"></i>Division</th>
                                        <th class="py-3 px-4"><i class="fas fa-door-open me-2 text-muted"></i>Room</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayClasses as $index => $class)
                                    <tr class="{{ $index % 2 == 0 ? '' : 'bg-light' }}">
                                        <td class="px-4 py-3">
                                            <span class="fw-semibold text-primary">{{ $class->formatted_time_range ?? 'N/A' }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                                {{ $class->subject->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="fw-medium">{{ $class->division->division_name ?? 'N/A' }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $class->room->room_number ?? $class->room_number ?? 'TBA' }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-calendar-check fa-2x text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-2">No Classes Today</h5>
                            <p class="text-muted mb-0">Enjoy your free time!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Weekly Overview -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100" style="border-radius: 14px; border: none;">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-calendar-week me-2"></i>Weekly Overview</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th class="text-center">Classes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($days as $value => $label)
                                    @php
                                        $dayClasses = $weekClasses[$value] ?? collect();
                                    @endphp
                                    <tr class="{{ $value === $today ? 'table-primary' : '' }}">
                                        <td class="fw-medium">{{ $label }}</td>
                                        <td class="text-center">
                                            @if($dayClasses->count() > 0)
                                                <span class="badge bg-primary">{{ $dayClasses->count() }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
