<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Timetable\StoreTimetableRequest;
use App\Http\Requests\Timetable\UpdateTimetableRequest;
use App\Models\Academic\Timetable;
use App\Models\Academic\Division;
use App\Models\Academic\Subject;
use App\Models\Academic\TimeSlot;
use App\Models\Academic\AcademicYear;
use App\Models\Academic\Room;
use App\Models\User;
use App\Services\HolidayService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Enhanced Timetable Controller
 * 
 * Handles all timetable operations including:
 * - CRUD operations with AJAX support
 * - Conflict detection and validation
 * - Import/Export functionality
 * - Grid and Table views
 * - Role-based access control
 * 
 * @package App\Http\Controllers\Web
 */
class TimetableController extends Controller
{
    /**
     * Days of the week
     */
    protected array $days = [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
    ];

    /**
     * Holiday service instance
     */
    protected HolidayService $holidayService;

    /**
     * Constructor - Apply middleware
     */
    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
        
        // Apply auth middleware to all timetable operations
        $this->middleware('auth')->only([
            'index', 'create', 'store', 'show', 'edit', 'update', 'destroy',
            'gridView', 'tableView', 'ajaxGetTimetable', 'ajaxUpdateStatus'
        ]);
    }

    /**
     * ============================================================
     * MAIN INDEX - Shows table view by default
     * ============================================================
     */

    /**
     * Display timetable - Table View
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        return $this->tableView($request);
    }

    /**
     * Table View with pagination, search, and filters
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function tableView(Request $request)
    {
        $query = Timetable::withRelationships()
            ->byStatus($request->get('status', 'active'))
            ->ordered();

        // Apply filters
        if ($request->filled('division_id')) {
            $query->byDivision($request->division_id);
        }

        if ($request->filled('day_of_week')) {
            $query->byDay($request->day_of_week);
        }

        if ($request->filled('teacher_id')) {
            $query->byTeacher($request->teacher_id);
        }

        if ($request->filled('date')) {
            // Filter by specific date
            $query->byDate($request->date);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Get current academic year or use requested one
        $academicYearId = $request->get('academic_year_id', AcademicYear::getCurrentAcademicYearId());
        if ($academicYearId) {
            $query->byAcademicYear($academicYearId);
        }

        // Pagination - 15 items per page
        $timetables = $query->paginate(15)->appends($request->query());

        // Get filter options
        $divisions = $this->getAccessibleDivisions();
        $teachers = User::role('teacher')->where('is_active', true)->get();
        $academicYears = AcademicYear::orderBy('year_number', 'desc')->get();
        $programs = \App\Models\Academic\Program::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $rooms = Room::where('status', Room::STATUS_AVAILABLE)->orderBy('room_number')->get();

        return view('academic.timetable.table', compact(
            'timetables',
            'divisions',
            'teachers',
            'academicYears',
            'programs',
            'subjects',
            'rooms'
        ));
    }

    /**
     * Grid View - Shows all timetable entries as a list
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function gridView(Request $request)
    {
        $divisionId = $request->get('division_id');
        $academicYearId = $request->get('academic_year_id', AcademicYear::getCurrentAcademicYearId());
        $status = $request->get('status', 'active');
        $dayOfWeek = $request->get('day_of_week');
        $teacherId = $request->get('teacher_id');
        $search = $request->get('search');
        // Default to today's date if no date is selected
        $selectedDate = $request->get('date', date('Y-m-d'));

        // Get all divisions for the dropdown
        $divisions = $this->getAccessibleDivisions();
        
        // Build the query - show all entries by default
        $query = Timetable::withRelationships()
            ->byAcademicYear($academicYearId)
            ->notBreakTime()
            ->ordered();
        
        // Apply filters if provided
        if ($divisionId) {
            $query->byDivision($divisionId);
        }
        
        if ($status) {
            $query->byStatus($status);
        }
        
        if ($dayOfWeek) {
            $query->byDay($dayOfWeek);
        }
        
        if ($teacherId) {
            $query->byTeacher($teacherId);
        }
        
        if ($search) {
            $query->search($search);
        }

        if ($selectedDate) {
            // Get the day of week from the date using Carbon
            $dayOfWeek = \Carbon\Carbon::parse($selectedDate)->format('l');
            $query->byDateOrDay($selectedDate, strtolower($dayOfWeek));
        }

        // Get paginated results
        $timetables = $query->paginate(20)->appends($request->query());
        
        // Get selected division if any
        $selectedDivision = $divisionId ? Division::find($divisionId) : null;

        // Get filter options
        $teachers = User::role('teacher')->where('is_active', true)->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('year_number', 'desc')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $programs = \App\Models\Academic\Program::where('is_active', true)->get();

        return view('academic.timetable.grid', compact(
            'timetables',
            'divisions',
            'selectedDivision',
            'selectedDate',
            'teachers',
            'academicYears',
            'subjects',
            'programs'
        ));
    }

    /**
     * AJAX endpoint to check if a date is a holiday for timetable
     */
    public function checkHoliday(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = Carbon::parse($request->date);
        $academicYearId = AcademicYear::getCurrentAcademicYearId();

        $holidayCheck = $this->holidayService->checkTimetableAvailability($date, $academicYearId);

        return response()->json($holidayCheck);
    }

    /**
     * Get timetable for a specific date with holiday check
     */
    public function getByDate(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'division_id' => 'nullable|exists:divisions,id',
        ]);

        $date = Carbon::parse($request->date);
        $academicYearId = AcademicYear::getCurrentAcademicYearId();

        // First check if it's a holiday
        $holidayCheck = $this->holidayService->checkTimetableAvailability($date, $academicYearId);

        if ($holidayCheck['status'] === 'holiday') {
            return response()->json($holidayCheck);
        }

        // If not a holiday, get the timetable
        $query = Timetable::withRelationships()
            ->byAcademicYear($academicYearId)
            ->byStatus('active')
            ->notBreakTime();

        if ($request->filled('division_id')) {
            $query->byDivision($request->division_id);
        }

        $dayName = $date->format('l'); // e.g., 'Monday'
        $timetables = $query->byDay(strtolower($dayName))->ordered()->get();

        return response()->json([
            'status' => 'active',
            'available' => true,
            'message' => 'Timetable loaded successfully',
            'date' => $date->format('Y-m-d'),
            'day' => $dayName,
            'periods' => $timetables,
        ]);
    }

    /**
     * ============================================================
     * CRUD OPERATIONS
     * ============================================================
     */

    /**
     * Show create form
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        // Check permission for admin/principal
        $user = Auth::user();
        $isAdminOrPrincipal = $user->hasAnyRole(['admin', 'principal']);
        $isTeacher = $user->hasAnyRole(['teacher', 'class_teacher', 'subject_teacher']);
        
        if (!$isAdminOrPrincipal && !$isTeacher) {
            abort(403, 'Only admins, principals and teachers can create timetables.');
        }

        // Get divisions based on user role
        if ($isAdminOrPrincipal) {
            // Admin/Principal sees all divisions
            $divisions = Division::where('is_active', true)->get();
        } else {
            // Teachers only see their assigned divisions
            $divisionIds = \App\Models\TeacherAssignment::where('teacher_id', $user->id)
                ->where('assignment_type', 'division')
                ->pluck('division_id');
            $divisions = Division::whereIn('id', $divisionIds)->where('is_active', true)->get();
        }
        
        $subjects = Subject::where('is_active', true)->get();
        $teachers = User::role('teacher')->where('is_active', true)->get();
        $timeSlots = TimeSlot::orderBy('start_time')->get();
        $academicYears = AcademicYear::where('is_active', true)->orderBy('year_number', 'desc')->get();
        $rooms = Room::where('status', Room::STATUS_AVAILABLE)->get();

        // Pre-select division if provided
        $selectedDivisionId = $request->get('division_id');

        return view('academic.timetable.create', compact(
            'divisions', 'subjects', 'teachers', 'timeSlots', 
            'academicYears', 'rooms', 'selectedDivisionId'
        ))->with('days', $this->days);
    }

    /**
     * Store new timetable entry
     *
     * @param StoreTimetableRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreTimetableRequest $request)
    {
        // Check permission
        $user = Auth::user();
        if (!$user->hasAnyRole(['admin', 'principal', 'teacher', 'class_teacher', 'subject_teacher'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            // Parse date if provided
            $date = $request->filled('date') ? Carbon::parse($request->date)->format('Y-m-d') : null;

            // Check if date is a holiday
            if ($date) {
                $holidayCheck = $this->holidayService->validateAttendanceDate($date, $request->academic_year_id);
                
                if ($holidayCheck['is_holiday']) {
                    DB::rollBack();
                    return back()
                        ->withInput()
                        ->with('error', 'This date is marked as Holiday. Attendance and Timetable cannot be added.');
                }
            }

            // Get time values from time_slot_id or direct input
            $startTime = $request->start_time;
            $endTime = $request->end_time;
            
            if ($request->filled('time_slot_id')) {
                $timeSlot = TimeSlot::find($request->time_slot_id);
                if ($timeSlot) {
                    $startTime = $timeSlot->start_time;
                    $endTime = $timeSlot->end_time;
                }
            }

            // If date is provided but day_of_week is not, derive day_of_week from it
            $dayOfWeek = $request->day_of_week;
            if (!$dayOfWeek && $request->filled('date')) {
                $dayOfWeek = Carbon::parse($request->date)->format('l');
            }
            $dayOfWeek = strtolower($dayOfWeek);

            // Validate we have either day_of_week or date
            if (empty($dayOfWeek) && empty($date)) {
                return back()
                    ->withInput()
                    ->with('error', 'Please select either a day of the week or a specific date.');
            }

            // Validate we have the required time values
            if (empty($startTime) || empty($endTime)) {
                return back()
                    ->withInput()
                    ->with('error', 'Please provide either a time slot or start/end times.');
            }

            // Check for conflicts before creating
            $conflicts = $this->checkTimetableConflicts(
                $request->division_id,
                $request->teacher_id,
                $request->room_id,
                $date,
                strtolower($dayOfWeek),
                $startTime,
                $endTime
            );

            if ($conflicts['has_conflicts']) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->with('error', 'Schedule conflict detected: ' . implode(', ', $conflicts['messages']));
            }

            $timetable = Timetable::create([
                'division_id' => $request->division_id,
                'subject_id' => $request->subject_id,
                'teacher_id' => $request->teacher_id,
                'room_id' => $request->room_id,
                'day_of_week' => strtolower($dayOfWeek),
                'date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'period_name' => $request->period_name,
                'room_number' => $request->room_number,
                'academic_year_id' => $request->academic_year_id,
                'status' => $request->status ?? Timetable::STATUS_ACTIVE,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return redirect()->route('academic.timetable.grid')
                ->with('success', 'Timetable entry created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('Timetable creation failed (Database): ' . $e->getMessage());
            
            // Provide more helpful error messages for common issues
            $errorMessage = 'Failed to create timetable entry. Please check your input and try again.';
            
            if ($e->getCode() === '23000') {
                // Foreign key constraint violation
                if (str_contains($e->getMessage(), 'division_id')) {
                    $errorMessage = 'Invalid division selected. Please refresh the page and try again.';
                } elseif (str_contains($e->getMessage(), 'subject_id')) {
                    $errorMessage = 'Invalid subject selected. Please refresh the page and try again.';
                } elseif (str_contains($e->getMessage(), 'teacher_id')) {
                    $errorMessage = 'Invalid teacher selected. Please refresh the page and try again.';
                } elseif (str_contains($e->getMessage(), 'room_id')) {
                    $errorMessage = 'Invalid room selected. Please refresh the page and try again.';
                } elseif (str_contains($e->getMessage(), 'academic_year_id')) {
                    $errorMessage = 'Invalid academic year. Please refresh the page and try again.';
                }
            }
            
            return back()->with('error', $errorMessage)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Timetable creation failed: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Failed to create timetable entry: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Check for timetable conflicts
     */
    private function checkTimetableConflicts(
        int $divisionId,
        int $teacherId,
        ?int $roomId,
        ?string $date,
        string $dayOfWeek,
        string $startTime,
        string $endTime,
        ?int $excludeId = null
    ): array {
        $conflicts = [];
        $messages = [];

        // Build base query
        $baseQuery = Timetable::where('status', 'active')
            ->where('is_break_time', false);

        if ($excludeId) {
            $baseQuery->where('id', '!=', $excludeId);
        }

        // Check division conflict
        $divisionConflict = (clone $baseQuery)
            ->where('division_id', $divisionId)
            ->where(function ($q) use ($date, $dayOfWeek) {
                if ($date) {
                    $q->whereDate('date', $date);
                } else {
                    $q->where('day_of_week', $dayOfWeek);
                }
            })
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($q2) use ($startTime, $endTime) {
                    $q2->where('start_time', '<', $endTime)
                       ->where('end_time', '>', $startTime);
                });
            })
            ->exists();

        if ($divisionConflict) {
            $conflicts[] = 'division';
            $messages[] = 'Division already has a class at this time';
        }

        // Check teacher conflict
        $teacherConflict = (clone $baseQuery)
            ->where('teacher_id', $teacherId)
            ->where(function ($q) use ($date, $dayOfWeek) {
                if ($date) {
                    $q->whereDate('date', $date);
                } else {
                    $q->where('day_of_week', $dayOfWeek);
                }
            })
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($q2) use ($startTime, $endTime) {
                    $q2->where('start_time', '<', $endTime)
                       ->where('end_time', '>', $startTime);
                });
            })
            ->exists();

        if ($teacherConflict) {
            $conflicts[] = 'teacher';
            $messages[] = 'Teacher is already scheduled for another class at this time';
        }

        // Check room conflict
        if ($roomId) {
            $roomConflict = (clone $baseQuery)
                ->where('room_id', $roomId)
                ->where(function ($q) use ($date, $dayOfWeek) {
                    if ($date) {
                        $q->whereDate('date', $date);
                    } else {
                        $q->where('day_of_week', $dayOfWeek);
                    }
                })
                ->where(function ($q) use ($startTime, $endTime) {
                    $q->where(function ($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '<', $endTime)
                           ->where('end_time', '>', $startTime);
                    });
                })
                ->exists();

            if ($roomConflict) {
                $conflicts[] = 'room';
                $messages[] = 'Room is already booked at this time';
            }
        }

        return [
            'has_conflicts' => count($conflicts) > 0,
            'conflicts' => $conflicts,
            'messages' => $messages,
        ];
    }

    /**
     * Show single timetable entry
     * 
     * @param Timetable $timetable
     * @return \Illuminate\View\View
     */
    public function show(Timetable $timetable)
    {
        $timetable->load(['division', 'subject', 'teacher', 'room', 'academicYear']);
        
        // Check for any conflicts
        $conflicts = $timetable->getConflicts();

        return view('academic.timetable.show', compact('timetable', 'conflicts'));
    }

    /**
     * Show edit form
     * 
     * @param Timetable $timetable
     * @return \Illuminate\View\View
     */
    public function edit(Timetable $timetable)
    {
        // Check permission
        if (!Auth::user()->hasAnyRole(['admin', 'principal'])) {
            abort(403, 'Only admins and principals can edit timetables.');
        }

        $divisions = Division::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $teachers = User::role('teacher')->where('is_active', true)->get();
        $timeSlots = TimeSlot::orderBy('start_time')->get();
        $academicYears = AcademicYear::where('is_active', true)->orderBy('year_number', 'desc')->get();
        $rooms = Room::where('status', Room::STATUS_AVAILABLE)->get();

        return view('academic.timetable.edit', compact(
            'timetable', 'divisions', 'subjects', 'teachers', 
            'timeSlots', 'academicYears', 'rooms'
        ))->with('days', $this->days);
    }

    /**
     * Update timetable entry
     * 
     * @param UpdateTimetableRequest $request
     * @param Timetable $timetable
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateTimetableRequest $request, Timetable $timetable)
    {
        // Check permission
        if (!Auth::user()->hasAnyRole(['admin', 'principal'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            // Get time values
            $timeSlot = TimeSlot::find($request->time_slot_id);
            
            // Parse date if provided
            $date = $request->filled('date') ? Carbon::parse($request->date)->format('Y-m-d') : null;
            
            // Check if date is a holiday
            if ($date) {
                $holidayCheck = $this->holidayService->validateAttendanceDate($date, $request->academic_year_id);
                
                if ($holidayCheck['is_holiday']) {
                    DB::rollBack();
                    return back()
                        ->withInput()
                        ->with('error', 'Selected date is a holiday. Timetable and Attendance cannot be added. Holiday: ' . ($holidayCheck['holiday_title'] ?? 'Holiday'));
                }
            }
            
            // If date is provided, derive day_of_week from it
            $dayOfWeek = $date ? Carbon::parse($date)->format('l') : $request->day_of_week;

            $timetable->update([
                'division_id' => $request->division_id,
                'subject_id' => $request->subject_id,
                'teacher_id' => $request->teacher_id,
                'room_id' => $request->room_id,
                'day_of_week' => strtolower($dayOfWeek),
                'date' => $date,
                'start_time' => $timeSlot?->start_time ?? $request->start_time,
                'end_time' => $timeSlot?->end_time ?? $request->end_time,
                'period_name' => $request->period_name,
                'room_number' => $request->room_number,
                'academic_year_id' => $request->academic_year_id,
                'status' => $request->status ?? $timetable->status,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return redirect()->route('academic.timetable.grid')
                ->with('success', 'Timetable entry updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Timetable update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update timetable entry.')->withInput();
        }
    }

    /**
     * Delete timetable entry
     * 
     * @param Timetable $timetable
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Timetable $timetable)
    {
        // Check permission
        if (!Auth::user()->hasAnyRole(['admin', 'principal'])) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return back()->with('error', 'Unauthorized to delete timetable entries.');
        }

        try {
            $timetableName = $timetable->subject->name ?? 'Entry';
            $timetable->delete();

            // Check if request is AJAX
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Timetable entry deleted successfully!']);
            }

            // Redirect back to grid view if that's where the request came from
            return redirect()->route('academic.timetable.grid')
                ->with('success', $timetableName . ' deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Timetable deletion failed: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json(['error' => 'Failed to delete timetable entry.'], 500);
            }
            
            return back()->with('error', 'Failed to delete timetable entry.');
        }
    }

    /**
     * ============================================================
     * AJAX METHODS
     * ============================================================
     */

    /**
     * Get timetable data for AJAX calls (for modals)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxGetTimetable(Request $request): JsonResponse
    {
        $timetableId = $request->get('id');

        // If ID is provided, get single timetable entry (for edit modal)
        if ($timetableId) {
            $timetable = Timetable::withRelationships()->find($timetableId);

            if (!$timetable) {
                return response()->json(['error' => 'Timetable not found'], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $timetable->id,
                    'division_id' => $timetable->division_id,
                    'division_name' => $timetable->division->division_name ?? 'N/A',
                    'subject_id' => $timetable->subject_id,
                    'subject_name' => $timetable->subject->name ?? 'N/A',
                    'teacher_id' => $timetable->teacher_id,
                    'teacher_name' => $timetable->teacher->name ?? 'No Teacher',
                    'room_id' => $timetable->room_id,
                    'room_name' => $timetable->room->room_number ?? $timetable->room_number ?? 'N/A',
                    'day_of_week' => $timetable->day_of_week,
                    'day_name' => $timetable->day_name,
                    'date' => $timetable->date?->format('Y-m-d'),
                    'start_time' => $timetable->start_time,
                    'end_time' => $timetable->end_time,
                    'formatted_time' => $timetable->formatted_time_range,
                    'period_name' => $timetable->period_name,
                    'status' => $timetable->status,
                    'notes' => $timetable->notes,
                ]
            ]);
        }

        // Otherwise, get timetables by division and date (for conflict check)
        $divisionId = $request->get('division_id');
        $date = $request->get('date');

        if (!$divisionId) {
            return response()->json(['error' => 'Division ID or Timetable ID required'], 400);
        }

        $dayOfWeek = $date ? strtolower(Carbon::parse($date)->format('l')) : null;

        $query = Timetable::withRelationships()
            ->byDivision($divisionId)
            ->byStatus('active')
            ->notBreakTime();

        if ($date) {
            $query->byDateOrDay($date, $dayOfWeek);
        }

        $timetables = $query->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $timetables->map(fn($t) => [
                'id' => $t->id,
                'subject_id' => $t->subject_id,
                'subject_name' => $t->subject->name ?? 'N/A',
                'teacher_id' => $t->teacher_id,
                'teacher_name' => $t->teacher->name ?? 'N/A',
                'room_name' => $t->room->room_number ?? $t->room_number ?? 'N/A',
                'day_of_week' => $t->day_of_week,
                'start_time' => $t->start_time,
                'end_time' => $t->end_time,
                'period_name' => $t->period_name,
                'status' => $t->status,
            ])
        ]);
    }

    /**
     * Get available time slots for a division and day (for AJAX)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxGetAvailableSlots(Request $request): JsonResponse
    {
        $divisionId = $request->get('division_id');
        $dayOfWeek = $request->get('day');
        $excludeId = $request->get('exclude_id');

        if (!$divisionId || !$dayOfWeek) {
            return response()->json(['error' => 'Division and day required'], 400);
        }

        // Get all time slots
        $allSlots = TimeSlot::orderBy('start_time')->get();

        // Get booked slots for this division and day
        $bookedSlots = Timetable::byDivision($divisionId)
            ->byDay($dayOfWeek)
            ->where('status', '!=', Timetable::STATUS_CANCELLED)
            ->where('is_break_time', false)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->get(['start_time', 'end_time']);

        // Filter available slots
        $availableSlots = $allSlots->filter(function ($slot) use ($bookedSlots) {
            $slotStart = $slot->start_time;
            $slotEnd = $slot->end_time;

            foreach ($bookedSlots as $booked) {
                // Check if times overlap
                if ($slotStart < $booked->end_time && $slotEnd > $booked->start_time) {
                    return false;
                }
            }
            return true;
        });

        return response()->json([
            'success' => true,
            'data' => $availableSlots->values()
        ]);
    }

    /**
     * Update timetable status (AJAX)
     * 
     * @param Request $request
     * @param Timetable $timetable
     * @return JsonResponse
     */
    public function ajaxUpdateStatus(Request $request, Timetable $timetable): JsonResponse
    {
        // Check permission
        if (!Auth::user()->hasAnyRole(['admin', 'principal'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $status = $request->get('status');

        if (!in_array($status, [Timetable::STATUS_ACTIVE, Timetable::STATUS_CANCELLED, Timetable::STATUS_COMPLETED])) {
            return response()->json(['error' => 'Invalid status'], 400);
        }

        try {
            $timetable->update(['status' => $status]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'data' => ['status' => $status]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update status'], 500);
        }
    }

    /**
     * Store timetable entry via AJAX (for grid modal)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxStore(Request $request): JsonResponse
    {
        // Check permission
        if (!Auth::user()->hasAnyRole(['admin', 'principal'])) {
            return response()->json(['error' => 'Unauthorized', 'message' => 'Only admins and principals can create timetables.'], 403);
        }

        try {
            DB::beginTransaction();

            // Parse date if provided
            $date = $request->filled('date') ? Carbon::parse($request->date)->format('Y-m-d') : null;

            // Check if date is a holiday
            if ($date) {
                $holidayCheck = $this->holidayService->validateAttendanceDate($date, $request->academic_year_id);
                
                if ($holidayCheck['is_holiday']) {
                    DB::rollBack();
                    return response()->json([
                        'error' => true,
                        'message' => 'This date is marked as Holiday. Timetable cannot be added. Holiday: ' . ($holidayCheck['holiday_title'] ?? 'Holiday'),
                        'errors' => ['date' => ['This date is marked as Holiday.']]
                    ], 422);
                }
            }

            // Get time values
            $timeSlot = TimeSlot::find($request->time_slot_id);

            // If date is provided but day_of_week is not, derive day_of_week from it
            $dayOfWeek = $request->day_of_week;
            if (!$dayOfWeek && $request->filled('date')) {
                $dayOfWeek = Carbon::parse($request->date)->format('l');
            }
            $dayOfWeek = strtolower($dayOfWeek);

            // Check for conflicts before creating
            $conflicts = $this->checkTimetableConflicts(
                $request->division_id,
                $request->teacher_id,
                $request->room_id,
                $date,
                strtolower($dayOfWeek),
                $timeSlot?->start_time ?? $request->start_time,
                $timeSlot?->end_time ?? $request->end_time
            );

            if ($conflicts['has_conflicts']) {
                DB::rollBack();
                return response()->json([
                    'error' => true,
                    'message' => 'Schedule conflict detected: ' . implode(', ', $conflicts['messages']),
                    'errors' => $conflicts['errors'] ?? []
                ], 422);
            }

            $timetable = Timetable::create([
                'division_id' => $request->division_id,
                'subject_id' => $request->subject_id,
                'teacher_id' => $request->teacher_id,
                'room_id' => $request->room_id,
                'day_of_week' => strtolower($dayOfWeek),
                'date' => $date,
                'start_time' => $timeSlot?->start_time ?? $request->start_time,
                'end_time' => $timeSlot?->end_time ?? $request->end_time,
                'period_name' => $request->period_name,
                'room_number' => $request->room_number,
                'academic_year_id' => $request->academic_year_id,
                'status' => $request->status ?? Timetable::STATUS_ACTIVE,
                'notes' => $request->notes,
            ]);

            $timetable->load(['division', 'subject', 'teacher', 'room']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Timetable entry created successfully!',
                'data' => [
                    'id' => $timetable->id,
                    'subject_name' => $timetable->subject->name ?? 'N/A',
                    'teacher_name' => $timetable->teacher->name ?? 'No Teacher',
                    'room_name' => $timetable->room->room_number ?? $timetable->room_number ?? 'TBA',
                    'day_of_week' => $timetable->day_of_week,
                    'start_time' => $timetable->start_time,
                    'end_time' => $timetable->end_time,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => true,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('AJAX Timetable creation failed: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Failed to create timetable entry: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update timetable entry via AJAX (for grid modal)
     * 
     * @param Request $request
     * @param Timetable $timetable
     * @return JsonResponse
     */
    public function ajaxUpdate(Request $request, Timetable $timetable): JsonResponse
    {
        // Check permission
        if (!Auth::user()->hasAnyRole(['admin', 'principal'])) {
            return response()->json(['error' => 'Unauthorized', 'message' => 'Only admins and principals can edit timetables.'], 403);
        }

        try {
            DB::beginTransaction();

            // Get time values
            $timeSlot = TimeSlot::find($request->time_slot_id);
            
            // Parse date if provided
            $date = $request->filled('date') ? Carbon::parse($request->date)->format('Y-m-d') : null;
            
            // Check if date is a holiday
            if ($date) {
                $holidayCheck = $this->holidayService->validateAttendanceDate($date, $request->academic_year_id);
                
                if ($holidayCheck['is_holiday']) {
                    DB::rollBack();
                    return response()->json([
                        'error' => true,
                        'message' => 'Selected date is a holiday. Timetable cannot be added.',
                        'errors' => ['date' => ['This date is marked as Holiday.']]
                    ], 422);
                }
            }
            
            // If date is provided, derive day_of_week from it
            $dayOfWeek = $date ? Carbon::parse($date)->format('l') : $request->day_of_week;

            $timetable->update([
                'division_id' => $request->division_id,
                'subject_id' => $request->subject_id,
                'teacher_id' => $request->teacher_id,
                'room_id' => $request->room_id,
                'day_of_week' => strtolower($dayOfWeek),
                'date' => $date,
                'start_time' => $timeSlot?->start_time ?? $request->start_time,
                'end_time' => $timeSlot?->end_time ?? $request->end_time,
                'period_name' => $request->period_name,
                'room_number' => $request->room_number,
                'academic_year_id' => $request->academic_year_id,
                'status' => $request->status ?? $timetable->status,
                'notes' => $request->notes,
            ]);

            $timetable->load(['division', 'subject', 'teacher', 'room']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Timetable entry updated successfully!',
                'data' => [
                    'id' => $timetable->id,
                    'subject_name' => $timetable->subject->name ?? 'N/A',
                    'teacher_name' => $timetable->teacher->name ?? 'No Teacher',
                    'room_name' => $timetable->room->room_number ?? $timetable->room_number ?? 'TBA',
                    'day_of_week' => $timetable->day_of_week,
                    'start_time' => $timetable->start_time,
                    'end_time' => $timetable->end_time,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => true,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('AJAX Timetable update failed: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Failed to update timetable entry: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete timetable entry via AJAX (for grid modal)
     * 
     * @param Timetable $timetable
     * @return JsonResponse
     */
    public function ajaxDestroy(Timetable $timetable): JsonResponse
    {
        // Check permission
        if (!Auth::user()->hasAnyRole(['admin', 'principal'])) {
            return response()->json(['error' => 'Unauthorized', 'message' => 'Only admins and principals can delete timetables.'], 403);
        }

        try {
            $timetableName = $timetable->subject->name ?? 'Class';
            $timetable->delete();

            return response()->json([
                'success' => true,
                'message' => "{$timetableName} deleted successfully!"
            ]);
        } catch (\Exception $e) {
            Log::error('AJAX Timetable deletion failed: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Failed to delete timetable entry: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ============================================================
     * IMPORT / EXPORT METHODS
     * ============================================================
     */

    /**
     * Export timetable to PDF
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        try {
            $divisionId = $request->get('division_id');
            $academicYearId = $request->get('academic_year_id', AcademicYear::getCurrentAcademicYearId());

            // If no academic year found, use first active one
            if (!$academicYearId) {
                $academicYear = AcademicYear::where('is_active', true)->first();
                $academicYearId = $academicYear?->id;
            }

            $query = Timetable::withRelationships()
                ->byAcademicYear($academicYearId)
                ->byStatus('active')
                ->notBreakTime()
                ->ordered();

            if ($divisionId) {
                $query->byDivision($divisionId);
            }

            $timetables = $query->get();
            $division = $divisionId ? Division::find($divisionId) : null;

            // Load PDF library and generate
            $pdf = \PDF::loadView('academic.timetable.pdf', [
                'timetables' => $timetables,
                'division' => $division,
                'days' => $this->days
            ]);

            $filename = $division 
                ? "timetable_{$division->division_name}_" . date('Ymd') . ".pdf"
                : "timetable_" . date('Ymd') . ".pdf";

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('PDF Export failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Show import form
     * 
     * @return \Illuminate\View\View
     */
    public function importForm()
    {
        // Check permission
        if (!Auth::user()->hasAnyRole(['admin', 'principal'])) {
            abort(403, 'Only admins and principals can import timetables.');
        }

        $academicYears = AcademicYear::where('is_active', true)->orderBy('year_number', 'desc')->get();
        $divisions = Division::where('is_active', true)->get();

        return view('academic.timetable.import', compact('academicYears', 'divisions'));
    }

    /**
     * Import timetable from Excel
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importExcel(Request $request)
    {
        // Check permission
        if (!Auth::user()->hasAnyRole(['admin', 'principal'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('file');
            $academicYearId = $request->academic_year_id;

            // Use Laravel Excel to read file
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->path());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            array_shift($rows);

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                try {
                    // Expected columns: day, start_time, end_time, subject, teacher, room, division
                    $day = strtolower(trim($row[0] ?? ''));
                    $startTime = trim($row[1] ?? '');
                    $endTime = trim($row[2] ?? '');
                    $subjectCode = trim($row[3] ?? '');
                    $teacherName = trim($row[4] ?? '');
                    $roomNumber = trim($row[5] ?? '');
                    $divisionName = trim($row[6] ?? '');

                    if (!$day || !$startTime || !$subjectCode) {
                        continue; // Skip empty rows
                    }

                    // Find subject
                    $subject = Subject::where('code', $subjectCode)->first();
                    if (!$subject) {
                        $errorCount++;
                        $errors[] = "Row " . ($index + 2) . ": Subject '{$subjectCode}' not found";
                        continue;
                    }

                    // Find teacher
                    $teacher = User::where('name', 'like', "%{$teacherName}%")->role('teacher')->first();
                    if (!$teacher) {
                        $errorCount++;
                        $errors[] = "Row " . ($index + 2) . ": Teacher '{$teacherName}' not found";
                        continue;
                    }

                    // Find division
                    $division = Division::where('division_name', 'like', "%{$divisionName}%")->first();
                    if (!$division) {
                        $errorCount++;
                        $errors[] = "Row " . ($index + 2) . ": Division '{$divisionName}' not found";
                        continue;
                    }

                    // Find or create room
                    $room = null;
                    if ($roomNumber) {
                        $room = Room::firstOrCreate(
                            ['room_number' => $roomNumber],
                            ['name' => $roomNumber, 'room_type' => Room::TYPE_CLASSROOM, 'status' => Room::STATUS_AVAILABLE]
                        );
                    }

                    // Check for conflicts
                    if (Timetable::checkDivisionConflict($division->id, $day, $startTime, $endTime)) {
                        $errorCount++;
                        $errors[] = "Row " . ($index + 2) . ": Division conflict - {$divisionName} on {$day} at {$startTime}";
                        continue;
                    }

                    if (Timetable::checkTeacherConflict($teacher->id, $day, $startTime, $endTime)) {
                        $errorCount++;
                        $errors[] = "Row " . ($index + 2) . ": Teacher conflict - {$teacherName} on {$day} at {$startTime}";
                        continue;
                    }

                    // Create timetable entry
                    Timetable::create([
                        'division_id' => $division->id,
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'room_id' => $room?->id,
                        'day_of_week' => $day,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'room_number' => $roomNumber,
                        'academic_year_id' => $academicYearId,
                        'status' => Timetable::STATUS_ACTIVE,
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            DB::commit();

            if ($errorCount > 0) {
                return back()->with('warning', "Imported {$successCount} entries. {$errorCount} errors:\n" . implode("\n", array_slice($errors, 0, 10)));
            }

            return redirect()->route('academic.timetable.index')
                ->with('success', "Successfully imported {$successCount} timetable entries!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Timetable import failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to import timetable: ' . $e->getMessage());
        }
    }

    /**
     * ============================================================
     * COPY TO NEXT SESSION
     * ============================================================
     */

    /**
     * Show copy to next session form
     * 
     * @return \Illuminate\View\View
     */
    public function copyToNextSessionForm()
    {
        // Check permission
        if (!Auth::user()->hasAnyRole(['admin', 'principal'])) {
            abort(403, 'Only admins and principals can copy timetables.');
        }

        $currentAcademicYear = AcademicYear::getCurrentAcademicYear();
        $nextAcademicYears = AcademicYear::where('start_date', '>', $currentAcademicYear?->start_date ?? now())
            ->orderBy('start_date')
            ->get();

        $divisions = Division::where('is_active', true)->get();

        return view('academic.timetable.copy', compact('currentAcademicYear', 'nextAcademicYears', 'divisions'));
    }

    /**
     * Copy timetable to next academic session
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function copyToNextSession(Request $request)
    {
        // Check permission
        if (!Auth::user()->hasAnyRole(['admin', 'principal'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'source_academic_year_id' => 'required|exists:academic_years,id',
            'target_academic_year_id' => 'required|different:source_academic_year_id|exists:academic_years,id',
            'division_ids' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $sourceYearId = $request->source_academic_year_id;
            $targetYearId = $request->target_academic_year_id;
            $divisionIds = $request->division_ids;

            // Get source timetables
            $sourceTimetables = Timetable::byAcademicYear($sourceYearId)
                ->byStatus('active')
                ->whereIn('division_id', $divisionIds)
                ->get();

            $successCount = 0;
            $skippedCount = 0;
            $errorCount = 0;

            foreach ($sourceTimetables as $source) {
                // Check if already exists in target
                $exists = Timetable::byAcademicYear($targetYearId)
                    ->byDivision($source->division_id)
                    ->byDay($source->day_of_week)
                    ->where('start_time', $source->start_time)
                    ->where('end_time', $source->end_time)
                    ->exists();

                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                try {
                    $source->copyToAcademicYear($targetYearId);
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                }
            }

            DB::commit();

            return redirect()->route('academic.timetable.index')
                ->with('success', "Copied {$successCount} entries to next session. Skipped: {$skippedCount}, Errors: {$errorCount}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Copy to next session failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to copy timetable: ' . $e->getMessage());
        }
    }

    /**
     * ============================================================
     * LEGACY/EXISTING METHODS
     * ============================================================
     */

    /**
     * Legacy check availability method
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'day_of_week' => 'required',
            'time_slot_id' => 'required|exists:time_slots,id',
        ]);

        $timeSlot = TimeSlot::find($request->time_slot_id);
        $hasConflict = Timetable::checkDivisionConflict(
            $request->division_id,
            $request->day_of_week,
            $timeSlot->start_time,
            $timeSlot->end_time
        );

        return response()->json(['available' => !$hasConflict]);
    }

    /**
     * Legacy print method
     */
    public function print(Request $request, $divisionId)
    {
        $division = Division::findOrFail($divisionId);
        
        $timetables = Timetable::withRelationships()
            ->byDivision($divisionId)
            ->byStatus('active')
            ->notBreakTime()
            ->ordered()
            ->get()
            ->groupBy('day_of_week');

        return view('academic.timetable.print', compact('timetables', 'division'));
    }

    /**
     * Teacher's own timetable
     */
    public function teacherTimetable()
    {
        $teacher = Auth::user();
        
        $today = strtolower(date('l'));
        $todayClasses = Timetable::where('teacher_id', $teacher->id)
            ->where('day_of_week', $today)
            ->with(['division', 'subject', 'room'])
            ->orderBy('start_time')
            ->get();

        $weekClasses = Timetable::where('teacher_id', $teacher->id)
            ->with(['division', 'subject', 'room'])
            ->ordered()
            ->get()
            ->groupBy('day_of_week');

        return view('academic.timetable.teacher', compact('todayClasses', 'weekClasses', 'today'))->with('days', $this->days);
    }

    /**
     * ============================================================
     * HELPER METHODS
     * ============================================================
     */

    /**
     * Get divisions accessible to the current user
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getAccessibleDivisions(): \Illuminate\Database\Eloquent\Collection
    {
        $user = Auth::user();

        // Admins and principals can see all divisions
        if ($user->hasAnyRole(['admin', 'principal'])) {
            return Division::where('is_active', true)->get();
        }

        // Teachers can only see their assigned divisions
        $divisionIds = \App\Models\TeacherAssignment::where('teacher_id', $user->id)
            ->where('assignment_type', 'division')
            ->pluck('division_id');

        return Division::whereIn('id', $divisionIds)
            ->where('is_active', true)
            ->get();
    }
}
