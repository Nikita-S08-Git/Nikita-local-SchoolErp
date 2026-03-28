# 🎓 School ERP System - Teacher & Student Module

A comprehensive school management system built with Laravel 12 and PHP 8.2.

## 🚀 Latest Updates (March 27, 2026)

### ✨ Major Features Added

#### 🎓 Student Module
- **Auto Password Generation** - 8-character random passwords on admission
- **Enhanced Dashboard** - Results, Fees, Exams, Notifications, Timetable
- **Unified UI** - Same sidebar design as teacher panel
- **Results Management** - Pagination, View/Print/Download actions
- **Complete Profile** - Edit profile, change password, upload photo

#### 👨‍🏫 Teacher Module
- **Settings Page** - Account, Contact, Notifications, Privacy settings
- **Profile Management** - Edit profile with validation
- **Division Tracking** - View all assigned divisions with badges
- **Attendance Tools** - Improved button visibility, quick mark all
- **Student Results** - Pagination, detailed view with actions

### 🐛 Critical Bug Fixes
- ✅ Bootstrap Icons loading issue
- ✅ Student role detection (no Spatie roles)
- ✅ Exam subject requirement enforcement
- ✅ Division assignment display
- ✅ Route naming for student pages

## 📋 Features

### Student Features
- 📊 Dashboard with statistics
- 📅 View timetable
- ✅ Check attendance
- 📝 View exam results
- 💰 Pay fees online
- 📚 Library book tracking
- 🔔 Notifications
- 👤 Profile management

### Teacher Features
- 📊 Dashboard with division overview
- 👥 View assigned students
- ✅ Mark attendance
- 📝 Enter exam marks
- 📅 View timetable
- 👤 Profile & Settings
- 🔔 Notifications

## 🛠️ Tech Stack

- **Backend:** Laravel 12.40.2
- **PHP:** 8.2.12
- **Database:** MySQL
- **Frontend:** Bootstrap 5, Font Awesome, Bootstrap Icons
- **Payment:** Razorpay Integration

## 📦 Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/ChetanKaturde/Nikita-local-SchoolErp.git
   cd Nikita-local-SchoolErp-Teacher_M
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Setup environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=school_erp
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run migrations:**
   ```bash
   php artisan migrate
   ```

6. **Seed sample data (optional):**
   ```bash
   php artisan db:seed
   ```

7. **Start development server:**
   ```bash
   php artisan serve
   ```

## 🔐 Default Login Credentials

### Student
- **Email:** Check admission records
- **Password:** Auto-generated (shown after admission)

### Teacher
- **Email:** david.lee@schoolerp.com
- **Password:** Check user records

### Admin
- **Email:** admin@schoolerp.com
- **Password:** Check database

## 📁 Project Structure

```
Nikita-local-SchoolErp-Teacher_M/
├── app/
│   ├── Http/Controllers/
│   │   ├── Student/
│   │   ├── Teacher/
│   │   └── Web/
│   ├── Models/
│   │   ├── User/
│   │   └── Result/
│   └── Services/
├── resources/
│   ├── views/
│   │   ├── student/
│   │   ├── teacher/
│   │   └── layouts/
│   └── js/
├── routes/
│   ├── web.php
│   ├── teacher.php
│   └── student.php
└── database/
    └── migrations/
```

## 🔄 Recent Changes

See [CHANGELOG.md](CHANGELOG.md) for detailed list of all changes.

## 📝 Key Updates

### Student Admission Flow
1. Student fills admission form
2. System generates 8-char random password
3. Credentials displayed immediately
4. Email auto-verified
5. Student role assigned
6. Password stored hashed + plain text (for admin)

### Teacher Settings
- Account Settings (Email)
- Contact Information (Phone, Address)
- Notification Preferences (Email, SMS)
- Privacy Settings (LinkedIn, Password)

### UI Unification
- All panels use same `layouts.app`
- Consistent sidebar across modules
- Same design tokens
- Better user experience

## 🧪 Testing

```bash
# Run tests
php artisan test

# Feature tests
php artisan test --testsuite=Feature

# Unit tests
php artisan test --testsuite=Unit
```

## 📊 Database Schema

Key tables:
- `users` - User accounts
- `students` - Student records
- `teacher_profiles` - Teacher details
- `divisions` - Class divisions
- `examinations` - Exam records
- `student_marks` - Result records
- `student_fees` - Fee records
- `teacher_assignments` - Teacher-Division mapping

## 🔒 Security Features

- Password hashing with bcrypt
- CSRF protection
- Role-based access control
- Input validation
- SQL injection prevention
- XSS protection

## 📞 Support

For issues or questions:
1. Check [CHANGELOG.md](CHANGELOG.md)
2. Review migration files
3. Check controller methods
4. Contact development team

## 📄 License

This project is proprietary software.

## 👥 Contributors

- Development Team - School ERP Project

---

**Last Updated:** March 27, 2026  
**Version:** 2.0.0  
**Status:** ✅ Production Ready
