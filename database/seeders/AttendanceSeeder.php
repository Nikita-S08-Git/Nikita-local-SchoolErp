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

        if ($divisions->isEmpty() || !$academicSession) {
            $this->command->warn('No divisions or active academic session found.');
            return;
        }

        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        foreach ($divisions->take(3) as $division) {
            $students = Student::where('division_id', $division->id)
                ->where('student_status', 'active')
                ->get();

            if ($students->isEmpty()) {
                continue;
            }

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                if ($date->isWeekend()) {
                    continue;
                }

                foreach ($students as $student) {
                    $status = rand(1, 100) <= 85 ? 'present' : 'absent';

                    Attendance::create([
                        'student_id' => $student->id,
                        'division_id' => $division->id,
                        'academic_session_id' => $academicSession->id,
                        'date' => $date->format('Y-m-d'),
                        'status' => $status,
                    ]);
                }
            }
        }

        $this->command->info('Attendance data seeded successfully for last 30 days!');
    }
}
