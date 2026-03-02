# [P0-10] Fix Broken Dashboard Quick Action Links

## Objective
Replace placeholder href="#" links with actual route links in Student and Teacher dashboards.

## Problem Statement
Dashboard quick action buttons use href="#" instead of actual routes:

```blade
<!-- dashboard/student.blade.php -->
<a href="#" class="btn btn-outline-primary">
    <i class="bi bi-calendar-check"></i> View Attendance
</a>
<!-- ❌ Links don't go anywhere -->
```

## Expected Outcome
- All href="#" replaced with valid route() helper calls
- All quick action buttons navigate to correct pages
- No JavaScript console errors on click

## Scope of Work
1. Identify all href="#" in dashboard views
2. Replace with appropriate route() helper
3. Verify routes exist
4. Test all navigation links

## Files to Modify
- MODIFY: `resources/views/dashboard/student.blade.php`
- MODIFY: `resources/views/dashboard/teacher.blade.php`
- VERIFY: routes/web.php for route definitions

## Dependencies
None - This is a blocking task with no prerequisites

## Acceptance Criteria
- [ ] All href="#" replaced with valid route() helper calls
- [ ] All quick action buttons navigate to correct pages
- [ ] No JavaScript console errors on click
- [ ] Routes exist and are accessible
- [ ] Proper permissions/ middleware applied

## Developer Notes
Example fix:
```blade
<!-- Before -->
<a href="#" class="btn btn-outline-primary">View Attendance</a>

<!-- After -->
<a href="{{ route('student.attendance') }}" class="btn btn-outline-primary">View Attendance</a>
```
