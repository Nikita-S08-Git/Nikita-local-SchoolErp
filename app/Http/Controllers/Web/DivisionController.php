<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Academic\Division;
use App\Models\Academic\Program;
use App\Models\Academic\AcademicSession;
use App\Models\User;
use App\Models\User\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class DivisionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Division::with(['program', 'session', 'classTeacher'])
            ->withCount(['students' => fn($q) => $q->where('student_status', 'active')])
            ->when($request->filled('program_id'), fn($q) => $q->where('program_id', $request->program_id))
            ->when($request->filled('session_id'), fn($q) => $q->where('session_id', $request->session_id))
            ->when($request->filled('search'), fn($q) => $q->search($request->search))
            ->when($request->filled('status'), fn($q) => $q->where('is_active', $request->status === 'active'))
            ->latest();

        $divisions = $query->paginate(15)->appends($request->query());
        $programs = Program::where('is_active', true)->get();
        $sessions = AcademicSession::where('is_active', true)->get();

        return view('academic.divisions.index', compact('divisions', 'programs', 'sessions'));
    }

    public function create(): View
    {
        $programs = Program::where('is_active', true)->get();
        $sessions = AcademicSession::where('is_active', true)->get();
        $teachers = User::role('teacher')->get();

        return view('academic.divisions.create', compact('programs', 'sessions', 'teachers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'session_id' => 'required|exists:academic_sessions,id',
            'division_name' => 'required|string|max:10',
            'max_students' => 'required|integer|min:1|max:200',
            'class_teacher_id' => 'nullable|exists:users,id',
            'classroom' => 'nullable|string|max:50',
            'is_active' => 'boolean'
        ]);

        Division::create($validated);

        return redirect()->route('academic.divisions.index')->with('success', 'Division created successfully!');
    }

    public function show(Division $division): View
    {
        $division->load(['program', 'session', 'classTeacher', 'students' => fn($q) => $q->where('student_status', 'active')]);
        return view('academic.divisions.show', compact('division'));
    }

    public function edit(Division $division): View
    {
        $programs = Program::where('is_active', true)->get();
        $sessions = AcademicSession::where('is_active', true)->get();
        $teachers = User::role('teacher')->get();

        return view('academic.divisions.edit', compact('division', 'programs', 'sessions', 'teachers'));
    }

    public function update(Request $request, Division $division): RedirectResponse
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'session_id' => 'required|exists:academic_sessions,id',
            'division_name' => 'required|string|max:10',
            'max_students' => 'required|integer|min:1|max:200',
            'class_teacher_id' => 'nullable|exists:users,id',
            'classroom' => 'nullable|string|max:50',
            'is_active' => 'boolean'
        ]);

        $division->update($validated);

        return redirect()->route('academic.divisions.index')->with('success', 'Division updated successfully!');
    }

    public function toggleStatus(Division $division): RedirectResponse
    {
        $division->update(['is_active' => !$division->is_active]);
        return redirect()->back()->with('success', 'Division status updated!');
    }

    public function destroy(Division $division): RedirectResponse
    {
        if ($division->students()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete division with assigned students!');
        }

        $division->delete();
        return redirect()->route('academic.divisions.index')->with('success', 'Division deleted successfully!');
    }

    public function assignStudents(Request $request, Division $division): RedirectResponse
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        if (!$division->canAssignStudents($validated['student_ids'])) {
            return redirect()->back()->with('error', "Insufficient capacity! Available: {$division->available_seats}");
        }

        Student::whereIn('id', $validated['student_ids'])->update(['division_id' => $division->id]);

        return redirect()->back()->with('success', count($validated['student_ids']) . ' students assigned successfully!');
    }

    public function removeStudent(Division $division, Student $student): RedirectResponse
    {
        $student->update(['division_id' => null]);
        return redirect()->back()->with('success', 'Student removed from division!');
    }

    public function unassignedStudents(Request $request): JsonResponse
    {
        $students = Student::whereNull('division_id')
            ->where('student_status', 'active')
            ->when($request->filled('program_id'), fn($q) => $q->where('program_id', $request->program_id))
            ->select('id', 'first_name', 'last_name', 'admission_number')
            ->get();

        return response()->json($students);
    }
}
