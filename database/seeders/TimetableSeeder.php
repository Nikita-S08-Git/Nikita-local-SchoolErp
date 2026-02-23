<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance\Timetable;
use App\Models\Academic\Division;
use App\Models\Result\Subject;
use App\Models\User;

class TimetableSeeder extends Seeder
{
    public function run()
    {
        $divisions = Division::where('is_active', true)->get();
        $teachers = User::role('teacher')->where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();

        if ($divisions->isEmpty()) {
            $this->command->warn('No divisions found. Please seed divisions first.');
            return;
        }

        if ($teachers->isEmpty()) {
            $this->command->warn('No teachers found. Please seed teachers first.');
            return;
        }

        if ($subjects->isEmpty()) {
            $this->command->warn('No subjects found. Please seed subjects first.');
            return;
        }

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        // Define unique time slots for each day to avoid conflicts
        $timeSlots = [
            ['09:00', '10:00'],
            ['10:00', '11:00'],
            ['11:00', '12:00'],
            ['12:00', '13:00'],
            ['14:00', '15:00'],
            ['15:00', '16:00'],
        ];

        $rooms = ['101', '102', '103', '104', '105', '201', '202', 'Lab 1', 'Lab 2', 'Seminar Hall'];

        $count = 0;
        $existingCount = Timetable::count();

        if ($existingCount > 0) {
            $this->command->info("⚠️  Found {$existingCount} existing timetable entries. Clearing them...");
            Timetable::truncate();
            $count = 0;
        }

        foreach ($divisions as $division) {
            $this->command->info("Creating timetable for: {$division->division_name}");

            foreach ($days as $day) {
                // Use all 6 time slots for each day, no duplicates
                $usedSlots = [];

                foreach ($timeSlots as $slot) {
                    // Skip if this exact slot is already used for this division on this day
                    $slotKey = $slot[0];
                    if (in_array($slotKey, $usedSlots)) {
                        continue;
                    }
                    $usedSlots[] = $slotKey;

                    $teacher = $teachers->random();
                    $subject = $subjects->random();
                    $room = $rooms[array_rand($rooms)];

                    // Check if entry already exists before creating
                    $exists = Timetable::where('division_id', $division->id)
                        ->where('day_of_week', $day)
                        ->where('start_time', $slot[0])
                        ->exists();

                    if (!$exists) {
                        Timetable::create([
                            'division_id' => $division->id,
                            'teacher_id' => $teacher->id,
                            'subject_id' => $subject->id,
                            'day_of_week' => $day,
                            'start_time' => $slot[0],
                            'end_time' => $slot[1],
                            'room' => $room,
                            'is_active' => true,
                        ]);

                        $count++;
                    }
                }
            }
        }

        $this->command->info("✅ Created {$count} timetable entries for {$divisions->count()} divisions!");
        $this->command->info('Each division has 6 periods per day (9:00-16:00) with assigned teachers, subjects, and rooms.');
    }
}
