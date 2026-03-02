# [P1-04] Create ATKT Exam Registration Workflow

## Objective
Build backlog exam registration form and subject selection for ATKT students.

## Problem Statement
ATKT students need to register for backlog exams but there is no workflow.

## Expected Outcome
- ATKT student eligibility check
- Subject selection form for backlog subjects
- ATKT exam fee calculation and payment
- Hall ticket generation with exam schedule

## Scope of Work
1. Create ATKTRegistrationController
2. Build eligibility check logic
3. Create subject selection form
4. Implement ATKT fee calculation
5. Build hall ticket PDF generator
6. Add email confirmation

## Files to Modify
- CREATE: `app/Http/Controllers/Web/ATKTRegistrationController.php`
- CREATE: `resources/views/atkt/registration.blade.php`
- CREATE: `resources/views/atkt/subject-selection.blade.php`
- CREATE: `resources/pdf/hall-ticket.blade.php`
- MODIFY: `routes/web.php`

## Dependencies
P1-05: Implement Backlog Subject Tracking

## Acceptance Criteria
- [ ] ATKT student eligibility check
- [ ] Subject selection form for backlog subjects
- [ ] ATKT exam fee calculation and payment
- [ ] Hall ticket generation with exam schedule
- [ ] Registration confirmation email
- [ ] Attempt counter tracking

## Developer Notes
- Check backlog_count from StudentAcademicRecord
- Limit subjects based on ATKT rules
- Integrate with payment gateway for fees
- Include exam schedule in hall ticket
