<?php

namespace Database\Seeders;

use App\Models\Result\Examination;
use App\Models\Academic\Subject;
use Illuminate\Database\Seeder;

class ExamSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $examinations = Examination::whereNull('subject_id')->get();
        
        $subjects = Subject::where('is_active', true)->get();
        
        if ($subjects->isEmpty()) {
            $this->command->warn('No active subjects found. Please create subjects first.');
            return;
        }
        
        foreach ($examinations as $examination) {
            // Try to match subject based on exam name
            $matchedSubject = null;
            
            // Look for subject with similar name in the exam
            foreach ($subjects as $subject) {
                if (stripos($examination->name, $subject->name) !== false || 
                    stripos($subject->name, $examination->name) !== false) {
                    $matchedSubject = $subject;
                    break;
                }
            }
            
            // If no match by name, try to find by program
            if (!$matchedSubject && $examination->academic_year) {
                // Try to find subjects from any program
                $matchedSubject = $subjects->random();
            }
            
            // If still no match, assign random subject
            if (!$matchedSubject) {
                $matchedSubject = $subjects->random();
            }
            
            $examination->update([
                'subject_id' => $matchedSubject->id
            ]);
            
            $this->command->info("Assigned subject '{$matchedSubject->name}' to exam '{$examination->name}'");
        }
        
        $this->command->info("Successfully assigned subjects to {$examinations->count()} examinations");
    }
}
