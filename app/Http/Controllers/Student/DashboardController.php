<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User\Student;
use App\Models\Academic\Timetable;
use App\Models\Academic\Attendance;
use App\Models\Result\StudentMark;
use App\Models\Result\Examination;
use App\Models\Fee\StudentFee;
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

        // Get recent results/marks
        $recentResults = StudentMark::where('student_id', $student->id)
            ->with(['examination', 'subject'])
            ->latest()
            ->limit(5)
            ->get();

        // Get upcoming exams - exams for student's program
        $upcomingExams = Examination::where('end_date', '>=', now())
            ->whereHas('subject', function($query) use ($student) {
                $query->where('program_id', $student->program_id);
            })
            ->with(['subject'])
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        // Get fee summary
        $totalFees = StudentFee::where('student_id', $student->id)->sum('total_amount');
        $paidFees = StudentFee::where('student_id', $student->id)->sum('paid_amount');
        $outstandingFees = $totalFees - $paidFees;

        return view('student.dashboard', compact(
            'student',
            'todayClasses',
            'attendanceSummary',
            'notifications',
            'upcomingClasses',
            'recentResults',
            'upcomingExams',
            'totalFees',
            'paidFees',
            'outstandingFees'
        ));
    }

    /**
     * Display student profile
     */
    public function profile()
    {
        $student = Auth::guard('student')->user();
        
        // Calculate attendance percentage
        $totalLectures = $student->attendances()->count();
        $presentDays = $student->attendances()->where('status', 'present')->count();
        $attendancePercentage = $totalLectures > 0 ? round(($presentDays / $totalLectures) * 100, 2) : 0;

        return view('student.profile.index', compact('student', 'attendancePercentage'));
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
        
        // Get the associated user for password verification
        $user = $student->user;
        
        if (!$user) {
            return back()->withErrors([
                'current_password' => 'User account not found.',
            ]);
        }

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        // Verify current password using the user model
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ])->withInput();
        }

        // Update password on the user model
        $user->update([
            'password' => Hash::make($validated['password']),
            'temp_password' => null, // Clear temp password after first change
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
            ->with(['timetable.subject'])
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
     * Display student fees
     */
    public function fees()
    {
        $user = Auth::guard('student')->user();
        
        // Get the associated student record via user_id
        $student = \App\Models\User\Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            // Try getting by id directly (if student guard returns Student model)
            $student = $user;
        }
        
        // Get fee records for the student
        $feeRecords = \App\Models\Fee\StudentFee::where('student_id', $student->id)
            ->with(['feeStructure.feeHead'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate totals
        $totalFees = $feeRecords->sum('total_amount');
        $totalPaid = $feeRecords->sum('paid_amount');
        $totalOutstanding = $totalFees - $totalPaid;
        
        return view('student.fees.index', compact(
            'student',
            'feeRecords',
            'totalFees',
            'totalPaid',
            'totalOutstanding'
        ));
    }

    /**
     * Display student results
     */
    public function results()
    {
        $student = Auth::guard('student')->user();
        
        // Get exam results for the student
        $results = \App\Models\Result\StudentMark::where('student_id', $student->id)
            ->with(['subject', 'examination'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('student.results.index', compact('student', 'results'));
    }

    /**
     * Display student library
     */
    public function library()
    {
        $student = Auth::guard('student')->user();
        
        // Get issued books for the student
        $issuedBooks = \App\Models\Library\BookIssue::where('student_id', $student->id)
            ->with(['book'])
            ->orderBy('issue_date', 'desc')
            ->get();
        
        return view('student.library.index', compact('student', 'issuedBooks'));
    }

    /**
     * Display fee payment page
     */
    public function feesPayment($studentFee)
    {
        $student = Auth::guard('student')->user();
        
        $fee = \App\Models\Fee\StudentFee::where('id', $studentFee)
            ->where('student_id', $student->id)
            ->with(['feeStructure'])
            ->firstOrFail();
        
        return view('student.fees.payment', compact('student', 'fee'));
    }

    /**
     * Get time slots
     */
     private function getTimeSlots()
     {
         return \App\Models\Academic\TimeSlot::orderBy('start_time')->get();
     }
}
