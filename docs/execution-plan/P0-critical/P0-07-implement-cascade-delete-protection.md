# [P0-07] Implement Cascade Delete Protection

## Objective
Add foreign key checks and cascade delete protection to prevent orphaned records.

## Problem Statement
Currently students with fee records and teachers with timetables can be deleted:

```php
// app/Http/Controllers/Web/StudentController.php
public function destroy(Student $student)
{
    $student->delete(); // ❌ No check for fee records
}

// app/Http/Controllers/Web/TeacherController.php
public function destroy(User $teacher)
{
    $teacher->delete(); // ❌ No check for timetable assignments
}
```

## Expected Outcome
- Foreign key constraints added to database
- Delete methods check for child records
- User-friendly error messages when deletion blocked

## Scope of Work
1. Add foreign key constraints to migrations
2. Update destroy methods to check for child records
3. Add user-friendly error messages
4. Implement soft delete where appropriate
5. Add cascade delete for dependent records

## Files to Modify
- MODIFY: Student migration (add FK constraints)
- MODIFY: `app/Http/Controllers/Web/StudentController.php`
- MODIFY: `app/Http/Controllers/Web/TeacherController.php`
- MODIFY: Related migrations

## Dependencies
None - This is a blocking task with no prerequisites

## Acceptance Criteria
- [ ] Foreign key constraints added to database
- [ ] Delete methods check for child records before deletion
- [ ] User-friendly error messages shown when deletion blocked
- [ ] Soft delete implemented where appropriate
- [ ] No orphaned records possible

## Developer Notes
Example implementation:
```php
public function destroy(Student $student)
{
    if ($student->fees()->exists()) {
        return back()->withErrors(['Student has fee records. Clear fees first.']);
    }
    $student->delete();
}
```
