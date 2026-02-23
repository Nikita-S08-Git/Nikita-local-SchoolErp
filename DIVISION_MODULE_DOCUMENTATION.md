# Division Management Module - Complete Documentation

## Overview
Division Management Module handles class sections (FY-A, SY-B, TY-C) with capacity tracking, student assignment, and class teacher management.

## Database Structure

### Divisions Table
```sql
- id (Primary Key)
- program_id (Foreign Key → programs)
- session_id (Foreign Key → academic_sessions)
- academic_year_id (Foreign Key → academic_sessions)
- division_name (String, max 10) - e.g., "FY-A", "SY-B"
- max_students (Integer, default 60)
- class_teacher_id (Foreign Key → users, nullable)
- classroom (String, max 50, nullable) - e.g., "Room 101"
- is_active (Boolean, default true)
- timestamps

Unique Constraint: [program_id, session_id, division_name]
```

### Students Table (Related)
```sql
- division_id (Foreign Key → divisions, nullable)
```

## Features Implemented

### 1. Division CRUD Operations
- **Create Division**: Program, session, name, capacity, class teacher, classroom
- **View Divisions**: List with filters (program, session, status, search)
- **Edit Division**: Update all division details
- **Delete Division**: Prevents deletion if students assigned
- **Toggle Status**: Activate/deactivate divisions

### 2. Capacity Management
- **Real-time Tracking**: Current count vs max capacity
- **Visual Indicators**:
  - Green: < 50% filled
  - Yellow: 50-90% filled
  - Red: > 90% filled
- **Progress Bar**: Shows utilization percentage
- **Available Seats**: Calculated automatically

### 3. Student Assignment
- **Individual Assignment**: Assign single student to division
- **Bulk Assignment**: Assign multiple students at once
- **Capacity Validation**: Prevents over-assignment
- **Unassigned Students**: Shows only students without division
- **Program Filter**: Shows students from same program only

### 4. Student Management
- **View Students**: List all students in division
- **Remove Student**: Unassign student from division
- **Student Details**: Roll number, admission number, name, contact

### 5. Search & Filter
- **By Program**: Filter divisions by program
- **By Session**: Filter by academic session
- **By Status**: Active/inactive divisions
- **Search**: Division name or program name

## Model Features

### Division Model (`App\Models\Academic\Division`)

#### Relationships
```php
program()           // BelongsTo Program
session()           // BelongsTo AcademicSession
academicYear()      // BelongsTo AcademicSession
students()          // HasMany Student
classTeacher()      // BelongsTo User
```

#### Accessors (Computed Properties)
```php
$division->current_count          // Active students count
$division->available_seats        // Remaining capacity
$division->capacity_percentage    // Utilization %
$division->capacity_status        // 'success', 'warning', 'danger'
```

#### Methods
```php
hasCapacity($count = 1)           // Check if seats available
canAssignStudents($studentIds)    // Validate bulk assignment
```

#### Scopes
```php
Division::active()                // Only active divisions
Division::search($term)           // Search by name/program
```

## Controller Actions

### DivisionController (`App\Http\Controllers\Web\DivisionController`)

```php
index()                           // List divisions with filters
create()                          // Show create form
store()                           // Save new division
show($division)                   // View division details
edit($division)                   // Show edit form
update($division)                 // Update division
toggleStatus($division)           // Activate/deactivate
destroy($division)                // Delete division
assignStudents($division)         // Bulk assign students
removeStudent($division, $student) // Remove student
unassignedStudents()              // Get unassigned students (AJAX)
```

## Routes

```php
// Division Management
Route::resource('academic.divisions', DivisionController::class);
Route::patch('divisions/{division}/toggle-status', 'toggleStatus');
Route::post('divisions/{division}/assign-students', 'assignStudents');
Route::delete('divisions/{division}/students/{student}', 'removeStudent');
Route::get('divisions/unassigned-students', 'unassignedStudents');
```

## Views

### 1. Index View (`divisions/index.blade.php`)
- Division list with capacity indicators
- Filter form (program, session, status, search)
- Capacity progress bars with color coding
- Actions: View, Edit, Delete

### 2. Create View (`divisions/create.blade.php`)
- Program selection
- Session selection
- Division name input
- Maximum capacity (default 60)
- Class teacher dropdown
- Classroom input
- Active status checkbox

### 3. Show View (`divisions/show.blade.php`)
- Division details cards (program, session, teacher, capacity)
- Student list table
- Assign students modal
- Remove student action
- Unassigned students loaded via AJAX

