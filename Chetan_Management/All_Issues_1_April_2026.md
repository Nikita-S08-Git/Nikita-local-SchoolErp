# All Issues - 1 April 2026

## Project Analysis Summary

This document contains all identified issues in the Nikita-local-SchoolErp project as of April 1, 2026. The project has multiple panels (Student, Accountant, Teacher, Admin/Principal) with various incomplete or broken functionalities.

---

## PART 1: UNNECESSARY FILES TO DELETE

The following files appear to be unnecessary, duplicate, or obsolete and should be considered for deletion:

### Redundant Documentation Files (Root Level)
1. `23_march_2026_plan.md` - Old planning document
2. `ACCOUNTANT_ROLE_IMPROVEMENT.md` - Implementation notes
3. `ADD_CLASS_BUTTON_UPDATE.md` - Completed task
4. `ADD_CLASS_MODAL_COMPLETE.md` - Completed task
5. `ADMISSION_MODAL_CREDENTIALS.md` - Previous implementation
6. `ALL_GITHUB_ISSUES_CREATED.md` - Can be regenerated
7. `ALL_PANELS_STATUS.md` - Status tracking
8. `ANALYSIS_AND_FIXES.md` - Old analysis
9. `ATTENDANCE_AUTH_FIX_IMPLEMENTATION.md` - Implemented
10. `ATTENDANCE_MARKING_GUIDE.md` - Reference guide
11. `ATTENDANCE_TIMETABLE_COMPLETE_VERIFICATION.md` - Verification done
12. `ATTENDANCE_TIMETABLE_CORRECTIONS.md` - Corrections done
13. `AUDIT_DASHBOARD.md` - Old audit
14. `AUDIT_EXECUTIVE_SUMMARY.md` - Old summary
15. `BRANCH_ANALYSIS_AND_RECOMMENDATION.md` - Analysis complete
16. `BRANCH_CLEANUP_GUIDE.md` - Cleanup guide
17. `COMPLETE_ANALYSIS.md` - Analysis complete
18. `COMPLETE_DATA_WITH_USERS_GUIDE.md` - Old guide
19. `COMPLETE_FIX_GUIDE.md` - Fixes complete
20. `COMPLETE_SETUP_GUIDE.md` - Setup complete
21. `COMPLETE_TIMETABLE_IMPLEMENTATION.md` - Implementation done
22. `COMPLETED_TASKS.md` - Task list
23. `COMPREHENSIVE_PROJECT_AUDIT_REPORT.md` - Old audit
24. `DASHBOARD_MODULES_REPORT.md` - Old report
25. `DATA_SEEDING_SUMMARY.md` - Summary done
26. `DATABASE_SETUP_INSTRUCTIONS.md` - Instructions done
27. `DOCUMENT_UPLOAD_AND_DIGILOCKER_ANALYSIS.md` - Analysis
28. `EXECUTIVE_SUMMARY.md` - Old summary
29. `EXPORT_PDF_BUTTON_FIX.md` - Fixes done
30. `FEATURE_DECISION_DOCUMENT.md` - Decision doc
31. `FEE_PAYMENT_MODAL_IMPROVEMENT.md` - Improvement done
32. `FORM_REQUEST_IMPLEVEMENT.md` - Implementation done
33. `GLOBAL_TIMETABLE_ENHANCEMENT.md` - Enhancement done
34. `HOLIDAY_SYSTEM_IMPLEVEMENT.md` - Implementation done
35. `IMPLEMENTATION_SUMMARY.md` - Old summary
36. `INDIAN_SCHOOL_SYSTEM_SETUP.md` - Setup done
37. `LOCAL_SETUP_GUIDE.md` - Old guide
38. `MEDIA_FILES_SECURITY_IMPLEVEMENT.md` - Implementation done
39. `OUTSTANDING_SCHOLARSHIP_DOCUMENTATION.md` - Documentation
40. `PHASE_4A_COMPLETE.md` - Phase complete
41. `PRINCIPAL_DASHBOARD_QUICK_ACTIONS_FIX.md` - Fixes done
42. `PRINCIPAL_DASHBOARD_README.md` - Old readme
43. `PRINCIPAL_DASHBOARD_ROUTE_FIX.md` - Fixes done
44. `PRINCIPAL_DASHBOARD_TIMETABLE.md` - Old documentation
45. `PRODUCTION_READY.md` - Old status
46. `PROGRAM_SESSION_REPORT.md` - Old report
47. `PROJECT_README.md` - Old readme
48. `project_status_14_March.md` - Old status
49. `REALITY_CHECK_ANALYSIS.md` - Analysis
50. `REMAINING_MODULES_INVESTIGATION.md` - Investigation done
51. `SCHOOL_SYSTEM_ANALYSIS.md` - Analysis done
52. `SEEDING_GUIDE.md` - Old guide
53. `SINGLE_COLLEGE_IMPLEMENTATION.md` - Implementation done
54. `STUDENT_DASHBOARD_COMPLETE.md` - Complete
55. `STUDENT_DASHBOARD_SETUP.md` - Setup done
56. `STUDENT_VIEWS_GUIDE.md` - Guide
57. `SYSTEM_OVERVIEW.md` - Old overview
58. `TEACHER_DASHBOARD_DOCUMENTATION.md` - Old doc
59. `TEACHER_DASHBOARD_FEATURES.md` - Features doc
60. `TEACHER_DASHBOARD_README.md` - Old readme
61. `TEACHER_PANEL_COMPLETE.md` - Complete
62. `TEACHER_STUDENT_PASSWORD_GENERATION.md` - Done
63. `TEST_L_CONNECTION_STATUS.md` - Test status
64. `TESTING_SECURITY_FIX.md` - Fixes done
65. `TIMETABLE_ACTION_BUTTONS_FIX.md` - Fixes done
66. `TIMETABLE_ATTENDANCE_HOLIDAY_MODULE.md` - Module done
67. `TIMETABLE_COMPLETE_FINAL.md` - Complete
68. `TIMETABLE_DATE_FUNCTIONALITY.md` - Functionality done
69. `TIMETABLE_FINAL_VERIFICATION.md` - Verification done
70. `TIMETABLE_GRID_VIEW_ONLY.md` - Implementation done
71. `TIMETABLE_QUICK_REFERENCE.md` - Reference
72. `TIMETABLE_SCROLL_FIX.md` - Fixes done
73. `TIMETABLE_STATUS_COMPLETE.md` - Status done
74. `UI_ENHANCEMENT_AND_CRUD_CHECK.md` - Enhancement done
75. `WHAT_I_DID.md` - History doc

