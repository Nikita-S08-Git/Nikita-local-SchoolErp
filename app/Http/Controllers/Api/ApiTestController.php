<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\ApiResponse;
use App\Models\User\Student;
use Illuminate\Http\Request;

/**
 * Test controller to verify ApiResponse helper class
 */
class ApiTestController extends Controller
{
    /**
     * Test success response
     * GET /api/test/success
     */
    public function success()
    {
        $data = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];
        
        return ApiResponse::success($data, 'Success response working correctly');
    }

    /**
     * Test error response
     * GET /api/test/error
     */
    public function error()
    {
        return ApiResponse::error('This is an error message', null, 400);
    }

    /**
     * Test error with errors array
     * GET /api/test/error-validation
     */
    public function errorValidation()
    {
        $errors = [
            'email' => ['The email field is required'],
            'name' => ['The name field is required']
        ];
        
        return ApiResponse::error('Validation failed', $errors, 422);
    }

    /**
     * Test created response
     * GET /api/test/created
     */
    public function created()
    {
        $data = [
            'id' => 1,
            'name' => 'New Student',
            'created_at' => now()->toISOString()
        ];
        
        return ApiResponse::created($data, 'Student created successfully');
    }

    /**
     * Test paginated response
     * GET /api/test/paginated
     */
    public function paginated(Request $request)
    {
        $perPage = $request->get('per_page', 5);
        
        $students = Student::paginate($perPage);
        
        return ApiResponse::paginated($students, 'Students retrieved successfully');
    }

    /**
     * Test paginated with additional meta
     * GET /api/test/paginated-meta
     */
    public function paginatedMeta(Request $request)
    {
        $perPage = $request->get('per_page', 5);
        
        $students = Student::paginate($perPage);
        
        return ApiResponse::paginated($students, 'Students retrieved successfully', 200, [
            'total_outstanding' => 15000.00
        ]);
    }

    /**
     * Test not found response
     * GET /api/test/not-found
     */
    public function notFound()
    {
        return ApiResponse::notFound('Student not found');
    }

    /**
     * Test unauthorized response
     * GET /api/test/unauthorized
     */
    public function unauthorized()
    {
        return ApiResponse::unauthorized();
    }

    /**
     * Test forbidden response
     * GET /api/test/forbidden
     */
    public function forbidden()
    {
        return ApiResponse::forbidden();
    }

    /**
     * Test no content response
     * GET /api/test/no-content
     */
    public function noContent()
    {
        return ApiResponse::noContent();
    }

    /**
     * Test null data in success response
     * GET /api/test/null-data
     */
    public function nullData()
    {
        return ApiResponse::success(null, 'Operation completed but no data');
    }
}
