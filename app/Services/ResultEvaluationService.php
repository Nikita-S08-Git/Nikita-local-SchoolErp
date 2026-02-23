<?php

namespace App\Services;

use App\Models\User\Student;
use App\Models\Academic\StudentAcademicRecord;
use App\Models\Result\StudentMark;
use App\Models\Result\Examination;
use App\Services\RuleEngineService;

/**
 * Result Evaluation Service
 *
 * Evaluates student examination results using configurable rules.
 * Determines PASS/FAIL/ATKT status based on institutional rules.
 *
 * Key Features:
 * - Subject-wise evaluation
 * - Aggregate percentage calculation
 * - ATKT eligibility checking
 * - Compulsory subject handling
 * - Grace marks application
 *
 * @package App\Services
 */
class ResultEvaluationService
{
    /**
     * Result status constants
     */
    const STATUS_PASS = 'pass';
    const STATUS_FAIL = 'fail';
    const STATUS_ATKT = 'atkt';
    const STATUS_NOT_ELIGIBLE = 'not_eligible';

    public function __construct(
        private RuleEngineService $ruleEngine
    ) {}

    /**
     * Evaluate student result for an examination.
     *
     * @param Student $student
     * @param Examination $examination
     * @param StudentAcademicRecord $academicRecord
     * @return array
     */
    public function evaluateResult(
        Student $student,
        Examination $examination,
        StudentAcademicRecord $academicRecord
    ): array {
        // Get applicable rules
        $rules = $this->getApplicableRules($academicRecord);

        // Get student marks
        $marks = $this->getStudentMarks($student->id, $examination->id);

        if (empty($marks)) {
            return [
                'status' => self::STATUS_NOT_ELIGIBLE,
                'reason' => 'No marks found',
                'percentage' => 0,
                'failed_subjects' => [],
                'passed_subjects' => [],
            ];
        }

        // Evaluate each subject
        $subjectResults = $this->evaluateSubjects($marks, $rules);

        // Count failed subjects
        $failedSubjects = $subjectResults['failed'];
        $failedCount = count($failedSubjects);

        // Calculate aggregate percentage
        $aggregatePercentage = $this->calculateAggregatePercentage($marks);

        // Determine final status
        $result = $this->determineResultStatus(
            $failedCount,
            $failedSubjects,
            $aggregatePercentage,
            $rules,
            $academicRecord
        );

        return [
            'status' => $result['status'],
            'reason' => $result['reason'],
            'percentage' => round($aggregatePercentage, 2),
            'failed_subjects' => $failedSubjects,
            'passed_subjects' => $subjectResults['passed'],
            'subject_count' => [
                'total' => count($marks),
                'passed' => count($subjectResults['passed']),
                'failed' => $failedCount,
            ],
            'rules_applied' => $rules,
        ];
    }

    /**
     * Evaluate individual subjects.
     *
     * @param array $marks
     * @param array $rules
     * @return array
     */
    protected function evaluateSubjects(array $marks, array $rules): array
    {
        $passed = [];
        $failed = [];

        foreach ($marks as $mark) {
            $subjectResult = $this->evaluateSubject($mark, $rules);
            
            if ($subjectResult['passed']) {
                $passed[] = [
                    'subject_id' => $mark->subject_id,
                    'subject_name' => $mark->subject->name ?? 'Unknown',
                    'marks_obtained' => $mark->marks_obtained,
                    'max_marks' => $mark->max_marks,
                    'percentage' => $subjectResult['percentage'],
                ];
            } else {
                $failed[] = [
                    'subject_id' => $mark->subject_id,
                    'subject_name' => $mark->subject->name ?? 'Unknown',
                    'marks_obtained' => $mark->marks_obtained,
                    'max_marks' => $mark->max_marks,
                    'percentage' => $subjectResult['percentage'],
                    'is_compulsory' => $subjectResult['is_compulsory'],
                    'deficit' => $subjectResult['deficit'],
                ];
            }
        }

        return [
            'passed' => $passed,
            'failed' => $failed,
        ];
    }

    /**
     * Evaluate a single subject.
     *
     * @param StudentMark $mark
     * @param array $rules
     * @return array
     */
    protected function evaluateSubject(StudentMark $mark, array $rules): array
    {
        $passPercentage = $rules['pass_percentage'];
        $graceMarks = $rules['grace_marks'];

        $maxMarks = $mark->max_marks;
        $obtainedMarks = $mark->marks_obtained;

        // Calculate raw percentage
        $percentage = $maxMarks > 0 ? ($obtainedMarks / $maxMarks) * 100 : 0;

        // Calculate passing marks
        $passingMarks = ($passPercentage / 100) * $maxMarks;

        // Apply grace marks if applicable
        $effectiveMarks = $obtainedMarks;
        $graceApplied = 0;

        if ($obtainedMarks < $passingMarks && $obtainedMarks + $graceMarks >= $passingMarks) {
            $graceApplied = ceil($passingMarks) - $obtainedMarks;
            $effectiveMarks = $obtainedMarks + $graceApplied;
            $percentage = ($effectiveMarks / $maxMarks) * 100;
        }

        // Determine if passed
        $passed = $effectiveMarks >= $passingMarks;

        // Calculate deficit (how many marks short)
        $deficit = $passed ? 0 : ceil($passingMarks) - $obtainedMarks;

        // Check if subject is compulsory
        $isCompulsory = in_array($mark->subject_id, $rules['compulsory_subjects']);

        return [
            'passed' => $passed,
            'percentage' => $percentage,
            'is_compulsory' => $isCompulsory,
            'deficit' => $deficit,
            'grace_applied' => $graceApplied,
        ];
    }

