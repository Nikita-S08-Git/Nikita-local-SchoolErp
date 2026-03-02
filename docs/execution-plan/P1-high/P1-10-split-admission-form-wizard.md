# [P1-10] Split Student Admission Form into Multi-Step Wizard

## Objective
Break the 408-line single-page admission form into logical multi-step wizard.

## Problem Statement
Student admission form has 35+ fields on one page causing user fatigue and errors.

## Expected Outcome
- Step 1: Personal Information
- Step 2: Contact Details
- Step 3: Academic Details
- Step 4: Document Upload
- Progress indicator
- Step validation before proceed

## Scope of Work
1. Refactor form into 4 separate views
2. Create wizard navigation component
3. Implement step validation
4. Add progress indicator
5. Store data in session between steps
6. Submit all data on final step

## Files to Modify
- MODIFY: `app/Http/Controllers/Web/StudentController.php`
- CREATE: `resources/views/dashboard/students/create-step1.blade.php`
- CREATE: `resources/views/dashboard/students/create-step2.blade.php`
- CREATE: `resources/views/dashboard/students/create-step3.blade.php`
- CREATE: `resources/views/dashboard/students/create-step4.blade.php`
- CREATE: `resources/views/components/wizard-progress.blade.php`

## Dependencies
None

## Acceptance Criteria
- [ ] Step 1: Personal Information
- [ ] Step 2: Contact Details
- [ ] Step 3: Academic Details
- [ ] Step 4: Document Upload
- [ ] Progress indicator
- [ ] Step validation before proceed
- [ ] Data preserved between steps
- [ ] Final submit creates student record

## Developer Notes
Use session to store data:
```php
// Store step data
session()->put('admission.step1', $validated);

// Retrieve on final submit
$data = array_merge(
    session()->get('admission.step1'),
    session()->get('admission.step2'),
    // ...
);
```
