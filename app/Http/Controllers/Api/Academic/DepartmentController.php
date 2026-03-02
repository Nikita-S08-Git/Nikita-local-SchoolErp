<?php

namespace App\Http\Controllers\Api\Academic;

use App\Http\Controllers\Controller;
use App\Models\Academic\Department;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    public function index(): JsonResponse
    {
        $departments = Department::where('is_active', true)
            ->with(['programs' => function($query) {
                $query->where('is_active', true);
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $departments
        ]);
    }


    public function show(Department $department): JsonResponse
{
    $department->load(['programs', 'hod']); // academicYears removed

    return response()->json([
        'success' => true,
        'data' => $department
    ]);
}



public function store(Request $request): JsonResponse
{
    $validated = $request->validate([
        'name' => 'required|string|max:100|unique:departments,name',
        'code' => 'required|string|max:20|unique:departments,code',
        'description' => 'nullable|string',
        'hod_user_id' => 'nullable|exists:users,id',
    ]);

    $department = Department::create([
        'name' => $validated['name'],
        'code' => $validated['code'],
        'description' => $validated['description'] ?? null,
        'hod_user_id' => $validated['hod_user_id'] ?? null,
        'is_active' => 1,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Department created successfully',
        'data' => $department
    ], 201);
}

public function update(Request $request, Department $department): JsonResponse
{
    $validated = $request->validate([
        'name' => 'required|string|max:100|unique:departments,name,' . $department->id,
        'code' => 'required|string|max:20|unique:departments,code,' . $department->id,
        'description' => 'nullable|string',
        'hod_user_id' => 'nullable|exists:users,id',
        'is_active' => 'boolean',
    ]);

    $department->update([
        'name' => $validated['name'],
        'code' => $validated['code'],
        'description' => $validated['description'] ?? $department->description,
        'hod_user_id' => $validated['hod_user_id'] ?? $department->hod_user_id,
        'is_active' => $validated['is_active'] ?? $department->is_active,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Department updated successfully',
        'data' => $department
    ]);
}

}