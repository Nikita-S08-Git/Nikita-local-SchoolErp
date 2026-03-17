<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\User\Student;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // First, check if user exists in main users table (admin, teacher, etc.)
        $user = User::where('email', $credentials['email'])->first();
        
        if ($user) {
            // Verify password
            if (\Hash::check($credentials['password'], $user->password)) {
                // Check if user is active
                if (!$user->isActive()) {
                    return back()->withErrors([
                        'email' => 'Your account is inactive. Please contact administration.',
                    ]);
                }

                // Login the user
                Auth::login($user, $request->filled('remember'));
                $request->session()->regenerate();

                // Check if password change is required
                if (empty($user->password_changed_at)) {
                    return redirect()->route('password.change')->with('warning', 'Please change your temporary password');
                }

                // Get user role
                $role = $user->roles->first()->name ?? 'student';

                // Role-based redirect
                $redirectRoutes = [
                    'principal' => 'dashboard.principal',
                    'admin' => 'dashboard.admin',
                    'teacher' => 'teacher.dashboard',
                    'class_teacher' => 'teacher.dashboard',
                    'subject_teacher' => 'teacher.dashboard',
                    'student' => 'student.dashboard',
                    'accounts_staff' => 'dashboard.accounts_staff',
                    'office' => 'dashboard.office',
                    'librarian' => 'dashboard.librarian',
                    'hod_commerce' => 'teacher.dashboard',
                    'hod_science' => 'teacher.dashboard',
                    'hod_management' => 'teacher.dashboard',
                    'hod_arts' => 'teacher.dashboard',
                ];

                $route = $redirectRoutes[$role] ?? 'student.dashboard';
                return redirect()->route($route);
            }
        } else {
            // Check if student exists
            $student = Student::where('email', $credentials['email'])->first();
            
            if ($student) {
                // For students, check if they have a user record (should always have)
                if (!$student->user) {
                    return back()->withErrors([
                        'email' => 'Student account configuration error. Please contact administration.',
                    ]);
                }

                // Verify password
                if (\Hash::check($credentials['password'], $student->user->password)) {
                    // Check if student is active
                    if ($student->student_status !== 'active') {
                        return back()->withErrors([
                            'email' => 'Your account is inactive. Please contact administration.',
                        ]);
                    }

                    // Login the student
                    Auth::guard('student')->login($student, $request->filled('remember'));
                    $request->session()->regenerate();

                    // Check if password change is required
                    if (empty($student->user->password_changed_at)) {
                        return redirect()->route('student.profile.change-password')->with('warning', 'Please change your temporary password');
                    }

                    return redirect()->route('student.dashboard');
                }
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        if (Auth::guard('student')->check()) {
            Auth::guard('student')->logout();
        } else {
            Auth::logout();
        }
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}