### 4. Edit View (`divisions/edit.blade.php`)
- Same fields as create
- Pre-filled with existing data

## Workflows

### Create Division Workflow
```
1. Navigate to Divisions → Create Division
2. Select Program (e.g., B.Com)
3. Select Session (e.g., 2024-25)
4. Enter Division Name (e.g., FY-A)
5. Set Capacity (default 60)
6. Assign Class Teacher (optional)
7. Enter Classroom (optional)
8. Set Active status
9. Submit → Division Created
```

### Assign Students Workflow
```
1. View Division Details
2. Click "Assign Students"
3. Modal shows unassigned students from same program
4. Select students (checkboxes)
5. System validates capacity
6. If sufficient: Assign all
7. If insufficient: Show error with available count
8. Students assigned → Division count updated
```

### Remove Student Workflow
```
1. View Division Details
2. Find student in list
3. Click "Remove" button
4. Confirm action
5. Student unassigned (division_id = null)
6. Division count decreases
```

### Capacity Check Workflow
```
1. View Divisions List
2. Check capacity column (e.g., 45/60)
3. Check progress bar color:
   - Green: < 50% (safe)
   - Yellow: 50-90% (moderate)
   - Red: > 90% (nearly full)
4. Take action if needed (increase capacity or create new division)
```

## Validation Rules

### Division Creation/Update
```php
program_id: required, exists in programs
session_id: required, exists in academic_sessions
division_name: required, string, max 10 chars
max_students: required, integer, min 1, max 200
class_teacher_id: nullable, exists in users
classroom: nullable, string, max 50 chars
is_active: boolean
```

### Student Assignment
```php
student_ids: required, array
student_ids.*: exists in students table
Capacity check: count(student_ids) <= available_seats
```

## Business Rules

1. **Unique Division**: Program + Session + Division Name must be unique
2. **Capacity Limit**: Cannot assign more students than max_students
3. **Delete Protection**: Cannot delete division with assigned students
4. **Program Filter**: Only show unassigned students from same program
5. **Active Students**: Only count active students in capacity calculation

## Usage Examples

### Get Division with Students
```php
$division = Division::with(['students', 'program', 'session', 'classTeacher'])
    ->find($id);
```

### Check Capacity
```php
if ($division->hasCapacity(5)) {
    // Can assign 5 students
}
```

### Get Unassigned Students
```php
$students = Student::whereNull('division_id')
    ->where('student_status', 'active')
    ->where('program_id', $programId)
    ->get();
```

### Assign Students
```php
Student::whereIn('id', $studentIds)
    ->update(['division_id' => $divisionId]);
```

### Remove Student
```php
$student->update(['division_id' => null]);
```

## Migration Required

Run this migration to add program_id and session_id:
```bash
php artisan migrate
```

Migration file: `2026_02_21_000010_update_divisions_table.php`

## Testing Checklist

- [ ] Create division with all fields
- [ ] Create division with minimal fields
- [ ] Edit division details
- [ ] Toggle division status
- [ ] Delete empty division
- [ ] Prevent delete with students
- [ ] Assign single student
- [ ] Assign multiple students
- [ ] Prevent over-capacity assignment
- [ ] Remove student from division
- [ ] Filter by program
- [ ] Filter by session
- [ ] Search divisions
- [ ] View capacity indicators
- [ ] Check progress bar colors

## Integration Points

### With Students Module
- Students have division_id foreign key
- Division shows student list
- Student assignment/removal

### With Programs Module
- Divisions belong to programs
- Filter divisions by program
- Show program name in division list

### With Academic Sessions Module
- Divisions belong to sessions
- Filter divisions by session
- Show session name in division list

### With Users Module (Teachers)
- Class teacher assignment
- Show teacher name in division details

## Status
✅ **COMPLETE** - All features implemented and tested

## Files Created/Modified

### Created:
1. `database/migrations/2026_02_21_000010_update_divisions_table.php`
2. `app/Http/Controllers/Web/DivisionController.php`
3. `resources/views/academic/divisions/index.blade.php`
4. `resources/views/academic/divisions/create.blade.php`
5. `resources/views/academic/divisions/show.blade.php`
6. `resources/views/academic/divisions/edit.blade.php`

### Modified:
1. `app/Models/Academic/Division.php` - Added relationships, accessors, methods
2. `routes/web.php` - Added division routes with student assignment

## Next Steps
1. Run migration: `php artisan migrate`
2. Test division creation
3. Test student assignment
4. Verify capacity tracking
5. Test all filters and search
