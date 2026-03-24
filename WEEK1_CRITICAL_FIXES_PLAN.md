# 🎯 CRITICAL FIXES - WEEK 1 IMPLEMENTATION PLAN

## Overview
This document provides step-by-step instructions for Week 1 critical fixes.

---

## 🔴 PHASE 1: CRITICAL FIXES (Days 1-5)

### Task 1: Fix Pending Migrations
**Branch:** `feature/fix-pending-migrations`  
**Priority:** P0-Critical  
**Estimated Time:** 2-3 hours

#### Steps:

1. **Checkout the branch**
```bash
git checkout feature/fix-pending-migrations
```

2. **Check pending migrations**
```bash
php artisan migrate:status
```

3. **Run pending migrations**
```bash
php artisan migrate --force
```

4. **Verify migrations ran successfully**
```bash
php artisan migrate:status
# All should show "ran"
```

5. **Test attendance functionality**
```bash
# Visit: /academic/attendance
# Try to mark attendance
# Check if it works without errors
```

6. **Test timetable functionality**
```bash
# Visit: /academic/timetable
# Check if grid view loads
# Try to create/edit timetable
```

7. **Commit and push**
```bash
git add database/migrations/
git commit -m "fix(pending-migrations): Run 30 pending migrations - fixes attendance and timetable"
git push -u origin feature/fix-pending-migrations
```

#### Acceptance Criteria:
- [ ] All 96 migrations show "ran" status
- [ ] Attendance module works without errors
- [ ] Timetable module works without errors
- [ ] No migration errors in logs

---

### Task 2: Fix Schema Mismatches
**Branch:** `feature/fix-schema-mismatches`  
**Priority:** P0-Critical  
**Estimated Time:** 3-4 hours

#### Steps:

1. **Checkout the branch**
```bash
git checkout feature/fix-schema-mismatches
```

2. **Fix attendance table schema**
```bash
# Check current schema
php artisan tinker
>>> Schema::getColumnListing('attendance');
```

3. **Create migration to fix attendance schema**
```bash
php artisan make:migration fix_attendance_date_column_name
```

4. **Edit the migration file**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if 'date' column exists and rename it
        if (Schema::hasColumn('attendance', 'date')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->renameColumn('date', 'attendance_date');
            });
        }
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->renameColumn('attendance_date', 'date');
        });
    }
};
```

5. **Run the migration**
```bash
php artisan migrate
```

6. **Fix timetable day_of_week case**
```bash
php artisan make:migration standardize_timetable_day_of_week_case
```

7. **Update AttendanceController**
Search for references to `date` and change to `attendance_date`:
```php
// In AttendanceController.php
// Change:
$request->date
// To:
$request->attendance_date
```

8. **Test the fixes**
```bash
# Test attendance marking
# Test timetable viewing
# Check for any errors
```

9. **Commit and push**
```bash
git add database/migrations/ app/Http/Controllers/Web/AttendanceController.php
git commit -m "fix(schema-mismatch): Rename attendance.date to attendance_date, standardize day_of_week"
git push -u origin feature/fix-schema-mismatches
```

#### Acceptance Criteria:
- [ ] `attendance.attendance_date` column exists
- [ ] No references to `attendance.date` in code
- [ ] Attendance marking works correctly
- [ ] Timetable grid displays correctly

---

### Task 3: Remove Duplicate Files
**Branch:** `feature/remove-duplicate-files`  
**Priority:** P0-Critical  
**Estimated Time:** 1 hour

#### Steps:

1. **Checkout the branch**
```bash
git checkout feature/remove-duplicate-files
```

2. **Delete duplicate empty models**
```bash
# These are empty stubs that should not exist
rm app/Models/Models/User/Student.php
rm app/Models/Models/User/StaffProfile.php
```

3. **Delete duplicate service**
```bash
# Keep the one in app/Services/Fee/, delete the root one
rm app/Services/FeeCalculationService.php
```

4. **Update any imports**
Search for references:
```bash
grep -r "FeeCalculationService" app/ --include="*.php"
```

Update imports if needed:
```php
// Change:
use App\Services\FeeCalculationService;
// To:
use App\Services\Fee\FeeCalculationService;
```

5. **Clear autoload cache**
```bash
composer dump-autoload
```

6. **Test the application**
```bash
# Test student listing
# Test staff listing
# Test fee calculation
```

7. **Commit and push**
```bash
git status
git add -A
git commit -m "refactor(duplicate-files): Remove duplicate empty models and services"
git push -u origin feature/remove-duplicate-files
```

#### Acceptance Criteria:
- [ ] Duplicate files deleted
- [ ] No broken imports
- [ ] Application runs without errors
- [ ] Composer autoload works

---

### Task 4: Fix Relationships
**Branch:** `feature/fix-relationships`  
**Priority:** P0-Critical  
**Estimated Time:** 2-3 hours

#### Steps:

1. **Checkout the branch**
```bash
git checkout feature/fix-relationships
```

2. **Fix Division.academicYear() relationship**

Edit `app/Models/Academic/Division.php`:
```php
// WRONG (line ~85):
public function academicYear(): BelongsTo
{
    return $this->belongsTo(\App\Models\Academic\AcademicSession::class, 'academic_year_id');
}

