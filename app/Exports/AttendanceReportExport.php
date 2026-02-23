<?php

namespace App\Exports;

use App\Models\Academic\Division;
use App\Models\User\Student;
use App\Models\Academic\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function collection()
    {
        $students = Student::where('division_id', $this->params['division_id'])
            ->where('student_status', 'active')
            ->orderBy('roll_number')
            ->get();
        
        $report = collect();
        foreach ($students as $student) {
            $attendances = Attendance::where('student_id', $student->id)
                ->whereBetween('date', [$this->params['from_date'], $this->params['to_date']])
                ->get();
            
            $total = $attendances->count();
            $present = $attendances->where('status', 'present')->count();
            $absent = $attendances->where('status', 'absent')->count();
            $percentage = $total > 0 ? ($present / $total) * 100 : 0;
            
            $report->push((object)[
                'roll_number' => $student->roll_number,
                'name' => $student->first_name . ' ' . $student->last_name,
                'total' => $total,
                'present' => $present,
                'absent' => $absent,
                'percentage' => $percentage,
            ]);
        }
        
        return $report;
    }

    public function headings(): array
    {
        return ['Roll No', 'Student Name', 'Total Days', 'Present', 'Absent', 'Attendance %'];
    }

    public function map($row): array
    {
        return [
            $row->roll_number,
            $row->name,
            $row->total,
            $row->present,
            $row->absent,
            number_format($row->percentage, 2) . '%',
        ];
    }
}
