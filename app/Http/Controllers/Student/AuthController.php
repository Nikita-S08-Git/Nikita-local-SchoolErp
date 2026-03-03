<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User\Student;

class AuthController extends Controller
{
    /**
     * Show student login form
     */
    public function showLogin()
    {
        return view('student.auth.login');
    }

    /**
     * Handle student login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check if student exists (through user relationship)
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        // Verify password from user account
        if (!\Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        // Get the student record
        $student = \App\Models\User\Student::where('user_id', $user->id)->first();

        if (!$student) {
            return back()->withErrors([
                'email' => 'Student record not found. Please contact administration.',
            ]);
        }

        // Check if student is active
        if ($student->student_status !== 'active') {
            return back()->withErrors([
                'email' => 'Your account is not active. Please contact administration.',
            ]);
        }

        // Login the student
        Auth::guard('student')->login($student, $request->filled('remember'));

        $request->session()->regenerate();

        return redirect()->intended(route('student.dashboard'));
    }

    /**
     * Logout student
     */
    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login');
    }
}
