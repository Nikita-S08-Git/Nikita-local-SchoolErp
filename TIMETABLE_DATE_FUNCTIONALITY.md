# Timetable Date Functionality Implementation

## Overview

This document describes the complete implementation of **Date Functionality** for the Timetable module, allowing both recurring weekly schedules and specific date-based scheduling.

---

## Database Changes

### Migration: Add Date Column to Timetables Table

**File:** `database/migrations/2026_02_28_070153_add_date_to_timetables_table.php`

```php
Schema::table('timetables', function (Blueprint $table) {
    // Add date column for specific date scheduling
    $table->date('date')->nullable()->after('day_of_week');
    
    // Add indexes for performance
    $table->index('date', 'timetables_date_index');
    $table->index(['division_id', 'date'], 'timetables_division_date_index');
});
```

**Run Migration:**
```bash
php artisan migrate
```

---

## Model Updates

### Timetable Model

**File:** `app/Models/Academic/Timetable.php`

#### 1. Added `date` to fillable attributes:
```php
protected $fillable = [
    'division_id',
    'subject_id',
    'teacher_id',
    'room_id',
    'day_of_week',
    'date',  // NEW
    'start_time',
    'end_time',
    // ... other fields
];
```

#### 2. Added date casting:
```php
protected $casts = [
    'date' => 'date',  // NEW - Casts to Carbon instance
    'start_time' => 'datetime:H:i',
    'end_time' => 'datetime:H:i',
    // ... other casts
];
```

#### 3. Added new query scopes:

**Filter by specific date:**
```php
public function scopeByDate(Builder $query, $date): Builder
{
    if ($date instanceof \Carbon\Carbon) {
        $date = $date->format('Y-m-d');
    }
    return $query->whereDate('date', $date);
}
```

**Filter by date range:**
```php
public function scopeByDateRange(Builder $query, $startDate, $endDate): Builder
{
    return $query->whereBetween('date', [$startDate, $endDate]);
}
```

**Filter by date OR day of week:**
```php
public function scopeByDateOrDay(Builder $query, $date, $dayOfWeek): Builder
{
    return $query->where(function ($q) use ($date, $dayOfWeek) {
        $q->whereDate('date', $date)
          ->orWhere('day_of_week', $dayOfWeek);
    });
}
```

---

## Controller Updates

### TimetableController

**File:** `app/Http/Controllers/Web/TimetableController.php`

#### 1. Create Method (Store)

Now handles both date and day_of_week:

```php
public function store(StoreTimetableRequest $request)
{
    // Parse date if provided
    $date = $request->filled('date') ? Carbon::parse($request->date)->format('Y-m-d') : null;
    
    // If date is provided, derive day_of_week from it
    $dayOfWeek = $date ? Carbon::parse($date)->format('l') : $request->day_of_week;

    Timetable::create([
        'division_id' => $request->division_id,
        'subject_id' => $request->subject_id,
        'teacher_id' => $request->teacher_id,
        'room_id' => $request->room_id,
        'day_of_week' => strtolower($dayOfWeek),
        'date' => $date,  // NEW
        'start_time' => $timeSlot?->start_time,
        'end_time' => $timeSlot?->end_time,
        // ... other fields
    ]);
}
```

#### 2. Update Method

Same logic as store:

```php
public function update(UpdateTimetableRequest $request, Timetable $timetable)
{
    $date = $request->filled('date') ? Carbon::parse($request->date)->format('Y-m-d') : null;
    $dayOfWeek = $date ? Carbon::parse($date)->format('l') : $request->day_of_week;

    $timetable->update([
        'day_of_week' => strtolower($dayOfWeek),
        'date' => $date,  // NEW
        // ... other fields
    ]);
}
```

#### 3. Table View with Date Filter

```php
public function tableView(Request $request)
{
    $query = Timetable::withRelationships()->byStatus('active');
    
    // NEW: Filter by specific date
    if ($request->filled('date')) {
        $query->byDate($request->date);
    }
    
    // Other filters...
    
    return view('academic.timetable.table', compact('timetables'));
}
```

#### 4. AJAX: Get Timetable by Date

