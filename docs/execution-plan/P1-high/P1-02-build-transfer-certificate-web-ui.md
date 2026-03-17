# [P1-02] Build Transfer Certificate Web UI

## Objective
Create admin web interface for transfer certificate workflow currently only available via API.

## Problem Statement
TransferService and API controllers exist but there is no web UI for admins to process TC requests.

## Expected Outcome
- TC request listing page
- TC approval/rejection workflow
- TC document generation (PDF)
- Student status update on TC issuance

## Scope of Work
1. Create TransferController for web
2. Build TC request index view
3. Create TC approval/rejection forms
4. Implement TC PDF generation
5. Add student status update logic
6. Create TC history view

## Files to Modify
- CREATE: `app/Http/Controllers/Web/TransferController.php`
- CREATE: `resources/views/academic/transfers/index.blade.php`
- CREATE: `resources/views/academic/transfers/show.blade.php`
- CREATE: `resources/views/academic/transfers/approve.blade.php`
- CREATE: `resources/pdf/transfer-certificate.blade.php`
- MODIFY: `routes/web.php`

## Dependencies
None

## Acceptance Criteria
- [ ] TC request listing page
- [ ] TC approval/rejection workflow
- [ ] TC document generation (PDF)
- [ ] Student status update on TC issuance
- [ ] Transfer history tracking
- [ ] TC number auto-generation

## Developer Notes
- Use existing TransferService for business logic
- TC PDF should match institutional format
- Include student photo and signatures
- Generate unique TC number per institution
