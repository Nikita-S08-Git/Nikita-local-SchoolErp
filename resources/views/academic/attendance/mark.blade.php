@extends('layouts.app')

@section('title', 'Mark Attendance')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1"><i class="bi bi-check-square me-2 text-primary"></i> Mark Attendance</h3>
                    <p class="text-muted mb-0">
                        Division: <strong>{{ $division->division_name }}</strong> | 
                        Date: <strong>{{ \Carbon\Carbon::parse($validated['date'])->format('d M Y') }}</strong>
                    </p>
                </div>
                <a href="{{ route('academic.attendance.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    @if($students->count() > 0)
        <form action="{{ route('academic.attendance.store') }}" method="POST">
            @csrf
            <input type="hidden" name="division_id" value="{{ $validated['division_id'] }}">
            <input type="hidden" name="academic_session_id" value="{{ $validated['academic_session_id'] }}">
            <input type="hidden" name="date" value="{{ $validated['date'] }}">

            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-people me-2"></i>Student List ({{ $students->count() }} students)</h6>
                    <div>
                        <button type="button" class="btn btn-sm btn-success me-2" onclick="markAll('present')">
                            <i class="bi bi-check-all"></i> Mark All Present
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="markAll('absent')">
                            <i class="bi bi-x-circle"></i> Mark All Absent
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Roll No.</th>
                                    <th>Student Name</th>
                                    <th style="width: 200px;" class="text-center">Attendance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $index => $student)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $student->roll_number }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($student->photo_path)
                                                    <img src="{{ asset('storage/' . $student->photo_path) }}" 
                                                         class="rounded-circle me-2" width="32" height="32" alt="Photo">
                                                @else
                                                    <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 32px; height: 32px;">
                                                        <i class="bi bi-person text-white"></i>
                                                    </div>
                                                @endif
                                                <strong>{{ $student->full_name }}</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <input type="hidden" name="students[{{ $index }}][student_id]" value="{{ $student->id }}">

                                                <input type="radio" class="btn-check"
                                                       name="students[{{ $index }}][status]"
                                                       id="present_{{ $student->id }}"
                                                       value="Present"
                                                       {{ ($existingAttendance[$student->id] ?? '') == 'Present' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success btn-sm" for="present_{{ $student->id }}">
                                                    <i class="bi bi-check-circle"></i> Present
                                                </label>

                                                <input type="radio" class="btn-check"
                                                       name="students[{{ $index }}][status]"
                                                       id="absent_{{ $student->id }}"
                                                       value="Absent"
                                                       {{ ($existingAttendance[$student->id] ?? '') == 'Absent' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-danger btn-sm" for="absent_{{ $student->id }}">
                                                    <i class="bi bi-x-circle"></i> Absent
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light text-center">
                    <button type="submit" class="btn btn-primary btn-lg me-3">
                        <i class="bi bi-save"></i> Save Attendance
                    </button>
                    <a href="{{ route('academic.attendance.index') }}" class="btn btn-secondary btn-lg">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </div>
        </form>
    @else
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-people fs-1 text-muted mb-3 d-block"></i>
                <h5>No Students Found</h5>
                <p class="text-muted">No active students found in this division.</p>
                <a href="{{ route('academic.attendance.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Go Back
                </a>
            </div>
        </div>
    @endif
</div>

<script>
function markAll(status) {
    const capitalizedStatus = status.charAt(0).toUpperCase() + status.slice(1);
    const radios = document.querySelectorAll(`input[type="radio"][value="${capitalizedStatus}"]`);
    radios.forEach(radio => {
        radio.checked = true;
    });
}
</script>
@endsection