### Duplicate/Backup Files
1. `$null` - Unknown/orphaned file
2. `-p/` - Partial/backup directory
3. `.qwen/` - AI artifacts directory
4. `Upload directories created/` - May be empty/temp

### SQL/ PHP Fix Files (Should be in migrations)
1. `add_admission_columns.php` - Should be migration
2. `check_schoolerp_db.php` - Debug file
3. `check_timetables.php` - Debug file
4. `fix_academic_session_id_column.sql` - Should be migration
5. `fix_attendance_column.php` - Should be migration
6. `fix_attendance.sql` - Should be migration
7. `fix_timetable_table.php` - Should be migration
8. `get_credentials.php` - Utility script
9. `NEW_ROUTES.php` - Routes reference
10. `run_migrations.php` - Manual migration
11. `seed_academic_rules.php` - Seeder
12. `seed_academic_rules.sql` - Seeder
13. `setup_test_data.php` - Test seeder
14. `trace_query.php` - Debug utility
15. `test_rate_limit.php` - Test utility

### Batch/PowerShell Scripts
1. `create-and-push.bat` - Git automation
2. `push-to-github-now.bat` - Git automation
3. `push-to-github.bat` - Git automation
4. `seed_attendance_timetable.bat` - Seeding
5. `seed_complete_timetable.bat` - Seeding
6. `setup_complete_system.bat` - Setup
7. `test_system.bat` - Testing
8. `setup.bat` - Setup scripts

---

## PART 2: STUDENT PANEL ISSUES

### Issue #1: Timetable Not Displaying on Student Login
**Description:** When a student logs into their dashboard, the timetable section does not display any data. The timetable component loads but shows empty content or an error.
**Impact:** Students cannot view their class schedule online.
**Priority:** High
**Status:** Not working

### Issue #2: Created Exams Not Displaying in Student Timetable
**Description:** Exams that have been created by administrators are not visible in the student timetable/exam schedule view.
**Impact:** Students miss important exam dates and schedule information.
**Priority:** High
**Status:** Not working

### Issue #3: Holidays Not Displaying in Student Timetable
**Description:** Holiday calendar entries are not being displayed on the student dashboard.
**Impact:** Students do not see upcoming holidays and may miss days or appear on working days.
**Priority:** Medium
**Status:** Not working

