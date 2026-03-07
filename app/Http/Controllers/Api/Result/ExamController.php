<?php

namespace App\Http\Controllers\Api\Result;

use App\Http\Controllers\Controller;
use App\Models\Result\Examination;
use App\Models\Result\StudentMark;
use App\Models\Result\Subject;
use App\Services\GradeCalculationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExamController extends Controller
{
      public function index(): JsonResponse
    {
        $exams = Examination::all(); // Or add pagination if needed

        return response()->json([
            'success' => true,
            'message' => 'Exams fetched successfully',
            'data' => $exams
        ]);
    }
    
    public function store(Request $request): JsonResponse
{
    $request->validate([
        'name' => 'required|string|max:100',
        'code' => 'required|string|max:20|unique:examinations,code',
        'type' => 'required|in:internal,external,practical',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'academic_year' => 'required|string|max:20',
        'status' => 'nullable|in:scheduled,ongoing,completed',
    ]);

    $exam = Examination::create([
        'name' => $request->name,
        'code' => $request->code,
        'type' => $request->type,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'academic_year' => $request->academic_year,
        'status' => $request->status ?? 'scheduled',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Exam created successfully',
        'data' => $exam
    ]);
}
public function show($id): JsonResponse
{
    $exam = Examination::find($id);

    if (!$exam) {
        return response()->json([
            'success' => false,
            'message' => 'Exam not found'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => $exam
    ]);
}
public function update(Request $request, $id): JsonResponse
{
    $exam = Examination::find($id);

    if (!$exam) {
        return response()->json([
            'success' => false,
            'message' => 'Exam not found'
        ], 404);
    }

    $request->validate([
        'name' => 'sometimes|required|string|max:100',
        'code' => 'sometimes|required|string|max:20|unique:examinations,code,' . $exam->id,
        'type' => 'sometimes|required|in:internal,external,practical',
        'start_date' => 'sometimes|required|date',
        'end_date' => 'sometimes|required|date|after_or_equal:start_date',
        'academic_year' => 'sometimes|required|string|max:20',
        'status' => 'sometimes|in:scheduled,ongoing,completed',
    ]);

    $exam->update($request->only([
        'name',
        'code',
        'type',
        'start_date',
        'end_date',
        'academic_year',
        'status'
    ]));

    return response()->json([
        'success' => true,
        'message' => 'Exam updated successfully',
        'data' => $exam
    ]);
}
public function destroy($id): JsonResponse
{
    $exam = Examination::find($id);

    if (!$exam) {
        return response()->json([
            'success' => false,
            'message' => 'Exam not found'
        ], 404);
    }

    $exam->delete();

    return response()->json([
        'success' => true,
        'message' => 'Exam deleted successfully'
    ]);
}


    public function enterMarks(Request $request): JsonResponse
    {
        $request->validate([
            'examination_id' => 'required|exists:examinations,id',
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.subject_id' => 'required|exists:subjects,id',
            'marks.*.marks_obtained' => 'required|numeric|min:0',
            'marks.*.max_marks' => 'required|numeric|min:1',
        ]);

        foreach ($request->marks as $markData) {
            $subject = Subject::find($markData['subject_id']);
            $percentage = ($markData['marks_obtained'] / $markData['max_marks']) * 100;
            
            StudentMark::updateOrCreate(
                [
                    'student_id' => $markData['student_id'],
                    'subject_id' => $markData['subject_id'],
                    'examination_id' => $request->examination_id,
                ],
                [
                    'marks_obtained' => $markData['marks_obtained'],
                    'max_marks' => $markData['max_marks'],
                    'grade' => GradeCalculationService::calculateGrade($percentage),
                    'result' => GradeCalculationService::determineResult(
                        $markData['marks_obtained'], 
                        $subject->passing_marks
                    ),
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Marks entered successfully'
        ]);
    }

    public function approveMarks(Request $request): JsonResponse
    {
        $request->validate([
            'mark_ids' => 'required|array',
            'mark_ids.*' => 'exists:student_marks,id'
        ]);

        StudentMark::whereIn('id', $request->mark_ids)
            ->update(['is_approved' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Marks approved successfully'
        ]);
    }

    public function getResults(Request $request): JsonResponse
    {
        $query = StudentMark::with(['student', 'subject', 'examination'])
            ->approved();

        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->examination_id) {
            $query->where('examination_id', $request->examination_id);
        }

        $results = $query->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    public function generateMarksheet(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'examination_id' => 'required|exists:examinations,id'
        ]);

        $marks = StudentMark::with(['subject', 'examination'])
            ->where('student_id', $request->student_id)
            ->where('examination_id', $request->examination_id)
            ->approved()
            ->get();

        $totalMarks = $marks->sum('marks_obtained');
        $totalMaxMarks = $marks->sum('max_marks');
        $percentage = $totalMaxMarks > 0 ? ($totalMarks / $totalMaxMarks) * 100 : 0;

        $marksheetData = [
            'marks' => $marks,
            'total_marks' => $totalMarks,
            'total_max_marks' => $totalMaxMarks,
            'percentage' => round($percentage, 2),
            'overall_grade' => GradeCalculationService::calculateGrade($percentage),
            'result' => $marks->where('result', 'fail')->count() > 0 ? 'FAIL' : 'PASS'
        ];

        return response()->json([
            'success' => true,
            'data' => $marksheetData
        ]);
    }
}