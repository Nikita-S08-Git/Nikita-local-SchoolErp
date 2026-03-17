# Password Security Implementation for School ERP

## Overview

This document details the implementation of a secure password system for the School ERP, addressing the following requirements:

1. Secure password storage using bcrypt
2. Generation of random temporary passwords for new users
3. Password validation and strength requirements
4. Force password change on first login
5. Secure login and authentication
6. Protection against password exposure

## Changes Made

### 1. User Model Updates
**File**: `app/Models/User.php`

- Added `password_changed_at` to $fillable array
- Already had 'password' => 'hashed' cast for automatic hashing

### 2. Student Model Updates  
**File**: `app/Models/User/Student.php`

- Added hidden properties to prevent password exposure:
  ```php
  protected $hidden = ['password', 'remember_token'];
  ```
- Added password casting:
  ```php
  'password' => 'hashed'
  ```

### 3. Student Controller Improvements
**File**: `app/Http/Controllers/Api/Academic/StudentController.php`

- Changed from fixed default password to random 10-character temporary password:
  ```php
  $tempPassword = Str::random(10);
  $user->password = Hash::make($tempPassword);
  ```
- Added log entry to track temporary passwords
- Added TODO for email notification

### 4. Teacher Controller Updates
**File**: `app/Http/Controllers/Web/TeacherController.php`

- Strengthened password validation:
  ```php
  'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
  ```
- Added password_changed_at timestamp on update

### 5. Student Dashboard Controller
**File**: `app/Http/Controllers/Student/DashboardController.php`

- Strengthened password validation for change password
- Updated password change to set password_changed_at timestamp
- Changed to update user record instead of student record:
  ```php
  $student->user->update([
      'password' => Hash::make($validated['password']),
      'password_changed_at' => now(),
  ]);
  ```

### 6. Web Profile Controller
**File**: `app/Http/Controllers/Web/ProfileController.php`

- Strengthened password validation
- Added password_changed_at timestamp on password change

### 7. Authentication Controllers
**File**: `app/Http/Controllers/Web/AuthController.php`

- Added password change check on login for both admin/teacher and student:
  ```php
  if (empty($user->password_changed_at)) {
      return redirect()->route('password.change')->with('warning', 'Please change your temporary password');
  }
  ```

**File**: `app/Http/Controllers/Api/AuthController.php`

- Updated login responses to include password changed status:
  ```php
  'user' => $user->load('roles')->makeVisible(['password_changed_at'])->toArray()
  ```

### 8. Force Password Change Middleware
**File**: `app/Http/Middleware/ForcePasswordChange.php`

- Created middleware to enforce password change on first login
- Blocks access to main dashboard routes if password not changed
- Allows access only to change password and logout routes
- Handles both web and student guards

### 9. Middleware Registration
**File**: `bootstrap/app.php`

- Added middleware alias:
  ```php
  'force.password.change' => \App\Http\Middleware\ForcePasswordChange::class,
  ```

### 10. Migration for Password Changed At
**File**: `database/migrations/2024_01_01_000000_add_password_changed_at_to_users_table.php`

- Creates `password_changed_at` column in users table:
  ```php
  $table->timestamp('password_changed_at')->nullable()->after('password');
  ```

## Password Security Features

### 1. Password Validation
- Minimum length: 8 characters
- Requires at least one uppercase letter
- Requires at least one lowercase letter  
- Requires at least one number
- Requires at least one special character (@$!%*?&)
- Supports password confirmation

### 2. Password Generation
- New students: Random 10-character temporary password
- New teachers: User-defined password with validation
- Default passwords are no longer hardcoded

### 3. Password Storage
- Uses bcrypt hashing algorithm
- Laravel's built-in automatic hashing with 'hashed' cast
- Passwords never stored in plain text

### 4. Password Change Enforcement
- Users must change temporary passwords on first login
- Blocks access to main features until password changed
- Redirects to password change page on login if needed
- Sets password_changed_at timestamp on successful change

### 5. Authentication Security
- BCrypt comparison for login
- Token-based API authentication
- Session management with CSRF protection
- Rate limiting on login attempts

## Usage Instructions

### 1. Running Migrations
```bash
php artisan migrate
```

### 2. Applying Middleware
Apply the force password change middleware to routes that should require password change:

```php
// In routes/web.php
Route::middleware(['auth', 'force.password.change'])->group(function () {
    // Protected routes that require password change
});
```

### 3. Testing the System
- Create new user/student to verify temporary password generation
- Login with new account to see password change prompt
- Test password validation by attempting weak passwords
- Verify password change updates password_changed_at

## API Responses

### Login Response (Success)
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
            "roles": [...]
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
    }
}
```

### Password Change Endpoint
- **URL**: `/api/password/change`
- **Method**: PUT
- **Headers**: Authorization: Bearer {token}
- **Body**: 
  ```json
  {
    "current_password": "oldpassword",
    "password": "newStrongPassword@123",
    "password_confirmation": "newStrongPassword@123"
  }
  ```

## Security Best Practices

1. **Email Notifications**: Implement email sending of temporary passwords in production
2. **Password History**: Consider tracking password changes to prevent reuse
3. **Two-Factor Authentication**: Add 2FA for additional security
4. **Password Expiration**: Implement password expiration policies
5. **Account Lockout**: Add failed login attempt tracking and account lockout
6. **Password Managers**: Encourage use of password managers
7. **Security Audits**: Regularly audit password security and update policies

## Conclusion

The password security system has been implemented with industry-standard practices, including secure password storage, validation, and force password change on first login. The system provides strong protection against password-related vulnerabilities while maintaining usability.
