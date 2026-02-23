<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Result\Examination;
use App\Models\Academic\AcademicSession;
use Carbon\Carbon;

class ExaminationSeeder extends Seeder
{
    public function run()
    {
        $session = AcademicSession::where('is_active', true)->first();
        
        if (!$session) {
            $this->command->warn('No active academic session found.');
            return;
        }

        $examinations = [
            [
                'name' => 'First Unit Test',
                'code' => 'UT1-2025',
                'type' => 'unit_test',
                'start_date' => Carbon::now()->addDays(10),
                'end_date' => Carbon::now()->addDays(15),
                'academic_year' => $session->session_name,
                'status' => 'scheduled',
            ],
            [
                'name' => 'Mid-Term Examination',
                'code' => 'MID-2025',
                'type' => 'midterm',
                'start_date' => Carbon::now()->addDays(30),
                'end_date' => Carbon::now()->addDays(40),
                'academic_year' => $session->session_name,
                'status' => 'scheduled',
            ],
            [
                'name' => 'Second Unit Test',
                'code' => 'UT2-2025',
                'type' => 'unit_test',
                'start_date' => Carbon::now()->addDays(60),
                'end_date' => Carbon::now()->addDays(65),
                'academic_year' => $session->session_name,
                'status' => 'scheduled',
            ],
            [
                'name' => 'Final Examination',
                'code' => 'FINAL-2025',
                'type' => 'final',
                'start_date' => Carbon::now()->addDays(90),
                'end_date' => Carbon::now()->addDays(100),
                'academic_year' => $session->session_name,
                'status' => 'scheduled',
            ],
        ];

        foreach ($examinations as $exam) {
            Examination::create($exam);
        }

        $this->command->info('✅ Created ' . count($examinations) . ' examinations');
    }
}
