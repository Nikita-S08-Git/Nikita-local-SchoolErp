# [P3-08] Add Payment Gateway Test Mode

## Objective
Implement sandbox mode for Razorpay testing without live transactions.

## Problem Statement
Testing payments requires live gateway. Test mode would allow safe testing.

## Expected Outcome
- Test mode toggle in settings
- Test card numbers documented

## Scope of Work
1. Add test mode to settings
2. Update payment controller
3. Document test credentials
4. Mark test transactions

## Files to Modify
- MODIFY: `app/Http/Controllers/Web/RazorpayController.php`
- MODIFY: `resources/views/settings/payment-gateways.blade.php`

## Dependencies
P2-12: Add Payment Gateway Configuration UI

## Acceptance Criteria
- [ ] Test mode toggle in settings
- [ ] Test card numbers documented
- [ ] Test transactions marked clearly
- [ ] No real charges in test mode
- [ ] Separate test credentials

## Developer Notes
Use Razorpay test keys:
```php
config('services.razorpay.test_key')
```
