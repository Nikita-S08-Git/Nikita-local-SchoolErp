<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User\Student;
use App\Models\User\Teacher;
use App\Models\Academic\Admission;
use Illuminate\Support\Facades\Storage;
use App\Models\ActivityLog;

class DocumentDownloadController extends Controller
{
    public function downloadStudentDocument(Student $student, string $documentType)
    {
        $user = auth()->user();

        if (!$this->canAccessStudentDocument($user, $student)) {
            abort(403, 'You are not authorized to access this document');
        }

        $filePath = match($documentType) {
            'photo'                => $student->photo_path,
            'signature'            => $student->signature_path,
            'cast_certificate'     => $student->cast_certificate_path,
            'marksheet'            => $student->marksheet_path,
            'aadhar'               => $student->aadhar_path,
            'income_certificate'   => $student->income_certificate_path,
            'domicile_certificate' => $student->domicile_certificate_path,
            default                => null,
        };

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'Document not found');
        }

        try {
            ActivityLog::log(
                $user,
                'document_downloaded',
                'students',
                "Downloaded {$documentType} for student ID {$student->id}",
            );
        } catch (\Exception $e) {
            // Logging failure must never block file serving
        }

        $fullPath = Storage::disk('public')->path($filePath);
        $mimeType = mime_content_type($fullPath);
        $isImage  = str_starts_with($mimeType, 'image/');

        return response()->file($fullPath, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => ($isImage ? 'inline' : 'attachment') . '; filename="' . basename($filePath) . '"',
        ]);
    }

    public function downloadTeacherDocument(Teacher $teacher, string $documentType)
    {
        $user = auth()->user();

        if (!$user->hasAnyRole(['admin', 'principal']) && $user->id !== $teacher->user_id) {
            abort(403, 'You are not authorized to access this document');
        }

        $filePath = match($documentType) {
            'photo'                     => $teacher->photo_path,
            'signature'                 => $teacher->signature_path ?? null,
            'qualification_certificate' => $teacher->qualification_certificate_path ?? null,
            'experience_certificate'    => $teacher->experience_certificate_path ?? null,
            default                     => null,
        };

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'Document not found');
        }

        try {
            ActivityLog::log($user, 'document_downloaded', 'teachers', "Downloaded {$documentType} for teacher ID {$teacher->id}");
        } catch (\Exception $e) {}

        $fullPath = Storage::disk('public')->path($filePath);
        $mimeType = mime_content_type($fullPath);
        $isImage  = str_starts_with($mimeType, 'image/');

        return response()->file($fullPath, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => ($isImage ? 'inline' : 'attachment') . '; filename="' . basename($filePath) . '"',
        ]);
    }

    public function downloadAdmissionDocument(Admission $admission, string $documentType)
    {
        $user = auth()->user();

        if (!$user->hasAnyRole(['admin', 'principal', 'student_section'])) {
            abort(403, 'You are not authorized to access this document');
        }

        $document = $admission->documents()
            ->where('document_type', $documentType)
            ->latest()
            ->first();

        $filePath = $document?->file_path;

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'Document not found');
        }

        try {
            ActivityLog::log($user, 'document_downloaded', 'admissions', "Downloaded {$documentType} for admission ID {$admission->id}");
        } catch (\Exception $e) {}

        $fullPath = Storage::disk('public')->path($filePath);
        $mimeType = mime_content_type($fullPath);
        $isImage  = str_starts_with($mimeType, 'image/');

        return response()->file($fullPath, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => ($isImage ? 'inline' : 'attachment') . '; filename="' . basename($filePath) . '"',
        ]);
    }

    private function canAccessStudentDocument($user, Student $student): bool
    {
        // Admin / Principal / Admission officer / Accounts staff — full access
        if ($user->hasAnyRole(['admin', 'principal', 'student_section', 'accounts_staff',
                               'hod_commerce', 'hod_science', 'hod_management', 'hod_arts', 'office'])) {
            return true;
        }

        // Student — own documents only
        if ($user->hasRole('student') && $user->id === $student->user_id) {
            return true;
        }

        // Teacher / class_teacher / subject_teacher — students in their division
        if ($user->hasAnyRole(['teacher', 'class_teacher', 'subject_teacher'])) {
            try {
                $teacherProfile = $user->teacherProfile;
                if ($teacherProfile) {
                    $divisionIds = $teacherProfile->divisions()->pluck('divisions.id')->toArray();
                    if (in_array($student->division_id, $divisionIds)) {
                        return true;
                    }
                }
            } catch (\Exception $e) {}
        }

        // Parent — child's documents via guardian email match
        if ($user->hasRole('parent')) {
            return $student->guardians()->where('email', $user->email)->exists();
        }

        return false;
    }
}
