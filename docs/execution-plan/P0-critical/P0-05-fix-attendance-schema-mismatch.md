# [P0-05] Fix Attendance Schema Mismatch

## Objective
Align the attendance migration columns with the model properties to ensure proper data access.

## Problem Statement
Migration and model have mismatched columns:

**Migration (2024_01_06_000001):**
```php
$table->date('attendance_date');
$table->enum('status', ['present', 'absent', 'late']);
```

**Model (Academic/Attendance):**
```php
protected $fillable = ['date', 'status']; // Uses 'date' not 'attendance_date'
```

## Expected Outcome
- Migration columns match model property names
- Status enum values consistent
- All attendance CRUD operations work correctly

## Scope of Work
1. Update migration to use 'date' instead of 'attendance_date'
2. Standardize status enum values (lowercase)
3. Create new migration to fix existing schema
4. Update model casts if needed

## Files to Modify
- CREATE: New migration for schema fix
- MODIFY: `app/Models/Academic/Attendance.php`
- MODIFY: Attendance controllers

## Dependencies
P0-03: Consolidate Duplicate Attendance Models

## Acceptance Criteria
- [ ] Migration columns match model property names
- [ ] Status enum values consistent across migration and model
- [ ] All attendance CRUD operations work correctly
- [ ] No SQL errors on attendance queries
- [ ] Existing data migrated if needed

## Developer Notes
- Create new migration rather than modifying existing
- Test with fresh database and existing data
- Ensure model $casts match column types
