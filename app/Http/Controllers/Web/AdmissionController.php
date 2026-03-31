<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Academic\Admission;
use App\Services\AdmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdmissionController extends Controller
{
    protected AdmissionService $admissionService;

    public function __construct(AdmissionService $admissionService)
    {
        $this->admissionService = $admissionService;
    }

    public function index(Request $request)
    {
        $admissions = Admission::with(['program', 'division'])
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('application_no', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20);

        return view('admissions.index', compact('admissions'));
    }

    public function show(Admission $admission)
    {
        $admission->load(['program', 'division', 'documents']);
        return view('admissions.show', compact('admission'));
    }

    public function showApplyForm()
    {
        $programs = \App\Models\Academic\Program::where('is_active', true)->get();
        $divisions = \App\Models\Academic\Division::where('is_active', true)->get();
        $sessions = \App\Models\Academic\AcademicSession::where('is_active', true)->get();
        
        return view('admissions.apply', compact('programs', 'divisions', 'sessions'));
    }

    public function apply(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|regex:/^[a-zA-Z\s]+$/|max:255',
            'middle_name' => 'nullable|regex:/^[a-zA-Z\s]+$/|max:255',
            'last_name' => 'required|regex:/^[a-zA-Z\s]+$/|max:255',
            'date_of_birth' => 'required|date|before:today|after:1990-01-01',
            'gender' => 'required|in:male,female,other',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'religion' => 'nullable|string|max:50',
            'category' => 'required|in:general,obc,sc,st,ews',
            'aadhar_number' => 'nullable|digits:12|unique:students,aadhar_number',
            'mobile_number' => 'required|regex:/^[6-9]\d{9}$/',
            'email' => 'required|email|unique:students,email',
            'current_address' => 'required|string|min:10|max:500',
            'permanent_address' => 'nullable|string|min:10|max:500',
            'program_id' => 'required|exists:standards,id',
            'division_id' => 'required|exists:divisions,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'academic_year' => 'required|in:FY,SY,TY',
            // File validations
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'twelfth_marksheet' => 'nullable|mimes:pdf,jpeg,png,jpg|max:5120',
            'cast_certificate' => 'nullable|mimes:pdf,jpeg,png,jpg|max:5120',
        ]);

        // If permanent address is empty, use current address
        if (empty($validated['permanent_address'])) {
            $validated['permanent_address'] = $validated['current_address'];
        }

        // Handle file uploads - save directly to students table
        $studentData = $validated;
        
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $studentData['photo_path'] = $request->file('photo')->store('uploads/students/photos', 'public');
        }
        if ($request->hasFile('signature') && $request->file('signature')->isValid()) {
            $studentData['signature_path'] = $request->file('signature')->store('uploads/students/signatures', 'public');
        }
        if ($request->hasFile('twelfth_marksheet') && $request->file('twelfth_marksheet')->isValid()) {
            $studentData['marksheet_path'] = $request->file('twelfth_marksheet')->store('uploads/students/documents', 'public');
        }
        if ($request->hasFile('cast_certificate') && $request->file('cast_certificate')->isValid()) {
            $studentData['cast_certificate_path'] = $request->file('cast_certificate')->store('uploads/students/documents', 'public');
        }

        // Generate unique admission number and create student in same transaction
        $year = date('Y');
        $baseNumber = 'ADM' . $year;
        $studentData['admission_date'] = date('Y-m-d');
        $studentData['student_status'] = 'active';

        // Create student inside transaction to maintain lock
        $student = DB::transaction(function() use ($baseNumber, $studentData) {
            // Get the last admission number for this year with lock
            $lastStudent = \App\Models\User\Student::where('admission_number', 'like', $baseNumber . '%')
                ->orderBy('admission_number', 'desc')
                ->lockForUpdate()
                ->first();

            if ($lastStudent && strpos($lastStudent->admission_number, $baseNumber) === 0) {
                $lastNumber = (int) substr($lastStudent->admission_number, strlen($baseNumber));
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            // Ensure uniqueness with while loop
            $attemptedNumber = $nextNumber;
            $maxAttempts = 10;
            $attempt = 0;
            
            while ($attempt < $maxAttempts) {
                $candidateNumber = $baseNumber . str_pad($attemptedNumber, 5, '0', STR_PAD_LEFT);
                
                // Check if this number already exists
                $exists = \App\Models\User\Student::where('admission_number', $candidateNumber)
                    ->lockForUpdate()
                    ->exists();
                
                if (!$exists) {
                    $studentData['admission_number'] = $candidateNumber;
                    break;
                }
                
                $attemptedNumber++;
                $attempt++;
            }
            
            // Fallback: use timestamp if all attempts fail
            if ($attempt >= $maxAttempts) {
                $studentData['admission_number'] = $baseNumber . time();
            }

            // Create student directly in the transaction
            return $this->admissionService->createStudentFromAdmission($studentData);
        });

        // Load relationships for program and division
        $student->load(['program', 'division', 'user']);

        // Get the temp password from the user record
        $user = $student->user;
        $tempPassword = $user->temp_password;

        // Prepare student details for the modal
        $studentDetails = [
            'admission_number' => $student->admission_number,
            'full_name' => $student->full_name,
            'email' => $student->email,
            'mobile_number' => $student->mobile_number,
            'program' => $student->program ? $student->program->name : 'N/A',
            'division' => $student->division ? $student->division->division_name : 'N/A',
            'academic_year' => $student->academic_year,
            'admission_date' => $student->admission_date->format('d M Y'),
        ];

        return redirect()->route('admissions.apply.form')
            ->with('success', 'Admission submitted successfully! Your Admission No. is: ' . $student->admission_number . '. Please save it for tracking.')
            ->with('student_email', $student->email)
            ->with('temp_password', $tempPassword)
            ->with('student_details', $studentDetails);
    }

    public function verify(Request $request, Admission $admission)
    {
        try {
            $this->admissionService->verifyAdmission($admission, $request->notes ?? null);
            
            return redirect()->back()
                ->with('success', 'Admission verified successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, Admission $admission)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        try {
            $this->admissionService->rejectAdmission($admission, $validated['rejection_reason']);
            
            return redirect()->back()
                ->with('success', 'Admission rejected.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Enroll a verified admission as a student
     */
    public function enroll(Admission $admission)
    {
        try {
            $student = $this->admissionService->enrollStudent($admission);
            
            return response()->json([
                'success' => true,
                'message' => 'Student enrolled successfully!',
                'student_id' => $student->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
