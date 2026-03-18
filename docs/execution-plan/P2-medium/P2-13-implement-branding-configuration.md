# [P2-13] Implement Branding Configuration

## Objective
Admin can set app name, logo, primary color, and visual identity from admin panel.

## Problem Statement
Branding elements are hardcoded in layouts. Admin should be able to customize for white-label deployment.

## Expected Outcome
- Branding settings form
- Logo upload with preview
- Primary/accent color pickers

## Scope of Work
1. Add branding fields to settings
2. Create branding configuration form
3. Update layouts to use settings
4. Implement logo upload

## Files to Modify
- MODIFY: `app/Services/SettingsService.php`
- CREATE: `resources/views/settings/branding.blade.php`
- MODIFY: `resources/views/layouts/app.blade.php`

## Dependencies
P1-11: Implement System Settings Module

## Acceptance Criteria
- [ ] Branding settings form
- [ ] Logo upload with preview
- [ ] Primary/accent color pickers
- [ ] App name configuration
- [ ] Changes reflected across all views

## Developer Notes
Store logo in public storage:
```php
$logo->store('branding', 'public');
```
