# SchoolERP Dashboard Modules - Complete Report

## Overview

This document provides a comprehensive overview of all dashboard modules available in the SchoolERP system, organized by user role: **Principal**, **Admin**, and **Teacher**.

---

## 1. PRINCIPAL DASHBOARD

**Route:** `/dashboard/principal`  
**Controller:** [`PrincipalDashboardController.php`](app/Http/Controllers/Web/PrincipalDashboardController.php)  
**View:** `dashboard.principal`

### 1.1 Statistics Overview

| Module | Metrics Displayed |
|--------|------------------|
| **Students** | Total active students count |
| **Teachers** | Total teachers count (users with 'teacher' role) |
| **Classes/Divisions** | Active divisions count |
| **Programs** | Active programs count |
| **Departments** | Total departments count |
| **Examinations** | Total examinations count |
| **Subjects** | Total subjects count |

### 1.2 Today's Attendance

- Present count
- Absent count  
- Total marked
- Attendance percentage

### 1.3 Fee Management

| Metric | Description |
|--------|-------------|
| **Monthly Collection** | Total fees collected in current month |
| **Transactions** | Number of payment transactions |
| **Pending Fees** | Total outstanding amount |

### 1.4 Timetable Management

**Features:**
- View all timetables by division
- Quick timetable entry creation from dashboard
- Edit existing timetable entries
- Delete timetable entries
- Holiday validation on date selection
- Conflict detection (division and teacher)

**Route:** `/dashboard/principal/timetable`

### 1.5 Teacher Assignment

**Features:**
- Assign divisions to teachers
- Set assignment type (division/subject)
- Mark as primary class teacher
- Activate/deactivate assignments
- Remove assignments

**Route:** POST `/dashboard/principal/assign-division`

### 1.6 Results Management

**Features:**
- View all student results
- Filter by division and examination
- Display marks for all students

**Route:** `/principal/results`

### 1.7 Recent Activities

- Recent fee payments (last 3)
- Recent student admissions (last 2)
- Activity timeline with icons

---

## 2. ADMIN DASHBOARD

**Route:** `/dashboard/admin`  
**Controller:** Uses same [`PrincipalDashboardController.php`](app/Http/Controllers/Web/PrincipalDashboardController.php) (shared with Principal)

> **Note:** Admin and Principal share the same dashboard controller. Role-based access control determines what each can see/edit.

### 2.1 Academic Management

**Base Route:** `/academic`

| Module | Routes | Features |
|--------|--------|----------|
| **Programs** | `/academic/programs` | Create, edit, toggle status |
| **Subjects** | `/academic/subjects` | CRUD operations |
| **Divisions** | `/academic/divisions` | Create classes, assign students |
| **Academic Sessions** | `/academic/sessions` | Manage sessions |
| **Academic Rules** | `/academic/rules` | Define academic policies |

### 2.2 Attendance Management

**Route:** `/academic/attendance`

| Feature | Description |
|---------|-------------|
| **Mark Attendance** | Create attendance records |
| **Edit Attendance** | Modify existing records |
| **Attendance Report** | View/download reports |
| **Holiday Check** | Validate dates against holidays |
| **Export** | Download as PDF/Excel |

### 2.3 Timetable Management

**Route:** `/academic/timetable`

| View | Route | Description |
|------|-------|-------------|
| **Table View** | `/academic/timetable/table` | Weekly schedule |
| **Grid View** | `/academic/timetable/grid` | Visual grid |
| **Create** | `/academic/timetable/create` | Add new entry |
| **Teacher Timetable** | `/academic/timetable/teacher` | Teacher's schedule |

**Features:**
- Conflict detection
- Holiday validation
- AJAX operations
- Import/Export (Excel, PDF)
- Copy to next session

### 2.4 Time Slots

**Route:** `/academic/time-slots`

- Create time slots
- Set start/end times
- Mark as break time

### 2.5 Holidays

**Route:** `/academic/holidays`

- Add holidays
- Set date ranges
- Toggle status

### 2.6 Leaves

**Route:** `/academic/leaves`

- View all leave requests
- Approve/reject leaves
- Staff leave management

### 2.7 Student Management

**Route:** `/dashboard/students`

