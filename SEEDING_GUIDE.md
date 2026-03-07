# Attendance & Timetable Data Seeding Guide

## 📊 Sample Data Overview

This guide will help you populate your database with sample attendance and timetable data for testing.

---

## 🎯 What Will Be Created

### **Timetable Data:**
- ✅ Weekly schedules for 3 divisions
- ✅ 3-5 periods per day
- ✅ Random subjects (Math, English, Science, etc.)
- ✅ Random teacher assignments
- ✅ Room assignments (Room 101-120)
- ✅ Time slots from 09:00 to 16:00

### **Attendance Data:**
- ✅ Last 30 days of attendance records
- ✅ For all active students in 3 divisions
- ✅ 85% attendance rate (realistic)
- ✅ Weekends excluded
- ✅ Linked to active academic session

---

## 🚀 Quick Start (Easiest Method)

### **Option 1: Use Batch Script (Windows)**

Simply double-click:
```
seed_attendance_timetable.bat
```

That's it! The script will:
1. Seed timetable data
2. Seed attendance data
3. Show you the URLs to test

---

## 💻 Manual Method

### **Step 1: Seed Timetable Data**
```bash
cd c:\xampp\htdocs\School\School
php artisan db:seed --class=TimetableSeeder
```

### **Step 2: Seed Attendance Data**
```bash
php artisan db:seed --class=AttendanceSeeder
```

### **Step 3: Seed Both at Once**
```bash
php artisan db:seed --class=AttendanceAndTimetableSeeder
```

---

## ✅ Verification

### **Check Timetable Data:**
```sql
SELECT COUNT(*) FROM timetables;
-- Expected: ~90-150 records (3 divisions × 6 days × 3-5 periods)
```

### **Check Attendance Data:**
```sql
SELECT COUNT(*) FROM attendances;
-- Expected: ~600-1500 records (depends on student count)
```

### **View in Browser:**
1. **Timetable:** http://127.0.0.1:8000/academic/timetable
2. **Attendance:** http://127.0.0.1:8000/academic/attendance
3. **Reports:** http://127.0.0.1:8000/reports/attendance

---

## 📋 Prerequisites

Before seeding, ensure you have:
- ✅ Active divisions (at least 1)
- ✅ Active students (at least 1 per division)
- ✅ Teachers with 'teacher' role
- ✅ Active academic session

### **Check Prerequisites:**
```bash
php artisan tinker
```

Then run:
```php
// Check divisions
\App\Models\Academic\Division::where('is_active', true)->count();

// Check students
\App\Models\User\Student::where('student_status', 'active')->count();

// Check teachers
\App\Models\User::role('teacher')->count();

// Check academic session
\App\Models\Academic\AcademicSession::where('is_active', true)->count();
```

---

## 🔧 Customization

### **Modify Timetable Seeder:**

Edit: `database/seeders/TimetableSeeder.php`

**Change subjects:**
```php
$subjects = ['Your', 'Custom', 'Subjects'];
```

**Change time slots:**
```php
$timeSlots = [
    ['08:00', '09:00'],
    ['09:00', '10:00'],
    // Add more...
];
```

**Change number of divisions:**
```php
foreach ($divisions->take(5) as $division) { // Change 3 to 5
```

---

### **Modify Attendance Seeder:**

Edit: `database/seeders/AttendanceSeeder.php`

**Change date range:**
```php
$startDate = Carbon::now()->subDays(60); // Change 30 to 60
```

**Change attendance rate:**
```php
$status = rand(1, 100) <= 90 ? 'present' : 'absent'; // Change 85 to 90
```

**Include weekends:**
```php
// Remove or comment out:
if ($date->isWeekend()) {
    continue;
}
```

---

## 🎨 Sample Data Details

### **Timetable Sample:**
```
Division: Class 10-A
Day: Monday
09:00-10:00 | Mathematics | Mr. John | Room 105
10:00-11:00 | English     | Ms. Sarah | Room 108
11:00-12:00 | Science     | Dr. Mike  | Room 112
14:00-15:00 | History     | Ms. Lisa  | Room 103
```

