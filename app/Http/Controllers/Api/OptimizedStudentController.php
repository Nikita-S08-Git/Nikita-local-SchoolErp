<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\User\Student;
use App\Repositories\StudentRepository;
use App\Services\ImprovedStudentService;
use App\Services\StudentExportService;
use App\Services\StudentImportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Optimized Student Controller
 * 
 * This controller follows Laravel best practices:
 * - Uses Form Requests for validation
 * - Uses Repository pattern for data access
 * - Uses Service layer for business logic
 * - Uses API Resources for response transformation
 * - Implements proper error handling
 * - Supports search, filter, and pagination
 * - Includes import/export functionality
 */
class OptimizedStudentController extends Controller
{
    public function __construct(
        private StudentRepository $repository,
        private ImprovedStudentService $service,
        private StudentExportService $exportService,
        private StudentImportService $importService
    ) {
        // Apply authorization middleware
        $this->middleware('auth:sanctum');
        $this->middleware('can:viewAny,App\Models\User\Student')->only(['index', 'search']);
        $this->middleware('can:view,student')->only('show');
        $this->middleware('can:create,App\Models\User\Student')->only('store');
        $this->middleware('can:update,student')->only('update');
        $this->middleware('can:delete,student')->only('destroy');
    }

    /**
     * Display a listing of students with filters and pagination
     * 
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only([
            'status',
            'program_id',
            'division_id',
            'academic_year',
            'academic_session_id',
            'gender',
            'category',
            'blood_group',
            'admission_date_from',
            'admission_date_to',
            'sort_by',
            'sort_order'
        ]);
        
        $perPage = $request->input('per_page', 20);
        
        $students = $this->repository->getAllWithFilters($filters, $perPage);
        
        return StudentResource::collection($students);
    }

    /**
     * Search students by multiple criteria
     * 
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'q' => 'required|string|min:2',
            'per_page' => 'nullable|integer|min:1|max:100'
        ]);
        
        $searchTerm = $request->input('q');
        $filters = $request->only([
            'status',
            'program_id',
            'division_id',
            'academic_year'
        ]);
        $perPage = $request->input('per_page', 20);
        
        $students = $this->repository->search($searchTerm, $filters, $perPage);
        
        return StudentResource::collection($students);
    }

    /**
     * Store a newly created student
     * 
     * @param StoreStudentRequest $request
     * @return JsonResponse
     */
    public function store(StoreStudentRequest $request): JsonResponse
    {
        try {
            $student = $this->service->createStudent($request->validated());
            
            return (new StudentResource($student))
                ->response()
                ->setStatusCode(201);
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified student
     * 
     * @param Student $student
     * @return StudentResource
     */
    public function show(Student $student): StudentResource
    {
        // Load relationships
        $student->load([
            'program',
            'division',
            'academicSession',
            'guardians',
            'user'
        ]);
        
        return new StudentResource($student);
    }

    /**
     * Update the specified student
     * 
     * @param UpdateStudentRequest $request
     * @param Student $student
     * @return JsonResponse
     */
    public function update(UpdateStudentRequest $request, Student $student): JsonResponse
    {
        try {
            $updatedStudent = $this->service->updateStudent($student, $request->validated());
            
            return (new StudentResource($updatedStudent))
                ->response()
                ->setStatusCode(200);
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified student (soft delete)
     * 
     * @param Student $student
     * @return JsonResponse
     */
    public function destroy(Student $student): JsonResponse
    {
        try {
            $this->service->deleteStudent($student);
            
            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student profile with statistics
     * 
     * @param Student $student
     * @return JsonResponse
     */
    public function profile(Student $student): JsonResponse
    {
        try {
            $profile = $this->service->getStudentProfile($student->id);
            
            return response()->json([
                'success' => true,
                'data' => $profile
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch student profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change student status
     * 
     * @param Request $request
     * @param Student $student
     * @return JsonResponse
     */
    public function changeStatus(Request $request, Student $student): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:active,graduated,dropped,suspended,tc_issued',
            'reason' => 'nullable|string|max:500'
        ]);
        
        try {
            $updatedStudent = $this->service->changeStatus(
                $student,
                $request->status,
                $request->reason
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Student status updated successfully',
                'data' => new StudentResource($updatedStudent)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update student status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update student status
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'required|integer|exists:students,id',
            'status' => 'required|in:active,graduated,dropped,suspended,tc_issued'
        ]);
        
        try {
            $updated = $this->service->bulkUpdateStatus(
                $request->student_ids,
                $request->status
            );
            
            return response()->json([
                'success' => true,
                'message' => "{$updated} students updated successfully",
                'updated_count' => $updated
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk update students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export students to Excel/CSV
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function export(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:xlsx,csv,pdf',
            'filters' => 'nullable|array'
        ]);
        
        try {
            $filters = $request->input('filters', []);
            $format = $request->input('format');
            
            $filePath = match($format) {
                'xlsx' => $this->exportService->exportToExcel($filters, 'xlsx'),
                'csv' => $this->exportService->exportToCsv($filters),
                'pdf' => $this->exportService->exportToPdf($filters),
            };
            
            return response()->json([
                'success' => true,
                'message' => 'Export completed successfully',
                'file_path' => $filePath,
                'download_url' => asset('storage/exports/' . basename($filePath))
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download export template
     * 
     * @return JsonResponse
     */
    public function exportTemplate(): JsonResponse
    {
        try {
            $filePath = $this->exportService->exportTemplate();
            
            return response()->json([
                'success' => true,
                'message' => 'Template generated successfully',
                'file_path' => $filePath,
                'download_url' => asset('storage/templates/' . basename($filePath))
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate template',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import students from Excel/CSV
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240' // 10MB max
        ]);
        
        try {
            $file = $request->file('file');
            $filePath = $file->store('imports', 'local');
            
            $results = $this->importService->importFromFile(storage_path('app/' . $filePath));
            
            // Clean up uploaded file
            \Storage::disk('local')->delete($filePath);
            
            return response()->json([
                'success' => $results['failed'] === 0,
                'message' => "Import completed: {$results['success']} successful, {$results['failed']} failed",
                'data' => $results
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to import students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate import file before processing
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function validateImport(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);
        
        try {
            $file = $request->file('file');
            $filePath = $file->store('imports', 'local');
            
            $validation = $this->importService->validateImportFile(storage_path('app/' . $filePath));
            
            // Clean up uploaded file
            \Storage::disk('local')->delete($filePath);
            
            return response()->json([
                'success' => true,
                'data' => $validation
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to validate import file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student statistics
     * 
     * @return JsonResponse
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->repository->getStatistics();
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get students by program
     * 
     * @param int $programId
     * @return AnonymousResourceCollection
     */
    public function byProgram(int $programId): AnonymousResourceCollection
    {
        $students = $this->repository->getByProgram($programId);
        
        return StudentResource::collection($students);
    }

    /**
     * Get students by division
     * 
     * @param int $divisionId
     * @return AnonymousResourceCollection
     */
    public function byDivision(int $divisionId): AnonymousResourceCollection
    {
        $students = $this->repository->getByDivision($divisionId);
        
        return StudentResource::collection($students);
    }
}
