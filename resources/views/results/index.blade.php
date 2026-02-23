@extends('layouts.app')

@section('title', 'Results')
@section('page-title', 'Results')

@section('content')
<div class="container-fluid p-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-trophy me-2"></i>Examination Results</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6>View Student Result</h6>
                            <form action="{{ route('results.student', ['student' => 0]) }}" method="GET">
                                <div class="input-group">
                                    <select name="student" class="form-select" required>
                                        <option value="">Select Student</option>
                                        @foreach($students as $student)
                                        <option value="{{ $student->id }}">{{ $student->user->name }} ({{ $student->roll_number }})</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-primary">View</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6>View Division Results</h6>
                            <form action="{{ route('results.division', ['division' => 0]) }}" method="GET">
                                <div class="input-group">
                                    <select name="division" class="form-select" required>
                                        <option value="">Select Division</option>
                                        @foreach($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-primary">View</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
