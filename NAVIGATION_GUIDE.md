# School ERP - Quick Navigation Guide

## 🔗 All Available URLs

### 📚 Academic Management
```
http://127.0.0.1:8000/academic/sessions          - Academic Sessions
http://127.0.0.1:8000/academic/programs          - Programs/Classes
http://127.0.0.1:8000/academic/divisions         - Divisions/Sections
http://127.0.0.1:8000/academic/subjects          - Subjects
http://127.0.0.1:8000/academic/attendance        - Attendance
```

### 👨‍🎓 Student Management
```
http://127.0.0.1:8000/dashboard/students         - Students List
http://127.0.0.1:8000/dashboard/students/create  - Add New Student
http://127.0.0.1:8000/admissions                 - Admissions
```

### 👨‍🏫 Teacher Management
```
http://127.0.0.1:8000/dashboard/teachers         - Teachers List
http://127.0.0.1:8000/staff                      - Staff Management
```

### 📝 Examinations & Results
```
http://127.0.0.1:8000/examinations               - Examinations List
http://127.0.0.1:8000/examinations/create        - Create Examination
http://127.0.0.1:8000/examinations/{id}/marks-entry  - Enter Marks
http://127.0.0.1:8000/results                    - Generate Results
```

### 💰 Fee Management
```
http://127.0.0.1:8000/fees/structures            - Fee Structures
http://127.0.0.1:8000/fees/assignments           - Fee Assignments
http://127.0.0.1:8000/fees/payments              - Fee Payments
http://127.0.0.1:8000/fees/payments/create       - Record Payment
http://127.0.0.1:8000/fees/outstanding           - Outstanding Fees
http://127.0.0.1:8000/fees/scholarships          - Scholarships
```

### 📊 Reports
```
http://127.0.0.1:8000/reports/attendance         - Attendance Reports
```

### 📖 Library
```
http://127.0.0.1:8000/library/books              - Books Management
http://127.0.0.1:8000/library/issues             - Book Issues
```

### 🏠 Dashboards
```
http://127.0.0.1:8000/dashboard/principal        - Principal Dashboard
http://127.0.0.1:8000/dashboard/admin            - Admin Dashboard
http://127.0.0.1:8000/teacher/dashboard          - Teacher Dashboard
http://127.0.0.1:8000/dashboard/student          - Student Dashboard
```

---

## 🎯 Common Workflows

### Workflow 1: Create Examination & Enter Marks
1. Create Examination: `/examinations/create`
2. View Examinations: `/examinations`
3. Enter Marks: Click "✏️ Enter Marks" on examination
4. Select Division & Subject
5. Enter marks and save

### Workflow 2: Generate Result Cards
1. Go to: `/results`
2. Select Examination & Division
3. Click "Generate Results"
4. Download PDF

### Workflow 3: Record Fee Payment & Generate Receipt
1. Go to: `/fees/payments/create`
2. Select student and enter payment details
3. Submit payment
4. Receipt will be displayed automatically
5. Download PDF receipt

### Workflow 4: Generate Attendance Report
1. Go to: `/reports/attendance`
2. Select Division, From Date, To Date
3. Click "Generate Report"
4. Download PDF or Excel

---

## 🔐 Default Login Credentials

Check `CREDENTIALS.md` file for login details.

---

## 🛠️ Quick Commands

### Start Server
```bash
cd c:\xampp\htdocs\School\School
php artisan serve
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Run Migrations
```bash
php artisan migrate
```

### Seed Database
```bash
php artisan db:seed
php artisan db:seed --class=GradeSeeder
```

---

## 📱 Mobile Access

Access from mobile devices on same network:
```
http://YOUR_IP_ADDRESS:8000
```

Find your IP: `ipconfig` (Windows) or `ifconfig` (Mac/Linux)

---

*Quick Reference - Keep this handy!*
