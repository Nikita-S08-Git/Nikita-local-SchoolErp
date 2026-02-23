<?php

namespace App\Http\Controllers\Api\Fee;

use App\Http\Controllers\Controller;
use App\Http\ApiResponse;
use App\Http\Requests\Fee\FeePaymentRequest;
use App\Models\Fee\FeeStructure;
use App\Models\Fee\StudentFee;
use App\Models\Fee\FeePayment;
use App\Models\User\Student;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FeeController extends Controller
{
    /* ================================
     * GET ASSIGNED FEES with pagination
     * ================================ */
    public function getAssignedFees(Request $student): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 25), 100);
            
            $fees = StudentFee::where('student_id', $student->id)
                ->with(['feeStructure.feeHead'])
                ->paginate($perPage);

            return ApiResponse::paginated($fees, 'Assigned fees retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Failed to fetch assigned fees', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);
            
            return ApiResponse::error('Failed to retrieve assigned fees', null, 500);
        }
    }

    /* ================================
     * GET PAYMENTS with pagination
     * ================================ */
    public function getPayments(Request $student): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 25), 100);
            
            $payments = FeePayment::whereHas('studentFee', function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                })
                ->with(['studentFee.feeStructure'])
                ->orderBy('payment_date', 'desc')
                ->paginate($perPage);

            return ApiResponse::paginated($payments, 'Payments retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Failed to fetch payments', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);
            
            return ApiResponse::error('Failed to retrieve payments', null, 500);
        }
    }

    /* ================================
     * ASSIGN FEES (SAFE) with exception handling
     * ================================ */
    public function assignFees(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'student_ids' => 'required|array',
                'student_ids.*' => 'exists:students,id',
                'fee_structure_ids' => 'required|array',
                'fee_structure_ids.*' => 'exists:fee_structures,id',
            ]);

            DB::transaction(function () use ($request) {
                foreach ($request->student_ids as $studentId) {
                    foreach ($request->fee_structure_ids as $feeStructureId) {

                        $feeStructure = FeeStructure::findOrFail($feeStructureId);

                        // ❗ DO NOT override existing paid fees
                        StudentFee::firstOrCreate(
                            [
                                'student_id' => $studentId,
                                'fee_structure_id' => $feeStructureId
                            ],
                            [
                                'total_amount'       => $feeStructure->amount,
                                'discount_amount'    => 0,
                                'final_amount'       => $feeStructure->amount,
                                'paid_amount'        => 0,
                                'outstanding_amount' => $feeStructure->amount,
                                'status'             => 'unpaid',
                        ]
                    );
                }
            }
        });

            return response()->json([
                'success' => true,
                'message' => 'Fees assigned successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to assign fees', [
                'error' => $e->getMessage()
            ]);
            
            return ApiResponse::error('Failed to assign fees', null, 500);
        }
    }

    /* ================================
     * RECORD PAYMENT with FormRequest validation
     * ================================ */
    public function recordPayment(FeePaymentRequest $request, Student $student): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request, $student) {
                $studentFee = StudentFee::with('feeStructure')
                    ->where('student_id', $student->id)
                    ->where('id', $request->student_fee_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                /* ❌ BLOCK if already paid */
                if ($studentFee->outstanding_amount <= 0) {
                    return ApiResponse::error('Fee already fully paid', null, 422);
                }

                /* ❌ BLOCK overpayment - validation already handles this in FeePaymentRequest */
                if ($request->amount > $studentFee->outstanding_amount) {
                    return ApiResponse::error('Payment exceeds outstanding amount', null, 422);
                }

                /* ❌ INSTALLMENT ENFORCEMENT */
                $allowedInstallments = $studentFee->feeStructure->installments ?? 1;
                $paidInstallments = FeePayment::where('student_fee_id', $studentFee->id)->count();

                if ($paidInstallments >= $allowedInstallments) {
                    return ApiResponse::error('All installments already paid', null, 422);
                }

                /* ✅ SAFE RECEIPT NUMBER */
                $receiptPrefix = config('schoolerp.fee.receipt_prefix', 'RCP');
                $receiptNumber = $receiptPrefix . date('Y') . strtoupper(Str::random(8));

                /* ✅ CREATE PAYMENT */
                $payment = FeePayment::create([
                    'student_fee_id' => $studentFee->id,
                    'receipt_number' => $receiptNumber,
                    'amount' => $request->amount,
                    'payment_mode' => $request->payment_method, // Updated to match FormRequest
                    'transaction_id' => $request->transaction_id,
                    'payment_date' => $request->payment_date,
                    'remarks' => $request->remarks,
                    'status' => 'success',
                ]);

                /* ✅ UPDATE LEDGER (SAFE) */
                $studentFee->paid_amount += $request->amount;
                $studentFee->outstanding_amount = max(
                    $studentFee->final_amount - $studentFee->paid_amount,
                    0
                );
                $studentFee->status = $studentFee->outstanding_amount == 0 ? 'paid' : 'partial';
                $studentFee->save();

                Log::info('Payment recorded successfully', [
                    'student_id' => $student->id,
                    'payment_id' => $payment->id,
                    'receipt_number' => $receiptNumber
                ]);

                return ApiResponse::created([
                    'payment' => $payment,
                    'receipt_number' => $receiptNumber,
                    'outstanding_amount' => $studentFee->outstanding_amount
                ], 'Payment recorded successfully');
            });
        } catch (\Exception $e) {
            Log::error('Failed to record payment', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error('Failed to record payment. Please try again.', null, 500);
        }
    }

    /* ================================
     * OUTSTANDING FEES with pagination
     * ================================ */
    public function outstanding(Request $student): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 25), 100);
            
            $fees = StudentFee::where('student_id', $student->id)
                ->where('outstanding_amount', '>', 0)
                ->with(['feeStructure.feeHead'])
                ->paginate($perPage);

            return ApiResponse::paginated($fees, 'Outstanding fees retrieved successfully', 200, [
                'total_outstanding' => $fees->sum('outstanding_amount')
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch outstanding fees', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);
            
            return ApiResponse::error('Failed to retrieve outstanding fees', null, 500);
        }
    }
}
