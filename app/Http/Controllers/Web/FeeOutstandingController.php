<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Fee\StudentFee;
use App\Models\Academic\Program;
use Illuminate\Http\Request;

class FeeOutstandingController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentFee::with(['student.program', 'student.division', 'feeStructure.feeHead'])
            ->where('outstanding_amount', '>', 0);

        if ($request->program_id) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('program_id', $request->program_id);
            });
        }

        if ($request->search) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('roll_number', 'like', '%' . $request->search . '%');
            });
        }

        $outstandingFees = $query->orderBy('outstanding_amount', 'desc')->paginate(15);
        $programs = Program::active()->get();
        $totalOutstanding = StudentFee::where('outstanding_amount', '>', 0)->sum('outstanding_amount');

        return view('fees.outstanding.index', compact('outstandingFees', 'programs', 'totalOutstanding'));
    }
}