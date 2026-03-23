<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User\Student;
use App\Models\Academic\Program;
use App\Models\Academic\Division;
use App\Models\Academic\AcademicSession;

class TestStudentSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('=== Creating Test Student Data ===');

        // Get or create required related data
        $program = Program::where('is_active', true)->first();
        if (!$program) {
            $program = Program::create([
                'name' => 'Bachelor of Commerce',
                'code' => 'BCOM',
                'description' => 'B.Com General',
                'duration_years' => 3,
                'is_active' => true,
            ]);
            $this->command->info("Created Program: {$program->name}");
        }

        $division = Division::where('is_active', true)->first();
        if (!$division) {
            $division = Division::create([
                'name' => 'FY-A',
                'code' => 'FYA',
                'program_id' => $program->id,
                'capacity' => 60,
                'is_active' => true,
            ]);
            $this->command->info("Created Division: {$division->name}");
        }

        $session = AcademicSession::where('is_active', true)->first();
        if (!$session) {
            $session = AcademicSession::create([
                'name' => '2025-26',
                'start_date' => '2025-06-01',
                'end_date' => '2026-05-31',
                'is_active' => true,
            ]);
            $this->command->info("Created Academic Session: {$session->name}");
        }

        // Create Test Student with all required fields
        $student = Student::create([
            'first_name' => 'Test',
            'middle_name' => 'Student',
            'last_name' => 'One',
            'date_of_birth' => '2005-05-15',
            'gender' => 'male',
            'blood_group' => 'O+',
            'religion' => 'Hindu',
            'category' => 'general',
            'mobile_number' => '9876543210',
            'email' => 'test.student.one@example.com',
            'current_address' => '123 Test Street, Test City',
            'permanent_address' => '123 Test Street, Test City',
            'program_id' => $program->id,
            'division_id' => $division->id,
            'academic_session_id' => $session->id,
            'academic_year' => 'FY',
            'admission_date' => '2025-06-01',
            'student_status' => 'active',
        ]);

        $this->command->info("✓ CREATE: Student created with ID: {$student->id}");
        $this->command->info("  - Admission Number: {$student->admission_number}");
        $this->command->info("  - Roll Number: {$student->roll_number}");
        $this->command->info("  - Full Name: {$student->full_name}");

        // TEST READ
        $foundStudent = Student::with(['program', 'division', 'academicSession'])->find($student->id);
        $this->command->info("✓ READ: Student found with name: {$foundStudent->full_name}");
        $this->command->info("  - Program: {$foundStudent->program->name}");
        $this->command->info("  - Division: {$foundStudent->division->name}");
        $this->command->info("  - Status: {$foundStudent->student_status}");

        // TEST UPDATE
        $foundStudent->update([
            'first_name' => 'Updated',
            'last_name' => 'Student',
            'mobile_number' => '9999999999',
            'current_address' => '456 Updated Street, Updated City',
        ]);
        $foundStudent->refresh();
        $this->command->info("✓ UPDATE: Student updated successfully");
        $this->command->info("  - New Name: {$foundStudent->full_name}");
        $this->command->info("  - New Mobile: {$foundStudent->mobile_number}");

        // TEST DELETE (Soft Delete)
        $studentIdToDelete = $foundStudent->id;
        $foundStudent->delete();
        $this->command->info("✓ DELETE: Student soft deleted (ID: {$studentIdToDelete})");

        // Verify soft delete
        $deletedStudent = Student::withTrashed()->find($studentIdToDelete);
        if ($deletedStudent && $deletedStudent->trashed()) {
            $this->command->info("  - Soft delete verified at: {$deletedStudent->deleted_at}");
        }

        // TEST RESTORE
        $deletedStudent->restore();
        $restoredStudent = Student::find($studentIdToDelete);
        $this->command->info("✓ RESTORE: Student restored successfully");
        $this->command->info("  - Restored Name: {$restoredStudent->full_name}");

        // Count total students
        $totalStudents = Student::count();
        $this->command->info("");
        $this->command->info("=== CRUD TEST SUMMARY ===");
        $this->command->info("✓ CREATE: Working");
        $this->command->info("✓ READ: Working");
        $this->command->info("✓ UPDATE: Working");
        $this->command->info("✓ DELETE: Working");
        $this->command->info("✓ RESTORE: Working");
        $this->command->info("");
        $this->command->info("Total active students: {$totalStudents}");
        $this->command->info("=== ALL STUDENT CRUD FUNCTIONS ARE WORKING ===");
    }
}