    /**
     * Determine final result status.
     *
     * @param int $failedCount
     * @param array $failedSubjects
     * @param float $aggregatePercentage
     * @param array $rules
     * @param StudentAcademicRecord $academicRecord
     * @return array
     */
    protected function determineResultStatus(
        int $failedCount,
        array $failedSubjects,
        float $aggregatePercentage,
        array $rules,
        StudentAcademicRecord $academicRecord
    ): array {
        // No failures = PASS
        if ($failedCount === 0) {
            return [
                'status' => self::STATUS_PASS,
                'reason' => 'Passed all subjects',
            ];
        }

        // Check for compulsory subject failures
        $compulsoryFailures = array_filter(
            $failedSubjects,
            fn($s) => $s['is_compulsory']
        );

        if (count($compulsoryFailures) > 0) {
            return [
                'status' => self::STATUS_FAIL,
                'reason' => 'Failed in compulsory subject(s)',
            ];
        }

        // Check ATKT eligibility
        $maxAtktSubjects = $rules['atkt_max_subjects'];

        if ($failedCount <= $maxAtktSubjects) {
            return [
                'status' => self::STATUS_ATKT,
                'reason' => "Allowed to keep terms ({$failedCount} backlogs, max allowed: {$maxAtktSubjects})",
            ];
        }

        // Too many failures = FAIL
        return [
            'status' => self::STATUS_FAIL,
            'reason' => "Failed in {$failedCount} subjects (max ATKT allowed: {$maxAtktSubjects})",
        ];
    }

    /**
     * Calculate aggregate percentage.
     *
     * @param array $marks
     * @return float
     */
    protected function calculateAggregatePercentage(array $marks): float
    {
        $totalObtained = 0;
        $totalMax = 0;

        foreach ($marks as $mark) {
            $totalObtained += $mark->marks_obtained;
            $totalMax += $mark->max_marks;
        }

        return $totalMax > 0 ? ($totalObtained / $totalMax) * 100 : 0;
    }

    /**
     * Get applicable rules for evaluation.
     *
     * @param StudentAcademicRecord $academicRecord
     * @return array
     */
    protected function getApplicableRules(StudentAcademicRecord $academicRecord): array
    {
        $sessionId = $academicRecord->academic_session_id;
        $programId = $academicRecord->program_id;

        return [
            'pass_percentage' => $this->ruleEngine->getDecimal(
                RuleEngineService::RULE_PASS_PERCENTAGE,
                40.0,
                $sessionId,
                $programId
            ),
            'grace_marks' => $this->ruleEngine->getInteger(
                RuleEngineService::RULE_GRACE_MARKS,
                5,
                $sessionId,
                $programId
            ),
            'atkt_max_subjects' => $this->ruleEngine->getInteger(
                RuleEngineService::RULE_ATKT_MAX_SUBJECTS,
                3,
                $sessionId,
                $programId
            ),
            'compulsory_subjects' => $this->ruleEngine->getArray(
                RuleEngineService::RULE_COMPLUSORY_SUBJECTS,
                [],
                $sessionId,
                $programId
            ),
        ];
    }

    /**
     * Get student marks for an examination.
     *
     * @param int $studentId
     * @param int $examinationId
     * @return array
     */
    protected function getStudentMarks(int $studentId, int $examinationId): array
    {
        return StudentMark::with('subject')
            ->where('student_id', $studentId)
            ->where('examination_id', $examinationId)
            ->get()
            ->toArray();
    }

    /**
     * Check if student is eligible for ATKT.
     *
     * @param StudentAcademicRecord $academicRecord
     * @param int $failedCount
     * @return array
     */
    public function checkAtktEligibility(
        StudentAcademicRecord $academicRecord,
        int $failedCount
    ): array {
        $rules = $this->getApplicableRules($academicRecord);

        $maxAtktSubjects = $rules['atkt_max_subjects'];
        $maxAttempts = $this->ruleEngine->getInteger(
            RuleEngineService::RULE_ATKT_MAX_ATTEMPTS,
            3,
            $academicRecord->academic_session_id,
            $academicRecord->program_id
        );

        $currentAttempt = $academicRecord->current_atkt_attempt;

        $eligible = $failedCount <= $maxAtktSubjects && $currentAttempt < $maxAttempts;

        return [
            'eligible' => $eligible,
            'reason' => $eligible
                ? "Eligible for ATKT ({$failedCount} backlogs, attempt {$currentAttempt + 1} of {$maxAttempts})"
                : "Not eligible for ATKT ({$failedCount} backlogs > {$maxAtktSubjects} max OR attempts exhausted)",
            'failed_count' => $failedCount,
            'max_allowed' => $maxAtktSubjects,
            'current_attempt' => $currentAttempt,
            'max_attempts' => $maxAttempts,
        ];
    }

    /**
     * Update student academic record with result.
     *
     * @param StudentAcademicRecord $record
     * @param array $result
     * @return bool
     */
    public function updateAcademicRecord(StudentAcademicRecord $record, array $result): bool
    {
        $statusMap = [
            self::STATUS_PASS => StudentAcademicRecord::STATUS_PASS,
            self::STATUS_FAIL => StudentAcademicRecord::STATUS_FAIL,
            self::STATUS_ATKT => StudentAcademicRecord::STATUS_ATKT,
            self::STATUS_NOT_ELIGIBLE => StudentAcademicRecord::STATUS_EXAM_PENDING,
        ];

        return $record->update([
            'result_status' => $statusMap[$result['status']] ?? StudentAcademicRecord::STATUS_EXAM_PENDING,
            'backlog_count' => $result['subject_count']['failed'] ?? 0,
        ]);
    }
}
