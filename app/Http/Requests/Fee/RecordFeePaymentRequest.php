<?php

namespace App\Http\Requests\Fee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request for Recording Fee Payments
 * 
 * Implements strict payment rules based on fee structure installments:
 * - For multiple installments: Only one installment amount can be recorded at a time
 * - For single installment: Full fee amount must be paid in a single transaction
 */
class RecordFeePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow authenticated users to record payments
        // Authorization is handled by middleware at route level
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Accept either payment_method or payment_mode for backward compatibility
        // At least one of them is required, but not both necessarily
        return [
            'student_id' => ['required', 'exists:students,id'],
            'student_fee_id' => ['required', 'exists:student_fees,id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'payment_method' => ['nullable', Rule::in(['cash', 'card', 'upi', 'net_banking', 'cheque', 'bank_transfer', 'online', 'dd'])],
            'payment_mode' => ['nullable', Rule::in(['cash', 'card', 'upi', 'net_banking', 'cheque', 'bank_transfer', 'online', 'dd'])],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:500'],
            'receipt_number' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'student_id.required' => 'Please select a student.',
            'student_fee_id.required' => 'Please select a fee to pay.',
            'amount.required' => 'Payment amount is required.',
            'amount.min' => 'Payment amount must be greater than zero.',
            'payment_date.required' => 'Payment date is required.',
            'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
            'payment_method.required' => 'Payment method is required.',
            'payment_method.in' => 'Invalid payment method selected.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'student_id' => 'student',
            'student_fee_id' => 'fee',
            'amount' => 'payment amount',
            'payment_date' => 'payment date',
            'payment_method' => 'payment method',
            'transaction_id' => 'transaction ID',
        ];
    }

    /**
     * Configure the validator instance with installment-based validation.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check that at least one of payment_method or payment_mode is provided
            $paymentMethod = $this->payment_method;
            $paymentMode = $this->payment_mode;
            
            if (empty($paymentMethod) && empty($paymentMode)) {
                $validator->errors()->add(
                    'payment_method',
                    'Payment method is required.'
                );
                return;
            }
            
            $studentFee = \App\Models\Fee\StudentFee::with('feeStructure')->find($this->student_fee_id);

            if (!$studentFee) {
                return;
            }

            $feeStructure = $studentFee->feeStructure;
            $totalInstallments = $feeStructure->installments ?? 1;
            $outstandingAmount = (float) $studentFee->outstanding_amount;
            $requestedAmount = (float) $this->amount;

            // Calculate single installment amount based on final amount (after discount)
            $singleInstallmentAmount = $studentFee->final_amount / $totalInstallments;

            // Get the count of successful payments made
            $paidPayments = \App\Models\Fee\FeePayment::where('student_fee_id', $studentFee->id)
                ->where('status', 'success')
                ->get();
            
            $totalPaidAmount = $paidPayments->sum('amount');
            $paidInstallmentsCount = $paidPayments->count();
            $nextInstallmentNumber = $paidInstallmentsCount + 1;

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
                        $validator->errors()->add(
                            'amount',
                            "Only " . number_format($expectedCurrentInstallmentAmount, 2) . " (one installment amount) can be recorded at a time."
                        );
                        return;
                    }
                } else {
                    // This is the last installment - allow remaining outstanding amount
                    $remainingAmount = $outstandingAmount;
                    
                    // Allow small floating point tolerance
                    $tolerance = 0.01;
                    
                    // Must pay the remaining amount (within tolerance)
                    if (abs($requestedAmount - $remainingAmount) > $tolerance) {
                        $validator->errors()->add(
                            'amount',
                            "Only " . number_format($expectedCurrentInstallmentAmount, 2) . " (one installment amount) can be recorded at a time."
                        );
                        return;
                    }
                }

                // Additional check: Do NOT allow amount more than current installment
                if ($requestedAmount > $expectedCurrentInstallmentAmount + 0.01) {
                    $validator->errors()->add(
                        'amount',
                        "Only " . number_format($expectedCurrentInstallmentAmount, 2) . " (one installment amount) can be recorded at a time."
                    );
                    return;
                }

                // Additional check: Do NOT allow amount less than current installment
                if ($requestedAmount < $expectedCurrentInstallmentAmount - 0.01) {
                    $validator->errors()->add(
                        'amount',
                        "Only " . number_format($expectedCurrentInstallmentAmount, 2) . " (one installment amount) can be recorded at a time."
                    );
                    return;
                }
            }
            // RULE 2: If Fee Structure contains ONLY 1 Installment
            else {
                // Full outstanding amount must be collected in a single transaction
                $tolerance = 0.01;
                
                if (abs($requestedAmount - $outstandingAmount) > $tolerance) {
                    $validator->errors()->add(
                        'amount',
                        "Full fee amount (" . number_format($outstandingAmount, 2) . ") must be paid in a single transaction."
                    );
                    return;
                }

                // Do NOT allow amount less than full amount
                if ($requestedAmount < $outstandingAmount - 0.01) {
                    $validator->errors()->add(
                        'amount',
                        "Full fee amount (" . number_format($outstandingAmount, 2) . ") must be paid in a single transaction."
                    );
                    return;
                }

                // Do NOT allow amount more than full amount
                if ($requestedAmount > $outstandingAmount + 0.01) {
                    $validator->errors()->add(
                        'amount',
                        "Full fee amount (" . number_format($outstandingAmount, 2) . ") must be paid in a single transaction."
                    );
                    return;
                }
            }

            // General check: Payment amount cannot exceed outstanding amount
            if ($requestedAmount > $outstandingAmount + 0.01) {
                $validator->errors()->add(
                    'amount',
                    "Payment amount cannot exceed the outstanding amount (₹" . number_format($outstandingAmount, 2) . ")."
                );
            }
        });
    }
}
