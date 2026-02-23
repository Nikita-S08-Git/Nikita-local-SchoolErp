@extends('layouts.app')

@section('page-title', 'Attendance Report')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-check me-2"></i>Attendance Report - {{ $assignedDivision->division_name }}
                    </h5>
                    <a href="{{ route('teacher.dashboard') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                    </a>
                </div>
                <div class="card-body">
                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('teacher.attendance') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="date" name="date" class="form-control" value="{{ $date }}">
                                    <button class="btn btn-outline-primary" type="submit">
                                        <i class="bi bi-search"></i> View
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <a href="{{ route('teacher.attendance', ['date' => today()->format('Y-m-d')]) }}" class="btn btn-secondary">
                                    <i class="bi bi-calendar-today me-1"></i>Today
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $summary['total'] }}</h3>
                                    <small>Total Students</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $summary['present'] }}</h3>
                                    <small>Present</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $summary['absent'] }}</h3>
                                    <small>Absent</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Details -->
                    @if($attendanceData->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Roll No.</th>
                                        <th>Student Name</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendanceData as $attendance)
                                        <tr>
                                            <td><strong>{{ $attendance->student->roll_number }}</strong></td>
                                            <td>{{ $attendance->student->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $attendance->status == 'Present' ? 'success' : 'danger' }}">
                                                    {{ $attendance->status }}
                                                </span>
                                            </td>
                                            <td>{{ $attendance->remarks ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Attendance Percentage -->
                        @if($summary['total'] > 0)
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6>Attendance Percentage for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h6>
                                            @php
                                                $percentage = round(($summary['present'] / $summary['total']) * 100, 2);
                                            @endphp
                                            <div class="progress" style="height: 25px;">
                                                <div class="progress-bar bg-{{ $percentage >= 75 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger') }}" 
                                                     role="progressbar" style="width: {{ $percentage }}%">
                                                    {{ $percentage }}%
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            No attendance records found for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection