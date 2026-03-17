<?php

namespace App\Http\Controllers\Api\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Academic\Attendance;
use App\Models\Academic\Division;
use App\Models\Academic\AcademicYear;
use App\Services\HolidayService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Holiday service instance
     */
    protected HolidayService $holidayService;

    /**
     * Ensure all attendance actions require authentication
     */
    public function __construct(HolidayService $holidayService)
    {
        $this->middleware('auth:sanctum');
        $this->holidayService = $holidayService;
    }

    /**
     * Mark attendance for students in a specific lecture
     * 
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\AuthenticationException if user is not authenticated
     */
    public function markAttendance(Request $request): JsonResponse
    {
        // Validation
        $request->validate([
            'timetable_id' => 'required|exists:timetables,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late',
            'attendance.*.check_in_time' => 'nullable|date_format:H:i',
            'attendance.*.remarks' => 'nullable|string',
        ]);

        $timetable = \App\Models\Academic\Timetable::find($request->timetable_id);
        
        // Check if timetable is active for attendance
        if (!$timetable->isActiveForAttendance()) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance cannot be marked for this lecture at this time'
            ], 422);
        }

        // Check if the date is a holiday
        $division = $timetable->division;
        $academicYearId = $division?->academic_year_id ?? AcademicYear::getCurrentAcademicYearId();
        
        $holidayCheck = $this->holidayService->validateAttendanceDate($request->date, $academicYearId);
        
        if (!$holidayCheck['valid'] && $holidayCheck['is_holiday']) {
            return response()->json([
                'success' => false,
                'message' => 'This date is marked as Holiday. Attendance and Timetable cannot be added.',
                'is_holiday' => true,
                'holiday_title' => $holidayCheck['holiday_title'] ?? null,
            ], 422);
        }

        // Get authenticated user ID (guaranteed by middleware)
        $userId = auth()->id();

        // Verify user has permission to mark attendance for this timetable
        if ($timetable->teacher_id && $timetable->teacher_id !== $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to mark attendance for this lecture'
            ], 403);
        }

        foreach ($request->attendance as $record) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $record['student_id'],
                    'timetable_id' => $request->timetable_id,
                    'date' => $request->date,
                ],
                [
                    'status' => $record['status'],
                    'check_in_time' => $record['check_in_time'] ?? null,
                    'remarks' => $record['remarks'] ?? null,
                    'marked_by' => $userId,
                    'division_id' => $timetable->division_id,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance marked successfully'
        ]);
    }

    /**
     * Get attendance report for a division
     * 
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\AuthenticationException if user is not authenticated
     */
    public function getAttendanceReport(Request $request): JsonResponse
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $division = Division::with('students')->find($request->division_id);
        $totalDays = Carbon::parse($request->from_date)->diffInDays(Carbon::parse($request->to_date)) + 1;

        $report = [];
        foreach ($division->students as $student) {
            $attendanceRecords = Attendance::where('student_id', $student->id)
                ->whereBetween('date', [$request->from_date, $request->to_date])
                ->get();

            $presentDays = $attendanceRecords->where('status', 'present')->count();
            $absentDays = $attendanceRecords->where('status', 'absent')->count();
            $percentage = $totalDays > 0 ? round($presentDays / $totalDays * 100, 2) : 0;

            $report[] = [
                'student' => $student,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'attendance_percentage' => $percentage
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get list of attendance defaulters
     * 
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\AuthenticationException if user is not authenticated
     */
    public function getDefaulters(Request $request): JsonResponse
    {
        $threshold = $request->threshold ?? 75;
        $fromDate = $request->from_date ?? Carbon::now()->subMonth()->toDateString();
        $toDate = $request->to_date ?? Carbon::now()->toDateString();

        $totalDays = Carbon::parse($fromDate)->diffInDays(Carbon::parse($toDate)) + 1;

        $defaulters = Attendance::selectRaw('student_id, COUNT(*) as present_days')
            ->where('status', 'present')
            ->whereBetween('date', [$fromDate, $toDate])
            ->groupBy('student_id')
            ->havingRaw('(COUNT(*) / ?) * 100 < ?', [$totalDays, $threshold])
            ->with('student')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $defaulters
        ]);
    }
}
