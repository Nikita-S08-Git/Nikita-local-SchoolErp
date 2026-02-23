<?php

namespace App\Http\Requests\Fee;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request for Fee Payment operations
 */
class FeePaymentRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('record payments', \App\Models\Fee\FeePayment::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'student_fee_id' => ['required', 'exists:student_fees,id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'payment_method' => ['required', Rule::in(['cash', 'card', 'upi', 'net_banking', 'cheque', 'bank_transfer'])],
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
            'payment_method.required' => 'Payment method is required.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $studentFee = \App\Models\Fee\StudentFee::find($this->student_fee_id);

            if ($studentFee && $this->amount > $studentFee->outstanding_amount) {
                $validator->errors()->add(
                    'amount',
                    "Payment amount cannot exceed the outstanding amount (₹{$studentFee->outstanding_amount})."
                );
            }
        });
    }
}
