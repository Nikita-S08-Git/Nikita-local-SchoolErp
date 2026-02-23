# Student Management Module - Complete Documentation

## Overview
The Student Management Module is the core of the SchoolERP system, handling the complete student lifecycle from admission to graduation, including personal information, academic records, guardian management, and document handling.

## ✅ Module Status: FULLY IMPLEMENTED

All features described in the requirements are already implemented and functional.

## Database Structure

### Students Table
```sql
- id (Primary Key)
- user_id (Foreign Key → users, nullable)
- program_id (Foreign Key → programs)
- division_id (Foreign Key → divisions, nullable)
- academic_session_id (Foreign Key → academic_sessions)
- admission_number (Unique, auto-generated)
- roll_number (Unique, auto-generated)
- prn (Permanent Registration Number, nullable)
- university_seat_number (nullable)
- first_name, middle_name, last_name
- date_of_birth
- gender (male/female/other)
- blood_group (A+, B+, O+, etc.)
- religion
- category (general/obc/sc/st/vjnt/nt/ews)
- mobile_number, email (unique)
- current_address, permanent_address
- academic_year (FY/SY/TY)
- student_status (active/graduated/dropped/suspended)
- admission_date
- photo_path, signature_path
- cast_certificate_path, marksheet_path
- timestamps
- deleted_at (soft delete)
```

### Student Guardians Table
```sql
- id (Primary Key)
- student_id (Foreign Key → students)
- first_name, last_name
- relation (father/mother/guardian)
- mobile_number, email
- occupation
- annual_income
- office_address
- photo_path
- is_primary_contact (Boolean)
- timestamps
```

## Features Implemented

### 1. Student Admission Process ✅

#### Step 1: Basic Information
- **Personal Details**:
  - First Name, Middle Name, Last Name (validated, letters only)
  - Date of Birth (must be before today)
  - Gender (Male/Female/Other)
  - Email (unique validation)
  - Mobile Number (validated format)
  - Blood Group (A+, B+, AB+, O+, A-, B-, AB-, O-)
  - Religion
  - Category (General/OBC/SC/ST/VJNT/NT/EWS)

- **Address Information**:
  - Current Address (500 chars max)
  - Permanent Address (500 chars max)

- **Academic Assignment**:
  - Program Selection (B.Com, B.Sc, etc.)
  - Division Selection (FY-A, SY-B, etc.)
  - Academic Session (2024-25, etc.)
  - Academic Year (FY/SY/TY)
  - Division capacity check (automatic)

