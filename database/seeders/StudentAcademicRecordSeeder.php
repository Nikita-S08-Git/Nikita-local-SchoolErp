<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\User\Student;
use App\Models\Academic\AcademicSession;
use App\Models\Academic\AcademicYear;
use App\Models\Academic\Program;
use App\Models\Academic\Division;
use App\Models\Academic\StudentAcademicRecord;
use Illuminate\Database\Seeder;

class StudentAcademicRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get active academic year (per program)
        $academicYears = AcademicYear::where('is_active', true)->get();
        
        if ($academicYears->isEmpty()) {
            // Create academic years for each program
            $programs = Program::where('is_active', true)->get();
            
            foreach ($programs as $program) {
                AcademicYear::firstOrCreate(
                    ['program_id' => $program->id, 'year_number' => 1],
                    [
                        'year_name' => 'Year 1',
                        'semester_start' => 1,
                        'semester_end' => 2,
                        'is_active' => true,
                        'start_date' => '2025-06-01',
                        'end_date' => '2026-05-31',
                    ]
                );
            }
            
            $academicYears = AcademicYear::where('is_active', true)->get();
            $this->command->info("Created academic years for programs");
        }

        // Get active session (if exists)
        $session = AcademicSession::where('is_active', true)->first();
        
        if (!$session) {
            $session = AcademicSession::create([
                'session_name' => '2025-26',
                'start_date' => '2025-06-01',
                'end_date' => '2026-05-31',
                'is_active' => true,
            ]);
            $this->command->info("Created active session: {$session->session_name}");
        }

        $totalRecordsCreated = 0;
        
        // Add academic records for ALL existing students
        $allStudents = Student::where('student_status', 'active')
            ->whereNotNull('program_id')
            ->get();
        
        $this->command->info("Found {$allStudents->count()} active students with program_id");
        
        foreach ($allStudents as $student) {
            // Check if record already exists for this session
            $existingRecord = StudentAcademicRecord::where('student_id', $student->id)
                ->where('academic_session_id', $session->id)
                ->first();
            
            if (!$existingRecord) {
                // Get the student's program
                $program = Program::find($student->program_id);
                if (!$program) continue;
                
                // Get academic year
                $academicYear = AcademicYear::where('program_id', $program->id)
                    ->where('is_active', true)
                    ->first();
                
                // Get or create division with proper academic_year_id
                $division = null;
                if ($student->division_id) {
                    $division = Division::find($student->division_id);
                }
                
                if (!$division && $academicYear) {
                    $division = Division::firstOrCreate(
                        ['program_id' => $program->id, 'division_name' => 'A', 'academic_year_id' => $academicYear->id],
                        [
                            'session_id' => $session->id,
                            'max_students' => 60,
                            'is_active' => true,
                        ]
                    );
                }
                
                // Determine result status based on student count (for variety)
                $studentCount = StudentAcademicRecord::count();
                $resultStatus = $studentCount % 3 === 0 ? 'fail' : ($studentCount % 3 === 1 ? 'atkt' : 'pass');
                $backlogCount = $resultStatus === 'pass' ? 0 : ($resultStatus === 'atkt' ? 1 : 2);
                
                // Create academic record
                StudentAcademicRecord::create([
                    'student_id' => $student->id,
                    'academic_session_id' => $session->id,
                    'program_id' => $program->id,
                    'academic_year' => $session->session_name,
                    'division_id' => $division ? $division->id : null,
                    'result_status' => $resultStatus,
                    'promotion_status' => 'not_eligible',
                    'backlog_count' => $backlogCount,
                    'attendance_percentage' => rand(70, 95),
                    'attendance_status' => 'eligible',
                    'fee_cleared' => true,
                    'outstanding_amount' => 0,
                ]);
                
                $totalRecordsCreated++;
            }
        }
        
        $this->command->info("Added {$totalRecordsCreated} academic records for existing students");

        // Create next session for promotion target
        $nextSessionName = '2026-27';
        $nextSession = AcademicSession::where('session_name', $nextSessionName)->first();
        
        if (!$nextSession) {
            $nextSession = AcademicSession::create([
                'session_name' => $nextSessionName,
                'start_date' => '2026-06-01',
                'end_date' => '2027-05-31',
                'is_active' => false,
            ]);
            
            // Create divisions for next session
            foreach ($academicYears as $academicYear) {
                $program = $academicYear->program;
                Division::firstOrCreate(
                    ['program_id' => $program->id, 'division_name' => 'A', 'academic_year_id' => $academicYear->id],
                    [
                        'session_id' => $nextSession->id,
                        'max_students' => 60,
                        'is_active' => true,
                    ]
                );
            }
            
            $this->command->info("Created next session: {$nextSessionName} with divisions");
        }

        $this->command->info('Student Academic Records seeded successfully!');
        $this->command->info("Current session: {$session->session_name} (ID: {$session->id})");
        $this->command->info("Next session for promotion: {$nextSession->session_name} (ID: {$nextSession->id})");
    }
}
