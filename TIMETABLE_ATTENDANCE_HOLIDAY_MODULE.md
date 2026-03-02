# School Management System - Complete Module Documentation

## Overview

This document provides comprehensive documentation for the **Timetable**, **Attendance**, and **Holiday** modules of the School Management System.

---

## Table of Contents

1. [Module Summary](#module-summary)
2. [Timetable Module - Detailed](#timetable-module---detailed)
3. [Attendance Module - Detailed](#attendance-module---detailed)
4. [Holiday Module - Detailed](#holiday-module---detailed)
5. [Database Schema](#database-schema)
6. [Backend Architecture](#backend-architecture)
7. [API Endpoints](#api-endpoints)
8. [Frontend Structure](#frontend-structure)
9. [Holiday Validation Logic](#holiday-validation-logic)
10. [Usage Guide](#usage-guide)
11. [Testing](#testing)
12. [Sidebar Navigation](#sidebar-navigation)

---

## Module Summary

### 1. Timetable Module
**Purpose:** Create and manage weekly/daily class schedules

**Features:**
- ✅ Create and manage weekly/class schedules
- ✅ Assign subjects to teachers and classes
- ✅ View timetable by Class, Teacher, Student
- ✅ Edit and update schedules
- ✅ **Prevent timetable creation on holiday dates**
- ✅ Conflict detection (teacher, room, division)
- ✅ Grid and Table views
- ✅ Import/Export functionality
- ✅ Responsive UI

### 2. Attendance Module
**Purpose:** Daily attendance tracking and reporting

**Features:**
- ✅ Daily attendance marking (Present, Absent, Late)
- ✅ Class-wise and subject-wise attendance
- ✅ Student-wise attendance reports
- ✅ Class-wise attendance reports
- ✅ Monthly attendance summaries
- ✅ Export reports (PDF/Excel)
- ✅ **STRICT: Cannot mark attendance on holidays**
- ✅ Real-time holiday validation

### 3. Holiday Module
**Purpose:** Manage school holidays and events

**Features:**
- ✅ Add holiday name and date range
- ✅ Single date or date range support
- ✅ Optional description
- ✅ View, edit, delete holidays
- ✅ **Automatically blocks timetable creation**
- ✅ **Automatically blocks attendance marking**
- ✅ Holiday types: Public, School, Event, Program
- ✅ Recurring holiday support

---

## Timetable Module - Detailed

### Overview
The Timetable Module allows administrators and principals to create, manage, and view class schedules for the entire institution. It supports both weekly recurring schedules and date-specific schedules.

### User Roles & Permissions

| Role | Create | Edit | Delete | View |
|------|--------|------|--------|------|
| Admin | ✅ | ✅ | ✅ | ✅ |
| Principal | ✅ | ✅ | ✅ | ✅ |
| Teacher | ❌ | ❌ | ❌ | ✅ (Own only) |
| Student | ❌ | ❌ | ❌ | ✅ (Own division) |
| Office Staff | ✅ | ✅ | ✅ | ✅ |

### Views Available

#### 1. Table View
- Paginated list of all timetable entries
- Search by subject, teacher, or room
- Filter by division, day, teacher, date, academic year
- Status badges (Active/Cancelled/Completed)

**Route:** `/academic/timetable` or `/academic/timetable/table`

#### 2. Grid View
- Weekly calendar-style display
- One division at a time
- Time slots on Y-axis, days on X-axis
- Quick add/edit via modal

**Route:** `/academic/timetable/grid`

#### 3. Teacher View
- Timetable for a specific teacher
- Shows all classes assigned to teacher

**Route:** `/academic/timetable/teacher`

#### 4. Print/PDF View
- Printable format of timetable
- Export to PDF for sharing

**Route:** `/academic/timetable/print` or `/academic/timetable/export/pdf`

### Creating a Timetable

**Step-by-Step:**

1. Navigate to **Academic → Timetable**
2. Click **"Add Timetable"** or use **+ Add** in Grid View
3. Fill in the form:

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| Division | Dropdown | ✅ | Class division (e.g., FY-A) |
| Subject | Dropdown | ✅ | Subject to schedule |
| Teacher | Dropdown | ✅ | Teacher for this class |
| Room | Dropdown | ❌ | Physical room (if available) |
| Day | Dropdown | ✅* | Day of week (for weekly schedule) |
| Date | Date Picker | ✅* | Specific date (for one-time class) |
| Start Time | Time | ✅ | Class start time |
| End Time | Time | ✅ | Class end time |
| Period Name | Text | ❌ | e.g., "Period 1", "Lecture A" |
| Academic Year | Dropdown | ✅ | Current academic year |
| Status | Dropdown | ✅ | Active/Cancelled/Completed |
| Notes | Text Area | ❌ | Additional information |

*Either Day OR Date is required

4. **System validates:**
   - Is the date a holiday? → **Block if yes**
   - Is the teacher available? → **Warn if conflict**
   - Is the room available? → **Warn if conflict**
   - Does the division have another class? → **Warn if conflict**

5. Click **Save**

### Conflict Detection

The system checks for three types of conflicts:

#### Division Conflict
```
Same division + Same day/time + Different subject
→ Warning: "Division already has a class at this time"
```

#### Teacher Conflict
```
Same teacher + Same day/time + Different division
→ Warning: "Teacher is already scheduled for another class"
```

#### Room Conflict
```
Same room + Same day/time + Different class
→ Warning: "Room is already booked at this time"
```

### Holiday Blocking

**Before saving, the system checks:**

```php
if ($this->holidayService->isHoliday($date, $academicYearId)) {
    return back()->with('error', 
        'This date is marked as Holiday. Attendance and Timetable cannot be added.');
}
```

**User sees:**
- ❌ Error message in red
- ❌ Form data preserved (except date)
- ❌ Cannot proceed until non-holiday date is selected

### AJAX Features

**Get Timetable for Date:**
```javascript
GET /academic/timetable/ajax/get-by-date?date=2025-03-15&division_id=1

Response:
{
    "status": "active",
    "available": true,
    "date": "2025-03-15",
    "day": "Saturday",
    "periods": [...]
}
```

**Check Holiday:**
```javascript
GET /academic/timetable/ajax/check-holiday?date=2025-01-26

Response:
{
    "status": "holiday",
    "available": false,
    "message": "Holiday - No Classes Scheduled",
    "holiday_title": "Republic Day"
}
```

### Import/Export

**Import from Excel:**
1. Download template
2. Fill in schedule data
3. Upload Excel file
4. System validates and imports

**Export to PDF:**
1. Select division and date range
2. Click **Export PDF**
3. Download printable timetable

---

## Attendance Module - Detailed

### Overview
The Attendance Module enables teachers and administrators to mark daily attendance for students, generate reports, and track attendance patterns. It is strictly integrated with the Holiday module to prevent attendance marking on holidays.

### User Roles & Permissions

| Role | Mark Attendance | Edit | Delete | View Reports |
|------|-----------------|------|--------|--------------|
| Admin | ✅ | ✅ | ✅ | ✅ (All) |
| Principal | ✅ | ✅ | ✅ | ✅ (All) |
| Teacher | ✅ (Own division) | ✅ (Own) | ✅ (Own) | ✅ (Own division) |
| Office Staff | ✅ | ✅ | ✅ | ✅ (All) |
| Student | ❌ | ❌ | ❌ | ✅ (Own only) |

### Attendance Status Options

| Status | Code | Description |
|--------|------|-------------|
| Present | `present` | Student attended class |
| Absent | `absent` | Student was absent |
| Late | `late` | Student arrived late (counted as present) |

### Marking Attendance

**Step-by-Step:**

1. Navigate to **Academic → Attendance**
2. Click **"Mark Attendance"**
3. Select:
   - **Division** (e.g., FY-A)
   - **Academic Session** (e.g., Forenoon)
   - **Date**

4. **System validates:**
   ```javascript
   // AJAX call to check holiday
   GET /academic/attendance/check-holiday?date=2025-03-15&division_id=1
   
   // If holiday:
   {
       "is_holiday": true,
       "holiday_title": "Holi",
       "message": "This date is marked as Holiday. Attendance and Timetable cannot be added."
   }
   ```

5. **If holiday:** 
   - ❌ Shows error message
   - ❌ Blocks form submission
   - ❌ Redirects back to index

6. **If not holiday:**
   - ✅ Shows student list with radio buttons
   - ✅ Default selection: "Present"
   - ✅ Can change to "Absent" or "Late" for each student

7. Click **Submit Attendance**

### Attendance Form Structure

```html
<form action="/academic/attendance/store" method="POST">
    @csrf
    
    <!-- Selection Fields -->
    <select name="division_id" required>...</select>
    <select name="academic_session_id" required>...</select>
    <input type="date" name="date" data-block-holidays required>
    
    <!-- Student List -->
    @foreach($students as $student)
        <div class="student-row">
            <span>{{ $student->name }} ({{ $student->roll_number }})</span>
            
            <label>
                <input type="radio" 
                       name="students[{{ $student->id }}][status]" 
                       value="present" checked>
                Present
            </label>
            
            <label>
                <input type="radio" 
                       name="students[{{ $student->id }}][status]" 
                       value="absent">
                Absent
            </label>
            
            <label>
                <input type="radio" 
                       name="students[{{ $student->id }}][status]" 
                       value="late">
                Late
            </label>
        </div>
    @endforeach
    
    <button type="submit" data-validate-holidays>Submit</button>
</form>
```

### Editing Attendance

**To edit existing attendance:**

1. Go to **Academic → Attendance**
2. Select same division and date
3. Click **"Edit Attendance"**
4. Modify status for students
5. Click **Update**

**Route:** `/academic/attendance/edit?division_id=1&date=2025-03-15`

### Attendance Reports

#### 1. Student-wise Report

Shows attendance for a single student across all dates.

**Filters:**
- Student
- Date Range
- Division

**Output:**
```
Student: John Doe
Division: FY-A
Roll No: 101

Date Range: 01-Jan-2025 to 31-Jan-2025

Total Days: 25
Present Days: 22
Absent Days: 3
Attendance %: 88%
```

#### 2. Class-wise Report

Shows attendance summary for an entire division.

**Filters:**
- Division
- Date Range
- Academic Session

**Output Table:**

| Roll No | Student Name | Present | Absent | Late | % |
|---------|--------------|---------|--------|------|---|
| 101 | John Doe | 22 | 3 | 0 | 88% |
| 102 | Jane Smith | 25 | 0 | 0 | 100% |

#### 3. Monthly Summary

Aggregated view of attendance per month.

**Route:** `/academic/attendance/report`

### Export Options

**Export to PDF:**
```
GET /reports/attendance/pdf?division_id=1&start_date=2025-01-01&end_date=2025-01-31
```

**Export to Excel:**
```
GET /reports/attendance/excel?division_id=1&start_date=2025-01-01&end_date=2025-01-31
```

### Holiday Blocking Implementation

**Server-Side (AttendanceController):**

```php
public function store(MarkAttendanceRequest $request)
{
    $validated = $request->validated();

    // Check if date is a holiday
    $holidayCheck = $this->holidayService->validateAttendanceDate(
        $validated['date'],
        Division::find($validated['division_id'])?->academic_year_id
    );

    if (!$holidayCheck['valid'] && $holidayCheck['is_holiday']) {
        return redirect()->back()
            ->withInput()
            ->with('error', $holidayCheck['message'] . ': ' . ($holidayCheck['holiday_title'] ?? ''));
    }

    // Proceed with storing attendance...
    DB::transaction(function () use ($validated) {
        foreach ($validated['students'] as $studentData) {
            Attendance::create([
                'student_id' => $studentData['student_id'],
                'division_id' => $validated['division_id'],
                'academic_session_id' => $validated['academic_session_id'],
                'date' => $validated['date'],
                'status' => $studentData['status']
            ]);
        }
    });

    return redirect()->route('academic.attendance.index')
        ->with('success', 'Attendance marked successfully.');
}
```

**Client-Side (JavaScript):**

```javascript
// holiday-validator.js
document.querySelector('form').addEventListener('submit', async function(e) {
    const dateInput = this.querySelector('input[name="date"]');
    const divisionInput = this.querySelector('select[name="division_id"]');
    const date = dateInput.value;
    const divisionId = divisionInput.value;
    
    const response = await fetch('/academic/attendance/check-holiday', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ date, division_id: divisionId })
    });
    
    const data = await response.json();
    
    if (data.is_holiday) {
        e.preventDefault();
        alert(`Cannot mark attendance: ${data.holiday_title} falls on this date.`);
        return false;
    }
});
```

### Attendance Defaulter List

**Students below threshold attendance:**

```php
// GET /api/attendance/defaulters?threshold=75
public function getDefaulters(Request $request): JsonResponse
{
    $threshold = $request->threshold ?? 75;
    $fromDate = $request->from_date ?? Carbon::now()->subMonth()->toDateString();
    $toDate = $request->to_date ?? Carbon::now()->toDateString();

    $totalDays = Carbon::parse($fromDate)->diffInDays(Carbon::parse($toDate)) + 1;

    $defaulters = Attendance::selectRaw('student_id, COUNT(*) as present_days')
        ->where('status', 'present')
        ->whereBetween('date', [$fromDate, $toDate])
        ->groupBy('student_id')
        ->havingRaw('(COUNT(*) / ?) * 100 < ?', [$totalDays, $threshold])
        ->with('student')
        ->get();

    return response()->json(['success' => true, 'data' => $defaulters]);
}
```

---

## Holiday Module - Detailed

### Overview
The Holiday Module is the foundation for blocking attendance and timetable operations on non-working days. It manages all types of holidays, events, and programs.

### Holiday Types

| Type | Code | Color | Description | Example |
|------|------|-------|-------------|---------|
| Public Holiday | `public_holiday` | 🔴 Red | National/State holidays | Republic Day, Independence Day |
| School Holiday | `school_holiday` | 🟡 Yellow | School-specific closures | Summer break, Winter break |
| Event | `event` | 🔵 Blue | School events | Sports Day, Annual Function |
| Program | `program` | 🟢 Green | Educational programs | Orientation, Seminars |

### Creating a Holiday

**Step-by-Step:**

1. Navigate to **Academic → Holidays**
2. Click **"Add Holiday"**
3. Fill in the form:

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| Title | Text | ✅ | Holiday name (e.g., "Diwali") |
| Description | Text Area | ❌ | Additional details |
| Start Date | Date Picker | ✅ | Holiday start date |
| End Date | Date Picker | ✅ | Holiday end date (same for single day) |
| Type | Dropdown | ✅ | Public/School/Event/Program |
| Is Recurring | Checkbox | ❌ | Repeats every year |
| Academic Year | Dropdown | ✅ | Associated academic year |
| Program Incharge | Dropdown | ❌ | Teacher in-charge (for events) |
| Location | Text | ❌ | Event location |
| Is Active | Checkbox | ✅ | Enable/Disable holiday |

4. Click **Save**

### Multi-Day Holidays

For holidays spanning multiple days (e.g., Summer Break):

```
Title: Summer Break
Start Date: 2025-05-15
End Date: 2025-06-30
Type: School Holiday

→ All dates from May 15 to June 30 are blocked
```

### Recurring Holidays

Check **"Is Recurring"** for holidays that repeat annually:

```
Title: Republic Day
Start Date: 2025-01-26
End Date: 2025-01-26
Is Recurring: ✅

→ Automatically applies to January 26 every year
```

### Holiday List View

**Features:**
- Search by title
- Filter by type, academic year, status
- Sort by date
- Duration badge (number of days)
- Quick edit/delete actions

**Table Columns:**
```
| # | Title | Type | Date Range | Duration | Academic Year | Incharge | Status | Actions |
|---|-------|------|------------|----------|---------------|----------|--------|---------|
| 1 | Republic Day | Public | 26 Jan 2025 | 1 day | 2024-2026 | - | Active | ✏️ 🗑️ |
| 2 | Summer Break | School | 15 May - 30 Jun | 47 days | 2024-2026 | - | Active | ✏️ 🗑️ |
```

### Holiday Statistics

Dashboard cards showing counts:

```
┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐
│  📅 Public      │ │  🏫 School      │ │  🎉 Events      │ │  📊 Programs    │
│  Holidays       │ │  Holidays       │ │                 │ │                 │
│       8         │ │       5         │ │       4         │ │       3         │
└─────────────────┘ └─────────────────┘ └─────────────────┘ └─────────────────┘
```

### API Endpoints

**Check if date is holiday:**
```bash
GET /academic/holidays/check-date?date=2025-01-26&academic_year_id=1

Response:
{
    "is_holiday": true,
    "title": "Republic Day",
    "type": "public_holiday",
    "type_label": "Public Holiday"
}
```

**Get holidays in date range:**
```bash
GET /api/holidays/range?start_date=2025-01-01&end_date=2025-12-31&academic_year_id=1

Response:
{
    "success": true,
    "holidays": {
        "2025-01-26": "Republic Day",
        "2025-05-15": "Summer Break",
        ...
    }
}
```

### Holiday Service Methods

```php
namespace App\Services;

class HolidayService
{
    /**
     * Check if a date is a holiday
     */
    public function isHoliday(Carbon|string $date, ?int $academicYearId = null): bool
    {
        return Holiday::where('is_active', true)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
            ->whereIn('type', ['public_holiday', 'school_holiday'])
            ->exists();
    }

    /**
     * Get all holidays in a date range
     */
    public function getHolidaysInRange(
        Carbon|string $startDate,
        Carbon|string $endDate,
        ?int $academicYearId = null
    ): Collection
    {
        return Holiday::where('is_active', true)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            })
            ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
            ->orderBy('start_date')
            ->get();
    }

    /**
     * Get working days count (excluding holidays)
     */
    public function getWorkingDaysCount(
        Carbon|string $startDate,
        Carbon|string $endDate,
        ?int $academicYearId = null
    ): int
    {
        $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $holidayDates = $this->getHolidayDatesInRange($startDate, $endDate, $academicYearId);
        
        return $totalDays - count($holidayDates);
    }
}
```

---

## Database Schema

### Holidays Table

```sql
CREATE TABLE holidays (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    type ENUM('public_holiday', 'school_holiday', 'event', 'program') DEFAULT 'public_holiday',
    is_recurring BOOLEAN DEFAULT FALSE,
    academic_year_id BIGINT UNSIGNED NOT NULL,
    program_incharge_id BIGINT UNSIGNED NULL,
    location VARCHAR(255) NULL,
    attachment_path VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
    FOREIGN KEY (program_incharge_id) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_dates (start_date, end_date),
    INDEX idx_type_status (type, is_active),
    INDEX idx_academic_year (academic_year_id, is_active)
);
```

### Timetables Table

```sql
CREATE TABLE timetables (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    division_id BIGINT UNSIGNED NOT NULL,
    subject_id BIGINT UNSIGNED NOT NULL,
    teacher_id BIGINT UNSIGNED NULL,
    room_id BIGINT UNSIGNED NULL,
    day_of_week ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'),
    date DATE NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    period_name VARCHAR(100) NULL,
    room_number VARCHAR(100) NULL,
    academic_year_id BIGINT UNSIGNED NOT NULL,
    is_break_time BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    status ENUM('active', 'cancelled', 'completed') DEFAULT 'active',
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (division_id) REFERENCES divisions(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
    
    INDEX idx_division_day (division_id, day_of_week),
    INDEX idx_teacher_day (teacher_id, day_of_week),
    INDEX idx_academic_year (academic_year_id, is_active),
    INDEX idx_date (date)
);
```

### Attendance Table

```sql
CREATE TABLE attendance (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    student_id BIGINT UNSIGNED NOT NULL,
    division_id BIGINT UNSIGNED NOT NULL,
    academic_session_id BIGINT UNSIGNED NULL,
    timetable_id BIGINT UNSIGNED NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent', 'late') NOT NULL,
    marked_by BIGINT UNSIGNED NULL,
    remarks TEXT NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (division_id) REFERENCES divisions(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_session_id) REFERENCES academic_sessions(id) ON DELETE SET NULL,
    FOREIGN KEY (timetable_id) REFERENCES timetables(id) ON DELETE SET NULL,
    FOREIGN KEY (marked_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_date_student (date, student_id),
    INDEX idx_timetable_date (timetable_id, date),
    INDEX idx_marked_by_date (marked_by, date),
    UNIQUE KEY unique_attendance (student_id, date)
);
```

---

## Backend Architecture

### Models

#### Holiday Model (`app/Models/Holiday.php`)

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Holiday extends Model
{
    protected $fillable = [
        'title', 'description', 'start_date', 'end_date',
        'type', 'is_recurring', 'academic_year_id',
        'program_incharge_id', 'location', 'attachment_path', 'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Check if a date is a holiday
    public static function isDateHoliday(Carbon $date, $academicYearId = null): bool
    {
        return self::where('is_active', true)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
            ->exists();
    }

    // Get holiday title for a date
    public static function getHolidayTitle(Carbon $date): ?string
    {
        return self::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first()?->title;
    }
}
```

### Services

#### HolidayService (`app/Services/HolidayService.php`)

```php
namespace App\Services;

use App\Models\Holiday;
use Carbon\Carbon;

class HolidayService
{
    /**
     * Validate if attendance can be marked on a date
     */
    public function validateAttendanceDate(Carbon|string $date, ?int $academicYearId = null): array
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        if ($this->isHoliday($date, $academicYearId)) {
            $holidayDetails = $this->getHolidayDetails($date, $academicYearId);

            return [
                'valid' => false,
                'is_holiday' => true,
                'message' => 'This date is marked as Holiday. Attendance and Timetable cannot be added.',
                'holiday_title' => $holidayDetails['title'] ?? 'Holiday',
                'holiday_type' => $holidayDetails['type_label'] ?? 'Holiday',
            ];
        }

        return [
            'valid' => true,
            'is_holiday' => false,
            'message' => 'Attendance can be marked',
        ];
    }

    /**
     * Check timetable availability for a date
     */
    public function checkTimetableAvailability(Carbon|string $date, ?int $academicYearId = null): array
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        if ($this->isHoliday($date, $academicYearId)) {
            $holidayDetails = $this->getHolidayDetails($date, $academicYearId);

            return [
                'status' => 'holiday',
                'available' => false,
                'message' => 'Holiday - No Classes Scheduled',
                'holiday_title' => $holidayDetails['title'] ?? 'Holiday',
            ];
        }

        return [
            'status' => 'active',
            'available' => true,
            'message' => 'Timetable available',
        ];
    }
}
```

---

## API Endpoints

### Holiday Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/holidays` | List all holidays |
| POST | `/api/holidays` | Create new holiday |
| GET | `/api/holidays/{id}` | Get holiday details |
| PUT | `/api/holidays/{id}` | Update holiday |
| DELETE | `/api/holidays/{id}` | Delete holiday |
| GET | `/api/holidays/check-date?date=YYYY-MM-DD` | Check if date is holiday |
| GET | `/api/holidays/range?start_date=&end_date=` | Get holidays in date range |

### Attendance Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/attendance/mark` | Mark attendance for students |
| GET | `/api/attendance/report` | Get attendance report |
| GET | `/api/attendance/defaulters` | Get attendance defaulters |
| POST | `/academic/attendance/check-holiday` | Check holiday before marking |

### Timetable Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/timetables` | List all timetables |
| POST | `/api/timetables` | Create new timetable |
| GET | `/api/timetables/{id}` | Get timetable details |
| PUT | `/api/timetables/{id}` | Update timetable |
| DELETE | `/api/timetables/{id}` | Delete timetable |
| GET | `/academic/timetable/ajax/check-holiday?date=` | Check holiday for timetable |

---

## Frontend Structure

### Holiday Management Views

```
resources/views/academic/holidays/
├── index.blade.php      # List all holidays
├── create.blade.php     # Create new holiday
├── edit.blade.php       # Edit existing holiday
└── show.blade.php       # View holiday details
```

### Attendance Management Views

```
resources/views/academic/attendance/
├── index.blade.php      # Attendance dashboard
├── create.blade.php     # Mark attendance form
├── mark.blade.php       # Student-wise marking
├── edit.blade.php       # Edit attendance
└── report.blade.php     # Attendance reports
```

### Timetable Management Views

```
resources/views/academic/timetable/
├── index.blade.php      # Table view
├── grid.blade.php       # Weekly grid view
├── create.blade.php     # Create timetable
├── edit.blade.php       # Edit timetable
├── show.blade.php       # View timetable
└── teacher.blade.php    # Teacher-wise view
```

---

## Holiday Validation Logic

### Server-Side Validation

#### In AttendanceController

```php
public function store(MarkAttendanceRequest $request)
{
    $validated = $request->validated();

    // Validate that the date is not a holiday
    $holidayCheck = $this->holidayService->validateAttendanceDate(
        $validated['date'],
        Division::find($validated['division_id'])?->academic_year_id
    );

    if (!$holidayCheck['valid'] && $holidayCheck['is_holiday']) {
        return redirect()->back()
            ->withInput()
            ->with('error', $holidayCheck['message'] . ': ' . ($holidayCheck['holiday_title'] ?? ''));
    }

    // Proceed with attendance marking...
}
```

#### In TimetableController

```php
public function store(StoreTimetableRequest $request)
{
    $date = $request->filled('date') ? Carbon::parse($request->date)->format('Y-m-d') : null;

    // Check if date is a holiday
    if ($date) {
        $holidayCheck = $this->holidayService->validateAttendanceDate(
            $date, 
            $request->academic_year_id
        );

        if ($holidayCheck['is_holiday']) {
            return back()
                ->withInput()
                ->with('error', 'This date is marked as Holiday. Attendance and Timetable cannot be added.');
        }
    }

    // Proceed with timetable creation...
}
```

### Client-Side Validation (JavaScript)

```javascript
// Check holiday before form submission
document.querySelector('form').addEventListener('submit', async function(e) {
    const dateInput = this.querySelector('input[name="date"]');
    const date = dateInput.value;
    
    const response = await fetch(`/academic/holidays/check-date?date=${date}`);
    const data = await response.json();
    
    if (data.is_holiday) {
        e.preventDefault();
        alert(`Cannot proceed: ${data.title} falls on this date. Attendance and Timetable cannot be added.`);
        return false;
    }
});
```

---

## Usage Guide

### 1. Adding a Holiday

**Via Web Interface:**
1. Navigate to **Academic → Holidays**
2. Click **"Add Holiday"**
3. Fill in the form:
   - Title (e.g., "Diwali")
   - Description (optional)
   - Start Date
   - End Date (for multi-day holidays)
   - Type (Public/School/Event/Program)
   - Academic Year
4. Click **Save**

**Via Seeder:**
```bash
php artisan db:seed --class=HolidaySeeder
```

### 2. Marking Attendance

1. Navigate to **Academic → Attendance**
2. Select Division and Date
3. Click **"Mark Attendance"**
4. **System automatically checks for holiday**
5. If holiday: Shows error and blocks submission
6. If not holiday: Mark Present/Absent/Late for each student
7. Click **Submit**

### 3. Creating Timetable

1. Navigate to **Academic → Timetable**
2. Click **"Add Timetable"** or use Grid View
3. Select Division, Subject, Teacher, Date/Day
4. **System automatically checks for holiday**
5. If holiday: Shows error "Cannot schedule classes on holidays"
6. If not holiday: Check for conflicts (teacher, room, division)
7. Click **Save**

---

## Testing

### Run Seeders

```bash
# Create sample holidays
php artisan db:seed --class=HolidaySeeder

# Create sample attendance and timetable data
php artisan db:seed --class=AttendanceAndTimetableSeeder
```

### Test Holiday Validation

```bash
# Test via tinker
php artisan tinker

>>> use App\Services\HolidayService;
>>> use Carbon\Carbon;

>>> $service = new HolidayService();
>>> $service->validateAttendanceDate('2025-01-26', 1);
// Returns: ['valid' => false, 'is_holiday' => true, ...]

>>> $service->validateAttendanceDate('2025-01-27', 1);
// Returns: ['valid' => true, 'is_holiday' => false, ...]
```

### API Testing

```bash
# Check if date is holiday
curl -X GET "http://localhost/academic/holidays/check-date?date=2025-01-26" \
  -H "Accept: application/json" \
  -H "X-CSRF-TOKEN: <your-token>"

# Response:
{
  "is_holiday": true,
  "title": "Republic Day",
  "type": "public_holiday"
}
```

---

## Error Messages

### Attendance Module

| Scenario | Error Message |
|----------|---------------|
| Date is holiday | "This date is marked as Holiday. Attendance and Timetable cannot be added." |
| Holiday with title | "Attendance cannot be marked on holidays: Republic Day" |

### Timetable Module

| Scenario | Error Message |
|----------|---------------|
| Date is holiday | "This date is marked as Holiday. Attendance and Timetable cannot be added." |
| Teacher conflict | "Teacher is already scheduled for another class at this time" |
| Room conflict | "Room is already booked at this time" |
| Division conflict | "Division already has a class at this time" |

---

## File Structure Summary

```
School/
├── app/
│   ├── Models/
│   │   ├── Holiday.php
│   │   ├── Academic/Timetable.php
│   │   └── Academic/Attendance.php
│   ├── Services/
│   │   └── HolidayService.php
│   └── Http/Controllers/
│       ├── Web/
│       │   ├── HolidayController.php
│       │   ├── AttendanceController.php
│       │   └── TimetableController.php
│       └── Api/
│           └── Attendance/AttendanceController.php
├── database/
│   ├── migrations/
│   │   ├── 2026_02_24_000031_create_holidays_table.php
│   │   ├── 2026_02_24_000030_create_enhanced_timetables_table.php
│   │   └── 2026_02_27_140000_update_attendance_table_structure.php
│   └── seeders/
│       ├── HolidaySeeder.php
│       └── AttendanceAndTimetableSeeder.php
├── resources/views/academic/
│   ├── holidays/
│   ├── attendance/
│   └── timetable/
├── resources/views/layouts/
│   └── sidebar.blade.php          ← Updated with module links
├── routes/
│   ├── web.php
│   └── api.php
└── public/js/
    └── holiday-validator.js
```

---

## Sidebar Navigation

The sidebar has been updated with role-based menu items for all three modules:

### Principal/Admin Sidebar
```
├── Dashboard
├── Students
├── Teachers
├── Academic Management
│   ├── Programs
│   ├── Subjects
│   ├── Divisions
│   └── Academic Sessions
├── Timetable & Attendance    ← NEW SECTION
│   ├── Timetable
│   ├── Attendance
│   └── Holidays
└── Reports
```

### Teacher Sidebar
```
├── Dashboard
├── Students
├── Assignments
└── Timetable & Attendance    ← NEW SECTION
    ├── Timetable
    ├── Mark Attendance
    └── Holidays
```

### Student Sidebar
```
├── Dashboard
├── Profile
├── Fees
├── Library
└── View Only Section         ← NEW SECTION
    ├── My Timetable
    ├── My Attendance
    └── Holidays
```

### Office Staff Sidebar
```
├── Dashboard
├── Admissions
├── Students
└── Timetable & Attendance    ← NEW SECTION
    ├── Timetable
    ├── Attendance
    └── Holidays
```

### Other Roles (View Only)
- **Accounts Staff**: Holidays
- **Librarian**: Holidays

### Icon Legend
- 📅 `calendar-week` - Timetable
- 📋 `clipboard-check` - Attendance
- 🗓️ `calendar-event` - Holidays

---

## Sidebar Implementation Details

### Blade Component Code

**File:** `resources/views/layouts/app.blade.php` (Main Layout File)

```php
<!-- Timetable & Attendance Section -->
<div class="nav-section">
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('academic.timetable.*') || request()->routeIs('academic.attendance.*') || request()->routeIs('academic.holidays.*') ? 'active' : '' }}"
           href="#" data-bs-toggle="collapse" data-bs-target="#timetableAttendance" aria-expanded="{{ request()->routeIs('academic.timetable.*') || request()->routeIs('academic.attendance.*') || request()->routeIs('academic.holidays.*') ? 'true' : 'false' }}">
            <i class="bi bi-calendar-week"></i>
            <span>Timetable & Attendance</span>
        </a>
        <div class="collapse {{ request()->routeIs('academic.timetable.*') || request()->routeIs('academic.attendance.*') || request()->routeIs('academic.holidays.*') ? 'show' : '' }}" id="timetableAttendance">
            <div class="dropdown-menu show">
                <a class="dropdown-item {{ request()->routeIs('academic.timetable.*') ? 'active' : '' }}"
                   href="{{ route('academic.timetable.index') }}">
                    <i class="bi bi-calendar-week me-2"></i>Timetable
                </a>
                <a class="dropdown-item {{ request()->routeIs('academic.attendance.*') ? 'active' : '' }}"
                   href="{{ route('academic.attendance.index') }}">
                    <i class="bi bi-clipboard-check me-2"></i>Attendance
                </a>
                <a class="dropdown-item {{ request()->routeIs('academic.holidays.*') ? 'active' : '' }}"
                   href="{{ route('academic.holidays.index') }}">
                    <i class="bi bi-calendar-event me-2"></i>Holidays
                </a>
            </div>
        </div>
    </li>
</div>
```

**Note:** The sidebar is implemented in `app.blade.php` (not a separate sidebar.blade.php file). The changes have been added for the following roles:
- **Admin**: Inside `@if($role === 'admin')` block
- **Principal**: Inside `@if(in_array($role, ['principal', 'office']))` block
- **Teacher**: Inside `@if($role === 'teacher')` block
- **Student**: Inside `@if($role === 'student')` block
- **Office**: Inside `@if(in_array($role, ['principal', 'office']))` block

### Visual Sidebar Layout

```
┌─────────────────────────────────┐
│  🎓 School ERP                  │  ← Logo/Header
│  Principal Portal               │  ← Role indicator
├─────────────────────────────────┤
│  🏠 Dashboard                   │
│  👥 Students                    │
│  👨‍🏫 Teachers                    │
│                                 │
│  📚 Academic Management         │  ← Section Header
│     ├── 🎓 Programs             │
│     ├── 📖 Subjects             │
│     ├── 📊 Divisions            │
│     └── 📅 Academic Sessions    │
│                                 │
│  📋 Timetable & Attendance      │  ← NEW Section
│     ├── 📅 Timetable            │
│     ├── 📋 Attendance           │
│     └── 🗓️ Holidays             │
│                                 │
│  📊 Reports                     │
├─────────────────────────────────┤
│  🚪 Logout                      │
└─────────────────────────────────┘
```

### Menu Item Configuration

| Property | Description | Example |
|----------|-------------|---------|
| `name` | Display text | `'Timetable'` |
| `route` | Laravel route name | `'academic.timetable.index'` |
| `icon` | Bootstrap icon class | `'calendar-week'` |

### Adding New Menu Items

```php
// Add to $menuByRole array
['name' => 'Menu Name', 'route' => 'route.name', 'icon' => 'icon-name'],
```

### Role-Based Visibility

The sidebar automatically shows/hides menu items based on user role:

```php
$user = Auth::user();
$role = $user->roles->first()->name ?? 'student';
$menuItems = $menuByRole[$role] ?? [];
```

### Active State Highlighting

Current page is highlighted using:

```php
{{ request()->routeIs($item['route']) ? 'bg-white bg-opacity-20' : '' }}
```

### Mobile Responsive Sidebar

```html
<!-- Mobile Sidebar (Hidden by default) -->
<div x-show="sidebarOpen" 
     class="mobile-sidebar position-fixed top-0 start-0 text-white p-3"
     style="width: 250px; height: 100vh; z-index: 1050;">
    
    <button class="btn btn-light btn-sm mb-3" @click="sidebarOpen = false">
        <i class="bi bi-x me-1"></i> Close
    </button>
    
    <!-- Menu items (same as desktop) -->
    @foreach($menuItems as $item)
        <a href="{{ route($item['route']) }}" 
           class="d-flex align-items-center text-white d-block mb-3 p-2 rounded"
           @click="sidebarOpen = false">
            <i class="bi bi-{{ $item['icon'] }} me-2"></i>
            {{ $item['name'] }}
        </a>
    @endforeach
</div>

<!-- Overlay -->
<div x-show="sidebarOpen" 
     class="position-fixed inset-0 bg-black bg-opacity-50"
     style="z-index: 1040;"
     @click="sidebarOpen = false"></div>
```

### Bootstrap Icons Used

```html
<!-- Timetable -->
<i class="bi bi-calendar-week"></i>

<!-- Attendance -->
<i class="bi bi-clipboard-check"></i>

<!-- Holidays -->
<i class="bi bi-calendar-event"></i>
```

### CSS Styling

```css
/* Desktop Sidebar */
.sidebar {
    width: 250px;
    min-height: 100vh;
    background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
}

/* Menu Item Hover */
.sidebar a:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(5px);
    transition: all 0.2s ease;
}

/* Active Item */
.sidebar a.active {
    background: rgba(255, 255, 255, 0.2);
    border-left: 4px solid #fff;
}

/* Mobile Sidebar */
.mobile-sidebar {
    background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
    box-shadow: 4px 0 10px rgba(0, 0, 0, 0.2);
}
```

### Navigation Flow

```
User Login
    ↓
Role Detected
    ↓
Sidebar Menu Generated
    ↓
User Clicks Menu Item
    ↓
Route Navigation
    ↓
Page Load (Timetable/Attendance/Holidays)
    ↓
Active State Highlighted
```

### Permission Matrix

| Menu Item | Admin | Principal | Teacher | Student | Office | Accounts | Librarian |
|-----------|-------|-----------|---------|---------|--------|----------|-----------|
| Timetable | Full | Full | View | View | Full | ❌ | ❌ |
| Attendance | Full | Full | Mark | View | Full | ❌ | ❌ |
| Holidays | Full | Full | View | View | Full | View | View |

**Legend:**
- **Full** = Create, Read, Update, Delete
- **Mark** = Can mark/edit attendance
- **View** = Read-only access
- **❌** = No access

### Icon Legend
- 📅 `calendar-week` - Timetable
- 📋 `clipboard-check` - Attendance
- 🗓️ `calendar-event` - Holidays

---

## Support

For issues or questions, refer to:
- Laravel Documentation: https://laravel.com/docs
- Project README.md
- Contact: Development Team

---

**Version:** 1.0  
**Last Updated:** March 2026  
**Status:** ✅ Complete & Production Ready