#### Step 2: Auto-Generated Numbers
- **Admission Number**: Auto-generated format `{PROGRAM_CODE}{YEAR}{SEQUENCE}`
  - Example: `BCO24001` (B.Com, 2024, Student #1)
- **Roll Number**: Auto-generated format `{ACADEMIC_YEAR}-{SEQUENCE}`
  - Example: `FY-001` (First Year, Student #1)
- **Uniqueness**: System ensures no duplicates

#### Step 3: Document Upload
- **Photo Upload**: JPEG/PNG, max 2MB
- **Signature Upload**: JPEG/PNG, max 2MB
- **Cast Certificate**: PDF/Image, max 5MB
- **Marksheet**: PDF/Image, max 5MB
- **Storage**: Files stored in `storage/uploads/students/`

#### Step 4: Submission
- Validates all required fields
- Checks email uniqueness
- Checks roll number uniqueness
- Creates student record
- Shows success message with admission number
- Redirects to student profile

### 2. Guardian/Parent Management ✅

#### Add Guardian
- Multiple guardians per student (Father, Mother, Guardian)
- Guardian Information:
  - Full Name (First + Last)
  - Relation to Student
  - Mobile Number (primary contact)
  - Email Address
  - Occupation
  - Annual Income
  - Office Address
  - Photo Upload
  - Primary Contact Flag

#### Primary Contact
- One guardian marked as primary
- System auto-unsets other primaries when new one selected
- Primary contact highlighted in UI

#### Guardian Operations
- **Add**: From student profile page
- **Edit**: Update guardian details
- **Delete**: Remove guardian (with confirmation)
- **View**: Display all guardians on student profile

### 3. Document Management ✅

#### Photo Upload
- Location: Student profile page
- Process:
  1. Click "Upload Photo"
  2. Select image (JPG/PNG)
  3. Validates size (<2MB) and type
  4. Uploads to `/storage/students/{student_id}/photo.jpg`
  5. Saves path in database
  6. Displays in profile

#### Signature Upload
- Similar process to photo
- Saves to `/storage/students/{student_id}/signature.jpg`
- Used for official documents

#### Document Viewing
- Preview in profile page
- Download option available
- Separate sections for each document type

#### Document Deletion
- Delete button for each document
- Confirmation required
- Removes file from server
- Clears path from database

### 4. Student Profile View ✅

Comprehensive profile displaying:

#### Personal Information Card
- Full name, DOB, Gender, Blood Group
- Religion, Category
- Mobile, Email
- Current & Permanent Address

#### Academic Information Card
- Admission Number (badge)
- Roll Number (badge)
- Program Name
- Division Name
- Academic Session
- Academic Year
- Admission Date
- Student Status (color-coded badge)
- PRN (if assigned)
- University Seat Number (if assigned)

#### Guardians Section
- List of all guardians
- Guardian photo (if uploaded)
- Name, Relation, Contact details
- Occupation
- Primary contact indicator
- Edit/Delete buttons for each

#### Photo & Documents Sidebar
- Student Photo (200x250px)
- Student Signature (200x100px)
- Cast Certificate (view/download)
- Marksheet (view/download)

#### Quick Actions Panel
- Edit Student
- Add Guardian
- Print Details
- Delete Student

### 5. Student Listing ✅

#### Statistics Dashboard
- Total Students count
- Active Students count
- Programs count
- Current page count

#### Filter Options
- **By Program**: Dropdown with all programs
- **By Academic Year**: FY/SY/TY
- **By Status**: Active/Graduated/Dropped/Suspended
- **Search**: Name, Email, Roll Number, Admission Number

#### Table Columns
- Photo (40x40px circular)
- Student Details (Name, Admission #, Roll #)
- Academic Info (Program badge, Division, Year)
- Contact (Email, Mobile)
- Status (color-coded badge)
- Actions (View, Edit, Delete)

#### Features
- Pagination (20 per page)
- Auto-submit filters
- Print list option
- Empty state with "Add First Student" CTA

### 6. Student Editing ✅

#### Editable Fields
- All personal information
- Contact details
- Address information
- Academic assignment (program, division, year)
- PRN and University Seat Number
- Photo and signature (replace)
- Student status

#### Locked Fields
- Admission Number (cannot be changed)
- Roll Number (cannot be changed)
- Created date

#### Validation
- Same as creation
- Email uniqueness (except current student)
- Division capacity check (if changing division)

#### File Handling
- Old files deleted when replaced
- New files uploaded to same location
- Path updated in database

#### Audit Trail
- System records who made changes
- Timestamp of modifications

### 7. Student Deactivation ✅

#### Soft Delete Implementation
- Students not permanently deleted
- Marked as deleted with timestamp
- Historical data preserved

#### Deactivation Reasons (via Status)
- **Graduated**: Completed course
- **Dropped**: Left college
- **Suspended**: Disciplinary action
- **Active**: Currently enrolled

#### Effects
- Deactivated students hidden from active lists
- Can be filtered to view
- All relationships preserved
- Documents remain accessible

## Controllers

### StudentController
```php
index()           // List students with filters
create()          // Show create form
store()           // Save new student
show($student)    // View student profile
edit($student)    // Show edit form
update($student)  // Update student
destroy($student) // Soft delete student
generateStudentNumbers() // Auto-generate admission/roll numbers
```

### GuardianController
```php
create($student)              // Show add guardian form
store($student)               // Save new guardian
edit($student, $guardian)     // Show edit form
update($student, $guardian)   // Update guardian
destroy($student, $guardian)  // Delete guardian
```

## Routes

### Student Routes
```php
GET  /dashboard/students                    // List
GET  /dashboard/students/create             // Create form
POST /dashboard/students                    // Store
GET  /dashboard/students/{student}          // Show
GET  /dashboard/students/{student}/edit     // Edit form
PUT  /dashboard/students/{student}          // Update
DELETE /dashboard/students/{student}        // Delete
```

### Guardian Routes
```php
GET  /dashboard/students/{student}/guardians/create           // Create form
POST /dashboard/students/{student}/guardians                  // Store
GET  /dashboard/students/{student}/guardians/{guardian}/edit  // Edit form
PUT  /dashboard/students/{student}/guardians/{guardian}       // Update
DELETE /dashboard/students/{student}/guardians/{guardian}     // Delete
```

## Views

### 1. Index View (`students/index.blade.php`)
- Statistics cards (4 cards)
- Filter form (program, year, status, search)
- Student table with photos
- Pagination
- Empty state

### 2. Create View (`students/create.blade.php`)
- Multi-section form
- Personal details section
- Contact information section
- Academic assignment section
- Document upload section
- Validation messages

### 3. Show View (`students/show.blade.php`)
- Personal information card
- Academic information card
- Guardians section with add button
- Photo & documents sidebar
- Quick actions panel
- Print-friendly layout

### 4. Edit View (`students/edit.blade.php`)
- Pre-filled form
- Same structure as create
- Locked fields (admission/roll number)
- File replacement option

### 5. Guardian Views (`guardians/create.blade.php`, `guardians/edit.blade.php`)
- Guardian information form
- Photo upload
- Primary contact checkbox
- Relation dropdown

## Validation Rules

### Student Creation/Update
```php
first_name: required, string, max 100, letters only
middle_name: nullable, string, max 100, letters only
last_name: required, string, max 100, letters only
date_of_birth: required, date, before today
gender: required, in [male, female, other]
blood_group: nullable, regex (A|B|AB|O)[+-]
religion: nullable, string, max 50
category: required, in [general, obc, sc, st, vjnt, nt, ews]
mobile_number: nullable, regex, max 15
email: nullable, email, unique
current_address: nullable, string, max 500
permanent_address: nullable, string, max 500
program_id: required, exists in programs
division_id: required, exists in divisions
academic_session_id: required, exists in active sessions
academic_year: required, string, max 20
admission_date: required, date, before or equal today
photo: nullable, image, max 2MB
signature: nullable, image, max 2MB
cast_certificate: nullable, file, max 5MB
marksheet: nullable, file, max 5MB
student_status: required, in [active, graduated, dropped, suspended]
```

### Guardian Creation/Update
```php
first_name: required, string, max 100
last_name: required, string, max 100
relation: required, in [father, mother, guardian]
mobile_number: required, regex, max 15
email: nullable, email
occupation: nullable, string, max 100
annual_income: nullable, numeric
office_address: nullable, string, max 500
photo: nullable, image, max 2MB
is_primary_contact: boolean
```

## Business Rules

1. **Unique Admission Number**: Auto-generated, never duplicates
2. **Unique Roll Number**: Auto-generated per academic year
3. **Unique Email**: One email per student
4. **Division Capacity**: Cannot assign if division full
5. **Primary Guardian**: Only one primary contact per student
6. **Soft Delete**: Students never permanently deleted
7. **File Size Limits**: Photos 2MB, Documents 5MB
8. **Date Validation**: DOB must be before today, Admission date ≤ today

## Auto-Generation Logic

### Admission Number Format
```
{PROGRAM_CODE}{YEAR_SUFFIX}{SEQUENCE}
Example: BCO24001
- BCO: First 3 letters of program code (B.Com)
- 24: Last 2 digits of year or academic year
- 001: Sequential number (4 digits)
```

### Roll Number Format
```
{ACADEMIC_YEAR}-{SEQUENCE}
Example: FY-001
- FY: Academic year (First Year)
- 001: Sequential number (3 digits)
```

### Sequence Logic
- Queries last student in same program + academic year
- Extracts sequence number
- Increments by 1
- Checks for uniqueness
- Prevents overflow (max 9999)

## File Storage Structure

```
storage/
└── uploads/
    └── students/
        ├── photos/
        │   └── {hash}.jpg
        ├── signatures/
        │   └── {hash}.jpg
        └── documents/
            ├── {hash}.pdf (certificates)
            └── {hash}.pdf (marksheets)
```

## Integration Points

### With Programs Module
- Students belong to programs
- Program name displayed in profile
- Filter students by program

### With Divisions Module
- Students assigned to divisions
- Division capacity checked before assignment
- Division name displayed in profile

### With Academic Sessions Module
- Students belong to sessions
- Session name displayed in profile
- Filter by active sessions

### With Fee Management Module
- Student fees linked via student_id
- Fee status summary in profile
- Quick assign fees action

### With Attendance Module
- Attendance records linked via student_id
- Attendance summary in profile

### With Results Module
- Exam results linked via student_id
- Results summary in profile

### With Library Module
- Book issues linked via student_id
- Issued books summary in profile

## User Workflows

### Complete Admission Workflow
```
1. Admin → Students → Add Student
2. Fill Personal Details (name, DOB, gender, etc.)
3. Fill Contact Info (email, mobile, address)
4. Select Program (e.g., B.Com)
5. Select Division (e.g., FY-A)
6. System checks division capacity
7. Select Academic Session (e.g., 2024-25)
8. Select Academic Year (e.g., FY)
9. Upload Photo (optional)
10. Upload Signature (optional)
11. Upload Documents (optional)
12. Submit Form
13. System generates Admission # and Roll #
14. Student Created → Redirect to Profile
15. Add Guardian → Fill Guardian Details
16. Submit Guardian → Guardian Added
17. Add More Guardians (repeat 15-16)
18. Student Ready for Classes
```

### Edit Student Workflow
```
1. Students List → Find Student
2. Click Edit Button
3. Modify Required Fields
4. Change Division (capacity checked)
5. Replace Photo/Signature (optional)
6. Submit Form
7. Old files deleted (if replaced)
8. Student Updated → Redirect to Profile
```

### Guardian Management Workflow
```
1. View Student Profile
2. Guardians Section → Add Guardian
3. Fill Guardian Details
4. Upload Photo (optional)
5. Mark as Primary Contact (optional)
6. Submit → Guardian Added
7. To Edit: Click Edit on Guardian Card
8. To Delete: Click Delete (with confirmation)
```

## Testing Checklist

- [x] Create student with all fields
- [x] Create student with minimal fields
- [x] Auto-generate admission number
- [x] Auto-generate roll number
- [x] Validate email uniqueness
- [x] Check division capacity
- [x] Upload photo
- [x] Upload signature
- [x] Upload documents
- [x] Edit student details
- [x] Replace photo/signature
- [x] Change division
- [x] Add guardian
- [x] Edit guardian
- [x] Delete guardian
- [x] Set primary contact
- [x] Filter by program
- [x] Filter by academic year
- [x] Filter by status
- [x] Search students
- [x] View student profile
- [x] Soft delete student
- [x] Print student details
- [x] Print student list

## Status Summary

✅ **FULLY IMPLEMENTED** - All features from requirements are complete and functional

### Implemented Features:
1. ✅ Student Admission Process (4 steps)
2. ✅ Guardian/Parent Management
3. ✅ Document Management (Photo, Signature, Certificates)
4. ✅ Student Profile View (Comprehensive)
5. ✅ Student Listing (with filters and search)
6. ✅ Student Editing (with validation)
7. ✅ Student Deactivation (soft delete)
8. ✅ Auto-generation (Admission #, Roll #)
9. ✅ File Upload & Storage
10. ✅ Primary Contact Management
11. ✅ Division Capacity Check
12. ✅ Statistics Dashboard
13. ✅ Print Functionality

## Files Verified

### Controllers:
- ✅ `app/Http/Controllers/Web/StudentController.php`
- ✅ `app/Http/Controllers/Web/GuardianController.php`

### Models:
- ✅ `app/Models/User/Student.php`
- ✅ `app/Models/User/StudentGuardian.php`

### Views:
- ✅ `resources/views/dashboard/students/index.blade.php`
- ✅ `resources/views/dashboard/students/create.blade.php`
- ✅ `resources/views/dashboard/students/show.blade.php`
- ✅ `resources/views/dashboard/students/edit.blade.php`
- ✅ `resources/views/academic/guardians/create.blade.php`
- ✅ `resources/views/academic/guardians/edit.blade.php`

### Routes:
- ✅ All student routes configured in `routes/web.php`
- ✅ All guardian routes configured in `routes/web.php`

## Next Steps

The Student Management Module is complete. You can:
1. Access via sidebar: **Students** menu
2. Create new students with auto-generated numbers
3. Add guardians with primary contact
4. Upload photos and documents
5. Filter and search students
6. Edit student details
7. View comprehensive profiles

No additional implementation needed! 🎉
