@extends('layouts.app')

@section('title', 'Marks Entry')

@section('content')
<div class="container-fluid p-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">✏️ Marks Entry - {{ $examination->name }}</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-5">
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
                    <div class="col-md-5">
                        <label class="form-label">Subject *</label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->subject_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">Load Students</button>
                    </div>
                </div>
            </form>

            @if(count($students) > 0)
            <form action="{{ route('examinations.save-marks', $examination) }}" method="POST">
                @csrf
                <input type="hidden" name="division_id" value="{{ request('division_id') }}">
                <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">
                <input type="hidden" name="max_marks" value="100">
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Roll No</th>
                                <th>Student Name</th>
                                <th>Marks Obtained (Max: 100)</th>
                                <th>Grade</th>
                                <th>Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr>
                                <td>{{ $student->roll_number }}</td>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>
                                    <input type="number" name="marks[{{ $student->id }}]" 
                                           class="form-control" min="0" max="100" step="0.01"
                                           value="{{ $marks[$student->id]->marks_obtained ?? '' }}">
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $marks[$student->id]->grade ?? '-' }}</span>
                                </td>
                                <td>
                                    @if(isset($marks[$student->id]))
                                        <span class="badge bg-{{ $marks[$student->id]->result == 'pass' ? 'success' : 'danger' }}">
                                            {{ ucfirst($marks[$student->id]->result) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">💾 Save Marks</button>
                    <a href="{{ route('examinations.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </form>
            @else
                <div class="alert alert-info">Please select division and subject to load students.</div>
            @endif
        </div>
    </div>
</div>
@endsection
