@extends('student.layouts.app')

@section('title', 'My Timetable')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-calendar-week me-2 text-primary"></i>My Timetable</h2>
                    <p class="text-muted mb-0">View your weekly class schedule</p>
                </div>
                <button onclick="window.print()" class="btn btn-outline-primary">
                    <i class="bi bi-printer me-1"></i>Print Timetable
                </button>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 15px;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="120">Time/Day</th>
                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day)
                                <th class="text-center {{ strtolower(date('l')) === $day ? 'bg-primary bg-opacity-10' : '' }}">
                                    {{ ucfirst($day) }}
                                    @if(strtolower(date('l')) === $day)
                                        <span class="badge bg-primary ms-2">Today</span>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $timeSlots = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00'];
                        @endphp
                        @foreach($timeSlots as $time)
                            <tr>
                                <td class="fw-bold bg-light">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $time }} - {{ date('H:i', strtotime($time . ' +1 hour')) }}
                                </td>
                                @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day)
                                    <td style="min-width: 180px;">
                                        @php
                                            $class = null;
                                            if (isset($timetable[$day])) {
                                                $class = $timetable[$day]->firstWhere('start_time', substr($time, 0, 5) . ':00');
                                            }
                                        @endphp
                                        @if($class)
                                            <div class="text-center p-2" style="background: linear-gradient(135deg, #f8f9ff 0%, #eef2ff 100%); border-radius: 8px; border-left: 3px solid #667eea;">
                                                <strong class="d-block text-primary mb-1">{{ $class->subject->name ?? 'N/A' }}</strong>
                                                <small class="text-muted d-block mb-1">
                                                    <i class="bi bi-person me-1"></i>{{ $class->teacher->name ?? 'N/A' }}
                                                </small>
                                                <span class="badge bg-info">
                                                    <i class="bi bi-geo-alt me-1"></i>{{ $class->room ?? 'TBA' }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Important Notes</h6>
                    <ul class="mb-0">
                        <li>Classes start at 9:00 AM sharp</li>
                        <li>Lunch break: 1:00 PM - 2:00 PM</li>
                        <li>Last period ends at 4:00 PM</li>
                        <li>Arrive 5 minutes before class starts</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Need Help?</h6>
                    <p class="mb-2 small">If you notice any discrepancies in your timetable, please contact:</p>
                    <p class="mb-0 small">
                        <i class="bi bi-envelope me-1"></i> admin@schoolerp.com<br>
                        <i class="bi bi-telephone me-1"></i> +91 1234567890
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        #sidebar, .navbar, .btn {
            display: none !important;
        }
        #content {
            margin-left: 0 !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }
    }
</style>
@endpush
@endsection
