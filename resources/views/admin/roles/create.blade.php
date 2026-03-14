@extends('layouts.app')

@section('title', 'Create Role')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="bi bi-shield-plus me-2"></i>Create New Role</h2>
            <p class="text-muted">Add a new role with specific permissions</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Roles
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Role Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Role Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="e.g., hod_science" required>
                            <small class="text-muted">Use lowercase letters, numbers, and underscores only</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="description" 
                                   value="{{ old('description') }}"
                                   placeholder="Brief description of the role">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Permissions</h5>
            </div>
            <div class="card-body">
                @forelse($permissions as $group => $groupPermissions)
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3">{{ $group }}</h6>
                    <div class="row">
                        @foreach($groupPermissions as $permission)
                        <div class="col-md-3 col-sm-4 col-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       name="permissions[]" 
                                       value="{{ $permission->id }}" 
                                       id="permission_{{ $permission->id }}"
                                       {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                    {{ str_replace($group . '.', '', $permission->name) }}
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <p class="text-muted">No permissions available. Please run the seeder first.</p>
                @endforelse
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-1"></i> Create Role
            </button>
        </div>
    </form>
</div>
@endsection
