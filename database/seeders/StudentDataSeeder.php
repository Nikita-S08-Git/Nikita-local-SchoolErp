<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User\Student;

class StudentDataSeeder extends Seeder
{
    public function run(): void
    {
        echo "=== Seeding Student Data ===" . PHP_EOL . PHP_EOL;

        // Get existing divisions
        $divisions = DB::table('divisions')
            ->whereIn('division_name', ['A', 'B', 'C'])
            ->where('program_id', 1) // B.Com
            ->pluck('id', 'division_name');

        if ($divisions->isEmpty()) {
            echo "⚠️  No divisions found. Please run CompleteSchoolDataSeeder first." . PHP_EOL;
            return;
        }

        echo "Found divisions: " . $divisions->implode(', ') . PHP_EOL . PHP_EOL;

        // Student data
        $students = [
            // Division A - 15 students
            [
                'division_id' => $divisions['A'],
                'students' => [
                    ['first_name' => 'Rahul', 'last_name' => 'Sharma', 'roll_number' => 'A001', 'email' => 'rahul.sharma@student.com'],
                    ['first_name' => 'Priya', 'last_name' => 'Patel', 'roll_number' => 'A002', 'email' => 'priya.patel@student.com'],
                    ['first_name' => 'Amit', 'last_name' => 'Kumar', 'roll_number' => 'A003', 'email' => 'amit.kumar@student.com'],
                    ['first_name' => 'Sneha', 'last_name' => 'Singh', 'roll_number' => 'A004', 'email' => 'sneha.singh@student.com'],
                    ['first_name' => 'Rohan', 'last_name' => 'Gupta', 'roll_number' => 'A005', 'email' => 'rohan.gupta@student.com'],
                    ['first_name' => 'Anjali', 'last_name' => 'Reddy', 'roll_number' => 'A006', 'email' => 'anjali.reddy@student.com'],
                    ['first_name' => 'Vikram', 'last_name' => 'Desai', 'roll_number' => 'A007', 'email' => 'vikram.desai@student.com'],
                    ['first_name' => 'Neha', 'last_name' => 'Joshi', 'roll_number' => 'A008', 'email' => 'neha.joshi@student.com'],
                    ['first_name' => 'Arjun', 'last_name' => 'Malhotra', 'roll_number' => 'A009', 'email' => 'arjun.malhotra@student.com'],
                    ['first_name' => 'Pooja', 'last_name' => 'Nair', 'roll_number' => 'A010', 'email' => 'pooja.nair@student.com'],
                    ['first_name' => 'Karan', 'last_name' => 'Mehta', 'roll_number' => 'A011', 'email' => 'karan.mehta@student.com'],
                    ['first_name' => 'Divya', 'last_name' => 'Iyer', 'roll_number' => 'A012', 'email' => 'divya.iyer@student.com'],
                    ['first_name' => 'Aditya', 'last_name' => 'Kapoor', 'roll_number' => 'A013', 'email' => 'aditya.kapoor@student.com'],
                    ['first_name' => 'Ritu', 'last_name' => 'Bansal', 'roll_number' => 'A014', 'email' => 'ritu.bansal@student.com'],
                    ['first_name' => 'Nikhil', 'last_name' => 'Chopra', 'roll_number' => 'A015', 'email' => 'nikhil.chopra@student.com'],
                ]
            ],
            // Division B - 15 students
            [
                'division_id' => $divisions['B'],
                'students' => [
                    ['first_name' => 'Siddharth', 'last_name' => 'Verma', 'roll_number' => 'B001', 'email' => 'siddharth.verma@student.com'],
                    ['first_name' => 'Kavya', 'last_name' => 'Rao', 'roll_number' => 'B002', 'email' => 'kavya.rao@student.com'],
                    ['first_name' => 'Abhishek', 'last_name' => 'Jain', 'roll_number' => 'B003', 'email' => 'abhishek.jain@student.com'],
                    ['first_name' => 'Ishita', 'last_name' => 'Agarwal', 'roll_number' => 'B004', 'email' => 'ishita.agarwal@student.com'],
                    ['first_name' => 'Varun', 'last_name' => 'Saxena', 'roll_number' => 'B005', 'email' => 'varun.saxena@student.com'],
                    ['first_name' => 'Riya', 'last_name' => 'Bhatt', 'roll_number' => 'B006', 'email' => 'riya.bhatt@student.com'],
                    ['first_name' => 'Akash', 'last_name' => 'Pandey', 'roll_number' => 'B007', 'email' => 'akash.pandey@student.com'],
                    ['first_name' => 'Simran', 'last_name' => 'Kaur', 'roll_number' => 'B008', 'email' => 'simran.kaur@student.com'],
                    ['first_name' => 'Manish', 'last_name' => 'Tripathi', 'roll_number' => 'B009', 'email' => 'manish.tripathi@student.com'],
                    ['first_name' => 'Tanya', 'last_name' => 'Mehra', 'roll_number' => 'B010', 'email' => 'tanya.mehra@student.com'],
                    ['first_name' => 'Harsh', 'last_name' => 'Goyal', 'roll_number' => 'B011', 'email' => 'harsh.goyal@student.com'],
                    ['first_name' => 'Megha', 'last_name' => 'Sinha', 'roll_number' => 'B012', 'email' => 'megha.sinha@student.com'],
                    ['first_name' => 'Saurabh', 'last_name' => 'Dubey', 'roll_number' => 'B013', 'email' => 'saurabh.dubey@student.com'],
                    ['first_name' => 'Nisha', 'last_name' => 'Arora', 'roll_number' => 'B014', 'email' => 'nisha.arora@student.com'],
                    ['first_name' => 'Deepak', 'last_name' => 'Yadav', 'roll_number' => 'B015', 'email' => 'deepak.yadav@student.com'],
                ]
            ],
            // Division C - 10 students
            [
                'division_id' => $divisions['C'],
                'students' => [
                    ['first_name' => 'Prakash', 'last_name' => 'Jha', 'roll_number' => 'C001', 'email' => 'prakash.jha@student.com'],
                    ['first_name' => 'Usha', 'last_name' => 'Menon', 'roll_number' => 'C002', 'email' => 'usha.menon@student.com'],
                    ['first_name' => 'Rajesh', 'last_name' => 'Pillai', 'roll_number' => 'C003', 'email' => 'rajesh.pillai@student.com'],
                    ['first_name' => 'Geeta', 'last_name' => 'Bose', 'roll_number' => 'C004', 'email' => 'geeta.bose@student.com'],
                    ['first_name' => 'Vinod', 'last_name' => 'Shetty', 'roll_number' => 'C005', 'email' => 'vinod.shetty@student.com'],
                    ['first_name' => 'Sunita', 'last_name' => 'Kulkarni', 'roll_number' => 'C006', 'email' => 'sunita.kulkarni@student.com'],
                    ['first_name' => 'Mukesh', 'last_name' => 'Bhat', 'roll_number' => 'C007', 'email' => 'mukesh.bhat@student.com'],
                    ['first_name' => 'Rekha', 'last_name' => 'Hegde', 'roll_number' => 'C008', 'email' => 'rekha.hegde@student.com'],
                    ['first_name' => 'Sanjay', 'last_name' => 'Naidu', 'roll_number' => 'C009', 'email' => 'sanjay.naidu@student.com'],
                    ['first_name' => 'Preeti', 'last_name' => 'Das', 'roll_number' => 'C010', 'email' => 'preeti.das@student.com'],
                ]
            ],
        ];

        $password = Hash::make('password');
        $totalStudents = 0;

        foreach ($students as $divisionData) {
            $divisionId = $divisionData['division_id'];
            $divisionName = DB::table('divisions')->where('id', $divisionId)->value('division_name');
            
            echo "Creating students for Division {$divisionName}..." . PHP_EOL;
            
            foreach ($divisionData['students'] as $studentData) {
                // Create student user account
                $userId = DB::table('users')->insertGetId([
                    'name' => $studentData['first_name'] . ' ' . $studentData['last_name'],
                    'email' => $studentData['email'],
                    'email_verified_at' => now(),
                    'password' => $password,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Assign student role
                $studentRoleId = DB::table('roles')->where('name', 'student')->value('id');
                if ($studentRoleId) {
                    DB::table('model_has_roles')->insert([
                        'role_id' => $studentRoleId,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $userId,
                    ]);
                }

                // Create student record
                DB::table('students')->insert([
                    'user_id' => $userId,
                    'first_name' => $studentData['first_name'],
                    'last_name' => $studentData['last_name'],
                    'email' => $studentData['email'],
                    'roll_number' => $studentData['roll_number'],
                    'admission_number' => 'ADM' . date('Y') . str_pad($totalStudents + 1, 4, '0', STR_PAD_LEFT),
                    'division_id' => $divisionId,
                    'program_id' => 1, // B.Com
                    'academic_year' => 'FY',
                    'academic_session_id' => 1,
                    'student_status' => 'active',
                    'gender' => ['male', 'female'][array_rand(['male', 'female'])],
                    'date_of_birth' => date('Y-m-d', strtotime('-' . rand(18, 22) . ' years')),
                    'mobile_number' => '+91 ' . rand(7000000000, 9999999999),
                    'current_address' => rand(1, 100) . ', MG Road, Mumbai, Maharashtra - 400001',
                    'admission_date' => '2024-06-01',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $totalStudents++;
                echo "   ✓ Created: {$studentData['first_name']} {$studentData['last_name']} ({$studentData['roll_number']})" . PHP_EOL;
            }
            
            echo PHP_EOL;
        }

        echo "═══════════════════════════════════════════════" . PHP_EOL;
        echo "     STUDENT SEEDING COMPLETE! 🎉              " . PHP_EOL;
        echo "═══════════════════════════════════════════════" . PHP_EOL;
        echo "Total Students Created: {$totalStudents}" . PHP_EOL;
        echo "═══════════════════════════════════════════════" . PHP_EOL . PHP_EOL;
        
        echo "LOGIN CREDENTIALS FOR STUDENTS:" . PHP_EOL;
        echo "───────────────────────────────────────────────" . PHP_EOL;
        echo "Email: rahul.sharma@student.com / password" . PHP_EOL;
        echo "Email: priya.patel@student.com / password" . PHP_EOL;
        echo "Email: amit.kumar@student.com / password" . PHP_EOL;
        echo "... and " . ($totalStudents - 3) . " more students" . PHP_EOL;
        echo "═══════════════════════════════════════════════" . PHP_EOL;
    }
}
