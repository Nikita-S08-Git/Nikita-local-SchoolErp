@extends('layouts.app')

@section('title', 'Edit Fee Structure')
@section('page-title', 'Edit Fee Structure')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Fee Structure</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('fees.structures.update', $structure) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Program <span class="text-danger">*</span></label>
                                    <select name="program_id" class="form-select @error('program_id') is-invalid @enderror" required>
                                        <option value="">Select Program</option>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}" {{ old('program_id', $structure->program_id) == $program->id ? 'selected' : '' }}>
                                                {{ $program->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('program_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Fee Head <span class="text-danger">*</span></label>
                                    <select name="fee_head_id" class="form-select @error('fee_head_id') is-invalid @enderror" required>
                                        <option value="">Select Fee Head</option>
                                        @foreach($feeHeads as $feeHead)
                                            <option value="{{ $feeHead->id }}" {{ old('fee_head_id', $structure->fee_head_id) == $feeHead->id ? 'selected' : '' }}>
                                                {{ $feeHead->name }} ({{ $feeHead->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('fee_head_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                                    <input type="text" name="academic_year" class="form-control @error('academic_year') is-invalid @enderror" 
                                           value="{{ old('academic_year', $structure->academic_year) }}" placeholder="2024-25" required>
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Amount <span class="text-danger">*</span></label>
                                    <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                                           value="{{ old('amount', $structure->amount) }}" step="0.01" min="0" placeholder="5000.00" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Installments <span class="text-danger">*</span></label>
                                    <select name="installments" class="form-select @error('installments') is-invalid @enderror" required>
                                        <option value="1" {{ old('installments', $structure->installments) == 1 ? 'selected' : '' }}>1 (Full Payment)</option>
                                        <option value="2" {{ old('installments', $structure->installments) == 2 ? 'selected' : '' }}>2 Installments</option>
                                        <option value="3" {{ old('installments', $structure->installments) == 3 ? 'selected' : '' }}>3 Installments</option>
                                        <option value="4" {{ old('installments', $structure->installments) == 4 ? 'selected' : '' }}>4 Installments</option>
                                    </select>
                                    @error('installments')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-circle me-2"></i>Update Fee Structure
                            </button>
                            <a href="{{ route('fees.structures.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection