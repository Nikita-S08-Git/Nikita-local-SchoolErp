# [P1-03] Implement Consolidated Marksheet Generator

## Objective
Create PDF result sheet with all subjects, grades, totals, and GPA calculations.

## Problem Statement
Currently marks can be entered but there is no way to generate a consolidated marksheet.

## Expected Outcome
- PDF marksheet with student details and photo
- All subjects with marks, grades, and credits
- Semester GPA and cumulative GPA calculation
- Pass/fail status and classification

## Scope of Work
1. Create ResultService for marksheet generation
2. Design marksheet PDF template
3. Implement GPA/CGPA calculation
4. Add student photo and signatures
5. Create marksheet preview view
6. Implement download functionality

## Files to Modify
- CREATE: `app/Services/ResultService.php`
- CREATE: `app/Http/Controllers/Web/ResultController.php` (add marksheet method)
- CREATE: `resources/pdf/marksheet.blade.php`
- CREATE: `resources/views/results/marksheet-preview.blade.php`
- MODIFY: `routes/web.php`

## Dependencies
P0-09: Replace Hardcoded Pass Percentage
P2-08: Add Internal/External Marks Split

## Acceptance Criteria
- [ ] PDF marksheet with student details and photo
- [ ] All subjects with marks, grades, and credits
- [ ] Semester GPA and cumulative GPA calculation
- [ ] Pass/fail status and classification
- [ ] Official formatting with signatures
- [ ] Downloadable and printable format

## Developer Notes
- Include institution logo and header
- Show grade table legend on marksheet
- Support multiple semester display
- Add verification QR code (optional)