```php
public function getByDate(Request $request): JsonResponse
{
    $date = Carbon::parse($request->date);
    
    // First check if it's a holiday
    $holidayCheck = $this->holidayService->checkTimetableAvailability($date);
    
    if ($holidayCheck['status'] === 'holiday') {
        return response()->json($holidayCheck);
    }

    // Get timetable for the date
    $dayName = $date->format('l');
    $timetables = Timetable::withRelationships()
        ->byDateOrDay($date, strtolower($dayName))
        ->get();

    return response()->json([
        'status' => 'active',
        'periods' => $timetables,
    ]);
}
```

---

## View Updates

### 1. Create View

**File:** `resources/views/academic/timetable/create.blade.php`

#### Added Date Field:
```html
<div class="col-md-6">
    <label for="date" class="form-label">Specific Date (Optional)</label>
    <input type="date" name="date" id="date" 
           class="form-select" 
           value="{{ old('date') }}" 
           min="{{ date('Y-m-d') }}">
    <div class="form-text">
        <i class="bi bi-info-circle"></i> 
        Leave empty for recurring schedule, or select a specific date
    </div>
</div>
```

#### JavaScript: Auto-populate Day from Date
```javascript
const dateInput = document.getElementById('date');
const daySelect = document.getElementById('day_of_week');

dateInput.addEventListener('change', function() {
    const selectedDate = this.value;
    if (selectedDate) {
        const dateObj = new Date(selectedDate + 'T00:00:00');
        const dayName = dateObj.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
        
        // Auto-select day
        daySelect.value = dayName;
        daySelect.dispatchEvent(new Event('change'));
    }
});
```

#### Form Validation:
```javascript
form.addEventListener('submit', function(e) {
    const dateValue = dateInput?.value;
    const dayValue = daySelect?.value;
    
    if (!dateValue && !dayValue) {
        e.preventDefault();
        alert('Please select either a specific date or a day of the week');
        return false;
    }
});
```

---

### 2. Edit View

**File:** `resources/views/academic/timetable/edit.blade.php`

#### Added Date Field:
```html
<div class="col-md-3">
    <label for="date" class="form-label">Specific Date (Optional)</label>
    <input type="date" name="date" id="date" class="form-control"
           value="{{ $timetable->date ? $timetable->date->format('Y-m-d') : old('date') }}">
    <div class="form-text">
        <i class="bi bi-info-circle"></i> For one-time schedule on specific date
    </div>
</div>
```

#### JavaScript: Auto-populate Day from Date
Same as create view (see above).

---

### 3. Table View

**File:** `resources/views/academic/timetable/table.blade.php`

#### Added Date Column:
```html
<thead>
    <tr>
        <th>#</th>
        <th>Date</th>  <!-- NEW -->
        <th>Day</th>
        <th>Time</th>
        <!-- ... other columns -->
    </tr>
</thead>
<tbody>
    @forelse($timetables as $timetable)
        <tr>
            <td>{{ $index }}</td>
            <td>
                @if($timetable->date)
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-event text-primary me-1"></i>
                        <span>{{ $timetable->date->format('d M Y') }}</span>
                    </div>
                    <small class="text-muted">Specific date</small>
                @else
                    <span class="text-muted">—</span>
                    <br><small class="text-muted">Recurring</small>
                @endif
            </td>
            <!-- ... other columns -->
        </tr>
    @endforelse
</tbody>
```

#### Added Date Filter:
```html
<div class="col-md-2">
    <label class="form-label">Date</label>
    <input type="date" name="date" id="date_filter" class="form-control"
           value="{{ request('date') }}" min="{{ date('Y-m-d') }}">
</div>
```

#### Improved Delete Confirmation:
```javascript
document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.dataset.id;
        const name = this.dataset.name;
        
        document.getElementById('deleteItemName').textContent = name;
        document.getElementById('deleteForm').action = `/timetable/${id}`;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    });
});
```

---

## Usage Examples

### 1. Create Recurring Weekly Timetable

**Scenario:** Math class every Monday at 9:00 AM

1. Go to **Academic → Timetable → Add Class**
2. Fill in:
   - Division: BSC Computer Science
   - Subject: Mathematics
   - Teacher: Dr. Smith
   - **Day:** Monday
   - **Date:** (Leave empty)
   - Time Slot: 09:00 - 10:00
