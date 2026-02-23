<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\User\Student;
use App\Models\Academic\Division;
use App\Models\Academic\Program;
use App\Models\Academic\Subject;
use App\Models\Academic\AcademicSession;
use App\Models\Academic\Timetable;
use App\Models\Academic\Attendance;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class CompleteSystemTestSeeder extends Seeder
{
    public function run()
    {
        echo "🧪 TESTING COMPLETE SCHOOL ERP SYSTEM\n";
        echo "=====================================\n\n";

        // 1. Get or Create Academic Session
        $session = AcademicSession::where('session_name', '2024-25')->first() ?:
                  AcademicSession::create([
                      'session_name' => '2024-25',
                      'start_date' => '2024-04-01',
                      'end_date' => '2025-03-31',
                      'is_active' => true
                  ]);
        echo "✅ Academic Session: {$session->session_name}\n";

        // 2. Create Program
        $program = Program::firstOrCreate([
            'name' => 'Bachelor of Computer Applications',
            'code' => 'BCA',
            'duration' => 3,
            'total_semesters' => 6,
            'is_active' => true
        ]);
        echo "✅ Program Created: {$program->name}\n";

        // 3. Create Subject
        $subject = Subject::firstOrCreate([
            'name' => 'Data Structures',
            'code' => 'DS101',
            'program_id' => $program->id,
            'academic_year_id' => 1,
            'semester' => 3,
            'type' => 'Theory',
            'credit' => 4
        ]);
        echo "✅ Subject Created: {$subject->name}\n";

        // 4. Create Teacher
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $teacher = User::firstOrCreate([
            'email' => 'teacher@school.com'
        ], [
            'name' => 'Prof. John Smith',
            'password' => Hash::make('password123')
        ]);
        $teacher->assignRole('teacher');
        echo "✅ Teacher Created: {$teacher->name}\n";

        // 5. Create Division and Assign Teacher
        $division = Division::firstOrCreate([
            'division_name' => 'BCA-3A',
            'academic_year_id' => $session->id,
            'max_students' => 60,
            'class_teacher_id' => $teacher->id,
            'classroom' => 'Room 101',
            'is_active' => true
        ]);
        echo "✅ Division Created: {$division->division_name} (Teacher: {$teacher->name})\n";

        // 6. Create Students
        for ($i = 1; $i <= 5; $i++) {
            $student = Student::firstOrCreate([
                'roll_number' => 'BCA24' . str_pad($i, 3, '0', STR_PAD_LEFT)
            ], [
                'name' => "Student {$i}",
                'email' => "student{$i}@school.com",
                'phone' => '9876543210',
                'division_id' => $division->id,
                'program_id' => $program->id,
                'admission_date' => '2024-07-01',
                'student_status' => 'active'
            ]);
        }
        echo "✅ Students Created: 5 students in {$division->division_name}\n";

        // 7. Create Timetable Entry
        $timetable = Timetable::firstOrCreate([
            'division_id' => $division->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'day_of_week' => 'Monday',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00'
        ]);
        echo "✅ Timetable Created: {$subject->name} on Monday 9-10 AM\n";

        // 8. Mark Attendance
        $students = Student::where('division_id', $division->id)->get();
        foreach ($students as $student) {
            Attendance::firstOrCreate([
                'student_id' => $student->id,
                'division_id' => $division->id,
                'date' => today(),
                'academic_session_id' => $session->id,
                'status' => 'Present'
            ]);
        }
        echo "✅ Attendance Marked: All students present for today\n";

        echo "\n🎉 SYSTEM TEST COMPLETE!\n";
        echo "========================\n";
        echo "📊 Test Data Summary:\n";
        echo "- Academic Sessions: " . AcademicSession::count() . "\n";
        echo "- Programs: " . Program::count() . "\n";
        echo "- Subjects: " . Subject::count() . "\n";
        echo "- Divisions: " . Division::count() . "\n";
        echo "- Teachers: " . User::role('teacher')->count() . "\n";
        echo "- Students: " . Student::count() . "\n";
        echo "- Timetable Entries: " . Timetable::count() . "\n";
        echo "- Attendance Records: " . Attendance::count() . "\n";
        
        echo "\n🔑 Login Credentials:\n";
        echo "Principal: principal@school.com / admin123\n";
        echo "Teacher: teacher@school.com / password123\n";
        
        echo "\n🌐 Access URLs:\n";
        echo "Principal Dashboard: http://127.0.0.1:8000/dashboard/principal\n";
        echo "Teacher Dashboard: http://127.0.0.1:8000/teacher/dashboard\n";
    }
}