# [P1-11] Implement System Settings Module

## Objective
Create central configuration system for branding, email, payment, and academic rules.

## Problem Statement
System lacks centralized settings management. Configuration is scattered across .env files and hardcoded values.

## Expected Outcome
- settings table created for key-value config
- SettingsService for CRUD operations
- Admin settings panel with categories
- Settings cached for performance

## Scope of Work
1. Create settings table migration
2. Create Setting model
3. Create SettingsService
4. Build admin settings panel
5. Implement caching layer
6. Update modules to use settings

## Files to Modify
- CREATE: `database/migrations/xxxx_create_settings_table.php`
- CREATE: `app/Models/Setting.php`
- CREATE: `app/Services/SettingsService.php`
- CREATE: `app/Http/Controllers/Web/SettingsController.php`
- CREATE: `resources/views/settings/index.blade.php`
- MODIFY: `routes/web.php`

## Dependencies
None

## Acceptance Criteria
- [ ] settings table created for key-value config
- [ ] SettingsService for CRUD operations
- [ ] Admin settings panel with categories
- [ ] Settings cached for performance
- [ ] All modules can access settings
- [ ] Settings change invalidates cache

## Developer Notes
Table structure:
```php
Schema::create('settings', function (Blueprint $table) {
    $table->id();
    $table->string('key')->unique();
    $table->text('value')->nullable();
    $table->string('type')->default('string');
    $table->string('group')->default('general');
    $table->boolean('is_public')->default(false);
    $table->timestamps();
});
```
