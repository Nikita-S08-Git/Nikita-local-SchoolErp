<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .good { color: green; font-weight: bold; }
        .poor { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>School ERP System</h2>
        <h3>Attendance Report</h3>
        <p><strong>Division:</strong> {{ $division->division_name }}</p>
        <p><strong>Period:</strong> {{ $request->from_date }} to {{ $request->to_date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Roll No</th>
                <th>Student Name</th>
                <th>Total Days</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Attendance %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report as $row)
            <tr>
                <td>{{ $row['student']->roll_number }}</td>
                <td style="text-align: left;">{{ $row['student']->first_name }} {{ $row['student']->last_name }}</td>
                <td>{{ $row['total'] }}</td>
                <td>{{ $row['present'] }}</td>
                <td>{{ $row['absent'] }}</td>
                <td class="{{ $row['percentage'] >= 75 ? 'good' : 'poor' }}">
                    {{ number_format($row['percentage'], 2) }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 40px;">
        <p><strong>Generated on:</strong> {{ date('d M Y, h:i A') }}</p>
    </div>
</body>
</html>
