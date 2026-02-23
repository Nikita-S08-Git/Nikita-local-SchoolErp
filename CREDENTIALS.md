# 🔐 CREDENTIALS & ACCESS INFORMATION - SchoolERP

## 📊 DATABASE CREDENTIALS

### MySQL Database
```
Host: 127.0.0.1
Port: 3306
Database: schoolerp
Username: root
Password: (empty)
```

### .env Configuration
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=schoolerp
DB_USERNAME=root
DB_PASSWORD=
```

---

## 👤 DEFAULT USER ACCOUNTS

### Super Admin
```
Email: admin@schoolerp.com
Password: password
Role: admin
```

### Principal
```
Email: principal@schoolerp.com
Password: password
Role: principal
```

### Teacher
```
Email: teacher@schoolerp.com
Password: password
Role: teacher
```

### Accountant
```
Email: accountant@schoolerp.com
Password: password
Role: accountant
```

### Librarian
```
Email: librarian@schoolerp.com
Password: password
Role: librarian
```

### Student
```
Email: student@schoolerp.com
Password: password
Role: student
```

---

## 🚀 APPLICATION ACCESS

### Local Development
```
URL: http://localhost:8000
Login: http://localhost:8000/login
```

### After XAMPP Setup
```
URL: http://localhost/School/School/public
Login: http://localhost/School/School/public/login
```

---

## 🔧 CREATE DEFAULT USERS (Run This Seeder)

Create file: `database/seeders/DefaultUsersSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DefaultUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles if not exist
        $roles = ['admin', 'principal', 'teacher', 'accountant', 'librarian', 'student'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@schoolerp.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        // Principal
        $principal = User::firstOrCreate(
            ['email' => 'principal@schoolerp.com'],
            [
                'name' => 'Dr. Principal',
                'password' => Hash::make('password'),
            ]
        );
        $principal->assignRole('principal');

        // Teacher
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@schoolerp.com'],
            [
                'name' => 'John Teacher',
                'password' => Hash::make('password'),
            ]
        );
        $teacher->assignRole('teacher');

        // Accountant
        $accountant = User::firstOrCreate(
            ['email' => 'accountant@schoolerp.com'],
            [
                'name' => 'Mary Accountant',
                'password' => Hash::make('password'),
            ]
        );
        $accountant->assignRole('accountant');

        // Librarian
        $librarian = User::firstOrCreate(
            ['email' => 'librarian@schoolerp.com'],
            [
                'name' => 'Sarah Librarian',
                'password' => Hash::make('password'),
            ]
        );
        $librarian->assignRole('librarian');

        // Student
        $student = User::firstOrCreate(
            ['email' => 'student@schoolerp.com'],
            [
                'name' => 'Test Student',
                'password' => Hash::make('password'),
            ]
        );
        $student->assignRole('student');
    }
}
```

**Run Seeder:**
```bash
php artisan db:seed --class=DefaultUsersSeeder
```

---

## 🔑 CHANGE DEFAULT PASSWORDS

After first login, change passwords using:

```bash
php artisan tinker
```

```php
$user = User::where('email', 'admin@schoolerp.com')->first();
$user->password = Hash::make('new_secure_password');
$user->save();
```

---

## 📱 API CREDENTIALS (If Using API)

### Sanctum Token
```
Generate token after login:
$token = $user->createToken('api-token')->plainTextToken;
```

### API Base URL
```
http://localhost:8000/api
```

---

## 🔐 SECURITY RECOMMENDATIONS

### Production Environment
1. Change all default passwords
2. Use strong passwords (min 12 characters)
3. Enable 2FA (if implemented)
4. Set proper file permissions
5. Use HTTPS
6. Configure firewall

### Password Policy
```
Minimum Length: 8 characters
Must Include: 
- Uppercase letter
- Lowercase letter
- Number
- Special character
```

---

## 🗄️ BACKUP CREDENTIALS

### Database Backup
```bash
# Export database
mysqldump -u root -p schoolerp > backup.sql

# Import database
mysql -u root -p schoolerp < backup.sql
```

### Application Backup
```bash
# Backup entire application
xcopy /E /I c:\xampp\htdocs\School\School c:\backups\schoolerp
```

---

## 🔧 TROUBLESHOOTING

### Forgot Password
```bash
php artisan tinker
$user = User::where('email', 'admin@schoolerp.com')->first();
$user->password = Hash::make('newpassword');
$user->save();
```

### Reset All Passwords
```bash
php artisan db:seed --class=DefaultUsersSeeder
```

### Check User Roles
```bash
php artisan tinker
User::with('roles')->get();
```

---

## 📞 SUPPORT

### Documentation Files
- QUICK_SETUP.md - Setup instructions
- IMPLEMENTATION_STATUS.md - Current status
- FINAL_IMPLEMENTATION_GUIDE.md - Complete guide

### Common Issues
1. **Can't login:** Check if user exists and has correct role
2. **403 Error:** Check role permissions
3. **Database error:** Verify MySQL is running

---

## ✅ QUICK TEST

After setup, test with these credentials:

```
1. Login as Admin:
   Email: admin@schoolerp.com
   Password: password

2. Access Dashboard:
   http://localhost:8000/dashboard/principal

3. Test New Modules:
   - Examinations: /examinations
   - Library: /library/books
   - Staff: /staff
   - Leaves: /leaves
```

---

**⚠️ IMPORTANT: Change all default passwords in production!**
