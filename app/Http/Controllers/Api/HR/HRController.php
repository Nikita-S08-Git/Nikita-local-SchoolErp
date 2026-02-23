<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\StaffProfile;
use App\Models\HR\SalaryStructure;
use App\Models\HR\StaffSalary;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HRController extends Controller
{
    public function getStaff(Request $request): JsonResponse
    {
        $query = StaffProfile::with(['department', 'user']);

        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $staff = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $staff
        ]);
    }

    public function generateSalaries(Request $request): JsonResponse
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12'
        ]);

        $year = $request->year;
        $month = $request->month;

        // Check if salaries already generated
        $existingSalaries = StaffSalary::where('salary_year', $year)
            ->where('salary_month', $month)
            ->exists();

        if ($existingSalaries) {
            return response()->json([
                'success' => false,
                'message' => 'Salaries already generated for this month'
            ], 400);
        }

        $activeStaff = StaffProfile::active()->get();
        $generatedCount = 0;

        foreach ($activeStaff as $staff) {
            // Get salary structure by designation
            $salaryStructure = SalaryStructure::where('designation', $staff->designation)
                ->where('is_active', true)
                ->first();

            if (!$salaryStructure) {
                continue; // Skip if no salary structure found
            }

            StaffSalary::create([
                'staff_id' => $staff->id,
                'salary_structure_id' => $salaryStructure->id,
                'salary_year' => $year,
                'salary_month' => $month,
                'basic_salary' => $salaryStructure->basic_salary,
                'total_allowances' => $salaryStructure->total_allowances,
                'total_deductions' => $salaryStructure->total_deductions,
                'net_salary' => $salaryStructure->net_salary,
                'status' => 'pending'
            ]);

            $generatedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Salaries generated for {$generatedCount} staff members",
            'data' => ['count' => $generatedCount]
        ]);
    }

    public function processSalaryPayment(Request $request): JsonResponse
    {
        $request->validate([
            'salary_ids' => 'required|array',
            'salary_ids.*' => 'exists:staff_salaries,id'
        ]);

        $salaries = StaffSalary::whereIn('id', $request->salary_ids)
            ->where('status', 'pending')
            ->get();

        foreach ($salaries as $salary) {
            $salary->update([
                'status' => 'paid',
                'payment_date' => now()->toDateString()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Salary payments processed successfully',
            'data' => ['processed_count' => $salaries->count()]
        ]);
    }

    public function getSalaryReport(Request $request): JsonResponse
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12'
        ]);

        $salaries = StaffSalary::with(['staff.department'])
            ->where('salary_year', $request->year)
            ->where('salary_month', $request->month)
            ->get();

        $summary = [
            'total_staff' => $salaries->count(),
            'total_amount' => $salaries->sum('net_salary'),
            'paid_count' => $salaries->where('status', 'paid')->count(),
            'pending_count' => $salaries->where('status', 'pending')->count(),
            'paid_amount' => $salaries->where('status', 'paid')->sum('net_salary'),
            'pending_amount' => $salaries->where('status', 'pending')->sum('net_salary')
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'salaries' => $salaries,
                'summary' => $summary
            ]
        ]);
    }

    public function getSalaryStructures(): JsonResponse
    {
        $structures = SalaryStructure::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => $structures
        ]);
    }
    
    
     public function show($id): JsonResponse
    {
        $staff = StaffProfile::with('department')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $staff
        ]);
    }
public function store(Request $request): JsonResponse
{
    $validated = $request->validate([
        'first_name'      => 'required|string|max:255',
        'last_name'       => 'required|string|max:255',
        'phone'           => 'required|string|max:20', //  REQUIRED
        'designation'     => 'required|string|max:255',
        'department_id'   => 'required|exists:departments,id',
        'employment_type' => 'required|in:permanent,contract,part_time',
        'joining_date'    => 'required|date',
        'date_of_birth'   => 'required|date',
        'gender'          => 'required|in:male,female,other',
        'address'         => 'required|string', // REQUIRED
        'status'          => 'nullable|in:active,inactive,terminated'
    ]);

    $staff = StaffProfile::create([
        'user_id'         => auth()->id() ?? 1,              // FIX
        'employee_id'     => 'EMP' . time(),                 //  FIX
        'first_name'      => $validated['first_name'],
        'last_name'       => $validated['last_name'],
        'phone'           => $validated['phone'],
        'designation'     => $validated['designation'],
        'department_id'   => $validated['department_id'],
        'employment_type' => $validated['employment_type'],
        'joining_date'    => $validated['joining_date'],
        'date_of_birth'   => $validated['date_of_birth'],
        'gender'          => $validated['gender'],
        'address'         => $validated['address'],
        'status'          => $validated['status'] ?? 'active',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Staff created successfully',
        'data' => $staff
    ], 201);
}


    public function update(Request $request, $id): JsonResponse
    {
        $staff = StaffProfile::findOrFail($id);

        $validated = $request->validate([
            'first_name'      => 'sometimes|required|string|max:255',
            'last_name'       => 'sometimes|required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'designation'     => 'sometimes|required|string|max:255',
            'department_id'   => 'sometimes|required|integer|exists:departments,id',
            'employment_type' => 'sometimes|required|string|in:permanent,contract,part_time',
            'status'          => 'nullable|string|in:active,inactive,terminated',
            'joining_date'    => 'sometimes|required|date',
            'address'         => 'nullable|string',
            'date_of_birth'   => 'sometimes|required|date',
            'gender'          => 'sometimes|required|string|in:male,female,other'
        ]);

        $staff->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Staff updated successfully',
            'data' => $staff
        ]);
    }

  public function destroy($id): JsonResponse
{
    $staff = StaffProfile::findOrFail($id);

    $staff->salaries()->delete(); // delete child records first
    $staff->delete();

    return response()->json([
        'success' => true,
        'message' => 'Staff deleted successfully'
    ]);
}

}