# [P2-04] Add Excel Export to All Tables

## Objective
Implement spreadsheet export for students, teachers, fees, and attendance tables.

## Problem Statement
Users need to export data to Excel for offline analysis but only PDF export exists.

## Expected Outcome
- Export button on all major tables
- Excel format (.xlsx) export
- All columns included in export

## Scope of Work
1. Create Export classes for each model
2. Add export buttons to views
3. Implement export endpoints
4. Add filtered export support

## Files to Modify
- CREATE: `app/Exports/StudentsExport.php`
- CREATE: `app/Exports/TeachersExport.php`
- CREATE: `app/Exports/FeesExport.php`
- MODIFY: All index controllers
- MODIFY: All index views

## Dependencies
None

## Acceptance Criteria
- [ ] Export button on all major tables
- [ ] Excel format (.xlsx) export
- [ ] All columns included in export
- [ ] Filtered data exported
- [ ] File naming with timestamp

## Developer Notes
Use Laravel Excel:
```php
return Excel::download(new StudentsExport, 'students.xlsx');
```
