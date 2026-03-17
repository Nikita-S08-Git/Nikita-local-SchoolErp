@extends('layouts.app')

@section('title', 'Roles Management')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="bi bi-shield-lock me-2"></i>Roles Management</h2>
            <p class="text-muted">Manage user roles and their permissions</p>
        </div>
        <div class="col-md-6 text-end">
            @can('roles.create')
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Add New Role
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Role Name</th>
                            <th>Description</th>
                            <th>Permissions</th>
                            <th>Users</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr>
                            <td>
                                <span class="fw-bold">{{ ucfirst($role->name) }}</span>
                                @if($role->name === 'super_admin')
                                    <span class="badge bg-danger ms-1">System</span>
                                @endif
                            </td>
                            <td>{{ $role->description ?? 'No description' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $role->permissions->count() }} permissions</span>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $role->users->count() }} users</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @can('roles.view')
                                    <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @endcan
                                    
                                    @if($role->name !== 'super_admin')
                                        @can('roles.edit')
                                        <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('roles.permissions.update')
                                        <a href="{{ route('admin.roles.permissions', $role->id) }}" class="btn btn-sm btn-secondary">
                                            <i class="bi bi-key"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('roles.delete')
                                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    @else
                                        <span class="badge bg-secondary">Protected</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">No roles found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
