<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\User\Student;
use App\Models\Academic\Division;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== Seeding Students ===');

        // Get student role
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        // Get active divisions
        $divisions = Division::where('is_active', true)->get();
        if ($divisions->isEmpty()) {
            $this->command->error('No divisions found!');
            return;
        }

        // Student data for each division
        $studentsData = [
            'BCOM-A' => [
                ['name' => 'Rahul Sharma', 'admission_no' => '2025/BCOM/A/001', 'roll' => '001'],
                ['name' => 'Priya Patel', 'admission_no' => '2025/BCOM/A/002', 'roll' => '002'],
                ['name' => 'Amit Kumar', 'admission_no' => '2025/BCOM/A/003', 'roll' => '003'],
                ['name' => 'Sneha Reddy', 'admission_no' => '2025/BCOM/A/004', 'roll' => '004'],
                ['name' => 'Vikram Singh', 'admission_no' => '2025/BCOM/A/005', 'roll' => '005'],
                ['name' => 'Anjali Gupta', 'admission_no' => '2025/BCOM/A/006', 'roll' => '006'],
                ['name' => 'Rohan Mehta', 'admission_no' => '2025/BCOM/A/007', 'roll' => '007'],
                ['name' => 'Pooja Joshi', 'admission_no' => '2025/BCOM/A/008', 'roll' => '008'],
                ['name' => 'Karan Desai', 'admission_no' => '2025/BCOM/A/009', 'roll' => '009'],
                ['name' => 'Neha Chopra', 'admission_no' => '2025/BCOM/A/010', 'roll' => '010'],
            ],
            'BCOM-B' => [
                ['name' => 'Arjun Verma', 'admission_no' => '2025/BCOM/B/001', 'roll' => '001'],
                ['name' => 'Divya Nair', 'admission_no' => '2025/BCOM/B/002', 'roll' => '002'],
                ['name' => 'Aditya Rao', 'admission_no' => '2025/BCOM/B/003', 'roll' => '003'],
                ['name' => 'Isha Malhotra', 'admission_no' => '2025/BCOM/B/004', 'roll' => '004'],
                ['name' => 'Siddharth Bose', 'admission_no' => '2025/BCOM/B/005', 'roll' => '005'],
                ['name' => 'Kavya Iyer', 'admission_no' => '2025/BCOM/B/006', 'roll' => '006'],
                ['name' => 'Abhishek Kapoor', 'admission_no' => '2025/BCOM/B/007', 'roll' => '007'],
                ['name' => 'Ritu Sinha', 'admission_no' => '2025/BCOM/B/008', 'roll' => '008'],
                ['name' => 'Nikhil Saxena', 'admission_no' => '2025/BCOM/B/009', 'roll' => '009'],
                ['name' => 'Tanya Agarwal', 'admission_no' => '2025/BCOM/B/010', 'roll' => '010'],
            ],
            'BCOM-C' => [
                ['name' => 'Manish Tiwari', 'admission_no' => '2025/BCOM/C/001', 'roll' => '001'],
                ['name' => 'Shruti Pandey', 'admission_no' => '2025/BCOM/C/002', 'roll' => '002'],
                ['name' => 'Gaurav Mishra', 'admission_no' => '2025/BCOM/C/003', 'roll' => '003'],
                ['name' => 'Deepika Menon', 'admission_no' => '2025/BCOM/C/004', 'roll' => '004'],
                ['name' => 'Harsh Bhatt', 'admission_no' => '2025/BCOM/C/005', 'roll' => '005'],
                ['name' => 'Meera Kulkarni', 'admission_no' => '2025/BCOM/C/006', 'roll' => '006'],
                ['name' => 'Yash Thakur', 'admission_no' => '2025/BCOM/C/007', 'roll' => '007'],
                ['name' => 'Simran Kohli', 'admission_no' => '2025/BCOM/C/008', 'roll' => '008'],
                ['name' => 'Varun Bajaj', 'admission_no' => '2025/BCOM/C/009', 'roll' => '009'],
                ['name' => 'Preeti Dubey', 'admission_no' => '2025/BCOM/C/010', 'roll' => '010'],
            ],
            'BSC-A' => [
                ['name' => 'Akash Pillai', 'admission_no' => '2025/BSC/A/001', 'roll' => '001'],
                ['name' => 'Lakshmi Menon', 'admission_no' => '2025/BSC/A/002', 'roll' => '002'],
                ['name' => 'Karthik Reddy', 'admission_no' => '2025/BSC/A/003', 'roll' => '003'],
                ['name' => 'Swathi Gowda', 'admission_no' => '2025/BSC/A/004', 'roll' => '004'],
                ['name' => 'Ravi Teja', 'admission_no' => '2025/BSC/A/005', 'roll' => '005'],
                ['name' => 'Harini Rao', 'admission_no' => '2025/BSC/A/006', 'roll' => '006'],
                ['name' => 'Suresh Babu', 'admission_no' => '2025/BSC/A/007', 'roll' => '007'],
                ['name' => 'Janaki Nath', 'admission_no' => '2025/BSC/A/008', 'roll' => '008'],
                ['name' => 'Bala Krishnan', 'admission_no' => '2025/BSC/A/009', 'roll' => '009'],
                ['name' => 'Radha Devi', 'admission_no' => '2025/BSC/A/010', 'roll' => '010'],
            ],
            'BSC-B' => [
                ['name' => 'Pradeep Kumar', 'admission_no' => '2025/BSC/B/001', 'roll' => '001'],
                ['name' => 'Vidya Sagar', 'admission_no' => '2025/BSC/B/002', 'roll' => '002'],
                ['name' => 'Mohan Das', 'admission_no' => '2025/BSC/B/003', 'roll' => '003'],
                ['name' => 'Hema Mukherjee', 'admission_no' => '2025/BSC/B/004', 'roll' => '004'],
                ['name' => 'Dinesh Babu', 'admission_no' => '2025/BSC/B/005', 'roll' => '005'],
                ['name' => 'Rekha Das', 'admission_no' => '2025/BSC/B/006', 'roll' => '006'],
                ['name' => 'Vinod Menon', 'admission_no' => '2025/BSC/B/007', 'roll' => '007'],
                ['name' => 'Usha Rani', 'admission_no' => '2025/BSC/B/008', 'roll' => '008'],
                ['name' => 'Suresh Nair', 'admission_no' => '2025/BSC/B/009', 'roll' => '009'],
                ['name' => 'Geeta Pillai', 'admission_no' => '2025/BSC/B/010', 'roll' => '010'],
            ],
            'BSC-C' => [
                ['name' => 'Anand Kumar', 'admission_no' => '2025/BSC/C/001', 'roll' => '001'],
                ['name' => 'Jyoti Singh', 'admission_no' => '2025/BSC/C/002', 'roll' => '002'],
                ['name' => 'Rajesh Yadav', 'admission_no' => '2025/BSC/C/003', 'roll' => '003'],
                ['name' => 'Sunita Devi', 'admission_no' => '2025/BSC/C/004', 'roll' => '004'],
                ['name' => 'Manoj Kumar', 'admission_no' => '2025/BSC/C/005', 'roll' => '005'],
                ['name' => 'Pinky Kumari', 'admission_no' => '2025/BSC/C/006', 'roll' => '006'],
                ['name' => 'Sanjay Paswan', 'admission_no' => '2025/BSC/C/007', 'roll' => '007'],
                ['name' => 'Anita Mandal', 'admission_no' => '2025/BSC/C/008', 'roll' => '008'],
                ['name' => 'Rakesh Giri', 'admission_no' => '2025/BSC/C/009', 'roll' => '009'],
                ['name' => 'Sarojini Naidu', 'admission_no' => '2025/BSC/C/010', 'roll' => '010'],
            ],
        ];

        // Map division names to division records
        $divisionMap = [
            'BCOM-A' => $divisions->firstWhere('division_name', 'A'),
            'BCOM-B' => $divisions->firstWhere('division_name', 'B'),
            'BCOM-C' => $divisions->firstWhere('division_name', 'C'),
            'BSC-A' => $divisions->filter()->firstWhere('division_name', 'A'),
            'BSC-B' => $divisions->filter()->firstWhere('division_name', 'B'),
            'BSC-C' => $divisions->filter()->firstWhere('division_name', 'C'),
        ];

        $parentPhones = [
            '9900001001', '9900001002', '9900001003', '9900001004', '9900001005',
            '9900001006', '9900001007', '9900001008', '9900001009', '9900001010',
        ];

        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $genders = ['male', 'female'];

        $count = 0;
        $phoneIndex = 0;

        foreach ($studentsData as $divKey => $students) {
            $division = $divisionMap[$divKey];
            if (!$division) {
                $this->command->warn("  ⚠ Division {$divKey} not found, skipping...");
                continue;
            }

            $this->command->info("  Creating students for Division {$division->division_name}...");

            foreach ($students as $index => $studentData) {
                $email = 'student.' . strtolower(str_replace('/', '', $studentData['admission_no'])) . '@school.com';
                
                // Create user account
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $studentData['name'],
                        'password' => Hash::make('password123'),
                        'is_active' => true,
                    ]
                );

                // Assign student role
                if (!$user->hasRole('student')) {
                    $user->assignRole('student');
                }

                // Create student record
                $student = Student::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'division_id' => $division->id,
                        'admission_number' => $studentData['admission_no'],
                        'roll_number' => $studentData['admission_no'] . '-' . $studentData['roll'], // Unique roll number
                        'first_name' => explode(' ', $studentData['name'])[0],
                        'last_name' => implode(' ', array_slice(explode(' ', $studentData['name']), 1)) ?: 'Student',
                        'date_of_birth' => date('Y-m-d', strtotime('-' . (18 + $index % 3) . ' years')),
                        'gender' => $genders[$index % 2],
                        'blood_group' => $bloodGroups[$index % 8],
                        'mobile_number' => '9800000' . str_pad($count + 1, 3, '0', STR_PAD_LEFT),
                        'email' => $email,
                        'current_address' => ($index + 1) . ' Cross Street, ' . ['Gandhi Nagar', 'Nehru Colony', 'MG Road', 'Anna Nagar', 'Shivaji Nagar'][$index % 5],
                        'permanent_address' => ($index + 1) . ' Main Road, ' . ['Patel Nagar', 'Civil Lines', 'Residency Road', 'Cantonment', 'Model Town'][$index % 5],
                        'program_id' => $division->program_id,
                        'academic_year' => '2025-2026',
                        'academic_session_id' => 1,
                        'admission_date' => '2025-06-01',
                        'student_status' => 'active',
                    ]
                );

                $count++;
                $phoneIndex++;
            }
        }

        $this->command->info('');
        $this->command->info('=== Seeding Complete ===');
        $this->command->info("Total students created: {$count}");
        $this->command->info("Total student records: " . Student::count());
        $this->command->info('');
        $this->command->info('Login credentials for all students:');
        $this->command->info('  Email: student.2025bcoma001@school.com (pattern varies)');
        $this->command->info('  Password: password123');
    }
}