### Issue #4: Timetable Not Getting Printed to PDF
**Description:** The student timetable does not have a PDF export/print functionality, or the existing print feature is broken.
**Impact:** Students cannot take a printable copy of their schedule.
**Priority:** Medium
**Status:** Not working

### Issue #5: Dynamic Razorpay Integration for Multi-Tenant Setup
**Description:** Implement a dynamic Razorpay configuration section in the Admin panel, allowing each school or college to input their own Razorpay Key ID and Secret directly from the dashboard. This ensures multi-tenant compatibility where clients can enable online fee payments without requiring code changes or environment file updates for each deployment. The system must securely encrypt and utilize these stored credentials for processing all student fee transactions dynamically.
**Impact:** Without this, each new school deployment requires manual code configuration, preventing scalable SaaS-like usage and blocking online fee collection. 
Currently Students cannot pay fees online through Razorpay.
**Priority:** High
**Status:** Not implemented

### Issue #6: Mail Should Go to Student on Successful Registration
**Description:** No automated welcome email is sent to students upon successfulregistration.
**Impact:** Students do not receive confirmation or login credentials via email.
**Priority:** High
**Status:** Not working

### Issue #7: Division Should Auto-Assign to Student
**Description:** When admitting a student, the system should automatically assign them to a division. When one division's intake capacity is full, admissions should automatically store in the second division.
**Impact:** Manual division assignment is required; potential over-enrollment in single division.
**Priority:** High
**Status:** Not working

---

## PART 3: ACCOUNTANT PANEL ISSUES

### Issue #8: Accountant Login Not Works
**Description:** The accountant role login is broken or redirects to incorrect dashboard.
**Impact:** Accountants cannot access their panel to manage fees.
**Priority:** Critical
**Status:** Not working

---

## PART 4: ADMIN/PRINCIPAL PANEL ISSUES

### Issue #9: Books Not Getting Created
**Description:** Library book creation/form is non-functional or data is not saved.
**Impact:** Cannot add new books to the library system.
**Priority:** High
**Status:** Not working

### Issue #10: In Edit Attendance Form, Old Attendance Data Not Getting Stored
**Description:** When editing a previously marked attendance record, the old data is not pre-populated in the form.
**Impact:** Cannot easily modify previous attendance entries; need to re-enter all data.
**Priority:** Medium
**Status:** Not working

### Issue #11: Promotion, ATKT, Backlog Logic Pending
**Description:** Student promotion logic with ATKT (Allow to Keep Terms) and backlog/carryover handling is not implemented.
**Impact:** Cannot automatically promote students or handle academic progression properly.
**Priority:** High
**Status:** Not implemented

### Issue #12: Year Start Date and End Date Not Noting
**Description:** Academic year configuration (start date and end date) is not being saved or used properly.
**Impact:** Year-based filtering and reports are inaccurate.
**Priority:** Medium
**Status:** Not working

### Issue #13: Bulk Upload Functionality Needed
**Description:** Need bulk data upload for seed data/previous data migration (students, staff, marks, etc.).
**Impact:** Manual data entry required for all historical data.
**Priority:** High
**Status:** Not implemented

### Issue #14: System Configuration Dynamic Needed
**Description:** Institute details, contact information, logos, and favicons should be dynamic/configurable from admin panel.
**Impact:** Hard-coded values require code changes to update.
**Priority:** High
**Status:** Not implemented

### Issue #15: Route Not Works - /dashboard/teachers/36/edit
**Description:** Teacher edit route with specific ID (e.g., /dashboard/teachers/36/edit) returns 404 or error. Another division cannot be assigned to teacher.
**Impact:** Cannot edit existing teacher records or change their division assignment.
**Priority:** High
**Status:** Not working

### Issue #16: Staff Member CRUD - Date of Birth Field Has No Validation
**Description:** In staff member creation form, the date of birth field lacks validation (future dates, invalid ages allowed).
**Impact:** Invalid staff records can be created.
**Priority:** Low
**Status:** Not working

### Issue #17: Staff Member Edit Form Missing Fields
**Description:** When editing a staff member, only a few fields display compared to the staff member creation form.
**Impact:** Cannot update all staff member details; incomplete editing capability.
**Priority:** Medium
**Status:** Not working

### Issue #18: Staff Member Login Redirects to Teacher Panel
**Description:** When a staff member (non-teaching staff) is created and logs in, they are redirected to the teacher panel instead of staff panel.
**Impact:** Incorrect role-based routing for non-teaching staff.
**Priority:** High
**Status:** Not working

