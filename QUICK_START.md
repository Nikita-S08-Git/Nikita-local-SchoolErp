# SchoolERP - Quick Start Guide

## 🚀 Get Started in 5 Minutes

### Prerequisites
- PHP 8.1+
- MySQL 8.0+ (running on port 3307)
- Composer
- Node.js & NPM

---

## Step 1: Database Setup

```bash
# Run migrations
php artisan migrate

# Seed database with test data
php artisan db:seed
```

**Test Data Includes**:
- 2 Programs (B.Com, B.Sc)
- 3 Divisions
- 15 Students
- 4 Teachers
- 2 Principals
- Fee structures

---

## Step 2: Storage Setup

```bash
# Create symbolic link for file uploads
php artisan storage:link
```

---

## Step 3: Razorpay Setup (Optional)

```bash
# Install Razorpay SDK
composer require razorpay/razorpay
```

Add to `.env`:
```env
RAZORPAY_KEY=rzp_test_xxxxxxxxxx
RAZORPAY_SECRET=xxxxxxxxxxxxxxxxxx
RAZORPAY_WEBHOOK_SECRET=xxxxxxxxxxxxxxxxxx
```

Get credentials from: https://dashboard.razorpay.com/app/keys

---

## Step 4: Start Server

```bash
php artisan serve
```

Access at: http://localhost:8000

---

## Step 5: Login

### Principal Account
```
Email: principal@school.com
Password: admin123
```

### Teacher Account
```
Email: teacher@school.com
Password: password123
```

### Alternative Accounts
```
Email: admin@schoolerp.com
Password: password
```

---

## 📋 Quick Navigation

### Admin/Principal Dashboard
After login, you'll see:

**Sidebar Menu**:
- 📊 Dashboard - Statistics overview
- 👥 Students - Student management
- 👨‍🏫 Teachers - Teacher management
- 🎓 Programs - Degree programs
- 📚 Subjects - Course subjects
- 🏫 Divisions - Class sections
- ✅ Attendance - Attendance tracking
- 📅 Timetable - Class schedules
- 📆 Academic Sessions - Year management
- 💰 Fees - Fee management

---

## 🎯 Common Tasks

### Add New Student
```
Students → Add Student → Fill Form → 
Select Program & Division → Upload Photo → 
Submit → Add Guardian
```

### Create Fee Structure
```
Fees → Fee Structures → Create → 
Select Program → Enter Amount → 
Set Installments → Save
```

### Assign Fees to Students
```
Fees → Assignments → Filter Students → 
Select Students → Choose Fee Structure → 
Apply Discount → Assign
```

### Collect Fee Payment
```
Fees → Payments → Create → 
Search Student → Enter Amount → 
Select Mode → Submit → Print Receipt
```

### Create Division
```
Academic → Divisions → Create → 
Select Program & Session → Enter Name → 
Set Capacity → Assign Teacher → Save
```

---

## 📊 Key Features

### 1. Student Management
- Auto-generated admission & roll numbers
- Document upload (photo, signature, certificates)
- Multiple guardians per student
- Comprehensive profile view

### 2. Division Management
- Capacity tracking with visual indicators
- Bulk student assignment
- Class teacher assignment
- Classroom allocation

### 3. Fee Management
- Program-wise fee structures
- Individual & bulk assignment
- Manual payment collection
- **Online payment (Razorpay)**
- Receipt generation (PDF)
- Outstanding tracking

### 4. Academic Structure
- Department management
- Program management with seat tracking
- Academic session (single active)
- Division capacity management

---

## 🔧 Configuration

### Database Connection
Check `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307  # Note: Not default 3306
DB_DATABASE=school_erp
DB_USERNAME=root
DB_PASSWORD=
```

### File Upload Limits
In `php.ini`:
```ini
upload_max_filesize = 10M
post_max_size = 10M
```

### Session Timeout
In `.env`:
```env
SESSION_LIFETIME=120  # minutes
```

---

## 📁 Important Directories

### Uploads
```
storage/app/public/uploads/
├── students/
│   ├── photos/
│   ├── signatures/
│   └── documents/
└── guardians/
    └── photos/
```

### Views
```
resources/views/
├── dashboard/students/
├── academic/
│   ├── programs/
│   ├── sessions/
│   └── divisions/
└── fees/
    ├── structures/
    ├── payments/
    └── outstanding/
```

---

## 🐛 Troubleshooting

### Issue: Storage link not working
```bash
# Remove existing link
rm public/storage

# Recreate link
php artisan storage:link
```

### Issue: Database connection failed
- Check MySQL is running on port 3307
- Verify credentials in `.env`
- Run: `php artisan config:clear`

### Issue: Razorpay not working
- Verify API keys in `.env`
- Check Razorpay SDK installed
- Test with test mode first

### Issue: File upload fails
- Check storage permissions: `chmod -R 775 storage`
- Verify `upload_max_filesize` in php.ini
- Check disk space

---

## 📚 Documentation

Detailed documentation available:

1. **SYSTEM_OVERVIEW.md** - Complete system overview
2. **PROGRAM_SESSION_REPORT.md** - Program & Session modules
3. **DIVISION_MODULE_DOCUMENTATION.md** - Division management
4. **STUDENT_MODULE_DOCUMENTATION.md** - Student management
5. **FEE_MODULE_DOCUMENTATION.md** - Fee management
6. **RAZORPAY_INTEGRATION.md** - Online payment setup

---

## ✅ Verification Checklist

After setup, verify:

- [ ] Can login as principal
- [ ] Can view students list
- [ ] Can create new student
- [ ] Can upload student photo
- [ ] Can add guardian
- [ ] Can create division
- [ ] Can assign students to division
- [ ] Can create fee structure
- [ ] Can assign fees to student
- [ ] Can collect payment
- [ ] Can generate receipt
- [ ] Can view outstanding fees

---

## 🎓 Sample Data

### Programs
- B.Com (Bachelor of Commerce)
- B.Sc (Bachelor of Science)

### Divisions
- FY-A (First Year Section A)
- SY-B (Second Year Section B)
- TY-C (Third Year Section C)

### Fee Structures
- Tuition Fee: ₹40,000
- Library Fee: ₹2,000
- Sports Fee: ₹1,500
- Lab Fee: ₹5,000

### Students
- 15 test students with complete profiles
- Assigned to different programs and divisions
- Some with fees assigned

---

## 🚀 Next Steps

1. **Customize Settings**
   - Update school name in views
   - Configure email settings
   - Set up SMS gateway (optional)

2. **Add Real Data**
   - Create actual programs
   - Add real students
   - Configure fee structures

3. **Configure Razorpay**
   - Complete KYC
   - Get live API keys
   - Set up webhook

4. **Train Staff**
   - Admin training
   - Office staff training
   - Teacher training

5. **Go Live**
   - Backup database
   - Switch to production
   - Monitor system

---

## 📞 Support

For issues or questions:
- Check documentation files
- Review Laravel logs: `storage/logs/laravel.log`
- Check database migrations
- Verify .env configuration

---

## 🎉 You're Ready!

The system is fully operational. Start by:
1. Login as principal
2. Explore the dashboard
3. Create a test student
4. Assign fees
5. Collect payment

**Happy Managing! 🚀**

---

**Version**: 1.0.0  
**Last Updated**: February 2026  
**Status**: Production Ready ✅
