<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User\Student;
use App\Models\User\StudentGuardian; // ✅ Correct model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuardianController extends Controller
{
 // ✅ If you need guardian listing via API:
    public function index()
    {
        $guardians = StudentGuardian::with('student')->paginate(20);
        return response()->json(['success' => true, 'data' => $guardians]); // JSON!
    }
    public function store(Request $request, $student_id)
    {
        $student = Student::findOrFail($student_id);

        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'relation' => 'required|string|max:100',
            'mobile_number' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $guardian = $student->guardians()->create($request->only([
            'full_name', 'relation', 'mobile_number', 'email'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Guardian added successfully',
            'data' => $guardian
        ], 201);
    }

    public function show($student_id, $guardian_id)
    {
        $guardian = StudentGuardian::where('student_id', $student_id)
            ->findOrFail($guardian_id);
        return response()->json(['success' => true, 'data' => $guardian]);
    }

    public function update(Request $request, $student_id, $guardian_id)
    {
        $guardian = StudentGuardian::where('student_id', $student_id)
            ->findOrFail($guardian_id);

        $validator = Validator::make($request->all(), [
            'full_name' => 'sometimes|required|string|max:255',
            'relation' => 'sometimes|required|string|max:100',
            'mobile_number' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $guardian->update($request->only(['full_name', 'relation', 'mobile_number', 'email']));

        return response()->json([
            'success' => true,
            'message' => 'Guardian updated successfully',
            'data' => $guardian
        ]);
    }

    public function destroy($student_id, $guardian_id)
    {
        $guardian = StudentGuardian::where('student_id', $student_id)
            ->findOrFail($guardian_id);
        $guardian->delete();
        return response()->json(['success' => true, 'message' => 'Guardian deleted successfully']);
    }
}