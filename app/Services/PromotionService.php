<?php

namespace App\Services;

use App\Models\User\Student;
use App\Models\Academic\StudentAcademicRecord;
use App\Models\Academic\PromotionLog;
use App\Models\Academic\AcademicSession;
use App\Models\Academic\Program;
use App\Models\Academic\Division;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Promotion Service
 *
 * Handles all student promotion logic including eligibility checking,
 * promotion execution, and audit logging.
 *
 * Key Features:
 * - Eligibility validation (academic, attendance, fee)
 * - ATKT conditional promotion support
 * - Override with authorization
 * - Complete audit trail
 * - Transaction-safe execution
 *
 * @package App\Services
 */
class PromotionService
{
    /**
     * Promotion eligibility criteria
     */
    protected array $eligibilityCriteria;

    /**
     * Create a new PromotionService instance.
     *
     * @param array $eligibilityCriteria
     */
    public function __construct(array $eligibilityCriteria = [])
    {
        // Default criteria from config
        $this->eligibilityCriteria = array_merge([
            'minimum_attendance' => config('schoolerp.attendance.minimum_percentage', 75),
            'attendance_grace' => config('schoolerp.attendance.grace_percentage', 5),
            'pass_percentage' => config('schoolerp.results.pass_percentage', 40),
            'max_atkt_subjects' => 3, // Maximum failed subjects for ATKT
            'fee_clearance_required' => false, // Can be overridden per institution
            'compulsory_subjects' => [], // Subject IDs that must be passed
        ], $eligibilityCriteria);
    }

    /**
     * Check if a student is eligible for promotion.
     *
     * @param StudentAcademicRecord $currentRecord
     * @param array $criteria
     * @return array
     */
    public function checkEligibility(StudentAcademicRecord $currentRecord, array $criteria = []): array
    {
        $criteria = array_merge($this->eligibilityCriteria, $criteria);

        $result = [
            'eligible' => true,
            'conditional' => false,
            'reasons' => [],
            'warnings' => [],
            'data' => [
                'result_status' => $currentRecord->result_status,
                'attendance_percentage' => $currentRecord->attendance_percentage,
                'attendance_status' => $currentRecord->attendance_status,
                'fee_cleared' => $currentRecord->fee_cleared,
                'outstanding_amount' => $currentRecord->outstanding_amount,
                'backlog_count' => $currentRecord->backlog_count,
            ],
        ];

        // Check result status
        if (!in_array($currentRecord->result_status, [
            StudentAcademicRecord::STATUS_PASS,
            StudentAcademicRecord::STATUS_ATKT,
            StudentAcademicRecord::STATUS_COMPLETED,
        ])) {
            $result['eligible'] = false;
            $result['reasons'][] = "Result status must be PASS, ATKT, or COMPLETED. Current: {$currentRecord->result_status}";
        }

        // Check attendance
        if ($currentRecord->attendance_status === StudentAcademicRecord::ATTENDANCE_NOT_ELIGIBLE) {
            $result['eligible'] = false;
            $result['reasons'][] = "Attendance not eligible ({$currentRecord->attendance_percentage}%)";
        } elseif ($currentRecord->attendance_status === StudentAcademicRecord::ATTENDANCE_CONDONABLE) {
            $result['warnings'][] = "Attendance is condonable ({$currentRecord->attendance_percentage}%)";
        }

        // Check fee clearance
        if ($criteria['fee_clearance_required'] && !$currentRecord->hasFeesCleared()) {
            $result['eligible'] = false;
            $result['reasons'][] = "Fees not cleared (Outstanding: ₹{$currentRecord->outstanding_amount})";
        }

        // Check ATKT status
        if ($currentRecord->result_status === StudentAcademicRecord::STATUS_ATKT) {
            if ($currentRecord->backlog_count > $criteria['max_atkt_subjects']) {
                $result['eligible'] = false;
                $result['reasons'][] = "Exceeds maximum ATKT subjects ({$currentRecord->backlog_count} > {$criteria['max_atkt_subjects']})";
            } else {
                $result['conditional'] = true;
                $result['warnings'][] = "Conditional promotion (ATKT with {$currentRecord->backlog_count} backlogs)";
            }
        }

        // Check if record is locked
        if ($currentRecord->is_locked) {
            $result['warnings'][] = "Academic record is locked";
        }

        return $result;
    }

    /**
     * Get list of students eligible for promotion.
     *
     * @param int $fromSessionId
     * @param int|null $programId
     * @param array $criteria
     * @return \Illuminate\Support\Collection
     */
    public function getEligibleStudents(
        int $fromSessionId,
        ?int $programId = null,
        array $criteria = []
    ): \Illuminate\Support\Collection {
        $query = StudentAcademicRecord::with(['student', 'program', 'division'])
            ->forSession($fromSessionId)
            ->whereNotIn('result_status', [
                StudentAcademicRecord::STATUS_FAIL,
                StudentAcademicRecord::STATUS_TC_ISSUED,
            ]);

        if ($programId) {
            $query->where('program_id', $programId);
        }

        $records = $query->get();

        return $records->filter(function ($record) use ($criteria) {
            $eligibility = $this->checkEligibility($record, $criteria);
            return $eligibility['eligible'];
        })->values();
    }

