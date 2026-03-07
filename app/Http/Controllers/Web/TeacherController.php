<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Academic\Department;
use App\Models\Academic\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = User::role('teacher')
            ->latest()
            ->paginate(15);

        return view('dashboard.teachers.index', compact('teachers'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $divisions = Division::where('is_active', true)->get();
        return view('dashboard.teachers.create', compact('departments', 'divisions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'department_id' => 'nullable|exists:departments,id',
            'phone' => 'nullable|string|max:15',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('teacher');

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('teachers', 'public');
            $user->update(['photo_path' => $path]);
        }

        return redirect()->route('dashboard.teachers.index')
            ->with('success', 'Teacher created successfully!');
    }

    public function show(User $teacher)
    {
        return view('dashboard.teachers.show', compact('teacher'));
    }

    public function edit(User $teacher)
    {
        $departments = Department::where('is_active', true)->get();
        $divisions = Division::where('is_active', true)->get();
        return view('dashboard.teachers.edit', compact('teacher', 'departments', 'divisions'));
    }

    public function update(Request $request, User $teacher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->id,
            'password' => 'nullable|string|min:8|confirmed',
            'department_id' => 'nullable|exists:departments,id',
            'phone' => 'nullable|string|max:15',
            'photo' => 'nullable|image|max:2048',
        ]);

        $teacher->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($request->filled('password')) {
            $teacher->update(['password' => Hash::make($validated['password'])]);
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('teachers', 'public');
            $teacher->update(['photo_path' => $path]);
        }

        return redirect()->route('dashboard.teachers.index')
            ->with('success', 'Teacher updated successfully!');
    }

    public function destroy(User $teacher)
    {
        $teacher->delete();
        return redirect()->route('dashboard.teachers.index')
            ->with('success', 'Teacher deleted successfully!');
    }
}
