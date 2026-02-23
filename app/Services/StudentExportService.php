<?php

namespace App\Services;

use App\Models\User\Student;
use App\Repositories\StudentRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport;

/**
 * Student Export Service
 * 
 * Handles exporting student data to various formats (Excel, CSV, PDF)
 * 
 * Requirements:
 * - composer require maatwebsite/excel
 * - composer require barryvdh/laravel-dompdf (for PDF)
 */
class StudentExportService
{
    public function __construct(
        private StudentRepository $repository
    ) {}

    /**
     * Export students to Excel
     * 
     * @param array $filters
     * @param string $format 'xlsx' or 'csv'
     * @return string File path
     */
    public function exportToExcel(array $filters = [], string $format = 'xlsx'): string
    {
        $students = $this->repository->getForExport($filters);
        
        $fileName = 'students_' . now()->format('Y-m-d_His') . '.' . $format;
        $filePath = 'exports/' . $fileName;
        
        // Create export data
        $exportData = $this->prepareExportData($students);
        
        // Store file
        Excel::store(
            new StudentsExport($exportData),
            $filePath,
            'public'
        );
        
        return Storage::disk('public')->path($filePath);
    }

    /**
     * Export students to CSV
     * 
     * @param array $filters
     * @return string File path
     */
    public function exportToCsv(array $filters = []): string
    {
        return $this->exportToExcel($filters, 'csv');
    }

    /**
     * Export students to PDF
     * 
     * @param array $filters
     * @return string File path
     */
    public function exportToPdf(array $filters = []): string
    {
        $students = $this->repository->getForExport($filters);
        
        $fileName = 'students_' . now()->format('Y-m-d_His') . '.pdf';
        $filePath = 'exports/' . $fileName;
        
        $pdf = \PDF::loadView('exports.students-pdf', [
            'students' => $students,
            'exportDate' => now()->format('d/m/Y H:i:s'),
        ]);
        
        Storage::disk('public')->put($filePath, $pdf->output());
        
        return Storage::disk('public')->path($filePath);
    }

    /**
     * Prepare data for export
     * 
     * @param Collection $students
     * @return array
     */
    private function prepareExportData(Collection $students): array
    {
        return $students->map(function (Student $student) {
            return [
                'Admission Number' => $student->admission_number,
                'Roll Number' => $student->roll_number,
                'PRN' => $student->prn,
                'First Name' => $student->first_name,
                'Middle Name' => $student->middle_name,
                'Last Name' => $student->last_name,
                'Full Name' => $student->full_name,
                'Date of Birth' => $student->date_of_birth?->format('d/m/Y'),
                'Age' => $student->date_of_birth?->age,
                'Gender' => ucfirst($student->gender),
                'Blood Group' => $student->blood_group,
                'Category' => strtoupper($student->category),
                'Religion' => $student->religion,
                // removed caste column from exports
                'Mobile Number' => $student->mobile_number,
                'Email' => $student->email,
                'Current Address' => $student->current_address,
                'Permanent Address' => $student->permanent_address,
                'Program' => $student->program?->name,
                'Division' => $student->division?->name,
                'Academic Year' => $student->academic_year,
                'Academic Session' => $student->academicSession?->name,
                'Admission Date' => $student->admission_date?->format('d/m/Y'),
                'Status' => ucfirst($student->student_status),
                'Father Name' => $this->getGuardianName($student, 'father'),
                'Father Mobile' => $this->getGuardianMobile($student, 'father'),
                'Mother Name' => $this->getGuardianName($student, 'mother'),
                'Mother Mobile' => $this->getGuardianMobile($student, 'mother'),
            ];
        })->toArray();
    }

    /**
     * Get guardian name by relation
     * 
     * @param Student $student
     * @param string $relation
     * @return string|null
     */
    private function getGuardianName(Student $student, string $relation): ?string
    {
        return $student->guardians
            ->where('relation', $relation)
            ->first()?->name;
    }

    /**
     * Get guardian mobile by relation
     * 
     * @param Student $student
     * @param string $relation
     * @return string|null
     */
    private function getGuardianMobile(Student $student, string $relation): ?string
    {
        return $student->guardians
            ->where('relation', $relation)
            ->first()?->mobile_number;
    }

    /**
     * Export template for bulk import
     * 
     * @return string File path
     */
    public function exportTemplate(): string
    {
        $headers = [
            'first_name',
            'middle_name',
            'last_name',
            'date_of_birth (YYYY-MM-DD)',
            'gender (male/female/other)',
            'blood_group',
            'category (general/obc/sc/st)',
            'religion',
                        'mobile_number',
            'email',
            'current_address',
            'permanent_address',
            'program_id',
            'division_id',
            'academic_session_id',
            'academic_year',
            'admission_date (YYYY-MM-DD)',
        ];
        
        $fileName = 'student_import_template.xlsx';
        $filePath = 'templates/' . $fileName;
        
        // Create simple template with headers
        $data = [
            $headers,
            // Add sample row
            [
                'John',
                'Michael',
                'Doe',
                '2005-01-15',
                'male',
                'O+',
                'general',
                'Hindu',
                'General',
                '9876543210',
                'john.doe@example.com',
                '123 Main St',
                '123 Main St',
                '1',
                '1',
                '1',
                'FY',
                now()->format('Y-m-d'),
            ]
        ];
        
        Excel::store(
            new \App\Exports\SimpleExport($data),
            $filePath,
            'public'
        );
        
        return Storage::disk('public')->path($filePath);
    }

    /**
     * Export students by program
     * 
     * @param int $programId
     * @param string $format
     * @return string
     */
    public function exportByProgram(int $programId, string $format = 'xlsx'): string
    {
        return $this->exportToExcel(['program_id' => $programId], $format);
    }

    /**
     * Export students by division
     * 
     * @param int $divisionId
     * @param string $format
     * @return string
     */
    public function exportByDivision(int $divisionId, string $format = 'xlsx'): string
    {
        return $this->exportToExcel(['division_id' => $divisionId], $format);
    }

    /**
     * Export students with pending fees
     * 
     * @param string $format
     * @return string
     */
    public function exportWithPendingFees(string $format = 'xlsx'): string
    {
        $students = $this->repository->getWithPendingFees();
        
        $fileName = 'students_pending_fees_' . now()->format('Y-m-d_His') . '.' . $format;
        $filePath = 'exports/' . $fileName;
        
        $exportData = $students->map(function (Student $student) {
            $totalPending = $student->fees
                ->where('payment_status', '!=', 'paid')
                ->sum(fn($fee) => $fee->amount - $fee->paid_amount);
            
            return [
                'Admission Number' => $student->admission_number,
                'Name' => $student->full_name,
                'Program' => $student->program?->name,
                'Division' => $student->division?->name,
                'Mobile' => $student->mobile_number,
                'Email' => $student->email,
                'Total Pending' => $totalPending,
            ];
        })->toArray();
        
        Excel::store(
            new StudentsExport($exportData),
            $filePath,
            'public'
        );
        
        return Storage::disk('public')->path($filePath);
    }
}
