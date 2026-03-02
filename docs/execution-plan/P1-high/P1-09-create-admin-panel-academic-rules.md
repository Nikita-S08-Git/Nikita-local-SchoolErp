# [P1-09] Create Admin Panel for Academic Rules

## Objective
Build UI to configure pass percentage, attendance rules, ATKT limits without code changes.

## Problem Statement
AcademicRule model exists but there is no admin interface to configure rules.

## Expected Outcome
- Academic rules listing page
- Rule configuration form with validation
- Effective date range support
- Rule history and audit trail

## Scope of Work
1. Create AcademicRuleController
2. Build rules index view
3. Create rule configuration form
4. Implement rule validation
5. Add effective date handling
6. Create rule history view

## Files to Modify
- CREATE: `app/Http/Controllers/Web/AcademicRuleController.php`
- CREATE: `resources/views/academic/rules/index.blade.php`
- CREATE: `resources/views/academic/rules/edit.blade.php`
- CREATE: `resources/views/academic/rules/history.blade.php`
- MODIFY: `routes/web.php`

## Dependencies
P0-09: Replace Hardcoded Pass Percentage

## Acceptance Criteria
- [ ] Academic rules listing page
- [ ] Rule configuration form with validation
- [ ] Effective date range support
- [ ] Rule history and audit trail
- [ ] Rules applied dynamically to calculations
- [ ] Only admin/principal can modify rules

## Developer Notes
Rules to configure:
- PASS_PERCENTAGE
- MIN_ATTENDANCE
- ATTENDANCE_GRACE
- ATKT_MAX_SUBJECTS
- ATKT_MAX_ATTEMPTS
- GRACE_MARKS
- FEE_CLEARANCE_REQUIRED
