# [P2-01] Add Column Sorting to Data Tables

## Objective
Implement clickable header sorting for students, teachers, and fees tables.

## Problem Statement
Data tables show records but column headers are not clickable for sorting.

## Expected Outcome
- All table headers clickable
- Sort direction toggle (asc/desc)
- Visual indicator for current sort

## Scope of Work
1. Add sort query parameter handling
2. Update controller to accept sort parameters
3. Add clickable headers in blade views
4. Add visual sort indicators

## Files to Modify
- MODIFY: All index controllers
- MODIFY: All index blade views

## Dependencies
None

## Acceptance Criteria
- [ ] All table headers clickable
- [ ] Sort direction toggle (asc/desc)
- [ ] Visual indicator for current sort
- [ ] Sort persists across pagination
- [ ] Multiple column sort support

## Developer Notes
Use query parameters:
```php
// Controller
$students = Student::orderBy($sortBy, $sortDir)->paginate(20);

// View
<a href="?sort=name&dir={{ $sortDir === 'asc' ? 'desc' : 'asc' }}">Name</a>
```
