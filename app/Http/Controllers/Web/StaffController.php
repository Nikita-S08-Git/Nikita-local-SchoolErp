<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\HR\StaffProfile;
use App\Models\Academic\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        $staff = StaffProfile::with(['user', 'department'])
            ->latest()
            ->paginate(15);
        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        return view('staff.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'employee_id' => 'required|unique:staff_profiles,employee_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'emergency_contact' => 'nullable|string|max:15',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string',
            'joining_date' => 'required|date',
            'designation' => 'required|string|max:100',
            'department_id' => 'required|exists:departments,id',
            'employment_type' => 'required|in:permanent,contract,part_time',
        ]);

        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('teacher');

        StaffProfile::create([
            'user_id' => $user->id,
            'employee_id' => $validated['employee_id'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'],
            'emergency_contact' => $validated['emergency_contact'],
            'date_of_birth' => $validated['date_of_birth'],
            'gender' => $validated['gender'],
            'address' => $validated['address'],
            'joining_date' => $validated['joining_date'],
            'designation' => $validated['designation'],
            'department_id' => $validated['department_id'],
            'employment_type' => $validated['employment_type'],
        ]);

        return redirect()->route('staff.index')
            ->with('success', 'Staff member added successfully!');
    }

    public function show(StaffProfile $staff)
    {
        $staff->load(['user', 'department']);
        return view('staff.show', compact('staff'));
    }

    public function edit(StaffProfile $staff)
    {
        $departments = Department::where('is_active', true)->get();
        return view('staff.edit', compact('staff', 'departments'));
    }

    public function update(Request $request, StaffProfile $staff)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'emergency_contact' => 'nullable|string|max:15',
            'address' => 'required|string',
            'designation' => 'required|string|max:100',
            'department_id' => 'required|exists:departments,id',
            'employment_type' => 'required|in:permanent,contract,part_time',
            'status' => 'required|in:active,inactive,terminated',
        ]);

        $staff->update($validated);

        $staff->user->update([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
        ]);

        return redirect()->route('staff.index')
            ->with('success', 'Staff updated successfully!');
    }

    public function destroy(StaffProfile $staff)
    {
        $staff->delete();
        return redirect()->route('staff.index')
            ->with('success', 'Staff deleted successfully!');
    }
}
