# 🚀 GLOBAL TIMETABLE ENHANCEMENT - DATABASE SETUP

## ⚠️ **IMPORTANT: MANUAL SETUP REQUIRED**

The database is currently empty. Follow these steps to set up everything:

---

## 📋 **SETUP STEPS**

### **Step 1: Run Main Migrations First**

Since the database is empty, you need to run the main project migrations first:

```bash
cd c:\xampp\htdocs\School\School

# This will create all base tables
php artisan migrate
```

**If you get errors**, run the SQL script manually:

### **Step 2: Run SQL Script (Alternative)**

1. Open **phpMyAdmin** (http://localhost/phpmyadmin)
2. Select database: `schoolerp`
3. Click **SQL** tab
4. Copy and paste contents from: `database/manual_migrations.sql`
5. Click **Go**

### **Step 3: Run Seeder**

After migrations are complete:

```bash
php artisan db:seed --class=GlobalTimetableSeeder
```

This will add:
- ✅ 10 holidays/programs
- ✅ 180+ timetable entries
- ✅ 50+ program participants
- ✅ Update attendance records

---

## 📊 **WHAT WILL BE CREATED**

### **Tables:**
- ✅ `timetables` - Enhanced timetable entries
- ✅ `holidays` - Holidays and programs
- ✅ `program_participants` - Program participants
- ✅ `attendance` - Updated with division_id, subject_id

### **Sample Data:**
- ✅ 10 Holidays (Republic Day, Independence Day, etc.)
- ✅ 5 Programs/Events (Sports Day, Annual Day, etc.)
- ✅ 180+ Timetable entries (for all divisions)
- ✅ 50+ Program participants

---

## 🎯 **VERIFY INSTALLATION**

After setup, verify in phpMyAdmin:

```sql
-- Check holidays
SELECT * FROM holidays;

-- Check timetables
SELECT * FROM timetables LIMIT 10;

-- Check program participants
SELECT * FROM program_participants;

-- Check updated attendance
SELECT COUNT(*) FROM attendance WHERE division_id IS NOT NULL;
```

---

## 🔧 **TROUBLESHOOTING**

### **If migrations fail:**
1. Open phpMyAdmin
2. Run: `DROP TABLE IF EXISTS migrations;`
3. Try: `php artisan migrate` again

### **If tables don't exist:**
Run the complete SQL script from `database/manual_migrations.sql`

### **If seeder fails:**
Make sure you have:
- ✅ Divisions table with data
- ✅ Subjects table with data
- ✅ Users with teacher roles

---

## 📞 **QUICK COMMANDS**

```bash
# Check migration status
php artisan migrate:status

# Run seeder
php artisan db:seed --class=GlobalTimetableSeeder

# View holidays
php artisan tinker
>>> App\Models\Holiday::count()

# View timetables
php artisan tinker
>>> App\Models\Academic\Timetable::count()
```

---

## ✅ **COMPLETION CHECKLIST**

- [ ] Run `php artisan migrate`
- [ ] OR run SQL script in phpMyAdmin
- [ ] Run `php artisan db:seed --class=GlobalTimetableSeeder`
- [ ] Verify tables exist in phpMyAdmin
- [ ] Check sample data is present
- [ ] Test holiday creation in admin panel
- [ ] Test timetable creation
- [ ] Test attendance with holiday check

---

## 🎉 **AFTER SETUP**

Access the new features:

**Holidays:**
- URL: `/academic/holidays`
- Create: `/academic/holidays/create`
- Calendar: `/academic/holidays/calendar/view`

**Timetables:**
- URL: `/academic/timetable`
- Create: `/academic/timetable/create`
- Teacher View: `/academic/timetable/teacher`

**Attendance:**
- URL: `/teacher/attendance`
- System will check for holidays automatically!

---

**Documentation:** `GLOBAL_TIMETABLE_ENHANCEMENT.md`  
**SQL Script:** `database/manual_migrations.sql`  
**Seeder:** `database/seeders/GlobalTimetableSeeder.php`
