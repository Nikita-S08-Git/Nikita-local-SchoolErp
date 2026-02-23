<?php

namespace App\Repositories;

use App\Models\User\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Student Repository
 * 
 * This class handles all database operations for students.
 * Benefits:
 * - Separates database logic from business logic
 * - Reusable query methods
 * - Easier to test
 * - Centralized caching
 * - Query optimization
 */
class StudentRepository
{
    /**
     * Cache duration in seconds (1 hour)
     */
    private const CACHE_TTL = 3600;

    /**
     * Get all students with optional filters and pagination
     * 
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithFilters(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Student::query()
            ->with(['program', 'division', 'academicSession']);

        // Apply filters
        $this->applyFilters($query, $filters);

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Search students by multiple criteria
     * 
     * @param string $searchTerm
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(string $searchTerm, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Student::query()
            ->with(['program', 'division', 'academicSession']);

        // Search in multiple fields
        $query->where(function (Builder $q) use ($searchTerm) {
            $q->where('first_name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('middle_name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('admission_number', 'LIKE', "%{$searchTerm}%")
                ->orWhere('roll_number', 'LIKE', "%{$searchTerm}%")
                ->orWhere('prn', 'LIKE', "%{$searchTerm}%")
                ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                ->orWhere('mobile_number', 'LIKE', "%{$searchTerm}%")
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"])
                ->orWhereRaw("CONCAT(first_name, ' ', middle_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"]);
        });

        // Apply additional filters
        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    /**
     * Find student by ID with relationships
     * 
     * @param int $id
     * @param array $relations
     * @return Student|null
     */
    public function findWithRelations(int $id, array $relations = []): ?Student
    {
        $defaultRelations = ['program', 'division', 'academicSession', 'guardians', 'user'];
        $relations = array_merge($defaultRelations, $relations);

        return Cache::remember(
            "student.{$id}",
            self::CACHE_TTL,
            fn() => Student::with($relations)->find($id)
        );
    }

    /**
     * Get student by admission number
     * 
     * @param string $admissionNumber
     * @return Student|null
     */
    public function findByAdmissionNumber(string $admissionNumber): ?Student
    {
        return Cache::remember(
            "student.admission.{$admissionNumber}",
            self::CACHE_TTL,
            fn() => Student::where('admission_number', $admissionNumber)
                ->with(['program', 'division', 'academicSession'])
                ->first()
        );
    }

    /**
     * Get students by program
     * 
     * @param int $programId
     * @param string $status
     * @return Collection
     */
    public function getByProgram(int $programId, string $status = 'active'): Collection
    {
        return Student::where('program_id', $programId)
            ->where('student_status', $status)
            ->with(['division', 'academicSession'])
            ->orderBy('roll_number')
            ->get();
    }

    /**
     * Get students by division
     * 
     * @param int $divisionId
     * @param string $status
     * @return Collection
     */
    public function getByDivision(int $divisionId, string $status = 'active'): Collection
    {
        return Student::where('division_id', $divisionId)
            ->where('student_status', $status)
            ->with(['program', 'academicSession'])
            ->orderBy('roll_number')
            ->get();
    }

    /**
     * Get students count by status
     * 
     * @return array
     */
    public function getCountByStatus(): array
    {
        return Cache::remember('students.count.by.status', self::CACHE_TTL, function () {
            return Student::select('student_status', DB::raw('count(*) as count'))
                ->groupBy('student_status')
                ->pluck('count', 'student_status')
                ->toArray();
        });
    }

    /**
     * Get students count by program
     * 
     * @return array
     */
    public function getCountByProgram(): array
    {
        return Cache::remember('students.count.by.program', self::CACHE_TTL, function () {
            return Student::select('program_id', DB::raw('count(*) as count'))
                ->where('student_status', 'active')
                ->groupBy('program_id')
                ->with('program:id,name')
                ->get()
                ->pluck('count', 'program.name')
                ->toArray();
        });
    }

    /**
     * Create new student
     * 
     * @param array $data
     * @return Student
     */
    public function create(array $data): Student
    {
        $student = Student::create($data);
        
        // Clear relevant caches
        $this->clearCache();
        
        return $student->load(['program', 'division', 'academicSession']);
    }

