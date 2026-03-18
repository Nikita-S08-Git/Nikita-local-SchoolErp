<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User\Student;
use App\Models\User\Teacher;
use App\Models\Academic\Admission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use App\Models\ActivityLog;

class DocumentDownloadController extends Controller
{
    /**
     * Download student document with authentication and authorization
     * 
     * Access Control:
     * - Student: Own documents only
     * - Parent: Child's documents only
     * - Teacher: Assigned division students
     * - Principal: All school students
     * - Admin: All documents
     */
    public function downloadStudentDocument(Student $student, string $documentType)
    {
        // Check authentication
        if (!auth()->check()) {
            abort(401, 'Authentication required');
        }

        $user = auth()->user();

        // Check authorization using policy
        if (!Gate::allows('viewDocument', $student)) {
            abort(403, 'You are not authorized to access this document');
        }

        // Get the file path based on document type
        $filePath = $this->getStudentDocumentPath($student, $documentType);

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'Document not found');
        }

        // Log the download
        ActivityLog::logEvent(
            $student,
            'document_downloaded',
            null,
            [
                'document_type' => $documentType,
                'file_path' => $filePath,
                'downloaded_by' => $user->name,
                'user_role' => $user->getRoleNames()->first(),
            ]
        );

        // Return the file with proper headers
        $fileName = $this->getDocumentFileName($student, $documentType);
        
        return Storage::disk('public')->download($filePath, $fileName);
    }

    /**
     * Download teacher document with authentication
     */
    public function downloadTeacherDocument(Teacher $teacher, string $documentType)
    {
        if (!auth()->check()) {
            abort(401, 'Authentication required');
        }

        $user = auth()->user();

        // Only admin, principal, or the teacher themselves can download
        if (!$user->hasAnyRole(['admin', 'principal']) && $user->id !== $teacher->user_id) {
            abort(403, 'You are not authorized to access this document');
        }

        $filePath = $this->getTeacherDocumentPath($teacher, $documentType);

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'Document not found');
        }

        // Log the download
        ActivityLog::logEvent(
            $teacher,
            'document_downloaded',
            null,
            [
                'document_type' => $documentType,
                'file_path' => $filePath,
                'downloaded_by' => $user->name,
                'user_role' => $user->getRoleNames()->first(),
            ]
        );

        $fileName = $this->getTeacherDocumentFileName($teacher, $documentType);
        
        return Storage::disk('public')->download($filePath, $fileName);
    }

    /**
     * Download admission document with authentication
     */
    public function downloadAdmissionDocument(Admission $admission, string $documentType)
    {
        if (!auth()->check()) {
            abort(401, 'Authentication required');
        }

        $user = auth()->user();

        // Only admin, principal, admission officer, or the applicant can download
        if (!$user->hasAnyRole(['admin', 'principal', 'student_section'])) {
            abort(403, 'You are not authorized to access this document');
        }

        $filePath = $this->getAdmissionDocumentPath($admission, $documentType);

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'Document not found');
        }

        // Log the download
        ActivityLog::logEvent(
            $admission,
            'document_downloaded',
            null,
            [
                'document_type' => $documentType,
                'file_path' => $filePath,
                'downloaded_by' => $user->name,
                'user_role' => $user->getRoleNames()->first(),
            ]
        );

        $fileName = $this->getAdmissionDocumentFileName($admission, $documentType);
        
        return Storage::disk('public')->download($filePath, $fileName);
    }

    /**
     * Get student document path based on type
     */
    private function getStudentDocumentPath(Student $student, string $documentType): ?string
    {
        return match($documentType) {
            'photo' => $student->photo_path,
            'signature' => $student->signature_path,
            'cast_certificate' => $student->cast_certificate_path,
            'marksheet' => $student->marksheet_path,
            'aadhar' => $student->aadhar_path ?? null,
            'income_certificate' => $student->income_certificate_path ?? null,
            'domicile_certificate' => $student->domicile_certificate_path ?? null,
            default => null,
        };
    }

    /**
     * Get teacher document path based on type
     */
    private function getTeacherDocumentPath(Teacher $teacher, string $documentType): ?string
    {
        return match($documentType) {
            'photo' => $teacher->photo_path,
            'signature' => $teacher->signature_path ?? null,
            'qualification_certificate' => $teacher->qualification_certificate_path ?? null,
            'experience_certificate' => $teacher->experience_certificate_path ?? null,
            default => null,
        };
    }

    /**
     * Get admission document path based on type
     */
    private function getAdmissionDocumentPath(Admission $admission, string $documentType): ?string
    {
        // Get the latest document of the specified type
        $document = $admission->documents()
            ->where('document_type', $documentType)
            ->latest('created_at')
            ->first();

        return $document ? $document->file_path : null;
    }

    /**
     * Generate download filename for student document
     */
    private function getDocumentFileName(Student $student, string $documentType): string
    {
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $student->first_name . '_' . $student->last_name);
        $admissionNo = $student->admission_number ?? 'STU' . $student->id;
        
        return match($documentType) {
            'photo' => "{$admissionNo}_{$safeName}_photo.jpg",
            'signature' => "{$admissionNo}_{$safeName}_signature.jpg",
            'cast_certificate' => "{$admissionNo}_{$safeName}_cast_certificate.pdf",
            'marksheet' => "{$admissionNo}_{$safeName}_marksheet.pdf",
            'aadhar' => "{$admissionNo}_{$safeName}_aadhar.pdf",
            'income_certificate' => "{$admissionNo}_{$safeName}_income_certificate.pdf",
            'domicile_certificate' => "{$admissionNo}_{$safeName}_domicile_certificate.pdf",
            default => "{$admissionNo}_{$safeName}_{$documentType}",
        };
    }

    /**
     * Generate download filename for teacher document
     */
    private function getTeacherDocumentFileName(Teacher $teacher, string $documentType): string
    {
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $teacher->name);
        $employeeId = $teacher->employee_code ?? 'TCH' . $teacher->id;
        
        return match($documentType) {
            'photo' => "{$employeeId}_{$safeName}_photo.jpg",
            'signature' => "{$employeeId}_{$safeName}_signature.jpg",
            'qualification_certificate' => "{$employeeId}_{$safeName}_qualification.pdf",
            'experience_certificate' => "{$employeeId}_{$safeName}_experience.pdf",
            default => "{$employeeId}_{$safeName}_{$documentType}",
        };
    }

    /**
     * Generate download filename for admission document
     */
    private function getAdmissionDocumentFileName(Admission $admission, string $documentType): string
    {
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $admission->first_name . '_' . $admission->last_name);
        $appNo = $admission->application_no ?? 'APP' . $admission->id;
        
        return "{$appNo}_{$safeName}_{$documentType}.pdf";
    }
}
