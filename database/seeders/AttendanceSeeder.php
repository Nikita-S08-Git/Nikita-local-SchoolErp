<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academic\Attendance;
use App\Models\Academic\Division;
use App\Models\Academic\AcademicSession;
use App\Models\User\Student;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run()
    {
        $divisions = Division::where('is_active', true)->get();
        $academicSession = AcademicSession::where('is_active', true)->first();

        if ($divisions->isEmpty()) {
            $this->command->warn('No divisions found. Please seed divisions first.');
            return;
        }

        if (!$academicSession) {
            $this->command->warn('No active academic session found.');
            return;
        }

        // Get last 30 working days (excluding weekends)
        $startDate = Carbon::now()->subDays(45);
        $endDate = Carbon::now();

        $attendanceData = [];
        $count = 0;

        foreach ($divisions as $division) {
            $students = Student::where('division_id', $division->id)
                ->where('student_status', 'active')
                ->get();

            if ($students->isEmpty()) {
                $this->command->warn("No active students in division: {$division->division_name}");
                continue;
            }

            $this->command->info("Creating attendance for division: {$division->division_name} ({$students->count()} students)");

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                // Skip weekends
                if ($date->isWeekend()) {
                    continue;
                }

                foreach ($students as $student) {
                    // 85% attendance rate
                    $rand = rand(1, 100);
                    
                    if ($rand <= 85) {
                        $status = 'present';
                    } elseif ($rand <= 92) {
                        $status = 'absent';
                    } else {
                        $status = 'late';
                    }

                    $attendanceData[] = [
                        'student_id' => $student->id,
                        'division_id' => $division->id,
                        'academic_session_id' => $academicSession->id,
                        'date' => $date->format('Y-m-d'),
                        'status' => $status,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $count++;
                }
            }
        }

        // Insert in batches of 500
        if (!empty($attendanceData)) {
            $existingCount = Attendance::count();
            
            if ($existingCount > 0) {
                $this->command->info("⚠️  Found {$existingCount} existing attendance records. Clearing them...");
                Attendance::truncate();
            }

            $batches = array_chunk($attendanceData, 500);
            foreach ($batches as $batch) {
                Attendance::insert($batch);
            }

            $this->command->info("✅ Created {$count} attendance records for {$divisions->count()} divisions!");
            $this->command->info('Attendance covers last 45 days (weekdays only) with ~85% attendance rate.');
        } else {
            $this->command->warn('No attendance data created. Check if students are assigned to divisions.');
        }
    }
}
