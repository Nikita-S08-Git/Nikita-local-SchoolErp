<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance\Timetable;
use App\Models\Academic\Division;
use App\Models\User;

class TimetableSeeder extends Seeder
{
    public function run()
    {
        $divisions = Division::where('is_active', true)->get();
        $teachers = User::role('teacher')->get();

        if ($divisions->isEmpty() || $teachers->isEmpty()) {
            $this->command->warn('No divisions or teachers found. Please seed divisions and teachers first.');
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
            ['09:00', '10:00'],
            ['10:00', '11:00'],
            ['11:00', '12:00'],
            ['12:00', '13:00'],
            ['14:00', '15:00'],
            ['15:00', '16:00'],
        ];

        $rooms = [];
        for ($i = 101; $i <= 120; $i++) {
            $rooms[] = 'Room ' . $i;
        }

        $count = 0;

        foreach ($divisions as $division) {
            $this->command->info("Creating timetable for: {$division->division_name}");
            
            foreach ($days as $day) {
                $periodsForDay = rand(4, 6);
                $selectedSlots = array_slice($timeSlots, 0, $periodsForDay);
                
                foreach ($selectedSlots as $slot) {
                    $teacher = $teachers->random();
                    $subject = $subjects[array_rand($subjects)];
                    $room = $rooms[array_rand($rooms)];
                    
                    Timetable::create([
                        'division_id' => $division->id,
                        'teacher_id' => $teacher->id,
                        'subject' => $subject,
                        'day_of_week' => $day,
                        'start_time' => $slot[0],
                        'end_time' => $slot[1],
                        'room' => $room,
                    ]);
                    
                    $count++;
                }
            }
        }

        $this->command->info("✅ Created {$count} timetable entries for {$divisions->count()} divisions!");
        $this->command->info('Each day has 4-6 periods with assigned teachers, subjects, and rooms.');
    }
}
