<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompleteSchoolDataSeeder extends Seeder
{
    public function run(): void
    {
        echo "=== Seeding Complete School Data ===" . PHP_EOL . PHP_EOL;

        // Clear existing data
        echo "Clearing existing data..." . PHP_EOL;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('timetables')->truncate();
        DB::table('teacher_assignments')->truncate();
        DB::table('teacher_profiles')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('users')->truncate();
        DB::table('holidays')->truncate();
        DB::table('subjects')->truncate();
        DB::table('divisions')->truncate();
        DB::table('academic_years')->truncate();
        DB::table('academic_sessions')->truncate();
        DB::table('programs')->truncate();
        DB::table('departments')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        echo "✓ Data cleared" . PHP_EOL . PHP_EOL;

        // 1. Departments
        echo "1. Creating Departments..." . PHP_EOL;
        $departments = [
            ['id' => 1, 'name' => 'Commerce', 'code' => 'COM', 'description' => 'Department of Commerce'],
            ['id' => 2, 'name' => 'Science', 'code' => 'SCI', 'description' => 'Department of Science'],
            ['id' => 3, 'name' => 'Arts', 'code' => 'ART', 'description' => 'Department of Arts'],
            ['id' => 4, 'name' => 'Management', 'code' => 'MGT', 'description' => 'Department of Management'],
        ];
        DB::table('departments')->insert($departments);
        echo "   ✓ Created " . count($departments) . " departments" . PHP_EOL . PHP_EOL;

        // 2. Programs
        echo "2. Creating Programs..." . PHP_EOL;
        $programs = [
            ['id' => 1, 'name' => 'Bachelor of Commerce', 'short_name' => 'BCom', 'code' => 'B.COM', 'duration_years' => 3, 'department_id' => 1],
            ['id' => 2, 'name' => 'Bachelor of Science', 'short_name' => 'BSc', 'code' => 'B.SC', 'duration_years' => 3, 'department_id' => 2],
            ['id' => 3, 'name' => 'Bachelor of Arts', 'short_name' => 'BA', 'code' => 'B.A', 'duration_years' => 3, 'department_id' => 3],
            ['id' => 4, 'name' => 'Master of Commerce', 'short_name' => 'MCom', 'code' => 'M.COM', 'duration_years' => 2, 'department_id' => 1],
            ['id' => 5, 'name' => 'Master of Science', 'short_name' => 'MSc', 'code' => 'M.SC', 'duration_years' => 2, 'department_id' => 2],
            ['id' => 6, 'name' => 'Master of Arts', 'short_name' => 'MA', 'code' => 'M.A', 'duration_years' => 2, 'department_id' => 3],
            ['id' => 7, 'name' => 'MBA', 'short_name' => 'MBA', 'code' => 'MBA', 'duration_years' => 2, 'department_id' => 4],
            ['id' => 8, 'name' => 'BBA', 'short_name' => 'BBA', 'code' => 'BBA', 'duration_years' => 3, 'department_id' => 4],
        ];
        DB::table('programs')->insert($programs);
        echo "   ✓ Created " . count($programs) . " programs" . PHP_EOL . PHP_EOL;

        // 3. Academic Years
        echo "3. Creating Academic Years..." . PHP_EOL;
        $academicYears = [
            ['id' => 1, 'program_id' => 1, 'year_number' => 1, 'year_name' => '2024-25', 'semester_start' => 1, 'semester_end' => 2],
            ['id' => 2, 'program_id' => 1, 'year_number' => 2, 'year_name' => '2025-26', 'semester_start' => 1, 'semester_end' => 2],
        ];
        DB::table('academic_years')->insert($academicYears);
        echo "   ✓ Created " . count($academicYears) . " academic years" . PHP_EOL . PHP_EOL;

        // 4. Academic Sessions
        echo "4. Creating Academic Sessions..." . PHP_EOL;
        $sessions = [
            ['id' => 1, 'session_name' => '2024-25', 'start_date' => '2024-06-01', 'end_date' => '2025-05-31'],
            ['id' => 2, 'session_name' => '2025-26', 'start_date' => '2025-06-01', 'end_date' => '2026-05-31'],
        ];
        DB::table('academic_sessions')->insert($sessions);
        echo "   ✓ Created " . count($sessions) . " academic sessions" . PHP_EOL . PHP_EOL;

        // 5. Divisions
        echo "5. Creating Divisions..." . PHP_EOL;
        $divisions = [
            ['id' => 1, 'program_id' => 1, 'session_id' => 1, 'academic_year_id' => 1, 'division_name' => 'A', 'max_students' => 60],
            ['id' => 2, 'program_id' => 1, 'session_id' => 1, 'academic_year_id' => 1, 'division_name' => 'B', 'max_students' => 60],
            ['id' => 3, 'program_id' => 1, 'session_id' => 1, 'academic_year_id' => 1, 'division_name' => 'C', 'max_students' => 60],
            ['id' => 4, 'program_id' => 2, 'session_id' => 1, 'academic_year_id' => 1, 'division_name' => 'A', 'max_students' => 60],
            ['id' => 5, 'program_id' => 2, 'session_id' => 1, 'academic_year_id' => 1, 'division_name' => 'B', 'max_students' => 60],
            ['id' => 6, 'program_id' => 3, 'session_id' => 1, 'academic_year_id' => 1, 'division_name' => 'A', 'max_students' => 60],
            ['id' => 7, 'program_id' => 4, 'session_id' => 1, 'academic_year_id' => 1, 'division_name' => 'A', 'max_students' => 50],
            ['id' => 8, 'program_id' => 7, 'session_id' => 1, 'academic_year_id' => 1, 'division_name' => 'A', 'max_students' => 60],
            ['id' => 9, 'program_id' => 8, 'session_id' => 1, 'academic_year_id' => 1, 'division_name' => 'A', 'max_students' => 60],
            ['id' => 10, 'program_id' => 8, 'session_id' => 1, 'academic_year_id' => 1, 'division_name' => 'B', 'max_students' => 60],
        ];
        DB::table('divisions')->insert($divisions);
        echo "   ✓ Created " . count($divisions) . " divisions" . PHP_EOL . PHP_EOL;

        // 6. Subjects
        echo "6. Creating Subjects..." . PHP_EOL;
        $subjects = [
            ['id' => 1, 'program_id' => 1, 'academic_year_id' => 1, 'name' => 'Financial Accounting', 'code' => 'ACC101', 'credit' => 4, 'semester' => 1, 'type' => 'theory', 'max_marks' => 80, 'passing_marks' => 35],
            ['id' => 2, 'program_id' => 1, 'academic_year_id' => 1, 'name' => 'Business Management', 'code' => 'BUS101', 'credit' => 4, 'semester' => 1, 'type' => 'theory', 'max_marks' => 80, 'passing_marks' => 35],
            ['id' => 3, 'program_id' => 1, 'academic_year_id' => 1, 'name' => 'Micro Economics', 'code' => 'ECO101', 'credit' => 4, 'semester' => 1, 'type' => 'theory', 'max_marks' => 80, 'passing_marks' => 35],
            ['id' => 4, 'program_id' => 1, 'academic_year_id' => 1, 'name' => 'Business Mathematics', 'code' => 'MAT101', 'credit' => 3, 'semester' => 1, 'type' => 'theory', 'max_marks' => 80, 'passing_marks' => 35],
            ['id' => 5, 'program_id' => 1, 'academic_year_id' => 1, 'name' => 'Statistics', 'code' => 'STAT101', 'credit' => 3, 'semester' => 1, 'type' => 'theory', 'max_marks' => 80, 'passing_marks' => 35],
            ['id' => 6, 'program_id' => 1, 'academic_year_id' => 1, 'name' => 'Computer Applications', 'code' => 'COM101', 'credit' => 3, 'semester' => 1, 'type' => 'both', 'max_marks' => 60, 'passing_marks' => 25],
            ['id' => 7, 'program_id' => 1, 'academic_year_id' => 1, 'name' => 'Business English', 'code' => 'ENG101', 'credit' => 2, 'semester' => 1, 'type' => 'theory', 'max_marks' => 80, 'passing_marks' => 35],
            ['id' => 8, 'program_id' => 1, 'academic_year_id' => 1, 'name' => 'Business Law', 'code' => 'LAW101', 'credit' => 3, 'semester' => 1, 'type' => 'theory', 'max_marks' => 80, 'passing_marks' => 35],
        ];
        DB::table('subjects')->insert($subjects);
        echo "   ✓ Created " . count($subjects) . " subjects" . PHP_EOL . PHP_EOL;

        // 7. Users (Teachers & Admin)
        echo "7. Creating Users..." . PHP_EOL;
        $password = Hash::make('password');
        $users = [
            ['id' => 1, 'name' => 'Dr. Principal', 'email' => 'principal@schoolerp.com'],
            ['id' => 2, 'name' => 'Prof. Teacher', 'email' => 'teacher@schoolerp.com'],
            ['id' => 3, 'name' => 'Prof. Class Teacher', 'email' => 'class.teacher@schoolerp.com'],
            ['id' => 4, 'name' => 'Prof. Commerce HOD', 'email' => 'hod.commerce@schoolerp.com'],
            ['id' => 5, 'name' => 'Prof. Science HOD', 'email' => 'hod.science@schoolerp.com'],
            ['id' => 6, 'name' => 'Prof. Math Teacher', 'email' => 'math.teacher@schoolerp.com'],
            ['id' => 7, 'name' => 'Prof. English Teacher', 'email' => 'english.teacher@schoolerp.com'],
            ['id' => 8, 'name' => 'Prof. Physics Teacher', 'email' => 'physics.teacher@schoolerp.com'],
            ['id' => 9, 'name' => 'Prof. Chemistry Teacher', 'email' => 'chemistry.teacher@schoolerp.com'],
            ['id' => 10, 'name' => 'Prof. Biology Teacher', 'email' => 'biology.teacher@schoolerp.com'],
        ];
        foreach ($users as $user) {
            DB::table('users')->insert([
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'email_verified_at' => now(),
                'password' => $password,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        echo "   ✓ Created " . count($users) . " users (Password: password)" . PHP_EOL . PHP_EOL;

        // 8. Assign Roles
        echo "8. Assigning Roles..." . PHP_EOL;
        $roleAssignments = [
            [1, 'principal'],
            [2, 'teacher'],
            [3, 'class_teacher'],
            [4, 'hod_commerce'],
            [5, 'hod_science'],
        ];
        foreach ($roleAssignments as $assignment) {
            $roleId = DB::table('roles')->where('name', $assignment[1])->value('id');
            if ($roleId) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $roleId,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $assignment[0],
                ]);
            }
        }
        // Assign subject_teacher role to teachers 6-10
        $subjectTeacherRoleId = DB::table('roles')->where('name', 'subject_teacher')->value('id');
        for ($i = 6; $i <= 10; $i++) {
            DB::table('model_has_roles')->insert([
                'role_id' => $subjectTeacherRoleId,
                'model_type' => 'App\\Models\\User',
                'model_id' => $i,
            ]);
        }
        echo "   ✓ Roles assigned" . PHP_EOL . PHP_EOL;

        // 9. Teacher Profiles
        echo "9. Creating Teacher Profiles..." . PHP_EOL;
        $profiles = [
            ['user_id' => 1, 'employee_id' => 'EMP001', 'qualification' => 'M.Com, Ph.D', 'experience_years' => 20, 'specialization' => 'Accounting', 'designation' => 'Principal'],
            ['user_id' => 2, 'employee_id' => 'EMP002', 'qualification' => 'M.Com, B.Ed', 'experience_years' => 10, 'specialization' => 'Management', 'designation' => 'Senior Lecturer'],
            ['user_id' => 3, 'employee_id' => 'EMP003', 'qualification' => 'M.Com, B.Ed', 'experience_years' => 8, 'specialization' => 'Accounting', 'designation' => 'Lecturer'],
            ['user_id' => 4, 'employee_id' => 'EMP004', 'qualification' => 'M.Com, Ph.D', 'experience_years' => 15, 'specialization' => 'Commerce', 'designation' => 'Head of Department'],
            ['user_id' => 5, 'employee_id' => 'EMP005', 'qualification' => 'M.Sc, Ph.D', 'experience_years' => 15, 'specialization' => 'Science', 'designation' => 'Head of Department'],
            ['user_id' => 6, 'employee_id' => 'EMP006', 'qualification' => 'M.Sc, B.Ed', 'experience_years' => 12, 'specialization' => 'Mathematics', 'designation' => 'Lecturer'],
            ['user_id' => 7, 'employee_id' => 'EMP007', 'qualification' => 'M.A, B.Ed', 'experience_years' => 10, 'specialization' => 'English', 'designation' => 'Lecturer'],
            ['user_id' => 8, 'employee_id' => 'EMP008', 'qualification' => 'M.Sc, B.Ed', 'experience_years' => 10, 'specialization' => 'Physics', 'designation' => 'Lecturer'],
            ['user_id' => 9, 'employee_id' => 'EMP009', 'qualification' => 'M.Sc, B.Ed', 'experience_years' => 8, 'specialization' => 'Chemistry', 'designation' => 'Lecturer'],
            ['user_id' => 10, 'employee_id' => 'EMP010', 'qualification' => 'M.Sc, B.Ed', 'experience_years' => 6, 'specialization' => 'Biology', 'designation' => 'Lecturer'],
        ];
        foreach ($profiles as $profile) {
            $profile['phone'] = '+91 987654320' . $profile['user_id'];
            $profile['is_active'] = 1;
            $profile['created_at'] = now();
            $profile['updated_at'] = now();
            DB::table('teacher_profiles')->insert($profile);
        }
        echo "   ✓ Created " . count($profiles) . " teacher profiles" . PHP_EOL . PHP_EOL;

        // 10. Teacher Assignments
        echo "10. Creating Teacher Assignments..." . PHP_EOL;
        $assignments = [
            ['teacher_id' => 1, 'division_id' => 1, 'assignment_type' => 'division'],
            ['teacher_id' => 2, 'division_id' => 1, 'assignment_type' => 'division'],
            ['teacher_id' => 3, 'division_id' => 1, 'assignment_type' => 'division'],
            ['teacher_id' => 4, 'division_id' => 1, 'assignment_type' => 'department'],
            ['teacher_id' => 5, 'division_id' => 4, 'assignment_type' => 'department'],
            ['teacher_id' => 6, 'division_id' => 1, 'subject_id' => 4, 'assignment_type' => 'subject'],
            ['teacher_id' => 7, 'division_id' => 1, 'subject_id' => 7, 'assignment_type' => 'subject'],
            ['teacher_id' => 8, 'division_id' => 4, 'subject_id' => 1, 'assignment_type' => 'subject'],
            ['teacher_id' => 9, 'division_id' => 4, 'subject_id' => 2, 'assignment_type' => 'subject'],
            ['teacher_id' => 10, 'division_id' => 4, 'subject_id' => 3, 'assignment_type' => 'subject'],
        ];
        foreach ($assignments as $assignment) {
            $assignment['is_primary'] = 1;
            $assignment['created_at'] = now();
            $assignment['updated_at'] = now();
            DB::table('teacher_assignments')->insert($assignment);
        }
        echo "   ✓ Created " . count($assignments) . " teacher assignments" . PHP_EOL . PHP_EOL;

        // 11. Holidays
        echo "11. Creating Holidays..." . PHP_EOL;
        $holidays = [
            ['Republic Day', 'National Holiday', '2026-01-26', '2026-01-26', 'public_holiday'],
            ['Independence Day', 'National Holiday', '2026-08-15', '2026-08-15', 'public_holiday'],
            ['Gandhi Jayanti', 'National Holiday', '2026-10-02', '2026-10-02', 'public_holiday'],
            ['Diwali Break', 'Festival Holiday', '2026-11-10', '2026-11-12', 'school_holiday'],
            ['Christmas Break', 'Festival Holiday', '2026-12-24', '2026-12-26', 'school_holiday'],
            ['Annual Sports Day', 'Sports event', '2026-03-15', '2026-03-15', 'program'],
            ['Annual Day', 'Cultural program', '2026-04-20', '2026-04-20', 'program'],
            ['Science Exhibition', 'Science projects', '2026-05-10', '2026-05-12', 'event'],
            ['Summer Break', 'Summer vacation', '2026-05-15', '2026-06-30', 'school_holiday'],
            ['Teacher\'s Day', 'Teachers celebration', '2026-09-05', '2026-09-05', 'event'],
            ['Holi', 'Festival of Colors', '2026-03-07', '2026-03-07', 'school_holiday'],
            ['Eid', 'End of Ramadan', '2026-04-10', '2026-04-10', 'school_holiday'],
            ['Maharashtra Day', 'State Holiday', '2026-05-01', '2026-05-01', 'public_holiday'],
            ['Ganesh Chaturthi', 'Lord Ganesha festival', '2026-08-25', '2026-08-28', 'school_holiday'],
            ['Navratri', 'Nine nights festival', '2026-10-15', '2026-10-18', 'school_holiday'],
        ];
        foreach ($holidays as $holiday) {
            DB::table('holidays')->insert([
                'title' => $holiday[0],
                'description' => $holiday[1],
                'start_date' => $holiday[2],
                'end_date' => $holiday[3],
                'type' => $holiday[4],
                'is_recurring' => 0,
                'academic_year_id' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        echo "   ✓ Created " . count($holidays) . " holidays" . PHP_EOL . PHP_EOL;

        // 12. Timetables
        echo "12. Creating Timetables..." . PHP_EOL;
        $timetables = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $periods = [
            ['09:00:00', '10:00:00'],
            ['10:00:00', '11:00:00'],
            ['11:00:00', '12:00:00'],
            ['12:00:00', '13:00:00'],
            ['14:00:00', '15:00:00'],
            ['15:00:00', '16:00:00'],
        ];
        
        // Division A (30 periods)
        foreach ($days as $dayIndex => $day) {
            foreach ($periods as $periodIndex => $period) {
                $timetables[] = [
                    'division_id' => 1,
                    'subject_id' => ($dayIndex + $periodIndex) % 8 + 1,
                    'teacher_id' => ($dayIndex + $periodIndex) % 3 + 1,
                    'day_of_week' => $day,
                    'start_time' => $period[0],
                    'end_time' => $period[1],
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        // Division B (20 periods)
        for ($i = 0; $i < 20; $i++) {
            $day = $days[$i % 5];
            $period = $periods[$i % 6];
            $timetables[] = [
                'division_id' => 2,
                'subject_id' => ($i % 8) + 1,
                'teacher_id' => ($i % 3) + 1,
                'day_of_week' => $day,
                'start_time' => $period[0],
                'end_time' => $period[1],
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        DB::table('timetables')->insert($timetables);
        echo "   ✓ Created " . count($timetables) . " timetable entries" . PHP_EOL . PHP_EOL;

        // Summary
        echo "═══════════════════════════════════════════════" . PHP_EOL;
        echo "          SEEDING COMPLETE! 🎉                 " . PHP_EOL;
        echo "═══════════════════════════════════════════════" . PHP_EOL;
        echo "Departments:        " . DB::table('departments')->count() . PHP_EOL;
        echo "Programs:           " . DB::table('programs')->count() . PHP_EOL;
        echo "Divisions:          " . DB::table('divisions')->count() . PHP_EOL;
        echo "Subjects:           " . DB::table('subjects')->count() . PHP_EOL;
        echo "Users:              " . DB::table('users')->count() . PHP_EOL;
        echo "Teacher Profiles:   " . DB::table('teacher_profiles')->count() . PHP_EOL;
        echo "Holidays:           " . DB::table('holidays')->count() . PHP_EOL;
        echo "Timetables:         " . DB::table('timetables')->count() . PHP_EOL;
        echo "═══════════════════════════════════════════════" . PHP_EOL . PHP_EOL;
        
        echo "LOGIN CREDENTIALS:" . PHP_EOL;
        echo "───────────────────────────────────────────────" . PHP_EOL;
        echo "principal@schoolerp.com / password" . PHP_EOL;
        echo "teacher@schoolerp.com / password" . PHP_EOL;
        echo "class.teacher@schoolerp.com / password" . PHP_EOL;
        echo "hod.commerce@schoolerp.com / password" . PHP_EOL;
        echo "hod.science@schoolerp.com / password" . PHP_EOL;
        echo "═══════════════════════════════════════════════" . PHP_EOL;
    }
}