### **Attendance Sample:**
```
Date: 2025-01-15
Division: Class 10-A
Total Students: 30
Present: 26 (86.7%)
Absent: 4 (13.3%)
```

---

## 🔄 Re-seeding (Fresh Data)

### **Clear Existing Data:**
```bash
# Clear timetable
php artisan tinker
\App\Models\Attendance\Timetable::truncate();

# Clear attendance
\App\Models\Academic\Attendance::truncate();
```

### **Then Re-seed:**
```bash
php artisan db:seed --class=AttendanceAndTimetableSeeder
```

---

## 🐛 Troubleshooting

### **Error: "No divisions found"**
**Solution:** Seed divisions first
```bash
php artisan db:seed --class=DivisionSeeder
```

### **Error: "No teachers found"**
**Solution:** Seed teachers first
```bash
php artisan db:seed --class=TeacherSeeder
```

### **Error: "No active academic session"**
**Solution:** Seed academic sessions
```bash
php artisan db:seed --class=AcademicSessionSeeder
```

### **Error: "Class not found"**
**Solution:** Clear cache and try again
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

---

## 📊 Testing After Seeding

### **1. View Timetable:**
```
1. Go to: http://127.0.0.1:8000/academic/timetable
2. Select a division from dropdown
3. You should see a weekly schedule with subjects, teachers, and rooms
```

### **2. View Attendance:**
```
1. Go to: http://127.0.0.1:8000/academic/attendance
2. Check "Today's Attendance Summary" section
3. You should see counts for marked, present, absent
```

### **3. Generate Attendance Report:**
```
1. Go to: http://127.0.0.1:8000/reports/attendance
2. Select division and date range (last 30 days)
3. Click "Generate Report"
4. You should see attendance percentages for each student
5. Try downloading PDF and Excel
```

### **4. Mark New Attendance:**
```
1. Go to: http://127.0.0.1:8000/academic/attendance
2. Select session, division, and today's date
3. Click "Proceed to Mark Attendance"
4. You should see the student list
5. Mark attendance and save
```

---

## 📈 Expected Results

After seeding, you should have:

| Item | Count | Details |
|------|-------|---------|
| Timetable Entries | 90-150 | 3 divisions × 6 days × 3-5 periods |
| Attendance Records | 600-1500 | Depends on student count × 30 days |
| Date Range | Last 30 days | Excluding weekends |
| Attendance Rate | ~85% | Realistic attendance pattern |

---

## 🎯 Next Steps

After seeding data:

1. ✅ **Test Timetable Features:**
   - View weekly schedule
   - Edit timetable entries
   - Create new entries
   - Delete entries

2. ✅ **Test Attendance Features:**
   - Mark today's attendance
   - View attendance summary
   - Generate reports
   - Export to PDF/Excel

3. ✅ **Verify Data Integrity:**
   - Check if all students have attendance
   - Verify timetable has no conflicts
   - Ensure dates are correct

---

## 💡 Tips

1. **Seed in Order:**
   - First: Users, Divisions, Sessions
   - Then: Timetable & Attendance

2. **Use Realistic Data:**
   - 85% attendance is realistic
   - 3-5 periods per day is standard
   - Exclude weekends for attendance

3. **Test Thoroughly:**
   - Try all CRUD operations
   - Generate reports
   - Check data consistency

---

## 📞 Support

If you encounter issues:
1. Check prerequisites are met
2. Clear cache and try again
3. Check error logs in `storage/logs/laravel.log`
4. Verify database connection

---

**Status:** ✅ Seeders Ready
**Files Created:**
- `TimetableSeeder.php`
- `AttendanceSeeder.php`
- `AttendanceAndTimetableSeeder.php`
- `seed_attendance_timetable.bat`

**Ready to Seed!** 🚀
