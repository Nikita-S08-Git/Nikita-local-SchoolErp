<?php

namespace App\Http\Controllers\Api\Academic;

use App\Http\Controllers\Controller;
use App\Models\Academic\Program;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProgramController extends Controller
{
    public function index(): JsonResponse
    {
        $programs = Program::with('department')->get();
        
        return response()->json([
            'success' => true,
            'data' => $programs
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:programs',
            'department_id' => 'required|exists:departments,id',
            'duration_years' => 'required|integer|min:1|max:10',
             'program_type' => 'required|in:undergraduate,postgraduate,diploma'
     ]);

        $program = Program::create($request->all());
        $program->load('department');

        return response()->json([
            'success' => true,
            'message' => 'Program created successfully',
            'data' => $program
        ], 201);
    }

 public function show(Program $program): JsonResponse
{
    // department exists 
    if ($program->department_id && $program->department()->exists()) {
        $program->load('department');
    }

    return response()->json([
        'success' => true,
        'data' => $program
    ]);
}


    public function update(Request $request, Program $program): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:programs,code,' . $program->id,
            'department_id' => 'required|exists:departments,id',
            'duration_years' => 'required|integer|min:1|max:10',
          //  'degree_type' => 'required|in:undergraduate,postgraduate,diploma,certificate'
             'program_type' => 'required|in:undergraduate,postgraduate,diploma'
        ]);

        $program->update($request->all());
        $program->load('department');

        return response()->json([
            'success' => true,
            'message' => 'Program updated successfully',
            'data' => $program
        ]);
    }

    public function destroy(Program $program): JsonResponse
    {
        $program->delete();

        return response()->json([
            'success' => true,
            'message' => 'Program deleted successfully'
        ]);
    }
}