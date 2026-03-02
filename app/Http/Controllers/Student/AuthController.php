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

        // Check if student exists
        $student = Student::where('email', $credentials['email'])->first();

        if (!$student) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        // Verify password
        if (!\Hash::check($credentials['password'], $student->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
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
