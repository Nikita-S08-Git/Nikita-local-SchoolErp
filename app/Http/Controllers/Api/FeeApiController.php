<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User\Student;
use App\Models\Fee\StudentFee;
use App\Models\Fee\FeePayment;
use Illuminate\Http\Request;

class FeeApiController extends Controller
{
    public function studentOutstanding($id)
    {
        $student = Student::findOrFail($id);
        $fees = StudentFee::where('student_id', $id)->with('feeStructure.feeHead')->get();

        $totalAssigned = $fees->sum('final_amount');
        $totalPaid = $fees->sum('paid_amount');
        $outstanding = $fees->sum('outstanding_amount');
        
        $overdueFees = $fees->filter(fn($f) => $f->due_date && $f->due_date->isPast() && $f->outstanding_amount > 0);
        $overdueAmount = $overdueFees->sum('outstanding_amount');
        $lateFee = $overdueAmount > 0 ? $overdueAmount * 0.01 : 0; // 1% late fee

        return response()->json([
            'student' => [
                'id' => $student->id,
                'name' => $student->full_name,
                'admission_number' => $student->admission_number
            ],
            'total_assigned' => $totalAssigned,
            'total_paid' => $totalPaid,
            'outstanding_balance' => $outstanding,
            'overdue_amount' => $overdueAmount,
            'late_fee' => $lateFee,
            'total_payable' => $outstanding + $lateFee,
            'fees' => $fees->map(fn($f) => [
                'fee_head' => $f->feeStructure->feeHead->name,
                'amount' => $f->final_amount,
                'paid' => $f->paid_amount,
                'outstanding' => $f->outstanding_amount,
                'due_date' => $f->due_date?->format('Y-m-d'),
                'is_overdue' => $f->due_date && $f->due_date->isPast() && $f->outstanding_amount > 0,
                'status' => $f->status
            ])
        ]);
    }

    public function paymentHistory($studentId)
    {
        $payments = FeePayment::whereHas('studentFee', fn($q) => $q->where('student_id', $studentId))
            ->with('studentFee.feeStructure.feeHead')
            ->orderBy('payment_date', 'desc')
            ->get();

        return response()->json([
            'payments' => $payments->map(fn($p) => [
                'id' => $p->id,
                'date' => $p->payment_date->format('Y-m-d'),
                'amount' => $p->amount,
                'payment_mode' => $p->payment_mode,
                'receipt_number' => $p->receipt_number,
                'transaction_id' => $p->transaction_id,
                'fee_head' => $p->studentFee->feeStructure->feeHead->name,
                'status' => $p->status
            ])
        ]);
    }

    public function dashboardStats()
    {
        $totalOutstanding = StudentFee::sum('outstanding_amount');
        $studentsWithPending = StudentFee::where('outstanding_amount', '>', 0)->distinct('student_id')->count();
        $overdueAmount = StudentFee::whereDate('due_date', '<', now())->where('outstanding_amount', '>', 0)->sum('outstanding_amount');
        $totalAssigned = StudentFee::sum('final_amount');
        $totalPaid = StudentFee::sum('paid_amount');
        $collectionRate = $totalAssigned > 0 ? ($totalPaid / $totalAssigned) * 100 : 0;

        return response()->json([
            'total_outstanding' => $totalOutstanding,
            'students_with_pending' => $studentsWithPending,
            'overdue_amount' => $overdueAmount,
            'collection_rate' => round($collectionRate, 2),
            'total_assigned' => $totalAssigned,
            'total_collected' => $totalPaid
        ]);
    }
}
