# [P3-07] Add Grace Marks Configuration

## Objective
Build UI for configuring grace marks rules for borderline cases.

## Problem Statement
Institutions may want to add grace marks to students just below pass threshold.

## Expected Outcome
- Grace marks settings form
- Percentage or fixed marks option

## Scope of Work
1. Add grace marks to academic rules
2. Create configuration UI
3. Update result calculation

## Files to Modify
- MODIFY: `app/Models/Academic/AcademicRule.php`
- MODIFY: `resources/views/academic/rules/edit.blade.php`

## Dependencies
P1-09: Create Admin Panel for Academic Rules

## Acceptance Criteria
- [ ] Grace marks settings form
- [ ] Percentage or fixed marks option
- [ ] Subject-level or total level
- [ ] Maximum grace limit
- [ ] Applied during result calculation

## Developer Notes
Apply grace marks before pass/fail determination