| Feature | Description |
|---------|-------------|
| **List** | View all students |
| **Create** | Add new student |
| **Edit** | Modify student details |
| **Delete** | Remove student |
| **Bulk Action** | Multiple operations |
| **Guardians** | Manage parent/guardian info |

### 2.8 Fee Management

**Route:** `/fees`

| Module | Routes | Features |
|--------|--------|----------|
| **Fee Structures** | `/fees/structures` | Define fee heads |
| **Assignments** | `/fees/assignments` | Assign fees to students |
| **Payments** | `/fees/payments` | Record payments |
| **Outstanding** | `/fees/outstanding` | View pending fees |
| **Scholarships** | `/fees/scholarships` | Manage scholarships |

### 2.9 Examination Management

**Route:** `/examinations`

| Feature | Description |
|---------|-------------|
| **Create Exam** | Set up examinations |
| **Marks Entry** | Enter student marks |
| **Auto-save** | Draft marks feature |
| **Results** | Generate student results |

### 2.10 Library Management

**Route:** `/library`

| Module | Features |
|--------|----------|
| **Books** | Add, edit, delete books |
| **Issues** | Issue books to students |
| **Returns** | Process returns |
| **Overdue** | Track overdue books |

### 2.11 Staff Management

**Route:** `/staff`

- List all staff
- Add new staff
- Edit staff details
- View staff profiles

### 2.12 Reports

**Route:** `/reports`

- Attendance reports
- PDF export
- Excel export

### 2.13 Results

**Route:** `/results`

- Generate results
- View student results
- Export PDF

### 2.14 User Management

**Routes:**
- `/dashboard/teachers` - Teacher management
- `/admin/users` - All users
- `/admin/roles` - Role management
- `/admin/permissions` - Permission management

### 2.15 Admission Management

**Route:** `/admissions`

- View applications
- Verify admissions
- Enroll students

---

## 3. TEACHER DASHBOARD

**Routes:** 
- `/teacher/dashboard` (Primary)
- `/dashboard/teacher` (Alternative)

**Controllers:**
- [`Teacher/DashboardController.php`](app/Http/Controllers/Teacher/DashboardController.php)
- [`Web/TeacherDashboardController.php`](app/Http/Controllers/Web/TeacherDashboardController.php)

### 3.1 Dashboard Home

**Statistics:**
- Total students (from assigned divisions)
- Number of subjects taught
- Today's attendance marked count
- This month's attendance statistics

### 3.2 Today's Schedule

| Type | Description |
|------|-------------|
| **Weekly Schedule** | Recurring classes based on day of week |
| **Date-Specific** | Classes for specific dates |
| **Combined View** | Both merged |

### 3.3 Profile Management

**Route:** `/teacher/profile`

| Feature | Description |
|---------|-------------|
| **View Profile** | See teacher details |
| **Edit Profile** | Update information |
| **Photo Upload** | Profile picture |
| **Qualifications** | Education & experience |

### 3.4 Division Management

**Route:** `/teacher/divisions`

- View assigned divisions
- See class teacher status
- View student count per division
- Today's holiday status

### 3.5 Student Management

**Route:** `/teacher/students`

| Feature | Description |
|---------|-------------|
| **List Students** | View assigned division students |
| **Search** | Find by name/roll number |
| **Student Details** | View individual profiles |
| **Attendance History** | Past attendance records |
| **Marks/Results** | View student exam marks |

### 3.6 Attendance Management

**Routes:**
- `/teacher/attendance` - View attendance
- `/academic/attendance/mark` - Mark attendance

| Feature | Description |
|---------|-------------|
| **Mark Attendance** | Record daily attendance |
| **View Report** | Attendance by date range |
| **Division Filter** | Filter by class |
| **Date Selection** | Pick any date |