3. Click **Schedule Class**

**Result:** Class appears every Monday in the timetable.

---

### 2. Create Specific Date Timetable

**Scenario:** Special workshop on 15th March 2026

1. Go to **Academic → Timetable → Add Class**
2. Fill in:
   - Division: BSC Computer Science
   - Subject: Advanced Programming
   - Teacher: Prof. Johnson
   - **Date:** 2026-03-15
   - **Day:** (Auto-populated as "sunday")
   - Time Slot: 14:00 - 16:00
3. Click **Schedule Class**

**Result:** Class appears only on 15th March 2026.

---

### 3. Edit Timetable Entry

**Scenario:** Change recurring class to specific date

1. Go to **Academic → Timetable → Table View**
2. Click **Edit** (pencil icon) on desired entry
3. To change from recurring to specific date:
   - Select a **Date** from the date picker
   - Day will auto-populate
4. Click **Update**

**Result:** Entry now has both date and day_of_week.

---

### 4. Delete Timetable Entry

**Scenario:** Remove a class from timetable

1. Go to **Academic → Timetable → Table View**
2. Click **Delete** (trash icon)
3. Confirmation modal appears:
   ```
   Confirm Delete
   
   Are you sure you want to delete this timetable entry?
   "Mathematics on 15 Mar 2026"
   
   [Cancel] [Delete]
   ```
4. Click **Delete** to confirm

**Result:** Entry is permanently deleted.

---

### 5. Filter by Date

**Scenario:** View all classes on a specific date

1. Go to **Academic → Timetable → Table View**
2. In the filter section, select a **Date**
3. Form auto-submits
4. Table shows only entries for that date

**Result:** Filtered view showing only selected date's classes.

---

## API Endpoints

### 1. Get Timetable by Date

**Endpoint:** `GET /academic/timetable/ajax/get-by-date`

**Request:**
```
GET /academic/timetable/ajax/get-by-date?date=2026-03-15&division_id=1
```

**Response (Holiday):**
```json
{
    "status": "holiday",
    "available": false,
    "message": "Holiday - No Classes Scheduled",
    "holiday_title": "Holi Festival",
    "periods": []
}
```

**Response (Active):**
```json
{
    "status": "active",
    "available": true,
    "message": "Timetable loaded successfully",
    "date": "2026-03-15",
    "day": "Sunday",
    "periods": [
        {
            "id": 1,
            "subject": {"name": "Mathematics", "code": "MATH101"},
            "teacher": {"name": "Dr. Smith"},
            "division": {"division_name": "BSC CS"},
            "start_time": "09:00",
            "end_time": "10:00",
            "date": "2026-03-15",
            "day_of_week": "sunday"
        }
    ]
}
```

---

### 2. Check Holiday

**Endpoint:** `GET /academic/timetable/ajax/check-holiday`

**Request:**
```
GET /academic/timetable/ajax/check-holiday?date=2026-03-15
```

**Response:**
```json
{
    "status": "holiday",
    "available": false,
    "message": "Holiday - No Classes Scheduled",
    "holiday_title": "Holi Festival",
    "periods": []
}
```

---

## Business Logic

### Date vs Day of Week Priority

1. **If `date` is provided:**
   - System derives `day_of_week` from the date
   - Timetable entry is specific to that date
   - Takes priority over recurring schedules

2. **If `date` is NULL:**
   - Uses `day_of_week` for recurring weekly schedule
   - Appears every week on that day

3. **If both are provided:**
   - Date takes priority
   - Day of week is auto-calculated

---

## Validation Rules

### Create/Update Form

```php
[
    'division_id' => 'required|exists:divisions,id',
    'subject_id' => 'required|exists:subjects,id',
    'teacher_id' => 'required|exists:users,id',
    'day_of_week' => 'required_without:date|string',
    'date' => 'nullable|date|after_or_equal:today',
    'time_slot_id' => 'required|exists:time_slots,id',
    'academic_year_id' => 'required|exists:academic_years,id',
]
```

**Key Rules:**
- Either `date` OR `day_of_week` must be provided
- Date cannot be in the past
- Date must be valid format (Y-m-d)

---

## Display Logic

### Table View

