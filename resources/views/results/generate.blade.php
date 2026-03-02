@extends('layouts.app')

@section('title', 'Generate Result Card')

@section('content')
<div class="container-fluid p-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">📊 Generate Result Card</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('results.generate') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Examination *</label>
                        <select name="examination_id" class="form-select" required>
                            <option value="">Select Examination</option>
                            @foreach($examinations as $exam)
                                <option value="{{ $exam->id }}" {{ request('examination_id') == $exam->id ? 'selected' : '' }}>
                                    {{ $exam->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
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
                    <div class="col-md-4 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">Generate Results</button>
                    </div>
                </div>
            </form>

            @if(isset($results) && count($results) > 0)
            <hr>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6>Results for {{ $examination->name }} - {{ $division->division_name }}</h6>
                <a href="{{ route('results.pdf', ['examination_id' => request('examination_id'), 'division_id' => request('division_id')]) }}" 
                   class="btn btn-danger btn-sm" target="_blank">📄 Download PDF</a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Roll No</th>
                            <th>Student Name</th>
                            @foreach($subjects as $subject)
                                <th>{{ $subject->subject_name }}</th>
                            @endforeach
                            <th>Total</th>
                            <th>Percentage</th>
                            <th>Grade</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                        <tr>
                            <td>{{ $result['student']->roll_number }}</td>
                            <td>{{ $result['student']->first_name }} {{ $result['student']->last_name }}</td>
                            @foreach($subjects as $subject)
                                <td>{{ $result['marks'][$subject->id] ?? '-' }}</td>
                            @endforeach
                            <td><strong>{{ $result['total'] }}</strong></td>
                            <td><strong>{{ number_format($result['percentage'], 2) }}%</strong></td>
                            <td><span class="badge bg-info">{{ $result['grade'] }}</span></td>
                            <td>
                                <span class="badge bg-{{ $result['result'] == 'Pass' ? 'success' : 'danger' }}">
                                    {{ $result['result'] }}
                                </span>
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
