<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Result\Examination;
use App\Models\Result\Subject;
use App\Models\Result\StudentMark;
use App\Models\Academic\Division;
use App\Models\User\Student;
use Illuminate\Http\Request;

class ExaminationController extends Controller
{
    public function index()
    {
        $examinations = Examination::latest()->paginate(15);
        return view('examinations.index', compact('examinations'));
    }

    public function show(Examination $examination)
    {
        return view('examinations.show', compact('examination'));
    }

    public function edit(Examination $examination)
    {
        return view('examinations.edit', compact('examination'));
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
        ]);

        $examination->update($validated);

        return redirect()->route('examinations.index')
            ->with('success', 'Examination updated successfully!');
    }

    public function create()
    {
        return view('examinations.create');
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
        ]);

        Examination::create($validated);

        return redirect()->route('examinations.index')
            ->with('success', 'Examination created successfully!');
    }

    public function marksEntry(Request $request, Examination $examination)
    {
        $divisions = Division::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        
        $students = [];
        $marks = [];
        
        if ($request->has('division_id') && $request->has('subject_id')) {
            $students = Student::where('division_id', $request->division_id)
                ->where('student_status', 'active')
                ->orderBy('roll_number')
                ->get();
            
            $marks = StudentMark::where('examination_id', $examination->id)
                ->where('subject_id', $request->subject_id)
                ->get()
                ->keyBy('student_id');
        }
        
        return view('examinations.marks-entry', compact('examination', 'divisions', 'subjects', 'students', 'marks'));
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
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'division_id' => 'required|exists:divisions,id',
            'marks' => 'required|array',
        ]);

        foreach ($request->marks as $studentId => $marksObtained) {
            if ($marksObtained === null || $marksObtained === '') continue;
            
            $maxMarks = $request->max_marks ?? 100;
            $percentage = ($marksObtained / $maxMarks) * 100;
            $grade = $this->calculateGrade($percentage);
            $result = $percentage >= 40 ? 'pass' : 'fail';

            StudentMark::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'examination_id' => $examination->id,
                    'subject_id' => $validated['subject_id'],
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
            'division_id' => $validated['division_id'],
            'subject_id' => $validated['subject_id']
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
}
