# Holiday System Implementation Guide

## Overview

This document describes the complete Holiday System implementation that integrates with the Attendance and Timetable modules to prevent operations on holiday dates.

---

## Database Structure

### Holidays Table

```sql
CREATE TABLE `holidays` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `type` enum('public_holiday','school_holiday','event','program') DEFAULT 'public_holiday',
  `is_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `academic_year_id` bigint UNSIGNED NOT NULL,
  `program_incharge_id` bigint UNSIGNED DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `holidays_start_date_end_date_index` (`start_date`,`end_date`),
  KEY `holidays_type_is_active_index` (`type`,`is_active`),
  KEY `holidays_academic_year_id_is_active_index` (`academic_year_id`,`is_active`),
  KEY `holidays_program_incharge_id_index` (`program_incharge_id`),
  CONSTRAINT `holidays_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `holidays_program_incharge_id_foreign` FOREIGN KEY (`program_incharge_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Service Layer: HolidayService

**Location:** `app/Services/HolidayService.php`

### Key Methods

#### 1. `isHoliday($date, $academicYearId = null)`
Check if a given date is a holiday.

```php
use App\Services\HolidayService;

$holidayService = app(HolidayService::class);
$isHoliday = $holidayService->isHoliday('2026-02-28');
// Returns: true or false
```

#### 2. `getHolidayDetails($date, $academicYearId = null)`
Get detailed information about a holiday.

```php
$details = $holidayService->getHolidayDetails('2026-02-28');
// Returns:
[
    'id' => 1,
    'title' => 'Republic Day',
    'description' => 'National holiday',
    'type' => 'public_holiday',
    'type_label' => 'Public Holiday',
    'start_date' => '2026-02-28',
    'end_date' => '2026-02-28',
    'is_recurring' => false,
]
```

#### 3. `validateAttendanceDate($date, $academicYearId = null)`
Validate if attendance can be marked on a date.

```php
$validation = $holidayService->validateAttendanceDate('2026-02-28');
// Returns:
[
    'valid' => false,
    'is_holiday' => true,
    'message' => 'Holiday - Attendance Not Allowed',
    'holiday_title' => 'Republic Day',
    'holiday_type' => 'Public Holiday',
]
```

#### 4. `checkTimetableAvailability($date, $academicYearId = null)`
Check if timetable should load for a date.

```php
$availability = $holidayService->checkTimetableAvailability('2026-02-28');
// Returns:
[
    'status' => 'holiday',
    'available' => false,
    'message' => 'Holiday - No Classes Scheduled',
    'holiday_title' => 'Republic Day',
    'periods' => [],
]
```

#### 5. `getHolidaysInRange($startDate, $endDate, $academicYearId = null)`
Get all holidays within a date range.

```php
$holidays = $holidayService->getHolidaysInRange('2026-01-01', '2026-12-31');
// Returns: Collection of Holiday models
```

#### 6. `getHolidayDatesInRange($startDate, $endDate, $academicYearId = null)`
Get flattened list of holiday dates.

```php
$dates = $holidayService->getHolidayDatesInRange('2026-01-01', '2026-12-31');
// Returns: ['2026-01-26', '2026-02-28', ...]
```

#### 7. `getWorkingDaysCount($startDate, $endDate, $academicYearId = null)`
Calculate working days excluding holidays.

```php
$workingDays = $holidayService->getWorkingDaysCount('2026-01-01', '2026-01-31');
// Returns: 26 (for example)
```

---

## Controller Integration

### AttendanceController

**Location:** `app/Http/Controllers/Web/AttendanceController.php`

#### Holiday Validation on Mark Attendance

```php
public function mark(Request $request)
{
    $validated = $request->validate([
        'division_id' => 'required|exists:divisions,id',
        'academic_session_id' => 'required|exists:academic_sessions,id',
        'date' => 'required|date'
    ]);

    $division = Division::with('academicYear')->findOrFail($validated['division_id']);
    
    // Check if the selected date is a holiday
    $holidayCheck = $this->holidayService->validateAttendanceDate(
        $validated['date'],
        $division->academic_year_id
    );

    if (!$holidayCheck['valid'] && $holidayCheck['is_holiday']) {
        return redirect()->route('academic.attendance.index')
            ->with('error', $holidayCheck['message'] . ': ' . ($holidayCheck['holiday_title'] ?? ''));
    }

    // Continue with attendance marking...
}
```

#### AJAX Holiday Check Endpoint

```php
/**
 * POST /attendance/check-holiday
 */
public function checkHoliday(Request $request): JsonResponse
{
    $request->validate([
        'date' => 'required|date',
        'division_id' => 'nullable|exists:divisions,id'
    ]);

    $date = Carbon::parse($request->date);
    $academicYearId = null;

    if ($request->filled('division_id')) {
        $academicYearId = Division::find($request->division_id)?->academic_year_id;
    }

    $holidayCheck = $this->holidayService->validateAttendanceDate($date, $academicYearId);

    return response()->json([
        'success' => true,
        'is_holiday' => $holidayCheck['is_holiday'],
        'valid' => $holidayCheck['valid'],
        'message' => $holidayCheck['message'],
        'holiday_title' => $holidayCheck['holiday_title'] ?? null,
        'holiday_type' => $holidayCheck['holiday_type'] ?? null,
    ]);
}
```

