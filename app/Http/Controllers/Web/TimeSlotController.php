<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Academic\TimeSlot;
use App\Models\Academic\AcademicSession;
use App\Models\Academic\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Time Slot Controller
 *
 * Manages time slots for timetable scheduling
 */
class TimeSlotController extends Controller
{
    /**
     * Display a listing of time slots
     */
    public function index(): View
    {
        $timeSlots = TimeSlot::with(['academicSession', 'assignedRoom', 'assignedTeacher'])
            ->orderBy('sequence_order')
            ->orderBy('start_time')
            ->paginate(20);

        return view('academic.time-slots.index', compact('timeSlots'));
    }

    /**
     * Show the form for creating a new time slot
     */
    public function create(): View
    {
        $academicSessions = AcademicSession::where('is_active', true)->get();
        $rooms = Room::where('status', Room::STATUS_AVAILABLE)->get();
        $teachers = User::role('teacher')->where('is_active', true)->get();

        return view('academic.time-slots.create', compact('academicSessions', 'rooms', 'teachers'));
    }

    /**
     * Store a newly created time slot
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'slot_name' => 'required|string|max:50',
            'slot_code' => 'required|string|max:10|unique:time_slots',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_type' => 'required|in:instructional,break,assembly,exam,lab,tutorial,other',
            'sequence_order' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'is_break' => 'boolean',
            'break_type' => 'nullable|in:short_break,lunch,long_break',
            'academic_session_id' => 'nullable|exists:academic_sessions,id',
            'is_default' => 'boolean',
            'applicable_days' => 'nullable|array',
            'assigned_room_id' => 'nullable|exists:rooms,id',
            'requires_room' => 'boolean',
            'assigned_teacher_id' => 'nullable|exists:users,id',
            'available_for_classes' => 'boolean',
            'available_for_exams' => 'boolean',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_break'] = $request->has('is_break');
        $validated['is_default'] = $request->has('is_default');
        $validated['requires_room'] = $request->has('requires_room');
        $validated['available_for_classes'] = $request->has('available_for_classes');
        $validated['available_for_exams'] = $request->has('available_for_exams');
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        TimeSlot::create($validated);

        return redirect()->route('time-slots.index')
            ->with('success', 'Time slot created successfully!');
    }

    /**
     * Display the specified time slot
     */
    public function show(TimeSlot $timeSlot): View
    {
        $timeSlot->load(['academicSession', 'assignedRoom', 'assignedTeacher']);

        return view('academic.time-slots.show', compact('timeSlot'));
    }

    /**
     * Show the form for editing the specified time slot
     */
    public function edit(TimeSlot $timeSlot): View
    {
        $academicSessions = AcademicSession::where('is_active', true)->get();
        $rooms = Room::where('status', Room::STATUS_AVAILABLE)->get();
        $teachers = User::role('teacher')->where('is_active', true)->get();

        return view('academic.time-slots.edit', compact('timeSlot', 'academicSessions', 'rooms', 'teachers'));
    }

    /**
     * Update the specified time slot
     */
    public function update(Request $request, TimeSlot $timeSlot): RedirectResponse
    {
        $validated = $request->validate([
            'slot_name' => 'required|string|max:50',
            'slot_code' => 'required|string|max:10|unique:time_slots,slot_code,' . $timeSlot->id,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_type' => 'required|in:instructional,break,assembly,exam,lab,tutorial,other',
            'sequence_order' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'is_break' => 'boolean',
            'break_type' => 'nullable|in:short_break,lunch,long_break',
            'academic_session_id' => 'nullable|exists:academic_sessions,id',
            'is_default' => 'boolean',
            'applicable_days' => 'nullable|array',
            'assigned_room_id' => 'nullable|exists:rooms,id',
            'requires_room' => 'boolean',
            'assigned_teacher_id' => 'nullable|exists:users,id',
            'available_for_classes' => 'boolean',
            'available_for_exams' => 'boolean',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_break'] = $request->has('is_break');
        $validated['is_default'] = $request->has('is_default');
        $validated['requires_room'] = $request->has('requires_room');
        $validated['available_for_classes'] = $request->has('available_for_classes');
        $validated['available_for_exams'] = $request->has('available_for_exams');
        $validated['updated_by'] = auth()->id();

        $timeSlot->update($validated);

        return redirect()->route('time-slots.index')
            ->with('success', 'Time slot updated successfully!');
    }

    /**
     * Remove the specified time slot
     */
    public function destroy(TimeSlot $timeSlot): RedirectResponse
    {
        // Check if time slot is being used
        if ($timeSlot->timetables()->count() > 0) {
            return back()->with('error', 'Cannot delete time slot. It is being used in timetable entries.');
        }

        $timeSlot->delete();

        return redirect()->route('time-slots.index')
            ->with('success', 'Time slot deleted successfully!');
    }
}
