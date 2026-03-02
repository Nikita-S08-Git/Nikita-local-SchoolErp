# [P0-03] Consolidate Duplicate Attendance Models

## Objective
Merge the duplicate Attendance models into a single canonical model to eliminate namespace confusion.

## Problem Statement
Two Attendance models exist with different schemas:

```
app/Models/Attendance/Attendance.php    (table: 'attendance')
app/Models/Academic/Attendance.php      (table: inferred 'attendances')
```

Different properties:
- Attendance/Attendance: attendance_date, check_in_time, marked_by
- Academic/Attendance: date, division_id, academic_session_id

## Expected Outcome
- Single Attendance model in appropriate namespace
- All controllers use consolidated model
- Database schema aligned with model

## Scope of Work
1. Analyze both models and identify required fields
2. Create unified Attendance model
3. Update all controllers and services
4. Delete duplicate model file
5. Update namespaces in all references

## Files to Modify
- CREATE: `app/Models/Academic/Attendance.php` (unified)
- DELETE: `app/Models/Attendance/Attendance.php`
- UPDATE: All controllers referencing Attendance models

## Dependencies
None - This is a blocking task with no prerequisites

## Acceptance Criteria
- [ ] Single Attendance model established in appropriate namespace
- [ ] All controllers updated to use consolidated model
- [ ] Database schema aligned with model properties
- [ ] No duplicate model files remain
- [ ] All tests pass

## Developer Notes
- Keep the model that has more complete relationships
- Ensure migration matches final model structure
- Update all use statements across codebase