---

### TimetableController

**Location:** `app/Http/Controllers/Web/TimetableController.php`

#### Holiday Check for Timetable

```php
/**
 * GET /timetable/ajax/check-holiday
 */
public function checkHoliday(Request $request): JsonResponse
{
    $request->validate([
        'date' => 'required|date',
    ]);

    $date = Carbon::parse($request->date);
    $academicYearId = AcademicYear::getCurrentAcademicYearId();

    $holidayCheck = $this->holidayService->checkTimetableAvailability($date, $academicYearId);

    return response()->json($holidayCheck);
}
```

#### Get Timetable by Date with Holiday Check

```php
/**
 * GET /timetable/ajax/get-by-date
 */
public function getByDate(Request $request): JsonResponse
{
    $request->validate([
        'date' => 'required|date',
        'division_id' => 'nullable|exists:divisions,id',
    ]);

    $date = Carbon::parse($request->date);
    $academicYearId = AcademicYear::getCurrentAcademicYearId();

    // First check if it's a holiday
    $holidayCheck = $this->holidayService->checkTimetableAvailability($date, $academicYearId);

    if ($holidayCheck['status'] === 'holiday') {
        return response()->json($holidayCheck);
    }

    // If not a holiday, get the timetable
    $query = Timetable::withRelationships()
        ->byAcademicYear($academicYearId)
        ->byStatus('active')
        ->notBreakTime();

    if ($request->filled('division_id')) {
        $query->byDivision($request->division_id);
    }

    $dayName = $date->format('l');
    $timetables = $query->byDay(strtolower($dayName))->ordered()->get();

    return response()->json([
        'status' => 'active',
        'available' => true,
        'message' => 'Timetable loaded successfully',
        'date' => $date->format('Y-m-d'),
        'day' => $dayName,
        'periods' => $timetables,
    ]);
}
```

---

## API Endpoints

### Attendance Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/attendance/check-holiday` | Check if a date is a holiday for attendance |

**Request:**
```json
{
    "date": "2026-02-28",
    "division_id": 1
}
```

**Response (Holiday):**
```json
{
    "success": true,
    "is_holiday": true,
    "valid": false,
    "message": "Holiday - Attendance Not Allowed",
    "holiday_title": "Republic Day",
    "holiday_type": "Public Holiday"
}
```

**Response (Not Holiday):**
```json
{
    "success": true,
    "is_holiday": false,
    "valid": true,
    "message": "Attendance can be marked"
}
```

### Timetable Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/timetable/ajax/check-holiday?date=2026-02-28` | Check if date is holiday |
| GET | `/timetable/ajax/get-by-date?date=2026-02-28&division_id=1` | Get timetable for date |

**Response (Holiday):**
```json
{
    "status": "holiday",
    "available": false,
    "message": "Holiday - No Classes Scheduled",
    "holiday_title": "Republic Day",
    "holiday_type": "Public Holiday",
    "periods": []
}
```

**Response (Active):**
```json
{
    "status": "active",
    "available": true,
    "message": "Timetable loaded successfully",
    "date": "2026-02-28",
    "day": "Saturday",
    "periods": [
        {
            "id": 1,
            "subject": {...},
            "teacher": {...},
            "room": {...},
            "start_time": "09:00",
            "end_time": "10:00"
        }
    ]
}
```

---

## Frontend Integration

### JavaScript AJAX Example

```javascript
/**
 * Check if a date is a holiday before marking attendance
 */
async function checkHoliday(date, divisionId) {
    try {
        const response = await fetch('/attendance/check-holiday', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                date: date,
                division_id: divisionId
            })
        });

        const data = await response.json();

        if (data.is_holiday) {
            showHolidayWarning(data);
            return false;
        }

        return true;
    } catch (error) {
        console.error('Error checking holiday:', error);
        return true; // Allow on error
    }
}

/**
 * Display holiday warning message
 */
function showHolidayWarning(data) {
    const alertHtml = `
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>${data.holiday_title}</strong> - ${data.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    document.querySelector('#alertContainer').innerHTML = alertHtml;
}

/**
 * Load timetable for a date with holiday check
 */
async function loadTimetableForDate(date, divisionId) {
    try {
        const response = await fetch(`/timetable/ajax/get-by-date?date=${date}&division_id=${divisionId}`);
        const data = await response.json();

        if (data.status === 'holiday') {
            showHolidayMessage(data);
            return;
        }

        renderTimetable(data.periods);
    } catch (error) {
        console.error('Error loading timetable:', error);
    }
}

/**
 * Show holiday message for timetable
 */
function showHolidayMessage(data) {
    const container = document.querySelector('#timetableContainer');
    container.innerHTML = `
        <div class="alert alert-info text-center">
            <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
            <h4>${data.holiday_title}</h4>
            <p class="mb-0">${data.message}</p>
        </div>
    `;
}
```

---

## Usage Examples

### Creating a Holiday

```php
use App\Models\Holiday;

