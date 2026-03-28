@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="content-area">
    <div class="container-fluid px-4 py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Settings Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="card shadow-sm" style="border-radius: 14px; border: none;">
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="#account-settings" class="list-group-item list-group-item-action active" 
                               data-bs-toggle="list" role="tab">
                                <i class="fas fa-user-gear me-2"></i> Account Settings
                            </a>
                            <a href="#contact-settings" class="list-group-item list-group-item-action" 
                               data-bs-toggle="list" role="tab">
                                <i class="fas fa-address-book me-2"></i> Contact Information
                            </a>
                            <a href="#notification-settings" class="list-group-item list-group-item-action" 
                               data-bs-toggle="list" role="tab">
                                <i class="fas fa-bell me-2"></i> Notifications
                            </a>
                            <a href="#privacy-settings" class="list-group-item list-group-item-action" 
                               data-bs-toggle="list" role="tab">
                                <i class="fas fa-shield-halved me-2"></i> Privacy
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="col-lg-9">
                <div class="tab-content">
                    <!-- Account Settings -->
                    <div class="tab-pane fade show active" id="account-settings" role="tabpanel">
                        <div class="card shadow-sm" style="border-radius: 14px; border: none;">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0"><i class="fas fa-user-gear me-2 text-primary"></i>Account Settings</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('teacher.settings.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-user me-1 text-muted"></i> Name
                                                </label>
                                                <input type="text" class="form-control" value="{{ $teacher->name }}" disabled>
                                                <small class="text-muted">Contact admin to change your name</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-envelope me-1 text-muted"></i> Email Address
                                                </label>
                                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                                       value="{{ old('email', $teacher->email) }}">
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-id-badge me-1 text-muted"></i> Employee ID
                                                </label>
                                                <input type="text" class="form-control" value="{{ $teacherProfile->employee_id ?? 'N/A' }}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-briefcase me-1 text-muted"></i> Designation
                                                </label>
                                                <input type="text" class="form-control" value="{{ $teacherProfile->designation ?? 'N/A' }}" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Update Account
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Settings -->
                    <div class="tab-pane fade" id="contact-settings" role="tabpanel">
                        <div class="card shadow-sm" style="border-radius: 14px; border: none;">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0"><i class="fas fa-address-book me-2 text-primary"></i>Contact Information</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('teacher.settings.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <h6 class="fw-bold mb-3 text-primary">
                                        <i class="fas fa-phone me-2"></i>Phone Numbers
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Primary Phone</label>
                                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                                       value="{{ old('phone', $teacherProfile->phone) }}"
                                                       placeholder="Enter primary phone number">
                                                @error('phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Alternate Phone</label>
                                                <input type="text" name="alternate_phone" class="form-control @error('alternate_phone') is-invalid @enderror" 
                                                       value="{{ old('alternate_phone', $teacherProfile->alternate_phone) }}"
                                                       placeholder="Enter alternate phone number">
                                                @error('alternate_phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold mb-3 text-primary mt-4">
                                        <i class="fas fa-map-marker-alt me-2"></i>Address
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Current Address</label>
                                                <textarea name="current_address" class="form-control @error('current_address') is-invalid @enderror" rows="3">{{ old('current_address', $teacherProfile->current_address) }}</textarea>
                                                @error('current_address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Permanent Address</label>
                                                <textarea name="permanent_address" class="form-control @error('permanent_address') is-invalid @enderror" rows="3">{{ old('permanent_address', $teacherProfile->permanent_address) }}</textarea>
                                                @error('permanent_address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">City</label>
                                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" 
                                                       value="{{ old('city', $teacherProfile->city) }}">
                                                @error('city')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">State</label>
                                                <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" 
                                                       value="{{ old('state', $teacherProfile->state) }}">
                                                @error('state')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Pincode</label>
                                                <input type="text" name="pincode" class="form-control @error('pincode') is-invalid @enderror" 
                                                       value="{{ old('pincode', $teacherProfile->pincode) }}">
                                                @error('pincode')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Update Contact
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Settings -->
                    <div class="tab-pane fade" id="notification-settings" role="tabpanel">
                        <div class="card shadow-sm" style="border-radius: 14px; border: none;">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0"><i class="fas fa-bell me-2 text-primary"></i>Notification Preferences</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('teacher.settings.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-4">
                                        <h6 class="fw-bold mb-3">
                                            <i class="fas fa-envelope me-2"></i>Email Notifications
                                        </h6>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="notification_email" 
                                                   id="notification_email" {{ old('notification_email', $teacherProfile->notification_email ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="notification_email">
                                                <div>
                                                    <strong>Enable Email Notifications</strong>
                                                    <p class="text-muted mb-0 small">Receive important updates, announcements, and reminders via email</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="fw-bold mb-3">
                                            <i class="fas fa-sms me-2"></i>SMS Notifications
                                        </h6>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="notification_sms" 
                                                   id="notification_sms" {{ old('notification_sms', $teacherProfile->notification_sms ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="notification_sms">
                                                <div>
                                                    <strong>Enable SMS Notifications</strong>
                                                    <p class="text-muted mb-0 small">Receive urgent alerts and reminders via SMS</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Note:</strong> You will always receive critical system notifications regardless of your preferences.
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Save Preferences
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Privacy Settings -->
                    <div class="tab-pane fade" id="privacy-settings" role="tabpanel">
                        <div class="card shadow-sm" style="border-radius: 14px; border: none;">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0"><i class="fas fa-shield-halved me-2 text-primary"></i>Privacy Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3">
                                        <i class="fab fa-linkedin me-2"></i>Social Media
                                    </h6>
                                    <form action="{{ route('teacher.settings.update') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-3">
                                            <label class="form-label">LinkedIn Profile URL</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fab fa-linkedin"></i></span>
                                                <input type="url" name="linkedin_url" class="form-control @error('linkedin_url') is-invalid @enderror" 
                                                       value="{{ old('linkedin_url', $teacherProfile->linkedin_url) }}"
                                                       placeholder="https://linkedin.com/in/yourprofile">
                                                @error('linkedin_url')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-1"></i> Update Social Media
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <hr>

                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3 text-danger">
                                        <i class="fas fa-triangle-exclamation me-2"></i>Danger Zone
                                    </h6>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Warning:</strong> These actions are irreversible. Please proceed with caution.
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card border-warning">
                                                <div class="card-body">
                                                    <h6 class="card-title">Change Password</h6>
                                                    <p class="card-text small text-muted">Update your account password</p>
                                                    <a href="{{ route('password.request') }}" class="btn btn-outline-warning btn-sm">
                                                        <i class="fas fa-key me-1"></i> Change Password
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-danger">
                                                <div class="card-body">
                                                    <h6 class="card-title">Delete Account</h6>
                                                    <p class="card-text small text-muted">Permanently delete your account</p>
                                                    <button class="btn btn-outline-danger btn-sm" disabled>
                                                        <i class="fas fa-user-slash me-1"></i> Contact Admin
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Handle tab switching and preserve form method
    document.querySelectorAll('[data-bs-toggle="list"]').forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function (event) {
            // Update URL hash without scrolling
            history.pushState(null, null, event.target.getAttribute('href'));
        });
    });

    // Check for hash on page load
    document.addEventListener('DOMContentLoaded', function() {
        const hash = window.location.hash;
        if (hash) {
            const tabTrigger = document.querySelector(`[data-bs-toggle="list"][href="${hash}"]`);
            if (tabTrigger) {
                const tab = new bootstrap.Tab(tabTrigger);
                tab.show();
            }
        }
    });
</script>
@endpush
@endsection
