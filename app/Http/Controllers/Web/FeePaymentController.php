<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Fee\StudentFee;
use App\Models\Fee\FeePayment;
use App\Models\User\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class FeePaymentController extends Controller
{
    public function index()
    {
        $payments = FeePayment::with(['studentFee.student', 'studentFee.feeStructure.feeHead'])
            ->orderBy('payment_date', 'desc')
            ->paginate(15);
        return view('fees.payments.index', compact('payments'));
    }

    public function create()
    {
        $students = Student::with(['program', 'division'])->get();
        return view('fees.payments.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_fee_id' => 'required|exists:student_fees,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|in:cash,online,cheque,dd',
            'transaction_id' => 'nullable|string',
            'payment_date' => 'required|date',
            'remarks' => 'nullable|string'
        ]);

        $studentFee = StudentFee::findOrFail($request->student_fee_id);

        if ($request->amount > $studentFee->outstanding_amount) {
            return back()->withErrors(['amount' => 'Payment amount exceeds outstanding amount']);
        }

        $receiptNumber = 'RCP' . date('Y') . strtoupper(Str::random(6));

        $payment = FeePayment::create([
            'student_fee_id' => $studentFee->id,
            'installment_number' => FeePayment::where('student_fee_id', $studentFee->id)->count() + 1,
            'receipt_number' => $receiptNumber,
            'amount' => $request->amount,
            'payment_mode' => $request->payment_mode,
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