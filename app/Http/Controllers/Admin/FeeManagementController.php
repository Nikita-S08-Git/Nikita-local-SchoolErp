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
use Illuminate\Support\Facades\Log;

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
        
        // Debug: Log what we get from database
        Log::info('Divisions from DB: ' . $divisions->toJson());
        
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

    /**
     * Process fee payment
     */
    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'fee_id' => 'required|exists:student_fees,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,card,upi,net_banking,cheque,bank_transfer',
            'transaction_id' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $fee = StudentFee::findOrFail($validated['fee_id']);
            
            // Create payment record
            $payment = new FeePayment();
            $payment->student_fee_id = $fee->id;
            $payment->student_id = $fee->student_id;
            $payment->amount = $validated['amount'];
            $payment->payment_method = $validated['payment_method'];
            $payment->transaction_id = $validated['transaction_id'] ?? null;
            $payment->remarks = $validated['remarks'] ?? null;
            $payment->payment_date = now();
            $payment->status = 'success';
            $payment->save();

            // Update fee record
            $fee->paid_amount += $validated['amount'];
            
            // Update status
            if ($fee->paid_amount >= $fee->total_amount) {
                $fee->status = 'paid';
            } else {
                $fee->status = 'partial';
            }
            $fee->save();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Payment of ₹' . number_format($validated['amount'], 2) . ' recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }
}
