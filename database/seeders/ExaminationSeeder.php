<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Result\Examination;
use App\Models\Result\StudentMark;
use App\Models\Result\Subject;
use App\Models\User\Student;
use App\Models\Academic\AcademicYear;
use Carbon\Carbon;

class ExaminationSeeder extends Seeder
{
    public function run()
    {
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            // Get any available academic year
            $academicYear = AcademicYear::first();
        }
        
        if (!$academicYear) {
            $this->command->warn('No academic year found. Using default year.');
            $academicYearName = '2025-2026';
        } else {
            $academicYearName = $academicYear->name ?? $academicYear->year_name ?? '2025-2026';
        }

        $examinations = [
            // Unit Tests
            [
                'name' => 'First Unit Test',
                'code' => 'UT1-2025',
                'type' => 'unit_test',
                'start_date' => Carbon::now()->subMonths(4)->format('Y-m-d'),
                'end_date' => Carbon::now()->subMonths(4)->addDays(5)->format('Y-m-d'),
                'academic_year' => $academicYearName,
                'status' => 'completed',
            ],
            [
                'name' => 'Second Unit Test',
                'code' => 'UT2-2025',
                'type' => 'unit_test',
                'start_date' => Carbon::now()->subMonths(2)->format('Y-m-d'),
                'end_date' => Carbon::now()->subMonths(2)->addDays(5)->format('Y-m-d'),
                'academic_year' => $academicYearName,
                'status' => 'completed',
            ],
            // Mid Term
            [
                'name' => 'Mid-Term Examination',
                'code' => 'MID-2025',
                'type' => 'midterm',
                'start_date' => Carbon::now()->subMonths(3)->format('Y-m-d'),
                'end_date' => Carbon::now()->subMonths(3)->addDays(10)->format('Y-m-d'),
                'academic_year' => $academicYearName,
                'status' => 'completed',
            ],
            // Final Exam (current/upcoming)
            [
                'name' => 'Final Examination',
                'code' => 'FINAL-2025',
                'type' => 'final',
                'start_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(45)->format('Y-m-d'),
                'academic_year' => $academicYearName,
                'status' => 'scheduled',
            ],
            // Practical Exam
            [
                'name' => 'Practical Examination',
                'code' => 'PRAC-2025',
                'type' => 'practical',
                'start_date' => Carbon::now()->addDays(20)->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(25)->format('Y-m-d'),
                'academic_year' => $academicYearName,
                'status' => 'scheduled',
            ],
        ];

        $createdExams = [];
        foreach ($examinations as $exam) {
            $examination = Examination::firstOrCreate(
                ['code' => $exam['code']],
                $exam
            );
            $createdExams[] = $examination;
            $this->command->info("Created examination: {$examination->name}");
        }

        // Now add sample student marks for completed examinations
        $this->addSampleMarks($createdExams);

        $this->command->info('✅ Created ' . count($createdExams) . ' examinations');
    }

    private function addSampleMarks($examinations)
    {
        $students = Student::where('student_status', 'active')->limit(10)->get();
        $subjects = Subject::where('is_active', true)->limit(5)->get();

        if ($students->isEmpty()) {
            $this->command->warn('No active students found. Skipping marks entry.');
            return;
        }

        if ($subjects->isEmpty()) {
            $this->command->warn('No active subjects found. Skipping marks entry.');
            return;
        }

        // Add marks only for completed examinations
        $completedExams = array_filter($examinations, function($exam) {
            return $exam->status === 'completed';
        });

        foreach ($completedExams as $examination) {
            foreach ($students as $student) {
                foreach ($subjects as $subject) {
                    // Check if marks already exist
                    $existingMark = StudentMark::where('student_id', $student->id)
                        ->where('examination_id', $examination->id)
                        ->where('subject_id', $subject->id)
                        ->first();

                    if (!$existingMark) {
                        // Generate random marks (weighted towards passing)
                        $random = rand(1, 100);
                        $marksObtained = $random < 30 ? rand(25, 39) : rand(40, 95);
                        $maxMarks = $subject->max_marks ?? 100;
                        
                        // Adjust if max marks is different
                        if ($maxMarks != 100) {
                            $marksObtained = round(($marksObtained / 100) * $maxMarks);
                        }

                        $percentage = ($marksObtained / $maxMarks) * 100;
                        $grade = $this->calculateGrade($percentage);
                        $result = $percentage >= 40 ? 'pass' : 'fail';

                        StudentMark::create([
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'examination_id' => $examination->id,
                            'marks_obtained' => $marksObtained,
                            'max_marks' => $maxMarks,
                            'grade' => $grade,
                            'result' => $result,
                            'is_approved' => true,
                        ]);
                    }
                }
            }
            $this->command->info("Added marks for examination: {$examination->name}");
        }
    }

    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }
}
