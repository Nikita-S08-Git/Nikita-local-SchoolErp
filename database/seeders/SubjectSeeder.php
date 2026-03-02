<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Result\Subject;
use App\Models\Academic\Program;
use App\Models\Academic\AcademicYear;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            1 => [
                ['name' => 'Financial Accounting', 'code' => 'FAC101', 'credit' => 4],
                ['name' => 'Business Economics', 'code' => 'BEC102', 'credit' => 3],
                ['name' => 'Cost Accounting', 'code' => 'CAC103', 'credit' => 4],
                ['name' => 'Income Tax', 'code' => 'ITX104', 'credit' => 3],
                ['name' => 'Auditing', 'code' => 'AUD105', 'credit' => 3],
                ['name' => 'Business Management', 'code' => 'BMT106', 'credit' => 3],
                ['name' => 'Corporate Law', 'code' => 'CLW107', 'credit' => 3],
            ],
            2 => [
                ['name' => 'Physics', 'code' => 'PHY101', 'credit' => 4],
                ['name' => 'Chemistry', 'code' => 'CHM102', 'credit' => 4],
                ['name' => 'Mathematics', 'code' => 'MAT103', 'credit' => 4],
                ['name' => 'Computer Science', 'code' => 'CSC104', 'credit' => 4],
                ['name' => 'Electronics', 'code' => 'ELN105', 'credit' => 3],
                ['name' => 'Biology', 'code' => 'BIO106', 'credit' => 3],
                ['name' => 'Environmental Science', 'code' => 'ENS107', 'credit' => 2],
            ]
        ];

        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            $this->command->error('No active academic year found. Please create one first.');
            return;
        }

        foreach ($programs as $programId => $subjects) {
            $program = Program::find($programId);
            
            if (!$program) {
                $this->command->warn("Program ID {$programId} not found. Skipping...");
                continue;
            }

            foreach ($subjects as $subjectData) {
                Subject::firstOrCreate(
                    ['code' => $subjectData['code'], 'program_id' => $programId],
                    [
                        'academic_year_id' => $academicYear->id,
                        'name' => $subjectData['name'],
                        'credit' => $subjectData['credit'],
                        'type' => 'theory',
                        'max_marks' => 100,
                        'passing_marks' => 40,
                        'is_active' => true,
                        'semester' => 1,
                    ]
                );
            }
            
            $this->command->info("Created subjects for: {$program->name}");
        }

        $this->command->info('Total subjects: ' . Subject::count());
    }
}
