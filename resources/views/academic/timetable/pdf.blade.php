<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Timetable Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 18px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .day-header {
            background-color: #e9ecef;
        }
        .timetable-cell {
            padding: 5px;
        }
        .subject {
            font-weight: bold;
            color: #333;
        }
        .teacher {
            font-size: 11px;
            color: #666;
        }
        .room {
            font-size: 11px;
            color: #888;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
        @media print {
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Timetable Report</h1>
        @if($division)
        <h2>{{ $division->division_name }}</h2>
        @endif
        <p>Generated on: {{ date('d M Y, h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="100">Time</th>
                @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                    <th class="day-header">{{ ucfirst($day) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                // Get unique time slots from all timetables
                $timeSlots = [];
                foreach($timetables as $t) {
                    if ($t && is_object($t) && isset($t->start_time)) {
                        $startTime = substr($t->start_time, 0, 5);
                        $endTime = substr($t->end_time, 0, 5);
                        $timeSlots[$startTime] = [
                            'start' => $startTime,
                            'end' => $endTime
                        ];
                    }
                }
                ksort($timeSlots);
                
                // Group timetables by day
                $groupedByDay = $timetables->groupBy('day_of_week');
            @endphp

            @forelse($timeSlots as $time => $slot)
                <tr>
                    <td>
                        <strong>{{ $slot['start'] }}</strong><br>
                        <span style="color: #999;">to</span><br>
                        <strong>{{ $slot['end'] }}</strong>
                    </td>
                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                        @php
                            $dayClasses = $groupedByDay[$day] ?? collect();
                            $class = $dayClasses->first(function($c) use ($time) {
                                return $c && substr($c->start_time, 0, 5) === $time;
                            });
                        @endphp
                        <td>
                            @if($class)
                                <div class="timetable-cell">
                                    <div class="subject">{{ $class->subject->code ?? '' }}</div>
                                    <div class="subject">{{ $class->subject->name ?? '' }}</div>
                                    <div class="teacher">{{ $class->teacher->name ?? 'No Teacher' }}</div>
                                    <div class="room">{{ $class->room->room_number ?? $class->room_number ?? 'TBA' }}</div>
                                </div>
                            @else
                                <span style="color: #ccc;">-</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">
                        No timetable entries found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Timetable Management System - {{ config('app.name', 'School Management') }}</p>
    </div>
</body>
</html>
