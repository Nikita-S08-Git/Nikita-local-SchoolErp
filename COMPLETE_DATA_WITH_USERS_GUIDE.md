# 🎓 COMPLETE SCHOOL ERP DATA - WITH USERS

## ✅ **ALL DATA IN ONE FILE!**

This SQL script adds **EVERYTHING**:
- ✅ Departments (4)
- ✅ Programs (8)
- ✅ Divisions (10)
- ✅ Subjects (20)
- ✅ **Users with password: password**
- ✅ Teachers (10)
- ✅ Holidays (15)
- ✅ Timetables (50)

---

## 🔐 **LOGIN CREDENTIALS**

**All passwords are: `password`**

| Email | Password | Role |
|-------|----------|------|
| **principal@schoolerp.com** | password | Principal |
| **teacher@schoolerp.com** | password | Teacher |
| **class.teacher@schoolerp.com** | password | Class Teacher |
| **hod.commerce@schoolerp.com** | password | HOD Commerce |
| **hod.science@schoolerp.com** | password | HOD Science |
| math.teacher@schoolerp.com | password | Math Teacher |
| english.teacher@schoolerp.com | password | English Teacher |
| physics.teacher@schoolerp.com | password | Physics Teacher |
| chemistry.teacher@schoolerp.com | password | Chemistry Teacher |
| biology.teacher@schoolerp.com | password | Biology Teacher |

---

## 🚀 **HOW TO RUN**

### **Step 1: Open phpMyAdmin**
1. Go to: **http://localhost/phpmyadmin**
2. Select database: **`schoolerp`**

### **Step 2: Run SQL Script**
1. Click **SQL** tab
2. Click **Choose File**
3. Select: `c:\xampp\htdocs\School\School\database\complete_data_with_users.sql`
4. Click **Go**

### **Step 3: Verify**
You should see:
```
✅ COMPLETE DATA INSERTION DONE!

Departments: 4
Programs: 8
Divisions: 10
Subjects: 20
Users: 10
Holidays: 15
Timetables: 50
```

---

## 📊 **WHAT'S INCLUDED**

### **Academic Structure:**
- ✅ 4 Departments (Commerce, Science, Arts, Management)
- ✅ 8 Programs (B.Com, B.Sc, B.A, M.Com, M.Sc, M.A, MBA, BBA)
- ✅ 10 Divisions (A, B, C for each program)
- ✅ 20 Subjects (All programs covered)

### **Users & Roles:**
- ✅ 1 Principal
- ✅ 1 Teacher
- ✅ 1 Class Teacher
- ✅ 2 HODs (Commerce, Science)
- ✅ 5 Subject Teachers (Math, English, Physics, Chemistry, Biology)

### **Teacher Profiles:**
- ✅ Employee IDs
- ✅ Phone numbers
- ✅ Qualifications
- ✅ Experience years
- ✅ Specializations

### **Holidays & Events:**
- ✅ 3 National Holidays
- ✅ 7 School Holidays
- ✅ 3 Programs/Events
- ✅ 2 Festival Breaks

### **Timetables:**
- ✅ Division A: Complete week (30 periods)
- ✅ Division B: Partial week (20 periods)

---

## 🎯 **TEST LOGIN**

### **1. Principal Login**
```
URL: http://127.0.0.1:8000/login
Email: principal@schoolerp.com
Password: password
```

### **2. Teacher Login**
```
URL: http://127.0.0.1:8000/login
Email: teacher@schoolerp.com
Password: password
```

### **3. After Login:**
- ✅ View Dashboard
- ✅ View Timetables
- ✅ Mark Attendance
- ✅ Manage Students

---

## ✅ **VERIFICATION QUERIES**

Run these in phpMyAdmin SQL tab:

```sql
-- Check all users
SELECT name, email FROM users WHERE email LIKE '%@schoolerp.com';

-- Check holidays
SELECT title, start_date, type FROM holidays;

-- Check timetables
SELECT * FROM timetables LIMIT 10;

-- Check departments and programs
SELECT d.name as department, p.name as program 
FROM departments d 
INNER JOIN programs p ON d.id = p.department_id;
```

---

## 🎉 **ALL DATA READY!**

**Total Records Added:**
- Departments: 4
- Programs: 8
- Divisions: 10
- Subjects: 20
- Users: 10
- Teacher Profiles: 10
- Teacher Assignments: 16
- Holidays: 15
- Timetables: 50

**Grand Total: 143+ records!**

---

## 📁 **FILE LOCATION**

**SQL Script:** `c:\xampp\htdocs\School\School\database\complete_data_with_users.sql`

---

## 🔧 **TROUBLESHOOTING**

### **"Duplicate entry" error**
This is normal - data already exists. The script handles this with `ON DUPLICATE KEY UPDATE`.

### **"Table doesn't exist"**
Run `complete_setup.sql` first to create tables, then run this file.

### **Can't login**
Make sure:
1. Users were created (check with verification query)
2. Password is: `password` (all lowercase)
3. You're using correct email

---

**Just run the SQL file and everything will be set up!** 🎓✨

**All users can login with password: `password`**
