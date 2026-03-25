<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Academic\Division;
use App\Models\User\Student;
use App\Models\Academic\Attendance;
use App\Models\Fee\StudentFee;
use App\Models\Fee\FeePayment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceReportExport;

class ReportController extends Controller
{
    /**
     * Show fee reports for accountant
     */
    public function index(Request $request)
    {
        // Fee collection statistics
        $todayCollection = FeePayment::whereDate('created_at', today())->sum('amount_paid');
        $monthlyCollection = FeePayment::whereMonth('created_at', now()->month)->sum('amount_paid');
        $totalOutstanding = StudentFee::where('outstanding_amount', '>', 0)->sum('outstanding_amount');
        
        // Recent payments
        $recentPayments = FeePayment::with(['student', 'studentFee'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Outstanding fees
        $outstandingFees = StudentFee::with(['student'])
            ->where('outstanding_amount', '>', 0)
            ->orderBy('outstanding_amount', 'desc')
            ->limit(10)
            ->get();
        
        return view('reports.fee-index', compact(
            'todayCollection',
            'monthlyCollection',
            'totalOutstanding',
            'recentPayments',
            'outstandingFees'
        ));
    }

    public function attendance(Request $request)
    {
        $divisions = Division::where('is_active', true)->get();
        
        if ($request->has('division_id') && $request->has('from_date') && $request->has('to_date')) {
            $division = Division::findOrFail($request->division_id);
            $students = Student::where('division_id', $division->id)
                ->where('student_status', 'active')
                ->orderBy('roll_number')
                ->get();
            
            $report = [];
            foreach ($students as $student) {
                $attendances = Attendance::where('student_id', $student->id)
                    ->whereBetween('date', [$request->from_date, $request->to_date])
                    ->get();
                
                $total = $attendances->count();
                $present = $attendances->where('status', 'present')->count();
                $absent = $attendances->where('status', 'absent')->count();
                $percentage = $total > 0 ? ($present / $total) * 100 : 0;
                
                $report[] = [
                    'student' => $student,
                    'total' => $total,
                    'present' => $present,
                    'absent' => $absent,
                    'percentage' => $percentage,
                ];
            }
            
            return view('reports.attendance', compact('divisions', 'division', 'report'));
        }
        
        return view('reports.attendance', compact('divisions'));
    }

    public function attendancePdf(Request $request)
    {
        $division = Division::findOrFail($request->division_id);
        $students = Student::where('division_id', $division->id)
            ->where('student_status', 'active')
            ->orderBy('roll_number')
            ->get();
        
        $report = [];
        foreach ($students as $student) {
            $attendances = Attendance::where('student_id', $student->id)
                ->whereBetween('date', [$request->from_date, $request->to_date])
                ->get();
            
            $total = $attendances->count();
            $present = $attendances->where('status', 'present')->count();
            $absent = $attendances->where('status', 'absent')->count();
            $percentage = $total > 0 ? ($present / $total) * 100 : 0;
            
            $report[] = [
                'student' => $student,
                'total' => $total,
                'present' => $present,
                'absent' => $absent,
                'percentage' => $percentage,
            ];
        }
        
        $pdf = Pdf::loadView('pdf.attendance-report', [
            'division' => $division,
            'report' => $report,
            'startDate' => $request->from_date,
            'endDate' => $request->to_date,
        ]);
        return $pdf->download('attendance-report-' . $division->division_name . '.pdf');
    }

    public function attendanceExcel(Request $request)
    {
        return Excel::download(new AttendanceReportExport($request->all()), 'attendance-report.xlsx');
    }
}
