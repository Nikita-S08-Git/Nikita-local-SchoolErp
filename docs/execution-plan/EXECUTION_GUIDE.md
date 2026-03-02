# School ERP - Master Execution Guide

**Document Version:** 1.0  
**Last Updated:** March 2, 2026  
**Audience:** Developers, Technical Leads, Project Managers

---

## SECTION 1 — PROJECT OVERVIEW

### Project Information

| Attribute | Value |
|-----------|-------|
| **Project Name** | School ERP |
| **Repository** | Nikita-S08-Git/Nikita-local-SchoolErp |
| **Framework** | Laravel 12.x |
| **PHP Version** | 8.2+ |
| **Total Issues** | 46 |
| **Documentation Files** | 47 |

### Priority Classification

| Priority | Label | Count | Description |
|----------|-------|-------|-------------|
| P0 | 🔴 Critical | 10 | System breakers, data integrity, core flow blockers |
| P1 | 🟠 High | 13 | Operational blockers, workflows incomplete |
| P2 | 🟡 Medium | 13 | Usability gaps, UX improvements |
| P3 | 🟢 Low | 10 | Enhancements, nice to have |

### Module Classification

| Module | Label | Color | Description |
|--------|-------|-------|-------------|
| Core System | `core` | Blue | Core infrastructure, services, configuration |
| Academic Engine | `academic` | Purple | Student lifecycle, examinations, results |
| UI/UX Frontend | `ui` | Green | Views, components, dashboards |
| Financial & Payment | `finance` | Gold | Fee management, payments, refunds |
| Infrastructure & Config | `infra` | Teal | DevOps, backup, deployment |

### Purpose of This Document

This guide provides developers with:
1. **Navigation** - Where to find documentation
2. **Structure** - How GitHub issues and tasks are organized
3. **Sequence** - What to work on and in what order
4. **Rules** - Coding standards, testing requirements, workflow
5. **Criteria** - Definition of Done and production readiness

---

## SECTION 2 — WHERE DOCUMENTATION IS STORED

### Folder Structure

```
/docs/
├── execution-plan/
│   ├── README.md                    # Project overview and statistics
│   ├── EXECUTION_GUIDE.md           # This document
│   ├── P0-critical/                 # 10 critical tasks
│   │   ├── P0-01-create-missing-admission-service.md
│   │   ├── P0-02-create-missing-api-response.md
│   │   └── ...
│   ├── P1-high/                     # 13 high-priority tasks
│   │   ├── P1-01-build-promotion-web-ui.md
│   │   ├── P1-02-build-transfer-certificate-web-ui.md
│   │   └── ...
│   ├── P2-medium/                   # 13 medium-priority tasks
│   │   ├── P2-01-add-column-sorting-tables.md
│   │   └── ...
│   └── P3-low/                      # 10 low-priority tasks
│       ├── P3-01-implement-email-notification-system.md
│       └── ...
├── architecture/
│   ├── database-schema.md
│   ├── folder-structure.md
│   └── technology-stack.md
├── api/
│   ├── endpoints.md
│   └── authentication.md
└── user-guides/
    ├── admin-guide.md
    ├── teacher-guide.md
    └── student-guide.md
```

### File Naming Convention

```
P{priority}-{id}-{task-name}.md

Examples:
P0-01-create-missing-admission-service.md
P1-03-implement-consolidated-marksheet.md
P2-08-add-internal-external-marks-split.md
```

### Task Document Structure

Each markdown file contains:
- **Objective** - What needs to be achieved
- **Problem Statement** - Why this task is needed
- **Expected Outcome** - What success looks like
- **Scope of Work** - Specific work items
- **Files to Modify** - Exact file paths to create/modify
- **Dependencies** - Tasks that must be completed first
- **Acceptance Criteria** - Checklist for completion
- **Developer Notes** - Implementation hints and examples

---

## SECTION 3 — HOW GITHUB ISSUES ARE STRUCTURED

### Issue Naming Format