**Important:** Attendance can ONLY be marked for timetables with status = "active" (today's date).

### 3.7 Marks Entry

**Route:** `/examinations/{exam}/marks-entry`

- Enter marks for examinations
- Auto-save drafts
- Subject-wise entry

### 3.8 Timetable View

**Route:** `/academic/timetable/teacher`

- View personal schedule
- Weekly view
- Date-specific classes

---

## 4. OTHER DASHBOARDS

### 4.1 Student Dashboard

**Route:** `/student/dashboard`

**Features:**
- View own attendance
- View timetable
- Check fees
- View results
- Library books
- Notifications

### 4.2 Accounts Staff Dashboard

**Route:** `/dashboard/accounts_staff`

**Features:**
- Total fees collected
- Monthly collection
- Pending fees count
- Student count

### 4.3 Librarian Dashboard

**Route:** `/dashboard/librarian`

**Features:**
- Total books
- Issued books
- Available books
- Overdue books
- Recent issues

---

## 5. ROLE-BASED ACCESS MATRIX

| Module | Principal | Admin | Teacher | Staff | Student |
|--------|-----------|-------|---------|-------|---------|
| **Dashboard** | ✅ | ✅ | ✅ | ✅ | ✅ |
| **All Students** | ✅ | ✅ | Own Division | ❌ | Own |
| **All Teachers** | ✅ | ✅ | ❌ | ❌ | ❌ |
| **Fee Collection** | ✅ | ✅ | ❌ | ✅ | ❌ |
| **Timetable (All)** | ✅ | ✅ | Own | ❌ | Own |
| **Timetable (Edit)** | ✅ | ✅ | ❌ | ❌ | ❌ |
| **Attendance (All)** | ✅ | ✅ | Own Division | ❌ | Own |
| **Attendance (Mark)** | ✅ | ✅ | ✅ | ❌ | ❌ |
| **Examinations** | ✅ | ✅ | ✅ | ❌ | ❌ |
| **Results** | ✅ | ✅ | Own Division | ❌ | Own |
| **Library** | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Promotions** | ✅ | ✅ | ❌ | ❌ | ❌ |
| **Reports** | ✅ | ✅ | ✅ | ✅ | ❌ |

---

## 6. KEY FEATURES SUMMARY

### 6.1 Timetable Status System

| Status | Condition | Attendance |
|--------|-----------|------------|
| **Closed** | Date < Today | ❌ Not allowed |
| **Active** | Date = Today | ✅ Allowed |
| **Upcoming** | Date > Today | ❌ Not allowed |

### 6.2 Automatic Features

1. **Status Calculation:** Timetable status automatically computed based on date
2. **Attendance Validation:** Blocks marking attendance for non-active timetables
3. **Conflict Detection:** Prevents double-booking teachers/divisions
4. **Holiday Validation:** Prevents timetable/attendance on holidays

### 6.3 Export Options

- PDF reports
- Excel/CSV exports
- Print-friendly views

---

## 7. ROUTES QUICK REFERENCE

### Principal Routes
```
GET  /dashboard/principal
POST /dashboard/principal/assign-division
POST /dashboard/principal/timetable/store
GET  /dashboard/principal/timetable
GET  /principal/results
```

### Admin Routes
```
GET  /dashboard/admin
GET  /academic/programs
GET  /academic/subjects
GET  /academic/divisions
GET  /academic/attendance
GET  /academic/timetable
GET  /dashboard/students
GET  /fees/structures
GET  /examinations
GET  /library/books
```

### Teacher Routes
```
GET  /teacher/dashboard
GET  /teacher/profile
GET  /teacher/divisions
GET  /teacher/students
GET  /teacher/attendance
POST /teacher/attendance/store
GET  /academic/timetable/teacher
```

---

## 8. FILES STRUCTURE

### Controllers
```
app/Http/Controllers/
├── Web/
│   ├── PrincipalDashboardController.php
│   ├── DashboardController.php
│   ├── TeacherDashboardController.php
│   ├── StudentController.php
│   ├── TimetableController.php
│   ├── AttendanceController.php
│   ├── FeePaymentController.php
│   └── ...
└── Teacher/
    ├── DashboardController.php
    ├── AttendanceController.php
    └── ...
```

### Views
```
resources/views/
├── dashboard/
│   ├── principal.blade.php
│   ├── admin.blade.php
│   ├── teacher.blade.php
│   └── ...
└── teacher/
    ├── dashboard.blade.php
    ├── divisions/
    ├── students/
    └── attendance/
```

---

*Generated on: March 2026*
*SchoolERP Version: Laravel Based*
