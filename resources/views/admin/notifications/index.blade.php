@extends('layouts.app')

@section('title', 'Notifications Management')
@section('page-title', 'Notifications Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-bell me-2 text-primary"></i>Notifications & Announcements</h2>
                    <p class="text-muted mb-0">Send important notes to students, teachers, and staff</p>
                </div>
                <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Create Notification
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Notifications</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="60">Type</th>
                            <th>Title</th>
                            <th>Audience</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Valid Until</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notification)
                        <tr>
                            <td>
                                <span class="badge bg-{{ $notification->badge_color }}">
                                    <i class="bi {{ $notification->icon }}"></i>
                                </span>
                            </td>
                            <td>
                                <strong>{{ $notification->title }}</strong>
                                <br><small class="text-muted">{{ Str::limit($notification->message, 60) }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($notification->audience) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $notification->priority === 'high' ? 'danger' : ($notification->priority === 'medium' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($notification->priority) }}
                                </span>
                            </td>
                            <td>
                                @if($notification->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                {{ $notification->created_at->format('d M Y') }}
                                <br><small class="text-muted">{{ $notification->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                @if($notification->expires_at)
                                    {{ $notification->expires_at->format('d M Y') }}
                                @else
                                    <span class="text-muted">No expiry</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.notifications.show', $notification) }}" 
                                       class="btn btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.notifications.edit', $notification) }}" 
                                       class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.notifications.toggle', $notification) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-{{ $notification->is_active ? 'secondary' : 'success' }}" 
                                                title="{{ $notification->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $notification->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.notifications.destroy', $notification) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this notification?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-bell-slash fa-3x mb-3"></i>
                                    <h5>No notifications yet</h5>
                                    <p>Create your first notification to get started.</p>
                                    <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Create Notification
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($notifications->hasPages())
            <div class="card-footer bg-light">
                {{ $notifications->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
