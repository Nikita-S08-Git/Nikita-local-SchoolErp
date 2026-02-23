# Student Module - Implementation Guide

## Quick Start Guide

This guide will help you implement the improved Student Management Module in your Laravel School ERP project.

---

## Step 1: Install Required Packages

```bash
# Install Laravel Excel for import/export functionality
composer require maatwebsite/excel

# Install PDF generator (optional, for PDF exports)
composer require barryvdh/laravel-dompdf

# Ensure Laravel Sanctum is installed (for API authentication)
composer require laravel/sanctum
```

---

## Step 2: Run Database Migrations

```bash
# Run the performance optimization migration
php artisan migrate

# This will add indexes to improve query performance
```

---

## Step 3: Update Routes

### API Routes (routes/api.php)

```php
use App\Http\Controllers\Api\OptimizedStudentController;

Route::middleware('auth:sanctum')->group(function () {
    // Student CRUD
    Route::apiResource('students', OptimizedStudentController::class);
    
    // Additional endpoints
    Route::get('students/search', [OptimizedStudentController::class, 'search']);
    Route::get('students/{student}/profile', [OptimizedStudentController::class, 'profile']);
    Route::post('students/{student}/change-status', [OptimizedStudentController::class, 'changeStatus']);
    Route::post('students/bulk-update-status', [OptimizedStudentController::class, 'bulkUpdateStatus']);
    
    // Import/Export
    Route::post('students/export', [OptimizedStudentController::class, 'export']);
    Route::get('students/export-template', [OptimizedStudentController::class, 'exportTemplate']);
    Route::post('students/import', [OptimizedStudentController::class, 'import']);
    Route::post('students/validate-import', [OptimizedStudentController::class, 'validateImport']);
    
    // Statistics
    Route::get('students/statistics', [OptimizedStudentController::class, 'statistics']);
    Route::get('students/by-program/{programId}', [OptimizedStudentController::class, 'byProgram']);
    Route::get('students/by-division/{divisionId}', [OptimizedStudentController::class, 'byDivision']);
});
```

### Web Routes (routes/web.php)

```php
use App\Http\Controllers\Web\StudentController;

Route::middleware(['auth', 'web'])->group(function () {
    Route::resource('students', StudentController::class);
});
```

---

## Step 4: Configure Storage

```bash
# Create symbolic link for public storage
php artisan storage:link

# Ensure storage directories exist
mkdir -p storage/app/public/uploads/students/photos
mkdir -p storage/app/public/uploads/students/signatures
mkdir -p storage/app/public/exports
mkdir -p storage/app/public/templates
```

---

## Step 5: Update Your Existing Controller (Optional)

If you want to update your existing Web controller to use the new services:

```php
// app/Http/Controllers/Web/StudentController.php

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Services\ImprovedStudentService;
use App\Repositories\StudentRepository;

class StudentController extends Controller
{
    public function __construct(
        private StudentRepository $repository,
        private ImprovedStudentService $service
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['program_id', 'division_id', 'status', 'academic_year']);
        $students = $this->repository->getAllWithFilters($filters, 20);
        
        return view('students.index', compact('students'));
    }

    public function store(StoreStudentRequest $request)
    {
        $student = $this->service->createStudent($request->validated());
        
        return redirect()
            ->route('students.show', $student)
            ->with('success', 'Student created successfully');
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $this->service->updateStudent($student, $request->validated());
        
        return redirect()
            ->route('students.show', $student)
            ->with('success', 'Student updated successfully');
    }
}
```

---

## Step 6: Create Export Classes

### Create StudentsExport class

```bash
php artisan make:export StudentsExport
```

```php
// app/Exports/StudentsExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromArray, WithHeadings, WithStyles
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return array_keys($this->data[0] ?? []);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
```

### Create SimpleExport class

```php
// app/Exports/SimpleExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class SimpleExport implements FromArray
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }
}
```

---

## Step 7: Create Events (Optional)

If you want to use events for notifications:

```bash
php artisan make:event StudentCreated
php artisan make:event StudentUpdated
php artisan make:event StudentDeleted
```

```php
// app/Events/StudentCreated.php

namespace App\Events;

use App\Models\User\Student;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Student $student)
    {
    }
}
```

---

## Step 8: Configure Permissions

Ensure your permission system is set up:

