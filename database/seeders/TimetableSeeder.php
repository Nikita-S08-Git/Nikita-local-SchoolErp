<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academic\Timetable;
use App\Models\Academic\Division;
use App\Models\Result\Subject;
use App\Models\Academic\TimeSlot;
use App\Models\Academic\AcademicYear;
use App\Models\User;

class TimetableSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Timetables...');

        // Get active academic year
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            $this->command->error('No active academic year found!');
            return;
        }

        // Get teacher
        $teacher = User::role('teacher')->first();
        if (!$teacher) {
            $this->command->error('No teacher found!');
            return;
        }

        // Get divisions
        $divisions = Division::where('is_active', true)->get();
        if ($divisions->isEmpty()) {
            $this->command->error('No divisions found!');
            return;
        }

        // Get subjects
        $subjects = Subject::where('is_active', true)->get();
        if ($subjects->isEmpty()) {
            $this->command->error('No subjects found!');
            return;
        }

        // Get time slots
        $timeSlots = TimeSlot::where('is_active', true)->orderBy('start_time')->get();
        if ($timeSlots->isEmpty()) {
            $this->command->error('No time slots found!');
            return;
        }

        // Days of the week
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        $count = 0;
        $subjectIndex = 0;

        foreach ($divisions as $division) {
            $this->command->info("Creating timetable for Division {$division->division_name}...");

            foreach ($days as $day) {
                $slotIndex = 0;

                // Schedule 6 periods per day (skip lunch break at period 5)
                for ($period = 1; $period <= 6; $period++) {
                    // Skip period 5 (14:00-15:00) for lunch break
                    if ($period == 5) {
                        continue;
                    }

                    $timeSlot = $timeSlots->get($slotIndex);
                    if (!$timeSlot) {
                        $slotIndex++;
                        continue;
                    }

                    // Get subject (cycle through subjects)
                    $subject = $subjects->get($subjectIndex % $subjects->count());

                    // Create timetable entry
                    Timetable::firstOrCreate(
                        [
                            'division_id' => $division->id,
                            'day_of_week' => $day,
                            'start_time' => $timeSlot->start_time,
                            'end_time' => $timeSlot->end_time,
                        ],
                        [
                            'subject_id' => $subject->id,
                            'teacher_id' => $teacher->id,
                            'period_name' => 'Period ' . $period,
                            'room_number' => 'Room ' . chr(64 + $division->id), // A, B, C, etc.
                            'academic_year_id' => $academicYear->id,
                            'is_break_time' => false,
                            'is_active' => true,
                        ]
                    );

                    $count++;
                    $slotIndex++;
                    $subjectIndex++;
                }
            }
        }

        $this->command->info("Created {$count} timetable entries!");
    }
}
