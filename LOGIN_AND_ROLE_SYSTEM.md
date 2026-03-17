# School ERP - Login and Role-Based Access System

## Overview

This document details the unified login system and role-based access control (RBAC) implementation for the School ERP. The system supports three main user types:

- **Students**: Can view attendance and timetable
- **Teachers**: Can view assigned lectures and mark attendance  
- **Admins/Principals**: Full access to all system features

## Login System

### Web Login (Session-based)

#### Endpoint: `/login`
**Controller**: [`AuthController.php`](app/Http/Controllers/Web/AuthController.php)

#### Flow:
1. **Credentials Validation**: Email and password validation
2. **User Lookup**: 
   - First checks main `users` table for admin/teacher
   - Then checks `students` table for student accounts
3. **Password Verification**: Uses Laravel's `Hash::check()` for secure comparison
4. **Account Status Check**: Ensures account is active
5. **Session Creation**: Generates secure session
6. **Role-based Redirection**: 
   - Admin/Principal → `/dashboard/admin` or `/dashboard/principal`
   - Teacher → `/teacher/dashboard`
   - Student → `/student/dashboard`

#### Code Highlights:
```php
// Check if user exists in main users table
$user = User::where('email', $credentials['email'])->first();

if ($user) {
    if (\Hash::check($credentials['password'], $user->password)) {
        if (!$user->isActive()) {
            return back()->withErrors(['email' => 'Account inactive']);
        }
        
        Auth::login($user);
        $request->session()->regenerate();
        
        return redirect()->route($redirectRoutes[$role]);
    }
}
```

### API Login (Token-based)

#### Endpoint: `POST /api/login`
**Controller**: [`AuthController.php`](app/Http/Controllers/Api/AuthController.php)

#### Flow:
1. Similar to web login but returns JWT token for API authentication
2. Token generation using Laravel Sanctum
3. Response includes user data and token

#### Code Highlights:
```php
$token = $user->createToken('auth_token')->plainTextToken;

return response()->json([
    'success' => true,
    'message' => 'Login successful',
    'data' => [
        'user' => $user->load('roles'),
        'token' => $token,
    ]
]);
```

## Role-Based Access Control (RBAC)

### Middleware Implementation

#### CheckUserRole Middleware
**File**: [`CheckUserRole.php`](app/Http/Middleware/CheckUserRole.php)

Features:
- **Student Access Control**: Restricts students to allowed routes only
- **Role Validation**: Verifies user roles before allowing access
- **Route Whitelisting**: Only specific routes accessible by students

#### Code:
```php
public function handle(Request $request, Closure $next, ...$allowedRoles): Response
{
    if (Auth::guard('student')->check()) {
        $studentAllowedRoutes = [
            'student.dashboard',
            'student.timetable',
            'student.attendance',
            'student.profile',
            'student.logout',
        ];

        $currentRoute = $request->route()->getName();
        
        if (!in_array($currentRoute, $studentAllowedRoutes)) {
            abort(403, 'You are not authorized to access this route.');
        }
        
        return $next($request);
    }
    
    // Teacher/Admin role validation...
}
```

### Role-based Route Protection

#### Student Routes (Session-based)
```php
Route::middleware(['auth:student', 'check.user.role'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/timetable', [StudentDashboardController::class, 'timetable'])->name('timetable');
    Route::get('/attendance', [StudentDashboardController::class, 'attendance'])->name('attendance');
    // ... other student routes
});
```

#### Teacher Routes (Session-based)
```php
Route::middleware(['auth', 'role:teacher|class_teacher'])->group(function () {
    Route::get('/teacher/dashboard', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
    Route::get('/teacher/attendance/create/{timetableId}', [AttendanceController::class, 'create'])->name('teacher.attendance.create');
    Route::post('/teacher/attendance/store/{timetableId}', [AttendanceController::class, 'store'])->name('teacher.attendance.store');
    // ... other teacher routes
});
```

### API Routes

All API routes require authentication via `auth:sanctum` middleware:

```php
Route::middleware('auth:sanctum')->group(function () {
    // Protected API routes here
});
```

## Security Measures

### Password Storage
- **Hashing Algorithm**: Bcrypt (Laravel's default)
- **Auto-hash**: Passwords automatically hashed when using `$user->password = 'value'`
- **Password Validation**: Minimum 8 characters with confirmation

### Session Security
- **Session Regeneration**: On login and logout
- **CSRF Protection**: Built-in Laravel CSRF middleware
- **Session Invalidation**: On logout

### Error Handling

#### Authentication Errors
```php
return response()->json([
    'success' => false,
    'message' => 'Invalid credentials',
], 401);
```

#### Authorization Errors
```php
abort(403, 'You are not authorized to access this route.');
```

## User Model Relationships

### Main User Model
**File**: [`User.php`](app/Models/User.php)

```php
class User extends Authenticatable
{
    use HasRoles, HasApiTokens;
    
    public function student(): HasOne
    {
        return $this->hasOne(\App\Models\User\Student::class);
    }
    
    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }
    
    public function isActive(): bool
    {
        return $this->is_active === true;
    }
}
```

### Student Model
**File**: [`Student.php`](app/Models/User/Student.php)

```php
class Student extends Model implements Authenticatable
{
    use AuthenticatableTrait;
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
```

## Configuration

### Authentication Guards (config/auth.php)
```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'api' => [
        'driver' => 'sanctum',
        'provider' => 'users',
    ],
    'student' => [
        'driver' => 'session',
        'provider' => 'students',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
    'students' => [
        'driver' => 'eloquent',
        'model' => App\Models\User\Student::class,
    ],
],
```

### Middleware Registration (bootstrap/app.php)
```php
$middleware->alias([
    'check.division.capacity' => \App\Http\Middleware\CheckDivisionCapacity::class,
    'role' => \App\Http\Middleware\CheckRole::class,
    'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
    'check.user.role' => \App\Http\Middleware\CheckUserRole::class,
]);
```

## Common Issues and Fixes

### 1. Role not found
**Symptom**: Login fails or redirects to wrong dashboard
**Solution**: Ensure user has at least one role assigned in `model_has_roles` table

### 2. Session timeout
**Symptom**: User gets logged out automatically
**Solution**: Adjust session lifetime in `config/session.php`

### 3. Token expires
**Symptom**: API requests return 401 after some time
**Solution**: Implement token refresh mechanism or increase token lifetime

### 4. Role-based access not working
**Symptom**: User can access restricted routes
**Solution**: Check middleware assignment and ensure allowed routes are correctly whitelisted

## Testing

### Login with Different Roles

#### Admin/Principal
```bash
Email: admin@schoolerp.com
Password: password123
Redirect: /dashboard/admin
```

#### Teacher
```bash
Email: teacher@schoolerp.com
Password: password123
Redirect: /teacher/dashboard
```

#### Student
```bash
Email: student@schoolerp.com
Password: password123
Redirect: /student/dashboard
```

### Role-based Access Tests
- **Student cannot access admin routes**: `/admin/*` should return 403
- **Teacher cannot access student routes**: `/student/*` should redirect to login
- **Admin can access all routes**: Should have full access

## Conclusion

The login and role-based access system provides:
- **Unified login interface** for all user types
- **Secure authentication** with password hashing
- **Role-based route protection** using middleware
- **API token authentication** for mobile apps/APIs
- **Comprehensive error handling** and logging
- **Session management** with security features

The system ensures that each user type has appropriate access levels, with students being strictly limited to viewing attendance and timetable.
