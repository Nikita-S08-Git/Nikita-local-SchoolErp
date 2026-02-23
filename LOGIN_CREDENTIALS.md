# 🔐 SCHOOL ERP SYSTEM - LOGIN CREDENTIALS

## 📋 **ALL USER ACCOUNTS**

### **1. ADMIN ACCOUNT**
```
Email: admin@schoolerp.com
Password: password
Role: Admin (Full System Access)
Dashboard: /dashboard/admin
```

### **2. PRINCIPAL ACCOUNTS**
```
Email: principal@schoolerp.com
Password: password
Role: Principal
Dashboard: /dashboard/principal

OR

Email: principal@school.com
Password: admin123
Role: Principal
Dashboard: /dashboard/principal
```

### **3. TEACHER ACCOUNTS**
```
Email: teacher@schoolerp.com
Password: password
Role: Teacher
Dashboard: /teacher/dashboard

OR

Email: teacher@school.com
Password: password123
Role: Teacher
Dashboard: /teacher/dashboard
```

### **4. ACCOUNTANT ACCOUNT**
```
Email: accountant@schoolerp.com
Password: password
Role: Accountant (Fee Management Access)
Dashboard: /dashboard/accounts_staff
```

### **5. OFFICE STAFF ACCOUNT**
```
Email: office@schoolerp.com
Password: password
Role: Office Staff
Dashboard: /dashboard/office
```

### **6. LIBRARIAN ACCOUNT**
```
Email: librarian@schoolerp.com
Password: password
Role: Librarian (Library Management)
Dashboard: /dashboard/librarian
```

### **7. STUDENT ACCOUNT**
```
Email: student@schoolerp.com
Password: password
Role: Student (View Only)
Dashboard: /dashboard/student
```

---

## 🌐 **ACCESS URLS**

### **Main Application**
- **Login Page**: http://127.0.0.1:8000/login
- **Home**: http://127.0.0.1:8000/

### **Dashboards**
- **Principal**: http://127.0.0.1:8000/dashboard/principal
- **Teacher**: http://127.0.0.1:8000/teacher/dashboard
- **Student**: http://127.0.0.1:8000/dashboard/student
- **Office**: http://127.0.0.1:8000/dashboard/office
- **Accountant**: http://127.0.0.1:8000/dashboard/accounts_staff
- **Librarian**: http://127.0.0.1:8000/dashboard/librarian

### **Key Features**
- **Departments**: http://127.0.0.1:8000/departments
- **Programs**: http://127.0.0.1:8000/academic/programs
- **Students**: http://127.0.0.1:8000/dashboard/students
- **Teachers**: http://127.0.0.1:8000/dashboard/teachers
- **Divisions**: http://127.0.0.1:8000/academic/divisions
- **Subjects**: http://127.0.0.1:8000/academic/subjects
- **Attendance**: http://127.0.0.1:8000/academic/attendance
- **Timetable**: http://127.0.0.1:8000/academic/timetable
- **Fee Management**: http://127.0.0.1:8000/fees/structures

---

## 🚀 **HOW TO START THE PROJECT**

### **Step 1: Start Database**
Make sure MySQL is running on port 3307 (or update .env file)

### **Step 2: Start Laravel Server**
```bash
cd c:\xampp\htdocs\School\School
php artisan serve
```

### **Step 3: Access Application**
Open browser and go to: http://127.0.0.1:8000/login

### **Step 4: Login**
Use any of the credentials above based on the role you want to test

---

## 👥 **ROLE-BASED ACCESS**

### **ADMIN / PRINCIPAL**
✅ Full system access
✅ User management
✅ Department management
✅ Program management
✅ Student management
✅ Teacher management
✅ Fee management
✅ Attendance management
✅ Timetable management
✅ Reports and analytics

### **TEACHER**
✅ View assigned division students
✅ Mark attendance
✅ View timetable
✅ Generate attendance reports
❌ Cannot manage fees
❌ Cannot create users

### **ACCOUNTANT / OFFICE**
✅ Fee structure management
✅ Fee assignment
✅ Payment collection
✅ Outstanding fees tracking
✅ Scholarship management
❌ Cannot manage academic operations

### **LIBRARIAN**
✅ Library management
✅ Book issue/return
❌ Cannot access academic modules

### **STUDENT**
✅ View personal information
✅ View fees and payments
✅ View attendance
✅ View timetable
❌ Read-only access (no modifications)

---

## 🔧 **TESTING RECOMMENDATIONS**

### **Test Authentication Module**
1. Login with principal@school.com / admin123
2. Verify dashboard loads correctly
3. Test logout functionality
4. Try forgot password feature
5. Login with different roles to test access control

### **Test Department Module**
1. Login as principal
2. Go to Departments section
3. Create new department
4. Search departments
5. Filter by status
6. Edit department
7. Try to delete (should check for programs)

### **Test Fee Management**
1. Login as principal or accountant
2. Go to Fee Management
3. Create fee structures
4. Assign fees to students
5. Record payments
6. View outstanding fees
7. Manage scholarships

---

## 📝 **QUICK REFERENCE**

### **Most Common Credentials**
```
Principal: principal@school.com / admin123
Teacher: teacher@school.com / password123
```

### **Alternative Credentials (All use password: "password")**
```
admin@schoolerp.com
principal@schoolerp.com
teacher@schoolerp.com
accountant@schoolerp.com
office@schoolerp.com
librarian@schoolerp.com
student@schoolerp.com
```

---

## ⚠️ **IMPORTANT NOTES**

1. **Database Must Be Running**: Ensure MySQL is running on port 3307
2. **Migrations**: Run `php artisan migrate` if database is empty
3. **Seeders**: Run `php artisan db:seed` to populate test data
4. **Storage Link**: Run `php artisan storage:link` for file uploads
5. **Cache Clear**: Run `php artisan cache:clear` if facing issues

---

## 🎯 **RECOMMENDED TEST FLOW**

1. **Start with Principal Account**
   - Login: principal@school.com / admin123
   - Explore all modules
   - Create test data

2. **Test Teacher Account**
   - Login: teacher@school.com / password123
   - View assigned students
   - Mark attendance

3. **Test Fee Management**
   - Login as principal or accountant
   - Create fee structures
   - Assign and collect fees

4. **Test Student View**
   - Login: student@schoolerp.com / password
   - View personal data
   - Check fees and attendance

---

## 📞 **SUPPORT**

If you encounter any issues:
1. Check database connection in .env file
2. Clear cache: `php artisan cache:clear`
3. Check logs: `storage/logs/laravel.log`
4. Verify all migrations are run
5. Ensure seeders have been executed

---

**System Status**: ✅ Ready for Testing
**Last Updated**: 2025
**Version**: 1.0.0
