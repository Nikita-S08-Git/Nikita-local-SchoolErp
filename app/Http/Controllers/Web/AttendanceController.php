<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\MarkAttendanceRequest;
use App\Http\Requests\Attendance\AttendanceReportRequest;
use App\Models\Academic\Attendance;
use App\Models\Academic\Division;
use App\Models\Academic\AcademicSession;
use App\Models\User\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $divisions = Division::where('is_active', true)
            ->with(['academicYear', 'session'])
            ->get();
        $sessions = AcademicSession::where('is_active', true)->get();

        return view('academic.attendance.index', compact('divisions', 'sessions'));
    }

    public function create()
    {
        $divisions = Division::where('is_active', true)
            ->with(['academicYear', 'session'])
            ->get();
        $sessions = AcademicSession::where('is_active', true)->get();

        return view('academic.attendance.create', compact('divisions', 'sessions'));
    }

    public function mark(Request $request)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'date' => 'required|date'
        ]);

        $division = Division::with('academicYear')->findOrFail($validated['division_id']);
        $students = Student::where('division_id', $validated['division_id'])
                          ->where('student_status', 'active')
                          ->orderBy('roll_number')
                          ->get();

        // Check if attendance already exists
        $existingAttendance = Attendance::where('division_id', $validated['division_id'])
                                      ->whereDate('date', $validated['date'])
                                      ->pluck('status', 'student_id');

        return view('academic.attendance.mark', compact('division', 'students', 'validated', 'existingAttendance'));
    }

    public function store(MarkAttendanceRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            // Delete existing attendance for this date and division
            Attendance::where('division_id', $validated['division_id'])
                     ->whereDate('date', $validated['date'])
                     ->delete();

            // Insert new attendance records
            foreach ($validated['students'] as $studentData) {
                Attendance::create([
                    'student_id' => $studentData['student_id'],
                    'division_id' => $validated['division_id'],
                    'academic_session_id' => $validated['academic_session_id'],
                    'date' => $validated['date'],
                    'status' => $studentData['status']
                ]);
            }
        });

        return redirect()->route('academic.attendance.index')
                         ->with('success', 'Attendance marked successfully.');
    }

    public function report(Request $request)
    {
        $divisions = Division::where('is_active', true)->get();

        $attendanceData = null;
        if ($request->filled(['division_id', 'start_date', 'end_date'])) {
            $validated = $request->validate([
                'division_id' => 'required|exists:divisions,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);

            // Load attendance with student relationship
            $attendanceRecords = Attendance::with(['student.user'])
                ->where('division_id', $validated['division_id'])
                ->whereBetween('date', [$validated['start_date'], $validated['end_date']])
                ->get();
            
            // Group by date
            $attendanceData = $attendanceRecords->groupBy('date');
        }

        return view('academic.attendance.report', compact('divisions', 'attendanceData'));
    }
}