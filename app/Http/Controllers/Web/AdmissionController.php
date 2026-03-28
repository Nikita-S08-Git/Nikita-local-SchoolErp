<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Academic\Admission;
use App\Services\AdmissionService;
use Illuminate\Http\Request;

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
        return view('admissions.apply');
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

        // Add admission number and status
        $studentData['admission_number'] = 'ADM' . date('Y') . str_pad(\App\Models\User\Student::count() + 1, 4, '0', STR_PAD_LEFT);
        $studentData['admission_date'] = date('Y-m-d');
        $studentData['student_status'] = 'active';

        // Create student directly
        $student = $this->admissionService->createStudentFromAdmission($studentData);

        // Get the temp password from the user record
        $user = $student->user;
        $tempPassword = $user->temp_password;

        return redirect()->route('admissions.apply.form')
            ->with('success', 'Admission submitted successfully! Your Admission No. is: ' . $student->admission_number . '. Please save it for tracking.')
            ->with('student_email', $student->email)
            ->with('temp_password', $tempPassword);
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
