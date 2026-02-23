<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Academic\Department;
use App\Models\Academic\Program;
use App\Models\Academic\Division;
use App\Models\Result\Subject;
use App\Models\User\Student;
use App\Models\Fee\FeeHead;
use App\Models\Fee\FeeStructure;
use App\Models\Library\Book;
use App\Models\Result\Examination;

class ResetDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Delete all data except users and roles
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        DB::table('students')->truncate();
        DB::table('divisions')->truncate();
        DB::table('programs')->truncate();
        DB::table('departments')->truncate();
        DB::table('subjects')->truncate();
        DB::table('fee_structures')->truncate();
        DB::table('fee_heads')->truncate();
        DB::table('books')->truncate();
        DB::table('examinations')->truncate();
        DB::table('student_marks')->truncate();
        DB::table('attendances')->truncate();
        DB::table('timetables')->truncate();
        DB::table('staff_profiles')->truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create Departments
        $cs = Department::create([
            'name' => 'Computer Science',
            'code' => 'CS',
            'description' => 'Department of Computer Science',
            'is_active' => true
        ]);

        $math = Department::create([
            'name' => 'Mathematics',
            'code' => 'MATH',
            'description' => 'Department of Mathematics',
            'is_active' => true
        ]);

        // Create Programs
        $bca = Program::create([
            'name' => 'Bachelor of Computer Applications',
            'short_name' => 'BCA',
            'code' => 'BCA',
            'department_id' => $cs->id,
            'duration_years' => 3,
            'is_active' => true
        ]);

        $bsc = Program::create([
            'name' => 'Bachelor of Science',
            'short_name' => 'BSC',
            'code' => 'BSC',
            'department_id' => $math->id,
            'duration_years' => 3,
            'is_active' => true
        ]);

        // Create Divisions
        $div1 = Division::create([
            'division_name' => 'A',
            'academic_year_id' => 1,
            'max_students' => 60,
            'is_active' => true
        ]);

        $div2 = Division::create([
            'division_name' => 'B',
            'academic_year_id' => 1,
            'max_students' => 50,
            'is_active' => true
        ]);

        // Create Subjects
        Subject::create([
            'name' => 'Programming in C',
            'code' => 'CS101',
            'program_id' => $bca->id,
            'academic_year_id' => 1,
            'semester' => 1,
            'credit' => 4,
            'is_active' => true
        ]);

        Subject::create([
            'name' => 'Data Structures',
            'code' => 'CS102',
            'program_id' => $bca->id,
            'academic_year_id' => 1,
            'semester' => 2,
            'credit' => 4,
            'is_active' => true
        ]);

        Subject::create([
            'name' => 'Calculus',
            'code' => 'MATH101',
            'program_id' => $bsc->id,
            'academic_year_id' => 1,
            'semester' => 1,
            'credit' => 3,
            'is_active' => true
        ]);

        // Create Fee Heads
        $tuition = FeeHead::create([
            'name' => 'Tuition Fee',
            'code' => 'TUITION',
            'description' => 'Annual tuition fee',
            'is_active' => true
        ]);

        $exam = FeeHead::create([
            'name' => 'Examination Fee',
            'code' => 'EXAM',
            'description' => 'Semester examination fee',
            'is_active' => true
        ]);

        // Create Fee Structures
        FeeStructure::create([
            'program_id' => $bca->id,
            'fee_head_id' => $tuition->id,
            'academic_year' => '2024-2025',
            'amount' => 50000,
            'installments' => 2
        ]);

        FeeStructure::create([
            'program_id' => $bca->id,
            'fee_head_id' => $exam->id,
            'academic_year' => '2024-2025',
            'amount' => 2000,
            'installments' => 1
        ]);

        // Create Books
        Book::create([
            'isbn' => '978-0-13-468599-1',
            'title' => 'The C Programming Language',
            'author' => 'Brian W. Kernighan',
            'publisher' => 'Prentice Hall',
            'category' => 'Programming',
            'total_copies' => 10,
            'available_copies' => 10,
            'is_active' => true
        ]);

        Book::create([
            'isbn' => '978-0-262-03384-8',
            'title' => 'Introduction to Algorithms',
            'author' => 'Thomas H. Cormen',
            'publisher' => 'MIT Press',
            'category' => 'Computer Science',
            'total_copies' => 8,
            'available_copies' => 8,
            'is_active' => true
        ]);

        // Create Examinations
        Examination::create([
            'name' => 'First Semester Examination',
            'code' => 'SEM1-2024',
            'start_date' => '2024-12-01',
            'end_date' => '2024-12-15',
            'academic_year' => '2024-2025'
        ]);

        Examination::create([
            'name' => 'Mid Term Test',
            'code' => 'MID1-2024',
            'start_date' => '2024-10-15',
            'end_date' => '2024-10-20',
            'academic_year' => '2024-2025'
        ]);

        // Create Sample Students
        $studentUser = \App\Models\User::where('email', 'student@schoolerp.com')->first();
        if ($studentUser) {
            Student::create([
                'user_id' => $studentUser->id,
                'admission_number' => 'ADM2024001',
                'roll_number' => 'BCA001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'date_of_birth' => '2005-01-15',
                'gender' => 'male',
                'email' => 'john.doe@example.com',
                'mobile_number' => '9876543210',
                'current_address' => '123 Main Street, Mumbai, Maharashtra - 400001',
                'permanent_address' => '123 Main Street, Mumbai, Maharashtra - 400001',
                'blood_group' => 'O+',
                'religion' => 'Hindu',
                'category' => 'General',
                'program_id' => $bca->id,
                'division_id' => $div1->id,
                'academic_session_id' => 1,
                'academic_year' => '2024-2025',
                'admission_date' => '2024-07-01',
                'student_status' => 'active'
            ]);
        }

        // Create additional students
        $adminUser = \App\Models\User::first();
        $students = [
            ['ADM2024002', 'BCA002', 'Jane', 'Smith', '2005-03-20', 'female', 'jane.smith@example.com', '9876543211', '456 Park Avenue, Delhi - 110001', 'A+', 'Christian', 'General', $bca->id, $div1->id],
            ['ADM2024003', 'BCA003', 'Mike', 'Johnson', '2005-05-10', 'male', 'mike.johnson@example.com', '9876543212', '789 Lake Road, Bangalore, Karnataka - 560001', 'B+', 'Hindu', 'OBC', $bca->id, $div1->id],
            ['ADM2024004', 'BCA004', 'Sarah', 'Williams', '2005-07-25', 'female', 'sarah.williams@example.com', '9876543213', '321 Hill Street, Pune, Maharashtra - 411001', 'AB+', 'Muslim', 'General', $bca->id, $div2->id],
            ['ADM2024005', 'BSC001', 'David', 'Brown', '2005-02-14', 'male', 'david.brown@example.com', '9876543214', '654 Beach Road, Chennai, Tamil Nadu - 600001', 'O-', 'Hindu', 'SC', $bsc->id, $div2->id],
        ];

        foreach ($students as $student) {
            Student::create([
                'user_id' => $adminUser->id,
                'admission_number' => $student[0],
                'roll_number' => $student[1],
                'first_name' => $student[2],
                'last_name' => $student[3],
                'date_of_birth' => $student[4],
                'gender' => $student[5],
                'email' => $student[6],
                'mobile_number' => $student[7],
                'current_address' => $student[8],
                'permanent_address' => $student[8],
                'blood_group' => $student[9],
                'religion' => $student[10],
                'category' => $student[11],
                'program_id' => $student[12],
                'division_id' => $student[13],
                'academic_session_id' => 1,
                'academic_year' => '2024-2025',
                'admission_date' => '2024-07-01',
                'student_status' => 'active'
            ]);
        }

        $this->command->info('Database reset successfully with sample data!');
    }
}
