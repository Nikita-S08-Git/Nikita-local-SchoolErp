@extends('student.layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <!-- Change Password Card -->
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 70px; height: 70px; font-size: 2rem;">
                            <i class="bi bi-key"></i>
                        </div>
                        <h4 class="fw-bold mb-1">Change Password</h4>
                        <p class="text-muted">Update your account password</p>
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

                    <!-- Change Password Form -->
                    <form action="{{ route('student.profile.update-password') }}" method="POST">
                        @csrf

                        <!-- Current Password -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label">
                                <i class="bi bi-lock me-1"></i>Current Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                       id="current_password" name="current_password" required
                                       placeholder="Enter current password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                    <i class="bi bi-eye" id="current_password_icon"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="new_password" class="form-label">
                                <i class="bi bi-shield-lock me-1"></i>New Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                       id="new_password" name="new_password" required
                                       placeholder="Enter new password" minlength="8">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                    <i class="bi bi-eye" id="new_password_icon"></i>
                                </button>
                            </div>
                            <small class="text-muted">Minimum 8 characters</small>
                            @error('new_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">
                                <i class="bi bi-shield-check me-1"></i>Confirm New Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control"
                                       id="new_password_confirmation" name="new_password_confirmation" required
                                       placeholder="Confirm new password" minlength="8">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation')">
                                    <i class="bi bi-eye" id="new_password_confirmation_icon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Password Strength Indicator -->
                        <div class="mb-4">
                            <label class="form-label">Password Strength</label>
                            <div class="progress" style="height: 8px;">
                                <div id="password_strength" class="progress-bar" role="progressbar" style="width: 0%;"></div>
                            </div>
                            <small id="strength_text" class="text-muted">Enter password to see strength</small>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Update Password
                            </button>
                            <a href="{{ route('student.profile.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to Profile
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Tips -->
            <div class="card shadow-sm border-0 mt-3" style="border-radius: 15px;">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-2"><i class="bi bi-lightbulb me-2 text-warning"></i>Password Tips</h6>
                    <ul class="mb-0 small text-muted">
                        <li>Minimum 8 characters</li>
                        <li>Use a mix of letters, numbers, and symbols</li>
                        <li>Avoid common words or personal information</li>
                        <li>Don't reuse passwords from other accounts</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
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

// Password strength checker
document.getElementById('new_password').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('password_strength');
    const strengthText = document.getElementById('strength_text');
    
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    const colors = ['#dc3545', '#dc3545', '#ffc107', '#17a2b8', '#28a745', '#28a745'];
    const texts = ['Very Weak', 'Very Weak', 'Weak', 'Medium', 'Strong', 'Very Strong'];
    
    strengthBar.style.width = (strength * 20) + '%';
    strengthBar.className = 'progress-bar';
    strengthBar.style.backgroundColor = colors[strength];
    strengthText.textContent = texts[strength];
    strengthText.className = 'text-' + (strength <= 2 ? 'danger' : strength <= 3 ? 'warning' : 'success');
});

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
