@extends('layouts.app')

@section('title', 'Fee Structures')
@section('page-title', 'Fee Structures')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Fee Structures</h5>
                    <a href="{{ route('fees.structures.create') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle me-1"></i>Add New
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Fee Head</th>
                                    <th>Program</th>
                                    <th>Academic Year</th>
                                    <th>Amount</th>
                                    <th>Installments</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feeStructures as $structure)
                                <tr>
                                    <td>{{ $structure->feeHead->name }}</td>
                                    <td>{{ $structure->program->name }}</td>
                                    <td>{{ $structure->academic_year }}</td>
                                    <td>₹{{ number_format($structure->amount, 2) }}</td>
                                    <td>{{ $structure->installments }}</td>
                                    <td>
                                        <span class="badge {{ $structure->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $structure->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('fees.structures.show', $structure) }}" class="btn btn-info btn-sm" title="View">
                                                👁️
                                            </a>
                                            <a href="{{ route('fees.structures.edit', $structure) }}" class="btn btn-warning btn-sm" title="Edit">
                                                ✏️
                                            </a>
                                            <form method="POST" action="{{ route('fees.structures.destroy', $structure) }}" class="d-inline">
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
                                        <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                        No fee structures found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $feeStructures->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection