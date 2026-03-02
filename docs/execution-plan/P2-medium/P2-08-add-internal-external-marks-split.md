# [P2-08] Add Internal/External Marks Split

## Objective
Support separate internal and external mark entry and calculation.

## Problem Statement
Universities often require 40% internal and 60% external split. System currently only supports total marks.

## Expected Outcome
- Internal/external max marks configurable
- Separate mark entry for each
- Weighted total calculation

## Scope of Work
1. Update student_marks table
2. Modify marks entry form
3. Implement weighted calculation
4. Update marksheet display

## Files to Modify
- MODIFY: `database/migrations/xxxx_add_internal_external_to_student_marks.php`
- MODIFY: `resources/views/examinations/marks-entry.blade.php`
- MODIFY: `app/Models/Result/StudentMark.php`

## Dependencies
P0-09: Replace Hardcoded Pass Percentage

## Acceptance Criteria
- [ ] Internal/external max marks configurable
- [ ] Separate mark entry for each
- [ ] Weighted total calculation
- [ ] Pass criteria for each component
- [ ] Display split on marksheet

## Developer Notes
Weighted calculation:
```php
$total = ($internal * 0.4) + ($external * 0.6);
```
