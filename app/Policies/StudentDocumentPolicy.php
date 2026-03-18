<?php

namespace App\Policies;

use App\Models\User\User;
use App\Models\User\Student;
use App\Models\Academic\Division;

class StudentDocumentPolicy
{
    /**
     * Determine if the user can view the student's documents
     * 
     * Access Rules:
     * - Admin: Can view all documents
     * - Principal: Can view all school students' documents
     * - Teacher: Can view documents of students in their assigned divisions
     * - Parent: Can view their own child's documents
     * - Student: Can view their own documents
     * - Others: No access
     */
    public function viewDocument(User $user, Student $student): bool
    {
        // Admin can view all documents
        if ($user->hasRole('admin')) {
            return true;
        }

        // Principal can view all school students' documents
        if ($user->hasRole('principal')) {
            return true;
        }

        // Student can view their own documents
        if ($user->hasRole('student') && $user->id === $student->user_id) {
            return true;
        }

        // Parent can view their child's documents
        if ($user->hasRole('parent')) {
            // Check if user is parent of this student
            // Assuming parent has email linked to student's guardian email
            return $this->isParentOfStudent($user, $student);
        }

        // Teacher can view documents of students in their assigned divisions
        if ($user->hasRole('teacher')) {
            return $this->isTeacherOfStudent($user, $student);
        }

        // Admission officer can view documents during admission process
        if ($user->hasRole('student_section')) {
            return true;
        }

        // Accounts staff can view documents for fee-related purposes
        if ($user->hasRole('accounts_staff')) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can download the student's documents
     */
    public function download(User $user, Student $student): bool
    {
        return $this->viewDocument($user, $student);
    }

    /**
     * Check if user is parent of the student
     */
    private function isParentOfStudent(User $user, Student $student): bool
    {
        // Check through guardians table
        if ($student->guardians) {
            foreach ($student->guardians as $guardian) {
                if ($guardian->email === $user->email) {
                    return true;
                }
            }
        }

        // Alternative: Check if student's email matches parent's email pattern
        // This is a fallback if guardians relationship is not loaded
        if ($student->email && str_contains($student->email, $user->email)) {
            return true;
        }

        return false;
    }

    /**
     * Check if user is teacher of the student
     */
    private function isTeacherOfStudent(User $user, Student $student): bool
    {
        // Check if teacher is assigned to student's division
        if ($student->division) {
            $teacherProfile = $user->teacherProfile;
            
            if ($teacherProfile) {
                // Check if teacher is assigned to this division
                $assignedDivisions = $teacherProfile->divisions ?? collect();
                
                foreach ($assignedDivisions as $division) {
                    if ($division->id === $student->division_id) {
                        return true;
                    }
                }
            }
        }

        // Check through TeacherSubject relationship
        if ($student->division && $student->division->subjects) {
            $teacherProfile = $user->teacherProfile;
            
            if ($teacherProfile) {
                $taughtSubjects = $teacherProfile->subjects ?? collect();
                
                foreach ($student->division->subjects as $subject) {
                    foreach ($taughtSubjects as $taughtSubject) {
                        if ($taughtSubject->id === $subject->id) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
}
