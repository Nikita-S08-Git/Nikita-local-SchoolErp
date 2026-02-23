@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Division: {{ $division->division_name }}</h2>
        <div>
            <a href="{{ route('academic.divisions.edit', $division) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('academic.divisions.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Program</h6>
                    <h5>{{ $division->program->name }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Session</h6>
                    <h5>{{ $division->session->session_name }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Class Teacher</h6>
                    <h5>{{ $division->classTeacher->name ?? 'Not Assigned' }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Capacity</h6>
                    <h5>
                        <span class="badge bg-{{ $division->capacity_status }} fs-6">
                            {{ $division->current_count }}/{{ $division->max_students }}
                        </span>
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Assigned Students ({{ $division->students->count() }})</h5>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignModal">
                <i class="bi bi-plus"></i> Assign Students
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Roll No</th>
                            <th>Admission No</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($division->students as $student)
                            <tr>
                                <td>{{ $student->roll_number }}</td>
                                <td>{{ $student->admission_number }}</td>
                                <td>{{ $student->full_name }}</td>
                                <td>{{ $student->mobile_number }}</td>
                                <td>{{ $student->email }}</td>
                                <td>
                                    <form action="{{ route('academic.divisions.remove-student', [$division, $student]) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Remove student?')">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">No students assigned</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Assign Students Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Students</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('academic.divisions.assign-students', $division) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        Available Seats: <strong>{{ $division->available_seats }}</strong>
                    </div>
                    <div id="unassignedStudents">Loading...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('assignModal');
    modal.addEventListener('show.bs.modal', function() {
        fetch('/academic/divisions/unassigned-students?program_id={{ $division->program_id }}')
            .then(res => res.json())
            .then(students => {
                const html = students.length ? students.map(s => `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="student_ids[]" value="${s.id}" id="student${s.id}">
                        <label class="form-check-label" for="student${s.id}">
                            ${s.admission_number} - ${s.first_name} ${s.last_name}
                        </label>
                    </div>
                `).join('') : '<p class="text-muted">No unassigned students found</p>';
                document.getElementById('unassignedStudents').innerHTML = html;
            });
    });
});
</script>
@endsection
