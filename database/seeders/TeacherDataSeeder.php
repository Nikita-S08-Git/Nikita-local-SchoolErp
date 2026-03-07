<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\TeacherProfile;
use App\Models\TeacherAssignment;
use Spatie\Permission\Models\Role;

class TeacherDataSeeder extends Seeder
{
    public function run(): void
    {
        echo "=== Seeding Teacher Data ===" . PHP_EOL . PHP_EOL;

        // Get existing IDs
        $divisions = DB::table('divisions')->pluck('id', 'division_name');
        $departments = DB::table('departments')->pluck('id', 'name');
        $programs = DB::table('programs')->pluck('id', 'name');
        $subjects = DB::table('subjects')->pluck('id', 'code');

        // Teacher data with profiles
        $teachers = [
            [
                'name' => 'Rajesh Kumar',
                'email' => 'rajesh.kumar@schoolerp.com',
                'role' => 'class_teacher',
                'phone' => '+91 9876543210',
                'address' => '123, MG Road, Mumbai, Maharashtra - 400001',
                'gender' => 'male',
                'date_of_birth' => '1985-06-15',
                'qualification' => 'M.Com, B.Ed',
                'experience_years' => 12,
                'specialization' => 'Accounting, Finance',
                'bio' => 'Passionate about teaching accounting and finance. 12 years of experience in higher education.',
                'division' => 'A',
                'department' => 'Commerce',
            ],
            [
                'name' => 'Priya Sharma',
                'email' => 'priya.sharma@schoolerp.com',
                'role' => 'subject_teacher',
                'phone' => '+91 9876543211',
                'address' => '456, Park Street, Delhi - 110001',
                'gender' => 'female',
                'date_of_birth' => '1990-03-22',
                'qualification' => 'M.Sc Mathematics, B.Ed',
                'experience_years' => 8,
                'specialization' => 'Mathematics, Statistics',
                'bio' => 'Love making mathematics easy and interesting for students.',
                'division' => 'B',
                'department' => 'Science',
            ],
            [
                'name' => 'Amit Patel',
                'email' => 'amit.patel@schoolerp.com',
                'role' => 'subject_teacher',
                'phone' => '+91 9876543212',
                'address' => '789, CG Road, Ahmedabad, Gujarat - 380009',
                'gender' => 'male',
                'date_of_birth' => '1988-11-08',
                'qualification' => 'MCA, B.Ed',
                'experience_years' => 10,
                'specialization' => 'Computer Science, IT',
                'bio' => 'Specialized in computer applications and programming.',
                'division' => 'A',
                'department' => 'Commerce',
            ],
            [
                'name' => 'Sneha Reddy',
                'email' => 'sneha.reddy@schoolerp.com',
                'role' => 'subject_teacher',
                'phone' => '+91 9876543213',
                'address' => '321, Banjara Hills, Hyderabad, Telangana - 500034',
                'gender' => 'female',
                'date_of_birth' => '1992-07-30',
                'qualification' => 'MA English, B.Ed',
                'experience_years' => 6,
                'specialization' => 'English Literature, Communication',
                'bio' => 'Focused on developing communication skills in students.',
                'division' => 'B',
                'department' => 'Arts',
            ],
            [
                'name' => 'Vikram Singh',
                'email' => 'vikram.singh@schoolerp.com',
                'role' => 'subject_teacher',
                'phone' => '+91 9876543214',
                'address' => '654, Civil Lines, Jaipur, Rajasthan - 302006',
                'gender' => 'male',
                'date_of_birth' => '1987-01-25',
                'qualification' => 'M.Sc Economics, B.Ed',
                'experience_years' => 11,
                'specialization' => 'Economics, Business Studies',
                'bio' => 'Making economics practical and relatable for students.',
                'division' => 'A',
                'department' => 'Commerce',
            ],
            [
                'name' => 'Anita Desai',
                'email' => 'anita.desai@schoolerp.com',
                'role' => 'subject_teacher',
                'phone' => '+91 9876543215',
                'address' => '987, FC Road, Pune, Maharashtra - 411004',
                'gender' => 'female',
                'date_of_birth' => '1991-09-14',
                'qualification' => 'M.Sc Chemistry, B.Ed',
                'experience_years' => 7,
                'specialization' => 'Chemistry, Science',
                'bio' => 'Passionate about practical chemistry and laboratory work.',
                'division' => 'B',
                'department' => 'Science',
            ],
            [
                'name' => 'Rahul Verma',
                'email' => 'rahul.verma@schoolerp.com',
                'role' => 'subject_teacher',
                'phone' => '+91 9876543216',
                'address' => '147, Hazratganj, Lucknow, UP - 226001',
                'gender' => 'male',
                'date_of_birth' => '1989-04-18',
                'qualification' => 'MBA, B.Ed',
                'experience_years' => 9,
                'specialization' => 'Business Management, Marketing',
                'bio' => 'Industry experience combined with academic excellence.',
                'division' => 'A',
                'department' => 'Management',
            ],
            [
                'name' => 'Kavita Joshi',
                'email' => 'kavita.joshi@schoolerp.com',
                'role' => 'subject_teacher',
                'phone' => '+91 9876543217',
                'address' => '258, MI Road, Jaipur, Rajasthan - 302001',
                'gender' => 'female',
                'date_of_birth' => '1993-12-05',
                'qualification' => 'LLB, B.Ed',
                'experience_years' => 5,
                'specialization' => 'Business Law, Legal Studies',
                'bio' => 'Simplifying legal concepts for commerce students.',
                'division' => 'B',
                'department' => 'Commerce',
            ],
        ];

        foreach ($teachers as $index => $teacherData) {
            echo "Creating Teacher " . ($index + 1) . ": {$teacherData['name']}..." . PHP_EOL;

            // Check if user already exists
            $user = User::where('email', $teacherData['email'])->first();
            
            if (!$user) {
                // Create user
                $user = User::create([
                    'name' => $teacherData['name'],
                    'email' => $teacherData['email'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]);

                // Assign role
                $role = Role::where('name', $teacherData['role'])->first();
                if ($role) {
                    $user->assignRole($role);
                }

                echo "  ✓ Created user and assigned role" . PHP_EOL;
            } else {
                echo "  ✓ User already exists" . PHP_EOL;
            }

            // Create or update teacher profile
            $profile = TeacherProfile::where('user_id', $user->id)->first();
            
            if (!$profile) {
                TeacherProfile::create([
                    'user_id' => $user->id,
                    'employee_id' => 'EMP' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'phone' => $teacherData['phone'],
                    'current_address' => $teacherData['address'],
                    'city' => 'Mumbai',
                    'state' => 'Maharashtra',
                    'pincode' => '400001',
                    'gender' => $teacherData['gender'],
                    'date_of_birth' => $teacherData['date_of_birth'],
                    'qualification' => $teacherData['qualification'],
                    'experience_years' => $teacherData['experience_years'],
                    'specialization' => $teacherData['specialization'],
                    'designation' => $teacherData['role'] === 'class_teacher' ? 'Senior Lecturer' : 'Lecturer',
                    'joining_date' => now()->subYears($teacherData['experience_years']),
                    'is_active' => true,
                ]);
                echo "  ✓ Created teacher profile" . PHP_EOL;
            } else {
                echo "  ✓ Profile already exists" . PHP_EOL;
            }

            // Create teacher assignments if not exist
            $divisionId = $divisions->get($teacherData['division']);
            $departmentId = $departments->get($teacherData['department']);

            if ($divisionId && !TeacherAssignment::where('teacher_id', $user->id)->where('division_id', $divisionId)->exists()) {
                TeacherAssignment::create([
                    'teacher_id' => $user->id,
                    'division_id' => $divisionId,
                    'assignment_type' => 'division',
                    'is_primary' => true,
                ]);
                echo "  ✓ Assigned to division {$teacherData['division']}" . PHP_EOL;
            }

            if ($departmentId && !TeacherAssignment::where('teacher_id', $user->id)->where('department_id', $departmentId)->exists()) {
                TeacherAssignment::create([
                    'teacher_id' => $user->id,
                    'department_id' => $departmentId,
                    'assignment_type' => 'department',
                    'is_primary' => ($teacherData['role'] === 'class_teacher'),
                ]);
                echo "  ✓ Assigned to {$teacherData['department']} department" . PHP_EOL;
            }

            // Assign subjects based on specialization
            $subjectCodes = $this->getSubjectCodesForSpecialization($teacherData['specialization']);
            foreach ($subjectCodes as $code) {
                $subjectId = $subjects->get($code);
                if ($subjectId && $divisionId) {
                    if (!TeacherAssignment::where('teacher_id', $user->id)->where('subject_id', $subjectId)->exists()) {
                        TeacherAssignment::create([
                            'teacher_id' => $user->id,
                            'subject_id' => $subjectId,
                            'division_id' => $divisionId,
                            'assignment_type' => 'subject',
                            'is_primary' => false,
                        ]);
                        echo "  ✓ Assigned subject: $code" . PHP_EOL;
                    }
                }
            }

            echo PHP_EOL;
        }

        echo PHP_EOL . "=== Teacher Data Seeding Complete! ===" . PHP_EOL;
        echo PHP_EOL . "Login Credentials:" . PHP_EOL;
        echo "------------------------" . PHP_EOL;
        foreach ($teachers as $teacher) {
            echo "Email: {$teacher['email']} | Password: password | Role: {$teacher['role']}" . PHP_EOL;
        }
    }

    private function getSubjectCodesForSpecialization($specialization): array
    {
        $mapping = [
            'Accounting' => ['ACC101'],
            'Finance' => ['ACC101'],
            'Mathematics' => ['MAT101'],
            'Statistics' => ['STAT101'],
            'Computer Science' => ['COM101'],
            'IT' => ['COM101'],
            'English' => ['ENG101'],
            'Economics' => ['ECO101'],
            'Business Studies' => ['BUS101'],
            'Chemistry' => [], // Add science subjects if available
            'Management' => ['BUS101'],
            'Marketing' => ['BUS101'],
            'Law' => ['LAW101'],
        ];

        $codes = [];
        foreach ($mapping as $key => $subjectCodes) {
            if (stripos($specialization, $key) !== false) {
                $codes = array_merge($codes, $subjectCodes);
            }
        }

        return array_unique($codes);
    }
}
