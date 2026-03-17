<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; font-size: 8px; }
        .present { color: green; }
        .absent { color: red; }
        .late { color: orange; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>School ERP System</h2>
        <h3>Attendance Report</h3>
        <p><strong>Division:</strong> {{ $division->division_name }}</p>
        <p><strong>Period:</strong> {{ $startDate }} to {{ $endDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">Roll No</th>
                <th>Student Name</th>
                <th class="text-center">Total Days</th>
                <th class="text-center">Present</th>
                <th class="text-center">Absent</th>
                <th class="text-center">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report as $item)
            <tr>
                <td class="text-center">{{ $item['total'] }}</td>
                <td class="text-center">{{ $item['student']->roll_number ?? 'N/A' }}</td>
                <td>{{ $item['student']->first_name ?? '' }} {{ $item['student']->last_name ?? '' }}</td>
                <td class="text-center">{{ $item['present'] }}</td>
                <td class="text-center">{{ $item['absent'] }}</td>
                <td class="text-center">{{ number_format($item['percentage'], 1) }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No records found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <p><strong>Generated on:</strong> {{ date('d M Y, h:i A') }}</p>
    </div>
</body>
</html>
