<?php

namespace App\Http\Controllers\Api\Academic;

use App\Http\Controllers\Controller;
use App\Models\User\Student;
use App\Models\Academic\TransferRecord;
use App\Services\TransferService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Transfer Controller
 *
 * Handles student transfer (TC) operations via API.
 *
 * Features:
 * - Transfer request creation
 * - Transfer approval workflow
 * - TC generation
 * - Transfer cancellation
 * - Transfer history
 *
 * @package App\Http\Controllers\Api\Academic
 */
class TransferController extends Controller
{
    public function __construct(
        private TransferService $transferService
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Verify transfer eligibility for a student.
     *
     * @param int $studentId
     * @return JsonResponse
     */
    public function verifyEligibility(int $studentId): JsonResponse
    {
        $student = Student::findOrFail($studentId);
        $eligibility = $this->transferService->verifyTransferEligibility($student);

        return response()->json([
            'success' => true,
            'data' => $eligibility,
        ]);
    }

    /**
     * Create a transfer request for a student.
     *
     * @param Request $request
     * @param int $studentId
     * @return JsonResponse
     */
    public function createRequest(Request $request, int $studentId): JsonResponse
    {
        $validated = $request->validate([
            'transfer_type' => 'required|in:voluntary,expulsion,academic_dismissal,financial,medical,family_relocation,course_completed,other',
            'reason' => 'nullable|string|max:1000',
            'tc_issue_date' => 'required|date',
            'last_attendance_date' => 'required|date|before_or_equal:today',
            'conduct' => 'required|in:excellent,good,fair,poor',
            'eligible_for_readmission' => 'boolean',
            'readmission_remarks' => 'nullable|string|max:500',
            'destination_institution' => 'nullable|string|max:255',
            'destination_city' => 'nullable|string|max:100',
            'destination_state' => 'nullable|string|max:100',
            'destination_course' => 'nullable|string|max:255',
            'is_override' => 'boolean',
            'override_reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $student = Student::findOrFail($studentId);
        $requestedBy = Auth::user();

        try {
            $transferRecord = $this->transferService->createTransferRequest(
                $student,
                $validated,
                $requestedBy
            );

            return response()->json([
                'success' => true,
                'message' => 'Transfer request created successfully',
                'data' => [
                    'transfer_id' => $transferRecord->id,
                    'tc_number' => $transferRecord->tc_number,
                    'status' => $transferRecord->status,
                    'transfer_type' => $transferRecord->transfer_type_label,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Approve a transfer request.
     *
     * @param Request $request
     * @param int $transferId
     * @return JsonResponse
     */
    public function approve(Request $request, int $transferId): JsonResponse
    {
        $validated = $request->validate([
            'tc_document_path' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $transferRecord = TransferRecord::findOrFail($transferId);
        $approvedBy = Auth::user();

        try {
            $transferRecord = $this->transferService->approveTransfer(
                $transferRecord,
                $approvedBy,
                $validated
            );

            return response()->json([
                'success' => true,
                'message' => 'Transfer request approved',
                'data' => [
                    'transfer_id' => $transferRecord->id,
                    'tc_number' => $transferRecord->tc_number,
                    'status' => $transferRecord->status,
                    'approved_by' => $approvedBy->name,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Issue transfer certificate.
     *
     * @param int $transferId
     * @return JsonResponse
     */
    public function issue(int $transferId): JsonResponse
    {
        $transferRecord = TransferRecord::findOrFail($transferId);
        $issuedBy = Auth::user();

        try {
            $transferRecord = $this->transferService->issueTransferCertificate(
                $transferRecord,
                $issuedBy
            );

            return response()->json([
                'success' => true,
                'message' => 'Transfer certificate issued',
                'data' => [
                    'transfer_id' => $transferRecord->id,
                    'tc_number' => $transferRecord->tc_number,
                    'status' => $transferRecord->status,
                    'issued_at' => $transferRecord->updated_at->toIso8601String(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Cancel a transfer request.
     *
     * @param Request $request
     * @param int $transferId
     * @return JsonResponse
     */
    public function cancel(Request $request, int $transferId): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $transferRecord = TransferRecord::findOrFail($transferId);
        $cancelledBy = Auth::user();

        try {
            $this->transferService->cancelTransfer(
                $transferRecord,
                $cancelledBy,
                $validated['reason']
            );

            return response()->json([
                'success' => true,
                'message' => 'Transfer request cancelled',
                'data' => [
                    'transfer_id' => $transferRecord->id,
                    'tc_number' => $transferRecord->tc_number,
                    'status' => $transferRecord->status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get transfer details.
     *
     * @param int $transferId
     * @return JsonResponse
     */
    public function show(int $transferId): JsonResponse
    {
        $details = $this->transferService->getTransferDetails($transferId);

        if (!$details) {
            return response()->json([
                'success' => false,
                'message' => 'Transfer record not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $details,
        ]);
    }

    /**
     * Get pending transfer requests.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function pendingRequests(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => 'nullable|exists:academic_sessions,id',
        ]);

        $transfers = $this->transferService->getPendingTransfers(
            $validated['session_id'] ?? null
        );

        return response()->json([
            'success' => true,
            'data' => [
                'transfers' => $transfers->map(function ($transfer) {
                    return [
                        'transfer_id' => $transfer->id,
                        'tc_number' => $transfer->tc_number,
                        'student' => [
                            'id' => $transfer->student->id,
                            'name' => $transfer->student->full_name,
                            'admission_number' => $transfer->student->admission_number,
                        ],
                        'academic_details' => [
                            'session' => $transfer->academicSession->name,
                            'program' => $transfer->program->name,
                            'year' => $transfer->academic_year,
                            'division' => $transfer->division->division_name,
                        ],
                        'transfer_type' => $transfer->transfer_type_label,
                        'status' => $transfer->status,
                        'created_at' => $transfer->created_at->toIso8601String(),
                        'processed_by' => $transfer->processedBy->name ?? null,
                    ];
                }),
                'total' => $transfers->count(),
            ],
        ]);
    }

    /**
     * Get transfer statistics.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function statistics(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => 'nullable|exists:academic_sessions,id',
        ]);

        $statistics = $this->transferService->getTransferStatistics(
            $validated['session_id'] ?? null
        );

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Get transfer history for a student.
     *
     * @param int $studentId
     * @return JsonResponse
     */
    public function studentHistory(int $studentId): JsonResponse
    {
        $student = Student::with('transferRecord')->findOrFail($studentId);

        if (!$student->transferRecord) {
            return response()->json([
                'success' => true,
                'data' => [
                    'student_id' => $studentId,
                    'has_transfer' => false,
                    'transfer' => null,
                ],
            ]);
        }

        $transfer = $student->transferRecord;

        return response()->json([
            'success' => true,
            'data' => [
                'student_id' => $studentId,
                'has_transfer' => true,
                'transfer' => [
                    'id' => $transfer->id,
                    'tc_number' => $transfer->tc_number,
                    'transfer_type' => $transfer->transfer_type_label,
                    'status' => $transfer->status,
                    'reason' => $transfer->reason,
                    'conduct' => $transfer->conduct_label,
                    'issue_date' => $transfer->tc_issue_date,
                    'eligible_for_readmission' => $transfer->eligible_for_readmission,
                    'destination' => $transfer->destination_details,
                    'created_at' => $transfer->created_at->toIso8601String(),
                ],
            ],
        ]);
    }

    /**
     * Upload TC document.
     *
     * @param Request $request
     * @param int $transferId
     * @return JsonResponse
     */
    public function uploadDocument(Request $request, int $transferId): JsonResponse
    {
        $validated = $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $transferRecord = TransferRecord::findOrFail($transferId);
        $uploadedBy = Auth::user();

        try {
            $filePath = $validated['document']->store(
                'transfer-certificates/' . $transferRecord->tc_number,
                'private'
            );

            $this->transferService->uploadTcDocument(
                $transferRecord,
                $filePath,
                $uploadedBy
            );

            return response()->json([
                'success' => true,
                'message' => 'TC document uploaded successfully',
                'data' => [
                    'document_path' => $this->transferService->getTcDocumentPath($transferRecord),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Download TC document.
     *
     * @param int $transferId
     * @return \Illuminate\Http\Response
     */
    public function downloadDocument(int $transferId)
    {
        $transferRecord = TransferRecord::findOrFail($transferId);

        if (!$transferRecord->tc_document_path) {
            return response()->json([
                'success' => false,
                'message' => 'No TC document available',
            ], 404);
        }

        return Storage::disk('private')->download($transferRecord->tc_document_path);
    }
}
