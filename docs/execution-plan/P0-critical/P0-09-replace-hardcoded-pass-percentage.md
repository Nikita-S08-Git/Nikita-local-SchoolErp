# [P0-09] Replace Hardcoded Pass Percentage

## Objective
Move the hardcoded 40% pass threshold from controllers to the AcademicRule configuration system.

## Problem Statement
Pass percentage is hardcoded in multiple controllers:

```php
// ExaminationController.php line 119
$result = $percentage >= 40 ? 'pass' : 'fail'; // ❌ HARDCODED

// ResultController.php lines 62, 111
'result' => $percentage >= 40 ? 'Pass' : 'Fail', // ❌ HARDCODED
```

AcademicRule model exists but is not used.

## Expected Outcome
- Pass percentage loaded from AcademicRule model
- All hardcoded 40% values replaced
- Admin can modify without code changes

## Scope of Work
1. Ensure AcademicRule has PASS_PERCENTAGE rule
2. Create service to fetch academic rules
3. Replace all hardcoded 40 with dynamic value
4. Set default of 40% for backward compatibility
5. Test all pass/fail calculations

## Files to Modify
- MODIFY: `app/Http/Controllers/Web/ExaminationController.php`
- MODIFY: `app/Http/Controllers/Web/ResultController.php`
- CREATE: `app/Services/AcademicRuleService.php`
- VERIFY: `app/Models/Academic/AcademicRule.php`

## Dependencies
None - This is a blocking task with no prerequisites

## Acceptance Criteria
- [ ] Pass percentage loaded from AcademicRule model
- [ ] All hardcoded 40% values replaced with dynamic config
- [ ] Default value of 40% maintained for backward compatibility
- [ ] Admin can modify pass percentage without code changes
- [ ] All pass/fail calculations use dynamic value

## Developer Notes
Use AcademicRuleService:
```php
$passPercentage = AcademicRuleService::get('PASS_PERCENTAGE', 40);
$result = $percentage >= $passPercentage ? 'pass' : 'fail';
```
