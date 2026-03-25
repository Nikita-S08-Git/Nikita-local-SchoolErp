@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-person-circle me-2 text-primary"></i>My Profile</h2>
                    <p class="text-muted mb-0">View and manage your accountant profile</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('dashboard.accountant') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        <!-- Profile Card -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 120px; height: 120px; font-size: 3rem;">
                        <i class="bi bi-calculator"></i>
                    </div>

                    <h4 class="mb-1 fw-bold">{{ auth()->user()->name }}</h4>
                    <p class="text-muted mb-3">{{ auth()->user()->email }}</p>

                    <div class="mb-3">
                        <span class="badge bg-primary">
                            <i class="bi bi-shield-check me-1"></i>Accountant
                        </span>
                        <span class="badge bg-success">
                            <i class="bi bi-check-circle me-1"></i>Active
                        </span>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="#change-password" class="btn btn-outline-primary" data-bs-toggle="collapse">
                            <i class="bi bi-key me-1"></i>Change Password
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow-sm border-0 mt-3" style="border-radius: 15px;">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3"><i class="bi bi-graph-up me-2"></i>Quick Stats</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <small class="text-muted">Today's Collection</small>
                        <span class="fw-bold text-success">₹25,000</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <small class="text-muted">Pending Fees</small>
                        <span class="fw-bold text-warning">88</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Monthly Target</small>
                        <span class="fw-bold text-primary">₹5,00,000</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-person-badge me-2"></i>Profile Information</h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Name</label>
                            <p class="fw-semibold mb-0">{{ auth()->user()->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Email</label>
                            <p class="fw-semibold mb-0">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Role</label>
                            <p class="fw-semibold mb-0">Accountant</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Member Since</label>
                            <p class="fw-semibold mb-0">{{ auth()->user()->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password Section -->
            <div class="card shadow-sm border-0 mt-4 collapse" id="change-password" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-key me-2"></i>Change Password</h5>

                    <form action="{{ route('accountant.change-password') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="current_password" class="form-label">
                                <i class="bi bi-lock me-1"></i>Current Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                       id="current_password" name="current_password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                    <i class="bi bi-eye" id="current_password_icon"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">
                                <i class="bi bi-shield-lock me-1"></i>New Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                       id="new_password" name="new_password" required minlength="8">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                    <i class="bi bi-eye" id="new_password_icon"></i>
                                </button>
                            </div>
                            <small class="text-muted">Minimum 8 characters</small>
                            @error('new_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label">
                                <i class="bi bi-shield-check me-1"></i>Confirm Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control"
                                       id="new_password_confirmation" name="new_password_confirmation" required minlength="8">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation')">
                                    <i class="bi bi-eye" id="new_password_confirmation_icon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Password
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="collapse">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Validate password match
document.getElementById('new_password_confirmation').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword && newPassword !== confirmPassword) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});
</script>
@endsection
