<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuardianRequest;
use App\Http\Requests\UpdateGuardianRequest;
use App\Models\User\StudentGuardian;
use App\Models\User\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GuardianController extends Controller
{
    /**
     * Show form to create new guardian
     */
    public function create(Student $student)
    {
        return view('academic.guardians.create', compact('student'));
    }

    /**
     * Store newly created guardian
     */
    public function store(StoreGuardianRequest $request, Student $student)
    {
        $validated = $request->validated();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store(
                'uploads/guardians/photos',
                'public'
            );
        }

        $validated['student_id'] = $student->id;
        $validated['is_primary_contact'] = $request->has('is_primary_contact');

        // If setting primary, unset other guardians
        if ($validated['is_primary_contact']) {
            StudentGuardian::where('student_id', $student->id)->update(['is_primary_contact' => false]);
        }

        StudentGuardian::create($validated);

        return redirect()
            ->route('dashboard.students.show', $student)
            ->with('success', 'Guardian added successfully.');
    }

    /**
     * Show edit form
     */
    public function edit(Student $student, StudentGuardian $guardian)
    {
        if ($guardian->student_id !== $student->id) {
            abort(403);
        }

        return view('academic.guardians.edit', compact('student', 'guardian'));
    }

    /**
     * Update guardian
     */
    public function update(UpdateGuardianRequest $request, Student $student, StudentGuardian $guardian)
    {
        if ($guardian->student_id !== $student->id) {
            abort(403, 'Unauthorized action');
        }

        $validated = $request->validated();

        // Handle photo replacement
        if ($request->hasFile('photo')) {
            if ($guardian->photo_path && Storage::disk('public')->exists($guardian->photo_path)) {
                Storage::disk('public')->delete($guardian->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store(
                'uploads/guardians/photos',
                'public'
            );
        }

        $validated['is_primary_contact'] = $request->has('is_primary_contact');

        if ($validated['is_primary_contact']) {
            StudentGuardian::where('student_id', $student->id)->update(['is_primary_contact' => false]);
        }

        $guardian->update($validated);

        return redirect()
            ->route('dashboard.students.show', $student)
            ->with('success', 'Guardian updated successfully.');
    }

    /**
     * Delete guardian
     */
    public function destroy(Student $student, StudentGuardian $guardian)
    {
        if ($guardian->student_id !== $student->id) {
            abort(403, 'Unauthorized action');
        }

        // Clean up photo
        if ($guardian->photo_path && Storage::disk('public')->exists($guardian->photo_path)) {
            Storage::disk('public')->delete($guardian->photo_path);
        }

        $guardian->delete();

        return redirect()
            ->route('dashboard.students.show', $student)
            ->with('success', 'Guardian deleted successfully.');
    }
}