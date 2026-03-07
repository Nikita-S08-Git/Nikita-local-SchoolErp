@extends('layouts.app')

@section('title', 'Academic Rules')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Academic Rules Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('academic.rules.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Rule
                        </a>
                        <a href="{{ route('academic.rules.history') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-history"></i> History
                        </a>
                        <a href="{{ route('academic.rules.clear-cache') }}" class="btn btn-warning btn-sm" onclick="return confirm('Are you sure you want to clear all caches?')">
                            <i class="fas fa-broom"></i> Clear Cache
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('academic.rules.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Search by name or code..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="category" class="form-control">
                                    <option value="">All Categories</option>
                                    <option value="result" {{ request('category') == 'result' ? 'selected' : '' }}>Result</option>
                                    <option value="attendance" {{ request('category') == 'attendance' ? 'selected' : '' }}>Attendance</option>
                                    <option value="promotion" {{ request('category') == 'promotion' ? 'selected' : '' }}>Promotion</option>
                                    <option value="fee" {{ request('category') == 'fee' ? 'selected' : '' }}>Fee</option>
                                    <option value="atkt" {{ request('category') == 'atkt' ? 'selected' : '' }}>ATKT</option>
                                    <option value="examination" {{ request('category') == 'examination' ? 'selected' : '' }}>Examination</option>
                                    <option value="general" {{ request('category') == 'general' ? 'selected' : '' }}>General</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('academic.rules.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Value</th>
                                    <th>Type</th>
                                    <th>Effective From</th>
                                    <th>Effective To</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rules as $rule)
                                    <tr>
                                        <td>
                                            <code>{{ $rule->rule_code }}</code>
                                        </td>
                                        <td>{{ $rule->name }}</td>
                                        <td>
                                            @switch($rule->category)
                                                @case('result')
                                                    <span class="badge bg-primary">Result</span>
                                                    @break
                                                @case('attendance')
                                                    <span class="badge bg-info">Attendance</span>
                                                    @break
                                                @case('promotion')
                                                    <span class="badge bg-success">Promotion</span>
                                                    @break
                                                @case('fee')
                                                    <span class="badge bg-warning">Fee</span>
                                                    @break
                                                @case('atkt')
                                                    <span class="badge bg-secondary">ATKT</span>
                                                    @break
                                                @case('examination')
                                                    <span class="badge bg-dark">Examination</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-light text-dark">General</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($rule->value_type === 'boolean')
                                                {{ $rule->typed_value ? 'Yes' : 'No' }}
                                            @elseif($rule->value_type === 'integer' || $rule->value_type === 'decimal')
                                                {{ $rule->value }}
                                            @else
                                                {{ Str::limit($rule->value, 30) }}
                                            @endif
                                        </td>
                                        <td>{{ ucfirst($rule->value_type) }}</td>
                                        <td>{{ $rule->effective_from ? $rule->effective_from->format('d-m-Y') : 'N/A' }}</td>
                                        <td>{{ $rule->effective_to ? $rule->effective_to->format('d-m-Y') : 'N/A' }}</td>
                                        <td>
                                            @if($rule->is_active)
                                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>
                                            @else
                                                <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('academic.rules.show', $rule->id) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('academic.rules.edit', $rule->id) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('academic.rules.toggle-status', $rule->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-{{ $rule->is_active ? 'warning' : 'success' }} btn-sm" title="{{ $rule->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fas fa-{{ $rule->is_active ? 'ban' : 'check' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('academic.rules.destroy', $rule->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this rule?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No rules found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $rules->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
