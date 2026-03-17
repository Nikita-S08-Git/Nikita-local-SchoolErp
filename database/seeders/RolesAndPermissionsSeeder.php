<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ============================================
        // PERMISSIONS
        // ============================================
        
        // Define all modules
        $modules = [
            'dashboard' => 'Dashboard',
            'students' => 'Students',
            'teachers' => 'Teachers',
            'timetable' => 'Timetable',
            'attendance' => 'Attendance',
            'holidays' => 'Holidays',
            'fees' => 'Fees',
            'reports' => 'Reports',
            'notifications' => 'Notifications',
            'settings' => 'Settings',
            'users' => 'User Management',
            'roles' => 'Role & Permission Management',
            'divisions' => 'Divisions',
            'subjects' => 'Subjects',
            'programs' => 'Programs',
            'sessions' => 'Academic Sessions',
            'examinations' => 'Examinations',
            'results' => 'Results',
            'library' => 'Library',
            'staff' => 'Staff',
        ];

        // Define all actions
        $actions = ['view', 'create', 'edit', 'delete'];

        // Create permissions for each module and action
        $permissions = [];
        
        foreach ($modules as $moduleKey => $moduleName) {
            foreach ($actions as $action) {
                $permissionName = "{$moduleKey}.{$action}";
                $permission = Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ], [
                    'name' => $permissionName,
                    'guard_name' => 'web',
                    'group_name' => $moduleName,
                ]);
                $permissions[$moduleKey][] = $permission->id;
            }
            
            // Also create "manage" permission (full access)
            $managePermission = Permission::firstOrCreate([
                'name' => "{$moduleKey}.manage",
                'guard_name' => 'web',
            ], [
                'name' => "{$moduleKey}.manage",
                'guard_name' => 'web',
                'group_name' => $moduleName,
            ]);
            $permissions[$moduleKey][] = $managePermission->id;
        }

        // ============================================
        // ROLES
        // ============================================

        // 1. SUPER ADMIN - Full system access
        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);
        // Give all permissions to super admin
        $superAdmin->givePermissionTo(Permission::all());

        // 2. ADMIN - Manage students, teachers, timetable, attendance, holidays, reports
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $adminPermissions = [
            ...$permissions['dashboard'],
            ...$permissions['students'],
            ...$permissions['teachers'],
            ...$permissions['timetable'],
            ...$permissions['attendance'],
            ...$permissions['holidays'],
            ...$permissions['reports'],
            ...$permissions['notifications'],
            ...$permissions['divisions'],
            ...$permissions['subjects'],
            ...$permissions['programs'],
            ...$permissions['sessions'],
            ...$permissions['examinations'],
            ...$permissions['results'],
            ...$permissions['staff'],
        ];
        $admin->givePermissionTo($adminPermissions);

        // 3. TEACHER - View timetable, mark attendance, view student list
        $teacher = Role::firstOrCreate([
            'name' => 'teacher',
            'guard_name' => 'web',
        ]);
        $teacherPermissions = [
            ...$permissions['dashboard'],
            ...$permissions['timetable'],
            ...$permissions['attendance'],
            ...$permissions['students'],
            ...$permissions['divisions'],
            ...$permissions['examinations'],
            ...$permissions['results'],
            ...$permissions['notifications'],
        ];
        $teacher->givePermissionTo($teacherPermissions);

        // 4. STUDENT - View timetable, view attendance, view fee status
        $student = Role::firstOrCreate([
            'name' => 'student',
            'guard_name' => 'web',
        ]);
        $studentPermissions = [
            ...$permissions['dashboard'],
            ...$permissions['timetable'],
            ...$permissions['attendance'],
            ...$permissions['fees'],
            ...$permissions['notifications'],
            ...$permissions['results'],
        ];
        $student->givePermissionTo($studentPermissions);

        // 5. ACCOUNTANT - Manage fees and generate financial reports
        $accountant = Role::firstOrCreate([
            'name' => 'accountant',
            'guard_name' => 'web',
        ]);
        $accountantPermissions = [
            ...$permissions['dashboard'],
            ...$permissions['fees'],
            ...$permissions['reports'],
            ...$permissions['notifications'],
        ];
        $accountant->givePermissionTo($accountantPermissions);

        // ============================================
        // CREATE DEFAULT USERS
        // ============================================

        // Create Super Admin user
        $superAdminUser = User::firstOrCreate([
            'email' => 'superadmin@schoolerp.com',
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $superAdminUser->assignRole('super_admin');

        // Create Admin user
        $adminUser = User::firstOrCreate([
            'email' => 'admin@schoolerp.com',
        ], [
            'name' => 'System Admin',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $adminUser->assignRole('admin');

        // Create Teacher user
        $teacherUser = User::firstOrCreate([
            'email' => 'teacher@schoolerp.com',
        ], [
            'name' => 'John Teacher',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $teacherUser->assignRole('teacher');

        // Create Student user
        $studentUser = User::firstOrCreate([
            'email' => 'student@schoolerp.com',
        ], [
            'name' => 'Jane Student',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $studentUser->assignRole('student');

        // Create Accountant user
        $accountantUser = User::firstOrCreate([
            'email' => 'accountant@schoolerp.com',
        ], [
            'name' => 'Mike Accountant',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $accountantUser->assignRole('accountant');

        $this->command->info('Roles and Permissions seeded successfully!');
        $this->command->info('Default users created:');
        $this->command->info('  - superadmin@schoolerp.com (password: password)');
        $this->command->info('  - admin@schoolerp.com (password: password)');
        $this->command->info('  - teacher@schoolerp.com (password: password)');
        $this->command->info('  - student@schoolerp.com (password: password)');
        $this->command->info('  - accountant@schoolerp.com (password: password)');
    }
}
