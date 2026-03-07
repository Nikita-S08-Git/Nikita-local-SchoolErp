# Timetable Implementation - Quick Reference

## ✅ All Features Implemented

### 1. Date Display & Filter ✅
- **Location:** Top of grid view
- **Default:** Current date
- **Auto-reload:** On date change
- **Holiday Check:** Shows warning if holiday

### 2. Add Class Button ✅
- **Location:** Top right of page
- **Type:** Modal form
- **Fields:**
  - Subject (required)
  - Teacher (required)
  - Date (required) - Auto-calculates day
  - Start Time (required)
  - End Time (required)
  - Room Number (optional)
  - Period Name (optional)
  - Notes (optional)

### 3. Edit Button ✅
- **Location:** On each class card (hover)
- **Icon:** ✏️ Pencil (yellow)
- **Action:** Opens edit modal
- **Validation:** Same as add class

### 4. Delete Button ✅
- **Location:** On each class card (hover)
- **Icon:** 🗑️ Trash (red)
- **Action:** Confirmation modal
- **Type:** Soft delete (can restore)

### 5. Holiday Integration ✅
- **Check:** Before save
- **Display:** Banner if holiday
- **Block:** Cannot add on holiday
- **Message:** Clear error message

### 6. Conflict Detection ✅
- **Division:** Same division, same time
- **Teacher:** Same teacher, same time
- **Room:** Same room, same time
- **API:** Returns detailed conflicts

---

## Quick Start

### Access Timetable Grid:
```
http://127.0.0.1:8000/academic/timetable/grid
```

### With Date Filter:
```
http://127.0.0.1:8000/academic/timetable/grid?division_id=1&date=2026-03-15
```

---

## API Endpoints

### Base URL: `http://127.0.0.1:8000/api`

#### Get Timetable:
```bash
GET /timetables?division_id=1&date=2026-03-15
```

#### Add Class:
```bash
POST /timetables
Content-Type: application/json
Authorization: Bearer {token}

{
  "division_id": 1,
  "subject_id": 1,
  "teacher_id": 5,
  "date": "2026-03-15",
  "start_time": "09:00",
  "end_time": "10:00",
  "academic_year_id": 1
}
```

#### Edit Class:
```bash
PUT /timetables/123
Content-Type: application/json

{
  "start_time": "10:00",
  "end_time": "11:00"
}
```

#### Delete Class:
```bash
DELETE /timetables/123
```

---

## Database Schema

### Key Columns:
```sql
date         - Specific date for class (nullable)
day_of_week  - Day name (monday, tuesday, etc.)
deleted_at   - Soft delete timestamp (nullable)
```

### Relationships:
```php
Timetable belongsTo Division
Timetable belongsTo Subject
Timetable belongsTo Teacher
Timetable belongsTo Room
Timetable belongsTo AcademicYear
```

---

## Files Modified

### Backend:
```
app/Models/Academic/Timetable.php          - Added SoftDeletes, date casting
app/Http/Controllers/Api/TimetableController.php - Complete REST API
app/Http/Controllers/Web/TimetableController.php - Updated gridView()
routes/api.php                             - Added API routes
database/migrations/*add_soft_deletes*     - Soft delete support
```

### Frontend:
```
resources/views/academic/timetable/grid.blade.php - Date filter, Add button, Actions
resources/views/academic/timetable/timetable-modals.blade.php - Add/Edit/Delete modals
```

### Documentation:
```
COMPLETE_TIMETABLE_IMPLEMENTATION.md - Full implementation guide
TIMETABLE_QUICK_REFERENCE.md         - This file
```

---

## Common Operations

### Add a Class:
1. Click "Add Class" button
2. Select subject
3. Select teacher
4. Select date (day auto-fills)
5. Set start and end time
6. Add room/period (optional)
7. Click "Add Class"

### Edit a Class:
1. Hover over class card
2. Click Edit (✏️) button
3. Modify fields
4. Click "Update Class"

### Delete a Class:
1. Hover over class card
2. Click Delete (🗑️) button
3. Confirm in modal
4. Class is soft-deleted