| Date Column Display | Condition |
|---------------------|-----------|
| 📅 15 Mar 2026<br><small>Specific date</small> | `date` is set |
| —<br><small>Recurring</small> | `date` is NULL |

---

## Integration with Holiday System

When creating/editing timetable:

1. **Holiday Check:**
   - If selected date is a holiday, show warning
   - User can proceed or cancel

2. **Timetable Loading:**
   - If date is holiday, return empty timetable with message
   - Prevents accidental scheduling on holidays

---

## Testing

### Manual Testing Checklist

- [ ] Create recurring timetable (no date)
- [ ] Create specific date timetable
- [ ] Edit recurring to add date
- [ ] Edit specific date to change date
- [ ] Delete timetable entry
- [ ] Filter table by date
- [ ] Auto-populate day from date
- [ ] Holiday detection on date selection
- [ ] View timetable by date (AJAX)

### Automated Test Example

```php
use Tests\TestCase;
use App\Models\Academic\Timetable;

class TimetableDateTest extends TestCase
{
    public function test_can_create_timetable_with_date()
    {
        $response = $this->post(route('academic.timetable.store'), [
            'division_id' => 1,
            'subject_id' => 1,
            'teacher_id' => 1,
            'date' => '2026-03-15',
            'time_slot_id' => 1,
            'academic_year_id' => 1,
        ]);

        $response->assertRedirect(route('academic.timetable.index'));
        
        $this->assertDatabaseHas('timetables', [
            'date' => '2026-03-15',
            'day_of_week' => 'sunday',
        ]);
    }

    public function test_date_auto_populates_day_of_week()
    {
        $timetable = Timetable::create([
            'division_id' => 1,
            'subject_id' => 1,
            'teacher_id' => 1,
            'date' => '2026-03-15', // Sunday
            'start_time' => '09:00',
            'end_time' => '10:00',
            'academic_year_id' => 1,
        ]);

        $this->assertEquals('sunday', $timetable->day_of_week);
    }

    public function test_filter_by_date()
    {
        Timetable::create([
            'division_id' => 1,
            'subject_id' => 1,
            'teacher_id' => 1,
            'date' => '2026-03-15',
            'day_of_week' => 'sunday',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'academic_year_id' => 1,
        ]);

        $response = $this->get(route('academic.timetable.table', ['date' => '2026-03-15']));
        
        $response->assertStatus(200);
        $response->assertSee('15 Mar 2026');
    }
}
```

---

## Troubleshooting

### Issue: Date column doesn't exist

**Solution:**
```bash
php artisan migrate
```

### Issue: Day not auto-populating from date

**Solution:**
1. Check JavaScript console for errors
2. Ensure date input has id="date"
3. Ensure day select has id="day_of_week"

### Issue: Date filter not working

**Solution:**
1. Clear cache: `php artisan cache:clear`
2. Check controller has `byDate` scope call
3. Verify Timetable model has `scopeByDate` method

---

## Performance Considerations

1. **Indexes:**
   - `date` column is indexed
   - Composite index on `division_id + date`

2. **Caching:**
   - Holiday checks are cached for 24 hours
   - Timetable queries use eager loading

3. **Query Optimization:**
   ```php
   // Good - Uses index
   Timetable::byDate('2026-03-15')->get();
   
   // Bad - Full table scan
   Timetable::whereDate('created_at', '2026-03-15')->get();
   ```

---

## Security

1. **Authorization:**
   - Only admin/principal can create/edit/delete
   - Middleware checks user permissions

2. **Validation:**
   - All inputs validated
   - SQL injection prevented by Eloquent

3. **CSRF Protection:**
   - All forms include `@csrf`
   - AJAX requests include CSRF token

---

## Future Enhancements

1. **Bulk Date Creation:**
   - Create classes for multiple dates at once
   - Copy timetable to specific dates

2. **Date Range Filter:**
   - Filter timetable by date range
   - Export timetable for date range

3. **Recurring Patterns:**
   - Custom recurrence rules (every 2 weeks, etc.)
   - End date for recurring schedules

4. **Conflict Detection:**
   - Warn if same teacher has class on same date
   - Room double-booking prevention

---

**Last Updated:** 2026-02-28  
**Version:** 2.0.0  
**Author:** School ERP Development Team
