# [P2-12] Add Payment Gateway Configuration UI

## Objective
Admin can configure Razorpay, PhonePe, or other payment gateway credentials from admin panel.

## Problem Statement
Payment gateway keys require .env file changes. Admin should be able to configure from UI.

## Expected Outcome
- Payment gateway settings form
- Support multiple gateways

## Scope of Work
1. Add gateway fields to settings
2. Create gateway configuration form
3. Implement encryption for credentials
4. Update payment controllers

## Files to Modify
- MODIFY: `app/Services/SettingsService.php`
- CREATE: `resources/views/settings/payment-gateways.blade.php`
- MODIFY: `config/services.php`

## Dependencies
P1-11: Implement System Settings Module

## Acceptance Criteria
- [ ] Payment gateway settings form
- [ ] Support multiple gateways
- [ ] Enable/disable toggle per gateway
- [ ] Test mode toggle
- [ ] Credentials encrypted in database

## Developer Notes
Encrypt sensitive credentials:
```php
Crypt::encryptString($value);
```