```php
// In your DatabaseSeeder or PermissionSeeder

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Create permissions
Permission::create(['name' => 'view students']);
Permission::create(['name' => 'create students']);
Permission::create(['name' => 'edit students']);
Permission::create(['name' => 'delete students']);
Permission::create(['name' => 'export students']);
Permission::create(['name' => 'import students']);

// Assign to roles
$admin = Role::findByName('admin');
$admin->givePermissionTo([
    'view students',
    'create students',
    'edit students',
    'delete students',
    'export students',
    'import students'
]);

$teacher = Role::findByName('teacher');
$teacher->givePermissionTo(['view students', 'edit students']);

$staff = Role::findByName('staff');
$staff->givePermissionTo(['view students', 'create students']);
```

---

## Step 9: Update Student Policy

```php
// app/Policies/StudentPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\User\Student;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view students');
    }

    public function view(User $user, Student $student): bool
    {
        return $user->hasPermissionTo('view students');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create students');
    }

    public function update(User $user, Student $student): bool
    {
        return $user->hasPermissionTo('edit students');
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->hasPermissionTo('delete students');
    }
}
```

Register in [`AuthServiceProvider`](app/Providers/AuthServiceProvider.php:1):

```php
protected $policies = [
    Student::class => StudentPolicy::class,
];
```

---

## Step 10: Test the Implementation

### Test API Endpoints

```bash
# Get authentication token
curl -X POST http://your-domain/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# List students
curl -X GET http://your-domain/api/students \
  -H "Authorization: Bearer YOUR_TOKEN"

# Create student
curl -X POST http://your-domain/api/students \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "John",
    "last_name": "Doe",
    "date_of_birth": "2005-01-15",
    "gender": "male",
    "program_id": 1,
    "division_id": 1,
    "academic_session_id": 1,
    "academic_year": "FY",
    "admission_date": "2024-06-01",
    "category": "general",
    "student_status": "active"
  }'

# Search students
curl -X GET "http://your-domain/api/students/search?q=john" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Export students
curl -X POST http://your-domain/api/students/export \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"format":"xlsx","filters":{"status":"active"}}'
```

---

## Step 11: Performance Optimization

### Enable Query Caching

In [`config/cache.php`](config/cache.php:1), ensure you have a cache driver configured:

```php
'default' => env('CACHE_DRIVER', 'redis'), // or 'file', 'database'
```

### Configure Redis (Recommended)

```bash
# Install Redis PHP extension
composer require predis/predis

# Update .env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Clear Cache When Needed

```bash
# Clear all cache
php artisan cache:clear

# Clear specific cache
php artisan cache:forget student.1
```

---

## Step 12: Monitoring & Logging

### Enable Query Logging (Development)

In [`AppServiceProvider`](app/Providers/AppServiceProvider.php:1):

```php
use Illuminate\Support\Facades\DB;

public function boot()
{
    if (app()->environment('local')) {
        DB::listen(function ($query) {
            logger()->info('Query executed', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time
            ]);
        });
    }
}
```

### Monitor Performance

```bash
# Install Laravel Telescope (optional)
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

---

## Common Issues & Solutions

### Issue 1: "Class not found" errors

**Solution:**
```bash
composer dump-autoload
php artisan clear-compiled
php artisan config:clear
```

### Issue 2: Import fails with "Invalid file format"

**Solution:**
- Download the template using `/api/students/export-template`
- Ensure all required columns are present
- Check data types match requirements

### Issue 3: Slow queries

**Solution:**
```bash
# Run the index migration
php artisan migrate

# Enable query caching
# Use eager loading in queries
```

### Issue 4: File upload fails

**Solution:**
```bash
# Create storage link
php artisan storage:link

# Check permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

## Next Steps

1. **Customize Validation Rules**: Modify [`StoreStudentRequest`](app/Http/Requests/StoreStudentRequest.php:1) and [`UpdateStudentRequest`](app/Http/Requests/UpdateStudentRequest.php:1) based on your requirements

2. **Add Custom Fields**: Extend the Student model and migration if you need additional fields

3. **Implement Notifications**: Create email/SMS notifications for student registration

4. **Add Reports**: Create custom reports using the export service

5. **Mobile App Integration**: Use the API endpoints for mobile app development

6. **Add Tests**: Write unit and feature tests for your implementation

---

## Support & Documentation

- **Full Documentation**: See [`STUDENT_MODULE_DOCUMENTATION.md`](STUDENT_MODULE_DOCUMENTATION.md:1)
- **Code Examples**: Check the controller and service files
- **Laravel Docs**: https://laravel.com/docs

---

## Checklist

- [ ] Install required packages
- [ ] Run migrations
- [ ] Update routes
- [ ] Configure storage
- [ ] Create export classes
- [ ] Set up permissions
- [ ] Update policies
- [ ] Test API endpoints
- [ ] Configure caching
- [ ] Review documentation

---

**Congratulations!** Your Student Management Module is now optimized and ready for production use.
