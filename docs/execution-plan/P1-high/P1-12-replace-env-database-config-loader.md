# [P1-12] Replace .env Runtime Dependency with Database Config Loader

## Objective
Load dynamic configuration from database instead of static .env file where applicable.

## Problem Statement
Changing config requires file system access and cache clear.

## Expected Outcome
- ConfigLoader service created
- Database config takes precedence over .env
- Settings module integrated with loader
- Cache invalidation on settings change

## Scope of Work
1. Create ConfigLoader service
2. Implement database-first config loading
3. Create fallback to .env for sensitive data
4. Add cache invalidation logic
5. Update mail, payment, academic config

## Files to Modify
- CREATE: `app/Services/ConfigLoader.php`
- MODIFY: `app/Providers/AppServiceProvider.php`
- MODIFY: `config/mail.php`
- MODIFY: `config/services.php`
- MODIFY: `config/schoolerp.php`

## Dependencies
P1-11: Implement System Settings Module

## Acceptance Criteria
- [ ] ConfigLoader service created
- [ ] Database config takes precedence over .env
- [ ] Settings module integrated with loader
- [ ] Cache invalidation on settings change
- [ ] Fallback to .env for sensitive data
- [ ] No breaking changes to existing config

## Developer Notes
Implementation approach:
```php
class ConfigLoader {
    public function get($key, $default = null) {
        // Try database first
        $setting = Setting::where('key', $key)->first();
        if ($setting) {
            return $this->castValue($setting->value, $setting->type);
        }
        // Fallback to config
        return config($key, $default);
    }
}
```
