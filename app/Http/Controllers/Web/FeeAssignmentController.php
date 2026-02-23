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

        foreach ($request->student_ids as $studentId) {
            foreach ($request->fee_structure_ids as $feeStructureId) {
                $feeStructure = FeeStructure::find($feeStructureId);
                
                StudentFee::firstOrCreate([
                    'student_id' => $studentId,
                    'fee_structure_id' => $feeStructureId
                ], [
                    'total_amount' => $feeStructure->amount,
                    'discount_amount' => 0,
                    'final_amount' => $feeStructure->amount,
                    'paid_amount' => 0,
                    'outstanding_amount' => $feeStructure->amount,
                    'status' => 'pending'
                ]);
            }
        }

        return redirect()->route('fees.assignments.index')->with('success', 'Fees assigned successfully');
    }
}