@extends('layouts.app')

@section('title', 'Attendance Reports')

@section('content')
<div class="container-fluid p-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">📊 Attendance Reports</h5>
        </div>
        <div class="card-body">
            <form method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Division *</label>
                        <select name="division_id" class="form-select" required>
                            <option value="">Select Division</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                    {{ $division->division_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">From Date *</label>
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">To Date *</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">Generate Report</button>
                    </div>
                </div>
            </form>

            @if(isset($report))
            <hr>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6>Attendance Report - {{ $division->division_name }}</h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('reports.attendance.pdf', request()->all()) }}" class="btn btn-danger btn-sm" target="_blank">📄 PDF</a>
                    <a href="{{ route('reports.attendance.excel', request()->all()) }}" class="btn btn-success btn-sm">📊 Excel</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Roll No</th>
                            <th>Student Name</th>
                            <th>Total Days</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report as $row)
                        <tr>
                            <td>{{ $row['student']->roll_number }}</td>
                            <td>{{ $row['student']->first_name }} {{ $row['student']->last_name }}</td>
                            <td>{{ $row['total'] }}</td>
                            <td><span class="badge bg-success">{{ $row['present'] }}</span></td>
                            <td><span class="badge bg-danger">{{ $row['absent'] }}</span></td>
                            <td>
                                <strong class="{{ $row['percentage'] >= 75 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($row['percentage'], 2) }}%
                                </strong>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
