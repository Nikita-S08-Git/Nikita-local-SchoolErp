# [P2-11] Add SMTP Configuration UI

## Objective
Admin can configure mail server, sender email, and SMTP credentials from admin panel.

## Problem Statement
Email configuration requires .env file changes. Admin should be able to configure SMTP from UI.

## Expected Outcome
- SMTP settings form in admin panel
- Test email button

## Scope of Work
1. Add SMTP fields to settings
2. Create SMTP configuration form
3. Implement test email feature
4. Update mail config to use settings

## Files to Modify
- MODIFY: `app/Services/SettingsService.php`
- CREATE: `resources/views/settings/smtp.blade.php`
- MODIFY: `config/mail.php`

## Dependencies
P1-11: Implement System Settings Module

## Acceptance Criteria
- [ ] SMTP settings form in admin panel
- [ ] Host, port, encryption, credentials fields
- [ ] Test email button
- [ ] Settings stored in database
- [ ] Mail config loaded from settings

## Developer Notes
Clear mail cache after changes:
```php
Artisan::call('config:clear');
```
