# Razorpay Online Payment Integration - Documentation

## Overview
Complete Razorpay payment gateway integration for online fee payments in SchoolERP system.

## Features Implemented

### 1. Payment Order Creation
- Creates Razorpay order with amount and student details
- Generates unique receipt number
- Returns order ID and payment key to frontend

### 2. Payment Processing
- Opens Razorpay checkout popup
- Supports multiple payment methods:
  - Credit/Debit Cards
  - Net Banking
  - UPI
  - Wallets (Paytm, PhonePe, etc.)

### 3. Payment Verification
- Verifies payment signature using HMAC SHA256
- Validates payment authenticity
- Prevents fraudulent transactions

### 4. Payment Recording
- Creates fee payment record
- Updates student outstanding amount
- Generates receipt
- Updates payment status

### 5. Webhook Handling
- Receives payment notifications from Razorpay
- Logs payment events
- Handles payment.captured and payment.failed events

## Installation Steps

### Step 1: Install Razorpay PHP SDK
```bash
composer require razorpay/razorpay
```

### Step 2: Configure Environment Variables
Add to `.env` file:
```env
RAZORPAY_KEY=rzp_test_xxxxxxxxxx
RAZORPAY_SECRET=xxxxxxxxxxxxxxxxxx
RAZORPAY_WEBHOOK_SECRET=xxxxxxxxxxxxxxxxxx
```

### Step 3: Get Razorpay Credentials
1. Sign up at https://razorpay.com
2. Go to Dashboard → Settings → API Keys
3. Generate Test/Live API Keys
4. Copy Key ID and Key Secret

### Step 4: Configure Webhook
1. Go to Dashboard → Webhooks
2. Create new webhook
3. URL: `https://yourdomain.com/razorpay/webhook`
4. Select events:
   - payment.captured
   - payment.failed
5. Copy webhook secret

### Step 5: Test Integration
Use test credentials and test card:
- Card: 4111 1111 1111 1111
- CVV: Any 3 digits
- Expiry: Any future date

## Workflow

### Student Payment Flow
```
1. Student logs in
2. Views fee dashboard
3. Sees outstanding: ₹28,500
4. Clicks "Pay Now"
5. Enters amount (full/partial)
6. Clicks "Proceed to Payment"
7. System creates Razorpay order
8. Razorpay checkout opens
9. Student selects payment method
10. Enters payment details
11. Completes payment
12. Razorpay sends callback
13. System verifies signature
14. If valid:
    - Creates payment record
    - Updates outstanding
    - Generates receipt
    - Shows success page
15. If invalid:
    - Logs error
    - Shows error message
```

### Backend Processing Flow
```
1. POST /razorpay/create-order
   - Validates student_fee_id and amount
   - Creates Razorpay order
   - Returns order_id, amount, key

2. Razorpay Checkout (Frontend)
   - Opens payment popup
   - Student completes payment
   - Returns payment_id, order_id, signature

3. POST /razorpay/verify-payment
   - Verifies signature
   - Fetches payment details
   - Creates fee_payment record
   - Updates student_fees
   - Returns receipt_id

4. POST /razorpay/webhook (Background)
   - Receives payment events
   - Logs events
   - Additional processing if needed
```

## API Endpoints

### Create Order
```
POST /razorpay/create-order
Headers: X-CSRF-TOKEN
Body: {
  student_fee_id: 1,
  amount: 28500
}
Response: {
  order_id: "order_xxxxx",
  amount: 2850000,
  currency: "INR",
  key: "rzp_test_xxxxx"
}
```

### Verify Payment
```
POST /razorpay/verify-payment
Headers: X-CSRF-TOKEN
Body: {
  razorpay_order_id: "order_xxxxx",
  razorpay_payment_id: "pay_xxxxx",
  razorpay_signature: "xxxxx",
  student_fee_id: 1
}
Response: {
  success: true,
  receipt_id: 123,
  receipt_number: "RCP2024ABC123"
}
```

### Webhook
```
POST /razorpay/webhook
Headers: X-Razorpay-Signature
Body: {
  event: "payment.captured",
  payload: { ... }
}
Response: {
  status: "ok"
}
```

## Security Features

### 1. Signature Verification
- Uses HMAC SHA256 algorithm
- Verifies payment authenticity
- Prevents tampering

```php
$signature = hash_hmac('sha256', 
    $order_id . '|' . $payment_id,
    $secret_key
);
```

