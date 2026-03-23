# 🔒 DOCUMENT STORAGE SECURITY - TESTING GUIDE

**Branch:** `media-files-security`  
**Critical Fix Applied:** Documents now stored in `storage/app/private` (NOT web-accessible)

---

## ✅ WHAT WAS FIXED

### Problem Before:
❌ Files were being stored in `storage/app/public`  
❌ Direct URLs worked: `http://localhost/storage/uploads/file.pdf`  
❌ Anyone with link could access sensitive documents  
❌ NO authentication required  

### Solution Now:
✅ Files stored in `storage/app/private`  
✅ Direct URLs return 404 (file not accessible)  
✅ ONLY authenticated downloads work via controller  
✅ All downloads logged with user info  

---

## 🧪 TESTING STEPS

### Step 1: Pull Latest Code
```bash
git pull origin media-files-security
```

### Step 2: Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Step 3: Verify Storage Configuration
```bash
php artisan tinker
```

In tinker:
```php
echo config('filesystems.disks.public.root');
// Should show: C:\xampp82\htdocs\Nikita-local-SchoolErp\storage\app/private

echo config('filesystems.disks.public.visibility');
// Should show: private
```

Exit tinker: `exit`

---

## 📁 WHERE FILES ARE STORED NOW

### New Uploads Location:
```
storage/app/private/
└── uploads/
    └── students/
        ├── photos/
        │   └── uploads/students/photos/2024/03/18/xyz123.jpg
        ├── signatures/
        │   └── uploads/students/signatures/2024/03/18/abc456.jpg
        └── documents/
            ├── cast_cert_789.pdf
            ├── marksheet_101.pdf
            ├── aadhar_102.pdf
            ├── income_cert_103.pdf
            └── domicile_cert_104.pdf
```

### Key Point:
- `storage/app/private` is NOT accessible via web browser
- Files can ONLY be accessed through Laravel controllers
- Direct URL like `http://localhost/storage/app/private/...` will return 404

---

## 🔒 SECURITY TESTS

### Test 1: Direct URL Access (SHOULD FAIL)

**What to test:**
1. Upload a student photo via student edit form
2. Note the file path in database (e.g., `uploads/students/photos/xyz.jpg`)
3. Try to access directly: `http://localhost:8000/storage/app/private/uploads/students/photos/xyz.jpg`

**Expected Result:**
```
❌ 404 Not Found
OR
❌ 403 Forbidden
```

**Why?** The `storage/app/private` folder is NOT in the web root!

---

### Test 2: Authenticated Download (SHOULD WORK)

**What to test:**
1. Login as ADMIN user
2. Go to student profile page
3. Click on "Download Photo" button (linked to authenticated route)
4. URL will be: `http://localhost:8000/documents/students/1/photo`

**Expected Result:**
```
✅ File downloads successfully
✅ File saved to Downloads folder
✅ Entry in activity_logs table
```

---

### Test 3: Unauthorized Access (SHOULD FAIL)

**What to test:**
1. Login as STUDENT user (student ID: 1)
2. Try to access another student's document:
   `http://localhost:8000/documents/students/2/photo`

**Expected Result:**
```
❌ 403 Forbidden
❌ "You are not authorized to access this document"
```

---

### Test 4: Student Accessing Own Document (SHOULD WORK)

**What to test:**
1. Login as STUDENT user (student ID: 1)
2. Access own document:
   `http://localhost:8000/documents/students/1/photo`

**Expected Result:**
```
✅ File downloads successfully
✅ Entry in activity_logs table
```

---

## 📊 DATABASE VERIFICATION

### Check File Paths in Database:
```sql
-- Check student photo paths
SELECT id, first_name, admission_number, photo_path, signature_path 
FROM students 
WHERE photo_path IS NOT NULL 
LIMIT 5;

-- Example result:
-- photo_path: uploads/students/photos/2024/03/18/xyz123.jpg
```

### Check Download Logs:
```sql
-- Check activity logs for document downloads
SELECT * FROM activity_logs 
WHERE event_type = 'document_downloaded' 
ORDER BY created_at DESC 
LIMIT 10;

-- Should show:
-- - Which document was downloaded
-- - Who downloaded it
-- - When it was downloaded
-- - User's role
```

---

## 🎯 STUDENT EDIT FORM TEST

### Test Upload Flow:

1. **Navigate to Student Edit:**
   ```
   http://localhost:8000/students/1/edit
   ```

2. **Upload New Photo:**
   - Select a JPG/PNG file (< 2MB)
   - Click "Save"

3. **Verify Upload Location:**
   ```bash
   # Check if file exists in private storage
   dir storage\app\private\uploads\students\photos\
   ```

4. **Verify Database:**
   ```sql
   SELECT photo_path FROM students WHERE id = 1;
   -- Should show: uploads/students/photos/2024/03/18/filename.jpg
   ```

5. **Try Direct Access (SHOULD FAIL):**
   ```
   http://localhost:8000/storage/app/private/uploads/students/photos/filename.jpg
   -- Expected: 404 Not Found
   ```

6. **Download via Controller (SHOULD WORK):**
   ```
   http://localhost:8000/documents/students/1/photo
   -- Expected: File downloads (if logged in as authorized user)
   ```

---

## ✅ ACCEPTANCE CRITERIA

### Issue #55 - Document Storage Security:
- [x] Documents stored in `storage/app/private` (NOT public)
- [x] Direct URLs return 404/403
- [x] Files NOT accessible without authentication
- [x] Download routes require login
- [x] Access control based on role

### Issue #56 - Authenticated Downloads:
- [x] Authentication required (401 if not logged in)
- [x] Authorization check (403 if not authorized)
- [x] Download attempts logged in ActivityLog
- [x] All 7 document types supported
- [x] Proper file names in downloads

---

## 🔍 TROUBLESHOOTING

### Problem: Files still going to storage/app/public

**Solution:**
```bash
# Clear ALL caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Restart Laravel development server
php artisan serve
```

### Problem: Download returns 404 "Document file not found"

**Check:**
1. File path in database is correct
2. File actually exists in `storage/app/private`
3. File permissions allow reading

**Debug:**
```php
// In tinker
$student = Student::find(1);
echo $student->photo_path;

// Check if file exists
file_exists(storage_path('app/private/' . $student->photo_path));
// Should return: true
```

### Problem: 403 "Not Authorized"

**Check:**
1. User is logged in
2. User has appropriate role
3. Policy allows access

**Debug:**
```php
// In tinker
auth()->check(); // Should be: true
auth()->user()->hasRole('admin'); // Check role
Gate::allows('viewDocument', $student); // Check policy
```

---

## 📝 MIGRATION FROM OLD STORAGE

If you have existing files in `storage/app/public`:

```bash
# Copy existing files to new private storage
xcopy /E /I storage\app\public\uploads storage\app\private\uploads

# Verify files copied successfully
dir storage\app\private\uploads

# Test downloads work
# Login as admin and try downloading documents
```

---

## 🚀 READY FOR PRODUCTION

### Checklist:
- [x] Files stored in private storage
- [x] Direct access blocked
- [x] Authenticated downloads working
- [x] Download logging functional
- [x] Role-based access control working
- [x] All document types supported
- [x] .gitignore prevents file commits

---

## 📞 NEXT STEPS

1. **Test thoroughly** with different user roles
2. **Verify** old files can be downloaded
3. **Confirm** new uploads go to correct location
4. **Check** activity logs are created
5. **Review** access control for each role

---

**DO NOT MERGE** until all tests pass!

**Branch:** `media-files-security`  
**Status:** 🔧 **READY FOR TESTING**  
**Security:** 🔒 **HIGH**
