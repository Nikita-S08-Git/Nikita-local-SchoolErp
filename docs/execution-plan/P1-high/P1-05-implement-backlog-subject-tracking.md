# [P1-05] Implement Backlog Subject Tracking

## Objective
Build database and UI for tracking failed subjects per student for ATKT workflow.

## Problem Statement
System needs to track which specific subjects a student failed.

## Expected Outcome
- BacklogSubject model and migration created
- Failed subjects recorded per examination
- Attempt counter per subject
- UI to view backlog subjects per student

## Scope of Work
1. Create BacklogSubject model
2. Create migration for backlog_subjects table
3. Update marks entry to record failures
4. Build backlog tracking UI
5. Implement subject clearance on passing
6. Add attempt counter logic

## Files to Modify
- CREATE: `app/Models/Academic/BacklogSubject.php`
- CREATE: `database/migrations/xxxx_create_backlog_subjects_table.php`
- MODIFY: `app/Http/Controllers/Web/ExaminationController.php`
- CREATE: `resources/views/academic/backlogs/index.blade.php`

## Dependencies
None

## Acceptance Criteria
- [ ] BacklogSubject model and migration created
- [ ] Failed subjects recorded per examination
- [ ] Attempt counter per subject
- [ ] UI to view backlog subjects per student
- [ ] Subject clearance on passing ATKT exam
- [ ] Maximum attempts enforcement

## Developer Notes
Table structure:
```php
Schema::create('backlog_subjects', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id');
    $table->foreignId('subject_id');
    $table->foreignId('examination_id');
    $table->integer('attempt_count')->default(1);
    $table->boolean('cleared')->default(false);
    $table->timestamps();
});
```
