# MVP Plan — School ERP
**Scope:** Admin, Principal, and Teacher panels only
**Excluded from MVP:** Student login, Accountant login, Librarian login (deferred to Phase 1 & 2)
**Goal:** Get a stable, working MVP as fast as possible with core modules functional

---

## How to Read This Document

Each task below is a **numbered step**. Complete them in order — later tasks depend on earlier ones.
Each task references the issue(s) it resolves from `All_Issues_1_April_2026.md`.

---

## STAGE 0 — Codebase Cleanup (Do This First, Before Any Feature Work)

These are blockers. If not fixed first, every other task will have unpredictable errors.

---

### Task 0.1 — Remove Test Routes from web.php
**Fixes:** AQ-9
**What to do:**
- Delete the `GET /test-storage` route from `routes/web.php`
- Delete the `GET /test-add-holiday` route from `routes/web.php`

**Why first:** These routes allow any logged-in user to delete holidays and expose student data. Must be gone before any testing.

---

### Task 0.2 — Fix Unauthenticated Attendance Route Group
**Fixes:** AQ-7
**What to do:**
- In `routes/web.php`, find the top-level `Route::prefix('attendance')` group (around lines 80–85) that has no auth middleware
- Either delete it entirely (the correct attendance routes already exist inside the `academic` prefix group with auth) or add `['auth']` middleware to it

**Why now:** Without this, attendance data is publicly accessible without login.

---

### Task 0.3 — Add Role Middleware to Principal Route Group
**Fixes:** AQ-8
**What to do:**
- In `routes/web.php`, find `Route::prefix('dashboard/principal')->middleware(['auth'])`
- Change middleware to `['auth', 'role:principal|admin']`

**Why now:** Any logged-in teacher or staff can currently access the principal dashboard.

---

### Task 0.4 — Delete Dead Files
**Fixes:** AQ-18, Part 1 of All_Issues file
**What to do:**
- Delete `app/Http/Controllers/Web/AttendanceControllerFixed.php`
- Delete the `app/Models/Models/` nested duplicate folder entirely
- Delete `public/fix_attendance.php` (debug file in public folder — security risk)

**Why now:** Duplicate models cause class resolution confusion in all subsequent tasks.

---

### Task 0.5 — Resolve Duplicate Subject Model
**Fixes:** AQ-3
**What to do:**
- Open `app/Models/Result/Subject.php` and `app/Models/Academic/Subject.php`
- Decide which one is the canonical model (likely `Academic/Subject.php`)
- Update all `use` statements in `ExaminationController`, `ResultController`, and any other file that imports `Result\Subject` to use `Academic\Subject` instead
- Delete `app/Models/Result/Subject.php`

**Why now:** Exam and result features (coming in later tasks) will silently use the wrong model if this is not fixed.

---

## STAGE 1 — Admin Login & Core Admin Panel

The admin panel is the foundation. Everything else (teachers, timetable, fees, admissions) is managed from here.

---

### Task 1.1 — Create Missing Admin Controllers
**Fixes:** AQ-1 (Critical — entire admin panel is broken)
**What to do:**
Create the following controller files inside `app/Http/Controllers/Admin/`:

1. `ProfileController.php` — methods: `index()`, `edit()`, `update()`, `editPassword()`, `updatePassword()`
2. `SettingsController.php` — methods: `index()`, `update()`, `system()`, `clearCache()`
3. `NotificationController.php` — full CRUD: `index()`, `create()`, `store()`, `show()`, `edit()`, `update()`, `destroy()`, `toggleActive()`
4. `FeeManagementController.php` — methods: `index()`, `structures()`, `createStructure()`, `storeStructure()`, `studentFees()`, `payments()`, `outstanding()`, `reports()`, `processPayment()`

Each controller should have basic working implementations — even simple ones that return a view. Views can be plain for now.

**Why now:** Without these, clicking any admin menu item crashes the app with a fatal error.

---

