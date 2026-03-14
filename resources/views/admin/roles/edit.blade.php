@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Role: {{ $role->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Roles
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group mb-3">
                            <label for="name">Role Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $role->name }}" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="guard_name">Guard Name</label>
                            <input type="text" class="form-control" id="guard_name" name="guard_name" value="{{ $role->guard_name }}" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label>Permissions</label>
                            <div class="row">
                                @forelse($permissions as $groupName => $groupPermissions)
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <strong>{{ $groupName }}</strong>
                                        </div>
                                        <div class="card-body">
                                            @foreach($groupPermissions as $permission)
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       class="form-check-input" 
                                                       id="permission_{{ $permission->id }}" 
                                                       name="permissions[]" 
                                                       value="{{ $permission->id }}"
                                                       {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <p class="text-muted">No permissions available. Please run the seeder.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Role
                            </button>
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
