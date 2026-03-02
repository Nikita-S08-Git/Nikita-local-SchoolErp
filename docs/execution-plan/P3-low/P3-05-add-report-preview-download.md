# [P3-05] Add Report Preview Before Download

## Objective
Show report in modal before PDF generation and download.

## Problem Statement
Users cannot preview reports before downloading. Preview would reduce unnecessary downloads.

## Expected Outcome
- Preview modal for reports
- Print from preview

## Scope of Work
1. Create preview endpoint
2. Build preview modal
3. Add print functionality
4. Implement download from preview

## Files to Modify
- MODIFY: All report controllers
- CREATE: `resources/views/reports/preview-modal.blade.php`

## Dependencies
None

## Acceptance Criteria
- [ ] Preview modal for reports
- [ ] Print from preview
- [ ] Download from preview
- [ ] Close preview option
- [ ] Loading state during generation

## Developer Notes
Use iframe for preview or render HTML directly
