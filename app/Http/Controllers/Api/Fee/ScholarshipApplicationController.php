<?php

namespace App\Http\Controllers\Api\Fee;

use App\Http\Controllers\Controller;
use App\Models\Fee\ScholarshipApplication;
use App\Models\Fee\Scholarship;
use App\Models\User\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ScholarshipApplicationController extends Controller
{
    /**
     * Student applies for scholarship
     * POST /api/students/{student}/scholarship/apply
     */
    public function apply(Request $request, Student $student): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'scholarship_id' => 'required|exists:scholarships,id',
            'application_reason' => 'required|string|max:1000',
            'documents' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $scholarship = Scholarship::findOrFail($request->scholarship_id);

            // Check if already applied
            $existingApplication = ScholarshipApplication::where('student_id', $student->id)
                ->where('scholarship_id', $scholarship->id)
                ->where('status', 'pending')
                ->first();

            if ($existingApplication) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have a pending application for this scholarship'
                ], 409);
            }

            $application = ScholarshipApplication::create([
                'student_id' => $student->id,
                'scholarship_id' => $scholarship->id,
                'status' => 'pending',
                'application_reason' => $request->application_reason,
                'documents' => $request->documents ?? []
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Scholarship application submitted successfully',
                'data' => $application->load(['scholarship', 'student'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit application',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify scholarship application (Student Section role)
     * POST /api/scholarship/{applicationId}/verify
     */
    public function verify(Request $request, $applicationId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'verification_notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $application = ScholarshipApplication::findOrFail($applicationId);

            if ($application->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Application is already ' . $application->status
                ], 409);
            }

            $application->update([
                'status' => $request->status,
                'verification_notes' => $request->verification_notes,
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Scholarship application ' . $request->status . ' successfully',
                'data' => $application->load(['scholarship', 'student', 'verifiedBy'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify application',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student's scholarship applications
     * GET /api/students/{student}/scholarship
     */
    public function getStudentScholarships(Request $request, Student $student): JsonResponse
    {
        try {
            $applications = ScholarshipApplication::where('student_id', $student->id)
                ->with(['scholarship', 'verifiedBy'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $applications
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch scholarship applications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * List all scholarship applications (admin/student_section)
     * GET /api/scholarship-applications
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = ScholarshipApplication::with(['student', 'scholarship', 'verifiedBy']);

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by scholarship
            if ($request->filled('scholarship_id')) {
                $query->where('scholarship_id', $request->scholarship_id);
            }

            // Filter by student
            if ($request->filled('student_id')) {
                $query->where('student_id', $request->student_id);
            }

            // Get pending applications
            if ($request->boolean('pending')) {
                $query->where('status', 'pending');
            }

            $applications = $query->orderBy('created_at', 'desc')->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $applications
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch scholarship applications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single scholarship application details
     * GET /api/scholarship-applications/{id}
     */
    public function show($applicationId): JsonResponse
    {
        try {
            $application = ScholarshipApplication::with(['student', 'scholarship', 'verifiedBy'])
                ->findOrFail($applicationId);

            return response()->json([
                'success' => true,
                'data' => $application
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
