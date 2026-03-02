<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance\Timetable;
use App\Models\Academic\Division;
use App\Models\User;

class DetailedTimetableSeeder extends Seeder
{
    public function run()
    {
        Timetable::truncate();
        
        $divisions = Division::where('is_active', true)->get();
        $teachers = User::role('teacher')->get();

        if ($divisions->isEmpty()) {
            $this->command->error('❌ No divisions found!');
            return;
        }

        if ($teachers->isEmpty()) {
            $this->command->error('❌ No teachers found!');
            return;
        }

        $subjects = [
            'Mathematics',
            'English',
            'Science',
            'History',
            'Geography',
            'Physics',
            'Chemistry',
            'Biology',
            'Computer Science',
            'Physical Education'
        ];

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        $timeSlots = [
            ['09:00', '10:00', 'Period 1'],
            ['10:00', '11:00', 'Period 2'],
            ['11:00', '12:00', 'Period 3'],
            ['12:00', '13:00', 'Period 4'],
            ['14:00', '15:00', 'Period 5'],
            ['15:00', '16:00', 'Period 6'],
        ];

        $this->command->info('');
        $this->command->info('🗓️  Creating Timetable Schedules...');
        $this->command->info('═══════════════════════════════════════');

        $totalCount = 0;

        foreach ($divisions as $index => $division) {
            $this->command->info('');
            $this->command->info("📚 Division: {$division->division_name}");
            $this->command->line('───────────────────────────────────────');
            
            $divisionCount = 0;

            foreach ($days as $day) {
                $periodsForDay = 5; // 5 periods per day
                
                for ($i = 0; $i < $periodsForDay; $i++) {
                    $slot = $timeSlots[$i];
                    $teacher = $teachers->random();
                    $subject = $subjects[array_rand($subjects)];
                    $room = 'Room ' . (101 + ($index * 5) + $i);
                    
                    Timetable::create([
                        'division_id' => $division->id,
                        'teacher_id' => $teacher->id,
                        'subject' => $subject,
                        'day_of_week' => $day,
                        'start_time' => $slot[0],
                        'end_time' => $slot[1],
                        'room' => $room,
                    ]);
                    
                    $divisionCount++;
                    $totalCount++;
                }
                
                $this->command->info("  ✓ {$day}: {$periodsForDay} periods assigned");
            }
            
            $this->command->info("  Total: {$divisionCount} entries");
        }

        $this->command->info('');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info("✅ Successfully created {$totalCount} timetable entries!");
        $this->command->info("📊 Divisions: {$divisions->count()} | Days: 6 | Periods/Day: 5");
        $this->command->info('');
        $this->command->info('🌐 View at: http://127.0.0.1:8000/academic/timetable');
        $this->command->info('');
    }
}
