@extends('layouts.app')

@section('title', 'Edit Academic Rule')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Academic Rule</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('academic.rules.update', $rule->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rule_code">Rule Code <span class="text-danger">*</span></label>
                                    <input type="text" name="rule_code" id="rule_code" class="form-control" required value="{{ $rule->rule_code }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" required value="{{ $rule->name }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category">Category <span class="text-danger">*</span></label>
                                    <select name="category" id="category" class="form-control" required>
                                        <option value="result" {{ $rule->category == 'result' ? 'selected' : '' }}>Result</option>
                                        <option value="attendance" {{ $rule->category == 'attendance' ? 'selected' : '' }}>Attendance</option>
                                        <option value="promotion" {{ $rule->category == 'promotion' ? 'selected' : '' }}>Promotion</option>
                                        <option value="fee" {{ $rule->category == 'fee' ? 'selected' : '' }}>Fee</option>
                                        <option value="atkt" {{ $rule->category == 'atkt' ? 'selected' : '' }}>ATKT</option>
                                        <option value="examination" {{ $rule->category == 'examination' ? 'selected' : '' }}>Examination</option>
                                        <option value="general" {{ $rule->category == 'general' ? 'selected' : '' }}>General</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="value_type">Value Type <span class="text-danger">*</span></label>
                                    <select name="value_type" id="value_type" class="form-control" required>
                                        <option value="boolean" {{ $rule->value_type == 'boolean' ? 'selected' : '' }}>Boolean (Yes/No)</option>
                                        <option value="integer" {{ $rule->value_type == 'integer' ? 'selected' : '' }}>Integer</option>
                                        <option value="decimal" {{ $rule->value_type == 'decimal' ? 'selected' : '' }}>Decimal</option>
                                        <option value="string" {{ $rule->value_type == 'string' ? 'selected' : '' }}>String</option>
                                        <option value="json" {{ $rule->value_type == 'json' ? 'selected' : '' }}>JSON</option>
                                        <option value="array" {{ $rule->value_type == 'array' ? 'selected' : '' }}>Array</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="value">Value <span class="text-danger">*</span></label>
                                    <input type="text" name="value" id="value" class="form-control" required value="{{ $rule->value }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="default_value">Default Value</label>
                                    <input type="text" name="default_value" id="default_value" class="form-control" value="{{ $rule->default_value }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="min_value">Min Value</label>
                                    <input type="number" name="min_value" id="min_value" class="form-control" step="any" value="{{ $rule->min_value }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="max_value">Max Value</label>
                                    <input type="number" name="max_value" id="max_value" class="form-control" step="any" value="{{ $rule->max_value }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="priority">Priority</label>
                                    <input type="number" name="priority" id="priority" class="form-control" min="0" value="{{ $rule->priority }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="display_order">Display Order</label>
                                    <input type="number" name="display_order" id="display_order" class="form-control" min="0" value="{{ $rule->display_order }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="effective_from">Effective From</label>
                                    <input type="date" name="effective_from" id="effective_from" class="form-control" value="{{ $rule->effective_from ? $rule->effective_from->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="effective_to">Effective To</label>
                                    <input type="date" name="effective_to" id="effective_to" class="form-control" value="{{ $rule->effective_to ? $rule->effective_to->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ $rule->description }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ $rule->is_active ? 'checked' : '' }}>
                                    <label for="is_active" class="form-check-label">Active</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="is_mandatory" id="is_mandatory" class="form-check-input" value="1" {{ $rule->is_mandatory ? 'checked' : '' }}>
                                    <label for="is_mandatory" class="form-check-label">Mandatory</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Rule
                            </button>
                            <a href="{{ route('academic.rules.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
