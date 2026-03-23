@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-people"></i> Library Students</h2>
        <a href="{{ route('dashboard.librarian') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if($students->isEmpty())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No active students found.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Program</th>
                                <th>Division</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr>
                                <td>{{ $student->id }}</td>
                                <td>{{ $student->user->name ?? 'N/A' }}</td>
                                <td>{{ $student->user->email ?? 'N/A' }}</td>
                                <td>{{ $student->division->program->name ?? 'N/A' }}</td>
                                <td>{{ $student->division->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-success">{{ ucfirst($student->student_status) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{ $students->links() }}
            @endif
        </div>
    </div>
</div>
@endsection
