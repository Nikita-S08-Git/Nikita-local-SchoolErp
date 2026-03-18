# [P0-02] Create Missing ApiResponse Class

## Objective
Create the missing ApiResponse helper class used by API controllers for standardized responses.

## Problem Statement
Multiple API controllers use ApiResponse methods that don't exist:

```php
// app/Http/Controllers/Api/StudentController.php
use App\Http\ApiResponse; // ❌ FILE DOES NOT EXIST

return ApiResponse::paginated($students, 'Students retrieved successfully');
return ApiResponse::error('Failed to retrieve students', null, 500);
```

## Expected Outcome
- ApiResponse class exists with all required methods
- Consistent JSON response structure across all API endpoints
- All API controllers can call ApiResponse methods without errors

## Scope of Work
1. Create `app/Http/ApiResponse.php`
2. Implement methods: success(), error(), created(), paginated()
3. Define standard response structure
4. Update all API controllers to use new class

## Files to Modify
- CREATE: `app/Http/ApiResponse.php`
- VERIFY: `app/Http/Controllers/Api/StudentController.php`
- VERIFY: `app/Http/Controllers/Api/Fee/FeeController.php`

## Dependencies
None - This is a blocking task with no prerequisites

## Acceptance Criteria
- [ ] ApiResponse class created in app/Http/ directory
- [ ] Methods implemented: success(), error(), created(), paginated()
- [ ] Consistent JSON response structure across all API endpoints
- [ ] All API controllers can call ApiResponse methods without errors
- [ ] Response format follows JSON:API or similar standard

## Developer Notes
Standard response format:
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... },
  "meta": { ... }
}
```
