@extends('layouts.app')

@section('title', 'Academic Rules History')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        @if(isset($rule))
                            Rule History: {{ $rule->name }} ({{ $rule->rule_code }})
                        @else
                            Academic Rules History
                        @endif
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('academic.rules.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Rules
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Filter Form -->
                    @if(!isset($rule))
                    <form method="GET" action="{{ route('academic.rules.history') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <select name="rule_id" class="form-control select2">
                                    <option value="">Select a Rule</option>
                                    @php
                                        $rules = \App\Models\Academic\AcademicRule::orderBy('name')->get();
                                    @endphp
                                    @foreach($rules as $r)
                                        <option value="{{ $r->id }}" {{ request('rule_id') == $r->id ? 'selected' : '' }}>
                                            {{ $r->name }} ({{ $r->rule_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Event</th>
                                    <th>Rule</th>
                                    <th>Changed By</th>
                                    <th>Old Value</th>
                                    <th>New Value</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($auditLogs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('d-m-Y H:i:s') }}</td>
                                        <td>
                                            @switch($log->event)
                                                @case('created')
                                                    <span class="badge badge-success">Created</span>
                                                    @break
                                                @case('updated')
                                                    <span class="badge badge-primary">Updated</span>
                                                    @break
                                                @case('deleted')
                                                    <span class="badge badge-danger">Deleted</span>
                                                    @break
                                                @case('status_toggled')
                                                    <span class="badge badge-warning">Status Changed</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-info">{{ ucfirst($log->event) }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($log->auditable)
                                                {{ $log->auditable->name ?? 'N/A' }}
                                                <br>
                                                <small class="text-muted">{{ $log->auditable->rule_code ?? '' }}</small>
                                            @else
                                                Rule #{{ $log->auditable_id }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->user)
                                                {{ $log->user->name }}
                                            @else
                                                System
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->old_values)
                                                @php
                                                    $oldDisplay = '';
                                                    if (isset($log->old_values['value'])) {
                                                        $oldDisplay = $log->old_values['value'];
                                                    } elseif (isset($log->old_values['is_active'])) {
                                                        $oldDisplay = $log->old_values['is_active'] ? 'Active' : 'Inactive';
                                                    } else {
                                                        $oldDisplay = json_encode($log->old_values);
                                                    }
                                                @endphp
                                                {{ Str::limit($oldDisplay, 50) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->new_values)
                                                @php
                                                    $newDisplay = '';
                                                    if (isset($log->new_values['value'])) {
                                                        $newDisplay = $log->new_values['value'];
                                                    } elseif (isset($log->new_values['is_active'])) {
                                                        $newDisplay = $log->new_values['is_active'] ? 'Active' : 'Inactive';
                                                    } else {
                                                        $newDisplay = json_encode($log->new_values);
                                                    }
                                                @endphp
                                                {{ Str::limit($newDisplay, 50) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $log->ip_address ?? 'N/A' }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            No history records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $auditLogs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
