# [P2-02] Add Bulk Actions to Student List

## Objective
Add checkbox column with bulk delete and status change for students.

## Problem Statement
Admin must delete students one by one. Bulk operations would save significant time.

## Expected Outcome
- Checkbox column added to table
- Select all/none toggle
- Bulk delete action

## Scope of Work
1. Add checkbox column to table
2. Create bulk action form
3. Implement bulk delete logic
4. Add bulk status change

## Files to Modify
- MODIFY: `resources/views/dashboard/students/index.blade.php`
- MODIFY: `app/Http/Controllers/Web/StudentController.php`

## Dependencies
None

## Acceptance Criteria
- [ ] Checkbox column added to table
- [ ] Select all/none toggle
- [ ] Bulk delete action
- [ ] Bulk status change action
- [ ] Confirmation dialog for bulk actions

## Developer Notes
Use JavaScript for select all:
```javascript
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.student-checkbox').forEach(cb => {
        cb.checked = this.checked;
    });
});
```
