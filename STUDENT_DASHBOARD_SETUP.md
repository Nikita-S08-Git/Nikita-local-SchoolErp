# Student Dashboard System - Setup Guide

## Overview
This document explains the Student Dashboard System built with Laravel. The system includes authentication, profile management, attendance tracking, timetable viewing, fees management, results, and library features.

---

## Authentication System

### How It Works
- **Login URL**: `http://127.0.0.1:8000/student/login`
- **Dashboard URL**: `http://127.0.0.1:8000/student/dashboard`
- **Password**: Uses Laravel's Hash::check() for secure password verification
- **Guard**: Custom `student` guard configured in `config/auth.php`

### Login Credentials (from Seeder)
```
Email: student.2025bcoma001@school.com
Password: password123
```

### Authentication Flow
1. User enters email and password at `/student/login`
2. System validates credentials against User table
3. Verifies password using `Hash::check()`
4. Checks if student record exists and is active
5. Logs in using `Auth::guard('student')->login()`
6. Redirects to `/student/dashboard`

---

## Database Structure

### Tables
1. **users** - Contains login credentials (email, hashed password)
2. **students** - Contains student details (name, roll_no, class, section, etc.)
3. **timetables** - Contains class schedule
4. **attendances** - Contains attendance records
5. **student_fees** - Contains fee records
6. **student_marks** - Contains exam results
7. **book_issues** - Contains library book issues

---

## Routes (web.php)

### Public Routes (Guest)
- `GET /student/login` - Show login form
- `POST /student/login` - Handle login

### Protected Routes (Requires Auth)
- `POST /student/logout` - Logout
- `GET /student/dashboard` - Dashboard
- `GET /student/profile` - View profile
- `GET /student/profile/edit` - Edit profile
- `PUT /student/profile` - Update profile
- `GET /student/profile/change-password` - Change password
- `POST /student/profile/change-password` - Update password
- `GET /student/timetable` - View timetable
- `GET /student/attendance` - View attendance
- `GET /student/fees` - View fees
- `GET /student/results` - View results
- `GET /student/library` - View library
- `GET /student/notifications` - View notifications

---

## Models & Relationships

### Student Model
- Implements `Authenticatable` interface
- Relationships:
  - `user()` - BelongsTo User
  - `division()` - BelongsTo Division
  - `attendances()` - HasMany Attendance
  - `notifications()` - HasMany StudentNotification

### Controllers
1. **Student\AuthController** - Handles login/logout
2. **Student\DashboardController** - Handles all dashboard features

---

## Security Features

1. **Auth Middleware** - Protects all student routes
2. **CSRF Protection** - Enabled by default in Laravel
3. **Password Hashing** - Uses bcrypt via Hash::make()
4. **Route Protection** - Uses `auth:student` middleware
5. **Input Validation** - Form requests validate all input

---

## Running the Application

### After Cloning from GitHub

1. **Install Dependencies**
```bash
composer install
```

2. **Setup Environment**
```bash
copy .env.example .env
php artisan key:generate
```

3. **Run Migrations**
```bash
php artisan migrate:fresh --seed
```

4. **Start Server**
```bash
php artisan serve
```

5. **Access Student Login**
```
URL: http://127.0.0.1:8000/student/login
Email: student.2025bcoma001@school.com
Password: password123
```

---

## Cache Commands (After GitHub Push/Pull)

If you push to GitHub and pull on another machine, run:
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## GitHub Upload

To upload to GitHub (Teacher_M branch):
```bash
run upload-to-github-final.bat
```

Or manually:
```bash
git init
git add .
git commit -m "Student Dashboard System"
git branch -M Teacher_M
git remote add origin https://github.com/Nikita-S08-Git/Nikita-local-SchoolErp.git
git push -u origin Teacher_M
```

---

## Troubleshooting

### Issue: Route not found
**Solution**: Run `php artisan route:clear`

### Issue: Login not working
**Solution**: 
1. Check .env database settings
2. Run `php artisan config:clear`
3. Verify user exists in users table
4. Verify student exists in students table

### Issue: Redirect loop
**Solution**: Clear cache with `php artisan cache:clear`

---

## File Structure

```
app/
├── Http/
│   └── Controllers/
│       └── Student/
│           ├── AuthController.php
│           └── DashboardController.php
├── Models/
│   └── User/
│       └── Student.php
config/
├── auth.php (student guard)
routes/
└── web.php (student routes)
resources/
└── views/
    └── student/
        ├── dashboard.blade.php
        ├── attendance/
        ├── auth/
        ├── fees/
        ├── library/
        ├── notifications/
        ├── profile/
        ├── results/
        └── timetable/
```

---

## Summary

The Student Dashboard System is complete with:
- ✅ Laravel built-in authentication with custom guard
- ✅ Login with email/password (passwords hashed)
- ✅ Dashboard with profile, attendance, timetable
- ✅ Fees management
- ✅ Results viewing
- ✅ Library book tracking
- ✅ Notifications system
- ✅ Full CRUD for profile
- ✅ Password change functionality
- ✅ Clean, responsive Bootstrap UI
- ✅ Security (auth middleware, CSRF, validation)
