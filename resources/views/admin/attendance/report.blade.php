@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Attendance Report</h1>
    <p class="mb-4">
        <strong>Division:</strong> {{ $division->name }} |
        <strong>Period:</strong> {{ $fromDate }} to {{ $toDate }}
    </p>

    <a href="{{ route('admin.attendance.report') }}" class="text-blue-600 mb-4 inline-block">&larr; Back to Filter</a>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border">
            <thead>
                <tr>
                    <th class="border px-4 py-2">Student</th>
                    <th class="border px-4 py-2">Admission No</th>
                    <th class="border px-4 py-2">Present</th>
                    <th class="border px-4 py-2">Absent</th>
                    <th class="border px-4 py-2">Attendance %</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report as $row)
                    <tr>
                        <td class="border px-4 py-2">{{ $row['student']->name }}</td>
                        <td class="border px-4 py-2">{{ $row['student']->admission_no }}</td>
                        <td class="border px-4 py-2">{{ $row['present_days'] }}</td>
                        <td class="border px-4 py-2">{{ $row['absent_days'] }}</td>
                        <td class="border px-4 py-2 {{ $row['attendance_percentage'] < 75 ? 'text-red-600 font-bold' : '' }}">
                            {{ $row['attendance_percentage'] }}%
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4">No records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection