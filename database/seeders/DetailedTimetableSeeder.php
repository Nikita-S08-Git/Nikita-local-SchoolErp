<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academic\Timetable;
use App\Models\Academic\Division;
use App\Models\User;
use App\Models\Academic\Subject;
use App\Models\Academic\Room;
use App\Models\Academic\AcademicYear;

class DetailedTimetableSeeder extends Seeder
{
    public function run()
    {
        // Don't truncate - just add to existing data
        
        $divisions = Division::where('is_active', true)->get();
        $teachers = User::role('teacher')->get();
        $subjects = Subject::all();
        $rooms = Room::all();
        
        // Get or create current academic year
        $academicYear = AcademicYear::first();

        if ($divisions->isEmpty()) {
            $this->command->error('❌ No divisions found!');
            return;
        }

        if ($teachers->isEmpty()) {
            $this->command->error('❌ No teachers found!');
            return;
        }

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        
        $timeSlots = [
            ['09:00:00', '10:00:00', 'Period 1'],
            ['10:00:00', '11:00:00', 'Period 2'],
            ['11:00:00', '12:00:00', 'Period 3'],
            ['12:00:00', '13:00:00', 'Period 4'],
            ['14:00:00', '15:00:00', 'Period 5'],
            ['15:00:00', '16:00:00', 'Period 6'],
        ];

        $this->command->info('');
        $this->command->info('🗓️  Creating Timetable Schedules...');
        $this->command->info('═══════════════════════════════════════');

        $totalCount = 0;
        $totalStudents = 0;

        foreach ($divisions as $index => $division) {
            // Get students for this division
            $students = $division->students()->where('student_status', 'active')->get();
            $studentCount = $students->count();
            $studentNames = $students->take(5)->pluck('first_name')->implode(', ');
            if ($studentCount > 5) {
                $studentNames .= ' +' . ($studentCount - 5) . ' more';
            }
            
            $this->command->info('');
            $this->command->info("📚 Division: {$division->division_name} ({$studentCount} students: {$studentNames})");
            $this->command->line('───────────────────────────────────────');
            
            $divisionCount = 0;

            foreach ($days as $dayIndex => $day) {
                $periodsForDay = 5; // 5 periods per day
                
                for ($i = 0; $i < $periodsForDay; $i++) {
                    $slot = $timeSlots[$i];
                    $teacher = $teachers->random();
                    $subject = $subjects->random();
                    $room = $rooms->random() ?? null;
                    
                    Timetable::create([
                        'division_id' => $division->id,
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'room_id' => $room ? $room->id : null,
                        'day_of_week' => $day,
                        'start_time' => $slot[0],
                        'end_time' => $slot[1],
                        'period_name' => $slot[2],
                        'room_number' => $room ? $room->room_number : 'Room ' . (101 + ($index * 5) + $i),
                        'academic_year_id' => $academicYear ? $academicYear->id : null,
                        'is_active' => true,
                        'is_break_time' => false,
                        'status' => 'active',
                    ]);
                    
                    $divisionCount++;
                    $totalCount++;
                }
                
                $this->command->info("  ✓ {$day}: {$periodsForDay} periods assigned");
            }
            
            $totalStudents += $studentCount;
            $this->command->info("  Total: {$divisionCount} entries, {$studentCount} students");
        }

        $this->command->info('');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info("✅ Successfully created {$totalCount} timetable entries!");
        $this->command->info("📊 Divisions: {$divisions->count()} | Days: " . count($days) . " | Periods/Day: 5");
        $this->command->info("👥 Total Students: {$totalStudents}");
        $this->command->info('');
        $this->command->info('🌐 View at: http://127.0.0.1:8000/academic/timetable');
        $this->command->info('');
    }
}
