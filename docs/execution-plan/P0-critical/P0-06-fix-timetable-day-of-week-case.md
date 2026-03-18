# [P0-06] Fix Timetable day_of_week Case Mismatch

## Objective
Standardize day_of_week enum values across migrations, models, and controllers.

## Problem Statement
Inconsistent day name casing:

**Migration:**
```php
$table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
```

**Web Controller:**
```php
'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'
```

**API Controller:**
```php
'in:monday,tuesday,wednesday,thursday,friday,saturday'
```

## Expected Outcome
- Enum values standardized to single case format
- All controllers use consistent day name format
- No enum mismatch errors

## Scope of Work
1. Decide on standard case (recommend lowercase)
2. Update migration enum
3. Update all validation rules
4. Update any hardcoded day comparisons
5. Create migration to fix existing data

## Files to Modify
- MODIFY: Timetable migration
- MODIFY: `app/Http/Controllers/Web/TimetableController.php`
- MODIFY: `app/Http/Controllers/Api/Attendance/TimetableController.php`
- CREATE: Migration to update existing data

## Dependencies
P0-04: Consolidate Duplicate Timetable Models

## Acceptance Criteria
- [ ] Enum values standardized to single case format
- [ ] All controllers use consistent day name format
- [ ] Validation rules updated to match
- [ ] No enum mismatch errors
- [ ] Existing data updated to new format

## Developer Notes
- Lowercase recommended for database storage
- Can display capitalized in views using ucfirst()
- Update all blade views that display day names