### Task 1.2 — Fix Admin Profile (View + Controller)
**Fixes:** Issue #20, AQ-1 (profile part)
**Depends on:** Task 1.1
**What to do:**
- Implement `ProfileController::index()` and `edit()` to load the authenticated admin user
- Create views: `resources/views/admin/profile/index.blade.php` and `edit.blade.php`
- Implement `update()` to save name and email
- Implement `editPassword()` and `updatePassword()` with current password verification

---

### Task 1.3 — Implement Dynamic System Configuration (Institute Settings)
**Fixes:** Issue #14
**Depends on:** Task 1.1, Task 1.2
**What to do:**
- Implement `SettingsController::index()` and `update()`
- Create a `settings` table (migration) with key-value pairs: `institute_name`, `institute_address`, `institute_phone`, `institute_email`, `institute_logo`, `institute_favicon`
- Create view: `resources/views/admin/settings/index.blade.php` with a form to update these values
- Store logo/favicon as file uploads in `storage/app/public/settings/`
- Make these values available globally via a service provider or config helper so layouts can use them

**Why now:** Institute name and logo appear on every page header, fee receipts, and PDF exports. Must be dynamic before any PDF work.

---

### Task 1.4 — Fix Academic Year Start/End Date Saving
**Fixes:** Issue #12
**Depends on:** Task 1.3
**What to do:**
- Check the `academic_years` table migration — confirm `start_date` and `end_date` columns exist
- Check `AcademicYear` model `$fillable` includes both fields
- Check the Academic Session create/edit form and controller to ensure both dates are submitted and saved
- Test by creating a new academic year and verifying dates are stored correctly

**Why now:** Academic year dates are used by attendance, timetable, and fee filtering. Wrong dates break all reports.

---

### Task 1.5 — Fix Multi-Academic Session Support
**Fixes:** Issue #27
**Depends on:** Task 1.4
**What to do:**
- Audit all queries in `AttendanceController`, `TimetableController`, `FeeStructureController` that may be hardcoding a single session
- Ensure all major listing queries filter by the currently active academic session (`is_active = true`)
- Add a "Set Active Session" button in the Academic Sessions list view so admin can switch the active year
- Ensure switching active session does not delete old data — only changes which session is shown by default

---

## STAGE 2 — Faculty (Teacher) Management

Teachers must be manageable before timetable and attendance can work correctly.

---

### Task 2.1 — Fix Teacher Edit Route
**Fixes:** Issue #15
**What to do:**
- In `routes/web.php`, the teacher resource route is `Route::resource('dashboard/teachers', TeacherController::class)->names('dashboard.teachers')`
- Test `GET /dashboard/teachers/{id}/edit` — if it returns 404, check that the route name and controller method exist
- In `TeacherController::edit()`, ensure it loads the teacher by ID and returns the edit view with all fields pre-populated
- Verify the form `action` uses `route('dashboard.teachers.update', $teacher->id)` with `@method('PUT')`

---

### Task 2.2 — Fix Teacher Division Assignment
**Fixes:** Issue #21
**Depends on:** Task 2.1
**What to do:**
- The bug: admin assigns division Y to teacher, but teacher sees division A
- Root cause is in `User::getAssignedDivisionAttribute()` — it first checks `teacher_assignments` table, then falls back to `divisions.class_teacher_id`
- Audit `TeacherController::update()` — confirm it saves to `teacher_assignments` table with `is_active = true` and deactivates old assignments
- Confirm the `teacher_assignments` table has an `is_active` column and old records are set to `false` when a new assignment is made
- Test: assign division Y to teacher → teacher logs in → teacher should see division Y

---

### Task 2.3 — Fix Staff Member Edit Form (Missing Fields)
**Fixes:** Issue #17
**Depends on:** Task 2.1
**What to do:**
- Open `resources/views/staff/edit.blade.php`
- Compare fields with `resources/views/staff/create.blade.php`
- Add all missing fields to the edit form
- Ensure `StaffController::update()` handles all those fields in validation and save

---

