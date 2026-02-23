<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Academic\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments
     */
    public function index(Request $request): View
    {
        $query = Department::with(['programs', 'hod']);
        
        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $departments = $query->withCount('students')
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view('departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department
     */
    public function create(): View
    {
        // ❌ is_active removed
        $users = User::orderBy('name')->get();

        return view('departments.create', compact('users'));
    }

    /**
     * Store a newly created department
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:departments,name',
            'code'        => 'required|string|max:20|unique:departments,code',
            'description' => 'nullable|string',
            'hod_user_id' => 'nullable|exists:users,id',
        ]);

        Department::create([
            'name'        => $validated['name'],
            'code'        => $validated['code'],
            'description' => $validated['description'] ?? null,
            'hod_user_id' => $validated['hod_user_id'] ?? null,
            'is_active'   => true, // departments table ka column
        ]);

        return redirect()
            ->route('web.departments.index')
            ->with('success', 'Department created successfully!');
    }

    /**
     * Display the specified department
     */
    public function show(Department $department): View
    {
        $department->load(['programs', 'hod']);

        return view('departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified department
     */
    public function edit(Department $department): View
    {
        // ❌ is_active removed
        $users = User::orderBy('name')->get();

        return view('departments.edit', compact('department', 'users'));
    }

    /**
     * Update the specified department
     */
    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:departments,name,' . $department->id,
            'code'        => 'required|string|max:20|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'hod_user_id' => 'nullable|exists:users,id',
            'is_active'   => 'boolean',
        ]);

        $department->update([
            'name'        => $validated['name'],
            'code'        => $validated['code'],
            'description' => $validated['description'],
            'hod_user_id' => $validated['hod_user_id'],
            'is_active'   => $request->has('is_active'),
        ]);

        return redirect()
            ->route('web.departments.index')
            ->with('success', 'Department updated successfully!');
    }

    /**
     * Remove the specified department
     */
    public function destroy(Department $department): RedirectResponse
    {
        // Check if department has programs
        if ($department->programs()->count() > 0) {
            return redirect()
                ->route('web.departments.index')
                ->with('error', 'Cannot delete department with existing programs. Please reassign or delete programs first.');
        }
        
        try {
            $department->delete(); // Soft delete

            return redirect()
                ->route('web.departments.index')
                ->with('success', 'Department deleted successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->route('web.departments.index')
                ->with('error', 'Error deleting department: ' . $e->getMessage());
        }
    }
}
