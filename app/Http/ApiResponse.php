<?php

namespace App\Http;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/**
 * API Response Helper Class
 * 
 * Provides standardized response formats for all API endpoints.
 * Ensures consistent JSON structure across the entire application.
 * 
 * @example
 * return ApiResponse::success($student, 'Student retrieved successfully');
 * return ApiResponse::error('Failed to retrieve students', null, 500);
 * return ApiResponse::created($student, 'Student created successfully');
 * return ApiResponse::paginated($students, 'Students retrieved successfully');
 */
class ApiResponse
{
    /**
     * Success response
     *
     * @param mixed $data Data to return (default: null)
     * @param string $message Success message (default: 'Success')
     * @param int $status HTTP status code (default: 200)
     * @return JsonResponse
     */
    public static function success($data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => [],
        ], $status);
    }

    /**
     * Error response
     *
     * @param string $message Error message (default: 'Error')
     * @param mixed $errors Validation errors or additional error data (default: null)
     * @param int $status HTTP status code (default: 400)
     * @return JsonResponse
     */
    public static function error(string $message = 'Error', $errors = null, int $status = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => [],
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Created response (201)
     *
     * @param mixed $data Created resource data (default: null)
     * @param string $message Success message (default: 'Resource created successfully')
     * @return JsonResponse
     */
    public static function created($data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => [],
        ], 201);
    }

    /**
     * Paginated response
     *
     * @param LengthAwarePaginator $paginator Paginator instance
     * @param string $message Success message (default: 'Data retrieved successfully')
     * @param int $status HTTP status code (default: 200)
     * @param array $additionalMeta Additional meta data to merge (default: [])
     * @return JsonResponse
     */
    public static function paginated(LengthAwarePaginator $paginator, string $message = 'Data retrieved successfully', int $status = 200, array $additionalMeta = []): JsonResponse
    {
        $meta = array_merge([
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ], $additionalMeta);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'meta' => $meta,
        ], $status);
    }

    /**
     * Validation error response (422)
     *
     * @param mixed $errors Validation errors
     * @param string $message Error message (default: 'Validation failed')
     * @return JsonResponse
     */
    public static function validationError($errors, string $message = 'Validation failed'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => [],
            'errors' => $errors,
        ], 422);
    }

    /**
     * Unauthorized response (401)
     *
     * @param string $message Error message (default: 'Unauthenticated')
     * @return JsonResponse
     */
    public static function unauthorized(string $message = 'Unauthenticated'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => [],
        ], 401);
    }

    /**
     * Forbidden response (403)
     *
     * @param string $message Error message (default: 'Access denied')
     * @return JsonResponse
     */
    public static function forbidden(string $message = 'Access denied'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => [],
        ], 403);
    }

    /**
     * Not found response (404)
     *
     * @param string $message Error message (default: 'Resource not found')
     * @return JsonResponse
     */
    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => [],
        ], 404);
    }

    /**
     * No content response (204)
     *
     * @return JsonResponse
     */
    public static function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }
}
