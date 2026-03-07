@extends('layouts.app')

@section('title', 'Fee Structure Details')
@section('page-title', 'Fee Structure Details')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Fee Structure Details</h5>
                    <div>
                        <a href="{{ route('fees.structures.edit', $structure) }}" class="btn btn-light btn-sm">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <a href="{{ route('fees.structures.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Fee Head:</th>
                                    <td><strong>{{ $structure->feeHead->name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Code:</th>
                                    <td><code>{{ $structure->feeHead->code }}</code></td>
                                </tr>
                                <tr>
                                    <th>Program:</th>
                                    <td>{{ $structure->program->name }}</td>
                                </tr>
                                <tr>
                                    <th>Academic Year:</th>
                                    <td>{{ $structure->academic_year }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Amount:</th>
                                    <td><strong class="text-success">₹{{ number_format($structure->amount, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Installments:</th>
                                    <td>{{ $structure->installments }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge {{ $structure->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $structure->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $structure->created_at->format('d M Y, h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($structure->feeHead->description)
                    <div class="mt-4">
                        <h6>Description:</h6>
                        <p class="text-muted">{{ $structure->feeHead->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection