@extends('layouts.app')

@section('title', 'View Academic Rule')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Academic Rule Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('academic.rules.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <a href="{{ route('academic.rules.edit', $rule->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="35%">Rule Code</th>
                                    <td><code>{{ $rule->rule_code }}</code></td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $rule->name }}</td>
                                </tr>
                                <tr>
                                    <th>Category</th>
                                    <td>
                                        @switch($rule->category)
                                            @case('result')
                                                <span class="badge badge-primary">Result</span>
                                                @break
                                            @case('attendance')
                                                <span class="badge badge-info">Attendance</span>
                                                @break
                                            @case('promotion')
                                                <span class="badge badge-success">Promotion</span>
                                                @break
                                            @case('fee')
                                                <span class="badge badge-warning">Fee</span>
                                                @break
                                            @case('atkt')
                                                <span class="badge badge-secondary">ATKT</span>
                                                @break
                                            @case('examination')
                                                <span class="badge badge-dark">Examination</span>
                                                @break
                                            @default
                                                <span class="badge badge-light">General</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th>Value Type</th>
                                    <td>{{ ucfirst($rule->value_type) }}</td>
                                </tr>
                                <tr>
                                    <th>Current Value</th>
                                    <td>
                                        @if($rule->value_type === 'boolean')
                                            {{ $rule->typed_value ? 'Yes' : 'No' }}
                                        @else
                                            {{ $rule->value }}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="35%">Default Value</th>
                                    <td>{{ $rule->default_value ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Min Value</th>
                                    <td>{{ $rule->min_value ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Max Value</th>
                                    <td>{{ $rule->max_value ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($rule->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Mandatory</th>
                                    <td>
                                        @if($rule->is_mandatory)
                                            <span class="badge badge-warning">Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Description</h5>
                                </div>
                                <div class="card-body">
                                    <p>{{ $rule->description ?? 'No description provided.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="35%">Effective From</th>
                                    <td>{{ $rule->effective_from ? $rule->effective_from->format('d-m-Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Effective To</th>
                                    <td>{{ $rule->effective_to ? $rule->effective_to->format('d-m-Y') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="35%">Priority</th>
                                    <td>{{ $rule->priority ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Display Order</th>
                                    <td>{{ $rule->display_order ?? 0 }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <form action="{{ route('academic.rules.toggle-status', $rule->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-{{ $rule->is_active ? 'warning' : 'success' }}">
                                    <i class="fas fa-{{ $rule->is_active ? 'ban' : 'check' }}"></i>
                                    {{ $rule->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <form action="{{ route('academic.rules.destroy', $rule->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this rule?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
