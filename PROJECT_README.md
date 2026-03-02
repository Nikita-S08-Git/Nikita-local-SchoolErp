# 🏫 School ERP System

## Complete School Management System

A comprehensive, production-ready School ERP system built with Laravel for managing all aspects of school operations.

---

## ✨ Features

### **Core Modules:**
- ✅ User & Role Management (Admin, Principal, Teachers, Students)
- ✅ Academic Setup (Sessions, Programs, Divisions, Subjects)
- ✅ Student Management (Enrollment, Records, Profiles)
- ✅ Teacher & Staff Management
- ✅ Attendance Management (Mark, Track, Report)
- ✅ Timetable Management (Grid & Table Views)
- ✅ Examination Management (Create, Marks Entry)
- ✅ Results Generation (Auto-calculate, PDF Export)
- ✅ Fee Management (Structures, Payments, Receipts)
- ✅ Reports & Analytics (PDF & Excel Export)

### **Advanced Features:**
- 📊 Grade Calculation System
- 📄 PDF Generation (Results, Receipts, Reports)
- 📊 Excel Export (Attendance Reports)
- 🎓 Scholarship Management
- 📅 Multiple Timetable Views (Grid/Table)
- 📈 Real-time Dashboard Statistics

---

## 🚀 Quick Start

### **1. Setup:**
```bash
cd c:\xampp\htdocs\School\School
composer install
php artisan key:generate
```

### **2. Configure Database (.env):**
```
DB_DATABASE=school_erp
DB_USERNAME=root
DB_PASSWORD=
```

### **3. Migrate & Seed:**
```bash
php artisan migrate
php artisan db:seed --class=CompleteSchoolDataSeeder
```

### **4. Start Server:**
```bash
php artisan serve
```

### **5. Access:**
```
http://127.0.0.1:8000
```

---

## 📊 Sample Data Included

- **Users:** Admin, Principal, 10-20 Teachers, 30-50 Students
- **Timetable:** Complete weekly schedules
- **Attendance:** 30 days of records
- **Examinations:** 4 scheduled exams
- **Fees:** Complete fee structures

---

## 🌐 Key URLs

| Feature | URL |
|---------|-----|
| Dashboard | `/dashboard/principal` |
| Timetable (Grid) | `/academic/timetable` |
| Timetable (Table) | `/academic/timetable/table` |
| Attendance | `/academic/attendance` |
| Examinations | `/examinations` |
| Results | `/results` |
| Reports | `/reports/attendance` |

---

## 📚 Documentation

- `PRODUCTION_READY.md` - Production checklist
- `COMPLETE_SETUP_GUIDE.md` - Detailed setup
- `TIMETABLE_VIEWS.md` - Timetable features
- `SEEDING_GUIDE.md` - Data seeding

---

## 🛠️ Tech Stack

- Laravel 10.x
- MySQL
- Bootstrap 5
- DomPDF
- Maatwebsite/Excel

---

## ✅ Status

**100% Complete | Production Ready | Fully Documented**

---

**Start using now:**
```bash
php artisan serve
```
