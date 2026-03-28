<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show admin profile
     */
    public function index()
    {
        $admin = Auth::user();
        return view('admin.profile.index', compact('admin'));
    }

    /**
     * Show edit profile form
     */
    public function edit()
    {
        $admin = Auth::user();
        return view('admin.profile.edit', compact('admin'));
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $admin = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($admin->id)],
            'phone' => 'nullable|string|max:15',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($admin->photo_path) {
                \Storage::disk('public')->delete($admin->photo_path);
            }
            
            $path = $request->file('photo')->store('admin-photos', 'public');
            $admin->photo_path = $path;
        }

        $admin->save();

        return redirect()->route('admin.profile')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Show change password form
     */
    public function editPassword()
    {
        return view('admin.profile.change-password');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $admin = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // Verify current password
        if (!Hash::check($validated['current_password'], $admin->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ])->withInput();
        }

        // Update password
        $admin->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return redirect()->route('admin.profile')
            ->with('success', 'Password changed successfully!');
    }
}
