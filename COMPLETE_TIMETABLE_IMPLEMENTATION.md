# Complete Timetable Management System Implementation

## Overview

This document provides a complete implementation of the Timetable Management System with:
- ✅ Date display and filtering
- ✅ Add Class button with modal form
- ✅ Edit and Delete buttons for each class
- ✅ Holiday validation
- ✅ Conflict detection
- ✅ REST API endpoints
- ✅ Soft delete support

---

## Table of Contents

1. [Database Structure](#database-structure)
2. [Model Updates](#model-updates)
3. [API Controller](#api-controller)
4. [Frontend Implementation](#frontend-implementation)
5. [Features](#features)
6. [Usage Guide](#usage-guide)
7. [API Documentation](#api-documentation)
8. [Testing](#testing)

---

## Database Structure

### Timetables Table Schema

```sql
CREATE TABLE `timetables` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `division_id` bigint UNSIGNED NOT NULL,
  `subject_id` bigint UNSIGNED NOT NULL,
  `teacher_id` bigint UNSIGNED DEFAULT NULL,
  `room_id` bigint UNSIGNED DEFAULT NULL,
  `day_of_week` varchar(20) NOT NULL,
  `date` date DEFAULT NULL,              -- NEW: Specific date for class
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `period_name` varchar(50) DEFAULT NULL,
  `room_number` varchar(50) DEFAULT NULL,
  `academic_year_id` bigint UNSIGNED NOT NULL,
  `is_break_time` tinyint(1) NOT NULL DEFAULT false,
  `is_active` tinyint(1) NOT NULL DEFAULT true,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,  -- NEW: Soft delete support
  PRIMARY KEY (`id`),
  KEY `timetables_date_index` (`date`),
  KEY `timetables_division_date_index` (`division_id`, `date`),
  KEY `timetables_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Migrations Applied

1. **Add Date Column** (Already exists)
```bash
php artisan migrate
```

2. **Add Soft Deletes**
```bash
php artisan make:migration add_soft_deletes_to_timetables_table
```

---

## Model Updates

### Timetable Model (`app/Models/Academic/Timetable.php`)

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Timetable extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'division_id',
        'subject_id',
        'teacher_id',
        'room_id',
        'day_of_week',
        'date',  // NEW
        'start_time',
        'end_time',
        'period_name',
        'room_number',
        'academic_year_id',
        'is_break_time',
        'is_active',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',  // NEW: Cast to Carbon instance
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_break_time' => 'boolean',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',  // NEW
    ];
}
```

---

## API Controller

### Location: `app/Http/Controllers/Api/TimetableController.php`

### REST Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/timetables` | List timetable with filters |
| POST | `/api/timetables` | Create new timetable entry |
| GET | `/api/timetables/{id}` | Get single timetable entry |
| PUT | `/api/timetables/{id}` | Update timetable entry |
| DELETE | `/api/timetables/{id}` | Soft delete timetable entry |

### Key Features

#### 1. Holiday Validation
```php
if (isset($validated['date'])) {
    $holidayCheck = $this->holidayService->validateAttendanceDate(
        $validated['date'],
        $validated['academic_year_id']
    );

    if ($holidayCheck['is_holiday']) {
        return response()->json([
            'success' => false,
            'message' => 'Cannot create timetable on holiday',
            'holiday_title' => $holidayCheck['holiday_title'],
        ], 422);
    }
}
```

#### 2. Conflict Detection
```php
private function checkConflicts(...): array
{
    // Check division conflict
    $divisionConflict = Timetable::where('division_id', $divisionId)
        ->where('day_of_week', $dayOfWeek)
        ->where(function ($q) use ($startTime, $endTime) {
            $q->where('start_time', '<', $endTime)
              ->where('end_time', '>', $startTime);
        })
        ->exists();

    // Check teacher conflict
    // Check room conflict
    
    return [
        'has_conflicts' => $hasConflicts,
        'details' => $conflicts,
    ];
}
```

---

## Frontend Implementation

### Grid View Updates

#### 1. Date Filter & Add Class Button

```blade
<!-- Date Filter -->
<div class="col-md-3">
    <label class="form-label">
        <i class="bi bi-calendar-event"></i> Select Date
    </label>
    <input type="date" name="date" 
           value="{{ request('date') ?? date('Y-m-d') }}"
           onchange="this.form.submit()">
</div>

<!-- Add Class Button -->
<button type="button" class="btn btn-primary" 
        data-bs-toggle="modal" 
        data-bs-target="#addClassModal">
    <i class="bi bi-plus-circle"></i> Add Class
</button>
```

#### 2. Selected Date Display

```blade
@if($selectedDivision && $selectedDate)
<div class="alert alert-info">
    <i class="bi bi-calendar-check"></i>
    Showing timetable for: {{ \Carbon\Carbon::parse($selectedDate)->format('l, F d, Y') }}
    @if($isHoliday)
        <span class="badge bg-danger">Holiday: {{ $holidayTitle }}</span>
    @endif
</div>
@endif
```

#### 3. Action Buttons on Each Class

```blade
<div class="action-buttons">
    <button class="btn btn-warning btn-sm btn-edit-class"
            data-id="{{ $class->id }}">
        <i class="bi bi-pencil"></i> Edit
    </button>
    <button class="btn btn-danger btn-sm btn-delete-class"
            data-id="{{ $class->id }}"
            data-name="{{ $class->subject->name }}">
        <i class="bi bi-trash"></i> Delete
    </button>
</div>
```

---

## Features

### 1. Date Display ✅

- **Date picker** at top of page
- **Selected date** displayed in alert banner
- **Holiday detection** with warning message
- **Auto-reload** when date changes

### 2. Add Class Button ✅

- **Modal form** with all required fields
- **Date field** with auto-day calculation
- **Validation** for required fields
- **Holiday check** before saving
- **Conflict detection** for teacher/room/division

### 3. Edit Button ✅

- **Inline edit** button on each class
- **Pre-filled form** with current data
- **Same validation** as add class
- **Update via API** or form submission

### 4. Delete Button ✅

- **Confirmation modal** before delete
- **Soft delete** (can be restored)
- **Success message** after deletion
- **Page refresh** to show updates

### 5. Holiday Integration ✅

- **Check holidays table** before saving
- **Block timetable creation** on holidays
- **Show holiday message** instead of timetable
- **HolidayService** integration

### 6. Conflict Detection ✅

- **Division conflicts** - Same division, same time
- **Teacher conflicts** - Same teacher, same time
- **Room conflicts** - Same room, same time
- **Real-time validation** via API

---

## Usage Guide

### For Admin/Principal

#### Add a Class:

1. Click **"Add Class"** button
2. Fill in the form:
   - Subject (required)
   - Teacher (required)
   - Date (required) - Auto-fills day
   - Start Time (required)
   - End Time (required)
   - Room Number (optional)
   - Period Name (optional)
   - Notes (optional)
3. Click **"Add Class"**
4. System validates:
   - Not a holiday
   - No conflicts
5. Success message appears

#### Edit a Class:

1. Hover over class card
2. Click **Edit (✏️)** button
3. Modify fields in modal
4. Click **"Update Class"**
5. System re-validates
6. Success message appears

#### Delete a Class:

1. Hover over class card
2. Click **Delete (🗑️)** button
3. Confirm in modal
4. Class is soft-deleted
5. Success message appears

### For Teachers/Students

- **View timetable** by selecting division
- **Filter by date** using date picker
- **View holiday warnings** if applicable
- **No edit/delete** permissions

---

## API Documentation

### GET /api/timetables

**Parameters:**
```
division_id (required) - Division ID
date (optional) - Specific date (Y-m-d)
academic_year_id (optional) - Academic year ID
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "division_id": 1,
            "division_name": "BSC CS",
            "subject_id": 1,
            "subject_name": "Mathematics",
            "teacher_id": 5,
            "teacher_name": "Dr. Smith",
            "room_id": 1,
            "room_number": "101",
            "day_of_week": "monday",
            "day_name": "Monday",
            "date": "2026-03-15",
            "start_time": "09:00",
            "end_time": "10:00",
            "formatted_time": "09:00 - 10:00",
            "status": "active",
            "is_specific_date": true
        }
    ],
    "total_periods": 1
}
```

### POST /api/timetables

**Request:**
```json
{
    "division_id": 1,
    "subject_id": 1,
    "teacher_id": 5,
    "room_id": 1,
    "date": "2026-03-15",
    "start_time": "09:00",
    "end_time": "10:00",
    "period_name": "Period 1",
    "room_number": "Room 101",
    "academic_year_id": 1,
    "status": "active",
    "notes": "Regular class"
}
```

**Success Response (201):**
```json
{
    "success": true,
    "message": "Timetable entry created successfully",
    "data": {
        "id": 123,
        "division_name": "BSC CS",
        "subject_name": "Mathematics",
        "teacher_name": "Dr. Smith",
        "day_name": "Sunday",
        "date": "2026-03-15",
        "time": "09:00 - 10:00"
    }
}
```

**Error Response (422) - Holiday:**
```json
{
    "success": false,
    "message": "Cannot create timetable on holiday",
    "holiday_title": "Holi Festival"
}
```

**Error Response (422) - Conflict:**
```json
{
    "success": false,
    "message": "Schedule conflict detected",
    "conflicts": [
        {
            "type": "teacher",
            "message": "Teacher is already scheduled for another class at this time"
        }
    ]
}
```

### PUT /api/timetables/{id}

**Request:**
```json
{
    "subject_id": 2,
    "start_time": "10:00",
    "end_time": "11:00"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Timetable entry updated successfully",
    "data": {
        "id": 123,
        "subject_name": "Physics",
        "time": "10:00 - 11:00"
    }
}
```

### DELETE /api/timetables/{id}

**Response:**
```json
{
    "success": true,
    "message": "Timetable entry deleted successfully",
    "data": {
        "id": 123,
        "deleted": true
    }
}
```

---

## Testing

### Manual Testing Checklist

#### Date Filter:
- [ ] Date picker displays current date
- [ ] Changing date reloads timetable
- [ ] Selected date shows in banner
- [ ] Holiday warning displays correctly
- [ ] Non-holiday dates show timetable

#### Add Class:
- [ ] Modal opens on button click
- [ ] All required fields validated
- [ ] Date auto-populates day field
- [ ] Holiday dates blocked
- [ ] Conflicts detected and shown
- [ ] Success message on save
- [ ] Timetable updates after add

#### Edit Class:
- [ ] Edit button visible on hover
- [ ] Modal opens with pre-filled data
- [ ] Changes save correctly
- [ ] Validation works on edit
- [ ] Success message appears

#### Delete Class:
- [ ] Delete button visible on hover
- [ ] Confirmation modal appears
- [ ] Cancel closes modal
- [ ] Delete removes entry
- [ ] Soft delete works (check DB)
- [ ] Success message shows

#### Holiday Integration:
- [ ] Holiday dates blocked in form
- [ ] Holiday warning in banner
- [ ] Timetable hidden on holidays
- [ ] Error message clear

#### API Testing:
- [ ] GET endpoint returns data
- [ ] POST creates new entry
- [ ] PUT updates existing entry
- [ ] DELETE soft deletes
- [ ] Error responses correct
- [ ] Validation errors returned

### Automated Test Example

```php
class TimetableApiTest extends TestCase
{
    public function test_can_create_timetable()
    {
        $response = $this->postJson('/api/timetables', [
            'division_id' => 1,
            'subject_id' => 1,
            'teacher_id' => 1,
            'date' => '2026-03-15',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'academic_year_id' => 1,
        ]);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);
    }

    public function test_cannot_create_on_holiday()
    {
        Holiday::create([
            'title' => 'Test Holiday',
            'start_date' => '2026-03-15',
            'end_date' => '2026-03-15',
            'type' => 'public_holiday',
            'academic_year_id' => 1,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/timetables', [
            'division_id' => 1,
            'subject_id' => 1,
            'teacher_id' => 1,
            'date' => '2026-03-15',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'academic_year_id' => 1,
        ]);

        $response->assertStatus(422)
                 ->assertJson(['message' => 'Cannot create timetable on holiday']);
    }

    public function test_soft_delete()
    {
        $timetable = Timetable::factory()->create();

        $response = $this->deleteJson("/api/timetables/{$timetable->id}");

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertSoftDeleted('timetables', ['id' => $timetable->id]);
    }
}
```

---

## Troubleshooting

### Issue: Date filter not working
**Solution:** Check form submits on change. Verify controller receives date parameter.

### Issue: Add Class modal not opening
**Solution:** Ensure Bootstrap JS is loaded. Check modal ID matches.

### Issue: Holiday not detected
**Solution:** Verify HolidayService is injected. Check holidays table has data.

### Issue: Conflicts not detected
**Solution:** Check `checkConflicts()` method. Verify time comparison logic.

### Issue: Delete not working
**Solution:** Check SoftDeletes trait in model. Verify route exists.

---

## Files Modified/Created

### Migrations:
- `database/migrations/2026_02_28_074636_add_soft_deletes_to_timetables_table.php`

### Models:
- `app/Models/Academic/Timetable.php` - Added SoftDeletes, date casting

### Controllers:
- `app/Http/Controllers/Api/TimetableController.php` - Complete REST API
- `app/Http/Controllers/Web/TimetableController.php` - Updated gridView()

### Views:
- `resources/views/academic/timetable/grid.blade.php` - Date filter, Add button, Actions
- `resources/views/academic/timetable/timetable-modals.blade.php` - Add/Edit/Delete modals

### Services:
- `app/Services/HolidayService.php` - Already exists (holiday validation)

---

## Security

### Authorization:
```php
@can('admin_principal')
    <!-- Show Add/Edit/Delete buttons -->
@endcan
```

### CSRF Protection:
```blade
@csrf
@method('DELETE')
```

### Input Validation:
```php
$validated = $request->validate([
    'division_id' => 'required|exists:divisions,id',
    'date' => 'nullable|date',
    // ... other fields
]);
```

---

## Performance

### Optimizations:
- **Eager loading** - `with(['division', 'subject', 'teacher'])`
- **Caching** - HolidayService caches for 24 hours
- **Indexes** - Date, division_id + date composite
- **Soft deletes** - Quick restore if needed

---

## Future Enhancements

1. **Bulk Import** - Upload CSV for multiple classes
2. **Drag & Drop** - Reschedule by dragging
3. **Copy Timetable** - Duplicate to another date
4. **Recurring Classes** - Set pattern (weekly, bi-weekly)
5. **Substitute Teacher** - Quick teacher replacement
6. **Room Change** - Bulk room updates
7. **Timetable Templates** - Save and reuse schedules

---

**Last Updated:** 2026-02-28  
**Version:** 4.0.0  
**Status:** ✅ Production Ready

---

## Quick Start Commands

```bash
# Run migrations
php artisan migrate

# Clear cache
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Test API endpoint
curl http://127.0.0.1:8000/api/timetables?division_id=1&date=2026-03-15

# View timetable in browser
http://127.0.0.1:8000/academic/timetable/grid?division_id=1&date=2026-03-15
```
