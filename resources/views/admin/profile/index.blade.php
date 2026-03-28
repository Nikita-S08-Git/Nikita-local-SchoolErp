@extends('layouts.app')

@section('title', 'Admin Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-user-shield me-2 text-primary"></i>Admin Profile</h2>
                    <p class="text-muted mb-0">View and manage your profile information</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Edit Profile
                    </a>
                    <a href="{{ route('admin.profile.edit-password') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-key me-1"></i> Change Password
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body text-center p-4">
                    @if($admin->photo_path)
                        <img src="{{ asset('storage/' . $admin->photo_path) }}" 
                             alt="{{ $admin->name }}"
                             class="rounded-circle mb-3"
                             style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #667eea;">
                    @else
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 150px; height: 150px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <span class="text-white fw-bold" style="font-size: 4rem;">
                                {{ strtoupper(substr($admin->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    
                    <h4 class="fw-bold mb-1">{{ $admin->name }}</h4>
                    <p class="text-muted mb-2">{{ $admin->email }}</p>
                    <span class="badge bg-primary">Administrator</span>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted small">Full Name</label>
                            <div class="fw-semibold">{{ $admin->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Email Address</label>
                            <div class="fw-semibold">{{ $admin->email }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Phone Number</label>
                            <div class="fw-semibold">{{ $admin->phone ?? 'Not set' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Role</label>
                            <div class="fw-semibold">
                                <span class="badge bg-primary">Super Admin</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Member Since</label>
                            <div class="fw-semibold">{{ $admin->created_at->format('d M Y') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Last Login</label>
                            <div class="fw-semibold">{{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'First login' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mt-4" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-cog me-2 text-primary"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('admin.profile.edit') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-user-edit d-block mb-2" style="font-size: 2rem;"></i>
                                <span>Edit Profile</span>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.profile.edit-password') }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="fas fa-key d-block mb-2" style="font-size: 2rem;"></i>
                                <span>Change Password</span>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.settings') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="fas fa-cogs d-block mb-2" style="font-size: 2rem;"></i>
                                <span>System Settings</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
