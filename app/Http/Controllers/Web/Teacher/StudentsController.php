<?php

namespace App\Http\Controllers\Web\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User\Student;
use Illuminate\Http\Request;

class StudentsController extends Controller
{
    /**
     * Display list of students in teacher's divisions
     */
    public function index(Request $request)
    {
        // Check authorization
        if (!auth()->check() || !auth()->user()->hasAnyRole(['teacher', 'class_teacher', 'subject_teacher', 'hod_commerce', 'hod_science', 'hod_management', 'hod_arts'])) {
            abort(403, 'Unauthorized access.');
        }

        $teacher = auth()->user();
        
        // Get assigned division IDs from teacher_assignments table
        $assignedDivisionIds = \App\Models\TeacherAssignment::where('teacher_id', $teacher->id)
            ->where('assignment_type', 'division')
            ->pluck('division_id');
        
        // If no divisions assigned, show empty result
        if ($assignedDivisionIds->isEmpty()) {
            $students = collect();
            return view('teacher.students.index', compact('students', 'assignedDivisionIds'));
        }

        $query = Student::whereIn('division_id', $assignedDivisionIds)
            ->where('student_status', 'active')
            ->whereNull('deleted_at')
            ->with(['division', 'user', 'studentProfile', 'attendances' => function ($q) {
                $q->where('date', '>=', now()->startOfMonth());
            }]);

        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(20);
        
        // Get division details for filter dropdown
        $assignedDivisions = \App\Models\Academic\Division::whereIn('id', $assignedDivisionIds)->get();

        return view('teacher.students.index', compact('students', 'assignedDivisions'));
    }

    /**
     * Display student details
     */
    public function show($studentId)
    {
        // Check authorization
        if (!auth()->check() || !auth()->user()->hasAnyRole(['teacher', 'class_teacher', 'subject_teacher', 'hod_commerce', 'hod_science', 'hod_management', 'hod_arts'])) {
            abort(403, 'Unauthorized access.');
        }

        $teacher = auth()->user();
        
        $student = Student::with([
            'division',
            'profile',
            'guardians',
            'attendances' => function ($q) {
                $q->latest()->limit(30);
            },
            'academicRecord'
        ])->findOrFail($studentId);

        // Verify teacher has access to this student's division
        $hasAccess = \App\Models\TeacherAssignment::where('teacher_id', $teacher->id)
            ->where('division_id', $student->division_id)
            ->where('assignment_type', 'division')
            ->exists();
        
        if (!$hasAccess) {
            abort(403, 'You do not have access to view this student.');
        }

        // Calculate attendance percentage
        $totalDays = $student->attendances->count();
        $presentDays = $student->attendances->where('status', 'present')->count();
        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;

        return view('teacher.students.show', compact('student', 'attendancePercentage'));
    }
}
