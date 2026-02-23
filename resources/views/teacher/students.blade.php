@extends('layouts.app')

@section('page-title', 'My Students')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-people me-2"></i>My Students - {{ $assignedDivision->division_name }}
                    </h5>
                    <a href="{{ route('teacher.dashboard') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('teacher.students') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search by name, roll number, or email..." 
                                           value="{{ request('search') }}">
                                    <button class="btn btn-outline-primary" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if(request('search'))
                                    <a href="{{ route('teacher.students') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Clear Search
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    <!-- Students Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Roll No.</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Admission Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr>
                                        <td><strong>{{ $student->roll_number }}</strong></td>
                                        <td>{{ $student->name }}</td>
                                        <td>{{ $student->email }}</td>
                                        <td>{{ $student->phone ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $student->student_status == 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($student->student_status) }}
                                            </span>
                                        </td>
                                        <td>{{ $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('d M Y') : 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            @if(request('search'))
                                                No students found matching your search.
                                            @else
                                                No students assigned to your division.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($students->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $students->appends(request()->query())->links() }}
                        </div>
                    @endif

                    <!-- Summary -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Showing {{ $students->count() }} of {{ $students->total() }} students in {{ $assignedDivision->division_name }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection