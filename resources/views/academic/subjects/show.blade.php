@extends('layouts.app')

@section('title', 'Subject Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">
                        <i class="bi bi-book me-2 text-primary"></i> 
                        {{ $subject->name }}
                    </h3>
                    <p class="text-muted mb-0">Subject Details</p>
                </div>
                <div>
                    <a href="{{ route('academic.subjects.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('academic.subjects.edit', $subject->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Subject Details Card -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Subject Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Subject Code:</th>
                            <td><span class="badge bg-primary">{{ $subject->code }}</span></td>
                        </tr>
                        <tr>
                            <th>Subject Name:</th>
                            <td>{{ $subject->name }}</td>
                        </tr>
                        <tr>
                            <th>Program:</th>
                            <td>{{ $subject->program->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Academic Year:</th>
                            <td>{{ $subject->academicYear->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Semester:</th>
                            <td>
                                @if($subject->semester)
                                    <span class="badge bg-info">Semester {{ $subject->semester }}</span>
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Credits:</th>
                            <td>
                                @if($subject->credit)
                                    <span class="badge bg-success">{{ $subject->credit }} Credits</span>
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Type:</th>
                            <td>
                                @if($subject->type)
                                    <span class="badge bg-{{ $subject->type === 'theory' ? 'primary' : ($subject->type === 'practical' ? 'success' : 'warning') }}">
                                        {{ ucfirst($subject->type) }}
                                    </span>
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Max Marks:</th>
                            <td>{{ $subject->max_marks ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Passing Marks:</th>
                            <td>{{ $subject->passing_marks ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($subject->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $subject->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>{{ $subject->updated_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('academic.subjects.edit', $subject->id) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil"></i> Edit Subject
                        </a>
                        <a href="{{ route('academic.subjects.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-list"></i> View All Subjects
                        </a>
                        <hr>
                        <a href="{{ route('examinations.index') }}" class="btn btn-outline-info">
                            <i class="bi bi-clipboard-check"></i> View Examinations
                        </a>
                        <a href="{{ route('academic.timetable.index') }}" class="btn btn-outline-warning">
                            <i class="bi bi-calendar-week"></i> View Timetables
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h2 class="text-primary">{{ $subject->studentsCount ?? 0 }}</h2>
                        <p class="text-muted mb-0">Enrolled Students</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
