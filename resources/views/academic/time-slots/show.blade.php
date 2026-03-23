@extends('layouts.app')

@section('title', 'Time Slot Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-clock me-2"></i>Time Slot Details</h2>
                    <p class="text-muted mb-0">{{ $timeSlot->slot_name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('academic.time-slots.edit', $timeSlot) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('academic.time-slots.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Time Slot Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Slot Code</label>
                            <div class="fs-5"><span class="badge bg-primary">{{ $timeSlot->slot_code }}</span></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Slot Name</label>
                            <div class="fs-5">{{ $timeSlot->slot_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Time Range</label>
                            <div class="fs-6">
                                <i class="bi bi-clock text-muted me-1"></i>
                                {{ substr($timeSlot->start_time, 0, 5) }} - {{ substr($timeSlot->end_time, 0, 5) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Duration</label>
                            <div class="fs-6"><span class="badge bg-info">{{ $timeSlot->duration }} minutes</span></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Slot Type</label>
                            <div class="fs-6">
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
                                <span class="badge bg-{{ $typeColors[$timeSlot->slot_type] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $timeSlot->slot_type)) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Sequence Order</label>
                            <div class="fs-6">{{ $timeSlot->sequence_order }}{{ $timeSlot->sequence_order == 1 ? ' (First)' : '' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Status</label>
                            <div class="fs-6">
                                @if($timeSlot->is_active)
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Inactive</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Is Break Time</label>
                            <div class="fs-6">
                                @if($timeSlot->is_break)
                                    <span class="badge bg-warning"><i class="bi bi-pause-circle"></i> Yes</span>
                                    @if($timeSlot->break_type)
                                        <span class="badge bg-{{ $timeSlot->break_type == 'lunch' ? 'danger' : 'warning' }}">
                                            {{ ucfirst(str_replace('_', ' ', $timeSlot->break_type)) }}
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-light text-dark">No</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Additional Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Available for Classes</label>
                            <div class="fs-6">
                                @if($timeSlot->available_for_classes)
                                    <span class="text-success"><i class="bi bi-check-circle"></i> Yes</span>
                                @else
                                    <span class="text-danger"><i class="bi bi-x-circle"></i> No</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Available for Exams</label>
                            <div class="fs-6">
                                @if($timeSlot->available_for_exams)
                                    <span class="text-success"><i class="bi bi-check-circle"></i> Yes</span>
                                @else
                                    <span class="text-danger"><i class="bi bi-x-circle"></i> No</span>
                                @endif
                            </div>
                        </div>
                        @if($timeSlot->academicSession)
                        <div class="col-md-6">
                            <label class="text-muted small">Academic Session</label>
                            <div class="fs-6">{{ $timeSlot->academicSession->session_name }}</div>
                        </div>
                        @endif
                        @if($timeSlot->assignedRoom)
                        <div class="col-md-6">
                            <label class="text-muted small">Assigned Room</label>
                            <div class="fs-6">{{ $timeSlot->assignedRoom->room_number }}</div>
                        </div>
                        @endif
                        @if($timeSlot->assignedTeacher)
                        <div class="col-md-6">
                            <label class="text-muted small">Assigned Teacher</label>
                            <div class="fs-6">{{ $timeSlot->assignedTeacher->name }}</div>
                        </div>
                        @endif
                        @if($timeSlot->applicable_days)
                        <div class="col-12">
                            <label class="text-muted small">Applicable Days</label>
                            <div class="fs-6">
                                @foreach($timeSlot->applicable_days as $day)
                                    <span class="badge bg-secondary me-1">{{ $day }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Description & Notes -->
            @if($timeSlot->description || $timeSlot->notes)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Description & Notes</h5>
                </div>
                <div class="card-body">
                    @if($timeSlot->description)
                        <div class="mb-3">
                            <label class="text-muted small">Description</label>
                            <p class="mb-0">{{ $timeSlot->description }}</p>
                        </div>
                    @endif
                    @if($timeSlot->notes)
                        <div>
                            <label class="text-muted small">Notes</label>
                            <p class="mb-0">{{ $timeSlot->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Usage Statistics -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Usage Statistics</h5>
                </div>
                <div class="card-body text-center">
                    <div class="display-4 text-success mb-2">{{ $timeSlot->timetables->count() }}</div>
                    <p class="text-muted mb-0">Timetable entries using this slot</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('academic.time-slots.edit', $timeSlot) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit Time Slot
                        </a>
                        <a href="{{ route('academic.timetable.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add to Timetable
                        </a>
                        <form action="{{ route('academic.time-slots.destroy', $timeSlot) }}" method="POST"
                              onsubmit="return confirm('Are you sure? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash"></i> Delete Time Slot
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
