<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Results - {{ $examination->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .pass { color: green; font-weight: bold; }
        .fail { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>School ERP System</h2>
        <h3>{{ $examination->name }} - Results</h3>
        <p><strong>Division:</strong> {{ $division->division_name }} | <strong>Academic Year:</strong> {{ $examination->academic_year }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Roll No</th>
                <th>Student Name</th>
                @foreach($subjects as $subject)
                    <th>{{ $subject->subject_name }}</th>
                @endforeach
                <th>Total</th>
                <th>%</th>
                <th>Grade</th>
                <th>Result</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
            <tr>
                <td>{{ $result['student']->roll_number }}</td>
                <td style="text-align: left;">{{ $result['student']->first_name }} {{ $result['student']->last_name }}</td>
                @foreach($subjects as $subject)
                    <td>{{ $result['marks'][$subject->id] ?? '-' }}</td>
                @endforeach
                <td><strong>{{ $result['total'] }}</strong></td>
                <td><strong>{{ number_format($result['percentage'], 2) }}</strong></td>
                <td>{{ $result['grade'] }}</td>
                <td class="{{ $result['result'] == 'Pass' ? 'pass' : 'fail' }}">{{ $result['result'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 40px;">
        <p><strong>Generated on:</strong> {{ date('d M Y, h:i A') }}</p>
    </div>
</body>
</html>
