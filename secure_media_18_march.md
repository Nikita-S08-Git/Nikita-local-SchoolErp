# Secure Media / Document Serving — Implementation Notes
**Date:** 18 March 2025 | **Branch:** `media-files-security`

---

## How It Works

### 1. Storage Location
All student files (photos, signatures, certificates) are stored under `public/uploads/students/` — **outside the web-accessible symlink path** (`public/storage`).

| Type | Stored Path |
|---|---|
| Photo | `public/uploads/students/photos/` |
| Signature | `public/uploads/students/signatures/` |
| Documents | `public/uploads/students/documents/` |

Filesystem disk config (`config/filesystems.php`):
```php
'public' => [
    'driver'     => 'local',
    'root'       => public_path(),   // resolves to /public
    'visibility' => 'private',
]
```

---

### 2. Direct Web Access Blocked
`public/uploads/.htaccess` denies all direct HTTP requests:
```apache
Order deny,allow
Deny from all
```
Attempting to open `http://localhost:8000/uploads/students/photos/file.jpg` directly returns **403 Forbidden**.

---

### 3. Authenticated Route Serves Files
All file requests go through a single authenticated Laravel route:

```
GET /documents/students/{student}/{documentType}
```
Named: `documents.students.document`  
Constraint: `documentType` must be one of `photo|signature|cast_certificate|marksheet|aadhar|income_certificate|domicile_certificate`

The route is wrapped in `auth` middleware — unauthenticated users are redirected to the login page.

---

### 4. Authorization (Who Can Access What)
`DocumentDownloadController::canAccessStudentDocument()` enforces role-based access:

| Role | Access |
|---|---|
| `admin`, `principal`, `student_section`, `accounts_staff`, HODs, `office` | All students |
| `teacher`, `class_teacher`, `subject_teacher` | Students in their assigned division only |
| `student` | Own documents only (`user_id` match) |
| `parent` | Child's documents only (guardian email match) |

Unauthorized access returns **403 Forbidden**.

---

### 5. File Response
- **Images** (`image/*`) → served with `Content-Disposition: inline` so `<img>` tags render them directly.
- **PDFs / other docs** → served with `Content-Disposition: attachment` to force download.
- Activity is logged via `ActivityLog::log()` (wrapped in try-catch so logging failure never blocks file serving).

---

### 6. Middleware Fix
`PreventBackHistory` middleware was calling `->header()` on `BinaryFileResponse` (returned by `response()->file()`), which caused a fatal error. Fixed by skipping both `StreamedResponse` and `BinaryFileResponse`:

```php
if ($response instanceof StreamedResponse || $response instanceof BinaryFileResponse) {
    return $response;
}
```

---

## Files Changed
| File | Change |
|---|---|
| `config/filesystems.php` | `public` disk root set to `public_path()` |
| `app/Http/Controllers/Web/DocumentDownloadController.php` | Inline auth, correct `ActivityLog::log()`, inline/attachment disposition |
| `app/Http/Controllers/Web/StudentController.php` | Upload paths standardized to `uploads/students/*` |
| `app/Http/Controllers/Web/AdmissionController.php` | Upload paths fixed |
| `app/Http/Middleware/PreventBackHistory.php` | Skip `BinaryFileResponse` to prevent fatal error |
| `app/Policies/StudentDocumentPolicy.php` | Fixed `use App\Models\User` namespace |
| `public/uploads/.htaccess` | Blocks all direct HTTP access |
| `public/uploads/.gitignore` | Ignores uploaded files, keeps `.htaccess` |
| `resources/views/dashboard/students/show.blade.php` | Uses authenticated document route |
| `resources/views/dashboard/students/edit.blade.php` | Uses authenticated route for previews |
| `resources/views/dashboard/students/create.blade.php` | Added missing `category` field |
| `resources/views/admissions/show.blade.php` | Uses authenticated download routes |
| `routes/web.php` | Document route registered under `auth` middleware |
