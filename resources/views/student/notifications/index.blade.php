@extends('student.layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-bell me-2 text-warning"></i>Notifications</h2>
                    <p class="text-muted mb-0">Stay updated with latest announcements</p>
                </div>
                @if($notifications->where('is_read', false)->count() > 0)
                    <form action="{{ route('student.notifications.read-all') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-check-double me-1"></i>Mark All as Read
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0"><i class="bi bi-list-ul me-2 text-primary"></i>All Notifications</h5>
                        <span class="badge bg-primary">{{ $notifications->total() }} Total</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($notifications as $notification)
                            <div class="list-group-item {{ !$notification->is_read ? 'bg-light bg-opacity-50' : '' }}" style="border-left: 4px solid {{ $notification->type === 'attendance' ? '#dc3545' : ($notification->type === 'timetable' ? '#0d6efd' : '#6c757d') }};">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            @if(!$notification->is_read)
                                                <span class="badge bg-primary me-2">
                                                    <i class="bi bi-star-fill me-1"></i>New
                                                </span>
                                            @endif
                                            <span class="badge bg-{{ $notification->type === 'attendance' ? 'danger' : ($notification->type === 'timetable' ? 'primary' : 'secondary') }} bg-opacity-75">
                                                <i class="bi bi-{{ $notification->type === 'attendance' ? 'calendar-x' : ($notification->type === 'timetable' ? 'calendar-week' : 'bullhorn') }} me-1"></i>
                                                {{ ucfirst($notification->type) }}
                                            </span>
                                        </div>
                                        <h6 class="mb-2 fw-semibold">{{ $notification->message }}</h6>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                            @if($notification->is_read && $notification->read_at)
                                                <span class="mx-2">•</span>
                                                <i class="bi bi-check-circle me-1"></i>Read {{ $notification->read_at->diffForHumans() }}
                                            @endif
                                        </small>
                                    </div>
                                    @if(!$notification->is_read)
                                        <form action="{{ route('student.notifications.read', $notification->id) }}" method="POST" class="ms-2">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-bell-slash text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                                <h5 class="text-muted mt-3 mb-2">No notifications yet</h5>
                                <p class="text-muted mb-0">You're all caught up! Check back later for updates.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                @if($notifications->hasPages())
                    <div class="card-footer bg-white border-0 py-3">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <!-- Notification Stats -->
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-4"><i class="bi bi-graph-pie me-2 text-primary"></i>Notification Stats</h6>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Unread</span>
                            <strong class="text-primary">{{ $notifications->where('is_read', false)->count() }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" 
                                 style="width: {{ $notifications->where('is_read', false)->count() / max($notifications->count(), 1) * 100 }}%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Read</span>
                            <strong class="text-success">{{ $notifications->where('is_read', true)->count() }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" 
                                 style="width: {{ $notifications->where('is_read', true)->count() / max($notifications->count(), 1) * 100 }}%"></div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><i class="bi bi-calendar-check me-1"></i>Attendance Alerts</span>
                        <span class="badge bg-danger">{{ $notifications->where('type', 'attendance')->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><i class="bi bi-calendar-week me-1"></i>Timetable Changes</span>
                        <span class="badge bg-primary">{{ $notifications->where('type', 'timetable')->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-bullhorn me-1"></i>General</span>
                        <span class="badge bg-secondary">{{ $notifications->where('type', 'general')->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Tips -->
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-lightbulb me-2 text-warning"></i>Quick Tips</h6>
                    <ul class="small text-muted mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Check notifications daily for updates
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Mark important notifications as read
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Attendance alerts require immediate attention
                        </li>
                        <li>
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Contact admin for timetable changes
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
