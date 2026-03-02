<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TeacherProfile;
use App\Models\Academic\Division;
use App\Models\Result\Subject;
use App\Models\Academic\Program;
use App\Models\Academic\AcademicYear;
use App\Models\Academic\AcademicSession;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== Seeding Sample Data ===');

        // Get or create academic year
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            $this->command->error('No active academic year found!');
            return;
        }

        // Get or create academic session
        $session = AcademicSession::where('is_active', true)->first();
        if (!$session) {
            $this->command->error('No active academic session found!');
            return;
        }

        // Get programs
        $programs = Program::all();
        if ($programs->isEmpty()) {
            $this->command->error('No programs found!');
            return;
        }

        // ========== CREATE TEACHERS ==========
        $this->command->info('Creating Teachers...');
        
        $teachers = [
            ['name' => 'John Teacher', 'email' => 'teacher@school.com'],
            ['name' => 'Sarah Johnson', 'email' => 'sarah@school.com'],
            ['name' => 'Michael Brown', 'email' => 'michael@school.com'],
            ['name' => 'Emily Davis', 'email' => 'emily@school.com'],
            ['name' => 'David Wilson', 'email' => 'david@school.com'],
        ];

        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);

        foreach ($teachers as $index => $teacherData) {
            $user = User::firstOrCreate(
                ['email' => $teacherData['email']],
                [
                    'name' => $teacherData['name'],
                    'password' => Hash::make('password123'),
                    'is_active' => true,
                ]
            );
            
            if (!$user->hasRole('teacher')) {
                $user->assignRole('teacher');
            }

            // Create teacher profile
            TeacherProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'employee_id' => 'EMP' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'phone' => '987654321' . $index,
                    'blood_group' => ['A+', 'B+', 'O+', 'AB+'][$index % 4],
                    'gender' => $index % 2 === 0 ? 'male' : 'female',
                    'qualification' => ['M.Sc', 'M.A', 'M.Com', 'M.Ed', 'Ph.D'][$index % 5],
                    'specialization' => ['Mathematics', 'Physics', 'Chemistry', 'English', 'Computer Science'][$index % 5],
                    'experience_years' => ($index + 1) * 2,
                    'joining_date' => now()->subYears($index + 1),
                    'designation' => ['Lecturer', 'Senior Lecturer', 'Assistant Professor', 'Associate Professor', 'Professor'][$index % 5],
                    'is_active' => true,
                ]
            );

            $this->command->info("  Created: {$teacherData['name']} ({$teacherData['email']})");
        }

        $teacherUsers = User::role('teacher')->get();
        $this->command->info("Total Teachers: {$teacherUsers->count()}");

        // ========== CREATE DIVISIONS ==========
        $this->command->info('Creating Divisions...');

        $divisionNames = ['A', 'B', 'C', 'D', 'E'];
        $createdDivisions = [];

        foreach ($programs as $program) {
            foreach ($divisionNames as $divName) {
                $division = Division::firstOrCreate(
                    [
                        'program_id' => $program->id,
                        'division_name' => $divName,
                        'academic_year_id' => $academicYear->id,
                    ],
                    [
                        'session_id' => $session->id,
                        'max_students' => 60,
                        'is_active' => true,
                    ]
                );

                $createdDivisions[] = $division;
                $this->command->info("  Created: {$program->name} - Division {$divName}");
            }
        }

        $this->command->info("Total Divisions: " . count($createdDivisions));

        // ========== CREATE SUBJECTS ==========
        $this->command->info('Creating Subjects...');

        $subjectData = [
            1 => [ // For Program 1 (e.g., B.Com)
                ['name' => 'Financial Accounting', 'code' => 'FAC101', 'credit' => 4],
                ['name' => 'Business Economics', 'code' => 'BEC102', 'credit' => 3],
                ['name' => 'Cost Accounting', 'code' => 'CAC103', 'credit' => 4],
                ['name' => 'Income Tax', 'code' => 'ITX104', 'credit' => 3],
                ['name' => 'Auditing', 'code' => 'AUD105', 'credit' => 3],
                ['name' => 'Business Management', 'code' => 'BMT106', 'credit' => 3],
                ['name' => 'Corporate Law', 'code' => 'CLW107', 'credit' => 3],
                ['name' => 'Business Mathematics', 'code' => 'MAT108', 'credit' => 3],
                ['name' => 'Business Communication', 'code' => 'BCO109', 'credit' => 2],
                ['name' => 'Computer Fundamentals', 'code' => 'CMP110', 'credit' => 3],
                ['name' => 'Principles of Management', 'code' => 'MGT111', 'credit' => 3],
                ['name' => 'Marketing Management', 'code' => 'MKT112', 'credit' => 3],
            ],
            2 => [ // For Program 2 (e.g., B.Sc)
                ['name' => 'Physics', 'code' => 'PHY101', 'credit' => 4],
                ['name' => 'Chemistry', 'code' => 'CHM102', 'credit' => 4],
                ['name' => 'Mathematics', 'code' => 'MAT103', 'credit' => 4],
                ['name' => 'Computer Science', 'code' => 'CSC104', 'credit' => 4],
                ['name' => 'Electronics', 'code' => 'ELN105', 'credit' => 3],
                ['name' => 'Biology', 'code' => 'BIO106', 'credit' => 3],
                ['name' => 'Environmental Science', 'code' => 'ENS107', 'credit' => 2],
                ['name' => 'English', 'code' => 'ENG108', 'credit' => 2],
                ['name' => 'Organic Chemistry', 'code' => 'OCH109', 'credit' => 4],
                ['name' => 'Inorganic Chemistry', 'code' => 'ICH110', 'credit' => 4],
                ['name' => 'Calculus', 'code' => 'CAL111', 'credit' => 4],
                ['name' => 'Algebra', 'code' => 'ALG112', 'credit' => 3],
            ],
        ];

        $createdSubjects = [];

        foreach ($subjectData as $programId => $subjects) {
            foreach ($subjects as $subject) {
                $subj = Subject::firstOrCreate(
                    [
                        'code' => $subject['code'],
                        'program_id' => $programId,
                    ],
                    [
                        'academic_year_id' => $academicYear->id,
                        'name' => $subject['name'],
                        'credit' => $subject['credit'],
                        'type' => 'theory',
                        'max_marks' => 80,
                        'passing_marks' => 35,
                        'semester' => 1,
                        'is_active' => true,
                    ]
                );

                $createdSubjects[] = $subj;
            }
        }

        $this->command->info("Total Subjects: " . count($createdSubjects));

        // ========== CREATE TEACHER ASSIGNMENTS ==========
        $this->command->info('Creating Teacher Assignments...');

        $subjects = Subject::where('is_active', true)->get();
        $divisions = Division::where('is_active', true)->get();

        foreach ($teacherUsers as $teacher) {
            // Assign each teacher to 2-3 divisions
            $assignedDivisions = $divisions->random(rand(2, 3));
            foreach ($assignedDivisions as $division) {
                DB::table('teacher_assignments')->updateOrInsert(
                    [
                        'teacher_id' => $teacher->id,
                        'division_id' => $division->id,
                        'assignment_type' => 'division',
                    ],
                    ['is_primary' => 1]
                );
            }

            // Assign each teacher to 4-6 subjects
            $assignedSubjects = $subjects->random(rand(4, 6));
            foreach ($assignedSubjects as $subject) {
                DB::table('teacher_assignments')->updateOrInsert(
                    [
                        'teacher_id' => $teacher->id,
                        'subject_id' => $subject->id,
                        'assignment_type' => 'subject',
                    ],
                    ['is_primary' => 1]
                );
            }
        }

        $this->command->info("Teacher assignments created!");

        // ========== SUMMARY ==========
        $this->command->info('');
        $this->command->info('=== Seeding Complete ===');
        $this->command->info('Teachers: ' . User::role('teacher')->count());
        $this->command->info('Divisions: ' . Division::where('is_active', true)->count());
        $this->command->info('Subjects: ' . Subject::where('is_active', true)->count());
        $this->command->info('');
        $this->command->info('Login credentials for all teachers:');
        $this->command->info('  Email: teacher@school.com (or sarah@school.com, etc.)');
        $this->command->info('  Password: password123');
    }
}
