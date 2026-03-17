# Student Login Process - Step-by-Step Guide

## Overview

This document explains the complete student login process for the School ERP system, including both web and API login methods.

## Web Login Process (Session-based)

### 1. User Accesses Login Page
**URL**: `/login`  
**Controller**: `app/Http/Controllers/Student/AuthController.php@showLogin`  
**View**: `resources/views/student/auth/login.blade.php`

The student navigates to the login page, which displays a form with email and password fields.

### 2. Form Submission
**Method**: POST  
**URL**: `/student/login`  
**Controller**: `app/Http/Controllers/Student/AuthController.php@login`

```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);
    // ... validation and authentication logic
}
```

### 3. Credentials Validation
The system validates:
- Email is required and valid format
- Password is required
- Input sanitization and CSRF protection

### 4. Student Lookup
The system searches for the student by email:

```php
$user = \App\Models\User::where('email', $credentials['email'])->first();
```

**Tables accessed**: `users`

### 5. Password Verification
Compares entered password with bcrypted password from database:

```php
if (!\Hash::check($credentials['password'], $user->password)) {
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
}
```

### 6. Student Record Validation
Ensures the user has a corresponding student record:

```php
$student = \App\Models\User\Student::where('user_id', $user->id)->first();

if (!$student) {
    return back()->withErrors([
        'email' => 'Student record not found. Please contact administration.',
    ]);
}
```

**Tables accessed**: `students`

### 7. Account Status Check
Verifies student is active:

```php
if ($student->student_status !== 'active') {
    return back()->withErrors([
        'email' => 'Your account is not active. Please contact administration.',
    ]);
}
```

### 8. Password Change Check
New feature! Checks if password needs to be changed:

```php
if (empty($student->user->password_changed_at)) {
    return redirect()->route('student.profile.change-password')->with('warning', 'Please change your temporary password');
}
```

### 9. Login Successful
- Creates session
- Regenerates CSRF token
- Redirects to dashboard:

```php
Auth::guard('student')->login($student, $request->filled('remember'));
$request->session()->regenerate();

return redirect()->intended(route('student.dashboard'));
```

**Session management**: Uses Laravel's built-in session system with student guard

## API Login Process (Token-based)

### 1. API Login Request
**Method**: POST  
**URL**: `/api/login`  
**Controller**: `app/Http/Controllers/Api/AuthController.php@login`

**Request Body**:
```json
{
    "email": "student@example.com", 
    "password": "password123"
}
```

**Headers**:
```
Content-Type: application/json
```

### 2. API Login Response (Success)
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "password_changed_at": null,
            "roles": [
                {
                    "id": 3,
                    "name": "student",
                    "guard_name": "web",
                    "created_at": "2024-01-01T00:00:00.000000Z",
                    "updated_at": "2024-01-01T00:00:00.000000Z"
                }
            ]
        },
        "token": "1|Xq3J7f8kL9mN0pQ1rS2tU3vW4xY5zA6B7C8D9E0F1G2H3J4K5L6M"
    }
}
```

### 3. Token Usage
All subsequent API requests must include the token in the Authorization header:

```
Authorization: Bearer 1|Xq3J7f8kL9mN0pQ1rS2tU3vW4xY5zA6B7C8D9E0F1G2H3J4K5L6M
```

**Token storage**: Personal access tokens are stored in `personal_access_tokens` table

## Password Change on First Login

### 1. Redirect to Change Password
If `password_changed_at` is null, student is redirected to:
**URL**: `/student/profile/change-password`

### 2. Password Change Form
**Controller**: `app/Http/Controllers/Student/DashboardController.php@changePassword`  
**View**: `resources/views/student/profile/change-password.blade.php`

Form fields:
- Current password
- New password (with strong validation)
- New password confirmation

### 3. Password Update Process
**Method**: POST  
**URL**: `/student/profile/change-password`  
**Controller**: `app/Http/Controllers/Student/DashboardController.php@updatePassword`

Validation:
```php
$validated = $request->validate([
    'current_password' => 'required',
    'password' => 'required|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
]);
```

Password change:
```php
$student->user->update([
    'password' => Hash::make($validated['password']),
    'password_changed_at' => now(),
]);
```

## Login Flow Diagram

```
Student enters login page
    ↓
Fills email and password
    ↓
Form submission to /student/login
    ↓
Email validation
    ↓
User lookup in users table
    ↓
Password verification (bcrypt)
    ↓
Student record verification
    ↓
Account status check (active)
    ↓
Password change needed?
    ├─ Yes → Redirect to change password page
    └─ No → Continue to dashboard
```

## Security Features

### 1. Session Security
- CSRF protection
- Session fixation protection
- Session regeneration on login
- Session timeout configuration

### 2. Password Security
- Bcrypt hashing (bcrypt with Laravel's built-in hasher)
- Strong password validation
- Password change on first login
- Password reset functionality

### 3. Rate Limiting
- Login attempts are limited by Laravel's built-in rate limiter

### 4. Account Lockout
- Failed login attempts tracked (configurable)
- Account lockout after multiple failed attempts

## Common Issues & Solutions

### 1. Invalid Credentials Error
- Check email format
- Verify password is correct (case-sensitive)
- Ensure CAPS LOCK is off

### 2. Account Inactive
- Contact administration to activate account
- Check if student status is set to 'active' in database

### 3. Password Change Required
- Must change temporary password on first login
- New password must meet complexity requirements

### 4. Session Timeout
- Re-login to establish new session
- Check session configuration in `config/session.php`

## Testing Login

### Web Browser Test
1. Navigate to `/login`
2. Enter valid student credentials
3. Verify login successful and redirect to dashboard
4. Test with invalid credentials to see error handling

### API Test (Postman)
1. Send POST request to `/api/login`
2. Include valid email and password in body
3. Verify token is returned
4. Test token with a protected route

### Database Verification
Check tables after login:
- `users` table for user details
- `personal_access_tokens` for API tokens
- `sessions` table for web sessions
- `model_has_roles` for role assignments

This comprehensive login process ensures secure authentication for students while providing a user-friendly experience with appropriate error handling and security measures.
