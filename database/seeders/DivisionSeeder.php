<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academic\Division;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = ['A', 'B', 'C'];
        
        // Get first program and session
        $programId = 1;
        $sessionId = 1;
        
        // Create divisions for each program
        $academicYears = [1, 2]; // BCOM FY, BSC FY
        $programId = 1; // Default program
        
        $id = 1;
        foreach ([1, 2] as $programId) { // BCOM and BSC programs
            foreach ($divisions as $divisionName) {
                Division::updateOrCreate(
                    ['id' => $id],
                    [
                        'program_id' => $programId,
                        'session_id' => $sessionId,
                        'academic_year_id' => 1,
                        'academic_year_id' => $academicYearId,
                        'program_id' => $programId,
                        'session_id' => $academicYearId, // Unique session per academic year
                        'division_name' => $divisionName,
                        'max_students' => 60,
                        'is_active' => true,
                    ]
                );
                $id++;
            }
        }
    }
}