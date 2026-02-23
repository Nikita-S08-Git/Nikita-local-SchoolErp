# Complete Timetable Structure

## 📅 Timetable Overview

Each division gets a **complete weekly schedule** with:
- ✅ **6 Days:** Monday to Saturday
- ✅ **5 Periods per day:** 09:00 to 16:00
- ✅ **Lunch Break:** 13:00 to 14:00
- ✅ **Each period has:**
  - 👨‍🏫 Assigned Teacher
  - 📚 Subject
  - 🏫 Room Number
  - ⏰ Time Slot

---

## 🗓️ Daily Schedule Structure

```
Period 1:  09:00 - 10:00  |  Subject  |  Teacher  |  Room
Period 2:  10:00 - 11:00  |  Subject  |  Teacher  |  Room
Period 3:  11:00 - 12:00  |  Subject  |  Teacher  |  Room
Period 4:  12:00 - 13:00  |  Subject  |  Teacher  |  Room
─────────  13:00 - 14:00  |  LUNCH BREAK
Period 5:  14:00 - 15:00  |  Subject  |  Teacher  |  Room
Period 6:  15:00 - 16:00  |  Subject  |  Teacher  |  Room
```

---

## 📊 Sample Timetable

### **Division: Class 10-A**

#### **Monday**
| Time | Subject | Teacher | Room |
|------|---------|---------|------|
| 09:00-10:00 | Mathematics | Mr. John Smith | Room 101 |
| 10:00-11:00 | English | Ms. Sarah Jones | Room 102 |
| 11:00-12:00 | Science | Dr. Mike Brown | Room 103 |
| 12:00-13:00 | History | Ms. Lisa White | Room 104 |
| 13:00-14:00 | **LUNCH BREAK** | - | - |
| 14:00-15:00 | Geography | Mr. Tom Davis | Room 105 |
| 15:00-16:00 | Physical Education | Coach Mark | Ground |

#### **Tuesday**
| Time | Subject | Teacher | Room |
|------|---------|---------|------|
| 09:00-10:00 | Physics | Dr. Mike Brown | Room 106 |
| 10:00-11:00 | Chemistry | Ms. Emily Clark | Room 107 |
| 11:00-12:00 | Biology | Dr. Anna Lee | Room 108 |
| 12:00-13:00 | Mathematics | Mr. John Smith | Room 101 |
| 13:00-14:00 | **LUNCH BREAK** | - | - |
| 14:00-15:00 | English | Ms. Sarah Jones | Room 102 |
| 15:00-16:00 | Computer Science | Mr. David Tech | Lab 1 |

*...and so on for Wednesday, Thursday, Friday, Saturday*

---

## 🚀 How to Seed Complete Timetable

### **Method 1: Use Enhanced Script**
```
Double-click: seed_complete_timetable.bat
```

### **Method 2: Command Line**
```bash
cd c:\xampp\htdocs\School\School

# Clear old data
php artisan tinker --execute="App\Models\Attendance\Timetable::truncate();"

# Seed new data
php artisan db:seed --class=DetailedTimetableSeeder
```

### **Method 3: Use Original Seeder**
```bash
php artisan db:seed --class=TimetableSeeder
```

---

## 📈 What Gets Created

### **For Each Division:**
- ✅ 6 days × 5 periods = **30 entries per division**
- ✅ Each entry has:
  - Division ID
  - Teacher ID (randomly assigned)
  - Subject (from 10 subjects)
  - Day of week
  - Start time
  - End time
  - Room number

### **Total Entries:**
```
Number of Divisions × 30 = Total Entries

Example:
- 3 divisions × 30 = 90 entries
- 5 divisions × 30 = 150 entries
```

---

## 🎯 Subjects Available

1. Mathematics
2. English
3. Science
4. History
5. Geography
6. Physics
7. Chemistry
8. Biology
9. Computer Science
10. Physical Education

---

## 🏫 Room Assignments

- **Rooms:** 101-120
- **Labs:** Lab 1, Lab 2 (for Computer Science)
- **Ground:** For Physical Education
- Each division gets different room numbers

---

## 👨‍🏫 Teacher Assignments

