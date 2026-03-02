<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User\Student;
use App\Models\Academic\Timetable;
use App\Models\Attendance\Attendance;
use App\Models\StudentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Constructor - Apply student auth middleware
     */
    public function __construct()
    {
        $this->middleware('auth:student');
    }

    /**
     * Display student dashboard
     */
    public function index()
    {
        $student = Auth::guard('student')->user();

        // Get today's timetable
        $today = strtolower(Carbon::now()->format('l'));
        $todayClasses = Timetable::where('division_id', $student->division_id)
            ->where('day_of_week', $today)
            ->with(['subject', 'teacher'])
            ->orderBy('start_time')
            ->get();

        // Get attendance summary
        $attendanceSummary = $this->getAttendanceSummary($student);

        // Get recent notifications
        $notifications = $student->notifications()
            ->latest()
            ->take(5)
            ->get();

        // Get upcoming classes (next 7 days)
        $upcomingClasses = $this->getUpcomingClasses($student);

        return view('student.dashboard', compact(
            'student',
            'todayClasses',
            'attendanceSummary',
            'notifications',
            'upcomingClasses'
        ));
    }

    /**
     * Display student profile
     */
    public function profile()
    {
        $student = Auth::guard('student')->user();

        return view('student.profile.index', compact('student'));
    }

    /**
     * Show edit profile form
     */
    public function editProfile()
    {
        $student = Auth::guard('student')->user();

        return view('student.profile.edit', compact('student'));
    }

    /**
     * Update student profile
     */
    public function updateProfile(Request $request)
    {
        $student = Auth::guard('student')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_no' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('student-photos', 'public');
            $student->photo = $path;
        }

        $student->update([
            'name' => $validated['name'],
            'contact_no' => $validated['contact_no'] ?? $student->contact_no,
        ]);

        return redirect()->route('student.profile')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Show change password form
     */
    public function changePassword()
    {
        return view('student.profile.change-password');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $student = Auth::guard('student')->user();

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        // Verify current password
        if (!Hash::check($validated['current_password'], $student->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        // Update password
        $student->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('student.profile')
            ->with('success', 'Password changed successfully!');
    }

    /**
     * Display student timetable
     */
    public function timetable()
    {
        $student = Auth::guard('student')->user();

        $timetable = Timetable::where('division_id', $student->division_id)
            ->with(['subject', 'teacher'])
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday')")
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $timeSlots = $this->getTimeSlots();

        return view('student.timetable.index', compact('timetable', 'days', 'timeSlots'));
    }

    /**
     * Display student attendance
     */
    public function attendance()
    {
        $student = Auth::guard('student')->user();

        $attendanceBySubject = $student->getAttendancePercentageBySubject();

        // Overall attendance
        $totalLectures = $student->attendances()->count();
        $presentDays = $student->attendances()->where('status', 'present')->count();
        $absentDays = $student->attendances()->where('status', 'absent')->count();
        $lateDays = $student->attendances()->where('status', 'late')->count();
        $overallPercentage = $totalLectures > 0 ? round(($presentDays / $totalLectures) * 100, 2) : 0;

        // Recent attendance records
        $recentAttendance = $student->attendances()
            ->with(['subject'])
            ->latest('date')
            ->take(10)
            ->get();

        return view('student.attendance.index', compact(
            'attendanceBySubject',
            'totalLectures',
            'presentDays',
            'absentDays',
            'lateDays',
            'overallPercentage',
            'recentAttendance'
        ));
    }

    /**
     * Display notifications
     */
    public function notifications()
    {
        $student = Auth::guard('student')->user();

        $notifications = $student->notifications()
            ->latest()
            ->paginate(20);

        return view('student.notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($id)
    {
        $student = Auth::guard('student')->user();

        $notification = $student->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        $student = Auth::guard('student')->user();

        $student->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    /**
     * Get attendance summary
     */
    private function getAttendanceSummary($student)
    {
        $totalLectures = $student->attendances()
            ->where('date', '>=', Carbon::now()->startOfMonth())
            ->count();

        $presentDays = $student->attendances()
            ->where('date', '>=', Carbon::now()->startOfMonth())
            ->where('status', 'present')
            ->count();

        $percentage = $totalLectures > 0 ? round(($presentDays / $totalLectures) * 100, 2) : 0;

        return [
            'total' => $totalLectures,
            'present' => $presentDays,
            'percentage' => $percentage,
        ];
    }

    /**
     * Get upcoming classes
     */
    private function getUpcomingClasses($student)
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $today = strtolower(Carbon::now()->format('l'));
        $todayIndex = array_search($today, $days);

        $upcomingClasses = collect();

        // Get classes for next 3 days
        for ($i = 0; $i < 3; $i++) {
            $dayIndex = ($todayIndex + $i) % count($days);
            $day = $days[$dayIndex];

            $classes = Timetable::where('division_id', $student->division_id)
                ->where('day_of_week', $day)
                ->with(['subject', 'teacher'])
                ->orderBy('start_time')
                ->get();

            if ($classes->isNotEmpty()) {
                $upcomingClasses->push([
                    'day' => ucfirst($day),
                    'date' => Carbon::now()->addDays($i)->format('M d'),
                    'classes' => $classes,
                ]);
            }
        }

        return $upcomingClasses;
    }

    /**
     * Get time slots
     */
    private function getTimeSlots()
    {
        return \App\Models\Academic\TimeSlot::orderBy('start_time')->get();
    }
}