```
[P{priority}-{id}] - Task Name

Examples:
[P0-01] - Create Missing AdmissionService Class
[P1-03] - Implement Consolidated Marksheet Generator
[P2-08] - Add Internal/External Marks Split
```

### Label System

Each issue has **three labels**:

1. **Priority Label** (required)
   - `P0` - Critical (10 issues)
   - `P1` - High (13 issues)
   - `P2` - Medium (13 issues)
   - `P3` - Low (10 issues)

2. **Severity Label** (required)
   - `S1` - Data loss / Security / System crash
   - `S2` - Workflow blocked
   - `S3` - Poor usability
   - `S4` - Cosmetic

3. **Module Label** (required)
   - `core` - Core System
   - `academic` - Academic Engine
   - `ui` - UI/UX Frontend
   - `finance` - Financial & Payment
   - `infra` - Infrastructure & Config

### Issue-to-Document Mapping

| GitHub Issue | Documentation File |
|--------------|-------------------|
| `#1` - [P0-01] | `/docs/execution-plan/P0-critical/P0-01-*.md` |
| `#2` - [P0-02] | `/docs/execution-plan/P0-critical/P0-02-*.md` |
| `#13` - [P1-03] | `/docs/execution-plan/P1-high/P1-03-*.md` |

### Issue Closure Rules

An issue can be **closed** only when:
- [ ] All acceptance criteria checkboxes are checked
- [ ] Code is merged to main branch
- [ ] Documentation updated if needed
- [ ] No related bugs introduced

---

## SECTION 4 — WHAT TO WORK ON FIRST (EXECUTION ORDER)

### ⚠️ STRICT EXECUTION ORDER

Tasks **MUST** be completed in the following order. Do not skip phases.

---

### PHASE 1 — Stability Fixes (ALL P0)

**Complete ALL tasks in this phase before moving to Phase 2.**

| Order | Issue | Task | Module |
|-------|-------|------|--------|
| 1 | P0-01 | Create Missing AdmissionService Class | core |
| 2 | P0-02 | Create Missing ApiResponse Class | core |
| 3 | P0-03 | Consolidate Duplicate Attendance Models | core |
| 4 | P0-05 | Fix Attendance Schema Mismatch | core |
| 5 | P0-04 | Consolidate Duplicate Timetable Models | core |
| 6 | P0-06 | Fix Timetable day_of_week Case Mismatch | core |
| 7 | P0-07 | Implement Cascade Delete Protection | core |
| 8 | P0-08 | Merge Teacher_M Branch to Main | core |
| 9 | P0-09 | Replace Hardcoded Pass Percentage | academic |
| 10 | P0-10 | Fix Broken Dashboard Quick Action Links | ui |

**Phase 1 Exit Criteria:**
- System does not crash on core operations
- No missing class errors
- Model namespace conflicts resolved
- Main branch has all features merged

---

### PHASE 2 — Academic Engine Core

**Complete in this order:**

| Order | Issue | Task | Module | Dependencies |
|-------|-------|------|--------|--------------|
| 1 | P0-09 | Replace Hardcoded Pass Percentage | academic | P0-01 |
| 2 | P1-05 | Implement Backlog Subject Tracking | academic | None |
| 3 | P1-01 | Build Promotion Web UI | academic | P0-09 |
| 4 | P1-02 | Build Transfer Certificate Web UI | academic | None |
| 5 | P1-04 | Create ATKT Exam Registration Workflow | academic | P1-05 |
| 6 | P2-08 | Add Internal/External Marks Split | academic | P0-09 |
| 7 | P1-03 | Implement Consolidated Marksheet Generator | academic | P0-09, P2-08 |
| 8 | P1-09 | Create Admin Panel for Academic Rules | academic | P0-09 |

**Phase 2 Exit Criteria:**
- Promotion workflow functional via web UI
- Transfer certificate generation works
- ATKT registration workflow complete
- Marksheet PDF generation works
- Academic rules configurable via admin panel

---

### PHASE 3 — White Label System

**Complete in this order:**

| Order | Issue | Task | Module | Dependencies |
|-------|-------|------|--------|--------------|
| 1 | P1-11 | Implement System Settings Module | core | None |
| 2 | P1-12 | Replace .env Runtime Dependency | core | P1-11 |
| 3 | P1-13 | Create Default Installation Seeder | core | P1-11 |
| 4 | P2-11 | Add SMTP Configuration UI | core | P1-11 |
| 5 | P2-12 | Add Payment Gateway Configuration UI | finance | P1-11 |
| 6 | P2-13 | Implement Branding Configuration | ui | P1-11 |

**Phase 3 Exit Criteria:**
- Settings module functional
- Configuration loaded from database
- SMTP configurable from admin panel
- Payment gateway configurable
- Branding (logo, colors, name) configurable

---

### PHASE 4 — UI/UX Improvements

**Complete in this order:**

| Order | Issue | Task | Module |
|-------|-------|------|--------|
| 1 | P1-06 | Replace Hardcoded Dashboard Data | ui |
| 2 | P1-10 | Split Student Admission Form into Wizard | ui |
| 3 | P2-01 | Add Column Sorting to Data Tables | ui |
| 4 | P2-02 | Add Bulk Actions to Student List | ui |
| 5 | P2-03 | Implement Auto-Save for Marks Entry | ui |
| 6 | P2-04 | Add Excel Export to All Tables | ui |
| 7 | P2-05 | Add Search to Dropdown Selects | ui |
| 8 | P2-06 | Build Merit List and Ranking System | academic |
| 9 | P2-07 | Implement Subject-Wise Pass Criteria | academic |
| 10 | P2-10 | Add Dynamic Max Marks in Marks Entry | ui |
| 11 | P2-09 | Build Subject-Teacher Allocation UI | core |

**Phase 4 Exit Criteria:**
- All dashboards show real data
- Forms are user-friendly
- Tables have sorting, export, bulk actions
- Data entry has auto-save
- Search works in dropdowns

---

### PHASE 5 — Enhancements (ALL P3)

**Can be done in any order:**

| Issue | Task | Module |
|-------|------|--------|
| P3-01 | Implement Email Notification System | core |
| P3-02 | Build Installation Wizard | core |
| P3-03 | Add Dark Mode Toggle | ui |
| P3-04 | Add Keyboard Shortcuts | ui |
| P3-05 | Add Report Preview Before Download | ui |
| P3-06 | Implement Scheduled Report Generation | academic |
| P3-07 | Add Grace Marks Configuration | academic |
| P3-08 | Add Payment Gateway Test Mode | finance |
| P3-09 | Implement Backup and Restore Feature | infra |
| P3-10 | Add Touch-Friendly Button Sizes | ui |

**Phase 5 Exit Criteria:**
- All enhancements complete (optional for MVP)

---

## SECTION 5 — PARALLEL EXECUTION RULES

### Execution Groups

Tasks within the same group **can** be worked on in parallel by different developers.

#### Group A: Backend Core Fixes
- P0-01 (AdmissionService)
- P0-02 (ApiResponse)
- P0-07 (Cascade Delete)
- P1-08 (Fee Refund)
- P1-11 (System Settings)

**Constraint:** Must complete before any UI that depends on these services.

#### Group B: UI/UX Frontend
- P0-10 (Dashboard Links)
- P1-06 (Dashboard Data)
- P1-10 (Admission Wizard)
- P2-01 through P2-05 (Table improvements)
- P2-10 (Dynamic Max Marks)
- P2-13 (Branding)
- P3-03 through P3-05 (Enhancements)

**Constraint:** Requires backend APIs to be stable.

#### Group C: Academic Engine
- P0-09 (Pass Percentage)
- P1-01 (Promotion UI)
- P1-02 (TC UI)
- P1-03 (Marksheet)
- P1-04 (ATKT Registration)
- P1-05 (Backlog Tracking)
- P1-09 (Academic Rules)
- P2-06 through P2-08 (Academic features)
- P3-06 through P3-07 (Academic enhancements)

**Constraint:** P0-09 must be completed first. P1-05 before P1-04. P2-08 before P1-03.

#### Group D: Finance & Payment
- P1-08 (Fee Refund)
- P2-12 (Payment Gateway Config)
- P3-08 (Test Mode)

**Constraint:** P1-11 (Settings) must exist first.

#### Group E: Infrastructure & Config
- P0-08 (Branch Merge)
- P1-12 (Config Loader)
- P1-13 (Installation Seeder)
- P2-11 (SMTP Config)
- P3-01 (Email Notifications)
- P3-02 (Installation Wizard)
- P3-09 (Backup/Restore)

**Constraint:** P1-11 must be completed before P1-12, P1-13, P2-11.

---

### Dependency Rules

| Rule | Description |
|------|-------------|
| **Rule 1** | Model/schema changes (P0-03 to P0-06) must be done before any UI using those models |
| **Rule 2** | Academic rules (P0-09) must be implemented before marksheet (P1-03) |
| **Rule 3** | Settings module (P1-11) must exist before SMTP (P2-11), Payment (P2-12), Branding (P2-13) |
| **Rule 4** | Backlog tracking (P1-05) must exist before ATKT registration (P1-04) |
| **Rule 5** | Internal/External split (P2-08) must exist before Marksheet (P1-03) |

---

## SECTION 6 — DEVELOPER WORKFLOW

### Standard Development Workflow

Follow this workflow for **every task**:

```
1. PICK ISSUE
   └─ Go to GitHub Projects → School ERP Execution
   └─ Filter by P0 → P1 → P2 → P3 (in order)
   └─ Assign issue to yourself

2. READ DOCUMENTATION
   └─ Open corresponding markdown file in /docs/execution-plan/
   └─ Read: Objective, Scope, Files to Modify, Dependencies
   └─ Verify dependencies are complete

3. CREATE FEATURE BRANCH
   └─ git checkout main
   └─ git pull origin main
   └─ git checkout -b feature/P1-03-marksheet
   
   Branch naming: feature/{issue-id}-{short-name}
   Examples:
     feature/P0-01-admission-service
     feature/P1-06-dashboard-data
     feature/P2-08-internal-external

4. IMPLEMENT
   └─ Follow Scope of Work in markdown file
   └─ Modify files listed in "Files to Modify"
   └─ Write tests if required (see Section 8)

5. TEST LOCALLY
   └─ Run feature in local environment
   └─ Test happy path
   └─ Test validation errors
   └─ Test edge cases
   └─ Verify no console errors
   └─ Verify no SQL errors

6. VERIFY ACCEPTANCE CRITERIA
   └─ Open markdown file
   └─ Check each acceptance criteria checkbox
   └─ All must be checked before proceeding

7. COMMIT
   └─ git add .
   └─ git commit -m "feat: [P1-03] Implement consolidated marksheet generator
   
   - Create ResultService for marksheet generation
   - Design marksheet PDF template
   - Implement GPA/CGPA calculation
   - Add student photo and signatures
   
   Closes #13"
   
   Commit format:
   feat: [P{priority}-{id}] Task name
   
   - Bullet list of changes
   - Closes #{issue-number}

8. CREATE PULL REQUEST
   └─ git push origin feature/P1-03-marksheet
   └─ Go to GitHub → Pull Requests → New Pull Request
   └─ Title: [P1-03] Implement Consolidated Marksheet Generator
   └─ Description:
       - What was changed
       - How to test
       - Screenshots (if UI)
   └─ Link issue: Closes #13
   └─ Request review from tech lead

9. CODE REVIEW
   └─ Address review comments
   └─ Make requested changes
   └─ Re-request review

10. MERGE & CLOSE
    └─ Tech lead approves PR
    └─ Merge to main branch
    └─ Delete feature branch
    └─ Close GitHub issue
    └─ Update task status in project board
```

---

## SECTION 7 — CODING STANDARDS

### Backend Standards (Laravel)

#### Service Layer
```php
// ✅ CORRECT: Business logic in service
class PromotionService
{
    public function checkEligibility(StudentAcademicRecord $record): array
    {
        // Business logic here
    }
}

// ❌ WRONG: Business logic in controller
class PromotionController extends Controller
{
    public function promote($id)
    {
        // Don't put business logic here
        if ($student->backlog_count > 3) { // ❌
            // ...
        }
    }
}
```

#### Repository Pattern
```php
// ✅ Use for complex queries
interface StudentRepositoryInterface
{
    public function getEligibleForPromotion(int $sessionId);
}

// ✅ Use in service
class PromotionService
{
    public function __construct(
        private StudentRepositoryInterface $students
    ) {}
    
    public function getEligibleStudents()
    {
        return $this->students->getEligibleForPromotion($sessionId);
    }
}
```

#### Controllers
```php
// ✅ CORRECT: Thin controllers
class StudentController extends Controller
{
    public function store(StoreStudentRequest $request)
    {
        $student = $this->studentService->create($request->validated());
        return redirect()->route('students.show', $student);
    }
}

// ❌ WRONG: Fat controllers
class StudentController extends Controller
{
    public function store(Request $request)
    {
        // 100 lines of business logic ❌
        // Database queries ❌
        // Validation logic ❌
    }
}
```

#### Validation
```php
// ✅ Use Form Requests
class StoreStudentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'email' => 'required|email|unique:students,email',
        ];
    }
}

// ❌ Don't validate in controller
public function store(Request $request)
{
    $request->validate([...]); // ❌
}
```

---

### Frontend Standards (Blade)

#### Components
```blade
{{-- ✅ CORRECT: Reusable component --}}
{{-- resources/views/components/stat-card.blade.php --}}
@props(['title', 'value', 'icon', 'color' => 'primary'])

<div class="card bg-{{ $color }} text-white">
    <div class="card-body">
        <h5>{{ $title }}</h5>
        <h3>{{ $value }}</h3>
    </div>
</div>

{{-- Usage --}}
<x-stat-card title="Total Students" :value="$totalStudents" icon="people" />
```

#### Avoid Inline Scripts
```blade
{{-- ❌ WRONG: Inline script for major logic --}}
<script>
    function submitForm() {
        // 50 lines of JavaScript
    }
</script>

{{-- ✅ CORRECT: External JS file --}}
<script src="{{ asset('js/student-form.js') }}"></script>
```

#### Consistent Layout
```blade
{{-- ✅ Use layout inheritance --}}
@extends('layouts.app')

@section('title', 'Students')

@section('content')
    <div class="container-fluid">
        <!-- Content -->
    </div>
@endsection
```

---

### Database Standards

#### Migrations
```php
// ✅ CORRECT: Always create new migrations
Schema::table('students', function (Blueprint $table) {
    $table->string('new_column')->nullable();
});

// ❌ NEVER modify existing migrations in production
// Don't change 2024_01_02_000001_create_students_table.php
```

#### Foreign Keys
```php
// ✅ Always define foreign keys
$table->foreignId('program_id')->constrained('programs');

// ✅ Define cascade behavior
$table->foreignId('user_id')->constrained('users')->onDelete('cascade');
```

#### Indexes
```php
// ✅ Add indexes for frequently queried columns
$table->index(['program_id', 'academic_year']);
$table->index(['division_id', 'student_status']);
```

---

## SECTION 8 — TESTING RULES

### Testing Requirements by Priority

| Priority | Testing Requirement |
|----------|---------------------|
| P0 | Unit test OR detailed manual test checklist |
| P1 | Unit test OR detailed manual test checklist |
| P2 | Manual test checklist |
| P3 | Basic functional testing |

### Test Coverage Areas

Every task must be tested for:

#### 1. Happy Path
```
Given: Valid data
When: Action is performed
Then: Expected result occurs
```

#### 2. Validation Errors
```
Given: Invalid data
When: Action is performed
Then: Appropriate error message shown
```

#### 3. Edge Cases
```
Given: Boundary conditions
When: Action is performed
Then: System handles gracefully
```

#### 4. Data Integrity
```
Given: Related records exist
When: Parent record is modified
Then: Child records remain consistent
```

### Example Test Checklist

**Task: P1-03 - Consolidated Marksheet Generator**

```markdown
## Test Checklist

### Happy Path
- [ ] Generate marksheet for student with all subjects passed
- [ ] PDF downloads successfully
- [ ] All subjects displayed correctly
- [ ] GPA calculated correctly
- [ ] CGPA calculated correctly

### Validation Errors
- [ ] Error shown if student has no marks
- [ ] Error shown if examination not approved

### Edge Cases
- [ ] Handles student with failed subjects
- [ ] Handles student with ATKT status
- [ ] Handles missing student photo gracefully

### Data Integrity
- [ ] Marks displayed match database
- [ ] Grade displayed matches grade table
- [ ] Credits sum correctly
```

---

## SECTION 9 — DEFINITION OF DONE

A task is **DONE** only when **ALL** criteria are met:

### Code Quality
- [ ] All acceptance criteria completed
- [ ] Code follows coding standards (Section 7)
- [ ] No console errors in browser
- [ ] No SQL errors in logs
- [ ] No PHP warnings or notices

### Functionality
- [ ] Feature works end-to-end
- [ ] Happy path tested
- [ ] Error cases handled
- [ ] Edge cases considered

### Review
- [ ] Pull request created
- [ ] Code reviewed by tech lead
- [ ] Review comments addressed
- [ ] PR approved

### Documentation
- [ ] Markdown file updated (if scope changed)
- [ ] API docs updated (if API changed)
- [ ] User guide updated (if user-facing)

### Deployment
- [ ] Merged to main branch
- [ ] GitHub issue closed
- [ ] Project board updated

---

## SECTION 10 — PRODUCTION READINESS CRITERIA

The School ERP system is **READY FOR CLIENT DEPLOYMENT** only when:

### Mandatory (Must Have)

| Criteria | Verification |
|----------|--------------|
| ✅ All P0 tasks completed | GitHub filter: `label:P0 is:closed` |
| ✅ All P1 tasks completed | GitHub filter: `label:P1 is:closed` |
| ✅ White-label system working | Settings, SMTP, Payment, Branding configurable |
| ✅ Dashboard uses real data | No hardcoded values in any dashboard |
| ✅ Marksheet generation works | PDF generates with all fields |
| ✅ ATKT workflow works | Registration → Hall ticket → Exam → Result |
| ✅ Promotion works | Bulk promotion with preview |

### Recommended (Should Have)

| Criteria | Verification |
|----------|--------------|
| ✅ P2 tasks completed | GitHub filter: `label:P2 is:closed` |
| ✅ Email notifications working | Admission, fee due, result emails sent |
| ✅ Backup/restore tested | Manual backup and restore successful |
| ✅ Installation wizard works | Fresh install completes without errors |

### Optional (Nice to Have)

| Criteria | Verification |
|----------|--------------|
| ✅ P3 tasks completed | GitHub filter: `label:P3 is:closed` |
| ✅ Dark mode available | Toggle works in all views |
| ✅ Keyboard shortcuts | Documented shortcuts work |

---

## SECTION 11 — FINAL NOTE FOR DEVELOPERS

### About This ERP

The School ERP system is designed for:

1. **Colleges** - Undergraduate and postgraduate programs
2. **Universities** - Multi-department, multi-program institutions
3. **White-label Deployment** - Configurable for different institutions

### Critical Success Factors

| Factor | Why It Matters |
|--------|----------------|
| **Code Quality** | This is a multi-tenant system. Bugs affect all clients. |
| **Configurability** | Each institution has different rules. Hardcoding is forbidden. |
| **Data Integrity** | Student records are permanent. Cascade deletes must be handled. |
| **Performance** | Large institutions have 10,000+ students. Queries must be optimized. |
| **Security** | Student data is sensitive. Authorization is mandatory. |

### Questions?

- **Technical Questions** → Tag tech lead in issue comments
- **Requirements Questions** → Refer to task markdown file
- **Workflow Questions** → Refer to this document

### Remember

> "This ERP will be used by real students, teachers, and administrators.
> Every line of code matters. Every bug affects someone's education.
> Write code you can be proud of."

---

**END OF EXECUTION GUIDE**
