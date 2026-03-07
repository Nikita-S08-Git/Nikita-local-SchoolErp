<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\Academic\AcademicYear;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Holiday Management Controller
 * 
 * Manages school holidays and events
 * Accessible by Principal and Admin
 */
class HolidayController extends Controller
{
    /**
     * Display a listing of holidays
     */
    public function index(Request $request): View
    {
        $query = Holiday::with(['academicYear', 'programIncharge'])
            ->latest('start_date');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by year
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $holidays = $query->paginate(15)->appends($request->query());
        $academicYears = AcademicYear::where('is_active', true)->orderBy('start_date', 'desc')->get();
        $teachers = User::role('teacher')->where('is_active', true)->get();

        return view('academic.holidays.index', compact('holidays', 'academicYears', 'teachers'));
    }

    /**
     * Show the form for creating a new holiday
     */
    public function create(): View
    {
        $academicYears = AcademicYear::where('is_active', true)->orderBy('start_date', 'desc')->get();
        $teachers = User::role('teacher')->where('is_active', true)->get();

        return view('academic.holidays.create', compact('academicYears', 'teachers'));
    }

    /**
     * Store a newly created holiday
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:public_holiday,school_holiday,event,program',
            'is_recurring' => 'boolean',
            'academic_year_id' => 'required|exists:academic_years,id',
            'program_incharge_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_recurring'] = $request->has('is_recurring');
        $validated['is_active'] = $request->has('is_active');

        Holiday::create($validated);

        return redirect()->route('academic.holidays.index')
            ->with('success', 'Holiday created successfully!');
    }

    /**
     * Display the specified holiday
     */
    public function show(Holiday $holiday): View
    {
        $holiday->load(['academicYear', 'programIncharge']);
        return view('academic.holidays.show', compact('holiday'));
    }

    /**
     * Show the form for editing the specified holiday
     */
    public function edit(Holiday $holiday): View
    {
        $academicYears = AcademicYear::where('is_active', true)->orderBy('start_date', 'desc')->get();
        $teachers = User::role('teacher')->where('is_active', true)->get();

        return view('academic.holidays.edit', compact('holiday', 'academicYears', 'teachers'));
    }

    /**
     * Update the specified holiday
     */
    public function update(Request $request, Holiday $holiday): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:public_holiday,school_holiday,event,program',
            'is_recurring' => 'boolean',
            'academic_year_id' => 'required|exists:academic_years,id',
            'program_incharge_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_recurring'] = $request->has('is_recurring');
        $validated['is_active'] = $request->has('is_active');

        $holiday->update($validated);

        return redirect()->route('academic.holidays.index')
            ->with('success', 'Holiday updated successfully!');
    }

    /**
     * Remove the specified holiday
     */
    public function destroy(Holiday $holiday): RedirectResponse
    {
        $holiday->delete();

        return redirect()->route('academic.holidays.index')
            ->with('success', 'Holiday deleted successfully!');
    }

    /**
     * Toggle holiday status
     */
    public function toggleStatus(Holiday $holiday): RedirectResponse
    {
        $holiday->update(['is_active' => !$holiday->is_active]);
        
        return redirect()->back()
            ->with('success', 'Holiday status updated!');
    }

    /**
     * Check if a date is a holiday (AJAX)
     */
    public function checkDate(Request $request): \Illuminate\Http\JsonResponse
    {
        $date = $request->input('date');
        $academicYearId = $request->input('academic_year_id');

        if (!$date) {
            return response()->json(['error' => 'Date required'], 400);
        }

        $isHoliday = Holiday::isDateHoliday(\Carbon\Carbon::parse($date), $academicYearId);
        $holidayTitle = $isHoliday ? Holiday::getHolidayTitle(\Carbon\Carbon::parse($date)) : null;

        return response()->json([
            'is_holiday' => $isHoliday,
            'title' => $holidayTitle,
        ]);
    }
}
