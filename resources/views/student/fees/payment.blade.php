@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Pay Fees Online</h2>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Fee Details</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>Fee Head:</th>
                            <td>{{ $studentFee->feeStructure->feeHead->name }}</td>
                        </tr>
                        <tr>
                            <th>Total Amount:</th>
                            <td>₹{{ number_format($studentFee->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Discount:</th>
                            <td>₹{{ number_format($studentFee->discount_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Final Amount:</th>
                            <td>₹{{ number_format($studentFee->final_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Paid Amount:</th>
                            <td class="text-success">₹{{ number_format($studentFee->paid_amount, 2) }}</td>
                        </tr>
                        <tr class="table-warning">
                            <th>Outstanding Amount:</th>
                            <td><strong>₹{{ number_format($studentFee->outstanding_amount, 2) }}</strong></td>
                        </tr>
                    </table>

                    <form id="paymentForm">
                        @csrf
                        <input type="hidden" name="student_fee_id" value="{{ $studentFee->id }}">
                        
                        <div class="mb-3">
                            <label class="form-label">Amount to Pay</label>
                            <input type="number" name="amount" id="amount" class="form-control" 
                                   min="1" max="{{ $studentFee->outstanding_amount }}" 
                                   value="{{ $studentFee->outstanding_amount }}" required>
                            <small class="text-muted">You can pay partial amount</small>
                        </div>

                        <button type="button" id="payButton" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-credit-card"></i> Pay Now
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Payment Methods</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><i class="bi bi-credit-card text-primary"></i> Credit/Debit Card</li>
                        <li><i class="bi bi-bank text-success"></i> Net Banking</li>
                        <li><i class="bi bi-phone text-warning"></i> UPI</li>
                        <li><i class="bi bi-wallet2 text-info"></i> Wallets</li>
                    </ul>
                    <div class="alert alert-info mt-3">
                        <small>Secure payment powered by Razorpay</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('payButton').addEventListener('click', function() {
    const amount = document.getElementById('amount').value;
    const studentFeeId = {{ $studentFee->id }};

    fetch('/razorpay/create-order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            student_fee_id: studentFeeId,
            amount: amount
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            return;
        }

        const options = {
            key: data.key,
            amount: data.amount,
            currency: data.currency,
            order_id: data.order_id,
            name: 'School ERP',
            description: 'Fee Payment',
            handler: function(response) {
                verifyPayment(response, studentFeeId);
            },
            prefill: {
                name: '{{ auth()->user()->name }}',
                email: '{{ auth()->user()->email }}'
            },
            theme: {
                color: '#0d6efd'
            }
        };

        const rzp = new Razorpay(options);
        rzp.open();
    });
});

function verifyPayment(response, studentFeeId) {
    fetch('/razorpay/verify-payment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            razorpay_order_id: response.razorpay_order_id,
            razorpay_payment_id: response.razorpay_payment_id,
            razorpay_signature: response.razorpay_signature,
            student_fee_id: studentFeeId
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/fees/payments/' + data.receipt_id + '/receipt';
        } else {
            alert('Payment verification failed');
        }
    });
}
</script>
@endsection
