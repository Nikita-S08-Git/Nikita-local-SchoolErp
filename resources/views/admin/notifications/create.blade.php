@extends('layouts.app')

@section('title', 'Create Notification')
@section('page-title', 'Create Notification')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.notifications.index') }}">Notifications</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </nav>
                    <h2 class="mb-1"><i class="fas fa-plus-circle me-2 text-primary"></i>Create Notification</h2>
                </div>
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Notification Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.notifications.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" placeholder="e.g., Important Note: Classes start at 9:00 AM" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control @error('message') is-invalid @enderror" 
                                      rows="5" placeholder="Enter detailed message..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="">Select Type</option>
                                    <option value="general" {{ old('type') === 'general' ? 'selected' : '' }}>General</option>
                                    <option value="urgent" {{ old('type') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    <option value="holiday" {{ old('type') === 'holiday' ? 'selected' : '' }}>Holiday</option>
                                    <option value="exam" {{ old('type') === 'exam' ? 'selected' : '' }}>Exam</option>
                                    <option value="fee" {{ old('type') === 'fee' ? 'selected' : '' }}>Fee</option>
                                    <option value="timetable" {{ old('type') === 'timetable' ? 'selected' : '' }}>Timetable</option>
                                    <option value="attendance" {{ old('type') === 'attendance' ? 'selected' : '' }}>Attendance</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Priority <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                    <option value="">Select Priority</option>
                                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Audience <span class="text-danger">*</span></label>
                                <select name="audience" class="form-select @error('audience') is-invalid @enderror" required>
                                    <option value="">Select Audience</option>
                                    <option value="all" {{ old('audience') === 'all' ? 'selected' : '' }}>All Users</option>
                                    <option value="students" {{ old('audience') === 'students' ? 'selected' : '' }}>Students Only</option>
                                    <option value="teachers" {{ old('audience') === 'teachers' ? 'selected' : '' }}>Teachers Only</option>
                                    <option value="staff" {{ old('audience') === 'staff' ? 'selected' : '' }}>Staff Only</option>
                                    <option value="parents" {{ old('audience') === 'parents' ? 'selected' : '' }}>Parents Only</option>
                                </select>
                                @error('audience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Who should see this notification?</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Target Users (Optional)</label>
                                <select name="target_users[]" class="form-select" multiple>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ in_array($user->id, old('target_users', [])) ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple users. Leave empty for all audience.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Publish At</label>
                                <input type="datetime-local" name="publish_at" class="form-control @error('publish_at') is-invalid @enderror" 
                                       value="{{ old('publish_at') }}">
                                @error('publish_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Leave empty to publish immediately</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Expires At</label>
                                <input type="datetime-local" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror" 
                                       value="{{ old('expires_at') }}">
                                @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Leave empty for no expiry</small>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i> Send Notification
                            </button>
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
