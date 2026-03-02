<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\MarkAttendanceRequest;
use App\Http\Requests\Attendance\AttendanceReportRequest;
use App\Models\Academic\Attendance;
use App\Models\Academic\Division;
use App\Models\Academic\AcademicSession;
use App\Models\User\Student;
use App\Services\HolidayService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Holiday service instance
     */
    protected HolidayService $holidayService;

    /**
     * Constructor
     */
    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

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

    /**
     * Show mark attendance form with holiday validation
     */
    public function mark(Request $request)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'date' => 'required|date'
        ]);

        // Check if the selected date is a Sunday (weekly off day)
        $selectedDate = Carbon::parse($validated['date']);
        if ($selectedDate->dayOfWeek === Carbon::SUNDAY) {
            return redirect()->route('academic.attendance.index')
                ->with('error', 'Sundays are weekly off days. Attendance cannot be marked on Sundays.');
        }
        
        $division = Division::with('academicYear')->findOrFail($validated['division_id']);
        
        // Check if the selected date is a holiday
        $holidayCheck = $this->holidayService->validateAttendanceDate(
            $validated['date'],
            $division->academic_year_id
        );

        if (!$holidayCheck['valid'] && $holidayCheck['is_holiday']) {
            return redirect()->route('academic.attendance.index')
                ->with('error', 'This date is marked as Holiday. Attendance and Timetable cannot be added.');
        }

        // Get ALL students of that division
        $students = Student::where('division_id', $validated['division_id'])
                          ->where('student_status', 'active')
                          ->orderBy('roll_number')
                          ->get();

        // Check if attendance already exists
        $existingAttendance = Attendance::where('division_id', $validated['division_id'])
                                      ->whereDate('date', $validated['date'])
                                      ->pluck('status', 'student_id');

        return view('academic.attendance.mark', compact('division', 'students', 'validated', 'existingAttendance', 'holidayCheck'));
    }

    /**
     * Edit attendance for a specific date and division
     */
    public function edit(Request $request)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'date' => 'required|date'
        ]);

        // Check if the selected date is a Sunday (weekly off day)
        $selectedDate = Carbon::parse($validated['date']);
        if ($selectedDate->dayOfWeek === Carbon::SUNDAY) {
            return redirect()->route('academic.attendance.index')
                ->with('error', 'Sundays are weekly off days. Attendance cannot be edited on Sundays.');
        }
        
        $division = Division::with('academicYear')->findOrFail($validated['division_id']);
        
        // Check if the selected date is a holiday
        $holidayCheck = $this->holidayService->validateAttendanceDate(
            $validated['date'],
            $division->academic_year_id
        );

        // Get ALL students of that division
        $students = Student::where('division_id', $validated['division_id'])
                          ->where('student_status', 'active')
                          ->orderBy('roll_number')
                          ->get();

        // Get existing attendance
        $existingAttendance = Attendance::where('division_id', $validated['division_id'])
                                      ->whereDate('date', $validated['date'])
                                      ->pluck('status', 'student_id');

        return view('academic.attendance.edit', compact('division', 'students', 'validated', 'existingAttendance', 'holidayCheck'));
    }

    /**
     * Update attendance for a specific date and division
     */
    public function update(MarkAttendanceRequest $request)
    {
        $validated = $request->validated();
        
        // Validate that the date is not a holiday
        $holidayCheck = $this->holidayService->validateAttendanceDate(
            $validated['date'],
            Division::find($validated['division_id'])?->academic_year_id
        );

        if (!$holidayCheck['valid'] && $holidayCheck['is_holiday']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $holidayCheck['message'] . ': ' . ($holidayCheck['holiday_title'] ?? ''));
        }

        DB::transaction(function () use ($validated) {
            // Update existing attendance records
            foreach ($validated['students'] as $studentData) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $studentData['student_id'],
                        'division_id' => $validated['division_id'],
                        'date' => $validated['date'],
                    ],
                    [
                        'academic_session_id' => $validated['academic_session_id'],
                        'status' => $studentData['status']
                    ]
                );
            }
        });

        return redirect()->route('academic.attendance.index')
                         ->with('success', 'Attendance updated successfully.');
    }

    /**
     * Delete attendance for a specific date and division
     */
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'date' => 'required|date'
        ]);

        $deleted = Attendance::where('division_id', $validated['division_id'])
                           ->whereDate('date', $validated['date'])
                           ->delete();

        if ($deleted) {
            return redirect()->route('academic.attendance.index')
                             ->with('success', 'Attendance deleted successfully.');
        }

        return redirect()->route('academic.attendance.index')
                         ->with('error', 'No attendance records found to delete.');
    }

    /**
     * Store attendance with holiday validation
     */
    public function store(MarkAttendanceRequest $request)
    {
        $validated = $request->validated();
        
        // Validate that the date is not a holiday
        $holidayCheck = $this->holidayService->validateAttendanceDate(
            $validated['date'],
            Division::find($validated['division_id'])?->academic_year_id
        );

        if (!$holidayCheck['valid'] && $holidayCheck['is_holiday']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $holidayCheck['message'] . ': ' . ($holidayCheck['holiday_title'] ?? ''));
        }

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

    /**
     * AJAX endpoint to check if a date is a holiday
     */
    public function checkHoliday(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'division_id' => 'nullable|exists:divisions,id'
        ]);

        $date = Carbon::parse($request->date);
        $academicYearId = null;

        if ($request->filled('division_id')) {
            $academicYearId = Division::find($request->division_id)?->academic_year_id;
        }

        $holidayCheck = $this->holidayService->validateAttendanceDate($date, $academicYearId);

        return response()->json([
            'success' => true,
            'is_holiday' => $holidayCheck['is_holiday'],
            'valid' => $holidayCheck['valid'],
            'message' => $holidayCheck['message'],
            'holiday_title' => $holidayCheck['holiday_title'] ?? null,
            'holiday_type' => $holidayCheck['holiday_type'] ?? null,
        ]);
    }

    public function report(Request $request)
    {
        $divisions = Division::where('is_active', true)->get();

        $attendanceData = null;
        $holidayDates = [];
        
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
            
            // Get holiday dates in the range for display
            $division = Division::find($validated['division_id']);
            $holidayDates = $this->holidayService->getHolidayDatesInRange(
                $validated['start_date'],
                $validated['end_date'],
                $division?->academic_year_id
            );
        }

        return view('academic.attendance.report', compact('divisions', 'attendanceData', 'holidayDates'));
    }
}