<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TeacherProfile;
use App\Models\StudentProfile;
use App\Models\User\Student;
use App\Models\Academic\Division;
use Spatie\Permission\Models\Role;

class TeacherDashboardSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('=== Seeding Teacher Dashboard Data ===');

        // Get all teachers
        $teacherRole = Role::where('name', 'teacher')->first();
        
        if (!$teacherRole) {
            $this->command->warn('Teacher role not found. Please run RolePermissionSeeder first.');
            return;
        }

        $teachers = User::role('teacher')->get();

        if ($teachers->isEmpty()) {
            $this->command->warn('No teachers found. Please seed teachers first.');
            return;
        }

        $this->command->info("Found {$teachers->count()} teachers");

        // Create teacher profiles
        foreach ($teachers as $teacher) {
            TeacherProfile::firstOrCreate(
                ['user_id' => $teacher->id],
                [
                    'employee_id' => 'EMP' . str_pad($teacher->id, 4, '0', STR_PAD_LEFT),
                    'phone' => fake()->phoneNumber(),
                    'alternate_phone' => fake()->optional()->phoneNumber(),
                    'date_of_birth' => fake()->dateTimeBetween('-40 years', '-25 years'),
                    'gender' => fake()->randomElement(['male', 'female']),
                    'marital_status' => fake()->randomElement(['single', 'married']),
                    'current_address' => fake()->address(),
                    'city' => fake()->city(),
                    'state' => fake()->state(),
                    'pincode' => fake()->postcode(),
                    'qualification' => fake()->randomElement(['M.Sc', 'M.A', 'M.Ed', 'Ph.D', 'B.Ed']),
                    'specialization' => fake()->randomElement(['Mathematics', 'Physics', 'Chemistry', 'English', 'History', 'Computer Science']),
                    'experience_years' => fake()->numberBetween(6, 120), // 6 months to 10 years
                    'joining_date' => fake()->dateTimeBetween('-10 years', '-1 year'),
                    'designation' => fake()->randomElement(['Lecturer', 'Senior Lecturer', 'Assistant Professor', 'Associate Professor']),
                    'emergency_contact_name' => fake()->name(),
                    'emergency_contact_relation' => fake()->randomElement(['Spouse', 'Parent', 'Sibling']),
                    'emergency_contact_phone' => fake()->phoneNumber(),
                    'is_active' => true,
                ]
            );

            $this->command->info("  ✓ Created profile for: {$teacher->name}");
        }

        // Assign divisions to teachers
        $divisions = Division::where('is_active', true)->get();
        
        if ($divisions->isNotEmpty()) {
            foreach ($teachers as $index => $teacher) {
                // Assign 1-2 divisions to each teacher
                $assignedDivisions = $divisions->random(min(2, $divisions->count()));
                
                foreach ($assignedDivisions as $division) {
                    $teacher->teacherDivisions()->syncWithoutDetaching([
                        $division->id => [
                            'is_class_teacher' => $index === 0 && $division->id === $assignedDivisions->first()->id,
                            'is_active' => true,
                            'academic_session_id' => null,
                        ]
                    ]);
                }
                
                $this->command->info("  ✓ Assigned " . $assignedDivisions->count() . " division(s) to: {$teacher->name}");
            }
        }

        // Create student profiles for existing students
        $students = Student::where('student_status', 'active')->get();
        
        if ($students->isNotEmpty()) {
            $this->command->info("Creating profiles for {$students->count()} students...");
            
            foreach ($students as $student) {
                StudentProfile::firstOrCreate(
                    ['student_id' => $student->id],
                    [
                        'father_name' => fake()->name('male'),
                        'father_phone' => fake()->phoneNumber(),
                        'father_occupation' => fake()->randomElement(['Business', 'Service', 'Professional', 'Farmer']),
                        'mother_name' => fake()->name('female'),
                        'mother_phone' => fake()->optional()->phoneNumber(),
                        'mother_occupation' => fake()->optional()->randomElement(['Homemaker', 'Service', 'Professional', 'Business']),
                        'guardian_name' => null,
                        'guardian_phone' => null,
                        'emergency_contact_name' => fake()->name(),
                        'emergency_contact_phone' => fake()->phoneNumber(),
                        'emergency_contact_relation' => fake()->randomElement(['Father', 'Mother', 'Guardian']),
                        'blood_group' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']),
                        'nationality' => 'Indian',
                        'mother_tongue' => fake()->randomElement(['Hindi', 'English', 'Marathi', 'Gujarati']),
                        'religion' => fake()->randomElement(['Hindu', 'Muslim', 'Christian', 'Buddhist']),
                        'medical_conditions' => null,
                        'has_medical_conditions' => false,
                        'uses_transport' => fake()->boolean(30),
                        'transport_type' => fake()->optional()->randomElement(['Bus', 'Van']),
                        'pickup_point' => fake()->optional()->city(),
                        'is_hosteler' => fake()->boolean(10),
                        'hostel_name' => null,
                        'room_number' => null,
                        'bank_account_number' => fake()->optional()->numerify('################'),
                        'bank_ifsc_code' => fake()->optional()->regexify('[A-Z]{4}0#######'),
                        'bank_name' => fake()->optional()->randomElement(['SBI', 'HDFC', 'ICICI', 'Axis Bank']),
                        'bank_branch' => fake()->optional()->city(),
                    ]
                );
            }
            
            $this->command->info("  ✓ Created student profiles");
        }

        $this->command->info('=== Teacher Dashboard Seeding Complete ===');
    }
}
