@extends('layouts.app')

@section('title', 'Staff Management')
@section('page-title', 'Staff Management')

@section('content')
<div class="container-fluid p-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-people me-2"></i>Staff Members</h5>
            <a href="{{ route('staff.create') }}" class="btn btn-light btn-sm">
                <i class="bi bi-plus-circle me-1"></i>Add Staff
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff as $member)
                        <tr>
                            <td>{{ $member->employee_id }}</td>
                            <td>{{ $member->first_name }} {{ $member->last_name }}</td>
                            <td>{{ $member->user->email ?? 'N/A' }}</td>
                            <td>
                                @if($member->user && $member->user->temp_password)
                                    <div class="input-group input-group-sm" style="max-width: 200px;">
                                        <input type="password" class="form-control font-monospace" value="{{ $member->user->temp_password }}"
                                               id="staff-password-{{ $member->id }}" readonly style="background-color: #f8f9fa; letter-spacing: 2px;">
                                        <button class="btn btn-outline-success" type="button"
                                                onclick="toggleStaffPassword('staff-password-{{ $member->id }}')" title="Show/Hide">
                                            <i class="bi bi-eye" id="staff-eye-{{ $member->id }}"></i>
                                        </button>
                                        <button class="btn btn-outline-primary" type="button"
                                                onclick="copyStaffPassword('staff-password-{{ $member->id }}')" title="Copy">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Generated: {{ $member->user->password_generated_at ? \Carbon\Carbon::parse($member->user->password_generated_at)->diffForHumans() : 'N/A' }}</small>
                                @else
                                    <span class="badge bg-warning">No Password Set</span>
                                @endif
                            </td>
                            <td>{{ $member->designation }}</td>
                            <td>{{ $member->department->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $member->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($member->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('staff.show', $member) }}" class="btn btn-sm btn-info" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('staff.edit', $member) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No staff members found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Custom Pagination Component -->
            <x-pagination :paginator="$staff" />
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleStaffPassword(inputId) {
    const input = document.getElementById(inputId);
    const eyeIcon = document.getElementById('staff-eye-' + inputId.replace('staff-password-', ''));
    
    if (input.type === 'password') {
        input.type = 'text';
        if (eyeIcon) {
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        }
    } else {
        input.type = 'password';
        if (eyeIcon) {
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    }
}

function copyStaffPassword(inputId) {
    const input = document.getElementById(inputId);
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value).then(function() {
        const toast = document.createElement('div');
        toast.className = 'alert alert-success position-fixed bottom-0 end-0 m-3';
        toast.textContent = 'Password copied to clipboard!';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }, function(err) {
        const toast = document.createElement('div');
        toast.className = 'alert alert-danger position-fixed bottom-0 end-0 m-3';
        toast.textContent = 'Failed to copy password';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    });
}
</script>
@endpush
@endsection
