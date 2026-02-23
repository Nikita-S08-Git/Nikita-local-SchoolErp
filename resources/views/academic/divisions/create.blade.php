@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Create Division</h2>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('academic.divisions.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Program *</label>
                        <select name="program_id" class="form-select @error('program_id') is-invalid @enderror" required>
                            <option value="">Select Program</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ $program->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('program_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Academic Session *</label>
                        <select name="session_id" class="form-select @error('session_id') is-invalid @enderror" required>
                            <option value="">Select Session</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->id }}" {{ old('session_id') == $session->id ? 'selected' : '' }}>
                                    {{ $session->session_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('session_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Division Name *</label>
                        <input type="text" name="division_name" class="form-control @error('division_name') is-invalid @enderror" 
                               value="{{ old('division_name') }}" placeholder="e.g., FY-A, SY-B" required>
                        @error('division_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Maximum Capacity *</label>
                        <input type="number" name="max_students" class="form-control @error('max_students') is-invalid @enderror" 
                               value="{{ old('max_students', 60) }}" min="1" max="200" required>
                        @error('max_students')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Class Teacher</label>
                        <select name="class_teacher_id" class="form-select @error('class_teacher_id') is-invalid @enderror">
                            <option value="">Select Teacher</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('class_teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_teacher_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Classroom</label>
                        <input type="text" name="classroom" class="form-control @error('classroom') is-invalid @enderror" 
                               value="{{ old('classroom') }}" placeholder="e.g., Room 101">
                        @error('classroom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create Division</button>
                    <a href="{{ route('academic.divisions.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