### Task 2.4 — Add Date of Birth Validation for Staff
**Fixes:** Issue #16
**Depends on:** Task 2.3
**What to do:**
- In `StaffController::store()` and `update()`, add validation rule for `date_of_birth`:
  `'date_of_birth' => 'nullable|date|before:today|after:1900-01-01'`

---

## STAGE 3 — Academic Structure (Programs, Subjects, Semesters, Academic Year)

This must be stable before timetable and attendance can work.

---

### Task 3.1 — Verify Programs / Courses CRUD Works End-to-End
**What to do:**
- Test create, edit, delete, and toggle-status for Programs at `/academic/programs`
- Ensure program name, duration, and type are saved correctly
- Fix any broken form submissions or missing validation

---

### Task 3.2 — Verify Subjects CRUD Works End-to-End
**What to do:**
- Test create, edit, delete for Subjects at `/academic/subjects`
- Ensure subject is linked to the correct program
- Fix any broken form submissions

---

### Task 3.3 — Verify Divisions CRUD Works End-to-End
**What to do:**
- Test create, edit, delete, toggle-status for Divisions at `/academic/divisions`
- Ensure division is linked to program and academic session
- Fix any broken form submissions

---

### Task 3.4 — Verify Academic Sessions CRUD Works End-to-End
**What to do:**
- Test create, edit, delete, toggle-status for Academic Sessions at `/academic/sessions`
- Confirm start_date and end_date are saved (related to Issue #12 / Task 1.4)
- Confirm only one session can be active at a time

---

## STAGE 4 — Admission Management (Admin Side)

Admission is the entry point for all student data. Must work before fee assignment or attendance.

---

### Task 4.1 — Fix Division Auto-Assignment with Capacity Check
**Fixes:** Issue #7, Issue #26
**Depends on:** Stage 3 complete
**What to do:**
- Add an `intake_capacity` column to the `divisions` table (migration) if it doesn't exist
- In `AdmissionService::createStudentFromAdmission()`, instead of using the submitted `division_id` directly:
  1. Find all active divisions for the selected program
  2. Check how many students are already in each division
  3. Auto-assign to the first division that has remaining capacity
  4. If all divisions are full, either reject admission or assign to the least-full division with a warning
- Update the admission form — the `division_id` field can be removed or made optional (auto-assigned)

---

### Task 4.2 — Fix Admission Email Uniqueness Validation
**Fixes:** AQ-14
**Depends on:** Task 4.1
**What to do:**
- In `AdmissionController::apply()`, change the email validation rule from `unique:students,email` to allow re-applications
- Option: validate uniqueness only against `pending` or `active` admissions, not all students
- Or: allow same email but generate a unique admission number regardless

---

### Task 4.3 — Implement Mail Sending on Successful Admission
**Fixes:** Issue #6
**Depends on:** Task 4.2
**What to do:**
- Create a Mailable class: `app/Mail/AdmissionConfirmation.php`
- The email should contain: student name, admission number, program, division, login URL, temporary password
- In `AdmissionController::apply()`, after the student is created, call `Mail::to($student->email)->send(new AdmissionConfirmation($student, $tempPassword))`
- Create the email blade view: `resources/views/emails/admission-confirmation.blade.php`
- Ensure `.env` has SMTP settings configured (`MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`)
- Test with a real email address

---

## STAGE 5 — Timetable Management

Timetable depends on teachers, divisions, subjects, and academic sessions all being correct.

---

### Task 5.1 — Verify Timetable CRUD Works (Admin/Principal)
**Depends on:** Stage 2, Stage 3 complete
**What to do:**
- Test creating a timetable entry at `/academic/timetable/create`
- Test the grid view at `/academic/timetable/grid`
- Test editing and deleting entries via the AJAX modal
- Confirm teacher, subject, division, day, and time slot are all saved correctly
- Fix any broken AJAX endpoints (`ajax/store`, `ajax/update`, `ajax/destroy`)

---

### Task 5.2 — Verify Holiday Management Works
**What to do:**
- Test creating, editing, deleting holidays at `/academic/holidays`
- Confirm holidays are linked to the correct academic year
- Confirm the toggle-status works
- Confirm the `checkDate` AJAX endpoint returns correct holiday info

---

### Task 5.3 — Verify Time Slot Management Works
**What to do:**
- Test creating, editing, deleting time slots at `/academic/time-slots`
- Confirm time slots appear in the timetable create/edit form dropdowns

---

## STAGE 6 — Attendance Management (Admin + Teacher)

Attendance depends on timetable being correct.

---

### Task 6.1 — Fix Attendance Edit Form (Pre-Population)
**Fixes:** Issue #10
**Depends on:** Task 5.1
**What to do:**
- In `AttendanceController::edit()`, the existing attendance records are already fetched as `$existingAttendance` (keyed by `student_id`)
- Open `resources/views/academic/attendance/edit.blade.php`
- Ensure each student's radio button (present/absent/late) is pre-selected using `$existingAttendance[$student->id] ?? null`
- Test: mark attendance for a division → go to edit → old values should be pre-selected

---

### Task 6.2 — Verify Teacher Attendance Marking Works
**Depends on:** Task 6.1
**What to do:**
- Log in as a teacher
- Go to `/teacher/attendance`
- Confirm today's timetable entries are listed
- Click a timetable entry → mark attendance for students
- Confirm records are saved to the `attendance` table
- Test editing a previously marked attendance record

---

### Task 6.3 — Verify Attendance Report and PDF Export
**What to do:**
- Test the attendance report at `/academic/attendance/report`
- Select a division and date range → confirm data loads
- Test PDF download (`/academic/attendance/report/download`)
- Fix any PDF generation errors (check DomPDF is configured correctly)

---

## STAGE 7 — Fee Management (Admin Side)

Fee management depends on students being admitted and academic structure being set up.

---

### Task 7.1 — Verify Fee Heads CRUD Works
**What to do:**
- Test create, edit, delete fee heads at `/fees/fee-heads`
- Test the AJAX store/update/delete endpoints
- Confirm fee heads appear in fee structure dropdowns

---

### Task 7.2 — Verify Fee Structures CRUD Works
**What to do:**
- Test create, edit, delete fee structures at `/fees/structures`
- Confirm fee structure is linked to program, academic session, and fee head
- Confirm installment count and amount are saved correctly

---

### Task 7.3 — Verify Fee Assignment to Students Works
**What to do:**
- Test assigning a fee structure to students at `/fees/assignments`
- Confirm `student_fees` records are created with correct `total_amount`, `final_amount`, `outstanding_amount`

---

### Task 7.4 — Fix Fee Receipt PDF Generation
**Fixes:** Issue #25
**Depends on:** Task 7.3
**What to do:**
- After recording a payment, test the receipt page at `/fees/payments/{id}/receipt`
- Test PDF download at `/fees/payments/{id}/download`
- Open `resources/views/pdf/fee-receipt.blade.php` — ensure it uses the dynamic institute name/logo from Task 1.3
- Fix any DomPDF rendering errors (missing fonts, broken image paths)
- Confirm receipt shows: student name, admission number, fee head, amount paid, payment mode, receipt number, date

---

### Task 7.5 — Implement Razorpay Webhook CSRF Exemption
**Fixes:** AQ-13
**Depends on:** Task 7.3
**What to do:**
- Open `app/Http/Middleware/VerifyCsrfToken.php`
- Add `'razorpay/webhook'` to the `$except` array
- Test by simulating a Razorpay webhook call — confirm it returns 200 instead of 419

---

### Task 7.6 — Implement Dynamic Razorpay Configuration in Admin Settings
**Fixes:** Issue #5
**Depends on:** Task 1.3, Task 7.5
**What to do:**
- Add `razorpay_key` and `razorpay_secret` fields to the settings table (from Task 1.3)
- Add these fields to the Admin Settings form
- In `RazorpayController`, instead of reading from `config('services.razorpay.key')`, read from the settings table
- Encrypt the secret key before storing (use Laravel's `encrypt()` / `decrypt()`)
- Test: save Razorpay credentials in admin settings → make a test payment → confirm it uses the saved credentials

---

## STAGE 8 — Examination & Results (Admin + Teacher)

---

### Task 8.1 — Create Missing Teacher Examinations View
**Fixes:** AQ-12
**What to do:**
- Create directory: `resources/views/teacher/examinations/`
- Create file: `resources/views/teacher/examinations/index.blade.php`
- It should list active examinations with links to enter marks
- Use the teacher layout (`resources/views/layouts/teacher.blade.php`)

---

### Task 8.2 — Verify Examination CRUD Works (Admin)
**Depends on:** Task 8.1
**What to do:**
- Test create, edit, delete examinations at `/examinations`
- Confirm exam is linked to a subject, has start/end dates, and type (midterm/final/unit_test/practical)
- Fix any broken form submissions

---

### Task 8.3 — Verify Marks Entry Works (Admin + Teacher)
**Depends on:** Task 8.2
**What to do:**
- Test marks entry at `/examinations/{id}/marks-entry?division_id=X`
- Confirm students load for the selected division
- Enter marks and save — confirm `student_marks` records are created
- Test the auto-save draft feature (AJAX)

---

### Task 8.4 — Create Missing Student Result View
**Fixes:** AQ-11
**What to do:**
- Create file: `resources/views/results/student.blade.php`
- It should show a student's marks across all examinations and subjects
- Include total marks, percentage, and grade

---

### Task 8.5 — Verify Result Generation and PDF Export
**Depends on:** Task 8.3, Task 8.4
**What to do:**
- Test result generation at `/results/generate` — select exam and division
- Confirm results table shows all students with marks, percentage, grade, pass/fail
- Test PDF export at `/results/pdf`
- Fix any DomPDF rendering errors

---

## STAGE 9 — Promotion, ATKT & Backlog Logic (Admin/Principal)

Promotion depends on examination marks being entered and results being generated. This stage must come after Stage 8.

---

### Task 9.1 — Verify Academic Rules Configuration Works
**Fixes:** Issue #19
**Depends on:** Stage 8 complete
**What to do:**
- Test the academic rules page at `/academic/rules`
- Confirm admin can set: minimum attendance percentage required for promotion, pass percentage, ATKT subject limit (max subjects a student can have ATKT in and still be promoted)
- Fix any broken form submissions or missing validation in `AcademicRuleController`
- Confirm rules are saved to the `academic_rules` table and retrieved correctly by `AcademicRuleService::getPassPercentage()`

**Why now:** Promotion eligibility checks read directly from these rules. Rules must be correct before promotion logic is tested.

---

### Task 9.2 — Verify Promotion Eligibility Display Works
**Fixes:** Issue #11 (part 1 — display)
**Depends on:** Task 9.1, Task 8.3 (marks must be entered)
**What to do:**
- Go to `/academic/promotions` as admin or principal
- Select a session, program, and division
- Confirm the list shows students with their result status, attendance percentage, backlog count, and promotion type (Normal / ATKT / Fail)
- Confirm eligible students are correctly identified based on academic rules set in Task 9.1
- Fix any errors in `PromotionController::index()` or `PromotionService::getEligibleStudents()`

---

### Task 9.3 — Verify Individual Student Promotion Works
**Fixes:** Issue #11 (part 2 — individual promotion)
**Depends on:** Task 9.2
**What to do:**
- From the promotions list, promote a single student using the individual promote button
- Confirm `PromotionController::promoteStudent()` creates a `promotion_logs` record
- Confirm the student's `division_id`, `academic_session_id`, and `academic_year` are updated to the new session values
- Confirm a `StudentAcademicRecord` is created for the new session
- Test promoting a student as ATKT (has backlogs but still eligible) — confirm `promotion_type` is saved as `atkt`
- Test that a student marked as Fail cannot be promoted without an override reason

---

### Task 9.4 — Verify Bulk Promotion Works
**Fixes:** Issue #11 (part 3 — bulk promotion)
**Depends on:** Task 9.3
**What to do:**
- From the promotions list, select multiple eligible students and use the bulk promote action
- Confirm `PromotionController::promote()` processes all selected students
- Confirm all selected students get `promotion_logs` records and updated academic records
- Confirm failed students in the selection are skipped or flagged, not silently promoted
- Check the success message shows correct count: "X students promoted, Y failed"

---

### Task 9.5 — Verify Promotion History and Rollback Works
**Fixes:** Issue #11 (part 4 — history and rollback)
**Depends on:** Task 9.4
**What to do:**
- Go to `/academic/promotions/history`
- Confirm all past promotions are listed with student name, from session, to session, promotion type, and promoted by
- Test the rollback button on a recent promotion
- Confirm rollback restores the student's previous `division_id`, `academic_session_id`, and `academic_year`
- Confirm the `promotion_logs` record is marked as rolled back
- Confirm rollback is not allowed if the student has already been promoted again in a later session

---

## STAGE 10 — Final Checks Before MVP Sign-Off

---

### Task 10.1 — Verify Admin/Principal Role Separation
**What to do:**
- Log in as admin → confirm access to all admin routes
- Log in as principal → confirm access to principal routes, no access to super-admin-only routes
- Log in as teacher → confirm NO access to `/dashboard/principal` or `/admin/*` routes

---

### Task 10.2 — Verify Forgot Password Flow Works
**Fixes:** Issue #22
**What to do:**
- Confirm `.env` has valid SMTP settings
- Go to `/forgot-password` → enter admin email → confirm reset email arrives
- Click reset link → set new password → confirm login works with new password
- Test for both admin and teacher users

---

### Task 10.3 — Smoke Test All Core Flows
Run through each flow end-to-end and confirm no crashes:

1. Admin logs in → updates institute settings → creates academic year → creates program → creates division → creates subject
2. Admin creates teacher → assigns division to teacher
3. Admin creates time slots → creates timetable entries for a division
4. Admin creates holidays
5. Admin creates fee head → creates fee structure → assigns fee to students
6. Admin records a fee payment → downloads receipt PDF
7. Admin creates examination → enters marks for a division → generates result PDF
8. Teacher logs in → sees correct division → marks attendance → views attendance history
9. Teacher views examination list → enters marks
10. Admin sets academic rules → views promotion list → promotes eligible students → verifies promotion history

---

## Issue Reference Map

| Task | Issue(s) Resolved |
|------|-------------------|
| 0.1 | AQ-9 |
| 0.2 | AQ-7 |
| 0.3 | AQ-8 |
| 0.4 | AQ-18, Part 1 cleanup |
| 0.5 | AQ-3 |
| 1.1 | AQ-1 |
| 1.2 | Issue #20 |
| 1.3 | Issue #14 |
| 1.4 | Issue #12 |
| 1.5 | Issue #27 |
| 2.1 | Issue #15 |
| 2.2 | Issue #21 |
| 2.3 | Issue #17 |
| 2.4 | Issue #16 |
| 3.1–3.4 | Foundation for all other modules |
| 4.1 | Issue #7, Issue #26 |
| 4.2 | AQ-14 |
| 4.3 | Issue #6 |
| 5.1–5.3 | Timetable stability |
| 6.1 | Issue #10 |
| 6.2–6.3 | Attendance stability |
| 7.1–7.3 | Fee management stability |
| 7.4 | Issue #25 |
| 7.5 | AQ-13 |
| 7.6 | Issue #5 |
| 8.1 | AQ-12 |
| 8.2–8.3 | Examination stability |
| 8.4 | AQ-11 |
| 8.5 | Result PDF stability |
| 9.1 | Issue #19 |
| 9.2 | Issue #11 (display) |
| 9.3 | Issue #11 (individual promotion) |
| 9.4 | Issue #11 (bulk promotion) |
| 9.5 | Issue #11 (history & rollback) |
| 10.2 | Issue #22 |

---

## Issues Deferred to Phase 1 & Phase 2 (Not in MVP)

The following issues are intentionally excluded from this MVP. They will be addressed in separate Phase 1 and Phase 2 planning documents.

- Issue #8 — Accountant login (excluded: accountant panel out of MVP scope)
- Issue #13 — Bulk upload functionality (deferred)
- Issue #18 — Staff member login routing (deferred)
- Issue #23 — Student attendance view (excluded: student panel out of MVP scope)
- Issue #24 — Teacher student list pagination (deferred to Phase 1)
- Issue #28 — Student search/filter (deferred)
- Issue #29 — Exam schedule conflict detection (deferred)
- Issue #30 — Library book tracking (excluded: librarian panel out of MVP scope)
- AQ-2 — Duplicate Models/Models folder (low risk, deferred cleanup)
- AQ-4 — Accountant profile methods missing (excluded: accountant out of MVP scope)
- AQ-5 — hod_commerce view missing (deferred)
- AQ-6 — Librarian notification misuse (excluded: librarian out of MVP scope)
- AQ-10 — Student auth guard inconsistency (excluded: student login out of MVP scope)
- AQ-15 — CSV vs Excel download (low priority, deferred)
- AQ-16 — Duplicate Student profile relationships (low priority, deferred)
- AQ-17 — Library route missing role middleware (excluded: librarian out of MVP scope)
- Issue #9 — Books not getting created (excluded: library out of MVP scope)

---

## MVP Time Estimate — 1 Developer (Average Experience)

Assumptions:
- One developer working full-time (8 hours/day)
- Average experience — knows Laravel, can debug independently, but is not a senior
- SMTP credentials are already available and configured ✅
- Razorpay test credentials are already available ✅
- No time for writing tests, only manual testing
- Buffer of ~15% added for unexpected blockers and re-work

| Stage | Tasks | Estimated Days |
|-------|-------|----------------|
| Stage 0 — Cleanup & Security Fixes | 0.1 to 0.5 | 1 day |
| Stage 1 — Admin Panel & Settings | 1.1 to 1.5 | 3 days |
| Stage 2 — Faculty Management | 2.1 to 2.4 | 1.5 days |
| Stage 3 — Academic Structure Verification | 3.1 to 3.4 | 1 day |
| Stage 4 — Admission Management + Mail | 4.1 to 4.3 | 1.5 days |
| Stage 5 — Timetable Management | 5.1 to 5.3 | 1.5 days |
| Stage 6 — Attendance Management | 6.1 to 6.3 | 1.5 days |
| Stage 7 — Fee Management + Razorpay | 7.1 to 7.6 | 2 days |
| Stage 8 — Examination & Results | 8.1 to 8.5 | 2 days |
| Stage 9 — Promotion / ATKT / Backlog | 9.1 to 9.5 | 2.5 days |
| Stage 10 — Final Checks & Smoke Testing | 10.1 to 10.3 | 1 day |
| **Total** | | **18.5 days** |

**Minimum realistic estimate: 19 working days (approximately 4 weeks)**

Where the 5 days were saved compared to original 24-day estimate:
- Stage 4 (Mail): saved 1 day — no time lost setting up and debugging SMTP config
- Stage 7 (Razorpay): saved 1 day — no time lost on test account setup, webhook URL registration, and credential debugging
- Stage 2, 3, 6: saved 0.5 days each — overall confidence is higher when environment is pre-configured and working

Remaining risk areas (things that can still add days):
- Stage 1 is still the biggest chunk — Admin controllers are completely missing and need to be built from scratch
- Stage 9 (Promotion logic) is complex business logic — if edge cases in ATKT/backlog rules are unclear, it can spill into an extra day
- If any major database schema mismatch is found during Stage 3 verification, add 1–2 extra days

---

*MVP Plan created: April 2026*
*Next documents to create: Phase 1 Plan, Phase 2 Plan*
