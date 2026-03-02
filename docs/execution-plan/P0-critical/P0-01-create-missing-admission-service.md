# [P0-01] Create Missing AdmissionService Class

## Objective
Implement the missing AdmissionService class or remove the dependency from AdmissionController to prevent runtime errors.

## Problem Statement
The AdmissionController constructor injects AdmissionService which does not exist in the codebase. This causes a fatal error when the controller is instantiated:

```php
// app/Http/Controllers/Web/AdmissionController.php
public function __construct(AdmissionService $admissionService)
{
    $this->admissionService = $admissionService; // ❌ FILE DOES NOT EXIST
}
```

## Expected Outcome
- AdmissionService class exists and is functional
- OR AdmissionController works without the service dependency
- No runtime errors on admission pages

## Scope of Work
1. Create `app/Services/AdmissionService.php`
2. Implement methods referenced in AdmissionController
3. Add proper dependency injection
4. Write unit tests

## Files to Modify
- CREATE: `app/Services/AdmissionService.php`
- MODIFY: `app/Http/Controllers/Web/AdmissionController.php` (if needed)
- CREATE: `tests/Unit/AdmissionServiceTest.php`

## Dependencies
None - This is a blocking task with no prerequisites

## Acceptance Criteria
- [ ] AdmissionService class created in app/Services/ directory OR dependency removed
- [ ] All methods referenced in AdmissionController are implemented
- [ ] Unit tests pass for admission enrollment flow
- [ ] No runtime errors on admission pages
- [ ] Service properly injected via constructor

## Developer Notes
- Check StudentService.php for similar service pattern
- Ensure transaction handling for enrollment operations
- Consider using interfaces for better testability