### 2. Webhook Verification
- Validates webhook signature
- Ensures requests from Razorpay only
- Logs suspicious activity

### 3. Amount Validation
- Checks amount doesn't exceed outstanding
- Prevents overpayment
- Server-side validation

### 4. CSRF Protection
- All POST requests require CSRF token
- Laravel middleware protection

## Payment Modes Supported

1. **Credit/Debit Cards**
   - Visa, Mastercard, Amex, RuPay
   - Domestic and International

2. **Net Banking**
   - All major banks
   - Real-time payment

3. **UPI**
   - Google Pay, PhonePe, Paytm
   - QR code and VPA

4. **Wallets**
   - Paytm, PhonePe, Mobikwik
   - Instant payment

5. **EMI**
   - Card EMI
   - Cardless EMI

## Error Handling

### Payment Failures
```javascript
// Frontend error handling
rzp.on('payment.failed', function(response) {
    alert('Payment failed: ' + response.error.description);
});
```

### Signature Mismatch
```php
// Backend logging
if ($signature !== $expected) {
    \Log::error('Signature mismatch', [
        'order_id' => $order_id,
        'payment_id' => $payment_id
    ]);
    return response()->json(['error' => 'Invalid signature'], 400);
}
```

### Webhook Errors
```php
// Webhook validation
if ($webhookSignature !== $expectedSignature) {
    \Log::error('Webhook signature mismatch');
    return response()->json(['error' => 'Invalid signature'], 400);
}
```

## Testing

### Test Mode
1. Use test API keys (rzp_test_xxxxx)
2. Use test cards
3. No real money charged
4. All features work same as live

### Test Cards
```
Success: 4111 1111 1111 1111
Failure: 4000 0000 0000 0002
3D Secure: 5104 0600 0000 0008
```

### Test UPI
```
UPI ID: success@razorpay
```

### Test Wallets
All test wallets work in test mode

## Going Live

### Checklist
- [ ] Complete KYC verification
- [ ] Submit business documents
- [ ] Get account activated
- [ ] Switch to live API keys
- [ ] Update .env with live keys
- [ ] Test with small amount
- [ ] Monitor transactions
- [ ] Set up settlement account

### Live Mode Configuration
```env
RAZORPAY_KEY=rzp_live_xxxxxxxxxx
RAZORPAY_SECRET=xxxxxxxxxxxxxxxxxx
RAZORPAY_WEBHOOK_SECRET=xxxxxxxxxxxxxxxxxx
```

## Monitoring

### Payment Logs
- All payments logged in database
- Transaction IDs stored
- Status tracked

### Webhook Logs
- Events logged in Laravel log
- Payment captured/failed events
- Debugging information

### Dashboard Monitoring
- View transactions in Razorpay Dashboard
- Real-time payment status
- Settlement reports

## Troubleshooting

### Issue: Payment not reflecting
**Solution**: Check webhook configuration and logs

### Issue: Signature verification failed
**Solution**: Verify secret key in .env file

### Issue: Amount mismatch
**Solution**: Ensure amount in paise (multiply by 100)

### Issue: Webhook not receiving
**Solution**: Check webhook URL is publicly accessible

## Files Created

1. **RazorpayController.php** - Payment processing logic
2. **payment.blade.php** - Payment interface
3. **services.php** - Razorpay configuration
4. **.env.razorpay.example** - Environment template
5. **RAZORPAY_INTEGRATION.md** - This documentation

## Routes Added

```php
POST /razorpay/create-order       // Create payment order
POST /razorpay/verify-payment     // Verify payment
POST /razorpay/webhook            // Webhook handler
```

## Database Updates

No new tables required. Uses existing:
- `fee_payments` - Stores payment records
- `student_fees` - Updates outstanding amount

## Integration Complete ✅

The Razorpay payment gateway is now fully integrated with:
- Order creation
- Payment processing
- Signature verification
- Webhook handling
- Receipt generation
- Outstanding updates

## Next Steps

1. Install Razorpay SDK: `composer require razorpay/razorpay`
2. Add credentials to .env
3. Configure webhook in Razorpay Dashboard
4. Test with test mode
5. Go live after verification

## Support

- Razorpay Docs: https://razorpay.com/docs/
- Support: support@razorpay.com
- Dashboard: https://dashboard.razorpay.com
