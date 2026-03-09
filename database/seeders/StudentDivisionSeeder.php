<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User\Student;
use App\Models\Academic\Division;
use App\Models\Academic\AcademicSession;

class StudentDivisionSeeder extends Seeder
{
    public function run()
    {
        $divisions = Division::where('is_active', true)->get();
        
        if ($divisions->isEmpty()) {
            $this->command->error('❌ No divisions found!');
            return;
        }

        // Get students without a division
        $studentsWithoutDivision = Student::whereNull('division_id')->orWhere('division_id', 0)->get();
        
        if ($studentsWithoutDivision->isEmpty()) {
            $this->command->info('ℹ️  All students already have divisions assigned!');
            return;
        }

        // Get or create current academic session
        $academicSession = AcademicSession::first();

        $this->command->info('');
        $this->command->info('👥 Assigning Students to Divisions...');
        $this->command->info('═══════════════════════════════════════');
        
        $totalAssigned = 0;
        
        // Distribute students across divisions
        $studentCount = $studentsWithoutDivision->count();
        $divisionCount = $divisions->count();
        $studentsPerDivision = ceil($studentCount / $divisionCount);
        
        $this->command->info("📊 Total students without division: {$studentCount}");
        $this->command->info("📊 Divisions available: {$divisionCount}");
        $this->command->info("📊 Students per division: ~{$studentsPerDivision}");
        
        $studentIndex = 0;
        $studentsArray = $studentsWithoutDivision->toArray();
        
        foreach ($divisions as $division) {
            $this->command->info('');
            $this->command->info("📚 Division: {$division->division_name}");
            
            // Get students for this division (those who already have this division_id)
            $existingStudents = Student::where('division_id', $division->id)->count();
            $this->command->info("   Existing students: {$existingStudents}");
            
            // Assign new students to this division
            $assignedCount = 0;
            for ($i = 0; $i < $studentsPerDivision && $studentIndex < count($studentsArray); $i++) {
                $student = Student::find($studentsArray[$studentIndex]['id']);
                if ($student) {
                    $student->update([
                        'division_id' => $division->id,
                        'academic_session_id' => $academicSession ? $academicSession->id : null,
                    ]);
                    $assignedCount++;
                    $totalAssigned++;
                    $studentIndex++;
                }
            }
            
            $this->command->info("   Newly assigned: {$assignedCount}");
        }

        $this->command->info('');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info("✅ Successfully assigned {$totalAssigned} students to divisions!");
        
        // Show summary
        $this->command->info('');
        $this->command->info('📊 Summary:');
        foreach ($divisions as $division) {
            $count = Student::where('division_id', $division->id)->count();
            $this->command->info("   {$division->division_name}: {$count} students");
        }
        $this->command->info('');
    }
}
