<?php

namespace App\Services;

use App\Models\User\Student;
use App\Models\User;
use App\Models\Academic\Admission;
use App\Repositories\StudentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Improved Student Service
 * 
 * Handles all business logic for student management
 * Features:
 * - Transaction management
 * - File handling
 * - Caching
 * - Event dispatching
 * - Audit logging
 * - Error handling
 */
class ImprovedStudentService
{
    public function __construct(
        private StudentRepository $repository,
        private RollNumberService $rollNumberService,
        private AuditLogService $auditLogService
    ) {}

    /**
     * Create new student with all related data
     * 
     * @param array $data
     * @return Student
     * @throws \Exception
     */
    public function createStudent(array $data): Student
    {
        return DB::transaction(function () use ($data) {
            // Create user account
            $user = $this->createUserAccount($data);
            
            // Generate unique numbers
            $admissionNumber = $this->generateAdmissionNumber();
            $rollNumber = $this->rollNumberService->generate(
                $data['program_id'],
                $data['academic_year'],
                $data['division_name'] ?? 'A'
            );
            
            // Handle file uploads
            $filePaths = $this->handleFileUploads($data);
            
            // Create student
            $student = $this->repository->create(array_merge($data, [
                'user_id' => $user->id,
                'admission_number' => $admissionNumber,
                'roll_number' => $rollNumber,
                'photo_path' => $filePaths['photo'] ?? null,
                'signature_path' => $filePaths['signature'] ?? null,
                'student_status' => $data['student_status'] ?? 'active',
            ]));
            
            // Create guardians if provided
            if (isset($data['guardians']) && is_array($data['guardians'])) {
                $this->createGuardians($student, $data['guardians']);
            }
            
            // Log creation
            $this->auditLogService->logEvent(
                $student,
                'created',
                null,
                $student->toArray()
            );
            
            // Dispatch event
            event(new \App\Events\StudentCreated($student));
            
            return $student;
        });
    }

    /**
     * Update student information
     * 
     * @param Student $student
     * @param array $data
     * @return Student
     */
    public function updateStudent(Student $student, array $data): Student
    {
        return DB::transaction(function () use ($student, $data) {
            $oldData = $student->toArray();
            
            // Handle file uploads
            $filePaths = $this->handleFileUploads($data, $student);
            
            if (!empty($filePaths)) {
                $data = array_merge($data, $filePaths);
            }
            
            // Update student
            $updatedStudent = $this->repository->update($student, $data);
            
            // Update user account if name changed
            if (isset($data['first_name']) || isset($data['last_name'])) {
                $student->user->update([
                    'name' => trim(
                        ($data['first_name'] ?? $student->first_name) . ' ' .
                        ($data['last_name'] ?? $student->last_name)
                    )
                ]);
            }
            
            // Log update
            $this->auditLogService->logEvent(
                $updatedStudent,
                'updated',
                $oldData,
                $updatedStudent->toArray()
            );
            
            // Dispatch event
            event(new \App\Events\StudentUpdated($updatedStudent));
            
            return $updatedStudent;
        });
    }

    /**
     * Delete student (soft delete)
     * 
     * @param Student $student
     * @return bool
     */
    public function deleteStudent(Student $student): bool
    {
        return DB::transaction(function () use ($student) {
            // Delete files
            $this->deleteStudentFiles($student);
            
            // Log deletion
            $this->auditLogService->logEvent(
                $student,
                'deleted',
                $student->toArray(),
                null
            );
            
            // Soft delete
            $result = $this->repository->delete($student);
            
            // Dispatch event
            if ($result) {
                event(new \App\Events\StudentDeleted($student));
            }
            
            return $result;
        });
    }

    /**
     * Enroll student from admission
     * 
     * @param Admission $admission
     * @return Student
     * @throws \Exception
     */
    public function enrollFromAdmission(Admission $admission): Student
    {
        if (!$admission->canBeEnrolled()) {
            throw new \Exception('Admission must be verified before enrollment');
        }
        
        if ($admission->student_id) {
            throw new \Exception('Student already enrolled from this admission');
        }
        
        return DB::transaction(function () use ($admission) {
            // Create user account
            $user = $this->createUserAccount([
                'first_name' => $admission->first_name,
                'last_name' => $admission->last_name,
                'email' => $admission->email,
            ]);
            
            // Generate roll number
            $rollNumber = $this->rollNumberService->generate(
                $admission->program_id,
                $admission->academic_year,
                $admission->division->division_name
            );
            
            // Create student
            $student = $this->repository->create([
                'user_id' => $user->id,
                'admission_number' => $admission->application_no,
                'roll_number' => $rollNumber,
                'first_name' => $admission->first_name,
                'middle_name' => $admission->middle_name,
                'last_name' => $admission->last_name,
                'date_of_birth' => $admission->date_of_birth,
                'gender' => $admission->gender,
                'blood_group' => $admission->blood_group,
                'religion' => $admission->religion,
                // caste removed from student payload

                'category' => $admission->category,
                'aadhar_number' => $admission->aadhar_number,
                'mobile_number' => $admission->mobile_number,
                'email' => $admission->email,
                'current_address' => $admission->current_address,
                'permanent_address' => $admission->permanent_address,
                'program_id' => $admission->program_id,
                'academic_year' => $admission->academic_year,
                'division_id' => $admission->division_id,
                'academic_session_id' => $admission->academic_session_id,
                'student_status' => 'active',
                'admission_date' => now(),
            ]);
            
            // Link admission to student
            $admission->update([
                'status' => 'enrolled',
                'student_id' => $student->id
            ]);
            
            // Log enrollment
            $this->auditLogService->logEvent(
                $student,
                'enrolled',
                null,
                array_merge($student->toArray(), [
                    'admission_id' => $admission->id,
                    'roll_number' => $rollNumber
                ])
            );
            
            return $student;
        });
    }