### Issue #19: Academic Rules Not Working Properly
**Description:** The academic rules configuration page (/academic/rules) is not functioning correctly.
**Impact:** Cannot configure academic policies and rules.
**Priority:** High
**Status:** Not working

### Issue #20: Admin Profile Not Working
**Description:** Admin/principal profile editing and viewing is broken.
**Impact:** Administrators cannot update their profile information.
**Priority:** Medium
**Status:** Not working

### Issue #21: Admin Assign Division "Y" to Teacher - Teacher Sees Division "A"
**Description:** When admin assigns division "Y" to a teacher, when that teacher logs in they see division "A" instead.
**Impact:** Teachers are assigned to wrong divisions; incorrect data access.
**Priority:** High
**Status:** Not working

---

## PART 5: FORGOT PASSWORD FUNCTIONALITY

### Issue #22: Forgot Password Functionality Status
**Description:** Need to verify if forgot password email reset functionality is working end-to-end.
**Status:** Routes exist in web.php; controller methods exist; mail driver configuration needed in .env
**Note:** This needs testing in production environment with proper mail configuration.

---

## PART 6: ADDITIONAL ISSUES DISCOVERED

### Issue #23: Student Attendance View Not Showing All Dates
**Description:** Student attendance history does not display all marked dates; some attendance records are missing from view.
**Impact:** Students cannot see their complete attendance record.
**Priority:** Medium

### Issue #24: Teacher Student List Pagination Broken
**Description:** When viewing students in teacher panel, pagination does not work correctly; page redirects or shows empty data.
**Impact:** Cannot navigate through large student lists.
**Priority:** Medium

### Issue #25: Fee Receipt PDF Not Generating
**Description:** After successful fee payment, the PDF receipt generation fails or returns blank.
**Impact:** Students/admins cannot download payment receipts.
**Priority:** High

### Issue #26: Division Capacity Not Enforced
**Description:** No enforcement of maximum intake capacity per division during admission.
**Impact:** Divisions can be over-admitted beyond capacity.
**Priority:** High

### Issue #27: Multi-Academic Session Not Supported
**Description:** System appears to only handle a single academic session; cannot manage multiple years of data.
**Impact:** Historical data conflicts when starting new academic year.
**Priority:** High

### Issue #28: Student Search/Filter Not Working
**Description:** Search and filter functionality in student lists returns no results or incorrect results.
**Impact:** Difficult to find specific students.
**Priority:** Medium

### Issue #29: Exam Schedule Conflict Not Detected
**Description:** When creating exam schedules, system does not detect scheduling conflicts (same time, same room).
**Impact:** Exam schedule conflicts go undetected.
**Priority:** Medium

### Issue #30: Library Book Issue/Return Not Tracking
**Description:** Library book issue and return transactions are not being tracked properly.
**Impact:** Cannot track book availability and overdue returns.
**Priority:** High

---

## SUMMARY

| Category | Count |
|----------|-------|
| Files identified for deletion | 75+ |
| Student Panel Issues | 7 |
| Accountant Panel Issues | 1 |
| Admin/Principal Panel Issues | 13 |
| Forgot Password Status | 1 |
| Additional Issues | 8 |
| **Total Issues** | **30** |

---

## RECOMMENDATIONS

1. **Priority 1 (Critical):** Fix Accountant login, Student timetable display, Razorpay integration
2. **Priority 2 (High):** Division auto-assignment with capacity, staff member role routing, promotion logic
3. **Priority 3 (Medium):** Forgot password testing, bulk upload, system configuration
4. **Priority 4 (Low):** Cleanup identified unnecessary files after fixes

---

*Document generated on: 1 April 2026*
*Project: Nikita-local-SchoolErp*


---

## Additional Issues Identified by AmazonQ

After reviewing the actual source code (controllers, models, routes, views), the following issues were found that are **not listed above**.

---

