@extends('layouts.app')

@section('title', 'Scholarships')
@section('page-title', 'Scholarships')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-award me-2"></i>Scholarships</h5>
                    <a href="{{ route('fees.scholarships.create') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle me-1"></i>Add New
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>Max Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($scholarships as $scholarship)
                                <tr>
                                    <td><strong>{{ $scholarship->name }}</strong></td>
                                    <td><code>{{ $scholarship->code }}</code></td>
                                    <td>
                                        <span class="badge {{ $scholarship->type === 'percentage' ? 'bg-primary' : 'bg-success' }}">
                                            {{ ucfirst($scholarship->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($scholarship->type === 'percentage')
                                            {{ $scholarship->value }}%
                                        @else
                                            ₹{{ number_format($scholarship->value, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($scholarship->max_amount)
                                            ₹{{ number_format($scholarship->max_amount, 2) }}
                                        @else
                                            <span class="text-muted">No limit</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $scholarship->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $scholarship->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('fees.scholarships.show', $scholarship) }}" class="btn btn-info btn-sm" title="View">
                                                👁️
                                            </a>
                                            <a href="{{ route('fees.scholarships.edit', $scholarship) }}" class="btn btn-warning btn-sm" title="Edit">
                                                ✏️
                                            </a>
                                            <form method="POST" action="{{ route('fees.scholarships.destroy', $scholarship) }}" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')" title="Delete">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-award display-4 d-block mb-2"></i>
                                        No scholarships found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $scholarships->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection