<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Academic\Division;
use App\Models\User\Student;
use App\Models\Attendance\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $divisions = Division::where('is_active', true)->get();
        return view('attendance.index', compact('divisions'));
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'attendance_date' => 'required|date',
        ]);

        $division = Division::with('students')->findOrFail($validated['division_id']);
        $attendanceDate = $validated['attendance_date'];

        $existing = Attendance::where('attendance_date', $attendanceDate)
            ->whereIn('student_id', $division->students->pluck('id'))
            ->pluck('status', 'student_id')
            ->toArray();

        return view('attendance.mark', compact('division', 'attendanceDate', 'existing'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'attendance_date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late',
            'attendance.*.remarks' => 'nullable|string|max:255',
        ]);

        foreach ($validated['attendance'] as $record) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $record['student_id'],
                    'attendance_date' => $validated['attendance_date'],
                ],
                [
                    'status' => $record['status'],
                    'remarks' => $record['remarks'] ?? null,
                    'marked_by' => auth()->id(),
                ]
            );
        }

        return redirect()->route('attendance.index')
            ->with('success', 'Attendance marked successfully.');
    }

    public function report(Request $request)
    {
        if (!$request->has(['division_id', 'from_date', 'to_date'])) {
            $divisions = Division::where('is_active', true)->get();
            return view('attendance.report-form', compact('divisions'));
        }

        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $division = Division::with('students')->findOrFail($validated['division_id']);
        $fromDate = $validated['from_date'];
        $toDate = $validated['to_date'];
        $totalDays = Carbon::parse($fromDate)->diffInDays(Carbon::parse($toDate)) + 1;

        $report = [];
        foreach ($division->students as $student) {
            $records = Attendance::where('student_id', $student->id)
                ->whereBetween('attendance_date', [$fromDate, $toDate])
                ->get();

            $present = $records->where('status', 'present')->count();
            $absent = $records->where('status', 'absent')->count();
            $percentage = $totalDays > 0 ? round(($present / $totalDays) * 100, 2) : 0;

            $report[] = [
                'student' => $student,
                'present_days' => $present,
                'absent_days' => $absent,
                'attendance_percentage' => $percentage,
            ];
        }

        return view('attendance.report', compact('division', 'fromDate', 'toDate', 'report'));
    }
}
