# Quick Reference - Single College System & Principal Dashboard

## 🚀 Quick Start Commands

```bash
# 1. Run migration to remove college_id
php artisan migrate

# 2. Seed principal user and role
php artisan db:seed --class=PrincipalSeeder

# 3. Clear all caches
php artisan optimize:clear

# 4. Test the application
php artisan serve
```

## 🔑 Login Credentials

```
URL: http://localhost:8000/login
Email: principal@school.edu
Password: password123
```

## 📊 Dashboard URL

```
http://localhost:8000/dashboard/principal
```

## 🔍 Testing Queries (Tinker)

```bash
php artisan tinker
```

### Test Student Count
```php
App\Models\User\Student::where('student_status', 'active')->count();
```

### Test Teacher Count
```php
App\Models\User::role('teacher')->count();
```

### Test Division Count
```php
App\Models\Academic\Division::where('is_active', true)->count();
```

### Test Fee Collection (This Month)
```php
use Carbon\Carbon;
App\Models\Fee\FeePayment::whereMonth('payment_date', Carbon::now()->month)
    ->whereYear('payment_date', Carbon::now()->year)
    ->sum('amount_paid');
```

### Test Attendance (Today)
```php
App\Models\Attendance\Attendance::whereDate('attendance_date', today())->count();
```

## 📁 Files Created/Modified

### New Files
- ✅ `app/Http/Controllers/Web/PrincipalDashboardController.php`
- ✅ `app/Http/Middleware/CheckRole.php`
- ✅ `database/migrations/2026_02_18_000001_convert_to_single_college_system.php`
- ✅ `database/seeders/PrincipalSeeder.php`
- ✅ `resources/views/dashboard/principal.blade.php`

### Modified Files
- ✅ `routes/web.php` (Updated principal route)
- ✅ `bootstrap/app.php` (Added role middleware)

## 🎯 Key Features

### Principal Dashboard Shows:
1. **Total Students** - Active students only
2. **Total Teachers** - Users with teacher role
3. **Total Classes** - Active divisions
4. **Fee Collection** - Current month summary
5. **Pending Fees** - Outstanding amounts
6. **Attendance** - Today's statistics
7. **Recent Activities** - Last 7 days

## 🔒 Security

### Role-Based Access
```php
// Only principal can access
Route::get('/dashboard/principal', [PrincipalDashboardController::class, 'index'])
    ->middleware('role:principal');
```

### Middleware Check
```php
// In CheckRole middleware
if (!auth()->user()->hasRole($role)) {
    abort(403, 'Unauthorized access');
}
```

## 📈 Performance Optimizations

### Optimized Queries
```php
// ✅ Good - Single query with aggregation
$feeCollection = FeePayment::whereMonth('payment_date', $currentMonth)
    ->select(DB::raw('SUM(amount_paid) as total_collected'))
    ->first();

// ❌ Bad - Multiple queries
$payments = FeePayment::whereMonth('payment_date', $currentMonth)->get();
$total = $payments->sum('amount_paid');
```

### Indexed Columns
- `students.student_status`
- `students.deleted_at`
- `divisions.is_active`
- `fee_payments.payment_date`
- `attendance.attendance_date`

## 🐛 Common Issues & Solutions

### Issue 1: "Class 'Role' not found"
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### Issue 2: "Route not found"
```bash
php artisan route:clear
php artisan route:cache
```

### Issue 3: "View not found"
```bash
php artisan view:clear
```

### Issue 4: "No data showing"
```bash
# Check if tables have data
php artisan tinker
>>> App\Models\User\Student::count();
>>> App\Models\User::role('teacher')->count();
```

## 📝 Database Schema Changes

### Before (Multi-College)
```sql
CREATE TABLE students (
    id BIGINT PRIMARY KEY,
    college_id BIGINT,  -- ❌ Removed
    user_id BIGINT,
    program_id BIGINT,
    ...
);
```

### After (Single College)
```sql
CREATE TABLE students (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,
    program_id BIGINT,
    ...
    -- No college_id ✅
);
```

## 🎨 UI Components

### Statistics Card Example
```blade
<div class="card">
    <div class="card-body">
        <p class="text-muted">Total Students</p>
        <h3 class="fw-bold">{{ number_format($totalStudents) }}</h3>
    </div>
</div>
```

### Fee Collection Display
```blade
<h4 class="text-success">₹{{ number_format($feeCollection->total_collected, 2) }}</h4>
<small>{{ $feeCollection->total_transactions }} transactions</small>
```

## 🧪 Testing Checklist

- [ ] Principal can login
- [ ] Dashboard loads without errors
- [ ] Student count is correct
- [ ] Teacher count is correct
- [ ] Class count is correct
- [ ] Fee collection shows current month
- [ ] Attendance shows today's data
- [ ] Recent activities display
- [ ] Non-principal users get 403 error
- [ ] All queries are optimized

## 📞 Support Commands

```bash
# Check Laravel version
php artisan --version

# Check routes
php artisan route:list | grep principal

# Check migrations status
php artisan migrate:status

# Check database connection
php artisan db:show

# View logs
tail -f storage/logs/laravel.log
```

## 🎓 Learning Resources

### Laravel Eloquent
- [Eloquent Relationships](https://laravel.com/docs/eloquent-relationships)
- [Query Builder](https://laravel.com/docs/queries)

### Spatie Permissions
- [Documentation](https://spatie.be/docs/laravel-permission)

### Blade Templates
- [Blade Documentation](https://laravel.com/docs/blade)

---

**Ready to use! 🚀**
