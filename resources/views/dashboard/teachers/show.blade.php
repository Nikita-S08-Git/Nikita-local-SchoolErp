@extends('layouts.app')

@section('page-title', 'Teacher Details')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-badge me-2"></i>Teacher Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Name:</th>
                                    <td>{{ $teacher->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $teacher->email }}</td>
                                </tr>
                                <tr>
                                    <th>Role:</th>
                                    <td><span class="badge bg-primary">{{ ucfirst($teacher->roles->first()->name ?? 'Teacher') }}</span></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td><span class="badge bg-success">Active</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Assigned Division:</th>
                                    <td>
                                        @if($teacher->assignedDivision)
                                            <span class="badge bg-secondary">
                                                {{ $teacher->assignedDivision->academicYear->name ?? 'N/A' }} - {{ $teacher->assignedDivision->division_name }}
                                            </span>
                                        @else
                                            <span class="text-muted">No division assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Joined:</th>
                                    <td>{{ $teacher->created_at->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $teacher->updated_at->format('d M Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <a href="{{ route('dashboard.teachers.edit', $teacher) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i>Edit Teacher
                        </a>
                        <a href="{{ route('dashboard.teachers.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to List
                        </a>
                        <form method="POST" action="{{ route('dashboard.teachers.destroy', $teacher) }}" class="d-inline" 
                              onsubmit="return confirm('Are you sure you want to delete this teacher?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-1"></i>Delete Teacher
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection