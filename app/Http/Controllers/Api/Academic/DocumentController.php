<?php

namespace App\Http\Controllers\Api\Academic;

use App\Http\Controllers\Controller;
use App\Models\User\Student;
use App\Models\Academic\StudentDocument;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    /**
     * Upload student photo with secure validation
     */
    public function uploadPhoto(Request $request, Student $student): JsonResponse
    {
        try {
            // Secure file upload validation
            $request->validate([
                'photo' => 'required|file|mimes:jpeg,png,pdf|max:2048', // 2MB max, only allowed formats
            ]);

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                
                // Additional file validation
                if (!$file->isValid()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File upload failed'
                    ], 400);
                }

                // Generate unique filename
                $filename = 'photo_' . $student->id . '_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                
                // Store in private storage (not public)
                $path = $file->storeAs('documents/students/photos', $filename, 'private');

                // Delete old photo if exists
                if ($student->photo_path) {
                    Storage::disk('private')->delete($student->photo_path);
                }

                $student->update(['photo_path' => $path]);

                Log::info('Student photo uploaded', [
                    'student_id' => $student->id,
                    'path' => $path
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Photo uploaded successfully',
                    'data' => [
                        'photo_url' => route('documents.photo', ['student' => $student->id])
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No photo file provided'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Photo upload failed', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload photo. Please try again.'
            ], 500);
        }
    }

    /**
     * Upload student signature with secure validation
     */
    public function uploadSignature(Request $request, Student $student): JsonResponse
    {
        try {
            // Secure file upload validation
            $request->validate([
                'signature' => 'required|file|mimes:jpeg,png,pdf|max:1024', // 1MB max
            ]);

            if ($request->hasFile('signature')) {
                $file = $request->file('signature');
                
                // Additional file validation
                if (!$file->isValid()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File upload failed'
                    ], 400);
                }

                // Generate unique filename
                $filename = 'signature_' . $student->id . '_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                
                // Store in private storage
                $path = $file->storeAs('documents/students/signatures', $filename, 'private');

                // Delete old signature if exists
                if ($student->signature_path) {
                    Storage::disk('private')->delete($student->signature_path);
                }

                $student->update(['signature_path' => $path]);

                Log::info('Student signature uploaded', [
                    'student_id' => $student->id,
                    'path' => $path
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Signature uploaded successfully',
                    'data' => [
                        'signature_url' => route('documents.signature', ['student' => $student->id])
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No signature file provided'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Signature upload failed', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload signature. Please try again.'
            ], 500);
        }
    }

    /**
     * Get document URLs (requires authentication)
     */
    public function getDocuments(Student $student): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'photo' => $student->photo_path ? Storage::url($student->photo_path) : null,
                'signature' => $student->signature_path ? Storage::url($student->signature_path) : null,
            ]
        ]);
    }

    /**
     * Delete student photo
     */
    public function deletePhoto(Student $student): JsonResponse
    {
        try {
            if ($student->photo_path) {
                Storage::disk('private')->delete($student->photo_path);
                $student->update(['photo_path' => null]);

                Log::info('Student photo deleted', ['student_id' => $student->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Photo deleted successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No photo to delete'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Photo deletion failed', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete photo'
            ], 500);
        }
    }

    /**
     * Delete student signature
     */
    public function deleteSignature(Student $student): JsonResponse
    {
        try {
            if ($student->signature_path) {
                Storage::disk('private')->delete($student->signature_path);
                $student->update(['signature_path' => null]);

                Log::info('Student signature deleted', ['student_id' => $student->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Signature deleted successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No signature to delete'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Signature deletion failed', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete signature'
            ], 500);
        }
    }
}