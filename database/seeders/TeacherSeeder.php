<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    public function run()
    {
        // Create teacher role if it doesn't exist
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        
        // Create test teacher
        $teacher = User::create([
            'name' => 'John Teacher',
            'email' => 'teacher@school.com',
            'password' => Hash::make('password123'),
        ]);
        
        $teacher->assignRole('teacher');
        
        echo "Teacher created:\n";
        echo "Email: teacher@school.com\n";
        echo "Password: password123\n";
    }
}