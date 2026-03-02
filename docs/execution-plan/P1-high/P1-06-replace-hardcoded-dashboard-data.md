# [P1-06] Replace Hardcoded Dashboard Data

## Objective
Connect Student, Teacher, Accounts, and Librarian dashboards to real database queries instead of hardcoded values.

## Problem Statement
Most role dashboards show fake hardcoded data like 85% attendance, 120 students, etc.

## Expected Outcome
- Student dashboard shows real attendance, fees, subjects
- Teacher dashboard shows real student count, classes
- Accounts dashboard shows real fee collection data
- Librarian dashboard shows real book statistics

## Scope of Work
1. Update dashboard controller methods with real queries
2. Replace hardcoded HTML with dynamic variables
3. Add proper eager loading for performance
4. Calculate real attendance percentages
5. Fetch real fee status from database

## Files to Modify
- MODIFY: `app/Http/Controllers/DashboardController.php`
- MODIFY: `app/Http/Controllers/Web/TeacherDashboardController.php`
- MODIFY: `resources/views/dashboard/student.blade.php`
- MODIFY: `resources/views/dashboard/teacher.blade.php`
- MODIFY: `resources/views/dashboard/accounts.blade.php`
- MODIFY: `resources/views/dashboard/librarian.blade.php`

## Dependencies
None

## Acceptance Criteria
- [ ] Student dashboard shows real attendance, fees, subjects
- [ ] Teacher dashboard shows real student count, classes
- [ ] Accounts dashboard shows real fee collection data
- [ ] Librarian dashboard shows real book statistics
- [ ] All stats update dynamically from database
- [ ] No hardcoded values remain

## Developer Notes
Example query for attendance:
```php
$attendance = Attendance::where('student_id', $studentId)
    ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
    ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status="present" THEN 1 ELSE 0 END) as present')
    ->first();
$percentage = $attendance->total > 0 ? ($attendance->present / $attendance->total) * 100 : 0;
```
