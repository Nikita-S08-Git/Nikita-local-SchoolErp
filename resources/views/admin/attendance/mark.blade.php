@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Mark Attendance for {{ $division->name }} on {{ $attendanceDate }}</h1>

    <form action="{{ route('attendance.store') }}" method="POST">
        @csrf
        <input type="hidden" name="division_id" value="{{ $division->id }}">
        <input type="hidden" name="attendance_date" value="{{ $attendanceDate }}">

        <table class="min-w-full bg-white border">
            <thead>
                <tr>
                    <th class="border px-4 py-2">Student</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Check-in Time</th>
                    <th class="border px-4 py-2">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($division->students as $student)
                    @php
                        $status = $existing[$student->id] ?? 'present';
                    @endphp
                    <tr>
                        <td class="border px-4 py-2">{{ $student->name }} ({{ $student->admission_no }})</td>
                        <td class="border px-4 py-2">
                            <select name="attendance[{{ $loop->index }}][status]" class="w-full">
                                <option value="present" {{ $status == 'present' ? 'selected' : '' }}>Present</option>
                                <option value="absent" {{ $status == 'absent' ? 'selected' : '' }}>Absent</option>
                                <option value="late" {{ $status == 'late' ? 'selected' : '' }}>Late</option>
                            </select>
                            <input type="hidden" name="attendance[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                        </td>
                        <td class="border px-4 py-2">
                            <input type="time" name="attendance[{{ $loop->index }}][check_in_time]"
                                value="{{ old("attendance.{$loop->index}.check_in_time") }}"
                                class="w-full">
                        </td>
                        <td class="border px-4 py-2">
                            <input type="text" name="attendance[{{ $loop->index }}][remarks]"
                                placeholder="Optional"
                                value="{{ old("attendance.{$loop->index}.remarks") }}"
                                class="w-full">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Save Attendance</button>
            <a href="{{ route('attendance.index') }}" class="ml-2 text-gray-600">Cancel</a>
        </div>
    </form>
</div>
@endsection