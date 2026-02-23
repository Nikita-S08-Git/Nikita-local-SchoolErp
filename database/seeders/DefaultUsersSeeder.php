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
        $roles = ['admin', 'principal', 'teacher', 'accountant', 'librarian', 'student', 'office'];
        
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
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Principal
        $principal = User::firstOrCreate(
            ['email' => 'principal@schoolerp.com'],
            [
                'name' => 'Dr. Principal',
                'password' => Hash::make('password'),
            ]
        );
        if (!$principal->hasRole('principal')) {
            $principal->assignRole('principal');
        }

        // Teacher
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@schoolerp.com'],
            [
                'name' => 'John Teacher',
                'password' => Hash::make('password'),
            ]
        );
        if (!$teacher->hasRole('teacher')) {
            $teacher->assignRole('teacher');
        }

        // Accountant
        $accountant = User::firstOrCreate(
            ['email' => 'accountant@schoolerp.com'],
            [
                'name' => 'Mary Accountant',
                'password' => Hash::make('password'),
            ]
        );
        if (!$accountant->hasRole('accountant')) {
            $accountant->assignRole('accountant');
        }

        // Librarian
        $librarian = User::firstOrCreate(
            ['email' => 'librarian@schoolerp.com'],
            [
                'name' => 'Sarah Librarian',
                'password' => Hash::make('password'),
            ]
        );
        if (!$librarian->hasRole('librarian')) {
            $librarian->assignRole('librarian');
        }

        // Student
        $student = User::firstOrCreate(
            ['email' => 'student@schoolerp.com'],
            [
                'name' => 'Test Student',
                'password' => Hash::make('password'),
            ]
        );
        if (!$student->hasRole('student')) {
            $student->assignRole('student');
        }

        // Office Staff
        $office = User::firstOrCreate(
            ['email' => 'office@schoolerp.com'],
            [
                'name' => 'Office Staff',
                'password' => Hash::make('password'),
            ]
        );
        if (!$office->hasRole('office')) {
            $office->assignRole('office');
        }

        $this->command->info('Default users created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@schoolerp.com / password');
        $this->command->info('Principal: principal@schoolerp.com / password');
        $this->command->info('Teacher: teacher@schoolerp.com / password');
        $this->command->info('Accountant: accountant@schoolerp.com / password');
        $this->command->info('Librarian: librarian@schoolerp.com / password');
        $this->command->info('Student: student@schoolerp.com / password');
        $this->command->info('Office: office@schoolerp.com / password');
    }
}
