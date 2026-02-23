@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Attendance Report</h1>

    <form method="GET" action="{{ route('admin.attendance.report') }}" class="bg-white p-6 rounded shadow">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-gray-700">Division</label>
                <select name="division_id" class="w-full border rounded p-2" required>
                    <option value="">-- Select --</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-gray-700">From Date</label>
                <input type="date" name="from_date" class="w-full border rounded p-2" required>
            </div>
            <div>
                <label class="block text-gray-700">To Date</label>
                <input type="date" name="to_date" class="w-full border rounded p-2" required>
            </div>
        </div>
        <button type="submit" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">Generate Report</button>
    </form>
</div>
@endsection