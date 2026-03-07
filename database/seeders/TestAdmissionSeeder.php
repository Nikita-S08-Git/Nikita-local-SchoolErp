<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User\Student;
use App\Models\User;

class TestAdmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates test students directly in students table
     */
    public function run(): void
    {
        // Create first test student
        if (!Student::where('email', 'john.doe@example.com')->first()) {
            // Create user first
            $user1 = User::create([
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'password' => bcrypt('password123'),
                'role' => 'student',
            ]);
            
            Student::create([
                'user_id' => $user1->id,
                'admission_number' => 'ADM2026001',
                'roll_number' => 'ADM2026001',
                'first_name' => 'John',
                'middle_name' => '',
                'last_name' => 'Doe',
                'date_of_birth' => '2005-05-15',
                'gender' => 'male',
                'blood_group' => 'B+',
                'religion' => 'Hindu',
                'category' => 'general',
                'aadhar_number' => '123456789012',
                'mobile_number' => '9876543210',
                'email' => 'john.doe@example.com',
                'current_address' => '123 Main Street, Mumbai, Maharashtra',
                'permanent_address' => '123 Main Street, Mumbai, Maharashtra',
                'program_id' => 1,
                'division_id' => 1,
                'academic_session_id' => 1,
                'academic_year' => 'FY',
                'admission_date' => '2026-03-01',
                'student_status' => 'active',
            ]);
            
            echo "Test student 1 created: ADM2026001\n";
        } else {
            echo "Test student 1 already exists\n";
        }
        
        // Create second test student
        if (!Student::where('email', 'jane.smith@example.com')->first()) {
            // Create user first
            $user2 = User::create([
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'password' => bcrypt('password123'),
                'role' => 'student',
            ]);
            
            Student::create([
                'user_id' => $user2->id,
                'admission_number' => 'ADM2026002',
                'roll_number' => 'ADM2026002',
                'first_name' => 'Jane',
                'middle_name' => 'Smith',
                'last_name' => 'Johnson',
                'date_of_birth' => '2004-08-22',
                'gender' => 'female',
                'blood_group' => 'A+',
                'religion' => 'Christian',
                'category' => 'obc',
                'aadhar_number' => '987654321098',
                'mobile_number' => '9876501234',
                'email' => 'jane.smith@example.com',
                'current_address' => '456 Oak Avenue, Pune, Maharashtra',
                'permanent_address' => '456 Oak Avenue, Pune, Maharashtra',
                'program_id' => 2,
                'division_id' => 2,
                'academic_session_id' => 1,
                'academic_year' => 'SY',
                'admission_date' => '2026-03-01',
                'student_status' => 'active',
            ]);
            
            echo "Test student 2 created: ADM2026002\n";
        } else {
            echo "Test student 2 already exists\n";
        }
        
        echo "Test admissions seeded successfully to students table!\n";
    }
}