- Teachers are **randomly assigned** from available teachers
- Each period gets a different teacher
- Same teacher can teach multiple subjects
- Teachers are assigned based on availability

---

## ⏰ Time Slots

| Period | Time | Duration |
|--------|------|----------|
| Period 1 | 09:00-10:00 | 60 min |
| Period 2 | 10:00-11:00 | 60 min |
| Period 3 | 11:00-12:00 | 60 min |
| Period 4 | 12:00-13:00 | 60 min |
| **Lunch** | **13:00-14:00** | **60 min** |
| Period 5 | 14:00-15:00 | 60 min |
| Period 6 | 15:00-16:00 | 60 min |

---

## 🔍 View Timetable

After seeding:

1. **Go to:** http://127.0.0.1:8000/academic/timetable
2. **Select Division** from dropdown
3. **View Weekly Grid** with all details

### **What You'll See:**
```
┌─────────┬──────────┬──────────┬───────────┬──────────┬────────┬──────────┐
│  Time   │  Monday  │ Tuesday  │ Wednesday │ Thursday │ Friday │ Saturday │
├─────────┼──────────┼──────────┼───────────┼──────────┼────────┼──────────┤
│ 09:00   │   Math   │ Physics  │  English  │ Science  │  Math  │ History  │
│ 10:00   │ Teacher  │ Teacher  │  Teacher  │ Teacher  │Teacher │ Teacher  │
│         │ Room 101 │ Room 106 │ Room 102  │ Room 103 │Rm 101  │ Room 104 │
├─────────┼──────────┼──────────┼───────────┼──────────┼────────┼──────────┤
│ 10:00   │ English  │Chemistry │   Math    │ History  │Science │Geography │
│ 11:00   │ Teacher  │ Teacher  │  Teacher  │ Teacher  │Teacher │ Teacher  │
│         │ Room 102 │ Room 107 │ Room 101  │ Room 104 │Rm 103  │ Room 105 │
└─────────┴──────────┴──────────┴───────────┴──────────┴────────┴──────────┘
```

---

## ✏️ Edit Timetable

You can:
- ✅ **Edit** any period (click ✏️)
- ✅ **Delete** periods (click 🗑️)
- ✅ **Add** new periods
- ✅ **Change** teacher, subject, room, time

---

## 🔄 Re-seed Timetable

To create fresh timetable data:

```bash
# Clear existing
php artisan tinker --execute="App\Models\Attendance\Timetable::truncate();"

# Re-seed
php artisan db:seed --class=DetailedTimetableSeeder
```

Or use: `seed_complete_timetable.bat`

---

## 📊 Database Structure

```sql
Table: timetables
├── id (Primary Key)
├── division_id (Foreign Key → divisions)
├── teacher_id (Foreign Key → users)
├── subject (VARCHAR)
├── day_of_week (ENUM: Monday-Saturday)
├── start_time (TIME: HH:MM)
├── end_time (TIME: HH:MM)
├── room (VARCHAR)
├── created_at
└── updated_at
```

---

## ✅ Verification

After seeding, verify:

```sql
-- Check total entries
SELECT COUNT(*) FROM timetables;

-- Check by division
SELECT d.division_name, COUNT(*) as periods
FROM timetables t
JOIN divisions d ON t.division_id = d.id
GROUP BY d.division_name;

-- Check by day
SELECT day_of_week, COUNT(*) as periods
FROM timetables
GROUP BY day_of_week;
```

Expected:
- **30 entries per division** (6 days × 5 periods)
- **5 entries per day per division**

---

## 🎯 Next Steps

After seeding timetable:

1. ✅ **View** weekly schedules
2. ✅ **Edit** periods as needed
3. ✅ **Add** more periods
4. ✅ **Assign** specific teachers to subjects
5. ✅ **Customize** time slots
6. ✅ **Print** timetables (PDF coming soon)

---

**Status:** ✅ Complete Timetable Structure Ready
**Entries per Division:** 30 (6 days × 5 periods)
**Total Subjects:** 10
**Time Range:** 09:00 - 16:00 (with lunch break)

**Ready to Use!** 🚀
