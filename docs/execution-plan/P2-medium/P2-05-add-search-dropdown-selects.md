# [P2-05] Add Search to Dropdown Selects

## Objective
Implement searchable dropdowns for program, division, and subject selectors.

## Problem Statement
Long dropdown lists make it hard to find specific options.

## Expected Outcome
- Select2 or TomSelect library integrated
- Search box in dropdown
- Type-ahead suggestions

## Scope of Work
1. Include Select2 library
2. Apply to all long dropdowns
3. Configure search options
4. Test mobile compatibility

## Files to Modify
- MODIFY: `resources/views/layouts/app.blade.php`
- MODIFY: All forms with long dropdowns

## Dependencies
None

## Acceptance Criteria
- [ ] Select2 or TomSelect library integrated
- [ ] Search box in dropdown
- [ ] Type-ahead suggestions
- [ ] Applied to all long dropdowns
- [ ] Mobile-friendly touch support

## Developer Notes
Initialize Select2:
```javascript
$('.select2').select2({
    placeholder: 'Search...',
    allowClear: true
});
```
