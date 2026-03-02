<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GlobalTimetableSeeder extends Seeder
{
    public function run(): void
    {
        echo "=== Seeding Global Timetable Enhancement Data ===" . PHP_EOL . PHP_EOL;

        // Get existing IDs
        $divisions = DB::table('divisions')->pluck('id', 'division_name');
        $subjects = DB::table('subjects')->pluck('id', 'code');
        $teachers = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', ['teacher', 'class_teacher', 'subject_teacher'])
            ->pluck('users.id')
            ->toArray();
        $academicYears = DB::table('academic_years')->pluck('id', 'name');

        if (empty($teachers)) {
            echo "⚠️  No teachers found. Please create teachers first." . PHP_EOL;
            return;
        }

        // 1. Seed Holidays
        echo "1. Seeding Holidays..." . PHP_EOL;
        $holidays = [
            ['Republic Day', 'National Holiday', '2026-01-26', '2026-01-26', 'public_holiday', 0, null],
            ['Independence Day', 'National Holiday', '2026-08-15', '2026-08-15', 'public_holiday', 0, null],
            ['Gandhi Jayanti', 'National Holiday', '2026-10-02', '2026-10-02', 'public_holiday', 0, null],
            ['Diwali Break', 'Festival Holiday', '2026-11-10', '2026-11-12', 'school_holiday', 0, null],
            ['Christmas Break', 'Festival Holiday', '2026-12-24', '2026-12-26', 'school_holiday', 0, null],
            ['Annual Sports Day', 'Annual sports event', '2026-03-15', '2026-03-15', 'program', 0, 'Main Ground'],
            ['Annual Day Function', 'Cultural program', '2026-04-20', '2026-04-20', 'program', 0, 'Auditorium'],
            ['Science Exhibition', 'Science projects', '2026-05-10', '2026-05-12', 'event', 0, 'Science Block'],
            ['Summer Break', 'Summer vacation', '2026-05-15', '2026-06-30', 'school_holiday', 0, null],
            ["Teacher's Day", 'Celebration', '2026-09-05', '2026-09-05', 'event', 0, 'Main Hall'],
        ];

        foreach ($holidays as $holiday) {
            DB::table('holidays')->insert([
                'title' => $holiday[0],
                'description' => $holiday[1],
                'start_date' => $holiday[2],
                'end_date' => $holiday[3],
                'type' => $holiday[4],
                'is_recurring' => $holiday[5],
                'academic_year_id' => reset($academicYears) ?: 1,
                'program_incharge_id' => $holiday[6] ? $teachers[array_rand($teachers)] : null,
                'location' => $holiday[6],
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        echo "   ✓ Created " . count($holidays) . " holidays/programs" . PHP_EOL . PHP_EOL;

        // 2. Seed Timetables
        echo "2. Seeding Timetables..." . PHP_EOL;
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $timeSlots = [
            ['09:00', '10:00', 'Period 1'],
            ['10:00', '11:00', 'Period 2'],
            ['11:00', '12:00', 'Period 3'],
            ['12:00', '13:00', 'Period 4'],
            ['14:00', '15:00', 'Period 5'],
            ['15:00', '16:00', 'Period 6'],
        ];

        $rooms = ['Room 101', 'Room 102', 'Room 201', 'Room 202', 'Computer Lab', 'Science Lab'];
        $subjectCodes = array_keys($subjects);
        
        $timetableCount = 0;

        foreach ($divisions as $divisionName => $divisionId) {
            foreach ($days as $dayIndex => $day) {
                foreach ($timeSlots as $slotIndex => $slot) {
                    $subjectCode = $subjectCodes[($dayIndex + $slotIndex) % count($subjectCodes)];
                    $subjectId = $subjects[$subjectCode] ?? reset($subjects);
                    $teacherId = $teachers[array_rand($teachers)];
                    
                    DB::table('timetables')->insert([
                        'division_id' => $divisionId,
                        'subject_id' => $subjectId,
                        'teacher_id' => $teacherId,
                        'day_of_week' => $day,
                        'start_time' => $slot[0] . ':00',
                        'end_time' => $slot[1] . ':00',
                        'period_name' => $slot[2],
                        'room_number' => $rooms[array_rand($rooms)],
                        'academic_year_id' => reset($academicYears) ?: 1,
                        'is_active' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $timetableCount++;
                }
            }
        }
        echo "   ✓ Created {$timetableCount} timetable entries" . PHP_EOL . PHP_EOL;

        // 3. Seed Program Participants
        echo "3. Seeding Program Participants..." . PHP_EOL;
        $programs = DB::table('holidays')->whereIn('type', ['program', 'event'])->get();
        $students = DB::table('students')->where('student_status', 'active')->pluck('id')->toArray();
        
        $participantCount = 0;
        foreach ($programs as $program) {
            // Add 5-10 student participants per program
            $studentCount = rand(5, 10);
            $selectedStudents = array_rand(array_flip($students), min($studentCount, count($students)));
            if (!is_array($selectedStudents)) {
                $selectedStudents = [$selectedStudents];
            }
            
            foreach ($selectedStudents as $studentId) {
                DB::table('program_participants')->insert([
                    'holiday_id' => $program->id,
                    'student_id' => $studentId,
                    'teacher_id' => $teachers[array_rand($teachers)],
                    'role' => ['Participant', 'Coordinator', 'Volunteer'][array_rand(['Participant', 'Coordinator', 'Volunteer'])],
                    'notes' => 'Assigned for ' . $program->title,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $participantCount++;
            }
        }
        echo "   ✓ Created {$participantCount} program participants" . PHP_EOL . PHP_EOL;

        // 4. Update Attendance with division_id and subject_id
        echo "4. Updating Attendance Records..." . PHP_EOL;
        $attendances = DB::table('attendance')
            ->join('students', 'attendance.student_id', '=', 'students.id')
            ->select('attendance.id', 'students.division_id')
            ->whereNull('attendance.division_id')
            ->get();

        $updatedCount = 0;
        foreach ($attendances as $attendance) {
            DB::table('attendance')
                ->where('id', $attendance->id)
                ->update([
                    'division_id' => $attendance->division_id,
                    'subject_id' => array_rand($subjects), // Random subject for existing records
                    'updated_at' => now(),
                ]);
            $updatedCount++;
        }
        echo "   ✓ Updated {$updatedCount} attendance records" . PHP_EOL . PHP_EOL;

        // Summary
        echo "═══════════════════════════════════════════════" . PHP_EOL;
        echo "          SEEDING COMPLETE!                    " . PHP_EOL;
        echo "═══════════════════════════════════════════════" . PHP_EOL;
        echo "Holidays:           " . DB::table('holidays')->count() . PHP_EOL;
        echo "Timetables:         " . DB::table('timetables')->count() . PHP_EOL;
        echo "Program Participants: " . DB::table('program_participants')->count() . PHP_EOL;
        echo "Updated Attendance: " . DB::table('attendance')->whereNotNull('division_id')->count() . PHP_EOL;
        echo "═══════════════════════════════════════════════" . PHP_EOL;
    }
}
