<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fee\FeeStructure;
use App\Models\Fee\StudentFee;
use App\Models\Fee\FeePayment;
use App\Models\User\Student;
use App\Models\Academic\Program;
use App\Models\Academic\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeeManagementController extends Controller
{
    /**
     * Fee dashboard
     */
    public function index()
    {
        $totalFees = StudentFee::sum('total_amount');
        $totalPaid = StudentFee::sum('paid_amount');
        $totalOutstanding = $totalFees - $totalPaid;
        $totalStudents = Student::where('student_status', 'active')->count();
        
        $recentPayments = FeePayment::with(['student', 'feeStructure'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.fees.index', compact(
            'totalFees',
            'totalPaid',
            'totalOutstanding',
            'totalStudents',
            'recentPayments'
        ));
    }

    /**
     * Fee structures list
     */
    public function structures()
    {
        $structures = FeeStructure::with(['program', 'division'])
            ->latest()
            ->paginate(20);
        return view('admin.fees.structures.index', compact('structures'));
    }

    /**
     * Create fee structure
     */
    public function createStructure()
    {
        $programs = Program::where('is_active', true)->get();
        $divisions = Division::where('is_active', true)->get();
        return view('admin.fees.structures.create', compact('programs', 'divisions'));
    }

    /**
     * Store fee structure
     */
    public function storeStructure(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'division_id' => 'nullable|exists:divisions,id',
            'fee_head' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:monthly,quarterly,half_yearly,yearly,once',
            'description' => 'nullable|string|max:500',
        ]);

        FeeStructure::create($validated);

        return redirect()->route('admin.fees.structures')
            ->with('success', 'Fee structure created successfully!');
    }

    /**
     * Student fees list
     */
    public function studentFees(Request $request)
    {
        $query = StudentFee::with(['student', 'feeStructure']);
        
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $fees = $query->latest()->paginate(20);
        $students = Student::where('student_status', 'active')->get();

        return view('admin.fees.student-fees', compact('fees', 'students'));
    }

    /**
     * Fee payments list
     */
    public function payments(Request $request)
    {
        $query = FeePayment::with(['studentFee.student', 'studentFee.feeStructure']);
        
        if ($request->filled('student_id')) {
            $query->whereHas('studentFee', function($q) use ($request) {
                $q->where('student_id', $request->student_id);
            });
        }
        
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->latest()->paginate(20);
        $students = Student::where('student_status', 'active')->get();

        return view('admin.fees.payments', compact('payments', 'students'));
    }

    /**
     * Outstanding fees
     */
    public function outstanding()
    {
        $outstandingFees = StudentFee::with(['student', 'feeStructure.feeHead'])
            ->whereColumn('paid_amount', '<', 'total_amount')
            ->latest()
            ->paginate(20);

        return view('admin.fees.outstanding', compact('outstandingFees'));
    }

    /**
     * Fee reports
     */
    public function reports()
    {
        $totalCollected = FeePayment::sum('amount');
        $thisMonth = FeePayment::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        $today = FeePayment::whereDate('created_at', today())
            ->sum('amount');

        return view('admin.fees.reports', compact('totalCollected', 'thisMonth', 'today'));
    }
}
