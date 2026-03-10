<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Result\Examination;
use App\Models\Academic\Subject;
use App\Models\Result\StudentMark;
use App\Models\Result\MarksDraft;
use App\Models\Academic\Division;
use App\Models\User\Student;
use App\Models\TeacherAssignment;
use App\Services\AcademicRuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExaminationController extends Controller
{
    public function index()
    {
        $examinations = Examination::with('subject')->latest()->paginate(15);
        return view('examinations.index', compact('examinations'));
    }

    /**
     * Show examinations list for teachers (with teacher layout)
     */
    public function teacherExaminations()
    {
        $examinations = Examination::where('is_active', true)
            ->latest()
            ->paginate(15);
        return view('teacher.examinations.index', compact('examinations'));
    }

    public function show(Examination $examination)
    {
        return view('examinations.show', compact('examination'));
    }

    public function edit(Examination $examination)
    {
        $subjects = Subject::where('is_active', true)->with('program')->get();
        return view('examinations.edit', compact('examination', 'subjects'));
    }

    public function update(Request $request, Examination $examination)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:midterm,final,unit_test,practical',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'academic_year' => 'required|string|max:20',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        $examination->update($validated);

        return redirect()->route('examinations.index')
            ->with('success', 'Examination updated successfully!');
    }

    public function create()
    {
        $subjects = Subject::where('is_active', true)->with('program')->get();
        return view('examinations.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:midterm,final,unit_test,practical',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'academic_year' => 'required|string|max:20',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        Examination::create($validated);

        return redirect()->route('examinations.index')
            ->with('success', 'Examination created successfully!');
    }

    public function marksEntry(Request $request, Examination $examination)
    {
        // Load subject from the exam (exam already has subject_id)
        $examination->load('subject');
        $subject = $examination->subject;
        
        $divisions = Division::where('is_active', true)->with('program')->get();
        
        $students = [];
        $marks = [];
        
        // Only require division selection now - subject comes from exam
        if ($request->has('division_id')) {
            $students = Student::where('division_id', $request->division_id)
                ->where('student_status', 'active')
                ->orderBy('roll_number')
                ->get();
            
            // Get marks using exam's subject_id
            if ($subject) {
                $marks = StudentMark::where('examination_id', $examination->id)
                    ->where('subject_id', $subject->id)
                    ->get()
                    ->keyBy('student_id');
            }
        }
        
        return view('examinations.marks-entry', compact('examination', 'divisions', 'subject', 'students', 'marks'));
    }

    public function getStudents(Request $request, Examination $examination)
    {
        $students = Student::where('division_id', $request->division_id)
            ->where('student_status', 'active')
            ->with(['marks' => function($q) use ($examination) {
                $q->where('examination_id', $examination->id);
            }])
            ->get();

        return view('examinations.students-list', compact('students', 'examination'));
    }

    public function saveMarks(Request $request, Examination $examination)
    {
        // Load subject from exam
        $examination->load('subject');
        
        if (!$examination->subject) {
            return redirect()->back()->with('error', 'This exam does not have a subject assigned. Please assign a subject to the exam first.');
        }
        
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'marks' => 'required|array',
        ]);

        foreach ($request->marks as $studentId => $marksObtained) {
            if ($marksObtained === null || $marksObtained === '') continue;
            
            $maxMarks = $request->max_marks ?? 100;
            $percentage = ($marksObtained / $maxMarks) * 100;
            $grade = $this->calculateGrade($percentage);
            $passPercentage = AcademicRuleService::getPassPercentage();
            $result = $percentage >= $passPercentage ? 'pass' : 'fail';

            StudentMark::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'examination_id' => $examination->id,
                    'subject_id' => $examination->subject->id,
                ],
                [
                    'marks_obtained' => $marksObtained,
                    'max_marks' => $maxMarks,
                    'grade' => $grade,
                    'result' => $result,
                    'is_approved' => true,
                ]
            );
        }

        return redirect()->route('examinations.marks-entry', [
            'examination' => $examination->id,
            'division_id' => $validated['division_id']
        ])->with('success', 'Marks saved successfully!');
    }

    private function calculateGrade($percentage)
    {
        $grade = \App\Models\Grade::getGradeForPercentage($percentage);
        return $grade ? $grade->grade_name : 'F';
    }

    public function destroy(Examination $examination)
    {
        $examination->delete();
        return redirect()->route('examinations.index')
            ->with('success', 'Examination deleted successfully!');
    }

    /**
     * Teacher Results - View all students' results for teacher's divisions
     */
    public function teacherResults(Request $request)
    {
        $teacher = Auth::user();
        
        // Get teacher's assigned divisions
        $divisionIds = TeacherAssignment::where('teacher_id', $teacher->id)
            ->where('assignment_type', 'division')
            ->pluck('division_id');
        
        $divisions = Division::whereIn('id', $divisionIds)
            ->with(['program', 'session'])
            ->get();
        
        // Get all active examinations
        $examinations = Examination::active()->get();
        
        $selectedDivision = null;
        $selectedExam = null;
        $results = collect();
        $students = collect();
        
        if ($request->filled('division_id') && $request->filled('examination_id')) {
            $selectedDivision = Division::find($request->division_id);
            $selectedExam = Examination::find($request->examination_id);
            
            // Verify teacher has access to this division
            if (!$divisionIds->contains($request->division_id)) {
                abort(403, 'You do not have access to this division.');
            }
            
            // Get students in the division
            $students = Student::where('division_id', $request->division_id)
                ->where('student_status', 'active')
                ->orderBy('roll_number')
                ->paginate(20);
            
            // Get ALL marks for the division (not just paginated students)
            // This ensures marks are available for all students regardless of page
            $results = StudentMark::where('examination_id', $request->examination_id)
                ->whereIn('student_id', Student::where('division_id', $request->division_id)
                    ->where('student_status', 'active')
                    ->pluck('id'))
                ->with(['subject'])
                ->get();
        }
        
        return view('teacher.results.index', compact(
            'divisions', 
            'examinations', 
            'selectedDivision', 
            'selectedExam',
            'results',
            'students'
        ));
    }

    /**
     * Division Results - View results for a specific division
     */
    public function divisionResults(Division $division)
    {
        $teacher = Auth::user();
        
        // Verify teacher has access to this division
        $hasAccess = TeacherAssignment::where('teacher_id', $teacher->id)
            ->where('division_id', $division->id)
            ->where('assignment_type', 'division')
            ->exists();
        
        if (!$hasAccess) {
            abort(403, 'You do not have access to this division.');
        }
        
        $students = Student::where('division_id', $division->id)
            ->where('student_status', 'active')
            ->with(['marks.subject', 'marks.examination'])
            ->orderBy('roll_number')
            ->get();
        
        $examinations = Examination::where('is_active', true)->get();
        
        return view('teacher.results.division', compact('division', 'students', 'examinations'));
    }

    /**
     * Enter Marks - Form to enter/edit marks for a division and examination
     */
    public function enterMarks(Request $request, $examinationId, $divisionId)
    {
        $teacher = Auth::user();
        
        // Verify teacher has access to this division
        $hasAccess = TeacherAssignment::where('teacher_id', $teacher->id)
            ->where('division_id', $divisionId)
            ->where('assignment_type', 'division')
            ->exists();
        
        if (!$hasAccess) {
            abort(403, 'You do not have access to this division.');
        }
        
        $examination = Examination::with('subject')->findOrFail($examinationId);
        $division = Division::with('program', 'session')->findOrFail($divisionId);
        
        // Get subject from exam - it's already assigned
        $subject = $examination->subject;
        
        $students = collect();
        $marks = collect();
        
        // Load students and marks if subject exists
        if ($subject) {
            $students = Student::where('division_id', $divisionId)
                ->where('student_status', 'active')
                ->orderBy('roll_number')
                ->paginate(20);
            
            $marks = StudentMark::where('examination_id', $examinationId)
                ->where('subject_id', $subject->id)
                ->get()
                ->keyBy('student_id');
        }
        
        return view('teacher.results.enter-marks', compact(
            'examination', 
            'division', 
            'subject',
            'students',
            'marks'
        ));
    }

    /**
     * Store Marks - Save marks entered by teacher
     */
    public function storeMarks(Request $request)
    {
        $validated = $request->validate([
            'examination_id' => 'required|exists:examinations,id',
            'marks' => 'required|array',
        ]);
        
        // Get the exam with its subject
        $examination = Examination::with('subject')->findOrFail($validated['examination_id']);
        
        if (!$examination->subject) {
            return redirect()->back()->with('error', 'This exam does not have a subject assigned. Please assign a subject to the exam first.');
        }
        
        $teacher = Auth::user();
        
        foreach ($request->marks as $studentId => $marksObtained) {
            if ($marksObtained === null || $marksObtained === '') continue;
            
            $maxMarks = $request->max_marks ?? 100;
            $percentage = ($marksObtained / $maxMarks) * 100;
            $grade = $this->calculateGrade($percentage);
            $passPercentage = AcademicRuleService::getPassPercentage();
            $result = $percentage >= $passPercentage ? 'pass' : 'fail';

            StudentMark::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'examination_id' => $validated['examination_id'],
                    'subject_id' => $examination->subject->id,
                ],
                [
                    'marks_obtained' => $marksObtained,
                    'max_marks' => $maxMarks,
                    'grade' => $grade,
                    'result' => $result,
                    'is_approved' => true,
                ]
            );
        }

        return redirect()->back()->with('success', 'Marks saved successfully!');
    }

    /**
     * Save draft marks - AJAX endpoint for auto-save
     */
    public function saveDraft(Request $request)
    {
        $validated = $request->validate([
            'examination_id' => 'required|exists:examinations,id',
            'subject_id' => 'required|exists:subjects,id',
            'marks' => 'required|array',
        ]);

        $teacher = Auth::user();
        $marksData = $validated['marks'];

        $result = MarksDraft::saveMultipleDrafts(
            $teacher->id,
            $validated['examination_id'],
            $validated['subject_id'],
            $marksData
        );

        return response()->json([
            'success' => true,
            'message' => 'Draft saved successfully!',
            'saved_count' => $result['saved_count'],
            'total' => $result['total'],
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Load draft marks - AJAX endpoint to fetch existing drafts
     */
    public function loadDrafts(Request $request)
    {
        $validated = $request->validate([
            'examination_id' => 'required|exists:examinations,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $teacher = Auth::user();

        $drafts = MarksDraft::getDraftsKeyedByStudent(
            $teacher->id,
            $validated['examination_id'],
            $validated['subject_id']
        );

        return response()->json([
            'success' => true,
            'drafts' => $drafts,
            'count' => count($drafts),
        ]);
    }

    /**
     * Clear draft marks - AJAX endpoint to clear drafts after successful submission
     */
    public function clearDrafts(Request $request)
    {
        $validated = $request->validate([
            'examination_id' => 'required|exists:examinations,id',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        $teacher = Auth::user();

        $deleted = MarksDraft::clearDrafts(
            $teacher->id,
            $validated['examination_id'],
            $validated['subject_id'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Drafts cleared successfully!',
            'deleted_count' => $deleted,
        ]);
    }
}
