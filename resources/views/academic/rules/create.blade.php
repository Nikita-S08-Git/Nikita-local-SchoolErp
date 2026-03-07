@extends('layouts.app')

@section('title', 'Create Academic Rule')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Academic Rule</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('academic.rules.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rule_code">Rule Code <span class="text-danger">*</span></label>
                                    <input type="text" name="rule_code" id="rule_code" class="form-control" required placeholder="e.g., PASS_PERCENTAGE">
                                    <small class="form-text text-muted">Use uppercase with underscores (e.g., PASS_PERCENTAGE)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" required placeholder="e.g., Pass Percentage">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category">Category <span class="text-danger">*</span></label>
                                    <select name="category" id="category" class="form-control" required>
                                        <option value="">Select Category</option>
                                        <option value="result">Result</option>
                                        <option value="attendance">Attendance</option>
                                        <option value="promotion">Promotion</option>
                                        <option value="fee">Fee</option>
                                        <option value="atkt">ATKT</option>
                                        <option value="examination">Examination</option>
                                        <option value="general">General</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="value_type">Value Type <span class="text-danger">*</span></label>
                                    <select name="value_type" id="value_type" class="form-control" required onchange="toggleValueField()">
                                        <option value="">Select Type</option>
                                        <option value="boolean">Boolean (Yes/No)</option>
                                        <option value="integer">Integer</option>
                                        <option value="decimal">Decimal</option>
                                        <option value="string">String</option>
                                        <option value="json">JSON</option>
                                        <option value="array">Array</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="value">Value <span class="text-danger">*</span></label>
                                    <input type="text" name="value" id="value" class="form-control" required placeholder="Rule value">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="default_value">Default Value</label>
                                    <input type="text" name="default_value" id="default_value" class="form-control" placeholder="Default value if not set">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="min_value">Min Value</label>
                                    <input type="number" name="min_value" id="min_value" class="form-control" step="any">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="max_value">Max Value</label>
                                    <input type="number" name="max_value" id="max_value" class="form-control" step="any">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="priority">Priority</label>
                                    <input type="number" name="priority" id="priority" class="form-control" min="0" value="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="display_order">Display Order</label>
                                    <input type="number" name="display_order" id="display_order" class="form-control" min="0" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="effective_from">Effective From</label>
                                    <input type="date" name="effective_from" id="effective_from" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="effective_to">Effective To</label>
                                    <input type="date" name="effective_to" id="effective_to" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" checked>
                                    <label for="is_active" class="form-check-label">Active</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="is_mandatory" id="is_mandatory" class="form-check-input" value="1">
                                    <label for="is_mandatory" class="form-check-label">Mandatory</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Rule
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

@push('scripts')
<script>
function toggleValueField() {
    var valueType = document.getElementById('value_type').value;
    var valueInput = document.getElementById('value');
    
    if (valueType === 'boolean') {
        valueInput.innerHTML = '<option value="true">Yes</option><option value="false">No</option>';
        valueInput.outerHTML = '<select name="value" id="value" class="form-control" required><option value="true">Yes</option><option value="false">No</option></select>';
    } else {
        valueInput.setAttribute('type', 'text');
    }
}
</script>
@endpush
@endsection
