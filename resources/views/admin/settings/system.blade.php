@extends('layouts.app')

@section('title', 'System Information')
@section('page-title', 'System Information')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.admin') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.settings') }}">Settings</a></li>
                            <li class="breadcrumb-item active">System Information</li>
                        </ol>
                    </nav>
                    <h2 class="mb-1"><i class="fas fa-info-circle me-2 text-primary"></i>System Information</h2>
                </div>
                <a href="{{ route('admin.settings') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Settings
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- System Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-server me-2"></i>System Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold" width="40%">College Name</td>
                                    <td>{{ config('app.name') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">System Type</td>
                                    <td><span class="badge bg-info">Single College ERP</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Laravel Version</td>
                                    <td><span class="badge bg-primary">{{ $systemInfo['laravel_version'] }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">PHP Version</td>
                                    <td><span class="badge bg-success">{{ $systemInfo['php_version'] }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Database</td>
                                    <td><span class="badge bg-info">{{ $systemInfo['database'] }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Timezone</td>
                                    <td>{{ $systemInfo['timezone'] }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Debug Mode</td>
                                    <td>
                                        @if($systemInfo['debug_mode'])
                                            <span class="badge bg-warning text-dark">Enabled</span>
                                        @else
                                            <span class="badge bg-secondary">Disabled</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Cache Driver</td>
                                    <td><span class="badge bg-secondary">{{ $systemInfo['cache_driver'] }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Session Driver</td>
                                    <td><span class="badge bg-secondary">{{ $systemInfo['session_driver'] }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Mail Driver</td>
                                    <td><span class="badge bg-secondary">{{ $systemInfo['mail_driver'] ?? 'Not configured' }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Cache Management -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="mb-0"><i class="fas fa-broom me-2"></i>Cache Management</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Clear application cache if you're experiencing issues.</p>
                    <form action="{{ route('admin.settings.clear-cache') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to clear all caches?')">
                            <i class="fas fa-trash me-1"></i> Clear All Cache
                        </button>
                    </form>
                </div>
            </div>

            <!-- Environment Info -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-code me-2"></i>Environment</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <strong><i class="fas fa-info-circle me-2"></i>Environment:</strong> 
                        {{ app()->environment() }}
                        <br><br>
                        <small>
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Currently running in <strong>{{ app()->environment() }}</strong> mode. 
                            @if(app()->environment('production'))
                                Debug mode is disabled for security.
                            @else
                                Debug mode is enabled. Disable in production for better security and performance.
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
