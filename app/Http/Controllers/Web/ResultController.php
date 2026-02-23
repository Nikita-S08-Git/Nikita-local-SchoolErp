<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User\Student;
use App\Models\Result\StudentMark;
use App\Models\Result\Examination;
use App\Models\Academic\Division;
use App\Models\Academic\Subject;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ResultController extends Controller
{
    public function index()
    {
        $examinations = Examination::latest()->get();
        $divisions = Division::where('is_active', true)->get();
        return view('results.generate', compact('examinations', 'divisions'));
    }

    public function generate(Request $request)
    {
        $examination = Examination::findOrFail($request->examination_id);
        $division = Division::findOrFail($request->division_id);
        $subjects = Subject::where('is_active', true)->get();
        
        $students = Student::where('division_id', $division->id)
            ->where('student_status', 'active')
            ->orderBy('roll_number')
            ->get();
        
        $results = [];
        foreach ($students as $student) {
            $marks = StudentMark::where('student_id', $student->id)
                ->where('examination_id', $examination->id)
                ->get()
                ->keyBy('subject_id');
            
            $total = 0;
            $maxTotal = 0;
            $marksArray = [];
            
            foreach ($subjects as $subject) {
                if (isset($marks[$subject->id])) {
                    $marksArray[$subject->id] = $marks[$subject->id]->marks_obtained;
                    $total += $marks[$subject->id]->marks_obtained;
                    $maxTotal += $marks[$subject->id]->max_marks;
                }
            }
            
            $percentage = $maxTotal > 0 ? ($total / $maxTotal) * 100 : 0;
            $grade = \App\Models\Grade::getGradeForPercentage($percentage);
            
            $results[] = [
                'student' => $student,
                'marks' => $marksArray,
                'total' => $total,
                'percentage' => $percentage,
                'grade' => $grade ? $grade->grade_name : 'F',
                'result' => $percentage >= 40 ? 'Pass' : 'Fail',
            ];
        }
        
        $examinations = Examination::latest()->get();
        $divisions = Division::where('is_active', true)->get();
        
        return view('results.generate', compact('examinations', 'divisions', 'examination', 'division', 'subjects', 'results'));
    }

    public function pdf(Request $request)
    {
        $examination = Examination::findOrFail($request->examination_id);
        $division = Division::findOrFail($request->division_id);
        $subjects = Subject::where('is_active', true)->get();
        
        $students = Student::where('division_id', $division->id)
            ->where('student_status', 'active')
            ->orderBy('roll_number')
            ->get();
        
        $results = [];
        foreach ($students as $student) {
            $marks = StudentMark::where('student_id', $student->id)
                ->where('examination_id', $examination->id)
                ->get()
                ->keyBy('subject_id');
            
            $total = 0;
            $maxTotal = 0;
            $marksArray = [];
            
            foreach ($subjects as $subject) {
                if (isset($marks[$subject->id])) {
                    $marksArray[$subject->id] = $marks[$subject->id]->marks_obtained;
                    $total += $marks[$subject->id]->marks_obtained;
                    $maxTotal += $marks[$subject->id]->max_marks;
                }
            }
            
            $percentage = $maxTotal > 0 ? ($total / $maxTotal) * 100 : 0;
            $grade = \App\Models\Grade::getGradeForPercentage($percentage);
            
            $results[] = [
                'student' => $student,
                'marks' => $marksArray,
                'total' => $total,
                'percentage' => $percentage,
                'grade' => $grade ? $grade->grade_name : 'F',
                'result' => $percentage >= 40 ? 'Pass' : 'Fail',
            ];
        }
        
        $pdf = Pdf::loadView('pdf.results', compact('examination', 'division', 'subjects', 'results'));
        return $pdf->download('results-' . $examination->code . '-' . $division->division_name . '.pdf');
    }

    public function studentResult(Student $student)
    {
        $marks = StudentMark::where('student_id', $student->id)
            ->with(['examination', 'subject'])
            ->get();

        $totalMarks = $marks->sum('marks_obtained');
        $totalPossible = $marks->sum('max_marks');
        $percentage = $totalPossible > 0 ? ($totalMarks / $totalPossible) * 100 : 0;
        $grade = \App\Models\Grade::getGradeForPercentage($percentage);

        return view('results.student', compact('student', 'marks', 'totalMarks', 'totalPossible', 'percentage', 'grade'));
    }
}
