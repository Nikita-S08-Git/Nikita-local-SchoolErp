# [P2-03] Implement Auto-Save for Marks Entry

## Objective
Add draft saving every 30 seconds during marks entry to prevent data loss.

## Problem Statement
Teachers entering marks risk losing data if browser crashes or session expires.

## Expected Outcome
- Auto-save every 30 seconds
- Draft indicator visible
- Resume from draft on page reload

## Scope of Work
1. Add draft endpoint
2. Create JavaScript auto-save timer
3. Store drafts in database
4. Add resume functionality

## Files to Modify
- MODIFY: `app/Http/Controllers/Web/ExaminationController.php`
- MODIFY: `resources/views/examinations/marks-entry.blade.php`
- CREATE: `app/Models/Fee/MarksDraft.php`

## Dependencies
None

## Acceptance Criteria
- [ ] Auto-save every 30 seconds
- [ ] Draft indicator visible
- [ ] Resume from draft on page reload
- [ ] Clear draft on successful submit
- [ ] Manual save button available

## Developer Notes
Use localStorage as backup:
```javascript
setInterval(() => {
    saveDraft();
}, 30000);
```
