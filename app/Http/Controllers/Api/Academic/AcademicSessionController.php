<?php

namespace App\Http\Controllers\Api\Academic;

use App\Http\Controllers\Controller;
use App\Models\Academic\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AcademicSessionController extends Controller
{
    public function index(): JsonResponse
    {
        $sessions = AcademicSession::orderBy('start_date', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }

   public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:academic_sessions,session_name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        if (($validated['is_active'] ?? false) && (now()->lt($validated['start_date']) || now()->gt($validated['end_date']))) {
            return response()->json([
                'success' => false,
                'errors' => ['is_active' => ['An active session must include the current date.']]
            ], 422);
        }
    $session = AcademicSession::create([
        'session_name' => $validated['name'], // ✅ mapping
        'start_date' => $validated['start_date'],
        'end_date' => $validated['end_date'],
        'is_active' => $validated['is_active'] ?? 1,
        'is_current' => 0,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Academic session created successfully',
        'data' => $session
    ], 201);
}


    public function show(AcademicSession $academicSession): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $academicSession
        ]);
    }

    public function update(Request $request, AcademicSession $academicSession): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:academic_sessions,session_name,' . $academicSession->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        if (($validated['is_active'] ?? false) && (now()->lt($validated['start_date']) || now()->gt($validated['end_date']))) {
            return response()->json([
                'success' => false,
                'errors' => ['is_active' => ['An active session must include the current date.']]
            ], 422);
        }

    $academicSession->update([
        'session_name' => $validated['name'],
        'start_date' => $validated['start_date'],
        'end_date' => $validated['end_date'],
        'is_active' => $validated['is_active'] ?? $academicSession->is_active,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Academic session updated successfully',
        'data' => $academicSession
    ]);
}

    public function destroy(AcademicSession $academicSession): JsonResponse
    {
        $academicSession->delete();

        return response()->json([
            'success' => true,
            'message' => 'Academic session deleted successfully'
        ]);
    }
}