### Filter by Date:
1. Select date from date picker
2. Page auto-reloads
3. Shows timetable for that date
4. Holiday warning if applicable

---

## Validation Rules

### Required Fields:
- division_id
- subject_id
- teacher_id
- start_time
- end_time
- academic_year_id

### Optional Fields:
- date (if empty, uses day_of_week)
- room_id
- room_number
- period_name
- notes

### Auto-Validations:
- ✅ Not a holiday
- ✅ No division conflict
- ✅ No teacher conflict
- ✅ No room conflict
- ✅ End time after start time

---

## Error Messages

### Holiday Error:
```
Cannot create timetable on holiday
Holiday: [Holiday Name]
```

### Conflict Error:
```
Schedule conflict detected

Conflicts:
- Division already has a class at this time
- Teacher is already scheduled for another class
- Room is already booked at this time
```

### Validation Error:
```
Validation failed

Errors:
- The subject field is required.
- the end time must be after start time.
```

---

## Testing Commands

### Check Routes:
```bash
php artisan route:list | findstr timetable
```

### Clear Cache:
```bash
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
```

### Test API (with token):
```bash
curl -X GET "http://127.0.0.1:8000/api/timetables?division_id=1&date=2026-03-15" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Troubleshooting

### Issue: Date filter not reloading
**Fix:** Check `onchange="this.form.submit()"` is present

### Issue: Modal not opening
**Fix:** Ensure Bootstrap JS is loaded, check modal ID

### Issue: Day not auto-populating
**Fix:** Check JavaScript event listener on date input

### Issue: Holiday not detected
**Fix:** Verify HolidayService is injected, check holidays table

### Issue: API 401 Unauthorized
**Fix:** Add `Authorization: Bearer {token}` header

---

## Next Steps (Optional Enhancements)

1. **Bulk Import** - CSV upload for multiple classes
2. **Drag & Drop** - Reschedule by dragging cards
3. **Copy Timetable** - Duplicate to another date/division
4. **Substitute Teacher** - Quick teacher replacement
5. **Print View** - Optimized print layout
6. **Export PDF** - Download timetable as PDF
7. **Email Notifications** - Notify teachers of changes
8. **Mobile App** - React Native/Flutter app

---

## Support

### Logs:
```
storage/logs/laravel.log
```

### Debug Mode:
```env
APP_DEBUG=true
APP_ENV=local
```

### Query Log:
```php
DB::enableQueryLog();
// ... your code
dd(DB::getQueryLog());
```

---

**Version:** 4.0.0  
**Last Updated:** 2026-02-28  
**Status:** ✅ Production Ready  
**Tested:** ✅ Routes, API, Views

---

## Quick Code Snippets

### Get Timetable for Date:
```php
use App\Models\Academic\Timetable;
use Carbon\Carbon;

$date = Carbon::parse('2026-03-15');
$timetables = Timetable::where('division_id', 1)
    ->where(function ($q) use ($date) {
        $q->whereDate('date', $date)
          ->orWhere('day_of_week', strtolower($date->format('l')));
    })
    ->with(['subject', 'teacher', 'room'])
    ->get();
```

### Check if Holiday:
```php
use App\Services\HolidayService;

$holidayService = app(HolidayService::class);
$isHoliday = $holidayService->isHoliday('2026-03-15');

if ($isHoliday) {
    $details = $holidayService->getHolidayDetails('2026-03-15');
    echo "Holiday: " . $details['title'];
}
```

### Create Timetable:
```php
Timetable::create([
    'division_id' => 1,
    'subject_id' => 1,
    'teacher_id' => 5,
    'date' => '2026-03-15',
    'day_of_week' => 'sunday',
    'start_time' => '09:00',
    'end_time' => '10:00',
    'academic_year_id' => 1,
    'status' => 'active',
]);
```

### Soft Delete:
```php
$timetable = Timetable::find(123);
$timetable->delete(); // Soft delete

// Restore
$timetable->restore();

// Force delete
$timetable->forceDelete();

// Get only non-deleted
Timetable::all();

// Include deleted
Timetable::withTrashed()->get();

// Get only deleted
Timetable::onlyTrashed()->get();
```
