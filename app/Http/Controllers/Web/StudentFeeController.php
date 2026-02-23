<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Fee\StudentFee;
use App\Models\Fee\FeePayment;
use Illuminate\Http\Request;

class StudentFeeController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        
        if (!$student) {
            return redirect()->route('dashboard.student')->with('error', 'Student profile not found');
        }

        $studentFees = StudentFee::with(['feeStructure.feeHead', 'payments'])
            ->where('student_id', $student->id)
            ->get();

        $totalFees = $studentFees->sum('final_amount');
        $totalPaid = $studentFees->sum('paid_amount');
        $totalOutstanding = $studentFees->sum('outstanding_amount');

        return view('student.fees.index', compact('studentFees', 'totalFees', 'totalPaid', 'totalOutstanding'));
    }

    public function payment(StudentFee $studentFee)
    {
        // Ensure student can only access their own fees
        if ($studentFee->student_id !== auth()->user()->student->id) {
            abort(403);
        }

        return view('student.fees.payment', compact('studentFee'));
    }
}