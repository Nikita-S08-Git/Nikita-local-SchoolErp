# [P0-04] Consolidate Duplicate Timetable Models

## Objective
Merge the duplicate Timetable models into a single canonical model to eliminate namespace confusion.

## Problem Statement
Two Timetable models exist:

```
app/Models/Attendance/Timetable.php
app/Models/Academic/Timetable.php
```

Both have similar properties but different namespaces causing confusion.

## Expected Outcome
- Single Timetable model in appropriate namespace
- All controllers use consolidated model
- All relationships properly defined

## Scope of Work
1. Analyze both models for unique properties
2. Create unified Timetable model
3. Update Web and API controllers
4. Delete duplicate model
5. Update all namespaces

## Files to Modify
- CREATE: `app/Models/Academic/Timetable.php` (unified)
- DELETE: `app/Models/Attendance/Timetable.php`
- UPDATE: All timetable controllers

## Dependencies
None - This is a blocking task with no prerequisites

## Acceptance Criteria
- [ ] Single Timetable model established
- [ ] All controllers updated
- [ ] All relationships properly defined
- [ ] No duplicate model files remain
- [ ] All tests pass

## Developer Notes
- Academic namespace is more appropriate for timetable
- Ensure day_of_week enum is consistent
- Check all relationship definitions
