# [P1-01] Build Promotion Web UI

## Objective
Create admin web interface for student promotion workflow currently only available via API.

## Problem Statement
PromotionService and API controllers exist but there is no web UI for admins to promote students.

## Expected Outcome
- Promotion index page showing eligible students
- Bulk promotion action with preview
- Individual student promotion with eligibility check
- ATKT/conditional promotion support

## Scope of Work
1. Create PromotionController for web
2. Build promotion index view with student list
3. Create promotion preview modal
4. Implement bulk promotion action
5. Add promotion history view
6. Create rollback UI

## Files to Modify
- CREATE: `app/Http/Controllers/Web/PromotionController.php`
- CREATE: `resources/views/academic/promotions/index.blade.php`
- CREATE: `resources/views/academic/promotions/preview.blade.php`
- CREATE: `resources/views/academic/promotions/history.blade.php`
- MODIFY: `routes/web.php`

## Dependencies
P0-09: Replace Hardcoded Pass Percentage

## Acceptance Criteria
- [ ] Promotion index page showing eligible students
- [ ] Bulk promotion action with preview
- [ ] Individual student promotion with eligibility check
- [ ] ATKT/conditional promotion support
- [ ] Promotion history and rollback capability
- [ ] All promotion actions require admin/principal role

## Developer Notes
- Use existing PromotionService for business logic
- Display eligibility criteria clearly to users
- Show warnings for borderline cases
- Log all promotion actions for audit
