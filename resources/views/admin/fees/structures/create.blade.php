@extends('layouts.app')

@section('title', 'Create Fee Structure')
@section('page-title', 'Create New Fee Structure')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Fee Structure Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.fees.structures.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="program_id" class="form-label">Program <span class="text-danger">*</span></label>
                            <select class="form-select" id="program_id" name="program_id" required>
                                <option value="">Select Program</option>
                                @foreach($programs as $program)
                                <option value="{{ $program->id }}">{{ $program->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="division_id" class="form-label">Division (Optional)</label>
                            <select class="form-select" id="division_id" name="division_id">
                                <option value="">All Divisions ({{ $divisions->count() }} found)</option>
                                @forelse($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->division_name }}</option>
                                @empty
                                <option value="">No divisions available</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="academic_year" class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="academic_year" name="academic_year" 
                                   placeholder="e.g., 2024-25" required>
                        </div>

                        <div class="mb-3">
                            <label for="fee_head" class="form-label">Fee Head <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="fee_head" name="fee_head" 
                                   placeholder="e.g., Tuition Fee, Lab Fee" required>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="amount" name="amount" 
                                   step="0.01" min="0" placeholder="0.00" required>
                        </div>

                        <div class="mb-3">
                            <label for="installments" class="form-label">Number of Installments</label>
                            <select class="form-select" id="installments" name="installments">
                                <option value="1">One Time</option>
                                <option value="2">2 Installments</option>
                                <option value="3">3 Installments</option>
                                <option value="4">4 Installments</option>
                                <option value="5">5 Installments</option>
                                <option value="6">6 Installments (Monthly)</option>
                                <option value="10">10 Installments (Monthly)</option>
                                <option value="12">12 Installments (Monthly)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="frequency" class="form-label">Frequency <span class="text-danger">*</span></label>
                            <select class="form-select" id="frequency" name="frequency" required>
                                <option value="once">One Time</option>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="half_yearly">Half Yearly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="3" placeholder="Optional description"></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.fees.structures') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Fee Structure</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection