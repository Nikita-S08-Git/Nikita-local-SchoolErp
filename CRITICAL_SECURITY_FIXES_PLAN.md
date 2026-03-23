# Critical Security Fixes - Implementation Plan

**Generated:** 14 March 2026  
**Priority:** P0 CRITICAL  
**Estimated Timeline:** 5-7 days  
**Risk Level:** HIGH (Public exposure of sensitive documents)

---

## Executive Summary

### Current Security Vulnerabilities

| Issue | Severity | Impact | Files Affected |
|-------|----------|--------|----------------|
| **Documents in Public Storage** | 🔴 CRITICAL | Anyone can access caste certificates, marksheets via direct URL | `StudentController.php` |
| **No Download Authentication** | 🔴 CRITICAL | No login required to view sensitive documents | N/A (missing feature) |
| **Missing Document Fields** | 🟠 HIGH | Cannot store Aadhaar, Income, Domicile certificates | Migration, Model |
| **Incomplete Role Management** | 🟠 HIGH | No UI to manage roles/permissions | Missing controllers |
| **Empty Permission Middleware** | 🔴 CRITICAL | Permission checks don't work | `CheckPermission.php` |

---

## Part 1: Document Storage Security Fix

### 1.1 Current Vulnerable Code

**File:** `app/Http/Controllers/Web/StudentController.php`

#### Vulnerable Lines (128-160):

```php
public function store(Request $request)
{
    $validated = $request->validate([...]);

    // ❌ VULNERABLE: Stored in public folder
    $validated['photo_path'] = $request->file('photo')->store(
        'uploads/students/photos',
        'public'  // ❌ ANYONE CAN ACCESS VIA /storage URL
    );

    // ❌ VULNERABLE: Stored in public folder
    $validated['signature_path'] = $request->file('signature')->store(
        'uploads/students/signatures',
        'public'  // ❌ PUBLICLY ACCESSIBLE
    );

    // 🔴 CRITICAL: Sensitive document in public folder
    $validated['cast_certificate_path'] = $request->file('cast_certificate')->store(
        'uploads/students/documents',
        'public'  // 🔴 CRITICAL: CASTE CERTIFICATE EXPOSED!
    );

    // 🔴 CRITICAL: Sensitive document in public folder
    $validated['marksheet_path'] = $request->file('marksheet')->store(
        'uploads/students/documents',
        'public'  // 🔴 CRITICAL: MARKSHEET EXPOSED!
    );

    $student = Student::create($validated);
    // ...
}
```

#### Same Issue in `update()` Method (Lines 220-250):

```php
public function update(Request $request, Student $student)
{
    // ... validation ...

    // ❌ Same vulnerability in update
    if ($request->hasFile('photo')) {
        $validated['photo_path'] = $request->file('photo')->store(
            'uploads/students/photos',
            'public'  // ❌ STILL PUBLIC
        );
    }

    if ($request->hasFile('signature')) {
        $validated['signature_path'] = $request->file('signature')->store(
            'uploads/students/signatures',
            'public'  // ❌ STILL PUBLIC
        );
    }
}
```

---

### 1.2 Files Requiring Changes

#### Backend Files (5 files):

| File | Changes | Lines Affected |
|------|---------|----------------|
| `app/Http/Controllers/Web/StudentController.php` | Change 'public' to 'private' in store() and update() | ~10 lines |
| `app/Http/Controllers/Web/GuardianController.php` | Change 'public' to 'private' | ~3 lines |
| `app/Http/Controllers/Web/TeacherController.php` | Change 'public' to 'private' | ~2 lines |
| `app/Http/Controllers/Web/ProfileController.php` | Change 'public' to 'private' | ~1 line |
| `app/Http/Controllers/Web/ScholarshipApplicationController.php` | Change 'public' to 'private' | ~1 line |

#### New Files to Create (4 files):

| File | Purpose |
|------|---------|
| `app/Http/Controllers/Web/DocumentDownloadController.php` | Authenticated document download |
| `app/Policies/StudentDocumentPolicy.php` | Document access authorization |
| `routes/documents.php` | Document download routes |
| `resources/views/documents/download-error.blade.php` | Error page for document access |

---

### 1.3 Implementation Steps

#### Step 1: Fix StudentController (30 minutes)

**Changes Required:**

```php
// CHANGE THIS (Line 134):
$validated['photo_path'] = $request->file('photo')->store(
    'uploads/students/photos',
    'public'  // ❌
);

// TO THIS:
$validated['photo_path'] = $request->file('photo')->store(
    'uploads/students/photos',
    'private'  // ✅
);

// CHANGE THIS (Line 148):
$validated['cast_certificate_path'] = $request->file('cast_certificate')->store(
    'uploads/students/documents',
    'public'  // ❌
);

// TO THIS:
$validated['cast_certificate_path'] = $request->file('cast_certificate')->store(
    'uploads/students/documents',
    'private'  // ✅
);

// CHANGE THIS (Line 155):
$validated['marksheet_path'] = $request->file('marksheet')->store(
    'uploads/students/documents',
    'public'  // ❌
);

// TO THIS:
$validated['marksheet_path'] = $request->file('marksheet')->store(
    'uploads/students/documents',
    'private'  // ✅
);
```

**Same changes in `update()` method (Lines 233, 244)**

---

#### Step 2: Fix Other Controllers (15 minutes)

**GuardianController.php (Line 32):**
```php
// CHANGE:
'public'  // ❌
// TO:
'private'  // ✅
```

**TeacherController.php (Lines 51, 92):**
```php
// CHANGE:
->store('teachers', 'public')  // ❌
// TO:
->store('teachers', 'private')  // ✅
```

**ProfileController.php (Line 38):**
```php
// CHANGE:
->store('profiles', 'public')  // ❌
// TO:
->store('profiles', 'private')  // ✅
```

**ScholarshipApplicationController.php (Line 39):**
```php
// CHANGE:
->store('scholarships', 'public')  // ❌
// TO:
->store('scholarships', 'private')  // ✅
```

---

#### Step 3: Create DocumentDownloadController (1 hour)

**File:** `app/Http/Controllers/Web/DocumentDownloadController.php`

**Purpose:** Authenticated document download with access control

**Methods Required:**

```php
class DocumentDownloadController extends Controller
{
    /**
     * Download student document (authenticated)
     * 
     * Access Rules:
     * - Student can download own documents
     * - Parents can download their child's documents
     * - Staff can download based on role permissions
     */
    public function downloadStudentDocument(Student $student, string $type)
    {
        // 1. Verify authentication
        // 2. Check authorization (policy)
        // 3. Verify document exists
        // 4. Log download
        // 5. Return file
    }

    /**
     * Download guardian document (authenticated)
     */
    public function downloadGuardianDocument(StudentGuardian $guardian)
    {
        // Similar flow
    }

    /**
     * Download teacher document (authenticated)
     */
    public function downloadTeacherDocument(User $teacher)
    {
        // Similar flow
    }
}
```

**Access Control Logic:**

```php
// Who can access what:

| User Role | Own Documents | Student Documents | All Documents |
|-----------|---------------|-------------------|---------------|
| Student   | ✅ Yes        | ❌ No             | ❌ No         |
| Parent    | ✅ Yes        | ✅ Child's only   | ❌ No         |
| Teacher   | ✅ Yes        | ✅ Assigned class | ❌ No         |
| Principal | ✅ Yes        | ✅ All school     | ✅ Yes        |
| Admin     | ✅ Yes        | ✅ All            | ✅ Yes        |
| Accounts  | ✅ Yes        | ✅ Fee-related    | ❌ No         |
```

---

#### Step 4: Create Document Policy (30 minutes)

**File:** `app/Policies/StudentDocumentPolicy.php`

**Purpose:** Define who can access which documents

**Methods:**

```php
class StudentDocumentPolicy
{
    /**
     * Can user view student documents?
     */
    public function view(User $user, Student $student): bool
    {
        // Admin/principal can view all
        if ($user->hasAnyRole(['admin', 'principal'])) {
            return true;
        }

        // Student can view own
        if ($user->hasRole('student') && $user->student->id === $student->id) {
            return true;
        }

        // Parent can view child's
        if ($user->hasRole('parent')) {
            return $user->guardians->contains('student_id', $student->id);
        }

        // Teacher can view assigned division students
        if ($user->hasRole('teacher')) {
            return $user->assignedDivision->students->contains($student->id);
        }

        return false;
    }

    /**
     * Can user download specific document type?
     */
    public function download(User $user, Student $student, string $documentType): bool
    {
        // First check if they can view at all
        if (!$this->view($user, $student)) {
            return false;
        }

        // Additional restrictions for sensitive documents
        if (in_array($documentType, ['cast_certificate', 'aadhar'])) {
            // Only student, parent, admin, principal
            return $user->hasAnyRole(['admin', 'principal', 'student', 'parent']);
        }

        return true;
    }
}
```

---

#### Step 5: Create Routes (15 minutes)

**File:** `routes/web.php` (add new section)

```php
// Document Download Routes (Authenticated)
Route::middleware(['auth'])->prefix('documents')->name('documents.')->group(function () {
    
    // Student documents
    Route::get('/students/{student}/photo', [DocumentDownloadController::class, 'downloadStudentPhoto'])
         ->name('students.photo');
    
    Route::get('/students/{student}/signature', [DocumentDownloadController::class, 'downloadStudentSignature'])
         ->name('students.signature');
    
    Route::get('/students/{student}/cast-certificate', [DocumentDownloadController::class, 'downloadCastCertificate'])
         ->name('students.cast-certificate');
    
    Route::get('/students/{student}/marksheet', [DocumentDownloadController::class, 'downloadMarksheet'])
         ->name('students.marksheet');

    // Guardian documents
    Route::get('/guardians/{guardian}/photo', [DocumentDownloadController::class, 'downloadGuardianPhoto'])
         ->name('guardians.photo');

    // Teacher documents
    Route::get('/teachers/{teacher}/photo', [DocumentDownloadController::class, 'downloadTeacherPhoto'])
         ->name('teachers.photo');
});
```

---

#### Step 6: Update Views (1 hour)

**Files to Update:**

| View File | Current Code | New Code |
|-----------|--------------|----------|
| `resources/views/dashboard/students/show.blade.php` | Direct `<img>` tag | Route to download controller |
| `resources/views/dashboard/students/index.blade.php` | Direct link | Authenticated route |
| `resources/views/dashboard/teachers/show.blade.php` | Direct `<img>` tag | Authenticated route |

**Example Change:**

```blade
<!-- OLD (VULNERABLE): -->
<img src="{{ asset('storage/' . $student->photo_path) }}" alt="Student Photo">

<!-- NEW (SECURE): -->
<img src="{{ route('documents.students.photo', $student) }}" alt="Student Photo">
```

---

### 1.4 Testing Checklist

#### Security Tests:

- [ ] **Test 1:** Logout and try to access document URL directly
  - Expected: Redirect to login
  - URL: `http://localhost/documents/students/1/photo`

- [ ] **Test 2:** Login as student A, try to access student B's documents
  - Expected: 403 Forbidden
  - URL: `http://localhost/documents/students/{other_student_id}/photo`

- [ ] **Test 3:** Login as teacher, try to access documents from other division
  - Expected: 403 Forbidden (if teacher has specific division assignment)

- [ ] **Test 4:** Login as admin, access any student document
  - Expected: Download works
  - Verify download is logged

- [ ] **Test 5:** Check old public URLs don't work
  - Expected: 404 Not Found
  - URL: `http://localhost/storage/uploads/students/documents/cast_cert_123.pdf`

#### Functional Tests:

- [ ] **Test 6:** Upload new student with all documents
  - Verify files stored in `storage/app/private`
  - Verify can download via authenticated route

- [ ] **Test 7:** Update existing student with new photo
  - Verify old file deleted from private storage
  - Verify new file accessible via route

- [ ] **Test 8:** Download all document types
  - Photo (JPEG/PNG)
  - Signature (JPEG/PNG)
  - Caste Certificate (PDF)
  - Marksheet (PDF)

- [ ] **Test 9:** Check download logs
  - Verify each download logged with user_id, timestamp, document_type

---

### 1.5 Migration Plan (For Existing Files)

**Problem:** Files already uploaded to `storage/app/public` need to be moved to `storage/app/private`

**Solution:** Create artisan command

**File:** `app/Console/Commands/MigrateDocumentsToPrivateStorage.php`

```php
class MigrateDocumentsToPrivateStorage extends Command
{
    protected $signature = 'documents:migrate-to-private';
    protected $description = 'Move all documents from public to private storage';

    public function handle()
    {
        $this->info('Starting document migration...');

        // Move student documents
        $students = Student::all();
        foreach ($students as $student) {
            $this->migrateStudentDocuments($student);
        }

        // Move teacher documents
        $teachers = User::role('teacher')->get();
        foreach ($teachers as $teacher) {
            $this->migrateTeacherDocuments($teacher);
        }

        $this->info('Migration completed!');
    }

    private function migrateStudentDocuments($student)
    {
        $fields = ['photo_path', 'signature_path', 'cast_certificate_path', 'marksheet_path'];
        
        foreach ($fields as $field) {
            if ($student->$field) {
                $oldPath = $student->$field;
                $newPath = str_replace('uploads/', 'private_uploads/', $oldPath);
                
                // Copy file to new location
                if (Storage::disk('public')->exists($oldPath)) {
                    $content = Storage::disk('public')->get($oldPath);
                    Storage::disk('private')->put($newPath, $content);
                    
                    // Update database
                    $student->update([$field => $newPath]);
                    
                    // Delete old file
                    Storage::disk('public')->delete($oldPath);
                    
                    $this->line("  Migrated {$field} for student {$student->id}");
                }
            }
        }
    }
}
```

**Run Command:**
```bash
php artisan documents:migrate-to-private
```

---

## Part 2: Missing Document Fields

### 2.1 Database Migration

**File:** `database/migrations/2026_03_14_add_document_fields_to_students_table.php`

**Create with:**
```bash
php artisan make:migration add_document_fields_to_students_table --table=students
```

**Migration Content:**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Aadhaar document (separate from Aadhaar number)
            $table->string('aadhar_path', 500)->nullable()
                  ->after('aadhar_number')
                  ->comment('Path to Aadhaar card scan (private storage)');

            // Income Certificate
            $table->string('income_certificate_path', 500)->nullable()
                  ->after('cast_certificate_path')
                  ->comment('Path to income certificate (private storage)');

            // Domicile Certificate
            $table->string('domicile_certificate_path', 500)->nullable()
                  ->after('income_certificate_path')
                  ->comment('Path to domicile certificate (private storage)');

            // Add index for faster lookups
            $table->index('aadhar_path');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['aadhar_path']);
            $table->dropColumn(['aadhar_path', 'income_certificate_path', 'domicile_certificate_path']);
        });
    }
};
```

---

### 2.2 Model Update

**File:** `app/Models/User/Student.php`

**Changes to `$fillable` array:**

```php
protected $fillable = [
    // ... existing fields ...
    
    // ADD THESE NEW FIELDS:
    'aadhar_path',              // Path to Aadhaar document
    'income_certificate_path',  // Path to income certificate
    'domicile_certificate_path', // Path to domicile certificate
];
```

---

### 2.3 Controller Updates

**File:** `app/Http/Controllers/Web/StudentController.php`

#### Update `store()` validation:

```php
public function store(Request $request)
{
    $validated = $request->validate([
        // ... existing validation ...

        // ADD NEW VALIDATION RULES:
        'aadhar' => 'nullable|file|mimes:pdf,jpeg,png|max:2048',
        'income_certificate' => 'nullable|file|mimes:pdf,jpeg,png|max:2048',
        'domicile_certificate' => 'nullable|file|mimes:pdf,jpeg,png|max:2048',
    ]);

    // ... existing code ...

    // ADD NEW FILE UPLOAD HANDLING:
    if ($request->hasFile('aadhar')) {
        $validated['aadhar_path'] = $request->file('aadhar')->store(
            'uploads/students/documents/aadhar',
            'private'  // ✅ Always private for Aadhaar
        );
    }

    if ($request->hasFile('income_certificate')) {
        $validated['income_certificate_path'] = $request->file('income_certificate')->store(
            'uploads/students/documents/income',
            'private'
        );
    }

    if ($request->hasFile('domicile_certificate')) {
        $validated['domicile_certificate_path'] = $request->file('domicile_certificate')->store(
            'uploads/students/documents/domicile',
            'private'
        );
    }

    $student = Student::create($validated);
    // ...
}
```

#### Update `update()` validation:

```php
public function update(Request $request, Student $student)
{
    $validated = $request->validate([
        // ... existing validation ...

        // ADD NEW VALIDATION RULES:
        'aadhar' => 'nullable|file|mimes:pdf,jpeg,png|max:2048',
        'income_certificate' => 'nullable|file|mimes:pdf,jpeg,png|max:2048',
        'domicile_certificate' => 'nullable|file|mimes:pdf,jpeg,png|max:2048',
    ]);

    // ... existing file handling ...

    // ADD NEW FILE UPLOAD HANDLING:
    if ($request->hasFile('aadhar')) {
        if ($student->aadhar_path && Storage::disk('private')->exists($student->aadhar_path)) {
            Storage::disk('private')->delete($student->aadhar_path);
        }
        $validated['aadhar_path'] = $request->file('aadhar')->store(
            'uploads/students/documents/aadhar',
            'private'
        );
    }

    if ($request->hasFile('income_certificate')) {
        if ($student->income_certificate_path && Storage::disk('private')->exists($student->income_certificate_path)) {
            Storage::disk('private')->delete($student->income_certificate_path);
        }
        $validated['income_certificate_path'] = $request->file('income_certificate')->store(
            'uploads/students/documents/income',
            'private'
        );
    }

    if ($request->hasFile('domicile_certificate')) {
        if ($student->domicile_certificate_path && Storage::disk('private')->exists($student->domicile_certificate_path)) {
            Storage::disk('private')->delete($student->domicile_certificate_path);
        }
        $validated['domicile_certificate_path'] = $request->file('domicile_certificate')->store(
            'uploads/students/documents/domicile',
            'private'
        );
    }

    $student->update($validated);
    // ...
}
```

---

### 2.4 View Updates

**File:** `resources/views/dashboard/students/create.blade.php`

**Add new file upload fields:**

```blade
<!-- Add after existing document fields -->
<div class="row">
    <div class="col-md-4 mb-3">
        <label for="aadhar" class="form-label">Aadhaar Card</label>
        <input type="file" class="form-control @error('aadhar') is-invalid @enderror"
               id="aadhar" name="aadhar" accept=".pdf,.jpeg,.jpg,.png">
        <small class="text-muted">PDF, JPEG, PNG (Max 2MB)</small>
        @error('aadhar')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="income_certificate" class="form-label">Income Certificate</label>
        <input type="file" class="form-control @error('income_certificate') is-invalid @enderror"
               id="income_certificate" name="income_certificate" accept=".pdf,.jpeg,.jpg,.png">
        <small class="text-muted">PDF, JPEG, PNG (Max 2MB)</small>
        @error('income_certificate')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="domicile_certificate" class="form-label">Domicile Certificate</label>
        <input type="file" class="form-control @error('domicile_certificate') is-invalid @enderror"
               id="domicile_certificate" name="domicile_certificate" accept=".pdf,.jpeg,.jpg,.png">
        <small class="text-muted">PDF, JPEG, PNG (Max 2MB)</small>
        @error('domicile_certificate')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
```

**Update `edit.blade.php` with same fields + show existing documents:**

```blade
<div class="col-md-4 mb-3">
    <label for="aadhar" class="form-label">Aadhaar Card</label>
    @if($student->aadhar_path)
        <div class="mb-2">
            <a href="{{ route('documents.students.aadhar', $student) }}" target="_blank" class="btn btn-sm btn-info">
                <i class="bi bi-download"></i> Download Existing Aadhaar
            </a>
        </div>
    @endif
    <input type="file" class="form-control @error('aadhar') is-invalid @enderror"
           id="aadhar" name="aadhar" accept=".pdf,.jpeg,.jpg,.png">
    <small class="text-muted">Upload new file to replace (Max 2MB)</small>
    @error('aadhar')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
```

**Update `show.blade.php` to display all documents:**

```blade
<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-file-earmark-text"></i> Documents</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="text-center">
                    <i class="bi bi-person-badge fa-3x text-primary"></i>
                    <h6 class="mt-2">Aadhaar Card</h6>
                    @if($student->aadhar_path)
                        <a href="{{ route('documents.students.aadhar', $student) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-download"></i> Download
                        </a>
                    @else
                        <span class="text-muted">Not uploaded</span>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="text-center">
                    <i class="bi bi-currency-rupee fa-3x text-success"></i>
                    <h6 class="mt-2">Income Certificate</h6>
                    @if($student->income_certificate_path)
                        <a href="{{ route('documents.students.income-certificate', $student) }}" class="btn btn-sm btn-success">
                            <i class="bi bi-download"></i> Download
                        </a>
                    @else
                        <span class="text-muted">Not uploaded</span>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="text-center">
                    <i class="bi bi-geo-alt fa-3x text-info"></i>
                    <h6 class="mt-2">Domicile Certificate</h6>
                    @if($student->domicile_certificate_path)
                        <a href="{{ route('documents.students.domicile-certificate', $student) }}" class="btn btn-sm btn-info">
                            <i class="bi bi-download"></i> Download
                        </a>
                    @else
                        <span class="text-muted">Not uploaded</span>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="text-center">
                    <i class="bi bi-file-earmark-medical fa-3x text-warning"></i>
                    <h6 class="mt-2">Caste Certificate</h6>
                    @if($student->cast_certificate_path)
                        <a href="{{ route('documents.students.cast-certificate', $student) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-download"></i> Download
                        </a>
                    @else
                        <span class="text-muted">Not uploaded</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
```

---

### 2.5 Add Download Routes for New Documents

**File:** `routes/web.php` (add to document routes section)

```php
// ADD THESE NEW ROUTES:

Route::get('/students/{student}/aadhar', [DocumentDownloadController::class, 'downloadAadhar'])
     ->name('documents.students.aadhar');

Route::get('/students/{student}/income-certificate', [DocumentDownloadController::class, 'downloadIncomeCertificate'])
     ->name('documents.students.income-certificate');

Route::get('/students/{student}/domicile-certificate', [DocumentDownloadController::class, 'downloadDomicileCertificate'])
     ->name('documents.students.domicile-certificate');
```

**Update `DocumentDownloadController` with new methods:**

```php
public function downloadAadhar(Student $student)
{
    // Extra strict access control for Aadhaar
    if (!auth()->user()->can('download', [$student, 'aadhar'])) {
        abort(403, 'Unauthorized to access Aadhaar document');
    }

    if (!$student->aadhar_path) {
        abort(404, 'Aadhaar document not found');
    }

    // Log access (critical for Aadhaar)
    Log::info('Aadhaar document accessed', [
        'student_id' => $student->id,
        'user_id' => auth()->id(),
        'user_role' => auth()->user()->roles->first()->name,
        'timestamp' => now(),
    ]);

    return Storage::disk('private')->download($student->aadhar_path);
}

// Similar methods for income_certificate and domicile_certificate
```

---

## Part 3: Roles & Permissions Module

### 3.1 Current Status

#### ✅ What Exists:

| Component | Status | File Location |
|-----------|--------|---------------|
| **Spatie Package** | ✅ Installed | `vendor/spatie/laravel-permission` |
| **Database Tables** | ✅ Migrated | `roles`, `permissions`, `model_has_roles`, etc. |
| **Seeders** | ✅ Created | `database/seeders/RolePermissionSeeder.php` |
| **User Trait** | ✅ Added | `app/Models/User.php` (HasRoles trait) |
| **Middleware** | ⚠️ Empty | `app/Http/Middleware/CheckPermission.php` |
| **Policies** | ⚠️ Partial | `StudentPolicy.php`, `AdmissionPolicy.php` |

#### ❌ What's Missing:

| Component | Status | Impact |
|-----------|--------|--------|
| **RoleController** | ❌ Missing | No UI to create/edit roles |
| **PermissionController** | ❌ Missing | No UI to manage permissions |
| **CheckPermission Middleware** | ❌ Empty | Permission checks don't work |
| **Role Views** | ❌ Missing | No admin interface |
| **Permission Views** | ❌ Missing | No permission matrix |

---

### 3.2 Files to Create

#### Controllers (2 files):

| File | Purpose | Methods |
|------|---------|---------|
| `app/Http/Controllers/Web/RoleController.php` | Role management | index, create, store, edit, update, destroy, assignPermissions |
| `app/Http/Controllers/Web/PermissionController.php` | Permission management | index, store, destroy |

#### Requests (2 files):

| File | Purpose |
|------|---------|
| `app/Http/Requests/StoreRoleRequest.php` | Role creation validation |
| `app/Http/Requests/UpdateRoleRequest.php` | Role update validation |

#### Views (5 files):

| File | Purpose |
|------|---------|
| `resources/views/roles/index.blade.php` | List all roles |
| `resources/views/roles/create.blade.php` | Create new role |
| `resources/views/roles/edit.blade.php` | Edit role |
| `resources/views/roles/assign-permissions.blade.php` | Permission matrix |
| `resources/views/permissions/index.blade.php` | List all permissions |

---

### 3.3 Implementation Plan

#### Step 1: Fix CheckPermission Middleware (30 minutes)

**File:** `app/Http/Middleware/CheckPermission.php`

**Current Code:**
```php
class CheckPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);  // ❌ EMPTY - Does nothing!
    }
}
```

**Required Code:**
```php
class CheckPermission
{
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            abort(401, 'Unauthorized');
        }

        $user = auth()->user();

        // Check if user has any of the required permissions
        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                return $next($request);
            }
        }

        // User doesn't have any of the required permissions
        abort(403, 'You do not have permission to access this resource.');
    }
}
```

**Register Middleware:**

**File:** `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'permission' => \App\Http\Middleware\CheckPermission::class,
        'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
    ]);
})
```

---

#### Step 2: Create RoleController (2 hours)

**File:** `app/Http/Controllers/Web/RoleController.php`

**Methods Required:**

```php
class RoleController extends Controller
{
    /**
     * Display list of all roles
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('roles.index', compact('roles'));
    }

    /**
     * Show form to create new role
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy('group'); // If you add groups
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store newly created role
     */
    public function store(StoreRoleRequest $request)
    {
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        if ($request->filled('permissions')) {
            $role->givePermissionTo($request->permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully');
    }

    /**
     * Show role details
     */
    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        return view('roles.show', compact('role'));
    }

    /**
     * Show form to edit role
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update role
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update(['name' => $request->name]);

        if ($request->filled('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]); // Remove all permissions
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully');
    }

    /**
     * Delete role
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of system roles
        if (in_array($role->name, ['admin', 'principal'])) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete system roles');
        }

        $role->delete();
        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully');
    }

    /**
     * Show permission assignment interface
     */
    public function assignPermissions(Role $role)
    {
        $permissions = Permission::all()->groupBy('module'); // Group by module
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('roles.assign-permissions', compact('role', 'permissions', 'rolePermissions'));
    }
}
```

---

#### Step 3: Create PermissionController (1 hour)

**File:** `app/Http/Controllers/Web/PermissionController.php`

```php
class PermissionController extends Controller
{
    /**
     * Display list of all permissions
     */
    public function index()
    {
        $permissions = Permission::with('roles')->get()->groupBy('module');
        return view('permissions.index', compact('permissions'));
    }

    /**
     * Store new permission
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'module' => 'nullable|string|max:100', // e.g., 'student', 'fee', 'exam'
            'description' => 'nullable|string',
        ]);

        Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
            'module' => $validated['module'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully');
    }

    /**
     * Delete permission
     */
    public function destroy(Permission $permission)
    {
        // Prevent deletion of critical permissions
        $protected = ['view_students', 'create_students', 'edit_students', 'delete_students'];
        if (in_array($permission->name, $protected)) {
            return redirect()->route('permissions.index')
                ->with('error', 'Cannot delete protected permission');
        }

        $permission->delete();
        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully');
    }
}
```

---

#### Step 4: Add Routes (15 minutes)

**File:** `routes/web.php`

```php
// Role & Permission Management (Admin Only)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Roles
    Route::resource('roles', RoleController::class);
    Route::get('roles/{role}/permissions', [RoleController::class, 'assignPermissions'])
         ->name('roles.assign-permissions');
    Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])
         ->name('roles.update-permissions');

    // Permissions
    Route::resource('permissions', PermissionController::class)->except(['create', 'edit', 'update']);
});
```

---

#### Step 5: Create Views (3 hours)

**View Structure:**

```
resources/views/
├── roles/
│   ├── index.blade.php           # List all roles
│   ├── create.blade.php          # Create role form
│   ├── edit.blade.php            # Edit role form
│   ├── show.blade.php            # Role details
│   └── assign-permissions.blade.php  # Permission matrix
└── permissions/
    └── index.blade.php           # List all permissions
```

**Key UI Components:**

**roles/index.blade.php:**
- Table with columns: Name, Permissions Count, Users Count, Actions
- Buttons: View, Edit, Assign Permissions, Delete
- "Create New Role" button

**roles/create.blade.php:**
- Role name input
- Checkbox list of all permissions (grouped by module)
- "Create Role" button

**roles/assign-permissions.blade.php:**
- Permission matrix (checkboxes)
- Grouped by module (Student, Fee, Exam, etc.)
- "Save Permissions" button

**permissions/index.blade.php:**
- Table with columns: Name, Module, Description, Roles, Actions
- "Add New Permission" button (modal)
- Delete button

---

### 3.4 Enhanced Permission System

#### Add Module/Group to Permissions:

**Migration:**
```bash
php artisan make:migration add_module_to_permissions_table --table=permissions
```

```php
Schema::table('permissions', function (Blueprint $table) {
    $table->string('module', 100)->nullable()->after('name')
          ->comment('Module grouping: student, fee, exam, etc.');
    $table->text('description')->nullable()->after('module');
});
```

**Update Permission Model:**
```php
// app/Models/Permission.php (if you create one)
protected $fillable = ['name', 'guard_name', 'module', 'description'];
```

---

#### Permission Groups (Recommended Structure):

```php
$permissions = [
    // Student Module
    'view_students',
    'create_students',
    'edit_students',
    'delete_students',
    'export_students',
    'import_students',

    // Fee Module
    'view_fees',
    'collect_fees',
    'manage_fee_structures',
    'view_fee_reports',
    'refund_fees',

    // Examination Module
    'view_exams',
    'create_exams',
    'enter_marks',
    'approve_marks',
    'generate_results',

    // Attendance Module
    'view_attendance',
    'mark_attendance',
    'edit_attendance',
    'view_attendance_reports',

    // Reports Module
    'view_reports',
    'generate_reports',
    'export_reports',

    // Admin Module
    'manage_roles',
    'manage_permissions',
    'manage_users',
    'view_audit_logs',
];
```

---

### 3.5 Testing Checklist

#### Role Management Tests:

- [ ] **Test 1:** Create new role "Coordinator"
  - Assign permissions: view_students, view_attendance, view_reports
  - Verify role appears in list

- [ ] **Test 2:** Edit role name
  - Change "Coordinator" to "Academic Coordinator"
  - Verify name updated

- [ ] **Test 3:** Assign permissions to role
  - Use permission matrix UI
  - Verify permissions saved

- [ ] **Test 4:** Delete custom role
  - Delete "Academic Coordinator"
  - Verify role deleted

- [ ] **Test 5:** Try to delete system role
  - Try to delete "admin" role
  - Expected: Error message "Cannot delete system roles"

#### Permission Tests:

- [ ] **Test 6:** Create new permission
  - Name: "bulk_promote_students"
  - Module: "student"
  - Verify permission created

- [ ] **Test 7:** Assign permission to role
  - Add "bulk_promote_students" to "principal" role
  - Verify assignment works

- [ ] **Test 8:** Try to delete protected permission
  - Try to delete "view_students"
  - Expected: Error message

#### Middleware Tests:

- [ ] **Test 9:** Access protected route without permission
  - Login as user without "delete_students" permission
  - Try to access delete student route
  - Expected: 403 Forbidden

- [ ] **Test 10:** Access protected route with permission
  - Login as admin with "delete_students" permission
  - Access delete student route
  - Expected: Route works

---

## Part 4: Complete Implementation Timeline

### Day 1-2: Document Security (CRITICAL)

| Task | Time | Priority |
|------|------|----------|
| Fix StudentController storage | 30 min | 🔴 P0 |
| Fix other controllers | 15 min | 🔴 P0 |
| Create DocumentDownloadController | 1 hour | 🔴 P0 |
| Create Document Policy | 30 min | 🔴 P0 |
| Add routes | 15 min | 🔴 P0 |
| Update views | 1 hour | 🔴 P0 |
| Test security fixes | 2 hours | 🔴 P0 |

**Total Day 1-2:** 6 hours

---

### Day 3: Missing Document Fields

| Task | Time | Priority |
|------|------|----------|
| Create migration | 15 min | 🟠 P1 |
| Run migration | 5 min | 🟠 P1 |
| Update Student model | 10 min | 🟠 P1 |
| Update StudentController | 1 hour | 🟠 P1 |
| Update create.blade.php | 30 min | 🟠 P1 |
| Update edit.blade.php | 30 min | 🟠 P1 |
| Update show.blade.php | 30 min | 🟠 P1 |
| Add download routes | 15 min | 🟠 P1 |
| Test document uploads | 1 hour | 🟠 P1 |

**Total Day 3:** 4 hours

---

### Day 4-5: Roles & Permissions

| Task | Time | Priority |
|------|------|----------|
| Fix CheckPermission middleware | 30 min | 🔴 P0 |
| Create RoleController | 2 hours | 🟠 P1 |
| Create PermissionController | 1 hour | 🟠 P1 |
| Create Form Requests | 30 min | 🟠 P1 |
| Add routes | 15 min | 🟠 P1 |
| Create role views (5 files) | 3 hours | 🟠 P1 |
| Create permission views | 1 hour | 🟠 P1 |
| Test role management | 1 hour | 🟠 P1 |

**Total Day 4-5:** 10 hours

---

### Day 6: Testing & Bug Fixes

| Task | Time |
|------|------|
| Test all document security fixes | 2 hours |
| Test document upload/download | 1 hour |
| Test role management | 1 hour |
| Test permission checks | 1 hour |
| Bug fixes | 2 hours |

**Total Day 6:** 7 hours

---

### Day 7: Documentation & Training

| Task | Time |
|------|------|
| Update user documentation | 2 hours |
| Create admin guide | 2 hours |
| Train staff on new features | 2 hours |
| Backup & deployment | 2 hours |

**Total Day 7:** 8 hours

---

## Part 5: Risk Mitigation

### Risk 1: Existing Public Files Exposed

**Mitigation:**
1. Run migration command immediately after deploying code
2. Verify old URLs return 404
3. Monitor access logs for suspicious activity

**Command:**
```bash
php artisan documents:migrate-to-private
```

---

### Risk 2: Users Can't Access Documents After Migration

**Mitigation:**
1. Test with admin account first
2. Test with teacher account
3. Test with student account
4. Keep backup of all files

**Rollback Plan:**
```bash
# If issues arise, rollback migration
php artisan migrate:rollback --step=1