    /**
     * Update student
     * 
     * @param Student $student
     * @param array $data
     * @return Student
     */
    public function update(Student $student, array $data): Student
    {
        $student->update($data);
        
        // Clear specific student cache
        Cache::forget("student.{$student->id}");
        Cache::forget("student.admission.{$student->admission_number}");
        $this->clearCache();
        
        return $student->fresh(['program', 'division', 'academicSession']);
    }

    /**
     * Delete student (soft delete)
     * 
     * @param Student $student
     * @return bool
     */
    public function delete(Student $student): bool
    {
        $result = $student->delete();
        
        if ($result) {
            Cache::forget("student.{$student->id}");
            Cache::forget("student.admission.{$student->admission_number}");
            $this->clearCache();
        }
        
        return $result;
    }

    /**
     * Get students for export
     * 
     * @param array $filters
     * @return Collection
     */
    public function getForExport(array $filters = []): Collection
    {
        $query = Student::query()
            ->with(['program', 'division', 'academicSession', 'guardians']);

        $this->applyFilters($query, $filters);

        return $query->get();
    }

    /**
     * Bulk update student status
     * 
     * @param array $studentIds
     * @param string $status
     * @return int Number of updated records
     */
    public function bulkUpdateStatus(array $studentIds, string $status): int
    {
        $updated = Student::whereIn('id', $studentIds)
            ->update(['student_status' => $status]);
        
        $this->clearCache();
        
        return $updated;
    }

    /**
     * Get students with pending fees
     * 
     * @return Collection
     */
    public function getWithPendingFees(): Collection
    {
        return Student::whereHas('fees', function (Builder $query) {
            $query->where('payment_status', 'pending')
                ->orWhere('payment_status', 'partial');
        })
        ->with(['program', 'division', 'fees' => function ($query) {
            $query->where('payment_status', '!=', 'paid');
        }])
        ->get();
    }

    /**
     * Get students without guardians
     * 
     * @return Collection
     */
    public function getWithoutGuardians(): Collection
    {
        return Student::doesntHave('guardians')
            ->where('student_status', 'active')
            ->with(['program', 'division'])
            ->get();
    }

    /**
     * Apply filters to query
     * 
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        // Status filter
        if (isset($filters['status'])) {
            $query->where('student_status', $filters['status']);
        } else {
            // Default to active students
            $query->where('student_status', 'active');
        }

        // Program filter
        if (isset($filters['program_id'])) {
            $query->where('program_id', $filters['program_id']);
        }

        // Division filter
        if (isset($filters['division_id'])) {
            $query->where('division_id', $filters['division_id']);
        }

        // Academic year filter
        if (isset($filters['academic_year'])) {
            $query->where('academic_year', $filters['academic_year']);
        }

        // Academic session filter
        if (isset($filters['academic_session_id'])) {
            $query->where('academic_session_id', $filters['academic_session_id']);
        }

        // Gender filter
        if (isset($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        // Category filter
        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        // Date range filter
        if (isset($filters['admission_date_from'])) {
            $query->where('admission_date', '>=', $filters['admission_date_from']);
        }
        if (isset($filters['admission_date_to'])) {
            $query->where('admission_date', '<=', $filters['admission_date_to']);
        }

        // Blood group filter
        if (isset($filters['blood_group'])) {
            $query->where('blood_group', $filters['blood_group']);
        }
    }

    /**
     * Clear all student-related caches
     * 
     * @return void
     */
    private function clearCache(): void
    {
        Cache::forget('students.count.by.status');
        Cache::forget('students.count.by.program');
    }

    /**
     * Get statistics for dashboard
     * 
     * @return array
     */
    public function getStatistics(): array
    {
        return Cache::remember('students.statistics', self::CACHE_TTL, function () {
            return [
                'total' => Student::count(),
                'active' => Student::where('student_status', 'active')->count(),
                'graduated' => Student::where('student_status', 'graduated')->count(),
                'dropped' => Student::where('student_status', 'dropped')->count(),
                'suspended' => Student::where('student_status', 'suspended')->count(),
                'by_gender' => Student::select('gender', DB::raw('count(*) as count'))
                    ->where('student_status', 'active')
                    ->groupBy('gender')
                    ->pluck('count', 'gender')
                    ->toArray(),
                'by_category' => Student::select('category', DB::raw('count(*) as count'))
                    ->where('student_status', 'active')
                    ->groupBy('category')
                    ->pluck('count', 'category')
                    ->toArray(),
            ];
        });
    }
}
