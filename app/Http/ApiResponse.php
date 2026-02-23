<?php

namespace App\Http;

/**
 * API Response Helper Class
 * 
 * Provides standardized response formats for all API endpoints
 * Ensures consistent JSON structure across the entire application
 */
class ApiResponse
{
    /**
     * Success response
     *
     * @param mixed $data Data to return
     * @param string $message Success message
     * @param int $statusCode HTTP status code
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data = null, string $message = 'Operation successful', int $statusCode = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Error response
     *
     * @param string $message Error message
     * @param mixed $errors Validation errors or additional error data
     * @param int $statusCode HTTP status code
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error(string $message = 'An error occurred', $errors = null, int $statusCode = 400): \Illuminate\Http\JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Validation error response
     *
     * @param mixed $errors Validation errors
     * @param string $message Error message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function validationError($errors, string $message = 'Validation failed'): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }

    /**
     * Unauthorized response
     *
     * @param string $message Error message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function unauthorized(string $message = 'Unauthenticated'): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 401);
    }

    /**
     * Forbidden response
     *
     * @param string $message Error message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function forbidden(string $message = 'Access denied'): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 403);
    }

    /**
     * Not found response
     *
     * @param string $message Error message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function notFound(string $message = 'Resource not found'): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 404);
    }

    /**
     * Paginated response
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $paginator Paginator instance
     * @param string $message Success message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function paginated($paginator, string $message = 'Records retrieved successfully'): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'meta' => [
                'total' => $paginator->total(),
                'count' => $paginator->count(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'total_pages' => $paginator->lastPage(),
                'links' => [
                    'first' => $paginator->url(1),
                    'last' => $paginator->url($paginator->lastPage()),
                    'prev' => $paginator->previousPageUrl(),
                    'next' => $paginator->nextPageUrl(),
                ],
            ],
        ], 200);
    }

    /**
     * Created response (201)
     *
     * @param mixed $data Created resource data
     * @param string $message Success message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function created($data, string $message = 'Resource created successfully'): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], 201);
    }

    /**
     * No content response (204)
     *
     * @param string $message Success message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function noContent(string $message = 'Resource deleted successfully'): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], 204);
    }
}
