@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Mark Attendance</h1>

    <form action="{{ route('attendance.create') }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700">Division</label>
            <select name="division_id" class="w-full border rounded p-2" required>
                <option value="">-- Select Division --</option>
                @foreach($divisions as $division)
                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Date</label>
            <input type="date" name="attendance_date" class="w-full border rounded p-2" value="{{ old('attendance_date', date('Y-m-d')) }}" required>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Load Students</button>
    </form>
</div>
@endsection