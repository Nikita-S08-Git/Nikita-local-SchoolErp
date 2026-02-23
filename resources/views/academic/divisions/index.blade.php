@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Division Management</h2>
        <a href="{{ route('academic.divisions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create Division
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="program_id" class="form-select">
                        <option value="">All Programs</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                {{ $program->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="session_id" class="form-select">
                        <option value="">All Sessions</option>
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" {{ request('session_id') == $session->id ? 'selected' : '' }}>
                                {{ $session->session_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Division</th>
                            <th>Program</th>
                            <th>Session</th>
                            <th>Class Teacher</th>
                            <th>Capacity</th>
                            <th>Utilization</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($divisions as $division)
                            <tr>
                                <td><strong>{{ $division->division_name }}</strong></td>
                                <td>{{ $division->program->name }}</td>
                                <td>{{ $division->session->session_name }}</td>
                                <td>{{ $division->classTeacher->name ?? 'Not Assigned' }}</td>
                                <td>
                                    <span class="badge bg-{{ $division->capacity_status }}">
                                        {{ $division->students_count }}/{{ $division->max_students }}
                                    </span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $division->capacity_status }}" 
                                             style="width: {{ $division->capacity_percentage }}%">
                                            {{ round($division->capacity_percentage) }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $division->is_active ? 'success' : 'secondary' }}">
                                        {{ $division->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('academic.divisions.show', $division) }}" class="btn btn-sm btn-info">View</a>
                                    <a href="{{ route('academic.divisions.edit', $division) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('academic.divisions.destroy', $division) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this division?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center">No divisions found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $divisions->links() }}
        </div>
    </div>
</div>
@endsection