### AQ-1: Admin Controllers Folder is Completely Empty
**File:** `app/Http/Controllers/Admin/` (empty directory)
**Description:** The `routes/web.php` references several controllers under `App\Http\Controllers\Admin\` namespace — including `NotificationController`, `ProfileController`, `SettingsController`, and `FeeManagementController` — but the `Admin/` folder contains no files at all. All these routes will throw a fatal `Class not found` error when accessed.
**Affected Routes:**
- `/admin/notifications` (all CRUD)
- `/admin/profile` and `/admin/profile/edit`
- `/admin/settings`
- `/admin/fees` and all sub-routes
**Impact:** Entire admin notification, profile, settings, and fee management sections are completely broken.
**Priority:** Critical

---

### AQ-2: Duplicate Model Folder — `app/Models/Models/` Exists
**File:** `app/Models/Models/` directory
**Description:** There is a nested `Models/Models/` directory containing duplicate model files (`Department.php`, `Division.php`, `Program.php`, `Subject.php`, `FeePayment.php`, `FeeStructure.php`, `StaffProfile.php`, `Student.php`). These are duplicates of models already in the correct locations. This can cause namespace confusion and unexpected class resolution.
**Impact:** Potential wrong class being loaded; confusing for developers; dead code.
**Priority:** Medium

---

### AQ-3: Duplicate Subject Model — `app/Models/Result/Subject.php` Conflicts with `app/Models/Academic/Subject.php`
**File:** `app/Models/Result/Subject.php` vs `app/Models/Academic/Subject.php`
**Description:** Two `Subject` models exist in different namespaces. Controllers and relationships may resolve to the wrong one depending on import order, causing silent data issues.
**Impact:** Incorrect subject data may be loaded in exam/result views.
**Priority:** High

---

### AQ-4: `DashboardController::accountantProfile()` and `accountantChangePassword()` Methods Missing View
**File:** `app/Http/Controllers/Web/DashboardController.php`
**Description:** The `accountant()` method returns `view('dashboard.accountant')` which exists, but `accountantProfile()` and `accountantChangePassword()` methods are defined in routes (`/accountant/profile`, `/accountant/profile/change-password`) yet these methods do not exist in `DashboardController`. This will throw a `Method not found` error.
**Impact:** Accountant profile page crashes with 500 error.
**Priority:** High

---

### AQ-5: `DashboardController::hod_commerce()` Returns Non-Existent View
**File:** `app/Http/Controllers/Web/DashboardController.php`, line near bottom
**Description:** The `hod_commerce()` method returns `view('dashboard.hod_commerce')` but no such view file exists in `resources/views/dashboard/`. There is also no route registered for this method.
**Impact:** If accessed, throws a `View not found` exception.
**Priority:** Medium

---

### AQ-6: `LibrarianDashboardController::notifyAdminOverdueBooks()` Misuses `StudentNotification` for Admin Users
**File:** `app/Http/Controllers/LibrarianDashboardController.php`
**Description:** The `notifyAdminOverdueBooks()` method creates `StudentNotification` records using `student_id = $admin->id` (an admin user ID, not a student ID). `StudentNotification` is designed for students. This will either fail with a foreign key constraint error or silently store corrupt data.
**Impact:** Admin overdue book alerts are never delivered correctly; possible DB integrity error.
**Priority:** High

---

### AQ-7: `AttendanceController` Has Two Conflicting Route Groups for Same Prefix
**File:** `routes/web.php`
**Description:** There are two separate `Route::prefix('attendance')` groups defined — one at the top level (lines ~80-85) and another inside `Route::prefix('academic')` (lines ~170+). The top-level group has no auth middleware. This means attendance routes are accessible without login.
**Impact:** Security vulnerability — unauthenticated users can access attendance data.
**Priority:** Critical

---

### AQ-8: `principal` Route Group Has No Role Middleware
**File:** `routes/web.php`
**Description:** The `Route::prefix('dashboard/principal')` group only uses `['auth']` middleware, not `role:principal|admin`. Any authenticated user (teacher, student, accountant) can access principal dashboard routes.
**Impact:** Unauthorized role access to principal-only features.
**Priority:** High

---

### AQ-9: Test Routes Left in Production Code
**File:** `routes/web.php`
**Description:** Two test routes are registered in `web.php` that should never be in production:
- `GET /test-storage` — exposes student data (name, program, division) to any authenticated user
- `GET /test-add-holiday` — allows any authenticated user to delete all holidays for today and create a new one, then redirects to teacher panel

**Impact:** Data exposure and unauthorized data manipulation.
**Priority:** Critical

---

### AQ-10: `Student` Model Implements `Authenticatable` but Uses a Separate `users` Table for Login
**File:** `app/Models/User/Student.php`
**Description:** The `Student` model implements `Authenticatable` directly (used for `auth:student` guard), but passwords are stored on the related `User` model (`users` table), not on the `students` table. The `updatePassword()` method in `Student\DashboardController` correctly updates `$student->user->password`, but the `auth:student` guard authenticates against the `students` table which has no `password` column. This means the student guard configuration is likely pointing to the wrong table/model or the `students` table has a redundant/unused password column.
**Impact:** Student login may work inconsistently; password changes may not take effect on next login.
**Priority:** High

---

### AQ-11: `ResultController::studentResult()` Returns Non-Existent View
**File:** `app/Http/Controllers/Web/ResultController.php`
**Description:** The `studentResult()` method returns `view('results.student', ...)` but no `resources/views/results/student.blade.php` file exists. Only `results/generate.blade.php` and `results/index.blade.php` exist.
**Impact:** Accessing `/results/student/{student}` throws a `View not found` exception.
**Priority:** High

---

### AQ-12: `ExaminationController` Teacher Routes Reference Non-Existent View Directory
**File:** `app/Http/Controllers/Web/ExaminationController.php`
**Description:** `teacherExaminations()` returns `view('teacher.examinations.index', ...)` but there is no `resources/views/teacher/examinations/` directory or `index.blade.php` inside it.
**Impact:** Teacher examination list page crashes with `View not found`.
**Priority:** High

---

### AQ-13: `Razorpay` Webhook Has No CSRF Exemption Listed in `VerifyCsrfToken`
**File:** `routes/web.php` — `Route::post('/razorpay/webhook', ...)`
**Description:** The Razorpay webhook route is a POST route outside any auth middleware, but it is not excluded from CSRF verification. Razorpay's server cannot send a valid CSRF token, so all webhook calls will be rejected with a 419 error.
**Impact:** Razorpay payment events (captured, failed) are never processed; payment status never auto-updates.
**Priority:** High

---

### AQ-14: `AdmissionController::apply()` Validates `email` as Unique Against `students` Table but Student Emails Are Stored There
**File:** `app/Http/Controllers/Web/AdmissionController.php`
**Description:** The admission form validates `'email' => 'required|email|unique:students,email'`. However, the `students` table `email` column stores the student's contact email. If a student re-applies or a family member with the same email applies, the form will reject them. There is no `ignore` clause for edit scenarios either.
**Impact:** Legitimate re-applications or family members sharing an email are blocked.
**Priority:** Medium

---

### AQ-15: `AttendanceController::downloadExcel()` Generates CSV but Names It `.csv` — Misleading for Excel Users
**File:** `app/Http/Controllers/Web/AttendanceController.php`
**Description:** The `downloadExcel()` method generates a plain CSV file and returns it with `Content-Type: text/csv` and a `.csv` filename. The route is named `attendance.report.excel` and the UI likely labels it "Download Excel", misleading users who expect an actual `.xlsx` file. The `maatwebsite/excel` package is already installed in the project.
**Impact:** Users expecting Excel format receive a CSV; formatting and multi-sheet features are unavailable.
**Priority:** Low

---

### AQ-16: `Student` Model Has Two Conflicting `profile()` and `studentProfile()` Relationships Pointing to Same Model
**File:** `app/Models/User/Student.php`
**Description:** The `Student` model defines both `profile()` (returns `hasOne(StudentProfile::class)`) and `studentProfile()` (also returns `hasOne(StudentProfile::class)`). Both point to the same model. This is redundant and can cause confusion when eager loading — developers may use either name inconsistently across the codebase.
**Impact:** Code inconsistency; potential N+1 query issues if both are loaded.
**Priority:** Low

---

### AQ-17: No Middleware Protection on Library Routes — Any Authenticated User Can Delete Books
**File:** `routes/web.php`
**Description:** The library routes group (`Route::middleware(['auth'])->prefix('library')`) only requires authentication, with no role restriction. Any logged-in user (student, teacher, accountant) can access `DELETE /library/books/{book}` and delete library books.
**Impact:** Unauthorized book deletion by non-admin users.
**Priority:** High

---

### AQ-18: `AttendanceControllerFixed.php` is a Dead File
**File:** `app/Http/Controllers/Web/AttendanceControllerFixed.php`
**Description:** A file named `AttendanceControllerFixed.php` exists alongside `AttendanceController.php`. It is not referenced in any route or service provider. It appears to be a leftover from a fix attempt that was never cleaned up.
**Impact:** Dead code; confusing for developers; increases maintenance burden.
**Priority:** Low

---

*Section added by AmazonQ after code analysis — April 2026*