// Single day holiday
Holiday::create([
    'title' => 'Republic Day',
    'description' => 'National holiday celebrating the constitution',
    'start_date' => '2026-01-26',
    'end_date' => '2026-01-26',
    'type' => 'public_holiday',
    'is_recurring' => true,
    'academic_year_id' => 1,
    'is_active' => true,
]);

// Multi-day holiday (e.g., Diwali break)
Holiday::create([
    'title' => 'Diwali Break',
    'description' => 'Festival holidays',
    'start_date' => '2026-11-10',
    'end_date' => '2026-11-15',
    'type' => 'public_holiday',
    'is_recurring' => false,
    'academic_year_id' => 1,
    'is_active' => true,
]);

// School event (blocks timetable but may allow attendance)
Holiday::create([
    'title' => 'Annual Sports Day',
    'description' => 'No regular classes',
    'start_date' => '2026-03-15',
    'end_date' => '2026-03-15',
    'type' => 'school_holiday',
    'is_recurring' => false,
    'academic_year_id' => 1,
    'is_active' => true,
]);
```

### Checking Multiple Dates

```php
use App\Services\HolidayService;

$holidayService = app(HolidayService::class);

// Bulk validation
$dates = ['2026-01-26', '2026-01-27', '2026-02-28'];
$results = $holidayService->bulkValidateDates($dates, 1);

// Get working days in a month
$workingDays = $holidayService->getWorkingDaysCount(
    '2026-01-01',
    '2026-01-31',
    1
);
echo "Working days in January: {$workingDays}";

// Get upcoming holidays
$upcoming = $holidayService->getUpcomingHolidays(5, 1);
foreach ($upcoming as $holiday) {
    echo "{$holiday->title}: {$holiday->start_date} to {$holiday->end_date}\n";
}
```

---

## Cache Management

The HolidayService uses Laravel's cache for performance:

```php
// Clear cache after creating/updating holidays
$holidayService->clearCache();

// Cache is automatically invalidated after 24 hours (1440 minutes)
```

---

## Error Handling

All methods include proper error handling:

```php
try {
    $holidayCheck = $holidayService->validateAttendanceDate('invalid-date');
} catch (\Exception $e) {
    // Handle error
    Log::error('Holiday validation failed: ' . $e->getMessage());
}
```

---

## Testing

### Unit Test Example

```php
use Tests\TestCase;
use App\Services\HolidayService;
use App\Models\Holiday;

class HolidayServiceTest extends TestCase
{
    public function test_is_holiday_returns_true_for_holiday_date()
    {
        Holiday::create([
            'title' => 'Test Holiday',
            'start_date' => '2026-02-28',
            'end_date' => '2026-02-28',
            'type' => 'public_holiday',
            'academic_year_id' => 1,
            'is_active' => true,
        ]);

        $service = app(HolidayService::class);
        $this->assertTrue($service->isHoliday('2026-02-28'));
    }

    public function test_is_holiday_returns_false_for_non_holiday_date()
    {
        $service = app(HolidayService::class);
        $this->assertFalse($service->isHoliday('2026-02-27'));
    }

    public function test_validate_attendance_date_blocks_holiday()
    {
        Holiday::create([
            'title' => 'Test Holiday',
            'start_date' => '2026-02-28',
            'end_date' => '2026-02-28',
            'type' => 'public_holiday',
            'academic_year_id' => 1,
            'is_active' => true,
        ]);

        $service = app(HolidayService::class);
        $result = $service->validateAttendanceDate('2026-02-28', 1);

        $this->assertFalse($result['valid']);
        $this->assertTrue($result['is_holiday']);
        $this->assertEquals('Holiday - Attendance Not Allowed', $result['message']);
    }
}
```

---

## Security Considerations

1. **Input Validation:** All dates are validated using Laravel's validation
2. **SQL Injection:** Protected by Eloquent ORM
3. **XSS Prevention:** All output is escaped in Blade templates
4. **CSRF Protection:** All forms include CSRF tokens
5. **Authorization:** Role-based access control is enforced

---

## Performance Optimization

1. **Caching:** Holiday data is cached for 24 hours
2. **Indexes:** Database indexes on `start_date`, `end_date`, and `is_active`
3. **Eager Loading:** Relationships are loaded efficiently
4. **Query Optimization:** Uses `exists()` for boolean checks instead of `get()`

---

## Troubleshooting

### Issue: Holiday check not working

**Solution:**
1. Clear cache: `php artisan cache:clear`
2. Check if holiday is active: `is_active = true`
3. Verify academic year ID matches

### Issue: Attendance still allowed on holiday

**Solution:**
1. Ensure AttendanceController uses HolidayService
2. Check route is using updated controller method
3. Verify middleware is applied correctly

### Issue: Cache not clearing

**Solution:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## Support

For issues or questions, check:
- Laravel logs: `storage/logs/laravel.log`
- Database queries: `DB::enableQueryLog()`
- Debug output: `Log::info()` statements

---

**Last Updated:** 2026-02-28
**Version:** 1.0.0
