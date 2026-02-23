<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Fee\StudentFee;
use App\Models\Fee\FeePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Razorpay\Api\Api;

class RazorpayController extends Controller
{
    private $api;

    public function __construct()
    {
        $this->api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'student_fee_id' => 'required|exists:student_fees,id',
            'amount' => 'required|numeric|min:1'
        ]);

        $studentFee = StudentFee::with('student')->findOrFail($request->student_fee_id);

        if ($request->amount > $studentFee->outstanding_amount) {
            return response()->json(['error' => 'Amount exceeds outstanding'], 400);
        }

        $order = $this->api->order->create([
            'amount' => $request->amount * 100, // Convert to paise
            'currency' => 'INR',
            'receipt' => 'RCP' . date('Y') . strtoupper(Str::random(6)),
            'notes' => [
                'student_id' => $studentFee->student_id,
                'student_fee_id' => $studentFee->id
            ]
        ]);

        return response()->json([
            'order_id' => $order->id,
            'amount' => $order->amount,
            'currency' => $order->currency,
            'key' => config('services.razorpay.key')
        ]);
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
            'student_fee_id' => 'required|exists:student_fees,id'
        ]);

        $signature = hash_hmac('sha256', 
            $request->razorpay_order_id . '|' . $request->razorpay_payment_id,
            config('services.razorpay.secret')
        );

        if ($signature !== $request->razorpay_signature) {
            \Log::error('Razorpay signature mismatch', $request->all());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $payment = $this->api->payment->fetch($request->razorpay_payment_id);
        $studentFee = StudentFee::findOrFail($request->student_fee_id);

        $feePayment = FeePayment::create([
            'student_fee_id' => $studentFee->id,
            'installment_number' => FeePayment::where('student_fee_id', $studentFee->id)->count() + 1,
            'receipt_number' => 'RCP' . date('Y') . strtoupper(Str::random(6)),
            'amount' => $payment->amount / 100,
            'payment_mode' => 'online',
            'transaction_id' => $request->razorpay_payment_id,
            'payment_date' => now(),
            'due_date' => now(),
            'status' => 'success',
            'remarks' => 'Online payment via Razorpay'
        ]);

        $studentFee->paid_amount += $feePayment->amount;
        $studentFee->outstanding_amount = max($studentFee->final_amount - $studentFee->paid_amount, 0);
        $studentFee->status = $studentFee->outstanding_amount == 0 ? 'paid' : 'partial';
        $studentFee->save();

        return response()->json([
            'success' => true,
            'receipt_id' => $feePayment->id,
            'receipt_number' => $feePayment->receipt_number
        ]);
    }

    public function webhook(Request $request)
    {
        $webhookSignature = $request->header('X-Razorpay-Signature');
        $webhookSecret = config('services.razorpay.webhook_secret');

        $expectedSignature = hash_hmac('sha256', $request->getContent(), $webhookSecret);

        if ($webhookSignature !== $expectedSignature) {
            \Log::error('Razorpay webhook signature mismatch');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $event = $request->input('event');
        $payload = $request->input('payload');

        if ($event === 'payment.captured') {
            \Log::info('Payment captured', $payload);
        } elseif ($event === 'payment.failed') {
            \Log::warning('Payment failed', $payload);
        }

        return response()->json(['status' => 'ok']);
    }
}
