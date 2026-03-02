# [P2-06] Build Merit List and Ranking System

## Objective
Implement student ranking based on marks/percentage for awards and recognition.

## Problem Statement
System cannot generate merit lists for scholarships, awards, or rank cards.

## Expected Outcome
- Rank calculation per program/division
- Tie-breaking rules implemented
- Merit list PDF generation

## Scope of Work
1. Create ranking algorithm
2. Implement tie-breaking rules
3. Build merit list view
4. Create PDF generator

## Files to Modify
- CREATE: `app/Services/RankingService.php`
- CREATE: `app/Http/Controllers/Web/RankingController.php`
- CREATE: `resources/pdf/merit-list.blade.php`

## Dependencies
P2-08: Add Internal/External Marks Split

## Acceptance Criteria
- [ ] Rank calculation per program/division
- [ ] Tie-breaking rules implemented
- [ ] Merit list PDF generation
- [ ] Rank displayed on marksheet
- [ ] Category-wise merit lists

## Developer Notes
Tie-breaking order:
1. Higher percentage
2. Higher external marks
3. Lower absence count
4. Alphabetical order