# Revert code changes
git checkout HEAD -- app/Http/Controllers/Web/StudentController.php
```

---

### Risk 3: Permission Changes Break Existing Functionality

**Mitigation:**
1. Don't modify existing roles initially
2. Create new test role for testing
3. Test all critical workflows before deploying
4. Keep list of protected permissions

**Protected Permissions (Don't Delete):**
```php
$protected = [
    'view_students',
    'create_students',
    'edit_students',
    'delete_students',
    'view_fees',
    'collect_fees',
    'enter_marks',
    'mark_attendance',
];
```

---

## Part 6: Success Criteria

### Document Security:

- [ ] ✅ All documents stored in `storage/app/private`
- [ ] ✅ No direct URL access without authentication
- [ ] ✅ Download routes require login
- [ ] ✅ Access control based on role
- [ ] ✅ Download logging implemented
- [ ] ✅ Old public URLs return 404

### Document Fields:

- [ ] ✅ Migration adds 3 new fields
- [ ] ✅ Upload forms accept new documents
- [ ] ✅ Download buttons for all document types
- [ ] ✅ Validation working correctly
- [ ] ✅ Files stored in private storage

### Roles & Permissions:

- [ ] ✅ Role list view working
- [ ] ✅ Create role form working
- [ ] ✅ Edit role form working
- [ ] ✅ Permission matrix working
- [ ] ✅ CheckPermission middleware functional
- [ ] ✅ Permission checks working on routes

---

## Part 7: Post-Implementation Checklist

### Immediate (Day 1):

- [ ] Backup database
- [ ] Backup all uploaded files
- [ ] Deploy code to staging
- [ ] Test on staging
- [ ] Deploy to production

### Week 1:

- [ ] Monitor error logs
- [ ] Check download logs
- [ ] Verify no 403 errors from legitimate users
- [ ] Test all document types
- [ ] Test role creation

### Month 1:

- [ ] Review download logs for suspicious activity
- [ ] Audit role assignments
- [ ] Clean up unused permissions
- [ ] Update documentation
- [ ] Train new staff

---

## Summary

### Total Effort:

| Phase | Days | Hours |
|-------|------|-------|
| Document Security | 2 | 6 |
| Document Fields | 1 | 4 |
| Roles & Permissions | 2 | 10 |
| Testing | 1 | 7 |
| Documentation | 1 | 8 |
| **TOTAL** | **7** | **35 hours** |

### Critical Success Factors:

1. **Fix storage security FIRST** - Public exposure is critical
2. **Test thoroughly before deploying** - Don't rush to production
3. **Keep backups** - Database and files
4. **Monitor after deployment** - Watch for issues
5. **Document everything** - For future reference

---

**Ready to start?** Let me know which section you want to tackle first!
