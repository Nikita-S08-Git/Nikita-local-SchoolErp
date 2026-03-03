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
                <th class="text-center">Date</th>
                <th class="text-center">Roll No</th>
                <th>Student Name</th>
                <th class="text-center">DOB</th>
                <th class="text-center">Gender</th>
                <th>Father Name</th>
                <th>Father Phone</th>
                <th>Mother Name</th>
                <th>Guardian Name</th>
                <th>Guardian Phone</th>
                <th class="text-center">Status</th>
                <th class="text-center">Teacher Name</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendanceRecords as $record)
            <tr>
                <td class="text-center">{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
                <td class="text-center">{{ $record->student->roll_number ?? 'N/A' }}</td>
                <td>{{ $record->student->full_name ?? 'N/A' }}</td>
                <td class="text-center">{{ $record->student->date_of_birth ? \Carbon\Carbon::parse($record->student->date_of_birth)->format('d M Y') : 'N/A' }}</td>
                <td class="text-center">{{ ucfirst($record->student->gender ?? 'N/A') }}</td>
                <td>{{ $record->student->studentProfile->father_name ?? 'N/A' }}</td>
                <td>{{ $record->student->studentProfile->father_phone ?? 'N/A' }}</td>
                <td>{{ $record->student->studentProfile->mother_name ?? 'N/A' }}</td>
                <td>{{ $record->student->studentProfile->guardian_name ?? 'N/A' }}</td>
                <td>{{ $record->student->studentProfile->guardian_phone ?? 'N/A' }}</td>
                <td class="text-center {{ $record->status }}">
                    {{ ucfirst($record->status) }}
                </td>
                <td class="text-center">{{ $record->markedBy->name ?? 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="12" class="text-center">No attendance records found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <p><strong>Generated on:</strong> {{ date('d M Y, h:i A') }}</p>
    </div>
</body>
</html>
