# Quick Seeding Reference

## 🚀 Fastest Way to Add Data

### **Windows Users:**
```
Double-click: seed_attendance_timetable.bat
```

### **Command Line:**
```bash
cd c:\xampp\htdocs\School\School
php artisan db:seed --class=AttendanceAndTimetableSeeder
```

---

## 📊 What Gets Created

### Timetable:
- 90-150 schedule entries
- 3 divisions × 6 days × 3-5 periods
- Subjects: Math, English, Science, etc.
- Time: 09:00 - 16:00
- Rooms: 101-120

### Attendance:
- Last 30 days of records
- 85% attendance rate
- Weekdays only
- All active students

---

## ✅ Test URLs

After seeding:
- **Timetable:** http://127.0.0.1:8000/academic/timetable
- **Attendance:** http://127.0.0.1:8000/academic/attendance
- **Reports:** http://127.0.0.1:8000/reports/attendance

---

## 🔧 Individual Seeders

```bash
# Timetable only
php artisan db:seed --class=TimetableSeeder

# Attendance only
php artisan db:seed --class=AttendanceSeeder
```

---

## 📋 Prerequisites

Before seeding, you need:
- ✅ Active divisions
- ✅ Active students
- ✅ Teachers
- ✅ Active academic session

---

## 🔄 Re-seed (Fresh Data)

```bash
php artisan tinker
\App\Models\Attendance\Timetable::truncate();
\App\Models\Academic\Attendance::truncate();
exit

php artisan db:seed --class=AttendanceAndTimetableSeeder
```

---

**For detailed instructions, see: SEEDING_GUIDE.md**
