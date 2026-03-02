# [P2-10] Add Dynamic Max Marks in Marks Entry

## Objective
Load max_marks from subject configuration instead of hardcoded value of 100.

## Problem Statement
Marks entry form has hardcoded max_marks=100. Different subjects may have different maximum marks.

## Expected Outcome
- Max marks stored in Subject model
- Marks entry form loads dynamic max

## Scope of Work
1. Add max_marks to subjects table
2. Update marks entry form
3. Implement dynamic validation

## Files to Modify
- MODIFY: `database/migrations/xxxx_add_max_marks_to_subjects.php`
- MODIFY: `resources/views/examinations/marks-entry.blade.php`
- MODIFY: `app/Models/Result/Subject.php`

## Dependencies
None

## Acceptance Criteria
- [ ] Max marks stored in Subject model
- [ ] Marks entry form loads dynamic max
- [ ] Validation uses subject max marks
- [ ] Display max marks on form
- [ ] Percentage calculation uses correct max

## Developer Notes
Add max_marks column to subjects table
