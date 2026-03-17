@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="bi bi-activity me-2"></i>Activity Logs</h2>
            <p class="text-muted">Track user activities and system actions</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="user_id" class="form-label">User</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="action" class="form-label">Action</label>
                    <select name="action" id="action" class="form-select">
                        <option value="">All Actions</option>
                        <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                        <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                        <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                        <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                        <option value="view" {{ request('action') == 'view' ? 'selected' : '' }}>View</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="from_date" class="form-label">From Date</label>
                    <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label for="to_date" class="form-label">To Date</label>
                    <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
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
                            <th>Date & Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Module</th>
                            <th>Description</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                            <td>{{ $log->user->name ?? 'Unknown' }}</td>
                            <td>
                                @switch($log->action)
                                    @case('login')
                                        <span class="badge bg-success">{{ ucfirst($log->action) }}</span>
                                        @break
                                    @case('create')
                                        <span class="badge bg-primary">{{ ucfirst($log->action) }}</span>
                                        @break
                                    @case('update')
                                        <span class="badge bg-warning">{{ ucfirst($log->action) }}</span>
                                        @break
                                    @case('delete')
                                        <span class="badge bg-danger">{{ ucfirst($log->action) }}</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ ucfirst($log->action) }}</span>
                                @endswitch
                            </td>
                            <td>{{ $log->module }}</td>
                            <td>{{ Str::limit($log->description, 50) }}</td>
                            <td>{{ $log->ip_address ?? 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No activity logs found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
