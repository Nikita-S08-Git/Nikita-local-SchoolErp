# [P2-07] Implement Subject-Wise Pass Criteria

## Objective
Allow different pass percentages per subject type (theory vs practical).

## Problem Statement
All subjects use same 40% pass threshold. Some institutions require different criteria.

## Expected Outcome
- Pass percentage configurable per subject
- Theory/practical separate thresholds

## Scope of Work
1. Add pass_percentage to subjects table
2. Update marks calculation
3. Create configuration UI

## Files to Modify
- MODIFY: `database/migrations/xxxx_add_pass_percentage_to_subjects.php`
- MODIFY: `app/Models/Result/Subject.php`
- MODIFY: `app/Http/Controllers/Web/ExaminationController.php`

## Dependencies
P0-09: Replace Hardcoded Pass Percentage

## Acceptance Criteria
- [ ] Pass percentage configurable per subject
- [ ] Theory/practical separate thresholds
- [ ] Aggregate calculation respects criteria
- [ ] UI shows subject-wise pass status

## Developer Notes
Add to subjects table:
```php
$table->integer('theory_pass_percentage')->default(40);
$table->integer('practical_pass_percentage')->default(40);
```
