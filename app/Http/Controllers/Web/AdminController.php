<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User\Student;
use App\Helpers\PasswordHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display user credentials management page (Admin Only)
     */
    public function credentials()
    {
        // Only admins can view this page
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized access. Only administrators can view this page.');
        }

        // Get all students with their user accounts (include all statuses)
        $students = Student::with(['user', 'division'])
            ->paginate(20);

        // Get all teachers (users with teacher role)
        $teachers = User::role('teacher')
            ->with('roles')
            ->paginate(20);

        // Get all staff members
        $staff = \App\Models\HR\StaffProfile::with(['user', 'department'])
            ->paginate(20);

        return view('admin.credentials', compact('students', 'teachers', 'staff'));
    }

    /**
     * Reset password for a user (Admin Only)
     */
    public function resetPassword(Request $request, $userId)
    {
        // Only admins can reset passwords
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            return back()->with('error', 'Unauthorized access. Only administrators can reset passwords.');
        }

        $user = User::findOrFail($userId);
        
        // Generate new password
        $newPassword = PasswordHelper::generate(10);
        
        $user->update([
            'password' => Hash::make($newPassword),
            'temp_password' => $newPassword,
            'password_generated_at' => now(),
        ]);

        return back()->with('success', 'Password reset successfully for ' . $user->name . '. New password: ' . $newPassword);
    }

    /**
     * Export credentials to CSV (Admin Only)
     */
    public function exportCredentials(Request $request)
    {
        // Only admins can export credentials
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized access. Only administrators can export credentials.');
        }

        $type = $request->get('type', 'students');
        
        if ($type === 'students') {
            $users = Student::with(['user', 'division'])
                ->where('student_status', 'active')
                ->get()
                ->map(function($student) {
                    return [
                        'Name' => $student->first_name . ' ' . $student->last_name,
                        'Email' => $student->user->email ?? 'N/A',
                        'Admission No' => $student->admission_number,
                        'Roll No' => $student->roll_number,
                        'Division' => $student->division->division_name ?? 'N/A',
                        'Password' => $student->user->temp_password ?? 'Not Set',
                        'Generated On' => $student->user->password_generated_at?->format('d M Y') ?? 'N/A',
                    ];
                });
        } else {
            $users = User::role('teacher')
                ->with('roles')
                ->get()
                ->map(function($teacher) {
                    return [
                        'Name' => $teacher->name,
                        'Email' => $teacher->email,
                        'Role' => $teacher->roles->first()->name ?? 'N/A',
                        'Password' => $teacher->temp_password ?? 'Not Set',
                        'Generated On' => $teacher->password_generated_at?->format('d M Y') ?? 'N/A',
                    ];
                });
        }

        // Simple CSV export
        $filename = 'Credentials_' . $type . '_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($output, array_keys($users->first() ?? []));
        
        // Add data
        foreach ($users as $user) {
            fputcsv($output, $user);
        }
        
        fclose($output);
        exit;
    }
}