// CORRECT:
public function academicYear(): BelongsTo
{
    return $this->belongsTo(\App\Models\Academic\AcademicYear::class, 'academic_year_id');
}
```

3. **Remove duplicate Student.profile() relationship**

Edit `app/Models/User/Student.php`:
```php
// Find and remove the duplicate relationship (around line 170):
// public function profile(): HasOne
// {
//     return $this->hasOne(\App\Models\StudentProfile::class);
// }
// Keep only studentProfile()
```

4. **Add missing return type hints**

Add return types to all relationships:
```php
public function program(): BelongsTo
{
    return $this->belongsTo(Program::class);
}

public function division(): BelongsTo
{
    return $this->belongsTo(Division::class);
}

public function guardians(): HasMany
{
    return $this->hasMany(StudentGuardian::class);
}
```

5. **Test relationships**
```bash
php artisan tinker
>>> $division = App\Models\Academic\Division::first();
>>> $division->academicYear; // Should work
>>> $student = App\Models\User\Student::first();
>>> $student->studentProfile; // Should work
>>> $student->profile; // Should be removed
```

6. **Commit and push**
```bash
git add app/Models/
git commit -m "fix(relationships): Fix Division.academicYear(), remove duplicate Student.profile(), add return types"
git push -u origin feature/fix-relationships
```

#### Acceptance Criteria:
- [ ] Division.academicYear() returns AcademicYear
- [ ] Student.profile() removed
- [ ] All relationships have return type hints
- [ ] No errors when accessing relationships

---

## 📊 WEEK 1 CHECKLIST

### Day 1-2: Migrations & Schema
- [ ] feature/fix-pending-migrations - PR created
- [ ] feature/fix-schema-mismatches - PR created
- [ ] Both PRs merged to parth_new

### Day 3: Duplicates
- [ ] feature/remove-duplicate-files - PR created
- [ ] PR merged to parth_new

### Day 4-5: Relationships
- [ ] feature/fix-relationships - PR created
- [ ] PR merged to parth_new

---

## 🧪 TESTING CHECKLIST

After each task, verify:

### Application Boots
- [ ] Homepage loads
- [ ] Login works
- [ ] Dashboard loads

### Core Modules Work
- [ ] Student listing works
- [ ] Attendance marking works
- [ ] Timetable view works
- [ ] Fee payment works
- [ ] Examination module works

### No Errors
- [ ] Check Laravel logs: `storage/logs/laravel.log`
- [ ] Check browser console for JS errors
- [ ] Check database for errors

---

## 📝 MERGE CHECKLIST

Before merging to parth_new:

- [ ] Code reviewed
- [ ] Tests pass locally
- [ ] No merge conflicts
- [ ] Commit message follows convention
- [ ] PR description complete

---

## 🚨 TROUBLESHOOTING

### Migration Fails
```bash
# Check which migration failed
php artisan migrate:status

# Rollback last batch
php artisan migrate:rollback

# Fix the migration file
# Run again
php artisan migrate
```

### Relationship Errors
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Class Not Found
```bash
# Regenerate autoload
composer dump-autoload
```

---

**Created:** March 24, 2026  
**For:** Week 1 Critical Fixes  
**Status:** Ready to Execute
