<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TeacherProfile;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TeacherProfileSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== Seeding Teacher Profiles ===');

        // Get teacher role
        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);

        // Teacher data with complete profile information
        $teachers = [
            [
                'name' => 'John Teacher',
                'email' => 'teacher@school.com',
                'profile' => [
                    'employee_id' => 'EMP001',
                    'phone' => '9876543210',
                    'alternate_phone' => '9876543211',
                    'blood_group' => 'A+',
                    'date_of_birth' => '1985-05-15',
                    'gender' => 'male',
                    'marital_status' => 'married',
                    'qualification' => 'M.Sc, Ph.D',
                    'specialization' => 'Mathematics',
                    'experience_years' => 120, // 10 years in months
                    'joining_date' => '2020-06-01',
                    'designation' => 'Senior Lecturer',
                    'current_address' => '123 Main Street, Gandhi Nagar',
                    'permanent_address' => '456 Park Avenue, Nehru Colony',
                    'city' => 'Mumbai',
                    'state' => 'Maharashtra',
                    'pincode' => '400001',
                    'emergency_contact_name' => 'Mary Teacher',
                    'emergency_contact_relation' => 'Spouse',
                    'emergency_contact_phone' => '9876543212',
                ]
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@school.com',
                'profile' => [
                    'employee_id' => 'EMP002',
                    'phone' => '9876543211',
                    'alternate_phone' => '9876543212',
                    'blood_group' => 'B+',
                    'date_of_birth' => '1988-08-22',
                    'gender' => 'female',
                    'marital_status' => 'single',
                    'qualification' => 'M.A, M.Ed',
                    'specialization' => 'English Literature',
                    'experience_years' => 84, // 7 years in months
                    'joining_date' => '2021-07-15',
                    'designation' => 'Lecturer',
                    'current_address' => '789 Lake View, Anna Nagar',
                    'permanent_address' => '321 River Side, Patel Nagar',
                    'city' => 'Delhi',
                    'state' => 'Delhi',
                    'pincode' => '110001',
                    'emergency_contact_name' => 'Robert Johnson',
                    'emergency_contact_relation' => 'Father',
                    'emergency_contact_phone' => '9876543213',
                ]
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael@school.com',
                'profile' => [
                    'employee_id' => 'EMP003',
                    'phone' => '9876543212',
                    'alternate_phone' => '9876543213',
                    'blood_group' => 'O+',
                    'date_of_birth' => '1982-03-10',
                    'gender' => 'male',
                    'marital_status' => 'married',
                    'qualification' => 'M.Sc, M.Phil',
                    'specialization' => 'Physics',
                    'experience_years' => 156, // 13 years in months
                    'joining_date' => '2019-01-10',
                    'designation' => 'Associate Professor',
                    'current_address' => '567 Hill Top, Rajaji Nagar',
                    'permanent_address' => '890 Valley View, Shivaji Nagar',
                    'city' => 'Bangalore',
                    'state' => 'Karnataka',
                    'pincode' => '560001',
                    'emergency_contact_name' => 'Lisa Brown',
                    'emergency_contact_relation' => 'Spouse',
                    'emergency_contact_phone' => '9876543214',
                ]
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily@school.com',
                'profile' => [
                    'employee_id' => 'EMP004',
                    'phone' => '9876543213',
                    'alternate_phone' => '9876543214',
                    'blood_group' => 'AB+',
                    'date_of_birth' => '1990-11-28',
                    'gender' => 'female',
                    'marital_status' => 'single',
                    'qualification' => 'M.Com',
                    'specialization' => 'Accounting & Finance',
                    'experience_years' => 48, // 4 years in months
                    'joining_date' => '2022-06-20',
                    'designation' => 'Assistant Professor',
                    'current_address' => '234 Garden City, Koramangala',
                    'permanent_address' => '678 Green Park, Vijay Nagar',
                    'city' => 'Chennai',
                    'state' => 'Tamil Nadu',
                    'pincode' => '600001',
                    'emergency_contact_name' => 'James Davis',
                    'emergency_contact_relation' => 'Brother',
                    'emergency_contact_phone' => '9876543215',
                ]
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david@school.com',
                'profile' => [
                    'employee_id' => 'EMP005',
                    'phone' => '9876543214',
                    'alternate_phone' => '9876543215',
                    'blood_group' => 'A-',
                    'date_of_birth' => '1978-07-05',
                    'gender' => 'male',
                    'marital_status' => 'married',
                    'qualification' => 'Ph.D, M.Sc',
                    'specialization' => 'Computer Science & AI',
                    'experience_years' => 180, // 15 years in months
                    'joining_date' => '2018-08-01',
                    'designation' => 'Professor',
                    'current_address' => '901 Tech Park, Electronic City',
                    'permanent_address' => '432 Residency Road, Cantonment',
                    'city' => 'Hyderabad',
                    'state' => 'Telangana',
                    'pincode' => '500001',
                    'emergency_contact_name' => 'Susan Wilson',
                    'emergency_contact_relation' => 'Spouse',
                    'emergency_contact_phone' => '9876543216',
                ]
            ],
            [
                'name' => 'Prof. Rajesh Kumar',
                'email' => 'rajesh@school.com',
                'profile' => [
                    'employee_id' => 'EMP006',
                    'phone' => '9876543215',
                    'alternate_phone' => '9876543216',
                    'blood_group' => 'B-',
                    'date_of_birth' => '1975-12-20',
                    'gender' => 'male',
                    'marital_status' => 'married',
                    'qualification' => 'M.Sc, B.Ed',
                    'specialization' => 'Chemistry',
                    'experience_years' => 204, // 17 years in months
                    'joining_date' => '2017-06-15',
                    'designation' => 'Head of Department',
                    'current_address' => '765 Science Enclave, Jayanagar',
                    'permanent_address' => '543 MG Road, Brigade Road',
                    'city' => 'Pune',
                    'state' => 'Maharashtra',
                    'pincode' => '411001',
                    'emergency_contact_name' => 'Sunita Kumar',
                    'emergency_contact_relation' => 'Spouse',
                    'emergency_contact_phone' => '9876543217',
                ]
            ],
            [
                'name' => 'Dr. Priya Sharma',
                'email' => 'priya@school.com',
                'profile' => [
                    'employee_id' => 'EMP007',
                    'phone' => '9876543216',
                    'alternate_phone' => '9876543217',
                    'blood_group' => 'O-',
                    'date_of_birth' => '1983-04-18',
                    'gender' => 'female',
                    'marital_status' => 'married',
                    'qualification' => 'Ph.D, M.A',
                    'specialization' => 'Economics',
                    'experience_years' => 132, // 11 years in months
                    'joining_date' => '2020-01-20',
                    'designation' => 'Associate Professor',
                    'current_address' => '876 University Road, Vasant Vihar',
                    'permanent_address' => '234 Civil Lines, Model Town',
                    'city' => 'Jaipur',
                    'state' => 'Rajasthan',
                    'pincode' => '302001',
                    'emergency_contact_name' => 'Amit Sharma',
                    'emergency_contact_relation' => 'Spouse',
                    'emergency_contact_phone' => '9876543218',
                ]
            ],
            [
                'name' => 'Prof. Anil Verma',
                'email' => 'anil@school.com',
                'profile' => [
                    'employee_id' => 'EMP008',
                    'phone' => '9876543217',
                    'alternate_phone' => '9876543218',
                    'blood_group' => 'AB-',
                    'date_of_birth' => '1980-09-25',
                    'gender' => 'male',
                    'marital_status' => 'married',
                    'qualification' => 'M.Com, CA',
                    'specialization' => 'Business Administration',
                    'experience_years' => 144, // 12 years in months
                    'joining_date' => '2019-07-01',
                    'designation' => 'Senior Lecturer',
                    'current_address' => '345 Business District, Bandra',
                    'permanent_address' => '678 Andheri West, Lokhandwala',
                    'city' => 'Kolkata',
                    'state' => 'West Bengal',
                    'pincode' => '700001',
                    'emergency_contact_name' => 'Kavita Verma',
                    'emergency_contact_relation' => 'Spouse',
                    'emergency_contact_phone' => '9876543219',
                ]
            ],
        ];

        $count = 0;
        $updated = 0;

        foreach ($teachers as $teacherData) {
            // Create or get user
            $user = User::firstOrCreate(
                ['email' => $teacherData['email']],
                [
                    'name' => $teacherData['name'],
                    'password' => Hash::make('password123'),
                    'is_active' => true,
                ]
            );

            // Assign teacher role
            if (!$user->hasRole('teacher')) {
                $user->assignRole('teacher');
            }

            // Create or update teacher profile
            $profile = TeacherProfile::firstOrCreate(
                ['user_id' => $user->id],
                $teacherData['profile']
            );

            if ($profile->wasRecentlyCreated) {
                $count++;
                $this->command->info("  ✓ Created profile: {$teacherData['name']} ({$teacherData['email']})");
            } else {
                $updated++;
                $this->command->info("  ~ Updated profile: {$teacherData['name']} ({$teacherData['email']})");
            }
        }

        $this->command->info('');
        $this->command->info('=== Seeding Complete ===');
        $this->command->info("New profiles created: {$count}");
        $this->command->info("Existing profiles updated: {$updated}");
        $this->command->info("Total teacher profiles: " . TeacherProfile::count());
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('  Password: password123');
    }
}
