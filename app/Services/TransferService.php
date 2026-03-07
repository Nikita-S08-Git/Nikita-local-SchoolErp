<?php

namespace App\Services;

use App\Models\User\Student;
use App\Models\Academic\StudentAcademicRecord;
use App\Models\Academic\TransferRecord;
use App\Models\Academic\AcademicSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Transfer Service
 *
 * Handles all student transfer (leaving certificate) workflow including
 * transfer request creation, approval, TC generation, and student status updates.
 *
 * Key Features:
 * - Transfer request workflow (pending → approved → issued)
 * - Fee clearance verification
 * - TC number generation
 * - Document attachment
 * - Override with authorization
 * - Complete audit trail
 *
 * @package App\Services
 */
class TransferService
{
    /**
     * Transfer configuration
     */
    protected array $config;

    /**
     * Create a new TransferService instance.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'tc_prefix' => 'TC',
            'fee_clearance_required' => true,
            'allow_pending_fee_transfer' => false,
            'auto_update_student_status' => true,
        ], $config);
    }

    /**
     * Create a transfer request for a student.
     *
     * @param Student $student
     * @param array $data
     * @param User $requestedBy
     * @return TransferRecord
     * @throws \Exception
     */
    public function createTransferRequest(
        Student $student,
        array $data,
        User $requestedBy
    ): TransferRecord {
        return DB::transaction(function () use ($student, $data, $requestedBy) {
            // Get current academic record
            $currentRecord = $student->currentAcademicRecord;
            
            if (!$currentRecord) {
                throw new \Exception("No current academic record found for student");
            }

            // Check if student already has a pending transfer
            $existingTransfer = TransferRecord::forStudent($student->id)
                ->pending()
                ->first();

            if ($existingTransfer) {
                throw new \Exception("Student already has a pending transfer request");
            }

            // Check fee clearance if required
            if ($this->config['fee_clearance_required'] && !$currentRecord->hasFeesCleared()) {
                if (!$this->config['allow_pending_fee_transfer']) {
                    throw new \Exception(
                        "Student has outstanding fees (₹{$currentRecord->outstanding_amount}). " .
                        "Clear fees before transfer request."
                    );
                }
            }

            // Generate TC number
            $tcNumber = TransferRecord::generateTcNumber($this->config['tc_prefix']);

            // Create transfer record
            $transferRecord = TransferRecord::create([
                'student_id' => $student->id,
                'academic_session_id' => $currentRecord->academic_session_id,
                'program_id' => $currentRecord->program_id,
                'academic_year' => $currentRecord->academic_year,
                'division_id' => $currentRecord->division_id,
                'tc_number' => $tcNumber,
                'transfer_type' => $data['transfer_type'] ?? TransferRecord::TYPE_VOLUNTARY,
                'reason' => $data['reason'] ?? null,
                'tc_issue_date' => $data['tc_issue_date'] ?? now(),
                'last_attendance_date' => $data['last_attendance_date'] ?? now(),
                'conduct' => $data['conduct'] ?? TransferRecord::CONDUCT_GOOD,
                'eligible_for_readmission' => $data['eligible_for_readmission'] ?? true,
                'readmission_remarks' => $data['readmission_remarks'] ?? null,
                'result_status' => $currentRecord->result_status,
                'attendance_percentage' => $currentRecord->attendance_percentage,
                'fee_cleared' => $currentRecord->fee_cleared,
                'outstanding_fees' => $currentRecord->outstanding_amount,
                'backlog_count' => $currentRecord->backlog_count,
                'destination_institution' => $data['destination_institution'] ?? null,
                'destination_city' => $data['destination_city'] ?? null,
                'destination_state' => $data['destination_state'] ?? null,
                'destination_course' => $data['destination_course'] ?? null,
                'approved_by' => null, // Will be set on approval
                'processed_by' => $requestedBy->id,
                'status' => TransferRecord::STATUS_PENDING,
                'tc_document_path' => null,
                'additional_documents' => $data['additional_documents'] ?? null,
                'is_override' => $data['is_override'] ?? false,
                'override_reason' => $data['override_reason'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            Log::info("Transfer request created", [
                'student_id' => $student->id,
                'tc_number' => $tcNumber,
                'transfer_type' => $transferRecord->transfer_type,
                'processed_by' => $requestedBy->id,
            ]);

            return $transferRecord;
        });
    }

    /**
     * Approve a transfer request.
     *
     * @param TransferRecord $transferRecord
     * @param User $approvedBy
     * @param array $data
     * @return TransferRecord
     * @throws \Exception
     */
    public function approveTransfer(
        TransferRecord $transferRecord,
        User $approvedBy,
        array $data = []
    ): TransferRecord {
        return DB::transaction(function () use ($transferRecord, $approvedBy, $data) {
            if (!$transferRecord->isPending()) {
                throw new \Exception("Transfer request is not pending (Status: {$transferRecord->status})");
            }

            // Update transfer record
            $transferRecord->update([
                'approved_by' => $approvedBy->id,
                'status' => TransferRecord::STATUS_APPROVED,
                'tc_document_path' => $data['tc_document_path'] ?? null,
                'notes' => $data['notes'] ?? $transferRecord->notes,
            ]);

            // Update student status if configured
            if ($this->config['auto_update_student_status']) {
                $this->updateStudentStatus($transferRecord->student);
            }

            // Update academic record
            $currentRecord = $transferRecord->student->currentAcademicRecord;
            if ($currentRecord) {
                $currentRecord->update([
                    'result_status' => TransferRecord::STATUS_TC_ISSUED,
                    'promotion_status' => StudentAcademicRecord::PROMOTION_TRANSFERRED,
                ]);
            }

            Log::info("Transfer approved", [
                'transfer_id' => $transferRecord->id,
                'tc_number' => $transferRecord->tc_number,
                'student_id' => $transferRecord->student_id,
                'approved_by' => $approvedBy->id,
            ]);

            return $transferRecord;
        });
    }

    /**
     * Mark transfer certificate as issued.
     *
     * @param TransferRecord $transferRecord
     * @param User $issuedBy
     * @return TransferRecord
     * @throws \Exception
     */
    public function issueTransferCertificate(
        TransferRecord $transferRecord,
        User $issuedBy
    ): TransferRecord {
        return DB::transaction(function () use ($transferRecord, $issuedBy) {
            if (!$transferRecord->isApproved()) {
                throw new \Exception("Transfer must be approved before issuing TC");
            }

            $transferRecord->markIssued();

            // Update student status
            $this->updateStudentStatus($transferRecord->student, 'tc_issued');

            Log::info("TC issued", [
                'transfer_id' => $transferRecord->id,
                'tc_number' => $transferRecord->tc_number,
                'student_id' => $transferRecord->student_id,
                'issued_by' => $issuedBy->id,
            ]);

            return $transferRecord;
        });
    }

    /**
     * Cancel a transfer request.
     *
     * @param TransferRecord $transferRecord
     * @param User $cancelledBy
     * @param string $reason
     * @return TransferRecord
     * @throws \Exception
     */
    public function cancelTransfer(
        TransferRecord $transferRecord,
        User $cancelledBy,
        string $reason = null
    ): TransferRecord {
        if ($transferRecord->isIssued()) {
            throw new \Exception("Cannot cancel issued TC. Create a new transfer request if needed.");
        }

        $transferRecord->cancel();
        $transferRecord->update(['notes' => trim(($transferRecord->notes ?? '') . "\n\nCancelled: " . $reason)]);

        // Revert student status if needed
        if ($this->config['auto_update_student_status']) {
            $transferRecord->student->update([
                'student_status' => 'active',
            ]);
        }

        Log::info("Transfer cancelled", [
            'transfer_id' => $transferRecord->id,
            'tc_number' => $transferRecord->tc_number,
            'student_id' => $transferRecord->student_id,
            'cancelled_by' => $cancelledBy->id,
            'reason' => $reason,
        ]);

        return $transferRecord;
    }

    /**
     * Update student status based on transfer.
     *
     * @param Student $student
     * @param string $status
     * @return bool
     */
    public function updateStudentStatus(Student $student, string $status = 'transferred'): bool
    {
        $statusMap = [
            'tc_issued' => 'tc_issued',
            'transferred' => 'transferred',
            'active' => 'active',
        ];

        $newStatus = $statusMap[$status] ?? 'transferred';

        return $student->update([
            'student_status' => $newStatus,
        ]);
    }

    /**
     * Get transfer request details with student info.
     *
     * @param int $transferId
     * @return array|null
     */
    public function getTransferDetails(int $transferId): ?array
    {
        $transfer = TransferRecord::with([
            'student',
            'student.user',
            'academicSession',
            'program',
            'division',
            'approvedBy',
            'processedBy',
        ])->find($transferId);

        if (!$transfer) {
            return null;
        }

        return [
            'transfer' => $transfer,
            'student' => [
                'id' => $transfer->student->id,
                'name' => $transfer->student->full_name,
                'admission_number' => $transfer->student->admission_number,
                'email' => $transfer->student->email,
                'mobile' => $transfer->student->mobile_number,
            ],
            'academic_details' => [
                'session' => $transfer->academicSession->name,
                'program' => $transfer->program->name,
                'year' => $transfer->academic_year,
                'division' => $transfer->division->division_name,
            ],
            'transfer_details' => [
                'tc_number' => $transfer->tc_number,
                'type' => $transfer->transfer_type_label,
                'status' => $transfer->status,
                'issue_date' => $transfer->tc_issue_date,
                'conduct' => $transfer->conduct_label,
            ],
            'fee_status' => [
                'cleared' => $transfer->fee_cleared,
                'outstanding' => $transfer->outstanding_fees,
            ],
            'approval_details' => [
                'processed_by' => $transfer->processedBy->name ?? null,
                'approved_by' => $transfer->approvedBy->name ?? null,
                'approved_at' => $transfer->approved_by ? $transfer->updated_at : null,
            ],
        ];
    }

    /**
     * Get pending transfer requests.
     *
     * @param int|null $sessionId
     * @return \Illuminate\Support\Collection
     */
    public function getPendingTransfers(?int $sessionId = null): \Illuminate\Support\Collection
    {
        $query = TransferRecord::with([
            'student',
            'academicSession',
            'program',
            'division',
            'processedBy',
        ])->pending();

        if ($sessionId) {
            $query->forSession($sessionId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get transfer statistics.
     *
     * @param int|null $sessionId
     * @return array
     */
    public function getTransferStatistics(?int $sessionId = null): array
    {
        $query = TransferRecord::query();

        if ($sessionId) {
            $query->where('academic_session_id', $sessionId);
        }

        $total = $query->count();
        $pending = (clone $query)->pending()->count();
        $approved = (clone $query)->approved()->count();
        $issued = (clone $query)->issued()->count();
        $cancelled = (clone $query)->where('status', TransferRecord::STATUS_CANCELLED)->count();

        $byType = TransferRecord::selectRaw('transfer_type, count(*) as count')
            ->when($sessionId, fn($q) => $q->where('academic_session_id', $sessionId))
            ->groupBy('transfer_type')
            ->get()
            ->pluck('count', 'transfer_type')
            ->toArray();

        return [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'issued' => $issued,
            'cancelled' => $cancelled,
            'by_type' => $byType,
        ];
    }

    /**
     * Upload TC document.
     *
     * @param TransferRecord $transferRecord
     * @param string $filePath
     * @param User $uploadedBy
     * @return bool
     */
    public function uploadTcDocument(
        TransferRecord $transferRecord,
        string $filePath,
        User $uploadedBy
    ): bool {
        // Verify file exists
        if (!Storage::disk('private')->exists($filePath)) {
            throw new \Exception("TC document file not found");
        }

        return $transferRecord->update([
            'tc_document_path' => $filePath,
        ]);
    }

    /**
     * Get TC document path.
     *
     * @param TransferRecord $transferRecord
     * @return string|null
     */
    public function getTcDocumentPath(TransferRecord $transferRecord): ?string
    {
        if (!$transferRecord->tc_document_path) {
            return null;
        }

        return Storage::disk('private')->url($transferRecord->tc_document_path);
    }

    /**
     * Verify transfer eligibility (fee clearance, etc.).
     *
     * @param Student $student
     * @return array
     */
    public function verifyTransferEligibility(Student $student): array
    {
        $currentRecord = $student->currentAcademicRecord;
        
        $result = [
            'eligible' => true,
            'blocking_issues' => [],
            'warnings' => [],
        ];

        if (!$currentRecord) {
            $result['eligible'] = false;
            $result['blocking_issues'][] = 'No current academic record found';
            return $result;
        }

        // Check fee clearance
        if ($this->config['fee_clearance_required'] && !$currentRecord->hasFeesCleared()) {
            if (!$this->config['allow_pending_fee_transfer']) {
                $result['eligible'] = false;
                $result['blocking_issues'][] = "Outstanding fees: ₹{$currentRecord->outstanding_amount}";
            } else {
                $result['warnings'][] = "Outstanding fees will be recorded in TC";
            }
        }

        // Check existing pending transfer
        $existingTransfer = TransferRecord::forStudent($student->id)->pending()->first();
        if ($existingTransfer) {
            $result['eligible'] = false;
            $result['blocking_issues'][] = "Pending transfer request already exists (TC: {$existingTransfer->tc_number})";
        }

        return $result;
    }
}
