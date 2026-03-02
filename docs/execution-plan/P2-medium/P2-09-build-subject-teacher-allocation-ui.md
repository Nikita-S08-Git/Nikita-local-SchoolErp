# [P2-09] Build Subject-Teacher Allocation UI

## Objective
Create interface for assigning teachers to subjects for timetable and marks entry.

## Problem Statement
TeacherSubject model exists but there is no UI to allocate teachers to subjects.

## Expected Outcome
- Subject-teacher allocation form
- View allocations by teacher/subject

## Scope of Work
1. Create allocation controller
2. Build allocation form
3. Add conflict detection
4. Create allocation view

## Files to Modify
- CREATE: `app/Http/Controllers/Web/SubjectAllocationController.php`
- CREATE: `resources/views/academic/allocation/index.blade.php`

## Dependencies
None

## Acceptance Criteria
- [ ] Subject-teacher allocation form
- [ ] View allocations by teacher/subject
- [ ] Bulk allocation support
- [ ] Conflict detection
- [ ] Allocation history tracking

## Developer Notes
Use TeacherSubject model for storage
