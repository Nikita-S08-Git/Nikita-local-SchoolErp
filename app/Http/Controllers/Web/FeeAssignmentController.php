<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Fee\FeeStructure;
use App\Models\Fee\StudentFee;
use App\Models\User\Student;
use App\Models\Academic\Program;
use Illuminate\Http\Request;

class FeeAssignmentController extends Controller
{
    public function index()
    {
        $programs = Program::active()->get();
        $feeStructures = FeeStructure::active()->with(['program', 'feeHead'])->get();
        return view('fees.assignments.index', compact('programs', 'feeStructures'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'fee_structure_ids' => 'required|array',
            'fee_structure_ids.*' => 'exists:fee_structures,id'
        ]);

        $alreadyAssigned = [];
        $successfullyAssigned = [];

        foreach ($request->student_ids as $studentId) {
            foreach ($request->fee_structure_ids as $feeStructureId) {
                $feeStructure = FeeStructure::find($feeStructureId);
                
                // Check if this fee is already assigned to the student
                $existingFee = StudentFee::where('student_id', $studentId)
                    ->where('fee_structure_id', $feeStructureId)
                    ->first();
                
                if ($existingFee) {
                    // Fee already assigned - track it for feedback
                    $student = Student::find($studentId);
                    $alreadyAssigned[] = $student->user->name . ' - ' . $feeStructure->feeHead->name;
                    continue; // Skip to next fee
                }
                
                StudentFee::create([
                    'student_id' => $studentId,
                    'fee_structure_id' => $feeStructureId,
                    'total_amount' => $feeStructure->amount,
                    'discount_amount' => 0,
                    'final_amount' => $feeStructure->amount,
                    'paid_amount' => 0,
                    'outstanding_amount' => $feeStructure->amount,
                    'status' => 'pending'
                ]);
                
                $successfullyAssigned[] = $studentId . '-' . $feeStructureId;
            }
        }

        // Provide appropriate feedback
        if (!empty($alreadyAssigned) && empty($successfullyAssigned)) {
            return redirect()->route('fees.assignments.index')
                ->with('error', 'These fees are already assigned: ' . implode(', ', $alreadyAssigned));
        } elseif (!empty($alreadyAssigned)) {
            return redirect()->route('fees.assignments.index')
                ->with('warning', 'Fees assigned successfully. Already assigned: ' . implode(', ', $alreadyAssigned));
        }

        return redirect()->route('fees.assignments.index')->with('success', 'Fees assigned successfully');
    }
}