    /**
     * Promote a student to the next academic session.
     *
     * @param Student $student
     * @param AcademicSession $toSession
     * @param Program $toProgram
     * @param string $toAcademicYear
     * @param Division $toDivision
     * @param User $promotedBy
     * @param bool $isOverride
     * @param string|null $overrideReason
     * @return array
     * @throws \Exception
     */
    public function promoteStudent(
        Student $student,
        AcademicSession $toSession,
        Program $toProgram,
        string $toAcademicYear,
        Division $toDivision,
        User $promotedBy,
        bool $isOverride = false,
        ?string $overrideReason = null
    ): array {
        return DB::transaction(function () use (
            $student,
            $toSession,
            $toProgram,
            $toAcademicYear,
            $toDivision,
            $promotedBy,
            $isOverride,
            $overrideReason
        ) {
            // Get current academic record
            $currentRecord = $student->currentAcademicRecord;
            
            if (!$currentRecord) {
                throw new \Exception("No current academic record found for student");
            }

            // Check eligibility
            $eligibility = $this->checkEligibility($currentRecord);
            
            if (!$eligibility['eligible'] && !$isOverride) {
                throw new \Exception(
                    "Student not eligible for promotion: " . implode(', ', $eligibility['reasons'])
                );
            }

            // Determine promotion type
            $promotionType = $this->determinePromotionType($currentRecord, $eligibility);

            // Create new academic record
            $newRecord = StudentAcademicRecord::create([
                'student_id' => $student->id,
                'academic_session_id' => $toSession->id,
                'program_id' => $toProgram->id,
                'academic_year' => $toAcademicYear,
                'division_id' => $toDivision->id,
                'result_status' => StudentAcademicRecord::STATUS_ACTIVE,
                'promotion_status' => StudentAcademicRecord::PROMOTION_PROMOTED,
                'backlog_count' => $eligibility['conditional'] ? $currentRecord->backlog_count : 0,
                'current_atkt_attempt' => $eligibility['conditional'] ? 1 : 0,
                'fee_cleared' => true, // Reset for new session
                'outstanding_amount' => 0,
            ]);

            // Update current record promotion status
            $currentRecord->updatePromotionStatus(
                $eligibility['conditional'] 
                    ? StudentAcademicRecord::PROMOTION_CONDITIONALLY_PROMOTED
                    : StudentAcademicRecord::PROMOTION_PROMOTED
            );

            // Create promotion log
            $promotionLog = PromotionLog::create([
                'student_id' => $student->id,
                'from_academic_session_id' => $currentRecord->academic_session_id,
                'from_program_id' => $currentRecord->program_id,
                'from_academic_year' => $currentRecord->academic_year,
                'from_division_id' => $currentRecord->division_id,
                'from_result_status' => $currentRecord->result_status,
                'to_academic_session_id' => $toSession->id,
                'to_program_id' => $toProgram->id,
                'to_academic_year' => $toAcademicYear,
                'to_division_id' => $toDivision->id,
                'to_result_status' => StudentAcademicRecord::STATUS_ACTIVE,
                'promotion_type' => $promotionType,
                'was_eligible' => $eligibility['eligible'],
                'attendance_percentage' => $currentRecord->attendance_percentage,
                'fee_cleared' => $currentRecord->fee_cleared,
                'backlog_count' => $currentRecord->backlog_count,
                'promoted_by' => $promotedBy->id,
                'promoted_by_role' => $promotedBy->roles->first()?->name ?? 'user',
                'is_override' => $isOverride,
                'override_reason' => $overrideReason,
                'new_academic_record_id' => $newRecord->id,
                'status' => PromotionLog::STATUS_COMPLETED,
            ]);

            // Log the promotion
            Log::info("Student promoted", [
                'student_id' => $student->id,
                'from_session' => $currentRecord->academicSession->name,
                'to_session' => $toSession->name,
                'promotion_type' => $promotionType,
                'is_override' => $isOverride,
                'user_id' => $promotedBy->id,
            ]);

            return [
                'success' => true,
                'message' => "Student promoted successfully",
                'promotion_type' => $promotionType,
                'is_conditional' => $eligibility['conditional'],
                'new_record_id' => $newRecord->id,
                'log_id' => $promotionLog->id,
                'eligibility' => $eligibility,
            ];
        });
    }

