<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attendance\Timetable;
use App\Models\Academic\Division;
use App\Models\Result\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    /**
     * Display timetable dashboard with master data overview
     */
    public function index(Request $request)
    {
        // Get all divisions
        $divisions = Division::where('is_active', true)
            ->with(['academicYear'])
            ->get();

        $selectedDivision = null;
        $timetables = collect();

        if ($request->filled('division_id')) {
            $selectedDivision = Division::with(['academicYear'])
                ->find($request->division_id);
            $timetables = Timetable::with(['division.academicYear', 'subject', 'teacher'])
                ->where('division_id', $request->division_id)
                ->orderByRaw("FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')")
                ->orderBy('start_time')
                ->get();
        }

        return view('academic.timetable.index', compact(
            'timetables',
            'divisions',
            'selectedDivision'
        ));
    }

    /**
     * Display timetable in table format
     */
    public function table(Request $request)
    {
        $divisions = Division::where('is_active', true)
            ->with(['academicYear'])
            ->get();

        $query = Timetable::with(['division.academicYear', 'subject', 'teacher']);

        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        if ($request->filled('day')) {
            $query->where('day_of_week', $request->day);
        }

        $timetables = $query->orderByRaw("FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')")
                           ->orderBy('start_time')
                           ->paginate(25);

        return view('academic.timetable.table', compact('timetables', 'divisions'));
    }

    /**
     * Show form to create new timetable entry
     */
    public function create()
    {
        $divisions = Division::where('is_active', true)
            ->with(['academicYear'])
            ->get();
        $subjects = Subject::where('is_active', true)->get();
        $teachers = User::role('teacher')->where('is_active', true)->get();
        $rooms = $this->getAvailableRooms();

        return view('academic.timetable.create', compact(
            'divisions',
            'subjects',
            'teachers',
            'rooms'
        ));
    }

    /**
     * Store new timetable entry
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
        ], [
            'division_id.required' => 'Please select a division.',
            'subject_id.required' => 'Please select a subject.',
            'teacher_id.required' => 'Please select a teacher.',
            'day_of_week.required' => 'Please select a day of week.',
            'start_time.required' => 'Start time is required.',
            'end_time.required' => 'End time is required.',
            'end_time.after' => 'End time must be after start time.',
        ]);

        // Check for duplicate entry
        $exists = Timetable::where('division_id', $validated['division_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where('start_time', $validated['start_time'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'start_time' => 'A class is already scheduled for this division on this day at this time.'
            ])->withInput();
        }

        Timetable::create($validated);

        return redirect()->route('academic.timetable.index')
            ->with('success', 'Timetable entry created successfully!');
    }

    /**
     * Show form to edit timetable entry
     */
    public function edit(Timetable $timetable)
    {
        $divisions = Division::where('is_active', true)
            ->with(['academicYear'])
            ->get();
        $subjects = Subject::where('is_active', true)->get();
        $teachers = User::role('teacher')->where('is_active', true)->get();
        $rooms = $this->getAvailableRooms();

        return view('academic.timetable.edit', compact(
            'timetable',
            'divisions',
            'subjects',
            'teachers',
            'rooms'
        ));
    }

    /**
     * Update existing timetable entry
     */
    public function update(Request $request, Timetable $timetable)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
        ]);

        // Check for duplicate entry (excluding current record)
        $exists = Timetable::where('division_id', $validated['division_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where('start_time', $validated['start_time'])
            ->where('id', '!=', $timetable->id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'start_time' => 'A class is already scheduled for this division on this day at this time.'
            ])->withInput();
        }

        $timetable->update($validated);

        return redirect()->route('academic.timetable.index')
            ->with('success', 'Timetable entry updated successfully!');
    }

    /**
     * Remove timetable entry
     */
    public function destroy(Timetable $timetable)
    {
        $timetable->delete();
        return redirect()->route('academic.timetable.index')
            ->with('success', 'Timetable entry deleted successfully!');
    }

    /**
     * Get list of available rooms
     */
    private function getAvailableRooms()
    {
        // Get rooms from existing timetables or use default list
        $rooms = Timetable::whereNotNull('room')
            ->distinct()
            ->pluck('room')
            ->toArray();

        // Add common room numbers if none exist
        if (empty($rooms)) {
            $rooms = [
                '101', '102', '103', '104', '105',
                '201', '202', '203', '204', '205',
                '301', '302', '303', '304', '305',
                'Lab 1', 'Lab 2', 'Lab 3', 'Lab 4',
                'Seminar Hall', 'Auditorium'
            ];
        }

        return $rooms;
    }

    /**
     * Get available teachers for a subject (AJAX endpoint)
     */
    public function getTeachersBySubject($subjectId)
    {
        $teachers = User::role('teacher')
            ->where('is_active', true)
            ->get(['id', 'name']);

        return response()->json($teachers);
    }

    /**
     * Check slot availability (AJAX endpoint)
     */
    public function checkSlotAvailability(Request $request)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
        ]);

        $query = Timetable::where('division_id', $validated['division_id'])
            ->where('day_of_week', $validated['day_of_week']);

        // Check for time overlap
        $conflict = $query->where(function($q) use ($validated) {
            $q->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
              ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
              ->orWhere(function($q2) use ($validated) {
                  $q2->where('start_time', '<=', $validated['start_time'])
                     ->where('end_time', '>=', $validated['end_time']);
              });
        })->exists();

        return response()->json([
            'available' => !$conflict,
            'message' => $conflict ? 'Time slot already occupied for this division.' : 'Slot is available.'
        ]);
    }
}
