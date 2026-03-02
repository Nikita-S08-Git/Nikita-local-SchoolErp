<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Models\Academic\Division;
use App\Models\User\Student;
use App\Models\StudentProfile;
use App\Models\Academic\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display teacher dashboard
     */
    public function index()
    {
        $teacher = Auth::user();
        $teacherProfile = $teacher->teacherProfile;

        // Get assigned divisions from teacher_assignments table
        $divisionIds = \App\Models\TeacherAssignment::where('teacher_id', $teacher->id)
            ->where('assignment_type', 'division')
            ->pluck('division_id');
        
        $divisions = Division::whereIn('id', $divisionIds)
            ->with(['program', 'session'])
            ->get();

        // Get all students from teacher's divisions
        $students = Student::whereIn('division_id', $divisionIds)
            ->where('student_status', 'active')
            ->whereNull('deleted_at')
            ->with(['division', 'user'])
            ->orderBy('division_id')
            ->orderBy('first_name')
            ->get();

        // Get student count for this teacher's divisions
        $totalStudents = $students->count();

        // Get today's schedule (both day-of-week based and date-specific)
        $today = strtolower(date('l'));
        $todayDate = now()->format('Y-m-d');
        
        // Get weekly schedule (based on day_of_week)
        $weeklySchedule = \App\Models\Academic\Timetable::where('teacher_id', $teacher->id)
            ->where('day_of_week', $today)
            ->whereNull('date') // Only get weekly recurring classes, not date-specific ones
            ->with(['subject', 'division'])
            ->orderBy('start_time')
            ->get();
        
        // Get today's date-specific schedule
        $dateSpecificSchedule = \App\Models\Academic\Timetable::where('teacher_id', $teacher->id)
            ->whereDate('date', $todayDate)
            ->with(['subject', 'division'])
            ->orderBy('start_time')
            ->get();
        
        // Combine both schedules (date-specific takes precedence for duplicates)
        $todaySchedule = $dateSpecificSchedule->concat($weeklySchedule);

        // Get attendance stats for this month
        $attendanceStats = $this->getAttendanceStats($teacher, $divisionIds);

        return view('teacher.dashboard', compact(
            'teacher',
            'teacherProfile',
            'divisions',
            'students',
            'totalStudents',
            'todaySchedule',
            'attendanceStats'
        ));
    }

    /**
     * Get attendance statistics
     */
    private function getAttendanceStats($teacher, $divisionIds)
    {
        // Get students from teacher's divisions
        $studentIds = Student::whereIn('division_id', $divisionIds)
            ->where('student_status', 'active')
            ->pluck('id');

        // Get attendance for this month (regardless of who marked it)
        $totalMarked = \App\Models\Attendance\Attendance::whereIn('student_id', $studentIds)
            ->where('date', '>=', now()->startOfMonth())
            ->count();

        $presentCount = \App\Models\Attendance\Attendance::whereIn('student_id', $studentIds)
            ->where('status', 'present')
            ->where('date', '>=', now()->startOfMonth())
            ->count();

        $absentCount = \App\Models\Attendance\Attendance::whereIn('student_id', $studentIds)
            ->where('status', 'absent')
            ->where('date', '>=', now()->startOfMonth())
            ->count();

        $percentage = $totalMarked > 0 ? round(($presentCount / $totalMarked) * 100, 2) : 0;

        return [
            'total' => $totalMarked,
            'present' => $presentCount,
            'absent' => $absentCount,
            'percentage' => $percentage,
        ];
    }

    /**
     * Display teacher profile
     */
    public function profile()
    {
        $teacher = Auth::user();
        $teacherProfile = $teacher->teacherProfile ?? new TeacherProfile(['user_id' => $teacher->id]);

        // Get assigned divisions from teacher_assignments table
        $divisionIds = \App\Models\TeacherAssignment::where('teacher_id', $teacher->id)
            ->where('assignment_type', 'division')
            ->pluck('division_id');
        
        $divisions = Division::whereIn('id', $divisionIds)
            ->with(['program', 'session'])
            ->get();

        // Get all active subjects
        $subjects = \App\Models\Result\Subject::where('is_active', true)->get();

        return view('teacher.profile.index', compact('teacher', 'teacherProfile', 'divisions', 'subjects'));
    }

    /**
     * Show edit profile form
     */
    public function editProfile()
    {
        $teacher = Auth::user();
        $teacherProfile = $teacher->teacherProfile ?? new TeacherProfile(['user_id' => $teacher->id]);

        return view('teacher.profile.edit', compact('teacher', 'teacherProfile'));
    }

    /**
     * Update teacher profile
     */
    public function updateProfile(Request $request)
    {
        $teacher = Auth::user();

        $validated = $request->validate([
            'employee_id' => 'nullable|string|max:50|unique:teacher_profiles,employee_id,' . $teacher->id . ',user_id',
            'phone' => 'nullable|string|max:15',
            'alternate_phone' => 'nullable|string|max:15',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'current_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0',
            'joining_date' => 'nullable|date',
            'designation' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_relation' => 'nullable|string|max:50',
            'emergency_contact_phone' => 'nullable|string|max:15',
            'linkedin_url' => 'nullable|url|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

        $teacherProfile = TeacherProfile::firstOrNew(['user_id' => $teacher->id]);
        $teacherProfile->fill($validated);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($teacherProfile->photo_path) {
                Storage::disk('public')->delete($teacherProfile->photo_path);
            }

            $path = $request->file('photo')->store('teacher-photos', 'public');
            $teacherProfile->photo_path = $path;
        }

        $teacherProfile->save();

        return redirect()->route('teacher.profile')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Display assigned divisions
     */
    public function divisions()
    {
        $teacher = Auth::user();

        // Get division IDs assigned to this teacher with is_class_teacher flag
        $assignments = DB::table('teacher_assignments')
            ->where('teacher_id', $teacher->id)
            ->where('assignment_type', 'division')
            ->get();

        $divisionIds = $assignments->pluck('division_id');
        $classTeacherDivisions = $assignments->filter(function($assignment) {
            return $assignment->is_primary == 1;
        })->pluck('division_id');

        $divisions = Division::whereIn('id', $divisionIds)
            ->where('is_active', true)
            ->with(['academicYear'])
            ->withCount(['students as student_count' => function ($query) {
                $query->where('student_status', 'active');
            }])
            ->get()
            ->map(function($division) use ($classTeacherDivisions) {
                $division->is_class_teacher = $classTeacherDivisions->contains($division->id);
                return $division;
            });

        return view('teacher.divisions.index', compact('teacher', 'divisions'));
    }

    /**
     * Display students in a specific division
     */
    public function divisionStudents($divisionId)
    {
        $teacher = Auth::user();

        // Check if teacher has access to this division
        $hasAccess = DB::table('teacher_assignments')
            ->where('teacher_id', $teacher->id)
            ->where('division_id', $divisionId)
            ->where('assignment_type', 'division')
            ->exists();

        if (!$hasAccess) {
            abort(403, 'You do not have access to this division.');
        }

        $division = Division::with(['academicYear'])->findOrFail($divisionId);

        $students = Student::where('division_id', $divisionId)
            ->where('student_status', 'active')
            ->with(['user', 'studentProfile'])
            ->orderBy('first_name')
            ->get();

        // Calculate attendance percentage for each student
        $students->each(function ($student) {
            $student->attendance_percentage = StudentProfile::getAttendancePercentageForStudent(
                $student->id,
                $student->division_id
            );
        });

        return view('teacher.divisions.students', compact('division', 'students'));
    }

    /**
     * Display student details
     */
    public function studentDetails($studentId)
    {
        $teacher = Auth::user();

        $student = Student::with([
            'user',
            'studentProfile',
            'division.academicYear',
            'academicSession',
            'guardians'
        ])->findOrFail($studentId);

        // Check if teacher has access to this student's division
        $hasAccess = DB::table('teacher_assignments')
            ->where('teacher_id', $teacher->id)
            ->where('division_id', $student->division_id)
            ->where('assignment_type', 'division')
            ->exists();

        if (!$hasAccess) {
            abort(403, 'You do not have access to this student.');
        }

        // Get attendance percentage
        $attendancePercentage = Attendance::getPercentageForStudent($student->id, $student->division_id);

        // Get recent attendance records
        $recentAttendance = Attendance::where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        return view('teacher.students.details', compact('student', 'attendancePercentage', 'recentAttendance'));
    }
}
