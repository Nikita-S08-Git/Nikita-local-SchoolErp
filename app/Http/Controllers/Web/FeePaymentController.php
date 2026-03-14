<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fee\RecordFeePaymentRequest;
use App\Models\Fee\StudentFee;
use App\Models\Fee\FeePayment;
use App\Models\User\Student;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class FeePaymentController extends Controller
{
    public function index(Request $request)
    {
        // Default per page is 15, allow user to customize
        $perPage = $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50]) ? (int) $perPage : 15;

        $sortBy = $request->query('sort', 'payment_date');
        $sortDir = $request->query('dir', 'desc');
        $allowedSorts = ['payment_date', 'amount', 'payment_mode', 'transaction_id', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'payment_date';
        }
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'desc';

        $payments = FeePayment::with(['studentFee.student', 'studentFee.feeStructure.feeHead'])
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)->appends($request->query());
        return view('fees.payments.index', compact('payments', 'sortBy', 'sortDir', 'perPage'));
    }

    public function create()
    {
        $students = Student::with(['program', 'division'])->get();
        return view('fees.payments.create', compact('students'));
    }

    public function store(RecordFeePaymentRequest $request)
    {
        $studentFee = StudentFee::with('feeStructure')->findOrFail($request->student_fee_id);
        
        $feeStructure = $studentFee->feeStructure;
        $totalInstallments = $feeStructure->installments ?? 1;
        
        // Calculate single installment amount based on final amount (after discount)
        $singleInstallmentAmount = $studentFee->final_amount / $totalInstallments;
        
        // Get the count of successful payments made
        $paidPayments = FeePayment::where('student_fee_id', $studentFee->id)
            ->where('status', 'success')
            ->get();
        
        $paidInstallmentsCount = $paidPayments->count();
        $nextInstallmentNumber = $paidInstallmentsCount + 1;

        // Additional validation in controller (backup for security)
        $requestedAmount = (float) $request->amount;
        $outstandingAmount = (float) $studentFee->outstanding_amount;

        // RULE 1: If Fee Structure contains MORE THAN 1 Installment
        if ($totalInstallments > 1) {
            // Calculate the expected amount for the current installment
            $expectedCurrentInstallmentAmount = $singleInstallmentAmount;
            
            // Check if this is not the last installment, enforce exact amount
            if ($nextInstallmentNumber < $totalInstallments) {
                // For installments before the last one, must pay exact installment amount
                // Allow small floating point tolerance
                $tolerance = 0.01;
                
                if (abs($requestedAmount - $expectedCurrentInstallmentAmount) > $tolerance) {
                    return back()->withErrors([
                        'amount' => 'Only ' . number_format($expectedCurrentInstallmentAmount, 2) . ' (one installment amount) can be recorded at a time.'
                    ])->withInput();
                }
            } else {
                // This is the last installment - allow remaining outstanding amount
                $remainingAmount = $outstandingAmount;
                
                // Allow small floating point tolerance
                $tolerance = 0.01;
                
                // Must pay the remaining amount (within tolerance)
                if (abs($requestedAmount - $remainingAmount) > $tolerance) {
                    return back()->withErrors([
                        'amount' => 'Only ' . number_format($expectedCurrentInstallmentAmount, 2) . ' (one installment amount) can be recorded at a time.'
                    ])->withInput();
                }
            }

            // Additional check: Do NOT allow amount more than current installment
            if ($requestedAmount > $expectedCurrentInstallmentAmount + 0.01) {
                return back()->withErrors([
                    'amount' => 'Only ' . number_format($expectedCurrentInstallmentAmount, 2) . ' (one installment amount) can be recorded at a time.'
                ])->withInput();
            }

            // Additional check: Do NOT allow amount less than current installment
            if ($requestedAmount < $expectedCurrentInstallmentAmount - 0.01) {
                return back()->withErrors([
                    'amount' => 'Only ' . number_format($expectedCurrentInstallmentAmount, 2) . ' (one installment amount) can be recorded at a time.'
                ])->withInput();
            }
        }
        // RULE 2: If Fee Structure contains ONLY 1 Installment
        else {
            // Full outstanding amount must be collected in a single transaction
            $tolerance = 0.01;
            
            if (abs($requestedAmount - $outstandingAmount) > $tolerance) {
                return back()->withErrors([
                    'amount' => 'Full fee amount (' . number_format($outstandingAmount, 2) . ') must be paid in a single transaction.'
                ])->withInput();
            }

            // Do NOT allow amount less than full amount
            if ($requestedAmount < $outstandingAmount - 0.01) {
                return back()->withErrors([
                    'amount' => 'Full fee amount (' . number_format($outstandingAmount, 2) . ') must be paid in a single transaction.'
                ])->withInput();
            }

            // Do NOT allow amount more than full amount
            if ($requestedAmount > $outstandingAmount + 0.01) {
                return back()->withErrors([
                    'amount' => 'Full fee amount (' . number_format($outstandingAmount, 2) . ') must be paid in a single transaction.'
                ])->withInput();
            }
        }

        // General check: Payment amount cannot exceed outstanding amount
        if ($requestedAmount > $outstandingAmount + 0.01) {
            return back()->withErrors([
                'amount' => 'Payment amount cannot exceed the outstanding amount (₹' . number_format($outstandingAmount, 2) . ')'
            ])->withInput();
        }

        $receiptNumber = 'RCP' . date('Y') . strtoupper(Str::random(6));

        // Determine payment mode - support both payment_method and payment_mode
        $paymentMode = $request->payment_method ?? $request->payment_mode ?? 'cash';

        $payment = FeePayment::create([
            'student_fee_id' => $studentFee->id,
            'installment_number' => $nextInstallmentNumber,
            'receipt_number' => $receiptNumber,
            'amount' => $request->amount,
            'payment_mode' => $paymentMode,
            'transaction_id' => $request->transaction_id,
            'payment_date' => $request->payment_date,
            'due_date' => $request->payment_date,
            'status' => 'success',
            'remarks' => $request->remarks
        ]);

        // Update student fee balance
        $studentFee->paid_amount += $request->amount;
        $studentFee->outstanding_amount = max($studentFee->final_amount - $studentFee->paid_amount, 0);
        $studentFee->status = $studentFee->outstanding_amount == 0 ? 'paid' : 'partial';
        $studentFee->save();

        return redirect()->route('fees.payments.receipt', $payment->id)->with('success', 'Payment recorded successfully');
    }

    public function receipt($id)
    {
        $payment = FeePayment::with(['studentFee.student', 'studentFee.feeStructure.feeHead'])->findOrFail($id);
        return view('fees.payments.receipt', compact('payment'));
    }

    public function downloadReceipt($id)
    {
        $payment = FeePayment::with(['studentFee.student', 'studentFee.feeStructure.feeHead'])->findOrFail($id);
        $pdf = Pdf::loadView('pdf.fee-receipt', compact('payment'));
        return $pdf->download('receipt-' . $payment->receipt_number . '.pdf');
    }
}