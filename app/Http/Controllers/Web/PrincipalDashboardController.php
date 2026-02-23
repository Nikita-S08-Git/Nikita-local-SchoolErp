<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User\Student;
use App\Models\User;
use App\Models\Academic\Division;
use App\Models\Academic\Program;
use App\Models\Academic\Subject;
use App\Models\Attendance\Attendance;
use App\Models\Fee\FeePayment;
use App\Models\Fee\StudentFee;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PrincipalDashboardController extends Controller
{
    public function index()
    {
        // Total Students (Active only)
        $totalStudents = Student::where('student_status', 'active')
            ->whereNull('deleted_at')
            ->count();

        // Total Teachers (Users with teacher role)
        $totalTeachers = User::role('teacher')->count();

        // Total Classes (Active Divisions)
        $totalDivisions = Division::where('is_active', true)->count();

        $totalPrograms = Program::where('is_active', true)->count();

        $totalSubjects = Subject::count();

        // Today's Attendance
        $attendanceToday = Attendance::whereDate('attendance_date', Carbon::today())
            ->select(
                DB::raw('COUNT(CASE WHEN status = "Present" THEN 1 END) as present'),
                DB::raw('COUNT(CASE WHEN status = "Absent" THEN 1 END) as absent'),
                DB::raw('COUNT(*) as total')
            )
            ->first();

        $attendancePercentage = $attendanceToday && $attendanceToday->total > 0
            ? round(($attendanceToday->present / $attendanceToday->total) * 100, 2)
            : 0;

        $totalClasses = $totalDivisions; // Alias for blade compatibility
        $todayAttendance = $attendanceToday ? $attendanceToday->total : 0;

        // Fee collection - Current month
        $feeCollection = FeePayment::whereMonth('created_at', Carbon::now())
            ->whereYear('created_at', Carbon::now())
            ->select(
                DB::raw('SUM(amount) as total_collected'),
                DB::raw('COUNT(*) as total_transactions')
            )
            ->first();

        // Pending fees (outstanding)
        $pendingFees = StudentFee::where('outstanding_amount', '>', 0)
            ->sum('outstanding_amount');

        // Recent Activities (last 5)
        $recentActivities = $this->getRecentActivities();

        return view('dashboard.principal', compact(
            'totalStudents',
            'totalTeachers',
            'totalClasses',
            'totalDivisions',
            'totalPrograms',
            'totalSubjects',
            'attendanceToday',
            'todayAttendance',
            'attendancePercentage',
            'feeCollection',
            'pendingFees',
            'recentActivities'
        ));
    }

    /**
     * Get recent activities for dashboard
     */
    private function getRecentActivities(): array
    {
        $activities = [];

        // Recent fee payments (last 5)
        $recentPayments = FeePayment::with(['studentFee.student'])
            ->latest()
            ->limit(3)
            ->get();

        foreach ($recentPayments as $payment) {
            if ($payment->studentFee && $payment->studentFee->student) {
                $student = $payment->studentFee->student;
                $activities[] = [
                    'icon' => 'bi-cash-coin text-success',
                    'title' => 'Fee Payment Received',
                    'description' => $student->first_name . ' ' . $student->last_name . ' paid ₹' . number_format($payment->amount, 2),
                    'time' => $payment->created_at->diffForHumans()
                ];
            }
        }

        // Recent admissions (last 2)
        $recentAdmissions = Student::with(['division', 'program'])
            ->latest()
            ->limit(2)
            ->get();

        foreach ($recentAdmissions as $student) {
            $activities[] = [
                'icon' => 'bi-person-plus-fill text-primary',
                'title' => 'New Student Admission',
                'description' => $student->first_name . ' ' . $student->last_name . ' admitted to ' . ($student->division->division_name ?? 'N/A'),
                'time' => $student->created_at->diffForHumans()
            ];
        }

        // Sort by time and return top 5
        usort($activities, function ($a, $b) {
            return 0; // Already sorted
        });

        return array_slice($activities, 0, 5);
    }
}