    /**
     * Bulk promote multiple students.
     *
     * @param array $studentIds
     * @param AcademicSession $toSession
     * @param Program $toProgram
     * @param string $toAcademicYear
     * @param Division $toDivision
     * @param User $promotedBy
     * @return array
     */
    public function bulkPromote(
        array $studentIds,
        AcademicSession $toSession,
        Program $toProgram,
        string $toAcademicYear,
        Division $toDivision,
        User $promotedBy
    ): array {
        $results = [
            'total' => count($studentIds),
            'successful' => 0,
            'failed' => 0,
            'conditional' => 0,
            'errors' => [],
            'promotion_logs' => [],
        ];

        foreach ($studentIds as $studentId) {
            try {
                $student = Student::findOrFail($studentId);
                
                $result = $this->promoteStudent(
                    $student,
                    $toSession,
                    $toProgram,
                    $toAcademicYear,
                    $toDivision,
                    $promotedBy
                );

                if ($result['success']) {
                    $results['successful']++;
                    if ($result['is_conditional']) {
                        $results['conditional']++;
                    }
                    $results['promotion_logs'][] = $result['log_id'];
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'student_id' => $studentId,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Determine the promotion type based on record and eligibility.
     *
     * @param StudentAcademicRecord $record
     * @param array $eligibility
     * @return string
     */
    protected function determinePromotionType(
        StudentAcademicRecord $record,
        array $eligibility
    ): string {
        if ($eligibility['conditional']) {
            return PromotionLog::TYPE_CONDITIONALLY_PROMOTED;
        }

        if ($record->result_status === StudentAcademicRecord::STATUS_COMPLETED) {
            return PromotionLog::TYPE_TC_ISSUED;
        }

        return PromotionLog::TYPE_PROMOTED;
    }

    /**
     * Get promotion preview for a student.
     *
     * @param Student $student
     * @param AcademicSession $toSession
     * @param Program $toProgram
     * @param string $toAcademicYear
     * @param Division $toDivision
     * @return array
     */
    public function getPromotionPreview(
        Student $student,
        AcademicSession $toSession,
        Program $toProgram,
        string $toAcademicYear,
        Division $toDivision
    ): array {
        $currentRecord = $student->currentAcademicRecord;
        
        if (!$currentRecord) {
            return [
                'eligible' => false,
                'error' => 'No current academic record found',
            ];
        }

        $eligibility = $this->checkEligibility($currentRecord);

        return [
            'student' => [
                'id' => $student->id,
                'name' => $student->full_name,
                'admission_number' => $student->admission_number,
            ],
            'current' => [
                'session' => $currentRecord->academicSession->name,
                'program' => $currentRecord->program->name,
                'year' => $currentRecord->academic_year,
                'division' => $currentRecord->division->division_name,
                'result_status' => $currentRecord->result_status,
                'attendance' => $currentRecord->attendance_percentage,
                'backlogs' => $currentRecord->backlog_count,
            ],
            'proposed' => [
                'session' => $toSession->name,
                'program' => $toProgram->name,
                'year' => $toAcademicYear,
                'division' => $toDivision->division_name,
            ],
            'eligibility' => $eligibility,
            'promotion_type' => $this->determinePromotionType($currentRecord, $eligibility),
            'can_promote' => $eligibility['eligible'] || $eligibility['conditional'],
        ];
    }

    /**
     * Rollback a promotion.
     *
     * @param int $promotionLogId
     * @param User $rolledBackBy
     * @return array
     * @throws \Exception
     */
    public function rollbackPromotion(int $promotionLogId, User $rolledBackBy): array
    {
        return DB::transaction(function () use ($promotionLogId, $rolledBackBy) {
            $promotionLog = PromotionLog::findOrFail($promotionLogId);

            if ($promotionLog->status !== PromotionLog::STATUS_COMPLETED) {
                throw new \Exception("Cannot rollback promotion with status: {$promotionLog->status}");
            }

            // Mark promotion log as rolled back
            $promotionLog->markRolledBack();

            // Delete the new academic record if it exists
            if ($promotionLog->new_academic_record_id) {
                $newRecord = StudentAcademicRecord::findOrFail($promotionLog->new_academic_record_id);
                $newRecord->delete();
            }

            // Reset the old record status
            $oldRecord = StudentAcademicRecord::where('student_id', $promotionLog->student_id)
                ->forSession($promotionLog->from_academic_session_id)
                ->first();

            if ($oldRecord) {
                $oldRecord->update([
                    'promotion_status' => StudentAcademicRecord::PROMOTION_REPEATED,
                ]);
            }

            Log::warning("Promotion rolled back", [
                'promotion_log_id' => $promotionLogId,
                'student_id' => $promotionLog->student_id,
                'rolled_back_by' => $rolledBackBy->id,
            ]);

            return [
                'success' => true,
                'message' => 'Promotion rolled back successfully',
                'rolled_back_by' => $rolledBackBy->id,
            ];
        });
    }
}
