<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fee Receipt - {{ $payment->receipt_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 5px 0; }
        .receipt-info { margin: 20px 0; }
        .receipt-info table { width: 100%; }
        .receipt-info td { padding: 5px; }
        .amount-box { border: 2px solid #000; padding: 15px; text-align: center; margin: 20px 0; }
        .amount-box h3 { margin: 0; font-size: 24px; }
        .footer { margin-top: 50px; }
        .signature { float: right; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>School ERP System</h2>
        <h3>Fee Payment Receipt</h3>
    </div>

    <div class="receipt-info">
        <table>
            <tr>
                <td><strong>Receipt No:</strong></td>
                <td>{{ $payment->receipt_number }}</td>
                <td><strong>Date:</strong></td>
                <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
            </tr>
            <tr>
                <td><strong>Student Name:</strong></td>
                <td>{{ $payment->studentFee->student->first_name }} {{ $payment->studentFee->student->last_name }}</td>
                <td><strong>Roll No:</strong></td>
                <td>{{ $payment->studentFee->student->roll_number }}</td>
            </tr>
            <tr>
                <td><strong>Division:</strong></td>
                <td>{{ $payment->studentFee->student->division->division_name ?? 'N/A' }}</td>
                <td><strong>Payment Mode:</strong></td>
                <td>{{ strtoupper($payment->payment_mode) }}</td>
            </tr>
            @if($payment->transaction_id)
            <tr>
                <td><strong>Transaction ID:</strong></td>
                <td colspan="3">{{ $payment->transaction_id }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="amount-box">
        <p style="margin: 0;">Amount Paid</p>
        <h3>₹ {{ number_format($payment->amount, 2) }}</h3>
        <p style="margin: 0; font-size: 12px;">{{ ucwords(\Illuminate\Support\Str::of($payment->amount)->toWords()) }} Only</p>
    </div>

    <div class="receipt-info">
        <table>
            <tr>
                <td><strong>Fee Head:</strong></td>
                <td>{{ $payment->studentFee->feeStructure->feeHead->fee_head_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Total Fee:</strong></td>
                <td>₹ {{ number_format($payment->studentFee->final_amount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Paid Amount:</strong></td>
                <td>₹ {{ number_format($payment->studentFee->paid_amount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Outstanding:</strong></td>
                <td>₹ {{ number_format($payment->studentFee->outstanding_amount, 2) }}</td>
            </tr>
        </table>
    </div>

    @if($payment->remarks)
    <div style="margin-top: 20px;">
        <strong>Remarks:</strong> {{ $payment->remarks }}
    </div>
    @endif

    <div class="footer">
        <div class="signature">
            <p>_____________________</p>
            <p>Authorized Signature</p>
        </div>
    </div>

    <div style="clear: both; margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
        <p>This is a computer-generated receipt and does not require a signature.</p>
    </div>
</body>
</html>
