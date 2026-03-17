@extends('layouts.app')

@section('title', 'Permissions Management')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="bi bi-key me-2"></i>Permissions Management</h2>
            <p class="text-muted">Manage system permissions</p>
        </div>
        <div class="col-md-6 text-end">
            @can('roles.create')
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Add New Permission
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

    @forelse($permissions as $group => $groupPermissions)
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">{{ $group }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($groupPermissions as $permission)
                <div class="col-md-3 col-sm-4 col-6 mb-2">
                    <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                        <span class="small">{{ $permission->name }}</span>
                        <div class="btn-group btn-group-sm">
                            @can('roles.edit')
                            <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('roles.delete')
                            @if($permission->roles()->count() == 0 && $permission->users()->count() == 0)
                            <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                            @endcan
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @empty
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-key fs-1 text-muted"></i>
            <h5 class="mt-3">No permissions found</h5>
            <p class="text-muted">Please run the roles and permissions seeder to create default permissions.</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