    /**
     * Change student status
     * 
     * @param Student $student
     * @param string $newStatus
     * @param string|null $reason
     * @return Student
     */
    public function changeStatus(Student $student, string $newStatus, ?string $reason = null): Student
    {
        $oldStatus = $student->student_status;
        
        $student = $this->repository->update($student, [
            'student_status' => $newStatus
        ]);
        
        // Log status change
        $this->auditLogService->logEvent(
            $student,
            'status_changed',
            ['status' => $oldStatus],
            ['status' => $newStatus, 'reason' => $reason]
        );
        
        return $student;
    }

    /**
     * Get student profile with all related data
     * 
     * @param int $studentId
     * @return array
     */
    public function getStudentProfile(int $studentId): array
    {
        return Cache::remember("student.profile.{$studentId}", 3600, function () use ($studentId) {
            $student = $this->repository->findWithRelations($studentId, [
                'program',
                'division',
                'academicSession',
                'guardians',
                'fees',
                'admission'
            ]);
            
            if (!$student) {
                throw new \Exception('Student not found');
            }
            
            return [
                'student' => $student,
                'statistics' => [
                    'total_fees' => $student->fees->sum('amount'),
                    'paid_fees' => $student->fees->sum('paid_amount'),
                    'pending_fees' => $student->fees->sum(fn($fee) => $fee->amount - $fee->paid_amount),
                    'attendance_percentage' => $this->calculateAttendancePercentage($student),
                ],
                'recent_activities' => $this->auditLogService->getRecentActivities($student, 10),
            ];
        });
    }

    /**
     * Bulk update student status
     * 
     * @param array $studentIds
     * @param string $status
     * @return int
     */
    public function bulkUpdateStatus(array $studentIds, string $status): int
    {
        $updated = $this->repository->bulkUpdateStatus($studentIds, $status);
        
        // Log bulk update
        $this->auditLogService->logBulkAction(
            'students',
            'bulk_status_update',
            ['student_ids' => $studentIds, 'new_status' => $status]
        );
        
        return $updated;
    }

    /**
     * Create user account for student
     * 
     * @param array $data
     * @return User
     */
    private function createUserAccount(array $data): User
    {
        $email = $data['email'] ?? 
                 strtolower($data['first_name'] . '.' . $data['last_name']) . '@student.local';
        
        $user = User::create([
            'name' => trim($data['first_name'] . ' ' . ($data['last_name'] ?? '')),
            'email' => $email,
            'password' => Hash::make('student123'), // Default password
            'email_verified_at' => now(),
        ]);
        
        $user->assignRole('student');
        
        return $user;
    }

    /**
     * Generate unique admission number
     * 
     * @return string
     */
    private function generateAdmissionNumber(): string
    {
        $year = date('Y');
        $sequence = Student::whereYear('created_at', $year)->count() + 1;
        
        return 'ADM' . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Handle file uploads
     * 
     * @param array $data
     * @param Student|null $student
     * @return array
     */
    private function handleFileUploads(array $data, ?Student $student = null): array
    {
        $filePaths = [];
        
        // Handle photo upload
        if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete old photo if exists
            if ($student && $student->photo_path) {
                Storage::disk('public')->delete($student->photo_path);
            }
            
            $filePaths['photo_path'] = $data['photo']->store('uploads/students/photos', 'public');
        }
        
        // Handle signature upload
        if (isset($data['signature']) && $data['signature'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete old signature if exists
            if ($student && $student->signature_path) {
                Storage::disk('public')->delete($student->signature_path);
            }
            
            $filePaths['signature_path'] = $data['signature']->store('uploads/students/signatures', 'public');
        }
        
        return $filePaths;
    }

    /**
     * Delete student files
     * 
     * @param Student $student
     * @return void
     */
    private function deleteStudentFiles(Student $student): void
    {
        if ($student->photo_path) {
            Storage::disk('public')->delete($student->photo_path);
        }
        
        if ($student->signature_path) {
            Storage::disk('public')->delete($student->signature_path);
        }
    }

    /**
     * Create guardians for student
     * 
     * @param Student $student
     * @param array $guardians
     * @return void
     */
    private function createGuardians(Student $student, array $guardians): void
    {
        foreach ($guardians as $guardianData) {
            $student->guardians()->create($guardianData);
        }
    }

    /**
     * Calculate attendance percentage
     * 
     * @param Student $student
     * @return float
     */
    private function calculateAttendancePercentage(Student $student): float
    {
        // This is a placeholder - implement based on your attendance system
        $totalClasses = $student->attendances()->count();
        $presentClasses = $student->attendances()->where('status', 'present')->count();
        
        return $totalClasses > 0 ? ($presentClasses / $totalClasses) * 100 : 0;
    }
}
